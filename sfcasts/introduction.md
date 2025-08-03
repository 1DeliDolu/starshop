# Introduction

Hey friends! Welcome to a brand new course that's all about *Dependency
Injection Attributes*. What *are* Dependency Injection Attributes? Well, here's
a little history lesson:

## A Brief History of Symfony Dependency Injection

A long time ago, in a Symfony version far, far away, *services* (which are
objects that do work) had to be configured in separate YAML or XML files. The
cool kids called it "wiring up a service". *In* these files, we'd create a
service ID, reference our service class, and *then* reference any service IDs,
parameters, or scalar values that were required. While this worked great, it
*was* a bit cumbersome. Anytime we needed to add, delete, or modify a service's
arguments, we'd have to jump into *another* configuration file and update *that*
as well. *Surely* we could find a better way to do that, right? You bet!

In a *subsequent* version of Symfony, *still* long ago, they added something
called "autowiring". This allowed us to just create a PHP service *object*, and
*any* other services required would be injected *automatically*. We just
needed to type-hint the service class or interface. While this was a *huge* step
forward, we *still* had to configure this in YAML or XML, even in only
*slightly* more advanced scenarios (like scalar arguments from a parameter). It
definitely *reduced* the amount of time spent in configuration files, but it
didn't *eliminate* it.

## Everything got better with PHP 8

*Then* PHP 8 came along and added native attributes, metadata that can be added
to class properties, methods, method arguments, and *more*. This was the perfect
feature to enable us to do *all* of the configuration right in our service
classes. With Symfony 7.1 - the version we'll use in this course - there's a
*plethora* of DI attributes to use, and we'll take a look at each of them. We'll
see that editing a separate configuration file is only necessary in rare, super
advanced scenarios. For the most part, the `services.yaml` file, which is
included in every new Symfony app, is all we'll ever need, and it will hardly
ever change. We'll showcase *all* of these attributes in a fun little app so you
can see how they work.

## The Problem

So here's the scenario: We have a smart TV, but we've lost the remote. We
searched *everywhere*, but it's nowhere to be found. I bet those pesky remote
gnomes took it to sell on "Gnomeslist"...

We found a replacement remote online, but it's backordered and it'll take
*forever* to get here. What do we do? Well, while we were looking for a solution
on "RemoteOverflow.com", a user (who is definitely *not* a gnome) told us about
an API that exists for our smart TV. As web developers, we can use that API to
create our *own* remote we can access and use on any tablet connected to our
home network. Thank goodness the gnomes didn't steal our tablet, right? Right..?

## Install the App

To code along with me, download the course code for this video, open it in your
IDE of choice, and follow the setup steps in the `README.md` file. I've already
done that. Now we can spin over to our terminal and run

```terminal
symfony serve -d
```

to run the server in the background. Hold "command" and click on the URL here to
open our app in the browser, and... *there's* our remote!
It does all the usual remote things like changing the channel, powering the TV
on and off, and increasing or decreasing the volume.
That's pretty much it for now. If we spin over
to the code, it's an almost out-of-the-box Symfony 7.1 app.

## Tour the App

We have a single controller - `RemoteController` - and single route - `home`.
This controller handles rendering the UI and button clicks. When a button is
clicked, we handle the different button logic (represented by a `dump()`), add
the flash message, and redirect to the same route. If it's *not* handling a
button click, it renders this `index.html.twig`. That's our remote template.

In our `templates/` directory, let's take a quick look at `base.html.twig`. This
comes from a standard Symfony install. The UI is styled using Tailwind CSS, but
*I'm* doing something a little different than you may be used to. Instead of
installing an asset management system like AssetMapper or Webpack Encore, I'm
using the Tailwind CSS CDN to keep things simple. This injects a bit of
JavaScript into your page that reads through all of the Tailwind classes you're
using in your HTML. Then it builds a custom CSS file and injects it, *styling*
the HTML. As you might imagine, this is kind of slow and should never be used in
production. For prototyping and *our* purposes, it's pretty easy to get going
and works great.

Now, if we take a look at `index.html.twig`, this is our *actual* remote
template. It's pretty standard, and you can see the Tailwind classes we're
using. This is where we're rendering our flash message, if one exists, and here
are the actual buttons. They're wrapped in a `<form>` where each `<button>`
*submits* the form with the name of the button that was clicked. The only thing
that's a little non-standard is this `<twig:ux:icon ...>` tag.
This is a combination of two third-party packages that are both part of the
*Symfony UX Initiative*.

### UX Icons

First, we're using [`symfony/ux-icons`](https://symfony.com/bundles/ux-icons/current/index.html).
This package enables you to add `.svg` files to this `assets/icons` directory.
Now, you can embed these SVGs in Twig using the filename (minus the `.svg`) as
the `name` attribute in this tag. You can also add *additional* attributes like
we have here: `height`, `width`, `class`, etc. These will be added to the
embedded `<svg>` element. This package does other awesome things too, so
google ["Symfony UX Icons"](https://symfony.com/bundles/ux-icons/current/index.html)
to learn more about it!

### Twig Components

The other part of this `<twig:ux:icons ...>` tag is the tag itself. This comes
from the [`symfony/ux-twig-component`](https://symfony.com/bundles/ux-twig-component/current/index.html)
package. At its core, it's basically a more advanced Twig `{{ include() }}` tag,
allowing you to pass HTML attributes like `class` to components. An *optional*
feature it provides is this HTML syntax. If you're familiar with components in
frontend frameworks like Vue.js, this is basically the *Twig* version. The UX
Icon package hooks into Twig Components and gives us a handy `UX:Icon` component
that we can render with `<twig:ux:icon...`. That can make our template easier to
read. To learn *more* about Twig components and all of the cool things they can
do, search for ["Symfony Twig Components"](https://symfony.com/bundles/ux-twig-component/current/index.html)
and check out the documentation!

Next: Let's start *refactoring* our app to use *dependency injection
attributes*.
