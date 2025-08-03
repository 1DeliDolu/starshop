# Enforce Named Autowiring with Target

In the last chapter, we added logging!
Check out the "Logs" profile panel. Notice this little "app" tag?
Symfony's logger, Monolog has the concept of log channels, which are like
*categories* for your log messages. The default channel is "app", but you can
create others.

## Adding a New Log Channel

Let's see if we can add a new channel called "button". Open
`config/packages/monolog.yaml`. Under `channels`, add a new one
called `button`:

[[[ code('3cae5681f0') ]]]

Perfect! Close this file.

We want `LoggerRemote` to use this new channel... but... how do we do that?

We're autowiring the `LoggerInterface` service. That gives us the
*main* logger: the one that logs to the "app" channel. To log to the new
channel, we need to autowire a *different* logger service.

Spin over to your terminal and run:

```terminal
bin/console debug:autowiring logger
```

The `logger` argument filters the results down to services with "logger"
in the name. The first is `LoggerInterface`: this is the service you get if using
*standard autowiring*, like we currently are.

But there are *multiple* logger services. The others all have an argument, 
including a new one called `buttonLogger`. This is the service we want!
To grab *this* service, we need to use *named autowiring*.

## Named Autowiring

In `LoggerRemote`, rename the argument to `$buttonLogger`:

[[[ code('f7df8f7e41') ]]]

Now, head back to our app... refresh the page, and... press the "Channel Up"
button. We've watched enough Sponge Bob for one day. Pop into the last request's
profile and check out the "Logs" panel. Sweet! We're now logging to the `button` channel!

This is working like it's supposed to, but I'd like to talk
about a problem that can occur when using named autowiring. Imagine,
a year from now, we rename this variable to something simpler like
just `$logger`:

[[[ code('b9a0356414') ]]]

Jump back to our app, refresh the page, and press a button. It *seems* like it
still works, but check out the "Logs" profile panel. The *message* is there, 
but it sneakily switched back the "app" channel. How dare it!

When we renamed the argument, we broke named autowiring and switched back to
standard autowiring. Maybe this isn't super problematic for logging, but
imagine something like a database or cache service being autowired
this way. That *could* be trouble... and I'm generally anti-trouble.

## Target Attribute

We need a way to *enforce* named autowiring. Say hello to the `#[Target]`
attribute!

Over in `LoggerRemote`, add `#[Target]` above the `LoggerInterface $logger`
argument. Inside, set the value to `buttonLogger` - the argument name
(without the `$`) we saw in the `debug:autowiring` command:

[[[ code('71074ee51f') ]]]

Now, the argument name could be anything, so get creative!

Jump back to the app, refresh, press "Volume Up", and check the "Logs" profile
panel. We're back to the `button` channel! *Sweet*!

## Enforcing Named Autowiring

To see what I mean by *enforcing* named autowiring, back in
`config/packages/monolog.yaml`, rename the channel to `buttons` (with an "s"):

[[[ code('4fcc68a4af') ]]]

Back in the app, refresh, and... we see an error!

> Cannot autowire service "LoggerRemote": argument "$logger" of method
> "__construct()" has "#[Target('buttonLogger')]" but no such target exists.

Yes! Normally we don't celebrate errors, but this is a good one! We now have a
hard error when the named autowiring service cannot be found. This is what we
want!

Fix this by going back to `LoggerRemote` and updating the `#[Target]`
to `buttonsLogger` (with an "s"):

[[[ code('ec40ae76f2') ]]]

Refresh the app and we're back in business! Press "Mute" and pop into the
"Logs" profile panel. Yep, we're logging to our renamed `buttons` channel!

I love named autowiring, but also love that we can enforce it with the
`#[Target]` attribute.

Next: We're going to add a *new* button, but *this time*, we'll make it
*conditional*, so it only appears in certain environments.
