# Fonctionnement d'un site {#operation}

Dans ce chapitre, nous verrons un peu de théorie sur le fonctionnement d'une application basée sur Laminas Framework.
Vous reverrez quelques bases de PHP comme les classes PHP, comment les espaces de noms PHP sont utilisés pour éviter
les collisions de noms, le chargement automatique des classes, comment définir les paramètres de configuration des
applications et les étapes présentes dans le cycle de vie d'une application.
Vous vous familiariserez également avec les composants Laminas importants tels que @`Laminas\EventManager`, @`Laminas\ModuleManager`
et @`Laminas\ServiceManager`.
Si au lieu d'apprendre la théorie, vous voulez avoir quelques exemples pratiques, passez ce chapitre et référez-vous
directement à la section [Modèle-Vue-Controleur](#mvc).

Les composants Laminas traités dans ce chapitre :

|--------------------------------|---------------------------------------------------------------|
| *Composant*                    | *Description*                                                 |
|--------------------------------|---------------------------------------------------------------|
| @`Laminas\Mvc`                     | Prise en charge du l'architecture MVC. Séparation de la logique métier de la présentation.                                      |
|--------------------------------|---------------------------------------------------------------|
| @`Laminas\ModuleManager`           | Ce composant est chargé du chargement et de l'initialisation des modules de l'application. |
|--------------------------------|---------------------------------------------------------------|
| @`Laminas\EventManager`            | Ce composant implémente une fonctionnalité pour le déclenchement et la gestion d'événements. |
|--------------------------------|---------------------------------------------------------------|
| @`Laminas\ServiceManager`          | Implémente le registre de tous les services disponibles dans l'application Web. |
|--------------------------------|---------------------------------------------------------------|

## Les Classes PHP

PHP est un langage de programmation orienté objet (OOP). Avec l'OOP, le bloc de code principal de votre code est
une *classe*. Une classe peut avoir des *propriétés* et des *méthodes*.
Par exemple, créons un script PHP nommé *Person.php* et définissons une classe simple nommée `Person` dans ce fichier :

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

I> Vous remarquerez peut-être que dans l'exemple ci-dessus nous avons la balise d'ouverture `<?php` qui indique au moteur
I> PHP que le texte situé après le tag est du PHP. Dans l'exemple ci-dessus, lorsque le fichier contient uniquement le code
I> PHP (sans mélanger les balises PHP et HTML), il n'est pas nécessaire d'insérer la balise de fermeture `?>` à la fin
I> du code. D'ailleurs, ceci n'est pas recommandé et peut provoquer des effets indésirables, si vous ajoutez occasionnellement
I> des caractères après la balise fermante `?>`.

La classe `Person` ci-dessus a une propriété privée `$fullName` et trois méthodes:

  * La méthode `__construct()` est une méthode spéciale appelée *constructeur*. Elle est utilisé si vous avez besoin
  d'initialiser des propriétés à cette classe.

  * `getFullName()` et `setFullName()` sont des méthodes publiques utilisées pour faire quelque chose avec la classe.

Une fois que vous avez défini votre classe, vous pouvez créer des objets de cette classe avec l'opérateur `New, comme ceci :

~~~php
<?php

// Instancier la personne.
$person = new Person();

// Définir le nom complet.
$person->setFullName('John Doe');

// Affiche le nom complet de la personne à l'écran
echo "Person's full name is: " . $person->getFullName() . "\n";
~~~

I> Les classes permettent de diviser votre code en plus petits blocs et de le rendre bien organisé.
I> Laminas se compose de centaines de classes. Auxquelles vous allez ajouter vos propres classes celles vos applications web.


## Les espaces de noms PHP

Lorsque vous utilisez des classes de différentes bibliothèques (ou même des classes provenant de différents
composants d'une même bibliothèque) dans votre programme, les noms de classe peuvent entrer en conflit.
Cela signifie que vous pouvez rencontrer deux classes ayant le même nom, ce qui entraîne une erreur
d'interpréteur PHP. Si vous avez déjà programmé des applications avec Laminas Framework 1, vous pouvez vous
souvenir de ces noms de classe très longs comme Laminas_Controller_Abstract. L'idée avec des noms longs a été
utilisée pour éviter les collisions de noms entre différents composants. Chaque composant a défini son
propre préfixe de nom, comme `Laminas_` ou `My_`.

Pour atteindre le même objectif, Laminas Framework utilise une fonctionnalité de langage PHP appelée *namespaces*.
Les espaces de noms permettent de résoudre les conflits de noms entre les composants de code et vous
permettent de raccourcir les noms longs.

Un espace de noms est un conteneur pour un groupe de noms. Vous pouvez imbriquer des espaces de noms entre
eux. Si une classe ne définit pas un espace de noms, elle réside à l'intérieur de l'espace de noms global
(par exemple, les classes PHP `Exception` et `DateTime` appartiennent à l'espace de noms global).

Un exemple réel d'une définition d'espace de noms (prise à partir du composant @`Laminas\Mvc` component) est présenté
ci-dessous :

~~~php
<?php
namespace Laminas\Mvc;

/**
 * Classe d'application principale pour l'appel d'applications.
 */
class Application
{
    // ... les membres de la classe ont été omis pour la simplicité ...
}
~~~

Dans Laminas Framework, toutes les classes appartiennent à l'espace de noms *Laminas* de niveau supérieur.
La ligne 2 définit l'espace de noms *Mvc*, qui est imbriqué dans l'espace de noms *Laminas* et toutes les
classes de ce composant (y compris la classe @`Application`[Laminas\Mvc\Application] représentée dans cet
exemple sur les lignes 7-10) appartiennent à cet espace de noms. Vous séparez les noms d'espaces de noms
imbriqués avec un back-slash ('\').

Dans d'autres parties du code, vous faites référence à la classe @`Application`[Laminas\Mvc\Application] en
utilisant son nom qualifié complet :

~~~php
<?php
$application = new \Laminas\Mvc\Application();
~~~

I> Veuillez noter qu'il y a un premier back-slash dans @`\Laminas\Mvc\Application`.
Si vous spécifiez un nom de classe avec un back-slash en début de chaîne, cela signifie que le nom de classe est entièrement spécifié.
Il est également possible de spécifier le nom de la classe par rapport à l'espace de noms courant, auquel cas vous ne spécifiez pas la barre oblique inverse.

Il est également possible d'utiliser l'*alias* (nom abrégé de la classe) à l'aide de l'instruction
`use` de PHP :

~~~php
<?php
// Définit l'alias au début du fichier.
use Laminas\Mvc\Application;

// Plus tard dans votre code, utilisez le nom de la classe abrégée.
$application = new Application();
~~~

T> Bien que l'alias permette d'utiliser un nom de classe court au lieu du nom complet, son utilisation est
T> facultative. Vous n'êtes pas obligé de toujours utiliser des alias et pouvez référencer la classe par son
T> nom complet.

Chaque fichier PHP de votre application spécifit généralement un namespace (à l'exception du script d'entrée
*index.php* et des fichiers de configuration). Par exemple, le module principal de votre site, le module
*Application*, définit son propre espace de noms dont le nom est égal au nom du module :

~~~php
<?php
namespace Application;

class Module
{
    // ... les membres de la classe ont été omis pour la simplicité ...
}
~~~

## Les Interfaces PHP

En PHP, les *interfaces* vous permettent de définir quel comportement doit avoir une classe mais sans fournir
l'implémentation d'un tel comportement. C'est ce qu'on appelle un *contrat* : en implémentant une interface,
une classe accepte les termes du contrat.

Dans Laminas Framework, les interfaces sont largement utilisées. Par exemple, la classe  @`Application`
implémente @`ApplicationInterface` qui définit les méthodes que chaque classe d'application doit fournir :

~~~php
<?php
namespace Laminas\Mvc;

interface ApplicationInterface
{
    // Récupère le gestionnaire de service.
    public function getServiceManager();

    // Récupère l'objet de requête HTTP.
    public function getRequest();

    // Récupère l'objet de réponse HTTP.
    public function getResponse();

    // Exécute l'application.
    public function run();
}
~~~

Comme vous pouvez le voir dans l'exemple ci-dessus, une interface est définie à l'aide du mot-clé `interface`,
de la même manière que vous définissez une classe PHP standard. En tant que classe habituelle,
l'interface définit des méthodes. Cependant, l'interface ne fournit aucune implémentation de ses méthodes.
Dans la définition de l'interface @`ApplicationInterface` ci-dessus, vous pouvez voir que chaque
application implémentant cette interface aura la méthode `getServiceManager()` pour récupérer le gestionnaire
de service (dont nous parlerons plus tard), les méthodes `getRequest()` et `getResponse()` pour récupérer
la requête et la réponse HTTP et la méthode `run()` pour exécuter l'application.

I> Dans Laminas Framework, par convention, les classes d'interface doivent être nommées avec le suffixe `Interface`,
comme @`ApplicationInterface`.

Une classe implémentant une interface est appelée une classe *concrète*. La classe concrète @`Application`
implémente @`ApplicationInterface`, ce qui signifie qu'elle fournit l'implémentation des méthodes définies
par l'interface :

~~~php
<?php
namespace Laminas\Mvc;

class Application implements ApplicationInterface
{
    // Implémentation des méthodes de l'interface :

    public function getServiceManager()
    {
        // Fournis une implémentation ...
    }

    public function getRequest()
    {
        // Fournis une implémentation ...
    }

    public function getResponse()
    {
        // Fournis une implémentation ...
    }

    public function run()
    {
        // Fournis une implémentation ...
    }
}
~~~

La classe concrete @`Application` utilise le mot clé `implements` pour montrer qu'elle fournit une
implémentation de toutes les méthodes de l'interface `ApplicationInterface`.
La classe @`Application` peut également avoir des méthodes supplémentaires, qui ne font pas partie de
l'interface.

Graphiquement, les relations de classe sont affichées en utilisant le diagramme d'héritage.
Dans la figure 3.1, le diagramme de la classe @`Application` est présenté. La flèche pointe
de la classe enfant à la classe parent.

![Figure 3.1. Diagramme de la classe Application](../en/images/operation/Application.png)

## Chargement automatique des classes PHP

Une application se compose de nombreuses classes PHP et chaque classe réside généralement dans un
fichier distinct. Cela introduit le besoin d'*inclure* les fichiers.

Par exemple, supposons que nous ayons le fichier *Application.php* qui contient la définition de la classe
@`\Laminas\Mvc\Application` de la section précédente. Avant de pouvoir créer une instance de la classe
@`Application` quelque part dans votre code, vous devez inclure le contenu du fichier *Application.php*
(vous pouvez le faire à l'aide de l'instruction `require_once` en lui passant le chemin d'accès complet
au fichier) :

~~~php
<?php
require_once "/path/to/laminas/laminas-mvc/src/Application.php";

use Laminas\Mvc\Application;

$application = new Application();
~~~

Au fur et à mesure que votre application augmente en taille, il peut être difficile d'inclure chaque
fichier nécessaire. Laminas Framework lui-même se compose de centaines de fichiers, et il peut être très
difficile de charger la bibliothèque entière et toutes ses dépendances de cette façon. De plus, lors de
l'exécution du code résultant, l'interpréteur PHP prendra du temps CPU pour traiter chaque fichier inclus,
même si vous ne créez pas une instance de ladite classe.

Pour résoudre ce problème, en PHP, une fonctionnalité de chargement automatique de classe a été introduite.
La fonction PHP `spl_autoload_register()` vous permet de créer une fonction d'*autoloading* (chargement automatique).
Pour les sites complexes, vous pouvez même créer plusieurs fonctions de chargement automatique, qui sont
chaînées dans une pile.

Pendant l'exécution du script, si l'interpréteur PHP rencontre un nom de classe qui n'a pas encore été défini,
il appelle la ou les autoloaders déclarées jusqu'à ce qu'il y en est un qui
trouve la classe ou que l'erreur "not found" soit levée. Cela permet un chargement "lazy" (paresseux),
l'interpréteur PHP ne traite la définition de la classe qu'au moment de l'appel de cette classe,
ie, quand cela est vraiment nécessaire.

### Mapping d'une classe Autoloader

Pour vous donner une idée de la façon dont une fonction d'autoloading se présente, voyons ci-dessous une
implémentation simplifiée d'une fonction d'autoloading :

~~~php
<?php
// Autoloader function.
function autoloadFunc($className)
{
    // Mappage des classes dans un tableau statique.
    static $classMap = [
        '\\Laminas\\Mvc\\Application' => '/path/to/laminas/laminas-mvc/src/Laminas/Mvc/Application.php',
        '\\Application\\Module' => '/path/to/app/dir/module/Application/Module.php',
        //...
    ];

    // Vérifie si un tel nom de classe est présent dans la class map.
    if(isset(static::$classMap[$className])) {
        $fileName = static::$classMap[$className];

        // Vérifie si le fichier existe et est lisible.
        if (is_readable($fileName)) {
            // Inclus le fichier.
            require $fileName;
        }
    }
}

// Déclaration de la function d'autoloading
spl_autoload_register("autoloadFunc");
~~~

Dans l'exemple ci-dessus, nous définissons la fonction de chargement automatique `autoloadFunc()`
que nous désignerons sous le nom de *class map* d'autoloader.

L'autoloader de class map utilise le mappage de classe pour mapper le nom de classe et le chemin absolu
vers le fichier PHP contenant cette classe. La class map est juste un tableau PHP contenant des clés et
des valeurs. Pour déterminer le chemin d'accès au fichier par nom de classe, l'autoloader de la classe
doit simplement extraire la valeur du tableau de class map. Il est évident que l'autoloader de class map
fonctionne très vite. Cependant, le désavantage est que vous devez maintenir la class map et la mettre à
jour à chaque fois que vous ajoutez une nouvelle classe à votre programme.

### La Norme PSR-4

Comme chaque fournisseur de bibliothèque utilise ses propres conventions de nommage et d'organisation de
fichier, vous devrez enregistrer une fonction de chargement automatique personnalisée différente pour
chaque bibliothèque dépendante, ce qui est plutôt ennuyeux.
Pour résoudre ce problème, la norme PSR-4 a été introduite.

I> PSR signifie PHP Standards Recommendation.

La [norme PSR-4](http://www.php-fig.org/psr/psr-4/)
définit la structure de code recommandée qu'une application ou une bibliothèque doit suivre pour garantir
l'interopérabilité de l'autoloader. En deux mots, la norme dit que :

* Les espaces de noms doivent être organisés de la manière suivante :

  `\<Vendor Name>\(<Namespace>)*\<Class Name>`

* Les espaces de noms peuvent avoir autant de niveaux d'imbrication que vous le souhaitez, mais le nom du
  fournisseur (vendor) doit correspondre au niveau supérieur.

* Les espaces de noms doivent correspondre à la structure du répertoire. Chaque séparateur d'espace de
  noms ('\\') est converti en une constante `DIRECTORY_SEPARATOR` spécifique au système d'exploitation
  lors du chargement à partir du système de fichiers.

* Le nom de la classe est suffixé avec l'extension *.php* lors du chargement du fichier à partir du système de fichiers.

Par exemple, pour la classe @`Laminas\Mvc\Application`,
vous aurez la structure de répertoire suivante :

~~~text
/path/to/laminas/laminas-mvc/src
  /Laminas
    /Mvc
       Application.php
~~~

L'inconvénient de ceci est que vous devez mettre votre code dans plusieurs répertoires imbriqués (*Laminas* et *Mvc*).

Pour résoudre ce problème, le PSR-4 vous permet de définir une série contiguë d'un ou plusieurs namespaces
et sous-namespaces correspondants à un "répertoire de base". Par exemple, si vous avez le nom de classe
complet @`\Laminas\Mvc\Application`, et si vous définissez que @`\Laminas\Mvc` correspond au répertoire
"/path/to/laminas/laminas-mvc/src", vous pouvez organisez vos fichiers comme suit:

```
/path/to/laminas/laminas-mvc/src
    Application.php
```

Pour le code soit conforme à la norme PSR-4, nous pouvons écrire et déclarer un autoloader, que nous
appellerons l'autoloader "standard" :

~~~php
<?php

// Fonction de chargement automatique "Standard".
function standardAutoloadFunc($className)
{
    // Remplace le préfixe du namespace par le répertoire de base.
    $prefix = '\\Laminas\\Mvc';
    $baseDir = '/path/to/laminas/laminas-mvc/src/';
    if (substr($className, 0, strlen($prefix)) == $prefix) {
        $className = substr($className, strlen($prefix)+1);
        $className = $baseDir . $className;
    }

    // Remplace les séparateurs d'espace de noms dans le namespace par des séparateurs de répertoire.
    $className = str_replace('\\', DIRECTORY_SEPARATOR, $className);

    // Ajoute l'extension .php.
    $fileName = $className . ".php";

    // Vérifie si le fichier existe et est lisible.
    if (is_readable($fileName)) {
        // Inclus  le fichier.
        require $fileName;
    }
}

// Déclaration de la fonction d'autoloader.
spl_autoload_register("standardAutoloadFunc");
~~~

L'autoloader standard fonctionne comme suit. En supposant que le namespace des classes peut être mappé à
la structure des répertoires un par un, la fonction calcule le chemin vers le fichier PHP en transformant
les back-slashes (séparateurs de namespace) en forward slashes (séparateurs de chemin) et en concaténant
le chemin d'accès absolu au dossier où se trouve la bibliothèque.
Ensuite, la fonction vérifie si un tel fichier PHP existe vraiment, et si oui, l'inclut avec l'instruction
`require`.

Il est évident que l'autoloader standard fonctionne plus lentement que l'autoloader de class map.
Cependant, son avantage est que vous n'avez pas besoin de gérer une class map, ce qui est très pratique
lorsque vous développez un nouveau code et ajoutez de nouvelles classes à votre application.

I> Laminas Framework est conforme à la norme PSR-4, ce qui permet d'utiliser un mécanisme d'autoloading
I> standard sur tous ses composants. Il est également compatible avec d'autres librairies conformes au PSR-4
I> comme Doctrine ou Symfony.

### L'Autoloader fournis par Composer

Composer peut générer des fonctions d'autoloading (à la fois des autoloaders de class map et des
autoloader norme PSR-4) pour le code que vous installez avec.
Laminas Framework utilise l'autoloader fournie par Composer.
Lorsque vous installez un package avec Composer, il crée automatiquement le fichier
*APP_DIR/vendor/autoload.php*, qui utilise la fonction PHP `spl_autoload_register()` pour déclarer
un autoloader. De cette façon, toutes les classes PHP situées dans le répertoire `APP_DIR/vendor`
sont chargées automatiquement.

Pour charger automatiquement les classes PHP situées dans vos propres modules (comme le module `Application`),
vous devez spécifier la clé `autoload` dans votre fichier `composer.json` :

{line-numbers=off,lang="json",title="Autoload key of composer.json file"}
~~~
"autoload": {
    "psr-4": {
        "Application\\": "module/Application/src/"
    }
},
~~~

Ensuite, la seule chose à faire est d'inclure ce fichier dans votre script d'entrée `index.php` :

```php
// Composer autoloading
include __DIR__ . '/../vendor/autoload.php';
```

T> Le fichier *autoload.php* est généré chaque fois que vous installez un package avec Composer.
T> Au besoin, pour que Composer génère le fichier autoload.php, vous pourrez exécuter la commande
T> `dump-autoload` :
T>
T> `php composer.phar dump-autoload`

### PSR-4 et la structure du répertoire source du module

Dans Laminas Skeleton Application, vous pouvez voir comment la norme PSR-4 est appliquée dans la pratique.
Pour le module par défaut de votre site Web, le module `Application`, les classes PHP déclarées avec
l'autoloader standard sont stockées dans le répertoire `APP_DIR/module/Application/src` ("src" abréviation
de "source").

I> Nous ferons référence au répertoire `src` en tant que répertoire source du module.

Par exemple, regardons le fichier `IndexController.php` du module `Application` (figure 3.2).

![Figure 3.2. La structure du répertoire de l'application squelette est conforme à la norme PSR-4](../en/images/operation/psr0_and_dir_structure.png)

Comme vous pouvez le voir, il contient la classe `IndexController` [^controller] appartenant à l'espace de
noms `Application\Controller`. Pour pouvoir suivre la norme PSR-4 et utiliser l'autoloader standard avec
cette classe PHP, nous devons le placer dans le dossier `Controller` du dossier source du module.

[^controller]: La classe `IndexController` est le contrôleur par défaut de l'application squelette.
Nous parlerons des contrôleurs plus loin dans le chapitre [Model-View-Controller](#mvc).

## Requête et Réponse HTTP

Lorsqu'un utilisateur du site ouvre une page web dans la fenêtre d'un navigateur, le navigateur génère un
message de demande (request) et l'envoie en utilisant le protocole HTTP au serveur web.
Le serveur dirige cette requête HTTP vers votre application.

I> [HTTP](https://en.wikipedia.org/wiki/Hypertext_Transfer_Protocol) (signifie Hyper Text Transfer Protocol
I> ) -- un protocole de transfert de données sous la forme de documents hyper texte (pages web).
I> HTTP est basé sur la technologie client-serveur : le client initie une connexion et envoie une requête
I> au serveur web. Le serveur attend une connexion, effectue les actions nécessaires et renvoie un message
I> de réponse.

Ainsi, l'objectif sous-jacent de toute application web est de gérer la requête HTTP et de produire une
réponse HTTP contenant généralement le code HTML de la page demandée. La réponse est envoyée par le serveur
web au navigateur web du client. Le navigateur affiche alors une page web à l'écran.

Une requête HTTP typique est présentée ci-dessous :

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

Le message de requête HTTP ci-dessus est composé de trois parties :

* La ligne de départ (ligne 1) spécifie la méthode de la requête (par exemple, GET ou POST),
  l'URL et la version du protocole HTTP.
* Les en-têtes optionnels (lignes 2 à 8) caractérisent le message, les paramètres de transmission et
  fournissent d'autres méta-informations. Dans l'exemple ci-dessus, chaque ligne représente une seule en-tête
  sous la forme *name:value*.
* Le corps du message (facultatif) contient des données de message. Il est séparé des en-têtes par une
  ligne vide.

Les en-têtes et le corps du message peuvent être absents, mais la ligne de départ est toujours présente
dans la requête, car elle indique son type et son URL.

La réponse du serveur pour la demande ci-dessus est présentée ci-dessous :

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
(ligne vide)
(le contenu de la page suit)
~~~

Comme vous pouvez le voir à partir du dump ci-dessus, la réponse HTTP a presque le même format que la requête :

* La ligne de départ (ligne 1) représente la version du protocole HTTP, le statut code de la réponse et le message (200 OK).

* Les en-têtes optionnels (lignes 2-10) fournissent diverses méta-informations sur la réponse.

* Le corps du message (facultatif) suit les en-têtes et doit être séparé des en-têtes par une ligne vide.
  Le corps du message contient généralement le code HTML de la page Web demandée.

## Script d'entrée du site

Lorsque le serveur Web Apache reçoit une requête HTTP d'un navigateur client, il exécute le fichier
*APP_DIR/public/index.php*, également appelé *script d'entrée*.

I> Le script d'entrée est le seul fichier PHP accessible au monde extérieur. Le serveur web Apache dirige
I> toutes les requêtes HTTP vers ce script (souvenez-vous du fichier .htaccess). Avoir ce script d'entrée
I> unique rend le site web plus sûr (en comparant avec la situation où vous permettez à tout le monde
I> d'accéder à tous les fichiers PHP de votre application).

Bien que le fichier *index.php* soit très important, il est étonnamment petit (voir ci-dessous) :

~~~php
<?php

use Laminas\Mvc\Application;
use Laminas\Stdlib\ArrayUtils;

/**
 * Le chemin de la racine de l'application est rendue relative.
 */
chdir(dirname(__DIR__));

// Refuse les demandes de fichiers statiques sur le serveur PHP
if (php_sapi_name() === 'cli-server') {
    $path = realpath(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    if (__FILE__ !== $path && is_file($path)) {
        return false;
    }
    unset($path);
}

// Chargement de l'autoloader Composer
include __DIR__ . '/../vendor/autoload.php';

if (! class_exists(Application::class)) {
    throw new RuntimeException(
        "Unable to load application.\n"
        . "- Type `composer install` if you are developing locally.\n"
        . "- Type `vagrant ssh -c 'composer install'` if you are using Vagrant.\n"
        . "- Type `docker-compose run zf composer install` if you are using Docker.\n"
    );
}

// Récupère la configuration
$appConfig = require __DIR__ . '/../config/application.config.php';
if (file_exists(__DIR__ . '/../config/development.config.php')) {
    $appConfig = ArrayUtils::merge($appConfig, require __DIR__ . '/../config/development.config.php');
}

// Exécute l'application
Application::init($appConfig)->run();
~~~

En gros, trois choses y sont faites.

Tout d'abord, à la ligne 10, le dossier de travail courrant est remplacé par `APP_DIR`.
Cela simplifie la définition de chemins de fichiers relatifs dans votre application.

Ensuite, à la ligne 22, l'auto-chargement des classes PHP est initialisé.
Cela permet de charger facilement n'importe quelle classe située dans la bibliothèque Laminas Framework ou
dans votre application sans avoir besoin de l'instruction `require_once`.

Et enfin, à la ligne 40, une instance de la classe @`Laminas\Mvc\Application` est créée. L'application est
initialisée avec les paramètres issus du fichier de configuration *application.config.php* et
l'application est exécutée.

## Événements et cycle de vie de l'application

Comme vous l'avez appris dans la section précédente, à chaque requête HTTP, l'objet
@`Laminas\Mvc\Application` est créé. Typiquement, une application "vit" pendant une seconde ou moins
(cette durée est suffisante pour générer la réponse HTTP). La "vie" de l'application comprend plusieurs
étapes.

I> Laminas Framework utilise le concept d'*événement*. Une classe peut *déclencher* un événement et
I> d'autres classes peuvent *écouter* des événements. Techniquement, déclencher un événement signifie
I> simplement appeler une autre méthode de "callback". La gestion des événements est implémentée dans
I> le composant @`Laminas\EventManager`.

Chaque étape de la vie de l'application est initiée par l'application en déclenchant un événement
(cet événement est représenté par la classe @`MvcEvent` vivant dans l'espace de noms @`Laminas\Mvc`).
D'autres classes (appartenant à Laminas Framework ou spécifiques à votre application) peuvent écouter
des événements et réagir en conséquence.

Ci-dessous, les cinq événements principaux (étapes de la vie de l'application) sont présentés :

**Bootstrap**. Lorsque cet événement est déclenché par l'application, un module a la possibilité de
se déclarer lui même en tant qu'écouteur d'autres événements de l'application dans sa méthode de callback `onBootstrap()`.

**Route**.  Lorsque cet événement est déclenché, l'URL de la requête est analysée à l'aide d'une classe de
routeur (généralement, avec la classe @`Laminas\Router\Http\TreeRouteStack`). Si une correspondance exacte
entre l'URL et une route est trouvée, la demande est transmise à la classe de *contrôleur* assignée à la
route demandée.

**Dispatch**. La classe de contrôleur "dispatch" (distribue) la demande en utilisant la méthode d'action
correspondante et produit les données qui peuvent être affichées sur la page web.

**Render**. Lors de cet événement, les données produites par la méthode d'action du contrôleur sont transmises
pour etre rendue vers la classe @`Laminas\View\Renderer\PhpRenderer`. La classe de rendu utilise un fichier
*vue* pour produire une page HTML.

**Finish**. Sur cet événement, la réponse HTTP est renvoyée au client.

Le flux d'événements est illustré à la figure 3.3 :

![Figure 3.3. Flux d'événements pendant le cycle de vie de l'application](../en/images/operation/app_life_cycle.png)

T> Bien que cela soit rarement nécessaire, vous trouverez des exemples pratiques d'écoute et de réaction
T> à un événement dans le chapitre [Créer un nouveau module](#modules).

## Configuration de l'application

La plupart des composants Laminas Framework utilisés sur votre site web nécessitent une configuration.
Par exemple, dans le fichier de configuration, vous définissez les informations d'identification de
connexion à la base de données, spécifiez les modules présents dans votre application et, facultativement,
fournissez des paramètres personnalisés spécifiques à votre application.

Vous pouvez définir les paramètres de configuration à deux niveaux : au niveau de l'application ou au
niveau du module. Au niveau de l'application, vous définissez généralement les paramètres qui contrôlent
l'application entière et sont communs à tous les modules de votre application. Au niveau du module,
vous définissez des paramètres qui affectent uniquement ce module.

I> Certains frameworks PHP préfèrent les *conventions au concept de configuration*, où la plupart de vos
I> paramètres sont codés en dur et ne nécessitent aucune configuration. Cela accélère le développement de
I> l'application, mais la rend moins personnalisable. Dans Laminas Framework, le *concept de configuration
I> sur conventions* est utilisé, vous pouvez donc personnaliser n'importe quel aspect de votre application
I> mais vous devez prendre le temps d'apprendre à le faire.

### Fichiers de configuration au niveau de l'application

Le sous-dossier *APP_DIR/config* contient des fichiers de configuration à l'échelle de l'application.
Regardons ce sous-dossier plus en détail (figure 3.4).

![Figure 3.4. Fichiers de configuration](../en/images/operation/config.png)

Le fichier *APP_DIR/config/application.config.php*  est le fichier de configuration principal. Il est utilisé
par l'application au démarrage pour déterminer les modules d'application à charger et les services à créer
par défaut.

Ci-dessous, le contenu du fichier *application.config.php* est présenté. Vous pouvez voir que le fichier de
configuration est juste un tableau associatif imbriqué et que chaque composant peut avoir une clé spécifique
dans ce tableau. Vous pouvez fournir des commentaires en ligne pour les clés du tableau afin de permettre
aux autres de mieux comprendre ce que chaque clé signifie.

T> Par convention, les noms de clé doivent être en minuscules et si le nom de clé est constitué de
T> plusieurs mots, les mots doivent être séparés par le symbole underscore ('_').

{line-numbers=on,lang=php, title="Content of application.config.php file"}
~~~
return [
    // Récupère la liste des modules utilisés dans cette application.
    'modules' => require __DIR__ . '/modules.config.php',

    // Ce sont différentes options pour les écouteurs attachés au ModuleManager
    'module_listener_options' => [
        // Doit être un tableau de chemins dans lequel les modules résident.
        // Si une clé est fournie, l'écouteur considérera que c'est le namespace
        // d'un module, la valeur de cette clé est le chemin spécifique de ce module.
        // Module class.
        'module_paths' => [
            './module',
            './vendor',
        ],

        // Un tableau de chemins à partir duquel globaliser les fichiers de configuration
        // après le chargement des modules. Ils remplacent la configuration fournie
        // par les modules eux-mêmes. Les chemins peuvent utiliser la notation GLOB_BRACE.
        'config_glob_paths' => [
            realpath(__DIR__) . '/autoload/{{,*.}global,{,*.}local}.php',
        ],

        // Active ou non un cache de configuration.
        // Si activé, la configuration fusionnée sera mise en cache et
        // utilisée dans les requêtes suivantes.
        'config_cache_enabled' => true,

        // La clé utilisée pour créer le nom du fichier de cache de configuration.
        'config_cache_key' => 'application.config.cache',

        // Activer ou non un cache de class map de module.
        //  Si activé, crée un cache de class map  de module qui sera utilisé par les
        // futures requêtes, afin de réduire le processus de chargement automatique.
        'module_map_cache_enabled' => true,

        // La clé utilisée pour créer le nom du fichier de cache du class map.
        'module_map_cache_key' => 'application.module.cache',

        // Le chemin dans lequel mettre en cache la configuration fusionnée.
        'cache_dir' => 'data/cache/',

        // Active ou non la vérification des dépendances des modules.
        // Activé par défaut, empêche l'utilisation des modules qui dépendent
        // d'autres modules  qui n'ont pas été chargés.
        // 'check_dependencies' => true,
    ],

    // Utilisé pour créer un propre gestionnaire de services.
    // Peut contenir un ou plusieurs tableaux enfants.
    //'service_listener_options' => [
    //     [
    //         'service_manager' => $stringServiceManagerName,
    //         'config_key'      => $stringConfigKey,
    //         'interface'       => $stringOptionalInterface,
    //         'method'          => $stringRequiredMethodName,
    //     ],
    // ],

   // Configuration initiale avec laquelle lancer le ServiceManager.
   // Doit être compatible avec Laminas\ServiceManager\Config.
   // 'service_manager' => [],
];
~~~

A la ligne 3, nous avons la clé des modules qui définit quels *modules* seront chargés au démarrage.
Vous pouvez voir que les noms des modules sont stockés dans un autre fichier de configuration
`modules.config.php`, qui répertorie tous les modules présents sur votre site web.

Ligne 11, il y a la clé `module_paths` qui indique à Laminas dans quels dossiers chercher les fichiers
source appartenant aux modules. Les modules de l'application que vous développez se trouvent dans le
dossier *APP_DIR/module* et les modules tiers se trouvent dans le dossier *APP_DIR/vendor*.

Et à la ligne 19, nous avons la clé `config_glob_paths` qui indique à Laminas où chercher les fichiers de
configuration supplémentaires. Vous voyez que les fichiers de *APP_DIR/config/autoload* qui ont un suffixe
*global.php* ou *local.php* sont automatiquement chargés.

Pour résumer, vous utilisez généralement le fichier principal *application.config.php* pour stocker les
informations sur les modules qui doivent être chargés dans votre application, où ils se trouvent et
comment ils sont chargés (par exemple, vous pouvez contrôler les options de mise en cache).
Dans ce fichier, vous pouvez également paramétrer le gestionnaire de service.
Il n'est pas recommandé d'ajouter plus de clés dans ce fichier. Il vaut mieux alors utiliser le fichier
`autoload/global.php`.

Jetons aussi un oeil au fichier `modules.config.php`. Actuellement, les modules suivants sont installés
sur votre site web :

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

Le module `Application` est un module contenant les fichiers de votre application.
Tous les autres modules répertoriés sont des composants Laminas Framework.

I> Dans Laminas, un plugin Composer spécial appelé *component installer* a été introduit.
I> Si vous vous souvenez, dans le chapitre [Laminas Skeleton Application](#skeleton), nous avons répondu à plusieurs
I> questions oui/non de l'installateur, en déterminant les composants à installer.
I> Et le programme d'installation a *injecté* les noms des modules de ces composants ici,
I> dans `modules.config.php`.

### Fichiers de configuration supplémentaires au niveau de l'application

Les fichiers de configuration "suplémentaires" : *APP_DIR/config/autoload/global.php* et *APP_DIR/config/autoload/local.php*
définissent respectivement les paramètres indifférents à l'environnement et dépendants de l'environnement de
l'application. Ces fichiers de configuration sont automatiquement chargés et fusionnés avec les fichiers
de configuration fournis par le module, c'est pourquoi leur répertoire est nommé *autoload*.

En ayant différents fichiers de configuration dans le dossier *APP_DIR/config/autoload* vous pourriez
être perdus quant aux paramètres qui doivent être placés dans chacun d'entre eux. Voici quelques conseils :

* Vous utilisez le fichier *autoload/global.php* pour stocker les paramètres qui ne dépendent pas de
  l'environnement de la machine. Par exemple, vous pouvez stocker ici les paramètres qui remplacent
  les paramètres par défaut de certains modules. Ne stockez pas d'informations sensibles (comme les
  informations d'identification de la base de données), dans ce cas là, il vaut mieux utiliser
  *autoload/local.php*.

* Vous utilisez le fichier *autoload/local.php* pour stocker les paramètres spécifiques à l'environnement
  utilisé. Par exemple, vous pouvez stocker ici vos informations d'identification de base de données.
  Chaque développeur a généralement une base de données locale lors du développement et du test du site web.
  Le développeur va donc éditer le fichier *local.php* et entrer ses propres références de base de données.
  Lorsque vous installez votre site sur le serveur de production, vous modifiez le fichier `local.php` et
  entrez les informations d'identification de la base de données "de prod".

I> Comme le fichier *autoload/local.php* contient des paramètres spécifiques à l'environnement, dans le
I> système de contrôle de version, vous stockez son "modèle de distribution" *local.php.dist*. Chaque
I> développeur de votre équipe renomme alors le fichier *local.php.dist* en *local.php* et entre ses
I> propres paramètres. Ce fichier *local.php* ne doit pas être stocké sous le contrôle de version car il
I> peut contenir des informations sensibles telles que les informations d'identification de la base de
I> données (nom d'utilisateur et mot de passe) et vous ne souhaitez peut-être qu'elles soient connus par d'autres personnes.

### Fichier de configuration de développement au niveau de l'application

Le fichier de configuration de développement au niveau de l'application (`APP_DIR/config/development.config.php`)
entre en jeu uniquement lorsque vous activez le *mode développement*. Si vous vous en souvenez, nous avons
activé le mode développement plus tôt dans le chapitre [Laminas Skeleton Application](#skeleton).

I> Vous activez le mode développement avec la commande suivante :
I>
I> `php composer.phar development-enable`

Le fichier `development.config.php` est fusionné avec le fichier principal `application.config.php`.
 Cela vous permet de remplacer certains paramètres. Par exemple, vous pouvez :

  * désactiver la mise en cache de la configuration. Lorsque vous développez votre site, vous modifiez
    fréquemment vos fichiers de configuration, la mise en cache de la configuration peut avoir des
    conséquences indésirables, telles que l'impossibilité de voir immédiatement le résultat de vos modifications.
  * charger des modules supplémentaires. Par exemple, vous pouvez charger le module [LaminasDeveloperTools](https://github.com/laminas/LaminasDeveloperTools) uniquement en mode développement.

Si vous désactivez le mode de développement, le fichier `development.config.php` sera supprimé. Donc, vous
ne devriez pas stocker ce fichier sous le contrôle de version. Au lieu de cela, stockez sa version de
*distribution* `development.config.php.dist` sous le contrôle de version.

### Fichiers de configuration de développement supplémentaire au niveau de l'application

Le fichier de configuration de développement supplémentaire au niveau de l'application
(`APP_DIR/config/autoload/development.local.php`) s'affiche uniquement lorsque vous activez le *mode développement*.

Le fichier `development.local.php` est fusionné avec d'autres fichiers de configuration au niveau du module.
Cela vous permet de remplacer certains paramètres spécifiques au module utilisés uniquement dans l'environnement de développement.

Si vous désactivez le mode développement, le fichier `development.local.php` sera supprimé. Donc, vous ne
devriez pas stocker ce fichier sous le contrôle de version. Au lieu de cela, stockez sa version de
distribution `development.local.php.dist` sous le contrôle de version.

### Fichiers de configuration au niveau du module

Dans la figure 3.4, vous pouvez voir que le module *Application* livré avec votre application contient le
fichier *module.config.php* dans lequel vous placez les paramètres spécifiques au module.
Regardons le fichier `module.config.php` du module `Application` :

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

Dans ce fichier, vous déclarez les contrôleurs du module, vous fournissez des informations sur les règles de
routage pour le mappage des URL vers vos contrôleurs, déclarez les plugins de contrôleurs, les vues et les
aides de vues (nous en apprendrons plus sur ces termes dans ce chapitre et dans les chapitres suivants ).

### Combiner les fichiers de configuration

Lors de la création d'une application, les fichiers de configuration fournis par le module et les fichiers
de configuration supplémentaires provenant du dossier *APP_DIR/config/autoload* sont fusionnés en un seul
grand tableau imbriqué. Ainsi, chaque paramètre de configuration devient disponible n'importe où dans le
site web. Donc, potentiellement, vous êtes capable de remplacer certains paramètres spécifiés par les
modules.

I> Vous avez peut-être également vu le fichier de configuration "combiné" lors de l'installation de PHP
I> où se trouve le fichier principal *php.ini* et plusieurs fichiers de configuration supplémentaires
I> qui sont inclus dans le fichier principal. Une telle séparation rend la configuration de votre
I> application précise et flexible car vous n'avez pas besoin de placer tous vos paramètres dans un seul
I> fichier et de les modifier chaque fois que vous avez besoin de changer quelque chose.

Les fichiers de configuration sont chargés dans l'ordre suivant :

* Le fichier principal *application.config.php* est chargé en premier. Il est utilisé pour initialiser
  le gestionnaire de service et charger les modules d'application. Les données chargées à partir de cette
  configuration sont stockées seules et ne sont pas fusionnées avec d'autres fichiers de configuration.

* Les fichiers de configuration pour chaque module d'application sont chargés et fusionnés. Les modules
  sont chargés dans le même ordre qu'ils sont déclarés dans le fichier *application.config.php*.
  Si deux modules stockent (intentionnellement ou par erreur) des paramètres dans les clés nommées
  similairement, ces paramètres peuvent être écrasés.

* Les fichiers de configuration supplémentaires du dossier *APP_DIR/config/autoload* sont chargés et
  fusionnés en un seul tableau. Ensuite, ce tableau est fusionné avec le tableau de configuration du module
  produit à l'étape précédente, lors du chargement de la configuration du module. La configuration à
  l'échelle de l'application a une priorité plus élevée que la configuration du module, de sorte que vous
  pouvez remplacer les clés du module ici si vous le souhaitez.

## Le point d'entrée du module

Dans Laminas, votre site est composé de modules. Par défaut, vous avez un module unique `Application` mais
vous pouvez en créer d'autres si nécessaire. En règle générale, vos propres modules sont stockés dans le
dossier *APP_DIR/module*, tandis que les modules tiers résident dans le dossier *APP_DIR/vendor*.

Au démarrage, lorsque l'objet @`Laminas\Mvc\Application` est créé, il utilise le composant @`Laminas\ModuleManager`
pour rechercher et charger tous les modules déclarés dans la configuration de l'application.

Chaque module du site web a un fichier *Module.php* qui est une sorte de *point d'entrée* pour le module.
Ce fichier fournit la classe `Module`. Ci-dessous, le contenu de la classe `Module` de l'application squelette est présenté :

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

La classe `Module` appartient à l'espace de noms du module (pour le module principal, il appartient à
l'espace de noms `Application`).

La méthode `getConfig()` est utilisée pour fournir la configuration du module à Laminas Framework
(fichier *module.config.php*).

I> Vous pouvez également déclarer certains écouteurs d'événements ici, nous verrons comment procéder plus
I> tard dans le chapitre [Créer un nouveau module](#modules).

## Gestionnaire de services

Vous pouvez imaginer l'application comme un ensemble de *services*. Par exemple, vous pouvez avoir un
service d'authentification responsable de la connexion des utilisateurs du site, d'un service de
gestionnaire d'entités chargé d'accéder à la base de données, d'un service de gestion des événements
chargé de déclencher les événements et de les transmettre aux écouteurs d'événements.

Dans Laminas Framework, la classe @`ServiceManager`  est un *conteneur* centralisé pour tous les services
d'application. Le gestionnaire de service est implémenté dans le composant @`Laminas\ServiceManager`
en tant que classe @`ServiceManager`. Le diagramme d'héritage de classe est montré dans la figure 3.5 ci-dessous :

![Figure 3.5. Diagramme d'héritage de classes du gestionnaire de service](../en/images/operation/service_manager_inheritance.png)

Le gestionnaire de service est créé au démarrage de l'application (à l'intérieur de la méthode statique `init()`
de la classe @`Laminas\Mvc\Application`). Les services standard disponibles via le gestionnaire de services
sont présentés dans le tableau 3.1. Cette table est incomplète car le nombre réel de services déclarés
dans le gestionnaire de services peut être beaucoup plus important.

{title="Table 3.1. Services standards"}
|----------------------|-----------------------------------------------------------------------|
| Nom du service         | Description                                                           |
|----------------------|-----------------------------------------------------------------------|
| `Application`          | Permet de récupérer le singleton de la classe @`Laminas\Mvc\Application`.     |
|----------------------|-----------------------------------------------------------------------|
| `ApplicationConfig`    | Tableau de configuration extrait du fichier *application.config.php*.     |
|----------------------|-----------------------------------------------------------------------|
| `Config`             | Tableau de configuration extrait des fichiers *module.config.php*   |
|                      | fusionné avec *autoload/global.php* et *autoload/local.php*.           |
|----------------------|-----------------------------------------------------------------------|
| `EventManager`       | Permet de récupérer une *nouvelle* instance de la classe@`Laminas\EventManager\EventManager`. Le gestionnaire d'événements permet d'envoyer (déclencher) des événements et d'attacher des écouteurs d'événements. |
|----------------------|-----------------------------------------------------------------------|
| `SharedEventManager` | Permet de récupérer l'instance singleton de la classe @`Laminas\EventManager\SharedEventManager`.Le gestionnaire d'événements partagés permet d'écouter les événements définis par d'autres classes et composants. |
|----------------------|-----------------------------------------------------------------------|
| `ModuleManager`        | Permet de récupérer le singleton de la classe @`Laminas\ModuleManager\ModuleManager`. Le gestionnaire de module est responsable du chargement des modules d'application.         |
|----------------------|-----------------------------------------------------------------------|
| `Request`              | Le singleton de la classe @`Laminas\Http\Request`. Représente la requête HTTP reçue du client.                                                 |
|----------------------|-----------------------------------------------------------------------|
| `Response`             | Le singleton de la classe @`Laminas\Http\Response`. Représente la réponse HTTP qui sera envoyée au client.                                              |
|----------------------|-----------------------------------------------------------------------|
| `Router`               | Le singleton de @`Laminas\Router\Http\TreeRouteStack`. Effectue le routage d'URL. |
|----------------------|-----------------------------------------------------------------------|
| `ServiceManager`       | Le gestionnaire de service lui même.                                               |
|----------------------|-----------------------------------------------------------------------|
| `ViewManager`          | Le singleton de la classe @`Laminas\Mvc\View\Http\ViewManager`. Responsable de la préparation de la vue pour le rendu de la page.                               |
|----------------------|-----------------------------------------------------------------------|

Un service est généralement composé d'une classe PHP basique, mais pas toujours. Par exemple, lorsque Laminas charge
les fichiers de configuration et fusionne les données dans des tableaux imbriqués, il déclare les tableaux
dans le gestionnaire de services sous la forme d'un couple de services (!): `ApplicationConfig` et `Config`.
Le premier est le tableau chargé à partir du fichier de configuration au niveau de l'application
*application.config.php* et le second est le tableau fusionné des fichiers de configuration au niveau du
module et des fichiers de configuration au niveau de l'application chargés automatiquement.
Ainsi, dans le gestionnaire de services, vous pouvez stocker tout ce que vous voulez : une classe PHP,
une variable simple ou un tableau.

À partir du tableau 3.1, vous pouvez voir que dans Laminas presque tout peut être considéré comme un service.
Le gestionnaire de services est lui-même déclaré en tant que tel. Ainsi que la classe @`Application`.

I> Une chose importante que vous devez noter à propos des services est qu'ils sont stockés
I> dans une seule instance (ceci est également appelé le modèle *singleton*). Évidemment, vous n'avez pas
I> besoin de la deuxième instance de la classe @`Application` (ce qui serait un vrai cauchemar).

T> Mais, il existe une exception importante à la règle ci-dessus. Ca peut être déroutant au début, mais l'
T> @`EventManager` n'est pas un singleton. Chaque fois que vous récupérez le service du gestionnaire
T> d'événements auprès du gestionnaire de services, vous recevez un *nouvel* objet.
T> Tout ça pour des raisons de performance et pour éviter d'éventuels conflits d'événements entre
T> différents composants. Nous en discuterons plus en détail dans la section
T> *About Event Manager* plus loin dans ce chapitre.

Le gestionnaire de services définit plusieurs méthodes nécessaires pour localiser et récupérer un service auprès du gestionnaire de services (voir le tableau 3.2 ci-dessous) :

{title="Table 3.2. Méthodes du ServiceManager"}
|----------------------|-----------------------------------------------------------------------|
| Nom de la méthode          | Description                                                           |
|----------------------|-----------------------------------------------------------------------|
| `has($name)`         | Vérifie si un tel service est déclaré.                               |
|----------------------|-----------------------------------------------------------------------|
| `get($name)`         |  Récupère l'instance d'un service déclaré.                            |
|----------------------|-----------------------------------------------------------------------|
| `build($name, $options)` | Renvoie toujours une nouvelle instance du service demandé..           |
|----------------------|-----------------------------------------------------------------------|

Vous pouvez tester si un service est déclaré en transmettant son nom à la méthode `has()` du gestionnaire
de services. Il renvoie le booléen `vrai` si le service est déclaré ou `faux` si un service avec un tel nom
n'est pas déclaré.

Vous pouvez récupérer un service par son nom à l'aide de la méthode `get() du gestionnaire de services.
Cette méthode prend un seul paramètre représentant le nom du service. Regardez l'exemple suivant :

~~~php
<?php

// Récupère le tableau de configuration de l'application.
$appConfig = $serviceManager->get('ApplicationConfig');

// L'utilise (par exemple, en récupérant la liste des modules).
$modules = $appConfig['modules'];
~~~

Et la méthode `build()` qui crée toujours une nouvelle instance du service lorsque vous l'appelez
(contrairement à `get()` qui crée l'instance du service une seule fois et la renvoie sur les requêtes ultérieures).

T> En règle générale, vous récupérez les services du gestionnaire de services non pas dans votre code
T> mais à l'intérieur d'une fabrique. Une fabrique est un code responsable de la création d'un objet.
T> Lors de la création de l'objet, vous pouvez extraire les services dont il dépend du gestionnaire de
T> services et transmettre ces services (dépendances) au constructeur de l'objet. Ceci est également
T> appelé *injection de dépendance*.

I> Si vous avez un peu d'expérience avec Laminas Framework 2, vous remarquerez peut-être que les choses sont
I> maintenant un peu différentes. Dans ZF2, il y avait un modèle `ServiceLocator` permettant d'obtenir
I> des dépendances du gestionnaire de service dans n'importe quelle partie de votre application
I> (dans les contrôleurs, les services, etc.). Dans Laminas, vous devez transmettre les dépendances
I> explicitement. C'est un peu plus galère mais ça évite les dépendances "cachées" et rend votre code plus
I> clair et plus facile à comprendre.

### Déclarer un service

Lorsque vous codez votre site web, vous devrez souvent déclarer votre propre service dans le gestionnaire
de services. L'un des moyens de déclarer un service consiste à utiliser la méthode `setService()` du
gestionnaire de services. Par exemple, créons et déclarons la classe de service "convertisseur de devise",
qui sera utilisée, par exemple, sur une page de panier pour convertir la devise EUR en USD:

~~~php
<?php
// Définis l'espace de noms où notre service personnalisé est situé.
namespace Application\Service;

// Définis la classe de service CurrencyConverter.
class CurrencyConverter
{
    // Convertit les euros en dollars américains.
    public function convertEURtoUSD($amount)
    {
        return $amount*1.25;
    }

    //...
}
~~~

Ci-dessus, dans les lignes 6-15 nous définissons un exemple de classe `CurrencyConverter` (pour simplifier,
nous implémentons une seule méthode `convertEURtoUSD()` qui est capable de convertir des euros en dollars
américains).

~~~php
// Création d'une instance de la classe.
$service = new CurrencyConverter();
// Déclaration de l'instance dans le gestionnaire de service.
$serviceManager->setService(CurrencyConverter::class, $service);
~~~

Dans l'exemple ci-dessus, nous instancions la classe avec le nouvel opérateur et l'enregistrons avec
le gestionnaire de service en utilisant la méthode `setService()` (nous supposons que la variable
`$serviceManager` est une classe de type @`Laminas\ServiceManager\ServiceManager` et qu'elle a été déclarée
ailleurs).

La méthode `setService()` prend deux paramètres : le nom du service et l'instance du service.
Le nom du service doit être unique dans tous les autres services possibles.

Une fois le service stocké dans le gestionnaire de services, vous pouvez le récupérer par son nom à
n'importe quel endroit de votre application à l'aide de la méthode `get()` du gestionnaire de services.
Regardez l'exemple suivant :

~~~php
<?php
// Récupère le service de convertion de devises.
$service = $serviceManager->get(CurrencyConverter::class);

// Et l'utilise (convertis une somme d'argent).
$convertedAmount = $service->convertEURtoUSD(50);
~~~

### Nommage des Services

Différents services peuvent utiliser différents styles de nommage. Par exemple, le même service de
convertisseur de devises peut être chargé sous différents noms :
`CurrencyConverter`, `currency_converter` et ainsi de suite.
Pour introduire une convention de nommage uniforme, il est recommandé de charger un service par son nom de
classe complet, comme cela :

~~~php
$serviceManager->setService(CurrencyConverter::class);
~~~

Dans l'exemple ci-dessus, nous avons utilisé le mot-clé `class`. Il est disponible depuis PHP 5.5 et est
utilisé pour la résolution de noms de classe. `CurrencyConverter::class` est appelé avec le nom complet
de la classe comme `\Application\Service\CurrencyConverter`.

### Remplacer un service existant

Si vous essayez de déclarer un nom de service qui est déjà présent, la méthode `setService()` lèvera une
exception. Mais parfois vous voulez remplacer un service avec un service du même nom.
Pour ce faire, vous pouvez utiliser la méthode `setAllowOverride()` du gestionnaire de services :

{line-numbers=of,lang=php}
~~~
<?php
// Autorise le remplacement des services
$serviceManager->setAllowOverride(true);

// Sauvegarde l'instance dans le gestionnaire de service. Il n'y aura pas d'exception
// même s'il y a un autre service avec le même nom.
$serviceManager->setService(CurrencyConverter::class, $service);
~~~

Ci-dessus, la méthode `setAllowOverride()` prend un paramètre booléen unique définissant si vous
permettez de remplacer le service `CurrencyConverter` si un tel nom est déjà présent ou non.

### Déclarer les classes invokable

Ce qui est dommage avec la méthode `setService()`, c'est que vous devez créer l'instance du service
avant d'en avoir vraiment besoin. Si vous n'utilisez jamais le service, l'instanciation du service ne
sera qu'une perte de temps et de mémoire. Pour résoudre ce problème, le gestionnaire de services vous
fournit la méthode `setInvokableClass()`.

~~~php
<?php
// Déclare une classe invokable
$serviceManager->setInvokableClass(CurrencyConverter::class);
~~~

Dans l'exemple ci-dessus, nous transmettons au gestionnaire de service le nom de classe complet du service
au lieu de transmettre son instance. Avec cette technique, le service sera instancié par le gestionnaire de
service uniquement lorsque quelqu'un appelle la méthode `get(CurrencyConverter::class)`. Ceci est également
appelé *lazy loading*.

T> Les services dépendent souvent les uns des autres. Par exemple, le service de conversion de devises peut
T> utiliser le service de gestionnaire d'entités pour lire les taux de change en base de données.
T> L'inconvénient de la méthode `setInvokableClass()` est qu'elle ne permet pas de passer des paramètres
T> (dépendances) au service lors de l'instanciation de l'objet. Pour résoudre ce problème, vous pouvez
T> utiliser des fabriques, comme décrit ci-dessous.

### Déclarer une fabrique

Une *fabrique* (factory en anglais) est une classe qui ne peut faire qu'une seule chose - créer d'autres objets.

Vous déclarez une fabrique pour un service avec la méthode `setFactory()` du gestionnaire de service :

La fabrique la plus simple est @`InvokableFactory` - elle est analogue à la méthode setInvokableClass()` de la section précédente.

~~~php
<?php
use Laminas\ServiceManager\Factory\InvokableFactory;

// L'équivalent à la méthode setInvokableClass () de la section précédente.
$serviceManager->setFactory(CurrencyConverter::class, InvokableFactory::class);
~~~

Après avoir enregistré la fabrique, vous pouvez récupérer le service auprès du gestionnaire de service
comme d'habitude avec la méthode `get()`. Le service sera instancié uniquement lorsque vous l'extrayez
du gestionnaire de service (lazy loading).

Parfois, l'instanciation de service est plus complexe que la simple création de l'instance de service
avec l'opérateur `new` (comme le fait @`InvokableFactory`). Vous devrez peut-être passer certains paramètres
au constructeur du service ou appeler certaines méthodes juste après sa construction. Cette logique
d'instanciation complexe peut être encapsulée dans votre propre classe de *fabrique* personnalisée.
La classe de fabrique implémente alors l'interface @`FactoryInterface`[Laminas\ServiceManager\Factory\FactoryInterface] :

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

Comme nous le voyons dans la définition de @`FactoryInterface`[Laminas\ServiceManager\Factory\FactoryInterface],
la classe factory doit fournir la méthode magique `__invoke` renvoyant l'instance d'un seul service.
Le gestionnaire de service est transmis à la méthode `__invoke` en tant que paramètre `$container`;
il peut être utilisé lors de la construction du service pour accéder à d'autres services (pour injecter des
*dépendances*). Le deuxième argument (`$requestedName`) est le nom du service. Le troisième argument
(`$options`) peut être utilisé pour transmettre certains paramètres au service et n'est utilisé que
lorsque vous demandez le service avec la méthode `build()` du gestionnaire de services.

A titre d'exemple, écrivons une fabrique pour notre service de convertisseur de devises (voir le code
ci-dessous). Nous n'utilisons pas de logiques de construction complexes pour notre service
`CurrencyConverter` mais pour des services plus complexes, vous devrez peut-être en utiliser un. (???)

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

I> Techniquement, dans Laminas vous pouvez utiliser la même classe de fabrique pour instancier plusieurs
I> services qui ont un code d'instanciation similaire (pour cela, vous pouvez utiliser l'argument
I> `$requestedName` passé à la méthode `__invoke()` de la fabrique). Cependant, la plupart du temps,
I> vous allez créer une fabrique différente pour chaque service.

### Déclarer une fabrique abstraite

Un cas encore plus complexe d'usage de classe fabrique est lorsque vous devez déterminer au moment de
l'exécution quels noms de service doivent être enregistrés. Pour une telle situation, vous pouvez utiliser
une *fabrique abstraite*. Une classe de fabrique abstraite doit implémenter l'interface
@`AbstractFactoryInterface`[Laminas\ServiceManager\Factory\AbstractFactoryInterface] :

~~~php
<?php
namespace Laminas\ServiceManager\Factory;

use Interop\Container\ContainerInterface;

interface AbstractFactoryInterface extends FactoryInterface
{
    public function canCreate(ContainerInterface $container, $requestedName);
}
~~~

Une fabrique abstraite a deux méthodes : `canCreate()`
et `__invoke()`. La première est nécessaire pour tester si la fabrique peut créer le service avec un
certain nom et le second permet réellement de créer le service. Les méthodes prennent deux paramètres :
le service manager (`$container`) et le nom du service (`$requestedName`).

La différence une classe de fabrique basique est qu'elle ne crée généralement qu'un seul type de service
mais une fabrique abstraite peut créer dynamiquement autant de types de services qu'elle le souhaite.

Vous déclarez une fabrique abstraite avec la méthode `setAbstractFactory()` du gestionnaire de service.

T> Les fabriques abstraites sont une fonctionnalité puissante mais vous ne devriez les utiliser que lorsque
T> c'est vraiment nécessaire car elles ont un impact négatif sur les performances. Il est préférable
T> d'utiliser les fabriques habituelles (non abstraites).

### Déclarer un Alias de Service

Parfois, vous pouvez définir un *alias* pour un service. L'alias est comme un lien symbolique :
il fait référence à un service déjà déclaré. Pour créer un alias, utilisez la méthode `setAlias()`
du gestionnaire de services :

~~~php
<?php
// Déclare un alias pour le service CurrencyConverter
$serviceManager->setAlias('CurConv', CurrencyConverter::class);
~~~

Une fois déclaré, vous pouvez récupérer le service par son nom ou son alias à l'aide de la méthode `get()` du gestionnaire de services.

### Services partagés et non partagés

Par défaut, les services sont stockés dans le gestionnaire de services dans une seule instance. Egalement
appelé modèle de conception *singleton*. Par exemple, lorsque vous essayez de récupérer le service
`CurrencyConverter` deux fois, vous recevrez le même objet. On appelle celà un service *partagé*.

Mais, dans certaines situations (rares), vous devrez créer une *nouvelle* instance d'un service à chaque fois
que quelqu'un le demandera au gestionnaire de services. Un bon exemple est l'@`EventManager` - vous obtenez
une nouvelle instance à chaque fois que vous l'appelez.

Pour définir un service comme étant non partagé, vous pouvez utiliser la méthode `setShared()` du
gestionnaire de service :

~~~php
$serviceManager->setShared('EventManager', false);
~~~

### Configuration du gestionnaire de service

Sur votre site internet, vous utilisez le fichier de configuration du gestionnaire de services pour déclarer vos services
(au lieu d'appeler les méthodes du gestionnaire de service comme décrit ci-dessus).

Pour déclarer automatiquement un service dans le gestionnaire de service, la clé `service_manager` d'un
fichier de configuration est généralement utilisée. Vous pouvez placer cette clé dans un fichier de
configuration au niveau de l'application ou dans un fichier de configuration au niveau du module.

W> Si vous placez cette clé dans un fichier de configuration au niveau du module, faites attention au
W> risque d'écrasement de nom lors de la fusion des configs. Ne déclarez pas le même nom de service dans
W> différents modules.

Cette clé du `service_manager` devrait ressembler à ça :

~~~php
<?php
return [
    //...

    // On déclare les services sous cette clé
    'service_manager' => [
        'services' => [
            // Ici les instances de classe service
            //...
        ],
        'invokables' => [
            // Ici les instances de classe service invokable
            //...
        ],
        'factories' => [
            // Ici les instances de classe fabrique
            //...
        ],
        'abstract_factories' => [
            // Ici les instances de classe abstraite fabrique
            //...
        ],
        'aliases' => [
            // Ici les alias des services déclarés au dessus
            //...
        ],
        'shared' => [
            // Spécifiez ici quels services ne doivent pas être partagés
        ]
  ],

  //...
];
~~~

Dans l'exemple ci-dessus, vous pouvez voir que la clé `service_manager` peut contenir plusieurs sous-clés
pour déclarer des services de différentes manières :

* la sous-clé `services` (ligne 7) permet de déclarer des instances de classe;
* la sous-clé `invokables` (ligne 11) permet de déclarer  le nom complet d'un service; le service sera instancié en utilisant le lazy loading;
* la sous-clé `factories` (ligne 15) permet de déclarer une fabrique, capable de créer des instances d'un seul service;
* `abstract_factories`  (ligne 19) peut être utilisé pour déclarer des fabriques abstraites, qui sont capables de déclarer plusieurs services par leur nom;
* la sous-clé `aliases` (ligne 23) offre la possibilité de déclarer un alias pour un service.
* la sous-clé `shared` (ligne 27) permet de spécifier quels services ne doivent pas être partagés

À titre d'exemple, déclarons notre service `CurrencyConverter` et créons un alias pour celui-ci :

~~~php
<?php
use Laminas\ServiceManager\Factory\InvokableFactory;
use Application\Service\CurrencyConverter;

return [
    //...

    // On déclare les services sous cette clé
    'service_manager' => [
        'factories' => [
            // On déclare le service CurrencyConverter.
            CurrencyConverter::class => InvokableFactory::class
        ],
        'aliases' => [
            // On déclare un alias pour le service CurrencyConverter.
            'CurConv' => CurrencyConverter::class
        ],
  ],

  //...
];
~~~

## Les gestionnaires de plugins

Maintenant que vous comprenez ce qu'est le gestionnaire de services, il ne devrait pas être compliqué
pour vous de comprendre le concept des *gestionnaires de plugins*. Un *gestionnaire de plugins* est presque
pareil qu'un gestionnaire de services sauf qu'il ne peut instancier que des services de type
unique. Quel type de plugin un gestionnaire de plugin peut instancier en dur dans la classe
du gestionnaire de plugins. (..?)

Pourquoi auriez-vous besoin d'une chose pareille ? En fait, dans Laminas, les *gestionnaires de plugins* sont
largement utilisés car ils permettent d'instancier un plugin seulement quand cela est nécessaire
(ceci réduit l'utilisation du processeur et de la mémoire). Il y a un gestionnaire de plugins séparé pour les :

  * controleurs (la classe @`Laminas\Mvc\Controller\ControllerManager`)
  * plugins de controleurs (la classe @`Laminas\Mvc\Controller\PluginManager`)
  * aides de vues (la classe @`Laminas\View\HelperPluginManager`)
  * éléments de formulaire (la classe @`Laminas\Form\FormElementManager\FormElementManagerV3Polyfill` )
  * filtres (la classe  @`Laminas\Filter\FilterPluginManager`)
  * validateurs (la classe @`Laminas\Validator\ValidatorPluginManager`)
  * et probablement quelques autres

Le fait que chaque gestionnaire de plugins hérite de la classe de base  @`Laminas\ServiceManager\ServiceManager`
permet à tous les gestionnaires de plugins d'avoir une configuration similaire. Par exemple, les contrôleurs
sont déclarés sous la clé `controllers` dans le fichier *module.config.php* et cette clé peut avoir les
mêmes sous-clés : *services*, *invokables*, *factories*, *abstract_factories*, and *aliases*.
La clé *view_helpers* a la même structure pour la déclaration des aides de vue ainsi que la clé *controller_plugins*
utilisée pour la déclaration des plugins de contrôleur et ainsi de suite.

## À propos du gestionnaire d'événements

T> Dans cette section, nous allons donner quelques informations sur le gestionnaire d'événements.
T> Vous pouvez ignorer cette section de manière relativement sûre mais référez-vous à celle-ci si vous
T> prévoyez d'implémenter des écouteurs d'événements un peu plus poussés sur votre site.

Auparavant dans ce chapitre nous avons mentionné que le cycle de vie de l'application est constitué
d'événements. Une classe peut *déclencher* un événement et d'autres classes peuvent *écouter* des événements.
Techniquement, déclencher un événement signifie simplement appeler une autre méthode de "callback"
de classe. La gestion des événements est implémentée dans le composant @`Laminas\EventManager`.

T> Laminas (et en particulier son composant @`Laminas\Mvc`) ne dépend que très peu des événements pour fonctionner,
T> et de ce fait, son code source est une combinaison d'écouteurs d'événements qui est plutôt difficile à
T> comprendre. Heureusement, dans la plupart des cas, vous n'avez pas besoin de comprendre comment Laminas
T> déclenche et gère les événements en interne, il vous suffit de comprendre de quel événement il s'agit,
T> quels événements sont présents dans le cycle de vie de l'application et quelle est la différence entre *gestionnaire d'événements*
T> et *gestionnaire d'événements partagés*.

### Événements & MvcEvent

Un *événement* est techniquement une instance de la classe @`Laminas\EventManager\Event`.
Un événement a au moins les propriétés suivantes :

  * *name* - identifie de manière unique l'événement;
  * *target* - c'est un pointeur vers l'objet qui a déclenché l'événement;
  * et *params* - les arguments spécifiques aux événements transmis aux écouteurs d'événement.

Il est possible de créer des types d'événements personnalisés en étendant la classe
@`Event`[Laminas\EventManager\Event].
Par exemple, le composant @`Laminas\Mvc` définit le type d'événement personnalisé nommé @`Laminas\Mvc\MvcEvent`,
qui étend la classe `Event`et ajoute plusieurs propriétés et méthodes nécessaires pour que le composant
@`Laminas\Mvc` fonctionne.

### Gestionnaire d'événements et gestionnaire d'événements partagés

Il est important de comprendre la différence entre le gestionnaire d'événements *classique et le
gestionnaire d'événements *partagés*.

Le gestionnaire d'événements classique n'est pas stocké en tant que singleton dans le gestionnaire de
service. Chaque fois que vous demandez le service @`EventManager` à partir du gestionnaire de services,
vous recevez une nouvelle instance. Cela pour des raisons de confidentialité et de performance :

  * Il est supposé par défaut que les événements déclencheurs de la classe demanderont et enregistreront
    quelque part leur propre gestionnaire d'événements privé, car il ne veulent pas que les autres classes
    écoutent automatiquement ces événements. Les événements déclenchés par la classe sont supposés
    appartenir à cette classe en privé.

  * Si n'importe qui pouvait écouter n'importe quel événement déclenché par n'importe quelle classe, il y aurait
    un enfer de performance - trop d'écouteurs d'événements seraient invoqués, augmentant ainsi le temps de
    chargement de la page. Il est préférable d'éviter ca en gardant les événements privés.

Mais, dans le cas où quelqu'un a intentionnellement *besoin* d'écouter les événements des autres, il existe
un gestionnaire d'événements *partagés*. Le service @`SharedEventManager` est stocké dans le gestionnaire
de services sous la forme d'un singleton. Vous pouvez donc être sûr que tout le monde aura la même instance.

Avec @`SharedEventManager`, vous pouvez lier un écouteur à des événements privés déclenchés par certaines (ou plusieurs)
classes. Vous spécifiez le(s) identificateur(s) de classe que vous souhaitez écouter.

T> Vous trouverez des exemples pratiques d'écoute et de réaction à un événement dans le chapitre [Création
T> d'un nouveau module](#modules) et dans le chapitre [Gestion des utilisateurs, authentification et
T> filtrage des accès](#users).

## Résumé

Dans ce chapitre, nous avons vu la théorie sur les bases du fonctionnement d'un site basé sur Laminas.

Laminas utilise des espaces de noms PHP et des fonctionnalités de chargement automatique de classe, ce qui
simplifie le développement d'applications utilisant de nombreux composants tiers.
Les espaces de noms permettent de résoudre les collisions de noms entre les composants de code et
vous permettent de raccourcir les noms longs.

L'autoloading de classe permet d'utiliser n'importe quelle classe PHP dans n'importe quelle bibliothèque
installée avec Composer sans utiliser l'instruction `require_once`. Composer fournit également un
autoloader PSR-4 pour les classes situées dans les modules de votre application.

La plupart des composants de Laminas Framework nécessitent une configuration. Vous pouvez définir les
paramètres de configuration au niveau de l'application ou au niveau du module.

L'objectif principal de toute application est la gestion de la requête HTTP et la production d'une
réponse HTTP contenant généralement le code HTML de la page demandée. Lorsque le serveur Apache reçoit
une requête HTTP d'un navigateur client, il exécute le fichier *index.php*, également appelé script
d'entrée du site. Sur chaque requête HTTP, l'objet @`Laminas\Mvc\Application` est créé, dont le "cycle de vie"
est constitué de plusieurs étapes (ou événements).

La logique métier de l'application peut également être considérée comme un ensemble de services.
Dans Laminas Framework, le gestionnaire de services est un conteneur centralisé pour tous les services
d'application. Un service est généralement une classe PHP mais il peut également s'agir d'une variable ou
d'un tableau si nécessaire.
