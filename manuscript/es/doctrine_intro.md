# Apéndice D. Introducción a Doctrine {#doctrine-intro}

En este apéndice damos una vista general de la biblioteca Doctrine, su arquitectura
y componentes. Como en este libro nos concentramos principalmente en el componente
de Mapeo objeto-relacional (ORM) de Doctrine la lectura de este apéndice nos dará
una idea más general de las otras capacidades de Doctrine.

## Doctrine y la Administración de la Base de Datos

Existen muchos sistemas administradores de base de datos en el mercado. Estos
sistemas se pueden dividir en dos grupos: las tradicionales *bases de datos
relacionales* que usan el lenguaje SQL para consultar y manipular datos, y el
segundo grupo, las *bases de datos NoSQL* que *no solo usan SQL* como método
para acceder y administrar los datos. En cada proyecto particular podemos
preferir determinado DBMS por sus capacidades y ventajas competitivas.

### Bases de Datos Relacionales

En una *base de datos relacional* tenemos una colección de tablas (relaciones)
que están constituidas por filas. Una fila puede tener una o varias columnas.
Una o varias filas de una tabla pueden estar enlazadas a una o varias filas
de otra tabla formando una relación entre datos.

Por ejemplo, suponiendo un sitio web para un blog cuya base de datos tiene
dos tablas: la tabla `post` y la tabla `comment`.  La tabla `post` tendría las
columnas `id`, `title`, `content`, `author` y `date_created` y la tabla comment
tendría las columnas `id`, `post_id`, `author`, `content` y `date_created`.
La tabla `post` se relaciona con la tabla `comment` de uno-a-muchos porque
una publicación tiene cero o más comentarios (muchos) mientras que un determinado
comentario pertenece a una sola publicación.

Las tablas mencionadas arriba con sus columnas y relaciones se muestran abajo
en la figura D.1.

![Figura D.1. Tablas y relaciones entre tablas. Una publicación tiene muchos comentarios](../en/images/doctrine_intro/post_comment_relationship.png)

En el marcado existe un número mayor de base de datos relacionales. Entre ellas
están:  [MySQL](http://www.mysql.com/), [PostgreSQL](http://www.postgresql.org/), [Oracle](https://www.oracle.com/index.html),
[Microsoft SQL Server](https://www.microsoft.com/en-us/cloud-platform/sql-server), etc.

Cada sistema de base de datos tiene sus características específicas que no tienen
otros DBMS. Por ejemplo:

  * SQLite está diseñado como un extensión incluida en el motor de PHP y no necesita
    instalación pero solo trabaja bien en sitios simples.

  * MySQL es un sistema libre que es muy simple de instalar y administrar. Es
    bueno para sistemas de pequeña y mediana escala.

  * Commercial Oracle DBMS tiene como objetivo principal sistemas de gran escala
    y tiene herramientas de administración sofisticadas.

  * PostgreSQL soporta grandes bases de datos y se puede considerar como un reemplazo
    de software libre y código abierto de Oracle.

La biblioteca Doctrine se diseñó para trabajar con las principales bases
de datos mediante el uso de un interfaz de programación unificada. Esta interfaz
de programación se implementa en dos niveles:

  1. A bajo nivel, Doctrine provee un mecanismo unificado para construir consultas
     SQL para cualquiera de las bases de datos relacionales soportadas y manipular
     el esquema de la base de datos. Este mecanismo se implementa en el componente
     *Database Abstraction Layer* (DBAL).

  2. A alto nivel, el componente *Object Relacional Mapper* (ORM) de Doctrine
     provee la capacidad de consultar y administrar bases de datos de una
     manera orientada a objetos asociando las tablas a clases PHP. Además, este
     componente provee un lenguaje de consulta a base datos propio llamado
     DQL que permite construir consultas con el estilo orientado a objetos.

Generalmente usamos la API que provee el componente de alto nivel ORM. Al mismo
tiempo podemos fácilmente usar el componente de bajo nivel DBAL si consideramos
que es necesario para nuestras necesidades particulares.

I> Doctrine es independiente de la base de datos. En teoría cuando usamos Doctrine
I> somos capaces de abstraernos del tipo de base de datos y cambiar entre bases
I> de datos más fácilmente que cuando usamos una solución dependiente de una
I> base de datos.

#### SQL vs. DQL

Cuando usamos un sistema de base de datos relacional generalmente usamos el
*lenguaje SQL* como una manera estándar para acceder a los datos de la base de
datos y administrar el esquema de base de datos. Sin embargo, cada DBMS generalmente
tiene una extensión específica del lenguaje SQL (dialecto).

I> La biblioteca Doctrine se diseño para trabajar con todos los principales
I> sistemas de bases de datos relacionales que usan el lenguaje SQL, pero es
I> obvio que solo soporte un subconjunto de su funcionalidad y de las
I> capacidades del lenguaje SQL.

Doctrine se construye sobre la principal extensión PDO de PHP (y otras extensiones
de PHP para base de datos como `sqlite`, `mysqli`, `oci8`, etc.) Estas extensiones
proveen controladores para todos los principales sistemas de base de datos
relacionales. Especificamos el controlador adecuado a usar cuando configuramos
la conexión a la base de datos.

T> Si no estamos familiarizados con SQL, un buen lugar para aprender su sintaxis
T> es [W3Schools Tutorials](http://www.w3schools.com/sql/default.asp).

[^pdo]: La extensión PHP Data Objects (PDO) define una interfaz ligera y consistente
        para acceder a la base de datos con PHP. Para hacerlo de una manera
        independiente de la base de datos PDO usa el concepto de controladores
        de base de datos. Cada controlador de base de datos que implementa una
        interfaz PDO puede proveer las características específicas de la base
        de datos como funciones regulares de la extensión.

Como el componente Object Relational Mapper de Doctrine se diseñó para trabajar
con objetos en lugar de tablas, este provee su propio lenguaje de consulta
"orientada a objetos" llamada *DQL* [^dql]. Es similar a SQL en el sentido de
que permite escribir y ejecutar consultas en la base de datos pero el resultado
del la consulta es un arreglo de objetos en lugar de un arreglo de columnas de
tabla.

[^dql]: DQL significa Doctrine Query Language.

### Base de Datos NoSQL

En contraste con los sistemas de base de datos relacionales un sistema de base
de datos NoSQL, como su nombre lo sugiere, usa no solo métodos SQL para acceder
a los datos. Esto significa que cada sistema NoSQL puede proveer sus propios
métodos y API para acceder y manipular datos. Técnicamente, las bases de datos
NoSQL se pueden dividir en los siguientes grupos:

  * **Base de datos documental**. Una base de datos de documentos funciona
    con el concepto *documentos y sus campos*. Por ejemplo, esto es útil si
    tenemos un árbol de documentos jerárquico en un sistema de gestión de
    contenidos (CMS). Los documentos se asocian en la base de datos por medio
    de una llave única que representa al documento. Otra característica que
    define a una base de datos documental, más allá de la llave de
    búsqueda que se usa para recuperar el documento, es que la base de datos ofrece
    una API o lenguaje de consulta que permite recuperar el documento basado
    en su contenido.

  * **Base de datos en columna**. Frecuentemente se usan para indexar en la web.
    Una sistema de gestión de base de datos orientada a columnas guarda los datos
    de una tabla en secciones de una columna y no en filas. En contraste
    con los principales sistemas de gestión de base de datos que guardan los
    datos en filas. Los SGBD en columna ofrecen ventajas para los sistemas
    de almacén de datos (data warehouses), customer relationship manager (CRM),
    catálogos de biblioteca y otras sistemas de información ad hoc donde el
    total se calcula en base a un gran número de datos similares.

  * **Base de datos llave-valor**. Este es el más simple almacenamiento de datos y usa una llave única
    para acceder a un dato determinado. Tales sistemas de base de datos proveen
    para el mecanismo de busque un simple par: llave-valor.

  * Entre otros.

I> Doctrine provee soporte solo para el subconjunto de Bases de Datos Documentales
I> de entre los sistemas de base de datos NoSQL. Los sistemas de almacenamiento
I> en columnas y el llave-valor tienen un campo específico de aplicación y no
I> son cubiertas por Doctrine.

#### Bases de Datos Documentales

Doctrine soporta algunas bases de datos documentales NoSQL: [MongoDB](https://www.mongodb.org/),
[CouchDB](http://couchdb.apache.org/), [OrientDB](http://www.orientechnologies.com/orientdb/) y
[PHPCR](http://phpcr.github.io/).

Por ejemplo en un sitio web para un blog tendríamos un documento llamado `post`
y un documento llamado `comment`. El documento `post` tendría los campos `id`,
`title`, `content`, `author` y `date_created`; y el documento `comment` tendría
los campos `id`, `author`, `content` y `date_created`. Esto es muy similar a las
tablas que tendríamos in una base de datos relacional.

I> En este libro no discutiremos sobre la API que provee Doctrine para las bases
I> de datos NoSQL. Si queremos aprender sobre estas características debemos
I> revisar las secciones correspondientes de la documentación de Doctrine.

## La Arquitectura de Doctrine

El [Proyecto Doctrine](http://www.doctrine-project.org/) está constituido por
varias librerías (componentes). Cada componente de Doctrine se distribuye como
un paquete que se puede instalar usando Composer y está registrado en el catalogo
[Packagist.org](https://packagist.org/). Se instala de la misma manera en que
se instalan los componentes de Laminas Framework.

Aquí ofreceremos una breve descripción de la arquitectura de la biblioteca Doctrine
para dar una idea general de sus capacidades.

### Componentes para las Bases de Datos Relacionales

Los componentes principales de Doctrine diseñados para trabajar con bases de datos
relacionales se muestran en la figura D.2 marcados con color verde. Los bloques
azules representan el motor PHP y las extensiones PHP. Doctrine esta construido
sobre estos dos elementos.

![Figura D.2. Componentes de Doctrine diseñados para trabajar con base de datos relacionales](../en/images/doctrine_intro/doctrine_orm_architecture.png)

Como podemos ver en la figura Doctrine se basa en las características del motor
de PHP y en las extensiones de PHP que son en realidad usados como controladores
para cada sistema de gestión de base de datos. Abajo en la capa base están los
componentes que constituyen el núcleo de Doctrine (como `Annotations`, `Common`,
etc.) que proveen las funcionalidades esenciales para los otros componentes de
más alto nivel. El componente `DBAL` provee una capa de abstracción para los
diferentes tipos de base de datos. Y arriba de todo esto está el componente `ORM`
que provee la API para trabajar con los datos de una manera orientada a objetos.
Los componentes `DoctrineModule` y `DoctrineORMModule` se diseñan para la
integración con Laminas Framework.

I> El componente ORM de Doctrine usa el patrón llamado [Data Mapper](http://en.wikipedia.org/wiki/Data_mapper_pattern).
I> Este patrón dice que una tabla de base de datos se puede representar como una
I> clase entidad de PHP. La base de datos en este patrón se considera como un
I> tipo de repositorio (deposito de entidades). Cuando recuperamos una entidad
I> del repositorio una sentencia SQL se ejecuta internamente y una instancia
I> de la clase entidad de PHP se construye y sus propiedades se llenan con
I> los datos.

Análogamente a los componentes de Laminas los nombres de los componentes de Doctrine
tiene dos partes: el nombre del proveedor ("Doctrine") y el nombre del componente
(por ejemplo, "Common"). Abajo podemos encontrar una lista de los componentes
de Doctrine junto con el nombre del paquete que se debe usar para instalarlo
usando Composer y una breve descripción:

  * `Doctrine\Common`. Biblioteca Común para los proyectos de Doctrine. Este
    componente contiene funcionalidades usadas comúnmente. Para instalarlo
    mediante composer usamos el nombre del paquete `doctrine/common`.

  * `Doctrine\Annotations`. Analizador Sintáctico de las anotaciones de Docblock.
    Se instala mediante Composer con el paquete `doctrine/annotations`.

  * `Doctrine\Inflector`. Manipulaciones comunes de cadena de caracteres en
    relación con las mayúsculas y minúsculas y reglas para singulares y plurales.
    El paquete instalable con Composer tine como nombre `doctrine/inflector`.

  * `Doctrine\Lexer`. Biblioteca Base para un analizador de léxico que se puede
    usar como un analizador sintáctico descendente recursivo.
    El paquete para instalarlo usando composer es `doctrine/lexer`.

  * `Doctrine\Cache`. La Biblioteca de Cache ofrece una API orientada a objetos
    para muchos backends cache. Se instala mediante composer con el paquete
    `doctrine/cache`.

  * `Doctrine\DBAL`. Capa de Abstracción de Base de Datos. Es una ligera y delgada
    capa en tiempo de ejecución alrededor de una API tipo PDO y un montón de extras,
    características horizontales como introspección del esquema de base de datos
    y manipulación mediante una API orientada a objetos. Se instala con el paquete
    `doctrine/dbal` usando Composer.

  * `Doctrine\Collections`. Biblioteca de Abstracción de Colecciones. Se instala
    con el paquete llamado `doctrine/collections` mediante Composer.

  * `Doctrine\ORM`. Mapeo objeto-relacional para PHP. Este es un componente de
    Doctrine que provee una manera para trabajar con modelos de entidad de una
    manera orientada a objetos en lugar de consultas SQL crudas. El paquete para
    Composer es `doctrine/orm`.

  * `Doctrine\Migrations`. Migraciones para el esquema de base de datos que usa
    Doctrine DBAL. Provee una manera consistente de manejar el esquema de base
    de base de datos y de actualizarlo. Se instala usando Composer con el paquete
    `doctrine/migrations`.

  * `Doctrine\DataFixtures`. Datos de Prueba para todos los Administradores de
    Objetos de Doctrine. Provee un framework para crear datos de prueba para la
    base de datos. Con Composer se instala mediante el paquete `doctrine/data-fixtures`.

Como Doctrine usa el autocargador de PHP y el estándar PSR-4 las clases que
pertenecen a un determinado componente están en el namespace del componente.
Por ejemplo, la clase `EntityManager` pertenece al componente `Doctrine\ORM`
y está en el namespace `Doctrine\ORM`.

### Componentes para las Bases de Datos Documentales NoSQL

Los componentes de Doctrine diseñados para trabajar con bases de datos documentales
NoSQL (MongoDB, CouchDB, etc) se muestran en la figura D.3 y se marcan en verde.
Los bloques azules representan al motor de PHP y a las extensiones de PHP sobre
las que Doctrine está construido.

![Figura D.3. Componentes de Doctrine diseñados para trabajar con bases de datos documentales](../en/images/doctrine_intro/doctrine_odm_architecture.png)

Como podemos ver en la figura D.3 los componentes NoSQL de Doctrine se basan en
las características del motor PHP y en las extensiones de PHP que se pueden
considerar "controladores" para un sistema gestor de base de datos. Arriba de la
capa base están los componentes de nivel medio. El componente `Common` es el
mismo componente que se muestra en la figura D.2 que provee funcionalidades
usadas comúnmente. Los componentes `MongoDB` y CouchDB` provee una API de bajo
nivel para sus correspondientes bases de datos. Los componentes `MongodbODM`,
`CouchdbODM`, `OrientdbODM` y `PhpcrODM` proveen Mapeo objeto-documento (ODM)
para sus correspondientes bases de datos. El concepto ODM es muy similar al ORM
en el sentido de que provee la capacidad de trabajar con bases de datos NoSQL
de una manera orientada a objetos mediate el mapeo de un documento a una clase
entidad de PHP. El componente `DoctrineMongoODMModule` tiene el propósito de
integrar Doctrine con Laminas.

Abajo podemos encontrar una lista de los componentes junto con el nombre del
paquete para instalar con Composer y una breve descripción:

  * `Doctrine\MongoDB` es la Capa de Abstracción de Doctrine para MongoDB.
    El nombre del paquete para instalar con Composer es `doctrine/mongodb`.

  * `Doctrine\MongodbODM` (Mapeo objeto-documento) provee una manera de mapear
    documentos NoSQL a un modelo de entidad PHP. El nombre del paquete para
    Composer es `doctrine/mongodb-odm`.

  * `Doctrine\MongoODMModule` es un módulo de Laminas Framework que provee
    funcionalidades para Doctrine MongoDB ODM. Facilita la integración de Doctrine
    con Laminas. El paquete para Composer se llama `doctrine/doctrine-mongo-odm-module`.

  * `Doctrine\CouchDB` es un componente que provee una API simple que se envuelve
    alrededor de la API HTTP de CouchDB. El paqueta para instalarlo usando
    Composer es `doctrine/couchdb`.

  * `Doctrine\CouchdbODM` es el componente de Mapeo objeto-documento para CouchDB.
    Es análogo a Doctrine ORM en el sentido de que provee una manera de acceder
    a la base de datos de una manera orientada a objetos. Se instala mediante
    Composer con el paquete `doctrine/couchdb-odm`.

  * `Doctrine\OrientdbODM` es un conjunto de bibliotecas PHP para usar OrientDB
    con PHP. El paquete para Composer se llama `doctrine/orientdb-odm`.

  * `Doctrine\PhpcrODM` es componente de mapeo objeto-documento para PHPCR.
    Se instala con el paquete `doctrine/phpcr-odm`.

## Resumen

En este apéndice hemos dado un vista general de la arquitectura y los componentes
de la biblioteca Doctrine. Doctrine es un proyecto grande que consiste en multiples
componentes que están principalmente enfocados en la persistencia de datos.

En el mercado hay dos grandes grupos de sistemas gestores de base de datos:
las tradicionales bases de datos relacionales y las llamadas bases de datos NoSQL.
Aunque,
la mayoría de las base de datos relacionales usan el lenguaje SQL para consultar
y manipular datos, cada sistema de base de datos tiene su características específicas.
Lo mismo se puede ver con las bases de datos NoSQL en donde cada sistema provee
su propio método para acceder a los datos. Doctrine se diseñó para trabajar con
datos de una manera independiente de la base de datos mediante el aprovisionamiento
de sofisticadas capas de abstracción.

El componente de Doctrine más útil, el Mapeo objeto-relación (ORM) se diseñó
para permitir al desarrollador trabajar con datos de una manera orientada a
objetos. Esto significa que en lugar de escribir consultas SQL
cargamos un objeto de la entidad (o un arreglo de objetos de entidades) desde
un repositorio. De esta manera una tabla de base de datos se mapea a una clase
PHP (también llamada entidad) y una fila de la tabla se mapea en una instancia
de aquella clase entity.
