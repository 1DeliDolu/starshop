# Introducción

¡Hola, amigos! Bienvenidos a un nuevo curso sobre los Atributos de Inyección de Dependencia. ¿Qué son los Atributos de Inyección de Dependencia? Bueno, aquí tienes una pequeña lección de historia:

## Breve historia de la inyección de dependencias en Symfony

Hace mucho tiempo, en una versión de Symfony muy, muy lejana, los servicios (que son objetos que funcionan) tenían que configurarse en archivos YAML o XML independientes. Los chicos guays lo llamaban "cablear un servicio". En estos archivos, creábamos un ID de servicio, hacíamos referencia a nuestra clase de servicio y, a continuación, hacíamos referencia a cualquier ID de servicio, parámetro o valor escalar que fuera necesario. Aunque esto funcionaba muy bien, era un poco engorroso. Cada vez que necesitábamos añadir, eliminar o modificar los argumentos de un servicio, teníamos que saltar a otro archivo de configuración y actualizarlo también. Seguro que podríamos encontrar una forma mejor de hacerlo, ¿verdad? ¡Seguro que sí!

En una versión posterior de Symfony, hace todavía mucho tiempo, añadieron algo llamado "autocableado". Esto nos permitía simplemente crear un objeto de servicio PHP, y cualquier otro servicio necesario se inyectaría automáticamente. Sólo teníamos que teclear la clase o interfaz del servicio. Aunque esto supuso un gran avance, aún teníamos que configurarlo en YAML o XML, incluso en escenarios ligeramente más avanzados (como argumentos escalares de un parámetro). Definitivamente redujo la cantidad de tiempo invertido en archivos de configuración, pero no lo eliminó.

## Todo mejoró con PHP 8

Entonces llegó PHP 8 y añadió los atributos nativos, metadatos que pueden añadirse a las propiedades de las clases, métodos, argumentos de métodos y mucho más. Esta fue la característica perfecta para permitirnos hacer toda la configuración directamente en nuestras clases de servicio. Con Symfony 7.1 - la versión que usaremos en este curso - hay una plétora de atributos DI que usar, y echaremos un vistazo a cada uno de ellos. Veremos que la edición de un archivo de configuración independiente sólo es necesaria en escenarios raros y superavanzados. En la mayoría de los casos, el archivo `services.yaml`, que se incluye en cada nueva aplicación Symfony, es todo lo que necesitaremos, y casi nunca cambiará. Mostraremos todos estos atributos en una pequeña y divertida aplicación para que puedas ver cómo funcionan.

## El problema

Este es el escenario: Tenemos un televisor inteligente, pero hemos perdido el mando a distancia. Lo hemos buscado por todas partes, pero no lo encontramos. Seguro que esos molestos gnomos del mando se lo llevaron para venderlo en "Gnomeslist"...

Hemos encontrado un mando de repuesto en Internet, pero está pendiente de entrega y tardará una eternidad en llegar. ¿Qué hacemos? Bueno, mientras buscábamos una solución en "RemoteOverflow.com", un usuario (que definitivamente no es un gnomo) nos habló de una API que existe para nuestra smart TV. Como desarrolladores web, podemos utilizar esa API para crear nuestro propio mando a distancia al que podamos acceder y utilizar en cualquier tableta conectada a nuestra red doméstica. Menos mal que los gnomos no nos robaron la tableta, ¿verdad? ¿Verdad?

## Instala la aplicación

Para codificar conmigo, descarga el código del curso de este vídeo, ábrelo en el IDE que prefieras y sigue los pasos de configuración del archivo `README.md`. Yo ya lo he hecho. Ahora podemos ir a nuestro terminal y ejecutar

```terminal
symfony serve -d
```

para ejecutar el servidor en segundo plano. Mantén pulsado "comando" y haz clic en la URL de aquí para abrir nuestra aplicación en el navegador, y... ¡ahí está nuestro mando a distancia! Hace todas las cosas habituales de un mando a distancia, como cambiar de canal, encender y apagar el televisor, y subir o bajar el volumen. Eso es todo por ahora. Si pasamos al código, es una aplicación Symfony 7.1 casi lista para usar.

## Recorrido por la aplicación

Tenemos un único controlador - `RemoteController` - y una única ruta - `home`. Este controlador se encarga de renderizar la interfaz de usuario y de los clics en los botones. Cuando se pulsa un botón, gestionamos la lógica de los distintos botones (representada por un `dump()`), añadimos el mensaje flash y redirigimos a la misma ruta. Si no está gestionando un clic de botón, renderiza este `index.html.twig`. Esa es nuestra plantilla remota.

En nuestro directorio `templates/`, echemos un vistazo rápido a `base.html.twig`. Proviene de una instalación estándar de Symfony. La interfaz de usuario está diseñada con CSS de Tailwind, pero estoy haciendo algo un poco diferente a lo que estás acostumbrado. En lugar de instalar un sistema de gestión de activos como AssetMapper o Webpack Encore, utilizo la CDN de CSS de Tailwind para simplificar las cosas. Esto inyecta un poco de JavaScript en tu página que lee todas las clases de Tailwind que estás utilizando en tu HTML. Luego construye un archivo CSS personalizado y lo inyecta, estilizando el HTML. Como puedes imaginar, esto es un poco lento y nunca debería utilizarse en producción. Para la creación de prototipos y nuestros propósitos, es bastante fácil de poner en marcha y funciona muy bien.

Ahora, si echamos un vistazo a `index.html.twig`, ésta es nuestra plantilla remota real. Es bastante estándar, y puedes ver las clases Tailwind que estamos utilizando. Aquí es donde estamos renderizando nuestro mensaje flash, si existe, y aquí están los botones reales. Están envueltos en un `<form>` en el que cada `<button>`envía el formulario con el nombre del botón en el que se ha hecho clic. Lo único que no es estándar es esta etiqueta `<twig:ux:icon ...>`. Se trata de una combinación de dos paquetes de terceros que forman parte de la Iniciativa Symfony UX.

### Iconos UX

En primer lugar, estamos utilizando [`symfony/ux-icons`](https://symfony.com/bundles/ux-icons/current/index.html). Este paquete te permite añadir archivos `.svg` a este directorio `assets/icons`. Ahora, puedes incrustar estos SVG en Twig utilizando el nombre del archivo (menos el `.svg`) como atributo `name` en esta etiqueta. También puedes añadir atributos adicionales como los que tenemos aquí: `height` `width` , `class`, etc. Éstos se añadirán al elemento `<svg>` incrustado. Este paquete también hace otras cosas increíbles, así que busca en Google ["Symfony UX Icons"](https://symfony.com/bundles/ux-icons/current/index.html) para saber más sobre él

### Componentes Twig

La otra parte de esta etiqueta `<twig:ux:icons ...>` es la propia etiqueta. Proviene del paquete [`symfony/ux-twig-component`](https://symfony.com/bundles/ux-twig-component/current/index.html). En esencia, es una etiqueta Twig `{{ include() }}` más avanzada, que te permite pasar atributos HTML como `class` a los componentes. Una característica opcional que proporciona es esta sintaxis HTML. Si estás familiarizado con los componentes de frameworks frontales como Vue.js, ésta es básicamente la versión Twig. El paquete UX Icon se conecta a Twig Components y nos proporciona un práctico componente `UX:Icon` que podemos representar con `<twig:ux:icon...`. Eso puede hacer que nuestra plantilla sea más fácil de leer. Para saber más sobre los componentes Twig y todas las cosas interesantes que pueden hacer, busca ["Symfony Twig Components"](https://symfony.com/bundles/ux-twig-component/current/index.html) y consulta la documentación

A continuación: Empecemos a refactorizar nuestra aplicación para utilizar atributos de inyección de dependencia.
