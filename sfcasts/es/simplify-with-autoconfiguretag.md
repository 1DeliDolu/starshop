# Simplificar con AutoconfigureTag y AsTaggedItem

En el último capítulo, refactorizamos la gran sentencia `switch` de nuestro controlador a este pequeño try-catch. Nuestro control remoto funciona igual que antes, pero ahora utilizamos el patrón Comando. También utilizamos este atributo `AutowireLocator` para decirle a Symfony cómo conectar este contenedor. Esto está muy bien, pero ¿y si te dijera que podemos simplificarlo aún más?

## `#[AutoconfigureTag]`

Como todos nuestros botones implementan este atributo `ButtonInterface`, añadiremos nuestro segundo atributo de inyección de dependencia: `#[AutoconfigureTag]`:

[[[ code('032131c063') ]]]

Este atributo le dice a Symfony:

> ¡Eh, añade una etiqueta a cualquier servicio que implemente esta interfaz!

## Etiquetas de servicio

Una etiqueta no es más que una cadena conectada a un servicio, y por sí misma, en realidad no hace nada. Pero, ahora que nuestros servicios tienen esta etiqueta, podemos sustituir la asignación manual de servicios que utilizamos en el atributo`#[AutowireLocator]` de `ButtonRemote` por el nombre de la etiqueta:

[[[ code('5c5e7e0928') ]]]

Cuando utilices el atributo `#[AutoconfigureTag]`, puedes personalizar el nombre de la etiqueta. Si decides no hacerlo, se utilizará por defecto el nombre de la interfaz. Nos quedaremos con el nombre por defecto porque el nombre no es superimportante.

Bien, de vuelta en nuestro navegador, veamos si todo sigue funcionando. Pulsa un botón y... error.

> Botón "power" no encontrado.

Desplázate hacia abajo para ver los detalles de la excepción. Ah, ¡aquí está! Esta excepción "no encontrado" fue lanzada desde el método `press()` porque no pudo encontrar "poder" en el contenedor `ButtonRemote`'s. Por suerte, los detalles de la excepción anterior nos muestran los ID de servicio que sí encontró: los nombres de clase completos de nuestros botones. Así que se están conectando, pero no por el ID que necesitamos.

## `#[AsTaggedItem]`

Queremos que los ID de servicio de nuestro contenedor sean los nombres slug de nuestros botones. Para ello, utilizaremos nuestro siguiente atributo de inyección de dependencia: `#[AsTaggedItem]`. Ponerlo en las implementaciones de `ButtonInterface` nos permite personalizar el ID de servicio.

Empieza con `ChannelDownButton`. Añade `#[AsTaggedItem]` a la clase... y para el primer argumento, que es el índice, escribiremos `channel-down`.

[[[ code('291ef9a2e5') ]]]

Haremos lo mismo en `ChannelUpButton`... `PowerButton`... `VolumeDownButton`... y `VolumeUpButton`.

[[[ code('5398795d6d') ]]]

[[[ code('415c444e7a') ]]]

[[[ code('a470d3c1c2') ]]]

[[[ code('d36901385b') ]]]

Muy bien, vuelve a tu navegador... actualiza la página... pulsa un botón, y... ¡funciona! Si miramos el perfilador, podemos ver que está volcando el mensaje correcto para el botón.

Así que ahora, siempre que queramos añadir un nuevo botón, sólo tenemos que crear la clase botón, hacer que implemente el `ButtonInterface`, y añadir el atributo `#[AsTaggedItem]` con un nombre de botón único.

Si queremos que esto aparezca en nuestra IU remota, aún tendremos que añadirlo a nuestra plantilla. Pero, ¿y si pudiéramos hacerlo aún mejor? ¿Y si no tuviéramos que editar este archivo cada vez que añadimos un nuevo botón? Para ello, necesitamos utilizar otro atributo de inyección de dependencia. Eso a continuación.
