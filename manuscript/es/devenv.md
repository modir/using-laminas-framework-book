{backmatter}

# Apéndice A. Configurar el Entorno de Desarrollo Web {#devenv}

Aquí proveeremos instrucciones para preparar nuestro entorno para el desarrollo
de aplicaciones basadas en Laminas. Si ya tenemos configurado el entorno podemos
saltar este apéndice.

Configurar el entorno de desarrollo es la primera cosa que debemos hacer cuando
comenzamos a crear nuestro primer sitio web. Esta incluye instalar el servidor
web, el motor PHP con las extensiones necesarias y una base de datos.

Para poder ejecutar el código fuente de los ejemplos de este libro usaremos
el Servidor HTTP Apache (v.2.4 o superior), el motor PHP (v.5.6 o superior)
con la extensión XDebug y la base de datos MySQL (v.5.6 o superior).

Además, damos las instrucciones necesarias para instalar NetBeans IDE, que es un
entorno de desarrollo integrado conveniente para desarrollar con PHP. Permite
una fácil navegación, edición y detección de errores de nuestra aplicación.
NetBeans IDE esta escrito con Java y se puede instalar en Windows, GNU/Linux y en
otras plataformas que soporten una maquina Java compatible.

I> Además, ofrecemos indicaciones para los principiantes sobre como instalar
I> un sitio web hecho con Laminas en «Amazon EC2 cloud», todo esto en el
I> [Apéndice E. Instalar una Aplicación Web hecha con Laminas en Amazon EC2](#ec2-tutorial).

## Instalar Apache, PHP and MySQL en GNU/Linux

En general, es recomendable que usemos una distribución GNU/Linux popular y con
buen soporte de 32-bit (x86) o 64-bit (amd64). Una versión de 64-bit puede dar
un buen rendimiento pero tiene algunos inconvenientes como problemas con la
compatibilidad de los controladores. Los sistemas de 32-bit han estado con
nosotros por largo tiempo y tienen menos problemas, este hecho es importante para
los novatos.

Hay dos grandes familias de distribuciones GNU/Linux: [Debian](https://www.debian.org/)
y [Red Hat](https://www.redhat.com). Debian es un proyecto libre y de código abierto
que tiene varias ramas, la más popular de ellas es [GNU/Linux Ubuntu](http://www.ubuntu.com/).
Red Hat es un sistema operativo que se distribuye comercialmente, Red Hat tiene
ramas «libres» como [GNU/Linux CentOS](https://www.centos.org/) y
[GNU/Linux Fedora](https://getfedora.org/).

Red Hat GNU/Linux es desarrollador por Red Hat Inc. Red Hat GNU/Linux (o su
modificación «libre» CentOS) es conocido como un sistema operativo «corporativo».
Su principal ventaja es la «estabilidad» (baja proporción de caídas del sistema).
Sin embargo, esta estabilidad se alcanza con una cuidadosa selección del software
que se puede instalar luego de la instalación. Cuando instalamos un sistema operativo
como este con el objetivo de desarrollar con PHP, la «estabilidad» se puede
convertir en un problema porque solo tenemos a nuestra disposición viejas,
pero estables, versiones de PHP y de otros programas. Estos sistemas no incluyes
dentro de los repositorios el nuevo pero riesgoso software, así si queremos
instalar alguno necesitamos descargarlo, leer el manual y posiblemente, si no
tenemos suerte, copilarlo.

Hay otra distribución GNU/Linux que en mi opinión se ajusta mejor al desarrollo
con PHP. Su nombre es GNU/Linux Ubuntu. Ubuntu es desarrollado por Canonical Ltd.
GNU/Linux Ubuntu tiene dos ediciones: la edición de escritorio y la edición para
servidores. La versión para escritorio es una distribución que contienen entornos
gráficos, mientras que la versión para servidores solo tiene una consola de comandos.
Para desarrollar con PHP es recomendable usar la versión de escritorio.

Canonical generalmente publica una nueva versión de GNU/Linux Ubuntu cada 6
meses, en abril y octubre, y una versión con soporte extendido (LTS) cada 2
años. Por ejemplo, mientras se escribían estas líneas la última versión era
Ubuntu 16.04 Xenial Xerus LTS (publicada en abril de 2016).

Las versiones que no son LTS tienen un corto periodo de soporte (alrededor de 9
meses) pero tienen las versiones de PHP más actuales. Por otro lado, las versiones
LTS tiene un soporte más prolongado (5 años) pero con versiones de PHP un poco
desactualizadas.

Para desarrollar con PHP, el autor recomendaría usar la última versión de
«Ubuntu Desktop» porque tiene la versión más nueva de PHP y de otros programas
disponible en los repositorios. La desventaja de usar una versión como esta es
que necesitaremos actualizarla a la nueva versión cada 9 meses cuando el periodo
de soporte termina. Si no nos gusta la idea de actualizar cada 9 meses podemos
elegir la versión LTS.

De manera informativa la tabla A.1 tiene una lista con las versiones de PHP
disponibles que se pueden instalar desde los repositorios por cada versión
de la distribución:

{title="Tabla A.1. Versiones de PHP disponibles en Ubuntu"}
|------------------------------------------|----------------------------|
| Ubuntu Release                           |	PHP Version               |
|------------------------------------------|----------------------------|
| GNU/Linux Ubuntu 18.10 Cosmic Cuttlefish | 7.2                        |
|------------------------------------------|----------------------------|
| GNU/Linux Ubuntu 18.04 Bionic Beaver LTS | 7.2                        |
|------------------------------------------|----------------------------|
| GNU/Linux Ubuntu 17.10 Artful Aardvark   | 7.1                        |
|------------------------------------------|----------------------------|
| GNU/Linux Ubuntu 16.04 Xenial Xerus LTS  | 7.0                        |
|------------------------------------------|----------------------------|
| GNU/Linux Ubuntu 14.04 Trusty Tahr       | 5.5                        |
|------------------------------------------|----------------------------|

I> Como podemos ver en la tabla de arriba para desarrollar con Laminas necesitamos
I> instalar Ubuntu 18.04 o superior.

Cuando elegimos entre las versiones 32-bit y 64-bit del sistema operativo,
recordemos que la versión de 64 bit de GNU/Linux Ubuntu tiene más problemas
de compatibilidad que las versiones de 32-bit. El soporte para controladores
puede causar problemas sobre plataformas 64-bit.

T> Si no conoces GNU/Linux Ubuntu, puedes ver estos excelentes videos tutoriales
T> [LearnLinux.tv](https://www.youtube.com/channel/UCxQKHvKbmSzGMvUrVtJYnUA).
T> [Tutorial - Installing Ubuntu 16.04 LTS](https://www.youtube.com/watch?v=ajYMQ69S4pg)
T> que muestra como instalar la versión de Ubuntu para escritorio y
T> [Tutorial - Installing Ubuntu Server 16.04](https://www.youtube.com/watch?v=w5W_48vyC6U)
T> que muestra como instalar la versión para servidores de Ubuntu que solo tiene
T> una consola de comandos.

### Instalar Apache y PHP

En las distribuciones GNU/Linux modernas podemos fácilmente descargar e instalar
software usando el *repositorio* centralizado. El repositorio contiene *paquetes*.
Un paquete tiene un nombre (por ejemplo, `php`, `apache2`) y una versión.

En general podemos instalar un paquete con un solo comando. Sin embargo, el comando
y el nombre del paquete puede ser diferente dependiendo de la distribución de
GNU/Linux que usemos. Por ejemplo, para descargar e instalar paquetes en las
distribuciones GNU/Linux basadas en Debian como Ubuntu usamos la Herramienta
Avanzada de Empaquetado («Advanced Packaging Tool», APT). En las distribuciones
basadas en Red Hat como Fedora y CentOS usamos YUM («Yellowdog Updater, Modified»).
Abajo, damos instrucciones detalladas de instalación de paquetes para estos
sistemas operativos.

**Debian o Ubuntu**

Primero que todo, se recomienda actualizar el sistema instalando las últimas
actualizaciones disponibles. Para hacer esto, ejecutamos los siguientes comandos
desde la consola:

```
sudo apt-get update

sudo apt-get upgrade
```

Los comandos de arriba ejecutan la herramienta APT e instalan las actualizaciones
más nuevas de los paquetes del sistema. El comando `sudo` que significa
«Super User DO» permite ejecutar otros comandos, en este caso `apt-get`, como
administrador del sistema (root). Generalmente usamos `sudo` cuando necesitamos
escalar privilegios para instalar un paquete o editar algún archivo de
configuración.

T> El comando `sudo` pedirá nuestra contraseña. Cuando la consola interactiva
T> pida la contraseña escribimos la contraseña que usamos para ingresar al sistema
T> y presionamos Enter.

Luego, en la consola ejecutamos los siguientes comandos:

```
sudo apt-get install apache2

sudo apt-get install php

sudo apt-get install libapache2-mod-php
```

Los comandos de arriba descargan del repositorio e instalan las últimas versiones
disponibles del Servidor Apache HTTP, del motor de PHP y de la extensión de PHP
para el módulo de Apache.

I> Los comandos de arriba pedirán una confirmación para instalar los paquetes.
I> Debemos responder «Yes» presionado la letra `y` y luego Enter.

**Fedora, CentOS o Red Hat**

Primero que todo es recomendable actualizar el sistema instalando las últimas
actualizaciones disponibles. Para hacer esto, ejecutamos el siguiente comando
desde la consola de comandos:

```
sudo yum update
```

El comando de arriba ejecuta la herramienta YUM que instala las más nuevas
actualizaciones de los paquetes en el sistema.

Luego, desde la consola de comandos ejecutamos los siguientes comandos:

```
sudo yum install httpd

sudo yum install php
```

Los comandos de arriba descargan del repositorio e instalan en el sistema las
últimas versiones disponibles del Servidor Apache HTTP y del motor de PHP.

Luego, ejecutamos los siguientes comandos para agregar al Servidor Apache HTTP
al sistema de autoarranque y para iniciar su ejecución ejecución.

```
sudo chkconfig --level 235 httpd on

sudo service httpd start
```

### Revisar la Instalación del Servidor Web

Después de configurar el servidor Apache HTTP revisamos que está instalador
correctamente y que el servidor reconozca al motor PHP. Para hacer esto,
creamos el archivo *phpinfo.php* en la carpeta raíz de documentos de Apache.

El *raíz de documentos* («document root») es una carpeta donde podemos guardar
los archivos web. Generalmente, la carpeta raíz de documentos es */var/www/html*.

T> Para poder navegar la estructura de carpetas y editar los archivos es
T> recomendable instalar «Midnight Commander» (un gestor de archivos y editor de
T> texto). Para instalar «Midnight Commander» en Debian o Ubuntu, escribimos
T> el siguiente comando:
T>
T> `sudo apt-get install mc`
T>
T>  El siguiente comando instala Midnight Commander en Fedora, CentOS o Red Hat:
T>
T> `sudo yum install mc`
T>
T> Después de la instalación, podemos ejecutar el gestor de archivos con el
T> comando `mc` y editar un archivo de texto escribiendo algo como:
T>
T> `mcedit /path/to/file`
T>
T> Si necesitamos permisos administrativos para editar el archivo podemos añadir
T> al principio del comando de arriba el comando `sudo`.

En el archivo *phpinfo.php* agregamos el método de PHP `phpinfo()`:

~~~text
<?php
  phpinfo();
?>
~~~

Abrimos el archivo en nuestro navegador web. La página de información estándar
de PHP aparecerá en pantalla (ver figura A.1).

![Figura A.1. Información de PHP](../en/images/devenv/phpinfo.png)

### Editar la Configuración de PHP

Para ajustar PHP a nuestro entorno de desarrollo necesitamos editar el archivo
de configuración de PHP (*php.ini*) y cambiar algunos parámetros.

T> Dependiendo de la distribución de GNU/Linux el archivo de configuración de
T> PHP puede estar ubicado en diferentes rutas. Para editar el archivo de
T> configuración de PHP en Debian o Ubuntu escribimos el siguiente comando:
T>
T> `sudo mcedit /etc/php/7.0/apache2/php.ini`
T>
T> Escribimos el siguiente comando para editar el archivo *php.ini* en Fedora,
T> CentOS o Red Hat:
T>
T> `sudo mcedit /etc/php.ini`

En el entorno de desarrollo es recomendable colocar los siguientes parámetros
de gestión de errores y registro de sucesos que obligan a PHP a mostrar los
errores de las páginas PHP en la pantalla.

`error_reporting = E_ALL`

`display_errors = On`

`display_startup_errors = On`

T> Podemos buscar dentro del archivo, presionamos `F7` en la ventana del Midnight
T> Commander y escribimos la cadena de caracteres que queremos buscar (el nombre
T> del parámetro que estamos buscando).

Colocamos la configuración para la zona horaria, reemplazamos el comodín
`<your_time_zone>` por nuestra zona horaria, por ejemplo, UTC` oo `America/New_York`:

`date.timezone = <your_time_zone>`

Colocamos los parámetros `max_execution_time`, `upload_max_filesize` y
`post_max_size` para permitir la carga de grandes archivos mediante POST.
Por ejemplo, colocamos el parámetro `upload_max_filesize` en `128M` para
permitir la subida de archivos de hasta 128 megabytes de tamaño. Si colocamos
el valor 0 en el parámetro `max_execution_time` permitiremos la ejecución del
script de PHP indefinidamente.

`max_execution_time = 0`

`post_max_size = 128M`

`upload_max_filesize = 128M`

Cuando estemos listos guardamos los cambios presionando la tecla *F2* y luego
la tecla *F10* para salir del editor Midnight Commander.

### Reiniciar el Servidor Web Apache

Después de editar los archivos de configuración generalmente debemos reiniciar
el Servidor Apache HTTP para aplicar los cambios. Para hacer esto usamos el
siguiente comando (en Debian o Ubuntu):

`sudo service apache2 restart`

O el siguiente comando en Fedora, CentOS o Red Hat:

`sudo service httpd restart`

### Habilitar el Módulo mod_rewrite

Laminas Framework necesita tener el módulo *mod_rewrite* de Apache habilitado.
El módulo *mod_rewrite* se usa para reescribir las URLs de las peticiones en
base a algunas reglas, redirigiendo a los usuarios del sitio a otra URL.

**En Debian o Ubuntu**

Para habilitar el módulo `mod_rewrite` de Apache escribimos el siguiente comando:

`a2enmod rewrite`

Finalmente, reiniciamos el servidor web Apache para aplicar los cambios.

**En Fedora, CentOS o Red Hat**

En estas distribuciones de GNU/Linux no necesitamos hacer nada. en ellas el módulo
`mod_rewrite` está habilitado por defecto.

### Crear un Servidor Virtual en Apache

Laminas Framework necesita que creemos un *servidor virtual* para nuestro sitio
web. El termino servidor virtual significa que podemos ejecutar varios sitios
web en la misma computadora.

Lo servidores virtuales se diferencian por el nombre de dominio (como
*site.mydomain.com* and *site2.mydomain.com*). Cada sitio virtual tiene su propia
carpeta raíz de documentos, esto permite colocar nuestros archivos web en cualquier
lugar del sistema (no solo en la carpeta */var/www/html*).

T> Nótese que en este momento no necesitamos crear un servidor virtual, lo crearemos
T> en el capítulo [Laminas Skeleton Application](#skeleton). Por el momento solo
T> necesitamos tener una idea de como se crean los servidores virtuales en cada
T> una de las diferentes distribuciones de GNU/Linux.

**En Debian o Ubuntu**

Tenemos un ejemplo de un servidor virtual en */etc/apache2/sites-available/000-default.conf*
(ver abajo).

~~~text
<VirtualHost *:80>
	# The ServerName directive sets the request scheme, hostname and port that
	# the server uses to identify itself. This is used when creating
	# redirection URLs. In the context of virtual hosts, the ServerName
	# specifies what hostname must appear in the request's Host: header to
	# match this virtual host. For the default virtual host (this file) this
	# value is not decisive as it is used as a last resort host regardless.
	# However, you must set it for any further virtual host explicitly.
	#ServerName www.example.com

	ServerAdmin webmaster@localhost
	DocumentRoot /var/www/html

	# Available loglevels: trace8, ..., trace1, debug, info, notice, warn,
	# error, crit, alert, emerg.
	# It is also possible to configure the loglevel for particular
	# modules, e.g.
	#LogLevel info ssl:warn

	ErrorLog ${APACHE_LOG_DIR}/error.log
	CustomLog ${APACHE_LOG_DIR}/access.log combined

	# For most configuration files from conf-available/, which are
	# enabled or disabled at a global level, it is possible to
	# include a line for only one particular virtual host. For example the
	# following line enables the CGI configuration for this host only
	# after it has been globally disabled with "a2disconf".
	#Include conf-available/serve-cgi-bin.conf
</VirtualHost>

# vim: syntax=apache ts=4 sw=4 sts=4 sr noet
~~~

Cuando lo necesitemos, todo lo que debemos hacer es editar el archivo del sitio
virtual y reiniciar Apache para aplicar los cambios.

Además, cuando necesitemos otros sitios virtuales podemos copiar el archivo por
defecto y crear otro sitio virtual, de esta manera funcionarán en la misma
maquina varios sitios web. Por ejemplo, para crear otro servidor virtual con el
nombre de archivo *001-vhost2.conf*, escribimos lo siguiente en la consola de
comandos:

`cd /etc/apache2/sites-available`

`sudo cp 000-default.conf 001-vhost2.conf`

`sudo a2ensite 001-vhost2.conf`

I> El nombre del archivo para el servidor virtual comienza con un prefijo que
I> define su prioridad (como *000*, *010*, etc.). El servidor web Apache intenta
I> dirigir una petición HTTP a cada sitio virtual por turnos (primero a *000-default*,
I> luego a *001-vhost2*), si un servidor virtual no acepta la petición se intenta
I> con el siguiente.

**En Fedora, CentOS or Red Hat**

Hay un servidor virtual de ejemplo en el archivo */etc/httpd/conf/httpd.conf*.
En el archivo debemos bajar hasta el final donde se encuentra una sección llamada
*Virtual Host*. Podemos editar esta sección y reiniciar Apache para aplicar
los cambios.

### Installing XDebug PHP extension

Para poder detectar los errores en nuestro sitio web debemos instalar la extensión
*XDebug*. Esta extensión nos permite inspeccionar el programa en ejecución, con
él podemos ver las variables pasadas desde el cliente, recorrer la pila de llamadas
y analizar el rendimiento de nuestro código. Además, con XDebug podemos hacer
un análisis de cobertura de código.

**En Debian o Ubuntu**

Para instalar XDebug simplemente escribimos el siguiente comando:

`sudo apt-get install php-xdebug`

Luego, editamos el archivo `/etc/php/7.0/mods-available/xdebug.ini` escribiendo
el siguiente comando:

`sudo mcedit /etc/php/7.0/mods-available/xdebug.ini`

Agregamos las siguientes líneas al final del archivo (debemos reemplazar el
comodín `<remove_ip_address>` con la dirección IP del sitio web que vamos a
revisar):

~~~text
xdebug.remote_enable=1
xdebug.remote_handler=dbgp
xdebug.remote_mode=req
xdebug.remote_host=<remote_ip_address>
~~~

Finalmente, reiniciamos el servidor Apache para aplicar los cambios. Luego,
podemos abrir el archivo *phpinfo.php* en nuestro navegador web y buscar la
sección XDebug, esta sección tiene una apariencia como la que se muestra en la
figura A.2:

![Figure A.2. XDebug Information](../en/images/devenv/xdebug.png)

**En Fedora, CentOS o Red Hat**

En estas distribuciones de GNU/Linux, instalar XDebug es un poco más difícil.
Instalamos el paquete XDebug con el siguiente comando:

`yum install php-pecl-xdebug`

Luego de la instalación es necesario crear el archivo *xdebug.ini* en la
carpeta */etc/php.d/*:

`mcedit /etc/php.d/xdebug.ini`

Agregamos las siguientes líneas al final del archivo, sin olvidar reemplazar
el comodín con la dirección IP del sitio que vamos a revisar:

~~~text
[xdebug]

Laminas_extension = /usr/lib/php/modules/xdebug.so
xdebug.remote_enable=1
xdebug.remote_handler=dbgp
xdebug.remote_mode=req
xdebug.remote_host=<remote_ip_address>
xdebug.remote_port=9000
~~~

Por último, reiniciamos el servidor web Apache para aplicar los cambios. Luego,
revisamos el archivo *phpinfo.php* desde el navegador. Si la instalación se hizo
correctamente veremos la información relacionada con XDebug.

### Instalar el Servidor de Base de Datos MySQL

[MySQL](http://www.mysql.com/) es una sistema de gestión de bases de datos
relacionales libre y gratuito apoyado por Oracle. MySQL es el sistema de base
de datos más popular usando con PHP. En este libro usaremos MySQL.

**Debian o Ubuntu**

Para instalar la base de datos MySQL, escribimos los siguientes comandos:

~~~
sudo apt-get install mysql-server

sudo apt-get install mysql-client

sudo apt-get install php-mysql
~~~

Los comandos de arriba instalan respectivamente el servidor MySQL, el cliente
de MySQL y el módulo de extensión para PHP.

**Fedora, CentOS o Red Hat**

Para instalar la base de datos MySQL, escribimos los siguientes comandos:

~~~
sudo yum install mysql-server

sudo yum install mysql

sudo yum install php-mysql
~~~

Los comandos de arriba instalan respectivamente el servidor MySQL, el cliente
de MySQL y el módulo de extensión para PHP.

Con los comandos de abajo, primero, se agrega el servidor MySQL al inicio automático
del sistema y, segundo, se coloca en ejecución el servidor de base de datos.

~~~
sudo chkconfig --level 235 mysqld on

sudo service mysqld start
~~~

### Configuring the MySQL Database Server

During the installation of the MySQL server,
a *root* user is created. By default the root user has no password,
so you have to set it manually. You will need that password for creating
other MySQL database users.

To connect to the MySQL server enter the following command:

~~~
mysql -u root -p
~~~

The MySQL command prompt will appear. In the command prompt
enter the following command and press Enter (in the command
below, replace the `<your_password>` placeholder with some password):

~~~
SET PASSWORD FOR 'root'@'localhost' = '<insert_your_password>';
~~~

If the command is executed successfully, the following message is displayed:

`Query OK, 0 rows affected (0.00 sec)`

Now we need to create a new database that will
store the tables. To do this, type the following:

~~~
CREATE DATABASE test_db;
~~~

The command above creates empty schema that we will populate later.

Next, we want to create another database user named `test_user` that
will be used by the laminas-based web site for connecting to the database.
To create the user, type the following (in the command below,
replace the `<insert_your_password>` placeholder with some password):

```
GRANT ALL PRIVILEGES ON test_db.* TO 'test_user'@'localhost' IDENTIFIED BY '<insert_your_password>';
```

The command above creates the user named
'test_user' and grants the user all privileges on the
'test_db' database.

Finally, type `quit` to exit the MySQL prompt.

## Installing Apache, PHP and MySQL in Windows

We strongly recommend to use Linux for the purposes of PHP development. Most server systems have Linux
installed. If you are using Windows for your everyday tasks, you still can install Linux on a virtual machine
(for example, on [VirtualBox](https://www.virtualbox.org/)) and run Apache, PHP and MySQL on that virtual machine. If at the same time you would like to use
NetBeans in Windows, you can do that - just configure a shared directory (for example, set up Samba server on your
virtual machine).

In case you strongly wish to install Apache, PHP and MySQL in Windows (which we do not recommend), here
are some instructions (but note that installing those in Windows may be more difficult than in Linux).

There are a couple of most popular Apache + MySQL + PHP distributions:

 * [WampServer](http://www.wampserver.com/)
 * [XAMPP](https://www.apachefriends.org/ru/index.html)

Choose one and install it on your Windows server machine.

### Checking Web Server Installation

After you set up your web server, check that it is installed correctly
and that your Apache server recognizes the PHP engine.

To check that Apache and PHP are installed correctly, create *phpinfo.php*
file in Apache document root directory.

In the *phpinfo.php* file, enter the PHP method `phpinfo()` as follows:

~~~text
<?php
	phpinfo();
?>
~~~

Open the file in your browser. The standard PHP information page should display
(figure A.3).

![Figure A.3. PHP Information](../en/images/devenv/phpinfo_win32.png)

### Enabling Apache's mod_rewrite module

Laminas Framework requires that you have Apache's `mod_rewrite` module enabled.
To enable `mod_rewrite`, open your the Apache config file (typically *httpd.conf*), then find the following line:

~~~text
#LoadModule rewrite_module modules/mod_rewrite.so
~~~

and remove the hash (#) sign from the beginning to uncomment the line. It
should now look like this:

~~~text
LoadModule rewrite_module modules/mod_rewrite.so
~~~

Finally, restart Apache web server to apply your changes.

### Creating Apache Virtual Host

A virtual host term means that you can run several web-sites on the same machine.
The virtual sites are differentiated by domain name (like *site.mydomain.com*
and *site2.mydomain.com*)

Please consult to your WAMP or XAMPP documentation for information on how to create virtual hosts.

T> Right now, you don't need to edit virtual host file, we'll do that in chapter [Laminas Skeleton Application](#skeleton)
T> when installing the Hello World application. Now you just need to understand
T> how to create virtual hosts.

### Installing XDebug PHP extension

To be able to debug your web sites in NetBeans IDE, it is recommended that
you install the XDebug extension of your PHP engine. Download an appropriate
DLL from [this site](http://www.xdebug.org/download.php).

Then edit your *php.ini* file and add the following line:

~~~text
Laminas_extension="C:/path/to/your/xdebug.dll"
~~~

Add the following lines to the end of file (replace the remote IP address placeholder with the IP address
you plan to debug your website from):

~~~
xdebug.remote_enable=on
xdebug.remote_handler=dbgp
xdebug.remote_host=<remote_ip_address>
~~~

Finally, restart the Apache server to apply your changes.
Then open the *phpinfo.php* in your browser and look for XDebug section (it should look
like in the figure A.4):

![Figure A.4. XDebug Information](../en/images/devenv/xdebug_win32.png)

### Configuring the MySQL Database Server

Now we want to create a database schema and a database user. We will use
MySQL Command Line Client. Consult your WAMP or XAMPP documentation on how to do that.

The MySQL Command Line Client console looks like follows (see the figure A.5):

![Figure A.5. MySQL Command-Line Client](../en/images/devenv/mysql_command_line_client.png)

Now we need to create a new database that will
store the tables. To do this, type the following in the
MySQL client window:

`CREATE DATABASE test_db;`

The command above creates an empty database that we will populate later.
If the command is executed successfully, the following message is displayed:

`Query OK, 1 rows affected (0.05 sec)`

Next, we want to create another database user named `test_user`
that will be used by the web site for connecting
to the database. To create the user, type the following (in the
command below, replace the `<your_password>` placeholder with
some password):

`GRANT ALL PRIVILEGES ON test_db.* TO 'test_user'@'localhost' IDENTIFIED BY '<your_password>';`

The command above creates the user named `test_user` and
grants the user all privileges on the `test_db` database schema.

## Installing NetBeans IDE in Linux

You can install NetBeans IDE using two methods: either from repository, as you did with Apache,
PHP and MySQL, or by downloading the installer from NetBeans web site and running it. The first
method is simpler, so we recommend to use it.

To install NetBeans IDE in Debian or Linux Ubuntu, type the following command from your command shell:

`sudo apt-get install netbeans`

or the following command to install it in Fedora, CentOS or Red Hat Linux:

`sudo yum install netbeans`

The command above downloads from repository and installs NetBeans and all its
dependent packages. After the installation is complete, you can run netbeans
by typing:

`netbeans`

The NetBeans IDE window is shown in figure A.6.

![Figure A.6. NetBeans IDE](../en/images/devenv/netbeans_window.png)

To be able to create PHP projects, you need to activate PHP plugin for NetBeans. To do that,
open menu `Tools->Plugins`, the Plugins dialog appears. In the appeared dialog, click
*Settings* tab and set check marks to all *Update Centers* (see the figure A.7).

![Figure A.7. NetBeans Plugins](../en/images/devenv/netbeans_plugins_dialog.png)

Then click the *Available Plugins* tab. On that tab, click the *Check for Newest* button
to build the list of all available plugins. Then in the list, set check mark to PHP plugin
and click the Install button (see the figure A.8).

![Figure A.8. NetBeans Plugins](../en/images/devenv/netbeans_plugins_php.png)

When the PHP plugin installation is complete, restart the IDE.
Then you should be able to create new PHP projects
from menu `New->New Project...`.

T>It is also recommended to update NetBeans IDE to the latest version
by opening menu `Help->Check for updates`.

## Installing NetBeans IDE in Windows

Installing NetBeans in Windows is strightforward. You just need to download the installer
from [NetBeans site](https://netbeans.org/) and run it. You may encounter several bundles
of NetBeans available for download, and you should download the bundle that is
intended for PHP development (see the figure A.9 for example).

![Figure A.9. NetBeans PHP Download Page](../en/images/devenv/netbeans_download_page.png)

## Summary

In this appendix, we've provided instructions on how to install and configure
Apache HTTP Server, PHP engine and MySQL database in both Linux and Windows platforms.

We've also provided instructions for installing NetBeans integrated development environment (IDE),
which is a convenient integrated development environment for PHP development. It allows you
to navigate, edit and debug your laminas-based application in an effective manner.

Q> **These installation instructions do not work for me. What do I do?**
Q>
Q> Please leave a comment below this page and describe your problem.
