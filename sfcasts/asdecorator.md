# Decorate a Service with AsDecorator

In the last chapter, we used `#[AsAlias]` to alias `RemoteInterface` to
`ButtonRemote` so that when we type-hint `RemoteInterface`, it gives us the
`ButtonRemote` service. But, this broke our logging! We need to tell Symfony
to give us `LoggerRemote` instead, but to pass the `ButtonRemote` service *to*
`LoggerRemote`.

## `#[AsDecorator]`

Basically, we need to tell Symfony that `ButtonRemote` is being decorated by `LoggerRemote`.
To do this, in `LoggerRemote`, use another attribute: `#[AsDecorator]` passing
in the service it decorates: `ButtonRemote::class`:

[[[ code('7e95e51e12') ]]]

This tells Symfony:

> Hey, if *anything* asks for the `ButtonRemote` service, give them `LoggerRemote` instead.

Symfony essentially *swaps* the services and then makes `ButtonRemote` the "inner" service
to `LoggerRemote`. This solidifies the need for the `RemoteInterface` we
created earlier. If we tried to type-hint `ButtonRemote` directly, we'd
get a type error because Symfony would be trying to inject `LoggerRemote`.

## Service Decoration

So, follow me on this: we autowire `RemoteInterface`. That's aliased to `ButtonRemote`,
so Symfony tries to give us that. But *then*, thanks to `#[AsDecorator]`, it swaps
that out for `LoggerRemote`... but passes `ButtonRemote` *to* `LoggerRemote`. In
short, `AsDecorator` allows us to decorate an existing service with another.

Spin back to the app, refresh and... press "volume up". Check the "Logs" profiler
panel and... we're logging again!

## Multiple Decorators

Using `#[AsDecorator]` makes it super easy to add multiple decorators. Maybe we want
to add a rate limiting decorator to prevent the kids from mashing buttons. We'd just
need to create a `RateLimitingRemote` class that implements `RemoteInterface` and add
`#[AsDecorator(ButtonRemote::class)]`.

```php
#[AsDecorator(ButtonRemote::class)]
class RateLimitingRemote implements RemoteInterface
{
    public function __construct(
        private RateLimiter $rateLimiter,
        private RemoteInterface $inner,
    ) {
    }

    // ...
}
```

Next: We'll add a custom logging channel and explore "named autowiring"!
