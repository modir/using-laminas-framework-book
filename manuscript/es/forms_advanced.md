# Uso avanzado de formularios {#advanced-forms}

En un capítulo anterior aprendimos el uso básico de los formularios: que son
los formulario HTML, como definir un modelo de formulario y la representación
de formulario en Laminas Framework. En este capítulo, aprenderemos algunos
temas avanzados sobre formularios tales como elementos de seguridad (CAPTCHA
y CSRF).

Los componentes de Laminas discutidos en este capítulo son:

|--------------------------------|---------------------------------------------------------------|
| *componente*                   | *Descripción*                                                 |
|--------------------------------|---------------------------------------------------------------|
| `Laminas\Captcha`                 | Implementa varios algoritmos CAPTCHA.                         |
|--------------------------------|---------------------------------------------------------------|
| `Laminas\Form`                    | Contiene las clases básicas para los modelos de formulario.   |
|--------------------------------|---------------------------------------------------------------|
| `Laminas\Filter`                  | Contiene varias clases de filtro.                             |
|--------------------------------|---------------------------------------------------------------|
| `Laminas\Validator`               | Implementa varias clases de validación.                       |
|--------------------------------|---------------------------------------------------------------|
| `Laminas\InputFilter`             | Implementa un contenedor para filtros y validadores.          |
|--------------------------------|---------------------------------------------------------------|

## Elementos de seguridad para formularios

Consideraremos el uso de dos elementos de seguridad para formularios que
provee Laminas Framework: `Captcha` y `Csrf` (ambas clases pertenecen
al espacio de nombre `Laminas\Form\Element`). Agregando estos elementos a
nuestro modelo de formulario (e imprimiéndolos en una plantilla de vista),
haremos a nuestro formulario resistente a ataques que crackers.

### CAPTCHA

Un CAPTCHA (siglas de «Completely Automated Public Turing test to
tell Computers and Humans Apart») es una prueba usada en los sitios webs
para determinar si el usuario es un humano o un robot.

Existen varios tipos de CAPTCHA. El que es usado más ampliamente obliga al
usuario a escribir las letras de una imagen distorsionada que se muestra en
la página web (ver figura 11.1 en la que hay unos ejemplos).

![Figura 11.1. Ejemplos de CAPTCHA](../en/images/forms_advanced/captcha_types.png)

Una prueba CAPTCHA trabaja usando los siguientes algoritmos:

1. Una secuencia secreta de caracteres (palabra) se genera del lado del servidor.
2. La palabra secreta se guarda en la variable de sesión de PHP.
3. Una imagen distorsionada se genera en base a la palabra secreta.
   Luego, la imagen se muestra al usuario en la página web.
4. Se le pide al usuario que escriba los caracteres que se muestran en la imagen.
5. Si los caracteres escritos por el usuario son los mismos que la palabra
   secreta guardada en la sesión la prueba se considera pasada.

El objetivo de la prueba CAPTCHA es proteger nuestro formulario de ser llenado
y enviado por procesos automáticos (llamados robots). Usualmente, estos robots
envían mensajes no deseados a foros, roban las contraseñas en los
formularios de inicio de sesión del sitio o ejecutan algunas otras acciones
maliciosas.

I> La prueba CAPTCHA permite distinguir de manera fidedigna entre humanos
I> y robots, porque los humanos son capaces de reconocer fácilmente y
I> reproducir caracteres desde imágenes distorsionadas, mientras que los
I> robots no (con el estado de evolución actual de los algoritmos de vision
I> computarizada).

#### Tipos de CAPTCHA

En Laminas Framework, existen varios tipos de CAPTCHA disponibles, todos ellos
pertenecen al componente `Laminas\Captcha`:

 * *Dumb.* Este es un algoritmo CAPTCHA muy simple que requiere que el usuario
   del sitio escriba las letras de una palabra en orden inverso. Aquí, no
   consideraremos con detalle este tipo de CAPTCHA porque provee un nivel de
   protección demasiado bajo.

 * *Image.* Un algoritmo CAPTCHA que distorsiona una imagen añadiendo algo de
   ruido con puntos y líneas curvas (figura 11.1, a).

 * *Figlet.* Un CAPTCHA poco usual que usa el programa FIGlet en lugar de un
   algoritmo de distorsión de imágenes. FIGlet es un programa de software libre
   que genera la imagen CAPTCHA de muchas pequeñas letras ASCII (figura 11.1, b).

El componente `Laminas\Captcha` provee una interfaz unificada para todos los tipos
de CAPTCHA, la interfaz `AdapterInterface`. La clase base `AbstractAdapter`
implementa esta interfaz y todos los otros algoritmos CAPTCHA se derivan
de la clase adaptadora abstracta [^adapter]. El diagrama de herencia de clase
se muestra en la figura 11.2 más abajo.

![Figura 11.2. Clase adaptadora CAPTCHA](../en/images/forms_advanced/captcha_adapters.png)

[^adapter]: El *adaptador* es una patrón de diseño que traduce una interfaz para
            una clase dentro de una interfaz compatible, que ayuda a dos o varias
            interfaces incompatibles a trabajar juntas. Generalmente, los algoritmos
            CAPTCHA tienen diferentes métodos públicos, pero como todo ellos
            implementan la interfaz `AbstractAdapter` se puede usar cualquier
            algoritmo de una misma manera, es decir, llamando al método apropiado
            por medio de la interfaz base.

Como podemos ver en la figura 11.2, existe otra clase base para todos los tipos
de CAPTCHA que utilizan palabras secretas de caracteres: la clase
`AbastractWord`. Esta clase base provee métodos para generar secuencias aleatorias
de caracteres y para ajustar la opciones de generación de palabras.

#### El elemento CAPTCHA para formularios y el ayudante de vista

Laminas provee una clase de elemento de formulario dedicada y una clase ayudante de
vista que nos permite usar campos CAPTCHA en nuestros formularios.

Para añadir un campo CAPTCHA a un modelo de formulario usamos la clase `Captcha`
que pertenece al componente `Laminas\Form` y vive en el espacio de nombres
`Laminas\Form\Element`.

La clase de elemento `Captcha` se puede usar con cualquier algoritmo CAPTCHA
del componente `Laminas\Captcha` (listados en la sección anterior). Por
esta razón, la clase de elemento tiene el método `setCaptcha()` que toma una
instancia de una clase que implementa la interfaz `Laminas\Captcha\AdapterInterface`
o una arreglo que contiene la configuración para el CAPTCHA [^array]. Con el
método `setCaptcha()` podemos asociar el tipo de CAPTCHA deseado al elemento.

[^array]: En el último caso (arreglo de configuración), el algoritmo del CAPTCHA
          será instanciado automáticamente e inicializado por la clase fábrica
          `Laminas\Captcha\Factory`.

Agregamos el elemento `Captcha` al modelo de formulario de la manera acostumbrada,
con el método `add()` que provee la clase base `Laminas\Form\Form`. Como sabemos,
podemos pasarle una instancia de la clase `Laminas\Form\Element\Captcha` o un
arreglo especificando las opciones de configuración para un determinado algoritmo
de CAPTCHA (en este caso, el elemento y su algoritmo CAPTCHA asociado serán
automáticamente instanciados y configurados por la clase fábrica).

El código de ejemplo de arriba muestra como usar el método `add()` (pasando un
arreglo de configuración). Preferimos este método porque se necesita menos código que
escribir. Además, se supone que se está llamando este código dentro del método protegido
`addElements()` del modelo del formulario:

~~~php
<?php
// Add the CAPTCHA field to the form model
$this->add([
  'type'  => 'captcha',
  'name' => 'captcha',
  'options' => [
    'label' => 'Human check',
    'captcha' => [
      'class' => '<captcha_class_name>', //
      // Certain-class-specific options follow here ...
    ],
  ],
]);
~~~

En el ejemplo de arriba llamamos al método `add()` que provee la clase base
`Form` y le pasamos un arreglo que describe al elemento que se insertará (línea 3):

 * La llave `type` del arreglo (línea 4), como es usual, puede ser el nombre completo
   de la clase (`Laminas\Form\Element\Captcha`) o su alias («captcha»).
 * La llave `name` (línea 5) es el valor para el atributo «name» del campo del
   formulario HTML.
 * La llave `options` contiene el algoritmo CAPTCHA asociado.
   La llave `class` (línea 9) puede contener el nombre completo de la clase CAPTCHA
   (ejemplo, `Laminas\Captcha\Image`) o su alias (por ejemplo, «Image»). Otras opciones
   específicas de cada adaptador se pueden agregar a la llave. Mostraremos como
   hacer esto un poco más tarde.

Para generar el código HTML para el elemento podemos usar la clase ayudante de
vista (que pertenece al espacio de nombres `Laminas\Form\View\Helper`). Pero, como
aprendimos en el capítulo previo generalmente usamos el ayudante de vista
genérico, de la manera que se muestra más abajo:

~~~text
<?= $this->formElement($form->get('captcha')); ?>
~~~

Aquí estamos suponiendo que se llama al ayudante de vista dentro de la plantilla
de vista.

A continuación daremos dos ejemplos que ilustran como usar tipos de CAPTCHA
diferentes con Laminas: `Image` y `Figlet`. Mostraremos como agregar el campo CAPTCHA
al formulario de contacto que usamos como ejemplo en capítulos anteriores.

#### Ejemplo 1: Agregar la Imagen CAPTCHA al ContactForm

W> La imagen CAPTCHA requiere tener instalada la extensión de PHP GD con
W> soporte para PNG y tipo de letra FT.

Para agregar la clase CAPTCHA `Image` al modelo de formulario llamamos al
método de formulario `add()` de la siguiente manera:

~~~php
<?php
namespace Application\Form;
// ...

class ContactForm extends Form
{
    // ...
    protected function addElements()
    {
        // ...

        // Add the CAPTCHA field
        $this->add([
            'type'  => 'captcha',
            'name' => 'captcha',
            'attributes' => [
            ],
            'options' => [
                'label' => 'Human check',
                'captcha' => [
                    'class' => 'Image',
                    'imgDir' => 'public/img/captcha',
                    'suffix' => '.png',
                    'imgUrl' => '/img/captcha/',
                    'imgAlt' => 'CAPTCHA Image',
                    'font'   => './data/font/thorne_shaded.ttf',
                    'fsize'  => 24,
                    'width'  => 350,
                    'height' => 100,
                    'expiration' => 600,
                    'dotNoiseLevel' => 40,
                    'lineNoiseLevel' => 3
                ],
            ],
        ]);
    }
}
~~~

Arriba, la llave `captcha` del arreglo de configuración contiene (línea 20)
los siguientes parámetros de configuración para el algoritmo de CAPTCHA de la
clase `Image` que esta asociado al elemento del formulario:

 * El parámetro `class` (línea 21) debe ser el nombre completo de la clase
   adaptadora CAPTCHA (`\Laminas\Captcha\Image`) o su alias (`Image`).

 * El parámetro `imgDir` (línea 22) debe ser la ruta a la carpeta donde se guardan
   las imágenes distorsionadas generadas (en este ejemplo, guardaremos las imágenes
   en la carpeta *APP_DIR/public/img/captcha*).

 * El parámetro `suffix` (línea 23) define la extensión para el archivo de imagen
   generado (en este caso «.png»).

 * El parámetro `imgUrl` (línea 24) define la parte base de la URL para abrir
   en un navegador web las imágenes CAPTCHA generadas. En este ejemplo, los
   visitantes del sitio serán capaces de acceder a las imágenes CAPTCHA usando
   URLs como «http://localhost/img/captcha/&lt;ID&gt;», donde ID es un identificador
   único para determinada imagen.

 * El parámetro `imgAlt` (línea 25) es un texto alternativo opcional que se muestra
   si la imagen CAPTCHA no se puede cargar en el navegador web (se trata del atributo
   «alt» de la etiqueta `<img>`).

 * El parámetro `font` (línea 26) es la ruta al archivo de tipografía. Podemos
   descargar un tipo de letra gratuitamente por ejemplo de [aquí](http://www.1001freefonts.com/).
   En este ejemplo, usamos el tipo de letra *Thorne Shaded* que luego que se
   descarga se coloca en el archivo *APP_DIR/data/font/thorne_shaded.ttf*.

  * La llave `fsize` (línea 27) es un número entero positivo que define el
    tamaño de la letra.

 * Los parámetros `width` (línea 28) y `height` (línea 29) definen respectivamente
   el ancho y el alto em pixeles de la imagen generada.

 * El parámetro `expiration` (línea 30) define el periodo de expiración en segundos
   de las imágenes CAPTCHA. Una vez que la imagen expira se remueve del disco.

 * El parámetro `dotNoiseLevel` (línea 31) y el parámetro and `lineNoiseLevel`
   (línea 32) definen las opciones de generación de la imagen, respectivamente,
   nivel de ruido y nivel de ruido de la linea.

Para imprimir el campo CAPTCHA, agregamos las siguientes líneas a nuestro archivo
de plantilla de vista *contact-us.phtml*:

~~~php
<div class="form-group">
  <?= $this->formLabel($form->get('captcha')); ?>
  <?= $this->formElement($form->get('captcha')); ?>
  <?= $this->formElementErrors($form->get('captcha')); ?>
  <p class="help-block">Enter the letters above as you see them.</p>
</div>
~~~

Finalmente, creamos la carpeta *APP_DIR/public/img/captcha* que guardará las
imágenes CAPTCHA generadas. Ajustamos los permisos de la carpeta para hacer
que el servidor web Apache pueda escribir en ella. En GNU/Linux, Ubuntu por ejemplo,
esto se consigue generalmente con los siguientes comandos de consola (debemos
reemplazar el comodín `APP_DIR` con el nombre de la carpeta real de nuestra
aplicación):

`mkdir APP_DIR/public/img/captcha`

`chown -R www-data:www-data APP_DIR`

`chmod -R 775 APP_DIR`

Arriba, el comando `mkdir` crea la carpeta y los comandos `chown` y `chmod`
asignan al usuario Apache como dueño de la carpeta y permite al servidor web
escribir en la carpeta, respectivamente.

Ahora, si abrimos la página «http://localhost/contactus» en nuestro navegador
web, la imagen CAPTCHA será generada en base a una secuencia aleatoria de
de letras y dígitos guardados en la sesión. Debemos ver algo como lo que
se muestra, más abajo, en la figura 11.3.

![Figura 11.3. Imagen CAPTCHA](../en/images/forms_advanced/image_captcha_page.png)

Cuando llenamos los campos del formulario y presionamos el botón *Submit*
las letras escritas en el campo *Human check* serán transferidos al servidor
como parte de la petición HTTP. Luego en la validación del formulario, la clase
`Laminas\Form\Element\Captcha` comparará las letras subidas con las que están
guardadas en la sesión de PHP. Si las letras son idénticas el formulario se
considera valido de lo contrario la validación del formulario falla.

Una vez que el renderizador de PHP procesa la plantilla de vista, las etiquetas
HTML para el elemento CAPTCHA que se generan son las siguientes:

~~~text
<div class="form-group">
  <label for="captcha">Human check</label>
  <img width="350" height="100" alt="CAPTCHA Image"
       src="/img/captcha/df344b37500dcbb0c4d32f7351a65574.png">
  <input name="captcha[id]" type="hidden"
         value="df344b37500dcbb0c4d32f7351a65574">
  <input name="captcha[input]" type="text">
  <p class="help-block">Enter the letters above as you see them.</p>
</div>
~~~

#### Ejemplo 2: Agregar un CAPTCHA FIGlet al ContactForm

Para usar el elemento CAPTCHA FIGlet en nuestro formulario reemplazamos la
definición del elemento del formulario del ejemplo anterior con el siguiente
código:

~~~php
<?php
// Add the CAPTCHA field
$this->add([
	'type'  => 'captcha',
	'name' => 'captcha',
	'attributes' => [
	],
	'options' => [
		'label' => 'Human check',
		'captcha' => [
			'class' => 'Figlet',
			'wordLen' => 6,
			'expiration' => 600,
		],
	],
]);
~~~

Arriba, la llave `captcha` del arreglo de configuración (ver línea 10) contiene
los siguiente parámetros para la configuración del algoritmo CAPTCHA `Figlet`
asociado al elemento del formulario:

 * El parámetro `class` (línea 11) debe ser el nombre completo de la clase
   adaptadora CAPTCHA (`\Laminas\Captcha\Figlet`) o su alias (`FIGlet`).

 * El parámetro `wordLen` (línea 12) define la longitud de la palabra secreta
   que se generará.

 * El parámetro `expiration` (línea 13) define el periodo de expiración en
   segundos del CAPTCHA.

Ahora, abrimos la página «http://localhost/contactus» en nuestro navegador web.
Una vez que lo hagamos debemos ver una página como la que se muestra más
abajo en la figura 11.4.

![Figura 11.4. FIGlet CAPTCHA](../en/images/forms_advanced/figlet_captcha_page.png)

Una vez que el renderizador de PHP procese la plantilla de vista, el código
HTML para el elemento CAPTCHA que se genera es como el que se muestra a
continuación.

~~~text
<div class="form-group">
  <label for="captcha">Human check</label>
    <pre>
 __   _    __   __   _    _      ___     _    _    __   __
| || | ||  \ \\/ // | \  / ||   / _ \\  | || | ||  \ \\/ //
| '--' ||   \ ` //  |  \/  ||  | / \ || | || | ||   \ ` //
| .--. ||    | ||   | .  . ||  | \_/ || | \\_/ ||    | ||
|_|| |_||    |_||   |_|\/|_||   \___//   \____//     |_||
`-`  `-`     `-`'   `-`  `-`    `---`     `---`      `-`'

</pre>
<input name="captcha[id]" type="hidden"
       value="b68b010eccc22e78969764461be62714">
<input name="captcha[input]" type="text">
<p class="help-block">Enter the letters above as you see them.</p>
</div>
~~~

### Prevención CSRF

La falsificación de peticiones en sitios cruzados generalmente conocido como CSRF,
en ingles Cross-site request forquery,
es un tipo de ataque de crackers que obliga al navegador del usuario
a transmitir una petición HTTP a un sitio arbitrario. A través de los
ataques CSRF un script malicioso es capaz de enviar comandos no autorizados
desde un usuario en el que el sitio web confía. Este tipo de ataques se ejecuta
generalmente en páginas que contienen formularios que envían datos sensibles
(por ejemplo, formularios de transferencia de dinero, carro de compras, etc.).

Para entender mejor como funciona este ataque, revisemos la figura 11.5.

![Figura 11.5. Un ejemplo de ataque CSRF](../en/images/forms_advanced/csrf_scheme.png)

La Figura 11.5 ilustra como funciona un ataque CSRF sobre un sitio web que
funciona como pasarela de pago:

1. Iniciamos sesión con nuestra cuenta en el sitio web de pasarela de pagos
   *https://payment.com*. Nótese que usamos una conexión protegida con SSL
   pero que ella no nos protege de este tipo de ataques.

2. Generalmente, seleccionamos la casilla de verificación «Remember me» del
   formulario para evitar escribir el nombre de usuario y contraseña tan a menudo.
   Una vez que iniciamos sesión con nuestra cuenta, el navegador web guarda
   la información de nuestra sesión una variable *cookie* en nuestra computadora.

3. En el sitio web de pasarela de pagos, usamos el formulario de pago
   *https://payment.com/moneytransfer.php* para comprar algún bien. Nótese
   que este formulario de pago será usado luego como una vulnerabilidad que
   permitirá ejecutar un ataque CSRF.

3. Luego usamos el mismo navegador web para visitar algún sitio web que nos guste.
   Supongamos que el sitio web contiene imágenes *http://coolpictures.com*.
   Desafortunadamente, este sitio web esta infectado con un script malicioso,
   disfrazado en una etiqueta HTML `<img src="image.php">`. Una ves que abrimos
   la página HTML en nuestro navegador web y se cargan todas las imágenes,
   también se ejecuta el script malicioso *image.php*.

4. El script malicioso revisa la variable *cookie*, y si está presente ejecuta
   un «cabalgamiento de sesión» con lo que puede actuar en nombre del usuario
   que inició sesión. Desde ese momento es capaz de enviar el formulario de
   pago desde el sitio web de pasarela de pago.

I> El ataque CSRF que se describe arriba es posible si el formulario web del
I> sitio de pasarela de pago no revisa el origen de la petición HTTP. La persona
I> que mantiene el sitio de pasarela de pago debe colocar más atención en
I> hacer a sus formularios más seguros.

Para prevenir ataques CSRF a un formulario, un formulario debe tener un *token*
especial:

1. Para un formulario determinado se genera una secuencia aleatoria de *bytes*
   (token) y se guarda del lado del servidor en los datos de sesión de PHP.

2. Agregamos un campo oculto al formulario y le colocamos como valor el *token*.

3. Una vez que el formulario es enviado por el usuario, se compara el valor
   oculto en el formulario con el *token* que se guardó del lado del
   servidor. Si ambos coinciden se consideran seguros los datos del formulario.

I> Si un usuario malicioso intenta atacar el sitio enviando un formulario,
I> no será capaz de colocar el *token* correcto en el formulario porque el
I> token no está guardado en las *cookies*.

#### Ejemplo: Agregar un elemento CSRF al formulario

En Laminas Framework, para agregar protección CSRF a nuestro modelo de formulario
usamos la clase de elemento de formulario `Laminas\Form\Element\Csrf`.

I> El elemento `Csrf` no tiene una representación visual (no lo veremos en la
I> pantalla).

Para insertar un elemento CSRF a nuestro formulario agregamos las siguientes
líneas dentro del método `addElements()`:

~~~php
// Add the CSRF field
$this->add([
  'type'  => 'csrf',
  'name' => 'csrf',
  'options' => [
    'csrf_options' => [
      'timeout' => 600
    ]
  ],
]);
~~~

Arriba, usamos el método `add()` de la clase `Form` (línea 2) al que le pasamos
un arreglo de configuración describiendo el elemento CSRF. El elemento será
instanciado automáticamente e inicialiazado por la fábrica.

En la línea 3, especificamos el nombre de la clase para el elemento CSRF. Este
puede ser el nombre completo de la clase (`Laminas\Form\Element\Csrf`) o su
alias («csrf»).

En la línea 4, colocamos el atributo «name» para el elemento. En este ejemplo,
usamos el nombre «csrf» pero podemos usar cualquier otro nombre que elijamos.

En la línea 6, dentro del arreglo `csrf_options`, especificamos las opciones
específicas para la clase `Laminas\Form\Element\Csrf`. Colocamos la opción
`timeout` en 600 (ver línea 7), que significa que la revisión CSRF expira
en 600 segundos (10 minutos) después de su creación.

Para generar el campo CSRF, en nuestro archivo de plantilla de vista *.phtml*
agregamos la siguiente línea:

~~~php
<?= $this->formElement($form->get('csrf')); ?>
~~~

Cuando el renderizador de PHP evalúa la plantilla de vista genera el código
HTML para el campo CSRF como se muestra abajo:

~~~text
<input type="hidden" name="csrf" value="1bc42bd0da4800fb55d16e81136fe177">
~~~

T> Como podemos ver del código HTML de más arriba, ahora el formulario contiene
T> un campo oculto con un *token* generado aleatoriamente. Como el script de
T> ataque no conoce este *token* no será capaz de enviar su valor correcto,
T> de esta manera prevenimos un ataque CSRF.

Q> **¿Que sucede si el elemento de validación falla?**
Q>
Q> Si durante la validación del formulario la revisión CSRF falla, el formulario
Q> se considera invalido y el usuario lo verá de nuevo para permitirle que
Q> corrija el error de entrada, pero no verá un mensaje de error para el elemento
Q> CSRF (no queremos que el cracker sepa con certeza que está mal con el formulario).

## Usar validación por grupos

En ocasiones puede ser útil desactivar temporalmente la validación de algunos
elementos del formulario. Podemos hacer esto con la característica llamada
*validación por grupos (validation groups)*.

I> Por defecto, todos los elementos del formulario se validan. La validación
I> por grupos permite desactivar la validación de determinados campos.

Por ejemplo, supongamos que implementamos un formulario llamado `PaymentForm`
que permite seleccionar un método de pago (tarjeta de crédito, transferencia
bancaria y efectivo). Si el usuario selecciona *tarjeta de crédito* necesitamos
que ingrese el número de tarjeta de crédito, si el usuario selecciona
*transferencia bancaria* debemos permitir que escriba el número de cuenta y,
finalmente, si se selecciona *efectivo* no es necesario agregar información.

T> Para este formulario debemos mostrar y ocultar dinámicamente algunos campos
T> dependiendo de la selección que haga el usuario, esto lo haremos en el
T> navegador web con JavaScript.

¿Como podemos validar un formulario como este en nuestra acción de controlador?
El problema es que algunos campos *dependen* de otros. El campo `card_number`
se necesita solo cuando el `payment_method` es una «tarjeta de crédito» de lo
contrario es opcional. Pasa lo mismo con el campo `bank_account`, se necesita
solo cuando el `payment_method` es una «transferencia bancaria».

Podemos gestionar este caso de manera elegante con un grupo de validación.
La clase `Form` provee el método `setValidationGroup()` que acepta una lista
de los campos que queremos validar y el resto de campos se suprimen y no se
validan.

~~~php
// First, we will validate the "payment_method" field.
$form->setValidationGroup(['payment_method']);
if ($form->isValid())
{
    $data = $form->getData();

    $paymentMethod = $data['payment_method'];

    // Next, validate the dependent fields
    if ($paymentMethod=='credit_card') {
        $form->setValidationGroup(['payment_method', 'card_number']);
    } else if ($paymentMethod=='bank_account') {
        $form->setValidationGroup(['payment_method', 'bank_account']);
    }

    if ($form->isValid()) {
        $data = $form->getData();

        // Do something with the data
        // ...
    }
}
~~~

T> Podemos ver este ejemplo en acción en la aplicación web de ejemplo *Form Demo*
T> que se distribuye junto a este libro. Solo necesitamos escribir la URL
T> «http://localhost/payment» en nuestro navegador.

## Implementar formulario multi-pasos {#multi-step-forms}

En esta sección mostraremos como implementar un formulario *multi-pasos* con
Laminas. Un formulario multi-pasos es un formulario que tiene muchos campos y que
se muestra en varios pasos. Para guardar el paso actual y los datos ingresados
por el usuario entre las peticiones a las páginas se utilizan las *sesiones*
de PHP.

I> Por ejemplo, el registro del usuario se puede ejecutar en varios pasos: en
I> el primer paso mostramos la página que permite ingresar el nombre de usuario
I> y contraseña, en el segundo paso mostramos la página donde el visitante
I> del sitio puede ingresar su información personal y en el tercer paso
I> ingresar la información de facturación.
I>
I> Otro ejemplo de formulario multi-pasos es un formulario de Encuesta. Este
I> formulario podría mostrar una pregunta y posibles variantes de su
I> respuesta. Este formulario tendría tantos paso como preguntas tiene la
I> encuesta.

En esta sección implementaremos el formulario *Registro de Usuario* que permite
recolectar información sobre el usuario que se está registrando.

T> Podemos ver este ejemplo funcionando completamente en la aplicación web de
T> ejemplo *Form Demo* que se distribuye junto a este libro.

### Habilitar sesiones

T> Si somos nuevos en las características de sesión de PHP podemos revisar
T> [Trabajar con sesiones](#session) antes de leer esta sección.

El soporte para sesiones se implementa en el componente `Laminas\Session`, por lo
que debemos instalarlo si aún no lo hemos hecho antes.

Primero, modificamos el archivo de configuración *APP_DIR/config/global.php*
de la siguiente manera:

~~~php
<?php
use Laminas\Session\Storage\SessionArrayStorage;
use Laminas\Session\Validator\RemoteAddr;
use Laminas\Session\Validator\HttpUserAgent;

return [
    // Session configuration.
    'session_config' => [
        // Session cookie will expire in 1 hour.
        'cookie_lifetime' => 60*60*1,
        // Store session data on server maximum for 30 days.
        'gc_maxlifetime'  => 60*60*24*30,
    ],
    // Session manager configuration.
    'session_manager' => [
        // Session validators (used for security).
        'validators' => [
            RemoteAddr::class,
            HttpUserAgent::class,
        ]
    ],
    // Session storage configuration.
    'session_storage' => [
        'type' => SessionArrayStorage::class
    ],

    // ...
];
~~~

Luego, agregamos las siguientes líneas al archivo *module.config.php* para
registrar el contenedor de sesión *UserRegistration*:

~~~php
<?php
return [
    // ...
    'session_containers' => [
        'UserRegistration'
    ],
];
~~~

¡Listo! Ahora podemos usar el contenedor de sesión en nuestro código. Luego,
implementaremos el modelo de formulario `RegistrationForm`.

### Agregar la clase RegistrationForm

El modelo de formulario `RegistrationForm` se usará para colectar los datos
sobre el usuario (correo electrónico, nombre completo, contraseña, información
personal e información de facturación). Agregaremos elementos a este formulario
en tres pedazos para permitir usarlo como un formulario de multiples-pasos.

Para agregar el modelo de formulario, creamos el archivo *RegistrationForm.php*
en la carpeta *Form* que está dentro de la carpeta fuente del módulo *Application*:

~~~php
<?php
namespace Application\Form;

use Laminas\Form\Form;
use Laminas\InputFilter\InputFilter;
use Application\Validator\PhoneValidator;

/**
 * This form is used to collect user registration data. This form is multi-step.
 * It determines which fields to create based on the $step argument you pass to
 * its constructor.
 */
class RegistrationForm extends Form
{
    /**
     * Constructor.
     */
    public function __construct($step)
    {
        // Check input.
        if (!is_int($step) || $step<1 || $step>3)
            throw new \Exception('Step is invalid');

        // Define form name
        parent::__construct('registration-form');

        // Set POST method for this form
        $this->setAttribute('method', 'post');

        $this->addElements($step);
        $this->addInputFilter($step);
    }

    /**
     * This method adds elements to form (input fields and submit button).
     */
    protected function addElements($step)
    {
        if ($step==1) {

            // Add "email" field
            $this->add([
                'type'  => 'text',
                'name' => 'email',
                'attributes' => [
                    'id' => 'email'
                ],
                'options' => [
                    'label' => 'Your E-mail',
                ],
            ]);

            // Add "full_name" field
            $this->add([
                'type'  => 'text',
                'name' => 'full_name',
                'attributes' => [
                    'id' => 'full_name'
                ],
                'options' => [
                    'label' => 'Full Name',
                ],
            ]);

            // Add "password" field
            $this->add([
                'type'  => 'password',
                'name' => 'password',
                'attributes' => [
                    'id' => 'password'
                ],
                'options' => [
                    'label' => 'Choose Password',
                ],
            ]);

            // Add "confirm_password" field
            $this->add([
                'type'  => 'password',
                'name' => 'confirm_password',
                'attributes' => [
                    'id' => 'confirm_password'
                ],
                'options' => [
                    'label' => 'Type Password Again',
                ],
            ]);

        } else if ($step==2) {

            // Add "phone" field
            $this->add([
                'type'  => 'text',
                'name' => 'phone',
                'attributes' => [
                    'id' => 'phone'
                ],
                'options' => [
                    'label' => 'Mobile Phone',
                ],
            ]);

            // Add "street_address" field
            $this->add([
                'type'  => 'text',
                'name' => 'street_address',
                'attributes' => [
                    'id' => 'street_address'
                ],
                'options' => [
                    'label' => 'Street address',
                ],
            ]);

            // Add "city" field
            $this->add([
                'type'  => 'text',
                'name' => 'city',
                'attributes' => [
                    'id' => 'city'
                ],
                'options' => [
                    'label' => 'City',
                ],
            ]);

            // Add "state" field
            $this->add([
                'type'  => 'text',
                'name' => 'state',
                'attributes' => [
                    'id' => 'state'
                ],
                'options' => [
                    'label' => 'State',
                ],
            ]);

            // Add "post_code" field
            $this->add([
                'type'  => 'text',
                'name' => 'post_code',
                'attributes' => [
                    'id' => 'post_code'
                ],
                'options' => [
                    'label' => 'Post Code',
                ],
            ]);

            // Add "country" field
            $this->add([
                'type'  => 'select',
                'name' => 'country',
                'attributes' => [
                    'id' => 'country',
                ],
                'options' => [
                    'label' => 'Country',
                    'empty_option' => '-- Please select --',
                    'value_options' => [
                        'US' => 'United States',
                        'CA' => 'Canada',
                        'BR' => 'Brazil',
                        'GB' => 'Great Britain',
                        'FR' => 'France',
                        'IT' => 'Italy',
                        'DE' => 'Germany',
                        'RU' => 'Russia',
                        'IN' => 'India',
                        'CN' => 'China',
                        'AU' => 'Australia',
                        'JP' => 'Japan'
                    ],
                ],
            ]);


        } else if ($step==3) {

            // Add "billing_plan" field
            $this->add([
                'type'  => 'select',
                'name' => 'billing_plan',
                'attributes' => [
                    'id' => 'billing_plan',
                ],
                'options' => [
                    'label' => 'Billing Plan',
                    'empty_option' => '-- Please select --',
                    'value_options' => [
                        'Free' => 'Free',
                        'Bronze' => 'Bronze',
                        'Silver' => 'Silver',
                        'Gold' => 'Gold',
                        'Platinum' => 'Platinum'
                    ],
                ],
            ]);

            // Add "payment_method" field
            $this->add([
                'type'  => 'select',
                'name' => 'payment_method',
                'attributes' => [
                    'id' => 'payment_method',
                ],
                'options' => [
                    'label' => 'Payment Method',
                    'empty_option' => '-- Please select --',
                    'value_options' => [
                        'Visa' => 'Visa',
                        'MasterCard' => 'Master Card',
                        'PayPal' => 'PayPal'
                    ],
                ],
            ]);
        }

        // Add the CSRF field
        $this->add([
            'type'  => 'csrf',
            'name' => 'csrf',
            'attributes' => [],
            'options' => [
                'csrf_options' => [
                     'timeout' => 600
                ]
            ],
        ]);

        // Add the submit button
        $this->add([
            'type'  => 'submit',
            'name' => 'submit',
            'attributes' => [
                'value' => 'Next Step',
                'id' => 'submitbutton',
            ],
        ]);
    }

    /**
     * This method creates input filter (used for form filtering/validation).
     */
    private function addInputFilter($step)
    {
        $inputFilter = new InputFilter();
        $this->setInputFilter($inputFilter);

        if ($step==1) {

            $inputFilter->add([
                    'name'     => 'email',
                    'required' => true,
                    'filters'  => [
                        ['name' => 'StringTrim'],
                    ],
                    'validators' => [
                        [
                            'name' => 'EmailAddress',
                            'options' => [
                                'allow' => \Laminas\Validator\Hostname::ALLOW_DNS,
                                'useMxCheck'    => false,
                            ],
                        ],
                    ],
                ]);

            $inputFilter->add([
                'name'     => 'full_name',
                'required' => true,
                'filters'  => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags'],
                    ['name' => 'StripNewlines'],
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 128
                        ],
                    ],
                ],
            ]);

            // Add input for "password" field
            $inputFilter->add([
                    'name'     => 'password',
                    'required' => true,
                    'filters'  => [
                    ],
                    'validators' => [
                        [
                            'name'    => 'StringLength',
                            'options' => [
                                'min' => 6,
                                'max' => 64
                            ],
                        ],
                    ],
                ]);

            // Add input for "confirm_password" field
            $inputFilter->add([
                    'name'     => 'confirm_password',
                    'required' => true,
                    'filters'  => [
                    ],
                    'validators' => [
                        [
                            'name'    => 'Identical',
                            'options' => [
                                'token' => 'password',
                            ],
                        ],
                    ],
                ]);

        } else if ($step==2) {

            $inputFilter->add([
                'name'     => 'phone',
                'required' => true,
                'filters'  => [
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 3,
                            'max' => 32
                        ],
                    ],
                    [
                        'name' => PhoneValidator::class,
                        'options' => [
                            'format' => PhoneValidator::PHONE_FORMAT_INTL
                        ]
                    ],
                ],
            ]);

            // Add input for "street_address" field
            $inputFilter->add([
                    'name'     => 'street_address',
                    'required' => true,
                    'filters'  => [
                        ['name' => 'StringTrim'],
                    ],
                    'validators' => [
                        ['name'=>'StringLength', 'options'=>['min'=>1, 'max'=>255]]
                    ],
                ]);

            // Add input for "city" field
            $inputFilter->add([
                    'name'     => 'city',
                    'required' => true,
                    'filters'  => [
                        ['name' => 'StringTrim'],
                    ],
                    'validators' => [
                        ['name'=>'StringLength', 'options'=>['min'=>1, 'max'=>255]]
                    ],
                ]);

            // Add input for "state" field
            $inputFilter->add([
                    'name'     => 'state',
                    'required' => true,
                    'filters'  => [
                        ['name' => 'StringTrim'],
                    ],
                    'validators' => [
                        ['name'=>'StringLength', 'options'=>['min'=>1, 'max'=>32]]
                    ],
                ]);

            // Add input for "post_code" field
            $inputFilter->add([
                    'name'     => 'post_code',
                    'required' => true,
                    'filters'  => [
                    ],
                    'validators' => [
                        ['name' => 'IsInt'],
                        ['name'=>'Between', 'options'=>['min'=>0, 'max'=>999999]]
                    ],
                ]);

            // Add input for "country" field
            $inputFilter->add([
                    'name'     => 'country',
                    'required' => false,
                    'filters'  => [
                        ['name' => 'Alpha'],
                        ['name' => 'StringTrim'],
                        ['name' => 'StringToUpper'],
                    ],
                    'validators' => [
                        ['name'=>'StringLength', 'options'=>['min'=>2, 'max'=>2]]
                    ],
                ]);

        } else if ($step==3) {

            // Add input for "billing_plan" field
            $inputFilter->add([
                    'name'     => 'billing_plan',
                    'required' => true,
                    'filters'  => [
                    ],
                    'validators' => [
                        [
                            'name' => 'InArray',
                            'options' => [
                                'haystack'=>[
                                    'Free',
                                    'Bronze',
                                    'Silver',
                                    'Gold',
                                    'Platinum'
                                ]
                            ]
                        ]
                    ],
                ]);

            // Add input for "payment_method" field
            $inputFilter->add([
                    'name'     => 'payment_method',
                    'required' => true,
                    'filters'  => [
                    ],
                    'validators' => [
                        [
                            'name' => 'InArray',
                            'options' => [
                                'haystack'=>[
                                    'PayPal',
                                    'Visa',
                                    'MasterCard',
                                ]
                            ]
                        ]
                    ],
                ]);
        }
    }
}
~~~

Como podemos ver en el código de arriba, la clase `RegistrationForm` es un
modelo de formulario usual pero acepta un argumento `$step` en su constructor que
permite especificar cuales elementos del formulario usar en el paso actual.

### Agregar la clase RegistrationController

Luego, agregaremos la clase controladora `RegistrationController`. Para hacer
esto, creamos el archivo *RegistrationController.php* dentro de la carpeta
*Controller* y agregamos las siguientes líneas de código:

~~~php
<?php
namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Application\Form\RegistrationForm;
use Laminas\Session\Container;

/**
 * This is the controller class displaying a page with the User Registration form.
 * User registration has several steps, so we display different form elements on
 * each step. We use session container to remember user's choices on the previous
 * steps.
 */
class RegistrationController extends AbstractActionController
{
    /**
     * Session container.
     * @var Laminas\Session\Container
     */
    private $sessionContainer;

    /**
     * Constructor. Its goal is to inject dependencies into controller.
     */
    public function __construct($sessionContainer)
    {
        $this->sessionContainer = $sessionContainer;
    }

    /**
     * This is the default "index" action of the controller. It displays the
     * User Registration page.
     */
    public function indexAction()
    {
        // Determine the current step.
        $step = 1;
        if (isset($this->sessionContainer->step)) {
            $step = $this->sessionContainer->step;
        }

        // Ensure the step is correct (between 1 and 3).
        if ($step<1 || $step>3)
            $step = 1;

        if ($step==1) {
            // Init user choices.
            $this->sessionContainer->userChoices = [];
        }

        $form = new RegistrationForm($step);

        // Check if user has submitted the form
        if($this->getRequest()->isPost()) {

            // Fill in the form with POST data
            $data = $this->params()->fromPost();

            $form->setData($data);

            // Validate form
            if($form->isValid()) {

                // Get filtered and validated data
                $data = $form->getData();

                // Save user choices in session.
                $this->sessionContainer->userChoices["step$step"] = $data;

                // Increase step
                $step ++;
                $this->sessionContainer->step = $step;

                // If we completed all 3 steps, redirect to Review page.
                if ($step>3) {
                    return $this->redirect()->toRoute('registration',
                                ['action'=>'review']);
                }

                // Go to the next step.
                return $this->redirect()->toRoute('registration');
            }
        }

        $viewModel = new ViewModel([
            'form' => $form
        ]);
        $viewModel->setTemplate("application/registration/step$step");

        return $viewModel;
    }

    /**
     * The "review" action shows a page allowing to review data entered on previous
     * three steps.
     */
    public function reviewAction()
    {
        // Validate session data.
        if(!isset($this->sessionContainer->step) ||
           $this->sessionContainer->step<=3 ||
           !isset($this->sessionContainer->userChoices)) {
            throw new \Exception('Sorry, the data is not available for review yet');
        }

        // Retrieve user choices from session.
        $userChoices = $this->sessionContainer->userChoices;

        return new ViewModel([
            'userChoices' => $userChoices
        ]);
    }
}
~~~

En el código de arriba tenemos tres métodos:

  * El constructor `__construct()` se usa para inyectar la dependencia, en este
    caso el contenedor de sesión, dentro del controlador.

  * El método de acción `indexAction()` extrae el paso actual almacenado en la
    sesión e inicializa el modelo de formulario. Si el usuario ha enviado el
    formulario extraemos los datos del formulario y lo guardamos en la sesión
    e incrementamos el paso. Si el paso es mayor que 3, dirigimos al usuario
    a la página «Review».

  * El método de acción `reviewAction()` extrae los datos ingresados por el
    usuario en los tres pasos y los pasa a la vista para que sea mostrado en
    pantalla.

#### Agregar la clase RegistrationControllerFactory

Luego, agregamos la fábrica para el controlador `RegistrationController`. Para
hacer esto, agregamos el archivo *RegistrationControllerFactory.php* dentro
de la carpeta *Controller/Form* que está dentro de la carpeta fuente del modulo.
Colocamos el siguiente código dentro del archivo:

~~~php
<?php
namespace Application\Controller\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Application\Controller\RegistrationController;

/**
 * This is the factory for RegistrationController. Its purpose is to instantiate the
 * controller and inject dependencies into it.
 */
class RegistrationControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container,
                       $requestedName, array $options = null)
    {
        $sessionContainer = $container->get('UserRegistration');

        // Instantiate the controller and inject dependencies
        return new RegistrationController($sessionContainer);
    }
}
~~~

T> ¡No olvidemos registrar el controlador en el archivo *module.config.php*!

### Agregar las plantillas de vista

Ahora, vamos a agregar las plantillas de vista para las acciones del controlador.
Tenemos cuatro plantilla: *step1.phtml*, *step2.phtml*, *step3.phtml* y
*review.phtml*. Las tres primeras se usan en el `indexAction()` y la última
es usada por el método `reviewAction()`.

Agregamos el archivo *step1.phtml* dentro de la carpeta *application/registration*
y colocamos el siguiente código dentro del archivo:

~~~php
<?php
$form->get('email')->setAttributes([
    'class'=>'form-control',
    'placeholder'=>'name@yourcompany.com'
    ]);

$form->get('full_name')->setAttributes([
    'class'=>'form-control',
    'placeholder'=>'John Doe'
    ]);

$form->get('password')->setAttributes([
    'class'=>'form-control',
    'placeholder'=>'Type password here (6 characters at minimum)'
    ]);

$form->get('confirm_password')->setAttributes([
    'class'=>'form-control',
    'placeholder'=>'Repeat password'
    ]);

$form->get('submit')->setAttributes(array('class'=>'btn btn-primary'));

$form->prepare();
?>

<h1>User Registration - Step 1</h1>

<div class="row">
    <div class="col-md-6">
        <?= $this->form()->openTag($form); ?>

        <div class="form-group">
            <?= $this->formLabel($form->get('email')); ?>
            <?= $this->formElement($form->get('email')); ?>
            <?= $this->formElementErrors($form->get('email')); ?>
        </div>

        <div class="form-group">
            <?= $this->formLabel($form->get('full_name')); ?>
            <?= $this->formElement($form->get('full_name')); ?>
            <?= $this->formElementErrors($form->get('full_name')); ?>
        </div>

        <div class="form-group">
            <?= $this->formLabel($form->get('password')); ?>
            <?= $this->formElement($form->get('password')); ?>
            <?= $this->formElementErrors($form->get('password')); ?>
        </div>

        <div class="form-group">
            <?= $this->formLabel($form->get('confirm_password')); ?>
            <?= $this->formElement($form->get('confirm_password')); ?>
            <?= $this->formElementErrors($form->get('confirm_password')); ?>
        </div>

        <div class="form-group">
        <?= $this->formElement($form->get('submit')); ?>
        </div>

        <?= $this->formElement($form->get('csrf')); ?>

        <?= $this->form()->closeTag(); ?>
    </div>
</div>
~~~

Luego, agregamos el archivo *step2.phtml* dentro de la carpeta *application/registration*
y colocamos el siguiente código dentro de él:

~~~php
<?php
$form->get('phone')->setAttributes([
    'class'=>'form-control',
    'placeholder'=>'Phone number in international format'
    ]);

$form->get('street_address')->setAttributes([
    'class'=>'form-control',
    ]);

$form->get('city')->setAttributes([
    'class'=>'form-control',
    ]);

$form->get('state')->setAttributes([
    'class'=>'form-control',
    ]);

$form->get('post_code')->setAttributes([
    'class'=>'form-control',
    ]);

$form->get('country')->setAttributes([
    'class'=>'form-control'
    ]);

$form->get('submit')->setAttributes(array('class'=>'btn btn-primary'));

$form->prepare();
?>

<h1>User Registration - Step 2 - Personal Information</h1>

<div class="row">
    <div class="col-md-6">
        <?= $this->form()->openTag($form); ?>

        <div class="form-group">
            <?= $this->formLabel($form->get('phone')); ?>
            <?= $this->formElement($form->get('phone')); ?>
            <?= $this->formElementErrors($form->get('phone')); ?>
        </div>

        <div class="form-group">
            <?= $this->formLabel($form->get('street_address')); ?>
            <?= $this->formElement($form->get('street_address')); ?>
            <?= $this->formElementErrors($form->get('street_address')); ?>
        </div>

        <div class="form-group">
            <?= $this->formLabel($form->get('city')); ?>
            <?= $this->formElement($form->get('city')); ?>
            <?= $this->formElementErrors($form->get('city')); ?>
        </div>

        <div class="form-group">
            <?= $this->formLabel($form->get('state')); ?>
            <?= $this->formElement($form->get('state')); ?>
            <?= $this->formElementErrors($form->get('state')); ?>
        </div>

        <div class="form-group">
            <?= $this->formLabel($form->get('post_code')); ?>
            <?= $this->formElement($form->get('post_code')); ?>
            <?= $this->formElementErrors($form->get('post_code')); ?>
        </div>

        <div class="form-group">
            <?= $this->formLabel($form->get('country')); ?>
            <?= $this->formElement($form->get('country')); ?>
            <?= $this->formElementErrors($form->get('country')); ?>
        </div>

        <div class="form-group">
        <?= $this->formElement($form->get('submit')); ?>
        </div>

        <?= $this->formElement($form->get('csrf')); ?>

        <?= $this->form()->closeTag(); ?>
    </div>
</div>
~~~

Luego, agregamos el archivo *step3.phtml* dentro de la carpeta *application/registration*
y colocamos el siguiente código dentro del archivo:

~~~php
<?php
$form->get('billing_plan')->setAttributes([
    'class'=>'form-control',
    ]);

$form->get('payment_method')->setAttributes([
    'class'=>'form-control',
    ]);

$form->get('submit')->setAttributes(array('class'=>'btn btn-primary'));

$form->prepare();
?>

<h1>User Registration - Step 3 - Billing Information</h1>

<div class="row">
    <div class="col-md-6">
        <?= $this->form()->openTag($form); ?>

        <div class="form-group">
            <?= $this->formLabel($form->get('billing_plan')); ?>
            <?= $this->formElement($form->get('billing_plan')); ?>
            <?= $this->formElementErrors($form->get('billing_plan')); ?>
        </div>

        <div class="form-group">
            <?= $this->formLabel($form->get('payment_method')); ?>
            <?= $this->formElement($form->get('payment_method')); ?>
            <?= $this->formElementErrors($form->get('payment_method')); ?>
        </div>

        <div class="form-group">
        <?= $this->formElement($form->get('submit')); ?>
        </div>

        <?= $this->formElement($form->get('csrf')); ?>

        <?= $this->form()->closeTag(); ?>
    </div>
</div>
~~~

Y finalmente, agregamos el archivo *review.phtml* dentro de la carpeta
*application/registration* y colocamos el siguiente código dentro de él:

~~~php
<h1>User Registration - Review</h1>

<p>Thank you! Now please review the data you entered in previous three steps.</p>

<pre>
<?php print_r($userChoices); ?>
</pre>
~~~

### Agregar la ruta

Agregamos la siguiente ruta dentro de nuestro archivo de configuración
*module.config.php*:

~~~php
'registration' => [
    'type'    => Segment::class,
    'options' => [
        'route'    => '/registration[/:action]',
        'constraints' => [
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*'
        ],
        'defaults' => [
            'controller'    => Controller\RegistrationController::class,
            'action'        => 'index',
        ],
    ],
],
~~~

¡Muy Bien! Ahora todo esta listo para ver los resultados.

### Resultados

Para ver a nuestro formulario multi-pasos en acción escribimos la siguiente
URL «http://localhost/registration» en la barra de navegación de nuestro
navegador web. La página *Registro de Usuario - Paso 1* aparecerá
(ver figura 11.6 más abajo):

![Figura 11.6. Registro de Usuario - Paso 1](../en/images/forms_advanced/registration_step1.png)

Una vez que el usuario agrego su dirección de correo electrónico, su nombre
completo, su contraseña y hace clic en *Next*, el usuario es movido al
siguiente paso (ver figura 11.7):

![Figura 11.7. Registro de Usuario - Paso 2](../en/images/forms_advanced/registration_step2.png)

El paso final se muestra más abajo en la figura 11.8:

![Figura 11.8. Registro de Usuario - Paso 3](../en/images/forms_advanced/registration_step3.png)

Haciendo clic en *Next* los resultados se muestran en la página *Resumen* que
permite ver los datos ingresados en los tres pasos anteriores:

![Figura 11.9. Registro de Usuario - Resumen](../en/images/forms_advanced/registration_review.png)

T> Podemos encontrar este ejemplo completo en la aplicación *Form Demo* que se
T> distribuye junto a este libro.

## Resumen

En este capítulo discutimos sobre algunas de las capacidades avanzadas de uso
del formulario.

Laminas Framework provee dos clases cuyo propósito es aumentar la seguridad del
formulario: `Captcha` y `Csrf`. Un CAPTCHA es un tipo de prueba basada en una
pregunta de desafío que se usa para determinar si el usuario es o no un humano.
Los elementos CAPTCHA se usan en los formularios para prevenir su envío por
parte de un proceso automático malicioso (un robot). El último elemento, `Csrf`,
se usa para prevenir los ataques de *falsificación de peticiones en sitios cruzados*
(CSRF).

Además, aprendimos como implementar un formulario multi-pasos con la ayuda de
sesiones.
