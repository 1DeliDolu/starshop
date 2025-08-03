# Contenedor e Iterador con ServiceCollectionInterface

En el capítulo anterior, hicimos que estos botones se listaran mediante programación. Pero al hacerlo, ¡rompimos la funcionalidad real de pulsar botones! Los niños se están poniendo inquietos: tenemos que arreglar esto.

En `ButtonRemote`, hay un par de formas de solucionarlo. La primera, que probablemente sea la más sencilla, consiste en inyectar dos argumentos: uno que sea un iterador de los servicios del botón y otro que sea un localizador, es decir, un minicontenedor con un método `get()` para obtener cada servicio. Eso funcionaría y es perfectamente válido. ¡Pero podemos hacerlo mejor!

## `ServiceCollectionInterface`

Podemos inyectar un objeto que sea a la vez un iterador y un localizador:`ServiceCollectionInterface`. Echémosle un vistazo. Esto es un `ServiceProviderInterface` (que es el localizador) y un `IteratorAggregate`(que es el iterador). Por si fuera poco, también es `Countable`.

De vuelta a `ButtonRemote`, tenemos que cambiar `AutowireIterator` por`AutowireLocator` para que Symfony inyecte `ServiceCollectionInterface`:

[[[ code('5ad7e20eb4') ]]]

Limpiaré aquí algunas importaciones no utilizadas y... bien.

De vuelta en nuestra app, refrescar y... Vale, seguimos listando los botones, así que es buena señal. Ahora, si pulsamos un botón... ¡parece que vuelve a funcionar! Entra en el perfilador para comprobar la petición `POST` y ver que se sigue llamando a la lógica adecuada del botón. ¡Genial!

## Pereza

Una de las grandes ventajas de un localizador de servicios es que es perezoso. Los servicios no se instancian hasta que llamamos a `get()` para obtenerlos. E incluso entonces, sólo se crea un único servicio, aunque nos volvamos locos y llamemos a `get()` para el mismo servicio un montón de veces.

Me encanta ser perezoso, pero tenemos un problema. Aquí abajo, en `buttons()`, estamos iterando sobre todos los botones. Esto está forzando la instanciación de todos los servicios de botones sólo para obtener sus `$name`'s. Como sólo nos interesan los nombres, ¡esto es un desperdicio!

## `ServiceCollectionInterface::getProvidedServices()`

`ServiceCollectionInterface` ¡al rescate! Los localizadores de servicios Symfony tienen un método especial llamado `getProvidedServices()`. Elimina todo este código y`dd($this->buttons->getProvidedServices())` para ver qué devuelve:

[[[ code('08be3abedf') ]]]

Vuelve a nuestra aplicación y actualízala. Esto parece casi idéntico al mapeo manual que utilizamos anteriormente con `#[AutowireLocator]`.

Queremos las claves de esta matriz. De vuelta aquí, devuelve `array_keys()` de`$this->buttons->getProvidedServices()`:

[[[ code('250cb67f36') ]]]

Vuelve a la app y... actualiza. Todo sigue funcionando y, entre bastidores, ya no estamos instanciando todos los servicios de botones.

¡Rendimiento ganado!

Para celebrarlo, ¡vamos a añadir un nuevo botón a nuestro mando a distancia!

## Añadir un botón de silencio

Crea una nueva clase PHP llamada `MuteButton` y haz que se implemente`ButtonInterface`. Pulsa `Ctrl+Enter` para generar el método `press()`. Dentro, escribe `dump('Pressed mute button')`. Ahora, añade `#[AsTaggedItem]` con un `$index`de `mute`. Deja la prioridad por defecto, `0`. Esto colocará este botón por debajo de los demás:

[[[ code('43d438a73c') ]]]

Sólo tenemos que hacer otra cosa. Cada botón tiene un icono SVG en`assets/icons` con el mismo nombre que el botón. Copia el archivo `mute.svg` de`tutorial/` y pégalo aquí.

¡Momento de la verdad! Vuelve a nuestra aplicación, actualízala y... ¡ahí está! Haz clic en él y comprueba el perfilador. ¡Funciona! Ahora podemos silenciar la tele cuando los niños estén viendo Barney. ¡Perfecto!

¡Eso es todo para esta refactorización! Añadir botones es sencillo y eficaz.

A continuación, vamos a añadir el registro a nuestro mando a distancia y a conocer nuestro siguiente atributo:`#[AsAlias]`.
