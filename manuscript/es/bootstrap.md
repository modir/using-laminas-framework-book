# Apéndice C. Introducción a Twitter Bootstrap {#bootstrap}

Twitter Bootstrap, o simplemente Bootstrap, es un popular framework CSS que
permite que nuestro sitio web tenga un aspecto profesional y atractivo
visualmente incluso si no tenemos habilidades avanzadas de diseñador. En este
apéndice podemos encontrar información introductoria sobre Bootstrap y ejemplos
de su uso.

## Perspectiva general de los archivos de bootstrap

El código fuente de los componentes del framework Bootstrap están esparcidos en
varios archivos CSS. Pero se sabe que descargar varios archivos pequeños es
generalmente más lento que descargar un solo archivo grande. Por esta razón,
las hojas de estilo CSS de bootstrap se «concatenan» con un herramienta especial
y se distribuyen en un solo archivo llamado *bootstrap.css*.

Sin embargo, el archivo *bootstrap.css* tiene una desventaja: contiene muchos
caracteres (caracteres de espacio en blanco, caracteres de línea nueva,
comentarios, etc.) innecesarios para la ejecución del código que malgasta el
ancho de banda de la red cuando descargamos el archivo y se incremente el tiempo
que demora la carga de la página. Para corregir este problema se usa la
*miniaturización*, en ingles *minification*.

La miniaturización es un proceso que remueve todos los caracteres innecesarios
del código fuente sin cambiar su funcionalidad. El archivo de Bootstrap
miniaturizado se llama *bootstrap.min.css*.

T> En general es recomendable usar el archivo miniaturizado, especialmente en
T> entornos de producción porque se reduce el tiempo de carga de la página.
T> Sin embargo, si planeamos cambiar el código de Bootstrap para entender como
T> funciona es mejor usar el archivo no miniaturizado o incluso descargar los
T> archivos fuente originales (no concatenados).

Miremos en detalle los archivos que están en la carpeta *APP_DIR/public* y
y sus subcarpetas (figura C.1).

![Figura C.1. Estructura de la carpeta APP_DIR/public](../en/images/bootstrap/bootstrap_files.png)

La carpeta *css* contiene las hojas de estilo CSS:

* Los archivos *bootstrap.css* y *bootstrap.min.css* que son respectivamente el
  archivo usual de Bootstrap y el miniaturizado.

* El archivo *bootstrap-theme.css* es un archivo Bootstrap opcional de temas
  para una «mejora de la experiencia visual». El archivo
  *bootstrap-theme.min.css* es su versión miniaturizada.

* El archivo *style.css* es una hoja de estilo que podemos usar y extender
  para definir nuestras propias reglas CSS que se aplicaran sobre las reglas
  de Bootstrap. De esta manera podemos personalizar la apariencia de nuestra
  aplicación web.

* Además, podemos notar varios archivos con extensión .map, son los archivos
  MAP [^map] que se pueden usar para depurar el código CSS.

[^map]: Después de la concatenación y la miniaturización del código CSS es difícil
        de leer y depurar. Un archivo MAP (mapa de fuente) permite devolver
        el archivo miniaturizado a su estado usual.

La carpeta *fonts* contiene varios archivos, como *glyphicons-halflings-regular.svg*,
necesarios para que Bootstrap genere los iconos. Estos iconos, llamados también
Glyphicons, se usan para mejorar la apariencia de los botones y los menús
desplegables.

La subcarpeta *APP_DIR/public/js* contiene las extensiones JavaScript del
framework Bootstrap. Ellas están implementadas como complementos jQuery:

* El archivo *bootstrap.js* contiene código JavaScript de las extensiones de
  Bootstrap. El archivo *bootstrap.min.js* es su versión miniaturizada.

* Como las extensiones de Bootstrap se implementan como complementos de jQuery,
  ellas necesitan la última versión de la biblioteca jQuery. La carpeta *js*
  incluye el archivo *jquery-2.2.4.min.js* de la biblioteca jQuery.

## Sistema de rejilla

En la mayoría de los sitios web, el contenido se necesita organizar en una
estructura de tipo tabla que tiene filas y columnas. En la figura C.2, podemos
ver un ejemplo del diseño de un sitio web típico: este tiene un bloque en la
cabecera con un logo, una barra lateral del lado izquierdo, el contenido de la
página en el centro, la barra para la publicidad del lado derecho y el pie de página
en la parte inferior de la página. Estos bloques son organizados en un rejilla
aunque las celdas de la rejilla tienen diferentes anchuras (algunas celdas
pueden ocupar varias columnas).

![Figura C.2. Diseño típico de un sitio web](../en/images/bootstrap/typical_grid.png)

Bootstrap provee un diseño simple de rejillas para facilitar la organización del
contenido a nuestras páginas en filas y columnas.

Bootstrap provides a simple layout grid system to make it easy to
arrange content on your pages in rows and columns.

Cada fila consiste de un máximo de 12 columnas [^cols] (figura C.3). El ancho
de la columna es flexible y dependen del ancho su elemento contenedor en la
rejilla. La altura de la columna puede variar dependiendo de la altura del
contenido de la celda. El espacio entre columnas es de 30 pixeles (15 pixeles
de relleno a ambos lados de la columna).

[^cols]: No estamos obligados a colocar exactamente 12 columnas en una fila,
         podría haber menos columnas. Si tenemos menos columnas, el espacio a la
         derecha de la última columna quedará vacío.

![Figura C.3. Sistema de rejillas de Bootstrap](../en/images/bootstrap/bootstrap_grid.png)

Las columnas se pueden ampliar de modo que una sola celda toma el espacio
de varias columnas. Por ejemplo, en la figura C.3, la fila superior de la
rejilla consiste en 12 columnas y cada celda ocupa una sola columna. En la
fila de abajo, la primera celda ocupa 2 columnas, la segunda y la tercera
4 columnas y la cuarta celda ocupa 2 columnas (en total 12).

Q> **¿Por qué la rejilla de bootstrap consiste en solo 12 columnas?**
Q>
Q> Probablemente porque 12 columnas son suficientes para la mayoría de los sitios
Q> web. Si tenemos una rejilla con muchas columnas podría ser más difícil calcular
Q> el espacio de la columna sin una calculadora. Por fortuna, Bootstrap permite
Q> personalizar el número de columnas por fila y así podemos tener tantas
Q> columnas como deseemos.

### Definir la Rejilla

Para organizar los elementos de nuestra página web en una rejilla comenzaremos
definiendo el contenedor agregando un elemento `<div>` con la clase CSS
`.container`. Para agregar una nueva columna a la rejilla usamos un elemento
`<div>` con la clase CSS `.row`, como se muestra en el ejemplo de abajo:

~~~text
<div class="container">
  <div class="row">
  ...
  </div>
</div>
~~~

Para agregar columnas usamos un elemento `<div>` con un nombre de clase CSS
que varía de `.col-md-1` a `.col-md-12`. El número en el nombre de la clase
especifica cuantas columnas ocupará cada celda de la rejilla:

~~~text
<div class="container">
  <div class="row">
    <div class="col-md-1">Cell 1</div>
    <div class="col-md-5">Cell 2</div>
    <div class="col-md-6">Cell 3</div>
  </div>
</div>
~~~

En el ejemplo de arriba tenemos tres celdas. La primera celda tiene un ancho de 1
(esta usa la clase `col-md-1`), la segunda celda ocupa 5 columnas de la rejilla
(con la clase `.col-md-5`) y la tercera celda ocupa 6 columnas (con la clase
`col-md-6`).

A modo de ejemplo, vamos a definir el diseño que se vio en la figura C.2.
El diseño tiene un encabezado (un logo que ocupa 3 columnas), el area para el
contenido principal (ocupa 7 columnas), la barra lateral (ocupa 3 columnas),
la barra para la publicidad (2 columnas) y el pie de página. Para producir
este diseño podemos usar el siguiente código HTML:

~~~text
<div class="container">
  <!-- Header -->
  <div class="row">
    <div class="col-md-3">Logo</div>
    <div class="col-md-9"></div>
  </div>
  <!-- Body-->
  <div class="row">
    <div class="col-md-3">Sidebar</div>
    <div class="col-md-7">Page Content</div>
    <div class="col-md-2">Ads</div>
  </div>
  <!-- Footer -->
  <div class="row">
    <div class="col-md-12">Page Footer</div>
  </div>
</div>
~~~

### Compensación de Columnas

En las páginas web reales a veces la rejilla necesita tener espacios en blanco.
Podemos definir estos espacios en blanco compensando las celdas de la derecha
con la ayuda de las clases CSS que toman el rango de nombres desde `.col-md-offset-1`
a `.col-md-offset-12`. El número en el nombre de la clase especifica cuantas
columnas se deben saltar.

Por ejemplo, veamos la figura C.4:

![Figura C.4. Compensación de columnas](../en/images/bootstrap/offset-grid.png)

La rejilla de arriba tiene tres celdas, las últimas dos celdas están compensadas
a la derecha, con lo que se obtiene un espacio en blanco. Para definir una rejilla
como en la figura C.4 podemos usar el siguiente código HTML:

{line-numbers=of, lang=text}
~~~
<div class="container">
  <div class="row">
    <div class="col-md-2">Cell 1</div>
    <div class="col-md-4 col-md-offset-2">Cell 2</div>
    <div class="col-md-2 col-md-offset-2">Cell 3</div>
  </div>
</div>
~~~

### Rejillas Anidadas

Podemos crear diseños de página complejos anidando rejillas (por ejemplo, veamos
la figura C.5). Para anidar el contenido, agregamos un elemento `<div>` con la
clase `.row` y hacemos empezar las columnas `.col-md-*` dentro de una columna
`.col-md-*` que ya existe.

![Figura C.5. Rejillas anidadas](../en/images/bootstrap/nested-grid.png)

Para producir una rejilla como la que se muestra en la figura C.5, podemos usar
el siguiente código HTML:

~~~text
<div class="container">
  <div class="row">
    <div class="col-md-2">Cell 1</div>
    <div class="col-md-8">
      <!-- Nested grid -->
      <div class="row">
        <div class="col-md-4">Cell 21</div>
        <div class="col-md-4">Cell 22</div>
      </div>
      <div class="row">
        <div class="col-md-4">Cell 23</div>
        <div class="col-md-4">Cell 24</div>
      </div>
    </div>
    <div class="col-md-2">Cell 3</div>
  </div>
</div>
~~~

En el ejemplo de arriba, definimos una rejilla de tres celdas (marcada con color
gris): la primera celda ocupa dos columnas, la segunda ocupa 8 columnas y la tercera
2 columnas. Luego colocamos la rejilla anidada dentro de la segunda celda. Como
la celda padre tiene o columnas la celda hija también tiene 9 columnas.

### "Mobile First" Concept

Twitter Bootstrap está diseñado para funcionar en diferentes dispositivos, desde
amplias pantallas a tabletas y teléfonos celulares. Por esta razón, el diseño
de la rejilla se adapta a diferentes resoluciones de pantalla.

T> A esto también se le llama «responsabilidad», *responsiveness*, o «mobile first».
T> Bootstrap es «mobile-first», lo que significa que nuestro sitio web se verá
T> y se podrá usar sobre cualquier pantalla, sin importar su tamaño. Sin embargo,
T> esto no nos libera de la tarea de preparar detalladamente y planificar el
T> diseño.

Esta adaptación se consigue de dos maneras. La primera manera es que el ancho
de la columna dentro de la rejilla es flexible. Por ejemplo, si incrementamos el
tamaño de la ventana del navegador, la rejilla se escalará para llenar los
espacios en blanco.

¿Pero que sucede si nuestra página es muy ancha para la pantalla? Para ver la
parte que se oculta el visitante del sitio necesitaría desplazarse a la derecha.
En el caso de los teléfonos celulares y otros dispositivos de baja resolución
esta no es una buena solución. En su lugar, sería mejor que la rejilla se
«apilara» ajustándose al tamaño de la pantalla. Cuando la rejilla se apila,
sus columnas se transforman, haciendo que las celdas se posicionen una debajo
de las otras (ver figura C.6).

Para mejorar el control cuando la rejilla se «apila», Bootstrap provee unas clases
CSS adicionales: desde `.col-xs-1` a `col-xs-12` (la abreviatura «xs» significa
extra pequeños, *extra-small*, o teléfonos), `.col-sm-1` a `.col-sm-12` («sm»
que significa pequeños dispositivos, *small devices*, o tabletas) y `.col-lg-1`
a `.col-lg-12` (grandes dispositivos o pantalla ancha). Estas clases se pueden
usar junto con las clases `.col-md-1` -- `.col-md-12`, que ya usamos (la
abreviatura «md» significa «dispositivos medios», o computadoras de escritorio).

Por ejemplo, las clases `.col-md-*` definen que la rejilla se apilará cuando la
pantalla sea menor a 992 pixeles de ancho y horizantal para pantallas más anchas.
La clase `.col-sm-*` se puede usar para hacer que la rejilla se «apile» cuando
el ancho de la pantalla sea menor a 768 pixeles de ancho y horizontal por encima
de este punto. La clase `.col-xs-*` hace siempre a la rejilla horizontal sin
depender del ancho de la pantalla.

![Figura C.6. Adaptación de la rejilla al tamaño de la pantalla](../en/images/bootstrap/stacked-grid.png)

Tabla C.1 provee el resumen de las clases para rejilla y el ancho en el que se
comienzan a apilar las celdas.

{title="Tabla C.1. Cases CSS para definir la rejilla del diseño"}
|--------------------------------|----------------------------|
| *Nombre de la clase*           | *Breakdown width*          |
|--------------------------------|----------------------------|
| `.col-xs-*`                    | <768px                     |
|--------------------------------|----------------------------|
| `.col-sm-*`                    | >=768px                    |
|--------------------------------|----------------------------|
| `.col-md-*`                    | >=992px                    |
|--------------------------------|----------------------------|
| `.col-lg-*`                    | >=1200px                   |
|--------------------------------|----------------------------|

I> El sistema de rejillas de Bootstrap simplifica enormemente la posición de los
I> elementos de la página web. Sin embargo, no es obligatorio el uso del sistema
I> de rejillas. Por ejemplo, a veces necesitamos un diseño mucho más complejo y
I> el sistema de rejillas simple será insuficiente. En estos casos, podemos crear
I> y usar nuestro propio diseño usando los elementos HTML `<table>` o `<div>`.

## Componentes de Bootstrap para interfaces

En esta sección, daremos un resumen de los componentes útiles para interfaces
que provee Twitter Bootstrap. Planeamos usar algunos de estos componentes en
futuros ejemplos y esta sección debería dar una idea de como podemos usar estos
artilugios, *widgets*, en nuestro sitio web.

### Barra de Navegación

La barra de navegación está usualmente colocada en la parte superior de nuestro
sitio web y contiene los enlaces a las páginas principales como *Home*,
*Descargas*, *Soporte*, *Acercad de*, etc. Twitter Bootstrap provee un bonito
estilo visual para la barra de navegación, *navbar*, (ver figura C.7):

![Figura C.7. Barra de navegación](../en/images/bootstrap/navbar.png)

Como podemos ver en la figura de arriba, una barra de navegación típica tiene
una cabecera, donde la marca de nuestro sitio web se puede colocar, y los
enlaces a las páginas principales. Para colocar una barra de navegación en
nuestra página podemos usar el siguiente código HTML:

~~~text
<nav class="navbar navbar-default" role="navigation">
  <div class="navbar-header">
    <a class="navbar-brand" href="#">Hello World</a>
  </div>
  <ul class="nav navbar-nav">
    <li><a href="#">Home</a></li>
    <li><a href="#">Download</a></li>
    <li><a href="#">Support</a></li>
    <li><a href="#">About</a></li>
  </ul>
</nav>
~~~

Arriba en la línea 1, usamos el elemento `<nav>` que contiene toda la
información de la barra de navegación. La clase CSS asociada `.navbar` está
definida en Bootstrap y provee la apariencia básica de la barra de navegación.
La clase CSS `.navbar-default` especifica el tema «por defecto» para la barra
de navegación.

El [atributo](http://www.w3.org/TR/xhtml-role/) opcional `role` es un atributo
HTML que permite comentar los elementos de la página con información semántica,
que una maquina puede extraer, sobre el propósito de un elemento. En este
ejemplo, el atributo señala que el elemento `<nav>` se usa para navegar.

En las líneas 2-4, definimos el area de cabecera para la barra de navegación
que contiene el enlace *Hello World*. El enlace de la marca generalmente apunta
a la página principal de nuestro sitio web. El enlace tiene la clase
`.navbar-brand` que realza visualmente el texto.

En las líneas 5-10, especificamos los enlaces de navegación para las páginas
*Home*, *Descargas*, *Soporte* y *Acerca de*. Estos enlaces se organizan dentro
de un elemento de lista no ordenado `<ul>`. El elemento tiene las clases CSS
`.nav` y `.navbar-nav` que colocan los ítems en línea y provee el efecto de
sombreado cuando se coloca el cursor sobre el enlace.

#### Menú Desplegable

En la barra de navegación de Bootstrap es posible usar un menú desplegable como
elemento de navegación. Por ejemplo, si la sección *Soporte* de nuestro sitio
se subdivide en las páginas *Documentación* y *Ayuda* estas se pueden implementar
usando un menú desplegable (ver figura C.8).

![Figura C.8. Barra de navegación con un menú desplegable](../en/images/bootstrap/navbar_with_dropdown.png)

Podemos definir el menú desplegable remplazando el elemento *Soporte* de la lista
en el ejemplo anterior de la siguiente manera:

~~~text
<li class="dropdown">
  <a href="#" class="dropdown-toggle" data-toggle="dropdown">
     Support <b class="caret"></b>
  </a>
  <ul class="dropdown-menu">
    <li><a href="#">Documentation</a></li>
    <li><a href="#">Help</a></li>
  </ul>
</li>
~~~

En el código de arriba, usamos el elemento `<li>` con la clase CSS `.dropdown`
que marca al elemento como un menú desplegable (línea 1). Entre las líneas 2-4,
el elemento `<a>` define el enlace que se muestra cuando el menú está oculto
(el texto *Soporte* está seguido por un triangulo caret).

Cuando el visitante del sitio hace clic sobre el enlace, el menú desplegable
aparece (líneas 5-8). El elemento `<ul>` que define una lista no ordenada define
su apariencia visual con la clase `.dropdown-menu`. El menú desplegable contiene
dos elementos: los enlaces *Documentación* y *Ayuda*.

#### Barra de Navegación Plegable

De la misma manera que el sistema de rejillas, el componente *navbar* soporta
diferentes tipos de resoluciones de pantalla. Con dispositivos de baja resolución,
la barra de navegación puede plegarse como se muestra en la figura C.9.

![Figura C.9. Barra de Navegación Plegada](../en/images/bootstrap/navbar_collapsed.png)

Como podemos ver en el modo plegable, solo se muestra el encabezado y tres barras
horizontales a la derecha dentro del botón denotan el menú. Haciendo clic en el
botón se expandirán los elementos ocultos de la barra de navegación.

Definimos una barra de navegación plegable como se muestra en el siguiente ejemplo:

~~~text
<nav class="navbar navbar-default" role="navigation">
  <!-- Brand and toggle get grouped for better mobile display -->
  <div class="navbar-header">
    <button type="button" class="navbar-toggle" data-toggle="collapse"
            data-target=".navbar-ex1-collapse">
      <span class="sr-only">Toggle navigation</span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </button>
    <a class="navbar-brand" href="#">Hello World</a>
  </div>

  <!-- Collect the nav links, forms, and other content for toggling -->
  <div class="collapse navbar-collapse navbar-ex1-collapse">
    <ul class="nav navbar-nav">
      <li><a href="#">Home</a></li>
      <li><a href="#">Download</a></li>
      <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
          Support <b class="caret"></b>
        </a>
        <ul class="dropdown-menu">
          <li><a href="#">Documentation</a></li>
          <li><a href="#">Help</a></li>
        </ul>
      </li>
      <li><a href="#">About</a></li>
    </ul>
  </div><!-- /.navbar-collapse -->
</nav>
~~~

Abajo en las líneas 3-12, definimos el encabezado de la barra de navegación que
se mostrará con independencia de la resolución de la pantalla. El encabezado
contiene el botón con tres barras horizontales y un texto con la descripción
«Toggle navigation».

La parte plegable del menú se puede ver entre las líneas 15-30. En esta área,
colocamos nuestros enlaces de navegación y los elementos del menú desplegable.

#### Barra de Navegación con Estilo Inverso

La barra de navegación se puede mostrar usando dos «temas» estándar: el tema
*default* que vimos arriba y el tema *inverse*. El tema *inverse* hace que los
elementos de la barra de navegación se muestren en colores oscuros (figura C.10).
Es probable que ya hallamos visto la barra de navegación inversa en el demo
*Laminas Skeleton Application*.

![Figura C.10. Barra de Navegación con el Estilo Inverso](../en/images/bootstrap/navbar_inverse.png)

El estilo inverso se define simplemente remplazando la clase `.navbar-default`
en el elemento `<nav>` por la clase `.navbar-inverse`:

~~~text
<nav class="navbar navbar-inverse" role="navigation">
  ...
</nav>
~~~

### Migas de Pan

Las migas de pan, en ingles *Breadcrumbs*, es un componente de interfaz útil que
se puede usar junto con la barra de navegación para dar al visitante una idea
de su actual ubicación dentro del sitio (figura C.11).

![Figura C.11. Migas de Pan](../en/images/bootstrap/breadcrumbs.png)

En la figura de arriba tenemos un ejemplo de migas de pan para el sistema de
documentación de nuestro sitio. Como las paginas de documentación tienen varios niveles,
las migas de pan le dicen al usuario que página está visitando actualmente
y así no se perderá y será capaz de regresar a la página que visitó
previamente o a las páginas del nivel superior.

Para definir las migas de pan usamos una lista ordenada de elementos `<ol>` con
la clase CSS `.breadcrumb` (ver un ejemplo abajo):

~~~text
<ol class="breadcrumb">
  <li><a href="#">Home</a></li>
  <li><a href="#">Support</a></li>
  <li class="active">Documentation</li>
</ol>
~~~

### Paginación

El componente de paginación es útil cuando tenemos una larga lista de elementos
para mostrar. Si una larga lista se muestra en una sola página el usuario estará
obligado a hacer varios desplazamientos a través de la página para ver el final
de la lista. Para mejorar la experiencia del usuario separaremos la salida en
páginas y usaremos el componente de paginación para navegar entre las páginas
(figura C.12):

![Figura C.12. Paginación](../en/images/bootstrap/pagination.png)

Para definir la paginación como se muestra en la figura de arriba usamos el
siguiente código HTML:

~~~text
<ul class="pagination">
  <li><a href="#">&laquo; Newest</a></li>
  <li><a href="#">&lt; Newer</a></li>
  <li><a href="#">1</a></li>
  <li><a href="#">2</a></li>
  <li><a href="#">3</a></li>
  <li><a href="#">4</a></li>
  <li><a href="#">5</a></li>
  <li><a href="#">Older &gt;</a></li>
  <li><a href="#">Oldest &raquo</a></li>
</ul>
~~~

### Botones y Glyphicons

Twitter Bootstrap provee un bonito estilo visual para elementos de tipo botón
(figura C.13).

![Figura C.13. Botones](../en/images/bootstrap/btn_save_cancel.png)

Para crear botones como los de la figura de arriba usamos el siguiente código
HTML:

~~~text
<p>
  <button type="button" class="btn btn-primary">Save</button>
  <button type="button" class="btn btn-default">Cancel</button>
</p>
~~~

En el código de arriba usamos la clase CSS `.btn` para asignarle al botón su
estilo visual. Además, usamos la clase `.btn-primary` para el botón *Save*
(que es generalmente el botón primario de un formulario) y la clase `btn-default`
para un botón que comúnmente no es primario como *Cancel*.

Para expresar mejor la utilidad de los botones, Bootstrap provee varias clases adicionales:
`.btn-success` (para botones que aplican cambios sobre la página), `btn-info`
(para botones informativos), `.btn-warning` (for botones que pueden tener efectos
no deseados) y `btn-danger` (para botones que llevan a consecuencias irreversibles).
Para un ejemplo del uso de estos estilos para botones veamos el siguiente código:

~~~text
<p>
  <button type="button" class="btn btn-default">Default</button>
  <button type="button" class="btn btn-primary">Primary</button>
  <button type="button" class="btn btn-success">Success</button>
  <button type="button" class="btn btn-info">Info</button>
  <button type="button" class="btn btn-warning">Warning</button>
  <button type="button" class="btn btn-danger">Danger</button>
</p>
~~~

La Figura C.14 muestra la apariencia que resulta de los botones:

![Figura C.14. Estilo de los Botones](../en/images/bootstrap/btn_colors.png)

Bootstrap incluye 180 iconos (llamados Glyphicons) que se pueden usar junto con
botones, menus desplegables, enlaces de navegación, etc. Para agregar un icono
a un botón podemos usar un código como el siguiente:

~~~text
<p>
  <button type="button" class="btn btn-default">
      <span class="glyphicon glyphicon-plus"></span> Create
  </button>
  <button type="button" class="btn btn-default">
      <span class="glyphicon glyphicon-pencil"></span> Edit
  </button>
  <button type="button" class="btn btn-default">
      <span class="glyphicon glyphicon-remove"></span> Delete
  </button>
</p>
~~~

En el código de arriba definimos una caja de herramientas simple que contiene
tres botones: *Create*, *Edit* y *Delete*. Colocamos un icono en cada botón
con la ayuda del elemento `<span>`. El elemento `<span>` deberá tener dos clases:
la clase `.glyphicon` que es común a todos los iconos y la clase que representa
al nombre del icono. En el ejemplo de arriba, usamos la clase `.glyphicon-plus`
para el botón *Create*, `.glyphicon-pencil` para el botón `Edit` y `glyphicon-remove`
para el botón *Delete*. El resultado de nuestro trabajo se muestra en la figura
C.15.

![Figura C.15. Botones con Iconos](../en/images/bootstrap/btn_icons.png)

Podemos cambiar el tamaño del botón especificando la clase `.btn-lg` para un
botón grande, `.btn-sm` para uno pequeño y `.btn-xs` para un botón extra-pequeño.
Por ejemplo, en la figura C.16, se muestra un botón grande de *Download*.

![Figure C.16. Botón Grande](../en/images/bootstrap/btn_lg.png)

Para definir un botón como este podemos usar el siguiente código HTML:

~~~text
<button type="button" class="btn btn-success btn-lg">
  <span class="glyphicon glyphicon-download"></span> Download
</button>
~~~

## Customizing Bootstrap

Para terminar la introducción a Twitter Bootstrap describiremos como modificar
algunos aspectos del framework Bootstrap. Podemos personalizar la apariencia de
Bootstrap usando la página [Customize](http://getbootstrap.com/customize/) del
sitio web de Bootstrap (figura C.17).

![Figura C.17. Página de Personalización de Bootstrap](../en/images/bootstrap/bootstrap_customize.png)

En la página *Customize* podemos elegir que archivo fuente de Bootstrap incluir
dentro de la «concatenación» que produce el archivo *bootstrap.css*. Si no
necesitamos alguna funcionalidad podemos excluirla del archivo resultante para
reducir el trafico de red y el tiempo de carga de la página. Además, podemos
remover el código JavaScript de los componentes que no usamos del archivo
*bootstrap.js* resultante.

Además, podemos elegir diferentes parámetros CSS como el color de fondo, el
color y el tipo de letra del texto, etc. Hay más de un centenar de parámetros
que se pueden personalizar.

I> La personalización CSS es posible, porque los archivos fuentes de Bootstrap
I> se guardan en formato LESS [^less], que permite definir parámetros variables
I> (como `@bodyBackground` o `@textColor`). Una vez que los parámetros se definen
I> los archivos LESS se copilan dentro de un archivo CSS común, se miniaturizan
I> y se disponen para su descarga.

Cuando terminemos de colocar los parámetros personalizados, podemos movernos
hasta el final de la página *Customize* y presionar el botón *Compile and Download*.
Como resultado el archivo *bootstrap.zip* será descargado, este archivo contiene
todos los archivos personalizados de Bootstrap (los archivos CSS y JS normales y
los miniaturizados, además, de las fuentes glyphicons).

[^less]: LESS es un lenguaje dinámico de hojas de estilo que extiende el estándar
         CSS con características como: variables, *mixins* (insertar todas las
         propiedades de una clase CSS dentro de otra clase CSS), anidar bloques
         de código, operaciones aritméticas y funciones.

## Resumen

Twitter Bootstrap es un framework CSS desarrollado para hacer del diseño de
páginas web una tarea más fácil. Provee un estilo agradable por defecto
para la tipografía, tablas, formulas, botones e imágenes que podemos usar
para crear páginas de aspecto profesional en un minuto.

El sistema de rejillas que provee Bootstrap permite ordenar elementos en nuestra
página web con una rejilla de filas y columnas. La rejilla se adapta a las
diferentes resoluciones de pantallas consiguiendo que nuestra página se lea
igualmente bien en teléfonos mobiles, tabletas, computadoras de escritorio y
pantallas anchas.

Twitter Bootstrap provee además componentes para interfaces web útiles como menus
desplegables, barras de navegación, migas de pan, etc. Estos componentes son
interactivos, capacidad que se consigue con la extensión JavaScript incluida en
el framework.

Bootstrap está incluido en *Laminas Skeleton Application* y podemos usarlo luego de
instalar la aplicación o, alternativamente, podemos descargar la nueva versión
de Bootstrap desde la página del proyecto y personalizarla si lo deseamos.
