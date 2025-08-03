# Patrón de comandos con AutowireLocator

¡Vamos a hacerlo! Si echamos un vistazo a nuestra aplicación, ésta es la interfaz de usuario de nuestro mando. Básicamente es un formulario, y cada botón envía el formulario. El atributo `name`de cada botón es único, y eso ayuda a nuestro controlador a determinar qué lógica del botón debe ejecutar. Cuando pulsamos el botón "Encendido", por ejemplo, vemos este mensaje flash (añadido por el controlador) que nos dice lo que ha pasado. Si pulsamos "Subir canal", "Bajar canal", etc., vemos los mismos mensajes correspondientes.

Así que esto es super sencillo. El formulario se limita a publicar en la misma página, gestionar la lógica del botón y luego redirigirnos de vuelta con el mensaje flash.

## Revisando la lógica del controlador

En nuestro código, abre `src/Controller`, busca `RemoteController` y... ¡allá vamos! Estamos comprobando si la petición es un `POST`, y como cada botón se presenta con un nombre, esta sentencia `switch()` lo coge de la petición. Cada botón está envuelto en un `case`, y cada `dump()` representa la lógica individual del botón. Si no se encuentra un botón, esto lanzará un 404. A continuación, añadimos el mensaje flash, manipulamos un poco la cadena para que el nombre del botón tenga un aspecto más agradable y redirigimos de nuevo a la misma ruta.

Por último, en la parte inferior, si la petición no es un `POST`, simplemente mostramos `index.html.twig`. Esa es nuestra plantilla remota. Cuando tienes una gran sentencia switch-case como ésta, suele ser una buena oportunidad para refactorizar, especialmente a medida que añadimos más botones y lógica. Una forma estupenda de hacerlo es con el patrón Comando. Si quieres profundizar más en este patrón, ¡consulta nuestro curso "Patrones de diseño"!

## Crear comandos

Bien, lo primero que vamos a hacer es crear algunos comandos, que representarán los botones y albergarán toda su lógica. En `src/`, vamos a crear un nuevo directorio para organizar mejor nuestro código. Lo llamaremos `Remote` y, dentro, crearemos otra carpeta llamada `Button`. Perfecto A continuación, necesitamos crear una nueva clase PHP para cada botón. Empezaremos creando una interfaz que implementará cada botón para que nuestro gestor de comandos pueda manejarlos de forma predecible. La llamaremos `ButtonInterface`. Dentro, escribiremos `public function press()`, que no tendrá argumentos y devolverá `void`.

[[[ code('7cdd99831b') ]]]

En el directorio `tutorial/`... ¡mira esto! ¡Todas las implementaciones de los botones están aquí y listas para funcionar! Sólo tenemos que copiar todos los archivos PHP y añadirlos al directorio `Button`. ¡Muy fácil! Si miramos `ChannelDownButton.php`, podemos ver que tiene implementado el método `press()` y el mismo `dump()`que vimos en nuestro controlador, junto con el mensaje del botón.

## Construyendo el Manejador de Comandos

Muy bien, ¡ya tenemos nuestros comandos! Ahora necesitamos un manejador de comandos que tome el nombre del botón y ejecute el comando correspondiente. Para ello, crearemos un objeto que actuará como nuestro manejador de comandos. En el directorio `Remote`, crea una nueva clase llamada `ButtonRemote`. Yo prefiero marcar las clases como `final` por defecto, y quitarlo sólo si es necesario ampliarlas. Esto no es necesario, así que siéntete libre de dejarlo desactivado.

En nuestra nueva clase, crea un método público - `press()`. Este tomará un argumento `string`, `$name`, que representa el nombre del botón. Este método no devuelve nada, así que utiliza `void` como tipo de retorno. Ahora podemos crear un constructor para este objeto, y dentro, añadir `private ContainerInterface $container`. Asegúrate de coger el de `PSR\Container`. Este contenedor almacenará nuestros objetos botón como valores-clave - siendo la clave el nombre del botón y el valor el objeto botón. En el método `press()`, utilizaremos `$this->container->get($name)` para recuperar la instancia `ButtonInterface` y llamaremos a `press()`.

## `#[AutowireLocator]`

Ahora mismo, llamar al método `press()` nos dará un error porque Symfony no sabe cómo cablear este contenedor. Para ayudar, utilizaremos el atributo de inyección de dependencia `#[AutowireLocator()]`. En versiones anteriores de Symfony, se llamaba `TaggedLocator`, pero cambió de nombre en Symfony 7.1 para ser más coherente con otros atributos. El primer argumento del atributo será una matriz con los nombres de los botones como claves y sus correspondientes nombres de clase como valores. Symfony los convertirá en las instancias reales de los botones cuando construya el contenedor.

Bien, vamos a añadir todos nuestros botones a este contenedor. El resto de nuestros botones tendrán un aspecto muy similar: `'channel-down' => ChannelDownButton::class`, `'volume-up' => VolumeUpButton::class`, y `'volume-down' => VolumeDownButton::class`. ¡Listo! ¡Nuestro controlador de comandos está listo! 

[[[ code('91a0f69297') ]]]

## Refactorización del controlador

Ahora tenemos que sustituir la gran sentencia switch-case de nuestro controlador por esto.

De vuelta en `RemoteController.php`, después de inyectar `Request`, vamos a inyectar `ButtonRemote`. Como el controlador es autocableado, Symfony lo inyectará automáticamente. Aquí abajo, copia esta línea para obtener el nombre del botón y pégala arriba. Debajo, escribe `$remote->press($button)`. Ahora podemos eliminar toda esta declaración `switch`, pero aún necesitamos resolver los casos en los que no se encuentra un botón. Copia esta línea de aquí, elimina por completo la sentencia switch y envuelve este método `press()` en un bloque try-catch. Mueve `$remote->press($button)`dentro de `try`, y debajo, esto hará `catch (NotFoundExceptionInterface)`. Pega nuestro código dentro y... ¡listo! Así, si `ContainerInterface::get()` no encuentra un comando para el nombre del botón, lanzará esta excepción. Por último, podemos añadir la excepción `previous` al 404 para una mejor depuración.

[[[ code('af27b2985d') ]]]

Nuestro controlador es ahora mucho más pequeño, así que vamos a probarlo en nuestra aplicación. Si pulsamos el botón "Encendido"... ¡"Encendido pulsado"! Si pulsamos el botón "Canal arriba"... ¡"Canal arriba pulsado"! Todo parece funcionar. Si comprobamos el perfilador, podemos ver que el mensaje `dump()` sigue ahí, y ahora procede de la implementación correcta del botón. ¡Genial!

Vale, esto tiene muy buena pinta, pero hay otra mejora que podemos hacer. Ahora mismo, cada vez que añadimos un nuevo botón, tenemos que actualizar el atributo `AutowireLocator`en nuestro `ButtonRemote`. Esto está bien, pero es un poco engorroso.

A continuación: Vamos a explorar una refactorización para eliminar este requisito.
