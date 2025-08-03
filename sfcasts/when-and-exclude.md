# Enable Services in Specific Environments with When

Wouldn't it be cool if we had a special *secret* button on our remote we could
use to make sure it's working correctly? *Of course* it would! Let's add a
sneaky new "Diagnostics" button that will *only* be available in the `dev`
environment.

## Adding a Diagnostics Button

In `App\Remote\Button`, create a new class:
`DiagnosticsButton`. Make it implement `ButtonInterface`... and hold
"control" + "enter" to add the `press()` method. Inside,
we'll `dump('Pressed diagnostics button.')`... and, just like before, add
`#[AsTaggedItem]` with `diagnostics` as the index:

[[[ code('f7353113ad') ]]]

Finally, copy the `diagnostics.svg` file from the `tutorial/` directory
into `assets/icons/`.

Spin over to our app and refresh... new button! And if
we press it... it even looks like it's working! We're pretty impressive 
remote control developers!

## `#[When]`

Our new button is automatically registered with the service container, but we
want it *only* in the `dev` environment. The `#[When]` attribute is *perfect* for this. Back
in `DiagnosticsButton`, add `#[When]` with `dev` as the
argument:

[[[ code('9e99eb0750') ]]]

Thanks to this, the class will be *completely* ignored by the service
container *unless* we're in the `dev` environment. Head over and refresh.
It's *still* there. That makes sense: we *are* in
the `dev` environment. So let's fudge this a bit. Change the `#[When]`
argument from `dev` to `prod` - so we can see it working:

[[[ code('6552d10d4a') ]]]

Refresh again and... boom! The button is *gone*! *Awesome*!

## `#[Exclude]`

Now that this is working, let's talk about the cousin
of `#[When]`: `#[Exclude]`. This is like a big warning sign that tells
Symfony to *never*, ever register a specific class as a service in the service
container. Right now, in `config/services.yaml`, this `App/:` section tells
Symfony to autowire every class in the `src/` directory.
*Inside* `App/:`, we have this `exclude` key. That contains a list of paths
Symfony should *ignore* and is the *traditional* way to exclude classes from
being registered as services.
It's fine, but I find it a bit clunky. *This* is where `#[Exclude]` comes in.

In `MuteButton`, up here, add `#[Exclude]`...

[[[ code('4f91191a4c') ]]]

then head back to our app and refresh. The "Mute" button is *gone*! It *worked*.

This won't be a super common attribute in your app, but hey! This is
the DI attribute tutorial! So you get to see *all* the neat stuff!

Next: Let's talk about *lazy* services.
