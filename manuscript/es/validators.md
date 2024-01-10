# Revisar los datos de entrada con validadores {#validators}

En este capítulo damos una visión general de los validadores estándares de ZF
que se pueden usar en los formularios, además, mostraremos como escribir un
validador a la medida. Un validador es una clase diseñada para tomar algunos datos,
revisarlos para garantizar su corrección y regresar un booleano indicando si el
dato es correcto y un mensaje de error si el dato tiene algunos errores.

I> Podemos usar validadores *fuera* de los formularios para procesar datos arbitrarios.
I> Por ejemplo, los validadores se puede usar en las acciones del controlador
I> para asegurar que los datos pasado en las variables GET y/o POST son seguras
I> y cumplen con un determinado formato.

Los componentes de Laminas tratados en este capítulo son:

|--------------------------------|---------------------------------------------------------------|
| *Componente*                   | *Descripción*                                                 |
|--------------------------------|---------------------------------------------------------------|
| @`Laminas\Validator`              | Implementa varias clases validadoras.                         |
|--------------------------------|---------------------------------------------------------------|
| @`Laminas\InputFilter`            | Implementa un contenedor para filtros/validadores.            |
|--------------------------------|---------------------------------------------------------------|

## Sobre los validadores

Un *validador* está diseñado para tomar algunos datos de entrada, revisar su
corrección y regresar un booleano que indica si los datos son correctos. Si los
datos son incorrectos el validador genera una lista de errores que describen
el porqué no paso la revisión.

### ValidatorInterface

En Laminas un validador es una clase PHP común que implementa la interfaz
@`ValidatorInterface`[Laminas\Validator\ValidatorInterface]
que pertenece al espacio de nombres @`Laminas\Validator`. La definición de la interfaz
se presenta abajo:

~~~php
<?php
namespace Laminas\Validator;

interface ValidatorInterface
{
  // Returns true if and only if $value meets the validation requirements.
  public function isValid($value);

  // Returns an array of messages that explain why
  // the most recent isValid() call returned false.
  public function getMessages();
}
~~~

Como podemos ver @`ValidatorInterface`[Laminas\Validator\ValidatorInterface] tiene dos métodos: el método `isValid()`
(línea 7) y el método `getMessages()` (línea 11).

El primer método `isValid()` esta concebido para ejecutar la revisión del valor
de entrada (el parámetro `$value`). Si `$value` pasa la validación, el
método `isValid()` regresa el booleano `true`. Si la validación de `$value` falla,
entonces el método regresa `false`.

T> Una clase validadora concreta implementa la interfaz @`ValidatorInterface`[Laminas\Validator\ValidatorInterface] y
I> puede tener métodos adicionales. Por ejemplo, muchas clases validadoras tienen
I> métodos que permiten configurar al validador (colocar las opciones de validación).

## Perspectiva general de los validadores estándares

Los validadores estándares de Laminas son provistos por el componente @`Laminas\Validator`
[^standard_validators]. La herencia de las clases validadoras estándares se
muestran en la figura 9.1. Como podemos ver en la figura la mayoría de las clases
se derivan de la clase base @`AbstractValidator`.

[^standard_validators]: Aquí solo consideramos las clases validadoras estándares
    que pertenecen al espacio de nombres @`Laminas\Validator`. Sin embargo, existen
    más validadores que se pueden considerar estándares. Hablaremos sobre ellos
    en capítulos posteriores.

![Figura 9.1. Herencia de la clase validadora](../en/images/validators/validator_inheritance.png)

Los validadores estándares junto con una breve descripción se muestran en la tabla
9.1. Como podremos notar en la tabla los validadores se pueden dividir a grandes
rasgos en los siguientes grupos:

Standard validators together with their brief description are listed in table 9.1. As you may notice from the
table, they can be roughly divided into several groups:

 * Validadores que revisan la conformidad de un valor a un formato determinado
   (dirección IP, nombre del servidor, dirección de correo electrónico, número de
   tarjeta de crédito, etc).
 * Validadores que revisan si un valor numérico está en un rango determinado
   (menos que, mayor que, entre, etc).
 * Validadores que funcionan como «procuradores» (proxies) de otros validadores
   (@`ValidatorChain`[Laminas\Validator\ValidatorChain], @`StaticValidator`
   y @`Callback`[Laminas\Validator\Callback]).

{title="Tabla 9.1. Validadores estándares"}
|--------------------------------|---------------------------------------------------------------|
| *Nombre de la clase*           | *Descripción*                                                 |
|--------------------------------|---------------------------------------------------------------|
| @`EmailAddress`                | Regresa el booleano `true` si el valor es una dirección de    |
| [Laminas\Validator\EmailAddress]   | correo electrónico valida, de lo contrario regresa `false`.  |
|--------------------------------|---------------------------------------------------------------|
| @`Hostname`[Laminas\Validator\Hostname]| Revisa si el valor es un nombre de servidor valido.      |
|--------------------------------|---------------------------------------------------------------|
| @`Barcode`[Laminas\Validator\Barcode]                     | Regresa el booleano `true` si y solo si el valor contiene un  |
|                                | código de barras valido.                                      |
|--------------------------------|---------------------------------------------------------------|
| @`CreditCard`                  | Regresa `true` si y solo si el valor sigue el formato común de|
|                                | las tarjetas de crédito (algoritmo Luhn, suma de comprobación mod-10).|
|--------------------------------|---------------------------------------------------------------|
| @`Iban`                        | Regresa `true` si el valor es un Código Internacional de Cuenta|
|                                | Bancaria, en ingles IBAN, de lo contrario regresa `false`.    |
|--------------------------------|---------------------------------------------------------------|
| @`Isbn`                        | Regresa el booleano `true` si y solo si el valor es un Número |
|                                | Internacional Normalizado del Libro, en ingles ISBN.          |
|--------------------------------|---------------------------------------------------------------|
| @`Ip`                          | Regresa `true` si el valor es una dirección IP valida, de lo  |
|                                | contrario regresa `false`.                                    |
|--------------------------------|---------------------------------------------------------------|
| @`Uri`[Laminas\Validator\Uri]                         | Regresa `true` si y solo si el valor es un Identificador de   |
|            | Recursos Uniforme, en ingles URI.                             |
|--------------------------------|---------------------------------------------------------------|
| @`Between`[Laminas\Validator\Between]                     | Regresa `true` si el valor está en un determinado rango, de lo|
|        | contrario regresa `false`.                                    |
|--------------------------------|---------------------------------------------------------------|
| @`LessThan`                    | Regresa el booleano `true` si el valor es menor que determinado|
|                                | número, de lo contrario regresa `false`.                      |
|--------------------------------|---------------------------------------------------------------|
| @`GreaterThan`                 | Regresa `true` si y solo si el valor es mayor que determinado |
|                                | número.                                                       |
|--------------------------------|---------------------------------------------------------------|
| @`Identical`                   | Regresa el booleano `true` si el valor es igual a un valor dado.|
|--------------------------------|---------------------------------------------------------------|
| @`Step`                        | Revisa si el valor es un escalar y un valor de paso valido.   |
|--------------------------------|---------------------------------------------------------------|
| @`Csrf`[Laminas\Validator\Csrf]                        | Este validador revisa si la seña (token) provista coincide con la |
|           | generada y guardada en la sesión de PHP anteriormente.        |
|--------------------------------|---------------------------------------------------------------|
| @`Date`[Laminas\Validator\Date]                        | Regresa `true` si el valor es una fecha valida en un          |
|           | determinado formato.                                          |
|--------------------------------|---------------------------------------------------------------|
| @`DateStep`                    | Regresa un booleano `true` si la fecha está dentro de una paso valido. |
|--------------------------------|---------------------------------------------------------------|
| @`InArray`                     | Regresa `true` si un valor está en una arreglo dado, de otra  |
|                                | manera regresa `false`.                                       |
|--------------------------------|---------------------------------------------------------------|
| @`Digits`[Laminas\Validator\Digits]                      | Regresa el booleano `true` si y solo si ``$value` contiene solo  |
|         | dígitos.                                                      |
|--------------------------------|---------------------------------------------------------------|
| @`Hex`                         | Regresa `true` si y solo si el valor contiene solo dígitos    |
|                                | hexadecimales.                                                |
|--------------------------------|---------------------------------------------------------------|
| @`IsInstanceOf`                | Regresa `true` si el valor es una instancia de determinada    |
|                                | clase, de lo contrario regresa `false`.                       |
|--------------------------------|---------------------------------------------------------------|
| @`NotEmpty`                    | Regresa `true` si el valor no es vacío.                       |
|--------------------------------|---------------------------------------------------------------|
| @`Regex`[Laminas\Validator\Regex]                       | Regresa `true` si el valor coincide con un patrón dado, de lo |
|          | contrario regresa `false`.                                    |
|--------------------------------|---------------------------------------------------------------|
| @`StringLength`                | Regresa `true` si la longitud de una cadena de caracteres está|
|                                | entre un rango.                                               |
|--------------------------------|---------------------------------------------------------------|
| @`Explode`                     | Separa el valor dado en partes y regresa `true` si todas las  |
|                                | partes pasan la revisión indicada.                            |
|--------------------------------|---------------------------------------------------------------|
| @`StaticValidator`             | Este validador permite ejecutar otro validador sin            |
|                                | instanciarlo explícitamente.                                  |
|--------------------------------|---------------------------------------------------------------|
| @`Callback`[Laminas\Validator\Callback]                    | Este validador permite ejecutar un algoritmo de validación    |
|       | personalizado a través de un función de retro llamada provista|
|                                | por el usuario.                                               |
|--------------------------------|---------------------------------------------------------------|
| @`ValidatorChain`[Laminas\Validator\ValidatorChain]              | Validador de envoltura que permite organizar varios           |
| | validadores en una cadena. Los validadores añadidos se        |
|                                | ejecutan en el orden en que fueron agregados a la cadena (FIFO).|
|--------------------------------|---------------------------------------------------------------|

## Validator Behaviour in Case of Invalid or Unacceptable Data

Si pasamos a un validador algún dato que no pasa la revisión, el validador crea
internamente una lista de mensajes de error que se pueden recuperar con el
método `getMessages()`. Por ejemplo, veamos abajo los posibles errores que el
validador @`EmailAdrress`[Laminas\Validator\EmailAddress] regresa si le pasamos
el valor «abc@ewr» (el carácter
barra invertida, «\», indica una nueva línea donde el código no se ajusta a la
página):

~~~text
array(3) {
  ["emailAddressInvalidHostname"] =>
    string(51) "'ewr' is not a valid hostname for the email address"
  ["hostnameInvalidHostname"] =>
    string(66) "The input does not match the expected structure for a DNS hostname"
  ["hostnameLocalNameNotAllowed"] =>
    string(84) "The input appears to be a local network name but local network names are not allowed"
}
~~~

El método validador `getMessages()` regresará un arreglo de mensajes que explican
el porqué la validación fallo. Las llaves del arreglo son identificadores de mensajes
de fallas y los valores del arreglo se corresponden con mensajes en forma de cadenas
de caracteres legibles por humanos.

Si el método `isValid()` nunca se llamo o si la llamada más reciente al método
`isValid()` regresó `true`, el método `getMessages()` regresa un arreglo vacío.
Además, cuando llamamos varias veces al método `isValid()` los mensajes de validación
anteriores se borran así que veremos solo los errores de validación de la última
llamada.

Algunos validadores pueden trabajar solamente con datos de entrada en determinado
formato (por ejemplo, un validador puede necesitar que los datos de entrada sean
cadenas de caracteres y no un arreglo). Si pasamos datos en un formato inaceptable
el validador puede lanzar una excepción @`Laminas\Validator\Exception\RuntimeException`
o producir una advertencia de PHP

I> Se recomienda revisar la documentación del validador que vamos a usar para
I> asegurarnos de su comportamiento en caso de datos inaceptables.

## Instanciar un validador

En Laminas Framework existen varios métodos para crear un validador:

 * Instanciarlo manualmente (con el operador `new`).
 * Crearlo con una clase fábrica (pasándole un arreglo de configuración).
   Esta manera es usada con más frecuencia cuando agregamos reglas de validación
   en un formulario.
 * Instanciarlo implícitamente con la clase de envoltura (wrapper) @`StaticValidator`.

Luego, hablaremos de estos métodos con más detalles.

### Método 1. Instanciación Manual del Validador

Un validador en general se puede usar no solo con formularios sino también para
validar datos arbitrarios. Para hacerlo simplemente creamos una instancia de la
clase validadora, configuramos el validador usando los métodos que provee y
llamando al método `isValid()` del validador.

Por ejemplo, vamos a considerar el uso del validador @`EmailAddress` que revisa si
una dirección de correo electrónico cumple el estándar [RFC-2822](https://tools.ietf.org/html/rfc2822).
Un correo electrónico generalmente consiste en una parte local (nombre de usuario)
seguida por el carácter «arroba» (@), que es seguido por el nombre del servidor.
Por ejemplo, en la dirección de correo electrónico «name@example.com», «name» es
la parte local y «example.com» es el nombre del servidor.

I> El validador @`EmailAddress` es útil para revisar la dirección de correo electrónico
I> ingresada por el usuario en el formulario. El validador revisará la corrección
I> de la parte local y el nombre del servidor, la presencia del carácter «arroba»
I> (@) y opcionalmente se conectará con el servidor receptor y preguntará al servicio
I> DNS por la existencia del registro MX (Registro de Intercambio de Correo) [^mx_record].

[^mx_record]: Un registro MX es un tipo de registro usado en el Sistema de Nombres
              de Dominio (DNS). El registro MX define uno o varios servidores de
              correo asignados al dominio receptor.

Los métodos provisto por el validador @`EmailAddress` se listan en la tabla 9.2:

{title="Tabla 9.2. Métodos públicos del validador EmailAddress"}
|--------------------------------|---------------------------------------------------------------|
| *Nombre del método*            | *Descripción*                                                 |
|--------------------------------|---------------------------------------------------------------|
| `__construct($options)`        | Construye el validador. Acepta una lista de opciones que      |
|                                | permiten configurarlo.                                        |
|--------------------------------|---------------------------------------------------------------|
| `isValid($value)`              | Regresa `true` si el valor es una dirección de correo         |
|                                | electrónico valido de acuerdo con RFC-2822, de lo contrario   |
|                                | regresa `false`.                                              |
|--------------------------------|---------------------------------------------------------------|
| `getMessages()`                | Si la validación falla, este método regresará un arreglo de   |
|                                | mensajes de error.                                            |
|--------------------------------|---------------------------------------------------------------|
| `useDomainCheck($domain)`      | Le dice al validador que revise la corrección de la parte del |
|                                | nombre de servidor.                                           |
|--------------------------------|---------------------------------------------------------------|
| `getDomainCheck()`             | Regresa `true` si la revisión de la parte del nombre del      |
|                                | servidor está activada.                                       |
|--------------------------------|---------------------------------------------------------------|
| `setHostnameValidator($hostnameValidator)` | Añade el validador que se usa para revisar la     |
|                                            | parte del nombre del servidor de la dirección de  |
|                                            | correo electrónico.                               |
|--------------------------------|---------------------------------------------------------------|
| `getHostnameValidator()`       | Regresa el validador usado para revisar la parte nombre del   |
|                                | servidor de la dirección de correo electrónico.               |
|--------------------------------|---------------------------------------------------------------|
| `setAllow($allow)`             | Asigna los tipos permitidos de nombres de servidor que se usan|
|                                | en una dirección de correo electrónico.                       |
|--------------------------------|---------------------------------------------------------------|
| `getAllow()`                   | Regresa los tipos permitidos para el nombre de servidor.      |
|--------------------------------|---------------------------------------------------------------|
| `useMxCheck($mx)`              | Señala si ejecutar la revisión de la validez del registro MX  |
|                                | a través del servicio DNS.                                    |
|--------------------------------|---------------------------------------------------------------|
| `getMxCheck($mx)`              | Regresa `true` si la revisión MX esta activada.               |
|--------------------------------|---------------------------------------------------------------|
| `useDeepMxCheck($deep)`        | Obliga a usar la validación profunda del registro MX.         |
|--------------------------------|---------------------------------------------------------------|
| `getDeepMxCheck()`             | Regresa `true` si la revisión MX profunda está activada, de lo|
|                                | contrario regresa `falso`.                                    |
|--------------------------------|---------------------------------------------------------------|
| `isMxSupported()`              | Regresa `true` si la revisión MX mediante la función de PHP   |
|                                | `getmxrr()` está soportada por el sistema, de lo contrario    |
|                                | regresa `false`.                                              |
|--------------------------------|---------------------------------------------------------------|
| `getMXRecord()`                | Luego de la validación, regresa la información encontrada del |
|                                | registro MX.                                                  |
|--------------------------------|---------------------------------------------------------------|

Como podemos ver en la tabla de arriba, el validador @`EmailAddress`, además de los
métodos `isValid()` y `getMessages()`, provee el método constructor al que se puede
opcionalmente pasar la lista completa de opciones para inicializar el validador.

I> Todos los validadores estándares tienen un método constructor, que opcionalmente
I> acepta un arreglo de opciones para configurar el validador cuando se instancia
I> manualmente.

Además, la clase @`EmailAddress` provee un número de métodos que se pueden usar para
asignar opciones específicas al validador.

El método `useDomainCheck()` dice si revisar o no el nombre del servidor.
Por defecto, esta revisión está habilitada. El método `setAllow()` otorga la capacidad
de especificar que tipos de nombres de servidor se permiten. Podemos pasar una
combinación de constantes separadas por el operador OR. La mayoría de las constantes
poseen el prefijo `ALLOW_` [^allow_constants] y son las siguientes:

  * `ALLOW_DNS`  permite nombres de dominio (este es el valor por defecto).
  * `IP_ADDRESS` permite direcciones IP.
  * `ALLOW_LOCAL` permite nombres de red locales.
  * `ALLOW_ALL`  permite todas las anteriores.

[^allow_constants]: Las constantes con el prefijo `ALLOW_` las provee el validador
                    @`Hostname`[Laminas\Validator\Hostname].

I> Internamente, el validador @`EmailAddress` usa el validador @`Hostname`[Laminas\Validator\Hostname] para revisar
I> la parte del nombre del servidor de la dirección de correo electrónico. Opcionalmente,
I> podemos añadir un validador personalizado para el nombre del servidor con el
I> uso del método `setHostnameValidator()`, sin embargo no es común hacerlo.

El método `useMxCheck()` dice si el validador debe conectarse al servidor receptor
y consultar al servicio DNS el o los registros MX. Si el servidor no tiene
registros MX la validación falla. Además, podemos usar el método `useDeepMxCheck()`
para indicarle al validador que compare la dirección de correo electrónico
extraída del registro MX contra la lista negra con los nombres de dominios reservados
y ejecutar revisiones adicionales por cada dirección detectada.

T> No es recomendable ejecutar una revisión MX (o una revisión MX a fondo) porque
T> este proceso puede tomar mucho tiempo e incrementar el tiempo de carga de la
T> página web. Por defecto, estas revisiones están desactivadas.

Abajo, damos un código de ejemplo que muestra dos métodos equivalentes para crear
manualmente una instancia del validador `EmailAddress`, colocando las opciones y
revisando los valores de entrada:

**Ejemplo 1. Pasar las opciones al método constructor.**

~~~php
<?php
// Optionally, define a short alias for the validator class name.
use Laminas\Validator\EmailAddress;
use Laminas\Validator\Hostname;

// Create an instance of the validator, passing options to the constructor.
$validator = new EmailAddress([
		'allow' => Hostname::ALLOW_DNS|Hostname::ALLOW_IP|Hostname::ALLOW_LOCAL,
		'mxCheck' => true,
		'deepMxCheck' => true
	]);

// Validate an E-mail address.
$isValid = $validator->isValid('name@example.com'); // Returns true.
$isValid2 = $validator->isValid('abc'); // Returns false.

if(!$isValid2) {
  // Get error messages in case of validation failure.
  $errors = $validator->getMessages();
}
~~~

En el código de arriba creamos el objeto del validador @`EmailAddress` con la ayuda
del operador `new` (líne 7). Pasamos un arreglo de opciones al constructor. Usamos
la llave `allow` para permitir que la dirección de correo electrónico sea un nombre
de dominio, una dirección IP o una dirección de la red local. Además, activamos
`mxCheck` y `deepMxCheck` para, respectivamente, habilitar la revisión del registro
MX y ejecutar una revisión a fondo del registro MX.

En la línea 14, llamamos al método `isValid()` y le pasamos el valor «name@example.com»
para su revisión. La salida esperada para esta llamada es el booleano `true`.

En la línea 15, pasamos el valor «abc» al validador. Se espera que el proceso de
validación falle (se retorna `false`). Luego, los mensajes de error se recuperan
con el método `getMessages()` (línea 19).

**Ejemplo 2. Sin pasar opciones a el constructor.**

~~~php
<?php
// Optionally, define a short alias for the validator class name.
use Laminas\Validator\EmailAddress;
use Laminas\Validator\Hostname;

// Create an instance of the validator.
$validator = new EmailAddress();

// Optionally, configure the validator
$validator->setAllow(
       Hostname::ALLOW_DNS|Hostname::ALLOW_IP|Hostname::ALLOW_LOCAL);
$validator->useMxCheck(true);
$validator->useDeepMxCheck(true);

// Validate an E-mail address.
$isValid = $validator->isValid('name@example.com'); // Returns true.
$isValid2 = $validator->isValid('abc'); // Returns false.

if(!$isValid2) {
  // Get error messages in case of validation failure.
  $errors = $validator->getMessages();
}
~~~

En el código de arriba, creamos el objeto validador @`EmailAddress` con la ayuda
del operador `new` (línea 7).

En las líneas 10-13, configuramos el validador. Llamamos al método `setAllow()`
para permitir que la dirección de correo electrónico sea un nombre de dominio,
una dirección IP o una dirección de red local. Además, usamos los métodos
`useMxCheck()` y `useDeepMxCheck()` para, respectivamente, habilitar la revisión
del registro MX y la revisión a profundidad del registro MX.

En la línea 16, llamamos al método `isValid()` y le pasamos la cadena de caracteres
con el valor «name@example.com» para que sea revisada. La salida esperada de esta
llamada es el booleano `true`.

En la línea 17, pasamos una cadena de caracteres con el valor «abc» al validador.
Se espera que el proceso de validación falle. Luego, los mensajes de error son
recuperados con el método `getMessages()` (línea 21).

### Método 2. Usar la envoltura StaticValidator

Una forma alternativa a la instanciación manual del validador es el uso de la clase
@`StaticValidator`. La clase @`StaticValidator` es un tipo de «procurador» (proxy)
diseñado para la instanciación, configuración y ejecución automática del validador.
Por ejemplo, vamos a considerar como crear el mismo validador @`EmailAddress`,
configurarlo y llamar a su método `isValid()`:

~~~php
<?php
// Create and execute the EmailAddress validator through StaticValidator proxy.
$validatedValue = \Laminas\Validator\StaticValidator::execute('name@example.com',
                    'EmailAddress',
                    [
                      'allow' =>
                         Hostname::ALLOW_DNS|
                         Hostname::ALLOW_IP|
                         Hostname::ALLOW_LOCAL,
                      'mxCheck' => true,
                      'deepMxCheck' => true
                    ]);

// The expected output is boolean true.
~~~

La clase @`StaticValidator` provee el método estático `execute()` que toma tres
argumentos: el valor de entrada, el nombre del filtro a aplicar y el arreglo de
opciones especificas del filtro.

En la línea 3 llamamos al método `execute()` para crear automática el validador
@`EmailAddress`, llamar a sus métodos `setAllowDns()`, `useMxCheck()` y `useDeepMxCheck()`
y pasar los valores de entrada a su método `isValid()`. Esto es muy útil porque
todo esto puede ser hecho en una sola llamada.

I> La clase @`StaticValidator` no provee la capacidad de extraer la lista de errores
I> de validación en una forma que pueda ser legible por humanos. Sin embargo,
I> como la clase @`StaticValidator` está diseñada para usarse fuera de los formularios
I> y no tiene el propósito de mostrar los resultados a un humano, esto no parece
I> ser una gran desventaja.

### Método 3. Usar un arreglo de configuración

Cuando usamos validadores con reglas de validación de formularios, generalmente
no construimos un objeto validador explícitamente como hicimos en la sección
anterior, en su lugar pasamos un arreglo de configuración a la clase fábrica
que automáticamente construye el validador y opcionalmente lo configura.
Ya vimos como esto funciona cuando agregamos reglas de validación para el formulario
de contacto en [Colectar las Entradas del Usuario con Forms](#forms)

Por ejemplo, vamos a mostrar como construir el mismo filtro @`EmailAddress` con
la ayuda de una fábrica:

~~~php
<?php
// It is assumed that you call the following code inside of the form model's
// addInputFilter() method.

$inputFilter->add([
  // ...
  'validators'  => [
    [
      'name' => 'EmailAddress',
      'options' => [
        'allow' => \Laminas\Validator\Hostname::ALLOW_DNS,
        'useMxCheck' => false,
        'useDeepMxCheck' => false,
      ],
    ],
  ],
  // ...
]);
~~~

En el código de arriba llamamos al método `add()` provisto por la clase contenedor
@`InputFilter`[Laminas\InputFilter\InputFilter] (línea 5). El método `add()` toma un arreglo que tiene la llave
`validators`. Generalmente registramos los validadores dentro de esta llave (línea 7).
Los validadores registrados dentro de esta llave se insertan dentro de la cadena
validadora in el orden en que aparecen en la lista.

La configuración de un validador generalmente consiste en una llave `name` (línea 9)
y otra `options` (línea 10). La primera, `name`, es el nombre completo de la clase
validadora (por ejemplo, @`\Laminas\Validator\EmailAddress`) o su alias (@`EmailAddress`).
La llave `options` es un arreglo que consiste en opciones especifica para cada validador.
Cuando la clase fábrica instancia el validador, ella pasa la lista de opciones
al constructor del validador y el constructor inicializa el validador según sea
necesario.

## El administrador de complementos para validadores

Cuando creamos un validador desde un fábrica podemos usar el nombre completo de la
clase validadora o su alias. Los alias para los validadores estándares se definen
en la clase @`ValidatorPluginManager`.

I> La clase @`ValidatorPluginManager` define los alias de los validadores.

El alias de un validador estándar es generalmente el mismo que el nombre de la
clase. Por ejemplo, la clase @`Laminas\Validator\EmailAddress` tiene el alias
@`EmailAddress`.

El administrador de complementos para validadores es usado internamente por la
clase contenedora @`InputFilter`[Laminas\InputFilter\InputFilter] para la
instanciación de los validadores estándar.

## Ejemplos de uso de validadores

Ahora consideraremos el uso de los más importantes validadores estándares. Describiremos
los métodos y las opciones que tiene el validador y daremos un ejemplo que muestra
como instanciar y aplicar el validador a los datos de entrada.

### Validadores para revisar la conformidad de un valor a un formato determinado

En esta sección, consideraremos ejemplos de uso de los validadores del grupo
de validadores diseñados para revisar si los valores de entrada se ajustan a un
formato determinado.

#### Validador Ip

La clase de validación @`Ip` está diseñada para revisar si el valor de entrada es una
dirección IP valida. Si el valor de entrada es una dirección IPv4 [^ipv4_address],
IPv6 [^ipv6_address], IPvFuture [^ipvfuture_address] o una IPv6 literal [^ipv6literal_address];
el validador regresara un booleano `true`, de lo contrario regresa `false`.
En caso de fallo los mensajes de error se pueden extraer con el método `getMessages()`
del validador.

[^ipv4_address]: Una dirección de Protocolo de Internet versión 4 (IPv4) consiste
                 en cuatro octetos separados por puntos, como «182.168.56.101».

[^ipv6_address]: Una dirección de Protocolo de Internet versión 6 (IPv6) consiste
                 en ocho grupos de cuatro dígitos hexadecimales separados por
                 dos puntos, como «2001:0db8:85a3:0000:0000:8a2e:0370:7334».

[^ipvfuture_address]: IPvFuture está definida sin rigor en la sección 3.2.2 del RFC 3986.

[^ipv6literal_address]: Una dirección IPv6 literal es una modificación de una dirección
                        IPv6 para que pueda ser usada dentro de una URL. (El problema
                        con la dirección IPv6 original es que los caracteres «:»
                        y «.» son delimitadores en las URLs)

Los métodos públicos provistos por el validador @`Ip` se listan en la tabla 9.3:

{title="Tabla 9.3. Métodos públicos del validador IP"}
|--------------------------------|---------------------------------------------------------------|
| *Nombre del método*            | *Descripción*                                                 |
|--------------------------------|---------------------------------------------------------------|
| `__construct($options)`        | Construye el validador. Acepta una lista de opciones.         |
|--------------------------------|---------------------------------------------------------------|
| `isValid($value)`              | Regresa `true` si y solo si el valor es una dirección IP valida. |
|--------------------------------|---------------------------------------------------------------|
| `getMessages()`                | Si la validación falla, este método regresará un arreglo de   |
|                                | mensajes de error.                                            |
|--------------------------------|---------------------------------------------------------------|
| `setOptions($options)`         | Indica las opciones del validador.                            |
|--------------------------------|---------------------------------------------------------------|

El método `setOptions()` permite indicar los tipos de dirección IP permitidos:

  * `allowipv4` para permitir direcciones IPv4.
  * `allowipv6` para permitir direcciones IPv6.
  * `allowipvfuture` para permitir direcciones IPvFuture.
  * `allowliteral` para permitir direcciones IPv6 literal.

Por defecto todas las direcciones que se muestran arriba son permitidas con la
excepción de la dirección IPv6 literal.

Abajo tenemos un código de ejemplo que demuestra el uso del validador @`Ip`.

~~~php
<?php
use Laminas\Validator\Ip;

// Create Ip validator.
$validator = new Ip();

// Configure the validator.
$validator->setOptions([
    'allowipv4'      => true,  // Allow IPv4 addresses.
    'allowipv6'      => true,  // Allow IPv6 addresses.
    'allowipvfuture' => false, // Allow IPvFuture addresses.
    'allowliteral'   => true,  // Allow IP addresses in literal format.
  ]);

// Check if input value is a valid IP address (IPv4).
$isValid = $validator->isValid('192.168.56.101'); // Returns true

// Check if input value is a valid IP address (IPv6).
$isValid2 = $validator->isValid(
       '2001:0db8:85a3:0000:0000:8a2e:0370:7334'); // Returns true

// Pass an invalid string (not containing an IP address).
$isValid3 = $validator->isValid('abc'); // Returns false
~~~

#### Validador Hostname

El validador @`Hostname`[Laminas\Validator\Hostname] está diseñado para revisar si el valor dado es un nombre
de servidor que pertenece al conjunto de tipos de nombres de servidor permitidos.
Los tipos son:

* Un nombre de servidor DNS (ejemplo, «example.com»);
* Una dirección IP (ejemplo, «192.168.56.101»);
* Un nombre de servidor local (ejemplo, «localhost»).

Los métodos públicos provistos por el validador se listan en la tabla 9.4:

{title="Tabla 9.4. Métodos públicos del validador Hostname"}
|--------------------------------|---------------------------------------------------------------|
| *Nombre del método*            | *Descripción*                                                 |
|--------------------------------|---------------------------------------------------------------|
| `__construct($options)`        | Construye el validador. Acepta una lista de opciones.         |
|--------------------------------|---------------------------------------------------------------|
| `isValid($value)`              | Regresa `true` cuando el valor es un nombre de servidor valido|
|                                | , de lo contrario regresa `false`.                            |
|--------------------------------|---------------------------------------------------------------|
| `getMessages()`                | Si la validación falla, el método regresará un arreglo de     |
|                                | mensajes de error.                                            |
|--------------------------------|---------------------------------------------------------------|
| `setIpValidator($ipValidator)` | Opcionalmente, permite colocar nuestro propio validador de    |
|                                | dirección IP.                                                 |
|--------------------------------|---------------------------------------------------------------|
| `getIpValidator()`             | Recupera el validador de dirección IP añadido.                |
|--------------------------------|---------------------------------------------------------------|
| `setAllow()`                   | Define el o los tipos de nombres de servidor permitidos.      |
|--------------------------------|---------------------------------------------------------------|
| `getAllow()`                   | Regresa los tipos de nombre de servidor permitidos.           |
|--------------------------------|---------------------------------------------------------------|
| `useIdnCheck()`                | Define si la revisión del Nombre de Dominio Internacionalizado|
|                                | (IDN) está habilitado. La opción por defecto es `true`.       |
|--------------------------------|---------------------------------------------------------------|
| `getIdnCheck()`                | Regresa `true` si la revisión IDN está habilitada.            |
|--------------------------------|---------------------------------------------------------------|
| `useTldCheck()`                | Define si la revisión de Dominio de Nivel Superior (TLD) está |
|                                | habilitada. Esta opción por defecto es `true`.                |
|--------------------------------|---------------------------------------------------------------|
| `getTldCheck()`                | Regresa `true` si la revisión TLD está activada.              |
|--------------------------------|---------------------------------------------------------------|

Podemos indicar los nombres de los tipos de servidor permitidos con el método `setAllow()`.
Este método acepta la combinación de las siguientes constantes:

  * `ALLOW_DNS` permite nombres de dominio de Internet (ejemplo, *example.com*).
  * `ALLOW_IP`  permite direcciones IP.
  * `ALLOW_LOCAL` permite nombres locales de red (ejemplo, *localhost*, *www.localdomain*);
  * `ALLOW_URI` permite nombre URI de servidor.
  * `ALLOW_ALL` permite todos los tipos de nombre de servidor.

Por defecto, solo se permiten los nombre de dominio de Internet.

La revisión del nombre de servidor consiste de varias etapas, algunas se pueden
omitir dependiendo de las opciones del validador:

1. Si el valor de entrada se parece a una dirección IP, esta se revisa con el validador
   de direcciones IP interno. Podemos sobrescribir el validador de direcciones IP
   con el método `setIpValidator()`.

2. El nombre del servidor se separa dejando la parte del dominio (con el separador
   «.»).

3. El dominio de primer nivel se revisa contra la lista blanca con los TLDs permitidos.
   (Podemos desactivar esta revisión con el método `useTldCheck()`).

4. La parte de dominio se revisa en base a las reglas de nombres de dominio aceptables.
   Si un nombre de dominio es un IDN [^idn] se revisa contra las reglas para
   validar IDNs. (Podemos desactivar la revisión IDN con el método `useIdnCheck()`).

[^idn]: Un Nombre de Dominio Internacionalizado (IDN) es un nombre de dominio de
        Internet que contiene al menos una letra en un alfabeto específico como
        en el Árabe, Chino o Ruso.

Abajo, mostramos un código de ejemplo que demuestra el uso del validador @`Hostname`[Laminas\Validator\Hostname].

~~~php
<?php
use Laminas\Validator\Hostname;

// Create the Hostname validator.
$validator = new Hostname();

// Configure the validator.
$validator->setAllow(Hostname::ALLOW_DNS|Hostname::ALLOW_IP);

// Check a host name.
$isValid = $validator->isValid('site1.example.com');
// Returns true.
$isValid2 = $validator->isValid('abc');
// Returns false (not a valid host name).
~~~

#### El validador Uri

El validador @`Uri`[Laminas\Validator\Uri] está diseñado para revisar si el valor de entrada es un
Identificador de Recursos Uniforme (URI) [^uri]. En caso de falla los mensajes
de error se pueden recuperar con el método del validador `getMessages()`.

I> No nos confundamos con el termino URI. En la mayoría de los casos podemos
I> pensar a la URI como un URL usual.

[^uri]: Un Identificador de Recursos Uniforme (URI) es una secuencia compacta
        de caracteres que identifica un recurso abstracto o físico. Un Localizador
        Uniforme de recursos (URL) es un tipo de URI. Pero, no todas las URIs son
        URLs.

Los métodos públicos que provee el validador @`Uri`[Laminas\Validator\Uri] se listan en la tabla 9.5:

{title="Table 9.5. Public methods of the Uri validator"}
|--------------------------------|---------------------------------------------------------------|
| *Nombre del método*            | *Descripción*                                                 |
|--------------------------------|---------------------------------------------------------------|
| `__construct($options)`        | Construye el validador. Acepta una lista de opciones.         |
|--------------------------------|---------------------------------------------------------------|
| `isValid($value)`              | Regresa `true` cuando el valor es una URI valida, de lo       |
|                                | regresa `false`.                                              |
|--------------------------------|---------------------------------------------------------------|
| `getMessages()`                | Si la validación falla, este método regresa un arreglo de     |
|                                | mensajes de error.                                            |
|--------------------------------|---------------------------------------------------------------|
| `setUriHandler($uriHandler)`   | Asigna el gestos de objetos URI para este validador.          |
|--------------------------------|---------------------------------------------------------------|
| `getUriHandler()`              | Recupera el gestos de objetos URI.                            |
|--------------------------------|---------------------------------------------------------------|
| `setAllowAbsolute($allowAbsolute)`| Indica al validador si se aceptan las URI absolutas.       |
|--------------------------------|---------------------------------------------------------------|
| `getAllowAbsolute()`           | Regresa `true` si se aceptan las URI absolutas.               |
|--------------------------------|---------------------------------------------------------------|
| `setAllowRelative($allowRelative)`| Le dice al validador si se aceptan las URI relativas.      |
|--------------------------------|---------------------------------------------------------------|
| `getAllowRelative()`           | Regresa `true` si se aceptan las URIs relativas.              |
|--------------------------------|---------------------------------------------------------------|

Internamente, el validador @`Uri`[Laminas\Validator\Uri] usa el *gestor de objetos URI* (URI handler object)
que es responsable de analizar la cadena de caracteres de la URI. Por defecto, la
clase @`Laminas\Uri\Uri` se usa como gestor de URI. (Si queremos, podemos colocar nuestro
gestor de URI personalizado con el método `setUriHandler()`).

Una URI puede ser absoluta o relativa. Por ejemplo, una URI absoluta es
«http://example.com/blog/2014/02/02/edit», mientras que una relativa es
«2014/02/02/edit». Podemos especificar si el validador considera aceptables a las
URIs relativas, a las absolutas o ambas. Para esto, usamos los métodos
`setAllowAbsolute()` and `setAllowRelative()`, respectivamente. Por defecto, ambas
son tratadas como tipos de URI aceptables.

Abajo, un código de ejemplo muestra el uso del validador @`Uri`[Laminas\Validator\Uri].

~~~php
<?php
use Laminas\Validator\Uri;

// Create the Uri validator.
$validator = new Uri();

// Configure the validator.
$validator->setAllowAbsolute(true);
$validator->setAllowRelative(true);

// Check an URI.
$isValid = $validator->isValid('http://site1.example.com/application/index/index');
// Returns true.
$isValid2 = $validator->isValid('index/index');
// Returns true.
~~~

#### El validador Date

El validador @`Date`[Laminas\Validator\Date] está pensado para revisar si el dato de entrada es una fecha
en un formato dado. En caso de falla, los mensajes de error se pueden extraer con
el método del validador `getMessages()`.

Los métodos públicos que provee el validador @`Date`[Laminas\Validator\Date] se listan en la tabla 9.6:

{title="Tabla 9.6. Métodos públicos del validador Date"}
|--------------------------------|---------------------------------------------------------------|
| *Nombre del método*            | *Descripción*                                                 |
|--------------------------------|---------------------------------------------------------------|
| `__construct($options)`        | Construye el validador. Acepta una lista de opciones.         |
|--------------------------------|---------------------------------------------------------------|
| `isValid($value)`              | Regresa `true` cuando el valor es una cadena de caracteres    |
|                                | que contiene una fecha en un formato esperado; de lo contrario|
|                                | regresa `false`.                                              |
|--------------------------------|---------------------------------------------------------------|
| `getMessages()`                | Si la validación falla el método regresa un arreglo de        |
|                                | mensajes de error.                                            |
|--------------------------------|---------------------------------------------------------------|
| `setFormat($format)`           | Coloca el formato de fecha aceptable.                         |
|--------------------------------|---------------------------------------------------------------|
| `getFormat()`                  | Regresa el formato esperado.                                  |
|--------------------------------|---------------------------------------------------------------|

Para colocar el formato de la fecha que se espera podemos usar el método `setFormat()`.

I> Internamente, el filtro @`DateTimeFormatter` usa la clase `DateTime` de la biblioteca
I> estándar de PHP para convertir y formatear las fechas. Los formatos de fecha
I> disponibles los podemos conseguir en la documentación de PHP de la clase `DateTime`.

Abajo, un código de ejemplo demuestra el uso del validador @`Date`[Laminas\Validator\Date].

~~~php
<?php
use Laminas\Validator\Date;

// Create validator instance.
$validator = new Date();

// Configure validator.
$validator->setFormat('Y-m-d');

// Check if the input value is a date having expected format.
$isValid = $validator->isValid('2014-04-04'); // Returns true.
$isValid2 = $validator->isValid('April 04, 2014'); // Returns false (format is unexpected).
~~~

#### El validador Regex

Este validador nos permite validar si una cadena de caracteres dada cumple alguna
expresión regular. Regresa `true` si la cadena de caracteres coincide con la expresión
regular, de lo contrario regresa `false`. En caso de fallo, los mensajes de error
se pueden extraer con el método `getMessages()` del validador.

Los métodos públicos que provee el validador @`Regex`[Laminas\Validator\Regex] se listan en la tabla 9.7:

{title="Tabla 9.7. Métodos públicos del validador Regex"}
|--------------------------------|---------------------------------------------------------------|
| *Nombre del método*            | *Descripción*                                                 |
|--------------------------------|---------------------------------------------------------------|
| `__construct($options)`        | Construye el validador. Acepta una lista de opciones.         |
|--------------------------------|---------------------------------------------------------------|
| `isValid($value)`              | Regresa `true` si y solo si `$value` coincide con el patrón   |
|                                | que resulta de la expresión regular dada.                     |
|--------------------------------|---------------------------------------------------------------|
| `getMessages()`                | Si la validación falla el método regresará un arreglo de      |
|                                | mensajes de error.                                            |
|--------------------------------|---------------------------------------------------------------|
| `setPattern($pattern)`         | Asigna el patrón para expresión regular.                      |
|--------------------------------|---------------------------------------------------------------|
| `getPattern()`                 | Recupera el patrón de la expresión regular.                   |
|--------------------------------|---------------------------------------------------------------|

El método `setPattern()` permite colocar la expresión regular sobre la que se hace
la comparación.

T> Para conocer la sintaxis y ver ejemplos de las expresiones regulares recomendamos
T> revisar la sección *Patrones PCRE* de la documentación de PHP.

Abajo, con un código de ejemplo mostramos el uso del validador @`Regex`[Laminas\Validator\Regex]. En el ejemplo
usamos una expresión regular para revisar si la cadena de caracteres de entrada es
una dirección IPv4 valida (una dirección consiste típicamente consiste en cuatro
grupos de dígitos separados por puntos).

~~~php
<?php
use Laminas\Validator\Regex;

// Create Regex validator.
$validator = new Regex();

// Set regular expression to check for an IP address.
$validator->setPattern('\b\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\b');

// Check for regular expression match.
$isValid = $validator->isValid("127.0.0.1"); // returns true.
$isValid2 = $validator->isValid("123"); // returns false.
~~~

### Validadores para revisar si un valor numérico está en un rango dado

En esta sección consideraremos ejemplos de uso de validadores del grupo de validadores
diseñados para revisar si los datos de entrada están en un rango dado.

#### Validador NotEmpty

El validador @`NotEmpty` permite revisar si el dato de entrada no está vacío.
Este validador es útil cuando se trabaja con elementos de formulario u otras
entradas de usuario, en donde podemos usarlo para asegurar que los elementos
obligatorios tienen valores asociados.

Los métodos públicos que provee el validador @`NotEmpty` se listan en la tabla 9.8:

{title="Tabla 9.8. Métodos públicos del validador NotEmpty"}
|--------------------------------|---------------------------------------------------------------|
| *Nombre del método*            | *Descripción*                                                 |
|--------------------------------|---------------------------------------------------------------|
| `__construct($options)`        | Construye el validador. Acepta una lista de opciones.         |
|--------------------------------|---------------------------------------------------------------|
| `isValid($value)`              | Regresa `true` si y solo si `$value` no es un valor vacío.    |
|--------------------------------|---------------------------------------------------------------|
| `getMessages()`                | Si la validación falla, el método regresará un arreglo de     |
|                                | mensajes de error.                                            |
|--------------------------------|---------------------------------------------------------------|
| `setType($type)`               | Coloca los tipos de valor que se consideran valores vacíos.   |
|--------------------------------|---------------------------------------------------------------|
| `getType()`                    | Regresa los tipos.                                            |
|--------------------------------|---------------------------------------------------------------|
| `getDefaultType()`             | Regresa los tipos por defectos.                               |
|--------------------------------|---------------------------------------------------------------|

El método `setType()` específica que tipos de variables se consideran valores
vacíos. Este método acepta un solo argumento `$type` que puede ser o una combinación
OR de las constantes listadas en la tabla 9.9, o un arreglo que contenga los equivalentes
literales de estas constantes.

{title="Tabla 9.9. Tipos de Constantes"}
|----------------------|--------------------|----------------------|----------------------------------------|
| *Constante*          | *Valor numérico*   | *Equivalente literal*| *Descripción*                          |
|----------------------|--------------------|----------------------|----------------------------------------|
| `BOOLEAN`            | 1                  | «boolean»            | Considera al booleano `false` como un valor vacío.|
|----------------------|--------------------|----------------------|----------------------------------------|
| `INTEGER`            | 2                  | «integer«            | Considera al entero 0 como un valor vacío.|
|----------------------|--------------------|----------------------|----------------------------------------|
| `FLOAT`              | 4                  | «float»              | Considera al flotante 0.0 como un valor vacío.|
|----------------------|--------------------|----------------------|----------------------------------------|
| `STRING`             | 8                  | «string»             | Considera la cadena de caracteres vacía|
|                      |                    |                      | '' como un valor vacío.                |
|----------------------|--------------------|----------------------|----------------------------------------|
| `ZERO`               | 16                 | «zero»               | Considera a la cadena de caracteres que|
|                      |                    |                      | contiene solamente el carácter cero    |
|                      |                    |                      | ('0') como un valor vacío.             |
|----------------------|--------------------|----------------------|----------------------------------------|
| `EMPTY_ARRAY`        | 32                 | «array»              | Considera a un arreglo vacío como un valor vacío.|
|----------------------|--------------------|----------------------|----------------------------------------|
| `NULL`               | 64                 | «null»               | Considera a `null` como un valor vacío.|
|----------------------|--------------------|----------------------|----------------------------------------|
| `PHP`                | 127                | «php»                | Considera vacío al valor si la función |
|                      |                    |                      | de PHP `empty()` regresa `true` sobre  |
|                      |                    |                      | el valor.                              |
|----------------------|--------------------|----------------------|----------------------------------------|
| `SPACE`              | 128                | «space»              | Considera una cadena de caracteres como|
|                      |                    |                      | un valor vacío si contiene solo espacios|
|                      |                    |                      | en blanco.                             |
|----------------------|--------------------|----------------------|----------------------------------------|
| `OBJECT`             | 256                | «object»             | Regresa `true`. Cuando un objeto no es |
|                      |                    |                      | permitido pero es dado se regresa `false`.|
|----------------------|--------------------|----------------------|----------------------------------------|
| `OBJECT_STRING`      | 512                | «objectstring»       | Regresa `false` cuando se da un objeto |
|                      |                    |                      | y su método `__toString()` regresa una |
|                      |                    |                      | cadena de caracteres vacía.            |
|----------------------|--------------------|----------------------|----------------------------------------|
| `OBJECT_COUNT`       | 1024               | «objectcount»        | Regresa `false` si el objeto dado tiene|
|                      |                    |                      | una interfaz `Countable` y su cuenta   |
|                      |                    |                      | es 0.                                  |
|----------------------|--------------------|----------------------|----------------------------------------|
| `ALL`                | 2047               | «all»                | Considera vacío a todos los valores anteriores.|
|----------------------|--------------------|----------------------|----------------------------------------|

Abajo, mostramos un código de ejemplo que demuestra el uso del validador @`NotEmpty`.

~~~php
<?php
use Laminas\Validator\NotEmpty;

// Create validator instance.
$validator = new NotEmpty();

// Configure validator.
$validator->setType(NotEmpty::ALL);

// Check if input value not empty.
$isValid1 = $validator->isValid('some string'); // returns true
$isValid2 = $validator->isValid(''); // returns false
$isValid3 = $validator->isValid(0); // returns false
~~~

#### Validador Between

El validador @`Between`[Laminas\Validator\Between] revisa si un número está en un determinado rango (min, max),
incluyéndolos (por defecto) o excluyéndolos.

Los métodos públicos que provee el validador @@`Between`[Laminas\Validator\Between] se listan en la tabla 9.10:

{title="Tabla 9.10. Métodos públicos del validador Between"}
|--------------------------------|---------------------------------------------------------------|
| *Nombre del método*            | *Descripción*                                                 |
|--------------------------------|---------------------------------------------------------------|
| `__construct($options)`        | Construye el validador. Acepta una lista de opciones.         |
|--------------------------------|---------------------------------------------------------------|
| `isValid($value)`              | Regresa `true` si y solo si el valor está en el rango dado.   |
|--------------------------------|---------------------------------------------------------------|
| `getMessages()`                | Si el validador falla, el método regresará un arreglo de      |
|                                | mensajes de error.                                            |
|--------------------------------|---------------------------------------------------------------|
| `setMin($min)`                 | Asigna el límite mínimo.                                      |
|--------------------------------|---------------------------------------------------------------|
| `getMin()`                     | Recupera el límite mínimo.                                    |
|--------------------------------|---------------------------------------------------------------|
| `setMax($max)`                 | Coloca el límite máximo.                                      |
|--------------------------------|---------------------------------------------------------------|
| `getMax()`                     | Recupera el limite máximo.                                    |
|--------------------------------|---------------------------------------------------------------|
| `setInclusive($inclusive)`     | Indica si la comparación se hace incluyendo los limites.      |
|--------------------------------|---------------------------------------------------------------|
| `getInclusive()`               | Regresa la opción inclusiva.                                  |
|--------------------------------|---------------------------------------------------------------|

El rango se puede colocar con los métodos `setMin()` y `setMax()`.

Por defecto el validador ejecuta una comparación inclusiva (para revisar si el valor
pertenece al rango dado, se compara si el valor es mayor o igual a su límite
inferior y si el valor es menor o igual a su límite superior). Se puede cambiar
este comportamiento con el método `setInclusive()`. Este le dice al validador
si ejecuta una comparación inclusiva (pasando `true` como argumento) o una
comparación exclusiva (pasando `false` como argumento).

Abajo, se muestra un ejemplo del uso del validador `Between`.

~~~php
<?php
use Laminas\Validator\Between;

// Create validator instance.
$validator = new Between();

// Configure validator.
$validator->setMin(1);
$validator->setMax(10);
$validator->setInclusive(true);

$isValid1 = $validator->isValid(5); // returns true.
$isValid2 = $validator->isValid(10); // returns true.
$isValid3 = $validator->isValid(0); // returns false (value is too small).
$isValid4 = $validator->isValid(15); // returns false (value is too big).
~~~

#### Validador InArray

El validador @`InArray` revisa si el valor de entrada pertenece a un arreglo dado.
Los métodos públicos provistos por el validador @`InArray` se listan en la tabla 9.11:

{title="Tabla 9.11. Métodos públicos del validador InArray"}
|--------------------------------|---------------------------------------------------------------|
| *Nombre del método*            | *Descripción*                                                 |
|--------------------------------|---------------------------------------------------------------|
| `__construct($options)`        | Construye el validador. Acepta una lista de opciones.         |
|--------------------------------|---------------------------------------------------------------|
| `isValid($value)`              | Regresa `true` si y solo si el valor pertenece a un arreglo dado.|
|--------------------------------|---------------------------------------------------------------|
| `getMessages()`                | Si la validación falla, el método regresará un arreglo de     |
|                                | mensajes de error.                                            |
|--------------------------------|---------------------------------------------------------------|
| `setHaystack($haystack)`       | Coloca el arreglo en el que buscar.                           |
|--------------------------------|---------------------------------------------------------------|
| `getHaystack()`                | Regresa el arreglo de los valores permitidos.                 |
|--------------------------------|---------------------------------------------------------------|
| `setStrict($strict)`           | Coloca el modo de comparación estricto.                       |
|--------------------------------|---------------------------------------------------------------|
| `getStrict()`                  | Pregunta si el modo de comparación estricta está activado.    |
|--------------------------------|---------------------------------------------------------------|
| `setRecursive($recursive)`     | Le dice al validador que busque recursivamente.               |
|--------------------------------|---------------------------------------------------------------|
| `getRecursive()`               | Pregunta si la búsqueda recursiva está activada.              |
|--------------------------------|---------------------------------------------------------------|

El método `setHaystack()` permite colocar el arreglo de valores permitidos.
El método `isValid()` buscará en el arreglo la presencia de `$value`.

Si el arreglo contiene valores anidados y queremos buscar en él recursivamente,
entonces usamos el método `setRecursive()`. Este método toma una bandera booleana.
Si la bandera es `true`, entonces la búsqueda sera ejecutada recursivamente, de
lo contrario los niveles anidados serán ignorados.

El método `setStrict()` provee la capacidad de decirle al validador como comparar
el valor de entrada y los valores en el arreglo. Este puede ser una combinación
de las siguientes constantes:

  * `COMPARE_NOT_STRICT` no ejecutar una revisión estricta del tipo de variable.
  * `COMPARE_NOT_STRICT_AND_PREVENT_STR_TO_INT_VULNERABILITY` no ejecutar una revisión
    estricta del tipo de variable, pero previene un falso positivo producto
    de la comparación de una cadena de caracteres con un entero (por ejemplo, `"asdf" == 0`).
    Esta es la opción por defecto.
  * `COMPARE_STRICT` comparar tanto el tipo de variable como su valor.

Abajo, demostramos con un ejemplo el uso del validador @`InArray`.

~~~php
<?php
use Laminas\Validator\InArray;

// Create validator instance.
$validator = new InArray();

// Configure validator.
$validator->setHaystack([1, 3, 5]);

// Perform validation.
$isValid1 = $validator->isValid(1); // returns true.
$isValid2 = $validator->isValid(2); // returns false.
~~~

#### Validador StringLength

El validador @`StringLength` revisa si la longitud de la cadena de caracteres de
entrada pertenece a un rango dado, incluyendo los extremos. Regresa `true` si
y solo si la longitud de la cadena de caracteres tiene el valor `min` y no es
mayor que la opción `max` (cuando la opción `max` no es nula).

Los métodos públicos que provee el validador @`StringLength` se listan en la tabla 9.12:

{title="Tabla 9.12. Métodos públicos del validador StringLength"}
|--------------------------------|---------------------------------------------------------------|
| *Nombre del método*            | *Descripción*                                                 |
|--------------------------------|---------------------------------------------------------------|
| `__construct($options)`        | Construye el validador. Acepta una lista de opciones.         |
|--------------------------------|---------------------------------------------------------------|
| `isValid($value)`              | Regresa `true` si y solo si la longitud del valor está dentro |
|                                | del rango dado.                                               |
|--------------------------------|---------------------------------------------------------------|
| `getMessages()`                | Si la validación falla, el método regresará un arreglo de     |
|                                | mensajes de error.                                            |
|--------------------------------|---------------------------------------------------------------|
| `setMin($min)`                 | Coloca el límite mínimo.                                      |
|--------------------------------|---------------------------------------------------------------|
| `getMin()`                     | Recupera el límite mínimo.                                    |
|--------------------------------|---------------------------------------------------------------|
| `setMax($max)`                 | Coloca el limite máximo.                                      |
|--------------------------------|---------------------------------------------------------------|
| `getMax()`                     | Recupera el límite máximo.                                    |
|--------------------------------|---------------------------------------------------------------|
| `setEncoding($encoding)`       | Coloca la nueva codificación a usar.                          |
|--------------------------------|---------------------------------------------------------------|
| `getEncoding()`                | Recupera la codificación.                                     |
|--------------------------------|---------------------------------------------------------------|

Por defecto, el validador @`StringLength` no considera a ninguna cadena de caracteres
como valida. Usamos el método `setMin()` para indicar el limite inferior y el
método `setMax()` para indicar el limite superior de la longitud de la cadena
de caracteres permitida. Existen tres maneras posibles de hacer esto.

  * Usar solamente el método `setMin()` para permitir cadenas de caracteres con
    un limite inferior mínimo, pero sin limite de longitud superior.
  * Usar solo el método `setMax()` para permitir cadenas de caracteres con una
    longitud mínima cero y una longitud máxima limite.
  * Usar ambos métodos, `setMin()` y `setMax()`, para permitir cadenas de caracteres
    con un longitud entre el límite inferior y el superior.

Por defecto, el motor de PHP usa codificación UTF-8 para las cadenas de caracteres.
Si nuestra cadena de caracteres de entrada usa una codificaciones diferente, debemos
especificarla con el método del validador `setEncoding()`.

Abajo, mostramos un código de ejemplo del uso del validador `StringLength`.

~~~php
<?php
use Laminas\Validator\StringLength;

// Create validator instance.
$validator = new StringLength();

// Configure the validator.
$validator->setMin(1);
$validator->setMax(10);

$isValid1 = $validator->isValid("string"); // returns true.
$isValid2 = $validator->isValid(""); // returns false (value is too short).
$isValid3 = $validator->isValid("a very long string"); // returns false (value is too long).
~~~

### Organizar los validadores en una cadena

Los validadores se pueden organizar en una cadena. Esto es posible con el
uso de la clase @`ValidatorChain`[Laminas\Validator\ValidatorChain]. Cuando un validador compuesto se ejecuta,
el valor de entrada se pasa a cada validador por turno. El método
`isValid()` del validador @`ValidatorChain`[Laminas\Validator\ValidatorChain] regresa `true` si todos los validadores
en la cadena regresan `true`, de lo contrario regresa `false`.

I> La clase @`ValidatorChain`[Laminas\Validator\ValidatorChain] es usada
I> internamente por la clase contenedor @`InputFilter`[Laminas\InputFilter\InputFilter]
I> para almacenar la secuencia de validadores asociados a un campo del modelo del
I> formulario.

Los métodos públicos que provee la clase @`ValidadorChain`[Laminas\Validator\ValidatorChain]
se presentan en la tabla 9.13:

{title="Tabla 9.13. Métodos públicos del validador ValidatorChain"}
|--------------------------------|---------------------------------------------------------------|
| *Nombre del método*            | *Descripción*                                                 |
|--------------------------------|---------------------------------------------------------------|
| `isValid($value)`              | Regresa `true` si todos los validadores en la cadena regresan `true`.|
|--------------------------------|---------------------------------------------------------------|
| `getMessages()`                | Regresa un arreglo con los mensajes de error de la validación.|
|--------------------------------|---------------------------------------------------------------|
| `getValidators()`              | Regresa el arreglo de los validadores encadenados.            |
|--------------------------------|---------------------------------------------------------------|
| `count()`                      | Regresa el total de validadores en la cadena.                 |
|--------------------------------|---------------------------------------------------------------|
| `attach($validator, $breakChainOnFailure)` | Añade un validador al final de la cadena.         |
|--------------------------------|---------------------------------------------------------------|
| `prependValidator($validator, $breakChainOnFailure)` | Añade un validador al principio de la cadena.|
|--------------------------------|---------------------------------------------------------------|
| `attachByName($name, $options, $breakChainOnFailure)` | Usa el gestor de complementos para     |
|                                                       | agregar un valor a partir de su nombre.|
|--------------------------------|---------------------------------------------------------------|
| `prependByName($name, $options, $breakChainOnFailure)` | Usar el gestor de complementos para   |
|                                                        | agregar al principio de la cadena un  |
|                                                        | validador por su nombre.              |
|--------------------------------|---------------------------------------------------------------|
| `merge($validatorChain)`       | Mezcla la cadena de validación con un validador pasado como   |
|                                | parámetro.                                                    |
|--------------------------------|---------------------------------------------------------------|

Un ejemplo de validadores en cadena se muestra en la figura 9.2. Consiste en el
validador @`NotEmpty` seguido por el validador @`StringLength` que a su vez está
seguido por el validador @`Date`[Laminas\Validator\Date]. Cuando esta cadena se ejecuta, primero, el
validador @`NotEmpty` revisa que el valor no sea un valor vacío, luego el validador
@`StringLength` revisa que la longitud de la cadena de caracteres este en el rango
(1, 16); ambos inclusive; y finalmente, el validador @`Date`[Laminas\Validator\Date] revisa que el valor
de entrada es una fecha con formato «YYYY-MM-DD».

![Figura 9.2. Cadena validadora](../en/images/validators/validator_chain.png)

Para construir una cadena de validación como la de la figura 9.2, podemos usar
el siguiente código:

~~~php
<?php
// Instantiate the validator chain.
$validator = new \Laminas\Validator\ValidatorChain();

// Insert validators into validator chain.
$validator->attachByName('NotEmpty');
$validator->attachByName('StringLength', ['min'=>1, 'max'=>16]);
$validator->attachByName('Date', ['format'=>'Y-m-d']);

// Execute all validators in the chain.
$isValid = $validator->isValid('2014-04-04'); // Returns true.
~~~

### Validador personalizado con el validador Callback

El validador @`Callback`[Laminas\Validator\Callback] puede ser un envoltorio para nuestro algoritmo de validación
personalizado. Por ejemplo, puede ser útil cuando un validador no es apropiado
y necesitamos aplicar nuestro propio algoritmo de revisión a los datos. Los métodos
públicos provistos por el validador @`Callback`[Laminas\Validator\Callback] se listan en la tabla 9.14.

{title="Tabla 9.14. Métodos públicos del validador Callback"}
|--------------------------------|---------------------------------------------------------------|
| *Nombre de la clase*           | *Descripción*                                                 |
|--------------------------------|---------------------------------------------------------------|
| `isValid($value, $context)`    | Ejecuta una función de retro llamada como un validador.       |
|--------------------------------|---------------------------------------------------------------|
| `getMessages()`                | Si la validación falla, el método regresará un arreglo de     |
|                                | mensajes de error.                                            |
|--------------------------------|---------------------------------------------------------------|
| `setCallback($callback)`       | Coloca una nueva retro llamada para el filtro.                |
|--------------------------------|---------------------------------------------------------------|
| `getCallback()`                | Regresa la retro llamada asociada al filtro.                  |
|--------------------------------|---------------------------------------------------------------|
| `setCallbackOptions($options)` | Coloca las opciones para la retro llamada.                    |
|--------------------------------|---------------------------------------------------------------|
| `getCallbackOptions()`         | Recupera los parámetros de la retro llamada.                  |
|--------------------------------|---------------------------------------------------------------|

Como se puede ver en la tabla, el validador `Callback` provee los métodos `setCallback()`
y `setCallbackOptions()` que se pueden usar para colocar las funciones de retro
llamada o métodos de una clase (opcionalmente) y pasarles uno o varios valores
parámetros.

#### Ejemplo

Para demostrar el uso del validador @`Callback`[Laminas\Validator\Callback], vamos a agregar un validador al
número de teléfono de nuestra clase de modelo de formulario `ContactForm`.
Este validador revisará el número de teléfono ingresado por el visitante del
sitio.

El validador debe ser capaz de revisar dos tipos de formato de número telefónico
comunes:

  * El formato internacional que se ve: «1 (234) 567-8901»;
  * El formato local, que tiene el siguiente aspecto «567-8901».

Como Laminas no provee un validador estándar que filtre el número de teléfono, usaremos
el validador @`Callback`[Laminas\Validator\Callback] como envoltorio. Para conseguirlo, hacemos los siguientes
cambios al código de nuestra clase `ContactForm`:

~~~php
<?php
// ...
class ContactForm extends Form
{
  // ..
  protected function addElements() {
    // ...

    // Add "phone" field
    $this->add([
        'type'  => 'text',
        'name' => 'phone',
        'attributes' => [
          'id' => 'phone'
        ],
        'options' => [
          'label' => 'Your Phone',
        ],
      ]);
  }

  private function addInputFilter()
  {
    // ...

    $inputFilter->add([
            'name'     => 'phone',
            'required' => true,
            'validators' => [
                [
                  'name' => 'Callback',
                  'options' => [
                     'callback' => [$this, 'validatePhone'],
                     'callbackOptions' => [
                     'format' => 'intl'
                  ]
                ]
              ]
            ]
        );
  }

  // Custom validator for a phone number.
  public function validatePhone($value, $context, $format)
  {
    // Determine the correct length and pattern of the phone number,
    // depending on the format.
    if($format == 'intl') {
      $correctLength = 16;
      $pattern = '/^\d\ (\d{3}\) \d{3}-\d{4}$/';
    } else { // 'local'
      $correctLength = 8;
      $pattern = '/^\d{3}-\d{4}$/';
    }

    // Check phone number length.
    if(strlen($value)!=$correctLength)
      return false;

    // Check if the value matches the pattern.
    $matchCount = preg_match($pattern, $value);

    return ($matchCount!=0)?true:false;
  }
}
~~~

En el código de arriba, creamos el campo `phone` en nuestro `ContactForm`
(Si ya tenemos este campo, podemos ignorar este paso)

Entre las líneas 26-40, agregamos el validador @`Callback`[Laminas\Validator\Callback] a la cadena de validación
de filtros de entrada para el campo «phone».

En las líneas 44-64, tenemos el método de retro llamada `validatePhone()`. Este
método acepta tres argumentos: el parámetro `$value` es el número de teléfono
a validar, la variable `$context` recibe los valores de cada campo del formulario
(puede ser necesario para algunos validadores, revisar los valores de otro campos
del formulario) y el parámetro `$format` es el formato esperado para el número
de teléfono («intl» o «local»).

Dentro del método de retro llamada, hacemos lo siguiente:

 1. Calcular la longitud del número de teléfono, revisar si la longitud es correcta
    para el formato de número de teléfono seleccionado.
 2. Comparar el número de teléfono contra una expresión regular de acuerdo con
    el formato de número de teléfono seleccionado.

## Escribir nuestro propio validador

Una alternativa al uso del validador @`Callback`[Laminas\Validator\Callback] es escribir nuestra propia clase
validadora, implementando la interfaz @`ValidatorInterface`[Laminas\Validator\ValidatorInterface]. Y luego, este validador
se puede usar en los formulario de nuestra aplicación web.

Para demostrar como crear nuestro propio validador, escribiremos la clase
`PhoneValidator`, encapsulando el algoritmo de validación que usamos en el ejemplo
del validador @`Callback`[Laminas\Validator\Callback].

I> Como podemos recordar, la clase concreta base para todos los validadores
I> estándares es la clase @`AbstractValidator`. Por analogía, también derivaremos
I> nuestro validador personalizado `PhoneValidator` de esta clase base.

Planeamos tener los siguientes métodos en nuestra clase validadora `PhoneValidator`
(ver tabla 9.15):

{title="Tabla 9.15. Métodos públicos del validador Callback"}
|--------------------------------|---------------------------------------------------------------|
| *Nombre del método*            | *Descripción*                                                 |
|--------------------------------|---------------------------------------------------------------|
| `__construct($options)`        | Constructor. Acepta un argumento opcional, `$options`, que es |
|                                | necesario para colocar las opciones del validador.            |
|--------------------------------|---------------------------------------------------------------|
| `setFormat($format)`           | Coloca la opción: formato de número de teléfono.              |
|--------------------------------|---------------------------------------------------------------|
| `getFormat()`                  | Regresa la opción del formato de número de telefónico.        |
|--------------------------------|---------------------------------------------------------------|
| `isValid($value)`              | Regresa `true` cuando el valor es un número de teléfono       |
|                                | valido, de lo contrario regresa `false`.                      |
|--------------------------------|---------------------------------------------------------------|
| `getMessages()`                | Si la validación falla, el método regresa un arreglo de       |
|                                | mensajes de error.                                            |
|--------------------------------|---------------------------------------------------------------|

Para la clase `PhoneValidator`, tendremos tres posibles mensajes de error:

  * Si se pasa un valor no escalar al validador, generará el mensaje de error:
    «El número de teléfono debe ser un valor escalar».
  * Si se selecciona el formato de teléfono internacional y el número de teléfono
    ingresado no coincide con el formato, el validador generará el mensaje:
    «El número de teléfono debe estar en formato internacional».
  * Si se selecciona el formato de número de teléfono local y el número de teléfono
    ingresado no coincide con el formato, el validador generará el mensaje:
    «El número de teléfono debe estar en formato local».

Para comenzar, creamos el archivo *PhoneValidator.php* en la carpeta *Validator*
que está dentro de la carpeta fuente del módulo [^phone_validator_service]. Colocamos
el siguiente código dentro del archivo:

[^phone_validator_service]: La clase `PhoneValidator` se puede considerar como
                            un modelo de servicio, porque su objetivo es procesar
                            datos y no guardarlos. Por convención, guardamos el
                            validador personalizado dentro de la carpeta `Validator`.

~~~php
<?php
namespace Application\Validator;

use Laminas\Validator\AbstractValidator;

// This validator class is designed for checking a phone number for
// conformance to the local or to the international format.
class PhoneValidator extends AbstractValidator
{
  // Phone format constants.
  const PHONE_FORMAT_LOCAL = 'local'; // Local phone format.
  const PHONE_FORMAT_INTL  = 'intl';  // International phone format.

  // Available validator options.
  protected $options = [
    'format' => self::PHONE_FORMAT_INTL
  ];

  // Validation failure message IDs.
  const NOT_SCALAR  = 'notScalar';
  const INVALID_FORMAT_INTL  = 'invalidFormatIntl';
  const INVALID_FORMAT_LOCAL = 'invalidFormatLocal';

  // Validation failure messages.
  protected $messageTemplates = [
    self::NOT_SCALAR  => "The phone number must be a scalar value",
    self::INVALID_FORMAT_INTL => "The phone number must be in international format",
    self::INVALID_FORMAT_LOCAL => "The phone number must be in local format",
  ];

  // Constructor.
  public function __construct($options = null)
  {
    // Set filter options (if provided).
    if(is_array($options)) {

      if(isset($options['format']))
        $this->setFormat($options['format']);
      }

      // Call the parent class constructor.
      parent::__construct($options);
  }

  // Sets phone format.
  public function setFormat($format)
  {
    // Check input argument.
    if($format!=self::PHONE_FORMAT_LOCAL &&
       $format!=self::PHONE_FORMAT_INTL) {
      throw new \Exception('Invalid format argument passed.');
    }

    $this->options['format'] = $format;
  }

  // Validates a phone number.
  public function isValid($value)
  {
    if(!is_scalar($value)) {
      $this->error(self::NOT_SCALAR);
      return false; // Phone number must be a scalar.
    }

    // Convert the value to string.
    $value = (string)$value;

    $format = $this->options['format'];

    // Determine the correct length and pattern of the phone number,
    // depending on the format.
    if($format == self::PHONE_FORMAT_INTL) {
      $correctLength = 16;
      $pattern = '/^\d \(\d{3}\) \d{3}-\d{4}$/';
    } else { // self::PHONE_FORMAT_LOCAL
      $correctLength = 8;
      $pattern = '/^\d{3}-\d{4}$/';
    }

    // First check phone number length
    $isValid = false;
    if(strlen($value)==$correctLength) {
      // Check if the value matches the pattern.
      if(preg_match($pattern, $value))
        $isValid = true;
    }

    // If there was an error, set error message.
    if(!$isValid) {
      if($format==self::PHONE_FORMAT_INTL)
        $this->error(self::INVALID_FORMAT_INTL);
      else
        $this->error(self::INVALID_FORMAT_LOCAL);
    }

    // Return validation result.
    return $isValid;
  }
}
~~~

En la línea 2, podemos ver que la clase validadora pertenece al espacio de nombres
`Application\Validator`.

En la línea 8, definimos la clase `PhoneValidator`. Derivamos nuestra clase validadora
de la clase base `AbstractValidator` para reusar la funcionalidad que provee. La
línea 4 contiene el alias para la clase @`AbstractValidator`.

Entre las líneas 11-12, por conveniencia, definimos las constantes para el formato
de número de teléfono (`PHONE_FORMAT_INTL` para el formato internacional y
`PHONE_FORMAT_LOCAL` para el formato local). Estas son equivalentes respectivamente
a las cadenas de caracteres «intl» y «local».

En las líneas 15-17, definimos la variable privada `$options` que es un arreglo
que tiene una sola llave llamada «format». Esta llave contendrá la opción del
formato de número de teléfono para nuestro validador.

En las líneas 20-22, definimos los identificadores de mensajes de error. Tenemos
tres identificadores (`NOT_SCALAR`, `INVALID_FORMAT_INTL` e `INVALID_FORMAT_LOCAL`),
porque nuestro validador puede generar tres diferentes mensajes de error. Estos
identificadores se usan para que la maquina, y no los humanos, distingan los
diferentes mensajes de error.

Entre las líneas 25-29, tenemos la variable de tipo arreglo `$messageTemplates`
que contiene la asociación entre los identificadores de mensajes de error anteriores
y su representación textual. Los mensajes textuales se mostrarán a los humanos.

En las líneas 32-43, tenemos al método constructor que toma a la variable `$options`
como único argumento. Cuando construimos el validador manualmente, podemos omitir
este parámetro. Pero, cuando el validador es construido por la clase fábrica,
la fábrica pasará las opciones al constructor del validador a través de este
argumento.

En las líneas 46-55, tenemos el método `setFormat()` que permite colocar el formato
de teléfono actual.

En las líneas 58-98, tenemos al método `isValid()`. Este método encapsula el algoritmo
que revisa el número de teléfono. Toma a la variable `$value` como parámetro,
ejecuta la comparación con la expresión regular y regresa `true` en caso de éxito.

En caso de falla, el método `isValid()` regresa el booleano `false` y la lista de
errores, esta se pueden recuperar con el método `getMessages()`.

I> Notemos que no se define el método `getMessages()` en nuestra clase `PhoneValidator`.
I> Esto es porque heredamos este método de la clase base @`AbstractValidator`.
I> Dentro de nuestro método `isValid()`, para generar mensajes de error, usamos
I> el método protegido `error()` que provee la clase base (líneas 61, 91, 93).

T> The `PhoneValidator` is only for demonstration of how to write custom validators in Laminas.
T> Implementing a validator that will work correctly against all possible phone numbers in
T> the world is beyond the scope of this book. If you'd like to use this validator in a real-life
T> app, you will definitely need to improve it. For example, take a look at the `libphonenumber`
T> PHP library from Google.

### Usar la clase PhoneValidator

Cuando la clase validadora `PhoneValidator` está lista, podemos fácilmente comenzar
a usarla en el formulario de contacto (o en otro formulario) de la siguiente manera.
Se asume que llamamos el siguiente código dentro del método `ContactForm::addInputFilter()`:

~~~php
$inputFilter->add([
      'name'     => 'phone',
      'required' => true,
      'validators' => [
        [
          [
            'name' => PhoneValidator::class,
            'options' => [
              'format' => PhoneValidator::PHONE_FORMAT_INTL
            ]
          ],
        ],
        // ...
      ],
      // ...
    ]);
~~~

Podemos ver al validador `PhoneValidator` funcionando en la aplicación de ejemplo
*Form Demo* que está disponible junto a este libro. Abrimos la página
«http://localhost/contactus» en nuestro navegador web. Si ingresamos algún número
de teléfono en un formato incorrecto, el validador mostrara un error (ver figura 9.3).

![Figura 9.3. Error de validación de número de teléfono](../en/images/validators/phone_number_validation_error.png)

Si lo deseamos, podemos usar la clase `PhoneValidator` fuera de los formularios
como se muestra en el código de ejemplo más abajo:

~~~php
<?php
use Application\Validator\PhoneValidator;

// Create PhoneValidator validator
$validator = new PhoneValidator();

// Configure the validator.
$validator->setFormat(PhoneValidator::PHONE_FORMAT_INTL);

// Validate a phone number
$isValid = $validator->isValid('1 (234) 567-8901'); // Returns true.
$isValid2 = $validator->isValid('12345678901'); // Returns false.

if(!$isValid2) {
  // Get validation errors.
  $errors = $validator->getMessages();
}
~~~

## Usar filtros y validadores fuera del formulario

En esta sección daremos un ejemplo de como podemos usar filtros y validadores
en nuestro controlador para transformar y revisar los datos extraídos de las
variables GET y POST.

Supongamos que implementamos un sistema de intermediación de pagos y necesitamos
crear una página web que muestre el historial de pago de una tarjeta de crédito
en una determinada fecha. Esta página se puede gestionar por medio una acción
`paymentHistoryAction()` de una clase controladora, el número de tarjeta de crédito
y la fecha se extraen de las variables GET. En el método `paymentHistoryAction()`
necesitamos implementar algunas revisiones de seguridad:

 * Queremos asegurarnos de que el número de la tarjeta de crédito se conforma con
   el estándar ISO/IEC 7812, con un aspecto como el siguiente «4532-7103-4122-1359».
 * Asegurarnos que la fecha tiene el formato 'YYYY-MM-DD'.

Abajo, podemos encontrar el código del método de acción:

~~~php
<?php
namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\Filter\StaticFilter;
use Laminas\Validator\StaticValidator;

class IndexController extends AbstractActionController
{
  // An action which shows the history of a credit
  // card operations on certain date.
  public function paymentHistoryAction()
  {
    // Get parameters from GET.
    $cardNumber = (string)$this->params()->fromQuery('card', '');
    $date = (string)$this->params()->fromQuery('date', date("Y-m-d"));

    // Validate credit card number.
    $isCardNumberValid = StaticValidator::execute($cardNumber, 'CreditCard');
    if(!$isCardNumberValid) {
      throw new \Exception('Not a credit card number.');
    }

    // Convert date to the right format.
    $date = StaticFilter::execute($date, 'DateTimeFormatter',
	                              ['format'=>'Y-m-d']);

    // The rest of action code goes here...

	return new ViewModel();
  }
}
~~~

Dentro del método de acción, usamos el complemento para controladores `params()`
(líneas 16-17) para capturar dos variables del arreglo super-global `$_GET`: la
variable `card` (número de tarjeta de crédito) y la variable `date` (la fecha).

En la línea 20, validamos el número de tarjeta de crédito con la ayuda del validador
`CreditCard`. Si el número de tarjeta no es aceptable, lanzaremos una excepción
indicando un error (línea 22).

En la línea 26, usamos el filtro `DateTimeFormatter` para convertir la fecha al
formato correcto.

## Resumen

Los validadores están diseñados para tomar algunos datos de entrada, revisarlos
y regresar un resultado booleano diciendo si los datos son correctos (y mensajes
de error si los datos tienen errores).

En Laminas Framework, existen varios grupos de validadores estándares:

 * Validadores para revisar la conformidad de un valor a un formato determinado.
 * Validadores para revisar si un valor numérico está dentro de un rango.
 * Validadores que trabajan como «procurador» (proxies) de otros validadores.

En algunos casos, un validador estándar no es útil y necesitamos aplicar nuestro
propio algoritmo de revisión a los datos de entrada. En este caso, podemos usar
el validador @`Callback`[Laminas\Validator\Callback] o escribir nuestra propia clase validadora.
