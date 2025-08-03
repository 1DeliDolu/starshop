# Más atributos de pereza

Lo bueno de los "servicios perezosos" es que no requieren cambios en tu código (siempre que los servicios no sean finales). Pero, ¿y si el servicio `ParentalControls`viviera dentro de un paquete de terceros y fuera final? ¡Difícil! Pero tenemos algunas opciones.

## `#[AutowireServiceClosure]`

Imagina que `ParentalControls` es final y vive en un paquete de terceros. En `VolumeUpButton`, sustituye `#[Lazy]` por `#[AutowireServiceClosure]` pasando`ParentalControls::class`:

[[[ code('b8be529458') ]]]

Esto inyectará un cierre que devuelve una instancia de `ParentalControls` cuando se invoca (y sólo se instanciará cuando se invoque).

Para ayudar a nuestro IDE, añade un docblock encima del constructor:`@param \Closure():ParentalControls $parentalControls`:

[[[ code('199911f50f') ]]]

Ahora, abajo en la sentencia `if` del método `press()`, cambia `false` por `true` para que siempre detectemos que el volumen es demasiado alto. Como `$parentalControls` es un cierre, tenemos que envolver `$this->parentalControls` entre llaves e invocarlo con `()` antes de llamar a `->volumeTooHigh()`:

[[[ code('5f6712c6b0') ]]]

¡Compruébalo! Como hemos añadido el docblock, nuestro IDE proporciona autocompletado y nos permite hacer clic (con CMD+clic) en el método `volumeTooHigh()`. ¡Genial!

Quita el `dump()`, gira hasta nuestra aplicación, actualiza y pulsa el botón "subir volumen". Salta al perfilador. Vemos que se está llamando a la lógica `volumeTooHigh()`. ¡Estupendo! El servicio `ParentalControls` sólo se instancia cuando se invoca el cierre, y sólo lo invocamos cuando es necesario.

## `#[AutowireCallable]`

Veamos otra forma de hacer lo mismo. En `VolumeUpButton`, sustituye `#[AutowireServiceClosure]` por `#[AutowireCallable]`. Mantén`ParentalControls::class` como primer argumento, pero anteponle `service`:

[[[ code('86344711fe') ]]]

`#[AutowireCallable]` también inyecta un cierre. Pero en lugar de devolver el objeto de servicio completo, instanciará el servicio, llamará a un único método y devolverá el resultado.

Hazlo multilínea para tener más espacio. Añade un segundo argumento:`method: 'volumeTooHigh'`:

[[[ code('13f5cff8d4') ]]]

Cuando Symfony instancie un servicio que utilice `#[AutowireCallable]`, por defecto, instanciará su servicio. ¡Es un castor ansioso! Para evitarlo, añade un tercer argumento: `lazy: true`:

[[[ code('32f8bbbb71') ]]]

Ahora, `ParentalControls` sólo se instanciará cuando se invoque el cierre.

En el docblock anterior, cambia el tipo de retorno del cierre a `void` para que coincida con el tipo de retorno de `volumeTooHigh()`:

[[[ code('621d54f47a') ]]]

Abajo, en `press()`, elimina la llamada a `->volumeTooHigh()`:

[[[ code('75fc23734b') ]]]

Ahora el cierre lo llama cuando se invoca.

Vuelve a la aplicación, actualízala, pulsa el botón "subir volumen" y salta al perfilador. Se sigue llamando al método `ParentalControls::volumeTooHigh()`. ¡Perfecto!

`#[AutowireCallable]` es ciertamente genial, pero para la mayoría de los casos, prefiero utilizar`#[AutowireServiceClosure]` porque:
* Es perezoso por defecto.
* Es más flexible porque devuelve el objeto de servicio completo.
* Y, con docblocks adecuados, obtenemos: autocompletado, navegación por métodos, soporte de refactorización y un mejor análisis estático con herramientas como PhpStan.

---

Bien equipo, ¡eso es todo por este curso! Pon un atributo `#[TimeForVacation]` en tu código y ¡relájate!

La configuración de servicios YAML no va a desaparecer del todo, pero estos atributos mejoran tu experiencia como desarrollador al mantener juntos tu código y la configuración de servicios.

Casi en cada nueva versión de Symfony se añaden más atributos. ¡Sigue el [blog de Symfony](https://symfony.com/blog) para estar al día! Mira esto, en Symfony 7.2, ¡hay un nuevo atributo `#[WhenNot]`! Es básicamente lo contrario del atributo `#[When]` del que hablamos antes. ¡Genial!

Consulta la sección "Inyección de dependencia" del documento [Descripción general de los atributos de Symfony](https://symfony.com/doc/current/reference/attributes.html#dependency-injection) para ver una lista de todos los atributos de inyección de dependencia disponibles actualmente y cómo funcionan.

¡Hasta la próxima! ¡Feliz programación!