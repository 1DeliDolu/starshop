# More Laziness Attributes

The awesome thing about "lazy services" is they require no changes to your
code (as long as services aren't final). But what if the `ParentalControls`
service lived inside a 3rd-party package *and* was final? Tricky! But we *do*
have some options.

## `#[AutowireServiceClosure]`

Pretend that `ParentalControls` is final and lives in a 3rd-party package.
In `VolumeUpButton`, replace `#[Lazy]` with `#[AutowireServiceClosure]` passing
`ParentalControls::class`:

[[[ code('b8be529458') ]]]

This will inject a closure that returns a `ParentalControls` instance when invoked
(and it will only be instantiated *when* invoked).

To help our IDE, add a docblock above the constructor:
`@param \Closure():ParentalControls $parentalControls`:

[[[ code('199911f50f') ]]]

Now, down in the `press()` method's `if` statement, switch `false` to `true` so
we always detect that the volume is too high. Because `$parentalControls` is a closure,
we need to wrap `$this->parentalControls` in braces and invoke it with `()` before
calling `->volumeTooHigh()`:

[[[ code('5f6712c6b0') ]]]

Check this out! Because we added the docblock, our IDE provides auto-completion
and lets us click through (with CMD+click) to the `volumeTooHigh()` method.
Awesome!

Remove the `dump()`, spin over to our app, refresh, and press the "volume up"
button. Jump into the profiler. We see that the `volumeTooHigh()` logic *is*
being called. Great! The `ParentalControls` service is only instantiated when
the closure is invoked - and we only invoke it when needed.

## `#[AutowireCallable]`

Let's look at another way to do the same thing. In `VolumeUpButton`,
replace `#[AutowireServiceClosure]` with `#[AutowireCallable]`. Keep
`ParentalControls::class` as the first argument but prefix it with `service`:

[[[ code('86344711fe') ]]]

`#[AutowireCallable]` *also* injects a closure. But instead of returning the full
service object, it instantiates the service, calls a single method on it, then
returns the result.

Make this multiline to give us some more room. Add a second argument:
`method: 'volumeTooHigh'`:

[[[ code('13f5cff8d4') ]]]

When Symfony instantiates a service that uses `#[AutowireCallable]`, by default, it
will instantiate its service. It's an eager beaver! To avoid this, add a third
argument: `lazy: true`:

[[[ code('32f8bbbb71') ]]]

Now, `ParentalControls` will only be instantiated when the closure is invoked.

In the docblock above, change the closure return type to `void` to match the
return type of `volumeTooHigh()`:

[[[ code('621d54f47a') ]]]

Down in `press()`, remove the `->volumeTooHigh()` call:

[[[ code('75fc23734b') ]]]

This is now called by the closure when invoked.

Spin back to the app, refresh, press the "volume up" button, and jump into the profiler.
The `ParentalControls::volumeTooHigh()` logic is still being called. Perfect!

`#[AutowireCallable]` is certainly cool, but for most cases, I prefer using
`#[AutowireServiceClosure]` because:

1. It's lazy by default.
2. More flexible because it returns the full service object.
3. And, with proper docblocks, we get: auto-completion, method navigation, refactoring
   support, and better static analysis with tools like PhpStan.

---

Ok team, that's it for this course! Put a `#[TimeForVacation]` attribute on your
code and go relax!

YAML service config isn't going away entirely, but these attributes improve
your developer experience by keeping your code *and* service configuration together.

More attributes are being added in almost every new Symfony version. Follow the [Symfony
blog](https://symfony.com/blog) to stay up-to-date! Check this out, in Symfony 7.2, there's
a new `#[WhenNot]` attribute! It's basically the opposite of the `#[When]` attribute we
discussed earlier. Cool!

Check out the "Dependency Injection" section of the
[Symfony Attributes Overview](https://symfony.com/doc/current/reference/attributes.html#dependency-injection)
doc to see a list of all the dependency injection attributes that are currently available
and how they work.

'Til next time! Happy coding!
