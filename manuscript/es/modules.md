# Crear un nuevo módulo {#modules}

Cuando nuestra aplicación web crece en tamaño podemos encontrar beneficioso
usar la característica de Laminas Framework llamada *módulos*. Las aplicaciones
modulares consisten en piezas que tienen relativamente pocas dependencias
una de otra. Esto permite instalar o remover módulos bajo demanda. En este
capítulo aprenderemos lo que es un módulo de Laminas, cuando crear un nuevo
módulo y como crearlo.

## ¿Cuando crear un nuevo módulo?

Un módulo es una unidad *reusable* y *autónoma* de nuestra aplicación. Por
defecto nuestra aplicación tiene un solo módulo llamado *Application*. Y es
normal colocar nuestros modelos, vistas y controladores en este módulo,
especialmente en un sitio web simple.

I> Un módulo puede contener modelos, vistas, controladores y archivos estáticos.
I> Las clases del módulo generalmente viven en un espacio de nombres separado
I> (el mismo nombre del módulo). Un módulo tiene su propio archivo de configuración
I> en donde podemos registrar rutas, controladores, complementos de controladores,
I> servicios, vistas, etc.

Podemos necesitar crear un nuevo módulo cuando alguna de las siguientes es
verdadera:

  * Si nuestro sitio web es relativamente grande y dividiéndolo en piezas resulta
    más fácil de mantener. Por ejemplo, podemos agregar el módulo *Admin* y
    colocar la funcionalidad de administración en este módulo.

  * Si la funcionalidad que estamos implementando es autónoma (no tiene o tiene
    pocas dependencias con otros módulos de nuestra aplicación). Por ejemplo,
    la funcionalidad de un Blog se puede en teoría separar en un módulo, porque
    esta no depende del resto de la aplicación pero la aplicación depende
    de él.

  * Si el módulo se puede separar de nuestra aplicación y simultáneamente
    usar en otro proyecto. Por ejemplo, el módulo del Blog se puede reusar en
    otro proyecto sin cambios (solo su configuración necesita cambios).

En cualquier caso, la vida real es compleja, y deberíamos guiarnos por la intuición
cuando parece ser que es necesario crear un nuevo módulo. Si pensamos que separar
una funcionalidad en un módulo nos dará beneficios significativos, creamos el
módulo.

Al mismo tiempo, no deberíamos crear un enorme número de módulos casi vacíos.
Es mejor combinar funcionalidades relacionadas en un solo módulo.

## ¿Como creer un nuevo módulo?

Existen al menos dos formas de crear un módulo nuevo en nuestro sitio web.
La primera de ellas es copiando el módulo existente que esta en la carpeta
*APP_DIR/module* (como el módulo *Application*), removiendo los controladores
innecesarios, los modelos y vistas; y luego cambiando el espacio de nombres
que esta en cada uno de los archivos fuente. Esta manera es bastante aburrida.

La segunda manera es descargando un *esqueleto de módulo* vacío desde el repositorio
oficial de Laminas Framework en GitHub. Podemos encontrar este repositorio
[aquí](https://github.com/laminas/LaminasSkeletonModule). Podemos clonar
el código o descargarlo en un archivo ZIP (recomendado).

Por ejemplo, en GNU/Linux, usamos el siguiente comando para descargar el esqueleto
de módulo:

~~~
cd ~
wget https://github.com/laminas/LaminasSkeletonModule/archive/master.zip
unzip master.zip
cp LaminasSkeletonModule-master APP_DIR/module/LaminasSkeletonModule
~~~

Con los comandos de arriba descargamos el código fuente del esqueleto de módulo
a nuestra carpeta *home*, desempacamos el archivo y copiamos los archivos en nuestra
carpeta `module` del sitio web.

Vamos a ver la estructura del esqueleto de módulo (ver figura 14.1):

![Figura 14.1. Estructura del esqueleto de módulo](../en/images/modules/skeleton_module_directory.png)

Como podemos ver, tenemos una estructura de carpeta típica con la que ya estamos
familiarizados:

  * La subcarpeta `config` contiene el archivo `module.config.php` que es el
    archivo de configuración del módulo.
  * La subcarpeta `src` contiene los archivos fuente del módulo.
    * La subcarpeta `Controller` contiene una clase de tipo controlador de
      ejemplo.
    * El archivo `Module.php` es el punto de entrada al módulo. Hablaremos sobre
      él un poco más tarde.
  * La subcarpeta `tests` contiene un esbozo para las pruebas unitarias de este
    módulo. Nosotros no cubrimos las pruebas unitarias en este libro por simplicidad.
  * La subcarpeta `view` contiene los scripts de vista (y además puede contener
    maquetas de plantilla específicas del módulo.)

### Renombrar la carpeta del esqueleto de aplicación

Antes que podamos usar este nuevo módulo vacío, deberíamos elegir un nombre para
él. Un buen nombre es el que describe bien al módulo. Por ejemplo, el nombre
`Admin` es bueno cuando necesitamos un módulo para las tareas de administración.
El nombre `Blog` sería bueno si planeamos tener las funcionalidades de un blog
en este módulo. Además, una buena práctica es añadir al principio del nombre
del módulo el nombre del proveedor, por ejemplo `YourCompanyBlog`.

Una vez que elegimos el nombre para el módulo, debemos cambiar el nombre a la
carpeta que contiene los archivos del módulo. Por ejemplo, el comando de abajo
cambia el nombre del módulo a `Admin`:

~~~
mv LaminasSkeletonModule Admin
~~~

Luego, debemos cambiar el nombre del archivo `SkeletonController.php` a uno
más descriptivo. No olvidemos renombrar las subcarpetas que están dentro de la
carpeta `view` para reflejar el nombre del controlador.

Finalmente, revisamos la configuración y los archivos fuente de los controladores
y nos aseguramos de cambiar el nombre del espacio de nombres `LaminasSkeletonModule`
al nombre de nuestro módulo (esto es necesario para asegurar que la clase
auto-cargador de PHP encuentre a nuestras clases).

### Habilitar la clase autocargador

El último paso es habilitar la clase de autocargador de PHP. Nuestro archivos
fuentes del módulo serán organizados en conformidad al estándar PSR-4, con el
objeto de usar el autocargador estándar que provee Composer. Para hacer esto,
agregamos la siguiente línea dentro de nuestro archivo `composer.json`
en la llave `psr-4` (debemos sustituir con el nombre de nuestro módulo):

~~~
...
"autoload": {
        "psr-4": {
            ...
            "Admin\\": "module/Admin/src/"
        }
    },
...
~~~

Luego, ejecutamos el siguiente comando para actualizar los archivos del autocargador
de Composer:

~~~
php composer.phar dump-autoload
~~~

I> El comando `dump-autoload` solo regenera el código del autocargador sin
I> instalar o actualizar ninguna dependencia.

¡Muy bien! El módulo está ahora listo para ser usado. Podemos agregar controladores,
modelos y vistas dentro de él. No olvidemos modificar el archivo `module.config.php`
y registrar las rutas, servicios, controladores, complementos de controlador,
ayudantes de vista, etc.

### Habilitar el módulo

Para que Laminas reconozca al nuevo módulo y lo cargue al iniciar la aplicación,
no olvidemos habilitar nuestro nuevo módulo en el archivo
*APP_DIR/config/modules.config.php* de la siguiente manera:

~~~
return [
    'Admin',
    //...
);
~~~

## Archivo Module.php y escucha de eventos

El archivo `Module.php` ubicado dentro de la carpeta fuente del módulo es un
tipo de *punto de entrada* al módulo. La clase `Module` que esta definida en este
archivo es cargada por el componente `Laminas\ModuleManager` cuando este carga
todos los módulos de la aplicación.

Una cosa útil que podemos hacer con esta clase es *registrar eventos*. Si recordamos
el capítulo [Operación del Sitio Web](#operation), la aplicación tiene varias
etapas de vida que son representadas por eventos. Podemos escribir una función
o clase de escucha de eventos, *event listener*, y registrarla en el punto de
entrada al módulo. Cuando un evento se lanza nuestro método o clase de escucha
es llamado permitiendo hacer algo útil.

Q> **¿Por qué querría registrar un escucha de eventos?**
Q>
Q> Aquí están varias aplicaciones prácticas de un escucha de eventos que
Q> podríamos encontrar útiles:
Q>
Q>   * Escuchar un evento *Route* para obligar a usar una conexión segura
Q>     HTTPS.
Q>   * Cuando nuestro sitio web está en modo de mantenimiento, escuchar un
Q>     evento *Route* para capturar todas las peticiones y redirigir al usuario
Q>     a una solo página.
Q>   * Escuchar un evento *Dispatch* para redirigir al usuario a una página
Q>     diferente. Por ejemplo, si el usuario no está autenticado él
Q>     es redirigido a la página de inicio de sesión.
Q>   * Escuchar un evento *Dispatch* para sobrescribir la maqueta de plantilla
Q>     predeterminada en todos los controladores que pertenecen al módulo.
Q>   * Escuchar un evento *Dispatch Error* para registrar o reportar cualquier
Q>     excepción o error que suceda en nuestro sitio web.
Q>   * Escuchar un evento *Render* para modificar el contenido de la página
Q>     resultante.

Existen dos maneras de registrar un escucha de eventos, *evento listener*, dentro
de la clase `Module`: con la ayuda del método `init()` del módulo o con la ayuda
de su método `onBootstrap()`. La diferencia entre el método `init()` y el
método `onBootstrap()` es que el método `init()` se llama primero que el método
`onBootstrap()`. El método `init()` se llama antes de inicializar todos los
otros módulos, mientras que `onBootstrap()` se llama una vez que todos los
módulos se han inicializado. En los siguientes ejemplos usaremos el método `init()`.

### Ejemplo 1. Cambiar la maqueta de plantilla

Para mostrar como suscribir un evento vamos a crear un escucha de eventos
que reaccionará a un evento *Dispatch* y colocará una maqueta de plantilla
diferente para *todos* los controladores del módulo:

~~~php
<?php
namespace YourCompanyModule;

use Laminas\ModuleManager\ModuleManager;
use Laminas\Mvc\MvcEvent;

class Module
{
    // The "init" method is called on application start-up and
    // allows to register an event listener.
    public function init(ModuleManager $manager)
    {
        // Get event manager.
        $eventManager = $manager->getEventManager();
        $sharedEventManager = $eventManager->getSharedManager();
        // Register the event listener method.
        $sharedEventManager->attach(__NAMESPACE__, 'dispatch',
                                    [$this, 'onDispatch'], 100);
    }

    // Event listener method.
    public function onDispatch(MvcEvent $event)
    {
        // Get controller to which the HTTP request was dispatched.
        $controller = $event->getTarget();
        // Get fully qualified class name of the controller.
        $controllerClass = get_class($controller);
        // Get module name of the controller.
        $moduleNamespace = substr($controllerClass, 0, strpos($controllerClass, '\\'));

        // Switch layout only for controllers belonging to our module.
        if ($moduleNamespace == __NAMESPACE__) {
            $viewModel = $event->getViewModel();
            $viewModel->setTemplate('layout/layout2');
        }
    }

    // ...
}
~~~

En el código de arriba, agregamos el método `init()` a la clase `Module`.
En este método, registramos un escucha de eventos (línea 17) con la ayuda
del método `attach()` que provee la clase `Laminas\EventManager\SharedEventManager`.
El método `attach()` toma cuatro argumentos: el identificador del componente
emisor, el nombre del evento («dispatch»), el método de escucha de eventos
(el método `onDispatch()` de la clase actual) y la prioridad (100).

El método `onDispatch()` se llama con un evento *Dispatch*. En este método,
revisamos (línea 32) si la petición HTTP fue enviada al controlador que
pertenece a nuestro módulo y de ser así cambiamos la maqueta de plantilla
(línea 34).

### Ejemplo 2. Forzar el uso con HTTPS

En este ejemplo, mostraremos como registrar un escucha de eventos que obliga
al sitio web a usar siempre conexión HTTPS en todas nuestras páginas web.

~~~php
<?php
namespace YourCompanyModule;

use Laminas\ModuleManager\ModuleManager;
use Laminas\Mvc\MvcEvent;

class Module
{
    // The "init" method is called on application start-up and
    // allows to register an event listener.
    public function init(ModuleManager $manager)
    {
        // Get event manager.
        $eventManager = $manager->getEventManager();
        $sharedEventManager = $eventManager->getSharedManager();
        // Register the event listener method.
        $sharedEventManager->attach(__NAMESPACE__, 'route',
                                    [$this, 'onRoute'], 100);
    }

    // Event listener method.
    public function onRoute(MvcEvent $event)
    {
        if (php_sapi_name() == "cli") {
            // Do not execute HTTPS redirect in console mode.
            return;
        }

        // Get request URI
        $uri = $event->getRequest()->getUri();
        $scheme = $uri->getScheme();
        // If scheme is not HTTPS, redirect to the same URI, but with
        // HTTPS scheme.
        if ($scheme != 'https'){
            $uri->setScheme('https');
            $response=$event->getResponse();
            $response->getHeaders()->addHeaderLine('Location', $uri);
            $response->setStatusCode(301);
            $response->sendHeaders();
            return $response;
        }
    }

    // ...
}
~~~

En el código de arriba, registramos un escucha de eventos que se llama con
cada evento *Route*.

Dentro del escucha de eventos, primero revisamos si nuestro sitio web está
trabajando en el modo de consola. No debemos redirigir a HTTPS si se está en
el modo de consola.

Luego, extraemos la URI del la petición HTTP y revisar si el esquema actual
es HTTPS o no. Si el esquema no es HTTPS, redirigimos al usuario a la misma
URL pero con el esquema HTTPS.

### Ejemplo 3. Reportar todas las excepciones de nuestro sitio web

Con esta técnica, podemos fácilmente rastrear todas las excepciones que ocurren
en nuestro sitio web. Reportar excepciones y errores es una tarea importante,
porque permite hacer a nuestro sitio web más estable, seguro y mejorar la
experiencia del usuario.

~~~php
<?php
namespace YourCompanyModule;

use Laminas\ModuleManager\ModuleManager;
use Laminas\Mvc\MvcEvent;

class Module
{
    // The "init" method is called on application start-up and
    // allows to register an event listener.
    public function init(ModuleManager $manager)
    {
        // Get event manager.
        $eventManager = $manager->getEventManager();
        $sharedEventManager = $eventManager->getSharedManager();
        // Register the event listener method.
        $sharedEventManager->attach(__NAMESPACE__, MvcEvent::EVENT_DISPATCH_ERROR,
                                    [$this, 'onError'], 100);
        $sharedEventManager->attach(__NAMESPACE__, MvcEvent::EVENT_RENDER_ERROR,
                                    [$this, 'onError'], 100);
    }

    // Event listener method.
    public function onError(MvcEvent $event)
    {
        // Get the exception information.
        $exception = $event->getParam('exception');
        if ($exception!=null) {
            $exceptionName = $exception->getMessage();
            $file = $exception->getFile();
            $line = $exception->getLine();
            $stackTrace = $exception->getTraceAsString();
        }
        $errorMessage = $event->getError();
        $controllerName = $event->getController();

        // Prepare email message.
        $to = 'admin@yourdomain.com';
        $subject = 'Your Website Exception';

        $body = '';
        if(isset($_SERVER['REQUEST_URI'])) {
            $body .= "Request URI: " . $_SERVER['REQUEST_URI'] . "\n\n";
        }
        $body .= "Controller: $controllerName\n";
        $body .= "Error message: $errorMessage\n";
        if ($exception!=null) {
            $body .= "Exception: $exceptionName\n";
            $body .= "File: $file\n";
            $body .= "Line: $line\n";
            $body .= "Stack trace:\n\n" . $stackTrace;
        }

        $body = str_replace("\n", "<br>", $body);

        // Send an email about the error.
        mail($to, $subject, $body);
    }

    // ...
}
~~~

En el código de arriba, registramos un escucha de eventos que se llamará en
cada error *Dispatch* (ruta no encontrada o una excepción) y  cada error para mostrar
la página. Dentro del método de escucha `onError()`, extraemos alguna información
sobre la excepción o error y lo enviamos como un mensaje de correo electrónico
a una dirección de correo electrónico.

## Registrar el módulo como un paquete de composer

Si escribimos un módulo reusable que planeamos esté disponible públicamente,
podemos publicar el código de nuestro módulo en GitHub y registrarlo en
el catálogo [Packagist.org](http://packagist.org) como un paquete que se puede
instalar con Composer. Esto es completamente gratis.

Después de registrar el paquete seremos capaces de agregarlo como una dependencia
a nuestra aplicación web de la siguiente manera (reemplazando respectivamente
las variables `vendor` y `package` con los nombres de nuestra compañía y paquete):

~~~
php composer.phar require vendor/package
~~~

Composer descargará e instalará nuestro módulo en la carpeta `vendor` y así
podremos usarlo como a cualquier otro módulo de tercero.

## Resumen

En este capítulo hemos aprendido sobre *módulos* en Laminas Framework. Un módulo
es una unidad *autónoma* y *reusable* de nuestra aplicación. Podemos crear un
nuevo módulo cuando nuestro sitio web se hace grande y cuando la funcionalidad
tiene muy pocas dependencias con otras partes de nuestra aplicación.

Cada módulo de Laminas tiene una clase que funciona como punto de entrada llamado
`Module`. Podemos usar esta clase para registrar un escucha de eventos. Los
escuchas de eventos son útiles, por ejemplo, cuando queremos cambiar la maqueta
de plantilla predeterminada para todo el módulo o retocar el contenido que por
defecto tiene una página.

Si estamos desarrollando un módulo que queremos publicar para que sea usado
por otros proyectos, podemos registrarlo en el catálogo Packagist.org
para que luego se pueda instalar con Composer como un paquete de un tercero.
