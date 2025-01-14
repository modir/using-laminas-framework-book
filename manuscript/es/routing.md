# Routing {#routing}

Cuando un usuario del sitio escribe una URL en el navegador web, la petición HTTP
se envía finalmente a una acción en un controlador de nuestro sitio web basado en Laminas.
En este capítulo aprenderemos como una aplicación basada en Laminas hace corresponder
una URL a una acción de nuestro controlador. La correspondencia se logra con la ayuda
del routing. El routing se implementa como una parte del componente `Laminas\Router`.

Laminas components covered in this chapter:

|--------------------------------|---------------------------------------------------------------|
| *Componente*                   | *Descripción*                                                 |
|--------------------------------|---------------------------------------------------------------|
| `Laminas\Router`                  | Implementa el soporte para routing.                           |
|--------------------------------|---------------------------------------------------------------|
| `Laminas\Barcode`                 | Componente auxiliar que implementa código de barras.          |
|--------------------------------|---------------------------------------------------------------|

## Estructura de la URL

Para entender mejor el routing necesitamos primero ver las estructura de una URL.
Una URL típica de una petición HTTP consiste en varias partes: esquema, nombre del
servidor, ruta, fragmento y consultas.

For example, let's look at the URL "http://site1.yourserver.com/path/to/page#section?a=1&b=2" (figura 5.1).

![Figure 5.1. Estructura URL Típica](../en/images/routing/url_segments.png)

Esta URL comienza con el esquema (el esquema generalmente es *http* o *https*).
Luego sigue el nombre del servidor que es el nombre de dominio de nuestro servidor
web (como, *site1.yourserver.com*). Opcionalmente los segmento de ruta (separados
por el carácter '/') luego del nombre del servidor. Si tenemos la ruta "/path/to/page",
tanto "path", "to" y "page" serán cada una un segmento de la ruta. Luego del numeral ('#')
tenemos el nombre del fragmento. Finalmente, después del signo de interrogación y
opcionalmente el par de consulta. Este consiste en uno o varios parámetros "name=value"
separados uno de otro por un ampersand ('&').

Cada parte de la URL usa una codificación especial de caracteres que se llama *codificación URL*.
Esta codificación asegura que la URL contiene solo caracteres "seguros" de la tabla
ASCII [^ascii]. Si la tabla contiene caracteres inseguros estos se reemplazan por
el carácter porcentaje ('%') seguido por dos dígitos hexadecimales (por ejemplo,
el carácter espacio se reemplaza por '%20').

[^ascii]: ASCII (American Standard Code for Information Interchange) es un conjunto
          de caracteres que puede ser usado para codificar caracteres del alfabeto
          Ingles. ASCII codifica 128 caracteres: dígitos, letras, signos de puntuación
          y varios otros códigos de control heredados de las maquina de escribir.

## Tipos de rutas

El *routing* es un mecanismo que permite hacer una correspondencia entre peticiones
HTTP y acciones en el controlador. Con el routing Laminas conoce cuales de los métodos
del controlador ejecutar como resultad de la petición. Por ejemplo, podemos hacer
corresponder la URL "http://localhost/" con el método `IndexController::indexAction()`
y la URL "http://localhost/about" con el método `IndexController::aboutAction()`.

I> Nosotros definimos la correspondencia entre las URL y el controlador con la ayuda
I> de *routes*.

Existen varios tipos de rutas provistas por Laminas Framework (ver la tabla 5.1).
Estos tipos de rutas se implementan como clases en el namespace `Laminas\Router\Http`.

{title="Table 5.1. Route Types"}
|--------------------------------|---------------------------------------------------------------|
| *Tipo de Ruta*                 | *Descripción*                                                 |
|--------------------------------|---------------------------------------------------------------|
| *Literal*                      | Coincidencia exacta contra la ruta de la URL.     |
|--------------------------------|---------------------------------------------------------------|
| *Segment*                      | Coincidencia contra un segmento de ruta (o varios segmentos) de la URL. |
|--------------------------------|---------------------------------------------------------------|
| *Regex*                        | Coincidencia entre la ruta de la URL y una expresión regular. |
|--------------------------------|---------------------------------------------------------------|
| *Hostname*                     | Coincidencia del nombre de dominio contra algún criterio.     |
|--------------------------------|---------------------------------------------------------------|
| *Scheme*                       | Coincidencia del esquema URL contra algún criterio.           |
|--------------------------------|---------------------------------------------------------------|
| *Method*                       | Coincidencia del método HTTP (ejemplo, GET, POST, etc.) contra algún criterio. |
|--------------------------------|---------------------------------------------------------------|

Cada tipo de ruta de la tabla de arriba (excepto el tipo *Method*) puede ser comparada
contra una parte específica (o varias partes) de la URL. El tipo de ruta *Method*
se compara contra el método HTTP (tanto GET como POST) recibido desde la petición HTTP.

## Combinar Tipos de Ruta

Las rutas se pueden combinar con la ayuda del tipo de ruta "aggregate" (ver tabla 5.2).
Los tipos de ruta compuestos permiten definir arbitrariamente reglas complejas para la comparación
de URL.

{title="Table 5.2. Tipo de Ruta Aggregate"}
|--------------------------------|---------------------------------------------------------------|
| *Tipo de Ruta*                 | *Descripción*                                                 |
|--------------------------------|---------------------------------------------------------------|
| *SimpleRouteStack*             | Agregar diferentes tipos de rutas en una lista con prioridades. |
|--------------------------------|---------------------------------------------------------------|
| *TreeRouteStack*               | Agregar diferentes tipos de rutas en una estructura tipo árbol. |
|--------------------------------|---------------------------------------------------------------|
| *Part*                         | Agregar diferentes tipos de rutas a un sub árbol.             |
|--------------------------------|---------------------------------------------------------------|
| *Chain*                        | Agregar diferentes tipos de rutas a una cadena (sub árbol degenerativo). |
|--------------------------------|---------------------------------------------------------------|

Los tipos de ruta `TreeRouteStack` y `SimpleRouteStack` se usan como los tipos de
ruta de primer nivel. El tipo de ruta *SimpleRouteStack* permite organizar diferentes
rutas en una lista con prioridades. *TreeRouteStack* permite *anidar* diferentes
rutas formando un *árbol*.

La figura 5.2 muestra el diagrama de herencia de la clase route.

![Figure 5.2. Diagrama de herencia de la clase route](../en/images/routing/route_inheritance.png)

Como podemos ver en la imagen, todas las clases de ruta heredan de la interface `RouteInterface`
(revisaremos más detalladamente esta interface en la sección *Escribir un Tipo de Ruta Propia*
que está más adelante en esta sección). `SimpleRouteStack` es la clase padre de la
clase `TreeRouteStack`, esta hereda su comportamiento de la clase padre (permitiendo
organizar las rutas en un lista con prioridades) y lo extiende (permitiendo organizar
rutas en sub árboles). Las clases `Part` y `Chain` se derivan de la clase `TreeRouteStack`
que las usa internamente para construir sub árboles y cadenas de rutas hijas.

### Simple Route Stack

El `SimpleRouteStack` permite combinar diferentes rutas en una lista con prioridades.
Un ejemplo de una lista como esta se puede ver en la pila de rutas de la parte
izquierda de la figura 5.3. La lista de ejemplo contiene varias rutas *Literal* y
varias rutas *Segment*.

Cuando se prueba la coincidencia con la petición HTTP, `SimpleRouteStack` recorre
la lista de rutas e intenta hacer coincidir cada ruta. Cada ruta de la lista tiene
una prioridad; la ruta con la mayor prioridad se visita primero. La búsqueda termina
una vez que alguna ruta coincide con la petición HTTP. Si ninguna ruta coincide el
error "not fount" se lanza.

![Figure 5.3. Un ejemplo de Simple Route Stack (izquierda) y Tree Route Stack (derecha)](../en/images/routing/route_tree.png)

### Tree Route Stack

La clase `TreeRouteStack` extiende de la clase `SimpleRouteStack` lo que significa
que esta se puede organizar en una lista de prioridades, además, provee la capacidad
de anidar rutas en subárboles y cadenas. Una ejemplo de una pila de rutas en árbol se
presenta en la parte derecha de la figura 5.3. La lista contiene una ruta `Literal`,
una cadena de rutas `Literal` y `Segment`, y un subárbol con dos ramas: una rama
contiene una ruta única `Segment` y otra rama contiene rutas `Scheme`, `Hostname` y
`Segment`.

La pila de rutas en árbol ejecuta la comparación con la petición de la siguiente
manera. Esta recorre los elementos de la lista de prioridades (con líneas punteadas en la figura 5.3),
comenzando con las rutas de más alta prioridad. Si un elemento determinado es una ruta `Chain`
o una ruta `Part`, se procesa cada ruta anidada desde su ruta padre al hijo. Si la
ruta padre coincide, los hijos (en líneas solidas) se analizan. La ruta anidada
se considera coincidente si al menos una ruta coincide en algún nivel del árbol
(o cadena).

Cada ruta en el árbol (o cadena) consume una parte de la URL (figura 5.4). La ruta
padre se compara contra la primera parte de la URL, el hijo se compara con la segunda
parte y así sucesivamente hasta el final de la cadena URL.

![Figure 5.4. Un ejemplo de la comparación anidada de rutas](../en/images/routing/route_matching.png)

## Configuración del Routing

Típicamente no creamos al pila de rutas (o árbol) nosotros mismos en su lugar le damos
las instrucciones a Laminas sobre como hacer esto.

La configuración del routing para un módulo se guarda en el archivo de configuración
*module.config.php*:

The routing configuration for a module is stored in *module.config.php* configuration file:

~~~php
<?php
use Laminas\Router\Http\TreeRouteStack;

return [
    //...
    'router' => [
        'router_class' => TreeRouteStack::class,
        'routes' => [
            // Register your routing rules here...
        ],
        'default_params' => [
            // Specify default parameters here for all routes here ...
        ]
    ],
];
~~~

Arriba en la línea 6 tenemos la llave *router* y debajo de esta está la subllave
*routes* (línea 8) que contiene las reglas de routing.

Podemos especificar la clase de ruta de primer nivel a usar modificando (tanto `TreeRouteStack`
o `SimpleRouteStack`) el parámetro `router_class` (línea 7). Si este parámetro
no está presente la clase `TreeRouteStack` se usa por defecto.

Podemos usar la llave opcional `default_params` (línea 11) para definir los
*valores por defecto* de los parámetros para todas las rutas. Sin embargo, generalmente
no usamos esta llave y definimos los valore por defecto en la base de cada ruta.

I> Mostraremos como extraer parámetros de la ruta más adelante en este capítulo.

### Configurar Rutas Simples

Una ruta típica tiene un *name*, un *type* y unas *options*:

  * El *name* se usa para identificar univocamente la ruta.
  * El *type* define el fully qualified name del tipo de ruta (la clase PHP que
    implementa el algoritmo de comparación).
  * *options* es una arreglo que incluye la cadena de la *ruta* que debería
    compararse con parte de la URL y varios parámetros llamados *dafaults*.

La configuración para cada ruta bajo la subllave `routes` puede tener el siguiente
formato:

~~~php
'<route_name>' => [
    'type' => '<route_type_class>',
    'priority' => <priority>,
    'options' => [
        'route' => '<route>',
        'defaults' => [
            //...
        ],
    ],
]
~~~

Arriba `<route_name>` es una variable que debería ser el nombre de la ruta. El nombre
de una ruta debe estar en minúsculas, por ejemplo, "home" o "about". La llave `type`
especifica el nombre completo de la clase ruta.

La llave opcional `priority` permite definir la prioridad (que debe ser un número
entero) de la ruta dentro de la lista de prioridades (las rutas con una prioridad
mayor se visitarán primero). Si se omite la llave `priority` las rutas se visitan
en orden LIFO [^lifo].

I> Las rutas que tienen igual prioridad se visitaran en un orden LIFO. Por esta razón
I> para conseguir un mejor rendimiento debemos registrar las rutas que coincidirán
I> más a menudo en último lugar y registrar las rutas menos comunes al principio.

[^lifo]: LIFO (siglas en ingles para Last In, First Out) se usa para organizar elementos
         en una pila donde el elemento más alto es el que se agrega de último y es
         el primero que se quita.

La llave `options` define el arreglo de opciones de ruta. Discutiremos las opciones
en la siguiente sección de este capítulo.

### Configurar Rutas Anidadas

Para organizar rutas en subárboles agregamos la llave `child_routes` a la definición
de la ruta y agregamos las rutas hijas debajo de esta llave, de la siguiente manera:

~~~php
'<route_name>' => [
    'type' => '<route_type_class>',
    'priority' => <priority>,
    'options' => [
        //...
    ],
    'child_routes' => [
        // Add child routes here.
        // ...
    ]
],
~~~

Si necesitamos organizar las rutas en una cadena (subárbol degenerativo) agregamos
la llave `chain_routes` a la configuración de la ruta.

~~~php
'<route_name>' => [
    'type' => '<route_type_class>',
    'priority' => <priority>,
    'options' => [
        //...
    ],
    'chain_routes' => [
        // Add chained routes here.
        // ...
    ]
],
~~~

T> Mirando los dos ejemplos de arriba no veremos el uso explicito de los tipos de
T> ruta `Part` y `Chain` por que (para nuestra conveniencia) ellas son usadas automáticamente
T> por Laminas cuando encuentra las llaves `child_routes` y `chain_routes` en nuestra
T> configuración de routing.

### Configuración por Defecto del Routing in Laminas Skeleton Application

Ahora que sabemos como configurar y organizar rutas en estructuras compuestas,
vamos a mirar un ejemplo de la vida real. En una Laminas Skeleton Application fresca
la configuración de routing se ve de la siguiente manera:

~~~php
<?php
use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;

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

  //...
];
~~~

En la configuración presentada arriba tenemos listadas dos reglas de routing una
luego de otra: primero tenemos la ruta "home" (línea 8) y luego tenemos la ruta
"application" (línea).

La ruta "home" hace corresponder la ruta URL vacía con la acción "index" del controlador
`IndexController`. Por ejemplo, escribimos en nuestro navegador "http://localhost/"
para ver la pagina Home de nuestro sitio. Esta ruta es de typo "Literal".

La ruta "application" (de tipo "Segment") hace corresponder las URL que tienen un
aspecto como "http://localhost/application", "http://localhost/application/about",
"http://localhost/application/news", etc, con la acción del controlador `IndexController`.
El nombre exacto de la acción se determina con el parámetro de "acción". El valor
por defecto para este parámetro es "index". Esto significa que si no especificamos
ninguna acción la solicitud se envía a la acción "index".

Esta configuración corresponde a la pila de rutas en árbol se muestra en la figura
5.5:

![Figure 5.5. Pila de ruta por defecto en la Skeleton Application](../en/images/routing/skeleton_route_tree.png)

En las siguientes secciones daremos más ejemplos sobre como usar los tipos de rutas
en nuestro sitio web.

## Tipo de Ruta Literal

Con el tipo de ruta *Literal* la coincidencia de la ruta se alcanza solo cuando
tenemos una coincidencia exacta de la cadena de ruta contra la ruta URL. Usamos
generalmente el tipo *Literal* para las URLs que deben ser cortas y fáciles de recordar,
como '/about' o '/news'.

Abajo se presenta la definición de una ruta llamada "home". La ruta "home" se corresponde
usualmente a la acción "index" del `IndexController` y apunta a la página *Home*
de nuestro sitio web:

~~~php
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
~~~

La línea 2 de este ejemplo dice que el tipo de ruta *Literal*. El algoritmo exacto
de comparación de ruta se implementa en la clase `Laminas\Rotuter\Http\Literal`.

La línea 4 define la cadena de ruta que se comparará contra la ruta URL (la barra '/'
significa que la parte-URL está vacía). Cuando tenemos un tipo de ruta literal la
coincidencia se alcanza solo cuando la ruta coincide exactamente. Por ejemplo, si
tenemos la URL "http://localhost/" o "http://localhost", estas coincidirán con la
ruta '/'.

Las líneas 5-8 definen los `defaults`, estos son los parámetros regresados por el router
si la ruta coincide. Los parámetros `controller` y `action` definen el controlador
y el método de acción del controlador que será ejecutado. Además, podemos definir
otros parámetros aquí si lo necesitamos.

Como otro ejemplo de un tipo de ruta *Literal*, vamos a agregar la ruta '/about' para
la página *About* que hemos creado antes en la sección *Vistas* del capítulo
[Modelo-Vista-Controlador](#mvc). Para crear un ruta, agregamos las siguientes líneas
después de la definición de la ruta "home" dentro de nuestro archivo *module.config.php*:

~~~php
'about' => [
    'type' => Literal::class,
    'options' => [
        'route' => '/about',
        'defaults' => [
            'controller' => Controller\IndexController::class,
            'action'     => 'about',
        ],
    ],
],
~~~

Si ahora abrimos la URL "http://localhost/about" en nuestro navegador web debemos
ver la página *About*.

## Tipo de Ruta Segment

El tipo de ruta *Segment* permite comparar la ruta contra uno o varios segmentos
de una URL.

I> Si vemos el archivo *module.config.php* podemos encontrar que el tipo de ruta
I> *Segment* se usa dentro de la ruta "application" para relacionar automáticamente
I> las acciones del controlador `IndexController` con las URLs. Si agregamos un
I> método de acción a la clase `IndexController` este comienza a estar disponible
I> desde una URL como esta "http://localhost/applications/&lt;action&gt;".
I> Por ejemplo, podemos ver la página *About* del sitio con la siguiente URL:
I> "http://localhost/application/about".

Para mostrar como se crea una ruta tipo *Segment* vamos a implementar una acción
en el controlador que genere una imagen simple con un código de barras. Los códigos
de barras son usados ampliamente en supermercados para el reconocimiento óptico
de los productos que tenemos en nuestro carro de compras. Los código de barras
pueden ser de diferente tipo y tener diferentes etiquetas. Nosotros usaremos la
ruta tipo *Segment* para relacionar la acción con una URL como
"http://localhost/barcode/&lt;type&gt;/&lt;label&gt;".

I> Para ser capaces de usar códigos de barra necesitamos instalar el componente
I> `Laminas\Barcode`, usando Composer escribimos el siguiente comando:
I>
I> `php composer.phar require laminas/laminas-barcode`

W> Debemos saber que para que las imágenes de los códigos de barra funcionen
W> necesitamos tener instalada y activada la extensión del motor de PHP GD[^gd].
W> En GNU/Linux Ubuntu podemos instalar esta extensión con el siguiente comando:
W>
W> `sudo apt-get install php-gd`
W>
W> Después de instalar la extensión, reiniciamos Apache para aplicar los cambios.

[^gd]: La extensión de PHP GD permite crear imágenes en diferentes formatos (como JPEG, PNG, GIF, etc.)

Primero se define la ruta "barcode" en el archivo *module.config.php*:

~~~php
'barcode' => [
    'type' => Segment::class,
    'options' => [
        'route' => '/barcode[/:type/:label]',
        'constraints' => [
            'type' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'label' => '[a-zA-Z0-9_-]*'
        ],
        'defaults' => [
            'controller' => Controller\IndexController::class,
            'action' => 'barcode',
        ],
    ],
],
~~~

Los segmentos de la ruta (línea 4) puede ser constantes o variables. Podemos definir
los segmentos variables usando "comodines". Tenemos tres segmentos: `barcode`,
`:type` y `:label`. El segmento `barcode` es constante mientras que los otros dos
son comodines (los comodines deben comenzar con dos puntos).

En la subllave `constraints` (líneas 5-8) especificamos el comodín. Definimos la
expresión regular `[a-zA-Z][a-zA-Z0-9_-]*` que limita el comodín `:type` a ser una
letra y (opcionalmente) contener varias letras, dígitos, guiones bajos o caracteres
en minúsculas. La restricción para el comodín `:label` es casi la misma pero este
segmento puede comenzar con cualquier carácter (tanto letras, dígitos, guiones
bajos o el signo menos).

Los segmentos opcionales se pueden encerrar dentro de corchetes. En nuestro ejemplo
son opcionales tanto los segmentos `:type` como `:label`.

En las líneas 9-12, definimos los `defaults`, que son los parámetros que serán retornados
por el router. Los elementos defaults `controller` y `action` especifican cuales
controladores y métodos de acción se ejecutan al coincidir una ruta.

Luego, agregamos el método `barcodeAction()` dentro de la clase `IndexController`:

~~~php
// Add name alias in the beginning of the file
use Laminas\Barcode\Barcode;

// ...

// The "barcode" action
public function barcodeAction()
{
  // Get parameters from route.
    $type = $this->params()->fromRoute('type', 'code39');
    $label = $this->params()->fromRoute('label', 'HELLO-WORLD');

    // Set barcode options.
    $barcodeOptions = ['text' => $label];
    $rendererOptions = [];

    // Create barcode object
    $barcode = Barcode::factory($type, 'image',
                 $barcodeOptions, $rendererOptions);

    // The line below will output barcode image to standard
    // output stream.
    $barcode->render();

    // Return Response object to disable default view rendering.
    return $this->getResponse();
}
~~~

En las líneas 10-11 traemos los valores de los comodines `type` y `label` desde la
ruta. Hacemos esto con la ayuda del método `fromRoute()` del complemento controlador
`Params`. Análogamente al método `fromQuery()` este toma dos argumentos: el nombre de
la variable y su valor por defecto.

Para generar la imagen de código de barras usamos el componente `Laminas\Barcode`.
En la línea 14 definimos la etiqueta de texto para el código de barras. En las líneas
18-19 creamos el objeto `Barcode` con el método `factory`. Finalmente, en la línea
23 creamos la imagen dirigiéndola a un flujo de salida PHP.

I> `Laminas\Barcode` es un componente auxiliar usado para generar diferentes imágenes
I> de código de barras. Para información adicional sobre este componente podemos
I> revisar la sección correspondiente del manual de referencia de Laminas Framework.

En la línea 26 regresamos el objeto `Response` para evitar que se genere la vista
por defecto.

Ahora podemos ingresar la URL "http://localhost/barcode" dentro de nuestro navegador
para ver la imagen de código de barras (figura 5.6):

![Figure 5.6. Ejemplo de una imagen de código de barras](../en/images/routing/barcode.png)

Como tenemos comodines en la ruta podemos pasar los parámetros `type` y `label`
para la imagen de código de barras en la URL. Abajo se muestran varias URL de
ejemplo (las imágenes de código de barras correspondientes se muestran en la
figura 5.7):

~~~
a. http://localhost/barcode/code39/HELLO-WORLD
b. http://localhost/barcode/leitcode/12345
c. http://localhost/barcode/identcode/98765453212
d. http://localhost/barcode/postnet/123456
e. http://localhost/barcode/planet/1234567890123
f. http://localhost/barcode/upca/12345678901
g. http://localhost/barcode/code128/ABCDEF
h. http://localhost/barcode/ean2/12
~~~

![Figure 5.7. Tipos de código de barras](../en/images/routing/barcodes.png)

## Tipos de Ruta Regex

El tipo de ruta con expresiones regulares (*Regex*) es útil si tenemos URLs que
se pueden comparar contra una expresión regular.

Por ejemplo, asumiendo que queremos crear un sistema simple de documentación para
nuestro sitio web. La documentación consistiría en páginas "estáticas" que se hacen
corresponder con URLs del tipo */doc/&lt;page_name&gt;.html*.

I> Con el termino "página estática" nos referimos a una página que contiene principalmente
I> código HTML estático más varios fragmentos de código PHP en linea. Para cada
I> página simple no necesitamos crear acciones separadas en el controlador. Todas las
I> páginas "estáticas" se pueden servir con una sola de acción en el controlador.

Vamos a implementar la ruta que servirá las páginas "estáticas" del sitio. Como
las páginas "estáticas" son simples, generalmente no necesitamos agregar un método
de acción por página en el controlador. Todas las páginas serán manejadas por una
sola acción: `IndexController::docAction()`.

Primero, agregamos la ruta *Regex* llamada "doc" en el archivo *module.config.php*:

~~~php
'doc' => [
    'type' => Regex::class,
    'options' => [
        'regex'    => '/doc(?<page>\/[a-zA-Z0-9_\-]+)\.html',
        'defaults' => [
            'controller' => Controller\IndexController::class,
            'action'     => 'doc',
        ],
        'spec'=>'/doc/%page%.html'
    ],
],
~~~

La línea 2 define el tipo de ruta como *Regex*. En la línea 4 tenemos le expresión
regular `/doc(?<page>\/[a-zA-Z0-9_\-]+)\.html`. Estas coincidirán con URLs como
"/doc/contents.html", "/doc/introduction.html", etc. La expresión contiene una
captura [^capture] llamada "page" que será regresada por la ruta encontrada
junto con los parámetros por defecto .

La linea 9 contiene la opción `spec` que es usada por la ruta para generar URLs
(hablaremos sobre la generación de URLs por la ruta luego en este capítulo).

[^capture]: Con las expresiones regulares de PHP PCRE es posible nombrar un sub-patrón
            usando la sintaxis `(?P<name>pattern)`. Este sub-patrón se indexará
            en el arreglo de *comparación* por su nombre.

I> No olvidemos agregar la siguiente línea al comienzo del archivo `module.config.php`:
I>
I> `user Laminas\Router\Http\Regex;`

Luego, agregamos la siguiente acción a la clase `IndexController`:

~~~php
public function docAction()
{
    $pageTemplate = 'application/index/doc'.
        $this->params()->fromRoute('page', 'documentation.phtml');

    $filePath = __DIR__.'/../../view/'.$pageTemplate.'.phtml';
    if(!file_exists($filePath) || !is_readable($filePath)) {
        $this->getResponse()->setStatusCode(404);
        return;
    }

    $viewModel = new ViewModel([
            'page'=>$pageTemplate
        ]);
    $viewModel->setTemplate($pageTemplate);

    return $viewModel;
}
~~~

En las líneas 3-4 de arriba recuperamos el parámetro `page` de la ruta (¿recuerdas
la captura llamada "page" de nuestra expresión regular?) y la guardamos en la variable
`$pageTemplate`. Usaremos la variable `$pageTemplate` para determinar el nombre del
plantilla de la vista que se pasará al view resolver. Luego, en las líneas 6-10 revisamos
si el archivo existe y si no regresamos el código de estado 404 "Not Found" que
forzará que Laminas muestre la página de error. En la línea 12 creamos el contenedor
de la variable `ViewModel` y en la línea 15 colocamos el nombre del plantilla de la
vista que se mostrará.

Para ver el sistema de documentación en acción creamos un par de plantillas "estáticas":
la página para la Tabla de Contenidos (`contents.phtml`) y la página de Introducción
(`introduction.phtml`). Creamos la subcarpeta *doc* debajo de la carpeta
*view/application/index* del modulo `Application` y colocamos la plantilla de vista
*contents.phtml* ahí:

~~~php
<h1>Table of Contents</h1>

<ul>
    <li>
        <a href="<?= $this->url('doc', ['page'=>'introduction']); ?>">
            Introduction
        </a>
    </li>
</ul>
~~~

En las líneas de arriba escribimos el código HTML para la cabecera de la página
de la "Tabla de Contenidos" y una lista que contiene un solo elemento llamado "Introduction"
apuntando a la página "estática" Introduction. El enlace URL se genera con el
ayudante de vista `Url` (para más detalles obre el ayudante `Url` podemos ver
las siguientes secciones de este capítulo).

Luego agregamos la página *introduction.phtml* dentro de la misma carpeta *doc*:

~~~php
<h1>Introduction</h1>

<p>Some introductory materials.</p>
~~~

En las líneas de arriba definimos las etiquetas HTML para una página de Introducción
simple.

Ahora, si abrimos la URL "http://localhost/doc/contents.html" en nuestro navegador,
podremos ver un sistema bueno y simple de documentación que podemos extender y usar
en nuestro sitio web (figura 5.8):

![Figure 5.8. Página "estática"](../en/images/routing/static_page.png)

Haciendo click en el enlace *Introduction* iremos a la página estática "Introduction".
Además, podemos agregar otras páginas al directorio *doc* haciendo que estén automáticamente
disponibles para los usuarios del sitio web a través de nuestra ruta *Regex*.

I> Una de las desventajas de este sistema de documentación es que no funciona bien
I> si anidamos páginas colocándolas en subcarpetas debajo de la carpeta *doc*.
I> La razón de esta limitación recae en la manera en que la ruta *Regex* construye
I> URLs. No podemos construir rutas que contienen caracteres barra, como esta es
I> un carácter "inseguro" la URL se codificará automáticamente. Sortearemos este
I> problema con el tipo de ruta custom que crearemos al final de este capítulo.

## Otros Tipos de Ruta

Los tipos de rutas *Hostname*, *Scheme* y *Method* se usan menos comúnmente en
comparación con los tipos de ruta mencionados anteriormente.

### Hostname

El tipo de ruta *Hostname* se puede usar, por ejemplo, si desarrollamos un Sistema
Gestor de Contenidos (CMS) [^cms] que serviría varios sitios web al mismo tiempo, cada
sitio usa un subdominio diferente. En este caso definiremos la ruta *Hostname*
como padre y dentro de ella rutas hijas de otro tipo:

[^cms]: Un Sistema Gestor de Contenidos es un sitio web que permite la creación,
        edición y publicación de contenido colaborativo (blogs, páginas, documentos,
        vídeos, etc.) usando una interface web centralizada. Un CMS hace posible
        que no programadores puedan ejecutar las tareas diarias de un sitio web
        como publicar contenido.

~~~php
'routename' => [
    'type' => Hostname::class,
    'options' => [
        'route' => ':subdomain.yourserver.com',
        'constraints' => [
            'subdomain' => '[a-zA-Z][a-zA-Z0-9_-]*'
        ],
        'defaults' => [
        ],
    ],
    'child_routes'=>[
        //...
    ],
],
~~~

En el ejemplo de arriba, en la línea 1 definimos la ruta que tiene como tipo: *Hostname*.
La opción `route` (línea 4) define el nombre de dominio contra el que se compara.
El `:subdomain` es un comodín que puede tomar diferentes valores de subdominio.
La llave `constraints` define la expresión regular con la que el parámetro subdominio
debe coincidir. La ruta *Hostname* diferenciará nuestro dominio y cada sitio se
comportará diferente dependiendo del valor del parámetro `subdomain` regresado:

~~~php
// An example of an action that uses parameters returned by
// Hostname route.
public function someAction()
{
    // Get the 'subdomain' parameter from the route.
    $subdomain = $this->params()->fromRoute('subdomain', null);

    // Use different logic based on sub-domain.
    //...

    // Render the view template.
    return new ViewModel();
}
~~~

### Scheme

El tipo de ruta *Scheme* es útil si necesitamos manejar los protocolos HTTP y
HTTPS [^https] de maneras diferentes.

[^https]: El protocolo HTTPS se usa generalmente para conexiones seguras como páginas
          de cuentas bancarias o de carro de compras. Cuando usamos HTTPS los datos
          de la petición se trasmiten dentro de un canal con Secure Socket Layer (SSS)
          con y no esta disponible para terceros.

Una configuración típica del tipo de ruta *Scheme* se presenta abajo:

~~~php
'routename' => [
    'type' => Scheme::class,
    'options' => [
        'scheme' => 'https',
        'defaults' => [
            'https' => true,
        ],
    ],
    'child_routes'=>[
        //...
    ],
],
~~~

Arriba definimos la ruta de tipo *Scheme*. Esta toma la opción `scheme`
que sería el esquema contra el que se hará la comparación (`http` o `https`).
Si el esquema en la petición de HTTP es exactamente el mismo que la opción
`scheme`, la ruta se considera coincidente. Podemos usar la llave `defaults` para
retornar algunos parámetros desde la ruta coincidente. En el ejemplo de arriba,
el parámetro booleano `https` se regresará.

### Method

El tipo de ruta *Method* se puede usar si necesitamos dirigir las peticiones *GET*
y *POST* a diferentes acciones en el controlador. Su configuración típica es la
siguiente:

~~~php
'routename' => [
    'type' => Method::class,
    'options' => [
        'verb' => 'post',
        'defaults' => [
        ],
    ],
    'child_routes'=>[
        //...
    ],
],
~~~

Arriba definimos la ruta que tiene como tipo: *Method*. Esta toma la opción `verb`
que puede ser una lista separada por comas de acciones HTTP validas (*get*, *post*,
*put*, etc.)

## Extraer Parámetros desde la Ruta

Cuando una ruta coincide el router (la clase route de primer nivel) regresa algunos
parámetros: los "defaults" (parámetros listados en la sección `defaults` de la configuración
del routing) más cualquier otro parámetro comodín extraído de la URL.

En nuestro controlador necesitaremos a menudo recuperar estos parámetros. Nosotros
ya hicimos esto en los ejemplos de arriba. En esta sección daremós un resumen.

Para recuperar un parámetro de la ruta desde nuestro método de acción en el controlador
típicamente usamos el controlador complementario `Params` y su método `fromRoute()`,
que toma dos argumentos: el nombre del parámetro a recuperar y el valor
a regresar si el parámetro no esta presente.

Además, el método `fromRoute()` se puede usar para recuperar todos los parámetros
en un solo arreglo. Para hacer esto llamamos al método `fromRoute()` sin argumentos
como se muestra en el ejemplo de abajo:

~~~php
// An example action.
public function someAction()
{
    // Get the single 'id' parameter from route.
    $id = $this->params()->fromRoute('id', -1);

    // Get all route parameters at once as an array.
    $params = $this->params()->fromRoute();

    //...
}
~~~

### Recuperar la RouteMatch y el Router Object

Cuando una ruta coincide, la clase router crea internamente una instancia de la clase
`Laminas\Router\RouteMatch` que provee los métodos para extraer el nombre de la ruta
coincidente y los parámetros extraídos de la ruta. Los métodos útiles de la clase
`RouteMatch` se listan en la tabla 5.3:

{title="Tabla 5.3. Métodos de la clase Laminas\Router\RouteMatch"}
|--------------------------------|---------------------------------------------------------------|
| *Nombre del Método*            | *Descripción*                                                 |
|--------------------------------|---------------------------------------------------------------|
| `getMatchedRouteName()`        | Trae el nombre de la ruta coincidente.                        |
|--------------------------------|---------------------------------------------------------------|
| `getParams()`                  | Trae todos los parámetros.                                    |
|--------------------------------|---------------------------------------------------------------|
| `getParam($name, $default)`    | Trae un parámetro específico.                                 |
|--------------------------------|---------------------------------------------------------------|

I> En la mayoría de los casos, será suficiente usar el controlador complementario
I> `Params`, pero alternativamente podemos usar el objeto `RouteMatch` para lograr
I> la misma tarea.

Para traer el objeto `RouteMatch` desde nuestro método de acción en el controlador podemos
usar el siguiente código:

~~~php
// An example action.
public function someAction()
{
    // Get the RouteMatch object.
    $routeMatch = $this->getEvent()->getRouteMatch();

    // Get matched route's name.
    $routeName = $routeMatch->getMatchedRouteName();

    // Get all route parameters at once as an array.
    $params = $routeMatch->getParams();

    //...
}
~~~

En la línea 5 del código de arriba usamos el método `getEvent()` de la clase base
`AbstractActionController` para recuperar el objeto `MvcEvent` que representa
el evento (en Laminas el ciclo de vida de la aplicación consiste en eventos). Nosotros
usamos el método `getRouteMatch()` de la clase `MvcEvent` para recuperar el
objeto `RouteMatch`.

En la línea 8 usamos el método `getMatchedRouteName()` para recuperar el nombre
de la ruta que coincidió con la petición HTTP y en la línea 11 recuperamos todos
los parámetros de la ruta.

Además, la clase `MvcEvent` se puede usar para recuperar la ruta (la clase ruta
de primer nivel). Podemos hacer esto con el método `getRouter()` de la clase `MvcEvent`,
como se muestra más abajo:

~~~php
    // Call this inside of your action method
    // to retrieve the RouteStackInterface for the router class.
    $router = $this->getEvent()->getRouter();
~~~

En el código de arriba usamos el método `getRouter()` que regresa la interface
`RouteStackInterface`. Esta interface es la interface base tanto para `SimpleRouteStack`
como para `TreeRouteStack` y provee los métodos para trabajar con las rutas contenidas
dentro de la pila de rutas.

## Generar URLs a Partir de Rutas

La principal tarea de cualquier clase route es determinar si la ruta definida coincide
con la petición HTTP, si hay coincidencia regresa el conjunto de parámetros con los
que se puede determina un controlador y una acción. La tarea opuesta de una clase
route permite generar URLs con parámetros. Esta característica se puede usar en un
método de acción en el controlador para generar URLs, por ejemplo, para redireccionar
a un usuario a otra página. Además, esta se puede usar dentro de la plantilla de
vista para generar enlaces.

### Generar URL en la plantilla de Vista

Las páginas web usualmente contienen enlaces a otras páginas. Estos enlaces puede
apuntar tanto a una página interna de nuestro sitio como a páginas de otros sitios.
Un enlace se representa con la etiqueta HTML `<a>` que tiene el atributo `href`
que especifica la URL de la página de destino. Abajo se muestra un ejemplo de un
enlace apuntando a una página externa:

`<a href="http://example.com/path/to/page">A link to another site page</a>`

Cuando generamos un enlace a un recurso interno de nuestro sitio generalmente
usamos una URL relativa (sin el nombre del servidor):

`<a href="/path/to/internal/page">A link to internal page</a>`

Para generar URLs en nuestra plantilla de vista (archivos *.phtml*) podemos usar
la clase ayudante de vista `Url` que toma el nombre de ruta como un argumento de
entrada:

~~~php
<!-- A hyperlink to Home page -->
<a href="<?= $this->url('home'); ?>">Home page</a>

<!-- A hyperlink to About page -->
<a href="<?= $this->url('about'); ?>">About page</a>
~~~

En las líneas de arriba generamos dos URLs relativas. En la línea 2 llamamos al
ayudante de vista `Url` y le pasamos el nombre de ruta "home" como su parámetro.
En la línea 5 pasamos el nombre de la ruta "about" como un argumento al ayudante
de vista `Url`.

I> En el ejemplo de arriba el ayudante de vista `Url` usa internamente el objeto
I> `RouteMatch` y llama a la ruta `Literal` para ensamblar la URL a partir del nombre
I> de la ruta.

Luego de que la clase `PhpRenderer` ejecuta el código de la plantilla de vista la
salida HTML será la siguiente:

~~~php
<!-- A hyperlink to Home page -->
<a href="/">Home page</a>

<!-- A hyperlink to About page -->
<a href="/about">About page</a>
~~~

#### Pasar Parámetros

Si una ruta usa algunos parámetros variables podemos pasarlos al ayudante de vista
`Url` como un segundo argumento:

~~~php
<!-- A hyperlink to About page -->
<a href="<?= $this->url('application', ['action' => 'about']); ?>" >
  About page
</a>

<!-- A hyperlink to Barcode image -->
<a href="<?= $this->url('application', ['action' => 'barcode',
  'type' => 'code39', 'text' => 'HELLO-WORLD']); ?>" >
  Barcode image </a>
~~~

En el ejemplo de arriba usamos el ayudate de vista `Url` para generar dos URLs a
partir de su nombre y parámetros. Pasamos el nombre de ruta "application" como el
primer argumento y un arreglo de parámetros como segundo argumento.

En la línea 2 pasamos el parámetro acción para decirle a la clase route *Segment*
que esta debe sustituir el comodín correspondiente en la cadena de ruta con la
cadena "about".

Después que la clase `PhpRenderer` ejecuta el código de la plantilla de vista la
salida HTML será como la siguiente:

~~~php
<!-- A hyperlink to About page -->
<a href="/application/about" > About page </a>

<!-- A hyperlink to Barcode image -->
<a href="/application/barcode/code39/HELLO-WORLD" > Barcode image </a>
~~~

Como otro ejemplo vamos a intentar generar una URL para nuestra ruta *Regex*
(una que sirve nuestras páginas "estáticas"):

~~~php
<!-- A hyperlink to Introduction page -->
<a href="<?= $this->url('doc', ['page'=>'introduction']); ?>">
 Introduction </a>
~~~

Esto genera el siguiente código HTML:

~~~php
<!-- A hyperlink to Introduction page -->
<a href="/doc/introduction.html"> Introduction </a>
~~~

#### Generar URLs Absolutas

Si necesitamos generar URL absolutas (que tienen un esquema y un nombre de servidor)
podemos especificar el tercer parámetro del ayudante de vista `Url`. El tercer
parámetro debería ser un arreglo que contiene una o varias opciones. Para construir
una URL absoluta pasamos la opción `force_canonical` como se muestra abajo:

~~~php
<!-- A hyperlink to Home page -->
<a href="<?= $this->url('home', [], ['force_canonical' => true]); ?>" >
  Home page </a>

<!-- A hyperlink to About page -->
<a href="<?php echo $this->url('application', ['action' => 'about'],
  ['force_canonical' => true]); ?>" > About page </a>
~~~

En la línea 2 del ejemplo de arriba pasamos el nombre de la ruta "home" como primer
argumento, un arreglo vacío como segundo argumento y un arreglo que contiene la
opción `force_canonical` como el tercer argumento. Además, en las líneas 6-7 pasamos
la opción `force_canonical` como tercer argumento para generar la URL de la página
About.

El resultado HTML del código de arriba se mostrará de la siguiente manera:

~~~php
<!-- A hyperlink to Home page -->
<a href="http://localhost/" > Home page </a>

<!-- A hyperlink to About page -->
<a href="http://localhost/application/index/about" > About page </a>
~~~

#### Especificar la Query Part

Si queremos que nuestra URL tenga una query part podemos especificar la opción
`query` como tercer argumento en el ayudante de vista `Url`. Por ejemplo, suponiendo
que tenemos la acción "search" en algún controlador (y una ruta asociada a esta acción)
y queremos pasar un parámetro de consulta y contar los resultados por página.
La URL para esta acción sería como esta: "http://localhost/search?q=topic&count=10".
Para generar una URL como esta podemos usar el siguiente código:

~~~
<a href="<?= $this->url('search', [], ['force_canonical' => true,
         'query'=>['q'=>'topic', 'count'=>10]]); ?>" >
  Search </a>
~~~

En el código de arriba especificamos la opción `query` que es un arreglo que
contiene el par *name=>value* como parámetros de consulta.

### Generar las URL en el Controlador

Podemos generar URLs dentro de nuestro método de acción en el controlador usado
el controlador complementario `Url`. Para generar una URL llamamos al método `fromRoute()`
del controlador complementario `Url` como en el ejemplo de abajo:

~~~php
// An example action method
public function someAction()
{
    // Generate a URL pointing to the Home page ('/')
    $url1 = $this->url()->fromRoute('home');

    // Generate an absolute URL pointing to the About page
    // ('http://localhost/application/about')
    $url2 = $this->url()->fromRoute('application',
              ['action'=>'about'], ['force_canonical'=>true]);
}
~~~

T> Los argumentos que toma el complemento `Url` son los mismos que los del ayudante de
T> vista `Url`. Así, podemos generar URLs absolutas y relativas de la misma manera
T> que lo hacemos en nuestras plantillas de vista.

### Codificación de la URL

Cuando se generan URLs tanto con el ayudante de vista `Url` o con el controlador
complementaria `Url` deberíamos recordar que las URLs solo pueden contener caracteres
"seguros", caracteres del conjunto de caracteres ASCII. Si pasamos un parámetro
que contiene caracteres inseguros estos caracteres se reemplazarán con una secuencia
constituida por el carácter porcentaje y dos dígitos.

Por ejemplo, vamos a intentar generar una URL para nuestra ruta *Regex* y pasarla
con el parámetro "page" que tendrá el valor "/chapter1/introduction".

~~~php
<!-- A hyperlink to Introduction page -->
<a href="<?= $this->url('doc', ['page'=>'chapter1/introduction']); ?>">
  Introduction </a>
~~~

Podemos asumir que se generará una URL como "/doc/chapter1/introduction.html". Pero
como la barra ('/') es un carácter inseguro será reemplazado por seguridad por
los caracteres "%2F" con lo que se obtiene un código HTML como el siguiente:

~~~text
<!-- A hyperlink to Introduction page -->
<a href="/doc/chapter1%2Fintroduction.html"> Introduction </a>
~~~

Desafortunadamente este enlace no es útil por que no coincidirá con nuestra ruta
*Regex*.

## Escribir nuestra Propia Ruta

Aunque Laminas nos provee de muchos tipos de rutas, en algunas situaciones, necesitaremos
escribir nuestro propio tipo de ruta.

Un ejemplo en el que necesitamos un tipo de ruta a la medida es cuando tenemos que
definir reglas dinámicas de asociación de URLs. Usualmente se almacenan las configuraciones
de rutas en el archivo de configuración de modulo pero en algunos sistemas CMS tendrás
documentos guardados en la base de datos. Un sistema como este necesitará del
desarrollo de un tipo de ruta a la medida que se pueda conectar a la base de datos
y ejecutar la comparación de rutas contra los datos guardados en base de datos.
No podemos guardar esta información en archivos de configuración por que los nuevos
documentos los guarda el administrador del sistema y no el programador.

### RouteInterface

Sabemos que cada clase ruta debe implementar la interface `Laminas\Router\Http\RouteInterface`.
Los métodos de esta interface se presenta en al tabla 5.4:

{title="Tabla 5.4. Métodos RouteInterface"}
|--------------------------------|---------------------------------------------------------------|
| *Nombre del Método*            | *Descripción*                                                 |
|--------------------------------|---------------------------------------------------------------|
| `factory($options)`            | Método estático para la creación de la clase ruta.            |
|--------------------------------|---------------------------------------------------------------|
| `match($request)`              | Método que ejecuta la comparación contra los datos de la petición HTTP. |
|--------------------------------|---------------------------------------------------------------|
| `assemble($params, $options)`  | Método para generar la URL a partir de parámetros de ruta.    |
|--------------------------------|---------------------------------------------------------------|
| `getAssembledParams()`         | Método para recuperar los parámetros que fueron utilizados en la generación de la URL. |
|--------------------------------|---------------------------------------------------------------|

El método estático `factory()` es usado por el router de Laminas (`TreeRouteStack` or `SimpleRouteStack`)
para instanciar la clase ruta. El router pasa el arreglo `options` como un argumento
al método `factory()`.

El método `match()` se usa para ejecutar la comparación de la petición HTTP (particularmente
su URL) contra las opciones pasadas a la clase ruta a través de `factory()`. El
método `match()` debería retornar o una instancia de la clase `RouteMatch` en caso
de éxito en la comparación o `null` en caso de fallo.

Los parámetros y opciones de la ruta junto con el método `assemble()` se usan para
generar la URL. El propósito del método ayudante `getAssembledParams()` es regresar
el arreglo de parámetros que fueron usados en la generación de la URL.

### Clase Rute a la Medida

Para demostrar la creación de un tipo de ruta a la medida vamos a mejorar nuestra
solución anterior, el sistema de documentación simple que usa un tipo de ruta *Regex*.
La desventaja del tipo de ruta *Regex* es que no podemos organizar las páginas estáticas
en jerarquía cuando se crean subcarpetas bajo la carpeta *doc* (cuando generamos
una URL para cada página la barra de separación de directorios será codificada haciendo
al enlace inútil). Crearemos nuestro propia clase `StaticRoute` que permite corregir
este problema.

Además, la clase que crearemos es más poderosa, por que esta no solo reconocerá
las URLs que comienzan con "/doc" y terminal con ".html". Adicionalmente reconocerá
URLs genéricas como "/help" o "/support/chapter1/introduction".

Lo que queremos alcanzar:

* La clase `StaticRoute` debe ser insertable en la pila de rutas (`SimpleRouteStack`
  o `TreeRouteStack`) y usarse junto con otros tipos de ruta.

* La clase ruta debe reconocer URLs genéricas como "/help" o "/introduction".

* La clase ruta debe comparar la URL contra la estructura de directorios. Por ejemplo,
  si la URL es "/chapter1/introduction" entonces la ruta debe revisar si la plantilla
  de vista correspondiente *&lt;base_dir&gt;/chapter1/introduction.phtml* existe y es
  legible y si es así reportar una coincidencia. Si el archivo no existe (o no es
  legible) regresa un estado fallido.

* La clase ruta debe revisar la URL usando una expresión regular que determina si
  es un nombre de archivo aceptable. Por ejemplo, el nombre de archivo "introduction"
  es aceptable pero el nombre "*int$roduction" no lo es. Si el nombre del archivo
  no es aceptable se debe retornar un estado de error.

* La clase ruta debe ser capaz de ensamblar la URL con el nombre de ruta y sus parámetros.

Para comenzar creamos la subcarpeta *Route* bajo el directorio fuente del módulo
y colocamos el archivo *StaticRoute.php* dentro de él (figura 5.9).

![Figure 5.9. Archivo StaticRoute.php](../en/images/routing/static_route_php.png)

Dentro del archivo pegamos este pedazo de código:

~~~php
<?php
namespace Application\Route;

use Traversable;
use \Laminas\Router\Exception;
use \Laminas\Stdlib\ArrayUtils;
use \Laminas\Stdlib\RequestInterface as Request;
use \Laminas\Router\Http\RouteInterface;
use \Laminas\Router\Http\RouteMatch;

// Custom route that serves "static" web pages.
class StaticRoute implements RouteInterface
{
    // Create a new route with given options.
    public static function factory($options = [])
    {
    }

    // Match a given request.
    public function match(Request $request, $pathOffset = null)
    {
    }

    // Assembles a URL by route params.
    public function assemble(array $params = [], array $options = [])
    {
    }

    // Get a list of parameters used while assembling.
    public function getAssembledParams()
    {
    }
}
~~~

Del código de arriba debemos notar que se coloco a la clase `StaticRoute` dentro
del namespace `Application\Route` (línea 2).

En las líneas 4-9 definimos algunos alias de nombre de clase para hacer al nombre
de la clase mas corto.

En las líneas 12-33 definimos el esqueleto para la clase `StaticRoute`. La clase
`StaticRoute` implementa a la interface `RouteInterface` y define todos los métodos
especificados en la interface: `factory()`, `match()`, `assemble()` y `getAssembledParams()`.

Luego vamos a agregar varias propiedades protegidas y el método constructor de la
clase `StaticRoute` como se muestra abajo:

~~~php
<?php
//...

class StaticRoute implements RouteInterface
{
    // Base view directory.
    protected $dirName;

    // Path prefix for the view templates.
    protected $templatePrefix;

    // File name pattern.
    protected $fileNamePattern = '/[a-zA-Z0-9_\-]+/';

    // Defaults.
    protected $defaults;

    // List of assembled parameters.
    protected $assembledParams = [];

    // Constructor.
    public function __construct($dirName, $templatePrefix,
            $fileNamePattern, array $defaults = [])
    {
        $this->dirName = $dirName;
        $this->templatePrefix = $templatePrefix;
        $this->fileNamePattern = $fileNamePattern;
        $this->defaults = $defaults;
    }

    // ...
}
~~~

Arriba en la línea 7 definimos la propiedad `$dirName` que guardará el nombre del
directorio base donde las plantillas "estáticas" de vista se almacenan. En la línea
10 definimos la variable de clase `$templatePrefix` que guarda el prefijo prefijado
de todos las plantillas de vista. La línea 13 contiene la variable `$fileNamePattern`
que se usará para revisar el nombre de archivo.

En las líneas 22-29 definimos el método constructor que se llama cuando se crear
una nueva instancia de la clase con lo que se inicializan las propiedades protegidas.

Luego, vamos a implementar el método `factory()` de nuestra clase a la medida
`StaticRoute`. El router llamará al método `factory()` para instanciar la clase route:

~~~php
<?php
//...

class StaticRoute implements RouteInterface
{
    //...

    // Create a new route with given options.
    public static function factory($options = [])
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        } elseif (!is_array($options)) {
            throw new Exception\InvalidArgumentException(__METHOD__ .
                ' expects an array or Traversable set of options');
        }

        if (!isset($options['dir_name'])) {
            throw new Exception\InvalidArgumentException(
                'Missing "dir_name" in options array');
        }

        if (!isset($options['template_prefix'])) {
            throw new Exception\InvalidArgumentException(
                'Missing "template_prefix" in options array');
        }

        if (!isset($options['filename_pattern'])) {
            throw new Exception\InvalidArgumentException(
                'Missing "filename_pattern" in options array');
        }

        if (!isset($options['defaults'])) {
            $options['defaults'] = [];
        }

        return new static(
            $options['dir_name'],
            $options['template_prefix'],
            $options['filename_pattern'],
            $options['defaults']);
    }
}
~~~

En el código de arriba vemos que el método `factory()` toma el arreglo `options`
como argumento (línea 9). El arreglo `options` puede contener las opciones de
configuración de la clase route. La clase `StaticRoute` aceptará las siguientes
opciones:

* `dir_name` - directorio base donde se almacenan todas plantillas de vista "estáticas".
* `template_prefix` - el prefijo que se prefija al nombre de todas las plantillas.
* `filename_pattern` - la expresión regular que revisa el nombre de los archivos.
* `defaults` - los parámetros regresados por el router por defecto.

Una vez que se analizan las opciones llamamos al método constructor de la clase
en las líneas 37-41 para inicializar y regresar el objeto `StaticRoute`.

El siguiente método que agregamos a la clase de ruta `StaticRoute` es el método
`match()`:

~~~php
<?php
//...

class StaticRoute implements RouteInterface
{
    //...

    // Match a given request.
    public function match(Request $request, $pathOffset=null)
    {
        // Ensure this route type is used in an HTTP request
        if (!method_exists($request, 'getUri')) {
            return null;
        }

        // Get the URL and its path part.
        $uri  = $request->getUri();
        $path = $uri->getPath();

        if($pathOffset!=null)
            $path = substr($path, $pathOffset);

        // Get the array of path segments.
        $segments = explode('/', $path);

        // Check each segment against allowed file name template.
        foreach ($segments as $segment) {
            if(strlen($segment)==0)
                continue;
            if(!preg_match($this->fileNamePattern, $segment))
            return null;
        }

        // Check if such a .phtml file exists on disk
        $fileName = $this->dirName . '/'.
                $this->templatePrefix.$path.'.phtml';
        if(!is_file($fileName) || !is_readable($fileName)) {
            return null;
        }

        $matchedLength = strlen($path);

        // Prepare the RouteMatch object.
        return new RouteMatch(array_merge(
              $this->defaults,
              ['page'=>$this->templatePrefix.$path]
             ),
             $matchedLength);
    }
}
~~~

En el código de arriba vemos que el método `match()` toma dos argumentos: el objeto
de la petición HTTP (una instancia de la clase `Laminas\Stdlib\Request`) y la ruta offset
URL. El objeto de la petición se usa para conseguir la URL de la petición (línea 17).
El parámetro de ruta offset es un entero positivo que apunta a la porción de la URL
contra la que se compara la ruta (línea 21).

En la línea 24 extraemos los segmentos de la URL. Luego revisamos si cada segmento
es un nombre de archivo o directorio valido (líneas 27-32). Si el segmento no
es un nombre de archivo valido regresamos `null` como un estado fallido.

En la línea 35 calculamos la ruta de la plantilla de vista y en las líneas 37-39
revisamos si el archivo realmente existe y se puede leer. De esta manera comparamos
la URL contra la estructura del directorio.

En las líneas 44-48 preparamos y regresamos el objeto `RouteMatch` con los parámetros
por defecto más el parámetro "página" que contiene el nombre de la plantilla de vista
que se va a mostrar.

Para completar la implementación de nuestra clase `StaticRoute` agregamos los métodos
`assemble()` y `getAssembledParams()` que se usarán para generar las URLs a partir
de los parámetros de una ruta. El código de estos métodos se muestra abajo:

~~~php
<?php
//...

class StaticRoute implements RouteInterface
{
    //...

    // Assembles a URL by route params
    public function assemble(array $params = [],
                           array $options = [])
    {
        $mergedParams = array_merge($this->defaults, $params);
        $this->assembledParams = [];

        if(!isset($params['page'])) {
            throw new Exception\InvalidArgumentException(__METHOD__ .
               ' expects the "page" parameter');
        }

        $segments = explode('/', $params['page']);
        $url = '';
        foreach($segments as $segment) {
            if(strlen($segment)==0)
                continue;
            $url .= '/' . rawurlencode($segment);
        }

        $this->assembledParams[] = 'page';

        return $url;
    }

    // Get a list of parameters used while assembling.
    public function getAssembledParams()
    {
        return $this->assembledParams;
    }
}
~~~

En el código de arriba definimos el método `assemble()` que toma dos argumentos:
El arreglo `parameters` y el arreglo `options` (línea 9). El método construye la
URL codificando los segmentos con la codificación URL para luego concatenarlos.

El método `getAssembledParams()` regresa exactamente los nombres de los parámetros
usados para la generación de la URL (línea 36).

Hemos terminado con la clase de ruta `StaticRoute`. Para usar nuestro tipo de ruta,
hecha a la medida, agregamos la siguiente configuración al archivo de configuración
*module.config.php*:

Now we've finished the `StaticRoute` route class. To use our custom route type,
we add the following configuration to the *module.config.php* configuration file:

~~~php
'static' => [
    'type' => StaticRoute::class,
    'options' => [
        'dir_name'         => __DIR__ . '/../view',
        'template_prefix'  => 'application/index/static',
        'filename_pattern' => '/[a-z0-9_\-]+/',
        'defaults' => [
            'controller' => Controller\IndexController::class,
            'action'     => 'static',
        ],
    ],
],
~~~

En la línea 1 de la configuración de arriba definimos una regla de routing llamada
"static". El parámetro `type` define el nombre completo de la clase `StaticRoute`
(línea 2). En el arreglo `options` definimos el directorio base donde las páginas
"estáticas" estarán almacenadas (línea 4), el prefijo de las plantillas (linea 5),
el patrón para el nombre del archivo (línea 6) y el arreglo `defaults` que contiene
el nombre del controlador y la acción que servirá todas las páginas estáticas.

I> No olvidemos insertar la siguiente línea al comienzo de la clase en el archivo
I> `module.config.php`:
I>
I> `use Application\Route\StaticRoute;`

El último paso es crear el método de acción en la clase `IndexController`:

~~~php
public function staticAction()
{
    // Get path to view template from route params
    $pageTemplate = $this->params()->fromRoute('page', null);
    if($pageTemplate==null) {
        $this->getResponse()->setStatusCode(404);
        return;
    }

    // Render the page
    $viewModel = new ViewModel([
            'page'=>$pageTemplate
        ]);
    $viewModel->setTemplate($pageTemplate);
    return $viewModel;
}
~~~

La acción de arriba es casi idéntica a la acción usada para la ruta *Regex*. En
la línea 4 recuperamos el parámetro `page` de la ruta y lo guardamos en la variable
`$pageTemplate`. En la línea 11 creamos la variable contenedor `ViewModel` y en la
línea 14 colocamos explícitamente el nombre de la plantilla de vista que se va a mostrar.

Para ver el sistema en acción vamos a agregar un par de páginas de vista "estáticas":
la página de ayuda (`help.phtml`) y la página de introducción (`intro.phtml`).
Creamos la subcarpeta *static* dentro del directorio *view/application/index* del
módulo `Application` y colocamos plantilla de vista *help.phtml* allí:

~~~php
<h1>Help</h1>

<p>
    See the help <a href="<?= $this->url('static',
	   ['page'=>'/chapter1/intro']); ?>">introduction</a> here.
</p>
~~~

Luego creamos la subcarpeta *chapter1* dentro del directorio *static* y colocamos
el archivo *chapter1/intro.phtml* allí:

~~~php
<h1>Introduction</h1>

<p>
    Write the help introduction here.
</p>
~~~

Finalmente deberíamos tener la siguiente estructura de directorios (figura 5.10):

![Figure 5.10. Static pages](../en/images/routing/static_page_dir.png)

Finalmente, escribimos la siguiente URL en el navegador web: *http://localhost/help*.
La página de ayuda debería aparecer (ver figura 5.11). Si escribimos la URL
*http://localhost/chapter1/intro* en nuestro navegador deberíamos ver la página
Introduction (figura 5.12).

![Figure 5.11. Help page](../en/images/routing/help_page.png)

![Figure 5.12. Introduction page](../en/images/routing/chapter1_intro.png)

Podemos crear páginas estáticas agregando archivos phtml al directorio *static* y
ellos automáticamente estarán disponibles para los usuarios del sitio.

T> Si nos encontramos atascados podemos encontrar este ejemplo completo y trabajando
T> dentro de la aplicación *Hello World*.

## Resumen

En este capítulo hemos aprendido sobre el routing. El routing se usa para asociar
peticiones HTTP a un método de acción en un controlador. Existen varios tipos de ruta:
*Literal*, *Segment*, *Regex*, *Hostname*, *Scheme*, *Method*, etc.). Cada tipo
de ruta usa diferentes partes de la URL (y en ocasiones otros datos de la petición
HTTP) para comparar la URL con una plantilla de ruta especifica. Además, aprendimos
como escribir clases de rutas a la medida si las capacidades de los tipos de ruta
estándar no son suficientes.

La principal tarea de una clase de ruta es regresar una ruta coincidente que contiene
un conjunto de parámetros con los que un controlador y una acción se pueden determinar.
La tarea opuesta de una clase de ruta permite generar una URL con parámetros. Esta
característica es ampliamente usada en la capa de vista de la aplicación para
generar enlaces.

Los tipos de ruta se pueden combinar en un árbol anidado con la ayuda del router
`TreeRouteStack` u organizar en una cadena con el router `SimpleRouteStack`. Estos
dos routers permiten definir arbitrariamente reglas complejas.

La configuración del routing se almacena en el archivo de configuración del módulo
bajo la llave `router`. Cada módulo tiene sus propias reglas de routing que se mezclan
con la configuración de otros módulos luego del inicio de la aplicación.
