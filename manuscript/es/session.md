# Trabajar con sesiones {#session}

En este capítulo, aprenderemos sobre *sesiones*. HTTP es un protocolo sin estado, por lo que no podemos
compartir información entre dos peticiones HTTP. Las sesiones de PHP permiten eludir esto guardando la información en el servidor durante
la petición de una página y recuperarla luego durante la petición de otra página. Por ejemplo, podemos recordar
que el usuario ha iniciado sesión y mostrar una página web personalizada la próxima vez que visite el sitio web.
Laminas Framework internamente usa las sesiones de PHP, pero adicionalmente provee un conveniente envoltorio para
las sesiones de PHP, con lo que no accedemos al arreglo super-global `$_SESSION` directamente.

Componentes de Laminas usados en este capítulo:

|--------------------------------|---------------------------------------------------------------|
| *Componente*                   | *Descripción*                                                 |
|--------------------------------|---------------------------------------------------------------|
| `Laminas\Session`                 | Implementa un envoltorio para las sesiones PHP.               |
|--------------------------------|---------------------------------------------------------------|

## Sesiones de PHP

Primero vamos a dar algo de teoría sobre lo que son las sesiones PHP. En PHP las sesiones trabajan de la siguiente manera:

  * Cuando el visitante del sitio abre una página por primera vez PHP coloca una cookie[^cookie] en el navegador web.
  * El sitio web puede guardar cualquier información en la sesión con la ayuda de un arreglo especial super-global llamado `$_SESSION`.
    La información guardada en la sesión es almacenada en archivos del disco duro del servidor.
  * Cuando el mismo visitante abre el sitio web de nuevo, el navegador envía una cookie guardada al servidor, de esta manera PHP
    determina que se trata del mismo visitante y carga la información de la sesión de nuevo en el arreglo `$_SESSION`.

[^cookie]: Una cookie HTTP es una pequeña pieza de información enviada desde el sitio web y almacenada en el navegador
web del usuario mientras el usuario esta navegando. Las cookies se usan para recordar estados entre peticiones HTTP.

Desde el punto de vista del desarrollador de la aplicación el trabajo con sesiones es simple. Primero se inicializa la sesión
llamando a la función de PHP `session_start()`. Luego, se usa el arreglo super-global `$_SESSION` para colocar o recuperar la información
de sesión. Por ejemplo, para guardar alguna información de sesión se usa el siguiente comando:

~~~php
session_start();
$_SESSION['my_var'] = 'Some data';
~~~

Luego recuperamos la información de sesión, con el siguiente código:

~~~php
session_start();
if (isset($_SESSION['my_var']))
    $sessionVar = $_SESSION['my_var'];
else
    $sessionVar = 'Some default value';
~~~

Para limpiar la información, usamos la función de PHP `unset()`, de la siguiente manera:

~~~php
unset($_SESSION['my_var']);
~~~

Advierta que las sesiones no duran para siempre (ellas expiran tarde o temprano, o cuando la cookie del usuario expira o cuando el motor PHP
limpia los archivos de sesión guardados). El tiempo que dura la sesión se define en el archivo de configuración *php.ini*. Es posible sobreescribir
el parámetro de expiración por defecto con la ayuda de la función `ini_set()`, de la siguiente manera:

~~~php
// Set session cookie lifetime (in seconds) to be 1 hour.
ini_set('session.cookie_lifetime', 60*60*1);

// Store session data on server for maximum 1 month.
ini_set('session.gc_maxlifetime', 60*60*24*30);
~~~

Hay varias otras configuraciones avanzadas para las sesiones en el archivo *php.ini*. Nosotros no las tratamos
aquí porque no son usualmente necesarias.

Q> **Pero si las sesiones PHP son tan simples, ¿por que necesitamos el envoltorio provisto por Laminas Framework?**
Q>
Q> La envoltura para las sesiones de PHP provista por Laminas es útil porque:
Q>
Q>   * La envoltura de sesión de Laminas es orientada a objetos, así podemos usarla consistentemente en nuestra aplicación MVC.
Q>   * Laminas provee el concepto de nombres de espacio de sesión, así diferentes modelos pueden almacenar información sin conflicto de nombres.
Q>   * Laminas provee características de seguridad (validadores de sesión), así es más difícil para los usuarios maliciosos conseguir y sustituir nuestra información de sesión.
Q>   * Usar directamente el arreglo super-global `$_SESSION` no es bueno, porqué hace las pruebas de nuestro sitio web más difícil. Cuando usamos una envoltura para las sesiones PHP es más fácil ofrecer información de prueba.
Q>   * Con las clases de sesión de Laminas es posible implementar almacenamiento de datos de sesión personalizados (por ejemplo, almacenar datos de sesión en base de datos en lugar de archivos).

## Instalar el componente Laminas\Session

En Laminas la funcionalidad de sesión es implementada dentro del componente `Laminas\Session`. Si aun no hemos instalado
este componente en nuestra aplicación web podemos hacerlo ahora usando Composer y escribiendo el siguiente comando:

~~~
php composer.phar require laminas/laminas-session
~~~

El comando anterior descarga el código del componente desde GitHub y lo instala en el directorio `APP_DIR/vendor`.
Composer también agrega la información sobre el modulo instalado dentro de nuestro archivo de configuración
`APP_DIR/config/modules.config.php`.

## El manejador de sesiones

ZFE provee un servicio especial llamando `SessionManager` que pertenece al espacio de nombres `Laminas\Session`. Este servicio
es un servicio común de Laminas y se registra automáticamente en el manejador de servicios. Podemos tener una instancia del servicio
`SessionManager` en una factory class con el siguiente código:

~~~php
// Use alias for the SessionManager class.
use Laminas\Session\SessionManager;

// Retrieve an instance of the session manager from the service manager.
$sessionManager = $container->get(SessionManager::class);
~~~

Pero, ¿Que hace el `SessionManager`? De hecho él hace todo para que la sesión se ejecute.
Un resumen de sus métodos más utíles se muestra en la table 15.1 más abajo:

{title="Table 15.1. Métodos que provee las clase SessionManager"}
|------------------------------------|--------------------------------------------------|
| *Método*                           | *Descripción*                                    |
|------------------------------------|--------------------------------------------------|
| `sessionExists()`                  | Revisa si una sesión existe y si esta actualmente activada. |
|------------------------------------|--------------------------------------------------|
| `start($preserveStorage = false)`  | Comienza la sesión (si aún no esta iniciada).    |
|------------------------------------|--------------------------------------------------|
| `destroy(array $options = null)`   | Termina una sesión.                              |
|------------------------------------|--------------------------------------------------|
| `getId()`                          | Regresa el ID de la sesión.                      |
|------------------------------------|--------------------------------------------------|
| `setId()`                          | Coloca un ID a la sesión.                        |
|------------------------------------|--------------------------------------------------|
| `regenerateId()`                   | Regenera el ID de la sesión.                     |
|------------------------------------|--------------------------------------------------|
| `getName()`                        | Regresa el nombre de la sesión.                  |
|------------------------------------|--------------------------------------------------|
| `setName()`                        | Sobreescribe el valor original para le nombre de sesión del *php.ini*. |
|------------------------------------|--------------------------------------------------|
| `rememberMe($ttl = null)`          | Coloca el tiempo de vida de la cookie de sesión (en segundos).       |
|------------------------------------|--------------------------------------------------|
| `forgetMe()`                       | Coloca en cero la vida para la cookie de sesión (la cookie expira cuando el navegador se cierra). |
|------------------------------------|--------------------------------------------------|
| `expireSessionCookie()`            | Expira la cookie de sesión inmediatamente.        |
|------------------------------------|--------------------------------------------------|
| `isValid()`                        | Ejecuta los validadores de sesión.               |
|------------------------------------|--------------------------------------------------|

Como podemos ver e la tabla de arriba el `SessionManager` puede comenzar la sesión y terminarla, revisar si la sesión existe y colocar los parámetros
de sesión (como el momento de expiración de la cookie). Además, el manejador provee una cadena validadora que puede contener validadores de sesión
(estos validadores permiten prevenir ataques a la información de sesión).

### Establecer una configuración de sesión

La clase `SessionManager` al inicializar lee la configuración de la aplicación,
así podemos poner los parámetros de sesión convenientemente. Para hacer esto modificamos nuestro `APP_DIR/config/autoload/global.php`
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
        // Session data will be stored on server maximum for 30 days.
        'gc_maxlifetime'     => 60*60*24*30,
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

T> Modificamos el archivo `global.php` porque de esta manera las sesiones pueden ser usadas por cualquier modulo de nuestro sitio web
y no dependen del entorno.

Como podemos ver la configuración de sesión se almacena en tres llaves:

  * La llave `session_config` permite definir el tiempo de vida de la cookie y por cuanto tiempo el motor PHP
    almacenará nuestra información de sesión en el servidor.
    En realidad esta llave puede contener opciones de sesión adicionales, pero las omitimos por simplicidad (si queremos sobreescribir
    estas opciones avanzadas debemos revisar le documentación de Laminas Framework).

  * La llave `session_manager` permite colocar validadores de sesión. Estos se usan para aumentar la seguridad. Se recomienda
    que siempre se especifiquen estas validaciones aquí.

  * La llave `session_storage` permite especificar la clase de almacenamiento de sesión. Usamos la clase `SessionArrayStorage` que es
    el almacenamiento por defecto y es suficiente en la mayoría de los casos.

### Hacer al manejador de sesiones predeterminado

En Laminas muchos componentes usan el manejo de sesión implícitamente (por ejemplo, el controller plugin y view helper de `FlashMessenger` usan
sesiones para guardar mensajes entre peticiones HTTP). Para permitir que cada componente use el manejador de sesiones necesitamos configurarlo,
tendremos que hacerlo "predeterminado" instanciando al manejador tan pronto como sea posible. Por ejemplo, podemos
instanciar el manejador de sesiones en nuestro método de modulo `onBootstrap()`, de la siguiente manera:

~~~php
<?php
namespace Application;

use Laminas\Mvc\MvcEvent;
use Laminas\Session\SessionManager;

class Module
{
    //...

    /**
     * This method is called once the MVC bootstrapping is complete.
     */
    public function onBootstrap(MvcEvent $event)
    {
        $application = $event->getApplication();
        $serviceManager = $application->getServiceManager();

        // The following line instantiates the SessionManager and automatically
        // makes the SessionManager the 'default' one.
        $sessionManager = $serviceManager->get(SessionManager::class);
    }
}
~~~

T> Hacer al manejador de sesiones predeterminado es muy importante porque de otra manera tendremos que pasarlo a cada componente
T> que dependa del manejador, lo que es bastante aburrido.

## Contenedores de sesión

Una vez que tengamos configurado el manejador de sesiones, podemos almacenar y recuperar información en/desde la sesión. Para hacer esto
usamos *contenedores de sesión*. Los contenedores de sesión son implementados por la clase `Container` que esta ubicada en el espacio de nombres
`Laminas\Session`.

El contenedor de sesión puede usarse para guardar la información en la sesión y recuperarla desde la sesión. Para evitar conflictos de nombres
entre diferentes clases, módulos y componentes que usan sesiones, el contenedor de sesiones nos permite especificar el *espacio de nombres* bajo
el que la información se guardará. El espacio de nombres de un contenedor puede contener caracteres en mayúsculas y minúsculas, pisos
y barras invertidas. Por ejemplo; "Session\ContainerName", "session_container_name" y "SessionContainerName" son todos
nombres de espacio validos para contenedores.

I> Los contenedores de sesión trabajan de cerca con el manejador de sesiones. Cuando creamos un contenedor de sesiones este llama al
I> método `start()` del manejador de sesiones y así la sesión es comenzada e inicializada.

Ahora vamos a comenzar a usar contenedores. Podemos crear un contenedor de dos maneras equivalentes: o la instanciación manual del contenedor
o dejar la tarea a una factory. La segunda es más fácil y es la que recomendamos.

### Método 1. Instanciación manual de un contenedor de sesiones

Podemos crear un contenedor de sesión con el operador `new` pero necesitamos pasar una instancia del
servicio de manejo de sesiones al constructor del contenedor:

~~~php
use Laminas\Session\Container;

// We assume that $sessionManager variable is an instance of the session manager.
$sessionContainer = new Container('ContainerNamespace', $sessionManager);
~~~

De esta manera, antes de crear el contenedor debemos haber injectado el manejador de sesiones en nuestro controlador, servicio, etc.
en el que necesitamos crear un contenedor.

### Método 2. Crear un contenedor de sesiones usando una factory

Este método es equivalente al primero, pero el contenedor de sesión es creado por una factory.
Necesitamos registrar el espacio de nombres del contenedor que usaremos. Para hacer esto
agregamos la llave `session_containers` en nuestro archivo `module.config.php` de la siguiente manera:

~~~php
<?php
return [
    // ...
    'session_containers' => [
        'ContainerNamespace'
    ],
];
~~~

Nosotros podemos escribir los nombres de contenedores permitidos dentro de esta llave. Elegir un nombre de contenedor depende de ti, solo hay que garantizar que el nombre es único entre todos los otros nombres de servicios.

Una vez registrado un nombre de contenedor (o varios nombres de contenedores) podemos crear el contenedor y trabajar con él.
Que es lo que típicamente hacemos en una factory con la ayuda del manejador de servicio:

~~~php
// The $container variable is the service manager.
$sessionContainer = $container->get('ContainerNamespace');
~~~

Como podemos ver se recupera un contenedor de sesión desde el manejador de servicio mediante el nombre con que fue registrado.

### Guardar la información en una sesión con el contenedor de sesión

Cuando se crea el contenedor de sesión, nosotros podemos guardar la información en él de la siguiente manera:

~~~php
$sessionContainer->myVar = 'Some data';
~~~

Para recuperar la información del contenedor de sesiones podemos usar el siguiente código:

~~~php
if(isset($sessionContainer->myVar))
    $myVar = $sessionContainer->myVar;
else
    $myVar = null;
~~~

Para remover la información de sesión usamos el siguiente código:

~~~php
unset($sessionContainer->myVar);
~~~

T> Para algunos ejemplos prácticos del uso de contenedores de sesión, podemos revisar la sección [Implementing Multi-Step Forms](#multi-step-forms)

## Resumen

Las sesiones de PHP son una característica útil que permite almacenar información entre peticiones a páginas. El motor PHP almacena información
en el servidor en forma de archivos, y usa las cookies de navegación para identificar al mismo visitante en el futuro y carga su información de sessión
en memoria. Por ejemplo, podemos recordar al usuario y mostrarle páginas personalizadas. Las sesiones no duran para siempre ellas expiran con el tiempo.

Laminas Framework provee un conveniente envoltorio para las sesiones PHP. Con este envoltorio, nosotros podemos guardar información en contenedores de sesión
de una manera orientada a objetos. Laminas tambíen provee caracteristícas de seguridad que permite la validación automática de sesiones y previene ataques.
