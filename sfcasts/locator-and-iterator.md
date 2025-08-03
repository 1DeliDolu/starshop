# Container and Iterator with ServiceCollectionInterface

In the last chapter, we made these buttons listed *programmatically*.
*But* when we did that, we broke the actual button-press functionality! Whoops!
The kids are getting restless: we need to fix this.

Over in `ButtonRemote`, there are a couple of ways to solve this. The
first approach, which is probably the *easiest*, is to inject two arguments:
one that's an *iterator* of the button services and one that's a
*locator*, meaning a mini-container with a `get()` method for fetching each
service. That *would* work and it's perfectly valid. But we can do better!

## `ServiceCollectionInterface`

We can inject an object that's both an iterator *and* a locator:
`ServiceCollectionInterface`. Let's take a look at that. This
a `ServiceProviderInterface` (that's the *locator*) *and* an `IteratorAggregate`
(that's the *iterator*). For good measure, it's also `Countable`.

Back in `ButtonRemote`, we need to switch `AutowireIterator` back to
`AutowireLocator` for Symfony to inject the `ServiceCollectionInterface`:

[[[ code('5ad7e20eb4') ]]]

I'll clean up some unused imports here, and... nice.

Back in our app, refresh and... Okay, we're *still* listing the buttons, so
that's a good sign. Now, if we click a button... it looks like this is working
again! Pop into the profiler to check the `POST` request to see that the proper button
logic *is* still being called. *Sweet*!

## Laziness

One of the great things about a service locator is that it's *lazy*. Services aren't
instantiated until and *unless* we call `get()` to fetch them. And even then,
only a single service is created, even if we go nuts and call `get()` for the
same service a bunch of times.

I *love* being lazy but we have a problem. Down here, in `buttons()`, we're
iterating over all the buttons. This is *forcing* the instantiation of *all*
the button services just to get their `$name`'s. Since we just care about the
names, this is a waste!

## `ServiceCollectionInterface::getProvidedServices()`

`ServiceCollectionInterface` to the rescue! Symfony service locators have
a special method called `getProvidedServices()`. Remove all this code and
`dd($this->buttons->getProvidedServices())` to see what it returns:

[[[ code('08be3abedf') ]]]

Jump back to our app and refresh. This looks almost identical to the manual
mapping we previously used with `#[AutowireLocator]`.

We want the *keys* of this array. Back here, return `array_keys()` of
`$this->buttons->getProvidedServices()`:

[[[ code('250cb67f36') ]]]

Go back to the app and... refresh. Everything is still working and
behind the scenes, we're no longer instantiating *all* the button services.

Performance win!

To celebrate, let's add a new button to our remote!

## Adding a Mute Button

Create a new PHP class called `MuteButton` and have it implement
`ButtonInterface`. Press `Ctrl+Enter` to generate the `press()` method. Inside,
write `dump('Pressed mute button')`. Now, add `#[AsTaggedItem]` with an `$index`
of `mute`. Leave the priority as the default, `0`. This will slot this button
below the others:

[[[ code('43d438a73c') ]]]

There's just one other thing we need to do. Each button has an SVG icon in
`assets/icons` with the same name as the button. Copy the `mute.svg` file from
`tutorial/` and paste it here.

Moment of truth! Go back to our app, refresh, and... there it is! Click it
and check the profiler. It's working! Now we can mute the TV when the
kids are watching Barney. *Perfect*!

That's it for this refactor! Adding buttons is simple *and* performant.

Next, let's add logging to our remote and learn about our next attribute:
`#[AsAlias]`.
