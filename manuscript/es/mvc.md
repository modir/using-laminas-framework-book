# Modelo-Vista-Controlador {#mvc}

En este capítulo aprenderemos sobre modelos, vistas y controladores (el patrón
de diseño MVC). Una aplicación web usa el patrón MVC para separar la lógica de
negocio de la presentación. El objetivo de esto es permitir la reutilización del
código y la separación de conceptos.

Los componentes de Laminas sobre los que discutiremos en este capítulo son:

|--------------------------------|---------------------------------------------------------------|
| *Componente*                   | *Descripción*                                                 |
|--------------------------------|---------------------------------------------------------------|
| `Laminas\Mvc`                     | Soporte para el patrón MVC. Implementa las clases de tipo     |
|                                | controlador básicas, complementos para los controladores      |
|                                | (controller plugins), etc.                                    |
|--------------------------------|---------------------------------------------------------------|
| `Laminas\View`                    | Implementa la funcionalidad para los contenedores de          |
|                                | variables (variable containers), la impresión de una página   |
|                                | web y ayudantes de vista comunes (view helpers).              |
|--------------------------------|---------------------------------------------------------------|
| `Laminas\Http`                    | Implementa un envoltorio alrededor de las peticiones y        |
|                                | respuestas HTTP.                                              |
|--------------------------------|---------------------------------------------------------------|

## Obtener el Ejemplo Hello World del GitHub

En este y en los capítulos siguiente proveeremos código fuente de ejemplo que podremos
ejecutar nosotros mismos. Podría ser díficil para un principiante escribir código
sin errores. Si estamos atascados o no entendemos porque nuestro código no funciona
podemos descargar la aplicación *Hello World* completa desde GitHub. Las líneas de
código de ejemplo de este capítulo son en su mayor parte de esta aplicación.

Para descargar la aplicación *Hello World* vamos a [esta página](https://github.com/olegkrivtsov/using-laminas-book-samples)
y hacemos clic en el botón *Clone or Download* para descargar el código en un archivo
ZIP (ver figura 4.1). Cuando la descarga este completa descomprimimos el archivo
en alguna carpeta.

![Figura 4.1. El ejemplo Hello World se puede descargar desde GitHub](../en/images/preface/samples_on_github.png)

Luego navegamos hasta el directorio `helloworld` que contiene el código fuente
completo del ejemplo *Hello World*:

~~~text
/using-laminas-book-samples
   /helloworld
     ...
~~~

Hello World es un sitio web completo que se puede instalar en nuestra computadora.
Para instalar el ejemplo podemos editar el sitio virtual de Apache que está
por defecto o crear un nuevo archivo. Después de editar el archivo reiniciamos
el servidor Apache HTTP y abrimos el sitio web desde nuestro navegador web.

## Separar la Lógica del Negocio de la Presentación

Un sitio web típico tiene tres tipos de funcionalidad: el código que implementa
la lógica del negocio, el código que implementa la interación con el usuario y el
código que imprime páginas HTML (presentación). Antes de los frameworks de PHP los
programadores usualmente mezclaban estos tres tipos de código en un solo gran
archivo PHP que lo hace doloroso de probar y mantener, especialmente cuando escribimos
un sitio web grande.

Con el tiempo PHP se convirtió en un lenguaje orientado a objeto y ahora podemos
organizar el código dentro de clases. El patrón *Modelo-Vista-Conatrolador* (MVC)
es un conjunto consejos que te dicen como organizar el código de una mejor manera
para hacerlo fácil de mantener.

En MVC las clases que implementan la lógica del negocio se llaman *modelos*, los
retazos de código (code snippets) HTML se llaman *vistas* y las clases responsables
de interactuar con los usuarios se llaman *controladores*.

I> Las vistas se implementan con *retazos de código* y no como clases. La causa
I> de esto es que las vistas son generalmente muy simples y contienen solo una
I> mezcla de código HTML y código PHP en línea.

El principal objetivo del concepto MVC es separar la lógica de negocio (modelos)
de su visualización (vistas). A esto tambíen se llama *separación de conceptos*,
cuando cada capa hace solo su tarea específica.

Al separar nuestro modelo de las vistas reducimos el número de dependencias entre
ellos. En consecuencia un cambio hecho en una de las capas tiene un impacto posible
muy bajo sobre las otras. Esta separación además mejora la reusabilidad del código.
Por ejemplo, podemos crear varias representaciones visuales para los mismos modelos
(temas cambiables).

Para entender mejor como funciona esto recordemos que cualquier sitio web solo es
un programa PHP que recibe una petición HTTP desde el servidor web y produce una
repuesta HTTP. En la Figura 4.2 se muestra como una petición HTTP es procesada por
una aplicación MVC y como la respuesta se genera:

![Figura 4.2. Procesamiento de una petición HTTP en una aplicación web MVC](../en/images/mvc/model-view-controller.png)

* Primero un visitante del sitio web ingresa la URL en su navegador web, por ejemplo
  *http://localhost*, y el navegador web envía la petición al servidor web. En el
  caso de una URL como *http://wikipedia.org* la petición viaja a través de internet.

* El servidor web con el motor de PHP ejecuta el script de entrada *index.php*.
  La única cosa que hace el script de entrada es crear una instancia de la clase
  `Laminas\Mvc\Application`.

* La aplicación usa el componente *router* para asociar la URL y determinar a que
  controlador pasar la petición. Si coincide una ruta el controlador es instanciado
  y se llama al *método de acción* apropiado.

* En el método de acción del controlador los parámetro son recuperados de las variables
  GET y POST. Para procesar los datos entrantes el controlador instancia las clases
  de modelo apropiadas y llama a sus métodos.

* Las clases de modelo usa los algoritmos de la lógica de negocio para procesar los
  datos de entrada y regresar los datos de salida. Los algoritmos de la lógica de
  negocios son específicos de nuestra aplicación y típicamente incluye recuperar
  datos de la base de datos, manejar archivos, interactuar con sistemas externos,
  etc.

* Lo que resulta de llamar a los modelos se pasa al script de vista correspondiente
  para imprimir la página HTML.

* El script de vista usa los datos que provee el modelo para imprimir la página HTML.

* El controlador pasa la respuesta HTTP resultante a la aplicación.

* El servidor web regresa la pagina web HTML resultante al navegador web del
  usuario.

* El usuario ve la página en el navegador web.

Ahora tenemos una idea de como los modelos, las vistas y los controladores cooperan
para generar una salida HTML. En las siguientes secciones los describiremos con
más detalle.

## Los Controladores

Una controlador provee comunicación entre la aplicación, los modelos y las vistas.
El controlador trae las entradas desde la petición HTTP y usa los modelos y la
correspondiente vista para producir la respuesta HTTP necesaria.

Los controladores que pertenecen a un módulo residen típicamente en la subcarpeta
`Controller` del directorio fuente del módulo (se muestra en la figura 4.3).

![Figura 4.3. Directorio Controlador](../en/images/mvc/controller_dir.png)

Laminas Skeleton Application nos provee de una implementación por defecto de la clase
`IndexController`. La clase `IndexController` es típicamente la clase controladora
principal de un sitio web. El código se presenta abajo (algunas partes se omitieron
por simplicidad):

~~~php
<?php
namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        return new ViewModel();
    }
}
~~~

En el ejemplo de arriba podemos ver que los controladores usualmente definen su
propio namespace (línea 2). El controlador Index, como todos los otros controladores
del módulo *Application*, están en el namespace `Application\Controller`.

Un controlador es una clase PHP normal que se deriva de la clase base `AbstractAcionController`
(línea 7).

Por defecto la clase controlador contiene un solo *método de acción* llamado
`indexAction()` (ver líneas 9-12). Generalmente, crearemos otros métodos de acción
en las clases controladoras.

T> Laminas reconoce automáticamente el método de acción por el sufijo `Action`.
T> Si el nombre del método en el controlador no tiene el sufijo, este se considera
T> como un método usual y no una acción.

Como lo dice su nombre, un método de acción ejecuta una acción del sitio web que
generalmente resulta en la producción de una página web. El controlador Index
usualmente contiene los métodos de acción para las páginas principales del sitio
web. Por ejemplo, tendremos la acción "index" para la página *Home*, la acción
"about" para la página *Acerca de*, la acción "contactUs" para la página *Contactanos*
y posiblemente otras acciones.

{title="Table 4.1. Acciones típicas de un controlador Index"}
|------------------------------------|--------------------------------------------------|
| *Método de Acción*                 | *Descripción*                                    |
|------------------------------------|--------------------------------------------------|
| `IndexController::indexAction()`   | La acción "index" muestra la página *Home* de    |
|                                    | nuestro sitio web.                               |
|------------------------------------|--------------------------------------------------|
| `IndexController::aboutAction()`   | La acción "about" muestra la página *Acerca de*  |
|                                    | del sitio web. La página *Acerca de* contiene    |
|                                    | información de contacto y de derechos de autor.  |
|------------------------------------|--------------------------------------------------|
| `IndexController::contactUsAction()`| La acción "contactUs" action muestra la página  |
|                                     | *Contactanos* del sitio web. Esta página muestra|
|                                     | el formulario para contactar a los autores del  |
|                                     | sitio.                                          |
|------------------------------------|--------------------------------------------------|

### Clase Controladora Base

Cada controlador de nuestra página web hereda desde la clase base `AbstractActionController`.
En la figura 4.4 el diagrama de herencia de clase se presenta.

![Figura 4.4. Diagrama de herencia del controlador](../en/images/mvc/controller_inheritance.png)

La clase `AbstractActionController` nos provee de varios métodos útiles que se pueden
usar en las clases controladoras. La tabla 4.2 muestra un breve resumen de los métodos:

{title="Tabla 4.2. Métodos útiles de la clase AbstractActionController"}
|----------------------------------|--------------------------------------------------------|
| *Nombre del Método*              | *Descripción*                                          |
|----------------------------------|--------------------------------------------------------|
| `getRequest()`                   | Recupera el objeto `Laminas\Http\Request`, que es la      |
|                                  | representación de los datos de la petición HTTP.       |
|----------------------------------|--------------------------------------------------------|
| `getResponse()`                  | Recupera el objeto `Laminas\Http\PhpEnvironment\Response` |
|                                  | que permite insertar datos en la respuesta HTTP.       |
|----------------------------------|--------------------------------------------------------|
| `getEventManager()`              | Regresa el objeto `Laminas\EventManager\EventManager`     |
|                                  | que permite lanzar eventos y escuchar los eventos.     |
|----------------------------------|--------------------------------------------------------|
| `getEvent()`                     | Regresa el objeto `Laminas\Mvc\MvcEvent` que representa   |
|                                  | al evento al que el controlador responde.              |
|----------------------------------|--------------------------------------------------------|
| `getPluginManager()`             | Regresa el objeto `Laminas\Mvc\Controller\PluginManager`  |
|                                  | que se puede usar para registrar complementos          |
|                                  | controladores (controller plugins)                     |
|----------------------------------|--------------------------------------------------------|
| `plugin($name, $options)`        | Este método permite acceder a determinado complemento  |
|                                  | a partir de un nombre dado.                            |
|----------------------------------|--------------------------------------------------------|
| `__call($method, $params)`       | Permite llamar a un complemento indirectamente usando  |
|                                  | el método mágico de PHP `__call`.                      |
|----------------------------------|--------------------------------------------------------|

Como podemos ver en la tabla de arriba la clase controladora base nos permite
acceder a los datos de la petición y respuesta HTTP, además, provee de acceso al
administrador de eventos (event manager). Además, nos da la capacidad de registrar
y llamar a los complementos del controlador (aprenderemos sobre los complementos
del controlador luego en este capítulo).

## Recuperar los Datos desde la Petición HTTP

En algún método de acción del controlador podríamos necesitar recuperar los datos
de la petición HTTP (datos como las variables GET o POST, cookies, las cabeceras
HTTP, etc). Para este proposíto, Laminas Framework nos provee con la clase
`Laminas\Http\Request` que es parte del componente `Laminas\Http`.

Para tener el objeto de la petición HTTP dentro de nuestro método de acción podemos
usar el siguiente código:

~~~php
// Get HTTP request object
$request = $this->getRequest();
~~~

El código de arriba regresa la instancia de la clase `Laminas\Http\Request` que contiene
todos los datos de la petición HTTP. En la tabla 4.3 podemos encontrar los métodos
más ampliamente usados de la clase `Request` junto con una breve descripción.

{title="Tabla 4.3. Métodos de la clase `Laminas\Http\Request`."}
|----------------------------------------|------------------------------------------------------|
| *Nombre del Método*                    | *Descripción*                                        |
|----------------------------------------|------------------------------------------------------|
| `isGet()`                              | Revisa si es una petición GET.                       |
|----------------------------------------|------------------------------------------------------|
| `isPost()`                             | Revisa si es una petición POST.                      |
|----------------------------------------|------------------------------------------------------|
| `isXmlHttpRequest()`                   | Revisa si es una petición AJAX.                      |
|----------------------------------------|------------------------------------------------------|
| `isFlashRequest()`                     | Revisa si la petición es una petición Flash.         |
|----------------------------------------|------------------------------------------------------|
| `getMethod()`                          | Regresa el método de la petición.                    |
|----------------------------------------|------------------------------------------------------|
| `getUriString()`                       | Regresa la URI desde el objeto de la petición como   |
|                                        | una cadena de caracteres.                            |
|----------------------------------------|------------------------------------------------------|
| `getQuery($name, $default)`            | Regresa el parámetro de consulta a partir de un      |
|                                        | nombre o todos los parámetros. Si el parámetro no se |
|                                        | encuentra regresa el valor `$defaut`.                |
|----------------------------------------|------------------------------------------------------|
| `getPost($name, $default)`             | Regresa el contenedor de parámetros responsable de   |
|                                        | los parámetros POST o un solo parámetro de la        |
|                                        | petición POST.                                       |
|----------------------------------------|------------------------------------------------------|
| `getCookie()`                          | Regresa el encabezado Cookie.                        |
|----------------------------------------|------------------------------------------------------|
| `getFiles($name, $default)`            | Regresa el contenedor de parámetros responsable de   |
|                                        | los parámetros de los archivos o los de un solo archivo. |
|----------------------------------------|------------------------------------------------------|
| `getHeaders($name, $default)`          | Regresa el contenedor del encabezado responsable de  |
|                                        | los encabezados o todos los encabezados de un        |
|                                        | determinado nombre o tipo.                           |
|----------------------------------------|------------------------------------------------------|
| `getHeader($name, $default)`           | Regresa un encabezado por un `$name`. Si el encabezado |
|                                        | no se encuentra regresa el valor `$default`.         |
|----------------------------------------|------------------------------------------------------|
| `renderRequestLine()`                  | Regresa la línea de la petición formateada (primera línea) |
|                                        | para la petición HTTP actual.                        |
|----------------------------------------|------------------------------------------------------|
| `fromString($string)`                  | Un método estático que produce un objeto Request a   |
|                                        | partir de una cadena de caracteres que contiene una  |
|                                        | petición HTTP bien formada.                          |
|----------------------------------------|------------------------------------------------------|
| `toString()`                           | Regresa la petición HTTP cruda como un string.       |
|----------------------------------------|------------------------------------------------------|

## Recuperar las Variables GET y POST

Para traer una variable GET o POST de la petición HTTP usamos el siguiente código:

~~~php
// Get a variable from GET
$getVar = $this->params()->fromQuery('var_name', 'default_val');

// Get a variable from POST
$postVar = $this->params()->fromPost('var_name', 'default_val');
~~~

En el ejemplo de arriba usamos el complemente para el controlador `Params` que
nos provee de los métodos convenientes para acceder a las variables GET y POST,
archivos subidos, etc.

En la línea 2 usamos el método `fromQuery()` para recuperar la variable que tiene
el nombre `var_name` desde el método GET. Si la variable no existe el valor por
defecto `default_val` se regresa. El valor por defecto es muy conveniente porque
no tenemos que usar la función `isset()` de PHP para contrastar si la variable
existe.

En la línea 5 usamos el método `fromPost()` para recuperar una variable del método POST.
El funcionamiento de este método es el mismo que él del método `fromQuery()`.

T> En Laminas no debemos acceder a los parámetros de la petición por medio de los tradicionales
T> arreglos globales `$_GET` y `$_POST`. En su lugar usamos la API que provee Laminas para
T> recuperar los datos de la petición.

## Colocar Datos en la Respuesta HTTP

Aunque raramente interactuamos con los datos de la respuesta HTTP directamente podemos
hacerlo con la ayuda del método `getResponse()` que provee la clase base `AbstractActionController`.
El método `getResponse()` regresa la instancia de la clase `Laminas\Http\PhpEnvironment\Response`.
La tabla 4.4 contiene los métodos más importantes de esta clase:

{title="Tabla 4.4. Métodos de la clase Laminas\Http\PhpEnvironment\Response."}
|----------------------------------------|--------------------------------------------------------|
| *Nombre del Método*                    | *Descripción*                                          |
|----------------------------------------|--------------------------------------------------------|
| `fromString($string)`                  | Poblar el objeto de respuesta a partir de una          |
|                                        | cadena de caracteres.                                  |
|----------------------------------------|--------------------------------------------------------|
| `toString()`                           | Imprime toda la respuesta como una respuesta HTTP      |
|                                        | en forma de cadena de caracteres.                      |
|----------------------------------------|--------------------------------------------------------|
| `setStatusCode($code)`                 | Coloca el código de estado HTTP y opcionalmente un     |
|                                        | mensaje.                                               |
|----------------------------------------|--------------------------------------------------------|
| `getStatusCode()`                      | Regresa el código de estado HTTP.                      |
|----------------------------------------|--------------------------------------------------------|
| `setReasonPhrase($reasonPhrase)`       | Coloca el mensaje de estado HTTP.                      |
|----------------------------------------|--------------------------------------------------------|
| `getReasonPhrase()`                    | Regresa el mensaje de estado HTTP.                     |
|----------------------------------------|--------------------------------------------------------|
| `isForbidden()`                        | Revisa si el código de respuesta es: 403 Forbidden.    |
|----------------------------------------|--------------------------------------------------------|
| `isNotFound()`                         | Revisa si el código de estado indica que el recurso    |
|                                        | no se encontró (código de estado 404).                 |
|----------------------------------------|--------------------------------------------------------|
| `isOk()`                               | Revisa si la respuesta es exitosa.                     |
|----------------------------------------|--------------------------------------------------------|
| `isServerError()`                      | Revisa si el código de estado de la respuesta es 5xx.  |
|----------------------------------------|--------------------------------------------------------|
| `isRedirect()`                         | Revisa si la respuesta es: 303 Redirect.               |
|----------------------------------------|--------------------------------------------------------|
| `isSuccess()`                          | Revisa si la respuesta es: 200 Successful.             |
|----------------------------------------|--------------------------------------------------------|
| `setHeaders(Headers $headers)`         | Permite colocar los encabezados de la respuesta.       |
|----------------------------------------|--------------------------------------------------------|
| `getHeaders()`                         | Regresa una lista con los encabezados de la respuesta. |
|----------------------------------------|--------------------------------------------------------|
| `getCookie()`                          | Regresa la cabecera Cookie.                            |
|----------------------------------------|--------------------------------------------------------|
| `setContent($value)`                   | Coloca el contenido de la respuesta en crudo.          |
|----------------------------------------|--------------------------------------------------------|
| `getContent()`                         | Regresa el contenido de la respuesta en crudo.         |
|----------------------------------------|--------------------------------------------------------|
| `getBody()`                            | Trae y codifica el contenido de la respuesta.          |
|----------------------------------------|--------------------------------------------------------|

Por ejemplo, usamos el siguiente código para colocar el código de estado 404 en la respuesta:

~~~php
$this->getResponse()->setStatusCode(404);
~~~

Usamos el siguiente código para agregar un encabezado a la respuesta:

~~~php
$headers = $this->getResponse()->getHeaders();
$headers->addHeaderLine(
             "Content-type: application/octet-stream");
~~~

Usamos el siguiente código para agregar contenido a la respuesta:

~~~php
$this->getResponse()->setContent('Some content');
~~~

## Contenedor de Variables

Después de recuperar los datos de la petición HTTP podríamos hacer algo con los datos
(generalmente procesaremos los datos en nuestra capa de modelo) y regresaremos los datos
desde el método de acción.

Podemos ver que el método `indexAction()` del controlador Index regresa una instancia de la
clase `ViewModel`. La clase `ViewModel` es un tipo de *contenedor de variables*. Todas las
variables pasadas a su constructor serán automáticamente accesibles al script de la
vista.

Vamos a ver un ejemplo de la vida real. Creamos otro método de acción en la clase
`IndexController` al que llamamos `aboutAction()`. La acción "about" mostrará la página
*Acerca de* de nuestro sitio web. En el método de acción crearemos dos variables que
contienen información sobre nuestro sitio web y regresaremos las variables para imprimirlos
en una vista con la ayuda del objeto `ViewModel`:

~~~php
// The "about" action
public function aboutAction()
{
    $appName = 'HelloWorld';
    $appDescription = 'A sample application for the Using Laminas Framework book';

    // Return variables to view script with the help of
    // ViewModel variable container
    return new ViewModel([
        'appName' => $appName,
        'appDescription' => $appDescription
    ]);
}
~~~

En las líneas 4-5 creamos las variables `$appName` y `$appDescription`. Ellas guardan
respectivamente el nombre y la descripción de nuestra aplicación.

En las líneas 9-12 pasamos las variables que hemos creado al constructor del objeto
`ViewModel` como una arreglo asociado. Las llaves del arreglo definen los nombres de las
variables que, al ser retornadas, serán accesibles en el script de la vista.

La clase `ViewModel` provee varios métodos que podemos usar para colocar variables en el
`ViewModel` y recuperar variables de él. La tabla 4.5 provee un resumen de los métodos:


{title="Tabla 4.5. Métodos de la clase ViewModel"}
|--------------------------------|---------------------------------------------------------------|
| *Nombre del Método*            | *Descripción*                                                 |
|--------------------------------|---------------------------------------------------------------|
| `getVariable($name, $default)` | Regresa una variable a partir de su nombre (o el valor por    |
|                                | defecto si la variable no existe).                            |
|--------------------------------|---------------------------------------------------------------|
| `setVariable($name, $value)`   | Coloca una variable.                                          |
|--------------------------------|---------------------------------------------------------------|
| `setVariables($variables, $overwrite)`| Coloca un grupo de variables, opcionalmente sobrescribe|
|                                       | las que existen.                                       |
|--------------------------------|---------------------------------------------------------------|
| `getVariables()`               | Regresa todas las variables como un arreglo.                  |
|--------------------------------|---------------------------------------------------------------|
| `clearVariables()`             | Remueve todas las variables.                                  |
|--------------------------------|---------------------------------------------------------------|

## Expresar Condiciones de Error

A veces las cosas van mal y ocurren algunos errores. Por ejemplo, esperamos recibir una
variable GET de la petición HTTP pero está ausente o tiene un valor invalido. Para expresar
esta condición de error generalmente colocamos un [código de estado](https://en.wikipedia.org/wiki/List_of_HTTP_status_codes)
*4xx* en la respuesta HTTP y la regresamos desde la acción del controlador.

Por ejemplo, en la aplicación Blog, asumiendo que un usuario ingresa la siguiente URL en
la barra de navegación del navegador web: *http://localhost/posts/view?id=10000*. La intención
de esta petición es mostrar la publicación del blog que tiene el ID=10000. Si la publicación
con este ID no existe no podemos mostrarla y usamos el siguiente código PHP para colocar un
código de estado 404 (Page Not Found) en la respuesta:

~~~php
// The "view" action displays a blog post with the given ID
public function viewAction()
{
    // Get ID argument from GET
    $id = (int)$this->params()->fromQuery('id', -1);

    // Validate the argument
    if ($id<1) {
        // Error condition - we can not display such post
        $this->getResponse()->setStatusCode(404);
        return;
    }

    // Try to find the post (we omit the actual SQL query for simplicity).
    $post = ...
    if (!$post) {
        // Error condition - post not found
        $this->getResponse()->setStatusCode(404);
        return;
    }

    // Normal execution
    // ...
}
~~~

Cuando Laminas encuentra el código de estado *4xx* en la respuesta él redirecciona al usuario
a la página especial *error page*. Hablaremos sobre las páginas de error más tarde en este
capítulo.

Otra manera de expresar una condición (critica) de error es lanzando una `Exception`, por
ejemplo:

~~~php
throw new \Exception("Post with ID=$id could not be found");
~~~

Cuando Laminas encuentra una excepción que no puede manejar muestra otra página de error con
la información sobre la exception.

## Registro de los Controladores

Todas las clases controladoras que pertenecen a un módulo deben ser registradas en el archivo
de configuración *module.config.php*. Si nuestra clase controladora no necesita usar servicios
(no tiene dependencias) podemos registrarla de la siguiente manera:

~~~php
<?php
use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    // ...

    'controllers' => [
        'factories' => [
            Controller\IndexController::class => InvokableFactory::class
            // Put other controllers registration here
        ],
    ],

    // ...
];
~~~

En la línea 7 tenemos la llave *controllers* que contiene la subllave *factories*. Para
registrar una clase controladora agregamos una línea que tenga la forma de un par: *llave => valor*.
La llave será el nombre completo (fully qualified name) de la clase controladora, como
`\Application\Controller\IndexController` (usamos la palabra clave de PHP `::class` para
la resolución de nombre de clase), y el valor será el nombre de una clase fábrica que creará
la clase controladora que necesitamos usar. En nuestro caso usamos la `InvokableFactory`
estándar pero podemos crear una propia si es necesario.

I> Al usar la `InvokableFactory` decimos a Laminas Framework que puede invocar el controlador
I> instanciandolo con el operador `new`. Esta es la manera más simple de instaciar el
I> controlador. Como una alternativa, podemos registrar nuestra propia fábrica para crear
I> la instancia del controlador e inyectar las dependencias dentro del él.

### Registrar una Fábrica para el Controlador

Si nuestra clase controladora necesita llamar a algún servicio (lo que sucede muy a menudo),
necesitamos pedirlo al *administrador de servicios* (discutimos sobre el administrador
de servicios en el capítulo [Website Operation](#operation)) y luego pasarlo al
constructor del controlador para que el controlador guarde el servicio que pasamos en una propiedad
privada para su uso interno (a esto se llama inyección de dependencia).

Este procedimiento se implementa típicamente dentro de la clase fábrica. Por ejemplo, asumiendo
que nuestra clase controladora necesita usar el servicio `CurrencyConverter` que convierte
dinero de USD a EUR. La clase fábrica de nuestro controlador tendrá el siguiente aspecto:

~~~php
<?php
namespace Application\Controller\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Application\Service\CurrencyConverter;
use Application\Controller\IndexController;

// Factory class
class IndexControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container,
                     $requestedName, array $options = null)
    {
        // Get the instance of CurrencyConverter service from the service manager.
        $currencyConverter = $container->get(CurrencyConverter::class);

        // Create an instance of the controller and pass the dependency
        // to controller's constructor.
        return new IndexController($currencyConverter);
    }
}
~~~

Luego registramos el controlador de la misma manera que antes pero especificando
la clase fábrica que hemos escrito antes:

~~~php
<?php
return [
    // ...

    'controllers' => [
        'factories' => [
            Controller\IndexController::class => Controller\Factory\IndexControllerFactory::class
        ],
    ],

    // ...
];
~~~

I> Si tenemos alguna experiencia con Laminas Framework 2 podemos notar que las cosas ahora
I> son un poco diferentes que antes. En ZF2 había un método `getServiceLocator()` en la
I> clase base `AbstractActionController` que permitía traer las dependencias al
I> controlador incluso sin la clase fábrica.
I> En Laminas tenemos que pasar las dependencias explícitamente. Esto es un poco más aburrido
I> pero elimina las dependencias "ocultas" y hace a nuestro código más claro y fácil de
I> entender.

### LazyControllerAbstractFactory

Escribir una fábrica para cada controlador puede parecer aburrido en un primer
momento. Si somos muy flojos para querer hacerlo podemos usar la clase fábrica
por defecto `LazyControllerAbstractFactory`.

T> La fábrica `LazyControllerAbstractFactory` usa *reflexión* para determinar que servicios
T> necesita usar nuestro controlador. Solo necesitamos *obligar las dependencias* (typehint)
T> colocando los servicios como argumentos del constructor del controlador y la fábrica
T> recuperará por si misma los servicios necesarios y los pasará al constructor.

Por ejemplo, para inyectar el servicio `CurrencyConverter` en nuestro controlador nos aseguramos
de que el constructor se vea de esta manera:

~~~php
namespace Application\Controller;

use Application\Service\CurrencyConverter;

class IndexController extends AbstractActionController
{
    // Here we will save the service for internal use.
    private $currencyConverter;

    // Typehint the arguments of constructor to get the dependencies.
    public function __construct(CurrencyConverter $currencyConverter)
    {
        $this->currencyConverter = $currencyConverter;
    }
}
~~~

Luego registramos el controlador de la misma manera pero especificando la fábrica
`LazyControllerAbstractFactory`:

~~~php
<?php
use Laminas\Mvc\Controller\LazyControllerAbstractFactory;

return [
    // ...

    'controllers' => [
        'factories' => [
            Controller\IndexController::class => LazyControllerAbstractFactory::class
        ],
    ],

    // ...
];
~~~

## ¿Cuando Crear un Nuevo Controlador?

Cuando el tamaño de nuestro sitio web crece debemos crear nuevas clases controladoras
en lugar de colocar todas las acciones en `IndexController`. El controlador Index se usa
para definir las acciones que trabajan para todo nuestro sitio web.

T> No es recomendable crear grandes controladores con cientos de acciones, porque ellos
T> son difíciles de entender y de mantener.

Es recomendable crear una nueva clase controladora para cada modelo (o para los más importantes)
de nuestro dominio lógico de negocio.

Por ejemplo, podemos crear el `UserController` para administrar los usuarios de nuestro
sitio web. Este controlador puede tener la acción por defecto "index" para mostrar la
página con todos los usuarios, la acción "add" para agregar un nuevo usuario, la acción
"edit" para editar el perfil de un usuario y la acción "delete" para borrar un usuario.

Por analogía crearemos `PurchaseController` y sus acciones para administrar las compras
de productos e implementar una carro de compras, crearemos `DownloadController` y sus
acciones para administrar la descarga de archivos de nuestro sitio web, etc

## Complementos para el Controlador

Un *complemento para controladores* (controller plugin) es una clase que extiende
la funcionalidad de *todos los controladores*.

I> Sin complementos para extender la funcionalidad a todos los controladores tendríamos
I> que crear una clase base a la medida, por decir `BaseController`, y derivar todos los
I> controladores de esta clase base. Esta manera puede ser usada, pero en opinión
I> de los creadores de Laminas los complements son una mejor solución porque ellos
I> usan *composición de clases* [^foo] que provee mayor flexibilidad en comparación
I> con la herencia de clases. Registramos el controlador de tipo complemento y este
I> automáticamente es accesible desde todos los controladores de nuestra aplicación
I> (la clase base `AbstractActionController` usa el método mágico `__call()`
I> como intermediario para llamar a los complementos para los controladores registrados).

[^foo]: La *Composición* es una relación entre dos clases que se describe mejor
        como una relación "tiene-una" (has-a) o "todo/parte" (whole/part). La clase dueña
        contienen una referencia a otra clase (complemento). El dueño es responsable
        de la vida del objeto que él usa.

Hay varios complementos estándares para clases disponibles luego de la instalación
(tabla 4.6) y ya hemos usado uno de ellos (el complemento `Param`) en uno de los
ejemplos anteriores.

{title="Tabla 4.6. Complementos Estándares para Controladores"}
|------------------------------------------|------------------------------------------------------|
| *Clase Complementaria Estándar*          | *Descripción*                                        |
|------------------------------------------|------------------------------------------------------|
| `Params`                                 | Permite recuperar variables desde la petición HTTP   |
|                                          | incluyendo las variables GET y POST.                 |
|------------------------------------------|------------------------------------------------------|
| `Url`                                    | Permite generar URLs absolutas o relativas dentro de |
|                                          | los controladores.                                   |
|------------------------------------------|------------------------------------------------------|
| `Layout`                                 | Da acceso al modelo de la vista para pasar datos a   |
|                                          | la plantilla de diseño.                              |
|------------------------------------------|------------------------------------------------------|
| `Identity`                               | Regresa la identidad del usuario quien ha iniciado   |
|                                          | sesión en el sitio web.                              |
|------------------------------------------|------------------------------------------------------|
| `FlashMessenger`                         | Permite definir mensajes "flash" que son almacenados |
|                                          | en la sesión y se puede mostrar en una página web    |
|                                          | diferente.                                           |
|------------------------------------------|------------------------------------------------------|
| `Redirect`                               | Permite redireccionar la petición a otro método de   |
|                                          | acción del controlador.                              |
|------------------------------------------|------------------------------------------------------|
| `PostRedirectGet`                        | Redireciona la petición POST convirtiendo todas las  |
|                                          | variables POST en variables GET.                     |
|------------------------------------------|------------------------------------------------------|
| `FilePostRedirectGet`                    | Redirecciona la petición POST conservando los        |
|                                          | archivos cargados.                                   |
|------------------------------------------|------------------------------------------------------|

Dentro de un método de acción en un controlador podemos acceder a un complemento
de la siguiente manera:

~~~php
// Access Url plugin
$urlPluguin = $this->url();

// Access Layout plugin
$layoutPlugin = $this->layout();

// Access Redirect plugin
$redirectPlugin = $this->redirect();
~~~

Como alternativa podemos invocar un complemento por medio de su nombre completo
con el método `plugin()` que provee la clase controladora base, de la siguiente manera:

~~~php
use Laminas\Mvc\Controller\Plugin\Url;

// Inside your controller's action use the plugin() method.
$urlPlugin = $this->plugin(Url::class);
~~~

### Escribir Nuestro Propio Complemento para Controladores

En nuestro sitio web probablemente necesitaremos crear un complemento a la medida
para controladores. Por ejemplo, asumiendo que necesitamos que todos las clases
controladoras sean capaces de revisar si a un usuario se le permite acceder a una
determinada acción del controlador. Esto se puede implementar con la clase
`AccessPlugin`.

El complemento para controladores se debería derivar de la clase `AbstractPlugin`.
Los complementos típicamente se encuentran en su propio namespace: `Plugin`, que
está anidado en el namespace `Controller`:

~~~php
<?php
namespace Application\Controller\Plugin;

use Laminas\Mvc\Controller\Plugin\AbstractPlugin;

// Plugin class
class AccessPlugin extends AbstractPlugin
{
    // This method checks whether user is allowed
    // to visit the page
    public function checkAccess($actionName)
    {
        // ...
    }
}
~~~

Para informar a Laminas Framework sobre el nuevo complemento necesitamos registrarlo
en el archivo *module.config.php* con la llave `controller_plugins`. Veamos el
ejemplo más abajo:

~~~php
<?php
return [
    // ...

    'controller_plugins' => [
        'factories' => [
            Controller\Plugin\AccessPlugin::class => InvokableFactory::class,
        ],
        'aliases' => [
            'access' => Controller\Plugin\AccessPlugin::class,
        ]
    ],

    // ...
];
~~~

I> Notemos que además registramos un alias para el complemento con lo que somos
I> capaces de traer el complemento por medio de su nombre corto.

Después de esto seremos capaces de acceder a nuestro complemento desarrollado a
la medida desde todas las acciones del controlador de la siguiente manera:

~~~php
// Check if site user is allowed to visit the "index" page
$isAllowed = $this->access()->checkAccess('index');
~~~

## Vistas

Las vistas pertenecen a la capa de presentación de la aplicación web porque su
objetivo es producir una salida HTML que regresa el servidor web al visitante del
sitio.

En Laminas Framework una vista se implementa como un *archivo de plantilla*
que es una archivo que tiene la extensión `.phtml` ("phtml" representa PHP+HTML).
Las plantillas de vista tienen ese nombre porque ellas usualmente contienen código
HTML mezclado con retazos (snippets) de código PHP que imprimen la página web.

Generalmente las vistas están dentro de la subcarpeta *view* del módulo (ver
figura 4.5):

![Figura 4.5. Carpeta View](../en/images/mvc/views_dir.png)

Q> **¿Por qué los archivos de plantilla de vistas no se almacenan dentro de la
Q> carpeta fuente del módulo?**
Q>
Q> Las plantillas de vista (archivos `.phtml`) no se almacenan dentro de la carpeta
Q> `src/` del módulo porque ellas no son clases de PHP normales y no es necesario
Q> que la funcionalidad de autoloading de clases de PHP las busque.
Q> Las plantillas de vista son buscadas por la clase especial de Laminas llamada
Q> *view resolver* y por esta razón las plantillas de vista se almacenan dentro
Q> del directorio `view` del módulo.

Las plantillas de vista pueden tener comportamientos diferentes dependiendo de las
variables que les pasamos desde los métodos de acción del controlador. Los datos
se pasan a las plantillas de vista con la ayuda del contenedor de variables `ViewModel`.

Por ejemplo, vamos a implementar la plantilla de vista para el método `aboutAction()`
de nuestro controlador Index. La página *Acerca de* mostrará el título y alguna
información sobre nuestra aplicación Hola Mundo.

Para crear el archivo de la plantilla de vista desde nuestro editor de texto favorito
(NetBeans, Atom, etc.) navegamos hasta el directorio *view/application/index*
(ver figura 4.6) y hacemos clic derecho sobre la carpeta "index". En el menú contextual
que se despliega seleccionamos "New->PHP File...".

![Figura 4.6. Menú Contextual](../en/images/mvc/new_php_file.png)

En la ventana de dialogo "New PHP File" que aparece (figura 4.7) ingresamos el siguiente
nombre para el archivo: *about.phtml*, y hacemos clic en el botón *Finish*.

![Figura 4.7. Menú Contextual](../en/images/mvc/new_php_file_dialog.png)

El archivo de plantilla de vista *about.phtml* será creado y mostrado en la ventana
derecha de NetBeans. En este archivo agregamos el siguiente código:

~~~php
<h1>About</h1>

<p>
    The Hello World application.
</p>

<p>
    Application name: <?= $this->escapeHtml($appName); ?>
</p>

<p>
    Application description: <?= $this->escapeHtml($appDescription); ?>.
</p>
~~~

Como podemos ver la plantilla de vista es una página HTML típica con varios fragmentos
de código PHP. Un script de vista solo imprime los datos que le pasamos en el
contenedor de variables `ViewModel`. Por ejemplo, en a línea 8 traemos el valor
de la variable `$appName` y la imprimimos en el flujo de salida estándar.

T> Dentro de la plantilla de vista podemos fácilmente acceder a las variables que
T> fueron pasadas desde la acción del controlador. Por ejemplo, para traer el valor
T> de la variable: nombre de la aplicación, usamos la sintaxis `$appName` o
T> `$this->appName`. Estas dos manera de acceder a la variable son equivalentes,
T> pero la primera forma requiere escribir menos así que usaremos esta de ahora
T> en adelante.

Notemos que estamos usando el ayudante de vista (view helper) `EscapeHtml` para
*escapar* la cadena de caracteres impresa en la página web y hacer a nuestro
sitio web resistente a los ataques de crackers.

W> Nosotros deberíamos siempre escapar las variables que imprimimos en nuestras
W> páginas webs. Escapar permite asegurar que código malicioso no es inyectado
W> en nuestra página.

I> Además, en nuestro script de vista podemos usar estructuras de control simples
I> (como `if`, `foreach` o `switch`) para cambiar la apariencia de la página
I> dependiendo del valor de las variables.

Ahora vamos a ver como se ve la página en el navegador web. Escribimos la URL
*http://localhost/application/about* en nuestra barra de navegación del navegador
web. La página *Acerca de* debería aparecer (ver figura 4.8):

![Figura 4.8. Página Acerca de](../en/images/mvc/about_page.png)

T> En general el código PHP que usamos dentro de las vistas debe ser tan simple
T> como sea posible. Las vistas generalmente no modifican los datos que pasamos
T> desde el controlador. Por ejemplo, una vista puede usar el modelo que le pasamos
T> para recorrer las filas de una tabla de base de datos e imprimir los elementos
T> en una página HTML, pero la vista nunca debería crear tablas de base de datos
T> o modificarlas.

## Ayudantes de Vista

Generalmente los ayudantes de vista (view helper) son código PHP (relativamente)
simple cuyo objetivo es imprimir alguna parte de la vista. Podemos invocar a los
ayudantes de vista desde cualquier plantilla. Con los ayudantes de vista podemos
crear widgets reusables (como menús, barras de navegación, etc.) para nuestra
página web.

I> Los ayudantes de vista son análogos a los complementos para controladores: los
I> complementos para controladores permiten "extender" la funcionalidad de los
I> controladores y los ayudantes de vista permiten "extender" la funcionalidad
I> de las plantillas de vista.

Laminas provee muchos ayudantes de vista estándares listos para usar. En la tabla 4.7
se presentan algunos de ellos junto con una breve descripción:

{title="Tabla 4.7. Ayudantes de Vista Estándar"}
|------------------------------------------|------------------------------------------------------|
| *Clase del Complemento Estándar*         | *Descripción*                                        |
|------------------------------------------|------------------------------------------------------|
| `BasePath`                               | Permite recuperar la ruta base de la aplicación web  |
|                                          | que es la ruta absoluta para `APP_DIR`.              |
|------------------------------------------|------------------------------------------------------|
| `Url`                                    | Permite generar direcciones URLs relativas o absoluta|
|                                          | desde dentro de la plantilla de vista.               |
|------------------------------------------|------------------------------------------------------|
| `ServerUrl`                              | Recuperar la URL de la petición actual.              |
|------------------------------------------|------------------------------------------------------|
| `Doctype`                                | Ayudante para colocar y recuperar el elemento        |
|                                          | HMTL doctype de la página web.                       |
|------------------------------------------|------------------------------------------------------|
| `HeadTitle`                              | Ayudante para colocar el elemento HTML title de la   |
|                                          | página web.                                          |
|------------------------------------------|------------------------------------------------------|
| `HtmlList`                               | Ayudante para generar listas HTML ordenadas o desordenada. |
|------------------------------------------|------------------------------------------------------|
| `ViewModel`                              | Ayudante para guardar y recuperar el modelo de vista.|
|------------------------------------------|------------------------------------------------------|
| `Layout`                                 | Recupera la vista de plantilla de diseño.            |
|------------------------------------------|------------------------------------------------------|
| `Partial`                                | Permite imprimir una plantilla de vista "parcial".   |
|------------------------------------------|------------------------------------------------------|
| `InlineScript`                           | Ayudante para colocar y recuperar elementos de tipo  |
|                                          | script que se incluyen en la sección HTML body.      |
|------------------------------------------|------------------------------------------------------|
| `Identity`                               | Ayudante de vista para recuperar la identidad        |
|                                          | del usuario autenticado.                             |
|------------------------------------------|------------------------------------------------------|
| `FlashMessenger`                         | Permite recuperar el mensaje "flash" guardado en la sesión. |
|------------------------------------------|------------------------------------------------------|
| `EscapeHtml`                             | Permite escapar una variable impresa en la página web. |
|------------------------------------------|------------------------------------------------------|

Para mostrar el uso de los ayudantes de vista mostramos abajo como colocar el título
de una página web. Generalmente es necesario dar un título diferente para cada
página web. Podemos colocar el título para la página *Acerca de* agregando el
siguiente código PHP al comienzo de la plantilla de vista *about.phtml*:

~~~php
<?php
$this->headTitle('About');
?>
~~~

En el código de arriba llamamos al ayudante de vista `HeadTitle` y le pasamos
el título de la página ("Acerca de") como argumento. El ayudante de vista `HeadTitle`
coloca internamente el texto para el elemento HTML `<title>` de la página web.
Luego, si abrimos la página *Acerca de* en nuestro navegador web el título de la
página que aparecerá es "Acerca de - ZF Skeleton Application" (ver la figura 4.9
abajo a manera de ejemplo):

![Figura 4.9. Colocar el título de la página Acerca de](../en/images/mvc/about_title.png)

I> Discutiremos sobre los ayudantes de vista con más detalle y daremos más
I> ejemplos de uso en el capítulo [Apariencia de la Página y Diseño](#appearance).

## Los Nombres de las Plantillas de vista

Cuando regresamos datos con el contenedor de variables `ViewModel` desde nuestro
método de acción del controlador Laminas Framework de alguna manera conoce el nombre
que corresponde al archivo de plantilla de vista. Por ejemplo, para nuestro
método `aboutAction()` del `IndexController` Laminas usa automáticamente la plantilla
de vista *about.phtml*.

I> Laminas determina el nombre correcto de la plantilla de vista por medio del nombre
I> del módulo, el nombre del controlador y el nombre de la acción. Por ejemplo,
I> la acción `IndexController::aboutAction()` que pertenece al módulo `Application`
I> tendrá la plantilla de vista por defecto `application/index/about.phtml`.

T> Si el nombre de nuestro controlador o acción tiene varias palabras en camel-case
T> (como `UserRegistrationController` y `registrationStep1Action`), la plantilla
T> de vista correspondiente será *application/user-registration/registration-step-1.phtml*
T> (los nombres en mayúsculas se convierten en minúsculas y las palabras se separan
T> por medio de guiones).

### Sobrescribir el Nombre por Defecto de la Plantilla de Vista

Además, el `ViewModel` se puede usar para sobrescribir la plantilla de vista que
se toma por defecto. En realidad la clase `ViewModel` es más que un contenedor
de variables. Adicionalmente permite especificar cuales plantillas de vista se
deben usar para imprimir la página. El resumen de los métodos que se proveen para
este propósito se muestra en la tabla 4.8.

{title="Tabla 4.8. Métodos de la clase ViewModel para colocar y recuperar el nombre de la plantilla de vista"}
|--------------------------------|---------------------------------------------------------------|
| *Nombre del Método*            | *Descripción*                                                 |
|--------------------------------|---------------------------------------------------------------|
| `setTemplate()`                | Colocar el nombre de la plantilla de vista.                   |
|--------------------------------|---------------------------------------------------------------|
| `getTemplate()`                | Regresa el nombre de la plantilla de vista.                   |
|--------------------------------|---------------------------------------------------------------|

Para colocar el nombre de la plantilla usamos el método `setTemplate()`. El método
`getTemplate()` regresa el nombre de la plantilla de vista que está colocado
actualmente para el modelo de la vista.

El siguiente código de ejemplo muestra como podemos llamar al métodhttp://bicentenariobu.com/o `setTemplate()`
desde el método `indexAction()` de la clase `IndexController` para forzar a Laminas
a usar el archivo de template de vista *about.phtml* para imprimir la página
*Home* en lugar del archivo *index.phtml*:

~~~php
// Index action renders the Home page of your site.
public function indexAction()
{
	// Use a different view template for rendering the page.
	$viewModel = new ViewModel();
	$viewModel->setTemplate('application/index/about');
	return $viewModel;
}
~~~

En el código de arriba creamos una nueva instancia de la clase `ViewModel` (línea 5).

Luego llamamos al método `setTemplate()` del objeto del modelo de vista (línea 6)
y pasamos el nombre de la plantilla de vista como argumento. El nombre de la plantilla
de vista es la ruta relativa al archivo `about.phtml`, sin la extensión
del archivo.

Finalmente regresamos el objeto del modelo de vista (el objeto de la clase `ViewModel`)
desde el método de acción (línea 7).

I> Sin embargo, llamar al método `setTemplate()` en cada método de acción es
I> opcional. Si no lo hacemos Laminas determinará el nombre de la plantilla de vista
I> automáticamente mediante la concatenación del nombre del módulo actual, el
I> nombre del controlador y el nombre del método de acción.

## Resolución de Vista

Una vez que Laminas Framework tiene el nombre de la plantilla solo queda por determinar
la ruta absoluta para el archivo *.phtml* correspondiente. Esto es llamado
también *resolución de la plantilla de vista*. Las plantillas de vista se resuelven
con una clase especial de Laminas Framework llamada *view resolver*.

En Laminas existen dos decisores de vista listos para usar: `TemplatePathStack` y
`TemplateMapResolver`. Ambos decisores toman el nombre de la plantilla de vista
como entrada y regresa la ruta al archivo de plantilla de vista como salida.
El nombre de la plantilla está usualmente compuesto del nombre del módulo
seguido por el nombre del controlador y el nombre de la plantilla, como
"application/index/about", "application/index/index". Una excepción es "layout/layout"
que no incluye el nombre del módulo.

* El *template map resolver* usa una arreglo PHP asociado para determinar la ruta
  al archivo de plantilla de vista por su nombre. Esta manera es rápida pero se
  necesita mantener un arreglo con el mapa de las plantillas y actualizarlo cada
  vez que que agregamos un nuevo script de vista.
* El *template path stack resolver* asume que el nombre de la plantilla de vista
  se corresponde con la estructura de carpetas. Por ejemplo, el nombre de la
  plantilla "application/index/about" corresponde a
  *APP_DIR/module/Application/view/application/index/about.phtml*. Esta manera
  es más simple porque no tenemos que mantener ningún mapa.

Las configuraciones de los decisores de vista se almacenan dentro de nuestro
archivo *module.config.php* bajo la llave *view_manager*:

~~~php
<?php
return [
    //...

    'view_manager' => [
        //...

        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
~~~

Como podemos ver las configuraciones del mapa de decisión de plantillas (template
map resolver) se almacena bajo la llave *template_map*. Por defecto, existen varias
plantillas de vista "estádares" que se resuelven de esta manera: la plantilla para
la página index, la plantilla de diseño (hablaremos sobre esto en
[Apariencia de la Página y Diseño](#appearance))
y las plantillas de error (hablaremos sobre ellas un poco más luego). Estas páginas
estándar se sirven con este tipo de decisor porque es más rápido.

Las configuraciones de la pila de rutas de plantilla (template path stack resolver)
se almacenan bajo la llave *template_path_stack*. Podemos ver que este decisor
busca nuestros script de vista en el directorio "view" de nuestro módulo. Esta
es la razón por la que solo podemos colocar el archivo *about.phtml* dentro
de esta carpeta y ZF encontrará automáticamente la plantilla.

Ambos decisores tanto el mapa de decisión de plantillas como la pila de rutas de
plantillas trabajan en pareja. Primero, el mapa de decisión de plantillas, que es
más rápido, intenta encontrar la plantilla de vista en su arreglo de mapa y si la
página no se encuentra el decisor de pila de rutas de plantilla se ejecutará.

## Desactivar la Impresión de la Vista

Algunas veces necesitamos desactivar la impresión de las plantillas por defecto.
Para hacer esto solo regresamos el objeto `Response` desde la acción del controlador.

Por ejemplo, vamos a crear la clase `DownloadController` y agregar la acción
"file" que permitirá a los usuarios del sitio descargar archivos desde nuestro
sitio web. Esta acción no necesita la correspondiente plantilla de vista *file.phtml*
porque esta solo vuelca el contenido del archivo en el flujo de salida estándar
de PHP.

Agregamos el archivo *DownloadController.php* a la carpeta *Controller* del módulo
*Application* y luego colocamos el siguiente código dentro del archivo:

~~~php
<?php
namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

/**
 * This is the controller class for managing file downloads.
 */
class DownloadController extends AbstractActionController
{
    /**
     * This is the 'file' action that is invoked
     * when a user wants to download the given file.
     */
    public function fileAction()
    {
        // Get the file name from GET variable
        $fileName = $this->params()->fromQuery('name', '');

        // Take some precautions to make file name secure
        $fileName = str_replace("/", "", $fileName);  // Remove slashes
        $fileName = str_replace("\\", "", $fileName); // Remove back-slashes

        // Try to open file
        $path = './data/download/' . $fileName;
        if (!is_readable($path)) {
            // Set 404 Not Found status code
            $this->getResponse()->setStatusCode(404);
            return;
        }

        // Get file size in bytes
        $fileSize = filesize($path);

        // Write HTTP headers
        $response = $this->getResponse();
        $headers = $response->getHeaders();
        $headers->addHeaderLine(
                 "Content-type: application/octet-stream");
        $headers->addHeaderLine(
                 "Content-Disposition: attachment; filename=\"" .
                $fileName . "\"");
        $headers->addHeaderLine("Content-length: $fileSize");
        $headers->addHeaderLine("Cache-control: private");

        // Write file content
        $fileContent = file_get_contents($path);
        if($fileContent!=false) {
            $response->setContent($fileContent);
        } else {
            // Set 500 Server Error status code
            $this->getResponse()->setStatusCode(500);
            return;
        }

        // Return Response to avoid default view rendering
        return $this->getResponse();
    }
}
~~~

El método de acción toma el parámetro *name* desde la parte de consulta de la URL
(línea 19), remueve las barras del nombre del archivo (líneas 22-23), agrega
las cabeceras HTTP al objeto `Response` (líneas 39-45) y el contenido del archivo
(líneas 48-55). Finalmente, regresamos el objeto `Response` y así desactivar
la impresión de la vista por defecto.

Registramos la clase `DownloadController` agregando la siguiente línea al archivo
*module.config.php*:

~~~php
<?php
return [
    // ...
    'controllers' => [
        'factories' => [
            // ...
            Controller\DownloadController::class => InvokableFactory::class
        ],
    ],
    // ...
];
~~~

Además, necesitaremos agregar la *ruta* al archivo *module.config.php* (una ruta
le dice a Laminas a que URL le corresponde que acción en el controlador). Modificamos
la llave `routes` del archivo de configuración de la siguiente manera:

~~~php
<?php
return [
  // ...
  'router' => [
        'routes' => [
            // Add this route for the DownloadController
            'download' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/download[/:action]',
                    'defaults' => [
                        'controller'    => Controller\DownloadController::class,
                        'action'        => 'index',
                    ],
                ],
            ],
        ],
    ],
  // ...
];
~~~

Para ver como funciona la descarga del archivo creamos la carpeta *APP_DIR/data/download*
y colocamos un archivo de texto llamado *sample.txt* en ella. Luego abrimos el
navegador web y escribimos la URL "http://localhost/download/file?name=sample.txt"
en nuestra barra de navegación del navegador web y presionamos la tecla Enter.
El navegador descargará el archivo *sample.txt* y ofrecerá guardarlo en alguna
parte.

## Estrategias para Imprimir Vistas

Una *estrategia de impresión* (rendering strategy) determina como la página será
mostrada en el navegador web. Por defecto, para producir una página HTML la
plantilla de vista *.phtml* se imprime con la ayuda de la clase `PhpRenderer`
que está en el namespace `Laminas\View\Renderer`. Esta estrategia es útil en el
99% de los casos. Pero algunas veces necesitamos regresar algo más, por ejemplo,
una respuesta JSON o una respuesta RSS.

I> Una respuesta en formato JSON se regresa generalmente cuando implementamos algún
I> tipo de API (Interfaz de Programación de Aplicaciones). Una API es usada para
I> recuperar algún tipo de datos en un formato legible por la maquina. Una respuesta
I> en formato RSS es generalmente usada para publicar información que cambia
I> frecuentemente como noticias y publicaciones de un blog.

Así, Laminas provee tres estrategias para imprimir vistas listas para usar:

  * La estrategia por defecto (también conocida como `PhpRenderingStrategy`).
  * La estrategia `JsonStrategy` que produce una respuesta JSON.
  * Y la estrategia `FeedStrategy` que produce un respuesta RSS.

### Regresar una Respuesta JSON

Por ejemplo, vamos a mostrar como usar la `JsonStrategy` para regresar una respuesta
JSON desde la acción en el controlador.

Primero, necesitaremos *registrar* la estrategia en el archivo de configuración
*module.config.php*:

~~~php
<?php
return [
    //...

    'view_manager' => [
        //...

        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],
];
~~~

Luego regresamos `JsonModel` (en lugar de `ViewModel` usual) desde nuestro método
de acción del controlador:

~~~php
namespace Application\Controller;

use Laminas\Mvc\Controller\ActionController;
use Laminas\View\Model\JsonModel;

class IndexController extends ActionController
{
    public function getJsonAction()
    {
        return new JsonModel([
            'status' => 'SUCCESS',
            'message'=>'Here is your data',
            'data' => [
                'full_name' => 'John Doe',
                'address' => '51 Middle st.'
            ]
        ]);
    }
}
~~~

Si abrimos esta página en nuestro navegador veremos la respuesta JSON:

~~~
{'status':'SUCCESS', 'message':'Here is your data', 'data':{'full_name:'John Doe', 'address':'51 Middle st.'}}
~~~

## Páginas de Error

Cuando una página no se puede encontrar o algún otro error sucede dentro de nuestra
aplicación web se muestra una página de error estándar. La apariencia de esta
página es controlada por las plantillas de error. Existen dos plantillas de error:
*error/404* que se usada para el error "404 Page Not Found" (ver figura 4.10)
y *error/index* que se muestra cuando un error genérico ocurre (tal como una
excepción no manejada lanzada desde cualquier lugar dentro de la aplicación).

![Figura 4.10. 404 Error Page](../en/images/mvc/error_404.png)

El archivo de configuración *module.config.php* contiene varios parámetros bajo
la llave *view_manager* que podemos usar para configurar la apariencia de nuestras
plantillas de error:

~~~php
<?php
return [
    //...

    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        //...
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            //...
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index'=> __DIR__ . '/../view/error/index.phtml',
        ],
        //...
    ],
];
~~~

* El parámetro *display_not_found_reason* controla si se muestra la información
  detallada sobre el error "Page not Found".
* El parámetro *display_exceptions* define si se muestra la información sobre
  una excepción no manejada y su traza.
* El parámetro *not_found_template* define el nombre de la plantilla para el error
  404.
* El parámetro *exception_template* específica el nombre de la plantilla para el
  error de excepción no manejada.

T> Típicamente colocamos los parámetros *display_not_found_reason* y *display_exceptions*
T> en `false` para entornos de producción porque no queremos que los visitantes
T> del sitio vean los detalles de los errores del sitio. Sin embargo, aún seremos
T> capaces de ver la información detallada en el archivo `error.log` de Apache.

## Modelos

Un *modelo* es una clase PHP que contiene la lógica de negocio de nuestra aplicación.
La lógica de negocio es el "núcleo" de nuestro sitio web. En ellos se implementa
el objetivo del sitio web. Por ejemplo, si implementamos un sitio web de comercio
electrónico tendremos modelos que implementan el catálogo de productos y el
carro de compras.

En general el termino modelo significa una representación simplificada de un objeto
de la vida real o un fenómeno. Simplificado porque el objeto de la vida real tiene
una infinita cantidad de propiedades. Por ejemplo, un persona en la vida real que
visita nuestro sitio está constituida por billones de átomos y no podemos describirlos
todos. En su lugar, tomamos varias propiedades de un objeto que son las más
importantes para nuestro sistema e ignoramos todas las otras. Por ejemplo, las
propiedades más importantes de un visitante del sitio (desde el punto de vista de
un arquitecto de software) son: primer nombre, segundo nombre, país, ciudad,
código postal y dirección.

Los modelos pueden tener algún comportamiento. Por ejemplo, el modelo *envió de correo*
puede enviar mensajes de correo electrónico, el modelo *convertidor de monedas*
puede ser capaz de convertir dinero, etc.

I> Con Laminas representamos los modelos como clases de PHP usuales. Las propiedades se
I> implementan como campos de una clase y el comportamiento se implementa como
I> métodos de clase.

## Tipos de Modelo

En Laminas Framework, como ya pudimos adivinar, no hay una sola carpeta `Model`
para guardar las clases de modelos. En cambio y por convención, los modelos
son subdivididos en los siguientes tipos principales que son guardados cada uno
en sus propias subcarpetas (ver tabla 4.9):

{title="Table 4.9. Tipos de Modelos y su ubicación"}
|--------------------------------|----------------------------------------------------------|
| *Tipo de Modelo*               | *Carpeta*                                                |
|--------------------------------|----------------------------------------------------------|
| Entidades                      | `APP_DIR/module/Application/src/Entity`                  |
|--------------------------------|----------------------------------------------------------|
| Repositorios                   | `APP_DIR/module/Application/src/Repository`              |
|--------------------------------|----------------------------------------------------------|
| Objetos con Valor (Value Objects)| `APP_DIR/module/Application/src/ValueObject`           |
|--------------------------------|----------------------------------------------------------|
| Servicios                      | `APP_DIR/module/Application/src/Service`                 |
|--------------------------------|----------------------------------------------------------|
| Fábricas                       | Una subcarpeta `Factory` dentro de cada carpeta de tipo  |
|                                | de modelo. Por ejemplo, las fábricas para los            |
|                                | controladores se almecenarán en                          |
|                                | `APP_DIR/module/Application/src/Controller/Factory`      |
|--------------------------------|----------------------------------------------------------|

I> Separar los modelos en diferentes tipos hace más fácil el diseño de nuestro
I> dominio de reglas de negocio. Esto es también llamado "Diseño guiado por dominio"
I> (o brevemente DDD). La persona que propuso DDD es Eric Evans en su famoso
I> libro llamado *Domain-Driven Design — Tackling Complexity in the Heart of Software*.

Abajo describiremos los principales tipos de modelos.

### Entidades

Las *Entidades* se usan para guardar datos que siempre tienen una propiedad que
funciona como identificador de manera que podemos identificar de manera única los
datos. Por ejemplo, una entidad `User` siempre tiene la propiedad única `login`,
y mediante este atributo podemos identificar al usuario. Podemos cambiar los otros
atributos de la entidad, como `primerNombre` o `dirección` pero su identificador
nunca cambia. Las entidades son almacenadas usualmente en una base de datos,
en un archivo del sistema o en cualquier otro almacenamiento.

Abajo podemos encontrar un ejemplo de la entidad `User` que representa a los
usuarios del sitio web:

~~~php
// The User entity represents a site visitor
class User
{
    // Properties
    private $login;     // e.g. "admin"
    private $title;     // e.g. "Mr."
    private $firstName; // e.g. "John"
    private $lastName;  // e.g. "Doe"
    private $country;   // e.g. "USA"
    private $city;      // e.g. "Paris"
    private $postCode;  // e.g. "10543"
    private $address;   // e.g. "Jackson rd."

    // Behaviors
    public function getLogin()
    {
        return $this->login;
    }

    public function setLogin($login)
    {
        $this->login = $login;
    }

    //...
}
~~~

En las líneas 5-12 definimos las propiedades del modelo `User`. La mejor práctica
es definir las propiedades usando el tipo de acceso privado y hacerlas disponibles
a través de los métodos públicos *getter* y *setter* (como `getLogin()` y
setLogin(), etc).

I> El comportamiento de los métodos del modelo no se limitan a los getters y
I> setters. Podemos crear otros métodos que manipulan los datos del modelo.
I> Por ejemplo, podemos definir a conveniencia el método `getFullName()`
I> que regresaría el nombre completo del usuario: "Mr. John Doe".

### Repositorios

Los *repositorios* son modelos específicos responsables de guardar y recuperar
entidades. Por ejemplo, el `UserRepository` puede representar una tabla de la
base de datos y proveer los métodos para recuperar las entidades `User`. Generalmente
usamos los repositorios cuando guardamos entidades en la base de datos. Con los
repositorios podemos encapsular la lógica de la consulta SQL en un solo lugar,
y de fácil mantenimiento y, además, probarlos.

I> Aprenderemos sobre los repositorios con más detalles en
I> [Administración de la Base de Datos con Doctrine](#doctrine) en donde
I> hablaremos sobre la biblioteca Doctrine.

### Objetos con Valor

Los *objetos con valor* son un tipo de modelo en que no es importante la identidad
como si lo es en las entidades. Un objeto con valor es usualmente una
pequeña clase identificada por medio de todos su atributos. Él no tiene un
atributo identificador. Los objetos con valor típicamente tienen métodos getter
pero no tienen métodos setters (los objetos con valor son inmutables).

Por ejemplo, un modelo que maneja una cantidad de dinero puede ser tratado
como un objeto con valor:

~~~php
class MoneyAmount
{
    // Properties
    private $currency;
    private $amount;

    // Constructor
    public function __construct($amount, $currency='USD')
    {
        $this->amount = $amount;
        $this->currency = $currency;
    }

    // Gets the currency code
    public function getCurrency()
    {
        return $this->currency;
    }

    // Gets the money amount
    public function getAmount()
    {
        return $this->amount;
    }
}
~~~

En las líneas 4-5 definimos dos propiedades: `currency` y `amount`. El modelo
no tiene una propiedad como identificador único, en cambio su identidad se define
por medio de todas sus propiedades: si cambiamos la propiedad `currency` o
`amount` tendríamos diferentes objetos con diferentes cantidades de dinero.

En las líneas 8-12 definimos el método constructor que inicializa las propiedades.

En las líneas 15-24 definimos los métodos getter para las propiedades del modelo.
Veamos que no tenemos métodos setter (el modelo es inmutable).

### Servicios

Los modelos de tipo *servicio* usualmente encapsulan alguna parte de las funcionalidades
de la lógica de negocio. Los servicios tienen nombres reconocibles fácilmente
por su terminación en "er", como `FileUploader` o `UserManager`.

Abajo un ejemplo del servicio `Mailer` se presenta. Este tiene el método `sendMail()`
que toma el objeto `EmailMessage` como valor y enviá un mensaje de correo electrónico
usando la función estándar `mail()` de PHP:

~~~php
<?php

// The Email message value object
class EmailMessage
{
    private $recipient;
    private $subject;
    private $text;

    // Constructor
    public function __construct($recipient, $subject, $text)
    {
        $this->recipient = $recipient;
        $this->subject = $subject;
        $this->text = $text;
    }

    // Getters
    public function getRecipient()
    {
        return $this->recipient;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function getText()
    {
        return $this->text;
    }
}

// The Mailer service, which can send messages by E-mail
class Mailer
{

    public function sendMail($message)
    {
        // Use PHP mail() function to send an E-mail
        if(!mail($message->getRecipient(), $message->getSubject(),
             $message()->getText()))
        {
            // Error sending message
            return false;
        }

        return true;
    }
}
~~~

T> En Laminas Framework registramos nuestros modelos de tipo servicio en el
T> Administrador de Servicios.

### Fábricas

Las fábricas se diseñan usualmente para instanciar otros modelos (particularmente
los modelos de tipo servicio). En los casos más simples podemos crear una instancia
sin una fábrica mediante el uso del operador `new` pero a veces la lógica de
creación de clases debe ser más compleja. Por ejemplo, es común que los servicios
dependan de otros servicios, así necesitaremos *inyectar* dependencias a un servicio.
Además, puede ser necesario inicializar el servicio justo después de la instanciación
mediante el llamado de uno o varios de sus métodos.

Los nombres de las clases tienen típicamente nombres que terminan con el sufijo
`Factory` tal como `CurrencyConverterFactory`, `MailerFactory`, etc.

Como un ejemplo de la vida real vamos a imaginar que tenemos el servicio `PurchaseManager`,
que puede procesar las compras de determinados bienes y que el servicio `PurchaseManager`
usa otro servicio llamado `CurrencyConverter` que se puede conectar a un servicio
externo que provee las tasas de cambio. Vamos a escribir una clase de tipo fábrica
para el `PurchaseManager` que instanciará el servicio y lo pasará como dependencia:

~~~php
<?php
namespace Application\Service\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Application\Service\CurrencyConverter;
use Application\Service\PurchaseManager;

/**
 * This is the factory for PurchaseManager service. Its purpose is to instantiate the
 * service and inject its dependencies.
 */
class PurchaseManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container,
                $requestedName, array $options = null)
    {
        // Get CurrencyConverter service from the service manager.
        $currencyConverter = $container->get(CurrencyConverter::class);

        // Instantiate the service and inject dependencies.
        return new PurchaseManager($currencyConverter);
    }
}
~~~

En el código de arriba tenemos la clase `PurchaseManagerFactory` que implementa
la interface `Laminas\ServiceManager\Factory\FactoryInterface`. La clase de tipo
fábrica tiene el método `__invoke()` cuyo objetivo es instanciar el objeto.
Este método tiene el argumento `$container` que es el administrador de servicios.
Podemos usar la variable `$container` para recuperar servicios del administrador
de servicios y pasarlos al método constructor del servicio que se está instanciando.

## Determinar el Tipo de Modelo Correcto

Q> **¿No es confuso tener tantos tipos de modelos?**
Q>
Q> Bueno, si y no. En un primer momento puede ser un poco difícil determinar
Q> el tipo de modelo correcto pero tan pronto como mejoremos nuestras habilidades
Q> podremos ser capaces de determinarlo intuitivamente. Recordemos que los tipos
Q> de modelos mejoran la estructura de nuestro modelo de dominio.

Cuando escribimos nuestra propia aplicación puede ser difícil determinar a que
tipo de modelo pertenece nuestra clase (si es una entidad, un objeto con
valor, un repositorio, un servicio o una fábrica). Abajo damos un algoritmo
simple que hace más fácil determinar el tipo correcto de modelo a usar cuando
escribimos nuestra aplicación:

* Nuestra clase modelo es definitivamente un *Servicio*:
    * Si encapsula algunas reglas de la lógica de negocio.
    * Si la llamamos desde la clase controladora.
    * Si creemos que el nombre de la clase debe terminar en "er", como en las.
      clases `FileUploader` o `VersionChecker`
* Nuestra clase modelo es una *Entidad*:
    * Si el modelo se almacena en una base de datos.
    * Si tiene un atributo único o ID.
    * Si tiene métodos getters y setters.
* Nuestra clase modelo es un *Objeto con valor* (ValueObject):
    * Si al cambiar cualquier atributo el modelo es completamente diferente.
    * Si el modelo tiene getters pero no setters (inmutable).
* Nuestro modelo es un *Repositorio*:
    * Si trabaja con la base de datos para recuperar entidades.
* Nuestro modelo es una *Fábrica*:
    * Si puede crear otros objetos y no hace nada más que eso.

Q> **¡um! ¿Que pasa si simplemente guardamos todos los modelos en una sola
Q> carpeta de modelos?**
Q>
Q> Por supuesto que podemos hacerlo si lo deseamos decididamente. Pero cuando
Q> usemos la biblioteca Doctrine ORM notaremos que ella usas los principios
Q> DDD. Además, usando DDD tendremos nuestra aplicación mejor organizada.

## Otros Tipos de Modelos

En nuestro sitio web dividiremos nuestros principales tipos de modelos en subtipos.
(que describimos abajo). Por ejemplo tendremos:

  * *Forms*. Los formularios son modelos cuyo propósito es recolectar los datos
    ingresados por el usuario. Los formularios son subtipos de *entidades*.
    Generalmente guardaremos los formularios en la carpeta
    `APP_DIR/module/Application/src/Form`.

  * *Filters*. Los filtros se diseñan para transformar los datos de entrada. Los
    filtros son un subtipo de *servicios*. Los filtros se guardan generalmente
    en la carpeta `APP_DIR/module/Application/src/Filter`.

  * *Validators*. Los validadores se usan para revisar la corrección de los
    datos de entrada. Los validadores son subtipos de *servicios*. Generalmente
    guardamos los validadores en la carpeta `APP_DIR/module/Application/src/Validator`.

  * *View Helpers*. Los ayudantes de vista encapsulan algunas funcionalidades
    que se imprimen en las páginas. Ellos son similares a los *servicios*.
    Guardamos generalmente a los ayudantes de vista en la carpeta
    `APP_DIR/module/Application/src/View/Helper`.

  * *Routes*. Las rutas son un modelo de *servicio* específico que se usa para
    implementar reglas a la medida de asociación entre URL y controladores.
    Generalmente guardamos las rutas a la medida en la carpeta
    `APP_DIR/module/Application/src/Route`.

Así, eventualmente tendremos la siguiente estructura de carpetas para nuestro
módulo del sitio web:

~~~
/Application/src
    /Controller
        /Factory
        /Plugin
            /Factory
    /Entity
    /Filter
    /Form
    /Repository
    /Route
    /Service
        /Factory
    /Validator
    /ValueObject
    /View
        /Helper
            /Factory
~~~

I> Es posible tener arbitrariamente subtipos de modelos. Mientras más compleja
I> es nuestra aplicación más subtipos de modelos podemos tener.

## Controladores Flacos, Modelos Gordos y Vistas Simples

Cuando desarrollamos un sitio web usando el patrón Modelo-Vista-Controlador está
el riesgo de mal interpretar el rol de los controladores, las vistas y los
modelos. El resultado de esto es controladores enormes y modelos pequeños lo que
hace difícil probar y mantener nuestra aplicación. El objetivo de esta sección
es dar una comprensión general sobre el código que se puede colocar en la clase
controladora, el que se puede colocar en la plantilla de vista y el código
que se puede colocar en una clase modelo.

### Controladores Flacos

La idea que está detrás del termino "controladores flacos" (skinny controller)
es que generalmente en nuestras clases controladoras solo colocamos código que:

* Acceder a los datos de la petición del usuario (`$_GET`, `$_POST`, `$_FILES`
  y otras variables PHP).
* Revisar la validez de los datos de entrada.
* Hacer algunas preparaciones básicas a los datos (opcional).
* Pasar los datos a el o los modelos y recuperar el resultado regresados por
  el o los modelos.
* Y finalmente, regresar los datos como parte del contenedor de variables
  `ViewModel`.

A controller class should avoid:

* Contener reglas complejas de la lógica de negocios que es mejor mantenerlas en
  las clases de modelo.
* Contener HTML o cualquier otro tipo de código de marcado propio de la capa
  de presentación. Este es mejor que se coloque en las plantillas de vista.

Para un ejemplo de un controlador "flaco" miremos más abajo la clase
`CurrencyConverterController`. Este controlador provee el método de acción
"convert" cuyo objetivo es convertir un cantidad de EUR a USD. El usuario pasa
la cantidad de dinero a través de la variable GET "amount".

~~~php
class CurrencyConverterController extends AbstractActionController
{
    // Currency converter model
    private $currencyConverter;

    // Constructor. It's purpose is to "inject" dependencies.
    public function __construct($currencyConverter)
    {
        $this->currencyConverter = $currencyConverter;
    }

    // The "convert" action displays the converted money amount
    public function convertAction()
    {
        // Get the money amount from GET
        $amount = (float)$this->params()->fromQuery('amount', -1);

        // Validate input data
        if($amount<0) {
            // Money amount is missing
            $this->getResponse()->setStatusCode(404);
            return;
        }

        // Pass the data to the CurrencyConverter model
        $convertedAmount = $this->currencyConverter->convertEURtoUSD($amount);

        return new ViewModel([
            'amount'=>$amount,
            'convertedAmount'=>$convertedAmount
        ]);
    }
}
~~~

El método de acción del controlador anterior hace lo siguiente:

* Toma los datos pasados por el usuario (línea 16). Estos datos son usualmente
  parte del objeto `Request` que se pueden recuperar usando el método del controlador
  `getRequest()` o por medio del complemento para controladores `Params`.

* Ejecutar revisiones básicas sobre los datos que el usuario pasa (línea 19) y
  si los datos no existen o son inválidos colocamos un código de error HTTP
  (línea 21).

* Pasar la cantidad de dinero al modelo `CurrencyConverter` (línea 26) mediante
  el llamado al método `convertEURtoUSD()`. Luego el método regresa la cantidad
  convertida.

* Construye el contenedor de variables `ViewModel` y le pasa los datos resultantes
  (línea 28). A este contenedor de variables se puede acceder luego en la
  correspondiente plantilla de vista responsable de la presentación de los datos.

### Modelos Gordos

Como necesitamos mantener nuestros controladores tan flacos como sea posible
la mayoría de la lógica de negocio de nuestra aplicación se debería colocar
dentro de las clases modelo. En una aplicación diseñada correctamente bajo el
patrón Modelo-Vista-Controlador los modelos se ven "enormes". Una clase de
modelo puede contener código que:

* Ejecuta filtrado de datos y validaciones complejas. Como los datos que
  recuperamos en el controlador están entrando a nuestra aplicación desde el
  mundo exterior en el modelo tenemos que esforzarnos por verificarlos
  y asegurar que estos no rompan nuestros sistema. El resultado de esto es un
  sitio web seguro que resiste los ataque de los crackers.

* Manipula los datos. Nuestros modelos deben manipular los datos, por ejemplo,
  cargar los datos desde la base de datos, guardarlos en la base de datos y
  transformar los datos. Los modelos son el lugar correcto para guardar
  las consultas de base de datos, los funcionalidades de lectura y escritura de
  archivos, etc.

En las clases de modelo no es recomendable:

* Acceder a los datos de las petición HTTP, `$_GET`, `$_POST` y otras variables
  PHP. Ese es el trabajo del controlador: extraer los datos y pasarlos a la
  entrada del modelo.

* Producir código HTML u otro código específico de la presentación. El código
  de presentación puede variar dependiendo de la petición del usuario y es
  mejor colocarlo en la plantilla de vista.

Si seguimos estos principios encontraremos que nuestros modelos son fáciles
de probar porque ellos tienen identificados claramente las entradas y las salidas.
Podemos escribir pruebas unitarias que pasan determinados datos de prueba como
entrada a los modelos, recuperar los datos de salida y verificar que los
datos son correctos.

Si estamos confundidos sobre donde colocar un determinado segmento de código
o en el controlador o en el modelo, podemos intentar preguntarnos a nosotros mismos:
¿Es este pedazo de código una regla del negocio importante que necesita ser
cuidadosamente probada? si la respuesta es sí debemos colocar el código en un
modelo.

### Plantillas de Vista Simple

Como la mayoría de la lógica se guarda en los modelos, nuestras plantillas de
vista serán tan simples como sea posible para producir la presentación de los
datos que se pasan a través del contenedor de variables. En una plantilla de
vista podemos:

* Tener código HTML estático.

* Recuperar los datos desde el contenedor de variables e imprimirlos en el
  flujo de salida PHP.

* Si un controlador pasa un determinado modelo por medio del contenedor de
  variables podemos consultar los datos del modelo
  (por ejemplo, podemos recuperar las columnas de una tabla de base de datos
  e imprimirlas).

* Contener estructuras de control simples de PHP como: `if`, `foreach`, `switch`,
  etc. Esto permite variar la presentación dependiendo de las variables pasadas
  por el controlador.

No es recomendable que las plantillas de vista:

* Acceder a los datos de la petición HTTP y a las variables PHP super globales.

* Crear modelos, manipularlos y modificar el estado de la aplicación.

Si seguimos estos principios encontraremos que nuestras vistas se pueden sustituir
fácilmente sin modificar la lógica de negocio de nuestra aplicación. Por ejemplo,
podemos fácilmente cambiar el diseño de nuestra página web o introducir
temas intercambiables.

## Resumen

Una aplicación web basada en Laminas Framework es solo un programa escrito en PHP
que recibe una petición HTTP desde el servidor web y produce una respuesta HTTP.
La aplicación web usa el patrón Modelo-Vista-Controlador para separar la lógica
del negocio de la presentación. El objetivo de esto es permitir la reutilización
del código y la separación de conceptos.

Un controlador es un mediador entre los modelos y las vistas de la aplicación:
el controlador trae las entradas desde la petición HTTP y usa los modelos y
la vista correspondiente para producir la respuesta HTTP necesaria. Un
controlador es una clase PHP usual que contiene métodos de acción.

Las vistas son una simple combinación de código HTML y retazos de código PHP que
producen una salida HTML que se regresa mediante el servidor web al visitante
del sitio. Pasamos los datos a los scripts de vista a través del contenedor
de variables `ViewModel`.

Un modelo es una clase PHP que contiene la lógica de negocio de nuestra aplicación.
La lógica de negocio es el "núcleo" de nuestro sitio web en donde se implementa
el objetivo de funcionamiento del sitio. Los modelos pueden acceder a la base
de datos, manipular los archivos del disco, conectarse a sistemas externos,
manipular otros modelos, etc.
