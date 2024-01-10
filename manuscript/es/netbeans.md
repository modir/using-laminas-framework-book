# Apéndice B. Introducción al Desarrollo PHP con NetBeans IDE {#netbeans}

En este libro usamos NetBeans IDE para desarrollar aplicaciones basadas en
Laminas Framework. En el [Apéndice A. Configurar el Entorno de Desarrollo Web](#devenv),
hemos instalado NetBeans IDE. Ahora, daremos algunos consejos útiles cuando se
usa NetBeans para la programación con PHP. Aprenderemos como usar la detección
interactiva de errores en sitios web basados en Laminas.

Q> *¿Que pasa si queremos usar otro IDE y no NetBeans para desarrollar las
Q> aplicaciones?*
Q>
Q> Bien, podemos usar el IDE que queramos. El problema es que es imposible cubrir
Q> todos los IDEs para desarrollar PHP en este libro. Solo proveemos instrucciones
Q> para NetBeans IDE. Para un aprendiz puede ser fácil usar NetBeans IDE. Los
Q> desarrolladores avanzados pueden usar el IDE de su elección.

## Configuración

Para poder ejecutar y hacer la detección de errores del sitio web, primero
necesitamos editar las propiedades del sitio. Para hacerlo, desde el panel
*Projects* de NetBeans hacemos clic derecho sobre el nombre del proyecto y en
el menú contextual seleccionar *Properties*. Luego, aparecerá el dialogo
*Properties* del proyecto (ver en la figura B.1).

![Figura B.1. Menú contextual «Properties» | *Sources*](../en/images/netbeans/properties_sources.png)

En el panel de la izquierda del dialogo que aparece hacemos clic en el nodo
*Sources*. En el panel de la derecha editamos el campo *Web Root* para que
apunte a la carpeta del sitio web *APP_DIR/public*. Podemos hacer esto haciendo
clic en el botón *Browse* que está a la derecha del campo. Luego, en el dialogo
hacemos clic sobre la carpeta *public* y finalmente sobre el botón *Select Folder*
(como se muestra en la figura B.2).

![Figura B.2. Dialogo de Búsqueda de Carpetas](../en/images/netbeans/properties_browse_folders.png)

Luego, hacemos clic en el nodo *Run Configuration* en el panel izquierdo. En el
panel derecho se mostrará las configuraciones de ejecución de nuestro sitio web
(figura B.3).

![Figure B.3. Menú contextual «Properties» | *Run Configuration*](../en/images/netbeans/properties_run_config.png)

En el panel derecho, podemos ver que la actual configuración es «default».
Podemos crear varias configuraciones de ejecución en forma de opciones.

Editamos los campos de la siguiente manera:

* En el campo *Run As*, seleccionamos «Local Website (running on local web server)».

* En el campo *Project URL*, escribimos «http://localhost». Si configuramos
  nuestro servidor virtual para escuchar en un puerto diferente (como por ejemplo
  el puerto 8080), escribimos el número de puerto de la manera siguiente
  «http://localhost:8080»

* Mantenemos el campo *Index File* vacío, porque el módulo *mod_rewrite* de
  Apache tomará el verdadero archivo *index.php*.

* En el campo *Arguments* podemos especificar que parámetros GET pasar a nuestro
  sitio web a través de la cadena URL. Generalmente dejamos este campo vacío.

Finalmente, hacemos clic en el botón *OK* para cerrar el dialogo *Properties*.

## Ejecutar el Sitio Web

Ejecutar el sitio web significa abrirlo en nuestro navegador web predeterminado.
Para lanzar el sitio web, presionamos el botón *Run* que esta en la *Run Toolbar*
(figura B.4). Alternativamente, podemos presionar el botón *F6* de nuestro
teclado.

![Figura B.4. *Run Toolbar*](../en/images/netbeans/launch_toolbar.png)

Si todo está bien con nuestra configuración de ejecución, el navegador web
predeterminado se ejecutará y en la ventana del navegador que se abre podremos
ver la página *Home* del sitio web.

Podríamos conseguir el mismo efecto escribiendo «http://localhost/» en el
navegador web, pero la barra de ejecución de NetBeans permite hacerlo con un
solo clic.

## Detección de Errores con NetBeans

La técnica «convencional» para detectar errores en PHP es colocando la función
`var_dump()` en el bloque de código que queremos examinar:

~~~php
var_dump($var);

exit;
~~~

Las líneas de arriba imprimirá el valor de la variable `$var` en el navegador
y detendrá la ejecución del programa. Aunque, esto se puede usar para detectar
errores, incluso en sitios complejos, esta manera hace pesada la detección de
errores, porque tenemos que escribir el comando *dump* con la variable en nuestro
archivo fuente de PHP y luego refrescar nuestra pagina web en el navegador para
ver la salida, luego editamos de nuevo el archivo fuente hasta determinar el
origen del problema.

En contraste, cuando detectamos errores con NetBeans IDE, el interprete de PHP
detiene el flujo de ejecución del programa en cada línea donde colocamos un
punto de control, «breakpoint». De esta manera es posible recuperar información
sobre el estado actual del programa, como los valores de las variables locales
y la pila de llamadas. Veremos la información en la ventana de NetBeans de una
forma gráfica.

W> Para poder hacer la detección de errores en el sitio necesitamos tener la
W> extensión XDebug instalada. Si no la hemos instalado aún podemos revisar
W> el [Apéndice A. Configuración del Entorno de Desarrollo Web](#devenv)
W> para ver las instrucciones de instalación.

Para comenzar la detección de errores de la sesión, desde la ventana de NetBeans,
hacemos clic en el botón *Debug* de la *Run Toolbar* (figura B.4). Alternativamente,
podemos presionar en combinación las teclas *CTRL+F5* en el teclado.

Si todo está bien, deberíamos ser capaces de ver el contador actual del programa
sobre la primera línea de código del archivo *index.php* (ver figura B.5):

![Figura B.5. *Debugging Session*](../en/images/netbeans/debug_session.png)

Mientras el programa está en pausa, la ventana de navegación estará congelada
porque el navegador estará esperando por datos desde del servidor web. Una
vez que la ejecución del programa continua, el navegador recibe los datos y
muestra la página web.

## Barra de Detección de Fallos «Debug Toolbar»

Podemos reanudar/suspender la ejecución del programa desde la *Debug Toolbar*
(ver figura B.6):

![Figura B.6. Barra de Detección de Fallos](../en/images/netbeans/debug_toolbar.png)

El botón *Finish Debugger Session* de la barra de herramientas permite detener
la detección de fallas. Presionamos este botón cuando terminamos la búsqueda de
errores en el programa. Podemos lograr lo mismo presionado las teclas *SHIFT+F5*.

Hacemos clic en el botón *Continue* (o presionamos *F5*) para continuar con la
ejecución del programa hasta el próximo punto de control o hasta el fin del
programa si no hay más puntos de control.

El botón en la barra de herramientas *Step Over* (or *F8*) mueve la posición
actual del indicador programa a la siguiente línea del programa.

El botón de la barra de herramientas *Step Into* (o *F7*) mueve la posición
actual del indicador del programa a la línea siguiente del programa y si es una
función marcada como punto de control entra en el cuerpo de la función. Usamos
esto cuando queremos investigar nuestro código a profundidad.

El botón *Step Out* de la barra de herramientas (*CTRL+F7*) permite continuar
la ejecución del programa hasta el regreso de la función actual.

El botón *Run to Cursor* (*F4*) permite continuar la ejecución del programa
hasta la línea de código donde colocamos el cursor. Esto puede ser conveniente
si queremos saltar algún bloque de código y detener la ejecución hasta la
determinada línea del programa.

## Puntos de Control («Breakpoints»)

Generalmente, colocamos uno o varios puntos de control en las líneas que queremos
examinar en el modo paso-por-paso. Para colocar un punto de control, colocamos
el cursor a la izquierda de la línea de código que queremos señalar como un
punto de control y hacemos clic en el número de la línea. También, podemos
colocar el cursor en la línea donde queremos colocar el punto de control y
presionar las teclas *CTRL+F8*.

Cuando colocamos un punto de control la línea se marca con color rojo y un pequeño
rectángulo rojo se coloca al lado izquierdo (ver figura B.7):

![Figura B.7. Colocar un punto de control](../en/images/netbeans/breakpoint.png)

T> Debemos ser cuidadosos de no colocar un punto de control en una línea vacía
T> o sobre un comentario. En este caso el punto de control será ignorado por
T> XDebug y será marcado con un cuadrado roto (ver figura B.8):

![Figura B.8. Punto de control inactivo](../en/images/netbeans/breakpoint_on_comment.png)

Podemos viajar entre puntos de control presionando la tecla *F5*. Este botón
continua la ejecución del programa hasta llegar al siguiente punto de control.
Una vez que el programa llega al siguiente punto de control, el interprete de
PHP se pausa y podemos ver el estado del programa.

Podemos ver la lista completa de los puntos de control que hemos fijado en la
ventana *Breakpoints* (ver figura B.9). La ventana *Breakpoints* está ubicada
en la parte baja de la ventana de NetBeans. En esta ventana podemos agregar
nuevos puntos de control o quitar los puntos de control que se fijaron antes.

![Figura B.9. Ventana Breakpoints](../en/images/netbeans/breakpoints_window.png)

## Examinar Variables

Cuando el interprete PHP está en pausa podemos ver los valores de las variables
PHP. Una manera simple de ir hasta una variable es colocando el cursor del ratón
sobre el nombre de la variable dentro del código y esperar por un segundo. Si
el valor de la variable se puede evaluar ella se mostrará dentro de una pequeña
ventana emergente.

Otra manera de ver las variables is por medio de la ventana *Variables* (como
se muestra en la figura B.10), que se muestra en la parte baja de la ventana
de NetBeans. La ventana *Variables* tiene tres columnas: *Name*, *Type* y *Value*.

![Figura B.10. Ventana *Variables*](../en/images/netbeans/variables_window.png)

Tendremos tres tipos de variables: *super globals*, *locals* y *$this*.

* *Super global* son variables especiales de PHP como `$_GET`, `$_POST`,
  `$_SERVER`, `$_COOKIES`, etc. Ellas generalmente contienen información del
  servidor y parámetros que son pasados por el navegador web como parte de la
  petición HTTP.

* *Local* son variables que «tienen vida» en el alcance de la función actual
  (o métodos de clase). Por ejemplo, en la aplicación «Hello World», si colocamos
  un punto de control dentro de la función `IndexController::aboutAction()`,
  la variable `$appName` será una variable local.

* La variable *$this* apunta a la instancia de la clase actual, si el código
  actual se está ejecutando en el contexto de una clase PHP.

Algunas variables se pueden «expandir» (para expandir una variable, necesitamos
hacer clic sobre el triangulo que está al lado del nombre de la variable).
Por ejemplo, haciendo clic y expandiendo la variable *$this* podemos ver todos
los campos de la instancia de la clase. Si expandimos una variable de tipo
arreglo, seremos capaces de ver los elementos del arreglo.

Usando la ventana *Variables* no solo es posible ver los valores de las variables
sino también cambiar su valor mientras se hace la detección de fallas. Para
hacer esto, colocamos el cursor del ratón sobre la columna *value* y hacemos
clic sobre la celda correspondiente. Luego aparece la caja de edición, donde
podemos asignar el nuevo valor de la variable.

## Pila de Llamadas

La pila de llamadas, «call stack», muestra la lista de funciones anidadas que
están siendo ejecutadas (ver la figura B.11). Cada línea de la pila de llamadas
(también llamada «stack frame») contiene el nombre completo de la clase, el nombre
del método dentro de la clase y el número de línea. Moviéndonos hacia abajo
de la pila, podemos entender mejor el actual estado de ejecución del programa.

![Figura B.11. Ventana «Call Stack»](../en/images/netbeans/call_stack_window.png)

Por ejemplo, en la figura B.11, podemos ver que actualmente el método
`IndexController::aboutAction()` se está ejecutando, y este método fue a su vez
llamado por el método `AbstractActionController::onDispatch()`. Podemos «caminar»
a través de la pila de llamadas hasta llegar al archivo *index.php*, que está
en la base de la pila. Además, podemos hacer clic en un «stack frame» para
ver el lugar del código que está siendo ejecutado actualmente.

## Opciones de Detección de Fallas

NetBeans nos permite configurar algunos aspectos del comportamiento de detector
de fallas. Para abrir el dialogo *Options*, seleccionamos el menú *Tools->Options*.
En el dialogo que aparece, hacemos clic en la pestaña *PHP* y dentro de esta
pestaña seleccionamos la sub-pestaña *Debugging* (figura B.12).

![Figura B.12. Opciones del detector de fallas de PHP](../en/images/netbeans/options_php_debugging.png)

Generalmente no cambiamos la mayoría de estas opciones, por ahora solo necesitamos
tener una idea sobre que hacen estas opciones. Estas son las opciones de
detección de fallas:

* Los parámetros *Debugger Port* y *Session ID* definen como NetBeans se conecta
  con XDebug. Por defecto, el número de puerto es 9000. El número de puerto
  debe ser el mismo que el asignado al puerto de detección de fallas en el
  archivo *php.ini* cuando se instala XDebug. El nombre de sesión es por defecto
  «netbeans-xdebug». Generalmente no cambiamos este valor.

* El parámetro *Stop at First Line* hace que el detector de fallas se detenga
  en la primera línea de nuestro archivo *index.php*, en lugar de parar en el
  primer punto de control. Esto puede ser incomodo, por lo que podríamos quitar
  está opción.

* El grupo de opciones *Watches and Balloon Evaluation* están desactivados por
  defecto, porque estas pueden causar el fallo de XDebug. Podemos habilitar
  estas opciones solo cuando conocemos lo que estamos haciendo.

    * El parámetro *Maximum Depth of Structures* indica si las estructuras
      anidadas (arreglos anidados, objetos dentro de objetos, etc.) serán o no
      visibles. Por defecto, la profundidad se coloca en 3.

    * La opción *Maximum Number of Children* define cuantos elementos de un
      arreglo se mostraran en la ventana *Variables*. Si colocamos esta opción en
      30, por ejemplo, veremos solo los primeros 30 elementos cuando el arreglo
      tiene más de 30 elementos.

* La opción *Show Requested URLs*, si está habilitada, muestra la URL que está
  siendo procesada actualmente. Esta opción imprime la URL en la ventana *Output*.

* La opción *Debugger Console*  permite ver la salida de los script de PHP que
  se están examinando. La salida se muestra en la ventana *Output*. Si planeamos
  usar está característica, es recomendable agregar el parámetro
  `output_buffering = Off` dentro de la sección `[xdebug]` del archivo *php.ini*,
  de lo contrario la salida puede aparecer con retardo.

## Análisis de Rendimiento

Cuando nuestro sitio ya esta listo y trabajando, generalmente comenzamos a
interesarnos en la forma de hacerlo tan rápido como sea posible. XDebug permite
analizar el rendimiento, «profile», de nuestro sitio. El análisis de rendimiento
permite determinar cuales métodos de un clase (o funciones) se ejecutan por más
tiempo. Esto permite determinar los «cuellos de botella» en nuestro código e
identificar los problemas de rendimiento.

Por cada petición HTTP la extensión XDebug mide la cantidad de tiempo que una
función toma en su ejecución y escribe esta información en una archivo. Generalmente,
los archivos de análisis de rendimiento se almacenan dentro de la carpeta temporal
del sistema (la carpeta *\tmp* en GNU/Linux) y tienen nombres como `xdebug.out.<timestamp>`,
donde `<timestamp>` es la marca de tiempo de la petición HTTP. Todo lo que tenemos
que hacer es abrir el archivo y analizarlo.

W> Para habilitar el analizador de XDebug, debemos cambiar el siguiente parámetro
W> de configuración en el archivo *xdebug.ini*:
W>
W> `xdebug.profiler_enable = 1`

Desafortunadamente, NetBeans para PHP no incluye una herramienta para ver los
resultados del análisis. Por esta razón debemos instalar una herramienta de
visualización. Abajo, daremos instrucciones de como instalar una herramienta
web simple llamada [Webgrind](https://github.com/jokkedk/webgrind). Webgrind
puede trabajar sobre cualquier plataforma, la propia herramienta está escrita
con PHP.

La instalación de Webgrind es sencillo.

Primero, necesitamos descargar webgrind de la página del proyecto y desempaquetarlo
en alguna carpeta. En GNU/Linux, podemos hacer esto con los siguientes comandos:

{lang="bash"}
~~~
$ cd ~

$ wget https://github.com/jokkedk/webgrind/archive/master.zip

$ unzip master.zip
~~~

El primer comando de arriba cambiará la carpeta de trabajo actual a nuestra
carpeta *home*, luego descargaremos el archivo Webgrind desde internet y finalmente
desempaquetamos el archivo.

Luego, necesitamos decirle al servidor web Apache donde encontrar los archivos
de Webgrind. Esto significa que necesitamos configurar un sitio virtual.
Ya hemos mostrado qué es un sitio virtual en el
[Apéndice A. Configuración del Entorno de Desarrollo Web](#devenv). No olvidemos
reiniciar el servidor web Apache después de tener configurado el sitio virtual.

Finalmente, abrimos Webgrind en nuestro navegador web colocando la URL de la
instalación de Webgrind. Por ejemplo, si configuramos el sitio virtual para
escuchar en el puerto 8080, escribimos en la barra de navegación del navegador
web «http://localhost:8080» y presionamos Enter. La página web de Webgrind
aparecerá (ver figura B.13):

![Figura B.13. Página Webgrind](../en/images/netbeans/webgrind.png)

En la cabecera de la página de Webgrind podemos seleccionar el porcentaje de las
funciones llamadas en razón de su peso (figura B.14).
Por defecto está configurado en 90%. Si configuramos un porcentaje bajo
se ocultarán las funciones que son llamadas pocas veces.

![Figure B.14. Selección del Porcentaje Webgrind](../en/images/netbeans/webgrind-select.png)

La lista desplegable que está a la derecha del campo de porcentaje nos permite
seleccionar el archivo de datos que se someterá a análisis. Por defecto, se coloca
«Auto (newest)» que obliga a Webgrind a usar el archivo con la marca de tiempo
más reciente. Podríamos necesitar seleccionar otra archivo, por ejemplo, en
el caso de que nuestra página web usara peticiones AJAX asíncronas.

La lista desplegable que está mas a la derecha permite asignar la unidad de medida
que se usará para medir los datos. Las opciones posibles son: porcentaje
(por defecto), mili-segundos y micro-segundos.

Si seleccionamos el porcentaje, el nombre del archivo, la unidad de medida y
hacemos clic en el botón *Update* veremos la visualización de los datos hecha
por Webgrind (el cálculo puede tardar algunos segundos). Cuando termine el
cálculo seremos capaces de ver una tabla con la lista de las llamadas a
funciones ordenadas de manera descendente o por el «peso» de la función.
Las funciones más pesadas serán mostradas en la parte superior.

La tabla tiene las siguientes columnas:

* La primera columna (*Function*) muestra el nombre de la clase seguido por el
  nombre del método (en caso de la llamada a un método) o el nombre de la
  función (en caso de una función regular).

* La segunda columna contiene los iconos de «párrafo». Si hacemos clic en ellos
  podemos ver el código fuente PHP de la función correspondiente.

* La columna *Invocation Count* muestra el número de veces que fue llamada la
  función.

* La columna *Total Self Cost* muestra el tiempo total que toma la ejecución
  del código interno de PHP para la función listada
  (excluyendo el tiempo gastado en la ejecución de las otras funciones no estándar).

* La columna *Total Inclusive Cost* contiene el tiempo total de ejecución para
  la función, que incluye el código interno de PHP y las funciones de usuario.

Haciendo clic en el encabezado de una columna podemos ordenar los datos de manera
ascendente o descendente.

Podemos hacer clic en el triangulo que está al lado del nombre de la función
para expandir la lista de invocaciones a la función. Esta lista permite ver quien
llamo a esta función y que cantidad de tiempo consumió, la lista tiene las
siguientes columnas:

* *Calls* es la función o clase «madre» que invoca a esta función (hija).
* *Total Call Cost* es el tiempo total de ejecución de la función cuando es
  llamada desde la función madre.
* *Count* es el número de veces en que la madre llama a la función hija.

La barra coloreada en la parte superior de la página muestra la proporción de
los diferentes tipos de función:

* *Azul* denota las funciones internas de PHP.
* *Gris* es el tiempo que toma incluir, «include» o «require», los archivos PHP.
* *Verde* muestra la proporción de nuestros métodos de clase.
* *Naranja* denota el tiempo que toman las funciones «procedimentales» (funciones
  que no son parte de las clases PHP).

T> Observemos que el análisis crea un nuevo archivo de datos en la carpeta */tmp* para
T> cada petición HTTP a nuestro sitio web. Esto puede causar el agotamiento del
T> espacio en disco. Por esta razón, cuando hemos terminado de generar el análisis
T> de rendimiento de la aplicación es recomendable desactivarlo
T> editando el archivo *php.ini* comentando el parámetro `xdebug.profiler_enable`
T> de la siguiente manera y luego reiniciar el servidor web Apache.
T>
T> `;xdebug.profiler_enable = 0`
T>

## Resumen

En este apéndice hemos aprendido como usar NetBeans IDE para ejecutar el sitio
web y detectar errores paso a paso desde el modo interactivo. Para ser capaces
de ejecutar un sitio web, primero necesitamos editar las propiedades del sitio.

Para detectar errores en el sitio necesitamos tener la extensión de PHP XDebug
instalada. Cuando detectamos errores con NetBeans el motor PHP detiene la
ejecución del programa en cada línea donde colocamos un punto de control. Vemos
la información de detección de fallas (como variables locales y la pila de
llamadas) en la venta de NetBeans de una forma gráfica.

Junto con la detección de fallas, además, la extensión XDebug permite analizar
el rendimiento del sitio web. Con el análisis podremos ver cuanto tiempo fue
consumido en la ejecución de una función o método de clase. Esto permite
determinar los «cuellos de botella» y problemas de rendimiento.
