# Subir archivos con formularios {#uploads}

En este capítulo, aprenderemos como subir archivos desde formularios. Primero,
revisaremos la teoría básica sobre las capacidades de carga de archivos de HTTP
y la codificación de transferencia de contenido binario. Luego construiremos un
ejemplo completamente funcional de una Galería de Imágenes mostrando como subir
imágenes a un servidor web.

Los componentes Laminas cubiertos en este capítulo son:

|--------------------------------|---------------------------------------------------------------|
| *Componente*                   | *Descripción*                                                 |
|--------------------------------|---------------------------------------------------------------|
| @`Laminas\Form`                   | Contiene las clases base para modelos de formulario.          |
|--------------------------------|---------------------------------------------------------------|
| @`Laminas\Filter`                 | Contiene varias clases filtro.                                |
|--------------------------------|---------------------------------------------------------------|
| @`Laminas\Validator`               | Implementa varias clases validadoras.                        |
|--------------------------------|---------------------------------------------------------------|
| @`Laminas\InputFilter`            | Implementa un contenedor para filtros y validadores.          |
|--------------------------------|---------------------------------------------------------------|

## Sobre la subida de archivos por HTTP

Los formularios HTML tienen la capacidad de subir archivos de un tamaño arbitrario [^rfc-1867].
Los archivos son generalmente transmitidos a través del método POST HTTP [^get].

[^rfc-1867]: La carga de archivos se describen en [RFC-1867](http://www.ietf.org/rfc/rfc1867.txt).
             Este mecanismo permite cargar grandes archivos usando la codificación
             de transferencia de contenido binario. El tipo de codificación
             «multipart/form-data» se utiliza para este propósito.

[^get]: El método HTTP GET es ineficiente para cargar archivos, porque la longitud
        de la URL tiene un limite superior. Además, la codificación de la URL
        del archivo de datos incrementa enormemente la longitud de la URL.

Por defecto, HTTP usa la *codificación URL* para transferir los datos del formulario,
podemos ver como esta codificación funciona en los capítulos anteriores.
Sin embargo, esta codificación es ineficiente para cargar grandes archivos porque
la codificación URL de datos binarios incrementa dramáticamente la longitud de las
peticiones HTTP. Si el propósito es cargar archivos se recomienda en su lugar
usar la «codificación de transferencia binaria» que se describe en la sección
siguiente.

### Codificación de transferencia binaria de HTTP

Un formulario HTML simple capaz de cargar archivos se muestra en el código de ejemplo
más abajo. El tipo de codificación binaria se habilita colocando el atributo
`enctype` del formulario con el valor «multipart/form-data»:

~~~html
<form action="upload" method="POST" enctype="multipart/form-data">
    <input type="file" name="myfile">
    <br/>
    <input type="submit" name="Submit">
</form>
~~~

En la línea 1, colocamos explícitamente la codificación (atributo `enctype`)
«multipart/form-data» para utilizar la codificación de transferencia de contenido
binario en el formulario.

En la línea 2, definimos un campo de entrada de tipo «file» y con el nombre «myfile».
Este campo de entrada permitirá al visitante del sitio seleccionar el archivo que
se cargará.

Si guardamos el código HTML mencionado arriba en un archivo *.html* y lo abrimos
en nuestro navegador web, veremos una página como la que se muestra en la figura 10.1.

![Figura 10.1. Un formulario HTML simple capaz de subir un archivo](../en/images/uploads/html_upload_form.png)

El elemento *file* tiene el botón *Browse...* que permite seleccionar el archivo
que se subirá. Cuando el usuario del sitio selecciona el archivo y hace clic
en el botón *Submit* del formulario, el navegador web enviará una petición HTTP
al servidor web y la petición contendrá los datos del archivo que será subido.
El ejemplo de abajo ilustra como se ve una petición HTTP:

~~~text
POST http://localhost/upload HTTP/1.1
Host: localhost
Content-Length: 488
User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64)
Content-Type: multipart/form-data; boundary=----j1bOrwgLvOC3dy7o
Accept-Encoding: gzip,deflate,sdch

------j1bOrwgLvOC3dy7o
Content-Disposition: form-data; name="myfile"; filename="Somefile.txt"
Content-Type: text/html

(file binary data goes here)
------j1bOrwgLvOC3dy7o
Content-Disposition: form-data; name="Submit"

Submit Request
------j1bOrwgLvOC3dy7o--
~~~

Como podemos ver en el ejemplo de arriba, la petición HTTP con el tipo de codificación
«multipart/form-data» tiene un aspecto análogo a una petición HTTP usual (tiene
la línea de estado, las cabeceras y el area de contenido), sin embargo tiene las
siguientes e importantes diferencias:

* La línea 5 coloca el encabezado «Content-Type» con el valor «multipart/form-data».
  Los campos que constituyen el formulario están delimitados por una marca límite, *boundary*.
  La marca «límite» o «boundary» es una secuencia única y aleatoria de caracteres
  que delimita los campos del formulario.

* Las líneas 8-17 representan el contenido de la petición HTTP. Los campos del formulario
  se delimitan con una secuencia «límite» (boundary), líneas 8, 13 y 17. Los datos
  del archivo que son subidos se transmiten en formato binario (línea 12) que
  permite reducir el tamaño del contenido a su mínimo.

W> Por defecto, las configuraciones del motor de PHP no permiten cargar grandes archivos
W> (más grandes que 2MB). Para subir grandes archivos, necesitamos editar
W> el archivo de configuración *php.ini* y modificar los parámetros `post_max_size`
W> y `upload_max_filesize` (podemos revisar el [Apéndice A. Configuración del Entorno
W> de Desarrollo Web](#devenv) para información de como hacer esto). Colocando
W> estos parámetros en `100M` se permite subir archivos de un tamaño de 100 Mb,
W> este valor es generalmente suficiente. Si nuestro plan es subir archivos muy
W> grandes, hasta 1GB, es mejor colocar este valor en 1024M. No olvidemos reiniciar
W> nuestro Servidor Web Apache después de editar el archivo de configuración.

### El arreglo super-global $_FILES en PHP

Cuando el visitante del sitio carga algunos archivos al Servidor Web Apache, los
archivo se colocan en una ubicación temporal (generalmente en la carpeta temporal
del sistema, en GNU/Linux */tmp* y en Windows *C:\\Windows\\Temp*). El código
PHP recibe la información sobre el archivo del arreglo especial super-global `$_FILES`.

I> El arreglo `$_FILES` es análogo a las variables super-globales `$_GET` y `$_POST`.
I> Estas últimas se usan para guardar las variables GET y POST, respectivamente,
I> mientras la primera se usa para guardar información sobre archivos subidos.

Por ejemplo, para el formulario simple de subida que mencionamos antes, el arreglo
super-global `$_FILES` tiene la siguiente forma (la salida se genero con la
función de PHP `var_dump()`).

~~~text
array (size=1)
    'myfile' =>
        array (size=5)
            'name' => string 'somefile.txt' (length=12)
            'type' => string 'text/plain' (length=10)
            'tmp_name' => string '/tmp/phpDC66.tmp' (length=16)
            'error' => int 0
            'size' => int 18
~~~

Como podemos ver del ejemplo de arriba, el arreglo `$_FILES` contiene una entrada
por cada archivo subido. Y por cada archivo subido contiene la siguiente información

As you can see from the example above, the `$_FILES` array contains an entry per
each uploaded file. For each uploaded file, it contains the following information:

  * `name` -- nombre original del archivo (línea 4).
  * `type` -- tipo MIME [^mime] del archivo (línea 5).
  * `tmp_name` -- nombre temporal del archivo subido (línea 6).
  * `error` -- código de error que indica el estado de la subida (línea 7);
     el código de error cero significa que el archivo fue subido correctamente.
  * `size` -- tamaño del archivo en bytes (línea 8).

[^mime]: el tipo MIME también conocido como «tipo contenido» (content type) es un
         identificador estándar en internet para indicar el tipo de dato que
         contiene el archivo. Por ejemplo el tipo MIME «text/plain» se asigna a
         un archivo de texto, mientras que el tipo MIME «application/octet-stream»
         se asigna a un archivo binario.

El motor de PHP almacena los archivos subidos en una ubicación temporal que se limpia
luego de que el script de PHP termina de ejecutarse. Así que, si queremos guardar
el archivos subido en alguna carpeta para usarlo posteriormente, necesitamos usar
la función de PHP `move_uploaded_file()`. La función `move_uploaded_file()` toma
dos argumentos: el primero de ellos es el nombre del archivo temporal y el segundo
es el nombre del archivo de destino.

T> Puede confundirnos el hecho de no poder usar la función de PHP `rename()` para
T> mover el archivo subido temporalmente a su ruta de destino. Por razones de seguridad
T> PHP tiene una función especial para mover los archivos subidos. La función
T> `move_uploaded_file()` es análoga a la función `rename()`, pero aquella hace
T> algunas revisiones adicionales para asegurar que el archivo se transfirió
T> a través de una petición HTTP POST y que el proceso de carga terminó sin
T> errores.

El siguiente código de ejemplo muestra como mover el archivo subido con el formulario
simple que hemos mostrado arriba:

~~~php
$destPath = '/path/to/your/upload/dir';
$result = move_uploaded_file($_FILES['myfile']['tmp_name'], $destPath);
if(!$result) {
    // Some error occurred.
}
~~~

Arriba en la línea 1, creamos la variable `$destPath` con el nombre de la carpeta
en donde se guardará el archivo subido.

En la línea 2, llamamos a la función `move_uploaded_file()` y le pasamos dos
argumentos: la ruta del archivo temporal y la ruta de destino.

T> Especificar solo el nombre de la carpeta como segundo argumento de la función
T> `move_uploaded_file()` es útil cuando no queremos renombrar el archivo. Si
T> necesitamos guardar el archivo subido con otro nombre diferente al original
T> debemos especificar la ruta de archivo completa en lugar del nombre de la
T> carpeta.

En la línea 3, revisamos el valor regresado por la función. Si la operación es
exitosa, la función regresará `true`. Si ocurre algún error (por ejemplo, si
los permisos de la carpeta no son suficientes para guardar el archivo) se regresará
el booleano `false`.

## Acceder a los archivos subidos con Laminas

En nuestra clase controlador generalmente no nos comunicamos con el arreglo
`$_FILES` directamente, en su lugar usamos la clase @`Request`[Laminas\Http\Request]
o el complemento para controladores @`Params`, como se muestra en el código más abajo:

~~~php
<?php
//...
class IndexController extends AbstractActionController
{
    // An example controller action intended for handling file uploads.
    public function uploadAction()
    {
        // Get the whole $_FILES array.
        $files = $this->getRequest()->getFiles();

        // The same, but with Params controller plugin.
        $files = $this->params()->fromFiles();

        // Get a single entry of the $_FILES array.
        $files = $this->params()->fromFiles('myfile');
  }
}
~~~

En la línea 9 del código de arriba usamos el método `getRequest()` de la clase
controlador para acceder al objeto @`Request`[Laminas\Http\Request] y el método `getFiles()` del
objeto *request* para recuperar la información de todos los archivos subidos.

En la línea 12, hacemos lo mismo pero con el complemento para controladores @`Params`.
Usamos su método `fromFiles()` para traer la información sobre todos los archivos
subidos.

Si lo necesitamos, podemos extraer la información de un solo archivo. En la línea
15, usamos el mismo método `fromFiles()` y le pasamos el nombre del campo *file*
que queremos recuperar. Este recupera una sola entrada de tipo *file* del arreglo
super-global `$_FILES`.

## Subida de archivos y el Modelo de Formulario de Laminas

Para agregar la capacidad de subir archivos a nuestro modelo de formulario,
necesitamos agregar un elemento de la clase @`Laminas\Form\Element\File` de la
siguiente manera:

~~~php
    // Add the following code inside of form's addElements() method.

    // Add the "file" field.
    $this->add([
        'type'  => 'file',
        'name' => 'file',
        'attributes' => [
            'id' => 'file'
        ],
        'options' => [
            'label' => 'Upload file',
        ],
    ]);
~~~

En el código de arriba, llamamos al método `add()` que provee la clase base
@`Form`[Laminas\Form\Form] y le pasamos un arreglo de configuración que describe el elemento.
La llave `type` del arreglo (línea 5) debe ser el nombre de la clase
@`Laminas\Form\Element\File` o su alias «file».

## Validar la Subida de Archivos

Se necesita revisar la corrección de los archivos subidos como se hace con cualquier
otro dato del formulario. Por ejemplo, podríamos necesitar revisar:

  * Si los archivos fueron realmente subidos a través de una petición POST HTTP y
    que no fueron solo copiados desde alguna carpeta.

  * Si el o los archivos fueron subidos exitosamente (con un código de error cero).

  * Si los nombres y/o las extensiones de los archivos son aceptables (por ejemplo,
    queremos guardar archivos JPEG solamente y rechazar todos los otros).

  * Si el tamaño del archivo está en el rango permitido (por ejemplo, queremos
    asegurarnos que el archivo no es muy grande).

  * Si el número total de archivos subidos no excede algún límite.

Para hacer este tipo de revisiones Laminas provee un número de validadores de archivo
útiles (listado en la tabla 10.1). Estas clases validadoras pertenecen al componente
`Laminas\Validator` y están en el espacio de nombres @`Laminas\Validator\File`[Laminas\Validator].

{title="Tabla 10.1. Validadores estándares de archivos"}
|---------------------|------------------|---------------------------------------------------------------|
| *Nombre de la Clase*| *Alias*          | *Descripción*                                                 |
|---------------------|------------------|---------------------------------------------------------------|
| @`Count`            | `FileCount`      | Revisa si el total luego de contar los archivos esta en un    |
|                     |                  | rango dado (min, max).                                        |
|---------------------|------------------|---------------------------------------------------------------|
| @`WordCount`        | `FileWordCount`  | Cuenta el número de palabras que están en un archivo y revisa |
|                     |                  | si el total está en un rango dado.                            |
|---------------------|------------------|---------------------------------------------------------------|
| @`Upload`           | `FileUpload`     | Ejecuta una revisión de seguridad para asegurar que todos los |
|                     |                  | archivos dados fueron realmente subidos a través de POST HTTP |
|                     |                  | y si no hay errores de subida.                                |
|---------------------|------------------|---------------------------------------------------------------|
| @`UploadFile`       | `FileUploadFile` | Ejecuta una revisión de seguridad para asegurar que un archivo|
|                     |                  | fue realmente subido a través de POST HTTP y que no existieron|
|                     |                  | errores de subida.                                            |
|----------------------------------------|---------------------------------------------------------------|
| @`Size`             | `FileSize`       | Revisa si el tamaño del archivo está en un rango dado.        |
|---------------------|------------------|---------------------------------------------------------------|
| @`FilesSize`        | `FileFilesSize`  | Revisa si el tamaño total de todos los archivos dado esta en  |
|                     |                  | un determinado rango.                                         |
|---------------------|------------------|---------------------------------------------------------------|
| @`Extension`        | `FileExtension`  | Revisa que la extensión de un archivo pertenezca a un conjunto|
|                     |                  | de extensiones permitidas.                                    |
|---------------------|------------------|---------------------------------------------------------------|
| @`ExcludeExtension` | `FileExcludeExtension` | Revisa que la extensión de un archivo NO pertenezca a un|
|                     |                        | conjunto de extensiones.                                |
|---------------------|------------------|---------------------------------------------------------------|
| @`MimeType`         | `FileMimeType`   | Revisa que el tipo MIME de un archivo pertenezca a la lista   |
|                     |                  | de tipos MIME permitidos.                                     |
|---------------------|------------------|---------------------------------------------------------------|
| @`ExcludeMimeType`  | `FileExcludeMimeType` | Revisa que el tipo MIME de un archivo no pertenezca a la |
|                     |                       | lista de tipos MIME.                                     |
|---------------------|------------------|---------------------------------------------------------------|
| @`IsImage`          | `FileIsImage`    | Revisa que el archivo es una imagen gráfica (JPEG, PNG, GIF, etc.) |
|---------------------|------------------|---------------------------------------------------------------|
| @`ImageSize`        | `FileImageSize`  | Revisa que las dimensiones del archivo de imagen está en un   |
|                     |                  | rango dado.                                                   |
|---------------------|------------------|---------------------------------------------------------------|
| @`Exists`           | `FileExists`     | Revisa si el archivo existe en el disco.                      |
|---------------------|------------------|---------------------------------------------------------------|
| @`NotExists`        | `FileNotExists`  | Revisa si el archivo no existe en el disco.                   |
|---------------------|------------------|---------------------------------------------------------------|
| @`IsCompressed`     | `FileIsCompressed` | Revisa si el archivo está empacado (ZIP, TAR, etc.)         |
|---------------------|------------------|---------------------------------------------------------------|
| @`Hash`[Laminas\Validator\File\Hash]| `FileHash`       | Revisa si el contenido del archivo coincide con uno o varios  |
|                                  |                  | *hash*.                                                       |
|---------------------|------------------|---------------------------------------------------------------|
| @`Crc32`            | `FileCrc32`      | Revisa que el contenido del archivo tiene la suma de          |
|                     |                  | CRC32 comprobación.                                           |
|---------------------|------------------|---------------------------------------------------------------|
| @`Sha1`             | `FileSha1`       | Revisa que el contenido del archivo tiene el *hash* SHA-1.    |
|---------------------|------------------|---------------------------------------------------------------|
| @`Md5`              | `FileMd5`        | Revisa que el contenido del archivo tiene el *hash* MD5 dado. |
|---------------------|------------------|---------------------------------------------------------------|

Como podemos ver de la tabla de arriba, los validadores de archivos se pueden
dividir aproximadamente en los siguientes grupos:

 * Los validadores que revisan si el o los archivos fueron realmente subidos
   a través de una petición POST HTTP y con un estado de subida *OK*.
 * Los validadores que revisan la cantidad de archivos cargados y su tamaño.
 * Los validadores que revisan la extensión del archivo y su tipo MIME.
 * Los validadores que revisan si el archivo es una imagen y cual es su dimensión.
 * Y los validadores que revisan el *hash* del archivo (o la suma) [^hash].

[^hash]: Un *hash* de archivo se usa para revisar la integridad de los datos
         del archivo (por ejemplo, para asegurar que los datos del archivo no
         están corruptos). Existen varios algoritmos *hash* disponibles:
         MD5, SHA-1, CRC32, etc.

T> Debemos notar que como los validadores de archivo están en el espacio de
T> nombres @`Laminas\Validator\File`[Laminas\Validator], su alias (que usamos cuando creamos un validador
T> con una fábrica) comienza con el prefijo `File`. Por ejemplo, el validador
T> @`IsImage` tiene el alias `FileIsImage`.

Mostraremos como usar algunos de estos filtros de validación de archivo con
una *Galería de Imágenes* de ejemplo luego en este capítulo.

## Filtros de archivos subidos

Laminas Framework provee varios filtros que «transforman» los campos del archivo.
Estas clases de filtro (listadas en la tabla 10.2) pertenecen al componente
@`Laminas\Filter` y están en el espacio de nombres @`Laminas\Filter\File`[Laminas\Filter].

{title="Tabla 10.2. Filtros de Archivo Estándar"}
|-----------------|---------------|---------------------------------------------------------------|
| *Nombre de la Clase*| *Alias*   | *Descripción*                                                 |
|-----------------|---------------|---------------------------------------------------------------|
| @`Rename`       | `FileRename`  | Cambia el nombre o mueve un archivo arbitrario.               |
|-----------------|---------------|---------------------------------------------------------------|
| @`RenameUpload` | `FileRenameUpload` | Cambia el nombre o mueve el archivo cargado con          |
|                 |                    | revisiones de seguridad.                                 |
|-----------------|---------------|---------------------------------------------------------------|
| @`Encrypt`[Laminas\Filter\File\Encrypt]| `FileEncrypt` | Cifra un archivo dado y guarda el contenido del archivo cifrado.|
|-----------------|---------------|---------------------------------------------------------------|
| @`Decrypt`[Laminas\Filter\File\Decrypt]| `FileDecrypt` | Descifra un archivo dado y guarda el contenido del archivo descifrado.|
|-----------------|---------------|---------------------------------------------------------------|
| @`LowerCase`    | `FileLowerCase`| Convierte el contenido del archivo a letras minúsculas.      |
|-----------------|---------------|---------------------------------------------------------------|
| @`UpperCase`    | `FileUpperCase`| Convierte el contenido del archivo a letras mayúsculas.      |
|-----------------|---------------|---------------------------------------------------------------|

De la tabla se puede ver que los filtros se pueden dividir en los siguientes grupos:

  * Filtros que mueven los archivos subidos de una ubicación temporal a su
    carpeta definitiva.
  * Filtros que cifran y descifran archivos.
  * Filtros para convertir el texto de los archivos a mayúsculas y minúsculas.

T> Nótese que como los filtros de archivo están en el espacio de nombres
T> @`Laminas\Filter\File`[Laminas\Filter], su alias (el que usamos cuando creamos un filtro con una
T> fábrica) comienza con el prefijo `File`. Por ejemplo, el filtro @`RenameUpload`
T> tiene el alias `FileRenameUpload`.

Los filtros @`Encrypt`[Laminas\Filter\File\Encrypt] y @`Decrypt`[Laminas\Filter\File\Decrypt]
permiten aplicar varios algoritmos de cifrado
y descifrado a los archivos cargados (el algoritmo concreto se añade especificando
un adaptador determinado). Los filtros @`LowerCase` y @`UpperCase` son útiles para
convertir respectivamente archivos de texto a minúsculas y mayúsculas [^four].

[^four]: En opinión del autor, los cuatro filtros mencionados arriba no son muy
         útiles cuando se trabajo con archivos subidos, porque raramente necesitamos
         cifrar un archivo subido o convertirlo a letras minúsculas.

El filtro @`Rename` permite renombrar o/y mover un archivo arbitrario (no solo un
archivo subido). Este filtro usa internamente la función de PHP `rename()` y
por razones de seguridad no es recomendable en general usar este filtro con
archivos que se han subido.

El filtro @`RenameUpload` parece ser mucho más útil que otros filtros, porque
él permite encapsular la llamada a la función `move_uploaded_file()` y mover
o renombrar el archivo que se ha subido desde la ubicación temporal a la carpeta
definitiva. Más tarde en este capítulo, mostraremos como usar el filtro @`RenameUpload`
en una *Galería de Imágenes* de ejemplo.

## El contenedor InputFilter y los archivos subidos

Como podemos recordar, los filtros y validadores que están asociados a un modelo
de formulario se guardan generalmente en el contenedor
@`InputFilter`[Laminas\InputFilter\InputFilter] que consiste
de *entradas* (una entrada se representa generalmente con la clase @`Input`[Laminas\InputFilter\Input] que
pertenece al espacio de nombres @`Laminas\InputFilter`). Con los campos comunes de
un formulario, los filtros se ejecutan *antes* que los validadores y los
validadores se ejecutan *después* de los filtros.

Sin embargo, con los archivos subidos existen algunas importantes diferencias:

  1. Para guardar las reglas de validación se debe usar la clase especial
     `FileInput` en lugar de la clase `Input`.
  2. Los validadores se aplican *antes* que los filtros.

### FileInput

Para almacenar las reglas de validación para los archivos que se suben debemos
usar la clase `FileInput` en lugar de la acostumbrada clase @`Input`[Laminas\InputFilter\Input].

En nuestro método privado `addInputFilter()` del modelo de formulario, agregamos
las reglas de validación para la entrada del archivo de la siguiente manera:

~~~php
    $inputFilter->add([
        'type'     => 'Laminas\InputFilter\FileInput',
        'name'     => 'file',  // Element's name.
        'required' => true,    // Whether the field is required.
        'filters'  => [        // Filters.
            // Put filter info here.
        ],
        'validators' => [      // Validators.
            // Put validator info here.
        ]
    ]);
~~~

Arriba, colocamos la llave «type» (línea 2) con el nombre de la clase @`Laminas\InputFilter\FileInput`
como valor. El resto de llaves es análogo a las que usamos antes cuando agregamos
reglas de validación para el modelo de formulario.

El comportamiento de la clase @`FileInput` es diferente del comportamiento de la
clase @`Input`[Laminas\InputFilter\Input] en los siguientes aspectos:

 1. Ella espera que los datos que se le pasan como entrada estén en el formato del
    arreglo `$_FILES` (un arreglo con las llaves `tmp_name`, `error`, `type`, etc).

 2. Un validador @`Laminas\Validator\File\Upload` se inserta automáticamente en la
    cadena de validación de entradas antes de todos los otros validadores.

 3. Los validadores insertados en la cadena de validación de entradas se ejecutan
    *antes* que los filtros insertados en la cadena de filtros. Este comportamiento
    es opuesto al de la clase @`Input`[Laminas\InputFilter\Input].

### Ejecutar los Validadores antes que los Filtros

Para los campos usuales de un formulario, los filtros se ejecutan generalmente
*antes* que los validadores y los validadores se ejecutan *después* que los filtros.
Sin embargo, para los archivos que se han subido este orden se invierte.

I> Para los archivos subidos, los validadores se ejecutan *antes* que los filtros.
I> Este comportamiento es inverso al comportamiento usual.

Cuando trabajamos con archivos que se han subido, primero necesitamos revisar que
los datos que se extraen del arreglo super-global `$_FILES` son correctos, para
luego hacer cualquier cosa con los archivos (mover el archivo a una carpeta,
renombrarlo, etc.). Por esto, los validadores de archivo se deben ejecutar
primero y los filtros en segundo lugar.

Para ver como se ejecuta este proceso recordemos el flujo de trabajo típico para
un formulario:

 * Primero, llamamos al método `setData()` para llenar el formulario con datos.
 * Llamamos al método `isValid()` para ejecutar los filtros y validadores que
   están en el filtro de entradas asociado al formulario.
 * Si la validación es exitosa, llamamos al método `getData()` para extraer los
   datos filtrados y validados del filtro de entradas asociado al formulario.
 * En caso de falla, llamamos al método `getMessages()` para recuperar los
   mensajes de error de la validación.

Cuando usamos la clase para entradas `FileInput` el flujo de trabajo es el mismo.
Sin embargo es importante entender lo que sucede en cada uno de estos pasos.

 * Llamamos al método `setData()` para llenar el formulario con los datos.
 * Llamamos al método `isValid()` para **ejecutar los validadores** que están en
   el filtro de entradas asociado al formulario.
 * Si la validación es exitosa, llamamos al método `getData()` para **ejecutar los
   filtros** y extraer los datos filtrados y validados del filtro de entradas
   asociado al formulario.
 * En caso de falla, llamamos al método `getMessages()` para recuperar los
   mensajes de error de la validación.

I> Nótese que con la clase para entradas @`FileInput` los filtros asociados solo
I> se ejecutan si se llama al método `getData()`.

Cuando usamos ambas clases para entradas, @`Input`[Laminas\InputFilter\Input] y @`FileInput`, en nuestro
filtro de entradas de formulario (que es el caso común), los filtros aún se
ejecutan primero para las entradas comunes pero los validadores se ejecutan
primero para las entradas de archivo.

## Acción de controlador y carga de archivos

En esta sección, con un ejemplo de código breve mostraremos como administrar
la subida de archivo con un método de acción en el controlador. Atraeremos
la atención del lector a los aspectos específicos de la subida de archivos.

Supongamos que queremos agregar una página web que muestre un formulario
(llamémoslo `YourForm`) capaz de subir archivos. Para esta página, necesitamos
agregar el método `uploadAction()` a la clase controladora:

~~~php
<?php
//...
class IndexController extends AbstractActionController
{
    // This is the "upload" action displaying the Upload page.
    public function uploadAction()
    {
        // Create the form model.
        $form = new YourForm();

        // Check if user has submitted the form.
        if($this->getRequest()->isPost()) {

            // Make certain to merge the files info!
            $request = $this->getRequest();
            $data = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );

            // Pass data to form.
            $form->setData($data);

            // Execute file validators.
            if($form->isValid()) {

                // Execute file filters.
                $data = $form->getData();

                // Redirect the user to another page.
                return $this->redirect()->toRoute('application', ['action'=>'index']);
            }
        }

        // Render the page.
        return new ViewModel([
                 'form' => $form
            ]);
    }
}
~~~

Como podemos ver en el código de arriba, el método `uploadAction()` tiene el aspecto
usual de una acción de controlador que implementa el flujo de trabajo típico,
sin embargo el método tiene algunos aspectos específicos para cargar archivos
(estos aspectos se marcan en **negritas**):

  * En la línea 9, creamos una instancia del modelo de formulario `YourForm` con
    la ayuda del operador `new`.

  * En la línea 12, revisamos si la petición es una petición HTTP POST. Si es así,
    **traemos los datos de los arreglos super-globales de PHP `$_POST` y `$_FILES`
    y los unimos en un solo arreglo (líneas 15-19). Esto es necesario para gestionar
    correctamente los archivos subidos. Luego pasamos este arreglo al modelo del
    formulario con el método `setData()` (línea 22)**.

  * En la línea 25, llamamos al método del modelo de formulario `isValid()`.
    Este método ejecuta el filtro de entradas asociado al modelo de formulario.
    Para las entradas de la clase @`FileInput` **solo se ejecutarán los validadores
    asociados**.

  * Si los datos son validos, llamamos al método `getData()` (línea 28). Para los
    campos de la clase @`FileInput`, **este método ejecutará los filtros de archivo
    asociados**. Los filtros de archivos, por ejemplo, podrían mover los archivos
    subidos a la carpeta de destino.

  * En caso de éxito, en la línea 31, dirigimos al usuario a la acción «index»
    del controlador.

I> Arriba, en la acción del controlador debemos recordar tres cosas: 1) unir
I> los arreglos super-globales `$_POST` y `$_FILES` antes de pasarlos
I> al método del formulario `setData()`; 2) usar el método del formulario
I> `isValid()` para revisar la corrección de los archivos subidos (ejecutar
I> validadores); 3) usar el método del formulario `getData()` para ejecutar los
I> filtros.

## Ejemplo: Galería de Imágenes

Para mostrar como cargar archivos con Laminas Framework, crearemos
una Galería de Imágenes que consistirá de dos páginas web: la página de carga
de imágenes permite cargar una imagen (figura 10.2) y la página de galería
contiene la lista de las imágenes subidas (figura 10.3).

T> Podemos ver trabajando la *Galería de Imágenes* en la aplicación
T> de ejemplo *Form Demo* que está adjunta a este libro.

{width=80%}
![Figura 10.2. Página de carga de imágenes](../en/images/uploads/upload_image_form.png)

{width=80%}
![Figura 10.3. Página para la galería de imágenes](../en/images/uploads/image_gallery.png)

Para este ejemplo, crearemos los siguientes elementos:

  * El modelo de formulario `ImageForm` capaz de subir los archivos de imagen.
  * La clase de servicio `ImageManager` diseñada para obtener la lista de las
    imágenes subidas, recuperar información sobre una imagen y cambiar el tamaño
    de una images.
  * La clase `ImageController` que contendrá los métodos de acción que muestran
    las páginas web.
  * La fábrica `ImageControllerFactory` que instanciará el controlador e inyectará
    las dependencias dentro del controlador.
  * Un archivo de plantilla de vista `.phtml` por cada método de acción en el
    controlador.

### Agregar el modelo ImageForm

Para este ejemplo, necesitaremos un modelo de formulario que se usará para cargar
los archivos de imagen. Llamaremos a está clase de modelo de formulario `ImageForm`.
Esta clase nos permitirá cargar un archivo de imagen al servidor. El formulario
tendrá los siguientes campos:

  * Un campo tipo `file` que permitirá al usuario seleccionar un archivo de imagen
    para subir.

  * Un campo tipo `submit` para el botón que permite enviar los datos del formulario
    al servidor.

El código del modelo de formulario `ImageForm` se presenta más abajo. Este se
debe colocar en un archivo *ImageForm.php* guardado en la carpeta *Form* que
está dentro de la carpeta fuente del módulo:

~~~php
<?php
namespace Application\Form;

use Laminas\Form\Form;

// This form is used for uploading an image file.
class ImageForm extends Form
{
    // Constructor.
    public function __construct()
    {
        // Define form name.
        parent::__construct('image-form');

        // Set POST method for this form.
        $this->setAttribute('method', 'post');

        // Set binary content encoding.
        $this->setAttribute('enctype', 'multipart/form-data');

        $this->addElements();
    }

    // This method adds elements to form.
    protected function addElements()
    {
        // Add "file" field.
        $this->add([
            'type'  => 'file',
            'name' => 'file',
            'attributes' => [
                'id' => 'file'
            ],
            'options' => [
                'label' => 'Image file',
            ],
        ]);

        // Add the submit button.
        $this->add([
            'type'  => 'submit',
            'name' => 'submit',
            'attributes' => [
                'value' => 'Upload',
                'id' => 'submitbutton',
            ],
        ]);
    }
}
~~~

Ya hemos discutido sobre la creación de un modelo de formulario por lo que el
código de arriba no debería ofrecer ningún problema para su comprensión. Solo
queremos llamar la atención del lector que en la línea 19 colocamos el valor
«multipart/form-data» para el atributo del formulario «enctype» que hace que el
formulario use codificación binaria para sus datos.

T> En realidad, colocar explícitamente el atributo «enctype» en el constructor
T> del formulario es opcional, porque la clase @`Laminas\Form\Element\File` asigna
T> este atributo cuando se llama al método del formulario `prepare()`.

### Agregar Reglas de Validación al Modelo ImageForm

Para mostrar el uso de los validadores y filtros diseñados para trabajar con
archivos que se cargan, los agregaremos a la clase de modelo de formulario
`ImageForm`. Queremos alcanzar los siguientes objetivos:

 * Usando el validador @`UploadFile` revisaremos si el archivo se subió a través
   del método POST de HTTP.
 * Usando el validador @`IsImage` revisaremos si el archivo subido es una imagen
   (JPEG, PNG, GIF, etc.).
 * Con el validador @`ImageSize` revisaremos si las dimensiones de la imagen están
   entre los límites permitidos.
 * Con el filtro @`RenameUpload` moveremos el archivo subido a su carpeta de
   residencia final.

Para agregar reglas de validación al formulario modificamos el código de la clase
`ImageForm` de la siguiente manera:

~~~php
<?php
namespace Application\Form;

use Laminas\InputFilter\InputFilter;

// This form is used for uploading an image file.
class ImageForm extends Form
{
    // Constructor
    public function __construct()
    {
        // ...

        // Add validation rules
        $this->addInputFilter();
    }

    // ...

    // This method creates input filter (used for form filtering/validation).
    private function addInputFilter()
    {
        $inputFilter = new InputFilter();
        $this->setInputFilter($inputFilter);

        // Add validation rules for the "file" field.
        $inputFilter->add([
                'type'     => 'Laminas\InputFilter\FileInput',
                'name'     => 'file',
                'required' => true,
                'validators' => [
                    ['name'    => 'FileUploadFile'],
                    [
                        'name'    => 'FileMimeType',
                        'options' => [
                            'mimeType'  => ['image/jpeg', 'image/png']
                        ]
                    ],
                    ['name'    => 'FileIsImage'],
                    [
                        'name'    => 'FileImageSize',
                        'options' => [
                            'minWidth'  => 128,
                            'minHeight' => 128,
                            'maxWidth'  => 4096,
                            'maxHeight' => 4096
                        ]
                    ],
                ],
                'filters'  => [
                    [
                        'name' => 'FileRenameUpload',
                        'options' => [
                            'target' => './data/upload',
                            'useUploadName' => true,
                            'useUploadExtension' => true,
                            'overwrite' => true,
                            'randomize' => false
                        ]
                    ]
                ],
            ]);
    }
}
~~~

En el código de arriba se agregaron las siguientes validaciones de archivo:

  * El validador @`UploadFile` (línea 32) revisa si el archivo realmente se subió
    usando el método POST de HTTP.

  * El validador @`MimeType` (línea 34) revisa si el archivo subido es una imagen
    JPEG o PNG. Esto se hace extrayendo la información MIME de los datos del archivo.

  * El validador @`IsImage` (línea 39) revisa si el archivo subido es una imagen
    (PNG, JPG, etc.). Esto se hace extrayendo la información MIME de los datos
    del archivo.

  * El validador @`ImageSize` (línea 41) permite revisar si las dimensiones del
    archivo están en un rango dado. En el código de arriba revisamos que la
    imagen tiene entre 128 y 4096 pixeles de ancho y que la altura de la imagen
    está entre 128 y 4086 pixeles.

En la línea 52, agregamos el filtro @`RenameUpload` y lo configuramos para guardar
el archivo subido en la carpeta *APP_DIR/data/upload*. El filtro usará el
nombre del archivo original como nombre para el archivo de destino (opción
`useUploadName`). Si un archivo con el mismo nombre ya existe, el filtro lo
sobreescribirá (opción `overwrite`).

W> Para que los validadores @`MimeType` y @`IsImage` funciones debemos activar la
W> extensión de PHP `fileinfo`. Esta extensión está habilitada por defecto en
W> sistemas GNU/Linux como Ubuntu pero no en Windows. No debemos olvidar que
W> después de activar la extensión debemos reiniciar el servidor Apache HTTP.

### Escribir el servicio ImageManager

Como nos esforzamos por escribir código siguiendo el patrón de Diseño Guiado
por Dominio crearemos un clase de modelo de servicio para encapsular la
funcionalidad de la gestión de imágenes. Llamaremos a esta clase `ImageManager`
y la colocaremos en el espacio de nombre `Application\Service`. Además,
registraremos este servicio en el componente de administración de servicios
de la aplicación web.

La clase de servicio `ImageManager` tendrá los siguientes métodos públicos
(listados en la tabla 10.3):

{title="Tabla 10.3. Métodos públicos de la clase ImageManager."}
|---------------------------------|------------------------------------------------------------------|
| *Método*                        | *Descripción*                                                    |
|---------------------------------|------------------------------------------------------------------|
| `getSaveToDir()`                | Regresa la ruta de la carpeta donde guardamos los archivos de imagen.|
|---------------------------------|------------------------------------------------------------------|
| `getSavedFiles()`               | Regresa un arreglo con los nombres de las imágenes guardadas.    |
|---------------------------------|------------------------------------------------------------------|
| `getImagePathByName($fileName)` | Regresa la ruta del archivo de imagen guardado.                 |
|---------------------------------|------------------------------------------------------------------|
| `getImageFileInfo($filePath)`   | Regresa la información del archivo (tamaño, tipo MIME) a partir  |
|                                 | de la ruta de la imagen.                                         |
|---------------------------------|------------------------------------------------------------------|
| `getImageFileContent($filePath)`| Regresa el contenido del archivo de imagen. En caso de error,    |
|                                 | se regresa el booleano `false`.                                  |
|---------------------------------|------------------------------------------------------------------|
| `resizeImage($filePath, $desiredWidth)` | Cambia el tamaño de la imagen conservando su cociente de |
|                                         | aspecto.                                                 |
|---------------------------------|------------------------------------------------------------------|

T> De hecho, podríamos colocar el código que planeamos agregar dentro del servicio
T> en las acciones del controlador, pero esto haría grande al controlador
T> y difícil de probar. Con la introducción de la clase de servicio, mejoramos
T> la separación de conceptos y la reusabilidad del código.

Agregamos el archivo *ImageManager.php* a la carpeta *Service* que esta dentro
de la carpeta fuente del módulo. Agregamos el siguiente código al archivo:

~~~php
<?php
namespace Application\Service;

// The image manager service.
class ImageManager
{
    // The directory where we save image files.
    private $saveToDir = './data/upload/';

    // Returns path to the directory where we save the image files.
    public function getSaveToDir()
    {
        return $this->saveToDir;
    }
}
~~~

Como podemos ver en el código de arriba, definimos la clase `ImageManager` en la
línea 5. La clase tiene la propiedad privada `$saveToDir` [^property] que
contiene la ruta a la carpeta que contiene nuestros archivos subidos (línea 8),
guardamos los archivos subidos en la carpeta *APP_DIR/data/upload*.

El método público `getSaveToDir()` (línea 11) permitirá recuperar la ruta de la
carpeta de los archivos subidos.

[^property]: Aunque la clase `ImageManager` es una servicio y está enfocada en
             proveer servicios, podemos tener propiedades para su uso interno.

Luego, vamos a agregar el método público `getSavedFiles()` a la clase de servicio.
El método escaneará la carpeta de subida y regresa un arreglo que contiene los
nombres de los archivos subidos. Para agregar el método `getSavedFiles()`
modificamos el código de la siguiente manera:

~~~php
<?php
//...

// The image manager service.
class ImageManager
{
    //...

    // Returns the array of uploaded file names.
    public function getSavedFiles()
    {
        // The directory where we plan to save uploaded files.

        // Check whether the directory already exists, and if not,
        // create the directory.
        if(!is_dir($this->saveToDir)) {
            if(!mkdir($this->saveToDir)) {
                throw new \Exception('Could not create directory for uploads: ' .
                             error_get_last());
            }
        }

        // Scan the directory and create the list of uploaded files.
        $files = [];
        $handle  = opendir($this->saveToDir);
        while (false !== ($entry = readdir($handle))) {

            if($entry=='.' || $entry=='..')
                continue; // Skip current dir and parent dir.

            $files[] = $entry;
        }

        // Return the list of uploaded files.
        return $files;
    }
}
~~~

Con el método `getSavedFiles()`, primero revisamos si existe la carpeta de subida
(línea 16), y si no, se intenta crearla (línea 17). Luego, traemos la lista de
archivos en la carpeta (líneas 24-32) y la regresamos a quien llamo al método.

Luego, agregamos los tres métodos para traer la información sobre el
archivo subido:

  * El método `getImagePathByName()` tomará el nombre del archivo y le añadirá al
    principio la ruta a la carpeta de subida.

  * El método `getImageFileInfo()` recuperará la información MIME del archivo y
    su tamaño en bytes.

  * Y el método `getImageFileContent()` leerá los datos del archivo y los regresará
    como una cadena de caracteres.

Para agregar estos tres métodos, cambiamos el código de la siguiente manera:

~~~php
<?php
//...

// The image manager service.
class ImageManager
{
    //...

    // Returns the path to the saved image file.
    public function getImagePathByName($fileName)
    {
        // Take some precautions to make file name secure.
        $fileName = str_replace("/", "", $fileName);  // Remove slashes.
        $fileName = str_replace("\\", "", $fileName); // Remove back-slashes.

        // Return concatenated directory name and file name.
        return $this->saveToDir . $fileName;
    }

    // Returns the image file content. On error, returns boolean false.
    public function getImageFileContent($filePath)
    {
        return file_get_contents($filePath);
    }

    // Retrieves the file information (size, MIME type) by image path.
    public function getImageFileInfo($filePath)
    {
        // Try to open file
        if (!is_readable($filePath)) {
            return false;
        }

        // Get file size in bytes.
        $fileSize = filesize($filePath);

        // Get MIME type of the file.
        $finfo = finfo_open(FILEINFO_MIME);
        $mimeType = finfo_file($finfo, $filePath);
        if($mimeType===false)
            $mimeType = 'application/octet-stream';

        return [
            'size' => $fileSize,
            'type' => $mimeType
        ];
    }
}
~~~

Finalmente, agregaremos a la clase `ImageManager` la funcionalidad para cambiar
el tamaño de la imagen. La funcionalidad para cambiar el tamaño de la imagen
se usará para crear imágenes en miniatura. Agregamos el método `resizeImage()`
a la clase `ImageManager` de la siguiente manera:

~~~php
<?php
//...
class ImageManager
{
    //...

    // Resizes the image, keeping its aspect ratio.
    public  function resizeImage($filePath, $desiredWidth = 240)
    {
        // Get original image dimensions.
        list($originalWidth, $originalHeight) = getimagesize($filePath);

        // Calculate aspect ratio
        $aspectRatio = $originalWidth/$originalHeight;
        // Calculate the resulting height
        $desiredHeight = $desiredWidth/$aspectRatio;

        // Get image info
        $fileInfo = $this->getImageFileInfo($filePath);

        // Resize the image
        $resultingImage = imagecreatetruecolor($desiredWidth, $desiredHeight);
        if (substr($fileInfo['type'], 0, 9) =='image/png')
            $originalImage = imagecreatefrompng($filePath);
        else
            $originalImage = imagecreatefromjpeg($filePath);
        imagecopyresampled($resultingImage, $originalImage, 0, 0, 0, 0,
            $desiredWidth, $desiredHeight, $originalWidth, $originalHeight);

        // Save the resized image to temporary location
        $tmpFileName = tempnam("/tmp", "FOO");
        imagejpeg($resultingImage, $tmpFileName, 80);

        // Return the path to resulting image.
        return $tmpFileName;
    }
}
~~~

El método `resizeImage()` toma dos argumentos: la ruta al archivo de imagen
`$filePath` y el ancho de la imagen miniatura `$desiredWidth`. Dentro del método
primero calculamos la altura apropiada para la imagen en miniatura (líneas 11-16)
manteniendo su cociente de aspecto. Luego, cambiamos el tamaño original de la imagen
y la guardamos en un archivo temporal (líneas 19-32).

Cuando la clase `ImageManager` está lista debemos registrar el servicio en el
componente de administración de servicios de la aplicación agregando las siguientes
líneas al archivo de configuración *module.config.php*.

~~~php
<?php
return [
    // ...
    'service_manager' => [
        // ...
        'factories' => [
            // Register the ImageManager service
            Service\ImageManager::class => InvokableFactory::class,
        ],
    ],
    // ...
];
~~~

### Agregar la clase ImageController

Para el ejemplo *Galería de Imagen* crearemos la clase controladora `ImageController`.
El controlador tendrá los siguientes métodos de acción (listados en la tabla 10.4):

{title="Tabla 10.4. Métodos de acción de la clase ImageController."}
|---------------------------------|------------------------------------------------------------------|
| *Método de acción*              | *Descripción*                                                    |
|---------------------------------|------------------------------------------------------------------|
| `__construct()`                 | Permitirá inyectar la dependencia `ImageManager` dentro del controlador.|
|---------------------------------|------------------------------------------------------------------|
| `uploadAction()`                | Muestra la página de subida de imágenes que permite cargar una imagen.|
|---------------------------------|------------------------------------------------------------------|
| `indexAction()`                 | Muestra la página con la galería de imágenes que lista las imágenes subidas.|
|---------------------------------|------------------------------------------------------------------|
| `fileAction()`                  | Provee la capacidad de descargar la imagen en tamaño completo o  |
|                                 | la imagen en miniatura.                                          |
|---------------------------------|------------------------------------------------------------------|

Para comenzar, creamos el archivo *ImageController.php* en la carpeta *Application/Controller*
que está dentro de la carpeta fuente del módulo. Colocamos el siguiente pedazo
de código dentro de archivo:

~~~php
<?php
namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Application\Form\ImageForm;

// This controller is designed for managing image file uploads.
class ImageController extends AbstractActionController
{
    // The image manager.
    private $imageManager;

    // The constructor method is used for injecting the dependencies
    // into the controller.
    public function __construct($imageManager)
    {
        $this->imageManager = $imageManager;
    }

    // This is the default "index" action of the controller. It displays the
    // Image Gallery page which contains the list of uploaded images.
    public function indexAction()
    {
    }

    // This action shows the image upload form. This page allows to upload
    // a single file.
    public function uploadAction()
    {
    }

    // This is the 'file' action that is invoked when a user wants to
    // open the image file in a web browser or generate a thumbnail.
    public function fileAction()
    {
    }
}
~~~

En el código de arriba definimos la clase `ImageController` que está en el espacio
de nombres `Application\Controller`, agregamos el método constructor y tres esbozos
de métodos de acción más en la clase: `indexAction()`, `uploadAction()` y `fileAction()`.
Luego, llenaremos estos métodos de acción con código.

#### Agregar la acción de subida y la plantilla de vista correspondiente

Primero, completaremos el método `uploadAction()` de nuestro controlador. Este
método de acción gestionará la página web *Cargar una Nueva Image* y contiene
el formulario de subida. El formulario proveerá la capacidad de subir un archivo
de imagen a la galería.

Cambiamos el archivo *ImageController.php* de la siguiente manera:

~~~php
<?php
//...
class ImageController extends AbstractActionController
{
    //...
    public function uploadAction()
    {
        // Create the form model.
        $form = new ImageForm();

        // Check if user has submitted the form.
        if($this->getRequest()->isPost()) {

            // Make certain to merge the files info!
            $request = $this->getRequest();
            $data = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );

            // Pass data to form.
            $form->setData($data);

            // Validate form.
            if($form->isValid()) {

                // Move uploaded file to its destination directory.
                $data = $form->getData();

                // Redirect the user to "Image Gallery" page.
                return $this->redirect()->toRoute('images');
            }
        }

        // Render the page.
        return new ViewModel([
                     'form' => $form
                 ]);
    }
}
~~~

En el método `uploadAction()` de arriba estamos haciendo lo siguiente.

En la línea 9, creamos una instancia del modelo de formulario `ImageForm` con la
ayuda del operador `new`.

En la línea 12, revisamos si la petición es una petición POST de HTTP. Si lo es,
traemos los datos de los arreglos super-globales de PHP `$_POST` y `$_FILES` y
los mezclamos dentro de un solo arreglo (líneas 15-19). Esto es necesario para
gestionar correctamente los archivos subidos en el caso de que existan. Luego,
con el método `setData()` pasamos el arreglo al modelo de formulario (línea 22).

En la línea 25, llamamos al método `isValid()` del modelo de formulario. Este
método ejecuta el filtro de entrada asociado al modelo de formulario. Como
tenemos solo una entrada de tipo *file* en el filtro de entradas, él solo ejecutará
tres validaciones de archivo : @`UploadFile`, @`IsImage` y @`ImageSize`.

Si los datos son validos, llamamos al método  `getData()` (línea 28). Para
nuestro campo tipo *file*, este método ejecutará el filtro @`RenameUpload`, que
mueve el archivo subido a la carpeta de residencia final.

Después de esto, en la línea 31, dirigimos al usuario a la acción «index»
del controlador (colocaremos el código de este método de acción dentro de poco).

Ahora, es el momento de agregar la plantilla de vista para la acción «upload».
Agregamos el archivo de plantilla de vista *upload.phtml* dentro de la carpeta
*application/image* que está dentro de la carpeta *view* del modulo:

~~~php
<?php
$form = $this->form;
$form->get('submit')->setAttributes(['class'=>'btn btn-primary']);
$form->prepare();
?>

<h1>Upload a New Image</h1>

<p>
    Please fill out the following form and press the <i>Upload</i> button.
</p>

<div class="row">
    <div class="col-md-6">
        <?= $this->form()->openTag($form); ?>

        <div class="form-group">
            <?= $this->formLabel($form->get('file')); ?>
            <?= $this->formElement($form->get('file')); ?>
            <?= $this->formElementErrors($form->get('file')); ?>
            <div class="hint">(PNG and JPG formats are allowed)</div>
        </div>

        <?= $this->formElement($form->get('submit')); ?>

        <?= $this->form()->closeTag(); ?>
    </div>
</div>
~~~

En el código de la plantilla de vista, primero colocamos el atributo «class»
(línea 3). Esto se hace para aplicar los estilos de Twitter Bootstrap al
botón *Submit* del formulario y obtener una mejor apariencia.

Luego, imprimimos el formulario con el ayudante de vista acostumbrado sobre el
que discutimos en [Colectar las Entradas del Usuario con Forms](#forms). Para
imprimir el campo tipo «file» usamos el ayudante de vista genérico @`FormElement`.

I> Generalmente, usamos el ayudante de vista genérico @`FormElement` para imprimir
I> el campo de tipo *file*. El ayudante @`FormElement` llama internamente al
I> ayudante de vista @`FormFile`, quien ejecuta realmente la impresión.

#### Agregar la Acción Index y su Correspondiente Plantilla de Vista

El segundo método que completaremos es el `indexAction()`. Esta acción gestionará
la página para la *Galería de Imágenes* que contiene una lista con los archivos
subidos y su miniatura. Para cada imagen, existirá el botón «Ver en Tamaño Natural»
que abrirá la imagen en otra pestaña del navegador.

Cambiamos el archivo *ImageController.php* de la siguiente manera:

~~~php
<?php
//...
class ImageController extends AbstractActionController
{
    //...
    public function indexAction()
    {
        // Get the list of already saved files.
        $files = $this->imageManager->getSavedFiles();

        // Render the view template.
        return new ViewModel([
            'files'=>$files
        ]);
    }
}
~~~

En el código de arriba usamos el método `getSavedFiles()` de la clase `ImageManager`
para recuperar la lista de imágenes subidas y pasarlas a la vista para su impresión.

T> ¡Nótese cuan «delgado» y claro es esta acción de controlador! Esto se alcanzó
T> moviendo las funcionalidades de gestión de imágenes al modelo de servicio
T> `ImageManager`.

Agregamos la plantilla de vista *index.phtml* en la carpeta *application/image*
que está dentro de la carpeta *view* del módulo. El contenido de este archivo se
muestra abajo:

~~~php
<h1>Image Gallery</h1>

<p>
  This page displays the list of uploaded images.
</p>

<p>
  <a href="<?= $this->url('images', ['action'=>'upload']); ?>"
     class="btn btn-primary" role="button">Upload More</a>
</p>

<hr/>

<?php if(count($files)==0): ?>

<p>
  <i>There are no files to display.</i>
</p>

<?php else: ?>

<div class="row">
  <div class="col-sm-6 col-md-12">

    <?php foreach($files as $file): ?>

    <div class="img-thumbnail">

      <img src="<?= $this->url('images', ['action'=>'file'],
            ['query'=>['name'=>$file, 'thumbnail'=>true]]); ?>">

      <div class="caption">
        <h3><?php echo $file; ?></h3>
        <p>
        <a target="_blank" href="<?= $this->url('images', ['action'=>'file'],
           ['query'=>['name'=>$file]]); ?>"
           class="btn btn-default" role="button">Show in Natural Size</a>
        </p>
      </div>
    </div>

    <?php endforeach; ?>
  </div>
</div>

<?php endif; ?>

<hr/>
~~~

En el código de arriba, creamos el código HTML para el botón *Upload More*.

Abajo del botón, revisamos si el arreglo `$files` está vacío. Si el arreglo está
vacío imprimimos el mensaje «There are no files to display», de lo contrario
recorremos los archivos e imprimimos las miniaturas de cada imagen subida.

Para mostrar una miniatura, usamos la etiqueta `<img>`. A la etiqueta le asignamos
el atributo `src` con la URL que apunta a la acción «file» de nuestro controlador
`ImageController`. Nosotros pasamos dos argumentos a la acción por medio de la
parte de consulta de la URL: el nombre de la imagen y la bandera de miniatura.

Para dar estilo a las miniaturas, usamos la clase CSS «.img-thumbnail» que provee
Twitter Bootstrap.

T> Para información adicional sobre estos estilos de Twitter Bootstrap podemos
T> ver la documentación oficial de Bootstrap.

Abajo de cada miniatura colocamos el enlace «Ver en Tamaño Natural» que apunta
a la acción «file» de nuestro controlador `ImageController`. Cuando un visitante
del sitio hace clic en el enlace se le mostrará la imagen en tamaño natural.
La imagen se abrirá en una nueva pestaña del navegador (nótese el atributo del
enlace `target="_blank"`).

#### Agregar la acción File

La última acción que completaremos es el método `ImageController::fileAction()`.
Este método permite tener un vista previa de la imagen subida o generar una
miniatura de la imagen. La acción del método tomará dos parámetros GET:

  * El parámetro «name» define el nombre del archivo del que se generará una
    vista previa.
  * El parámetro «thumbnail» es una bandera que indica si queremos volcar la
    imagen completa o su copia pequeña.

Cambiamos el archivo *ImageController.php* de la siguiente manera:

~~~php
 <?php
//...
class ImageController extends AbstractActionController
{
    //...
    public function fileAction()
    {
        // Get the file name from GET variable.
        $fileName = $this->params()->fromQuery('name', '');

        // Check whether the user needs a thumbnail or a full-size image.
        $isThumbnail = (bool)$this->params()->fromQuery('thumbnail', false);

        // Get path to image file.
        $fileName = $this->imageManager->getImagePathByName($fileName);

        if($isThumbnail) {

            // Resize the image.
            $fileName = $this->imageManager->resizeImage($fileName);
        }

        // Get image file info (size and MIME type).
        $fileInfo = $this->imageManager->getImageFileInfo($fileName);
        if ($fileInfo===false) {
            // Set 404 Not Found status code
            $this->getResponse()->setStatusCode(404);
            return;
        }

        // Write HTTP headers.
        $response = $this->getResponse();
        $headers = $response->getHeaders();
        $headers->addHeaderLine("Content-type: " . $fileInfo['type']);
        $headers->addHeaderLine("Content-length: " . $fileInfo['size']);

        // Write file content.
        $fileContent = $this->imageManager->getImageFileContent($fileName);
        if($fileContent!==false) {
            $response->setContent($fileContent);
        } else {
            // Set 500 Server Error status code.
            $this->getResponse()->setStatusCode(500);
            return;
        }

        if($isThumbnail) {
            // Remove temporary thumbnail image file.
            unlink($fileName);
        }

        // Return Response to avoid default view rendering.
        return $this->getResponse();
    }
}
~~~

En el código de arriba, primero traemos los parámetros «name» y «thumbnail» del
arreglo super-global `$_GET` (líneas 9 y 12). Si los parámetros no están presentes
se usan valores por defectos en su lugar.

En la línea 15, usamos el método `getImagePathByName()` que provee el servicio
`ImageManager` para traer la ruta absoluta a la imagen a partir de su nombre.

Si se solicita la imagen en miniatura cambiamos el tamaño de la imagen con el
método `resizeImage()` del servicio `ImageManager` (línea 20). Este método
regresa la ruta a un archivo temporal que contiene la imagen miniatura.

Luego, traemos la información sobre el archivo de imagen (su tipo MIME y tamaño)
con el método `getImageFileInfo()` del servicio `ImageManager` (línea 24).

Finalmente creamos el objeto @`Response`[Laminas\Http\PhpEnvironment\Response],
llenaremos su encabezado con la información
de la imagen, colocaremos en su contenido los datos del archivo de imagen
(líneas 32-45) y regresamos el objeto @`Response`[Laminas\Http\PhpEnvironment\Response]
desde la acción del controlador (línea 53).

I> Nótese que al retornar el objeto @`Response`[Laminas\Http\PhpEnvironment\Response]
I> se desactiva la impresión por
I> defecto de la plantilla de vista para este método de acción. Por esta razón
I> no creamos un archivo de plantilla de vista *file.phtml*.

#### Crear una fábrica para el controlador

Como nuestro controlador `ImageController` usa la clase de servicio `ImageManager`
necesitamos pasarle de alguna manera una instancia de `ImageManager` (inyectar
la dependencia dentro del constructor del controlador). Esto lo hacemos con la
ayuda de una *fábrica*.

Creamos el archivo `ImageControllerFactory.php` dentro de la subcarpeta
*Controller/Factory* que está dentro de la carpeta fuente del módulo.

~~~php
<?php
namespace Application\Controller\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Application\Service\ImageManager;
use Application\Controller\ImageController;

/**
 * This is the factory for ImageController. Its purpose is to instantiate the
 * controller.
 */
class ImageControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container,
                        $requestedName, array $options = null)
    {
        $imageManager = $container->get(ImageManager::class);

        // Instantiate the controller and inject dependencies
        return new ImageController($imageManager);
    }
}
~~~

#### Registrar el ImageController

Para que Laminas conozca la existencia de nuestro controlador `ImageController` lo
registramos en el archivo de configuración *module.config.php*.

~~~php
<?php
return [
    //...
    'controllers' => [
        'factories' => [
            Controller\ImageController::class =>
                    Controller\Factory\ImageControllerFactory::class,
            //...
        ],
    ],
    //...
];
~~~

#### Crear la ruta

Necesitamos agregar una *ruta* hacia nuestro controlador `ImageController`.
Para hacer esto modificamos el archivo `module.config.php`.

~~~php
<?php
return [
    //...
    'router' => [
        'routes' => [
            'images' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/images[/:action]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*'
                    ],
                    'defaults' => [
                        'controller'    => Controller\ImageController::class,
                        'action'        => 'index',
                    ],
                ],
            ],
        ],
    ],
    //...
];
~~~

Después de esto seremos capaces de tener acceso a la galería de imágenes a través
de las URLs: «http://localhost/images», «http://localhost/images/upload» or
«http://localhost/images/file».

### Resultados

Por último, ajustamos los permisos de la carpeta *APP_DIR/data* para que el
servidor Web Apache pueda escribir en ella. En GNU/Linux Ubuntu esto se logra
con los siguientes comandos de consola (se debe reemplazar el comodín `APP_DIR`
por el nombre de la carpeta real de nuestra aplicación web):

`chown -R www-data:www-data APP_DIR/data`

`chmod -R 775 APP_DIR/data`

Arriba, el comando `chown` y `chmod`, respectivamente, coloca como dueño de la
carpeta al usuario con que se ejecuta Apache y permite al servidor web escribir
en la carpeta.

Si ahora ingresamos la URL *http://localhost/images* en la barra de navegación
de nuestro navegador web veremos una página con la galería de imágenes como la
que mostramos en la figura 10.4.

{width=80%}
![Figura 10.4. Página con la Galería de Imágenes](../en/images/uploads/empty_image_gallery.png)

Haciendo clic sobre el botón *Subir Más* se abrirá la página *Subir una Nueva Imagen*
desde donde podemos agarrar un archivo de imagen para subirlo. Si agarramos un
archivo que es inaceptable, no es una imagen o es muy grande, veremos errores
de validación (ver la figura 10.5 más abajo).

{width=80%}
![Figura 10.5. Errores de validación del archivo](../en/images/uploads/image_validation_errors.png)

Si la subida se completa con éxito el usuario será redirigido a la página de
la *Galería de Imágenes* en donde podrá ver la imagen subida en la lista de
miniaturas. Haciendo clic en el botón *Ver Tamaño Completo* abriremos la imagen
en una nueva pestaña del navegador (ver la figura de ejemplo más abajo 10.6).

{width=80%}
![Figura 10.6. Abrir una Imagen en su Tamaño Natural](../en/images/uploads/image_preview.png)

T> Podemos encontrar una *Galería de Imágenes* de ejemplo en la aplicación
T> *Form Demo* que se añade a este libro.

## Resumen

La subida de archivos es una característica estándar de los formularios HTML.
La subida de archivos se logra cambiando la codificación de contenido al tipo
de codificación binaria. Laminas Framework nos provee de funcionalidades convenientes
para subir archivos y validar los archivos subidos.
