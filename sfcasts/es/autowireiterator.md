# Lista de botones con AutowireIterator

Hemos refactorizado nuestra aplicación para utilizar el patrón Comando para ejecutar cada botón ¡Genial! Nuevo objetivo: hacer que los botones sean más dinámicos: a medida que añadamos nuevas clases de botones, me gustaría no tener que editar nuestra plantilla.

Empieza dentro de `ButtonRemote`. Necesitamos una forma de obtener una lista de todos los nombres de los botones: los índices de nuestro contenedor. Para ello, crea aquí un método `public` llamado `buttons()`, que devolverá un `array`. Éste será una matriz de cadenas: ¡nuestros nombres de botones!

[[[ code('15fc223848') ]]]

## `#[AutowireIterator]`

El minicontenedor es estupendo para obtener servicios individuales. Pero no puedes hacer un bucle sobre todos los servicios de botones que hay dentro. Para solucionarlo, cambia `#[AutowireLocator]` por `#[AutowireIterator]`. Esto le dice a Symfony que inyecte un iterable de nuestros servicios, por lo que éste ya no será un `ContainerInterface`. En su lugar, utiliza `iterable` y renombra`$container` a `$buttons` aquí... y aquí. ¡Estupendo!

[[[ code('2bae748fd6') ]]]

Ahora, abajo, haz un bucle sobre los botones:`foreach ($this->buttons as $name => $button)`. `$button` es el servicio real, pero vamos a ignorarlo por completo y sólo cogeremos el `$name`, y lo añadiremos a esta matriz `$buttons`. En la parte inferior, `return $buttons`.

[[[ code('4776e9ba11') ]]]

## Pasar botones a la plantilla

De vuelta en el controlador, ya estamos inyectando `ButtonRemote`, así que abajo, donde renderizamos la plantilla, pasamos una nueva variable `buttons` con `'buttons' => $remote->buttons()`:

[[[ code('7721d9e4ee') ]]]

Añade un `dd()` para ver qué devuelve:

[[[ code('f02e72eb70') ]]]

Vale, de vuelta al navegador, actualiza la página y... hm... no es exactamente lo que queremos. En lugar de una lista de números, queremos una lista de nombres de botones. Para solucionarlo, vuelve a `ButtonRemote`, busca `#[AutowireIterator]`. `#[AutowireLocator]`, el atributo que teníamos antes, utiliza automáticamente la propiedad `$index` de `#[AsTaggedItem]` para las claves de servicio. `#[AutowireIterator]` ¡no lo hace! Sólo nos da un iterable con claves enteras.

## `#[AutowireIterator]`'s `indexAttribute`

Para decirle que clave el iterable utilizando `#[AsTaggedItem]`'s `$index`, añade`indexAttribute` set a `key`:

[[[ code('86e2d6bb0a') ]]]

Ahora, cuando hagamos un bucle sobre `$this->buttons`, `$name` será `$index` que, en nuestro caso, es el nombre del botón.

En nuestro controlador, seguimos teniendo este `dd()`, así que volvemos a nuestra aplicación, actualizamos y... ¡ya está! ¡Ya tenemos los nombres de los botones! ¡Genial!

Elimina el `dd()`, luego abre `index.html.twig`.

[[[ code('28a4c0dff3') ]]]

## Renderizar botones dinámicamente

Aquí tenemos una lista de botones codificada. Añade un poco de espacio y luego`for button in buttons`:

[[[ code('f931ad08ce') ]]]

En la interfaz de usuario, probablemente hayas notado que el primer botón, el de "Encendido", tiene un aspecto diferente: es rojo y más grande. Para mantener ese estilo especial, añade un `if loop.first` aquí, y un `else` para el resto de los botones:

[[[ code('8d5ab6ce3f') ]]]

Copia el código del primer botón y pégalo aquí. En lugar de codificar "power" como valor del botón, utiliza la variable `button`. Lo mismo para el nombre del icono Twig:

[[[ code('1c78dcee5a') ]]]

Para el resto de botones, copia el código del segundo botón, pégalo y sustituye el atributo `value` del botón y el nombre del icono por la variable `button`:

[[[ code('52e554b3ae') ]]]

Bien. Celébralo borrando el resto de botones codificados.

¡Vamos a probarlo! Vuelve a nuestra aplicación y actualiza... hm... Está mostrando los botones, pero no están en el orden correcto. Queremos que éste esté arriba, así que... ¿qué hacemos?

## Ordenar los servicios con `AsTaggedItem::$priority`

Tenemos que imponer el orden de nuestros botones en el iterador. Para ello, abre `PowerButton`. `#[AsTaggedItem]` tiene un segundo argumento: `priority`.

Antes, con `#[AutowireLocator]`, esto no era importante porque sólo buscábamos servicios por su nombre. Pero ahora que sí nos importa el orden, añade`priority` y ponlo en, qué tal, `50`:

[[[ code('711fc15ae5') ]]]

Ahora vamos al botón "Subir canal" y añadimos una prioridad de `40`: 

[[[ code('1b22c31e7a') ]]]

Al botón "Canal Abajo", una prioridad de `30`:

[[[ code('7f3030ca72') ]]]

"Subir Volumen" una prioridad de `20`:

[[[ code('8819dd0d68') ]]]

y "Bajar volumen", una prioridad de `10`:

[[[ code('94117d8d29') ]]]

Cualquier botón sin prioridad asignada tiene una prioridad por defecto de `0`.

Vuelve a nuestra aplicación y actualízala... ¡todo bien! ¡Ya estamos de nuevo en marcha! Todos los botones se han añadido automáticamente y en el orden correcto.

Pero te habrás dado cuenta de que tenemos un gran problema. Pulsa cualquier botón y... ¡Error!

> Se ha intentado llamar a un método no definido "get" de la clase
> `RewindableGenerator`.

¿Eh?

Este `RewindableGenerator` es el objeto iterable que Symfony inyecta con `#[AutowireIterator]`. Podemos hacer un bucle sobre él, pero no tiene un método `get()`. ¡Buf!

A continuación, vamos a solucionarlo inyectando un objeto que sea a la vez un iterador de servicio y un localizador.
