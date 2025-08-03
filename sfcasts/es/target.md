# Imponer el autocableado por nombre con Target

En el último capítulo, ¡añadimos el registro! Echa un vistazo al panel de perfil "Registros". ¿Notas esta pequeña etiqueta "app"? El registrador de Symfony, Monolog tiene el concepto de canales de registro, que son como categorías para tus mensajes de registro. El canal por defecto es "app", pero puedes crear otros.

## Añadir un nuevo canal de registro

Veamos si podemos añadir un nuevo canal llamado "botón". Abre`config/packages/monolog.yaml`. Bajo `channels`, añade uno nuevo llamado `button`:

[[[ code('3cae5681f0') ]]]

¡Perfecto! Cierra este archivo.

Queremos que `LoggerRemote` utilice este nuevo canal... pero... ¿cómo lo hacemos?

Estamos autocableando el servicio `LoggerInterface`. Eso nos da el registrador principal: el que registra en el canal "app". Para registrar el nuevo canal, tenemos que autoconectar otro servicio de registro.

Ve a tu terminal y ejecuta:

```terminal
bin/console debug:autowiring logger
```

El argumento `logger` filtra los resultados hasta los servicios con "logger" en el nombre. El primero es `LoggerInterface`: es el servicio que obtendrás si utilizas la autoconexión estándar, como hacemos actualmente.

Pero hay varios servicios de logger. Todos los demás tienen un argumento, incluido uno nuevo llamado `buttonLogger`. Para obtener este servicio, tenemos que utilizar la autocableado con nombre.

## Autocableado con nombre

En `LoggerRemote`, cambia el nombre del argumento a `$buttonLogger`:

[[[ code('f7df8f7e41') ]]]

Ahora, vuelve a nuestra aplicación... actualiza la página, y... pulsa el botón "Canal Arriba". Ya hemos visto suficiente Bob Esponja por hoy. Entra en el perfil de la última petición y comprueba el panel "Registros". ¡Qué bien! ¡Ahora estamos entrando en el canal `button`!

Esto funciona como se supone que debe funcionar, pero me gustaría hablar de un problema que puede ocurrir cuando se utiliza el autocableado con nombre. Imagina que, dentro de un año, cambiamos el nombre de esta variable por algo más sencillo, como `$logger`:

[[[ code('b9a0356414') ]]]

Volvemos a nuestra aplicación, actualizamos la página y pulsamos un botón. Parece que sigue funcionando, pero comprueba el panel de perfil "Registros". El mensaje está ahí, pero ha vuelto a cambiar sigilosamente el canal "app". ¡Cómo se atreve!

Cuando cambiamos el nombre del argumento, rompimos la autoconexión con nombre y volvimos a la autoconexión estándar. Puede que esto no sea muy problemático para el registro, pero imagina que algo como una base de datos o un servicio de caché se autocable de esta forma. Eso podría ser un problema... y en general estoy en contra de los problemas.

## Atributo de destino

Necesitamos una forma de imponer el autocableado con nombre. ¡Saluda al atributo `#[Target]`!

En `LoggerRemote`, añade `#[Target]` sobre el argumento `LoggerInterface $logger`. Dentro, fija el valor a `buttonLogger` - el nombre del argumento (sin el `$`) que vimos en el comando `debug:autowiring`:

[[[ code('71074ee51f') ]]]

Ahora bien, el nombre del argumento puede ser cualquier cosa, ¡así que sé creativo!

Vuelve a la aplicación, actualízala, pulsa "Subir volumen" y comprueba el panel del perfil "Registros". ¡Hemos vuelto al canal `button`! ¡Genial!

## Forzar el autocableado por nombre

Para ver a qué me refiero cuando hablo de imponer el autocableado por nombre, vuelve a`config/packages/monolog.yaml` y cambia el nombre del canal a `buttons` (con una "s"):

[[[ code('4fcc68a4af') ]]]

Vuelve a la aplicación, actualízala y... ¡veremos un error!

> No se puede autocablear el servicio "LoggerRemote": argumento "$logger" del método
> "__construct()" tiene "#[Target('buttonLogger')]" pero no existe tal target.

¡Sí! Normalmente no celebramos los errores, ¡pero éste es bueno! Ahora tenemos un error duro cuando no se puede encontrar el servicio de autocableado nombrado. ¡Esto es lo que queremos!

Arréglalo volviendo a `LoggerRemote` y actualizando el `#[Target]`a `buttonsLogger` (con una "s"):

[[[ code('ec40ae76f2') ]]]

Actualiza la aplicación y ¡ya estamos de vuelta! Pulsa "Silenciar" y entra en el panel de perfil "Registros". Sí, ¡estamos registrándonos en nuestro canal renombrado `buttons`!

Me encanta el autocableado con nombre, pero también me encanta que podamos imponerlo con el atributo`#[Target]`.

A continuación: Vamos a añadir un nuevo botón, pero esta vez lo haremos condicional, para que sólo aparezca en determinados entornos.
