# Apariencia y Diseño de la Página {#appearance}

En este capítulo aprenderemos como hacer las páginas de nuestro sitio web
atractivas y con aspecto profesional con la ayuda de "Twitter Bootstrap CSS Framework"
y como posicionar elementos sobre una página usando el mecanismo de diseño
de Laminas. Además, nos familiarizaremos con los ayudantes de vista más comunes
para componer páginas web con partes reusables. Si somos nuevos con Twitter
Bootstrap es bueno revisar el [Apéndice C. Introducción a Twitter Bootstrap](#bootstrap)
para una descripción avanzada de las capacidades de Bootstrap.

Laminas components covered in this chapter:

|--------------------------------|---------------------------------------------------------------|
| *Componente*                   | *Descripción*                                                 |
|--------------------------------|---------------------------------------------------------------|
| `Laminas\Mvc`                     | Soporta el patrón MVC. Implementa clases controladoras        |
|                                | básicas, complementos para controladores, etc.                |
|--------------------------------|---------------------------------------------------------------|
| `Laminas\View`                    | Implementa la funcionalidad para los contenedores de          |
|                                | variables, imprimir una página web y ayudantes de vista de uso|
|                                | común.                                                        |
|--------------------------------|---------------------------------------------------------------|

## Sobre las Hojas de Estilo y Twitter Bootstrap

En un sitio web basado en Laminas se usan las hojas de estilo CSS para definir la
apariencia visual y el estilo de las páginas web. Los archivos CSS [^css] se
guardan generalmente en el directorio *APP_DIR/public/css*.

[^css]: Si somos nuevos en el uso de CSS podemos revisar el excelente tutorial
        de W3Schools visitando [este enlace](http://w3schools.com/).

Como las reglas CSS puede llegar a ser complejas y necesitar laboriosos ajustes,
además, de la experiencia de un diseñador se pueden separar en un "biblioteca"
(framework). Análogamente a los frameworks PHP los frameworks CSS permiten
la reutilización del código.

Hoy en día existen varios frameworks CSS en el mercado y uno de ellos es
[Twitter Bootstrap](http://getbootstrap.com/) (o simplemente Bootstrap).
Originalmente fue diseñado por Twitter para unificar la apariencia de sus
propias herramientas web. Con el tiempo Bootstrap se ha convertido en un
framework CSS popular que permite dar a nuestro sitio web una apariencia
profesional y atractivo visual incluso si no tenemos habilidades avanzadas
de diseñador y sin la necesidad de crear reglas básicas CSS (aunque por supuesto
podemos definir nuestras propias reglas CSS por sobre Bootstrap para personalizar
la apariencia de nuestro sitio web). Bootstrap es software libre que se
distribuye bajo la licencia
[Apache License v.2.0](http://www.apache.org/licenses/LICENSE-2.0.html).

T> Twitter Bootstrap viene empacado con *Laminas Skeleton Application* así que
T> podemos usarlos luego de terminar la instalación de Laminas, apenas al sacarlo
T> de la caja! Además, podemos descargar la versión más reciente de Bootstrap
T> desde la [página web](http://getbootstrap.com/) oficial del proyecto.
T> En el momento de escribir este libro la última versión es la número 3.x.

En general Bootstrap hace las siguientes cosas:

* Provee el *CSS reset* que es una hoja de estilo que define los estilos para
  todos los posibles elementos HTML. Con esto aseguramos que el sitio web se
  vea de la misma manera en todos los navegadores webs.

* Provee las *reglas CSS básicas* que definen el estilo de la tipografía
  (encabezado y texto), tablas, formularios, botones, imágenes, etc.

* Define el *grid system*. El sistema de rejillas permite ordenar los elementos
  de nuestra página web en una estructura que se asemeja a las rejillas. Por
  ejemplo, revisemos la página principal de la *Skeleton Application* (figura 6.1),
  donde tendemos una estructura de rejillas que consiste en tres columnas.

* Define útiles *componentes para la interfaz web* como menús desplegables,
  barras de navegación, migas de pan, paginación, etc. Por ejemplo, en la
  página principal de la aplicación esqueleto tenemos hay varios componentes.
  En la parte de arriba está la barra de navegación y debajo de ella está el encabezado
  (también llamado *Hero Unit* o *Jumbotron*). Estos componentes son muy útiles
  para cualquier sitio web.

* Incluye *extensiones de JavaScript* que permiten que los componentes de
  Bootstrap sean más interactivos. Por ejemplo, JavaScript es usado para animar
  a los menús desplegables y para mostrar "diálogos modales" (modal dialogs).

![Figura 6.1. Página principal de la aplicación esqueleto y su diseño](../en/images/appearance/skeleton-layout.png)

T> Si somo nuevos nuevos en Twitter Bootstrap es recomendables que revisemos
T> el [Apéndice C. Introducción a Twitter Bootstrap](#bootstrap), en donde
T> podemos encontrar más información sobre el uso de Twitter Bootstrap y
T> sus componentes.

## Diseño de Páginas en Laminas Framework

Las páginas de nuestro sitio web generalmente tiene una estructura común que
se puede compartir entre ellas. Por ejemplo, una página típica tiene generalmente
la declaración `<!DOCTYPE>` que identifica al documento HTML y los elementos
`<head>` y `<body>`:

{line-numbers=off, lang=html, title="Typical page structure"}
~~~
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Welcome</title>
    <!-- Include metas, stylesheets and scripts here -->
  </head>
  <body>
    <!-- Include page content here -->
  </body>
</html>
~~~

El elemento `<head>` contiene el título de la página, meta información y
referencias a las hojas de estilo y scripts que se incluyen. El elemento `<body>`
contiene el contenido de la página como el logo, la barra de navegación, el
texto de la página y el pie de página con la información de derechos de autor.

En Laminas Framework definimos esta estructura común con una plantilla de vista
"maestra" llamada la *maqueta*. El maqueta "adorna" a las otras plantillas.

La plantilla de maqueta generalmente tiene una *palabra clave o comodín* en la
que Laminas coloca el contenido específico de una página en particular (ver figura
6.2 para un ejemplo).

![Figura 6.2. Ubicación del comodín en la plantilla de maqueta](../en/images/appearance/layout_placeholder.png)

En la Skeleton Application el archivo por defecto de la plantilla de maqueta
se llama *layout.html* y está ubicada dentro de la carpeta *view/layout* en
la carpeta del módulo *Application* (para un ejemplo ver la figura 6.3).

![Figura 6.3. Directorio de la Maqueta](../en/images/appearance/layout_dir.png)

Vamos a ver con más detalles el archivo de plantilla *layout.phtml*. Abajo se
presenta el contenido completo del archivo:

~~~php
<?= $this->doctype() ?>

<html lang="en">
  <head>
    <meta charset="utf-8">
    <?= $this->headTitle('ZF Skeleton Application')
             ->setSeparator(' - ')->setAutoEscape(false) ?>

    <?= $this->headMeta()
          ->appendName('viewport', 'width=device-width, initial-scale=1.0')
          ->appendHttpEquiv('X-UA-Compatible', 'IE=edge')
    ?>

    <!-- Le styles -->
    <?= $this->headLink(['rel' => 'shortcut icon', 'type' => 'image/vnd.microsoft.icon',
                         'href' => $this->basePath() . '/img/favicon.ico'])
        ->prependStylesheet($this->basePath('css/style.css'))
        ->prependStylesheet($this->basePath('css/bootstrap-theme.min.css'))
        ->prependStylesheet($this->basePath('css/bootstrap.min.css'))
    ?>

    <!-- Scripts -->
    <?= $this->headScript()
        ->prependFile($this->basePath('js/bootstrap.min.js'))
        ->prependFile($this->basePath('js/jquery-2.2.4.min.js'))
    ?>
    </head>
    <body>
      <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse"
                    data-target=".navbar-collapse">
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="<?php echo $this->url('home') ?>">
              <img src="<?= $this->basePath('img/zf-logo.png') ?>"
                   alt="Laminas Framework <?= \Application\Module::VERSION ?>"/>
                   &nbsp;Skeleton Application
            </a>
          </div>
          <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
              <li class="active">
                <a href="<?= $this->url('home') ?>">Home</a>
              </li>
            </ul>
          </div>
        </div>
      </nav>
      <div class="container">
        <?= $this->content; ?>
        <hr>
        <footer>
          <p>&copy; 2005 - <?= date('Y') ?> by Laminas Technologies Ltd.
            All rights reserved.
          </p>
        </footer>
      </div>
      <?= $this->inlineScript() ?>
  </body>
</html>
~~~

Como podemos ver el archivo *layout.phtml* (que es una usual plantilla de vista)
consiste en etiquetas HTML con fragmentos de código PHP. Cuando una plantilla se
comienza a imprimir Laminas evalúa los fragmentos de código PHP en linea y genera
la página HTML final que verán los usuarios del sitio.

Arriba en la línea 1 se genera la declaración `<!DOCTYPE>` [^doctype] de la
página HTML con el ayudante de vista `Doctype`.

[^doctype]: La declaración `<!DOCTYPE>` va primera en un documento HTML antes
            de la etiqueta `<html>`. Esta declaración le dice al navegador
            web con que versión de HTML se escribió la página (en nuestro sitio
            web usamos la declaración de tipo de documento HTML5-conformant).

La línea 3 define el elemento `<html>` que representa la raíz del documento
HTML. A la etiqueta `<html>` le sigue `<head>` (línea 4) que generalmente
contiene el título del documento y puede incluir otra información como scripts,
estilos CSS y meta información.

En la línea 5 la etiqueta `<meta>` le provee al navegador la guía de que
el documento está codificado usando la codificación de caracteres UTF-8 [^utf8].

[^utf8]: UTF-8 permite codificar cualquier carácter de cualquier alfabeto del
         mundo, esta es la razón por la que se recomiende para codificar las
         páginas web.

En la línea 6 tenemos el ayudante de vista `HeadTitle` que permite definir el
título de la página ("ZF Skeleton Application"). El título se mostrará en la
cabecera del navegador web. El método `setSeparator()` se usa para definir el
carácter de separación para los títulos de página compuestos[^compound];
el método `setAutoEscape()` aumenta la seguridad al escapar los caracteres del
título de la página.

[^compound]: Un título de página compuesto consiste en dos partes: la primera
             parte ("ZF Skleton Application") se define en la maqueta y la
             segunda parte, que es definida por cada página en particular, se
             prefija a la primera parte. Por ejemplo, para la página *Acerca de*
             de nuestro sitio tendremos el título "Acerca de - ZF Skeleton Application",
             y para la página de documentación tendremos algo como
             "Documentación - ZF Skeleton Application".

En la línea 9 el ayudante de vista `HeadMeta` permite definir la etiqueta
`<meta name="viewport">` que contiene meta información para que el navegador web
controle la maqueta en relación con las diferentes pantallas de los dispositivos,
incluyendo dispositivos móviles. La propiedad `width` controla el tamaño de la
página (viewport) mientras que la propiedad `initial-scale` controla el zoom
con que la página es cargada. Esto hace a la maqueta de la página web "adaptable"
(responsive) al tamaña del dispositivo.

En la línea 15 el ayudante de vista `HeadLink` permite definir las etiquetas
`<link>`. Con las etiquetas `<link>` generalmente definimos el "favicon" de la
página (archivo ubicado en `APP_DATA/public/img/favicon.ico`) y las hojas de
estilo CSS.

En las líneas 17-19 las hojas de estilo comunes a todas las páginas del sitio
se incluyen con el método `prependStylesheet()` del ayudante de vista `HeadLink`.
Toda página de nuestro sitio web cargará tres archivos de hoja de estilo CSS:
*bootstrap.min.css* (la versión miniaturizada del framework de CSS de Twitter
Bootstrap), *bootstrap-theme.min.css* (la miniaturización de las hojas de estilo
de temas de Bootstrap) y *style.css* (archivo CSS que nos permite definir
nuestras propias reglas sobrescribiendo las reglas CSS de Bootstrap).

En las líneas 23-25 se incluyen los archivos JavaScript que se cargarán en todas
las páginas web. Los scripts se ejecutan en el navegador web del cliente
permitiendo agregar algunas características interactivas a nuestras páginas.
Usamos los scripts `bootstrap.min.js` (versión miniaturizada de Twitter Bootstrap)
y `jquery-2.2.4.min.js` (versión miniaturizada de la biblioteca jQuery). Todos
los scripts se ubican en la carpeta *APP_DIR/public/js*.

La línea 28 define la etiqueta `<body>`, el cuerpo del documento que contiene
toda el contenido del documento como la barra de navegación, texto, hiperenlaces,
imágenes, tablas, listas, etc.

En las líneas 29-52 podemos reconocer la definición de Bootstrap para una barra
de navegación. La aplicación esqueleto usa la barra de navegación plegable con
el tema "dark inverse". La barra de navegación contiene un solo enlace a *Home*.

Si vemos las líneas 53-61 notaremos la presencia del elemento `<div>` con la
clase `container` que marca el elemento contenedor para el sistema de rejillas.
Así, podemos usar el sistema de rejillas de Bootstrap para ordenar el contenido
de nuestras páginas.

La línea 54 es muy importante porque esta línea contiene el código PHP en línea
que representa el comodín para el contenido de la página. Hablamos sobre esto
al comienzo de esta sección. Cuando Laminas imprime la página evaluá la plantilla
de maqueta e imprime el contenido real de la página.

Las líneas 56-60 definen el area para el pie de página. El pie de página contiene
la información de derechos de autor "2016 by Laminas Technologies Ltd. All rights reserved."
Podemos reemplazar esta información por la de nuestra compañia.

En la línea 62 está el comodín para los scripts JavaScript cargados por cada
página en particular. El ayudante de vista `InlineScript` colocará todos los
scripts que registremos (el registro de scripts JavaScript lo veremos luego
en este capítulo).

Finalmente las líneas 63 y 64 contienen respectivamente las etiquetas de cierre
para el cuerpo y el documento HTML.

## Modificar la Maqueta de la Página por Defecto

Para demostrar como podemos definir nuestra propia maqueta de página modificaremos
la maqueta original del sitio web: Laminas Skeleton Application. Queremos que se
muestre como título de página "Hello world", "Hello world!" como el texto del
encabezado, la barra de navegación y las migas de pan debajo del
encabezado, el comodín para el contenido de la página en medio de la página y
el pie de página con la información abajo en la página (para un ejemplo de
lo que intentamos alcanzar veamos la figura 6.4).

![Figura 6.4. El resultado de la Maqueta de la Página](../en/images/appearance/mainpage.png)

Vamos a comenzar con el título de la página "Hello World". Reemplazaremos las
líneas 6 y 7 en el archivo *layout.phtml* de la siguiente manera:

~~~php
<?= $this->headTitle('Hello World')->setSeparator(' - ')->setAutoEscape(false) ?>
~~~

Luego usaremos el sistema de rejillas que provee Bootstrap para organizar el
bloque principal de la página. Reemplazamos el código HTML del elemento `<body>`
(líneas 28-63) con el siguiente código:

~~~php
<body>
  <div class="container">
    <div class="row">
      <!-- Page header -->
      <div class="col-md-4">
        <div class="app-caption">Hello World!</div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <!-- Navigation bar -->
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <!-- Breadcrumbs -->
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <!-- Page content placeholder -->
        <?= $this->content; ?>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <hr>
        <p>&copy; 2005 - <?= date('Y') ?> by Your Company. All rights reserved.</p>
    </div>
  </div> <!-- /container -->
  <?php echo $this->inlineScript() ?>
</body>
~~~

En el código de arriba definimos el elemento `<div>` con la clase `container`
y colocamos los elementos `<div>` de la rejilla dentro de él. La rejilla consiste
en 5 columnas:

  * El encabezado de la página contiene el texto "Hello World!" (línea 3-8).
    El texto del encabezado abarca cuatro columnas de la rejilla. Para el estilo
    del texto usamos nuestro clase CSS `app-caption` (definiremos esta clase en
    el archivo *style.css* un poco más adelante).

  * Dejamos el espacio para la barra de navegación. En la línea 11 colocaremos
    este componente de interfaz.

  * En la línea 16 tenemos el espacio para las migas de pan.

  * En la línea 22 tenemos el comodín para el contenido de la página. Antes de que
    Laminas imprimá la página la evaluá, es decir, determina el contenido de la variable
    `$content` antes de imprimirla. En consecuencia el contenido de la página
    será sustituido dependiendo de la página actual.

  * En las líneas 25-29 proveemos el pie de página con el texto "(c) 2013 by
    Your Company. All rights reserved." Si lo deseamos podemos cambiar este
    texto y sustituirlo por el nombre de nuestra empresa.

Luego colocamos la barra de navegación en el espacio de la rejilla que antes
reservamos para ella:

~~~php
<!-- Navigation bar -->
<nav class="navbar navbar-default" role="navigation">
  <div class="collapse navbar-collapse navbar-ex1-collapse">
    <ul class="nav navbar-nav">
      <li class="active">
        <a href="<?= $this->url('home') ?>">Home</a>
      </li>
      <li>
        <a href="<?= $this->url('application', ['action'=>'downloads']) ?>">
          Downloads
        </a>
      </li>
      <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
          Support <b class="caret"></b>
          <ul class="dropdown-menu">
            <li>
              <a href="<?= $this->url('doc', ['page'=>'contents']) ?>">
                Documentation
              </a>
            </li>
            <li>
              <a href="<?= $this->url('static', ['page'=>'help']) ?>">
                Help
              </a>
            </li>
          </ul>
        </a>
      </li>
      <li>
        <a href="<?= $this->url('about') ?>">About</a>
      </li>
    </ul>
    </div>
</nav>
~~~

En el código de arriba usamos un componente para la interfaz que genera la
barra de navegación que provee Bootstrap. Además, usamos el ayudante de vista
`Url` para insertar los enlaces de los elementos de navegación.

I> Discutimos el uso del ayudante de vista `Url` en la sección *Generar URLs a
I> Partir de Rutas* en el capítulo [Routing](#routing).

Luego colocamos las migas de pan en la columna correspondiente de la rejilla:

~~~php
<!-- Breadcrumbs -->
<ol class="breadcrumb">
  <li class="active">Home</li>
</ol>
~~~

Finalmente necesitamos escribir un par de reglas CSS para ajustar el aspecto.
Definimos muestras propias reglas CSS en la hoja de estilo *style.css*.

Queremos que el texto del encabezado "Hello World!" tenga un tamaño de letra más
grande y en negritas, además, de un color más bonito. Para hacer esto abrimos
el archivo *style.css* y añadimos las siguientes líneas al final:

~~~css
div.app-caption {
  padding: 25px 0px;
  font-size: 3.0em;
  font-weight: bold;
  color: #6aacaf
}
~~~

En el código CSS de arriba creamos la clase `app-caption` que se aplicará al
elemento `<div>` y que define 25 pixeles de relleno vertical, el tamaño de la
letra, negritas y la representación hexadecimal del color del texto en RGB.

Por defecto, en la aplicación esqueleto, la barra de navegación esta clavada
en la parte superior de la página y la regla CSS para el cuerpo define 20 pixeles
de relleno en la parte superior de la página dejando un espacio para la barra
de navegación. Pero como nuestro ejemplo Hello
World ha desclavado la barra de navegación del tope de la página y la ha colocado
a "fluir" en la página necesitamos remover el relleno del tope del cuerpo de la
página. Para hacer esto editamos la regla CSS `body` en el archivo `style.css`
quedando de la siguiente manera:

~~~css
body {
  padding-bottom: 40px;
}
~~~

Muy bien, hemos completado la plantilla para la maqueta de la página! Para ver
el resultado de nuestros cambios abrimos el sitio en el navegador web. Deberíamos
ver una página coma la de la figura 6.4. Podemos hacer clic en los enlaces
de la barra de navegación para visitar las páginas *About* o *Documentation*.
El contenido de una página en particular se coloca con la ayuda del comodín
de contenido de nuestra maqueta.

T> El resultado se puede ver en acción en la aplicación de ejemplo que es parte
T> de los ejemplo de este libro que disponibles en GitHub.

## Cambiar entre Maquetas

Por defecto Laminas provee una sola plantilla de maqueta *layout.phtml*. En las
aplicaciones de la vida real probablemente necesitaremos tener varias maquetas
y cambiar de maqueta en determinados controles o acciones.

Por ejemplo, podemos tener un *front-end* y un *back-end* como partes de nuestro
sitio. La parte del *front-end* consistiría en páginas web visibles públicamente
para todos los usuarios y utilizaría la maqueta por defecto en todas sus páginas.
La parte del *back-end* consistiría de páginas visibles solo al usuario administrador
y utilizaría otra plantilla de maqueta que contiene el menú administrativo.

Primero preparamos otro archivo para la plantilla de maqueta. Por ejemplo, lo
podemos llamar *layout2.phtml*. Para simplificar la preparación del archivo
copiamos el contenido del archivo por defecto *layout.phtml* y hacemos los
cambios necesarios.

Cuando la segunda plantilla de maqueta está lista podemos cambiar entre maquetas
desde una determinada acción del controlador usando el siguiente código:

~~~php
// A controller's action method that uses an alternative
// layout template.
public function indexAction()
{
  //...

  // Use the Layout plugin to access the ViewModel
  // object associated with layout template.
  $this->layout()->setTemplate('layout/layout2');

  //...
}
~~~

Arriba el método de acción usa el complemento para controladores `Layout`
(línea 9) que permitir acceder a una instancia de la clase `ViewModel` asociada
con la plantilla de maqueta. Para cambiar una plantillad de maqueta desde un
método de acción llamamos al método `setTemplate()` provisto por la clase
`ViewModel`.

T> Además del complemento para controladores `Layout` existen el ayudante de
T> vista `Layout` que nos provee con la misma capacidad. Por ejemplo, con el
T> ayudante de vista `Layout` podemos cambiar la maqueta de una página "estática"
T> que no tiene una acción de controlador especifica.

### Colocar una Maqueta para todas las Acciones de un Controlador

Si todos los métodos de acción de una clase controladora usan la misma maqueta
alternativa podemos sobrescribir el método `onDispatch()` de la clase
`AbstractActionController` y llamar al método `setTemplate()` como se
muestra en el ejemplo siguiente:

~~~php
// Add this alias in the beginning of the controller file
use Laminas\Mvc\MvcEvent;

// ...

class IndexController extends AbstractActionController
{
  /**
   * We override the parent class' onDispatch() method to
   * set an alternative layout for all actions in this controller.
   */
  public function onDispatch(MvcEvent $e)
  {
    // Call the base class' onDispatch() first and grab the response
    $response = parent::onDispatch($e);

    // Set alternative layout
    $this->layout()->setTemplate('layout/layout2');

    // Return the response
    return $response;
  }
}
~~~

## Vistas Parciales

Una vista *parcial* es una archivo *.phtml*, es decir una plantilla de vista,
que puede ser impresa por otra plantilla de vista. Las vistas parciales permiten
componer nuestra página con piezas y piezas reusables de vista imprimiendo
la lógica en diferentes plantillas de vista.

Como un ejemplo simple del uso de una vista parcial vamos a imaginar que necesitamos
imprimir una tabla con algunos productos. Cada producto tiene un ID, el nombre
y el precio. Podemos usar una plantilla de vista parcial para imprimir una
columna de una tabla varias veces.

Primero agregamos el método `partialDemoAction()` al controlador Index:

~~~php
// An action that demonstrates the usage of partial views.
public function partialDemoAction()
{
  $products = [
    [
      'id' => 1,
      'name' => 'Digital Camera',
      'price' => 99.95,
    ],
    [
      'id' => 2,
      'name' => 'Tripod',
      'price' => 29.95,
    ],
    [
      'id' => 3,
      'name' => 'Camera Case',
      'price' => 2.99,
    ],
    [
      'id' => 4,
      'name' => 'Batteries',
      'price' => 39.99,
    ],
    [
      'id' => 5,
      'name' => 'Charger',
      'price' => 29.99,
    ],
  ];

  return new ViewModel(['products' => $products]);
}
~~~

El método de acción de arriba prepara un arreglo con los productos que vamos a
imprimir y los pasa a la plantilla de vista con la ayuda del contenedor de variables
`ViewModel`.

Luego agregamos el archivo para la plantilla *partial-demo.phtml*:

~~~php
<?php
$this->headTitle('Partial View Demo');
?>

<h1>Partial View Demo</h1>
<p>
    Below, the table of products is presented. It is rendered with the help of
    partial views.
</p>
<table class="table table-striped table-hover">
  <tr>
    <th>ID</th>
    <th>Product</th>
    <th>Price</th>
  </tr>

  <?php
    foreach ($this->products as $product) {
      echo $this->partial('application/index/table-row', ['product'=>$product]);
    }
  ?>
</table>
~~~

En la plantilla de vista de arriba definimos las etiquetas para la tabla de
productos (línea 10-22). En la línea 18 recorremos los elementos del arreglo
de productos e imprimimos cada columna con el ayudante de vista `Partial`.

El primer argumento del ayudante de vista `Partial` es el nombre del archivo
de plantilla de vista parcial ("application/index/table-row").

El segundo argumento del ayudante de vista `Partial` debe ser el arreglo de
argumentos pasado a la plantilla de vista. Ellos serán accesibles de la misma
manera que si los hubiéramos pasado con el contenedor de variables `ViewModel`.

Finalmente, creamos la plantilla de vista *table-row.phtml* que se usará como
la plantilla de vista parcial:

~~~php
<tr>
  <td> <?= $this->product['id'] ?> </td>
  <td> <?= $this->product['name'] ?> </td>
  <td> <?= $this->product['price'] ?> </td>
</tr>
~~~

En la plantilla de vista de arriba simplemente imprimimos una sola columna
de la tabla.

Para ver la página web que resulta escribimos la URL "http://localhost/application/partial-demo"
en la barra de navegación de nuestro navegador web. Deberíamos ver algo como lo
que se muestra en la figura 6.5.

![Figura 6.5. Las columnas de la tabla se imprimen con la vista parcial](../en/images/appearance/partial_demo.png)

## Ayudante de Vista Placeholder

`Placeholder` es otro ayudante de vista útil que permite capturar contenido
HTML y guardarlo [^store] para su uso posterior. De manera análoga al ayudante
de vista `Partial` podemos componen nuestra página en varias piezas.

[^store]: El ayudante de vista `Placeholder` almacena los datos en la variable
          de sesión de PHP. Así, en teoría, incluso podemos capturar el contenido
          de una página y luego imprimirlo o usarlo en otra página.

Por ejemplo, podemos usar el ayudante de vista `Placeholder` en conjunto con el
ayudante de vista `Partial` para "decorar" el contenido de una plantilla de vista
con otra plantilla de vista. Una aplicación práctica útil para esto es la
"herencia" de maquetas.

Imaginemos la situación en la que necesitamos crear una maqueta alternativa que
tiene exactamente la misma sección head, header y footer pero es diferente en la
sección page central. La manera de lograr esto usando la "fuerza bruta"
sería copiar y pegar el contenido de la plantilla de maqueta original,
y hacer los ajustes necesarios. Otra manera (mucho mejor) es "heredar" la plantilla
original así la maqueta que resulta reusará las partes comunes.

Para demostrar como heredar una maqueta crearemos la plantilla de vista *layout2.phtml*
que heredará la plantilla por defecto *layout.phtml* y agregaremos una barra con
anuncios a la derecha de la página. Tener anuncios en la plantilla será
útil si planeamos conseguir ganancias mostrando publicidad en todas o en la
mayoría de nuestras páginas.

Colocamos el siguiente código en el archivo de plantilla *layout2.phtml*:

~~~php
<?php $this->placeholder('content')->captureStart(); ?>

<div class="row">
    <div class="col-md-8">
    <?= $this->content; ?>
    </div>
    <div class="col-md-4">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title">Ads</h3>
          </div>
          <div class="panel-body">
            <strong>Laminas Framework Book</strong>
            <p>Learn how to create modern web applications with PHP
                and Laminas Framework</p>
            <a target="_blank"
               href="https://github.com/olegkrivtsov/using-laminas-framework-book">
               Learn More
            </a>
          </div>
        </div>
    </div>
</div>

<?php
  $this->placeholder('content')->captureEnd();
  echo $this->partial('layout/layout',
          ['content'=>$this->placeholder('content')]);
?>
~~~

En el código de arriba llamamos al método `captureStart()` (línea 1) y el método
`captureEnd()` (línea 26) del ayudante de vista `Placeholder` para delimitar
el código HTML que capturará el ayudante de vista y que guardará en su almacenamiento
interno (en lugar de imprimirlo en el flujo de salida estándar de PHP).

En las líneas 3-23 colocamos el código HTML de la maqueta "heredada". La maqueta
derivada usa dos celdas de la rejilla. La primera celda de la rejilla (abarcando
8 columnas) contendrá el contenido actual de la página y la segunda celda
(abarcando 4 columnas) contendrá la publicidad. Para el estilo de las anuncios
usamos el componente para interfaces *Panel* provisto por Twitter Bootstrap.

En la línea 27 usamos el ayudante de vista `Partial` que se usa para imprimir
la maqueta "padre" (*layout.phtml*). Pasamos el contenido capturado
por el ayudante de vista `Placeholder` al ayudante de vista `Partial` como
segundo argumento.

Esta manera produce una bonita y vistosa maqueta que hereda la maqueta
por defecto y mejora la reusabilidad del código.

Ahora si colocamos el *layout2.phtml* para todas las acciones del controlador
Index seremos capaces de ver un resultado como el de la figura 6.6.

![Figura 6.6. Herencia de maquetas](../en/images/appearance/inherited_layout.png)

## Agregar Scripts a la Página Web

El código JavaScript se puede insertar dentro de las páginas HTML y hacerlas
interactivas. Los scripts se podrían insertar en el archivo HTML entre las
etiquetas `<script>` y `<\script>`. Abajo presentamos código JavaScript de
ejemplo:

~~~php
<script type="text/javascript">
  // Show a simple alert window with the "Hello World!" text.
  $(document).ready(function() {
    alert('Hello World!');
  });
</script>
~~~

En el ejemplo de arriba creamos el elemento `<script>` y colocamos la llamada
a la función jQuery en él. La función que está atada a jQuery se ejecuta cuando
el DOM ha terminado de cargarse. Cuando la función se ejecuta una ventana de
alerta con el texto "Hello World!" y el botón OK aparecerá.

Como colocamos el código JavaScript dentro del archivo HTML nos referiremos a él
como un script *en linea*. Una manera alternativa para guardar código JavaScript
es colocándolo en un archivo `.js` *externo*. Los archivos externos generalmente
contienen código que se diseña para usarse en varias páginas. Generalmente los
archivos JavaScript externos se almacenan en el directorio *APP_DIR/public/js/*.
Para enlazar un archivo JS a nuestra página HTML agregamos el elemento `<script>`
como se muestra abajo:

~~~php
<script type="text/javascript" src="/js/yourscript.js"></script>
~~~

Cuando el navegador encuentra un elemento `<script>` lee el archivo JS externo
y ejecuta el código.

Generalmente hay dos lugares dentro del archivo HTML donde podemos colocar
el script:

* El código JavaScript se puede colocar en la sección `<head>` de una página
  HTML. Es recomendable usar este método cuando necesitamos que el JavaScript
  se cargue antes que el contenido de la página. Usamos este método para cargar
  la extensión de JavaScript Twitter Bootstrap y la biblioteca jQuery.

* El script se puede colocar al final de la sección `<body>` de la paǵina HTML,
  exactamente antes de cerrar la etiqueta `</body>`. Esta manera es mejor cuando
  si necesitamos que todo el DOM se cargue antes de que el script pueda comenzar
  a ejecutarse.

[^dom]: El DOM (Modelo de Objetos del Documento) es una conveniente representación
        de la estructura de un documento HTML en forma de un árbol de documentos.

Si un determinado archivo JavaScript se necesita usar en todos (o en la mayoría)
de las páginas web es mejor colocarlo en una plantilla de vista. Pero cuando un
script se usa en sola una página, colocarlo en la plantilla no es la mejor
idea. Si colocamos un script en una plantilla el script se cargará en todas
las páginas lo que puede producir un trafico innecesario y un incremento del tiempo
de carga de la página y de todo el sitio. Para evitarlo podemos agregar un script
para determinadas páginas solamente.

Para agregar un script especifico de página que colocaremos en la sección `<head>`
de nuestra página usamos el ayudante de vista `HeadScript`. Los métodos de este
ayudante se resumen abajo en la tabla 6.1:

{title="Tabla 6.1. Métodos que provee el ayudate de vista HeadScript"}
|--------------------------------|---------------------------------------------------------------|
| *Método*                       | *Descripción*                                                 |
|--------------------------------|---------------------------------------------------------------|
| `appendFile()`                 | Coloca un enlace a un archivo JS externo después de todos     |
|                                | los otros.                                                    |
|--------------------------------|---------------------------------------------------------------|
| `offsetSetFile()`              | Inserta un enlace a un archivo JS externo en una posición     |
|                                | determinada dentro de la lista.                               |
|--------------------------------|---------------------------------------------------------------|
| `prependFile()`                | Coloca un enlace a un archivo JS externo antes que todos los  |
|                                | otros.                                                        |
|--------------------------------|---------------------------------------------------------------|
| `setFile()`                    | Limpia la lista de scripts y coloca solo el archivo JS        |
|                                | externo que se indicó.                                        |
|--------------------------------|---------------------------------------------------------------|
| `appendScript()`               | Coloca un script en línea después de todos los otros.         |
|--------------------------------|---------------------------------------------------------------|
| `offsetSetScript()`            | Inserta un script en línea en una posición dada de la lista.  |
|--------------------------------|---------------------------------------------------------------|
| `prependScript()`              | Coloca un script en línea antes de todos los otros scripts.   |
|--------------------------------|---------------------------------------------------------------|
| `setScript()`                  | Limpia la lista de scripts en línea y coloca solo el script   |
|                                | en línea dado.                                                |
|--------------------------------|---------------------------------------------------------------|

Para agregar un enlace a un archivo JS externo en la sección `<head>` de la página
agregamos el siguiente código PHP al comienzo de nuestro archivo de plantilla de
vista (*.phtml*):

~~~php
<?php
$this->headScript()->appendFile('/js/yourscript.js', 'text/javascript');
?>
~~~

En el código de arriba llamamos al método `appendFile()` del ayudante de vista
`HeadScript`. Este método toma dos argumentos. El primero de ellos es la ruta
al archivo JS externo (si el archivo se guarda dentro del directorio
*APP_DIR/public/js* o una URL a un archivo JS que está ubicado en otro servidor
web). El segundo argumento es el tipo de script (generalmente este valor es
igual "text/javascript").

Otros métodos que provee el ayudante de vista `HeadScript`; como `prependFile()`,
`offsetSetFile()` y `setFile()`; se diferencian solo por la posición en la lista
de scripts donde el nuevo script se insertará.

Los métodos `prependScript()`, `appendScript()`, `offsetSetScript()` y `setScript()`
se diseñan para insertar código JavaScript en línea. Ellas se usan raramente porque
generalmente insertamos scripts JS externos en la sección head del documento.

Para insertar un script al final de la sección `<body>` del documento podemos
usar el ayudante de vista `InlineScript` [^inlinescript]. Este provee exactamente
los mismos métodos que el ayudante `HeadScript`. Abajo presentamos un ejemplo
de como se puede agregar código JavaScript en línea al final del cuerpo del
documento.

[^inlinescript]: El nombre `InlineScript` no refleja con exactitud las capacidades
                 de este ayudante de vista. Este, en realidad, puede insertar
                 tanto script en línea como externos. La mejor forma de nombrar
                 a este script debería ser `BodyScript` porque él está destinado
                 para insertar scripts en el cuerpo del documento.

~~~php
<?php
$script = <<<EOT
  $(document).ready(function() {
    alert('Hello World!');
  });
EOT;
$this->inlineScript()->appendScript($script);
~~~

En el ejemplo de arriba usamos la sintaxis Heredoc [^heredoc] de PHP para llenar
la variable `$script` con el código JavaScript en línea. Luego llamamos a la
función `appendScript()` del ayudante de vista `InlineScript` y le pasamos el
código como argumento.

[^heredoc]: Heredoc es un método alternativo provisto por PHP que permite
            definir cadenas de caracteres. Este método trabaja bien con cadenas
            de caracteres de multiples líneas.

Sin embargo usar el ayudante de vista `InlineScript` puedo no se conveniente
si pensamos en la legibilidad. Además, el corrector de sintaxis de editores de
texto como NetBeans fallan al detectar la notación Heredoc y no reconocerán
el código JavaScript. Para reparar este problema simplemente podemos colocar
el elemento `<script>` al final de nuestra plantilla de vista como se muestra
en el ejemplo de abajo:

~~~php
<!-- Page content goes first -->

<!-- Inline script goes last -->
<script type="text/javascript">
  $(document).ready(function() {
    // Show a simple alert window with the "Hello World!" text.
    alert("Hello World!");
  });
</script>
~~~

Con esto logramos el mismo efecto que con el ayudante de vista `InlineScript`
pero esta manera mejora la legibilidad del script y la sintaxis es automáticamente
revisada por NetBeans.

I> Es necesario que el contenido se imprima en la plantilla de vista para que
I> los ayudantes de vista `HeadScript` y `InlineScript` funcionen (como se
I> muestra en las líneas 23 y 62 del archivo *layout.phtml*). Si quitamos estas
I> líneas de la plantilla de maqueta los scripts no se insertaran in la página
I> web.

### Ejemplo

A manera de ejemplo realista insertaremos un pedazo de código JavaScript en
nuestra página web que nos permitirá agregar la característica de autocompletado.
Con esta característica el navegador web predirá una palabra o frase a partir
de las primeras letras que el usuario escriba evitando que él escriba el texto
completo. Podemos usar la biblioteca de JavaScript *Twitter Typeahead*.
Análogamente a Twitter Bootstrap la biblioteca Typeahead fue desarrollada por
Twitter Inc. para satisfacer sus necesidades pero se distribuye libremente.

Descargamos el archivo *typeahead.min.js* (la versión miniaturizada de la
biblioteca Typeahead) desde la [página oficial](http://twitter.github.io/typeahead.js/)
del proyecto. Cuando la descarga termina colocamos el archivo en la carpeta
*APP_DIR/public/js*.

Luego agregamos el archivo *typeahead.phtml* a la subcarpeta *application/index/static*
que esta dentro de la carpeta *view* del módulo. El tipo de ruta `StaticRoute`,
que creamos y configuramos antes en el capítulo [Routing](#routing),
usa esa carpeta y hace que todas las páginas "estáticas" que se colocan allí
estén automáticamente disponibles para todos los usuarios del sitio,

En el archivo de plantilla de vista *typeahead.phtml* colocamos el siguiente
código:

~~~php
<?php
$this->headTitle('Typeahead');
// Add a JavaScript file
$this->headScript()->appendFile('/js/typeahead.min.js', 'text/javascript');
?>

<h1>Typeahead</h1>
<p>Type a continent name (e.g. Africa) in the text field below:</p>
<input type="text" class="typeahead" title="Type here"/>

<script type="text/javascript">
  $(document).ready(function() {
    $('input.typeahead').typeahead({
       name: 'continents',
       local: [
            'Africa',
            'Antarctica',
            'Asia',
            'Europe',
            'South America',
            'North America'
        ]
    });
  });
</script>
~~~

En el código de arriba colocamos el título de la página (línea 2) luego agregamos
el archivo *typeahead.min.js* a la sección `<head>` de la página con el ayudante
de vista `HeadScript` (línea 4).

En la línea 9 creamos un campo de texto de entrada donde los usuarios serán
capaces de escribir algún texto. Marcamos el campo de entrada con la clase CSS
`typeahead`.

Las líneas 11-25 contienen código JavaScript en línea colocado al final de la
plantilla de vista (no usamos el ayudante de vista `InlineScript` en procura
de mejorar la legibilidad).

En la línea 12 tenemos el administrador de eventos de jQuery asociado al evento
"document is ready". Este evento se lanzá cuando el árbol DOM se ha cargado.

En la línea 13 tenemos el selector jQuery ("input.typeahead") que selecciona todos
los campos *input* marcados con la clase CSS `typeahead` y ejecuta la función
`typeahead()` en ellos.

La función `typeahead()` vincula al administrador de eventos con algún cambio en
el campo de texto *input*. Una vez que el usuario ingresa un carácter en el
campo el administrador se ejecuta y revisa las letras introducidas. Luego se
muestra un menú desplegable con las variantes sugeridas por el autocompletado.

La función `typeahead()` toma dos argumentos: el argumento `name` identifica
el conjunto de datos y el argumento `local` es un arreglo JSON que contiene
las variables disponibles para la autocompletación.

Para dar al campo de autocompletación y a su menú desplegable una apariencia
visual agradable agregamos las siguientes reglas CSS a nuestro archivo *style.css*.

~~~css
.typeahead,
.tt-query,
.tt-hint {
  width: 396px;
  height: 30px;
  padding: 0px 12px;
  font-size: 1.1em;
  border: 2px solid #ccc;
  border-radius: 4px;
  outline: none;
}

.tt-dropdown-menu {
  width: 422px;
  margin-top: 12px;
  padding: 8px 0;
  background-color: #fff;
  border: 1px solid #ccc;
  border: 1px solid rgba(0, 0, 0, 0.2);
  border-radius: 4px;
}

.tt-suggestion {
  padding: 3px 20px;
  font-size: 1.1em;
  line-height: 24px;
}

.tt-suggestion.tt-is-under-cursor {
  color: #fff;
  background-color: #0097cf;
}

.tt-suggestion p {
  margin: 0;
}
~~~

Para ver la característica de autocompletación trabajando escribimos la URL
"http://localhost/typeahead" en nuestro navegador web y presionamos Enter.
La página *Typeahead* aparecerá con el cursor en el campo dispuesto para
ingresar un nombre. Por ejemplo, al escribir a letra *a* veremos como
Typeahead sugiere las variables disponibles (figura 6.7).

![Figura 6.7. Característica Auto-complete](../en/images/appearance/typeahead.png)

T> Podemos ver el ejemplo trabajando en la aplicación *Hello World* que se añade
T> a este libro escribiendo la URL "http://localhost/typeahead" en el
T> navegador web.

## Agregar las Hojas de Estilo CSS a la Página Web

Las hojas de estilos CSS se colocan generalmente en la sección `<head>` del
documento HTML directamente o con un enlace a un archivo externo
(los archivos de hojas de estilo CSS externos se almacenan usualmente en la
carpeta `APP_DIR/public/css`).

~~~text
<link rel="stylesheet" type="text/css" href="/css/style.css">
~~~

o como un elemento `<style>` en línea:

~~~php
<style>
  body {
    padding-top: 60px;
    padding-bottom: 40px;
 }
</style>
~~~

Para guardar las reglas CSS se recomienda usar una hoja de estilo CSS externa.
Por ejemplo, las reglas CSS básicas que provee el framework CSS Twitter Bootstrap
se cargan desde los archivos *bootstrap.min.css* y *bootstrap-theme.min.css*.
Las reglas CSS desarrolladas a la medida para nuestro sitio web se pueden guardar
en el archivo *style.css*. Como necesitamos estas hojas de estilo CSS en la
mayoría de las páginas es mejor enlazarlas en la sección *head* de la plantilla
de maqueta. Pero, si necesitamos que una hoja de estilo CSS determinada se
cargue en solo una página podemos colocarla en la plantilla de vista de la
página.

Para agregar una hoja de estilo CSS externa a la plantilla de vista usamos el
ayudante de vista `HeadLink`:

~~~php
<?php
$this->headLink()->appendStylesheet('/css/style.css');
$this->headLink()->appendStylesheet(
       '//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css');
~~~

En el ejemplo de arriba usamos el método `appendStylesheet()` del ayudante
de vista `HeadLink` para agregar una hoja de estilo CSS a la sección *head*
del documento. El método acepta una ruta a un archivo CSS local (línea 2)
o una URL a un archivo CSS ubicado en otro servidor (línea 3).

Una lista de los métodos del ayudante de vista `HeadLink` se muestra en la
tabla 6.2:

{title="Tabla 6.2. Métodos provistos por el ayudante de vista HeadLink"}
|--------------------------------|---------------------------------------------------------------|
| *Método*                       | *Descripción*                                                 |
|--------------------------------|---------------------------------------------------------------|
| `appendStylesheet()`           | Colocar un enlace a un archivo de hoja de estilo CSS después  |
|                                | de todos los otros.                                           |
|--------------------------------|---------------------------------------------------------------|
| `offsetSetStylesheet()`        | Insertar un enlace a un archivo de hoja de estilo CSS en una  |
|                                | posición determinada dentro de la lista.                      |
|--------------------------------|---------------------------------------------------------------|
| `prependStylesheet()`          | Coloca un enlace a un archivo de hoja de estilo CSS antes     |
|                                | de todos los otros estilos.                                   |
|--------------------------------|---------------------------------------------------------------|
| `setStylesheet()`              | Limpia la lista y coloca un solo archivo CSS en su lugar.     |
|--------------------------------|---------------------------------------------------------------|

Si queremos agregar un elemento `<style>` en línea en la sección *head* del
documento podemos usar el ayudante de vista `HeadStyle`, presentamos sus métodos
en la tabla 6.3:

{title="Table 6.3. Métodos del ayudante de vista HeadStyle"}
|--------------------------------|---------------------------------------------------------------|
| *Métodos*                      | *Descripción*                                                 |
|--------------------------------|---------------------------------------------------------------|
| `appendStyle()`                | Agregar código CSS en línea después de todos los otros.       |
|--------------------------------|---------------------------------------------------------------|
| `offsetSetStyle()`             | Insertar código CSS en línea en una posición dada de la lista.|
|--------------------------------|---------------------------------------------------------------|
| `prependStyle()`               | Coloca código CSS antes de todos los otros.                   |
|--------------------------------|---------------------------------------------------------------|
| `setStyle()`                   | Limpia la lista y coloca el código CSS dado en su lugar.      |
|--------------------------------|---------------------------------------------------------------|

### Ejemplo

Para demostrar como agregar hojas de estilo CSS a nuestra página web tomaremos
un ejemplo realista. Supongamos que necesitamos permitir al usuario escribir la
fecha (en formato YYYY-MM-DD) en un campo de texto *input*. Nos gustaría
mejorar la experiencia del usuario permitiéndole escribir la fecha pero
seleccionandola desde un ventana desplegable que tiene un selector de fechas.

Para alcanzar este objetivo podemos usar la biblioteca de terceros llamada
jQuery UI [^jqueryui]. Para integrar jQuery UI en nuestra página necesitamos
descargar dos archivos desde la [página oficial del proyecto](http://jqueryui.com/):

* *jquery-ui.min.js* -- la versión miniaturizada del código JavaScript de
  jQuery UI.

* *jquery-ui.min.css* -- la versión miniaturizada de los estilos del tema
  de jQuery UI.

[^jqueryui]: jQuery UI provee un conjunto de "interacciones de interfaz, efectos,
             widgets y temas para usuarios"; que están basados en la biblioteca
             jQuery. jQuery UI es análoga a Twitter Bootstrap en el sentido de
             que provee componentes reusables para la interfaz de usuarios.

Colocamos el archivo *jquery-ui.min.js* en la carpeta *APP_DIR/public/js* y el
archivo *jquery-ui.min.css* en la carpeta *APP_DIR/public/css*. Finalmente,
agregamos la plantilla de vista *datepicker.phtml* al directorio
*application/index/static* dentro de la carpeta *view* del módulo:

~~~php
<?php
$this->headTitle('Datepicker');

$this->headScript()->appendFile('/js/jquery-ui.min.js', 'text/javascript');
$this->headLink()->appendStylesheet('/css/jquery-ui.min.css');
?>

<h1>Datepicker</h1>

<p>
    Click the edit box below to show the datepicker.
</p>

<input type="text" class="datepicker" title="Type here"/>

<script>
    $(document).ready(function() {
        $("input.datepicker").datepicker({ dateFormat: 'yy-mm-dd' });
    });
</script>
~~~

En el ejemplo de arriba usamos el método `appendFile()` del ayudante de vista
`HeadScript` (línea 4) agregamos el enlace al archivo *jquery-ui.min.js* a la
sección *head* del documento.

En la línea 5 usamos el método `appendStylesheet()` del ayudante de vista
`HeadLink` para agregar el enlace a la hoja de estilo CSS *jquery-ui.min.css*
a la sección *head* del documento.

En la línea 14 agregamos el campo de texto *input* que se usará para ingresar
la fecha.

En las líneas 16-20 agregamos el código JavaScript en línea para enlazar al
administrador de eventos de jQuery con el campo de texto *input*. Cuando el
usuario hace clic en el campo de texto *input* el widget datepicker aparecerá
permitiendo seleccionar la fecha.

Para ver el resultado ingresamos la URL "http://localhost/datepicker" en la barra
de navegación de nuestro navegador web (ver el ejemplo en la figura 6.8).

![Figura 6.8. Datepicker](../en/images/appearance/datepicker.png)

## Escribir Nuestros Ayudantes de Vista

Antes en este capítulo hemos creado una maqueta común para todas la páginas del
sitio web. Pero aún tenemos un par de cosas que hacer para tener una maqueta
totalmente funcional. Si recordamos, la plantilla de maqueta contiene una barra
de navegación y migas de pan, sin embargo, ambos componentes de interfaz que
provee Twitter Bootstrap son actualmente "estáticos" y necesitan ser más
interactivos.

Por ejemplo el elemento activo de la barra de navegación debería depender de la
acción del controlador que se está ejecutando en el momento. Las migas de pan
deberían mostrar la ruta de la página actual. En esta sección dejaremos estos
detalles completamente listos para el sitio web con la ayuda de nuestros propios
ayudantes de vista.

Un ayudante de vista típico es una clase PHP derivada de la clase base
`Laminas\View\Helper\AbstractHelper` que a su vez implementa la interfaz
`Laminas\View\Helper\HelperInterface` (el diagrama de herencia se presenta en
la figura 6.9).

![Figura 6.9. Diagrama de Clase de un Ayudante de Vista](../en/images/appearance/view_helper_inheritance.png)

### Menú

Primero, vamos a implementar la clase para el ayudante de vista `Menu` que
imprimirá el código HTML de la barra de navegación. La clase `Menu` provee
varios métodos que permiten establecer los elementos del menú en forma de arreglo,
activar el elemento en el menú e imprimir el menú (ver un resumen de los
métodos en la tabla 6.4).

{title="Table 6.4. Métodos del ayudante de vista Menú"}
|----------------------------------|---------------------------------------------------------------|
| *Método*                         | *Descripción*                                                 |
|----------------------------------|---------------------------------------------------------------|
| `__construct($items)`            | Constructor de la clase.                                      |
|----------------------------------|---------------------------------------------------------------|
| `setItems($items)`               | Método para colocar los elementos del menú.                   |
|----------------------------------|---------------------------------------------------------------|
| `setActiveItemId($activeItemId)` | Método para marcar el elemento activo del menú.               |
|----------------------------------|---------------------------------------------------------------|
| `render()`                       | Imprime el menú.                                              |
|----------------------------------|---------------------------------------------------------------|
| `renderItem($item)`              | Imprime un solo elemento del menú.                            |
|----------------------------------|---------------------------------------------------------------|

La información que describe un solo elemento del menú se representará con un
arreglo como el que se muestra abajo (por ejemplo, el elemento *Home* tendrá
un ID, una etiqueta y una URL para el hiperenlace):

~~~php
[
  'id' => 'home',
  'label' => 'Home',
  'link' => $this->url('home')
]
~~~

Además, queremos agregar el soporte para el menú desplegable con elementos
navegables. Por ejemplo, en el caso del menú desplegable *Soporte* que tiene
los subelementos *Documentación* y *Ayuda* la descripción del elemento tomará
la forma siguiente:

~~~php
[
  'id' => 'support',
  'label' => 'Support',
  'dropdown' => [
    [
      'id' => 'documentation',
      'label' => 'Documentation',
      'link' => $this->url('doc', ['page'=>'contents'])
    ],
    [
      'id' => 'help',
      'label' => 'Help',
      'link' => $this->url('static', ['page'=>'help'])
    ]
  ]
]
~~~

Colocaremos la clase `Menu` dentro del namespace `Application\View\Helper`.
Comenzamos creando el archivo `Menu.php` dentro de la carpeta *View/Helper*
que está dentro de la carpeta fuente del módulo *Application* (figura 6.10).

![Figura 6.10. Carpeta View helper](../en/images/appearance/view_helper_dir.png)

Q> **¿Por qué colocamos la clase del ayudante de vista dentro de la carpeta
Q> fuente del módulo?**
Q>
Q> Los ayudantes de vista (a diferencia de las plantillas de vista `.phtml`)
Q> se guardan dentro de la carpeta `src/` del módulo porque ellas son
Q> clases PHP y necesitan ser resueltas por la clase PHP que implementa la
Q> característica de autocargado. Por otro lado, las plantillas de vista se
Q> resuelven usando una clase especial de Laminas llamada *view resolver*, es por
Q> esta razón que las plantillas de vista se almacenan dentro de la carpeta
Q> `view/` del módulo.

Luego creamos el esbozo de código para la clase `Menu`:

~~~php
<?php
namespace Application\View\Helper;

use Laminas\View\Helper\AbstractHelper;

// This view helper class displays a menu bar.
class Menu extends AbstractHelper
{
  // Menu items array.
  protected $items = [];

  // Active item's ID.
  protected $activeItemId = '';

  // Constructor.
  public function __construct($items=[])
  {
    $this->items = $items;
  }

  // Sets menu items.
  public function setItems($items)
  {
    $this->items = $items;
  }

  // Sets ID of the active items.
  public function setActiveItemId($activeItemId)
  {
    $this->activeItemId = $activeItemId;
  }
}
~~~

En el código de arriba definimos varios campos privados para la clase `Menu`.
El campo `$items` (línea 10) es un arreglo que almacenará la información con
los elementos del menú y el campo `$activeItemId` (línea 13) tendrá el ID del
elemento activo en el menú. El elemento del menú activo se verá resaltado.

En las líneas 16-19 definimos el método constructor de la clase que opcionalmente
toma un arreglo de elementos para iniciar el menú. Una manera alternativo para
iniciar el menú es mediante el método `setItems()` (líneas 22-25). Y el método
`setActiveItemId()` (líneas 28-31) coloca el ID del elemento activo actual.

Luego vamos a agregar el método `render()` que generará el código HTML para
la barra de navegación y la regresará como una cadena de texto:

~~~php
// Renders the menu.
public function render()
{
  if (count($this->items)==0)
    return ''; // Do nothing if there are no items.

  $result = '<nav class="navbar navbar-default" role="navigation">';
  $result .= '<div class="navbar-header">';
  $result .= '<button type="button" class="navbar-toggle" ';
  $result .= 'data-toggle="collapse" data-target=".navbar-ex1-collapse">';
  $result .= '<span class="sr-only">Toggle navigation</span>';
  $result .= '<span class="icon-bar"></span>';
  $result .= '<span class="icon-bar"></span>';
  $result .= '<span class="icon-bar"></span>';
  $result .= '</button>';
  $result .= '</div>';

  $result .= '<div class="collapse navbar-collapse navbar-ex1-collapse">';
  $result .= '<ul class="nav navbar-nav">';

  // Render items
  foreach ($this->items as $item) {
    $result .= $this->renderItem($item);
  }

  $result .= '</ul>';
  $result .= '</div>';
  $result .= '</nav>';

  return $result;
}
~~~

En el código de arriba producimos el código HTML para el componente que genera
la barra de navegación de Bootstrap. La barra de navegación usará el tema por
defecto y será desplegable (adaptable a los diferentes tamaños de las pantallas).
La barra de navegación no tendrá el texto en la cabecera. En las líneas 22-24
recorremos los elementos del menú e imprimiremos cada uno de ellos con el método
`renderItem()`. Finalmente, el método `render()` regresa el código HTML
resultante como una cadena de texto.

Para terminar la creación de la clase `Menu` vamos a implementar el método
`renderItem()`. Este método producirá el código HTML para un solo elemento
del menú:

~~~php
// Renders an item.
protected function renderItem($item)
{
  $id = isset($item['id']) ? $item['id'] : '';
  $isActive = ($id==$this->activeItemId);
  $label = isset($item['label']) ? $item['label'] : '';

  $result = '';

  if(isset($item['dropdown'])) {

    $dropdownItems = $item['dropdown'];

    $result .= '<li class="dropdown ' . ($isActive?'active':'') . '">';
    $result .= '<a href="#" class="dropdown-toggle" data-toggle="dropdown">';
    $result .= $label . ' <b class="caret"></b>';
    $result .= '</a>';

    $result .= '<ul class="dropdown-menu">';

    foreach ($dropdownItems as $item) {
      $link = isset($item['link']) ? $item['link'] : '#';
      $label = isset($item['label']) ? $item['label'] : '';

      $result .= '<li>';
      $result .= '<a href="'.$link.'">'.$label.'</a>';
      $result .= '</li>';
    }

    $result .= '</ul>';
    $result .= '</a>';
    $result .= '</li>';

  } else {
    $link = isset($item['link']) ? $item['link'] : '#';

    $result .= $isActive?'<li class="active">':'<li>';
    $result .= '<a href="'.$link.'">'.$label.'</a>';
    $result .= '</li>';
  }

  return $result;
}
~~~

En el código del método `renderItem()` de arriba hicimos lo siguiente:
primero revisamos si el elemento es un menú desplegable o un elemento simple
(línea 10). Si el elemento es un menú desplegable recorremos los elementos
que constituyen el menú desplegable e imprimimos cada uno de ellos (líneas 21-28).
Las líneas 35-39 contienen el código HTML para el caso de un elemento simple.

Para poder usar el ayudante de vista `Menu` en la plantilla de vista necesitamos
registrarlo en la configuración. Para hacerlo agregamos la llave `view_helpers`
al archivo *module.config.php*:

~~~php
<?php
return [

    // ...

    // The following registers our custom view
    // helper classes in view plugin manager.
    'view_helpers' => [
        'factories' => [
            View\Helper\Menu::class => InvokableFactory::class,
        ],
       'aliases' => [
            'mainMenu' => View\Helper\Menu::class
       ]
    ],
];
~~~

En el código de arriba registramos nuestra clase `Menu` como el ayudante de
vista `mainMenu` con lo que seremos capaces de acceder a él desde cualquier
template de vista.

Como planeamos usar el ayudante de vista `Menu` en la plantilla de maqueta
de vista reemplazamos el código para el menú de navegación que está en el
archivo *layout.phtml* con el siguiente código:

~~~php
<!-- Navigation bar -->
<?php
  $this->mainMenu()->setItems([
    [
      'id' => 'home',
      'label' => 'Home',
      'link' => $this->url('home')
    ],
    [
      'id' => 'downloads',
      'label' => 'Downloads',
      'link' => $this->url("application", ['action'=>'downloads'])
    ],
    [
      'id' => 'support',
      'label' => 'Support',
      'dropdown' => [
        [
          'id' => 'documentation',
          'label' => 'Documentation',
          'link' => $this->url('doc', ['page'=>'contents'])
        ],
        [
          'id' => 'help',
          'label' => 'Help',
          'link' => $this->url('static', ['page'=>'help'])
        ]
      ]
    ],
    [
      'id' => 'about',
      'label' => 'About',
      'link' => $this->url('about')
    ],
  ]);

  echo $this->mainMenu()->render();
?>
~~~

En el código de arriba accedemos al ayudante de vista registrado `mainMenu`
y colocamos los elementos de la barra de navegación con la ayuda del método
`setItems()` (línea 3). Como parámetro para el método pasamos un arreglo
de elementos. Luego imprimimos la barra de navegación con el método `render()`.

Para colocar el elemento activo en la barra de navegación llamamos al
método `setActiveItemId()` desde cualquier plantilla de vista. Por ejemplo,
agregamos el siguiente código al comienzo de la plantilla de vista para la
página *Acerca de* (*application/index/about.phtml*) de la siguiente manera:

~~~php
<?php
$this->mainMenu()->setActiveItemId('about');
?>
~~~

Ahora si abrimos la página *Acerca de* en nuestro navegador web deberíamos ver
que el elemento *Acerca de* (About) del menú de navegación está sombreado con
un color diferente. Para mostrar el elemento activo apropiadamente necesitamos
llamar al método `setActivoItemId()` en cada página que este en la barra
de navegación (*Home*, *Descargas*, *Documentación*, etc.) Podemos ver como
se hace esto en la aplicación de ejemplo *Hello World*.

### Migas de Pan

Ahora que sabemos como implementar un ayudante de vista vamos a crear un segundo
ayudante para imprimir las migas de pan. Es completamente análogo al ayudante
de vista `Menu` por esta razón solo mostramos el código completo de la clase
`Breadcrumbs`:

~~~php
<?php
namespace Application\View\Helper;

use Laminas\View\Helper\AbstractHelper;

// This view helper class displays breadcrumbs.
class Breadcrumbs extends AbstractHelper
{
  // Array of items.
  private $items = [];

  // Constructor.
  public function __construct($items=[])
  {
    $this->items = $items;
  }

  // Sets the items.
  public function setItems($items)
  {
    $this->items = $items;
  }

  // Renders the breadcrumbs.
  public function render()
  {
    if(count($this->items)==0)
      return ''; // Do nothing if there are no items.

    // Resulting HTML code will be stored in this var
    $result = '<ol class="breadcrumb">';

    // Get item count
    $itemCount = count($this->items);

    $itemNum = 1; // item counter

    // Walk through items
    foreach ($this->items as $label=>$link) {

      // Make the last item inactive
      $isActive = ($itemNum==$itemCount?true:false);

      // Render current item
      $result .= $this->renderItem($label, $link, $isActive);

      // Increment item counter
      $itemNum++;
    }

    $result .= '</ol>';

    return $result;
  }

  // Renders an item.
  protected function renderItem($label, $link, $isActive)
  {
   $result = $isActive?'<li class="active">':'<li>';

    if(!$isActive)
      $result .= '<a href="'.$link.'">'.$label.'</a>';
    else
      $result .= $label;

    $result .= '</li>';

    return $result;
  }
}
~~~

Para poder usar el ayudante de vista `Breadcrumbs` tenemos que registrarlo en
el archivo *module.config.php*:

~~~php
<?php
return [

  //...

  // The following registers our custom view helper classes.
  'view_helpers' => [
    'factories' => [
      View\Helper\Breadcrumbs::class => InvokableFactory::class,
    ],
    'aliases' => [
        'pageBreadcrumbs' => View\Helper\Breadcrumbs::class,
    ]
  ],
];
~~~

Como planeamos usar el ayudante de vista `Breadcrumbs` en la plantilla de maqueta
de vista reemplazamos el código HTML que está en el archivo *layout.phtml* por
el siguiente código:

~~~php
<!-- Breadcrumbs -->
<?= $this->pageBreadcrumbs()->render(); ?>
~~~

En el código de arriba accedemos al ayudante de vista `pageBreadcrumbs()` y
lo llamamos con el método `render()`. Luego, el operador `echo` imprime el
código HTML de las migas de pan.

Finalmente necesitamos pasar los elementos para las migas de pan en cada
plantilla de vista. Es decir, agregamos las siguientes líneas en la plantilla
de vista de la página *About*:

~~~php
<?php
$this->pageBreadcrumbs()->setItems([
            'Home'=>$this->url('home'),
            'About'=>$this->url('about'),
            ]);
?>
~~~

Ahora si abrimos la página *Acerca de* deberíamos ver las migas de como como
en la figura 6.11 que está más abajo. Los usuarios del sitio verán fácilmente
la página que están visitando y no estarán perdidos.

![Figura 6.11. Migas de Pan para la página Acerca de](../en/images/appearance/about_breadcrumbs.png)

## Los Modelos de Vista y la Composición de Páginas

Antes cuando escribimos los métodos de acción para las clases controladoras
usamos la clase `ViewModel` como un contenedor de variables para pasar las
variables desde el controlador a la plantilla de vista y, además, usamos el
`ViewModel` para sobrescribir el nombre de la plantilla de vista por defecto.

Pero, en realidad la clase `ViewModel` es más que solo un contenedor de variables
y un medio para cambiar el nombre de la plantilla. De hecho, está muy relacionada
a la maqueta y a la composición de la página.

La tercera mayor capacidad de la clase de vista de modelo es que permite
*combinar* varios modelos de vista en una estructura de árbol. Cada modelo
de vista tiene en el árbol un nombre de plantilla de vista asociado y datos que
se pueden pasar a la plantilla de vista para controlar el proceso de impresión.

Esta característica es usada internamente por Laminas Framework cuando "combina"
la maqueta de plantilla de vista y las plantillas de vista asociadas en el
el método de acción del controlador. Laminas crea internamente el modelo de vista
para la maqueta de plantilla y le asigna el nombre de la plantilla de vista
`layout/layout`. Cuando el método de acción del controlador regresa el objeto
`ViewModel` este objeto es adjuntado como un hijo a la maqueta del modelo de
vista (ver figura 6.12 para un ejemplo).

![Figura 6.12. Modelos de vista anidados en una estructura tipo árbol](../en/images/appearance/viewmodel_tree.png)

El resultado del proceso de imprimir una página es el siguiente:

* El modelo de vista hijo se visita primero y su plantilla de vista asociada
  se imprime. El código HTML que resulta se guarda temporalmente.
* La salida HTML del modelo de vista hijo se pasa a la maqueta de modelo de
  vista como la variable `$content`. De esta manera la maqueta de plantilla de
  vista puede imprimir el contenido especifico para una página determinada.

La tabla 6.5 da un resumen de los métodos que provee la clase `ViewModel` para
la composición de páginas:

{title="Tabla 6.5. Los métodos de la clase ViewModel para la composición de la página"}
|--------------------------------|---------------------------------------------------------------|
| *Método*                       | *Descripción*                                                 |
|--------------------------------|---------------------------------------------------------------|
| `addChild()`                   | Agrega un hijo al modelo de vista.                            |
|--------------------------------|---------------------------------------------------------------|
| `getChildren()`                | Trae la lista de los modelos de vista hijos.                  |
|--------------------------------|---------------------------------------------------------------|
| `hasChildren()`                | Comprueba si el modelo de vista tiene un hijo o no.           |
|--------------------------------|---------------------------------------------------------------|
| `clearChildren()`              | Remueve todos los modelos de vista hijos.                     |
|--------------------------------|---------------------------------------------------------------|
| `count()`                      | Regresa el número de modelos de vista hijos.                  |
|--------------------------------|---------------------------------------------------------------|
| `getIterator()`                | Regresa el iterador para los modelos de vista hijos.          |
|--------------------------------|---------------------------------------------------------------|
| `setTerminal()`                | Coloca la bandera terminal.                                   |
|--------------------------------|---------------------------------------------------------------|
| `terminate()`                  | Comprueba si el modelo de vista es el terminal.               |
|--------------------------------|---------------------------------------------------------------|
| `setCaptureTo()`               | Coloca el nombre de la variable que captura la salida.        |
|--------------------------------|---------------------------------------------------------------|
| `setAppend()`                  | Coloca la bandera añadir.                                     |
|--------------------------------|---------------------------------------------------------------|
| `isAppend()`                   | Comprueba si está añadido este modelo de vista u otro.        |
|--------------------------------|---------------------------------------------------------------|

Daremos una breve descripción de los métodos que se presentaron en la tabla de
arriba.

Los métodos `addChild()`, `getChild()`, `hasChildren()` y `clearChildren()` se
usan respectivamente para agregar un modelo de vista hijo a uno padre, recuperar
el arreglo de los modelos de vista adjuntados, probar si el modelo de vista
es una hoja (no tiene hijos) y separar todos los hijos.

El método `setCaptureTo()` permite establecer la variable en la plantilla de vista
padre en la que se inyecta el código HTML producido por la plantilla de vista
hija. Si dos models de vista hijos usan la misma variable el segundo sobrescribirá
al primero. El método `setAppend()` se puede usar cuando necesitamos inyectar
los resultados de dos o más plantillas de vista dentro de una solo variable
comodín. La siguiente plantilla de vista impresa se añadirá al contenido
existente de la variable.

Un modelo de vista se puede marcar como *terminal* con el método `setTerminal()`.
El método `setTerminal` toma un solo parámetro como bandera. Si es `true`, el
modelo de vista se considera como terminal (padre) y el mecanismo de impresión
regresa la salida de la plantilla de vista a la aplicación, de lo contrario
sus padres también se imprimen. El método `terminate()` comprueba si el modelo
de vista es terminal o no

T> El método `setTerminal()` es muy útil en algunas situaciones porque con su
T> ayuda podemos desactivar la impresión de la maqueta de plantilla de vista.
T> Si regresamos desde el controlador el modelo de vista marcado como terminal,
T> la maqueta no se aplicará. Esto se puede usar, por ejemplo, cuando queremos
T> cargar parte de la página asíncronamente usando una petición AJAX [^ajax]
T> y necesitamos insertar su código HTML en el árbol DOM de una página existente.

[^ajax]: AJAX (acrónimo para Asynchronous JavaScript and XML) es una capacidad
         provista por los navegadores modernos que se puede usar para enviar datos
         y recuperar datos de un servidor asíncronamente (en segundo plano) sin
         interferir con la impresión ni con el comportamiento de la página.

## Resumen

Laminas Framework viene con Twitter Bootstrap un framework CSS que permite
crear aplicaciones web atractivas visualmente y con un aspecto profesional.
Bootstrap provee reglas CSS básicas, una maqueta de rejillas simple y
componentes para la interfaz útiles como barras de navegación, migas de pan,
paginación, etc.

En un sitio web típico las páginas tienen una estructura común, por ejemplo,
una página típica tiene una barra de navegación en la parte superior,
un cuerpo con el contenido de la página y un pie de página en la parte de abajo.
En Laminas Framework, definimos esta estructura común con un archivo de plantilla
de vista que llamamos maqueta. La maqueta de plantilla puede tener comodines
en donde Laminas coloca el contenido específico para una página en particular.

Los ayudantes de vista son clases PHP relativamente simples que encapsulan una
parte de la página que se imprime. Por ejemplo, ellos permiten componer la
página en varias partes, establecer el título de la página o las meta etiquetas
y crear código reusable para cosas como la barra de navegación y las migas de pan.
