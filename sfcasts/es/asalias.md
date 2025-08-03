# Asignar un alias a una interfaz con AsAlias

¡Es hora de añadir una nueva función! Quiero añadir un registro de las pulsaciones de los botones para que podamos controlar lo que hacen nuestros esbirros

En `ButtonRemote`, podríamos inyectar el servicio logger y registrar las pulsaciones de botón aquí mismo. Pero técnicamente... esto viola algo llamado "principio de responsabilidad única". Es una forma elegante de decir que una clase sólo debe hacer una cosa. Ahora mismo, esta clase se encarga de pulsar botones. Añadiendo aquí el registro, haría dos cosas. Y eso probablemente esté bien, ¡pero desafiémonos a nosotros mismos!

## Patrón Decorador

En su lugar, utilizaremos un patrón de diseño llamado "Decorador" creando una nueva clase que envuelva, o "decore", `ButtonRemote`.

En `src/Remote/`, crea una nueva clase PHP llamada `LoggerRemote`. Este es nuestro "decorador" y necesita los mismos métodos que la clase a la que decora. 
Copia los dos métodos de `ButtonRemote`, pégalos aquí y quítales las tripas. Añade un constructor, e inyecta el servicio logger con`private LoggerInterface` (el de `Psr\Log`) `$logger`. Ahora, inyecta el objeto que estamos decorando: `private ButtonRemote $inner`. Me gusta utilizar `$inner`como nombre del parámetro al crear decoradores.

[[[ code('0038383af8') ]]]

En cada método, primero, remite al objeto interno. En `press()`, escribe`$this->inner->press($name);` y en `buttons()`, `return $this->inner->buttons()`:

[[[ code('366f79ea60') ]]]

Ahora vamos a añadir el registro. Antes de la prensa interna, añade`$this->logger->info('Pressing button {name}')` y añade una matriz de contexto con `'name' => $name`. Esto de las llaves es un minisistema de plantillas utilizado por Monolog, el logger de Symfony. Copia esto, pégalo debajo de la prensa interna y cambia "Pressing" por "Pressed":

[[[ code('f9c9f2e1f0') ]]]

¡Decorador listo!

Para utilizar realmente este decorador, en `RemoteController`, en lugar de inyectar `ButtonRemote`, inyecta `LoggerRemote`:

[[[ code('85a46f21ce') ]]]

¡Vamos a probarlo! De vuelta a nuestra aplicación, pulsa el botón de encendido y salta al perfilador de la última petición. Podemos ver que se sigue llamando a la lógica de `ButtonRemote`. Y si comprobamos el panel "Registros", ¡veremos los mensajes!

## Interfaz del Decorador

Las dos clases remotas tienen los mismos métodos, esto es señal de que podríamos utilizar una interfaz común. Crea una nueva clase en `src/Remote/` llamada `RemoteInterface`. Copia el stub del método `press()` de `LoggerRemote` y pégalo aquí. Haz lo mismo para `buttons()`:

[[[ code('f6e5b7302b') ]]]

A continuación, haz que ambas clases remotas implementen esta interfaz. En `ButtonRemote`, añade`implements RemoteInterface`:

[[[ code('5e6b55bb26') ]]]

... y haz lo mismo en `LoggerRemote`:

[[[ code('51875e7d20') ]]]

En el constructor de `LoggerRemote`, cambia `ButtonRemote` por `RemoteInterface`:

[[[ code('af721a5771') ]]]

No tenemos que hacer esto, pero ahora que tenemos una interfaz, es realmente lo mejor para teclear.

De vuelta en la aplicación, actualiza y... ¡Error!

> No se puede autoconectar el servicio `LoggerRemote`: el argumento `$inner` del método `__construct()`
> hace referencia a la interfaz `RemoteInterface` pero no existe tal servicio.

Esto ocurre cuando Symfony intenta autoconectar una interfaz pero existen múltiples implementaciones. Tenemos que decirle a Symfony cuál de nuestros dos servicios debe utilizar cuando escribimos `RemoteInterface`. ¡Y el error incluso nos da una pista!

> Quizá deberías ponerle un alias a esta interfaz con uno de estos servicios existentes:
> "ButtonRemote", "LoggerRemote".

Ah, tenemos que "aliasear" nuestra interfaz. Técnicamente, esto creará un nuevo servicio cuyo id es `App\Remote\RemoteInterface`, pero en realidad es sólo un alias -o un puntero- a uno de nuestros servicios remotos reales. 

## `#[AsAlias]`

Hazlo con, lo has adivinado, otro atributo: `#[AsAlias]`. En `ButtonRemote`, nuestra clase más interna, añade `#[AsAlias]`:

[[[ code('c4a17ec755') ]]]

Esto le dice a Symfony:

> Cuando necesites autocablear un `RemoteInterface`, utiliza `ButtonRemote`.

De vuelta en nuestra aplicación, actualiza y... ¡el error ha desaparecido! Pulsa "channel up" y comprueba el perfilador. Se sigue llamando a la lógica del botón y si comprobamos el panel "Registros", ahí están nuestros mensajes.

Abre `RemoteController`: seguimos inyectando una instancia concreta de nuestro servicio. Eso está bien, pero ahora podemos ser más elegantes y utilizar `RemoteInterface`:

[[[ code('f51056b5be') ]]]

De vuelta en la aplicación, pulsa "canal abajo" y comprueba el perfilador. La lógica del botón funciona, ¡pero nuestros registros han desaparecido!

Como hemos puesto el alias `RemoteInterface` en `ButtonRemote`, ¡Symfony no conoce nuestra decoración! Cuando ve la sugerencia de tipo `RemoteInterface`, inyecta `ButtonRemote`, no `LoggerRemote`.

Siguiente paso: Arreglemos esto utilizando la decoración del servicio y, por supuesto, ¡otro atributo!
