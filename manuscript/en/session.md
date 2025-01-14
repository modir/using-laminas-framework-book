# Working with Sessions {#session}

In this chapter, you will learn about *sessions*. The HTTP protocol is stateless, so you can't
share data between two HTTP requests by default. PHP sessions allow to workaround this by saving data on server during
one page request and retrieve it later during another page request. For example, you can remember
that the user has logged in, and show a personalized web page the next time he visits the website.
Laminas Framework internally uses the PHP sessions, but additionally provides a convenient wrapper around
PHP sessions, so you don't access `$_SESSION` super-global array directly.

Laminas components covered in this chapter:

|--------------------------------|---------------------------------------------------------------|
| *Component*                    | *Description*                                                 |
|--------------------------------|---------------------------------------------------------------|
| @`Laminas\Session`                 | Implements a wrapper around PHP sessions.                     |
|--------------------------------|---------------------------------------------------------------|

## PHP Sessions

First, let's give some theory on what PHP sessions are. In PHP, sessions work as follows:

  * when a site visitor opens the website for the first time, PHP sets a cookie [^cookie] in the client browser.
  * the website may save any information to session with the help of special super-global array named `$_SESSION`. The
    data saved to session is stored on server in form of disk files.
  * when the same visitor opens the website again, the web browser sends the saved cookie to server, so PHP determines
    that this is the same visitor and loads the session data again to the `$_SESSION` array.

[^cookie]: An HTTP cookie is a small piece of data sent from a website and stored in the user's
web browser while the user is browsing. Cookies are used to remember some state between HTTP requests.

From PHP application developer's point of view, the work with sessions is simple. First, initialise the session by
calling `session_start()` PHP function. Then, use `$_SESSION` super-global array for setting/retrieving session data.
For example, to save some data to session, use the following code:

~~~php
session_start();
$_SESSION['my_var'] = 'Some data';
~~~

To later retrieve the data from session, use the following code:

~~~php
session_start();
if (isset($_SESSION['my_var']))
    $sessionVar = $_SESSION['my_var'];
else
    $sessionVar = 'Some default value';
~~~

To clear the data, use the `unset()` PHP function, as follows:

~~~php
unset($_SESSION['my_var']);
~~~

Note that sessions do not last forever (they expire sooner or later when the user's cookie expires or when PHP engine
cleans up the session storage files). How long the session lasts is defined in *php.ini* configuration file. It is possible to
override the default expiration parameters with the help of `ini_set()` function, as follows:

~~~php
// Set session cookie lifetime (in seconds) to be 1 hour.
ini_set('session.cookie_lifetime', 60*60*1);

// Store session data on server for maximum 1 month.
ini_set('session.gc_maxlifetime', 60*60*24*30);
~~~

There are several other "advanced" session-related PHP configuration settings in *php.ini*. We do not
cover them here, because they are usually not needed.

Q> **So, if PHP sessions is so simple, why do I need additional wrapper provided by Laminas Framework?**
Q>
Q> laminas-provided wrapper around the PHP sessions is useful, because:
Q>
Q>   * Laminas session wrapper is object-oriented, so you can use it consistently in your MVC application.
Q>   * Laminas provides the concept of session namespaces, so different models can store data without naming conflicts.
Q>   * Laminas provides security features (session validators), so it is more difficult for a malicious user to hack and substitute your session data.
Q>   * Using `$_SESSION` super-global array directly is not good, because it makes testing your website more difficult. When you use a wrapper around PHP sessions, it is easier to supply test data.
Q>   * With Laminas session classes, it is possible to implement custom session data storages (for example, store session data in database instead of files).

## Installing Laminas\Session Component

In Laminas, the session functionality is implemented inside of @`Laminas\Session` component. If you haven't
yet installed this component in your web application, do this now with Composer by typing the following command:

~~~
php composer.phar require laminas/laminas-session
~~~

The command above downloads the component code from GitHub and installs it in the `APP_DIR/vendor` directory.
It also injects the information about the installed module inside of your `APP_DIR/config/modules.config.php`
configuration file.

## Session Manager

Laminas provides a special service called @`SessionManager` which belongs to @`Laminas\Session` namespace. This service
is a usual Laminas service and is automatically registered in service manager. You can get an instance of the @`SessionManager`
service in a factory class with the following code:

~~~php
// Use alias for the SessionManager class.
use Laminas\Session\SessionManager;

// Retrieve an instance of the session manager from the service manager.
$sessionManager = $container->get(SessionManager::class);
~~~

So, what does the @`SessionManager` do? Actually, it does everything for session to run.
The summary of its most useful methods is provided in the table 15.1 below:

{title="Table 15.1. Methods provided by the SessionManager class"}
|------------------------------------|--------------------------------------------------|
| *Method*                           | *Description*                                    |
|------------------------------------|--------------------------------------------------|
| `sessionExists()`                  | Checks whether session exists and currently active. |
|------------------------------------|--------------------------------------------------|
| `start($preserveStorage = false)`  | Starts the session (if not started yet).         |
|------------------------------------|--------------------------------------------------|
| `destroy(array $options = null)`   | Ends the session.                                |
|------------------------------------|--------------------------------------------------|
| `getId()`                          | Returns session ID.                              |
|------------------------------------|--------------------------------------------------|
| `setId()`                          | Sets session ID.                                 |
|------------------------------------|--------------------------------------------------|
| `regenerateId()`                   | Regenerates the session ID.                      |
|------------------------------------|--------------------------------------------------|
| `getName()`                        | Returns session name.                            |
|------------------------------------|--------------------------------------------------|
| `setName()`                        | Overrides the default session name from *php.ini*. |
|------------------------------------|--------------------------------------------------|
| `rememberMe($ttl = null)`          | Sets session cookie lifetime (in seconds).       |
|------------------------------------|--------------------------------------------------|
| `forgetMe()`                       | Set a zero lifetime for the session cookie (the cookie will expire when browser is closed).      |
|------------------------------------|--------------------------------------------------|
| `expireSessionCookie()`            | Expires the session cookie immediately.          |
|------------------------------------|--------------------------------------------------|
| `isValid()`                        | Executes session validators.                     |
|------------------------------------|--------------------------------------------------|

As you can see from the table above, the @`SessionManager` can start the session and end it, check if session exists, and set session parameters
(such as cookie expiration). It also provides a validator chain that may contain session validators (those
validators allow to prevent hacker attacks on session data).

### Providing Session Configuration

The @`SessionManager` class on initialization reads the application configuration,
so you can set up the session parameters conveniently. To do that, modify your `APP_DIR/config/autoload/global.php`
as follows:

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

T> We modify `global.php` file here, because sessions may be used by any module in your website and do not
depend on environment.

As you can see, the session configuration is stored under three keys:

  * The `session_config` key allows to define how long the session cookie will live and how long the PHP engine will
    store your session data on server.
    Actually, this key may contain additional session options, but we omit them for simplicity (if you'd like to override
    those advanced options, please refer to Laminas Framework documentation).

  * The `session_manager` key allows to set session validators. These are used to enhance the security. It is recommended
    that you always specify these validators here.

  * The `session_storage` key allows to specify the session storage class. We use the @`SessionArrayStorage` class, which
    is the default storage and is sufficient for the most cases.

### Making the Session Manager the Default One

In Laminas, many components use the session manager implicitly (for example, @`FlashMessenger`[Laminas\Mvc\Plugin\FlashMessenger] controller plugin and view helper
uses session to save messages between HTTP requests). To let such components use the session manager you just configured, you'll have to make it
"the default one" by instantiating it as early as possible. For example, you can instantiate the session manager in your
module's `onBootstrap()` method, as follows:

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

T> Making the session manager the default one is very important, because otherwise you'll have to explicitly pass it to every component
T> depending on the session manager, which is rather boring.

## Session Containers

Once you have configured the session manager, you can actually store and retrieve data to/from session. To do that,
you use *session containers*. Session containers are implemented by the @`Container`[Laminas\Session\Container] class living in @`Laminas\Session` namespace.

The session container can be used to save your data to session and retrieve it from session. To avoid naming conflicts between
different classes, modules and components that use sessions, the session container allows you to specify the *namespace* under
which the data will be stored. A container namespace may contain upper-case and lower-case characters,
underscores and back-slashes. So, for example, "Session\ContainerName", "session_container_name" and "SessionContainerName" are all
valid container namespaces.

I> Session containers work closely with the session manager. When you create a session container, it calls the session
I> manager's `start()` method automatically, so session is started and initialized.

Now let's start using containers. You can create a container using two equivalent ways: either manually instantiating a container or
let a factory do that for you. The second one is easier, so we recommend it.

### Method 1. Manual Instantiation of a Session Container

You can just create a session container with the `new` operator, but you need to pass an instance of
the session manager service to container's constructor:

~~~php
use Laminas\Session\Container;

// We assume that $sessionManager variable is an instance of the session manager.
$sessionContainer = new Container('ContainerNamespace', $sessionManager);
~~~

So, before you create the container, be sure you have injected the session manager in your controller, service or wherever
you need to create the container.

### Method 2. Creating a Session Container Using Factory

This method is equivalent to the first one, but the session container is created by the factory.
You just need to register what container namespaces you need. To do that,
add the `session_containers` key to your `module.config.php` file as follows:

~~~php
<?php
return [
    // ...
    'session_containers' => [
        'ContainerNamespace'
    ],
];
~~~

You may list the allowable container names under this key. Choosing a container name is up to you, just be sure it is unique among all other service names.

Once you registered a container name (or several container names), you can create the container and work with it.
You typically do that in a factory with the help of the service manager:

~~~php
// The $container variable is the service manager.
$sessionContainer = $container->get('ContainerNamespace');
~~~

As you can see, you retrieve a session container from the service manager by its registered name.

### Saving Data to Session with Session Container

When you created the session container, you are able to save data to it as follows:

~~~php
$sessionContainer->myVar = 'Some data';
~~~

To retrieve the data from session container, you use the following code:

~~~php
if(isset($sessionContainer->myVar))
    $myVar = $sessionContainer->myVar;
else
    $myVar = null;
~~~

To remove data from session, use the following code:

~~~php
unset($sessionContainer->myVar);
~~~

T> For some practical examples of using session containers, please refer to [Implementing Multi-Step Forms](#multi-step-forms)
   section.

## Summary

PHP sessions is a useful feature allowing you to store some data between page requests. PHP engine stores session data on server
in form of files, and uses browser cookies to identify the same visitor the next time and load his session data to memory. For example, you can
remember the user and show him personalized pages. The session doesn't last forever - it expires in some time.

Laminas Framework provides a convenient wrapper around PHP sessions. With this wrapper, you can store data in session containers in object-oriented way.
Laminas also provides security features allowing to automatically validate session and prevent hacker attacks.
