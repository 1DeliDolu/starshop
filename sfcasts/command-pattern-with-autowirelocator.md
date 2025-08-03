# Command Pattern with AutowireLocator

Let's *do this*! If we take a look at our app, *this* is the UI for our remote.
It’s basically just a form, and each button *submits* the form. The `name`
attribute for each button is unique, and that helps our controller determine
which button's logic to execute. When we click the "Power" button, for example,
we see this flash message (added by the controller) that tells us what happened.
If we press "Channel Up", "Channel Down", and so on, we see the same
corresponding messages.

So this is *super* simple. The form just posts to the same page, handles the
button logic, and then redirects us back with the flash message.

## Reviewing the Controller Logic

Over in our code, open up `src/Controller`, find `RemoteController` and... here
we go! We’re checking to see if the request is a `POST`, and since each button
*submits* with a name, this `switch()` statement grabs that from the request.
Each button is wrapped in a `case`, and each `dump()` represents the button's
individual logic. If a button *isn't* found, this will throw a 404. *Then* we
add the flash message, do a bit of string manipulation to make the button name
look nicer, and redirect right back to the same route.

Finally, at the bottom, if the request *isn’t* a `POST`, we just
render `index.html.twig`. That's our remote template. When you have a big
switch-case statement like this, it's usually a good opportunity to refactor,
especially as we add more buttons and logic. A great way to do this is with the
Command pattern. If you'd like a more in-depth look at this pattern, check out
our "Design Patterns" course!

## Creating Commands

Okay, the first thing we’re going to do is create some commands, which will
represent the buttons and house all of their logic. In `src/`, let's create a
new directory to better organize our code. We’ll call it `Remote` and, inside,
create another folder called `Button`. Perfect! Next, we need create a new PHP
class for each button. We'll start by creating an *interface* that each button
will implement so our command handler can *predictably* handle them.
We’ll call it `ButtonInterface`. Inside, we’ll write `public function press()`,
which will have *no* arguments and return `void`.

[[[ code('7cdd99831b') ]]]

In the `tutorial/` directory... look at that! All of the button implementations
are here and ready to go! We just need to copy all of the PHP files and add them
to the `Button` directory. *Easy peasy*! If we look at `ChannelDownButton.php`,
we can see that it has the `press()` method implemented and the same `dump()`
that we saw in our controller, along with the button message.

## Building the Command Handler

All right, we have our commands! Now we need a *command handler* to take the
button name and execute the corresponding command. For this, we’ll create an
*object* to *act* as our command handler. In the `Remote` directory, create a
new class called `ButtonRemote`. I prefer to mark classes as `final` by default,
removing it only if extension is needed. This isn't required, so feel free to
leave that off.

In our new class, create a public method - `press()`. This will take a `string`
argument, `$name`, that represents the button name. This method doesn't return
anything, so use `void` as the return type. Now we can create a constructor for
this object, and inside, add `private ContainerInterface $container`. Make sure
you grab the one from `PSR\Container`. This container will store our button
objects as key-values - the *key* being the button name and the *value* being
the button object. In the `press()` method, we’ll
use `$this->container->get($name)` to *retrieve* the `ButtonInterface` instance
and call `press()`.

## `#[AutowireLocator]`

Right now, calling the `press()` method will give us an error because Symfony
doesn’t know how to wire up this container. To help out, we’ll use
the `#[AutowireLocator()]` dependency injection attribute. In *older* Symfony
versions, this was called `TaggedLocator`, but it was *renamed* in Symfony 7.1
to be more consistent with other attributes. The first argument on the attribute
will be an array with the button names as keys and their corresponding class
names as values. Symfony will convert these into the *actual* button instances
when building the container.

Okay, let's add all of our buttons to this container. The rest of our buttons
will look very
similar: `'channel-down' => ChannelDownButton::class`, `'volume-up' => VolumeUpButton::class`,
and `'volume-down' => VolumeDownButton::class`. Ta-da! Our command handler is
ready! 

[[[ code('91a0f69297') ]]]

## Refactoring the Controller

Now we need to replace the big switch-case statement in our controller
with *this*.

Back in `RemoteController.php`, after injecting the `Request`, let’s
inject `ButtonRemote`. Since the controller is *autowired*, Symfony will inject
this *automatically*. Down here, copy this line to get the button name and paste
it above. Below that, write `$remote->press($button)`. Now we can remove this
entire `switch` statement, but we *still* need to address cases where a button
*isn’t* found. Copy this line here, delete the switch statement entirely, and
wrap this `press()` method in a try-catch block. Move `$remote->press($button)`
into the `try`, and below, this will `catch (NotFoundExceptionInterface)`. Paste
our code inside and... done! So if `ContainerInterface::get()` doesn’t find a
command for the button name, it will throw this exception. Finally, we can add
the `previous` exception to the 404 for better debugging.

[[[ code('af27b2985d') ]]]

Our controller is *much* smaller now, so let’s test it in our app. If we press
the "Power" button... “Power pressed”! If we press the "Channel Up" button...
“Channel up pressed”! Everything seems to be working. If we check out the
profiler, we can see that the `dump()` message is still there, and now it's
coming from the correct button implementation. *Sweet*!

Okay, this looks great, but there’s *another* improvement we can make. Right
now, every time we add a new button, we need to update the `AutowireLocator`
attribute in our `ButtonRemote`. This is *fine*, but it's a bit cumbersome.

Next: Let's explore a refactor to *remove* this requirement.
