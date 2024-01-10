# Colectar las Entradas del Usuario con Forms {#forms}

En este capítulo nos familiarizaremos con el uso de los formularios web para
reunir los datos que el usuario ingresa al sitio web. En Laminas Framework,
la funcionalidad para trabajar con formularios está distribuida principalmente
en cuatro   componentes: el componente @`Laminas\Form` que permite construir
formularios y contiene los ayudantes de vista para imprimir los elementos del
formularios; los componentes @`Laminas\Filter`, @`Laminas\Validator` y
@`Laminas\InputFilter` permiten filtrar y validar los datos ingresados por el
usuario.

|--------------------------------|---------------------------------------------------------------|
| *Componente*                   | *Descripción*                                                 |
|--------------------------------|---------------------------------------------------------------|
| @`Laminas\Form`                    | Contiene clases modelo para la base de los formularios.       |
|--------------------------------|---------------------------------------------------------------|
| @`Laminas\Filter`                  | Contiene varias clases filtros.                               |
|--------------------------------|---------------------------------------------------------------|
| @`Laminas\Validator`               | Implementa varias clases validadoras.                         |
|--------------------------------|---------------------------------------------------------------|
| @`Laminas\InputFilter`             | Implementa un contenedor para filtros/validadores.            |
|--------------------------------|---------------------------------------------------------------|
| @`Laminas\Mail`                    | Contiene la funcionalidad de envió de correo electrónico.     |
|--------------------------------|---------------------------------------------------------------|

## Obtener el Formulario de Demostración desde GitHub

Demostramos el uso de formularios en el aplicación web de ejemplo *Form Demo*
que añadimos a este libro. Este ejemplo es un sito web completo que podemos
instalar y ver a los formularios en acción.

Para descargar la aplicación *Form Demo* debemos ir [esta página](https://github.com/olegkrivtsov/using-laminas-book-samples)
y hacer clic en el botón *Clone or Download* para descargar el código como un
archivo ZIP. Cuando la descarga se complete descomprimimos el archivo en alguna
carpeta.

Luego navegamos hasta la carpeta `formdemo` que contiene el código fuente
completo de la aplicación web *Form Demo*:

~~~text
/using-laminas-book-samples
  /formdemo
  ...
~~~

T> Para instalar el ejemplo podemos editar el archivo del sitio virtual
T> por defecto o crear uno nuevo. Después de editar el archivo reiniciamos
T> el servidor HTTP de Apache y abrimos el sitio web en el navegador. Para
T> información adicional sobre los sitios virtuales de Apache podemos
T> ver el [Apéndice A. Configurar el Entorno de Desarrollo Web](#devenv).

## Sobre los Formularios HTML

La funcionalidad de formularios que provee Laminas Framework usa internamente
los formularios HTML. Por eso comenzaremos con una breve introducción al tema
de formularios HTML.

En HTML los formularios se encierran con las etiquetas `<form>` y `</form>`.
Un formulario típicamente consiste en campos: campos de entra de texto, casillas
de verificación, botones de opción, botón de enviar, campos ocultos, etc.
HTML provee varias etiquetas para definir los campos de un formulario:

* `<input>` - especifica un campo de entrada donde el usuario puede ingresar
  datos (la apariencia del campo y su comportamiento depende del tipo de
  campo);
* `<textarea>` - texto de múltiples líneas que pueden contener un número
  ilimitado de caracteres;
* `<button>` - un botón al que se puede hacer clic[^button];
* `<select>` - una lista desplegable;
* `<option>` - se usa dentro del elemento `<select>` para definir las opciones
  disponibles en la lista desplegable.

[^button]: El campo `<button>` es análogo a `<input type="button">` sin embargo
           aquel provee otras capacidades como la posibilidad de especificar un
           icono gráfico sobre el botón.

En la tabla 7.1 podemos encontrar ejemplos de definiciones de campos para
formularios HTML. La Figura 7.1 muestra la representación gráfica correspondiente
de los campos (con excepción del campos tipo "hidden" que no tiene una
representación visual).

![Figura 7.1. Campos Estándares para Formularios HTML](../en/images/forms/standard_form_fields.png)

{title="Tabla 7.1. Campos Estándares para Formularios HTML"}
|--------------------------------|---------------------------------------------------------------|
| *Campo*                        | *Definición*                                                  |
|--------------------------------|---------------------------------------------------------------|
| Campo de entra de texto        | `<input type="text">`                                         |
|--------------------------------|---------------------------------------------------------------|
| Área de texto                  | `<textarea rows=4></textarea>`                                |
|--------------------------------|---------------------------------------------------------------|
| Contraseña                     | `<input type="password">`                                     |
|--------------------------------|---------------------------------------------------------------|
| Botón                          | `<input type="button" value="Apply">` o                       |
|                                | `<button type="button">Apply</button>`                        |
|--------------------------------|---------------------------------------------------------------|
| Botón de enviar                | `<input type="submit" value="Submit">`                        |
|--------------------------------|---------------------------------------------------------------|
| Imagen (botón de enviar gráfico)| `<input type="image" src="button.jpg">`                      |
|--------------------------------|---------------------------------------------------------------|
| Botón de reinicio              | `<input type="reset" value="Reset">`                          |
|--------------------------------|---------------------------------------------------------------|
| Casilla de verificación        | `<input type="checkbox">Remember me</input>`                  |
|--------------------------------|---------------------------------------------------------------|
| Botón de opción                | `<input type="radio" value="Radio">Allow</input>`             |
|--------------------------------|---------------------------------------------------------------|
| Seleccionar                    | `<select><option>Enable</option><option>Disable</option></select>` |
|--------------------------------|---------------------------------------------------------------|
| Archivo                        | `<input type="file">`                                         |
|--------------------------------|---------------------------------------------------------------|
| Campo oculto                   | `<input type="hidden">`                                       |
|--------------------------------|---------------------------------------------------------------|

HTML5 introduce varios nuevos tipos de campos para formularios (listados en la
tabla 7.2). La figura 7.2 contiene las representaciones gráficas correspondientes.

Los campos de HTML5 proveen otras manera convenientes de definir los tipos
de datos que se usan más frecuentemente: números, fechas, correo electrónico,
URLs, etc. Además, al enviar el formulario el navegador valida que los datos
que el usuario ingresó están en la forma correcta si no es así el navegador
evitará el envío del formulario y pedirá al usuario que corrija los datos
incorrectos.

{title="Tabla 7.2. Campos para formularios en HTML5"}
|--------------------------------|---------------------------------------------------------------|
| *Campo*                        | *Definición*                                                  |
|--------------------------------|---------------------------------------------------------------|
| Selector de Color              | `<input type="color">`                                        |
|--------------------------------|---------------------------------------------------------------|
| Fecha                          | `<input type="date">`                                         |
|--------------------------------|---------------------------------------------------------------|
| Fecha-hora (con zona horaria)  | `<input type="datetime">`                                     |
|--------------------------------|---------------------------------------------------------------|
| Fecha-hora (sin zona horaria)  | `<input type="datetime-local">`                               |
|--------------------------------|---------------------------------------------------------------|
| Dirección de correo electrónico| `<input type="email">`                                        |
|--------------------------------|---------------------------------------------------------------|
| Número                         | `<input type="number">`                                       |
|--------------------------------|---------------------------------------------------------------|
| Hora                           | `<input type="time">`                                         |
|--------------------------------|---------------------------------------------------------------|
| Mes                            | `<input type="month">`                                        |
|--------------------------------|---------------------------------------------------------------|
| Semana                         | `<input type="week">`                                         |
|--------------------------------|---------------------------------------------------------------|
| URL                            | `<input type="url">`                                          |
|--------------------------------|---------------------------------------------------------------|
| Rango (deslizante)             | `<input type="range">`                                        |
|--------------------------------|---------------------------------------------------------------|
| Campo de Búsqueda              | `<input type="search" name="googlesearch">`                   |
|--------------------------------|---------------------------------------------------------------|
| Número de teléfono             | `<input type="tel">`                                          |
|--------------------------------|---------------------------------------------------------------|

![Figura 7.2. Campos para formularios en HTML5](../en/images/forms/html5_form_fields.png)

### Conjunto de Campos (Fieldsets)

En el formulario podemos agrupar campos relacionados con la ayuda de la etiqueta
`<fieldset>` como se muestra en el ejemplo de abajo. La etiqueta opcional
`<legend>` nos permite definir el título para el grupo.

~~~html
<fieldset>
  <legend>Choose a payment method:</legend>
  <input type="radio" name="payment" value="paypal">PayPal</input>
  <input type="radio" name="payment" value="card">Credit Card</input>
</fieldset>
~~~

El código HTML de arriba generará un grupo como el de la figura 7.3:

![Figura 7.3. Conjunto de Campos](../en/images/forms/fieldset.png)

### Ejemplo: Formulario de "Contacto"

Un ejemplo de un formulario HTML típico se presenta abajo:

~~~html
<form name="contact-form" action="/contactus" method="post">
  <label for="email">E-mail</label>
  <input name="email" type="text">
  <br>
  <label for="subject">Subject</label>
  <input name="subject" type="text">
  <br>
  <label for="body">Message</label>
  <textarea name="body" class="form-control" rows="6"></textarea>
  <br>
  <input name="submit" type="submit" value="Submit">
</form>
~~~

En el ejemplo de arriba tenemos un formulario para hacer comentarios que
permite al usuario ingresar su correo electrónico, el asunto del mensaje,
un texto y enviar el formulario al servidor. La definición de un formulario
comienza con la etiqueta `<form>` (línea 1).

La etiqueta `<form>` contiene varios atributos importantes:

* El atributo `name` especifica el nombre del formulario ("contact-form").
* El atributo `action` define la URL del script del lado del servidor que es
  responsable de procesar el formulario enviado ("/contactus").
* El atributo `method` define el método (GET o POST) que se usa para enviar
  los datos del formulario. En este ejemplo usamos el método POST (recomendado).

En la línea 3 definimos un campo de entrada de texto con la ayuda del elemento
`<input>`. El atributo `name` especifica el nombre de campo ("email"). El
atributo `type` especifica el propósito del elemento (tipo "text" significa
que el campo de entrada está pensado para ingresar texto).

En la línea 2 tenemos el elemento `<label>` que representa la etiqueta del
campo de entrada de texto para el correo electrónico (el nombre del campo
de entrada correspondiente se determina mediante el atributo `for` del
elemento `<label>`).

En las líneas 5-6, por analogía, tenemos el campo de entrada "Subject" y su
etiqueta.

En la línea 9 tenemos el campo de texto de área que funciona bien para ingresar
texto de múltiples líneas. La altura del área de texto (6 filas) se define
con el atributo `rows`.

En la línea 11 tenemos el botón de envío (un elemento `input` que tiene como
tipo "submit"). El atributo `value` permite colocar el texto del título para
el botón ("Submit"). Con el uso de este botón el usuario envía los datos del
formulario al servidor.

El elemento de salto de línea `<br>` se usa como en líneas 4, 7 y 10 para
posicionar los controles del formulario uno debajo de otro, de lo contrario
ellos se colocarán en una línea.

Para ver como se ve el formulario podemos colocar el código HTML en un archivo
`.html` y abrir el archivo en un navegador web. Veremos el aspecto que toma el
formulario en la figura 7.4.

![Figura 7.4. Aspecto del formulario de contacto](../en/images/forms/typical_form.png)

Si ingresamos algunos datos en el formulario de contacto y hacemos clic en el
botón *Submit* el navegador web enviará una petición HTTP a la URL que se
especificó en el atributo `action` del formulario. La petición HTTP contendrá
los datos que se ingresaron.

### Métodos GET y POST

Los formularios soportan los métodos GET y POST para enviar los datos al
servidor. Estos métodos tienen importantes diferencias técnicas.

Cuando usamos el método POST para enviar el formulario los datos se envían
en el cuerpo de la petición HTTP. Por ejemplo, cuando presionamos el botón
*Submit* del formulario de contacto la petición HTTP tendrá la siguiente
forma:

~~~text
POST http://localhost/contactus HTTP/1.1
Host: localhost
Connection: keep-alive
Content-Length: 76
Accept: text/html,application/xhtml+xml,application/xml
Origin: null
User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64)
Content-Type: application/x-www-form-urlencoded

email=name%40example.com&subject=Example+Subject&body=Hello%21&submit=Submit
~~~

Arriba podemos ver que los datos del formulario se transmiten en el cuerpo
de la petición (línea 10). Los campos del formulario se concatenan en una
sola cadena de caracteres y luego se codifica para reemplazar los caracteres
inseguros por caracteres de la tabla ASCII.

En comparación, cuando colocamos el método GET en el formulario la petición
HTTP tiene el siguiente aspecto:

~~~text
GET http://localhost/contactus?email=name%40example.com&subject=Example+Subject&body=Hello%21&submit=Submit HTTP/1.1
Host: localhost
Connection: keep-alive
Accept: text/html,application/xhtml+xml,application/xml
User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64)
Accept-Encoding: gzip,deflate,sdch

~~~

En el ejemplo de arriba podemos ver que los datos del formulario se concatenan,
se codifican y se envían como parte de la URL de la petición HTTP (línea 1),
sin embargo esto hace a las URL largas y difíciles de leer. Además, los datos
del formulario se envían dentro de la URL haciéndolos fácilmente visibles a
los visitantes del sitio.

En muchos casos usaremos el método POST para enviar los datos del formulario
en el cuerpo de la petición porque el usuario no necesita ver los datos en
la barra de navegación del navegador web especialmente cuando enviamos
la contraseña o algún otro dato sensible.

W> Por favor notemos que enviar los datos del formulario usando el método
W> POST no protege nuestros datos sensibles (como contraseñas, número de
W> tarjetas de crédito, etc.) de ser robados. Para proteger nuestros datos
W> debemos dirigir el tráfico HTTP a un túnel
W> [SSL](http://en.wikipedia.org/wiki/Secure_Sockets_Layer)
W> (SSL significa Secure Sockets Layer). Los conexiones protegidas con SSL
W> se distinguen por el esquema *https://* en la URL de la página web.
W> Para habilitar SSL en nuestro servidor HTTP Apache necesitamos obtener
W> un certificado SSL de un proveedor de confianza (como [VeriSign](http://www.verisign.com/))
W> e instalarlo en nuestro servidor.

## Estilos para Formularios HTML con Twitter Bootstrap

En los sitios web basados en Laminas usamos el framework CSS Twitter Bootstrap
que provee reglas CSS por defecto para dar estilos a formularios y campos
del formularios. Para aplicar las reglas CSS a un campo del formulario
(como `<input>`, `<textarea>`, etc.), debemos asignarle la clase CSS
`.form-control`. Además, cuando usamos etiquetas junto con los campos
de entrada colocamos el par entrada-etiqueta dentro de un elemento `<div>`
con la clase CSS `.form-group`. Para el botón `submit` podemos usar la clase
CSS `.btn` más una clase de tema como `.btn-default`, `.btn-primary`, etc.

Abajo mostramos el formulario de contacto modificado para usar
los estilos de Bootstrap:

~~~html
<h1>Contact Us</h1>

<p>
    Please fill out the following form to contact us.
    We appreciate your feedback.
</p>

<form name="contact-form" action="/contactus" method="post">

  <div class="form-group">
    <label for="email">Your E-mail</label>
    <input name="email" type="text" class="form-control"
           placeholder="name@example.com">
  </div>

  <div class="form-group">
    <label for="subject">Subject</label>
    <input name="subject" type="text" class="form-control"
           placeholder="Type subject here">
  </div>

  <div class="form-group">
    <label for="body">Message Body</label>
    <textarea name="body" class="form-control" rows="6"
              placeholder="Type message text here"></textarea>
  </div>

  <input name="submit" type="submit"
         class="btn btn-primary" value="Submit">
</form>
~~~

El aspecto del formulario se muestra en la figura 7.5.

![Figura 7.5. Formulario de Contacto con Estilo](../en/images/forms/styled_contact_form.png)

Como Twitter Bootstrap se diseño para soportar teléfonos, tabletas y escritorios
el formulario será tan ancho como el tamaño de la pantalla. Esto pude hacer
que nuestro formulario sea muy ancho y difícil de ver. Para limitar el ancho
del formulario podemos usar las rejillas que provee Bootstrap, como se muestra
el siguiente ejemplo:

~~~html
<div class="row">
  <div class="col-md-6">
    <form>
      ...
    </form>
  </div>
</div>
~~~

En el código HTML de arriba colocamos un formulario dentro de una celda de
la rejilla de 6 columnas de ancho que hace que el formulario ocupe la mitad
de la pantalla.

## Instalar los componentes para formularios de Laminas

Para usar formularios en una aplicación web MVC necesitamos instalar al menos
el metapaquete de Composer `laminas/laminas-mvc-form` (si no lo hemos hecho aún):

~~~
php composer.phar require laminas/laminas-mvc-form
~~~

Con la instalación de este paquete se instalarán todos los componentes necesarios
para implementar formularios en Laminas: @`Laminas\Form`, @`Laminas\InputFilter`,
@`Laminas\Filter`, @`Laminas\Validator` y probablemente otros más.

## Recuperar los datos del formulario desde una acción del controlador

El sitio web del usuario generalmente trabaja con el formulario en el siguiente
orden:

* Primero, cuando la acción del controlador se ejecuta se imprime la página web
  que contiene el formulario que el usuario del sitio debe llenar.
  Una vez que los usuarios llenan los campos del formulario y hacen clic
  en el botón *Submit* se genera la petición HTTP y se envían los datos
  al servidor.

* Segundo, desde el método de acción del controlador podemos extraer los
  datos enviados a través de las variables POST (y/o GET) y mostrar la página
  con los resultados del procesamiento de los datos.

Generalmente estas dos páginas web se generan en *la misma* acción del
controlador.

En el siguiente ejemplo mostraremos como podemos crear una acción en
el controlador para mostrar el formulario de contacto y recuperar los datos
enviados por el usuario. Para comenzar agregamos la plantilla de vista
*contact-us.phtml* dentro de la carpeta *application/index* que esta dentro
de la carpeta */view* del módulo (ver figura 7.6 para un ejemplo).

![Figura 7.6. Crear el archivo contact-us.phtml](../en/images/forms/contactus_file.png)

Colocamos el código HTML del formulario de contacto que usamos en la sección
anterior dentro del archivo de plantilla de vista.

Luego, agregamos el método de acción `contactUsAction()` a la clase
`IndexController`. Con el método de acción queremos extraer los datos crudos
del formulario de contacto que fue enviado por el usuario del sitio:

~~~php
<?php
namespace Application\Controller;

// ...

class IndexController extends AbstractActionController
{
  // This action displays the feedback form
  public function contactUsAction()
  {
    // Check if user has submitted the form
    if($this->getRequest()->isPost()) {

	  // Retrieve form data from POST variables
	  $data = $this->params()->fromPost();

	  // ... Do something with the data ...
	  var_dump($data);
    }

    // Pass form variable to view
    return new ViewModel([
          'form' => $form
       ]);
  }
}
~~~

En el código de arriba definimos el método de acción `contactUsAction()`
dentro de la clase `IndexController` (línea 9).

Luego en las línea 12 revisamos si la solicitud es una petición POST (revisando
la primera línea de la petición HTTP). Generalmente, el formulario usa el
método POST para enviar los datos. Por esta razón podemos detectar si el
formulario se envío o no revisando la primera línea de la petición HTTP.

En la línea 15 recuperamos los datos crudos enviados por el usuario. Extraemos
todas las variables POST con la ayuda del complemento para controladores @`Params`.
Los datos se regresan en forma de un arreglo y se guardan dentro de la variable
`$data`.

Finalmente debemos agregar una ruta de tipo literal para hacer una URL corta
y fácil de recordar para la página *Contactanos*. Agregamos la siguiente llave
`contactus` a la configuración de rutas en el archivo *module.config.php*:

~~~php
<?php
return [
  // ...
  'router' => [
    'routes' => [
      // Add the following routing rule for the "Contact Us" page
      'contactus' => [
        'type' => Literal::class,
          'options' => [
             'route'    => '/contactus',
             'defaults' => [
               'controller' => Controller\IndexController::class,
               'action'     => 'contactUs',
             ],
           ],
         ],
       ],
    ],
  ],
  // ...
];

~~~

Ahora si escribimos la URL "http://localhost/contactus" en la barra de
navegación del navegador web deberíamos ver una página como la que se muestra
en la figura 7.7.

![Figura 7.7. Formulario de Contacto](../en/images/forms/feedback_form.png)

Ingresamos el correo electrónico, el asunto y el cuerpo de texto y hacemos
clic en el botón *Submit* del formulario. Los datos serán enviados al
servidor y finalmente extraídos en el método `IndexController::contactUsAction()`.

Abajo se muestra una ejemplo del arreglo `$data` (producido con la función
PHP `var_dump()`). Como podemos ver el arreglo contiene una llave para cada
campo incluyendo el campo "submit".

~~~php
array (size=4)
    'email' => string 'name@example.com' (length=16)
    'subject' => string 'Happy New Year!' (length=15)
    'body' => string 'Dear Support, I'd like to thank you for the
              excellent quality of your support service and wish you
              a Happy New Year!' (length=118)
    'submit' => string 'Submit' (length=6)
~~~

## Los formularios y el Modelo-Vista-Controlador

En la sección anterior consideramos un caso de uso de un formulario muy simple:
preparamos la plantilla de vista con código HTML para un formulario y un
acción de controlador para mostrar el formulario e imprimir en la pantalla
los datos crudos ingresados por el usuario. Sin embargo, usar los datos crudos
ingresados por el usuario en una aplicación real tiene la desventaja de
que no revisamos los datos enviados en búsqueda de errores y/o código malicioso.
Ahora discutiremos como hacer validaciones.

En un sitio web basado en Laminas que usa el patrón Modelo-Vista-Controlador el
formulario usualmente se separa en modelos y vistas. Los *modelos de formulario*
son responsables de la definición de los campos, filtrado y validación; y la
vista o *presentación de formulario* se implementada generalmente con la ayuda
de ayudantes de vista especiales.

Esta funcionalidad que se muestra esquemáticamente en la figura 7.8 permite
crear *modelos de formulario*, agregar filtros y reglas de validación además
de usar ayudantes de vista. Como podemos ver en la figura, se usa como base los
formularios HTML estándar.

![Figura 7.8. La funcionalidad de formularios en Laminas](../en/images/forms/html_zf2_forms.png)

El enfoque MVC para trabajar con formularios tiene las siguientes ventajas:

* Podemos reusar nuestros *modelos de formulario* en diferentes acciones
  de controlador.
* Con el uso de ayudantes de vista podemos parcialmente evitar el aburrido
  trabajo de escribir el código HTML correspondiente al formulario y sus
  validaciones.
* Podemos crear una o varias representaciones visuales para el mismo *modelo
  de formulario*.
* Cuando se encapsulan las validaciones del formulario en una única clase
  *modelo de formulario* tenemos menos lugares del código donde necesitaremos
  revisar la entrada del usuario y de esta manera se mejora la seguridad del
  sitio.

### Flujo de trabajo de un formulario típico

Hablando en general, se instancia un *modelo de formulario* dentro de nuestro
método de acción de un controlador, luego recuperamos los datos enviados por
el usuario mediante las variables PHP y las pasamos al modelo para la validación.
Los ayudantes de vista de formularios se usan en la plantilla de vista para
generar el código HTML del formulario. Este flujo de trabajo típico se ilustra
en la figura 7.9.

![Figura 7.9. Trabajar con formularios en una aplicación MVC](../en/images/forms/forms_and_mvc.png)

La flechas en la figura 7.9 denotan la dirección de las acciones:

1. Primero, dentro del método de acción del controlador, recuperamos los datos
   enviados por el usuario del sitio revisando las variables PHP GET, POST y
   posiblemente otras. Luego creamos una instancia del modelo de formulario
   y le pasamos los datos enviados por el usuario. La función del
   modelo de formulario es revisar (validar) la corrección de los datos y
   si algo está mal se produce un o unos mensajes de error para cualquier
   campo del formulario invalido.

2. Segundo, pasamos el modelo de formulario a la plantilla de vista `.phtml`
   para imprimirla en la pantalla (con la ayuda de la variable contenedor
   @`ViewModel`[Laminas\View\Model\ViewModel]). La plantilla de vista será capaz de
   acceder al modelo de formulario y llamar a sus métodos.

3. Y finalmente, la plantilla de vista usa el modelo de formulario y los
   ayudantes de vista que provee Laminas Framework para imprimir en la pantalla
   los campos del formulario (y si es el caso mostrar los mensajes de error
   producidos en la etapa de validación).
   Como resultado el código HTML del formulario se produce.

En las siguientes secciones discutiremos esto con más detalle.

## Un Formulario de Modelo

Un modelo de formulario es usualmente una clase PHP que crea un número de
*campos*. La clase base para todos los formularios de modelos es la clase
`Form` definida en el componente `Laminas\Form`.

Los campos en el modelo de formulario puede opcionalmente ser agrupado dentro
de *conjuntos de campos*. De hecho, el propio modelo de formulario se puede
considerar como un conjunto de campos. Este hecho se refleja en la herencia
de la clase formulario (figura 7.10).

![Figura 7.10. Herencia de la clase Form](../en/images/forms/form_inheritance.png)

Como podemos ver en la figura la clase @`Form`[Laminas\Form\Form] extiende de la
clase @`Fieldset`. La clase @`Fieldset` a su vez se deriva de la clase @`Element`
que representa un solo campo de formulario y sus atributos.

T> Esta herencia de clases puede parecer estraña a primera vista pero todo
T> se vuelve lógico si recordamos que la clase @`Form`[Laminas\Form\Form] hereda métodos para
T> agregar campos de formulario desde la clase @`Fieldset` que hereda métodos
T> para colocar atributos de formulario desde la clase @`Element`.

Abajo, proveemos el esbozo de una clase modelo para el formulario de contacto
de nuestro ejemplo anterior:

~~~php
<?php
namespace Application\Form;

use Laminas\Form\Form;

// A feedback form model
class ContactForm extends Form
{
  // Constructor.
  public function __construct()
  {
    // Define form name
    parent::__construct('contact-form');

    // Set POST method for this form
    $this->setAttribute('method', 'post');

    // (Optionally) set action for this form
    $this->setAttribute('action', '/contactus');

    // Create the form fields here ...
  }
}
~~~

Como podemos ver los modelos de formulario del módulo `Application` del
sitio web pertenecen por convención al namespace `Application\Form`
(línea 2).

En la línea 7 definimos la clase para el modelo de formulario `ContactForm` que
extiende de la clase base @`Form`[Laminas\Form\Form].

En la línea 10 definimos el método contructor para la clase. Como derivamos
nuestro modelo de formulario de la clase base @`Form`[Laminas\Form\Form] debemos llamar al
constructor de clase *parent* para inicializarlo (línea 13). El constructor
de la clase padre acepta un argumento opcional que permite colocar el nombre
del formulario ('contact-form').

Además, podemos colocar el método de envio de datos del formulario (POST)
usando el método `setAttribute()` que provee la clase base (línea 16). El
método `setAttribute()` toma dos parámetros: el primero de ellos es el
nombre del atributo que se va a colocar y el segundo es el valor del atributo.

También podemos colocar el atributo "action" del formulario (línea 19) con
el método `setAttribute()` de manera analoga a como lo hicimos con el atributo
*method*. Pero como veremos más adelante colocar el atributo "action" del
formulario es opcional.

I> Colocar el atributo "action" en el formulario es opcional porque la
I> ausencia de este atributo fuerza a enviar los datos del formulario
I> a la URL de la página actual. Esto es suficiente en la mayoría de los
I> escenarios porque usualmente usamos una sola acción de controlador tanto
I> para imprimir el formulario como para procesar sus datos.

Los campos se crean generalmente dentro del constructor del modelo de formulario
(ver línea 21). En la siguiente sección aprenderemos cuales campos de formulario
están disponibles y como agregarlos al modelo de formulario.

## Elementos del Formulario

En un modelo de formulario un campo de entrada está generalmente acompañado
de una etiqueta de texto (Las etiquetas `<label>` y `<input>` se usan juntas).
A este par de etiquetas se les conoce como *elemento* del modelo de formulario.

Análogamente a un campo de un formulario HTML un elemento del modelo de
formulario puede contener el nombre y otros atributos opcionales como: "id",
"class", etc. Adicionalmente, podemos colocar *opciones* a un elemento;
las opciones permiten principalmente especificar el texto y los atributos
para la etiqueta del elemento.

Todos los elementos del modelo de formulario se heredan de la clase base @`Element`
que además pertenece al componente @`Laminas\Form`. La clase base `Element` implementa
la interfaz @`ElementInterface`. El diagrama de herencia de clases se muestra
en la figura 7.11.

![Figura 7.11. Herencia de clases de los elementos del formulario](../en/images/forms/form_element_inheritance.png)

Los clases de los elementos concretos del formulario extienden de la clase base
@`Element`. Ellos se listan en las tablas 7.3 - 7.7. Estas clases están en el
namespace @`Laminas\Form\Element`[Laminas\Form].

{title="Tabla 7.3. Elementos de formulario compatibles con HTML 4"}
|--------------------------------|---------------------------------------------------------------|
| *Nombre de la Clase*           | *Descripción*                                                 |
|--------------------------------|---------------------------------------------------------------|
| @`Button`                      | Botón.                                                        |
|--------------------------------|---------------------------------------------------------------|
| @`Checkbox`[Laminas\Form\Element\Checkbox] | Casilla de verificación.                             |
|--------------------------------|---------------------------------------------------------------|
| @`File`[Laminas\Form\Element\File]| Campo para archivo.                                           |
|--------------------------------|---------------------------------------------------------------|
| @`Hidden`                      | Campo oculto.                                                 |
|--------------------------------|---------------------------------------------------------------|
| @`Image`[Laminas\Form\Element\Image]| Campo para imagen.                                          |
|--------------------------------|---------------------------------------------------------------|
| @`Password`[Laminas\Form\Element\Password]| Campo de contraseña.                                  |
|--------------------------------|---------------------------------------------------------------|
| @`Radio`                       | Botón de opción.                                              |
|--------------------------------|---------------------------------------------------------------|
| @`Select`[Laminas\Form\Element\Select]| Lista desplegable.                                        |
|--------------------------------|---------------------------------------------------------------|
| @`Submit`                      | Botón de envío.                                               |
|--------------------------------|---------------------------------------------------------------|
| @`Text`[Laminas\Form\Element\Text]| Campo de entrada de texto de propósito general.               |
|--------------------------------|---------------------------------------------------------------|
| @`Textarea`                    | Área de texto de multiples líneas.                            |
|--------------------------------|---------------------------------------------------------------|

{title="Tabla 7.4. Elementos de formulario compatibles con HTML 5"}
|--------------------------------|---------------------------------------------------------------|
| *Nombre de la Clase*           | *Descripción*                                                 |
|--------------------------------|---------------------------------------------------------------|
| @`Color`                        | Selector de Color.                                            |
|--------------------------------|---------------------------------------------------------------|
| @`Date`[Laminas\Form\Element\Date] | Selector de Fecha.                                            |
|--------------------------------|---------------------------------------------------------------|
| @`DateTime`[Laminas\Form\Element\DateTime]| Fecha y Hora (con zona horaria).                       |
|--------------------------------|---------------------------------------------------------------|
| @`DateTimeLocal`                | Fecha y Hora (sin zona horaria).                              |
|--------------------------------|---------------------------------------------------------------|
| @`Email`                        | Campo para el correo electrónico.                             |
|--------------------------------|---------------------------------------------------------------|
| @`Month`                        | Campo de entrada para el mes.                                 |
|--------------------------------|---------------------------------------------------------------|
| @`Number`[Laminas\Form\Element\Number]| Campo de entrada de texto que acepta números.              |
|--------------------------------|---------------------------------------------------------------|
| @`Time`[Laminas\Form\Element\Time] | Campo de entra de texto para ingresar la hora.                |
|--------------------------------|---------------------------------------------------------------|
| @`Url`[Laminas\Form\Element\Url]   | Campo de entrada de texto para ingresar una URL.              |
|--------------------------------|---------------------------------------------------------------|
| @`Week`                         | Campo de entrada de texto para ingresar el número de la semana.|
|--------------------------------|---------------------------------------------------------------|
| @`Range`[Laminas\Form\Element\Range]| Campo para rango (control deslizante).                       |
|--------------------------------|---------------------------------------------------------------|

{title="Tabla 7.5. Campos Compuestos"}
|--------------------------------|---------------------------------------------------------------|
| *Nombre de Clase*              | *Descripción*                                                 |
|--------------------------------|---------------------------------------------------------------|
| @`MultiCheckbox`                | Un grupo de casillas de verificación relacionadas.           |
|--------------------------------|---------------------------------------------------------------|
| @`DateTimeSelect`[Laminas\Form\Element\DateTimeSelect]| Selector de Fecha y hora.                 |
|--------------------------------|---------------------------------------------------------------|
| @`DateSelect`[Laminas\Form\Element\DateSelect]| Selector de Fecha.                                |
|--------------------------------|---------------------------------------------------------------|
| @`MonthSelect`[Laminas\Form\Element\MonthSelect]| Selector de Mes.                                |
|--------------------------------|---------------------------------------------------------------|

{title="Tabla 7.6. Elementos de Seguridad de Formularios"}
|--------------------------------|---------------------------------------------------------------|
| *Nombre de Clase*              | *Descripción*                                                 |
|--------------------------------|---------------------------------------------------------------|
| @`Captcha`                     | Imagen de detección de humanos.                               |
|--------------------------------|---------------------------------------------------------------|
| @`Csrf`[Laminas\Form\Element\Csrf]| Prevención de falsificación de petición en sitios cruzados    |
|                                | (Cross-site request forgery prevention).                      |
|--------------------------------|---------------------------------------------------------------|

{title="Tabla 7.7. Otros Elementos de Formularios"}
|--------------------------------|---------------------------------------------------------------|
| *Nombre de Clase*              | *Descripción*                                                 |
|--------------------------------|---------------------------------------------------------------|
| `Collection`[Laminas\Form\Element\Collection]| Colección de elementos.                            |
|--------------------------------|---------------------------------------------------------------|

En la tabla de arriba podemos ver que los elementos del formulario que provee
Laminas están directamente asociados con los campos de entrada de HTML4 y HTML5
(discutido al comienzo de este capítulo).

Además, por conveniencia Laminas provee varios campos «compuestos». El campo
@`MultiCheckbox` es un campo que está compuesto por un grupo de casillas de
verificación relacionadas unas con otras. Los elementos @`DateTimeSelect`[Laminas\Form\Element\DateTimeSelect],
@`DateSelect`[Laminas\Form\Element\DateSelect] y @`MonthSelect`[Laminas\Form\Element\MonthSelect] son análogos a sus correspondientes elementos
HTML5 pero simulan ser un campo de selección usual. Estos campos de entrada
tienen la ventaja de que son soportados por todos los navegadores web a
diferencia de los campos correspondientes a HTML5. La representación visual
de estos elementos se pueden ver en la figura 7.12.

![Figura 7.12. Campos de formulario compuesto](../en/images/forms/compound_form_fields.png)

Además, Laminas provee los campos de «seguridad» @`Captcha`[Laminas\Form\Element\Captcha] y @`Csrf`[Laminas\Form\Element\Csrf] que se pueden
usar en un formulario para mejorar la seguridad. El elemento @`Captcha`[Laminas\Form\Element\Captcha] es un
elemento gráfico (imagen) que se coloca en el formulario para revisar si el
usuario del sitio es un humano o un robot. El elemento @`Csrf`[Laminas\Form\Element\Csrf] no tiene una
representación visual y se usa para prevenir los ataques CSRF [^csrf] de
crackers.

[^csrf]: CSRF en ingles cross-site request forgery es un tipo de ataque a
         sitios web (exploit) por medio del cual comandos no autorizados son
         ejecutados por un usuario de confianza del sitio.

Existe otro elemento especial de formulario llamado @`Collection`. Este elemento
es análogo a un conjunto de datos (fieldset), porque este nos permite agrupar
elementos del formulario relacionados. Pero, esta diseñado para agregar elementos
al formulario dinámicamente mediante la asociación de un arreglo de objetos
al formulario.

### Agregar Elemento a un Formulario

Los métodos heredados por la clase base @`Form`[Laminas\Form\Form] de la clase @`Fieldset`
se usan para agregar elementos (y conjuntos de campos) al modelo de
formulario. Estos métodos se resumen en la tabla 7.8.

{title="Tabla 7.8. Métodos provisto por la clase Fielset"}
|----------------------------------|---------------------------------------------------------------|
| *Nombre del método*              | *Descripción*                                                 |
|----------------------------------|---------------------------------------------------------------|
| `add($elementOrFieldset, $flags)`| Agrega un elemento o un conjunto de elementos.                |
|----------------------------------|---------------------------------------------------------------|
| `has($elementOrFieldset)`        | Revisa si determinado elemento fue agregado.                  |
|----------------------------------|---------------------------------------------------------------|
| `get($elementOrFieldset)`        | Recupera un elemento dado (o un conjunto) a partir de su nombre. |
|----------------------------------|---------------------------------------------------------------|
| `getElements()`                  | Recupera todos los elementos agregados.                       |
|----------------------------------|---------------------------------------------------------------|
| `getFieldsets()`                 | Recupera todos los conjuntos de elementos.                    |
|----------------------------------|---------------------------------------------------------------|
| `count()`                        | Recupera el número de elementos o conjunto de elementos agregados. |
|----------------------------------|---------------------------------------------------------------|
| `remove($elementOrFieldset)`     | Remueve un elemento o cojunto de elementos.                   |
|----------------------------------|---------------------------------------------------------------|

Especialmente, estamos interesados en el método `add()` que se usa para
añadir un elemento al formulario. El método `add()` toma dos argumentos:
el primero de ellos (llamado `$elementOrFieldset`) es el elemento que se
insertará y el segundo argumento (llamado `$flags`) es una bandera opcional.

El parámetro `$elementOrFieldset` puede ser una instancia de una clase @`Element`
derivada (o la clase @`Fieldset`) o un arreglo describiendo el elemento que
debe ser creado.

El argumento opcional `$flags` es un arreglo que puede contener una combinación
de las siguientes llaves: `name` (permite colocar el nombre del elemento) y
`priority` (permite especificar el índice, comenzando en cero, en la lista de
elementos donde se insertará). Si la bandera de prioridad no se especifica
el elemento se insertará al final de la lista de elementos del modelo de
formulario.

Abajo, proveemos dos ejemplos que ilustran dos maneras posibles de agregar
elementos al formulario.

### Método 1: Pasar una Instancia de un Elemento

El siguiente fragmento de código crea una instancia de la clase
@`Laminas\Form\Element\Text` y agrega el elemento al modelo de formulario:

~~~php
<?php
namespace Application\Form;

// Define an alias for the class name
use Laminas\Form\Form;
use Laminas\Form\Element\Text;

// A feedback form model
class ContactForm extends Form
{
  // Constructor.
  public function __construct()
  {
    // Create the form fields here ...
    $element = new Text(
                'subject',            // Name of the element
                [                     // Array of options
                 'label'=> 'Subject'  // Text label
                ]);
    $element->setAttribute('id', 'subject');

    // Add the "subject" field to the form
    $this->add($element);
  }
}
~~~

En el código de arriba hemos creado una instancia de la clase
`Laminas\Form\Element\Text` (línea 15). La clase constructora toma dos parámetros:
el nombre del elemento ("subject") y una arreglo de opciones (que aquí usamos
para especificar la etiqueta de texto "Subject").

Además, podemos configurar un elemento usando los métodos provistos por la
clase base @`Element`. Por ejemplo, en la línea 20, colocamos el atributo «id»
con el método `setAttribute()`. Para nuestra referencia, los método más
importantes de la clase base @`Element` que se pueden usar para configurar un
elemento del formulario se presentan en la tabla 7.9.

{title="Tabla 7.9. Métodos provistos por la clase Element"}
|----------------------------------|---------------------------------------------------------------|
| *Nombre del método*              | *Descripción*                                                 |
|----------------------------------|---------------------------------------------------------------|
| `setName($name)`                 | Coloca el nombre del elemento.                                |
|----------------------------------|---------------------------------------------------------------|
| `getName()`                      | Recupera el nombre del elemento.                              |
|----------------------------------|---------------------------------------------------------------|
| `setOptions($options)`           | Coloca opciones.                                              |
|----------------------------------|---------------------------------------------------------------|
| `getOptions($options)`           | Recupera opciones.                                            |
|----------------------------------|---------------------------------------------------------------|
| `getOption($option)`             | Recupera una opción dada.                                     |
|----------------------------------|---------------------------------------------------------------|
| `setAttribute($key, $value)`     | Coloca un atributo al elemento.                               |
|----------------------------------|---------------------------------------------------------------|
| `getAttribute($key)`             | Recupera una atributo del elemento.                           |
|----------------------------------|---------------------------------------------------------------|
| `removeAttribute($key)`          | Quitar un atributo.                                           |
|----------------------------------|---------------------------------------------------------------|
| `hasAttribute($key)`             | Revisa si un atributo determinado está presente.              |
|----------------------------------|---------------------------------------------------------------|
| `setAttributes($arrayOrTraversable)`| Coloca un grupo de atributos.                              |
|----------------------------------|---------------------------------------------------------------|
| `getAttributes()`                | Recupera todos los atributos.                                 |
|----------------------------------|---------------------------------------------------------------|
| `clearAttributes()`              | Quita todos los atributos.                                    |
|----------------------------------|---------------------------------------------------------------|
| `setValue()`                     | Coloca el valor de un elemento.                               |
|----------------------------------|---------------------------------------------------------------|
| `getValue()`                     | Recupera el valor de un elemento.                             |
|----------------------------------|---------------------------------------------------------------|
| `setLabel()`                     | Coloca la etiqueta de un elemento.                            |
|----------------------------------|---------------------------------------------------------------|
| `getLabel()`                     | Recupera la etiqueta de un elemento.                          |
|----------------------------------|---------------------------------------------------------------|
| `setLabelAttributes()`           | Coloca los atributos de una etiqueta.                         |
|----------------------------------|---------------------------------------------------------------|
| `getLabelAttributes()`           | Recupera los atributos de una etiqueta.                       |
|----------------------------------|---------------------------------------------------------------|
| `setLabelOptions()`              | Coloca las opciones de una etiqueta.                          |
|----------------------------------|---------------------------------------------------------------|
| `getLabelOptions()`              | Recupera las opciones de una etiqueta.                        |
|----------------------------------|---------------------------------------------------------------|

### Método 2: Usando un Arreglo con las Especificaciones

El segundo ejemplo que se muestra abajo es equivalente al primero y muestra
como usar un arreglo de especificaciones para agregar un elemento al formulario.
Este método es preferibles porque requiere menos código que escribir.

I> Cuando se usa un arreglo de especificaciones para agregar una elemento al
I> formulario el elemento será instanciado y configurado automáticamente.
I> Internamente esto es posible con la ayuda de la clase fábrica
I> @`Laminas\Form\Factory` (como se muestra en la figura 7.13).

![Figura 7.13. La lógica del método add()](../en/images/forms/factory_graph.png)


~~~
<?php
namespace Application\Form;

// Define an alias for the class name
use Laminas\Form\Form;

// A feedback form model
class ContactForm extends Form
{
  // Constructor.
  public function __construct()
  {
    // Add "subject" field
    $this->add([
      'type'  => 'text',        // Element type
      'name' => 'subject',      // Field name
      'attributes' => [         // Array of attributes
        'id'  => 'subject',
      ],
      'options' => [            // Array of options
         'label' => 'Subject',  // Text label
      ],
    ]);
  }
}
~~~

Arriba en la línea 14 llamamos al método `add()` del modelo de formulario para
agregar un elemento al formulario. Pasamos las especificaciones del elemento
al método `add()` en forma de arreglo. El arreglo tiene generalmente las
siguientes llaves:

* La llave `type` (línea 15) define el nombre de la clase que se usará para
  instanciar el elemento. Aquí podemos usar el nombre completo de la clase
  (por ejemplo, `Text::class`) o su alias [^alias] (por ejemplo, "text").

* La llave `name` (línea 16) define el nombre del campo ("subject").

* La llave `attributes` (línea 17) define la lista de atributos HTML que se
  colocarán (en esta ocasión colocamos el atributo "id").

* El arreglo de `options` (línea 18) permite especificar el texto de la etiqueta
  para el elemento.

[^alias]: Si no sabemos de donde sacamos los alias de los elementos del
          formulario, debes saber que ellos están definidos dentro de la
          clase @`Laminas\Form\FormElementManager\FormElementManagerTrait`.

## Ejemplo: Crear el Modelo del Formulario de Contacto

Ahora que sabemos como colocar el nombre del formulario, la acción y los
métodos para los atributos y como agregar campos (elementos) al formulario
vamos a completar la clase modelo para el formulario de contacto que usamos
en el ejemplo anterior.

Como sabemos las clases de modelo para el módulo `Application` están dentro
del namespace `Application\Form`. Así, debemos crear el archivo *ContactForm.php*
dentro de la carpeta *Form* que esta dentro de la carpeta *src* del módulo
*Application* (figura 7.14).

![Figura 7.14. Carpeta Form](../en/images/forms/form_dir.png)

Tendremos dos métodos en nuestra clase formulario:

* `__construct()` constructor definirá el nombre del formulario y el método
  (POST) e inicializa el formulario agregando sus elementos.
* El método privado `addElements()` contendrá el código actual para agregar
  los elementos del formulario y será llamado por el constructor.

I> Colocamos la lógica de creación de campos dentro del método privado
I> `addElements()` para tener una mejor estructura en el código del modelo
I> del formulario.

El código de la clase `ContactForm` se muestra abajo:

~~~php
<?php
namespace Application\Form;

use Laminas\Form\Form;

/**
 * This form is used to collect user feedback data like user E-mail,
 * message subject and text.
 */
class ContactForm extends Form
{
  // Constructor.
  public function __construct()
  {
    // Define form name
    parent::__construct('contact-form');

    // Set POST method for this form
    $this->setAttribute('method', 'post');

    // Add form elements
    $this->addElements();
  }

  // This method adds elements to form (input fields and
  // submit button).
  private function addElements()
  {
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

    // Add "subject" field
    $this->add([
            'type'  => 'text',
            'name' => 'subject',
            'attributes' => [
              'id' => 'subject'
            ],
            'options' => [
                'label' => 'Subject',
            ],
        ]);

    // Add "body" field
    $this->add([
            'type'  => 'text',
            'name' => 'body',
            'attributes' => [
			  'id' => 'body'
            ],
            'options' => [
                'label' => 'Message Body',
            ],
        ]);

    // Add the submit button
    $this->add([
            'type'  => 'submit',
            'name' => 'submit',
            'attributes' => [
                'value' => 'Submit',
            ],
        ]);
    }
}
~~~

Arriba en la línea 10 definimos la clase `ContactForm` que extiende a la clase
base @`Form`[Laminas\Form\Form].

En las líneas 13-23 tenemos el método constructor. Él llama al constructor
de la clase base (línea 16) y pasa el nombre del formulario como su argumento
("contact-form"). En la línea 19, el método `setAttribute()` de la clase base
se llama para colocar el nombre del método para el formulario, en este caso
colocamos el método POST.

En la línea 22 se llama al método privado `addElements()` que es quien realmente
agrega los elementos al formulario. El código del método `addElements()`
está entre las líneas 27-73. Para agregar elementos al formulario llamamos
al método `add()` que provee la clase base. Este método acepta un solo
argumento, un arreglo que contiene la configuración del elemento. Agregamos
cuatro campos: el `email`, el `subject`, el `body` y el campo `submit`.

En la figura 7.15 podemos ver una representación gráfica esquemática del modelo
de formulario que hemos creado.

![Figura 7.15. El modelo del formulario de contacto y sus elementos](../en/images/forms/form_model.png)

## Agregar Filtros al Formulario y Reglas de Validación

La validación de formularios es el procedimiento de filtrar y revisar los datos
enviados al servidor luego de hacer clic en el botón submit del formulario.
Por ejemplo, queremos hacer las siguientes revisiones en el formulario de
contacto:

* Queremos revisar que la dirección de correo electrónico, el asunto del
  mensaje y el cuerpo del mensaje están presentes (porque son campos *required*).

* Queremos estar seguros de que el usuario ingreso una dirección de
  correo electrónico valida como *name@example.com*.

* El usuario podría agregar un espacio en blanco al principio y/o al
  final de la dirección de correo electrónico por lo que deberíamos
  filtrar esos caracteres ejecutando una operación para cortarlos.

* Sería útil revisar el tamaño mínimo y máximo permitido para el asunto
  del mensaje y del cuerpo de texto.

* Para el asunto del mensaje nos gustaría quitar los caracteres de salto
  de línea y las etiquetas HTML [^html].

* Además, queremos quitar las etiquetas HTML del cuerpo del mensaje.

[^html]: Podrían haber usuarios maliciosos que agregan código HTML en
         el mensaje. Si abrimos ese código en el navegador podríamos
         ver contenido indeseado. Para evitar esto necesitamos
         reemplazar las etiquetas HTML del asunto y del cuerpo del
         mensaje.

Los requerimientos de arriba se llaman *filtros y reglas de validación*.
Estas reglas pueden ser divididas dentro de dos categorías: filtros
y validadores.

Los *filtros* transforman los datos ingresados por el usuario para
reparar errores y asegurar que cumplen un determinado formato.
Los filtros generalmente se aplican primero y los validadores se aplican de
último.

Los *validadores* revisan si los datos son aceptables o no. Si todos los datos
son correctos el formulario se considera valido y los datos pueden ser usados
por la capa de la lógica de negocio. Si determinado campo es invalido, un
validador levanta una bandera de error. En este caso, generalmente el formulario
se muestra de nuevo al usuario al que se le pide que corrija cualquier error
de entrada y que reenvíe el formulario al servidor.

Q> **¿Que sucede si no se agrega una regla de validación a un determinado
Q> campo del formulario?**
Q>
Q> Si no se agrega una regla de validación entonces los valores de los campos enviados por el
Q> usuario no serán revisados dejando un hueco en la seguridad del sitio web.
Q> Siempre es recomendable agregar una regla de validación a cada campo
Q> que el usuario llena del formulario y agregar tantas revisiones por campo
Q> como sea necesario para mantener el formulario seguro.

### Filtros de Entrada

En Laminas guardamos los filtros y la reglas de validación con la ayuda de la clase
@`InputFilter`[Laminas\InputFilter\InputFilter]. La clase
@`InputFilter`[Laminas\InputFilter\InputFilter] está definida en el componente
@`Laminas\InputFilter`. Un filtro de entrada es un contenedor para las *entradas*
(inputs). Generalmente agregamos un filtro de entrada por cada campo de nuestro
modelo de formulario.

I> Una entrada puede consistir en filtros y/o validadores y alguna información
I> adicional. Por ejemplo, una entrada puede contener la bandera que señala
I> si el campo es obligatorio o si su valor puede faltar en la petición HTTP.

De una manera análoga a como agregamos campos al modelo del formulario existen
dos formas de agregar entradas al contenedor de filtros: pasando una instancia
de la clase entrada como argumento del método `add()` o pasando un
arreglo de especificaciones [^inputfactory]. En la sección siguiente
describiremos la última forma, ella es preferible porque necesitamos escribir
menos código.

[^inputfactory]: En el caso siguiente en el que usamos un arreglo de
                 especificaciones la entrada se creará automáticamente
                 con la ayuda de la clase @`Laminas\InputFilter\Factory`.

### Agregar Entradas al Filtro de Entrada

Para agregar una entrada al filtro de entrada usamos el método `add()` que
toma un solo argumento, un arreglo con las especificaciones de la entrada como
se muestra a continuación:

~~~php
[
  'name'     => '<name>',
  'type'     => '<type>',
  'required' => <required>,
  'filters'  => [
     // Add filters configuration here ...
  ],
  'validators' => [
     // Add validators configuration here ...
  ]
]
~~~

En el arreglo de arriba tenemos las siguientes llaves:

* La llave `name` (línea 2) define el nombre de la entrada. El nombre debe ser
  el mismo que el nombre del campo en el modelo de formulario. Si el nombre
  de la entrada no coincide con el nombre del campo en el modelo de formulario
  la regla de validacion no se aplicará al campo.

* La llave `type` (línea 3) define el nombre de la clase de entrada. Esta llave
  es opcional. Por defecto (cuando el valor se omite) se usa la clase
  @`Laminas\InputFilter\Input`. Las clases de entrada disponibles se muestran en
  la figura 7.16. En la figura 7.16 la clase @`Input`[Laminas\InputFilter\Input]
  está diseñada para ser usada con valores escalares regulares, el @`ArrayInput`
  se usa para filtrar/validar arreglo de valores y @`FileInput` se usa para
  revisar los archivos cargados.

* La llave `required` (línea 4) dice si el campo del formulario es obligatorio
  u opcional. Si el campo es obligatorio el usuario del sitio deberá llenarlo
  de lo contrario recibirá un error de validación.

* Los llaves `filters` (línea 5) y `validators` (línea 8) pueden contener la
  configuración de ninguno, uno o varios filtros y/o validadores que se aplican
  al campo del modelo de formulario.

![Figura 7.16. Herencia de la clase Input](../en/images/forms/input_inheritance.png)

#### Configuración del Filtro

La configuración de un filtro típico se muestra abajo:

~~~php
[
  'name' => '<filter_name>',
  'priority' => <priority>,
  'options' => [
    // Filter options go here ...
  ]
],
~~~

La llave `name` (línea 2) es el nombre para el filtro. Este puede ser o el
nombre completo de la clase filtro, es decir `StringTrim::class`, o un
alias, @`StringTrim`.

La llave opcional `priority` (línea 3) define la prioridad del filtro en la
lista de filtros. La prioridad debe ser un número entero. El filtro con la
prioridad más alta será aplicado primero. Por defecto se asigna la constante
`FilterChain::DEFAULT_PRIORITY` (valor 1000).

El arreglo de `options` (línea 4) es especifico para cada filtro
y puede contener parámetros para configurar el filtro.

#### Configuración del Validador

Una configuración típica para el validador se presenta abajo:

~~~php
[
  'name' => '<validator_name>',
  'break_chain_on_failure' => <flag>,
  'options' => [
    // Validator options go here ...
  ]
],
~~~

La llave `name` (línea 2) es el nombre para el validador. Este puede ser el
nombre completo de la clase validador (`EmailAddress::class`) o un alias
(@`EmailAddress`).

La llave opcional `break_chain_on_failure` (línea 3) define el comportamiento
en el caso de que el validador falle. Si es igual a `true` los siguientes
validadores en la lista no serán ejecutados de lo contrario cada validador
en la lista será ejecutado sin depender del resultado de los otros validadores.

El arreglo de `options` (línea 4) es especifico para una determinada clase
validador y puede contener parámetros para configurar el validador.

#### Añadir el Filtro de Entrada al Modelo de Formulario

Una vez que hemos creado y llenado el contenedor para el filtro de entrada
debemos añadirlo al modelo del formulario. La clase base @`Form`[Laminas\Form\Form] provee el
método `setInputFilter()` que esta pensado con este propósito (ver tabla 7.10).

{title="Tabla 7.10. Métodos que provee la clase base Form"}
|--------------------------------|---------------------------------------------------------------|
| *Nombre del Método*            | *Descripción*                                                 |
|--------------------------------|---------------------------------------------------------------|
| `setInputFilter($inputFilter)` | Añade el contenedor de filtro de entrada al formulario.       |
|--------------------------------|---------------------------------------------------------------|
| `getInputFilter()`             | Recupera el filtro de entrada añadido a un formulario.        |
|--------------------------------|---------------------------------------------------------------|

### Crear un Filtro de Entrada para el Formulario de Contacto

Ahora que tenemos un idea general sobre como definir y llenar el contenedor
del filtro de entrada con filtros y validadores para cada campo vamos a
completar nuestra clase de modelo de formulario `ContactForm`. Abajo agregamos
el método privado `addInputFilter()` que define las reglas de filtrado/validación,
las guarda en el contenedor de filtro de entrada y asocia el filtro de
entrada al modelo de formulario.

~~~php
<?php
// ...
use Laminas\InputFilter\InputFilter;

class ContactForm extends Form
{
  public function __construct()
  {
    // ... call this method to add filtering/validation rules
    $this->addInputFilter();
  }

  // ...

  // This method creates input filter (used for form filtering/validation).
  private function addInputFilter()
  {
    $inputFilter = new InputFilter();
    $this->setInputFilter($inputFilter);

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
              'useMxCheck' => false,
            ],
          ],
        ],
      ]
    );

    $inputFilter->add([
        'name'     => 'subject',
        'required' => true,
        'filters'  => [
           ['name' => 'StringTrim'],
           ['name' => 'StripTags'],
           ['name' => 'StripNewlines'],
        ],
        'validators' => [
           [
            'name' => 'StringLength',
              'options' => [
                'min' => 1,
                'max' => 128
              ],
           ],
        ],
      ]
    );

    $inputFilter->add([
        'name'     => 'body',
        'required' => true,
        'filters'  => [
          ['name' => 'StripTags'],
        ],
        'validators' => [
          [
            'name' => 'StringLength',
            'options' => [
              'min' => 1,
              'max' => 4096
            ],
          ],
        ],
      ]
    );
  }
}
~~~

Como podemos ver en el código de arriba primero declaramos el alias para la
clase @`Laminas\InputFilter\InputFilter` (línea 3).

En el constructor del modelo de formulario (línea 10) llamamos al método
`addInputFilter()` que está definido entre las líneas 16-76.

El objetivo del método `addInputFilter()` es crear el contenedor @`InputFilter`[Laminas\InputFilter\InputFilter]
(línea 18), asociarlo al modelo de formulario (línea 19) y agregar
reglas de filtrado/validación (líneas 21-75). Para asociar el filtro de entrada
al modelo de formulario usamos el método `setInputFilter()` provisto por
la clase @`Form`[Laminas\Form\Form]. Para insertar reglas de filtrado/validación dentro del
contenedor de filtro de entrada usamos el método `add()` que provee la clase
@`InputFilter`[Laminas\InputFilter\InputFilter] y que toma el arreglo de especificaciones de la entrada que se
quiere crear.

Agregamos tres entradas una por cada campo de nuestro formulario excepto el
botón submit:

* Para el campo `email` colocamos la bandera `required` en `true` para así obligar
  a que se llene el campo. Usamos el filtro @`StringTrim` para remover los
  espacios en blanco del principio y el final de la dirección de correo
  electrónico. Además, usamos el validador @`EmailAddress` para revisar la
  dirección de correo electrónico ingresada por el usuario. Configuramos el
  validador @`EmailAddress` para permitir nombres de dominio como direcciones
  de correo electrónico (la bandera `Laminas\Validator\Hostname::ALLOW_DNS`)
  y desactivamos la revisión del registro MX (colocamos la opción `useMxCheck`
  en `false`).

* Hacemos obligatorio al campo `subject` y usamos el filtro @`StringTrim` para
  remover los espacios en blanco del principio y el final. Además, usamos los
  filtros @`StripNewlines` y @`StripTags` para respectivamente quitar los
  caracteres de nueva línea y las etiquetas HTML. Limitamos la longitud de la
  cadena entre 1 y 128 caracteres usando el validador @`StringLength`.

* Necesitamos que el campo `body` sea obligatorio, usamos el filtro
  @`StripTags` para quitar las etiquetas HTML del texto. Además, usamos el
  validador `StringLength` para limitar el tamaño del texto entre 1 y 4096
  caracteres.

En la figura 7.17 podemos encontrar el esquema gráfico del filtro de entrada
que hemos creado.

![Figura 7.17. El filtro de entrada para el ContactForm](../en/images/forms/input_filter.png)

T> Arriba describimos brevemente como crear un filtro de entrada para el modelo
T> de formulario. Para información detallada sobre lo que mencionamos arriba,
T> filtros y validadores, y sobre otras cosas
T> podemos revisar [Transformar los Datos de Entrada con Filtros](#filters)
T> y [Revisar los Datos de Entrada con Validadores](#validators)
T> en donde encontraremos además ejemplos de uso.

## Usar el Formulario en una Acción de Controlador

Cuando la clase para el modelo de formulario esta lista podemos usar finalmente
el formulario en un método de acción del controlador.

Como ya deberíamos saber la manera en que el sitio funciona junto al formulario
es generalmente un proceso interactivo (ilustrado esquemáticamente en la figura
7.18).

![Figura 7.18. Flujo de trabajo típico del uso de un formulario](../en/images/forms/form_workflow.png)

 * Primero mostramos el formulario y sus campos en una página, un cursor
   indicará los campos que el usuario debe llenar. El usuario llena los
   campos del formulario y hace clic en el botón *Submit* que envía los datos
   al servidor.

 * Luego nuestro controlador extrae los datos enviados y pide su validación
   al modelo de formulario. Si existen errores de entrada se muestra de nuevo
   el formulario pidiendo al usuario que corrija los errores de entrada. Si
   los datos son correctos procesamos los datos con la capa de lógica de negocio
   y (generalmente) dirigimos al usuario a otra pagina web.

La clase base @`Form`[Laminas\Form\Form] provee varios métodos para alcanzar este objetivos
(ver tabla 7.11).

{title="Tabla 7.11. Métodos provistos par la clase base Form"}
|----------------------------------|---------------------------------------------------------------|
| *Nombre del método*              | *Descripción*                                                 |
|----------------------------------|---------------------------------------------------------------|
| `setData($data)`                 | Coloca los datos del formulario para la validación.           |
|----------------------------------|---------------------------------------------------------------|
| `getData($flag)`                 | Recupera los datos validados.                                 |
|----------------------------------|---------------------------------------------------------------|
| `isValid()`                      | Valida el formulario.                                         |
|----------------------------------|---------------------------------------------------------------|
| `hasValidated()`                 | Revisa si el formulario ha sido validado.                     |
|----------------------------------|---------------------------------------------------------------|
| `getMessages($elementName = null)`| Regresa una lista de mensajes de fracaso en la validación,   |
|                                  | si se indica solo para un elemento o para todos los           |
|                                  | elementos del formulario.                                     |
|----------------------------------|---------------------------------------------------------------|

Así el flujo de uso de un formulario genérico es el siguiente:

* Revisar si los datos del formulario se han enviado y si no mostrar el
  formulario en la página web.

* Si los datos fueron enviados por el usuario del sitio se recuperan los datos
  crudos desde la variable `POST` (y/o `GET`) en forma de un arreglo.

* Los datos se asignan a los campos del modelo de formulario usando el método
  `setData()`.

* Los filtros y validadores son ejecutados usando el método de formulario
  `isValid()` (esto da como resultado la ejecución de los filtros de entrada
  asociados al formulario). Si determinados campos son inválidos se muestra el
  formulario nuevamente y se pide al usuario que corrija los datos ingresados.

* Tan pronto como los datos se han filtrado/validado recuperamos los datos del
  formulario el modelo del formulario usa el método `getData()` y podemos pasar
  los datos a otros modelos o usarlos de cualquier otra manera.

El código de abajo muestra como implementar este flujo de trabajo típico en
nuestro método de acción en el controlador:

~~~php
<?php
namespace Application\Controller;

use Application\Form\ContactForm;
// ...

class IndexController extends AbstractActionController
{
  // This action displays the feedback form
  public function contactUsAction()
  {
    // Create Contact Us form
    $form = new ContactForm();

    // Check if user has submitted the form
    if($this->getRequest()->isPost())
    {
      // Fill in the form with POST data
      $data = $this->params()->fromPost();
      $form->setData($data);

      // Validate form
      if($form->isValid()) {

        // Get filtered and validated data
        $data = $form->getData();

        // ... Do something with the validated data ...

        // Redirect to "Thank You" page
        return $this->redirect()->toRoute('application', ['action'=>'thankYou']);
      }
    }

    // Pass form variable to view
    return new ViewModel([
          'form' => $form
       ]);
  }
}
~~~

En el código de arriba definimos el método de acción `contactUsAction()` dentro
de la clase `IndexController` (línea 10). En el método de acción creamos una
instancia de la clase `ContactForm` (línea 13).

Luego en la línea 16 revisamos si la petición es una petición POST (revisando
la primera línea de la petición HTTP).

En la línea 19 recuperamos los datos crudos enviados por el usuario. Extraemos
todas las variables POST con la ayuda del complemento para controladores @`Params`.
Los datos se regresan en forma de un arreglo y se guardan dentro de la variable
`$data`.

Los datos enviados por el usuario pueden contener errores y deberían ser filtrados
y validados antes de su uso. Para hacer esto, en la línea 20 colocamos los datos
en el modelo de formulario con el método `setData()` que provee la clase base
`Form`. Validamos los datos del formulario con el método `isValid()` (línea 23),
que regresa `true` si la validación es exitosa. Si la validación es exitosa
recuperamos los datos validados usando el método `getData()` (línea 26) y luego
podemos pasar los datos a nuestra capa de lógica de negocio.

Una vez que hemos usado los datos validados en la línea 31 dirigimos al
usuario de la página web a la página *Gracias a ti*. Para redirigir se ejecuta
el complemento para controladores @`Redirect`. El método `toRoute()` del
complemento @`Redirect` toma dos parámetros: el primer parámetro es el nombre
de la ruta («application») y el segundo parámetro es el arreglo
de parámetros que se pasa al enrutador. Estos identifican la página web
a donde redirigimos el usuario.

I> Prepararemos la acción de controlador y la plantilla de vista para la página
I> *Gracias a ti* un poco más adelante.

En la línea 37 pasamos el modelo de formulario a través de la variable `$form`
a la plantilla de vista. La plantilla de vista tendrá acceso a esta variable
y la usará para mostrar en pantalla el formulario (y los posibles errores de
validación).

### Pasar los Datos del Formulario al Modelo

Para dar un ejemplo realista de como usar la validación de datos en el formulario
de contacto crearemos en esta sección una clase de modelo simple llamada `MailSender` [^service]
que se usará para enviar un correo electrónico a una dirección de correo electrónico.
Cuando el usuario envía el formulario validamos los datos del formulario y pasamos
los datos validados al modelo `MailServer` pidiéndole que envíe el correo electrónico
al destinatario.

[^service]: En la terminología DDD `MailSender` se puede considerar como un modelo
            de servicio porque su objetivo es manipular datos y no guardar datos.

T> La lectura de esta sección es opcional y esta pensada fundamentalmente para
T> principiantes. Podemos saltarla y revisar directamente la siguiente sección
T> *Form Presentation*.

El modelo `MailSender` usará internamente el componente @`Laminas\Mail`. El componente
@`Laminas\Mail` es un componente que provee Laminas Framework diseñado para proveer las
funcionalidades necesarias para construir mensajes de correo electrónico (la clase
@`Laminas\Mail\Message`) y varias clases que implementan los diferentes transportes
para enviar correos electrónicos (en este ejemplo usaremos la clase
@`Laminas\Mail\Transport\Sendmail` que usa el programa *sendmail* para enviar correos
electrónicos).

I> Instalamos el componente @`Laminas\Mail` con Composer escribiendo el siguiente
I> comando:
I>
I> `php composer.phar require laminas/laminas-mail`

I> El programa [sendmail](http://www.sendmail.com/sm/open_source/) es un MTA
I> (Mail Transfer Agent) de software libre para sistemas GNU/Linux y Unix.
I> Este MTA acepta mensajes pasados por scripts de PHP y decide en base a la
I> cabecera del mensaje que método de envío debe usar y luego pasa el mensaje
I> mediante el protocolo SMTP al servidor de correo apropiado (como Google Mail)
I> para enviarlo al destinatario.

Comenzaremos creando el archivo *MailSender.php* dentro de la carpeta *Service*
que está dentro de la carpeta *src* del módulo (ver la figura 7.19).

![Figura 7.19. Crear el archivo MailSender.php](../en/images/forms/mailsender.png)

El siguiente es el código que deberíamos colocar dentro del archivo *MailSender.php*:

~~~php
<?php
namespace Application\Service;

use Laminas\Mail;
use Laminas\Mail\Message;
use Laminas\Mail\Transport\Sendmail;

// This class is used to deliver an E-mail message to recipient.
class MailSender
{
  // Sends the mail message.
  public function sendMail($sender, $recipient, $subject, $text)
  {
    $result = false;
    try {

      // Create E-mail message
      $mail = new Message();
      $mail->setFrom($sender);
      $mail->addTo($recipient);
      $mail->setSubject($subject);
      $mail->setBody($text);

      // Send E-mail message
      $transport = new Sendmail('-f'.$sender);
      $transport->send($mail);
      $result = true;
    } catch(\Exception $e) {
      $result = false;
    }

    // Return status
    return $result;
  }
}
~~~

En el código de arriba definimos el namespace `Application\Service` (línea 2)
porque la clase `MailSender` se comporta como un modelo de servicio (su objetivo
es manipular los datos y no guardarlos).

En las líneas 4-6 declaramos los alias para las clases @`Mail`, @`Message`[Laminas\Mail\Message] y
@`Laminas\Mail\Transport\Sendmail` que provee el componente @`Laminas\Mail`.

En las líneas 9-35 definimos la clase `MailSender`. La clase tiene un único
método `sendMail()` (línea 12) que toma cuatro argumentos: remitente del
correo electrónico, destinatario del correo, el asunto del mensaje y el cuerpo
del mensaje.

En la línea 18 creamos una instancia de la clase @`Message`[Laminas\Mail\Message]. Usamos los métodos
que provee esta clase para construir el mensaje (colocar el asunto, el cuerpo,
etc) en las líneas 19-22.

En la línea 25 creamos una instancia de la clase @`Sendmail` que usa el programa
*sendmail* para pasar el mensaje al servidor de correo apropiado (ver líneas 25-26).
Como las clases que provee el componente @`Laminas\Mail` pueden lanzar una excepción
en el caso de un fallo encerramos el bloque de código entre el administrador de
excepciones `try`-`catch`.

El método `sendMail()` regresará `true` si el correo electrónico se envía
con éxito de lo contrario regresará `false` (línea 33).

I> Configurar un sistema de correo para nuestro servidor web es una tarea que
I> reviste algo de complejidad. Generalmente es necesario instalar sendmail y
I> configurar el registro MX DNS del servidor para usar determinado servidor
I> de correo (o también un servidor de correo local, por ejemplo,
I> [Postfix](http://www.postfix.org/) o un servidor remoto como Google Mail).
I> Por la complejidad del problema este no se discute en este libro. Podemos
I> encontrar en internet información adicional sobre la configuración de un
I> servidor de correo electrónico para nuestra sistema en particular.

Ahora vamos a registrar el servicio `MailSender` en nuestro archivo
`module.config.php` de la siguiente manera:

~~~php
return [
    //...
    'service_manager' => [
        'factories' => [
            Service\MailSender::class => InvokableFactory::class,
        ],
    ],

    //...
];
~~~

Luego vamos a instanciar el modelo `MailSender` en nuestro método
`IndexController::contactUsAction()` y le pasaremos los datos del formulario
validado.

I> Como usamos el servicio `MailSender` en nuestro controlador este servicio
I> es una *dependencia* de nuestro controlador. Así, necesitaremos crear una
I> fábrica para el controlador e *inyectar* la dependencia dentro de constructor
I> del controlador. Parece complejo a primera vista pero a medida que
I> mejoremos nuestras habilidades encontraremos que esto más simple y que mejora
I> enormemente la estructura del código.

Vamos a crear una fábrica para el `IndexController`, la colocaremos dentro de la
subcarpeta `Factory` que esta dentro de la subcarpeta `Controller`. Como podemos
ver el único trabajo de la clase fábrica es crear el controlador y pasarle la
dependencia.

~~~php
<?php
namespace Application\Controller\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Application\Service\MailSender;
use Application\Controller\IndexController;

class IndexControllerFactory
{
    public function __invoke(ContainerInterface $container,
                             $requestedName, array $options = null)
    {
        $mailSender = $container->get(MailSender::class);

        // Instantiate the controller and inject dependencies
        return new IndexController($mailSender);
    }
}
~~~

Modificamos el archivo `module.config.php` para usar la fábrica ajustada a la
medida que acabamos de crear:

~~~php
return [
    //...
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => Controller\Factory\IndexControllerFactory::class,
        ],
    ],

    //...
];
~~~

Luego agregamos el constructor y los métodos `contactUsAction()`, `thankYouAction()`
y `sendErrorAction()` al controlador. Abajo mostramos todo el código:

~~~php
<?php
// ...
use Application\Service\MailSender;

class IndexController extends AbstractActionController
{
  private $mailSender;

  public function __construct($mailSender)
  {
    $this->mailSender = $mailSender;
  }

  public function contactUsAction()
  {
    // Create Contact Us form
    $form = new ContactForm();

    // Check if user has submitted the form
    if($this->getRequest()->isPost()) {

      // Fill in the form with POST data
      $data = $this->params()->fromPost();

      $form->setData($data);

      // Validate form
      if($form->isValid()) {

        // Get filtered and validated data
        $data = $form->getData();
        $email = $data['email'];
        $subject = $data['subject'];
        $body = $data['body'];

        // Send E-mail
        if(!$this->mailSender->sendMail('no-reply@example.com', $email,
                        $subject, $body)) {
          // In case of error, redirect to "Error Sending Email" page
          return $this->redirect()->toRoute('application',
                        ['action'=>'sendError']);
        }

        // Redirect to "Thank You" page
        return $this->redirect()->toRoute('application',
                        ['action'=>'thankYou']);
      }
    }

    // Pass form variable to view
    return new ViewModel([
      'form' => $form
    ]);
  }

  // This action displays the Thank You page. The user is redirected to this
  // page on successful mail delivery.
  public function thankYouAction()
  {
    return new ViewModel();
  }

  // This action displays the Send Error page. The user is redirected to this
  // page on mail delivery error.
  public function sendErrorAction()
  {
    return new ViewModel();
  }
}
~~~

Como podemos ver en el código hacemos lo siguiente:

* En la línea 3 declaramos un alias para la clase `Application\Service\MailSender`.
  Esto permitirá referirnos a la clase modelo por su nombre corto.

* En las líneas 32-34 después de que hemos validado el formulario depositamos los
  campos validados dentro de las variables de PHP `$email`, `$subject` y `$body`.

* En la línea 37 llamamos al método `sendMail()` del servicio `MailSender` y
  le pasamos cuatro parámetros: la dirección de correo electrónico del remitente
  (aquí usamos "no-reply@example.com" pero podemos remplazarlo con la dirección
  de nuestro *sendmail*); la dirección de correo del remitente, el asunto y
  cuerpo del correo.

* Si el correo se envía con éxito (si el método `sendMail()` regresa `true`)
  redirigimos al usuario a la página *Gracias a ti* (línea 45). En caso
  de fallar (si el método `sendMail()` regresa `false`) redirigimos al
  usuario a la página *Enviar Error* (línea 40).

* En las líneas 58-61 tenemos al método `thankYouAction()` que muestra la
  página *Gracias a ti*. La página se muestra si el correo electrónico se
  envía exitosamente.

* En las líneas 65-68 tenemos el método `sendErrorAction()` que muestra la
  página *Error al enviar el correo*. Esta página se muestra cuando falla el
  envío del correo electrónico.

## Presentación del Formulario

Cuando nuestra acción de controlador está lista todo lo que tenemos que hacer
es preparar el archivo de plantilla de vista `.phtml` para mostrar nuestro
formulario en una página web. En la plantilla de vista necesitamos definir
la estructura con el uso de `<form>`, `<label>`, `<input>` y quizás de otras
etiquetas HTML.

Además, debemos mostrar los mensajes de error si la validación del formulario
falla. Como este trabajo es aburrido Laminas Framework provee unos ayudantes de
vista especiales con la intención de mostrar el formulario en la pantalla.

T> Para un formulario simple (que no muestra mensajes de error) podemos usar
T> etiquetas HTML crudas y así mostrar el formulario ignorando los ayudantes
T> de vista provistos por Laminas. Pero, los ayudantes de vista son realmente
T> inevitables cuando mostramos formularios complejos que deben mostrar errores
T> de validación y/o agregar campos dinámicamente.

### Preparar el Modelo de Formulario para la Impresión en la Pantalla

Antes de mostrar el formulario en la pantalla es necesario que llamemos al
método `prepare()` en la instancia del modelo de formulario (ver tabla 7.12).
Si olvidamos llamar a este método puede haber efectos indeseables.

{title="Tabla 7.12. Métodos provistos por la clase base Form"}
|--------------------------------|---------------------------------------------------------------|
| *Nombre del método*            | *Descripción*                                                 |
|--------------------------------|---------------------------------------------------------------|
| `prepare()`                    | Asegura que el formulario está listo para usarse.             |
|--------------------------------|---------------------------------------------------------------|

El método `prepare()` hace los siguientes preparativos al modelo de formulario:

  * Llama al contenedor del filtro de entrada asociado al modelo de formulario
    para asegurar que los mensajes de error están disponibles.

  * Prepara cualquier elemento y/o conjunto de elementos que necesiten
    preparación [^wrapping].

[^wrapping]: Generalmente en la preparación los nombres de campos se envuelven
             con el nombre del formulario o del conjunto de elementos (por
             ejemplo, el nombre del campo "email" se convertirá en
             "contact-form[email]") que resulta técnicamente en una agrupación
             de campos en el cuerpo de la petición HTTP más conveniente.

## Ayudantes de Vista Estándares para Formularios

Los ayudantes de vista estándar para formularios provistos por Laminas se muestran en las tablas
7.13 - 7.16 más abajo. Estas clases se encuentran en el namespace @`Laminas\Form\View\Helper`[Laminas\Form].
Como podemos ver en la tabla los ayudantes de vista se pueden dividir en las
siguientes categorías:

* *Ayudantes de vista para formularios genéricas.* Estas clases se diseñaron
  para imprimir el formulario completo (ayudante @`Form`[Laminas\Form\View\Helper\Form]) o un solo elemento
  (ayudante @`FormElement`) y posibles errores de validación (ayudante @`FormElementErrors`).

* *Ayudantes de vista para imprimir campos HTML de un tipo determinado.* Estos
  permiten generar código HTML para campos concretos de formulario (ejemplo,
  @`FormButton`, @`FormRadio`, etc.) y una etiqueta de texto (@`FormLabel`).

* *Ayudantes de vista para imprimir campos de formulario introducidos en HTML5.*
  Estos son análogos a los ayudantes de vista de la categoria anterior pero son
  para imprimir campos HTML5 (ejemplo, @`FormDate`, @`FormUrl`, etc.)

* *Otros ayudantes de vista.* En esta categoría podemos colocar las clases de
  ayudantes de vista diseñadas para imprimir campos específicos de Laminas como
  @`FormMultiCheckbox`, @`FormCaptcha`, etc.

{title="Tabla 7.13. Ayudantes de vista diseñados para usarse con formularios"}
|----------------------------------|---------------------------------------------------------------|
| *Nombre del Método*              | *Descripción*                                                 |
|----------------------------------|---------------------------------------------------------------|
| *Ayudantes Genéricos*            |                                                               |
|----------------------------------|---------------------------------------------------------------|
| @`Form`[Laminas\Form\View\Helper\Form]| Imprime el formulario completo y todos sus elementos.       |
|----------------------------------|---------------------------------------------------------------|
| @`FormElement`                   | Imprime un elemento generico del formulario.                  |
|----------------------------------|---------------------------------------------------------------|
| @`FormElementErrors`             | Imprime errores de validación para un elemento del formulario.|
|----------------------------------|---------------------------------------------------------------|
| @`FormRow`                       | Imprime la etiqueta, el campo y los errores de validación.    |
|----------------------------------|---------------------------------------------------------------|

{title="Tabla 7.14. Ayudantes para campos de HTML 4"}
|----------------------------------|---------------------------------------------------------------|
| *Nombre del Método*              | *Descripción*                                                 |
|----------------------------------|---------------------------------------------------------------|
| @`FormButton`                    | Imprime el campo de formulario `<button>`.                    |
|----------------------------------|---------------------------------------------------------------|
| @`FormCheckbox`                  | Imprime el campo de formulario `<input type="checkbox">`.     |
|----------------------------------|---------------------------------------------------------------|
| @`FormFile`                      | Imprime el campo de formulario `<input type="file">`.         |
|----------------------------------|---------------------------------------------------------------|
| @`FormHidden`                    | Imprime el campo de formulario `<input type="hidden">`.       |
|----------------------------------|---------------------------------------------------------------|
| @`FormInput`                     | Imprime un campo `<input>`.                                   |
|----------------------------------|---------------------------------------------------------------|
| @`FormImage`                     | Imprime el campo de formulario `<input type="image">`.        |
|----------------------------------|---------------------------------------------------------------|
| @`FormLabel`                     | Imprime el campo de formulario `<label>`.                     |
|----------------------------------|---------------------------------------------------------------|
| @`FormPassword`                  | Imprime el campo de formulario `<input type="password">`.     |
|----------------------------------|---------------------------------------------------------------|
| @`FormRadio`                     | Imprime el campo de formulario `<input type="radio">`.        |
|----------------------------------|---------------------------------------------------------------|
| @`FormReset`                     | Imprime el campo de formulario `<input type="reset">`.        |
|----------------------------------|---------------------------------------------------------------|
| @`FormSelect`                    | Imprime el campo de formulario `<select>` (menú desplegable). |
|----------------------------------|---------------------------------------------------------------|
| @`FormSubmit`                    | Imprime el campo de formulario `<input type="submit">`.       |
|----------------------------------|---------------------------------------------------------------|
| @`FormText`                      | Imprime el campo `<input type="text">`.                       |
|----------------------------------|---------------------------------------------------------------|
| @`FormTextarea`                  | Imprime el campo `<textarea>` (texto de multiples líneas).    |
|----------------------------------|---------------------------------------------------------------|


{title="Tabla 7.15. Ayudantes para campos de HTML 5"}
|----------------------------------|------------------------------------------------------------------|
| *Nombre del Método*              | *Descripción*                                                    |
|----------------------------------|------------------------------------------------------------------|
| @`FormColor`                     | Imprime el campo de formulario de HTML 5 `<input type="color">`. |
|----------------------------------|------------------------------------------------------------------|
| @`FormDate`                      | Imprime el campo de formulario de HTML 5 `<input type="date">`.  |
|----------------------------------|------------------------------------------------------------------|
| @`FormDateTime`                  | Imprime el campo de formulario de HTML 5 `<input type="date">`.  |
|----------------------------------|------------------------------------------------------------------|
| @`FormDateTimeLocal`             | Imprime el campo de formulario de HTML 5 `<input type="datetime-local">`. |
|----------------------------------|------------------------------------------------------------------|
| @`FormEmail`                     | Imprime el campo de formulario de HTML 5 `<input type="email">`. |
|----------------------------------|------------------------------------------------------------------|
| @`FormMonth`                     | Imprime el campo de formulario de HTML 5 `<input type="month">`. |
|----------------------------------|------------------------------------------------------------------|
| @`FormNumber`                    | Imprime el campo de formulario de HTML 5 `<input type="number">`.|
|----------------------------------|------------------------------------------------------------------|
| @`FormRange`                     | Imprime el campo de formulario de HTML 5 `<input type="range">`. |
|----------------------------------|------------------------------------------------------------------|
| @`FormTel`                       | Imprime el campo de formulario de HTML 5 `<input type="tel">`.   |
|----------------------------------|------------------------------------------------------------------|
| @`FormTime`                      | Imprime el campo de formulario de HTML 5 `<input type="time">`.  |
|----------------------------------|------------------------------------------------------------------|
| @`FormUrl`                       | Imprime el campo de formulario de HTML 5 `<input type="url">`.   |
|----------------------------------|------------------------------------------------------------------|
| @`FormWeek`                      | Imprime el campo de formulario de HTML 5 `<input type="week">`.  |
|----------------------------------|------------------------------------------------------------------|

{title="Tabla 7.16. Otros ayudantes"}
|----------------------------------|---------------------------------------------------------------|
| *Nombre del Método*              | *Descripción*                                                 |
|----------------------------------|---------------------------------------------------------------|
| @`FormCaptcha`                   | Imprime el campo de seguridad CAPTCHA.                        |
|----------------------------------|---------------------------------------------------------------|
| @`FormDateSelect`                | Imprime el campo de seguridad date.                           |
|----------------------------------|---------------------------------------------------------------|
| @`FormDateTimeSelect`            | Imprime el campo de seguridad datetime.                       |
|----------------------------------|---------------------------------------------------------------|
| @`FormMonthSelect`               | Imprime el campo de selección de mes.                         |
|----------------------------------|---------------------------------------------------------------|
| @`FormMultiCheckbox`             | Imprime el campo para casillas de verificación multiple.      |
|----------------------------------|---------------------------------------------------------------|
| @`FormCollection`                | Imprime el campo para una colección de elementos.             |
|----------------------------------|---------------------------------------------------------------|

En las secciones siguientes daremos una visión general de varios ayudantes de
vista para formularios que se usan frecuentemente junto con unos ejemplos de
su uso.

### Imprimir un Elemento del Formulario

Podemos imprimir un campo del formulario con el ayudante de vista @`FormElement`.
Se diseño pensando en maximizar la flexibilidad y reconoce tanto tipos de campo
como es posible. Con este ayudante de vista podemos producir código HTML para
campos de texto, botones, menús desplegables, etc.

Los métodos que provee este ayudante de vista se listan en la tabla 7.17.

{title="Tabla 7.17. Métodos que provee el ayudante de vista FormElement"}
|----------------------------------|---------------------------------------------------------------|
| *Nombre del método*              | *Descripción*                                                 |
|----------------------------------|---------------------------------------------------------------|
| `render($element)`               | Método mágico de PHP que imprime el campo de formulario dado. |
|----------------------------------|---------------------------------------------------------------|
| `__invoke($element)`             | Método mágico de PHP que imprime el campo de formulario dado  |
|                                  | (el efecto es el mismo que con `render()`).                   |
|----------------------------------|---------------------------------------------------------------|

Como podemos ver existen dos métodos que hacen la misma cosa:

  * El método `render()` produce el código HTML para el campo de formulario.
    Acepta un solo argumento, la instancia del elemento a imprimir. Podemos
    recuperar los elementos del formulario con el método `get()` de modelo del
    formulario (ver el ejemplo más abajo).
  * El método `__invoke()` es un envoltorio conveniente que reduce el código que
    es necesario escribir.

~~~php
<?php
// We assume that the form model is stored in $form variable.
// Render the E-mail field with the render() method.
echo $this->formElement()->render($form->get('email')); ?>

// The same, but with __invoke
echo $this->formElement($form->get('email'));
~~~

Cuando se ejecuta el código de arriba genera el siguiente código HTML:

~~~html
<input type="text" name="email" id="email" value="">
~~~

T> Generalmente no es necesario llamar al ayudante de vista con un campo HTML
T> (o HTML5) concreto (ejemplo, @`FormText`, @`FormSubmit`, etc.)
T> En su lugar podemos usar el ayudante de vista genérico `FormElement` que
T> determina el tipo de campo automáticamente y produce el código HTML necesario.

### Imprimir los Errores de Validación del Elemento

La clase de ayudante de vista @`FormElementErrors` permite producir código HTML
para los campos de validación de errores (si están presentes). Si no existen
errores de validación para un determinado elemento este ayudante de vista no
producen ninguna salida.

Un ejemplo del uso del ayudante de vista @`FormElementErrors` se presenta abajo:

~~~php
<?php
// We assume that the form model is stored in $form variable.
// Render validation errors for the E-mail field.
echo $this->formElementErrors($form->get('email'));
~~~

Si existe algún error de validación este código generará una lista no ordenada
de errores usando la etiqueta HTML `<ul>` y la lista contendrá tantos elementos
como errores existan para el campo. Un ejemplo de una lista como esta para el
campo de correo electrónico del formulario de contacto se muestra abajo.

~~~html
<ul>
  <li>&#039;hostname&#039; is not a valid hostname for the email address</li>
  <li>The input does not match the expected structure for a DNS hostname</li>
  <li>The input appears to be a local network name but local network names are not allowed</li>
</ul>
~~~

### Imprimir la Etiqueta del Elemento

El ayudante @`FormLabel` permite imprimir la etiqueta de texto para un elemento:

~~~php
<?php
// We assume that the form model is stored in $form variable.
// Render text label for the E-mail field.
echo $this->formLabel($form->get('email'));
~~~

Cuando se ejecuta el código de arriba se genera el siguiente código HTML:

~~~html
<label for="email">Your E-mail</label>
~~~

### Imprimir un Formuario en Columna

El ayudate de vista @`FormRow` está diseñado para simplificar la impresión de
un campo de formulario, su etiqueta y los posibles errores de validación.
Con esta clase podemos imprimirlos en un solo paso. Este ayudate es flexible
en su configuración, con lo que se puede aplicar una decoración diferente
a la columna del formulario. Los métodos de esta clase ayudante de vista se listan
en la tabla 7.18.

{title="Tabla 7.18. Métodos provistos por el ayudante de vista FormRow"}
|----------------------------------------|---------------------------------------------------------|
| *Nombre del Método*                    | *Descripción*                                           |
|----------------------------------------|---------------------------------------------------------|
| `render($element)`                     | Imprime una columna del formulario.                     |
|----------------------------------------|---------------------------------------------------------|
| `__invoke($element, $labelPosition, $renderErrors, $partial)` | Imprime una columna del formulario (envoltorio conveniente).|
|----------------------------------------|---------------------------------------------------------|
| `setInputErrorClass($inputErrorClass)` | Coloca la clase CSS para los errores de entrada.        |
|----------------------------------------|---------------------------------------------------------|
| `setLabelAttributes($labelAttributes)` | Coloca atributos de la etiqueta.                        |
|----------------------------------------|---------------------------------------------------------|
| `setLabelPosition($labelPosition)`     | Indica la posición de la etiqueta (antes o después del campo).|
|----------------------------------------|---------------------------------------------------------|
| `setRenderErrors($renderErrors)`       | Establece (boole) si los errores son impresos por este ayudante.|
|----------------------------------------|---------------------------------------------------------|
| `setPartial($partial)`                 | Coloca un script de vista parcial para usar en la       |
|                                        | impresión de la columna.                                |
|----------------------------------------|---------------------------------------------------------|

Un ejemplo de uso del ayudante de vista @`FormRow` se presenta abajo:

~~~php
<?php
// We assume that the form model is stored in $form variable.
// Render the E-mail field, its label and (possible) validation errors.
echo $this->formRow($form->get('email'));
~~~

Cuando se ejecuta el código de arriba se genera un código HTML como el siguiente:

~~~html
<label for="email">Your E-mail</label>
<input type="text" name="email" id="email">
<ul>
  <li>&#039;hostname&#039; is not a valid hostname for the email address</li>
  <li>The input does not match the expected structure for a DNS hostname</li>
  <li>The input appears to be a local network name but local network names
      are not allowed</li>
</ul>
~~~

### Imprimir el Formulario Completo

El ayudante de vista @`Form`[Laminas\Form\View\Helper\Form] permite imprimir la etiqueta de apertura `<form>`
con sus atributos y la etiqueta de cierre `</form>`. Pero el principal propósito
es imprimir el formulario completo y todos sus campos con una sola línea de
código. Los métodos públicos de la clase ayudante de vista @`Form`[Laminas\Form\View\Helper\Form] se resumen en
la tabla 7.19.

{title="Tabla 7.19. Métodos provistos por el ayudante de vista From"}
|----------------------------------|---------------------------------------------------------------|
| *Nombre del Método*              | *Descripción*                                                 |
|----------------------------------|---------------------------------------------------------------|
| `render($form)`                  | Imprime el formulario completo y sus elementos.               |
|----------------------------------|---------------------------------------------------------------|
| `__invoke($form)`                | Método mágico de PHP que imprime el formulario completo y     |
|                                  | todos sus elementos (el efecto es el mismo que con `render()`).|
|----------------------------------|---------------------------------------------------------------|
| `openTag($form)`                 | Imprime la etiqueta de apertura `<form>`.                     |
|----------------------------------|---------------------------------------------------------------|
| `closeTag()`                     | Imprime la etiqueta de cierre `</form>`.                      |
|----------------------------------|---------------------------------------------------------------|

Podemos imprimir todo el formulario con la ayuda del método `render()` de `Form`.

~~~php
// We assume that the form model is stored in $form variable

// Render the whole form
echo $this->form()->render($form);
~~~

El mismo efecto se consigue con el método mágico `__invoke` (ver más abajo):

~~~php
// The same, but with `__invoke`
echo $this->form($form);
~~~

## Ejemplo: Crear la Plantilla de Vista para el Formulario de Contacto

Ahora estamos listos para definir la presentación de nuestro formulario de
contacto. Si recordamos, antes agregamos la plantilla de vista *contact-us.phtml*
en la carpeta *application/index* que esta dentro de la carpeta *view/* del módulo.
Debemos reemplazar el código en este archivo por el siguiente:

~~~php
<?php
$form = $this->form;
$form->prepare();
?>

<?= $this->form()->openTag($form); ?>

<?= $this->formLabel($form->get('email')); ?>
<?= $this->formElement($form->get('email')); ?>
<?= $this->formElementErrors($form->get('email')); ?>

<?= $this->formLabel($form->get('subject')); ?>
<?= $this->formElement($form->get('subject')); ?>
<?= $this->formElementErrors($form->get('subject')); ?>

<?= $this->formLabel($form->get('body')); ?>
<?= $this->formElement($form->get('body')); ?>
<?= $this->formElementErrors($form->get('body')); ?>

<?= $this->formElement($form->get('submit')); ?>

<?= $this->form()->closeTag(); ?>
~~~

Como podemos ver en el código de arriba se hacen las siguientes cosas para
imprimir el formulario:

* En la línea 2 accedemos a la variable `$form` pasada desde la acción del
  controlador.

* En la línea 3 llamamos al método `prepare()` de @`Form`[Laminas\Form\Form] para preparar la impresión
  del formulario. Nótese que llamar a este método es muy importante. Si olvidamos
  hacer esto pueden haber algunos problemas de impresión indeseable.

* En la línea 6 llamamos al método `openTag()` del ayudante de vista @`Form`[Laminas\Form\View\Helper\Form].
  Su propósito es imprimir la etiqueta de apertura `<form>` y sus atributos.
  El método toma un solo argumento, una instancia del modelo de formulario.
  La etiqueta de cierre `</form>` se imprime en la línea 22 con la ayuda del
  método `closeTag()` del ayudante de vista @`Form`[Laminas\Form\View\Helper\Form].

* En las líneas 8-10 imprimimos la etiqueta del campo dirección de correo
  electrónico, el campo propiamente y los posibles errores de validación con
  la ayuda de los ayudantes de vista @`FormLabel`, @`FormElement` y @`FormElementErrors`.
  Estos ayudantes toman la instancia de un elemento del modelo de formulario
  como único argumento. Traemos una instancia del elemento con el método `get()`
  provisto por la clase base @`Form`[Laminas\Form\Form].

* Por analogía en las líneas 12-14 imprimimos el campo *subject*, su etiqueta
  y los errores de validación.

* En las líneas 16-18 imprimimos la etiqueta, el campo y los errores de
  validación para el campo de área de texto *body*.

* En la línea 20 imprimimos el botón *Submit*.

Cuando la plantilla de vista impresa evalúa este código producirá una salida
HTML como la siguiente:

~~~html
<form action="/contact" method="post" name="contact-form">
  <label for="email">Your E-mail</label>
  <input type="text" name="email" id="email" value="">

  <label for="subject">Subject</label>
  <input name="subject" type="text" id="subject" value="">

  <label for="body">Message Body</label>
  <textarea name="body" id="body"></textarea>

  <input name="submit" type="submit" value="Submit">
</form>
~~~

I> En el código de arriba usamos principalmente los ayudantes de vista
I> @`FormElement`, @`FormElementErrors` y @`FormLabel`. Podemos usar los ayudantes
I> de vista genéricos @`FormRow` o @`Form`[Laminas\Form\View\Helper\Form] si queremos reducir la cantidad de
I> código que debemos escribir pero perdiendo el control sobre la decoración
I> del formulario.

Si determinado campo tiene un error de validación estos errores se muestran
debajo del campo del formulario en una lista HTML no ordenada `<ul>`.
Por ejemplo, si colocamos el correo electrónico "123@hostname" dentro del campo
para el correo electrónico recibiremos el siguiente error de validación.

~~~html
<label for="email">Your E-mail</label>
<input type="text" name="email" value="123@hostname">
<ul>
  <li>&#039;hostname&#039; is not a valid hostname for the email address</li>
  <li>The input does not match the expected structure for a DNS hostname</li>
  <li>The input appears to be a local network name but local network names
      are not allowed</li>
</ul>
~~~

### Applying the Bootstrap CSS Styles to Form

El código HTML de arriba no tiene estilos CSS pero queremos usar las clases CSS
de Twitter Bootstrap para dar al formulario una apariencia bonita y profesional.
Para agregar los estilos de Bootstrap al formulario debemos modificar el código
en el archivo *.phtml* para que tenga el siguiente aspecto:

~~~php
<?php
$form = $this->form;
$form->prepare();

$form->get('email')->setAttributes([
  'class'=>'form-control',
  'placeholder'=>'name@example.com'
  ]);

$form->get('subject')->setAttributes([
  'class'=>'form-control',
  'placeholder'=>'Type subject here'
  ]);

$form->get('body')->setAttributes([
  'class'=>'form-control',
  'rows'=>6,
  'placeholder'=>'Type message text here'
  ]);

$form->get('submit')->setAttributes(['class'=>'btn btn-primary']);
?>

<h1>Contact Us</h1>

<p>
  Please fill out the following form to contact us.
  We appreciate your feedback.
</p>

<div class="row">
  <div class="col-md-6">
    <?= $this->form()->openTag($form); ?>

    <div class="form-group">
      <?= $this->formLabel($form->get('email')); ?>
      <?= $this->formElement($form->get('email')); ?>
      <?= $this->formElementErrors($form->get('email')); ?>
    </div>

    <div class="form-group">
      <?= $this->formLabel($form->get('subject')); ?>
      <?= $this->formElement($form->get('subject')); ?>
      <?= $this->formElementErrors($form->get('subject')); ?>
    </div>

    <div class="form-group">
      <?= $this->formLabel($form->get('body')); ?>
      <?= $this->formElement($form->get('body')); ?>
      <?= $this->formElementErrors($form->get('body')); ?>
    </div>

    <?= $this->formElement($form->get('submit')); ?>

    <?= $this->form()->closeTag(); ?>
  </div>
</div>
~~~

En el código de arriba agregamos la clase CSS `.form-control` para cada campo
de entrada en el formulario para esto usamos el método `setAttribute()`
(ver líneas 5, 10 y 15). Además, con este método agregamos el atributo "placeholder"
que define el texto de sugerencia cuando el campo esta vacío. Para el campo
"body" agregamos el atributo "rows" que define la altura del campo (6 filas).

Para el botón *Submit* del formulario usamos las clases CSS `.btn` y `.btn-primary`
(ver línea 21).

Además, colocamos dentro de los elementos `<div>` las clases CSS `.form-group`
(líneas 35, 41 y 41).

Colocamos el formulario dentro de una celda de la grilla con 6 columnas de ancho
que hace al formulario ocupar la mitad de la pantalla (ver las líneas 31-32).

T> En ocasiones no es posible usar los estilos de Twitter Bootstrap con los
T> ayudantes de vista estándar para formularios. Por ejemplo, los ayudantes de
T> vista estándar q`FormCheckbox` y @`FormRadio` no se pueden ajustar para
T> soportar los estilos de Bootstrap. Por fortuna tenemos un modulo de terceros,
T> el [neilime/zf2-twb-bundle](https://github.com/neilime/zf2-twb-bundle),
T> que se puede instalar usando Composer (no debemos confundirlo con el nombre
T> del módulo de Laminas como tal). Este modulo provee ayudantes
T> de vista adecuados para imprimir los formulario de Laminas y aplicar los estilos
T> de Bootstrap a ellos. El módulo trabaja transparentemente de manera que una
T> vez que el módulo está instalado los ayudantes de vista estándar de Laminas
T> para formularios son reemplazados por los ayudantes de vista para formularios
T> que provee el módulo de esta manera no es necesario cambiar el código
T> nuestras plantillas de vista.

### Aplicar Estilos a la Lista de Errores de Validación

Por defecto los mensajes de error de nuestro formulario tienen el aspecto de una
típica lista no ordenada (`<ul>`). Para dar a la lista de errores una bonita
apariencia, agregamos un par de reglas CSS al archivo *style.css* que está en
la carpeta *APP_DIR/public*:

~~~css
form ul {
  list-style-type: none;
  padding: 0px;
  margin: 0px 5px;
}

form ul li {
  color: red;
}
~~~

Las reglas CSS de arriba removerán las viñetas de la lista de errores y colocarán
el mensaje de error producto de la validación en rojo.

### Agregar las Páginas "Gracias" y "Error al enviar el mensaje"

La última pequeña cosa que haremos es preparar las plantillas de vista para las
páginas "Gracias" y "Error al enviar el mensaje".

Agregamos la plantilla *thank-you.phtml* al directorio *application/index/*
que esta dentro de la carpeta *view/* del modulo. Colocamos el siguiente código
HTML dentro del archivo de plantilla de vista:

~~~html
<h1>Thank You!</h1>

<p>
  <div class="alert alert-success">
    We will respond to the E-mail address you have provided.
  </div>
</p>
~~~

Luego agregamos el archivo para la plantilla de vista *send-error.phtml*. El
código HTML para la página *Error al enviar el mensaje* se muestra abajo:

~~~html
<h1>Error Sending Email!</h1>

<p>
  <div class="alert alert-warning">
    Sorry, but we had an unexpected problem when trying to deliver
    your message. Please try again later.
  </div>
</p>
~~~

### Resultado

¡Felicidades! si abrimos la URL "http://localhost/contactus" en nuestro
navegador web deberíamos ver una página como la que mostramos en la figura
7.20.

{width=80%}
![Figura 7.20. Formulario de Contacto](../en/images/forms/contact_form.png)

Si ingresamos datos inválidos en el formulario y hacemos clic en el botón Enviar
deberíamos ver los errores de validación (figura 7.21).

![Figure 7.21. Errores de Validación del Formulario](../en/images/forms/validation_errors_page.png)

Si ingresamos un correo electrónico correcto, un asunto, un texto de mensaje
y enviamos el formulario el mensaje se enviará y se mostrará la página
*Gracias* (ver figura 7.22).

![Figure 7.22. Página Gracias](../en/images/forms/thank_you_page.png)

En caso de que el envío del mensaje falle se muestra la página *Error al enviar el mensaje*
(ver la figura 7.23).

![Figure 7.23. Página Error al Enviar el Mensaje](../en/images/forms/send_error_page.png)

T> Podemos ver el formulario *Contactanos* en acción en la aplicación de ejemplo
T> que se adjunta a este libro.

## Resumen

El formulario es la manera de recolectar los datos ingresados por el usuario en
una página web. Un formulario usualmente esta formado por elementos (pares
campos de entrada + etiquetas de campo). Opcionalmente los elementos pueden ser
agrupados en conjuntos de campos.

En un sitio web basado en MVC las funciones de un formulario están separadas
en modelos de formulario, responsables de la definición de los elementos y la
validación, y la presentación del formulario que se implementa con la ayuda
de ayudantes de vista.

Para crear un modelo de formulario escribimos una clase que deriva de la clase
base @`Form`[Laminas\Form\Form]. El modelo de formulario se inicializa añadiendo sus elementos con la
ayuda de los métodos provistos por la clase base.

Para enviar los datos del formulario al servidor el usuario hace clic en el
botón *Submit*, luego los datos se envían como parte de la petición HTTP.
Una vez que el usuario envía el formulario podemos extraer los datos del
formulario en el controlador y pedirle al modelo de formulario que valide
los datos.

Para revisar y filtrar los datos ingresados por el usuario se utilizan filtros
y validadores. Podemos usar la clase @`InputFilter`[Laminas\InputFilter\InputFilter] que es el contenedor para
las reglas de validación.

Si existen errores de entrada se muestra el formulario de nuevo pero pidiéndole
al usuario que corrija los errores de entrada. Si los datos son correctos
procesamos los datos con nuestra capa de lógica de negocio.
