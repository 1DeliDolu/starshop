# Alias an Interface with AsAlias

Time to add a new feature! I want to add logging to the button presses so
we can keep track of what our minions are doing!

In `ButtonRemote`, we *could* inject the logger service and log the button
presses right here. But technically... this violates something called the
"single responsibility principle". That's just a fancy way of saying that
a class should only do one thing. Right now, this class handles
pressing buttons. Adding logging here would make it do two things. And that's
probably fine, but let's challenge ourselves!

## Decorator Pattern

Instead, we'll use a design pattern called "Decoration" by
creating a new class that wraps, or "decorates", `ButtonRemote`.

In `src/Remote/`, create a new PHP class called `LoggerRemote`. This is our
"decorator" and it needs the same methods as the class it's decorating. 
Copy the two methods from `ButtonRemote`, paste them here and remove their guts.
Add a constructor, and inject the logger service with
`private LoggerInterface` (the one from `Psr\Log`) `$logger`. Now, inject the
object we're decorating: `private ButtonRemote $inner`. I like to use `$inner`
as the parameter name when creating decorators.

[[[ code('0038383af8') ]]]

In each method, first, defer to the inner object. In `press()`, write
`$this->inner->press($name);` and in `buttons()`, `return $this->inner->buttons()`:

[[[ code('366f79ea60') ]]]

*Now* let's add the logging. Before the inner press, add
`$this->logger->info('Pressing button {name}')` and add a context array
with `'name' => $name`. This curly brace stuff is a mini-templating system
used by Monolog, Symfony's logger. Copy this, paste below the inner press
and change "Pressing" to "Pressed":

[[[ code('f9c9f2e1f0') ]]]

Decorator done!

To actually *use* this decorator, in `RemoteController`, instead of injecting `ButtonRemote`,
inject `LoggerRemote`:

[[[ code('85a46f21ce') ]]]

Let's try it! Back in our app, press the power button and jump into the profiler
for the last request. We can see that the logic from `ButtonRemote` *is* still being
called. And if we check out the "Logs" panel, we see the messages!

## Decorator Interface

The two remote classes have the same methods, this is a sign that
we could use a common interface. Create a new class in `src/Remote/` called `RemoteInterface`.
Copy the `press()` method *stub* from `LoggerRemote` and paste it here.
Do the same for `buttons()`:

[[[ code('f6e5b7302b') ]]]

Next, make both remote classes implement this interface. In `ButtonRemote`, add
`implements RemoteInterface`:

[[[ code('5e6b55bb26') ]]]

... and do the same in `LoggerRemote`:

[[[ code('51875e7d20') ]]]

In `LoggerRemote`'s constructor, Change `ButtonRemote` to `RemoteInterface`:

[[[ code('af721a5771') ]]]

We don't *have* to do this, but now that we have an interface, that's really the
best thing to type-hint.

Back in the app, refresh and... Error!

> Cannot autowire service `LoggerRemote`: argument `$inner` of method `__construct()`
> references interface `RemoteInterface` but no such service exists.

This happens when Symfony tries to autowire an interface but there are multiple
implementations. We need to tell Symfony *which* of our two services to use when we
type-hint `RemoteInterface`. And the error even gives us a hint!

> You should maybe alias this interface to one of these existing services:
> "ButtonRemote", "LoggerRemote".

Ah, we need to "alias" our interface. Technically, this will create a new service
whose id is `App\Remote\RemoteInterface`, but is really just an alias - or a pointer -
to one of our *real* remote services. 

## `#[AsAlias]`

Do this with, you guessed it, another attribute: `#[AsAlias]`. In `ButtonRemote`,
our inner-most class, add `#[AsAlias]`:

[[[ code('c4a17ec755') ]]]

This tells Symfony:

> Hey! When you need to autowire a `RemoteInterface`, use `ButtonRemote`.

Back in our app, refresh and... the error is gone! Press "channel up" and check
the profiler. The button logic is still being called and if we check the "Logs" panel,
there's our messages.

Open up `RemoteController`: we're still injecting a concrete instance of our
service. That's fine, but we can be fancier now and use `RemoteInterface`:

[[[ code('f51056b5be') ]]]

Back in the app, press "channel down" and check the profiler. The button logic
is working, but our logs are gone!

Because we aliased `RemoteInterface` to `ButtonRemote`, Symfony doesn't know about
our decoration! When it sees the `RemoteInterface` type-hint, it injects `ButtonRemote`,
not `LoggerRemote`.

Next: Let's fix this by using service decoration, and of course, another attribute!
