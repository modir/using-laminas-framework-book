# Laminas Skeleton Application {#skeleton}

O Laminas Framework fornece o que chamamos de "Skeleton Application" uma aplicação padrão que facilitar a criação dos
seus novos sites a partir do zero. Neste capítulo, vamos mostrar como instalar o skeleton
application e como criar um Apache virtual host.É recomendável antes de começarmos esse capítulo que seja feita a
leitura do [Appendix A. Configuring Web Development Environment](#devenv) onde ensina como deixar o ambiente
desenvolvimento configurado e pronto para o desenvolvimento.

## Iniciando com Laminas Skeleton Application

O Skeleton Application é um aplicativo simples baseado em Laminas que traz o
necessário para criar seu próprio site.

O código do  skeleton application's está disponível no GitHub e pode ser acessado através
deste [link](https://github.com/laminas/LaminasSkeletonApplication).
Porém, Geralmente não é feito o download diretamente do seu código-fonte é recomendando
que seja utilizado o [Composer](http://getcomposer.org/) como mostramos abaixo.

Antes de tudo, você precisa ter a versão mais recente do Composer.
Você consegue fazer isso através dos seguintes comandos:

```
cd ~

php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"

php composer-setup.php

php -r "unlink('composer-setup.php');"
```

Os comandos acima mudam sua pasta o home, e baixa o script para a instalação `composer-setup.php`
para sua pasta, execute-o e, após isso, remova o instalador.

T> Após executar esse comando, você deve encontrar um arquivo com o nome `composer.phar`, na pasta onde realizou instalação.

Agora, digite o seguinte comando no seu terminal:

```
php composer.phar create-project -sdev laminas/skeleton-application helloworld
```

O comando acima faz o download do Laminas Skeleton Application na pasta `helloworld` e executa seu
instalador interativo. Agora você vai precisar responder várias perguntas sim/não digitando `y` ou `n` e pressionando Enter.
Suas respostas vão determinar quais dependências ele deve instalar.
Se você não souber o que responder, responda `n` (nao); você poderá instalar dependências novamente
mais tarde a qualquer momento.

Para começar, você pode responder as perguntas da seguinte maneira:

```
    Do you want a minimal install (no optional packages)? Y/n
n

    Would you like to install the developer toolbar? y/N
n

    Would you like to install caching support? y/N
n

    Would you like to install database support (installs laminas-db)? y/N
n

    Would you like to install forms support? y/N
y
    Will install laminas/laminas-mvc-form (^1.0)
    When prompted to install as a module, select application.config.php or modules.config.php

    Would you like to install JSON de/serialization support? y/N
n

    Would you like to install logging support? y/N
n

    Would you like to install MVC-based console support? (We recommend migrating to zf-console, symfony/console, or Aura.CLI) y/N
n

    Would you like to install i18n support? y/N
n

    Would you like to install the official MVC plugins, including PRG support, identity, and flash messages? y/N
n

    Would you like to use the PSR-7 middleware dispatcher? y/N
n

    Would you like to install sessions support? y/N
n

    Would you like to install MVC testing support? y/N
n

    Would you like to install the laminas-di integration for laminas-servicemanager? y/N
n
```

Após você responder as perguntas, o instalador vai baixar e instalar todos os pacotes e vai perguntar
em qual arquivo de configuração você gostaria de injetar as informações referente aos módulos instalados. Quando solicitado,
digite '1' e pressione Enter:

```
 Please select which config file you wish to inject 'Laminas\Form' into:
  [0] Do not inject
  [1] config/modules.config.php
  [2] config/development.config.php.dist
  Make your selection (default is 0):1

  Remember this option for other packages of the same type? (y/N) y
```

A proxima perguntar,  o prompt irá perguntar se você deseja remover os arquivos de controle de versão do projeto.
Como provavelmente você usará o sistema a sua escolha (como o Git) e não vai precisar de arquivos
VCS, digite `y` e pressione Enter:

```
Do you want to remove the existing VCS (.git, .svn..) history? [Y,n]? y
```

Agora copie `composer.phar` para a sua nova pasta `helloworld`:

```
cp composer.phar helloworld
```

E o ultimo passo que devemos seguir é ativar *development mode* digitando seguinte comando:

~~~
cd helloworld
php composer.phar development-enable
~~~

I> O modo de desenvolvimento é geralmente usado quando você *desenvolvendo* sua aplicação. Quando você ativa
I> o modo de desenvolvimento arquivos de configuração adicionais são criados na pasta config. Dessa maneira
I> sua aplicação pode carregar módulos adicionais de "desenvolvimento". O cache é
I> desativado nesta configuração disponiblizando que seja possivel ver qualquer alteração de imediato.
I>
I> Quando terminar de desenvolver, você pode ativar o modo de produção com seguinte código.
I>
I> `php composer.phar development-disable`

Parabéns!  Com isso terminamos a instalação. Agora vamos olhar dentro  da pasta `helloworld`.

## Estrutura típica dos diretórios

Todo site feito em Laminas (incluindo o skeleton application) é organizado da maneira recomendada.
Claro, você pode configurar seu aplicativo para usar um layout de uma pasta diferente, mas isso pode
dificultar o suporte ao seu site por outras pessoas que não estão familiarizadas com essa estrutura de
diretórios.

Vamos dar uma olhada na estrutura de pastas padrão (veja a imagem 2.1):

![Imagem 2.1. Estrutura Padrão de Pastas](../en/images/skeleton/skeleton_dir_structure.png)

Como você pode ver a nossa pasta principal (que vamos chamar a partir de agora de `APP_DIR`),
tem os seguintes arquivos:

* `composer.json` é um arquivo JSON com as configurações do Composer.

* `composer.lock` este arquivo contém informações sobre os pacotes instalados com o Composer.

* `composer.phar` é um arquivo PHP executável contendo o código
  do Composer.

* `docker-compose.yml`  e `Dockerfile` arquivos auxiliares somente se você utilizar do [Docker](https://www.docker.com) uma ferramenta que gerencia containers.
Neste livro nós não iremos abordar a utilização do Docker.

* `LICENSE.md` é um arquivo de texto que contem os dados da licença do Laminas (Também disponível em
 [Introdução ao Laminas Framework](#intro)).
 Não remova ou modifique esse arquivo, pois a licença do  Laminas, não permite esse tipo de alteração.

* `phpunit.xml.dist` é um arquivo com a configuração do PHPUnit [PHPUnit](https://phpunit.de/)). Você pode utilizar este
  arquivo para efetuar testes no seu site.

* `README.md` é um arquivo de texto que contem uma breve descrição do skeleton application.
  Você pode substituir por um arquivo que contenha informações sobre seu site.

* `TODO.md` é um arquivo que pode ser removido sem problemas.

* `Vagrantfile` um arquivo que contém a configuração do [Vagrant](https://www.vagrantup.com/).
   Você pode ignorar esse arquivo se não souber o que é o Vagrant. Neste livro, não usamos o Vagrant.

E também temos as sub-pastas:

Na pasta `config`  encontra-se a configuração do aplicativo.

Na pasta `data`  contém os dados que seu aplicativo pode criar; também contem o cache do Laminas Framework
geralmente utilizado para aumentar a velocidade do Laminas.

Na pasta `module` contém todos os módulos da sua aplicação. Nesse primeiro momento existe um único
módulo chamado `Application`.
O `Application` é o principal módulo do seu site.
Você pode adicionar outros módulos se quiser.

O assunto de módulos será tratado nós proximos capitulos.

A pasta `vendor` contém blibiotecas que foram adquiridas através da internet, incluindo
os arquivos das blibiotecas do Laminas Framework. Esta pasta geralmente é somente preenchida pelo o Composer.

O diretório `public` contém dados publicamente acessíveis pelo usuário da web.Como você pode ver, os usuários
irá se comunicar com o `index.php`, que também é chamado de * ponto de entrada * do seu site.

Na pasta `public` contém os dados acessíveis pelo o usuário.
Como você pode ver os usuários irão se comunicar principalmente com o `index.php`, que também é o ponto de entrada do seu site.

I>Seu site terá um único ponto de acesso, através do *index.php*, porque isso é mais seguro do que permitir
I>qualquer pessoa acesse os seus arquivos PHP.

Dentro da pasta `public`, você vai encontrar o arquivo `.htaccess` oculto. Seu principal objetivo é definir
Regras de acesso a URL do seu site.

Na pasta `public` contém varias sub-pastas que estão disponiveis publicamente para acesso dos usuários:

* `css` nessa pasta contém todos os arquivos CSS públicos e acessíveis do seu site;
* `fonts` nessa pasta contém todos os arquivos fontes públicos e acessíveis do seu site;
* `img` nessa pasta conteḿ todas as imagem públicas do seu site (*.JPG, *.PNG, *.GIF, *.ICO, etc.);
* `js` nessa pasta contém todos os arquivos JS públicos e acessíveis do seu site;
  Normalmente, os arquivos do [jQuery](http://jquery.com/) são colocados aqui,
  mas você também pode colocar seus próprios arquivos JavaScript também.

Q> **O que é uma blibioteca Jquery?**
Q>

Q> jQuery é uma biblioteca JavaScript que foi criada para simplifica o HTML das
Q> páginas.O mecanismo do jQuery permite manipular eventos a certos elementos HTML, tornando
Q> é muito simples fazer suas páginas HTML interativas.

Como o Laminas Skeleton Application é armazenado no GitHub, dentro das pastas você,
você pode encontrar o arquivo `.gitignore`. Este arquivo é um controle de versão [GIT] (http://git-scm.com/)
de arquivos.Você pode ignorá-lo (ou até mesmo remover se não planeja armazenar seu código em um repositório GIT).

## Dependências de aplicativos

Uma dependência é um código de terceiros que seu aplicativo usa.
Por exemplo, o Laminas Framework é a dependência do seu site.

No Composer, qualquer biblioteca é chamada de *pacote*. Todos os pacotes instaláveis ​​pelo Composer
estão registrados no site [Packagist.org] (https://packagist.org/).
Com o Composer, você pode identificar os pacotes que o seu aplicativo requer e baixá-los e instalá-los automaticamente.

As dependências do skeleton application estão no arquivo `APP_DIR/composer.json` (veja abaixo):

{line-numbers=off,lang=text, title="Contents of composer.json file"}
~~~
{
    "name": "laminas/skeleton-application",
    "description": "Skeleton Application for Laminas Framework laminas-mvc applications",
    "type": "project",
    "license": "BSD-3-Clause",
    "keywords": [
        "framework",
        "mvc",
        "zf2"
    ],
    "homepage": "http://framework.Laminas.com/",
    "minimum-stability": "dev",
    "prefer-stable": true,
    "require": {
        "php": "^5.6 || ^7.0",
        "laminas/laminas-component-installer": "^1.0 || ^0.3 || ^1.0.0-dev@dev",
        "laminas/laminas-mvc": "^3.0.1",
        "zfcampus/zf-development-mode": "^3.0",
        "laminas/laminas-mvc-form": "^1.0",
        "laminas/laminas-mvc-plugins": "^1.0.1",
        "laminas/laminas-session": "^2.7.1"
    },
    "autoload": {
        "psr-4": {
            "Application\\": "module/Application/src/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "ApplicationTest\\": "module/Application/test/"
        }
    },
    "extra": [],
    "scripts": {
        "development-disable": "zf-development-mode disable",
        "development-enable": "zf-development-mode enable",
        "development-status": "zf-development-mode status",
        "serve": "php -S 0.0.0.0:8080 -t public/ public/index.php"
    }
}
~~~

Q> **O que é JSON?**
Q>
Q>JSON (JavaScript Object Notation), é um formato de arquivo baseado em texto usado para legível para humanos
Q>com uma estrutura de arrays aninhados. Embora JSON
Q>tenha originado do JavaScript, é usado em PHP e em outras linguagens, porque
Q>é conveniente para armazenar dados de configuração.

Nesse arquivo, vemos algumas informações básicas sobre o  skeleton application (seu nome,
descrição, licença, keywords e home page). Você normalmente irá alterar esta informação para o seu
websites. Esta informação é opcional, então você pode até removê-la ,
se você não desejar publicar sua aplicação no `Packagist`.

O que é interessante nesta imagem para nós agora é a palavra `require`. O `require`
contém as dependências para nosso aplicativo. Essa keyword só funciona a partir do
PHP versão 5.6 ou posterior e funciona com vários componentes do Laminas Framework, como `laminas-mvc`,` laminas-mvc-form`, etc.

As informações contidas no arquivo `composer.json` são suficientes para localizar as
dependências, baixar e instalar nos subdiretório `vendor`. A qualquer momento que você precisa
você pode instalar outra dependência, você pode fazer isso editando o `composer.json` e adicionando sua dependência nele,
e, em seguida, digitando os seguintes comandos no seu terminal de comando:


{line-numbers=off}
~~~
php composer.phar self-update
php composer.phar install
~~~

Os comandos acima atualizarão o Composer para a última versão disponível e, em seguida,
vai instalar suas dependências. A propósito, o Composer não instala o PHP para você,
ele apenas garente que PHP tenha uma versão apropriada, e se não, ele irá avisá-lo diLaminaso que a versão não é compatível.

Se você olhar dentro da sub-pastas do `vendor`, você verá que ele contém muitos arquivos dos pacotes instalados.

Os arquivos do Laminas Framework podem ser encontrados dentro do `APP_DIR/vendor/laminas/`
(Imagem 2.2).

![Imagem 2.2. Pasta Vendor](../en/images/skeleton/vendor_dir.png)

I> Em outros frameworks, outra forma (convencional) de instalação de instalação de dependências é usada.
I> Onde você baixa o arquivo da blibioteca que você vai utilizar descompacta e coloca em algum lugar da sua pastas
I> (geralmente, vai para a pasta `vendor`). Essa abordagem foi usada no Laminas Framework 1.
I> Mas, no Laminas Framework, é recomendável instalar dependências com o Composer.

## Apache Virtual Host

Agora estamos quase prontos para colocarmos no ar o skeleton application!   A última coisa
que vamos fazer é configurar virtual host Apache. Um virtual host significa
que você pode executar vários sites na mesma máquina. Os sites são diferenciados pelo
o nome do domínio (por exemplo `site.meudominio.com` e `site2.meudominio.com`) ou
pelo número da porta  (como `localhost` e `localhost:8080`). O virtual hosts funcionam de maneira
transparente, isso significa que os usuários não têm idéia se os sites estão na mesma maquina ou em outra.

Atualmente, temos o skeleton application dentro do seu computador. Para configurar o Apache
precisamos editar o arquivo do virtual host.

I>O arquivo host virtual pode estar localizado em uma pasta diferente, dependendo do seu tipo de sistema operacional.
I>Por exemplo, no Linux Ubuntu ele está localizado no arquivo `/etc/apache2/sites-available/000-default.conf`.
I>Para informações específicas de cada sistema operacional e de virtual hosts, consulte [Appendix A. Configuring Web Development Environment](#devenv).

Vamos agora editar o arquivo padrão do virtual host para que fique parecido com o arquivo abaixo (supomos que você esteja usando o Apache v2.4):

{line-numbers=on,lang=text, title="Virtual host file"}
~~~
<VirtualHost *:80>
    ServerAdmin webmaster@localhost

    DocumentRoot /home/username/helloworld/public

	<Directory /home/username/helloworld/public/>
        DirectoryIndex index.php
        AllowOverride All
        Require all granted
    </Directory>

</VirtualHost>
~~~

A linha 1 do arquivo faz com que o Apache veja todos os endereços IP (*) na porta 80.

A linha 2 define o endereço de e-mail do webmaster. Se algo de ruim acontece com o
site, o Apache envia um e-mail de alerta para esse endereço. Você pode digitar seu e-mail
Aqui.

A linha 4 define o diretório raiz do documento (`APP_DIR/public`). Todos os arquivos e diretórios
sob a raiz do documento serão acessíveis por usuários da web. Você deve definir
este é o caminho como absoluto para o diretório `public` do skeleton application's.
Então, os diretórios e arquivos dentro de `public` (como` index.php`, `css`,` js`, etc.)
estará acessível, enquanto diretórios e arquivos acima do diretório `public` (como
`config`,` module`, etc.) não serão acessíveis por usuários da web, o que aumenta a
segurança do site.

As linhas 6 a 10 definem regras para o diretório raiz do documento (`APP_DIR/public`). Por exemplo, o `DirectoryIndex`
informa ao Apache que *index.php* deve ser como o index padrão.O  `AllowOverride All`
permite definir qualquer regra em arquivos `.htaccess`.


W> Laminas Framework utiliza o mod rewrite para reescrever a URL do Apache e para redirecionar
W> os usuários para o script do seu site. Cerifique-se que seu servidor tem o
W> `mod_rewrite` habilitado. Para obter instruções sobre como ativar, por favor
W> consulte [Appendix A. Configuring Web Development Environment](#devenv).

T> Depois de editar o arquivo de configuração, não esqueça de reiniciar o Apache para verificar suas alterações.

## Abrindo o site no seu navegador

Para abrir o site, digite "http://localhost" na barra de navegação do seu navegador e pressione Enter.
Imagem 2.3 Mostra seu site em funcionamento.

Na página que apareceu, você pode ver o menu de navegação na parte superior. A barra de navegação atualmente
contém o único link chamado *Home*.
Sob a barra de navegação, você pode ver a legenda "Welcome to Laminas Framework". Abaixo
a legenda, você pode encontrar algumas dicas para os iniciantes sobre como desenvolver aplicações em Laminas.

![Imagem 2.3. Laminas Skeleton Application](../en/images/skeleton/Laminas_skeleton_default_view.png)

## Criando um Projeto no NetBeans

Agora que temos o skeleton application configurado e funcionando, Nós iremos realizar alterações no site.
Para navegar facilmente pela estrutura de pastas, editar arquivos e depurar o site,
a prática comum é usar um IDE (Integrated Development Environment). Neste livro, nós
vamos usar o NetBeans IDE (consulte [Appendix A. Configuring Web Development Environment](#devenv) para maiores informações sobre como instalar o NetBeans).

Para criar um projeto no Netbeans para a nossa skeleton application, execute o NetBeans e abra o menu
*File->New Project...*. Será aberto uma janela com o nome  *New Project* (veja a imagem  2.4).

![Imagem 2.4. Criando um Projeto no Netbeans - Escolhendo Qual Tipo de Projeto](../en/images/skeleton/netbeans_create_project.png)

Na tela de *Choose Project* que apareceu, você deve escolher o tipo de projeto PHP
e selecione *Application with Existing Sources* (porque já temos o código do skelleton application).
Em seguida, clique no botão *Next* para ir para a próxima tela
(mostrado na imagem 2.5).

![Imagem 2.5. Criando um Projeto no Netbeans - Nome e Localização](../en/images/skeleton/netbeans_create_project_step2.png)


Na página de diálogo * Nome e localização *, você deve digitar o caminho para o código (como * / home / username / helloworld *),
o nome do projeto (por exemplo, `helloworld`) e especifique a versão do PHP que seu código usa (PHP 5.6 ou posterior).
A versão do PHP é necessária para o verificador de sintaxe do NetBeans, que verificará seu código PHP em busca de erros e
destaque-os. Pressione o botão * Next * para ir para a próxima página de diálogo (mostrada na imagem 2.6).]

Na parte de  *Name and Location*, você deve digitar o caminho para o código (como */home/username/helloworld*),
e o nome para o projeto (por exemplo, `helloworld`) você também deve especificar a versão do seu PHP (PHP 5.6 or superior).
A versão do PHP é necessária para o verificador de sintaxe do NetBeans, que verificará seu código PHP em busca de erros.
Pressione o botão  *Next* para ir para proxíma imagem (mostrado na imagem 2.6).

![Imagem 2.6. Criando um Projeto no Netbenas - Escolhendo a Página de Configuração](../en/images/skeleton/netbeans_create_project_step3.png)

Na tela *Run Configuration*,é recomendável especififcar a maneira que vai executar o seu site (Local Web
Site) também definindo URL (`http://localhost`). Mantendo o *Index File* vazio (como utilizamos o `mod_rewrite`,
o caminho atual do seu  `index.php`  é oculto pelo o apache). Se você ver uma mensagem de aviso como
"Index File must be specified in order to run or debug project in command line", apenas ignore.


Clique no botão *Finish* para criar o projeto. O projeto *helloworld*
foi criado com sucesso, você deve ver a Aba do projeto (veja a imagem 2.7).

![Imagem 2.7. Aba do Projeto do Netbeans](../en/images/skeleton/netbeans_project_window.png)

Na janela do projeto, você pode ver a barra de menus, a barra de ferramentas,
o painel *Projects*, onde seus arquivos do seu projeto estão listados, e, à direita,
você pode ver o código do `index.php`.

Por favor, consulte o [Appendix B. Introduction to PHP Development in NetBeans IDE](#netbeans)
para mais dicas de uso do NetBeans, incluindo utlização e depuração de
Sites baseados em Laminas.

T> ** É hora de algumas coisas avançadas ... **
T>
T> Parabéns! Nós fizemos o trabalho duro de instalar e executar
T> o Laminas Skeleton Application, e agora é hora de descansar
Vamos ler sobre algums assuntos avançados na última parte deste capítulo.

## Arquivo de Hypertext Access (.htaccess)

Nós mencionamos o arquivo `APP_DIR/public/.htaccess` quando falamos sobre
estrutura de pastas. Agora vamos tentar entender o papel desse arquivo.

O arquivo `.htaccess` (hypertext access) é na verdade arquivo de configuração do servidor web Apache
que permite sobrescrever a configuração global do servidor web.
O arquivo `.htaccess` é uma configuração no nível de diretórios, o que significa que
afeta apenas seu diretório próprio e todos os subdiretórios.

O conteúdo do arquivo `.htaccess` é apresentado abaixo:

~~~text
RewriteEngine On
# The following rule tells Apache that if the requested filename
# exists, simply serve it.
RewriteCond %{REQUEST_FILENAME} -s [OR]
RewriteCond %{REQUEST_FILENAME} -l [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^.*$ - [L]
# The following rewrites all other queries to index.php. The
# condition ensures that if you are using Apache aliases to do
# mass virtual hosting or installed the project in a subdirectory,
# the base path will be prepended to allow proper resolution of
# the index.php file; it will work in non-aliased environments
# as well, providing a safe, one-size fits all solution.
RewriteCond %{REQUEST_URI}::$1 ^(/.+)/(.*)::\2$
RewriteRule ^(.*) - [E=BASE:%1]
RewriteRule ^(.*)$ %{ENV:BASE}/index.php [L]
~~~

A linha 1 informa aoApache para habilitar o mecanismo de reescrita de URL (`mod_rewrite`).
O mecanismo de reescrita modifica as solicitações de URL recebidas, com base na regular expressions.
Isso permite que você mapeie URLs arbitrárias em sua estrutura de URL interna da maneira que desejar.

As linhas 4 a 7 definem as regras de rewrite que informam ao servidor da Web que, se o cliente (navegador da Web)
solicita um arquivo que existe no diretório raiz do documento, do que para retornar o conteúdo desse diretório
arquivo como resposta HTTP. Porque nós temos nosso diretório `public` dentro da raiz de documentos do host virtual,
permitimos que os usuários do site vejam todos os arquivos dentro do diretório `public`, incluindo` index.php`,
Arquivos CSS, arquivos JavaScript e arquivos de imagem.

As linhas 14 a 16 definem regras do rewrite  informam ao Apache o que fazer se o usuário do site solicitar um arquivo
que não existe na raiz do documento. Nesse caso, o usuário deve ser redirecionado para `index.php`.

A Tabela 2.1 contém vários exemplos de rewrite de URL. Os primeiros e segundos URLs apontam para
arquivos, então `mod_rewrite` retorna os caminhos de arquivos solicitados. O URL no terceiro exemplo
aponta para um arquivo inexistente `htpasswd` (que pode ser um sintoma de um ataque de hacker),
e com base em nossas regras de reescrita, o mecanismo retorna o arquivo `index.php`.

{title="Table 2.1. Exemplos de URL rewrite"}
|-------------------------------------|-----------------------------------------|
| **URL Solicitada*                   | **URL Reescrita**                       |
|-------------------------------------|-----------------------------------------|
| `http://localhost/index.php`        | O arquivo existe, retorna para arquivo  |
|                                     | local                                   |
|                                     | `APP_DIR/public/index.php`              |
|-------------------------------------|-----------------------------------------|
| `http://localhost/css/bootstrap.css`| O arquivo existe, retorna para arquivo  |
|                                     | local                                   |
|                                     | `APP_DIR/public/css/bootstrap.css`      |
|-------------------------------------|-----------------------------------------|
| `http://localhost/htpasswd`         | Arquivo não existe; retorna             |
|                                     | `APP_DIR/public/index.php`              |
|-------------------------------------|-----------------------------------------|

##  Bloqueando o acesso ao site pelo endereço IP


Às vezes, pode ser necessário bloquear o acesso ao seu site de todos os outros endereços IP, exceto o seu.
Por exemplo, quando você desenvolve um site, não quer que alguém veja seu trabalho incompleto. Além disso,
talvez você não queira permitir que o Google ou outros mecanismos de pesquisa indexem seu website.

Para proibir o acesso ao seu site, você pode alterar o virtual host e adicionar a seguinte linha a ele:

~~~text
Require ip <your_ip_address>
~~~

Q> **Como eu descubro o meu endereço de IP?**
Q>
Q> Você pode usar o site [http://www.whatismyip.com](http://www.whatismyip.com/) para saber
Q> seu endereço IP externo. O endereço IP externo é o endereço pelo qual outros
Q> computadores na Internet podem acessar seu site.

## Autenticação HTTP


Você pode permitir o acesso ao seu site para determinados usuários. Por exemplo, quando você está
mostrando o seu site para o seu chefe, você vai dar a ele um usuário e senha para
fazer login no seu site.

Para permitir o acesso ao seu site por nome de usuário e senha, você pode modificar o arquivo do virtual host
da seguinte forma:

~~~text
...
<Directory /home/username/helloworld/public/>
    DirectoryIndex index.php
    AllowOverride All
    AuthType Basic
    AuthName "Authentication Required"
    AuthUserFile /usr/local/apache/passwd/passwords
    Require valid-user
</Directory>
...
~~~


A linha 5 define o método de autenticação básica. O método mais
Básico É importante esteja ciente, no entanto, que a autenticação básica envia
a senha do cliente para o servidor sem criptografia. Este método não deve,
portanto, ser usado para dados sensíveis. O Apache suporta uma outra autenticação
método: `AuthType Digest`. Este método é muito mais seguro.
Os navegadores mais recentes suportam a autenticação Digest.


A linha 6 define o texto que será exibido ao usuário quando ele tentar efetuar login.

A linha 7 define o arquivo onde as senhas serão armazenadas. Este arquivo deve ser criado
com o `htpasswd`.

A linha 8 permitirá que qualquer pessoa faça o login listando no arquivo, para aqueles
que digitarem sua senha corretamente.

Para criar o arquivo `passwords`, digite o seguinte comando:

~~~
htpasswd -c /usr/local/apache/passwd/passwords <username>
~~~

No comando acima, você deve substituir o espaço `<username>`
com o nome do usuário. Você pode escolher um nome, por exemplo "admin".
O comando solicitará a senha do usuário e vai salvar a senha no arquivo:


~~~text
# htpasswd -c /usr/local/apache/passwd/passwords <username>
New password:
Re-type new password:
Adding password for user <username>
~~~

Quando o usuário tenta visitar o site, ele vai ver uma mensagem solicitando a autenticação HTTP.
Para entrar em seu site, o visitante deve digitar o nome de usuário e senha corretos.

I> Para informações adicionais sobre autenticação HTTP, você pode consultar na documentação do
I> Apache [Autenticação e Autorização](http://httpd.apache.org/docs/current/howto/auth.html)

## Tendo vários Virtual Hosts

Ao desenvolver vários sites na mesma máquina, você pode querer criar
vários virtual hosts. Para cada virtual host, você precisa especificar um  domínio (como `site1.mydomain.com`).
Mas se você atualmente não tem um nome de domínio, você pode especificar uma porta diferente
(veja o exemplo abaixo).

~~~text
# Listen directive tells Apache to listen requests on port 8080
Listen 8080

<VirtualHost *:8080>
    ...
</VirtualHost>
~~~

Para acessar o site, na barra de navegação do seu navegador, digite "http://localhost:8080".

T> Após editar o arquivo do virtual host, você deve reiniciar o Apache para testar as alterações.

## Hosts File

Quando você tem vários sites mapeados em  diferentes portas, torna-se difícil
para lembrar em qual porta cada site está. Para simplificar isso, você pode usar o nome baseado
virtual host e definir um alias para o seu site no arquivo `hosts` do seu sistema.

Primeiro, altere o Arquivo do Apache virtual host para que possa usar host virtual *com nome*:

~~~text
<VirtualHost *:80>
    # Add the ServerName directive
	ServerName site1.localhost
	...
</VirtualHost>
~~~

Em seguida, você deve editar o arquivo `hosts`. O arquivo `hosts` é um arquivo de sistema
que contém mapeamentos entre endereços IP e nomes de host. O arquivo hosts contém
linhas de texto que consiste em um endereço IP no primeiro campo de texto seguido por um ou
mais nomes do host.

Para adicionar um alias do seus sites, adicione uma linhas para cada um de seu website como
mostrado no exemplo abaixo.

~~~text
127.0.0.1            site1.localhost
~~~

Então agora você pode simplesmente digitar "site1.localhost" na barra de endereço do seu navegador
em vez de digitar o endereço com a porta.

I> No Linux, o arquivo hosts está localizado em `/etc/hosts`.
I> No Windows, o arquivo está normalmente em `C:\Windows\System32\drivers\etc\hosts`.
I> Para editar o arquivo, você precisa ser um administrador. Atenção que alguns
I> software anti-vírus podem bloquear as alterações no arquivo hosts, então você terá que desativar temporariamente
I> seu antivírus para editar o arquivo e ativá-lo depois.

I> Se você comprou um nome de domínio real para o seu site (como `example.com`), você não
I> precisa modificar o seu arquivo `hosts`, porque o Apache será capaz de resolver o endereço IP do
I> seu site usando o DNS. Você modifica seu arquivo `hosts` somente quando o DNS não sabe
I> sobre o domínio e não consegue resolver o endereço IP do seu site.

## Uso Avançado do Composer

Anteriormente neste capítulo, usamos o Composer para instalar as biblioteca do Laminas Framework.
Agora vamos mostrar brevemente alguns exemplos avançados de uso do Composer.

Como já sabemos, a única chave obrigatória no arquivo `composer.json` é a `require`. Esta chave
informa quais pacotes são necessários para a sua aplicativo:


~~~text
{
    "require": {
        "php": "^5.6 || ^7.0",
        "laminas/laminas-component-installer": "^1.0 || ^0.3 || ^1.0.0-dev@dev",
        "laminas/laminas-mvc": "^3.0.1",
        "zfcampus/zf-development-mode": "^3.0",
        "laminas/laminas-mvc-form": "^1.0",
        "laminas/laminas-mvc-plugins": "^1.0.1",
        "laminas/laminas-session": "^2.7.1"
    }
}
~~~

### Nomes de Pacotes & Versões


Um pacote consiste em duas partes: o nome do vendor e o nome do projeto. Por exemplo
O nome do pacote "laminas/laminas-mvc" consiste no nome do vendor "laminas"
e o nome do projeto "laminas-mvc". Você pode procurar outros pacotes de "laminas"
através do site [Packagist.org](https://packagist.org/search/?q=laminas)
(veja a imagem 2.8 para um exemplo).

![Imagem 2.8. Você pode pesquisar pacotes no Packagist.org](../en/images/skeleton/packagist_search.png)


Um pacote também possui um número de versão associada a ele. Um número de versão consiste em um número maior ou menor
número de compilação. Dentro da chave `require` podemos especificamos qual versões do pacote são desejamos.
Por exemplo, se digitarmos "^5.6" significa que podemos instalar versões maiores que "5.6", e menores que "6.0"
(Caso quisermos podemos instalar somente aqueles pacotes que não afetem a compatibilidade com versões anteriores).
Na tabela 2.2, são apresentadas possíveis maneiras de especificar versões:


{title="Tabela 2.2. Definições de versão dos pacotes"}
|-------------------------|----------------------------------------------------------------------------|
| *Exemplo*               | *Descrição*                                                                |
|-------------------------|----------------------------------------------------------------------------|
| 3.0.1                   | Versão exata. Neste exemplo, apenas a versão 3.0.1 pode ser instalada.     |
|-------------------------|----------------------------------------------------------------------------|
| >=3.0.1                 | Maior ou Igual a versão poderá ser instalada.(3.0.1, 3.2.1, etc.)          |
|-------------------------|----------------------------------------------------------------------------|
| >3.0.1                  | Versões maiores que podem ser instaladas. (3.0.2 etc.)                     |
|-------------------------|----------------------------------------------------------------------------|
| <=3.0.1                 | Versão menor ou igual pode ser instalada (1.0, 1.5, 2.0.0 etc.)            |
|-------------------------|----------------------------------------------------------------------------|
| <3.0.1                  | Versão inferior pode ser instalada (1.0, 1.1, 1.9, etc.)                   |
|-------------------------|----------------------------------------------------------------------------|
| !=3.0.1                 | Todas as versões, exceto esta versão, podem ser instaladas.                |
|-------------------------|----------------------------------------------------------------------------|
| >=3.0,<3.1.0            | Qualquer versão entre essa faixa de versões pode ser instalada.            |
|-------------------------|----------------------------------------------------------------------------|
| 3.*                     | Qualquer versão com um número maior ou igual a 3 pode ser instalada        |
|                         |                                                                            |
|-------------------------|----------------------------------------------------------------------------|
| ~3.0                    | Qualquer versão a partir de 3.0, mas menor que a próxima versão principal  |
|                         | (equivale a >=3.0,<4.0).                                                   |
|-------------------------|----------------------------------------------------------------------------|
| ^3.0                    | Qualquer versão a partir de 3.0, mas menor que a próxima versão principal  |
|                         | (equivalente a> = 3.0, <4.0). Semelhante ao `~3.0`.                       |
|-------------------------|----------------------------------------------------------------------------|

### Instalando e atualizando os pacotes

Nós vimos como usar o comando `php composer.phar install` para instalar nossas dependências.
Assim que você executa o comando, o Composer vai, baixar e instalar as dependências
para o seu subdiretório `vendor`.


Q> ** É seguro instalar dependências com o Composer? **
Q>
Q> Bem, algumas pessoas podem ter medo receio do estilo de gerenciamento do Composer,
Q> Porque acham que alguém pode atualizar todo o sistema por engano ou até intencionalmente,
Q> faLaminaso com que o aplicativo da web seja
Q> pare de funcionar . Note que o Composer *nunca* instala estes
Q> arquivos no sistema, em vez disso, instala-os no diretório `APP_DIR/vendor/`.

Agora suponhamos que que após certo tempo novas atualizações de segurança para seus pacotes de dependência sejam liberadas.
Você vai querer atualizar seus pacotes para manter seu site seguro. Você pode fazer isso digitando o seguinte:

`php composer.phar update`

Se você deseja atualizar apenas uma única dependência, digite seu nome da seguinte forma:

`php composer.phar update laminas/laminas-mvc`

Após o comando `update`, seu arquivo `composer.lock` será atualizado também.

Q> ** O que faço se eu quiser voltar para a versão anterior do pacote? **
Q>
Q> Se o procedimento de atualização resultou em problemas indesejados com o seu sistema, você pode reverter
Q> revertendo as mudanças do seu arquivo `composer.lock` digitando  comando `install` novamente.
Q>A  alterações no `composer.lock` é fácil se você usar um sistema de controle de versão, como o GIT ou o SVN.
Se você não usa um sistema de controle de versão, faça um backup do `composer.lock` antes de atualizar.

### Adicionando uma nova dependência

Se você quiser adicionar nova dependência a sua aplicação, você pode editar `composer.json`
manualmente, ou execute o comando `require`. Por exemplo, para instalar o módulo Doctrine ORM em seu site
site (para adicionar o pacote "doctrine/doctrine-module" a sua aplicação), digite o seguinte:

`php composer.phar require doctrine/doctrine-module 2.*`

O comando acima edita o arquivo `composer.json` e faz o download e instala o pacote. Nós vamos usar este comando
mais adiante no capítulo [Gerenciando Banco de Dados com Doctrine](#doctrine), quando estivermos familiarizado
com o banco de dados.

### Virtual Packages

O Composer pode ser usado para exigir algumas funcionalidades do seu sistema. Você já viu
como precisamos "php:^5.6". O pacote PHP é uma virtual Package representando o próprio PHP.
Você também pode exigir outras coisas, como extensões PHP (veja a tabela 2.3 abaixo).

{title="Tabela 2.3. Virtual Composer Packages"}
|------------------------------------------------------------------------------------------------------|
| *Exemplo*               | *Descrição*                                                                |
|------------------------------------------------------------------------------------------------------|
| "php":"^5.6"            | Requer a versão do PHP maior ou igual a 5.6 porém menor que a 6.0.         |
|------------------------------------------------------------------------------------------------------|
| ext-dom, ext-pdo-mysql  | Requer as extensões PHP DOM e PDO MySQL.                                   |
|------------------------------------------------------------------------------------------------------|
| lib-openssl             | Requer a blibioteca OpenSSL.                                               |
|------------------------------------------------------------------------------------------------------|


Você pode executar o comando `php composer.phar show --platform` para exibir a lista de pacotes disponíveis
para a sua máquina.

### Composer e Sistemas de Controle de Versão

Se você estiver usando um sistema de controle de versão (como o Git), deve está curioso
sobre o que deve ser armazenado no Git: apenas o codigo da sua aplicação ou o código do seu aplicativo
mais todas as dependências que foram instaladas pelo Composer no diretório `APP_DIR/vendor`?

Geralmente, não é recomendado armazenar suas dependências do Composer
sob controle de versão, porque isso pode tornar seu repositório muito grande e lento para fazer check-out.
Em vez disso você deve armazenar seu arquivo `composer.lock` no seu controle de versão. O arquivo
`composer.lock` garante que instalem as mesmas versões de dependências que você possuia.
Isso é útil em equipes de desenvolvimento com mais de um desenvolvedor, porque todos
os desenvolvedores devem ter o mesmo código para evitar problemas indesejados com o ambiente
configuração incorreta.

Q> *E se alguma dependência se tornar obsoleta ou for removida da Packagist.org?*
Q>
Q> Bem, a possibilidade de remoção de pacotes é mínima. Todos os pacotes são gratuitos e de código aberto,
Q> e a comunidade de usuários sempre pode restaurar a dependência, mesmo se ela for removida do packagist.
Q> O mesmo conceito de instalação de dependência é usado no Linux (lembra APT ou RPM manager?),
Q> você já viu algum pacote Linux perdido?


Mas pode haver situações em que você *deve* armazenar algumas bibliotecas dependentes sob
controle de versão:

* Se você tiver feito uma alterações no código de terceiros. Por exemplo, suponha
  você tem que consertar um bug em uma biblioteca, e você não pode esperar pelo fornecedor da biblioteca
  para corrigi-lo para você (ou se o fornecedor da biblioteca não puder consertar o bug). Nesse caso,
  você deve colocar o código da biblioteca sob controle de versão para garantir o seu
  as mudanças não serão perdidas.

* Se você escreveu um módulo ou biblioteca reutilizável e deseja armazená-lo no `vendor'
  sem publicá-lo em *Packagist.org*.

* Se você quiser uma garantia de 100% de que um pacote não será perdido.
  Embora o risco seja mínimo, para algumas aplicações é essencial ser autônomo e não depender da
  disponibilidade do pacote no *Packagist.org*.

## Resumo

Neste capítulo, baixamos o Laminas Skeleton Application do GitHub e o instalamos através do Composer.
Nós configuramos o Apache Virtual Host para informar ao servidor da Web sobre o local do diretório raiz do site.

O Skeleton Application demonstra a estrutura de pastas recomendada de um site.
Nós temos a pasta `public` contendo arquivos publicamente acessíveis pelos usuários do site, incluindo o arquivo
`index.php` que é o arquivo de entrada, arquivos CSS, arquivos JavaScript e imagens. Todas as outras pastas do
aplicativos são inacessíveis para os usuários e contêm configuração, dados e módulos.

Na segunda parte do capítulo,falamos sobre configurações avançadas do Apache. Por exemplo, você
pode proteger seu site com senha e permite acessá-lo somente a partir de determinados endereços IP.

O Composer é uma ferramenta poderosa para instalar as dependências de seu site.
Por exemplo, o próprio Laminas Framework pode ser considerado como uma dependência.
Todos os pacotes instaláveis ​​pelo Composer são registrados em um catálogo no
site Packagist.org.
