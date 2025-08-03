# Decorar un servicio con AsDecorator

En el capítulo anterior, utilizamos `#[AsAlias]` para asignar un alias de `RemoteInterface` a`ButtonRemote`, de modo que cuando tecleamos `RemoteInterface`, nos da el servicio`ButtonRemote`. Pero, ¡esto rompió nuestro registro! Tenemos que decirle a Symfony que nos dé `LoggerRemote` en su lugar, pero que pase el servicio `ButtonRemote` a`LoggerRemote`.

## `#[AsDecorator]`

Básicamente, tenemos que decirle a Symfony que `ButtonRemote` está siendo decorado por `LoggerRemote`. Para ello, en `LoggerRemote`, utiliza otro atributo: `#[AsDecorator]` pasando el servicio que decora: `ButtonRemote::class`:

[[[ code('7e95e51e12') ]]]

Esto le dice a Symfony:

> Oye, si algo te pide el servicio `ButtonRemote`, dale `LoggerRemote` en su lugar.

En esencia, Symfony intercambia los servicios y convierte `ButtonRemote` en el servicio "interno" de `LoggerRemote`. Esto solidifica la necesidad del `RemoteInterface` que creamos antes. Si intentáramos inyectar directamente `ButtonRemote`, obtendríamos un error de tipo porque Symfony estaría intentando inyectar `LoggerRemote`.

## Decoración del servicio

Así que, sígueme en esto: autoconectamos `RemoteInterface`. Eso está aliasado a `ButtonRemote`, así que Symfony intenta darnos eso. Pero entonces, gracias a `#[AsDecorator]`, lo cambia por `LoggerRemote`... pero pasa `ButtonRemote` a `LoggerRemote`. En resumen, `AsDecorator` nos permite decorar un servicio existente con otro.

Vuelve a la aplicación, actualízala y... pulsa "subir volumen". Comprueba el panel del perfilador "Registros" y... ¡estamos registrando de nuevo!

## Decoradores múltiples

Utilizar `#[AsDecorator]` hace que sea superfácil añadir múltiples decoradores. Quizá queramos añadir un decorador de limitación de velocidad para evitar que los niños machaquen botones. Sólo tendríamos que crear una clase `RateLimitingRemote` que implemente `RemoteInterface` y añadir`#[AsDecorator(ButtonRemote::class)]`.

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

A continuación: Añadiremos un canal de registro personalizado y exploraremos el "autocableado con nombre"
