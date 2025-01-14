# Operación del Sitio Web {#operation}

En este capítulo daremos algo de teoría sobre como funciona una típica aplicación web basada en Laminas Framework.
Aprenderemos cosas básicas como clases de PHP, sobre como los espacios de nombres son usados para evitar la colisión de nombres,
aprenderemos lo que es una clase *autoloading*, como definir los parámetros de configuración de una aplicación y
la etapa actual dentro del ciclo de vida de una aplicación. Además, nos familiarizaremos con cada uno de estos
importantes componentes de Laminas: @`Laminas\EventManager`, @`Laminas\ModuleManager` y @`Laminas\ServiceManager`.
Si en lugar de aprender la teoría quieres tener algunos ejemplos prácticos salta este capítulo
y revisa directamente el [Modelo-Vista-Controlador](#mvc).

Los componentes de Laminas sobre los que hablaremos en este capítulo son:

|--------------------------------|---------------------------------------------------------------|
| *Componente*                   | *Descripción*                                                 |
|--------------------------------|---------------------------------------------------------------|
| @`Laminas\Mvc`                    | Provee el patrón Modelo-Vista-Controlador. Separación de la   |
|                                | lógica del negocio de la presentación.                        |
|--------------------------------|---------------------------------------------------------------|
| @`Laminas\ModuleManager`          | Este componente es responsable de cargar e inicializar los módulos de la aplicación. |
|--------------------------------|---------------------------------------------------------------|
| @`Laminas\EventManager`           | Este componente implementa funcionalidades para el lanzamiento y manejo de eventos. |
|--------------------------------|---------------------------------------------------------------|
| @`Laminas\ServiceManager`         | Implementa un registro de todos los servicios disponibles en la aplicación web. |
|--------------------------------|---------------------------------------------------------------|

## Clases PHP

PHP soporta programación orientada a objetos (POO). En la POO, el principal bloque de nuestro código es una *clase*.
Una clase puede tener *propiedades* y *métodos*. Por ejemplo, vamos a crear un script llamado *Person.php* y
luego definimos un clase muy simple llamada `Person` dentro del archivo:

~~~php
<?php

class Person
{
    private $fullName;

    public function __construct()
    {
        // Some initialization code.
        $this->fullName = 'Unknown person';
    }

    public function getFullName()
    {
        return $this->fullName;
    }

    public function setFullName($fullName)
    {
        $this->fullName = $fullName;
    }
}
~~~

I> Podemos observar que en el ejemplo de arriba tenemos una etiqueta de apertura `<?php`
I> que le dice al motor de PHP que el texto después de la etiqueta es código PHP. En el ejemplo
I> de arriba, el archivo solo contiene código PHP (no está mezclado código PHP con HTML) por lo que no
I> necesitamos insertar la etiqueta de cierre `?>` al final del código. Además, esto no se recomienda
I> y puede causar efectos indeseables si inadvertidamente agregas algunos caracteres después de la
I> etiqueta de cierre.

La clase `Person` de arriba tiene un propiedad privada `$fullName` y tres métodos:

  * El método `__construct` es un método especial llamado *constructor*. Se usa en el caso de que necesitemos
    inicializar propiedades de la clase.

  * `getFullName()` y `setFullName()` son métodos públicos que se usan para hacer algo con la clase.

Una vez que tenemos definidos los métodos de la clase podemos crear *objetos* de la clase con el operador `new`,
de la siguiente manera:

~~~php
<?php

// Instantiate the Person.
$person = new Person();

// Set full name.
$person->setFullName('John Doe');

// Print person's full name to screen.
echo "Person's full name is: " . $person->getFullName() . "\n";
~~~

I> Las clases permiten separar nuestras funcionalidades en pequeños bloques manteniéndola
I> bien organizada.
I> Laminas consiste en cientos de clases.
I> Además, escribiremos nuestras propias clases para nuestra aplicación web.

## PHP Namespaces

Cuando usamos clases de diferentes bibliotecas (o incluso clases de diferentes componentes de
una misma biblioteca) en nuestro programa los nombres de las clases pueden chocar. Esto significa
que podemos encontrar dos clases con el mismo nombre, el resultado es un error de interpretación
de PHP. Si alguna vez programamos sitios webs con Laminas Framework 1 podemos recordar esos nombres de
clases *extra* largos como `Laminas_Controller_Abstract`. La idea detrás de estos nombres tan largos
era evitar la colisión de nombres entre diferentes componentes. Cada componente definía su propio
prefijo como `Laminas_` o `My_`

Para alcanzar el mismo objetivo Laminas Framework usa una característica de PHP
llamada *namespaces*.
Los namespaces o espacio de nombres permiten resolver la colisión de nombres
entre componentes y nos permite tener nombres más cortos.

Un namespace es un contenedor para un grupo de nombres. Podemos anidar un
namespaces dentro de otro.
Si una clase no define un namespace ella se puede buscar dentro del namespace
*global*  (por ejemplo, las clases de PHP `Exception` o `DateTime` pertenecen al
namespace global).

Un ejemplo realista de la definición de un namespace, tomado del componente
@`Laminas\Mvc`, se muestra abajo:

~~~php
<?php
namespace Laminas\Mvc;

/**
 * Main application class for invoking applications.
 */
class Application
{
    // ... class members were omitted for simplicity ...
}
~~~

En Laminas Framework todas las clases están dentro del namespace de primer nivel *Laminas*.
La línea 2 define el namespace *Mvc* que está anidado dentro del namespace *Laminas*,
todas las clases de este componente (incluida la clase @`Application`[Laminas\Mvc\Application]
que se muestra entre
las líneas 7-10) pertenece a este namespace. Los namespace anidados se separan con
la barra invertida ('\\').

En las otras partes del código hacemos referencia a la clase @`Application`[Laminas\Mvc\Application]
usando su nombre completo (*fully-qualified name*):

~~~php
<?php
$application = new \Laminas\Mvc\Application();
~~~

I> Obsérvese la presencia de la primera barra invertida en el nombre @`\Laminas\Mvc\Application`.
I> Si especificamos un nombre de clase que comienza con una barra invertida estamos usando
I> el nombre completo de la clase. Es posible especificar un nombre de clase relativo al actual
I> namespace, en este caso no usamos la barra invertida inicial.

Es posible usar *alias* (un nombre corto para la clase) con la ayuda de la sentencia
`use` de PHP:

~~~php
<?php
// Define the alias in the beginning of the file.
use Laminas\Mvc\Application;

// Later in your code, use the short class name.
$application = new Application();
~~~

T> Aunque los alias permiten usar nombres de clase cortos en lugar del nombre completo
T> su uso es opcional. No es necesario usar alias y podemos hacer referencia a la clase
T> por su nombre completo cualificado.

Generalmente, cada archivo PHP de nuestra aplicación define un espacio de nombres (excepto el script
de inicio *index.php* y los archivos de configuración que comúnmente no tienen
un espacio de nombres.
Por ejemplo, el módulo principal de nuestro sitio web, el módulo *Application* define su
propio espacio de nombres y este es igual al nombre del módulo:

~~~php
<?php
namespace Application;

class Module
{
    // ... class members were omitted for simplicity ...
}
~~~

## Interfaces PHP

En PHP las *interfaces* permiten definir el comportamiento que una clase debe tener pero
sin proveer la implementación de tal comportamiento. Esto también es llamado un *contract*:
al implementar una interface la clase acepta los términos del contrato.

En Laminas Framework las interfaces son ampliamente usadas. Por ejemplo, la clase @`Application` implementa
la @`ApplicationInterface`, de esta manera se definen los métodos que cada clase *application* debe proveer:

~~~php
<?php
namespace Laminas\Mvc;

interface ApplicationInterface
{
    // Retrieves the service manager.
    public function getServiceManager();

    // Retrieves the HTTP request object.
    public function getRequest();

    // Retrieves the HTTP response object.
    public function getResponse();

    // Runs the application.
    public function run();
}
~~~

Como podemos ver en el ejemplo de arriba una interfaz es definida usando la palabra
reservada `interface` casi de la misma manera como definimos una clase PHP estándar.
Así como una clase usual las interfaces definen métodos. Sin embargo, la interfaz no provee
ninguna implementación de sus métodos. En la interface @`ApplicationInterface` definida arriba
podemos ver que cada aplicación que implemente esta interfaz tendrá el método `getServiceManager()`
para recuperar el Gestor de Servicios (el Gestor de Servicios lo revisaremos más adelante
en este capítulo), los métodos `getRequest()` y `getResponse()` que respectivamente recuperan
las peticiones y respuestas HTTP, y el método `run()` para ejecutar la aplicación.

I> En Laminas Framework, por convención, las interfaces de clases deben incluir en su nombre el
I> sufijo `Interface` por ejemplo @`ApplicationInterface`.

La clase que implementa una interfaz se llama clase *concreta*. La clase concreta @`Application`
implementa la @`ApplicationInterface`, lo que significa que provee la implementación de los métodos
definidos por la interface:

~~~php
<?php
namespace Laminas\Mvc;

class Application implements ApplicationInterface
{
    // Implement the interface's methods here

    public function getServiceManager()
    {
        // Provide some implementation...
    }

    public function getRequest()
    {
        // Provide some implementation...
    }

    public function getResponse()
    {
        // Provide some implementation...
    }

    public function run()
    {
        // Provide some implementation...
    }
}
~~~

La clase concreta @`Application` usa la palabra clave `implements` para mostrar que
esta provee una implementación de todos los métodos de la interfaz `ApplicationInterface`.
Además, la clase @`Application` puede tener métodos adicionales que no están en
la interfaz.

Gráficamente las relaciones entre las clases se muestran usando un diagrama de herencia.
En la figura 3.1, se muestra el diagrama de la clase @`Application`. La flecha
apunta desde la clase hijo a la clase padre.

![Figura 3.1. Diagrama de la clase Application](../en/images/operation/Application.png)

## La Clase Autoloading de PHP

Una aplicación web consiste en muchas clases PHP y cada clase generalmente reside en
un archivo separado. Esto crea la necesidad de *incluir* los archivos.

Por ejemplo, vamos a asumir que tenemos el archivo llamado *Application.php* que
contiene la definición para la clase @`\Laminas\Mvc\Application` de la sección anterior.
Antes de que podamos crear una instancia de la clase `Application` en cualquier lugar
de nuestro código debemos incluir el contenido del archivo *Application.php* (podemos
hacer esto con la ayuda de la sentencia `require_once`, pasándole la ruta completa
del archivo):

~~~php
<?php
require_once "/path/to/laminas/laminas-mvc/src/Application.php";

use Laminas\Mvc\Application;

$application = new Application();
~~~

Como la aplicación crece en tamaña puede ser difícil incluir cada nuevo archivo.
Laminas Framework consiste en cientos de archivos y puede ser muy difícil cargar
la biblioteca entera y todas sus dependencias. Además, cuando se ejecuta el código
resultante el interprete PHP consumirá tiempo de CPU para procesar cada archivo
incluido, aun cuando no se crea una instancia de su clase.

Para corregir este problema se introdujo en PHP una característica que permite la
carga automática de clases (class autoloading).
La función de PHP `spl_autoload_register()` permite registrar una función *autoloader*.
En sitios webs complejos podemos crear varias funciones *autoloader* que se
encadenan en una pila.

Durante la ejecución de un script si el interprete de PHP encuentra el nombre de una clase
que no se ha definido aún, se llama una por una a todas las funciones
*autoloader* registradas hasta encontrar a la función *autoloader* que tiene la clase o hasta que se alcanza
un error «not fount». Esto permite una *lazy loading*, el interprete PHP procesa la
definición de la clase solo en el momento de su invocación, cuando es realmente
necesario.

### La Clase «Map Autoloader»

Para dar una idea de como es una función autoloader, mostraremos una implementación
simplificada de una función autoloader:

~~~php
<?php
// Autoloader function.
function autoloadFunc($className)
{
    // Class map static array.
    static $classMap = [
        '\\Laminas\\Mvc\\Application' => '/path/to/laminas/laminas-mvc/src/Laminas/Mvc/Application.php',
        '\\Application\\Module' => '/path/to/app/dir/module/Application/Module.php',
        //...
    ];

    // Check if such a class name presents in the class map.
    if(isset(static::$classMap[$className])) {
        $fileName = static::$classMap[$className];

        // Check if file exists and is readable.
        if (is_readable($fileName)) {
            // Include the file.
            require $fileName;
        }
    }
}

// Register our autoloader function.
spl_autoload_register("autoloadFunc");
~~~

En el ejemplo de arriba definimos la función autoloader `autoloadFunc()`, que
llamaremos de ahora en adelante *clase map autoloader*.

La clase map autoloader usa la clase map para hacer una correspondencia entre el nombre
de la clase y la ruta absoluta al archivo PHP que contiene la clase. La clase map
es un típico arreglo de PHP que contiene llaves y valores. Para determinar la ruta
del archivo para cada nombre de clase, la clase map autoloader necesita traer el valor
desde el arreglo `$classMap`. Es obvio que la clase map autoloader trabaja muy rápido.
Sin embargo, su desventaja es que tenemos que mantener a la clase map actualizada, lo que
es necesario cada vez que agregamos una nueva clase a nuestro programa.

### El Estándar PSR-4

A causa de que cada biblioteca usa su propia nomenclatura para el código y sus propias convenciones
para la organización de los archivos, tendremos que registrar una función autoloader diferente y personalizada
por cada biblioteca que usemos, lo que es bastante molesto (y de hecho
un trabajo innecesario). Para resolver este problema, el estándar PSR-4 fue introducido.

I> PSR significa en ingles «PHP Standards Recommendation».

El [estándar PSR-4](http://www.php-fig.org/psr/psr-4/) define la estructura del código
recomendada que una aplicación o un biblioteca debe seguir para garantizar la
interoperabilidad entre autoloaders. En dos palabras el estándar dice que:

* Los namespaces de clases deben ser organizados de la siguiente manera:

  `\<Vendor Name>\(<Namespace>)*\<Class Name>`

* Los namespaces pueden anidar tantos niveles como se desee pero el *Vendor Name*
  debe ser el namespace de primer nivel.

* Los namespace deben corresponderse con la estructura de directorios. Cada separador
  de namespace ('\\') es convertido al separador de directorio especifico de cada
  sistema operativo, usando la constante `DIRECTORY_SEPARATOR`, cuando se carga
  desde el sistema de archivos.

* Al nombre de la clase se agrega el sufijo de extensión *.php* cuando se carga el
  archivo desde el sistema de archivos.

Por ejemplo, para la clase @`Laminas\Mvc\Application` tendremos la siguiente estructura
de directorios:

~~~text
/path/to/laminas/laminas-mvc/src
  /Laminas
    /Mvc
       Application.php
~~~

La desventaja de esto es que es necesario colocar el código dentro de varios directorios
anidados (*Laminas* y *Mvc*).

Para corregir esto PSR-4 permite que definamos una serie continua de uno o más namespace
o sub-namespaces iniciales que correspondan a una «carpeta base». Por ejemplo, si tenemos
el nombre de clase fully qualified: `Laminas\Mvc\Application`, y definimos que la serie
`Laminas\Mvc` corresponde a la carpeta "/path/to/laminas/laminas-mvc/src" podemos
organizar los archivos de la siguiente manera:

```
/path/to/laminas/laminas-mvc/src
    Application.php
```

Para que el código esté conforme al estándar PSR-4, podemos escribir y registrar
un autoloader que llamaremos autoloader «standard»:

~~~php
<?php

// "Standard" autoloader function.
function standardAutoloadFunc($className)
{
    // Replace the namespace prefix with base directory.
    $prefix = '\\Laminas\\Mvc';
    $baseDir = '/path/to/laminas/laminas-mvc/src/';
    if (substr($className, 0, strlen($prefix)) == $prefix) {
        $className = substr($className, strlen($prefix)+1);
        $className = $baseDir . $className;
    }

    // Replace namespace separators in class name with directory separators.
    $className = str_replace('\\', DIRECTORY_SEPARATOR, $className);

    // Add the .php extension.
    $fileName = $className . ".php";

    // Check if file exists and is readable.
    if (is_readable($fileName)) {
        // Include the file.
        require $fileName;
    }
}

// Register the autoloader function.
spl_autoload_register("standardAutoloadFunc");
~~~

El autoloader estándar funciona de la siguiente manera. Asumiendo que el namespace
de la clase se corresponde uno-a-uno a la estructura de directorios, la función
calcula la ruta hasta el archivo PHP transformando las barras invertidas (separadores
de namesapaces) a barras (separadores de ruta) y concatena la ruta resultante a la ruta
absoluta del directorio donde la biblioteca está ubicada. Luego, la función revisa
si el archivo PHP existe y si es así incluye el archivo usando la sentencia `require`.

Es obvio que el autoloader estándar funciona más lento que la clase map autoloader.
Sin embargo, su ventaja es que no necesitamos mantener ninguna clase map, lo que es
muy conveniente cuando desarrollamos código nuevo y agregamos nuevas clases a la
aplicación.

I> Laminas Framework se ajusta al estándar PSR-4, haciendo posible usar el mecanismo
I> de autoloading estándar con todos sus componentes. Es también compatible
I> con otras bibliotecas que se ajustan a PSR-4 como Doctrine o Symfony.

### El Autoloader provisto por Composer

Composer puede generar las funciones de autoloader (tanto la clase map autoloaders
como el estándar de autoloaders PSR-4) para el código que se instala con él.
Laminas Framework usa la implementación que hace Composer del «autoloader». Cuando
se instala un paquete usando Composer se crea automáticamente el archivo *APP_DIR/vendor/autoload.php*,
que usa la función de PHP `spl_autoload_register()` para registrar un autoloader.
De esta manera todas las clases de PHP ubicadas en el directorio `APP_DIR/vendor`
se cargan correctamente.

Para autocargar las clases de PHP ubicadas en nuestro propios módulos (por ejemplo, el
módulo `Application`) tendremos que especificar la llave `autoload` en nuestro archivo
`composer.json`.

{line-numbers=off,lang="json",title="Autoload key of composer.json file"}
~~~
"autoload": {
    "psr-4": {
        "Application\\": "module/Application/src/"
    }
},
~~~

Luego la única cosa que necesitamos hacer es incluir este archivo en nuestro script
de entrada `index.php`:

```php
// Composer autoloading
include __DIR__ . '/../vendor/autoload.php';
```

T> El archivo *autoload.php* se genera cada vez que se instala un paquete usando Composer.
T> Además, podemos ejecutar el comando `dump-autoload` para que Composer genere el
T> archivo *autoload.php*.
T>
T> `php composer.phar dump-autoload`

### PSR-4 y Estructura de la Carpeta Fuente del Módulo

En la aplicación Laminas Skeleton podemos ver como el estándar PSR-4 se aplica en la
práctica. Para el módulo por defecto de nuestro sitio web, el módulo `Application`,
las clases PHP que se registran en el autoloader estándar se guardan en el directorio
`APP_DIR/module/Application/src` («src» es la abreviatura de «source»).

I> Llamaremos a la carpeta `src`: carpeta fuente del módulo.

Por ejemplo, vamos a ver el archivo `IndexController.php` del módulo `Application` (figura 3.2).

![Figura 3.2. Estructura de carpetas de la aplicación Skeleton conforme al estándar PSR-4](../en/images/operation/psr0_and_dir_structure.png)

Como podemos ver este contiene la clase [^controller] `IndexController` que pertenece
al namespace `Application\Controller`. Para seguir el estándar PSR-4 y usar el autoloader
estándar con esta clase PHP tenemos que colocarla dentro de la carpeta `Controller` que está
dentro del directorio fuente del módulo.

[^controller]: La clase `IndexController` es el controlador por defecto para la aplicación
               skeleton. Hablaremos luego sobre los controladores en el capítulo
               [Model-View-Controller](#mvc).

## Peticiones y respuestas HTTP

Cuando un usuario del sitio abre una página en el navegador web el buscador genera
un mensaje de petición y lo envía usando el protocolo HTTP al servidor web. El servidor
web dirige esta petición HTTP a la aplicación web.

I> [HTTP](https://es.wikipedia.org/wiki/Hypertext_Transfer_Protocol) (es la abreviatura
I> para Hyper Text Transfer Protocol en español Protocolo de Transferencia de Hipertexto)
I> es un protocolo para transferir datos en forma de documentos de hyper texto (páginas webs).
I> HTTP se basa en la arquitectura cliente-servidor: el cliente inicia una conexión y envía
I> una petición al servidor web, el servidor que está a la espera de una conexión ejecuta
I> las acciones necesarias y regresa un mensaje de respuesta.

De este modo el objetivo subyacente de cualquier aplicación web es manejar la petición HTTP
y producir una respuesta HTTP que generalmente contiene el código HTML de la página web pedida.
La respuesta es enviada por el servidor web al cliente, el navegador web, y el navegador
muestra la página web en la pantalla.

Abajo se muestra una petición HTTP típica:

{line-numbers=on,lang="text",title="An HTTP request example"}
~~~
GET http://www.w3schools.com/ HTTP/1.1
Host: www.w3schools.com
Connection: keep-alive
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8
User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64)
Accept-Encoding: gzip,deflate,sdch
Accept-Language: en-US;q=0.8,en;q=0.6
Cookie: __gads=ID=42213729da4df8df:T=1368250765:S=ALNI_MaOAFe3U1T9Syh;
(empty line)
(message body goes here)
~~~

La petición HTTP de arriba tiene tres partes:

* La primera línea especifica el método de la petición (GET o POST), la URL
  y la versión del protocolo HTTP.
* Las líneas 2-8 contienen cabeceras opcionales del mensaje con parámetros de transmisión
  y meta información. En el ejemplo de arriba cada línea representa una cabecera con
  la forma *nombre:valor*.
* Un cuerpo («body») opcional contiene los datos del mensaje y está separado de las
  cabeceras por una línea en blanco.

Las cabeceras y el cuerpo del mensaje pueden no existir, pero la línea inicial
siempre está presente en la petición porque esta línea indica el tipo de petición
y la URL.

La respuesta del servidor para el ejemplo anterior es:

{line-numbers=on,lang="text",title="An HTTP response example"}
~~~
HTTP/1.1 200 OK
Cache-Control: private
Content-Type: text/html
Content-Encoding: gzip
Vary: Accept-Encoding
Server: Microsoft-IIS/7.5
Set-Cookie: ASPSESSIONIDQQRBACTR=FOCCINICEFAMEKODNKIBFOJP; path=/
X-Powered-By: ASP.NET
Date: Sun, 04 Aug 2013 13:33:59 GMT
Content-Length: 8434
(empty line)
(page content follows)
~~~

Como podemos ver arriba, la respuesta HTTP tiene casi el mismo formato que la petición.

* La primera línea representa la versión del protocolo HTTP, el código de estado
  y el mensaje (200 OK).

* Las cabeceras opcionales, líneas 2-10, proveen meta información de la respuesta.

* Las cabeceras son seguidas por un cuerpo (body) opcional que está separado de las cabeceras
  por una línea en blanco. El cuerpo generalmente contiene código HTML de la página
  web solicitada.

## El Script de Entrada al Sitio

Cuando el servidor web Apache recibe una petición HTTP, desde el navegador web del cliente,
ejecuta el archivo *APP_DIR/public/index.php*, este script también es llamado *script de
entrada*.

I> El script de entrada es el único archivo PHP accesible desde el mundo exterior.
I> El servidor web Apache dirige todas las peticiones HTTP a este script (recordemos
I> el archivo *.htaccess*). Tener un solo script de entrada hace al sitio web más
I> seguro (en comparación con la situación en la que permitimos a todo el mundo acceder
I> a todos los archivos PHP de nuestra aplicación).

A pesar de que el archivo *index.php* es muy importante es sorprendentemente pequeño:

~~~php
<?php

use Laminas\Mvc\Application;
use Laminas\Stdlib\ArrayUtils;

/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(dirname(__DIR__));

// Decline static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server') {
    $path = realpath(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    if (__FILE__ !== $path && is_file($path)) {
        return false;
    }
    unset($path);
}

// Composer autoloading
include __DIR__ . '/../vendor/autoload.php';

if (! class_exists(Application::class)) {
    throw new RuntimeException(
        "Unable to load application.\n"
        . "- Type `composer install` if you are developing locally.\n"
        . "- Type `vagrant ssh -c 'composer install'` if you are using Vagrant.\n"
        . "- Type `docker-compose run zf composer install` if you are using Docker.\n"
    );
}

// Retrieve configuration
$appConfig = require __DIR__ . '/../config/application.config.php';
if (file_exists(__DIR__ . '/../config/development.config.php')) {
    $appConfig = ArrayUtils::merge($appConfig, require __DIR__ . '/../config/development.config.php');
}

// Run the application!
Application::init($appConfig)->run();
~~~

Fundamentalmente, hay tres cosas que hace el script.

Primero, en la línea 10, la carpeta actual de trabajo se cambia a `APP_DIR`.
Esto simplifica la definición de rutas de archivos relativas en nuestra aplicación.

Luego en la línea 22, la clase autoloading de PHP se ejecuta. Esto permite cargar
fácilmente cualquier clase, tanto si está ubicada en la biblioteca Laminas Framework como
si está en nuestra aplicación, sin necesidad de usar una sentencia `require_once`.

Finalmente, en la línea 40, se crea una instancia de la clase @`Laminas\Mvc\Application`.
La aplicación se inicia con las configuraciones leídas desde el archivo de
configuración *application.config.php* y por último la aplicación se ejecuta.

## Eventos y Ciclo de Vida de la Aplicación

Como hemos aprendido de las secciones anteriores, con cada petición HTTP se crea
un objeto @`Laminas\Mvc\Application`. Normalmente una aplicación «vive» por un segundo o menos
(este tiempo es suficiente para generar la respuesta HTTP). La «vida» de la aplicación
tiene varias etapas.

I> Laminas Framework usa el concepto de *eventos*. Una clase puede *lanzar* un evento,
I> y otro clase puede *escuchar* los eventos. Técnicamente, lanzar un evento significa
I> llamar a otra clase, más exactamente a un método «callback» de la clase. El
I> administrador de eventos está implementado dentro del componente @`Laminas\EventManger`.

Cada etapa de vida de una aplicación se inicia cuando la aplicación lanza un
un evento (este evento está representado par la clase `MvcEvent` que está ubicada en el
namespace `Laminas\Mvc`). Otras clases (tanto las que pertenecen a Laminas Framework como
las de nuestra aplicación) pueden escuchar los eventos y actuar en consecuencia.

Los cinco principales eventos (etapas de vida) se muestran abajo:

**Bootstrap**. Cuando este evento es lanzado por la aplicación un módulo tiene la posibilidad
de registrarse como un oyente (listener) de los próximos eventos de la aplicación
en su método callback `onBootstrap()`.

**Route**. Cuando este evento se lanza el URL de la petición es analizado usando una
clase *router* (normalmente es la clase @`Laminas\Route\Http\TreeRouteStack`). Si existe
una correspondencia exacta entre la URL y una ruta, la petición es pasada a una
*clase-controlador* específica asignada en el router.

**Dispatch**. La clase-controlador atiende o «despacha» la petición usando el
método de acción correspondiente y produce los datos que se muestran en la página web.

**Render**. En este evento las datos producidos por el método de acción en el controlador
se pasan a la clase @`Laminas\View\Renderer\PhpRenderer` para ser representados.
La clase «renderer» usa un archivo de *plantilla de vista* o *view template*
para producir una página HTML.

**Finish**. En este evento la respuesta HTTP se envía al cliente.

El flujo de eventos se muestra en la figura 3.3:

![Figura 3.3. Flujo de eventos durante el ciclo de vida de la aplicación](../en/images/operation/app_life_cycle.png)

T> A pesar de que es relativamente raro, algunos ejemplos prácticos de como hacer escuchar
T> y actuar a un evento se encuentran en el capítulo [Crear un Nuevo módulo](#modules).

## Configuración de la Aplicación

Muchos de los componentes que usa nuestro sitio web necesitan configuración (afinación).
Por ejemplo, en los archivos de configuración definimos las credenciales de conexión a
la base de datos, se especifican los módulos de nuestra aplicación y opcionalmente
se dan algunos parámetros específicos a nuestra aplicación.

Podemos definir los parámetros de configuración en dos niveles: a nivel de aplicación
o a nivel del módulo. En el nivel de aplicación comúnmente definimos parámetros que
controlan a toda la aplicación y que son comunes a todos los módulos de la aplicación.
A nivel de módulo definimos parámetros que solo tienen efecto en el módulo.

I> Algunos frameworks de PHP prefieren el concepto de *convención sobre configuración*,
I> donde muchos de los parámetros están embebidos en el código y no necesitan configuración.
I> Esto hace más rápido el desarrollo de la aplicación, pero la hace menos configurable.
I> En Laminas Framework se usa el concepto de *configuración sobre convención*
I> así podemos personalizar cualquier aspecto de nuestra aplicación, para esto tenemos
I> que invertir algún tiempo en aprender a como hacerlo.

### Archivos de Configuración a Nivel de Aplicación

La subcarpeta *APP_DIR/config* contiene los archivos de configuración de toda la
aplicación. Miremos en detalle esta subcarpeta (figura 3.4).

![Figura 3.4. Archivos de configuración](../en/images/operation/config.png)

El archivo *APP_DIR/config/application.config.php* es el archivo de configuración
principal. Es usado por la aplicación al arrancar para determinar que módulos de la
aplicación deben ser cargados y cuales servicios crear por defecto.

Abajo presentamos el contenido del archivo *application.config.php*. Como podemos
ver el archivo de configuración es solo un arreglo asociado y anidado de PHP, cada
componente tendrá una llave específica en el arreglo. Podemos dejar comentarios
de una línea en las llaves para hacer más fácil que otros entiendan lo que cada
llave significa.

T> Por convención los nombres de las llaves deben estar en minúsculas y si el nombre
T> de la llave tiene varias palabras, las palabras deben ir separadas por un símbolo
T> piso ('_').

{line-numbers=on,lang=php, title="Contenido del archivo application.config.php"}
~~~
return [
    // Retrieve list of modules used in this application.
    'modules' => require __DIR__ . '/modules.config.php',

    // These are various options for the listeners attached to the ModuleManager
    'module_listener_options' => [
        // This should be an array of paths in which modules reside.
        // If a string key is provided, the listener will consider that a module
        // namespace, the value of that key the specific path to that module's
        // Module class.
        'module_paths' => [
            './module',
            './vendor',
        ],

        // An array of paths from which to glob configuration files after
        // modules are loaded. These effectively override configuration
        // provided by modules themselves. Paths may use GLOB_BRACE notation.
        'config_glob_paths' => [
            realpath(__DIR__) . '/autoload/{{,*.}global,{,*.}local}.php',
        ],

        // Whether or not to enable a configuration cache.
        // If enabled, the merged configuration will be cached and used in
        // subsequent requests.
        'config_cache_enabled' => true,

        // The key used to create the configuration cache file name.
        'config_cache_key' => 'application.config.cache',

        // Whether or not to enable a module class map cache.
        // If enabled, creates a module class map cache which will be used
        // by in future requests, to reduce the autoloading process.
        'module_map_cache_enabled' => true,

        // The key used to create the class map cache file name.
        'module_map_cache_key' => 'application.module.cache',

        // The path in which to cache merged configuration.
        'cache_dir' => 'data/cache/',

        // Whether or not to enable modules dependency checking.
        // Enabled by default, prevents usage of modules that depend on other modules
        // that weren't loaded.
        // 'check_dependencies' => true,
    ],

    // Used to create an own service manager. May contain one or more child arrays.
    //'service_listener_options' => [
    //     [
    //         'service_manager' => $stringServiceManagerName,
    //         'config_key'      => $stringConfigKey,
    //         'interface'       => $stringOptionalInterface,
    //         'method'          => $stringRequiredMethodName,
    //     ],
    // ],

   // Initial configuration with which to seed the ServiceManager.
   // Should be compatible with Laminas\ServiceManager\Config.
   // 'service_manager' => [],
];
~~~

En la línea 3 tenemos la llave *modules* que define que módulos serán cargados al arrancar.
Podemos ver que los nombres de los módulos se guardan dentro de otro archivo de configuración
`modules.config.php` en el que se listan todos los módulos presentes en nuestro
sitio web.

En la línea 11 está la llave `module_paths` que le indica a Laminas en que carpetas
buscar el código fuente que pertenece a los módulos. Los módulos de aplicación
que desarrollamos se ubican dentro del directorio *APP_DIR/module* y los módulos
de terceros se ubican dentro del directorio *APP_DIR/vendor*.

En la línea 19 tenemos la llave `config_glob_paths` que le dice a Laminas en donde buscar
archivos de configuración extra. Aquí se ve que los archivos con el sufijo *global.php*
o *local.php* del *APP_DIR/config/autoload* se cargan automáticamente.

En resumen, comúnmente usamos el archivo de configuración principal *application.config.php*
para guardar información sobre los módulos que deben ser cargados en nuestra aplicación,
donde están ubicados y como se cargan (por ejemplo, aquí podemos controlar las opciones de caché).
Además, en este archivo podemos afinar la configuración del administrador de servicios.
No es recomendable agregar más llaves a este archivo. Para este propósito es mejor
usar el archivo `autoload/global.php`.

Finalmente vamos a ver el contenido del archivo `modules.config.php`. Actualmente,
tenemos los siguientes módulos instalados en nuestro sitio web.

{line-numbers=off,lang=php, title="Content of modules.config.php file"}
~~~
return [
    'Laminas\Session',
    'Laminas\Mvc\Plugin\Prg',
    'Laminas\Mvc\Plugin\Identity',
    'Laminas\Mvc\Plugin\FlashMessenger',
    'Laminas\Mvc\Plugin\FilePrg',
    'Laminas\Form',
    'Laminas\Router',
    'Laminas\Validator',
    'Application',
];
~~~

El módulo `Application` es el módulo que contiene los archivos de nuestra aplicación.
Todos los otros módulos listados son componentes de Laminas Framework.

I> En Laminas se introdujo un complemento especial de Composer llamado *instalador de componentes*.
I> Si recordamos, en el capítulo [Laminas Skeleton Application](#skeleton) respondimos varias preguntas
I> con «yes» o «no» sobre la instalación, con lo que determinamos que componentes instalar.
I> El instalador *inyecta* los nombres de los módulos de componentes en el archivo
I> `modules.config.php`.

### Archivos de Configuración Extra a Nivel de la Aplicación

Los archivos de configuración «extra» *APP_DIR/config/autoload/global.php* y *APP_DIR/config/autolocal.php*
definen, respectivamente, parámetros para toda la aplicación independientes del
entorno y parámetros dependientes del
entorno. Estos archivos de configuración se cargan automáticamente
y se mezclan recursivamente con los archivos de configuración del módulo,
esta es la razón por la que este directorio se llame *autoload*.

Tener diferentes archivos de configuración en el directorio *APP_DIR/config/autoload*
puede ser confuso, podríamos no saber que parámetros se deben colocar en cada uno.
Aquí hay algunos consejos:

* Usamos el archivo *autoload/global.php* para guardar parámetros que no dependen
  de un entorno de maquina concreto. Por ejemplo, aquí podemos guardar parámetros
  que sobrescriben los parámetros por defecto de algún módulo. Aquí no guardamos
  información sensible (como credenciales de base de datos), para ese propósito
  es mejor usar el archivo *autoload/local.php*.

* Usamos el archivo *autoload/local.php* para guardar parámetros específicos de
  un entorno concreto. Por ejemplo, aquí podemos guardar las credenciales de la
  base de datos. Usualmente, cada desarrollador tiene una base de datos local cuando
  desarrolla y prueba el sitio web. Entonces, el desarrollador editará el archivo
  *local.php* y colocará sus propias credenciales. Cuando instalamos nuestro sitio
  en el servidor de producción, editaremos el archivo `local.php` y colocaremos
  las credenciales para la base de datos de producción.

I> Como el archivo *autoload/local.php* contiene parámetros específicos para un
I> entorno no lo guardamos en el sistema de control de versiones.
I> En el sistema de control de versiones almacenaremos la *plantilla de distribución*
I> *local.php.dist*. Cada desarrollador del equipo puede renombrar el archivo
I> *local.php.dist*
I> a *local.php* y colocar sus propios parámetros. El archivo *local.php* no debe
I> ser guardado en el control de versiones porque puede contener información
I> sensible como credenciales de base de datos (usuario y contraseña) y no deseamos
I> que otras personas lo vean.

### Archivo de Configuración del Desarrollador a Nivel de Aplicación

El archivo de configuración del desarrollador a nivel de aplicación
(`APP_DIR/config/development.config.php`)
está presente solo cuando activamos el *modo de desarrollo*. Si recordamos, nosotros
habilitamos el modo de desarrollo al principio en el capítulo [Laminas Skeleton Application](#skeleton).

I> Activamos el modo de desarrollo con el siguiente comando:
I>
I> `php composer.phar development-anable`

El archivo `development.config.php` se mezcla con el archivo principal `application.config.php`.
Esto permite sobrescribir algunos parámetros. Por ejemplo, podemos:

  * Desactivar la configuración de caché. Cuando desarrollamos el sitio web frecuentemente
    modificamos los archivos de configuración por lo que la configuración de caché
    puede tener consecuencias indeseables como impedir que veamos el resultado de
    nuestros cambios inmediatamente.
  * Cargar módulos adicionales. Por ejemplo, podemos cargar el módulo
    [LaminasDeveloperTools](https://github.com/laminas/LaminasDeveloperTools)
    solo en el entorno de desarrollo.

Si desactivamos el módulo de desarrollo, el archivo `development.config.php` se
removerá. Así que no deberíamos guardar este archivo en el control de versiones.
En su lugar, guardamos en el control de versiones la versión de «distribución»
`development.config.php.dist`.

### Archivos de Configuración Extra de Desarrollo a Nivel de Aplicación

El archivo de configuración extra de desarrollo a nivel de aplicación
(`APP_DIR/config/autoload/development.local.php`) está presente solo cuando
activamos el *modo de desarrollo*.

El archivo `development.local.php` se mezcla con otros archivos de configuración a
nivel de módulo. Esto permite sobrescribir algunos parámetros específicos usados
solamente en el entorno de desarrollo.

Si desactivamos el modo de desarrollo el archivo `development.local.php` será removido.
Por esta razón no deberíamos guardar este archivo en el control de versiones. En su lugar,
guardaremos la versión de *distribución* `development.local.php.dist` en el control
versiones.

### Archivos de Configuración a Nivel de Módulo

En la figura 3.4 podemos ver que el módulo *Application* que viene con nuestra aplicación
tiene el archivo *module.config.php* en el que colocamos los parámetros específicos del
módulo. Vamos a ver como es el archivo `module.config.php` del módulo `Application`:

{line-numbers=off,lang=php, title="module.config.php file"}
~~~
<?php
namespace Application;

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    'router' => [
        'routes' => [
            'home' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'application' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/application[/:action]',
                    'defaults' => [
                        'controller'    => Controller\IndexController::class,
                        'action'        => 'index',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => InvokableFactory::class,
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
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

En este archivo registramos los controladores del módulo, colocamos información sobre
las reglas de direccionamiento para las URL que se asocian con nuestros controladores, registramos
los complementos de controlador y también registramos las plantillas de vista y los
ayudantes de vista (aprenderemos más sobre estos términos en este capítulo y en los siguientes
capítulos).

### Combinando los Archivos de Configuración

Cuando una aplicación se crea, los archivos de configuración provistos por el módulo
y los archivos de configuración extra del directorio *APP_DIR/config/autoload* se
mezclan dentro de un gran arreglo anidado y de esta manera cada parámetro de configuración
comienza a estar disponible en cualquier parte del sitio web. Por esta razón podemos
sobrescribir desde el módulo algunos parámetros específicos.

I> Es posible ver en otros lugares la práctica de «combinación» de archivos de configuración,
I> por ejemplo, cuando instalamos PHP, existe el archivo principal *php.ini* y otros archivos
I> de configuración que se incluyen dentro de uno principal. Cada separación
I> hace que la configuración de la aplicación sea granular y flexible, porque no
I> tenemos que colocar todos los parámetros en un solo archivo y editarlo cada vez
I> que necesitemos cambiar algo.

Los archivos de configuración son cargados en el siguiente orden:

* El archivo *application.config.php* se carga primero. Se usa para inicializar
  el administrador de servicios y cargar los módulos de la aplicación. Los datos
  cargados desde este archivo de configuración se almacenan solos y no se mezclan
  con otros archivos de configuración.

* Los archivos de configuración para cada módulo de la aplicación se cargan y se
  mezclan. Los módulos son cargados en el mismo orden en que son listados en el archivo
  *application.config.php*. Si dos módulos, intencionalmente o por error, guardan
  parámetros en llaves con nombres iguales estos parámetros serán sobrescritos.

* Los archivos de configuración extra de la carpeta *APP_DIR/config/autoload* se
  cargan y mezclan dentro de un solo arreglo. Luego, este arreglo se mezcla con
  el arreglo de configuración del módulo producido en el paso anterior, cuando
  se carga la configuración del módulo. La configuración general de la aplicación
  tiene una prioridad más alta que la configuración del módulo, así que podemos
  sobrescribir los llaves del módulo si lo deseamos.

## Punto de Acceso al Módulo

En Laminas nuestra aplicación esta constituida por módulos. Por defecto, tenemos un
solo módulo, `Application`, pero podemos crear más si lo necesitamos. Normalmente, nuestros
propios módulos se guardan en el directorio *APP_DIR/modulo*, mientras los módulos
de terceros se guardan en el directorio *APP_DIR/vendor*.

Al iniciar, cuando se crea el objeto @`Laminas\Mvc\Application`, se usa el componente
@`Laminas\ModuleManager` para encontrar y cargar todos los módulos registrados en la
configuración de la aplicación.

Cada módulo de la aplicación web tiene el archivo *Module.php* que es un tipo de
*punto de entrada* para el módulo. Este archivo provee la clase `Module`. Abajo se
presenta el contenido de la clase `Module` de la aplicación *skeleton*.

{line-numbers=off, lang=php, title="Contenido del archivo Module.php"}
~~~
<?php
namespace Application;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
}
~~~

La clase `Module` pertenece al namespace del módulo (el módulo principal pertenece
al namespace `Application`).

El método `getConfig()` se usa normalmente para proveerle a Laminas Framework la
configuración del módulo (archivo *module.config.php*).

I> Aquí también podemos registrar algunos *event listeners* (escuchas de eventos),
I> veremos como hacer
I> esto luego en el capítulo [Crear un Nuevo módulo](#modules).

## Administrador de Servicios

Podemos imaginar a la aplicación web como un conjunto de *servicios*. Por ejemplo,
podemos tener un servicio de autenticación responsable del inicio de sesión para
usuarios, un servicio administrador de entidades responsable del acceso
a la base de datos, un servicio para la administración de eventos responsable de
lanzar eventos y entregarlos al *listeners* de eventos, etc.

En Laminas Framework la clase @`ServiceManager` es un *contenedor* centralizado para
todos los servicios de aplicación. El administrador de servicios se implementa en
el componente @`Laminas\ServiceManager`, con la clase @`ServiceManager`. El diagrama
de herencia de la clase se muestra en la figura 3.5:

![Figura 3.5. Diagrama de herencia de la clase administradora de servicios](../en/images/operation/service_manager_inheritance.png)

El administrador de servicios es creado luego de arrancar la aplicación (dentro
del método estático `init()` de la clase @`Laminas\Mvc\Application`). Los servicios
estándares disponibles a través del administrador de servicios se presentan en la
tabla 3.1. Esta tabla está incompleta, porque el número actual de servicios registrados
en el administrador de servicios puede ser muy grande.

{title="Tabla 3.1. Servicios Estándares"}
|----------------------|-----------------------------------------------------------------------|
| Nombre del Servicio  | Descripción                                                           |
|----------------------|-----------------------------------------------------------------------|
| `Application`        | Permite recuperar la clase singleton @`Laminas\Mvc\Application`.         |
|----------------------|-----------------------------------------------------------------------|
| `ApplicationConfig`  | Arreglo de configuración extraído desde el archivo *application.config.php*. |
|----------------------|-----------------------------------------------------------------------|
| `Config`             | Arreglo de configuración mezclado, extraído del archivo *module.config.php* |
|                      | junto con *autoload/global.php* y el *autoload/local.php*.            |
|----------------------|-----------------------------------------------------------------------|
| `EventManager`       | Permite recuperar una *nueva* instancia de la clase                   |
|                      | @`Laminas\EventManager\EventManager`. El administrador de eventos permite|
|                      | enviar (lanzar) eventos y asociar *listeners* de evento.              |
|----------------------|-----------------------------------------------------------------------|
| `SharedEventManager` | Permite recuperar una instancia *singleton* de la clase @`Laminas\EventManager\SharedEventManager`. |
|                      | El administrador de eventos compartidos permite escuchar eventos definidos |
|                      | por otras clases y componentes.                                       |
|----------------------|-----------------------------------------------------------------------|
| `ModuleManager`      | Permite recuperar una clase *singleton* de @`Laminas\ModuleManager\ModuleManager`. |
|                      | El administrador de módulos es responsable de cargar los módulos de la aplicación. |
|----------------------|-----------------------------------------------------------------------|
| `Request`            | La clase *singleton* de @`Laminas\Http\Request`. Representa una petición HTTP |
|                      | recibida desde el client.                                             |
|----------------------|-----------------------------------------------------------------------|
| `Response`           | La clase *singleton* de @`Laminas\Http\Response`. Representa la respuesta HTTP |
|                      | que será enviada al cliente.                                          |
|----------------------|-----------------------------------------------------------------------|
| `Router`             | La clase *singleton* de @`Laminas\Router\Http\TreeRouteStack`. Ejecuta el|
|                      | direccionamiento URL.                                                 |
|----------------------|-----------------------------------------------------------------------|
| `ServiceManager`     | El administrador de servicios.                                        |
|----------------------|-----------------------------------------------------------------------|
| `ViewManager`        | La clase *singleton* de @`Laminas\Mvc\View\Http\ViewManager`. Responsable|
|                      | de preparar la capa de vista para pintar la página.                   |
|----------------------|-----------------------------------------------------------------------|

Un servicio es típicamente una clase PHP arbitraria, pero no siempre. Por ejemplo,
cuando Laminas carga los archivos de configuración y mezcla los datos dentro de un arreglo
anidado, se guarda el arreglo en dos servicios del administrador de servicios: `ApplicationConfig` y
`Config`. El primero es una arreglo cargado desde el nivel de aplicación con el
archivo *application.config.php* y el segundo servicio es la mezcla de los archivos de configuración
a nivel de módulo y los archivos de configuración autocargados del nivel de aplicación.
Luego, en el administrador de eventos podemos guardar cualquier cosa que queramos:
una clase PHP, una simple variable o un arreglo.

En la tabla 3.1 podemos ver que en Laminas casi todo se puede considerar un servicio.
El administrador de servicios mismo se registra como un servicio. Además, la clase
@`Application` se registra también como un servicio.

I> Una cosa importante que deberías saber sobre los servicios es que ellos se guardan
I> *normalmente* en una única instancia (este es un patrón llamado *singleton*).
T> Obviamente, no necesitamos otra instancia de la clase @`Application` (o podríamos
T> tener una pesadilla).

T> Pero existe una importante excepción para la regla de arriba. Aunque puede ser confuso
T> al principio el @`EventManager` no es un singleton. Cada vez que recuperamos el
T> administrador de eventos desde el administrador de servicios recibimos un *nuevo*
T> objeto. Esto se hace por razones de rendimiento y para evitar conflictos entre
T> eventos de distintos componentes. Discutiremos esto luego en la sección
T> *Sobre los Administradores de Eventos* de este capítulo.

El administrador de servicios define varios métodos necesarios para localizar y
recuperar un servicio desde el administrador de servicios (ver la tabla 3.2).

{title="Tabla 3.2. Métodos del ServiceManager"}
|----------------------|-----------------------------------------------------------------------|
| Nombre del Método    | Descripción                                                           |
|----------------------|-----------------------------------------------------------------------|
| `has($name)`         | Revisa si el servicio está registrado.                                |
|----------------------|-----------------------------------------------------------------------|
| `get($name)`         | Recupera una instancia del servicio registrado.                       |
|----------------------|-----------------------------------------------------------------------|
| `build($name, $options)` | Siempre regresa una nueva instancia del servicio solicitado.      |
|----------------------|-----------------------------------------------------------------------|

Podemos probar si un servicio está registrado pasando su nombre al método `has()`
del administrador de servicios. Si regresa el valor booleano `true` el servicio
está registrado, si regresa `false` el servicio con el nombre dado no está registrado.

Luego podemos recuperar un servicio a partir de su nombre con la ayuda del método
`get()` del administrador de servicios. Este método toma un único parámetro que
representa el nombre del servicio. Veamos el siguiente ejemplo:

~~~php
<?php

// Retrieve the application config array.
$appConfig = $serviceManager->get('ApplicationConfig');

// Use it (for example, retrieve the module list).
$modules = $appConfig['modules'];
~~~

Cuando se llama al método `build()` siempre se crea una nueva instancia del servicio (comparado
con `get()` que normalmente crea una instancia del servicio una sola vez y la regresa
luego en cada nueva petición).

T> Normalmente no recuperamos servicios con el administrador de servicios desde
T> cualquier lugar de nuestro código sino dentro de una *fábrica*. Una fábrica
T> es un código responsable de crear un objeto. Cuando se crea el objeto podemos
T> traer desde administrador de servicios a los servicios de que depende el objeto
T> que estamos creando y pasar estos
T> servicios (dependencias) al constructor del objeto. A esto se le llama
T> *inyección de dependencias*.

I> Si tenemos alguna experiencia con Laminas Framework 2 podemos ver que las cosas ahora
I> son un poco diferentes. En ZF2, había un patrón `ServiceLocator` que
I> permitía traer dependencias desde administrador de servicios en *cualquier*
I> parte de la aplicación (controladores, servicios, etc). En Laminas, tenemos que pasar
I> las dependencias explícitamente. Esto es un poco aburrido pero remueve las dependencias
I> «ocultas» y hace que nuestro código sea más fácil y claro de entender.

### Registrar un Servicio

Cuando escribimos un sitio web a menudo necesitamos registrar nuestro propio servicio
en el administrador de servicios. Una de las maneras de registrar un servicio es
usando el método `setService()` del administrador de servicios. Por ejemplo, vamos
a crear y registrar la clase de servicio que convierte monedas y que se usa, por ejemplo,
en una página con carrito de compras para convertir EUR a USD:

~~~php
<?php
// Define a namespace where our custom service lives.
namespace Application\Service;

// Define a currency converter service class.
class CurrencyConverter
{
    // Converts euros to US dollars.
    public function convertEURtoUSD($amount)
    {
        return $amount*1.25;
    }

    //...
}
~~~

Arriba entre las líneas 6 y 15 definimos una clase `CurrencyConverter` de ejemplo
(por simplicidad, implementamos solo un método `convertEURtoUSD()` que es capaz
de convertir euros a dolares norte americanos).

~~~php
// Create an instance of the class.
$service = new CurrencyConverter();
// Save the instance to service manager.
$serviceManager->setService(CurrencyConverter::class, $service);
~~~

En este ejemplo se crea una instancia de la clase con el operador `new` y la registramos en el
administrador de servicios usando el método `setService()` (asumimos que la variable
`$serviceManager` es de tipo *class* correspondiente a @`Laminas\ServiceManager\ServiceManager`
y que ha sido declarada en algún lugar).

El método `setService()` toma dos parámetros: una cadena de caracteres que es el
nombre del servicio y la instancia del servicio. El nombre del servicio debe ser
único entre todos los otros servicios posibles.

Una vez que el servicio se almacena en el administrador de servicios podemos recuperarlo
por su nombre en cualquier lugar de la aplicación con la ayuda del método `get()`
del administrador de servicios. Miremos el siguiente ejemplo:

~~~php
<?php
// Retrieve the currency converter service.
$service = $serviceManager->get(CurrencyConverter::class);

// Use it (convert money amount).
$convertedAmount = $service->convertEURtoUSD(50);
~~~

### Nombres de Servicio

Diferentes servicios pueden usar diferentes convenciones de nomenclatura. Por ejemplo,
el mismo servicio que convierte monedas puede ser registrado con diferentes nombres:
`CurrencyConverter`, `currency_converter`, etc. Podemos introducir una convención
para hacer uniforme los nombres, recomendamos registrar un servicio por su nombre
de clase completo, *fully qualified name*, de la siguiente manera:

~~~php
$serviceManager->setService(CurrencyConverter::class);
~~~

En el ejemplo de arriba usamos la palabra clave `class`. Esta se encuentra disponible desde
PHP 5.5 y se usa para la resolución de nombres de clase. `CurrencyConverter::class`
se expande al nombre completo de la clase, es decir `\Application\Service\CurrencyConverter`.

### Sobrescribir un Servicio Existente

Si estamos intentando registrar un nombre de servicio que ya está registrado el
método `setService()` lanzará una excepción. Sin embargo, en ocasiones queremos sobrescribir
el servicio con el mismo nombre (reemplazarlo por uno nuevo). Con este propósito,
podemos usar el método del administrador de servicios `setAllowOverride()`.

{line-numbers=of,lang=php}
~~~
<?php
// Allow to replace services
$serviceManager->setAllowOverride(true);

// Save the instance to service manager. There will be no exception
// even if there is another service with such a name.
$serviceManager->setService(CurrencyConverter::class, $service);
~~~

El método `setAllowOverride()` toma un único parámetro booleano que define si se permite
reemplazar el servicio `CurrencyConverter` tanto si ya está registrado como si no.

### Registrar Clases Invocables

Hay algo que está mal con el método `setService()`, si se usa tenemos que crear la instancia
del servicio antes de que realmente lo necesitemos. Si nunca usamos el servicio la
instanciación del servicio solo derrochará tiempo y memoria. Para resolver esta
cuestión el administrador de servicios nos provee del método `setInvokableClass()`.

~~~php
<?php
// Register an invokable class
$serviceManager->setInvokableClass(CurrencyConverter::class);
~~~

En el ejemplo de arriba pasamos al administrador de servicios el nombre completo
de la clase (fully qualified) que implementa el servicio en lugar de pasar su
instancia. Con esta técnica, el
servicio será instanciado por el administrador de servicios solo cuando se
llama al método `get(CurrencyConverter::class)`. A esta técnica también se le
llama *lazy loading*.

T> Los servicios a menudo dependen de otro. Por ejemplo, el servicio que convierte
T> monedas puede usar el servicio de administración de entidades para leer la tasa de cambio
T> desde la base de datos. La desventaja del método `setInvokableClass()` es que
T> no permite pasar parámetros (dependencias) al servicio en la instanciación del
T> objeto. Para resolver esta cuestión podemos usar *fábricas* (factories) como
T> se describe más adelante.

### Registrar una Fábrica

Una fábrica es una clase que solo puede hacer una cosa, crear otros objetos.

Registramos una fábrica para un servicio con el método `setFactory()` del administrador
de servicios.

La fábrica más simple es @`InvokableFactory`, esta es análoga al método `setInvokableClass()`
de la sección anterior.

~~~php
<?php
use Laminas\ServiceManager\Factory\InvokableFactory;

// This is equivalent to the setInvokableClass() method from previous section.
$serviceManager->setFactory(CurrencyConverter::class, InvokableFactory::class);
~~~

Después de haber registrado la fábrica podemos recuperar el servicio desde el administrador
de servicios como es usual usando el método `get()`. El servicio se instanciará
solo cuando lo recuperemos desde el administrador de servicios (lazy loading).

En ocasiones la instanciación de un servicio es más compleja que solo crear la instancia
del servicio con el operador `new` (como lo hace la clase @`InvokableFactory`). Podemos
necesitar pasar algunos parámetros al constructor del servicio o invocar algunos
métodos del servicio justo después de la construcción. Esta lógica de instanciación
compleja se puede encapsular dentro de nuestra propia clase *fábrica* escrita a la
medida. La clase fábrica normalmente implementa a la interfaz
@`FactoryInterface`[Laminas\ServiceManager\Factory\FactoryInterface]:

~~~php
<?php
namespace Laminas\ServiceManager\Factory;

use Interop\Container\ContainerInterface;

interface FactoryInterface
{
    public function __invoke(ContainerInterface $container,
                        $requestedName, array $options = null);
}
~~~

Como podemos ver a partir de la definición de la interfaz
@`FactoryInterface`[Laminas\ServiceManager\Factory\FactoryInterface],
la clase fábrica debe
proveer el método mágico `__invoke` que retorna una instancia de un solo servicio.
El administrador de servicios se pasa al método `__invoke` en el parámetro `$container`;
este se puede usar durante la construcción del servicio para acceder a otros servicios
(inyectar *dependencias*). El segundo parámetro (`$requestedName`) es el nombre del
servicio. El tercer argumento (`$options`) se puede usar para pasar algunos parámetros
al servicio y se usa solo cuando pedimos el servicio con el método del administrador
de servicios `build()`.

Como un ejemplo, vamos a escribir una fábrica para nuestro servicio que convierte
monedas (ver el código de abajo). No usamos una lógica de construcción compleja para
nuestro servicio `CurrencyConverter` pero para servicios más complejos necesitaremos
hacerlo.

~~~php
<?php
namespace Application\Service\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Application\Service\CurrencyConverter;

// Factory class
class CurrencyConverterFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container,
                     $requestedName, array $options = null)
    {
        // Create an instance of the class.
        $service = new CurrencyConverter();

        return $service;
    }
}
~~~

I> Técnicamente, con Laminas *podemos* usar la misma clase fábrica para instanciar varios
I> servicios que tienen código de instanciación similar (para este propósito podemos
I> usar el argumento `$requestedName` que se pasa al método fábrica `__invoke()`).
I>  Sin embargo, *principalmente* crearemos diferentes fábrica una por cada servicio.

### Registrar un Fábrica Abstracta

Un escenario más complejo para una fábrica es cuando necesitamos determinar en tiempo de ejecución
cuales nombres de servicios deberían ser registrados. En esta situación podemos
usar una *fábrica abstracta*. Una fábrica abstracta deberá implementar la interfaz
@`AbstractFactoryInterface`[Laminas\ServiceManager\Factory\AbstractFactoryInterface]:

~~~php
<?php
namespace Laminas\ServiceManager\Factory;

use Interop\Container\ContainerInterface;

interface AbstractFactoryInterface extends FactoryInterface
{
    public function canCreate(ContainerInterface $container, $requestedName);
}
~~~

Una fábrica abstracta tiene dos métodos: `canCreate()` e `__invoke()`. La primera
es necesaria para revisar si la fábrica puede crear el servicio con determinado nombre y
el segundo permite crear el servicio. Los métodos toman dos parámetros: el administrador
de servicios (`$container`) y el nombre del servicio (`$requestedName`).

En comparación con la clase fábrica normal, la diferencia está en que la clase fábrica
normal *generalmente* crea solo un tipo de servicio pero la clase abstracta puede
crear dinámicamente tantos tipos de servicios como se quiera.

Registramos una fábrica abstracta con el método del administrador de servicios
`setAbstractFactory()`.

T> Las fábricas abstractas son una poderosa característica pero solo deberíamos usarlas
T> cuando realmente es necesario, porque ellas impactan negativamente en el rendimiento.
T> Es mejor usar las fábricas usuales (no abstractas).

### Registrar Alias de Servicios

A veces podemos querer definir un *alias* para el servicio. Los alias son como enlaces
simbólicos: estos hacen referencia a servicios que ya están registrados. Para crear
un alias usamos el método `setAlias()` del administrador de servicios:

~~~php
<?php
// Register an alias for the CurrencyConverter service
$serviceManager->setAlias('CurConv', CurrencyConverter::class);
~~~

Una vez registrado podemos recuperar el servicio usando el método `get()` del administrador
de servicios tanto con el nombre como con el alias.

### Servicios Compartidos y no Compartidos

Por defecto los servicios se guardan en una sola instancia en el administrador de
servicios. A esto también se le llama patrón de diseño *singleton*. Por ejemplo,
cuando intentamos recuperar dos veces el servicio `CurrencyConverter` recibiremos
el mismo objeto. A esto lo llamamos un servicio *compartido*.

Pero en algunas (raras) ocasiones necesitamos crear una *nueva* instancia de un
servicio cada vez que alguien lo pida al administrador de servicios. Un ejemplo de
esto es el @`EventManager`, tendremos una nueva instancia de él cada vez que lo pidamos.

Para marcar un servicio como no compartido podemos usar el método del administrador
de servicios `setShared()`:

~~~php
$serviceManager->setShared('EventManager', false);
~~~

### Configuración del Administrador de Servicios

En nuestro sitio web normalmente usamos la configuración del administrador de servicios
para registrar nuestros servicios (en lugar de llamar a los métodos del administrador
de servicios como describimos arriba).

Para registrar automáticamente un servicio dentro del administrador de servicios,
normalmente usamos la llave `service_manager` en el archivo de configuración. Podemos
colocar esta llave dentro de un archivo de configuración a nivel de aplicación
o en un archivo de configuración a nivel de módulo.

W> Si colocamos esta llave en el archivo de configuración a nivel de módulo debemos
W> ser cuidadosos de no sobrescribir el nombre durante la mezcla de la configuración.
W> No debemos registrar el mismo nombre de servicio en diferentes módulos.

La llave `service_manager` debería verse así:

~~~php
<?php
return [
    //...

    // Register the services under this key
    'service_manager' => [
        'services' => [
            // Register service class instances here
            //...
        ],
        'invokables' => [
            // Register invokable classes here
            //...
        ],
        'factories' => [
            // Register factories here
            //...
        ],
        'abstract_factories' => [
            // Register abstract factories here
            //...
        ],
        'aliases' => [
            // Register service aliases here
            //...
        ],
        'shared' => [
            // Specify here which services must be non-shared
        ]
  ],

  //...
];
~~~

En el ejemplo de arriba podemos ver que la llave `service_manager` puede contener
varias subllaves para registrar servicios de diferentes maneras:

* la subllave `services` (línea 7) permite registrar instancias de clases.
* la subllave `invokables` (línea 11) permite registrar un nombre completo de clase;
  el servicio será instanciado usando *lazy loading*.
* la subllave `factories` (línea 15) permite registrar una fábrica, que es capaz
  de crear instancias de un solo servicio.
* la subllave `abstract_factories` (línea 9) se usa para registrar fábricas abstractas,
  que son capaces de registrar varios servicios por nombre.
* la subllave `aliases` (línea 23) provee la capacidad de registrar un alias para un
  servicio.
* la subllave `shared` (línea 27) permite especificar cuales servicios no deben ser
  compartidos.

A manera de ejemplo vamos a registrar nuestro servicio `CurrencyConverter` y crearemos
un alias para él:

~~~php
<?php
use Laminas\ServiceManager\Factory\InvokableFactory;
use Application\Service\CurrencyConverter;

return [
    //...

    // Register the services under this key
    'service_manager' => [
        'factories' => [
            // Register CurrencyConverter service.
            CurrencyConverter::class => InvokableFactory::class
        ],
        'aliases' => [
            // Register an alias for the CurrencyConverter service.
            'CurConv' => CurrencyConverter::class
        ],
  ],

  //...
];
~~~

## Administrador de Complementos

Ahora que entendemos lo que es un administrador de servicios no debería ser difícil
aprender el concepto de *administrador de complementos*. Un *administrador de complementos*
es casi lo mismo que un administrador de servicios con la excepción de que solo puede
instanciar servicios de un único tipo. Con este tipo de complemento un administrador
de complementos se puede instanciar incrustado en el código dentro de la clase
administradora de complementos.

¿Por qué necesitamos tal cosa? De hecho, en Laminas, el administrador de complementos
se usa ampliamente porque ellos permiten instanciar un complemento solo cuando se
necesita (esto reduce el uso de CPU y memoria). Existen administradores de complementos
separados para:

  * controladores (la clase @`Laminas\Mvc\Controller\ControllerManager`)
  * complementos controladores (la clase @`Laminas\Mvc\Controller\PluginManager`)
  * ayudante de vista (la clase @`Laminas\View\HelperPluginManager`)
  * elementos del formulario (la clase @`Laminas\Form\FormElementManager\FormElementManagerV3Polyfill`)
  * filtros (la clase @`Laminas\Filter\FilterPluginManager`)
  * validadores (la clase @`Laminas\Validator\ValidatorPluginManager`)
  * y probablemente otras cosas.

El hecho de que cada administrador de complementos herede desde la clase base
@`Laminas\ServiceManager\ServiceManager` permite que los administradores de complementos
tengan una configuración similar. Por ejemplo, los controladores se registran dentro de la
llave `controllers` en el archivo *module.config.php* y esta llave puede tener
las mismas subllaves: *services*, *invokables*, *factories*, *abstract_factories*
y *aliases*. La llave *view_helpers* que se usa para registrar los ayudantes de vistas
tiene la misma estructura, la llave *controller_plugins* se usa para registrar
complementos controladores, etc.

## Sobre el Administrador de Eventos

T> En esta sección daremos información avanzada sobre el administrador de eventos.
T> Podemos saltar esta sección con relativa seguridad, sin embargo será necesario
T> leerla si planeamos implementar algunos oyentes de eventos (event listeners)
T> avanzados en nuestro sitio web.

Anteriormente en este capítulo hemos mencionado que el ciclo de vida de la aplicación
consiste en *eventos*. Una clase puede *lazar* un evento y otra clase puede *escuchar*
los eventos. Técnicamente, lanzar un evento significa con exactitud llamar a otro
método *callback* de una clase. El administrador de eventos se implementa dentro del
componente @`Laminas\EventManager`.

T> Laminas (y particularmente su componente @`Laminas\Mvc`) depende de eventos para
T> funcionar, como consecuencia su código es una combinación de oyentes de
T> eventos que son algo difíciles de entender. Por fortuna, en la mayoría de los
T> casos no necesitamos entender como Laminas lanza y maneja eventos internamente, solo
T> necesitamos entender lo que es un evento, que eventos están presentes en una
T> aplicación y cual es la diferencia entre un *administrador de eventos* usual
T> y un *administrador de eventos compartidos*.

### Event y MvcEvent

Un evento es técnicamente una instancia de la clase @`Laminas\EventManager\Event`. Un
evento puede en principio tener al menos las siguientes partes:

* *name* - identifica unívocamente al evento;
* *target* - es típicamente un puntero al objeto que lanza el evento;
* y *params* - argumentos específicos pasados a los oyentes de evento.

Es posible crear tipos de eventos a la medida extendiendo a la clase @`Event`[Laminas\EventManager\Event].
Por ejemplo, el componente @`Laminas\Mvc` define un tipo de evento a la medida llamado
@`Laminas\Mvc\MvcEvent` que extiende a la clase `Event` y agrega varias propiedades
y métodos necesarios para el funcionamiento de @`Laminas\Mvc`.

### EventManager y SharedEventManager

Es importante entender la diferencia entre el *usual* administrador de eventos y el
administrador de eventos *compartido*.

El administrador de eventos usual no es guardado como un *singleton* en el administrador
de servicios. Cada vez que pedimos el servicio @`EventManager` desde el administrador de
servicios recibimos una nueva instancia de él. Esto se hace por privacidad y
rendimiento:

  * Se asume por defecto que la clase lanzadora de eventos pedirá y guardará en algún lugar
    su propio administrador de eventos privado porque no se quiere que otras
    clases escuchen automáticamente estos eventos. Los eventos lanzados por la clase se
    consideran como pertenecientes a la clase privadamente.

  * Si alguien quisiera ser capaz de escuchar cualquier evento lanzado por alguna clase,
    sería un infierno, demasiados oyentes de eventos serían invocados, incrementando
    el tiempo de carga de la página. Es mejor evitar esto manteniendo eventos privados.

Pero en caso de que alguien intencionalmente *necesite* escuchar a otro evento, existe
un administrador especial para eventos compartidos. El servicio @`SharedEventManager`
se guarda en el administrador de servicios como un *singleton*, de esta manera podemos
estar seguros de que todos tendrán la misma instancia de él.

Con el @`SharedEventManager` podemos fijar un oyente a un evento privado lanzado por
determinada clase (o varias clases). Para esto debemos especificar el o los identificadores
únicos de la clase que quieras escuchar. ¡Así de simple!

T> Algunos ejemplos prácticos de como escuchar y actuar frente a un evento se pueden
T> encontrar en los capítulos [Crear un Nuevo módulo](#modules) y
T> [Administración de Usuarios, Autenticación y Filtro de Acceso](#users).

## Resumen

En este capítulo aprendimos algo de teoría sobre el funcionamiento básica de un
un sitio web basado en Laminas.

Laminas usa los *namespaces* de PHP y las características de clase *autoloading* que simplifica
el desarrollo de aplicaciones que usan muchos componentes de terceros. Los nombres de
espacio permiten resolver la colisión de nombres entre componentes y proveernos de
la capacidad de hacer cortos los nombres largos.

La clase *autoloading* permite usar cualquier clase PHP y de cualquier biblioteca instalada con
*Composer* sin el uso de una sentencia `require_once`. Además, *Composer* provee un *autoloader PSR-4*
para las clases que están en los módulos de nuestra aplicación web.

La mayoría de los componentes de Laminas Framework requieren configuración. Podemos definir
los parámetros de configuración tanto a nivel de aplicación como a nivel de módulo.

El objetivo principal de cualquier aplicación web es manejar una petición HTTP y producir
una respuesta HTTP que comúnmente contiene el código HTML de la página web solicitada.
Cuando el servidor web Apache recibe una petición HTTP desde el navegador web del cliente
ejecuta el archivo *index.php*, que también es conocido como script de entrada.
Con cada petición HTTP se crea un objeto @`Laminas\Mvc\Application` cuyo «ciclo de vida»
consiste de varias etapas (o eventos).

La lógica de negocios de la aplicación web se puede considerar como un conjunto
de servicios. En Laminas Framework, el administrador de servicios es un contenedor
centralizado para todos los servicios de aplicación. Un servicio es normalmente una
clase PHP pero, si es necesario, también puede ser una variable o un arreglo.
