# Laminas Skeleton Application {#skeleton}

Laminas Framework nos provee con la llamada «skeleton application» para facilitar
la creación de nuestros sitios web desde cero. En este capítulo mostraremos
como instalar la aplicación *skeleton* y como crear un sitio virtual de Apache.
Es recomendable revisar el [Apéndice A. Configuración del Entorno de Desarrollo Web](#devenv)
antes de leer este capítulo para tener nuestro entorno de desarrollo configurado.

## Descargar Laminas Skeleton Application

Skeleton Application es un aplicación simple basada en Laminas que contiene las
cosas más necesarias para crear nuestro sitio web.

El código de Skeleton Application está guardado en el servicio de alojamiento
de código GitHub y es accesible públicamente por medio de
[este enlace](https://github.com/laminas/LaminasSkeletonApplication).
Sin embargo generalmente no descargamos el código fuente de la aplicación
esqueleto directamente sino que usamos el administrador de dependencias
[Composer](http://getcomposer.org/), como se muestra abajo:

Primero necesitamos conseguir la última versión de Composer. Podemos hacer
esto con los siguientes comandos:

```
cd ~

php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"

php composer-setup.php

php -r "unlink('composer-setup.php');"
```

Con los comandos de arriba cambiamos la carpeta de trabajo colocándonos en la
carpeta *home*, descargamos el script de instalación `composer-setup.php` en la
carpeta actual, lo ejecutamos y finalmente borramos el instalador.

T> Una vez que ejecutamos los comandos de arriba tendremos el archivo `composer.phar`
T> en nuestra carpeta de trabajo.

Ahora escribimos el siguiente comando en la consola de comandos:

```
php composer.phar create-project -sdev laminas/skeleton-application helloworld
```

El comando de arriba descarga la *Laminas Skeleton Application* en la carpeta
`helloworld` y ejecuta su instalador interactivo. Deberemos responder varias
preguntas de tipo *yes/no* escribiendo `y` o `n` y presionando Enter. Las
respuestas ayudaran al instalador a determinar que dependencias instalar.
Si no sabemos que responder podemos respondamos `n` (no), luego podremos instalar
dependencias adicionales en cualquier momento.

Para comenzar podemos responder de la siguiente manera:

```
    Do you want a minimal install (no optional packages)? Y/n
n

    Would you like to install the developer toolbar? y/N
n

    Would you like to install caching support? y/N
n

    Would you like to install database support (installs laminas-db)? y/N
n

    Would you like to install forms support? y/N
y
    Will install laminas/laminas-mvc-form (^1.0)
    When prompted to install as a module, select application.config.php or modules.config.php

    Would you like to install JSON de/serialization support? y/N
n

    Would you like to install logging support? y/N
n

    Would you like to install MVC-based console support? (We recommend migrating to zf-console, symfony/console, or Aura.CLI) y/N
n

    Would you like to install i18n support? y/N
n

    Would you like to install the official MVC plugins, including PRG support, identity, and flash messages? y/N
n

    Would you like to use the PSR-7 middleware dispatcher? y/N
n

    Would you like to install sessions support? y/N
n

    Would you like to install MVC testing support? y/N
n

    Would you like to install the laminas-di integration for laminas-servicemanager? y/N
n
```

Una vez que respondimos las preguntas el instalador descargará e instalará todos
los paquetes necesarios y nos preguntará en que archivo de configuración queremos
inyectar la información sobre los módulos instalados. Cuando veamos el cursor
escribimos '1' y presionamos Enter:

```
 Please select which config file you wish to inject 'Laminas\Form' into:
  [0] Do not inject
  [1] config/modules.config.php
  [2] config/development.config.php.dist
  Make your selection (default is 0):1

  Remember this option for other packages of the same type? (y/N) y
```

Luego el instalador nos preguntará si queremos borrar el archivo de control de
versiones del proyecto. Como nosotros probablemente guardaremos la aplicación
web en nuestro propio sistema de control de versiones (como Git) no necesitaremos
los archivos existentes, entonces escribimos 'y' y presionamos Enter:

```
Do you want to remove the existing VCS (.git, .svn..) history? [Y,n]? y
```

Ahora copiamos el archivo `composer.phar` en la nueva carpeta `helloworld`:

```
cp composer.phar helloworld
```

El último paso y muy importante es habilitar el *development mode* escribiendo
el siguiente comando:

~~~
cd helloworld
php composer.phar development-enable
~~~

I> El modo de desarrollo se usa típicamente cuando *desarrollamos* nuestra
I> aplicación. Cuando habilitamos el modo de desarrollo se crean archivos de
I> configuración adicionales en nuestra carpeta
I> `config` de la aplicación web. En este modo nuestra aplicación
I> puede cargar opcionalmente módulos de "desarrollo". Además, en este modo la
I> configuración de la cache está deshabilitada lo que nos permite cambiar los
I> archivos de configuración del sitio web y ver los cambios inmediatamente.
I>
I> Una vez que hemos terminado el desarrollo podemos habilitar el modo de
I> *producción* escribiendo lo siguiente:
I>
I> `php composer.phar development-disable`

¡Muy bien! El trabajo duro está terminado. Ahora vamos a ver el contenido de
la carpeta `helloworld`.

## Estructura de Carpetas Típica

Cada sitio web basado en Laminas (incluyendo la aplicación *skeleton*) se
organiza de una misma manera siguiendo las recomendaciones. Por supuesto que podemos
configurar nuestra aplicación para usar una estructura de carpetas
diferente pero esto hace más difícil que otras personas que
no están familiarizadas con esta estructura puedan mantener nuestro
sitio web.

Vamos a dar un vistazo general a la estructura de carpetas típica
(ver figura 2.1):

![Figura 2.1. Estructura de Carpetas Típica](../en/images/skeleton/skeleton_dir_structure.png)

Como podemos ver en la carpeta de primer nivel, que llamaremos desde ahora
`APP_DIR`, hay varios archivos:

* `composer.json` es el archivo de configuración JSON para Composer.

* `composer.lock` es un archivo que contiene información sobre los
  paquetes instalados con Composer.

* `composer.phar` es un archivo PHP ejecutable que contiene el código
  de la herramienta de manejo de dependencias, es decir, de Composer.

* `docker-compose.yml` y `Dockerfile` son archivos auxiliares que solo
  son útiles si usamos la herramienta de manejo de contenedores
  [Docker](https://www.docker.com). En este libro no estudiamos el
  uso de Docker.

* `LICENSE.md` es un archivo de texto que contiene la licencia de Laminas
  (hemos hablado sobre ella en el capítulo [Introducción a Laminas Framework](#intro)).
  No podemos quitar o modificar este archivo por que la licencia de
  Laminas no permite hacerlo.

* `phpunit.xml.dist` es un archivo de configuración para [PHPUnit](https://phpunit.de/)
  (framework para pruebas unitarias). Usamos este archivo cuando queremos
  crear pruebas unitarias para nuestro sitio web.

* `README.md` es una archivo de texto que contiene una breve descripción
  sobre la aplicación *skeleton*. Generalmente reemplazamos el contenido
  de este archivo con la información sobre nuestro sitio web: su nombre,
  que hace y como instalarlo.

* `TODO.md` es un archivo auxiliar que se puede borrar sin problemas.

* `Vagrantfile` es una archivo auxiliar que contiene la configuración
  para [Vagrant](https://www.vagrantup.com/) que es un administrador
  de entornos de desarrollo virtuales. Podemos ignorar este archivo si
  no conocemos que es Vagrant. En este libro no usaremos Vagrant.

Además, tenemos varias subcarpetas:

La carpeta `config` contiene los archivos de configuración de nivel de
aplicación.

La carpeta `data` contienen los datos que nuestra aplicación puede crear;
esta carpeta puede contener la cache usada para mejorar la aceleración de Laminas
Framework.

La carpeta `module` contiene todos los módulos de la aplicación. Actualmente
solo hay un módulo llamando `Application`. `Application` es el módulo
principal de nuestro sitio web. Además, si lo deseamos, podemos colocar
otros módulos allí. Hablaremos sobre los módulos más tarde.

El propósito de la carpeta `vendor` es contener archivos de bibliotecas
de terceros incluyendo los archivos de la biblioteca Laminas Framework.
Esta carpeta es usada principalmente por Composer.

La carpeta `public` contiene los datos accesibles públicamente por el
usuario web. Como podemos ver, los usuarios web se
comunicarán principalmente con el archivo `index.php` que también es
llamado *punto de entrada* a nuestro sitio web.

I> Nuestro sitio web tendrá solo un punto de entrada, *index.php*,
I> esto es más seguro que permitir el acceso
I> a todos nuestros archivos PHP.

Dentro de la carpeta `public` además podemos encontrar el archivo
oculto `.htaccess`. Su propósito principal es definir las reglas de
reescritura de URL.

La carpeta `public` contiene varias subcarpetas que son accesibles
públicamente para los usuarios web.

* La subcarpeta `css` contiene todos los archivos CSS accesibles públicamente.
* La subcarpeta `fonts` contiene las tipografías web específicas de la
  aplicación.
* La subcarpeta `img` contiene las imágenes accesibles públicamente
  (*.JPG, *.PNG, *.GIF, *.ICO, etc.).
* Y la subcarpeta `js` guarda los archivos JavaScript que usan nuestras
  páginas web. Generalmente los archivos de la biblioteca
  [jQuery](http://jquery.com/) se colocan aquí pero también podemos
  colocar nuestros archivos JavaScript.

Q> **¿Que es la biblioteca jQuery?**
Q>
Q> jQuery es una biblioteca JavaScript que fue creada para simplificar
Q> el código del lado del cliente de las páginas HTML. Los mecanismos
Q> de selección de jQuery permiten fácilmente adjuntar administradores
Q> de eventos a un determinado elemento HTML, haciendo realmente simple
Q> tener páginas HTML interactivas.

Como *Laminas Skeleton Application* se guarda en GitHub encontraremos
dentro de la estructura de carpetas el archivo oculto `.gitignore`.
Este es un archivo del sistema de control de versiones [GIT](http://git-scm.com/).
Podemos ignorarlo o incluso borrarlo si no planeamos guardar nuestro
código en un repositorio GIT.

## Dependencias de la Aplicación

Una dependencia es algún código de un tercero que nuestra aplicación usa. Por
ejemplo Laminas Framework es una dependencia de nuestro sitio web.

Con Composer cualquier biblioteca se llama *paquete*. Todos los paquetes que se
pueden instalar con Composer están registrados en el sitio web [Packagist.org](https://packagist.org/).
Con Composer podemos identificar los paquetes que nuestra aplicación necesita,
Composer los descargará e instalará automáticamente.

Las dependencias de la aplicación *skeleton* se declaran en el archivo
`APP_DIR/composer.json` (ver más abajo):

{line-numbers=off,lang=text, title="Contenido del archivo composer.json"}
~~~
{
    "name": "laminas/skeleton-application",
    "description": "Skeleton Application for Laminas Framework laminas-mvc applications",
    "type": "project",
    "license": "BSD-3-Clause",
    "keywords": [
        "framework",
        "mvc",
        "zf2"
    ],
    "homepage": "http://framework.Laminas.com/",
    "minimum-stability": "dev",
    "prefer-stable": true,
    "require": {
        "php": "^5.6 || ^7.0",
        "laminas/laminas-component-installer": "^1.0 || ^0.3 || ^1.0.0-dev@dev",
        "laminas/laminas-mvc": "^3.0.1",
        "zfcampus/zf-development-mode": "^3.0",
        "laminas/laminas-mvc-form": "^1.0",
        "laminas/laminas-mvc-plugins": "^1.0.1",
        "laminas/laminas-session": "^2.7.1"
    },
    "autoload": {
        "psr-4": {
            "Application\\": "module/Application/src/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "ApplicationTest\\": "module/Application/test/"
        }
    },
    "extra": [],
    "scripts": {
        "development-disable": "zf-development-mode disable",
        "development-enable": "zf-development-mode enable",
        "development-status": "zf-development-mode status",
        "serve": "php -S 0.0.0.0:8080 -t public/ public/index.php"
    }
}
~~~

Q> **¿Qué es JSON?**
Q>
Q> JSON (JavaScript Object Notation), es un archivo de texto legible por
Q> humanos para representar estructuras simples y arreglos asociados anidados.
Q> A pesar de que JSON tiene su origen en JavaScript es usado por PHP y otros
Q> lenguajes porque es conveniente para guardar datos de configuración.

En este archivo podemos ver información básica sobre la aplicación *skeleton*
(su nombre, descripción, licencia, palabras claves y la página oficial).
Nosotros generalmente cambiamos esta información por la de nuestra futura
aplicación. Esta información es opcional así que incluso podemos borrarla
si no tenemos planeado publicar nuestra aplicación web en el catalogo
`Packagist`.

La parte interesante para nosotros es la llave `require`. La llave `require`
contiene las declaraciones de las dependencias para nuestra aplicación.
Vemos que es necesario el motor de PHP es su versión 5.6 o superior y varios
componentes de Laminas Framework como `laminas-mvc`, `laminas-mvc-form`, etc.

La información que está en el archivo `composer.json` es suficiente para
encontrar las dependencias, descargarlas e instalarlas en la carpeta `vendor`.
Si en algún momento necesitamos instalar otra dependencia podemos agregarla
editando el archivo `composer.json` cambiando las dependencias en él y luego
escribir los siguiente comandos en la consola de comandos:

{line-numbers=off}
~~~
php composer.phar self-update
php composer.phar install
~~~

Los comandos de arriba actualizarán Composer a su última versión y luego
instalará nuestras dependencias. Sin embargo Composer no instalará PHP, él
simplemente se asegurara que está instalada la versión apropiada y si no
mostrará una advertencia.

Si revisamos la subcarpeta `vendor` podemos ver que ella contiene muchos
archivos. Los archivos de Laminas Framework se encuentran dentro de la carpeta
`APP_DIR/vendor/laminas/` (figura 2.2).

![Figura 2.2. Carpeta Vendor](../en/images/skeleton/vendor_dir.png)

I> Algunos frameworks usan otra manera convencional de instalar dependencias.
I> Es posible descargar las librerías que necesita nuestra aplicación como
I> un archivo y colocarlo en algún lugar dentro de nuestra
I> estructura de carpetas (generalmente en la carpeta `vendor`). Esta forma se
I> usó en Laminas Framework 1. Pero en Laminas Framework se recomienda instalar
I> las dependencias mediante Composer.

## Sitio Virtual de Apache

¡Ahora estamos casi listos para que nuestra aplicación *skeleton* viva! La
última cosa que tenemos que hacer es configurar el sitio virtual de Apache.
El termino sitio virtual significa que podemos ejecutar varios sitios web en
la misma computadora. Los sitios virtuales se diferencian por el nombre de
dominio (como `site.mydomain.com` y `site2.mydomain.com`) o por el número de
puerto (como `localhost` y `localhost:8080`). Los sitios virtuales funcionan
de manera transparente para los usuarios, esto significa que los usuarios
no tienen la menor idea de si el sitio esta funcionando sobre la misma o sobre
diferentes computadoras.

Actualmente tenemos a la aplicación *skeleton* dentro del directorio `home`.
Para que Apache lo sepa, necesitamos editar el archivo de configuración
del sitio virtual.

I> El archivo para el sitio virtual puede estar localizado en diferentes rutas
I> dependiendo del sistema operativo. Por ejemplo, en Ubuntu GNU/Linux está
I> ubicado en `/etc/apache2/sites-available/000-default.conf`. Para
I> información sobre sitios virtuales específicos por sistema operativo
I> podemos revisar el [Apéndice A. Configuración del Entorno de Desarrollo Web](#devenv).

Vamos a editar el sitio virtual por defecto para dejarlo de la siguiente manera
(asumimos que usamos Apache 2.4):

{line-numbers=on,lang=text, title="Archivo para el Sitio Virtual"}
~~~
<VirtualHost *:80>
    ServerAdmin webmaster@localhost

    DocumentRoot /home/username/helloworld/public

	<Directory /home/username/helloworld/public/>
        DirectoryIndex index.php
        AllowOverride All
        Require all granted
    </Directory>

</VirtualHost>
~~~

En la línea 1 se indica a Apache que escuche por todas las direcciones IP (*)
y por el puerto 80.

La línea 2 define el correo electrónico del administrador web. Si algo malo
pasa en el sitio. Apache envía una alerta por correo electrónico a esta
dirección. Podemos colocar nuestra dirección de correo electrónico aquí.

La línea 4 define el directorio raíz de documentos (`APP_DIR/public`). Todos
los archivos y carpetas dentro del la raíz de documentos serán accesibles
para los usuarios web. Debemos colocar allí la ruta absoluta a la carpeta
`public` de la aplicación *skeleton*. Así, las carpetas y archivos dentro
de `public` (como `index.php`, `css`, `js`, etc.) serán accesibles mientras
que las carpetas y archivos por encima de la carpeta `public` (como `config`,
`module`, etc.) no serán accesibles para los usuarios web, con esto aumentamos
la seguridad de nuestro sitio web.

Las líneas 6-10 definen las reglas para la carpeta raíz de documentos (`APP_DIR/public`).
Por ejemplo, la directiva `DirectoryIndex` le dice a Apache que el archivo
`index.php` debe ser usado como el archivo index por defecto. La directiva
`AllowOverride All` permite definir reglas en el archivo `.htaccess`. La
directiva `Require all granted` permite a cualquiera visitar la página web.

W> Laminas Framework utiliza el módulo de reescritura de URL de Apache para
W> redirigir a los usuarios web al punto de entrada de nuestro sitio web.
W> Debemos asegurarnos que el servidor web tiene el módulo `mod_rewrite` habilitado.
W> Para instrucciones sobre como habilitar el módulo podemos revisar el
W> [Apéndice A. Configuración del Entorno de Desarrollo Web](#devenv).

T> Después de editar el archivo de configuración no debemos olvidar reiniciar Apache
T> para que se apliquen los cambios.

## Abrir el Sitio Web en Nuestro Navegador Web

Para abrir el sitio web escribimos en nuestra barra de navegación del navegador
web «http://localhost» y presionamos Enter. La Figura 2.3 muestra el sitio web
en acción.

En la página que aparece, podemos ver el menú de navegación en la parte superior.
La barra de navegación actualmente contiene un solo enlace llamado *Home*.
Debajo de la barra de navegación podemos ver el título «Welcome to Laminas Framework».
Debajo del título encontramos algunos consejos útiles para los principiantes
sobre como desarrollar nuevas aplicaciones basadas en Laminas.

![Figura 2.3. La Aplicación Skeleton de Laminas](../en/images/skeleton/Laminas_skeleton_default_view.png)

## Crear un Proyecto en NetBeans

Ahora que tenemos la aplicación *skeleton* lista y trabajando necesitaremos
cambiar algunas cosas. Comúnmente usamos un *entorno de desarrollo integrado*,
IDE (Integrated Development Environment),
para navegar fácilmente en la estructura de carpetas, editar los archivos
y encontrar errores en el sitio web. En este libro usamos NetBeans IDE
(podemos ver el [Apéndice A. Configuración del Entorno de Desarrollo Web](#devenv)
para más información sobre como instalar NetBeans).

Crearemos un proyecto en NetBeans para nuestra aplicación *skeleton*, primero
ejecutamos NetBeans y abrimos el menú *File->New Project...*. Aparecerá
el dialogo de ventana *New Project* (ver figura 2.4).

![Figura 2.4. Crear un Proyecto en NetBeans - Ventana "Choose Project"](../en/images/skeleton/netbeans_create_project.png)

En la página que aparece, *Choose Project*, debemos elegir PHP como tipo de proyecto
y en la lista de la derecha seleccionamos *Application with Existing Sources*
(porque ya tenemos el código de la aplicación *skeleton*). Luego hacemos clic
en el botón *Next* para ir a la siguiente página (ver figura 2.5)

![Figura 2.5. Crear un Proyecto en NetBeans - Ventana "Name and Location"](../en/images/skeleton/netbeans_create_project_step2.png)

En el dialogo de página *Name and Location* debemos colocar la ruta al código
(como */home/username/helloworld*), el nombre para el proyecto (por ejemplo
`helloworld`) y la versión de PHP. Nuestro código usa (PHP 5.6 o superior).
La versión de PHP se necesita para que el corrector de sintaxis de NetBeans
busque en nuestro código los errores y los resalte.
Presionamos el botón *Next* para ir al siguiente dialogo de página
(ver figura 2.6).

![Figure 2.6. Crear un Proyecto en NetBeans - Choosing Configuration Page](../en/images/skeleton/netbeans_create_project_step3.png)

En la ventana *Run Configuration* es recomendable especificar la manera en que
ejecutaremos el sitio web (Local Web Site) y la URL del sitio web (`http://localhost`).
Mantenemos el campo *Index File* vacío porque estamos usando `mod_rewrite` y
la ruta real al archivo `index.php` es ocultada por Apache2. Si se muestra
un mensaje de alerta como "Index File must be specified in order to run or debug
project in command line" solo debemos ignorarlo.

Hacemos clic en el botón *Finish* para crear el proyecto. Si el proyecto
*helloworld* se crea exitosamente deberíamos ver la ventana del proyecto
(ver figura 2.7).

![Figura 2.7. Ventana de Proyecto de NetBeans](../en/images/skeleton/netbeans_project_window.png)

En la ventana de proyectos podemos ver la barra de menú, la barra de herramientas,
el panel *Projects* donde los archivos del proyecto son listados. En la parte
derecha de la ventana podemos ver el código del archivo de entrada `index.php`.

Para más consejos sobre el uso de NetBeans, como lanzar y resolver problemas
interactivamente en sitios web basados en Laminas, podemos revisar el
[Apéndice B. Introducción al Desarrollo con PHP usando NetBeans IDE](#netbeans)

T> **Es momento de hablar de algunas cosas más avanzadas...*
T>
T> ¡Felicitaciones! Hemos hecho el trabajo duro de instalación y ejecución
T> de *Laminas Skeleton Application*, ahora es momento de tener un descaso
T> y leer sobre algunas cosas avanzadas en la última parte de este capítulo.

## Hypertext Access File (.htaccess)

Hemos mencionado al archivo `APP_DIR/public/.htaccess` cuando hablamos sobre
la estructura de carpetas típica. Ahora vamos a entender el rol de este archivo.

El archivo `.htaccess` («hypertext access») es de hecho un archivo de configuración
del servidor web Apache que permite sobrescribir parte de la configuración global
del servidor. El archivo `.htaccess` es un archivo de configuración a nivel de
carpetas esto significa que afecta solo a la carpeta donde esta guardado y
a sus subcarpetas.

El contenido del archivo `.htaccess` se presenta más abajo:

~~~text
RewriteEngine On
# The following rule tells Apache that if the requested filename
# exists, simply serve it.
RewriteCond %{REQUEST_FILENAME} -s [OR]
RewriteCond %{REQUEST_FILENAME} -l [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^.*$ - [L]
# The following rewrites all other queries to index.php. The
# condition ensures that if you are using Apache aliases to do
# mass virtual hosting or installed the project in a subdirectory,
# the base path will be prepended to allow proper resolution of
# the index.php file; it will work in non-aliased environments
# as well, providing a safe, one-size fits all solution.
RewriteCond %{REQUEST_URI}::$1 ^(/.+)/(.*)::\2$
RewriteRule ^(.*) - [E=BASE:%1]
RewriteRule ^(.*)$ %{ENV:BASE}/index.php [L]
~~~

La línea 1 le dice al servidor web Apache que habilite el motor de rescritura
de URL (`mod_rewrite`). El motor de rescritura modifica las peticiones URL
entrantes basado en un expresión regular. Esto permite asociar URLs
arbitrarias a las URLs de nuestra estructura interna de la manera que queramos.

Las líneas 4-7 definen las reglas de rescritura que dicen al servidor web que
si el cliente (navegador web) pide un archivo que existe en la carpeta raíz de
documentos entonces se regrese el contenido del archivo como una respuesta HTTP.
Como nosotros tenemos nuestra carpeta `public` dentro de la raíz de documentos
del sitio virtual se permite a los usuarios del sitio ver todos los archivos
que están dentro de la carpeta `public` incluyendo el `index.php`, los archivos
CSS, los archivos JavaScript y los archivos de imágenes.

Las líneas 14-16 definen las reglas de rescritura que le dicen a Apache que debe
hacer si el usuario pide un archivo que no existe en la raíz
de documentos. En este caso, el usuario será redirigido al archivo `index.php`.

La tabla 2.1 contiene varios ejemplos de rescritura de URL. La primera y
segunda URL apuntan a archivos que existen, así que `mod_rewrite` regresa la
ruta del archivo solicitado. La URL del tercer ejemplo apunta al archivo
`htpasswd` que no existe (lo que puede ser la señal del ataque de un cracker)
y basado en nuestras reglas de rescritura el motor regresa el archivo
`index.php`.

{title="Tabla 2.1. Ejemplo de rescritura de URL"}
|-------------------------------------|-----------------------------------------|
| **URL solicitada**                  | **URL sobrescrita**                     |
|-------------------------------------|-----------------------------------------|
| `http://localhost/index.php`        | El archivo existe; se regresa el archivo|
|                                     | local `APP_DIR/public/index.php`.       |
|-------------------------------------|-----------------------------------------|
| `http://localhost/css/bootstrap.css`| El archivo existe; se regresa el archivo|
|                                     | local `APP_DIR/public/css/bootstrap.css`|
|-------------------------------------|-----------------------------------------|
| `http://localhost/htpasswd`         | El archivo no existe; en su lugar se    |
|                                     | regresa `APP_DIR/public/index.php`.     |
|-------------------------------------|-----------------------------------------|

## Bloquear el Acceso al Sitio Web por Dirección IP

A veces puede ser necesario bloquear el acceso a nuestro sitio web a todas las
direcciones IP con excepción de la nuestra. Por ejemplo, cuando desarrollamos
un sitio web no queremos que alguien vea el trabajo incompleto. Además,
podemos no desear que Google y otros motores de búsqueda indexen nuestro
sitio web.

Para prohibir el acceso a nuestro sitio web debemos modificar el sitio virtual
agregando las siguientes líneas:

~~~text
Require ip <your_ip_address>
~~~

Q> **¿Como determino mi dirección IP?**
Q>
Q> Podemos usar la página web [http://www.whatismyip.com](http://www.whatismyip.com/)
Q> para determinar nuestra dirección IP externa. La dirección IP externa es la
Q> dirección por medio de la que otras computadoras en Internet pueden acceder
Q> a nuestro sitio web.

## Autenticación HTTP

En ocasiones necesitamos permitir el acceso a nuestro sitio web a determinados
usuarios. Por ejemplo, cuando mostramos el sitio web a nuestro jefe podemos
darle un nombre de usuario y una contraseña para que inicie sesión en nuestro
sitio.

Para permitir el acceso a nuestro sitio web mediante un usuario y su contraseña
podemos modificar el sitio virtual de la siguiente manera:

~~~text
...
<Directory /home/username/helloworld/public/>
    DirectoryIndex index.php
    AllowOverride All
    AuthType Basic
    AuthName "Authentication Required"
    AuthUserFile /usr/local/apache/passwd/passwords
    Require valid-user
</Directory>
...
~~~

La línea 5 define el método de autenticación, `Basic`. El método más común es
`Basic`. Es importante ser cuidadosos porque este método de autenticación envía
la contraseña desde el cliente al servidor sin codificar. Por esta razón este
método no se usa para datos altamente sensibles. Apache soporta otro método
de autenticación: `AuthType Digest`. Este método es mucho más seguro. Los
navegadores más recientes soportan este último tipo de autenticación.

La línea 6 define el texto que se mostrará al usuario cuanto intente iniciar
sesión.

La línea 7 define el archivo donde se guardan las contraseñas. Este archivo
se debe crear con la herramienta `htpasswd`.

La línea 8 permite iniciar sesión a cualquiera que este en el archivo de
contraseñas y que ingrese correctamente su contraseña.

Para crear el archivo `passwords` escribimos el siguiente comando:

~~~
htpasswd -c /usr/local/apache/passwd/passwords <username>
~~~

En el comando de arriba debemos reemplazar el comodín `<username>` con el
nombre del usuario. Podemos elegir un nombre arbitrario, por ejemplo, «admin».
El comando pedirá la contraseña del usuario y la guardará en el archivo:

~~~text
# htpasswd -c /usr/local/apache/passwd/passwords <username>
New password:
Re-type new password:
Adding password for user <username>
~~~

Cuando el usuario intente visitar el sitio web verá un dialogo de autenticación
HTTP. Para iniciar sesión en el sitio, el visitante debe ingresar el nombre de
usuario y la contraseña correcta.

I> Para información adicional sobre la autenticación HTTP podemos revisar
I> el tema [Authentication and Authorization](http://httpd.apache.org/docs/current/howto/auth.html)
I> en la documentación de Apache.

## Tener Multiples Sitios Virtuales

Cuando desarrollamos varios sitios web en la misma computadora necesitaremos
crear varios sitios virtuales. Para cada sitio virtual necesitamos especificar
un nombre de dominio (como `site1.mydomain.com`). Pero si aún no tenemos un
nombre de dominio podemos especificar en su lugar un puerto diferente
(veamos un ejemplo más abajo).

~~~text
# Listen directive tells Apache to listen requests on port 8080
Listen 8080

<VirtualHost *:8080>
    ...
</VirtualHost>
~~~

Para acceder al sitio web escribimos en la barra de navegación del navegador
web la URL http://localhost:8080 y luego presionamos Enter.

T> Después de editar el archivo de configuración del sitio virtual debemos
T> reiniciar Apache para aplicar los cambios.

## El Archivo Hosts

Cuando tenemos varios sitios web asociados a diferentes puertos se hace
difícil recordar en que puerto esta cada sitio. Para simplificar esto podemos
usar sitios virtuales basados en nombre y definir un alias para nuestro
sitio web en el archivo del sistema `hosts`.

Primero modificamos el virtual host de Apache para convertirlo en un sitio
virtual *basado en nombres*:

~~~text
<VirtualHost *:80>
  # Add the ServerName directive
	ServerName site1.localhost
	...
</VirtualHost>
~~~

Luego debemos editar el archivo `hosts`. El archivo `host` es un archivo
de sistema que contiene la correspondencia entre direcciones IP y nombres de
servidores. El archivo hosts contiene lineas de texto que consisten en una
dirección IP en el primer campo de texto seguido por uno o más nombres.

Para agregar un alias a nuestros sitios web agregamos una línea para cada
uno de ellos como se muestra abajo:

~~~text
127.0.0.1            site1.localhost
~~~

De esta manera solamente ingresamos «site1.localhost» en la barra de navegación
de nuestro navegador web en lugar de recordar el número de puerto:

I> En GNU/Linux el archivo `hosts` esta ubicado en `/etc/hosts`. En Windows,
I> el archivo esta generalmente en `C:\Windows\System32\drivers\etc\hosts`.
I> Para editar el archivo necesitamos ser administradores. Observe que algunos
I> antivirus pueden bloquear los cambios del archivo hosts, así que tendremos
I> que desactivar temporalmente nuestro software antivirus, luego editar
I> el archivo y habilitarlo de nuevo.

I> Si hemos comprado un dominio real para nuestro sitio web (como `example.com`)
I> no necesitamos modificar el archivos `hosts` porque Apache será capaz de
I> resolver la dirección IP de nuestro sitio web usando el sistema de DNS.
I> Solo modificamos el archivo `hosts` cuando el sistema DNS no sabe nada
I> sobre el nombre de dominio y no puede resolver la dirección IP de nuestro
I> sitio web.

## Uso Avanzado de Composer

Al principio de este capítulo hemos usado Composer para instalar el código de
la biblioteca Laminas Framework. Ahora vamos a describir rápidamente algunos
usos avanzados de Composer.

Como ya sabemos, la única llave que es obligatoria en el archivo `composer.json`
es `require`. Esta llave le dice a Composer que paquetes necesita la aplicación:

~~~text
{
    "require": {
        "php": "^5.6 || ^7.0",
        "laminas/laminas-component-installer": "^1.0 || ^0.3 || ^1.0.0-dev@dev",
        "laminas/laminas-mvc": "^3.0.1",
        "zfcampus/zf-development-mode": "^3.0",
        "laminas/laminas-mvc-form": "^1.0",
        "laminas/laminas-mvc-plugins": "^1.0.1",
        "laminas/laminas-session": "^2.7.1"
    }
}
~~~

### Nombre de los Paquetes y Versiones

El nombre de un paquete consiste en dos partes: el nombre del proveedor y el
nombre del proyecto. Por ejemplo, el nombre del paquete «laminas/laminas-mvc»
consiste en el nombre del proveedor «laminas» y el nombre del proyecto
«laminas-mvc». Podemos buscar cada uno de los paquetes del proveedor «laminas»
en el sitio web [Packagist.org](https://packagist.org/search/?q=laminas)
(ver un ejemplo en la figura 2.8).

![Figure 2.8. Podemos buscar paquetes en Packagist.org](../en/images/skeleton/packagist_search.png)

Además, un paquete tiene un número de version asociado. Un número de versión
consiste de un número principal, un número secundario, opcionalmente el
número de compilación y opcionalmente un sufijo de estabilidad (ejemplo, b1, rc1).
Dentro de la llave `require` especificamos la versión del paquete que es
aceptable. Por ejemplo, «^5.6» significa que podemos instalar versiones
superiores a «5.6» pero inferiores a «6.0» (es decir que podemos instalar solo
aquellos paquetes que no rompen la retrocompatibilidad). En la tabla 2.2 se
muestran las posibles maneras de especificar versiones aceptables:

{title="Tabla 2.2. Definiciones de las Versiones de los Paquetes"}
|-------------------------|----------------------------------------------------------------------------|
| *Ejemplo de Definición* | *Descripción*                                                              |
|-------------------------|----------------------------------------------------------------------------|
| 3.0.1                   | Versión exacta. En este ejemplo solo la versión 3.0.1 se puede instalar.   |
|-------------------------|----------------------------------------------------------------------------|
| >=3.0.1                 | La versión mayor o igual se puede instalar (3.0.1, 3.2.1, etc.)            |
|-------------------------|----------------------------------------------------------------------------|
| >3.0.1                  | La versión mayor se puede instalar (3.0.2 etc.)                            |
|-------------------------|----------------------------------------------------------------------------|
| <=3.0.1                 | La versión menor o igual se puede instalar (1.0, 1.5, 2.0.0 etc.)          |
|-------------------------|----------------------------------------------------------------------------|
| <3.0.1                  | La versión menor se puede instalar (1.0, 1.1, 1.9, etc.)                   |
|-------------------------|----------------------------------------------------------------------------|
| !=3.0.1                 | Todas las versiones con excepción de esta se pueden instalar.              |
|-------------------------|----------------------------------------------------------------------------|
| >=3.0,<3.1.0            | Cualquier versión dentro del rango de versiones se puede instalar.         |
|-------------------------|----------------------------------------------------------------------------|
| 3.*                     | Cualquier versión que tenga el número principal igual a 3 se puede instalar|
|                         | (el número secundario puede ser cualquiera).                               |
|-------------------------|----------------------------------------------------------------------------|
| ~3.0                    | Cualquier versión que comience en 3.0 pero menor que el número de versión  |
|                         | principal siguiente (equivalente a >=3.0,<4.0).                            |
|-------------------------|----------------------------------------------------------------------------|
| ^3.0                    | Cualquier versión comenzando por 3.0 pero menor que la siguiente versión   |
|                         | principal (equivalente a >=4.0,<4.0). Similar a `~3.0`, pero está más      |
|                         | cercano al versionado semántico y siempre permite actualizaciones sin      |
|                         | interrumpirlas.                                                            |
|-------------------------|----------------------------------------------------------------------------|

### Instalar y Actualizar Paquetes

Hemos visto como usar el comando `php composer.phar install` para instalar
nuestras dependencias. Tan pronto como ejecutamos este comando Composer
encuentra, descarga e instala las dependencias en nuestra carpeta `vendor`.

Q> **¿Es seguro instalar dependencias con Composer?**
Q>
Q> Algunas personas podrían tener miedo de administrar las dependencias usando
Q> Composer porque piensan que alguien puede actualizar por error o
Q> intencionalmente las dependencias de todo el sistema operativo causando que
Q> la aplicación falle. Observemos que Composer *nunca* instala dependencias
Q> para todo el sistema operativo en realidad Composer las instala
Q> en la carpeta `APP_DIR/vendor/`.

Después de la instalación Composer crea el archivo `APP_DIR/composer.lock`.
Este archivo contiene todas las versiones de los paquetes que fueron instalados.
Si ejecutamos el comando `install` de nuevo, Composer encontrará el archivo
`composer.lock`, revisará que dependencias ya están instaladas y si todos
los paquetes ya están instalados Composer no hará nada.

Ahora si suponemos que pasado un período de tiempo se publican nuevas
actualizaciones de seguridad para nuestros paquetes vamos a querer actualizarlos
para mantener la seguridad del sitio web, entonces, tenemos que escribir el
siguiente comando:

`php composer.phar update`

Si queremos actualizar solo una dependencia escribimos su nombre de la
siguiente manera:

`php composer.phar update laminas/laminas-mvc`

Después de ejecutar el comando `update` el archivo `composer.lock` también
se actualizará.

Q> **¿Que hago si quiero regresar a la versión anterior del paquete?**
Q>
Q> Si el proceso de actualización trajo problemas inesperados a nuestros
Q> sistema podemos revertir los cambios en el archivo `composer.lock` y
Q> ejecutar el comando `install` de nuevo. Revertir los cambios del archivo
Q> `composer.lock` es muy fácil si usamos un sistema de control de versiones,
Q> como GIT o SVN. Si no usamos un sistema de control de versiones podemos
Q> hacer un respaldo del `composer.lock` antes de actualizar.

### Agregar una Dependencia Nueva

Si queremos agregar una nueva dependencia a la aplicación podemos o editar el
archivo `composer.json` manualmente o ejecutar un comando `require`. Por ejemplo,
para instalar el módulo Doctrine ORM en nuestro sitio web o en otros términos
agregar el paquete `doctrine/doctrine-module` como una dependencia de la
aplicación, escribimos el siguiente comando:

`php composer.phar require doctrine/doctrine-module 2.*`

El comando de arriba edita el archivo `composer.json`, descarga e instala el
paquete. Usaremos este comando luego en el capítulo
[Administración de Bases de Datos con Doctrine](#doctrine) cuando comencemos
a familiarizarnos con la administración de base de datos.

### Paquetes Virtuales

Composer se puede usar para obligar a que algunas funcionalidades estén presentes
en nuestro sistema. Ya hemos visto como señalar el requisito «php:^5.6».
El «Paquete PHP» es un paquete virtual que representa a PHP mismo. Además,
podemos necesitar otras cosas como extensiones de PHP (ver tabla 2.3 más abajo).

{title="Table 2.3. Los Paquetes Virtuales en Composer"}
|------------------------------------------------------------------------------|
| *Ejemplo de Definición* | *Descripción*                                      |
|------------------------------------------------------------------------------|
| "php":"^5.6"            | Se necesita una versión de PHP mayor o igual a 5.6 |
|                         | pero menor a 6.0.                                  |
|------------------------------------------------------------------------------|
| ext-dom, ext-pdo-mysql  | Se necesitan la extensiones PHP DOM y PDO MySQL.   |
|------------------------------------------------------------------------------|
| lib-openssl             | Se necesita la biblioteca OpenSSL.                 |
|------------------------------------------------------------------------------|

Podemos usar el comando `php composer.phar show --platform` para mostrar una
lista con los paquetes virtuales disponibles en nuestra computadora.

### Composer y el Sistema de Control de Versiones

Si estamos usando un sistema de control de versiones como Git, tendremos
curiosidad sobre que debería guardarse en Git: ¿solo el código de nuestra
aplicación o el código de nuestra aplicación más todas las dependencias que
están en la carpeta `APP_DIR/vendor` y que instalamos usando Composer?.

En general no es recomendable guardar nuestras dependencias en el control de
versiones porque pueden hacer a nuestro repositorio realmente grande y lento
de obtener y ramificar. En su lugar debemos guardar el archivo `composer.lock`
en el control de versiones. El archivo `composer.lock` garantiza que cualquiera
pueda instalar las mismas versiones de las dependencias que nosotros tenemos.
Esto es útil en equipos de desarrollo que tienen más de un desarrollador
porque todos los desarrolladores tendrán el mismo código y se evitarán problemas
indeseables relacionados con una mala configuración del entorno.

Q> **¿Que pasa si alguna dependencia se declara obsoleta y se borra de Packagist.org?**
Q>
Q> La posibilidad de que un paquete se remueva es mínima. Todos los paquetes
Q> son software libre y de código abierto, la comunidad de usuarios siempre
Q> puede restaurar la dependencia incluso si es borrada de Packagist.org.
Q> Por cierto el mismo concepto de instalación de dependencias se usa en
Q> GNU/Linux (¿recuerdas APT o RPM?), ¿viste algún paquete perdido en tu
Q> distribución de GNU/Linux?

Sin embargo hay ocasiones en las que *debemos* guardar alguna biblioteca de la
que depende nuestra aplicación dentro del control de versiones:

* Si tenemos que hacer algunos cambios en el código de terceros. Por ejemplo,
  asumiendo que tenemos que reparar un error en una biblioteca y no podemos esperar
  que el proveedor los repare por nosotros o no puede reparar el error. En este
  caso debemos colocar el código de la biblioteca dentro del control de versiones
  para asegurar que nuestras adaptaciones no se pierdan.

* Si hemos escrito un módulo o biblioteca reusable y queremos almacenarlo en la
  carpeta `vendor` sin publicarlo en *Packagist.org*. Como no es posible
  instalar este código desde *Packagist.org* lo guardaremos dentro
  del control de versiones.

* Si queremos garantizar que el 100% de una paquete de terceros no se pierda.
  A pesar de que este riesgo es mínimo, para algunas aplicaciones es crítico
  ser autónomas y no depender de un paquete que esta disponible en *Packagist.org*.

## Resumen

En este capítulo descargamos e instalamos el código del proyecto *Laminas Skeleton Application*
desde GitHub por medio del administrador de dependencias Composer. Configuramos
un Sitio Virtual de Apache para que el servidor web sepa donde esta ubicada
la carpeta raíz de documentos del sitio web.

La aplicación *skeleton* muestra la estructura de carpetas recomendada de un
sitio web típico. Tenemos la carpeta `public` que contiene los archivos que
son accesibles públicamente por los usuarios del sitio incluyendo el archivo
de punto de entrada `index.php`, los archivos CSS, los archivos JavaScript y
los archivos de imagen. Todas las otras carpetas de la aplicación son inaccesibles
para los usuarios del sitio y contienen la configuración de la aplicación,
datos y módulos.

En la segunda parte del capítulo discutimos sobre algunas configuraciones
avanzadas de Apache. Por ejemplo, podemos proteger nuestro sitio web con una
contraseña y permitir el acceso solo a una determinada dirección IP.

El administrador de dependencias Composer es una herramienta poderosa para
instalar las dependencias de nuestro sitio web. Por ejemplo, el propio
Laminas Framework se puede considerar como una dependencia. Todos los paquetes
que se pueden instalar usando Composer están registrados en el sitio web
Packagist.org que es un catalogo centralizado de paquetes.
