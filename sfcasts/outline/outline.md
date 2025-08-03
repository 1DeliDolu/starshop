# DI Attributes

## Introduction

- smart tv webapp
- lost smart tv remote
- tv has an api
- api is ugly, different, complex logic is required for each button
- https://symfony.com/doc/current/reference/attributes.html#dependency-injection
  - `#[Autowire]` already covered in SF7 EP2

## Command Pattern with AutowireLocator

- Previously called `#[TaggedLocator]`
- Create `ButtonInterface`
- Copy buttons from tutorial directory
- Use array with AutowireLocator to inject directly in constructor
- Not ideal, each time we add a button, we need to update the attribute array

## Simplify with AutoconfigureTag and AsTaggedItem

- add to ButtonInterface
- replace AutowireLocator array with interface name
- broken... container keyed by class name
- add AsTaggedItem to each button with service id
- working again!

## List Buttons with AutowireIterator

- Let's remove the need to update our template each time we add a button
- Previously called `#[TaggedIterator]`
- Need to set the `AutowireIterator::$indexAttribute` to `key`
- Buttons are now added/removed dynamically
- Use `AsTaggedItem::$priority` to adjust order
- Pressing buttons is now broken!

## Container and Iterator with ServiceCollectionInterface

- We could add two ButtonRemote constructor but there's a better way
- Switch back to `AutowireLocator` attribute
- Can remove the `indexAttribute: 'key'`
- `ServiceCollectionInterface` is both a container AND an iterator
- Utilize `array_keys(ServiceCollectionInterface::getProvidedServices())` to lazily list the buttons
- Added and utilized in Symfony 7.1

## Alias an Interface with AsAlias

- create `RemoteInterface` and have `ButtonRemote` implement
- use interface in controller
- ensure works as normal
- Add `LoggerRemote` and implement interface
- Broken - Symfony doesn't know what concrete object that implements interface to use
- Add AsAlias to ButtonRemote
- Logging not working yet...

## Decorate a Service with AsDecorator

- use AsDecorator on LoggerRemote
- Logging works!
- Mention `AutowireDecorated`

## Enforce Named Autowiring with Target

- add custom button logging channel
- show debug:autowiring output with buttonLogger
- use named autowiring to change argument to $buttonLogger
- show logging in correct channel
- rename custom channel to "remote"
- show how it falls back to app channel - not super important for logging but you can imagine this being a big problem with other services
- add Target to logger arg
- now works as expected
- rename channel again - to "buttons"
- hard error - but this is what we want!
- edit the Target argument
- works again!

## Enable Services in Specific Environments with When

- add a diagnostics button
- we only want available in development
- add When
- to show that it works, temporarily change When env to `prod` and show that it disappears
- Mention `Exclude`

## Lazy Services

- Add `ParentalControls` service to `App\Remote` namespace
- Inject `MailerInterface` into `ParentalControls`
- Create `volumeToHigh()` method that `dump('send volume alert email')`
- Inject `ParentalControls` into `VolumeUpButton`
- Add fudged `true` conditional to `press()` that calls `ParentalControls::volumeToHigh()`
- Demo that `volumeToHigh()` logic is being called
- Switch `true` to `false`
- Add `dump($this->parentalControls)`
- Show that `ParentalControls` is being instantiated even though it's not being used
- Add `#[Lazy]` to `ParentalControls`
- Refresh app and show error (including previous for hint about removing final)
- Remove `final` from `ParentalControls`
- Refresh app, press volume up and show that `ParentalControls` is not being instantiated
- It's a ghost!
- What if we don't "own" `ParentalControls`
- Move `#[Lazy]` to `VolumeUpButton` constructor argument
- Still is a ghost!
- Class: All instances of the class are lazy (can only use on services you "own")
- Property: Only that instance is lazy (can use for any service!)

## More Laziness Attributes

- What if using a 3rd party service that's final?
- The following all require your consumer code being "lazy-aware"
- Switch to `AutowireServiceClosure` for `ParentalControl` in `VolumeUpButton`
  - injects a `Closure` that returns a service when invoked
  - `private \Closure $parentalControls`
  - Add docblock `\Closure():ParentalControls`
  - switch condition to `true`
  - remove `dump($parentalControls)`
  - change invocation to `($this->parentalControls)()->volumeToHigh()`
  - IDE method autocompletion works (because of docblock)!
  - Demo!
- Switch to `AutowireCallable`
  - `Closure` that executes a method on a service (and returns the result)
  - Swap `AutowireServiceClosure` for `AutowireCallable`
  - Use `service: ParentalControls::class`
  - Add `method: 'volumeToHigh'` to `AutowireCallable`
  - NOT lazy by default, service is instantiated during constructor, add `lazy: true` to avoid
  - Change invocation to `($this->parentalControls)()` - calls the method directly, if not void, would have return value
  - Change docblock to `\Closure():void` (method return type)
  - No method autocompletion
  - Demo!
- For most cases, prefer `AutowireServiceClosure`:
  - lazy by default
  - more flexible (full service so can call multiple methods)
  - more IDE friendly (autocompletion, refactoring method name)
- Outro
  - YAML/XML service config isn't going away
  - Attributes are about improving the developer experience
  - More are added all the time!
    - Follow "living on the edge" symfony.com blog posts
    - Attribute Overview: https://symfony.com/doc/current/reference/attributes.html#dependency-injection
