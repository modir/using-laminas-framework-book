{mainmatter}

# Introdução ao Laminas Framework {#intro}

Neste capítulo, você aprenderá sobre o Laminas Framework, seus princípios e seus componentes.

## O que é o Laminas Framework??

PHP é uma linguagem popular de desenvolvimento de sites. No entanto, fazer sites em PHP puro é difícil.
Se você fizer uma aplicação web em PHP puro, você terá que organizar seu código de alguma forma, coletar
e validar a dados do usuário, implementar suporte de autenticação do usuário e controle de acesso, gerenciar banco de dados,
teste seu código e assim por diante.
À medida que seu site cresce em tamanho, fica cada vez mais difícil desenvolver
o código de maneira consistente. Cada desenvolvedor de sua equipe aplica sua codificação personalizada favorita
estilos e padrões. O código se torna supercomplicado, lento e difícil de suportar.
Você mescla o seu código em um único script gigante sem separação de interesses. Você tem que reinventar a roda
muitas vezes e isso causa problemas de segurança. Além disso, quando você muda para o desenvolvimento de um novo
site você notará que uma grande parte do código que você já escreveu para o site antigo
pode ser usado novamente com pequenas modificações. Este código pode ser separado
numa biblioteca. É assim que os frameworks aparecem.

I> Um framework é um tipo de biblioteca, de um software (também escrito em PHP)
Que fornecer aos desenvolvedores da Web a base de código e formas padronizadas e consistentes de criação
Para aplicativos da web.

Laminas Framework é um Framework de PHP open-source e Grátis.
Seu desenvolvimento é guiado (e patrocinado) pela Laminas Technologies, que também é conhecida como a fornecedora da linguagem
PHP. A primeira versão (Laminas Framework 1) foi lançada em 2007; Laminas Framework 2, a segunda versão deste software,
foi lançado em setembro de 2012. Laminas Framework (ou brevemente Laminas) foi lançado em junho de 2016.

O Laminas Framework fornece os seguintes recursos:

* Desenvolva seu site muito mais rápido do que quando feito em PHP puro. Laminas fornece
  muitos componentes e blibiotecas que podem ser usados ​​como base de código para criar seu site.

* Cooperação mais fácil com outros membros da equipe de criação de sites. Model-View-Controller (MVC)
  O padrão usado pelo Laminas permite separar a lógica de negócios e a camada de apresentação do seu
  site, tornando sua estrutura consistente e sustentável.

* Escale seu site com o conceito de módulos(modules). Laminas usa o termo * module *,
  permitindo separar partes dissociadas do site, permitindo assim a reutilização de modelos, visualizações,
  controladores e ativos do seu site em outros trabalhos.

* Acessando banco de dados com orientação a objeto. Em vez de interagir diretamente com o banco de dados
  usando consultas SQL, você pode usar o Object-Relational Mapping (ORM) do Doctrine para gerenciar
  estrutura e relacionamentos entre seus dados. Com o Doctrine você mapeia seu banco de dados
  tabela para uma classe PHP (também chamada de uma classe * entity *) e uma linha daquela tabela é
  mapeado para uma instância dessa classe. Doctrine permite resumo do tipo de banco de dados
  e tornar o código mais fácil de entender.

* Faça sites seguros com componentes fornecidos pelo Laminas, como
  filtros de entrada(input filters) e validadores(validators), algoritmos de criptografia,
   (Captcha) e Cross-Site Request Forgery (CSRF).


## Um exemplo de site PHP


Para demonstrar como é difícil escrever um site * sem * um framework, aqui nós
vamos escrever um site muito simples, composto por três páginas HTML: * Home *, * Login * e * Logout *.
Para este exemplo, não usaremos nenhuma estrutura e tentaremos usar apenas PHP "puro".

I> Não fique confuso - escrever um site com framework também pode ser difícil, mas
I> com um framework, você fará isso de maneira consistente e segura.

### Home Page

I> Quando você escreve um site em PHP, você coloca seu código em um arquivo com a extensão * .php *. Este arquivo é chamado
   um script PHP *.

Primeiro, vamos implementar a página * Home * do site. Para fazer isso, você deve criar o arquivo * index.php * no seu Apache ou
no seu diretório raiz e coloque o seguinte código:

T> Para entender o código abaixo, você precisa ter alguma experiência com PHP. Se você não tem experiência
T> com PHP, caso tenha duvidas ou necessite de algum tutorial de PHP,
T> recomendamos [w3schools.com](http://www.w3schools.com/php/).

~~~php
<?php
// index.php
session_start();

// If user is logged in, retrieve identity from session.
$identity = null;
if (isset($_SESSION['identity'])) {
    $identity = $_SESSION['identity'];
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Home page</title>
    </head>
    <body>
        <h1>Home</h1>
        <?php if ($identity==null): ?>
        <a href="login.php">Sign in</a>
        <?php else: ?>
        <strong>Welcome, <?= $identity ?></strong> <a href="logout.php">Sign out</a>
        <?php endif; ?>

        <p>
            This is a simple website to demonstrate the advantages of a PHP framework
            and disadvantages of "pure" PHP.
        </p>
    </body>
</html>
~~~

Se você agora digitar "http: //localhost/index.php" no seu navegador (como Google Chrome ou Firefox),
deve ver a página como abaixo:

![Home page](../en/images/intro/simple_home_page.png)

### Login Page

Em seguida, vamos implementar a página * Login *. Essa página teria um formulário com os campos * E-mail *
e * Senha * . Depois que o usuário envia o formulário, ele será autenticado e vamos salvar os seus dados na
na sessão do PHP. O Código ficaria:

~~~php
<?php
// login.php
session_start();

// If user is logged in, redirect him to index.php
if (isset($_SESSION['identity'])) {
    header('Location: index.php');
    exit;
}

// Check if form is submitted.
$submitted = false;
if ($_SERVER['REQUEST_METHOD']=='POST') {

    $submitted = true;

    // Extract form data.
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Authenticate user.
    $authenticated = false;
    if ($email=='admin@example.com' && $password=='Secur1ty') {
        $authenticated = true;

        // Save identity to session.
        $_SESSION['identity'] = $email;

        // Redirect the user to index.php.
        header('Location: index.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Login page</title>
    </head>
    <body>
        <h1>Sign in</h1>
        <?php if ($submitted && !$authenticated): ?>
            <div class="alert">
                Invalid credentials.
            </div>
        <?php endif; ?>
        <form name="login-form" action="/login.php" method="POST">
            <label for="email">E-mail</label>
            <input type="text" name="email">
            <br>
            <label for="password">Password</label>
            <input type="password" name="password">
            <br>
            <input type="submit" name="submit" value="Sign In">
        </form>
    </body>
</html>
~~~

Se você abrir o URL "http: //localhost/login.php" em seu navegador, você vai ver algo como:

![Login page](../en/images/intro/simple_login_page.png)

T> Para efetuar login, use o `admin@example.com` e `Secur1ty` como seu E-mail e senha, respectivamente.

### Logout Page

E finalmente, vamos implementar a página * Logout * que irá limpar os dados do usuário e da sessão:

~~~php
<?php
// logout.php
session_start();

unset($_SESSION['identity']);
header('Location: index.php');
exit;
~~~

T> O código completo deste site pode ser encontrado em
T> [Pure PHP](https://github.com/olegkrivtsov/using-laminas-book-samples/tree/master/purephp) exemplo está incluso com este livro.

### Revisando o Código

Os scripts acima não são apenas um exemplo típico de um site PHP "puro". É também um exemplo de
como você * não deve * escrever seus sites (mesmo sites simples). O que há de errado nisso?

1. Os scripts * index.php * e * login.php * tendem a juntar o código em um único arquivo.
   Você não tem nenhuma separação, o que torna o seu código muito complexo.
   Intuitivamente, você entende que seria
   porém é mais conveniente dividir o código responsável pela autenticação do usuário e o código
   responsável pela apresentação (renderização HTML).

2. As URLs das suas páginas parecem feias (por exemplo, "http: //localhost/index.php"). Nós seria melhor esconder
   essa extensão .php em tudo. E o que acontece quando um usuário da web tenta visitar uma página que não existe?
   Não seria melhor redirecionar o usuário para uma página de erro nesse caso.

3. E se este site aumentar de tamanho? Como você vai organizar o seu código? Um script PHP por página da web?
   E se você quiser reutilizar alguns dos seus scripts PHP em outros sites sem alterações? Intuitivamente
   Você pode entender que seria útil organizar o código em algum tipo de * módulos * reutilizáveis.

4. Os scripts * index.php * e * login.php * contêm HTML comum. Por que copiamos e colamos
   este layout comum em todos os scripts PHP? Não seria melhor reutilizar o mesmo layout padrão em todos
   (ou quase todas) as páginas.


5. O script * login.php * tem problemas de segurança, porque não implementamos nenhuma validação do ​​POST.
   A sessão do PHP também está sujeita a ataques hackers. E o script * login.php * PHP está localizado no
   diretório raiz do documento, o que não é muito seguro (seria melhor colocá-lo em um local não acessível
   para usuários da web). O * index.php * também é inseguro, porque nós não filtramos a saída do PHP (ele está sujeito a ataques XSS).

6. Esses scripts não usam classes PHP. Encapsulando funcionalidade em classes em teoria faria
   o código bem estruturado e fácil de  dar suporte.

7. Nesses scripts, você precisa escrever sua própria implementação de autenticação do usuário (e assim por diante).
   Em vez de reinventamos a roda por que não usar uma biblioteca bem projetada para isso?

Os problemas acima são facilmente resolvidos quando você escreve um site dentro de um framework (como o Laminas Framework):


1. No Laminas, você usa o padrão de projeto * Model-View-Controller *, dividindo seu código PHP em models
   (o código responsável pela autenticação), views (o código responsável pela renderização de HTML)
   e controllers (o código responsável por recuperar as variáveis ​​do POST).

2. O sistema de rotas do  Laminas  permite deixar as URLS mais profissionais ocultando a extensões .php. Todas as URLS seguem regras
   estritas. Se um usuário tentar ver uma página não existente, ele será automaticamente
   redirecionado para uma página de erro padrão.

3.  No Laminas, você pode usar o conceito de * module *. Isso permite convenientemente separar seus  models, views and
   controllers em unidade autônoma (module) e reutilize facilmente essa unidade em outro projeto.

4. No Laminas, você pode definir um modelo padrão para visualização * layout * e reutilizá-lo em todas (ou na maioria) páginas da web.

5. O Laminas fornece vários recursos de segurança, como filters e validators, criptografia
   algoritmos e assim por diante. Em um site da Laminas, apenas o * index.php * é acessível para usuários da web, todos os outros scripts PHP
   estão localizados fora do diretório raiz de documentos do Apache.

6. Em um site da Laminas, você coloca seu código em classes, o que o torna bem organizado.

7. O Laminas fornece muitos componentes que você pode usar em seu site: existem componentes para autenticação, um componente
   para trabalhar com formulários e assim por diante.

T> Agora você pode ter alguma idéia das vantagens do Laminas Framework e do que ele pode fazer por você. Na próxima
   seções, descreveremos o Laminas em mais detalhes.

## Licença


O Laminas Framework é licenciado sob licença do tipo BSD, permitindo que você o use em aplicativos comerciais e gratuitos.
Você pode até modificar o código da biblioteca e liberá-lo com outro nome.
A única coisa que você não pode fazer é remover o aviso de direitos autorais do código.
Se você usa o Laminas Framework, também é recomendável mencionar isso na documentação do seu site ou na página Sobre.

Segue abaixo, a licença do Laminas Framework:

~~~text
Copyright (c) 2005-2016, Laminas Technologies USA, Inc.
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions
are met:

	* Redistributions of source code must retain the above copyright
	  notice, this list of conditions and the following disclaimer.

	* Redistributions in binary form must reproduce the above copyright
	  notice, this list of conditions and the following disclaimer in
	  the documentation and/or other materials provided with the
	  distribution.

	* Neither the name of Laminas Technologies USA, Inc. nor the names of
	  its contributors may be used to endorse or promote products
	  derived from this software without specific prior written
	  permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
"AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED
TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
~~~

## Suporte ao usuário


O suporte é uma coisa importante a considerar ao decidir se deve usar o framework como base para o seu site ou não.
O suporte deve inclui documentação bem escrita, webinars, fóruns da comunidade e (opcionalmente) serviços de suporte comercial,
como treinamentos e programas de certificação.

![Site Oficial do Projeto Laminas Framework](../en/images/intro/Laminas_framework_site.png)

*Documentação*. A documentação do Laminas Framework está disponivel [neste link](https://framework.Laminas.com/learn).
Inclui o manual  utlização e tutoriais para progamadores iniciantes.

* API de Referência * pode ser encontrada [neste link](https://olegkrivtsov.github.io/laminas-api-reference/html/).

*Fórum*. Você pode fazer uma pergunta sobre a utilização do Laminas Framework no [StackOverflow](https://stackoverflow.com/search?q=Laminas+framework+3).
Suas perguntas serão respondidas pela comunidade de desenvolvedores do Laminas.

* Webinars * são tutoriais em vídeo cp, vários recursos do Laminas Framework. Lista completa de webinars
pode ser encontrado [neste link] (http://www.Laminas.com/en/resources/webinars/framework).

* Classes de treinamento * com instrutores ao vivo podem ser acessados ​​por
[nesse link] (http://www.Laminas.com/en/services/training).
Aqui você pode aprender o Laminas Framework faLaminaso exercícios,
mini-projetos e desenvolvimento de código real.

* Programa de Certificação *. Permite que você se torne um Engenheiro Certificado Laminas (ZCE),
tornando assim mais fácil melhorar as suas habilidades como arquiteto e encontrar um emprego em um
mercado de trabalho competitivo PHP. Certificações podem ser encontradas [aqui](http://www.Laminas.com/en/services/certification).

* Quer mais recursos do Laminas? * Confira esta impressionante lista de [recursos do Laminas Framework](https://github.com/dignityinside/awesome-zf).

## Framework Source Code

O código-fonte do Laminas Framework é armazenado no GitHub [repositório](https://github.com/laminas).
Existe um repositório separado para cada componente Laminas.

I> Na maioria dos casos, você não precisará obter o código do Laminas Framework manualmente.
I> Em vez disso, você irá instalá-lo com o gerenciador de dependência do Composer. Nós iremos nos
I> familiarizar com o Composer posteriormente em um capítulo chamado [Laminas Skeleton Application](# skeleton).

### Padrões de Código

É uma boa prática introduzir algum padrão de código comum para  o seu código. Este padrão definiria a nomenclatura de classes
regras, regras de formatação de código, etc. O Laminas Framework define esse padrão [aqui](https://github.com/laminas/laminas/wiki/Coding-Standards).
Todos os códigos no Laminas segue as regras descritas nesse documento.

T> Se você planeja escrever um site baseado no Laminas, é recomendável seguir o mesmo padrão para o seu código. Isso vai
T> tornar seu código mais consistente e mais fácil de dar suporte a outras pessoas.

## Sistemas Operacionais Suportados

Como qualquer site em PHP, ou aplicativo da Web baseado em Laminas pode funcionar em um servidor Linux ou em qualquer outro sistema operacional em que o PHP possa
funcionar. Para este livro, o autor usou o sistema operacional Ubuntu Linux.

Se você ainda não sabe qual SO usar para o seu desenvolvimento web, é
recomendado para você usar o Linux, porque a maioria dos servidores opera em servidores Linux.
Você pode consultar o [Apêndice A. Configurando o Ambiente de Desenvolvimento da Web](#devenv)
para obter algumas instruções sobre como configurar seu ambiente de desenvolvimento.

## Requisitos do servidor

O Laminas Framework requer que seu servidor tenha a versão 5.6 do PHP (ou maior)
instaladada. Note que este é um requisito bastante restrito. Nem todos os hosts possuem essa versão do PHP.

Além disso, a maneira recomendada de instalar o Laminas (e outros componentes
do seu aplicativo) é usando o [Composer](http://getcomposer.org/).
Isso força a necessidade de acesso ao shell (SSH) para poder
para executar a linha de comando do Composer. Algumas web host fornecem apenas acesso FTP, então
você não poderá instalar o Laminas nesses servidores da maneira usual.

O Laminas utiliza a extensão de reescrita de URL para redirecionar os usuários da Web para
script de entrada do seu site (você tem que habilitar o módulo `mod_rewrite` do Apache).
Você também pode precisar instalar algumas extensões PHP, como `memcached`.
Isso pode ser uma dificuldade ao usar uma hospedagem compartilhada
e requer que você tenha direitos de administrador no seu servidor.

O Laminas utiliza a extensão de reescrita de URL (URL rewriting) para redirecionar os usuários para
script do seu site (você tem que habilitar o módulo `mod_rewrite` do Apache).
Você também pode precisar instalar algumas extensões PHP, como `memcached`.
Isso pode ser uma dificuldade ao usar uma hospedagem compartilhada
e requer que você tenha direitos de administrador no seu servidor.

Então, se você está planejando usar o Laminas em uma hospedagem compartilhada, pense duas vezes.
O melhor servidor para instalar o Laminas é um servidor com a última versão do
PHP e com acesso shell para poder executar o Composer e instalar extensões PHP.

Se sua empresa gerencia tem sua própria infra-estrutura de servidores e pode proporcionar
atualizando a versão do PHP para a mais recente, você pode instalar o Laminas em seu servidor privado.

Uma alternativa aceitável é instalar um aplicativo Web em Laminas em um
serviço de hospedagem baseado em nuvem, como [Amazon Web Services](http://aws.amazon.com/).
A Amazon fornece instâncias de servidor Linux como parte do serviço EC2. EC2 é bastante
barato, e fornece um [nível de uso gratuito](http://aws.amazon.com/free/)
você tenta de graça por um ano.
Fornecemos instruções para iniciantes sobre como instalar um site da Laminas na nuvem do Amazon EC2 em
[Anexos E. Instalação um aplicativo da Web Laminas no Amazon EC2](#ec2-tutorial).

## Segurança

O Laminas Framework segue as melhores práticas para fornecer um código seguro
para seus sites. Os criadores de Laminas liberam patches de segurança uma vez que a comunidade de usuários
encontra um problema. Você pode incorporar essas correções com um único comando por meio do Composer.

I> Na prática usar um framework para fazer seu site é mais seguro do que usar PHP "puro"
I> porque você não precisa reinventar a roda. Maioria das vulnerabilidades de segurança em frameworks
I> já são conhecidos e corrigidos pela comunidade de usuários.

O Laminas oferece os seguintes recursos que permitem tornar seu site seguro:

* *Script de Entrada* (*index.php*) é o único arquivo PHP acessível aos visitantes. Todos os outros arquivos PHP
  estão localizados fora da pasta raiz do Apache. Isso é muito mais seguro do que permitir que todos
  visite qualquer um dos seus scripts PHP.

* *Rotas* permite definir regras estritas sobre o acesso de uma URL.
  Se um usuário inserir uma URL inválida na barra de navegação do navegador da Web,
  ele/ela é redirecionado automaticamente para uma página de erro.

* *Lista de Controle de Acesso (ACL)* e *Controle de Acesso Baseado na Função (RBAC)* permitem
  definir regras flexíveis para conceder ou negar o acesso a certos recursos de
  seu site. Por exemplo, um usuário anônimo teria acesso ao índice
  somente na página, os usuários autenticados teriam acesso à página de perfil e
  o usuário administrador teria acesso ao dashboard de gerenciamento do site.

* *Formulários de Validação e Filtros* garantem que nenhum dado indesejado seja coletado
 através de formulários. Os filtros, por exemplo, permite remover tags HTML.
 Os validadores são usados ​​para verificar se os dados que foram enviados por meio de um formulário
 está conforme com certas regras. Por exemplo, o validador de e-mail verifica se um campo de e-mail
 contém um endereço de e-mail válido e, se não, gera um erro que força o usuário do site a corrigir.

* *Captcha* and *CSRF* (Cross-Site Request Forgery)  são form elements que são usados ​​para verificações humanas
 e prevenção de ataques de hackers, respectivamente.

* *Laminas\Escaper* componente permite retirar tags HTML indesejadas dos dados enviados para as páginas do site.

* *Suporte a Criptografia* permite que você armazene seus dados confidenciais (por exemplo, credenciais) criptografados com
  um algoritmo de criptografia que são difíceis de hackear.

## Performance

O Laminas fornece os seguintes recursos para garantir que seu desempenho seja aceitável:

  * *Lazy class autoloading.* As classes são carregadas somente quando necessárias.
    Você não precisa escrever `require_once` para cada classe
    que você quer carregar. Em vez disso, o framework descobre automaticamente suas classes
    usando o recurso *autoloader*.

  * *Serviço Eficiente de Carregamento de Plugins.* No Laminas, as classes são instanciadas
    somente quando eles realmente precisam. Isso é conseguido através do gerenciador de serviços
    (o container central para serviços da sua aplicação).

  * *Suporte de Cache.* PHP tem várias extensões de caching (como Memcached) que
    pode ser usado para acelerar sites baseados em Laminas. O cache salva com frequência
    utilizando dados na memória para acelerar a recuperação de dados.

## Design Patterns

Os criadores do Laminas Framework são grandes fãs de vários padrões de design (Design Patterns). Apesar
você não precisa entender esses padrões para ler este livro, esta seção é destinada
para lhe dar uma idéia de quais design patterns o Laminas é baseado

* *Padrão Model-View-Controller (MVC)*. O padrão Model-View-Controller é usado em todos os frameworks modernos do PHP.
  Em uma aplicação MVC, você separa seu código em três categorias:
  models (sua lógica de negócios), views (sua apresentação) e
  controllers (código responsável pela interação com usuário).sso também é chamado
  *A separação das preocupações*. Com o MVC, você pode *reutilizar* seus componentes.
  Também é fácil substituir qualquer parte desses três. Por exemplo, você pode facilmente substituir uma exibição por outra, sem
  mudando sua lógica de negócios.

* *Domain Driven Design (DDD) pattern*. In Laminas Framework, by convention, you will have model
  layer further divided into *entities* (classes mapped on database tables),
  *repositories* (classes used to retrieve entities from database),
  *value objects* (model classes not having identity),
  *services* (classes responsible for business logic).

* *Aspect Oriented Design pattern.*No Laminas, tudo é orientado a eventos.
  Quando um usuário do site solicita uma página, um *evento* é gerado (acionado). Um Listener (ou observador) pode
  pegar evento e fazer alguma coisa com ele. Por exemplo, o componente, @`Laminas\Router` analisa a
  URL e determina qual controller deve chamar Quando o evento finalmente chega à página
  renderizador, uma resposta HTTP é gerada e o usuário vê a página da web.

* *Padrão Singleton.* No Laminas, existe um objeto que gerenciador de serviços (service manager) é o container que centraliza
   todos os serviços disponíveis no aplicativo. Cada serviço existe apenas para uma única instância.

* *Strategy pattern.* Uma estratégia é apenas uma classe que encapsula algum algoritmo.
   E você pode usar algoritmos diferentes com base em alguma condição. Por exemplo,
   o processador de páginas tem várias estratégias de renderização,
   tornando possível renderizar páginas da Web de forma
   (o renderizador pode gerar uma página HTML, uma resposta JSON, um feed RSS etc.)

* *Adapter pattern.*  Os adaptadores permitem adaptar uma classe genérica.
   Por exemplo, o componente @ `Laminas\Db` fornece acesso ao banco de dados de maneira genérica.
   Internamente, ele usa adaptadores para cada banco de dados suportado (SQLite, MySQL, PostgreSQL e assim por diante).

* *Factory pattern.*  Você pode criar uma instância de uma classe usando o operador `new`. Ou você pode criá-lo com uma factory.
   Uma factory é apenas uma classe que encapsula a criação de outros objetos
   Factories são úteis porque simplificam a injeção de dependência. Usando factory também
   simplifica o teste do seu modelo e das classes do controlador.

## Principais componentes Laminas

Os desenvolvedores da Laminas acreditam que o framework deve ser um conjunto de componentes desacoplados
com dependências mínimas uns dos outros.
É assim que o Laminas é organizado.

A ideia era permitir que você usasse apenas alguns componentes do Laminas,
mesmo se você escrever seu site com outro framework.Isso se torna ainda mais fácil,
tendo em mente que cada componente do Laminas é um pacote instalável através do Composer,
para que você possa instalar facilmente qualquer componente Laminas junto com suas dependências através de um único comando

Existem vários componentes "essenciais" do Laminas que são usados ​​(explícita ou implicitamente)
em quase todo applicativo web:

  * @`Laminas\EventManager`. Componente permite enviar eventos e registrar os listeners para reagir a eles.

  * @`Laminas\ModuleManager`. No Laminas, cada aplicativo consiste em módulos e este componente contém
     funcionalidade de carregar os módulos.

  * @`Laminas\ServiceManager`. Este é o registro central de todos os serviços disponíveis no aplicativo,
      possibilitando o acesso a serviços de qualquer ponto do site.

  * @`Laminas\Http`. Fornece uma interface fácil para executar solicitações de protocolo
   de transferência de hipertexto (HTTP).

  * @`Laminas\Mvc`. Suporte do padrão Model-View-Controller e separação da lógica de negócios da apresentação.

  * @`Laminas\View`. Fornece helpers, filtros de saída variável. Usado na camada de apresentação(View).

  * @`Laminas\Form`. Coleta, filtragem, validação e renderização de dados de formulários.

  * @`Laminas\InputFilter`. Fornece a capacidade de definir regras de validação de dados do formulário.

  * @`Laminas\Filter`. Fornece um conjunto de filtros de dados comumente usados.

  * @`Laminas\Validator`. Fornece um conjunto de Validadores & Inputs geralmente usados.

## Diferenças do Laminas Framework 2

Para os leitores que têm uma experiência no Laminas Framework 2, nesta seção
Temos algumas informações sobre o que mudou no Laminas Framework.

Abaixo, são apresentadas as principais diferenças técnicas entre ZF2 e Laminas:

### Compatibilidade com versões anteriores

Laminas é a mais nova versão lançada, portanto, a compatibilidade com versões anteriores é preservada na maioria dos casos.
Porém se você usou `ServiceLocatorAwareInterface` terá certo trabalho para fazer a migração do seu código.
No Laminas, essa interface foi removida e agora todas as dependências devem ser injetadas por meio de factories.
Sendo assim, você terá que criar factories para a maioria de seus controllers, services, view helpers e plugin dos controllers.

### Componentes

No ZF2, os componentes foram armazenados em um único repositório GIT.
No Laminas, os componentes são armazenados em vários repositórios do GIT, um repositório por componente
(por exemplo, `laminas/laminas-mvc`, `laminas/laminas-servicemanager`, `laminas/laminas-form`, etc).
Isso permite desenvolver e liberar componentes independentemente um do outro.

Os componentes são ainda mais desacoplados do que antes e têm dependências mínimas uns dos outros. Componente @`Laminas\Mvc`
foi dividido em vários outros. Por exemplo, a funcionalidade de roteamento foi movida para o novo componente  @`Laminas\Router`.

Agora é recomendado que você especifique individualmente os componentes dos quais seu aplicativo depende no seu `composer.json`,
embora ainda seja possível depender do pacote laminas/laminas, que é um pacote que instala *todos* componentes disponíveis.

### Instalador de Componentes

No Laminas, um plugin especial do Composer foi introduzido, chamado *component installer* . Que permite instalar
componentes como módulos ZF. Ele injeta informações no componente e na configuração do aplicativo
Arquivo.

### Performance do ServiceManager e EventManager

Os desenvolvedores do Laminas fizeram um ótimo trabalho melhorando o desempenho dos componentes @`Laminas\ServiceManager` e @`Laminas\EventManager`.
Eles são agora estão muito mais rápidos do que antes. A desvantagem você pode ter certo trabalho para fazer a migração.
Para Controllers e Services são recomendados para utilizar um recurso disponivel no PHP 5.5 chamado de `::class`.
Por exemplo, se anteriormente você registrou seu controller como `Application\Controller\Index`, agora você vai registrá-lo como
`IndexController::class`.
Se anteriormente você registrou nomes de serviço como desejava, recomendamos que faça isso usando `ServiceClassName::class`.
Leia a documentação do componente `Mvc` para maiores informações.

### PSR-4

No ZF2, a estrutura era PSR-0, enquanto no Laminas é PSR-4.
Isso requer alguns(pequenos) trabalhos de migração.

### Middleware

Laminas acredita que o futuro do PHP está no middleware.
"O middleware é, simplesmente, um código entre uma solicitação HTTP de entrada e a resposta HTTP de saída."

### Foco na Documentação

Cada componentes contém sua própria documentação.
A documentação está agora em um  formato melhor e se tornou melhor projetado.

## Resumo

Um Framework em PHP é uma biblioteca, fornecendo a base de código e definindo maneiras consistentes
de criar aplicações Web. O Laminas Framework é um framework moderno criado pela Laminas Technologies,
o fornecedor de linguagem PHP. Ele fornece aos desenvolvedores recursos excepcionais para
criar sites da modernos ​​e seguros. O Laminas é licenciado sob licença do tipo BSD e pode ser usado gratuitamente
em aplicativos comerciais e de código aberto.