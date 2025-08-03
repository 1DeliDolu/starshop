# Habilitar Servicios en Entornos Específicos con Cuando

¿No sería genial tener un botón secreto especial en nuestro mando a distancia que pudiéramos utilizar para asegurarnos de que funciona correctamente? ¡Claro que sí! Vamos a añadir un nuevo y sigiloso botón "Diagnóstico" que sólo estará disponible en el entorno `dev`.

## Añadir un botón de diagnóstico

En `App\Remote\Button`, crea una nueva clase:`DiagnosticsButton`. Haz que implemente `ButtonInterface`... y mantén pulsados "control" + "intro" para añadir el método `press()`. Dentro, haremos `dump('Pressed diagnostics button.')`... y, al igual que antes, añadiremos`#[AsTaggedItem]` con `diagnostics` como índice:

[[[ code('f7353113ad') ]]]

Por último, copia el archivo `diagnostics.svg` del directorio `tutorial/` en `assets/icons/`.

Giramos sobre nuestra aplicación y refrescamos... ¡nuevo botón! Y si lo pulsamos... ¡hasta parece que funciona! ¡Somos unos desarrolladores de mandos a distancia impresionantes!

## `#[When]`

Nuestro nuevo botón se registra automáticamente en el contenedor de servicios, pero sólo lo queremos en el entorno `dev`. El atributo `#[When]` es perfecto para esto. De vuelta en `DiagnosticsButton`, añade `#[When]` con `dev` como argumento:

[[[ code('9e99eb0750') ]]]

Gracias a esto, la clase será completamente ignorada por el contenedor de servicios a menos que estemos en el entorno `dev`. Dirígete a él y actualízalo: sigue ahí. Tiene sentido: estamos en el entorno `dev`. Así que vamos a amañar esto un poco. Cambia el argumento `#[When]`de `dev` a `prod`, para que podamos ver que funciona:

[[[ code('6552d10d4a') ]]]

Actualiza de nuevo y... ¡boom! ¡El botón ha desaparecido! ¡Fantástico!

## `#[Exclude]`

Ahora que esto funciona, hablemos del primo de `#[When]`: `#[Exclude]`. Esto es como una gran señal de advertencia que le dice a Symfony que nunca jamás registre una clase específica como servicio en el contenedor de servicios. Ahora mismo, en `config/services.yaml`, esta sección `App/:` le dice a Symfony que autoaliste cada clase en el directorio `src/`. Dentro de `App/:`, tenemos esta clave `exclude`. Contiene una lista de rutas que Symfony debe ignorar y es la forma tradicional de excluir clases para que no se registren como servicios. Está bien, pero lo encuentro un poco tosco. Aquí es donde entra `#[Exclude]`.

En `MuteButton`, aquí arriba, añade `#[Exclude]`...

[[[ code('4f91191a4c') ]]]

luego vuelve a nuestra aplicación y actualízala. ¡El botón "Silenciar" ha desaparecido! Ha funcionado.

No será un atributo muy común en tu aplicación, pero ¡eh! ¡Este es el tutorial del atributo DI! ¡Así que puedes ver todas las cosas buenas!

A continuación: Hablemos de los servicios perezosos.
