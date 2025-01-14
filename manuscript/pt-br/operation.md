# Website Operation {#operation}

Neste capítulo, vamos aprender algumas teorias e conceitos sobre como um aplicativo é desenvolvido no Laminas Framework funciona.
Você aprenderá neste capítulo algumas noções básicas de PHP, como classes PHP, namespaces, como definir os parâmetros de
configuração do aplicativo e os estágios presentes no ciclo de vida de uma aplicação. Você também vai se familiarizar
com componentes importantes do Laminas como @`Laminas\EventManager`, @`Laminas\ModuleManager` e @`Laminas\ServiceManager`.
Se não deseja aprender a teoria e quiser ter alguns exemplos práticos, pule este capítulo
e consulte diretamente [Model-View-Controller](#mvc).

Componentes Laminas que serão abordados neste capítulo:




|--------------------------------|---------------------------------------------------------------|
| *Componentes*                  | *Descrição*                                                   |
|--------------------------------|---------------------------------------------------------------|
| @`Laminas\Mvc`                    | Suporte do padrão Model-View-Controller. Separação da lógica  |
|                                | de negócios (Model) da apresentação (View).                   |
|--------------------------------|---------------------------------------------------------------|
| @`Laminas\ModuleManager`          |Este componente é responsável por carregar e inicializar       |
|                                |módulos da aplicação                                           |
|--------------------------------|---------------------------------------------------------------|
| @`Laminas\EventManager`            | Este componente implementa a funcionalidade para acionar eventos e tratamento de eventos. |
|--------------------------------|---------------------------------------------------------------|
| @`Laminas\ServiceManager`          |Implementa o registro de todos os serviços disponíveis na sua aplicação. |
|--------------------------------|---------------------------------------------------------------|

## Classes PHP

O PHP suporta o estilo de programação orientada a objetos (POO). Na POO, o principal meio de construção do seu código é uma *class*.
Uma classe pode ter *propriedades* e *métodos*. Por exemplo, vamos criar um script PHP chamado *Person.php* e
definir uma classe simples chamada `Person` nesse arquivo:

~~~php
<?php

class Person
{
    private $fullName;

    public function __construct()
    {
        // Some initialization code.
        $this->fullName = 'Unknown person';
    }

    public function getFullName()
    {
        return $this->fullName;
    }

    public function setFullName($fullName)
    {
        $this->fullName = $fullName;
    }
}
~~~

I> Você pode notar que no exemplo acima nós temos a tag de abertura `<?php` onde
I> informamos ao mecanismo PHP que o texto após essa tag é um código PHP. No exemplo acima, quando o arquivo contém
I> somente o código PHP (sem misturar tags PHP e HTML), você não precisa inserir o
I> o tag fechamento `?>` quando terminar o código.


A classe Person tem uma propriedade privada `$fullName`  e três métodos:

  * `__construct()` é um método especial chamado *constructor*. Ele é usado se você precisar de alguma forma inicializar a classe com alguma propriedades.

  * `getFullName()` e `setFullName()` são métodos públicos usados ​​para fazer algo com a classe.

Depois de definir a classe, você pode criar *objetos* dessa classe com o operador `new`, da seguinte maneira:

~~~php
<?php

// Instancia a Pessoa
$person = new Person();

// Definir (set) nome completo.
$person->setFullName('John Doe');

// Exibe o nome completo da pessoa para a tela.
echo "Person's full name is: " . $person->getFullName() . "\n";
~~~

I> As classes permitem dividir seu código em blocos menores e torná-los bem organizados.
I> O Laminas consiste em centenas de classes.
I> Você também escreverá suas próprias classes em seus aplicativos da web.

## Namespaces PHP

Quando você usa classes de bibliotecas diferentes (ou mesmo classes de componentes diferentes de uma única biblioteca)
no seu programa, os nomes das classes podem entrar em conflito.
Isso significa que você pode encontrar duas classes com o mesmo nome, resultando em um erro no PHP.
Se você já programou sites com o Laminas Framework 1, talvez se lembre daqueles *extra* longos
nomes de classes como `Laminas_Controller_Abstract`. A ideia com nomes longos era
utilizado para evitar colisões de nomes entre diferentes componentes. Cada componente definido
seu próprio prefixo de nome, como `Laminas_` ou `My_`.

O Laminas Framework usa um recurso do PHP chamado *namespaces*.
Os namespaces permitem resolver colisões de nomes entre componentes de código.

Um namespace é um contêiner para um grupo de nomes. Você pode aninhar namespaces uns nos outros.
Se uma classe não define um namespace, ele vive dentro do namespace *global*
(por exemplo, as classes PHP `Exception` e` DateTime` pertencem ao namespace global).

Veja um exemplo real da definição de namespace (tirada do componente @`Laminas\Mvc`):

~~~php
<?php
namespace Laminas\Mvc;

/**
 * Main application class for invoking applications.
 */
class Application
{
    // ... class members were omitted for simplicity ...
}
~~~


No Laminas Framework, todas as utilizam o namespace *Laminas*.
A linha 2 define o namespace *Mvc*, que é aninhado no namespace *Laminas*,
e todas as classes (incluindo a classe @`Application`[Laminas\Mvc\Application] mostrada
neste exemplo nas linhas 7-10) pertence a este namespace *Laminas*. O nome do namespace é
separado com o caractere de barra invertida ('\\').

Em outras partes do código, você referência à classe @`Application`[Laminas\Mvc\Application] usando
seu nome:

~~~php
<?php
$application = new \Laminas\Mvc\Application();
~~~


I> Por favor, note a barra invertida em @`\Laminas\Mvc\Application`.
Quando você especificar um nome de classe com barras invertidas, que está acessando caminho diretamente da classe.
Também é possível especificar o nome da classe ao namespace atual, nesse caso você não
usar a barra invertida inicial.

Também é possível usar um *alias* (nome abreviado da classe) com o
ajuda da declaração `use` do PHP:

~~~php
<?php
// Define um alias no inicio do arquivo.
use Laminas\Mvc\Application;

// Posteriormente em seu código você pode usar o nome abreviado da classe.
$application = new Application();
~~~


T> Embora o alias permita usar um nome curto em vez do nome completo,
T> seu uso é opcional. Você não é obrigado a usar sempre aliases e pode
T> referenciar a classe pelo seu nome completo.

Cada arquivo PHP da sua aplicação geralmentevai definir  o namespace
(exceto *index.php* script de entrada e arquivos de configuração).
Por exemplo, o módulo principal do seu site, o módulo *Application*,
define seu próprio namespace cujo nome é igual ao nome do módulo:

~~~php
<?php
namespace Application;

class Module
{
    // ... class members were omitted for simplicity ...
}
~~~

## Interfaces PHP

Em PHP, *interfaces* permitem definir qual comportamento uma classe
deve ter, mas sem fornecer a sua implementação.

Isto é também chamado *contract (contrato)* ao implementar uma interface,
uma classe concorda em aceitar o termos do contrato.


No Laminas Framework, as interfaces são amplamente usadas.

Por exemplo, a classe @`Application` implementa o @`ApplicationInterface`,
que define os métodos que cada classe de aplicativo deve fornecer:

~~~php
<?php
namespace Laminas\Mvc;

interface ApplicationInterface
{
    // Retrieves the service manager.
    public function getServiceManager();

    // Retrieves the HTTP request object.
    public function getRequest();

    // Retrieves the HTTP response object.
    public function getResponse();

    // Runs the application.
    public function run();
}
~~~

Como você pode ver no exemplo acima, uma interface é definida usando a palavra `interface`,
quase da mesma maneira que você define uma classe no PHP. Como uma classe normal
a interface define métodos. No entanto, a interface não fornece não implementa nenhum
de seus métodos. Na definição da interface acima  @`ApplicationInterface`, você pode
ver que a implementação desta interface terá o método `getServiceManager()` para
trazer informações do service manager (sobre o service manager, vamos ver logo mais neste capítulo), o
métodos `getRequest()` e `getResponse()` para pegar o pedido HTTP e a resposta, respectivamente,
e o método `run()` para executar o aplicativo.

I>No Laminas Framework, por padrão, as interface devem ser nomeadas com o sufixo `Interface`,
como @`ApplicationInterface`.

Uma classe que implementa uma interface é chamada de classe *concrete (concreta)*.
A classe concreta @`Application` implementa o @`ApplicationInterface`, o que significa
que ele prover a implementação dos métodos definido pela interface:

~~~php
<?php
namespace Laminas\Mvc;

class Application implements ApplicationInterface
{
    // Implement the interface's methods here

    public function getServiceManager()
    {
        // Provide some implementation...
    }

    public function getRequest()
    {
        // Provide some implementation...
    }

    public function getResponse()
    {
        // Provide some implementation...
    }

    public function run()
    {
        // Provide some implementation...
    }
}
~~~

A classe concreta @`Application` usa a palavra `implements` para mostrar que
fornece uma implementação de todos os métodos da interface `ApplicationInterface`.
A classe @`Application` também pode ter métodos adicionais, que não fazem parte
da interface.

Graficamente, as relações de classe são exibidas usando diagramas de herança.
Na imagem 3.1, o diagrama apresentado para o @`Application`. Onde a seta indica
classe pai e classe filho.

![Imagem 3.1. Diagrama de Classes da Aplicação](../en/images/operation/Application.png)

## Autoloading de Classes PHP

Uma aplicação web consiste em muitas classes PHP, e
cada classe normalmente fica em um arquivo separado. Isso introduz
a necessidade de *incluir* os arquivos.

Por exemplo, vamos supor que temos o arquivo chamado *Application.php*
que contém a definição para a classe @`\Laminas\Mvc\Application`. Antes que você possa
criar uma instância da classe @`Application` em algum lugar do seu código,
você tem que incluir o conteúdo do arquivo *Application.php* (você pode fazer isso com o
ajuda do `require_once`, passando o caminho completo para o arquivo):

~~~php
<?php
require_once "/path/to/laminas/laminas-mvc/src/Application.php";

use Laminas\Mvc\Application;

$application = new Application();
~~~

Porém a medida que seu aplicativo for crescendo, pode ser difícil incluir
cada arquivo necessário. O próprio Laminas Framework consiste em centenas de arquivos,
e pode ser muito difícil carregar todas as bibliotecas e toda a sua
dependências dessa maneira. Além disso, ao executar o código, o PHP
levar tempo para processar cada arquivo, mesmo se você não criar um
instância nenhuma classe.

Para corrigir esse problema, no PHP, o recurso de autoloading de classes foi introduzido.
A função `spl_autoload_register()` permite que você registre
uma função *autoloader*. Para sites complexos, você ainda pode criar
várias funções do autoloader, que são encadeadas em pilha.

Durante a execução do script, se o PHP encontrar um nome de classe
que ainda não foi definido, ele chama todas as funções de autoloader registradas,
até que a função autoloader inclua a classe ou gere um erro "não encontrado".
Isso permite o "lazy" loading, que é quando o PHP apenas processa a classe
definição apenas no momento da invocação de classe, quando é realmente necessário.

### Class Map Autoloader

Para ter uma ideia de como funciona uma função de autoloader, abaixo vamos apresentar uma
implementação simplificada de uma função autoloader:

~~~php
<?php
// Autoloader function.
function autoloadFunc($className)
{
    // Class map static array.
    static $classMap = [
        '\\Laminas\\Mvc\\Application' => '/path/to/laminas/laminas-mvc/src/Laminas/Mvc/Application.php',
        '\\Application\\Module' => '/path/to/app/dir/module/Application/Module.php',
        //...
    ];

    // Check if such a class name presents in the class map.
    if(isset(static::$classMap[$className])) {
        $fileName = static::$classMap[$className];

        // Check if file exists and is readable.
        if (is_readable($fileName)) {
            // Include the file.
            require $fileName;
        }
    }
}

// Register our autoloader function.
spl_autoload_register("autoloadFunc");
~~~

No exemplo acima, nós definimos `autoloadFunc()` como uma  função de autoloader,
a qual nos referiremos a ela como *class map* autoloader.

O class map autoloader usa o class map para mapear entre o nome da classe e
caminho absoluto para o arquivo PHP contendo essa classe. O class map é apenas um PHP normal
array contendo chaves e valores. Para determinar o caminho do arquivo por nome de classe,
class map autoloader só precisa buscar o valor do array.
É óbvio que o autoloader do class map funciona muito rápido. No entanto, a desvantagem
é que você tem que manter o class map atualizado e vai ter que você adicionar um novo
classe para o seu programa toda vez que atualizá-lo.

### Padrão PSR-4

Como cada biblioteca tem seu fornecedor e cada uma delas usa suas próprias convenções de
código e organização de arquivos, você terá que registrar uma função autoloader diferente
para cada biblioteca, o que é bastante irritante (e na verdade este é um trabalho desnecessário).
Para resolver esse problema, o padrão PSR-4 foi introduzido.

I> PSR significa PHP Standards Recommendation..


O [padrão PSR-4](http://www.php-fig.org/psr/psr-4/)
define a estrutura de código recomendada que uma aplicação ou biblioteca deve seguir
para garantir a interoperabilidade do autoloader. Em duas palavras, o padrão diz que:

* Os namespaces da classe devem ser organizados da seguinte maneira:

  `\<Nome da biblioteca (Vendor) >\(<Namespace>)*\<Nome da Classe>`

* Os namespaces podem ter quantos níveis forem desejados
  mas o *Vendor* deve ser o primeiro nome.

* Namespaces devem mapear a estrutura de diretórios. Cada separador de namespace ('\\')
  é convertido em uma constante `DIRECTORY_SEPARATOR` para que a OS do sistema carregue o arquivo.

* O nome de classe tem que ter sufixo com *.php * quando os arquivos forem carregados pelo o sistema.

Por exemplo, para a classe @`Laminas\Mvc\Application`,
você terá a seguinte estrutura de pastas:

~~~text
/path/to/laminas/laminas-mvc/src
  /Laminas
    /Mvc
       Application.php
~~~

A desvantagem disso é que você precisa colocar seu código em vários pastas (*Laminas* e *Mvc*).

Para corrigir isso, o PSR-4 permite que você defina que uma
uma série de um ou mais de namespace e sub namespace corresponde a um "diretório base".
Por exemplo, se você tiver o nome completo da classe @`\Laminas\Mvc\Application` e se você definir
a série @`\Laminas\Mvc` corresponde ao diretório "/path/to/laminas/laminas-mvc/src",
você pode organizar seus arquivos da seguinte forma:


```
/path/to/laminas/laminas-mvc/src
    Application.php
```

Para o código em conforme com o padrão PSR-4, podemos escrever e registrar
um autoloader, ao qual nos referiremos como autoloader "padrão":

~~~php
<?php

// "Standard" autoloader function.
function standardAutoloadFunc($className)
{
    // Replace the namespace prefix with base directory.
    $prefix = '\\Laminas\\Mvc';
    $baseDir = '/path/to/laminas/laminas-mvc/src/';
    if (substr($className, 0, strlen($prefix)) == $prefix) {
        $className = substr($className, strlen($prefix)+1);
        $className = $baseDir . $className;
    }

    // Replace namespace separators in class name with directory separators.
    $className = str_replace('\\', DIRECTORY_SEPARATOR, $className);

    // Add the .php extension.
    $fileName = $className . ".php";

    // Check if file exists and is readable.
    if (is_readable($fileName)) {
        // Include the file.
        require $fileName;
    }
}

// Register the autoloader function.
spl_autoload_register("standardAutoloadFunc");
~~~

O padrão do autoloader funciona da seguinte maneira. Assumindo que a classe do namespace
possa ser mapeado para a estrutura de diretórios um por um, a função calcula
o caminho para o arquivo PHP, transformando back-slashes (separadores de namespace) em
barras (separadores de caminho) e concatenando o caminho resultante com
o caminho absoluto para o diretório no qual a biblioteca está localizada. Então o
função verifica se tal arquivo PHP realmente existe, e se assim for, inclui
com a declaração `require`.

É óbvio que o autoloader padrão funciona mais devagar que o class map autoloader.
No entanto, sua vantagem é que você não precisa manter nenhum class map,
o que é muito conveniente quando você desenvolve um novo código e adiciona novas classes
sua aplicação.


I> Laminas Framework está dentro dos pardrões da PSR-4, tornando possível usar
I> mecanismo de carregamento automático em todos os seus componentes. Também é compatível com outras
I> blibiotecas que utilizam o padrão PSR-4 como o Doctrine ou o Symfony.

### Composer-provided Autoloader

O Composer pode gerar funções de autoloader (mesmo padrão de carregament ode classes e padrão PSR-4) para o código que você instala com ele.
O Laminas Framework usa a implementação do autoloader fornecida pelo Composer. Quando você instala um pacote com
Composer, cria automaticamente o arquivo *APP_DIR/vendor/autoload.php*,
que usa a função PHP `spl_autoload_register()` para registrar um autoloader. Desta forma todas as classes PHP
localizado no diretório `APP_DIR/vendor` serão carregados automaticamente.

Para auto-carregar classes PHP do seus próprios módulos (como o módulo `Application`), você terá que especificar
o `autoload` no seu arquivo` composer.json`:

{line-numbers=off,lang="json",title="Autoload key of composer.json file"}
~~~
"autoload": {
    "psr-4": {
        "Application\\": "module/Application/src/"
    }
},
~~~

Então, a única coisa que precisa ser feita é incluir esse arquivo no script de entrada do seu site `index.php`:

```php
// Composer autoloading
include __DIR__ . '/../vendor/autoload.php';
```

T> O arquivo * autoload.php * é gerado toda vez que você instala um pacote com o Composer. Além disso, para
T> faz o Composer gerar o arquivo *autoload.php*, você pode precisar executar o comando `dump-autoload`:
T>
T> `php composer.phar dump-autoload`

### PSR-4 e a Estrutura de origem do módulo

No Laminas Skeleton Application, você pode ver como o padrão PSR-4 é aplicado
na prática. Para o módulo padrão do seu site, o módulo `Application`, classes PHP que
são registrados com o autoloader padrão são armazenados sob o `APP_DIR/module/Application/src`
diretório (abreviação "src" significa "source (fonte)").

I> Vamos nos referir ao diretório `src` como o diretório de origem do módulo.

Por exemplo, vamos dar uma olhada no arquivo `IndexController.php` do módulo `Application` (imagem 3.2).

![Imagem 3.2. Estrutura de pastas do skelleton application conforme o padrão PSR-4](../en/images/operation/psr0_and_dir_structure.png)

Como você pode ver, ele contém a classe `IndexController` [^controller] pertencente ao namespace `Application\Controller`.
Para poder seguir o padrão PSR-4 e usar o autoloader padrão com esta classe PHP,
temos que colocá-lo no diretório `Controller` dentro da pasta do módulo.

[^controller]: A classe `IndexController` é o controller padrão do skelleton application
Vamos falar sobre controllers mais adiante no capítulo [Model-View-Controller](#mvc).

## Solicitações e Resposta HTTP

Quando um usuário do seu site abre página, o navegador envia
uma solicitação e a envia usando o protocolo HTTP para o servidor. O servidor
direciona essa solicitação HTTP para sua aplicação.


I> [HTTP](https://en.wikipedia.org/wiki/Hypertext_Transfer_Protocol) (HTTP é a abreviação de Hyper Text
I> Transfer Protocol) -- um protocolo para transferência de dados
I> na forma de documentos de hyper text (páginas da web). HTTP é na baseado na tecnologia de
I> cliente-servidor: o cliente inicia uma conexão e envia uma solicitação ao servidor da Web, e o
I> servidor aguarda por uma conexão, realiza o necessário
I> ações e retorna  de resposta de volta.

Portanto, o principal objetivo de qualquer aplicação Web é manipular a solicitação HTTP
e produzindo uma resposta HTTP contendo o código HTML da página solicitada.
A resposta é enviada pelo servidor web para o navegador cliente e o navegador exibe
página da web na tela.


Segue abaixo, uma solicitação HTTP típica:

{line-numbers=on,lang="text",title="An HTTP request example"}
~~~
GET http://www.w3schools.com/ HTTP/1.1
Host: www.w3schools.com
Connection: keep-alive
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8
User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64)
Accept-Encoding: gzip,deflate,sdch
Accept-Language: en-US;q=0.8,en;q=0.6
Cookie: __gads=ID=42213729da4df8df:T=1368250765:S=ALNI_MaOAFe3U1T9Syh;
(empty line)
(message body goes here)
~~~

A solicitação HTTP acima consiste em três partes:

* A linha inicial (linha 1 1) especifica o metodo da solicitação (pode ser GET ou POST), e a URL
  exibe a versão do HTTP que é usado.

* Cabeçalhos opcionais (linhas 2-8) caracterizam a mensagem, os parâmetros de transmissão
e fornecem outras meta-informações.7
  No exemplo acima, cada linha representa um único cabeçalho na forma de * name:value *.

* O corpo da mensagem é opcional e contém dados da mensagem. Ele é separado dos cabeçalhos com uma linha em branco

Os cabeçalhos e o corpo da mensagem podem estar ausentes, mas a linha de partida é sempre
presente na solicitação, porque indica seu tipo e URL.

Os cabeçalhos e o corpo da mensagem podem estar ausentes, mas a linha de partida é sempre
presente na solicitação, porque indica seu tipo e URL.


A resposta do servidor para o pedido acima é apresentada abaixo:

{line-numbers=on,lang="text",title="An HTTP response example"}
~~~
HTTP/1.1 200 OK
Cache-Control: private
Content-Type: text/html
Content-Encoding: gzip
Vary: Accept-Encoding
Server: Microsoft-IIS/7.5
Set-Cookie: ASPSESSIONIDQQRBACTR=FOCCINICEFAMEKODNKIBFOJP; path=/
X-Powered-By: ASP.NET
Date: Sun, 04 Aug 2013 13:33:59 GMT
Content-Length: 8434
(empty line)
(page content follows)
~~~

Como você pode ver acima, a resposta HTTP tem quase o mesmo formato da solicitação:

* A linha inicial (linha 1) representa a versão do protocolo HTTP,
  código de status da resposta e mensagem (200 OK).

* Cabeçalhos opcionais (linhas 2-10) fornecem várias informações meta sobre a resposta.

* O corpo da mensagem opcional segue os cabeçalhos e deve ser separado dos cabeçalhos
  por uma linha vazia. O corpo da mensagem normalmente contém o código HTML do solicitado
  página da web.

## Script de Entrada do Site

Quando o servidor da web Apache recebe uma solicitação HTTP do navegador,
ele executa o arquivo *APP_DIR/public/index.php*, também chamado de *script de entrada*.

I> O script de entrada é o único arquivo PHP acessível. O servidor web Apache
I> Direciona todas as solicitações HTTP para este script (lembre-se do arquivo *.htaccess*). Tendo isto
I> script de entrada única torna o site mais seguro (comparando com a situação quando você permite
I> todos para acessar todos os arquivos PHP da sua aplicação).

Embora o arquivo *index.php* seja muito importante, é surpreendentemente pequeno (veja abaixo):

~~~php
<?php

use Laminas\Mvc\Application;
use Laminas\Stdlib\ArrayUtils;

/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(dirname(__DIR__));

// Decline static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server') {
    $path = realpath(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    if (__FILE__ !== $path && is_file($path)) {
        return false;
    }
    unset($path);
}

// Composer autoloading
include __DIR__ . '/../vendor/autoload.php';

if (! class_exists(Application::class)) {
    throw new RuntimeException(
        "Unable to load application.\n"
        . "- Type `composer install` if you are developing locally.\n"
        . "- Type `vagrant ssh -c 'composer install'` if you are using Vagrant.\n"
        . "- Type `docker-compose run zf composer install` if you are using Docker.\n"
    );
}

// Retrieve configuration
$appConfig = require __DIR__ . '/../config/application.config.php';
if (file_exists(__DIR__ . '/../config/development.config.php')) {
    $appConfig = ArrayUtils::merge($appConfig, require __DIR__ . '/../config/development.config.php');
}

// Run the application!
Application::init($appConfig)->run();
~~~

Principalmente, há três coisas feitas nele.

Primeiro, na linha 10, a pasta é alterada  para `APP_DIR`.
Isso simplifica a definição de caminhos de arquivos em sua aplicação.

Em seguida, na linha 22, o autoload da classe é inicializado. Isso permite carregar facilmente qualquer
classe ou localizada qualquer biblioteca do Laminas Framework ou em sua aplicação sem a necessidade
da utilização `require_once`.

E finalmente, na linha 40, uma instância da classe @`Laminas\Mvc\Application` é criada.
O aplicativo é inicializado com as configurações do *application.config.php*
e a sua aplicação é executada.

## Ciclo de vida da Aplicação & Eventos

Como você aprendeu na seção anterior, em cada requisição HTTP, o @`Laminas\Mvc\Application`
objeto é criado. Normalmente, um aplicativo "vive" por um segundo ou menos
(esse tempo é suficiente para gerar a resposta HTTP). A "vida" da aplicação consiste em várias etapas.

I> Laminas Framework usa o conceito de *eventos*. Uma classe pode *desencadear* um evento,
I> e outras classes podem *escutar (listen)* esse eventos. Tecnicamente, desencadear um evento significa
I>  apenas chamar outro método de "callback" de classe. O gerenciamento de eventos é implementado dentro de
I> o componente @`Laminas\EventManager`.

Cada estágio da vida da aplicação é iniciando é pelo aplicativo, disparando um evento
(este evento é representado pela classe @`MvcEvent` que está no namespace @`Laminas\Mvc`). Outras
classes (pertencentes ao Laminas Framework ou específicas da sua aplicação) podem
aos eventos e reagir de acordo.

Abaixo, os cinco principais eventos (etapas) são apresentados:

** Bootstrap **. Quando esse evento é acionado pela sua aplicação, um módulo tem a chance de
registre-se como usar o listener (ouvir) de eventos de aplicativos adicionais em seu `onBootstrap()`
método de retorno de chamada.

**Rota**. Quando esse evento é acionado, a URL da solicitação é analisado usando uma classe *router*
(normalmente, com a classe  @`Laminas\Router\Http\TreeRouteStack`). Se uma correspondência exata entre o URL e uma rota
for encontrado, o pedido é passado para o *controller* específica do local atribuída à rota.

**Despacho (Dispatch)**. A classe do controller "despacha" a solicitação usando o método de ação correspondente
e produz os dados que podem ser exibidos na página.

** Render**. Neste evento, os dados produzidos pela ação do controller são passados ​​para serem renderizados pela
Classe @`Laminas\View\Renderer\PhpRenderer`. A classe do renderizador usa um
*view template* para produzir uma página HTML.

**Termino**. Neste evento, a resposta HTTP é enviada de volta ao cliente.

O fluxo de eventos é ilustrado na imagem 3.3:

![Imagem 3.3. Fluxo de eventos durante o ciclo de vida da aplicação](../en/images/operation/app_life_cycle.png)

T> Embora raramente necessário, alguns exemplos práticos de
T> como ouvir (listen) e reagir a um evento pode ser encontrado no capítulo [Criando um novo Módulo](#modules)

## Configuração da Aplicação

A maioria dos componentes do Laminas Framework que são usados ​​em seu site,
requer configuração (pequenos ajustes). Por exemplo, na configuração
arquivo que você define credenciais de  acesso a conexão com o banco de dados,
ou quando especifica quais módulos estão presentes em seu aplicativo e, fornecem alguns
parâmetros específicos para sua aplicação.

Você pode definir os parâmetros de configuração em dois níveis: no
nível de aplicação, ou no nível do módulo. No nível do aplicativo, você normalmente
definir parâmetros que controlam o aplicativo inteiro e são comuns a todos
módulos de sua aplicação. No nível do módulo, você define parâmetros que
afeta apenas este módulo.

Você pode definir os parâmetros de configuração em dois níveis: no
nível da aplicação, ou no nível do módulo. No nível da aplicativo, você normalmente
definir parâmetros que controlam o aplicativo inteiro e são comuns a todos
módulos de sua aplicação. No nível do módulo, você define parâmetros que
afeta apenas este módulo.

I> Alguns frameworks PHP preferem aplicar o conceito *convenções sobre configuração*, onde
I> A maioria dos seus parâmetros é codificada e pre-configurados e não requer configuração.
I> Isso torna mais rápido desenvolver o aplicativo, mas o torna menos personalizável.
I> No Laminas Framework, o conceito *convenções sobre configuração* é usado,
I> porém você pode personalizar qualquer aspecto de sua aplicação, mas terá que
I> gastar algum tempo para aprender como fazer isso.

### Configuração dos Arquivos (no Nível de Aplicação)

O subdiretório *APP_DIR/config * contém arquivos de configuração de toda a sua aplicação. Vamos ver
mais detalhes (Imagem 3.4).

![Imagem 3.4. Arquivos de Configuração](../en/images/operation/config.png)

O  *APP_DIR/config/application.config.php* é o arquivo de configuração principal.
Ele é usado pela aplicação na na inicialização para determinar quais módulos do aplicativo devem ser carregados
e quais serviços criar por padrão.

Abaixo, o conteúdo do arquivo *application.config.php*
é mostrado. Você pode ver que o arquivo de configuração é apenas um
array associativo em PHP e cada componente tem  uma chave específica nessa matriz.
Você pode fornecer comentários in-line para as chaves do array
para tornar mais fácil para os outros entenderem o significado de cada chave.

T> Por convenção, os nomes das chaves dos arrays devem estar em minúsculas, e se o nome da chave consiste
T> em palavras, as palavras devem ser separadas pelo símbolo de sublinhado ('_').

{line-numbers=on,lang=php, title="Content of application.config.php file"}
~~~
return [
    // Retrieve list of modules used in this application.
    'modules' => require __DIR__ . '/modules.config.php',

    // These are various options for the listeners attached to the ModuleManager
    'module_listener_options' => [
        // This should be an array of paths in which modules reside.
        // If a string key is provided, the listener will consider that a module
        // namespace, the value of that key the specific path to that module's
        // Module class.
        'module_paths' => [
            './module',
            './vendor',
        ],

        // An array of paths from which to glob configuration files after
        // modules are loaded. These effectively override configuration
        // provided by modules themselves. Paths may use GLOB_BRACE notation.
        'config_glob_paths' => [
            realpath(__DIR__) . '/autoload/{{,*.}global,{,*.}local}.php',
        ],

        // Whether or not to enable a configuration cache.
        // If enabled, the merged configuration will be cached and used in
        // subsequent requests.
        'config_cache_enabled' => true,

        // The key used to create the configuration cache file name.
        'config_cache_key' => 'application.config.cache',

        // Whether or not to enable a module class map cache.
        // If enabled, creates a module class map cache which will be used
        // by in future requests, to reduce the autoloading process.
        'module_map_cache_enabled' => true,

        // The key used to create the class map cache file name.
        'module_map_cache_key' => 'application.module.cache',

        // The path in which to cache merged configuration.
        'cache_dir' => 'data/cache/',

        // Whether or not to enable modules dependency checking.
        // Enabled by default, prevents usage of modules that depend on other modules
        // that weren't loaded.
        // 'check_dependencies' => true,
    ],

    // Used to create an own service manager. May contain one or more child arrays.
    //'service_listener_options' => [
    //     [
    //         'service_manager' => $stringServiceManagerName,
    //         'config_key'      => $stringConfigKey,
    //         'interface'       => $stringOptionalInterface,
    //         'method'          => $stringRequiredMethodName,
    //     ],
    // ],

   // Initial configuration with which to seed the ServiceManager.
   // Should be compatible with Laminas\ServiceManager\Config.
   // 'service_manager' => [],
];
~~~

Na linha 3, temos os *módulos* sendo definindo quais módulos serão carregados na inicialização da aplicação.
Você pode ver isso os nomes dos módulos são armazenados dentro de outro arquivo de configuração `modules.config.php`,
que lista todos os módulos presente no seu site.

Na linha 11, temos o `module_paths` que informa ao Laminas sobre
diretórios onde procurar pelo os arquivos de origem pertencentes a módulos. Os módulos da sua aplicação
que você desenvolver estão localizados no diretório *APP_DIR/module* e os que você adquirir atavés do Composer
provavelmente vão estar localizados dentro do diretório *APP_DIR/vendor*.

E na linha 19 nós temos o `config_glob_paths`, que diz ao Laminas onde
procure por arquivos extras de configuração. Você vê esses arquivos de *APP_DIR/config/autoload*
que têm o sufixo *global.php* ou *local.php*, são automaticamente carregados.

Resumindo, você normalmente usa o arquivo *application.config.php* para armazenar as informações
sobre quais módulos devem ser carregados na sua aplicação e onde eles estão localizados e
como eles são carregados (por exemplo, você pode controlar as opções de cache aqui). Nesse
arquivo você também pode ajustar o service manager. Não é recomendado adicionar mais
chaves neste arquivo. Para isso, é melhor usar o arquivo `autoload/global.php`.

E vamos também dar uma olhada dentro do arquivo `modules.config.php`. Atualmente, você tem os seguintes módulos
instalado em seu site:

{line-numbers=off,lang=php, title="Content of modules.config.php file"}
~~~
return [
    'Laminas\Session',
    'Laminas\Mvc\Plugin\Prg',
    'Laminas\Mvc\Plugin\Identity',
    'Laminas\Mvc\Plugin\FlashMessenger',
    'Laminas\Mvc\Plugin\FilePrg',
    'Laminas\Form',
    'Laminas\Router',
    'Laminas\Validator',
    'Application',
];
~~~

O módulo `Application` contém o módulo do arquivos do seu aplicativo.
Todos os outros módulos são listados como componentes do Laminas Framework.

I> No Laminas, existe um um plugin especial do Composer chamado *component installer*. Se você se lembra, no
capítulo [Laminas Skeleton Application](#skeleton), nós respondemos várias perguntas sim/não do instalador, determinando
quais componentes instalar. E o instalador *injetou* os nomes dos módulos desses componentes no `modules.config.php`

###  Arquivos de Configuração Extra (no Nível de Aplicação)

Arquivos de configuração "extra", *APP_DIR/config/autoload/global.php* e *APP_DIR/config/autoload/local.php*
Os arquivos definem parâmetros dependentes de ambiente e dependentes do ambiente em todo o aplicativo, respectivamente.
Esses arquivos de configuração são automaticamente carregados e mesclados recursivamente
com os arquivos de configuração fornecidos pelo módulo, é por isso que seu diretório é chamado *autoload*.

Tendo diferentes arquivos de configuração no diretório *APP_DIR/config/autoload*, você pode estar
confuso sobre quais parâmetros devem ser colocados em cada um. Aqui estão algumas dicas:

* Você usa o arquivo *autoload/global.php* para armazenar parâmetros que não dependem
  no ambiente da máquina. Por exemplo, aqui você pode armazenar parâmetros que
  substituir os parâmetros padrão de algum módulo. Não armazene informações confidenciais
  (como credenciais de banco de dados) aqui, para isso é melhor usar *autoload/local.php*

* Você usa o arquivo *autoload/local.php* para armazenar parâmetros específicos para o
  ambiente desenvolvimento. Por exemplo, aqui você pode armazenar suas credenciais de banco de dados.
  Cada desenvolvedor geralmente possui um banco de dados local ao desenvolver e testar o site.
  O desenvolvedor irá editar o arquivo *local.php* e inserir suas próprias credenciais de banco de dados aqui.
  Quando você instala seu site no servidor de produção, você edita o arquivo `local.php` e digita
  as credenciais para o banco de dados aqui.


I> Porque o arquivo * autoload/local.php * contém parâmetros específicos do ambiente,
I> no sistema de controle de versão você armazena seu "modelo de distribuição" *local.php.dist*.
I> Cada desenvolvedor em sua equipe renomeia o arquivo * local.php.dist * para * local.php * e
I> informa em seus próprios parâmetros. Este arquivo *local.php* não deve ser armazenado em
I> Controle de versão (GIT), porque ele pode conter informações confidenciais, como credenciais de banco de dados
I> (nome de usuário e senha), e você pode querer que outras pessoas não os vejam.

### Configuração dos Arquivos de Desenvolvimento (no Nível de Aplicação)

O arquivo de configuração de desenvolvimento em nível da aplicativo (`APP_DIR/config/development.config.php`) apenas é usado
quando você ativa o *modo de desenvolvimento*. Se você lembrar, ativamos o modo de desenvolvimento
anteriormente no capítulo [Laminas Skeleton Application](#skeleton).

I> Você ativa o modo de desenvolvimento com o seguinte comando:
I>
I> `php composer.phar development-enable`

O arquivo `development.config.php` é incorporado com o arquivo` application.config.php`.
Isso permite que você substituir alguns parâmetros. Por exemplo, você pode:

  * desabilitar a configuração de cache. Quando você desenvolve seu site, você freqüentemente modifica seus arquivos de configuração, portanto, a configuração cache de
    pode ter consequências indesejadas, como incapacidade de ver o resultado de suas alterações imediatamente.
  * Carregar módulos adicionais. Por exemplo, você pode carregar o módulo [LaminasDeveloperTools](https://github.com/laminas/LaminasDeveloperTools) apenas no modo de desenvolvimento.

Se você desabilitar o modo de desenvolvimento, o arquivo `development.config.php` será apagado. Então você não deve
armazenar este arquivo sob o controle de versão. Em vez disso, armazene sua versão *distribution*, `development.config.php.dist` no seu controle de versão.

### Configuração dos Arquivos de Desenvolvimento Extra (no Nível de Aplicação)

O arquivo de configuração de desenvolvimento extra no nível de aplicação (`APP_DIR/config/autoload/development.local.php`) apresenta apenas
quando você ativa o *modo de desenvolvimento*.

O arquivo `development.local.php` é incorporado com outros arquivos de configuração. Isso permite que você
Substituir alguns parâmetros específicos do módulo usados ​​apenas no ambiente de desenvolvimento.

Se você desabilitar o modo de desenvolvimento, o arquivo `development.local.php` será apagado. Então você não deveria
armazene este arquivo sob o controle de versão. Em vez disso, armazene sua versão *distribution*, `development.local.php.dist`
no controle de versão.

### Arquivo de Configuração (no Nível do Módulo)

Na imagem 3.4, você pode ver que o módulo *Application* é enviado com seu aplicativo
tem o arquivo *module.config.php*, no qual você coloca seus parâmetros específicos do módulo. Vamos
veja o arquivo `module.config.php` do módulo` Application`:

{line-numbers=off,lang=php, title="module.config.php file"}
~~~
<?php
namespace Application;

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;

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
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => InvokableFactory::class,
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
~~~

Neste arquivo, você registra os module's controllers, coloca informações sobre regras de rotas
para mapear URLs para seus controllers, registrar plugins dos controllers e também registrar as views
e os views helpers (aprenderemos mais sobre esses termos neste capítulo e nos próximos capítulos).

### Combinando os arquivos de configuração

Quando uma aplicação está sendo criada, arquivos de configuração fornecidos pelo módulo
O diretório *APP_DIR/config/autoload* está sendo incorporado em uma grande array,
Assim, cada parâmetro de configuração fica disponível para qualquer parte do site.
Então, potencialmente, você pode substituir alguns parâmetros especificados pelos módulos.

I> Você também pode ter visto o arquivo de configuração "combinado" ao instalar o PHP, onde existe
I> o arquivo principal *php.ini* e vários arquivos extras de configuração, que são incluídos no arquivo principal.
I> Essa separação torna a configuração da sua aplicação mais refinada e flexível,
I> porque você não precisa colocar todos os seus parâmetros em um único arquivo e editá-lo toda vez que precisar
I> que necessitar mudar alguma coisa.

Os arquivos de configuração são carregados na seguinte ordem:

* O principal arquivo o *application.config.php* é carregado primeiro. É usado para inicializar o
  service manager e carregar os módulos da aplicação. Os dados carregados desta configuração
  é armazenado sozinho e não são incorporados com outros arquivos de configuração.

* Arquivos de configuração para cada módulo de aplicativo são carregados e incorporados. Módulos
  são carregados na mesma ordem em que estão listados no arquivo *application.config.php*.
  Se dois módulos armazenam (intencionalmente ou por engano) parâmetros no
  chaves de nome similar, esses parâmetros podem ser sobrescritos.

* Arquivos de configuração extra do *APP_DIR/config/autoload* são carregados e mesclados em um
  array único. Em seguida, essa array é incorporado com o module config com array que foi produzido anteriormente
  ao carregar a configuração do módulo. A configuração de todo o aplicativo
  prioridade mais alta que a configuração do módulo, então você pode sobrescrever as chaves do módulo aqui,
  se você desejar.

## Ponto de entrada do módulo

No Laminas, seu aplicativo consiste em módulos. Por padrão, você tem o único módulo `Application`, mas pode
crie mais, se necessário. Normalmente, seus próprios módulos são armazenados no diretório *APP_DIR/module*, enquanto o módulos de terceiros
fica dentro diretório *APP_DIR/vendor*.

Quando você inicia a sua aplicação, o objeto @`Laminas\Mvc\Application` é criado, ele usa o componente @`Laminas\ModuleManager` para localizar e carregar
todos os módulos registrados no application config.

Cada módulo da sua aplicação tem o arquivo *Module.php*, que é um tipo
de *ponto de entrada* para o módulo. Este arquivo fornece a classe `Module`. Abaixo, o conteúdo
da classe `Module` do skelleton application:

{line-numbers=off, lang=php, title="Contents of Module.php file"}
~~~
<?php
namespace Application;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
}
~~~

A classe `Module` pertence ao namespace do módulo (para o módulo principal
ele pertence ao namespace `Application`).

O  `getConfig ()` é normalmente usado para fornecer a configuração do módulo para o Laminas Framework (arquivo *module.config.php*).

I> Você também pode registrar alguns *event listeners*, vamos ver como fazer isso mais tarde
I> no capítulo [Criando um Novo Módulo](#modules).

## Service Manager

Você pode imaginar a sua aplicação como um conjunto de *serviços(services)*. Por exemplo,
você pode ter um serviço de autenticação responsável por fazer login os usuários do site,
um serviço gerenciador de entity por acessar o banco de dados, service manager de eventos
responsável por acionar eventos e entregá-los aos event listeners, etc.

No Laminas Framework, a classe @`ServiceManager` é um *container* centralizado para todos
serviços da aplicação. O service maanger é incorporado ao componente @`Laminas\ServiceManager`
como a classe @`ServiceManager`. O diagrama de herança de classes é mostrado na imagem 3.5 logo abaixo:

![Imagem 3.5. Service manager diagrama de herança de classes](../en/images/operation/service_manager_inheritance.png)

O service manager é criado na inicialização do aplicativo (dentro do `init()`)
método estático da classe @`Laminas\Mvc\Application`).
Os serviços padrão disponíveis através do service manager são apresentados na tabela 3.1.
Esta tabela está incompleta, porque o número real de serviços registrados no gerenciador de serviços
pode ser muito maior.

{title="Table 3.1. Serviços Padrões"}
|----------------------|-----------------------------------------------------------------------|
| Nome do Serviço      | Descrição                                                             |
|----------------------|-----------------------------------------------------------------------|
| `Application`        |Permite acessar o singleton da classe @`Laminas\Mvc\Application`.         |
|----------------------|-----------------------------------------------------------------------|
| `ApplicationConfig`  | Array de configuração extraída do arquivo *application.config.php*.   |
|----------------------|-----------------------------------------------------------------------|
| `Config`             |Array de configuração Incorporado do *module.config.php* mesclado com  |
|                      |*autoload/global.php* e *autoload/local.php *.                         |
|----------------------|-----------------------------------------------------------------------|
| `EventManager`       | Permite uma *nova* instância da classe @`Laminas\EventManager\EventManager`.|
|                      | O event manager permite enviar eventos (triggers) e anexar event listeners.|
|----------------------|-----------------------------------------------------------------------|
| `SharedEventManager` | Permite instânciar singleton da classe @`Laminas\EventManager\SharedEventManager`  |
|                      | O Shared Event Manager permite usar event listeners definidos por outras classes e componentes. |
|----------------------|-----------------------------------------------------------------------|
| `ModuleManager`        | Permite acessar o singleton da classe @`Laminas\ModuleManager\ModuleManager`.  |
|                      | module manager é responsável por carregar os módulos do aplicativo..        |
|----------------------|-----------------------------------------------------------------------|
| `Request`              | O singleton da classe @`Laminas\Http\Request`.                         |
|                      | Representa a solicitação HTTP recebida do cliente.                    |
|----------------------|-----------------------------------------------------------------------|
| `Response`             | O singleton da classe @`Laminas\Http\Response`.                        |
|                      | Representa a resposta HTTP que será enviada ao cliente.               |
|----------------------|-----------------------------------------------------------------------|
| `Router`               | O singleton de @`Laminas\Router\Http\TreeRouteStack`. Executa o roteamento de URL. |
|----------------------|-----------------------------------------------------------------------|
| `ServiceManager`       | O Próprio Service manager em si.                                               |
|----------------------|-----------------------------------------------------------------------|
| `ViewManager`          | O singleton da classe @`Laminas\Mvc\View\Http\ViewManager`.    |
|                      | Responsável por preparar o view da página                             |
|----------------------|-----------------------------------------------------------------------|


Um serviço é tipicamente uma classe PHP arbitrária, mas nem sempre. Por exemplo, quando Laminas
carrega os arquivos de configuração e incorpora os dados em arrays, salva as arrays
no service manager como serviços (!): `ApplicationConfig` e` Config`.
O primeiro é o array carregado a partir do arquivo de configuração em nível de aplicativo *application.config.php*,
e a mais recente é a matriz mesclada dos arquivos de configuração no nível do módulo e carregada automaticamente
arquivos de configuração em nível de aplicativo. Assim, no gerenciador de serviços, você pode armazenar qualquer coisa
você quer: uma classe PHP, uma variável simples ou um array.

Na tabela 3.1, você pode ver que no Laminas quase tudo pode ser considerado como um serviço. O service manager
é registrado como um serviço. Além disso, a classe @`Application` é também
registrado como um serviço.


I> Uma coisa importante que você deve observar sobre os serviços é que eles são *tipicamente*
I> rmazenado em uma única instância única (isso também é chamado o padrão *singleton*). Obviamente,
I> você não precisa da segunda instância da classe @ Application (nesse caso você
I>  Eu teria um pesadelo.

T> Mas existe uma exceção importante da regra acima. Pode ser confuso no início, mas o
T> @`EventManager` ão é um singleton. Toda vez que você recupera o service manager do event manager,
T> você recebe um objeto *novo(new)*. Isso é feito por motivos de desempenho e para evitar possíveis conflitos de eventos entre
T> componentes diferentes. Discutiremos isso mais adiante na seção *Sobre o Event Manager* mais adiante neste capítulo.

O service manager define vários métodos necessários para localizar e recuperar
um serviço do service manager (veja a tabela 3.2 abaixo).


{title="Table 3.2. ServiceManager métodos"}
|----------------------|-----------------------------------------------------------------------|
| Nome do Método       | Descrição                                                           |
|----------------------|-----------------------------------------------------------------------|
| `has($name)`         | Verifica se esse serviço está registrado.                            |
|----------------------|-----------------------------------------------------------------------|
| `get($name)`         | Recupera a instância de um serviço registrado.                            |
|----------------------|-----------------------------------------------------------------------|
| `build($name, $options)` | Sempre retorna uma nova instância do serviço solicitado.           |
|----------------------|-----------------------------------------------------------------------|

Você pode testar se um serviço está registrado, passando seu nome para o service manager's.
através do método `has()`. Ele retorna um booleano `true` se o serviço estiver registrado, ou
`false` se o serviço com esse nome não estiver registrado.

Você pode recuperar um serviço pelo seu nome mais tarde com a ajuda do método `get()` do service manager's.
Esse método usa um único parâmetro que representa o nome do serviço. Veja o exemplo:

~~~php
<?php

// Retrieve the application config array.
$appConfig = $serviceManager->get('ApplicationConfig');

// Use it (for example, retrieve the module list).
$modules = $appConfig['modules'];
~~~

E o método `build()` sempre cria uma nova instância do serviço quando você o chama (comparando com `get()`, que
normalmente cria a instância do serviço apenas uma vez e a retorna em solicitações posteriores).

T> Você normalmente recuperará serviços do service manager não em *qualquer* lugar do seu código, mas dentro de um *factory*. Uma factory
T> é um código responsável pela criação de um objeto. Ao criar o objeto, você pode recuperar serviços dos quais depende o service manager
T> e passar esses serviços (dependências) para o construtor (constructor) do objeto. Isso também é chamado de *injeção de dependência(dependency injection)*.

I> Se você tiver alguma experiência com o Laminas Framework 2, poderá perceber que as coisas agora estão um pouco diferentes do que antes.
I> No ZF2, havia o padrão `ServiceLocator` que permitia obter dependências do gerenciador de serviços em *qualquer* parte da sua aplicação
I> (em controllers, services, etc.) No Laminas, você tem que passar dependências explicitamente. É um pouco mais chato,
I> mas remove as dependências "ocultas" e torna seu código mais claro e fácil de entender.

### Registrando um Serviço

Ao escrever seu site, você frequentemente precisará registrar seu próprio serviço
no service manager. Uma das maneiras de registrar um serviço é usando o método `setService()` do service manager.
Por exemplo, vamos criar e registrar a classe de serviço de conversor de moeda, que
será usado, por exemplo, em uma página do carrinho de compras para converter moeda EUR em USD

~~~php
<?php
// Define a namespace where our custom service lives.
namespace Application\Service;

// Define a currency converter service class.
class CurrencyConverter
{
    // Converts euros to US dollars.
    public function convertEURtoUSD($amount)
    {
        return $amount*1.25;
    }

    //...
}
~~~


Acima, nas linhas 6-15, definimos um exemplo da classe `CurrencyConverter` (para simplificar, implementamos
apenas um único método `convertEURtoUSD()` que é capaz de converter euros em dólares americanos).

~~~php
// Create an instance of the class.
$service = new CurrencyConverter();
// Save the instance to service manager.
$serviceManager->setService(CurrencyConverter::class, $service);
~~~

No exemplo acima, instanciamos a classe com o operador `new`, e registramos
com o service manager com o o método `setService()` (assumimos que a variável `$serviceManager`
é da classe type @`Laminas\ServiceManager\ServiceManager`, e que foi declarado em algum outro lugar).

O método `setService()` usa dois parâmetros: o nome do serviço e a instância do serviço.
O nome do serviço deve ser exclusivo em todos os outros serviços possíveis.

Depois que o serviço é armazenado no service manager, você pode recuperá-lo pelo nome em qualquer
aplicação com a ajuda do método `get()` do service manager. Olhe para o seguinte
exemplo:


~~~php
<?php
// Retrieve the currency converter service.
$service = $serviceManager->get(CurrencyConverter::class);

// Use it (convert money amount).
$convertedAmount = $service->convertEURtoUSD(50);
~~~

### Nomes de serviço

Serviços diferentes podem usar diferentes estilos de nomenclatura. Por exemplo, o mesmo serviço de conversor de moeda
pode ser registrado sob os diferentes nomes: `CurrencyConverter`,  `currency_converter`
e assim por diante. Para introduzir alguma convenção de nomenclatura uniforme, recomenda-se registrar um serviço
seu nome de classe totalmente, como podemos ver abaixo:

~~~php
$serviceManager->setService(CurrencyConverter::class);
~~~

No exemplo acima, usamos a palavra-chave `class`. Está disponível desde o PHP 5.5 e é usado para classe
resolução de nomes. `CurrencyConverter::class` é expandido para o nome completo da classe,
como `\Application\Service\CurrencyConverter`.

### Substituindo um serviço existente

Se você está tentando registrar o nome do serviço que já está presente, no  `setService()` método exibirá um errro. Mas às vezes
você deseja substituir o serviço com o mesmo nome (para substituí-lo por um novo). Para este propósito,
você pode usar o método `setAllowOverride()` do service manager:

{line-numbers=of,lang=php}
~~~
<?php
// Allow to replace services
$serviceManager->setAllowOverride(true);

// Save the instance to service manager. There will be no exception
// even if there is another service with such a name.
$serviceManager->setService(CurrencyConverter::class, $service);
~~~

Acima, o método `setAllowOverride()` apenas aceita um único parâmetro booleano que define se
para permitir que você substitua o serviço "CurrencyConverter" se tal nome já estiver presente, ou não.

### Registrando Classes Invocáveis (Invokable)

O que é ruim com o método `setService()` é que você tem que criar a instância do serviço
antes que você realmente precise. Se você nunca usar o serviço, a instanciação do serviço será
desperdice o tempo e a memória. Para resolver esse problema, o service manager fornece a você
método `setInvokableClass()`.

~~~php
<?php
// Register an invokable class
$serviceManager->setInvokableClass(CurrencyConverter::class);
~~~

No exemplo acima, passamos para o service manager o nome completo da classe do
o serviço em vez de passar sua instância. Com esta técnica, o serviço
será instanciado pelo service manager somente quando alguém chamar o método `get(CurrencyConverter::class)`.
Isso também é chamado de lazy loading.

T> Os serviços geralmente dependem uns dos outros. Por exemplo, o serviço de conversor de moeda pode usar o serviço gentity manager
T> para ler as taxas de câmbio do banco de dados. A desvantagem do método `setInvokableClass()` é que ele não permite passar parâmetros (dependências)
T> para o serviço na instanciação de objetos. Para resolver esse problema, você pode usar *factories*, conforme descrito abaixo.

### Registrando uma Fábrica (Factory)

A *factory* é uma classe que pode fazer apenas uma coisa - para criar outros objetos.

Você registra uma factory para um serviço com o método `setFactory()` do service manager:

A fábrica mais simples é  @`InvokableFactory`  é análoga ao método da seção anterior `setInvokableClass()`.

~~~php
<?php
use Laminas\ServiceManager\Factory\InvokableFactory;

// This is equivalent to the setInvokableClass() method from previous section.
$serviceManager->setFactory(CurrencyConverter::class, InvokableFactory::class);
~~~

Depois de ter registrado a fábrica, você pode recuperar o serviço do service manager como de costume com o método `get()`. O serviço
será instanciado somente quando você recuperá-lo do service manager (lazy loading).

Às vezes, a instanciação de serviços é mais complexa do que apenas criar a instância de serviço
com o operador `new` (como @`InvokableFactory` faz). Você pode precisar passar alguns parâmetros para o construtor do serviço ou
Invoque alguns métodos de serviço logo após a construção. Esta lógica de instanciação complexa
pode ser encapsulado dentro de sua própria classe  *factory*.
A classe de fábrica geralmente implementa o @`FactoryInterface`[Laminas\ServiceManager\Factory\ FactoryInterface]:

~~~php
<?php
namespace Laminas\ServiceManager\Factory;

use Interop\Container\ContainerInterface;

interface FactoryInterface
{
    public function __invoke(ContainerInterface $container,
                        $requestedName, array $options = null);
}
~~~

Como vemos na definição do @`FactoryInterface`[Laminas\ServiceManager\Factory\FactoryInterface], a classe da factory deve fornecer
o método mágico `__invoke` retornando a instância de um único serviço. O service manager é
passado para o método `__invoke` como o parâmetro `$container`; pode ser usado durante a construção de
o serviço para acessar outros serviços (para injetar *dependências*). O segundo argumento (`$requestedName`) é
o nome do serviço. O terceiro argumento (`$options`) pode ser usado para passar alguns parâmetros para o serviço, e
é usado somente quando você solicita o serviço com o método `build()` do service manager.

Como exemplo, vamos escrever uma factory para o nosso serviço de conversão de moeda (observe o código abaixo).
Nós não usamos lógicas de construção complexas para o nosso serviço `CurrencyConverter`, mas para serviços mais complexos.
serviços, talvez seja necessário usar um.

~~~php
<?php
namespace Application\Service\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Application\Service\CurrencyConverter;

// Factory class
class CurrencyConverterFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container,
                     $requestedName, array $options = null)
    {
        // Create an instance of the class.
        $service = new CurrencyConverter();

        return $service;
    }
}
~~~

I> Tecnicamente, no Laminas você *pode* usar a mesma classe da factory para instanciar vários serviços que possuem
I> código de instanciação (para esse propósito, você pode usar o argumento `$requestedName` passado para o método `__invoke()` da factory).
I> No entanto, *principalmente* você criará uma fábrica diferente para cada serviço.

###  Registrando uma Fábrica Abstrata (Abstract Factory)

Ainda mais complexo de uma factory é quando você precisa determinar na execução
em real time em que os nomes dos serviços devem ser registrados. Para tal situação,
você pode usar uma *abstract factory*. Uma classe de fábrica abstrata deve
implementar a interface @`AbstractFactoryInterface`[Laminas\ServiceManager\Factory\AbstractFactoryInterface]:

~~~php
<?php
namespace Laminas\ServiceManager\Factory;

use Interop\Container\ContainerInterface;

interface AbstractFactoryInterface extends FactoryInterface
{
    public function canCreate(ContainerInterface $container, $requestedName);
}
~~~

Uma abstract factory tem dois métodos: `canCreate()`
e `__invoke ()`. O primeiro é necessário para testar se a factory pode
criar o serviço com o nome certo, e este último permite realmente
criar o serviço. Os métodos usam dois parâmetros: service manager (`$container`) e
nome do serviço (`$requestedName`).


Comparando com a usual factory classe, a diferença é que o
classe de factory usual *tipicamente* cria apenas um único tipo de serviço, mas uma abstract factory pode dinamicamente
criar tantos tipos de serviços quanto quiser.

Você registra uma abstract factory com o método `setAbstractFactory()` do service manager.


T> Abstract factory são um recurso poderoso, mas você deve usá-las somente quando realmente necessário, porque
T> Eles afetam negativamente o desempenho. É melhor usar as fábricas usuais (não abstratas).

### Registrando Aliases de Serviço

Às vezes, você pode querer definir um *alias* para um serviço. O alias
é como um link simbólico: faz referência ao serviço já registrado.
Para criar um alias, você usa o método `setAlias​​()` do service manager:

~~~php
<?php
// Register an alias for the CurrencyConverter service
$serviceManager->setAlias('CurConv', CurrencyConverter::class);
~~~

Uma vez cadastrado, você pode recuperar o serviço pelo seu nome e alias usando o
método `get()` do service manager.

### Serviços compartilhados e não compartilhados

Por padrão, os serviços são armazenados no service manager somente em instância única. Isso também é chamado do design patterns *singleton*
Por exemplo, quando você tenta recuperar o serviço `CurrencyConverter` duas vezes, você receberá
o mesmo objeto. Isso também é chamado de serviço *compartilhado (shared)*.

Mas, em algumas situações (raras), você precisará criar uma *nova* instância de um serviço sempre que alguém solicitar
do service manager. Um exemplo é o @`EventManager` - você obtém uma nova instância a cada vez que o solicita.

Para marcar um serviço como não compartilhado, você pode usar o método `setShared()` do service manager:

~~~php
$serviceManager->setShared('EventManager', false);
~~~

### Configuração do Service Manager

Em seu site, você normalmente usa a configuração do service manager para registrar seus serviços (em vez de chamar
métodos do service manager, conforme descrito acima).

Para registrar automaticamente um serviço no service manager, geralmente
é usado um arquivo de configuração para o com uma chave `service_manager`. Você pode colocar essa chave
dentro de um arquivo de configuração no nível do aplicativo ou em um nível de módulo
arquivo de configuração.

W> Se você está colocando essa chave em um arquivo de configuração de nível de módulo, seja
W> Cuidado com o perigo de sobrescrever o nome durante a incorporação de configurações.
W> Não registre o mesmo nome de serviço em módulos diferentes.

A chave de configuração `service_manager` deve se parecer como:

~~~php
<?php
return [
    //...

    // Register the services under this key
    'service_manager' => [
        'services' => [
            // Register service class instances here
            //...
        ],
        'invokables' => [
            // Register invokable classes here
            //...
        ],
        'factories' => [
            // Register factories here
            //...
        ],
        'abstract_factories' => [
            // Register abstract factories here
            //...
        ],
        'aliases' => [
            // Register service aliases here
            //...
        ],
        'shared' => [
            // Specify here which services must be non-shared
        ]
  ],

  //...
];
~~~

No exemplo acima, você pode ver que o `service_manager` pode conter vários
subchaves para registrar serviços de maneiras diferentes:

* a subchave `services` (linha 7) permite registrar instâncias de classes;
* a subchave `invokables` (linha 11) permite registrar o nome completo da classe de um serviço;
  o serviço será instanciado usando o carregamento lento;
* a subchave `factories` (linha 15) permite registrar uma fábrica, que é capaz
  criar instâncias de um único serviço;
* o `abstract_factories` (linha 19) pode ser usado para registrar fábricas abstratas,
  que são capazes de registrar vários serviços pelo nome;
* A subchave `aliases` (linha 23) fornece a capacidade de registrar um alias para um serviço.
* A subchave `shared` (linha 27) permite especificar quais serviços devem ser não compartilhados.

Como exemplo, vamos registrar nosso serviço `CurrencyConverter` e criar um alias para ele:

~~~php
<?php
use Laminas\ServiceManager\Factory\InvokableFactory;
use Application\Service\CurrencyConverter;

return [
    //...

    // Register the services under this key
    'service_manager' => [
        'factories' => [
            // Register CurrencyConverter service.
            CurrencyConverter::class => InvokableFactory::class
        ],
        'aliases' => [
            // Register an alias for the CurrencyConverter service.
            'CurConv' => CurrencyConverter::class
        ],
  ],

  //...
];
~~~

## Gerenciamento de Plugin (Plugin Manager)

Agora que você entende o que é o service manager, não deve ser muito difícil para você aprender
o conceito de *Gerenciamento de plugins*. Um * gerenciador de plug-ins* é quase o mesmo que o service manager,
mas pode instanciar serviços apenas do tipo único. Qual tipo de plugin um gerenciador de plugins pode
ser codificado e instanciado dentro da classe do gerenciador de plugins.

Por que você precisaria de tal coisa? Na verdade, no Laminas, os gerenciadores de plug-ins são amplamente utilizados, porque
permitir instanciar um plug-in somente quando necessário (isso reduz o uso da CPU e da memória). Existe um
gerenciador de plug-ins separado para:


  * controllers (a classe @`Laminas\Mvc\Controller\ControllerManager`)
  * controller plugins (a classe @`Laminas\Mvc\Controller\PluginManager`)
  * view helpers (a classe @`Laminas\View\HelperPluginManager`)
  * form elements (a classe @ `Laminas\Form\FormElementManager\FormElementManagerV3Polyfill`)
  * filters (a classe @ `Laminas\Filter\FilterPluginManager`)
  * validators (a classe @ `Laminas\Validator\ ValidatorPluginManager`)
  * e provavelmente outras coisas

O fato de cada gerenciador de plugins herdar da base a classe @`Laminas\ServiceManager\ServiceManager`
 permite que todos os gerenciadores de plugin tenham configuração similar. Por exemplo, os controllers são
registrado sob a chave `controllers` no arquivo *module.config.php*, e esta chave pode ter as mesmas subchaves:
*services*, *invokables*, *factories*, *abstract_factories* e *aliases*. A mesma estrutura tem o
*view_helpers* chave que é usada para registrar helpers de visualização, a chave *controller_plugins* que é usada
para registrar plugins do controllers e assim por diante.

## Sobre o  Event Manager

T> Nesta seção, forneceremos algumas informações avançadas sobre o service manager. Você pode com relativa segurança pular esta
   seção, no entanto, consulte se você pretende implementar alguns events listeners em seu site.

Anteriormente neste capítulo, mencionamos que o ciclo de vida da aplicação consiste em *eventos*.
Uma classe pode *desencadear* um evento e outras classes podem *listen (ouvir)* eventos. Tecnicamente, desencadear um evento significa apenas chamar
método de "callback" de outra classe. O service manager é implementado dentro de
o componente @`Laminas\EventManager`.

T> Laminas (e particularmente seu componente @`Laminas\Mvc`) dificilmente depende de eventos para operar,
T> e, por causa disso, seu código-fonte é uma combinação de events listeners, o que é bastante difícil de entender.
T> Felizmente, na maioria dos casos, você não precisa entender como o Laminas dispara e lida com eventos internamente, você só precisa
T> para entender o que é evento, quais eventos estão presentes no ciclo de vida da aplicação e qual é a diferença entre o usual *service manager* e
T> *shared event manager*.

### Event & MvcEvent

Um *event* é tecnicamente uma instância da classe @`Laminas\EventManager\Event`.
Um evento basicamente pode ter pelo menos as seguintes partes:

  * *name*  - identifica exclusivamente o evento;
  * *target*  - normalmente é um ponteiro para o objeto que acionou o evento;
  * e *params* - argumentos específicos do evento passados ​​aos events listeners.

É possível criar tipos personalizados de eventos estendendo a classe @`Event`[Laminas\EventManager\Event].
Por exemplo, o componente @`Laminas\Mvc` define o tipo de evento personalizado chamado @`Laminas\Mvc\MvcEvent`,
que estende a classe `Event` e adiciona várias propriedades e métodos
necessário para o componente @`Laminas\Mvc` funcionar.

### EventManager & SharedEventManager

É importante entender a diferença entre o event manager *usual* e o event manager *shared*.

O event manager comum não é armazenado como singleton no service manager. Toda vez que você solicita o serviço @`EventManager`
do service manager, você recebe uma nova instância dele. Isso é feito por motivos de privacidade e desempenho:

  * Presume-se, por padrão, que os eventos de acionamento da classe serão solicitados e salvos em algum lugar
    seu próprio event manager privado, porque não quer que outras classes
    ouvir automaticamente esses eventos. Considera-se que os eventos acionados pela classe pertencem a essa classe de forma privada.

  * Se alguém fosse capaz de ouvir qualquer evento desencadeado por qualquer classe, haveria um desempenho infernal - muitos
    ouvintes de eventos seriam invocados, aumentando assim o tempo de carregamento da página. É melhor evitar isso mantendo os eventos privados.

Mas, no caso de alguém intencionalmente *precisar* ouvir os eventos dos outros, há um event manager *compartilhado* especial. O @`SharedEventManager`
o serviço é armazenado no service manager como um singleton, portanto, você pode ter certeza de que todos terão a mesma instância dele.

T> Alguns exemplos práticos de como ouvir e reagir a um evento podem ser encontrados no capítulo [Criando um novo Módulo](#modules)
T> e em [User Management, Authentication & Access Filtering](#users).

## Resumo

Neste capítulo, aprendemos alguma teoria sobre os conceitos básicos de operação de sites baseados no Laminas.

O Laminas usa namespaces PHP e recursos de carregamento automático de classes, simplificando o desenvolvimento
de aplicativos que usam muitos componentes de terceiros. Os namespaces permitem resolver o
colisões de nomes entre componentes de código e fornecem a capacidade de tornar os nomes longos mais curtos.

O autoloading de classe possibilita o uso de qualquer classe PHP em qualquer biblioteca instalada com o Composer
sem o uso da declaração `require_once`. O Composer também fornece um autoloader PSR-4 para as classes
localizado nos módulos do seu aplicativo da web.

A maioria dos componentes do Laminas Framework requer configuração. Você pode definir os parâmetros de configuração em
o nível do aplicativo ou no nível do módulo.

O principal objetivo de qualquer aplicativo da Web é manipular a solicitação HTTP e produzir um
Resposta HTTP normalmente contendo o código HTML da página da web solicitada. Quando
O servidor web Apache recebe uma requisição HTTP de um navegador cliente, ele executa o *index.php*
arquivo, que também é chamado de script de entrada do site. Em cada solicitação HTTP, o @`Laminas\Mvc\Application`
objeto é criado, cujo "ciclo de vida" consiste em vários estágios (ou eventos).

A lógica de negócios do aplicativo da Web também pode ser considerada como um conjunto de serviços. No Laminas Framework,
o service maanger é um contêiner centralizado para todos os serviços de aplicativos. Um serviço é tipicamente
uma classe PHP, mas em geral pode ser uma variável ou uma array, se necessário.
