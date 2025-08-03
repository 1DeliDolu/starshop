# Servicios perezosos

Es hora de hablar de una de mis características favoritas de Symfony: los servicios perezosos. Muchas veces, inyectas un servicio, pero sólo se utiliza bajo ciertas condiciones. Aquí tienes un ejemplo:

```php
public function calculateSomething(ExpensiveService $service)
{
    if ($this->isCached()) {
        return $this->getCachedValue();
    }

    return $service->doSomethingExpensive();
}
```

En este caso, la mayor parte del tiempo, el `ExpensiveService` no se utiliza. Pero, como está inyectado, siempre se instancia.

Un "servicio perezoso" se relaja en el sofá, comiendo patatas fritas, hasta que realmente se necesita.

## Crear un nuevo botón

¡Hagamos uno! Crea una nueva clase PHP en `src/Remote/` llamada`ParentalControls`. Esto nos enviará alertas cuando los niños hagan algo que saben perfectamente que no deben hacer, los muy granujas. Marca la clase como `final` y añade un constructor con: `private MailerInterface $mailer` para que podamos enviar las alertas por correo electrónico:

[[[ code('d4f9d598d9') ]]]

Añade un nuevo método público llamado `volumeTooHigh()` con un tipo de retorno `void`. Dentro, para representar el envío del correo electrónico, sólo tienes que escribir `dump('send volume alert email')`:

[[[ code('69225f1594') ]]]

A continuación, abre `VolumeUpButton`, añade un constructor aquí e inyecta`private ParentalControls $parentalControls`:

[[[ code('4ec52d9ac3') ]]]

En el método `press()`, imagina que estamos detectando cuando el volumen es demasiado alto. Añade una declaración `if (true)` (con un comentario para recordarnos lo que representa), y luego `$this->parentalControls->volumeTooHigh()`:

[[[ code('3ef8ad588b') ]]]

Vuelve a nuestra aplicación, actualízala, pulsa el botón "subir volumen" y comprueba el perfilador. ¡Podemos ver que nuestro servicio `ParentalControls` se está utilizando y funciona!

De vuelta en `VolumeUpButton`, cambia `true` por `false` para fingir que no detectamos un volumen alto. Debajo de la sentencia if, escribe `dump($this->parentalControls)`:

[[[ code('a87cfe375e') ]]]

Vuelve atrás, actualiza, pulsa "subir volumen" y comprueba el perfilador. Aunque no utilizamos `ParentalControls`, ¡seguía instanciado! También lo estaba el servicio mailer del que depende, el transporte mailer, etc. Se trata de una larga cadena de dependencias instanciadas pero no utilizadas

## `#[Lazy]` Atributo de clase

¿La solución? Haz de `ParentalControls` un servicio perezoso. Abre esa clase y añade el atributo `#[Lazy]`:

[[[ code('2dd0260632') ]]]

Volvemos a nuestra aplicación, la actualizamos, y... ¡un error!

> No se puede generar un proxy perezoso para el servicio `ParentalControls`.

Comprueba la excepción anterior para ver por qué:

> La clase `ParentalControls` es final.

Éste es un pequeño inconveniente de utilizar servicios perezosos: la clase no puede ser final. Veremos por qué en un segundo.

Abre `ParentalControls`, elimina final...

[[[ code('4ad8c9a44b') ]]]

y actualiza la aplicación. ¡Ya está!

Pulsa "subir volumen" y comprueba el perfilador.

## Proxies fantasma

¡Guau! ¿Qué es esto? ¿ `ParentalControlsGhost` con una cadena aleatoria detrás? Se llama "proxy fantasma" y lo genera Symfony. Extiende nuestra clase `ParentalControls`(por eso no puede ser final) y, hasta que no se utiliza realmente, no se instancia completamente: ¡es un fantasma! ¡Espeluznante!

Pero, ¿y si no fuéramos "propietarios" de `ParentalControls`? ¿Y si formara parte de un paquete de terceros? ¿Cómo podríamos hacerlo perezoso? No podemos editar el código del proveedor, pero se puede añadir el atributo `#[Lazy]` a un argumento para hacerlo perezoso según su uso.

## `#[Lazy]` Atributo Argumento

En `ParentalControls`, elimina `#[Lazy]`:

[[[ code('4ad8c9a44b') ]]]

y en `VolumeUpButton`, añade `#[Lazy]` encima del argumento `$parentalControls`:

[[[ code('37be2e208d') ]]]

En nuestra aplicación, actualiza, pulsa "subir volumen" y comprueba el perfilador. ¡Sigue siendo perezoso!

Cuando añades el atributo `#[Lazy]` a una clase, todas las instancias de ese servicio son perezosas. Cuando lo añades a un argumento, sólo es perezoso cuando se utiliza en ese contexto.

¿Y si existiera en un paquete de terceros y fuera final? ¿Nos quedamos sin suerte?

No Symfony tiene otros trucos -y atributos- en la manga para ayudarnos. ¡Veámoslos a continuación!
