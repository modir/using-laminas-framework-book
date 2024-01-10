# Modèle-Vue-Controleur{#mvc}

Dans ce chapitre, vous découvrirez les notions de modèles, vues et contrôleurs (le modèle de conception MVC).
Une application Web utilise le modèle MVC pour séparer la logique métier de la présentation.
Son but est de permettre la réutilisation du code et la séparation des préoccupations.

Les composants Laminas abordés dans ce chapitre:

|--------------------------------|---------------------------------------------------------------|
| *Composant*                    | *Description*                                                 |
|--------------------------------|---------------------------------------------------------------|
| @`Laminas\Mvc`                     | Implémente les classes de base du contrôleur, les plugins de contrôleur, etc. |
|--------------------------------|---------------------------------------------------------------|
| @`Laminas\View`                    | Implémente la fonctionnalité des conteneurs de variables, le rendus HTML et les aides de vues. |
|--------------------------------|---------------------------------------------------------------|
| @`Laminas\Http`                    | Implémente un wrapper autour des requêtes et réponses HTTP.        |
|--------------------------------|---------------------------------------------------------------|

## Obtenir l'exemple Hello World de GitHub

Dans ce chapitre et dans les suivants, nous vous fournirons des exemples de code que vous pourrez reproduire vous-même.
Il peut être difficile pour un novice d'écrire du code sans erreurs.
Si vous êtes bloqué ou si vous ne comprenez pas pourquoi votre code ne fonctionne pas, vous pouvez
télécharger l'application *Hello World* complète à partir de GitHub.
Les exemples de code de ce chapitre sont principalement issus de cet exemple d'application.

Pour télécharger l'application *Hello World*, rendez vous [ici](https://github.com/olegkrivtsov/using-laminas-book-samples)
et cliquez sur le bouton *Clone or Download* pour télécharger le code sous forme d'archive ZIP (voir figure 4.1).
Lorsque le téléchargement est terminé, décompressez l'archive dans un répertoire.

![Figure 4.1. L'exemple Hello World peut être téléchargé à partir de GitHub](../en/images/preface/samples_on_github.png)

Accédez ensuite au répertoire `helloworld` contenant le code source complet de l'exemple * Hello World *:

~~~text
/using-laminas-book-samples
   /helloworld
     ...
~~~

Le dossier Hello World est un site complet qui peut être installé sur votre machine.
Pour installer l'exemple, vous devez modifier le fichier host de votre serveur Apache ou en créer un nouveau.
Après avoir modifié le fichier, redémarrez le serveur Apache et ouvrez le site internet dans votre navigateur.

## Séparer la logique métier du code de présentation

Un site Web type a trois types de fonctionnalités: le code implémentant la logique applicative, le code implémentant l'interaction avec l'utilisateur et le code de rendu HTML (présentation).
Avant les frameworks PHP, les développeurs fusionnaient généralement ces trois types de code dans un gros fichier PHP, ce qui était pénible à tester et à maintenir,
en particulier avec des sites volumineux.

Mais depuis cette sombre époque, PHP est devenu un langage orienté objet, vous pouvez maintenant organiser votre code en classes.
Le modèle MVC (*Model-View-Controller*) est simplement un ensemble de règles vous indiquant comment organiser vos classes, pour les rendre faciles à maintenir.

En MVC, les classes implémentant la logique métier sont appelées des *modèles*,
les extraits de code permetant le rendu des pages HTML sont appelés des *vues* et les classes
chargées d'interagir avec l'utilisateur sont appelées des *contrôleurs*.

I> Les vues sont implémentée en tant qu'*extraits de code* et non en tant que classes.
I> En effet, les vues sont généralement très simples et ne contiennent que des balises HTML mélangées aux données PHP (déjà traitées) à afficher.

L'objectif principal du concept MVC est de séparer la logique métier (modèles) de l'affichage (vues). Ceci est également appelé la *séparation des préoccupations*, lorsque chaque couche ne fait que ses tâches spécifiques.

En séparant les modèles des vues, vous réduisez le nombre de dépendances entre elles.
Par conséquent, les modifications apportées à l'une des couches a le moins d'impact possible sur les autres couches. Cette séparation améliore également la * réutilisabilité du code *.
Par exemple, vous pouvez créer plusieurs vues pour les mêmes modèles (thèmes modifiables).

Pour mieux comprendre comment cela fonctionne, rappelons qu'un site web est un programme PHP recevant une requête HTTP d'un serveur web, et produisant une réponse HTTP.
La figure 4.2 montre comment une requête HTTP est traitée par l'application MVC et comment la réponse est générée:

![Figure 4.2. Traitement de requête HTTP dans une application MVC](../en/images/mvc/model-view-controller.png)

* Tout d'abord, un internaute saisi une URL dans son navigateur, par exemple *http://localhost*, son navigateur
  envoie alors une demande au serveur web.

* Le moteur PHP du serveur web exécute le script de base index.php. La seule chose que ce script fait est de créer
  une instance de la classe  @`Laminas\Mvc\Application`.

* L'Application utilise alors son composant *router* pour analyser l'URL et déterminer à quel contrôleur transmettre la requête.
  Si une correspondance de route est trouvée, le contrôleur est instancié et la *méthode Action* demandée est appelée.

* Dans la méthode Action du contrôleur, les paramètres (données entrantes) sont extraits des variables GET et POST.
  Pour traiter les données entrantes, le contrôleur instancie les classes des modèles nécessaires et appelle leurs méthodes.

* Les classes de modèle utilisent des algorithmes de logique métier pour traiter les données d'entrée et renvoyer les données de
  sortie. Les algorithmes de logique applicative sont spécifiques à l'application et incluent généralement la récupération des
  données issues de la base de données, la gestion des fichiers, l'interaction avec des systèmes externes, etc.

* Le résultat de l'appel des modèles est transmis à la vue qui correspond à la méthode Action.

* La vue utilise les données fournies par le modèle pour effectuer le rendu de la page HTML.

* Le contrôleur transmet la réponse HTTP qui résulte des étapes précédentes.

* Le serveur renvoie la page demandée sous forme de code HTML au navigateur de l'internaute.

* L'internaute voit la page s'afficher dans son navigateur.

Vous avez maintenant une idée de la façon dont les modèles, les vues et les contrôleurs coopèrent pour générer du
code HTML. Dans les sections suivantes, nous les décrivons plus en détails.

## Les Controleurs

Un contrôleur permet de faire communiquer l'application, les modèles et les vues: il reçoit en entrée une requête HTTP et
utilise le(s) modèle(s) et la vue correspondante pour produire une réponse HTTP.

Les contrôleurs appartiennent à un module. Ils sont situés généralement dans un sous-dossier `Controller` situé dans le dossier source du module
(illustré à la figure 4.3).

![Figure 4.3. Répertoire du contrôleur](../en/images/mvc/controller_dir.png)

L'application Laminas Skeleton vous fournit par défaut une classe `IndexController`.
L'`IndexController` est généralement le contrôleur principal de votre site web.
Son code est présenté ci-dessous (certaines parties du code ont été omises pour plus de simplicité) :

~~~php
<?php
namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        return new ViewModel();
    }
}
~~~

Dans l'exemple ci-dessus, vous pouvez voir que les contrôleurs sont définit dans un Namespace dédié (ligne 2).
Le contrôleur Index, comme tous les autres contrôleurs du module *Application*, est situé dans le namespace `Application\Controller`.

Un contrôleur est une classe généralement dérivée de la classe de base @`AbstractActionController`[Laminas\Mvc\Controller\AbstractActionController]
 (ligne 7).

Par défaut, votre contrôleur contient une *méthode d'action* appelée `indexAction()` (voir les lignes 9-12).
En général, vous allez ajouter d'autres méthodes d'action dans votre controleur qui corresponderont à d'autres pages du site.


T> Laminas reconnaît automatiquement les méthodes d'action par le suffixe `Action`. Si le nom d'une méthode de contrôleur n'a pas ce suffixe,
T> elle est considérés comme une méthode simple et non comme une action.

Comme son nom l'indique, une méthode d'action effectue une action sur le site, ce qui entraîne généralement la production d'une seule
page web. Le contrôleur index pourrait contenir plusieurs méthodes d'action correspondantes à différentes pages d'un site (tableau 4.1).
Par exemple, vous auriez une action "index" pour la page *Accueil*, "about" pour la page *À propos*, "contactUs" pour la page
*Contactez-nous* et éventuellement d'autres actions pour les autres pages.


{title="Table 4.1. Actions typiques du contrôleur index"}
|------------------------------------|--------------------------------------------------|
| *Methode Action*                    | *Description*                                    |
|------------------------------------|--------------------------------------------------|
| `IndexController::indexAction()`   | L'action "index" affiche la page d'accueil de votre site.|
|------------------------------------|--------------------------------------------------|
| `IndexController::aboutAction()`   | L'action "about" affiche la page A Propos du site. La page À propos contient des informations de contact et de copyright..                           |
|------------------------------------|--------------------------------------------------|
| `IndexController::contactUsAction()`|  L'action "contactUs" affiche la page Contactez-nous du site. Elle affiche pour formulaire pour contacter les auteurs du site.            |
|------------------------------------|--------------------------------------------------|

### Classe de base des contrôleurs

Chaque contrôleur de votre site hérite de la classe de base @`AbstractActionController`[Laminas\Mvc\Controller\AbstractActionController].
Dans la figure 4.4, le diagramme d'héritage de classe est présenté.

![Figure 4.4. Schéma d'héritage du contrôleur](../en/images/mvc/controller_inheritance.png)

La classe @`AbstractActionController`[Laminas\Mvc\Controller\AbstractActionController] vous fournit plusieurs méthodes utiles que vous pouvez
utiliser dans vos contrôleurs. Le tableau 4.2 fournit un bref résumé de ces méthodes :

{title="Table 4.2. Méthodes utiles de AbstractActionController"}
|----------------------------------|--------------------------------------------------------|
| Nom de la méthode                    | Description                                          |
|----------------------------------|--------------------------------------------------------|
| `getRequest()`                   | Récupère l'objet *@`Laminas\Http\Request`*, un objet qui représente les données de la requête HTTP.                   |
|----------------------------------|--------------------------------------------------------|
| `getResponse()`                  | Récupère l'objet *@`Laminas\Http\PhpEnvironment\Response`* permettant de définir les données de la réponse HTTP.                 |
|----------------------------------|--------------------------------------------------------|
| `getEventManager()`              | Renvoie l'objet @`Laminas\EventManager\EventManager`, permettant de déclencher et d'écouter des événements.       |
|----------------------------------|--------------------------------------------------------|
| `getEvent()`                     | Renvoie l'objet @`Laminas\Mvc\MvcEvent`, qui représente l'événement auquel le contrôleur répond.                  |
|----------------------------------|--------------------------------------------------------|
| `getPluginManager()`             | Renvoie l'objet @`Laminas\Mvc\Controller\PluginManager`, qui peut être utilisé pour déclarer des plugins de contrôleur.  |
|----------------------------------|--------------------------------------------------------|
| `plugin($name, $options)`        | Cette méthode permet d'accéder à un plugin de contrôleur donnée via son nom.                                   |
|----------------------------------|--------------------------------------------------------|
| `__call($method, $params)`       | Permet d'appeler un plugin indirectement en utilisant la méthode magique PHP `__call`.                                          |
|----------------------------------|--------------------------------------------------------|

Comme vous pouvez le voir dans le tableau ci-dessus, la classe de contrôleur de base vous permet d'accéder aux données de requête
et de réponse HTTP et vous fournit un accès au gestionnaire d'événements. Il vous donne également la possibilité de déclarer et
d'appeler des plugins de contrôleurs (nous verrons plus loin dans ce chapitre ce qu'est un plugin de contrôleur).

## Récupération des données d'une requête HTTP

Dans la méthode d'action d'un contrôleur, vous devrez peut-être récupérer les données de la requête HTTP (les données telles que
les variables GET et POST, les cookies, les en-têtes HTTP, etc.).
Pour cela, Laminas Framework vous fournit la classe  @`Laminas\Http\Request`, qui fait partie du composant @`Laminas\Http`.

Pour obtenir l'objet de requête HTTP, dans votre méthode d'action, vous pouvez utiliser le code suivant :

~~~php
// Récupération de l'objet de requete HTTP
$request = $this->getRequest();
~~~

Le code ci-dessus renvoie une instance de la classe @`Laminas\Http\Request`, contenant toutes les données de la requête HTTP.
Dans le tableau 4.3, vous trouverez les méthodes les plus utilisées de la classe  @`Request`[Laminas\Http\Request]
avec une brève description.

{title="Table 4.3. Méthodes de la classe `Laminas\Http\Request`."}
|----------------------------------------|------------------------------------------------------|
| *Nom de la méthode*                          | *Description*                                        |
|----------------------------------------|------------------------------------------------------|
| `isGet()`                              | Vérifie s'il s'agit d'une requête GET.                     |
|----------------------------------------|------------------------------------------------------|
| `isPost()`                             | Vérifie s'il s'agit d'une requête POST.                    |
|----------------------------------------|------------------------------------------------------|
| `isXmlHttpRequest()`                   | Vérifie si cette requête est une requête AJAX.           |
|----------------------------------------|------------------------------------------------------|
| `isFlashRequest()`                     | Vérifie si cette requête est une requête Flash.            |
|----------------------------------------|------------------------------------------------------|
| `getMethod()`                          | Renvoie une méthode pour cette requête.                 |
|----------------------------------------|------------------------------------------------------|
| `getUriString()`                       | Renvoie l'URI de cet objet requête sous forme de string. |
|----------------------------------------|------------------------------------------------------|
| `getQuery($name, $default)`            | Renvoie un paramètre de la requête spécifique ($name) ou tous les paramètres de la requête. Si un paramètre n'est pas trouvé, renvoie la valeur `$default`.|
|----------------------------------------|------------------------------------------------------|
| `getPost($name, $default)`             | Renvoie les paramètres post de la requete ou un seul de ces paramètres si un $name est spécifié.               |
|----------------------------------------|------------------------------------------------------|
| `getCookie()`                          | Retourne l'en-tête du cookie.                           |
|----------------------------------------|------------------------------------------------------|
| `getFiles($name, $default)`            | Renvoie les paramètres File liés à un fichier spécifié ($name) ou à l'ensemble des fichiers.                         |
|----------------------------------------|------------------------------------------------------|
| `getHeaders($name, $default)`          | Renvoie l'ensemble des en-têtes passé dans la requete ou de tous les en-têtes d'un certain nom/type.               |
|----------------------------------------|------------------------------------------------------|
| `getHeader($name, $default)`           | Renvoie un en-tête par `$name`.  Si un en-tête est introuvable, renvoie la valeur $default.                        |
|----------------------------------------|------------------------------------------------------|
| `renderRequestLine()`                  | Renvoie la ligne de requête formatée (première ligne) pour cette requête HTTP.                                   |
|----------------------------------------|------------------------------------------------------|
| `fromString($string)`                  | Méthode statique qui génère un objet Request à partir d'une string HTTP Request bien formatée                      |
|----------------------------------------|------------------------------------------------------|
| `toString()`                           | Renvoie la requête HTTP brute au format string.            |
|----------------------------------------|------------------------------------------------------|

## Récupération des variables GET et POST

Pour obtenir simplement une variable GET ou POST à ​​partir d'une requête HTTP, utilisez le code suivant :

~~~php
// Récupération d'une variable passée en GET
$getVar = $this->params()->fromQuery('var_name', 'default_val');

// Récupération d'une variable passée en POST
$postVar = $this->params()->fromPost('var_name', 'default_val');
~~~

Dans l'exemple ci-dessus, nous avons utilisé le plugin de controleur @`Params`, qui vous fournit des méthodes pratiques pour
accéder aux variables GET et POST, aux fichiers téléchargés, etc.


À la ligne 2, nous utilisons la méthode `fromQuery()` pour extraire une variable ayant le nom "var_name" passée en GET.
Si une telle variable n'est pas présente, la valeur par défaut "default_val" est renvoyée.
La valeur par défaut est très pratique, car vous n'avez pas besoin d'utiliser la fonction PHP `isset()` pour tester si la
variable existe.

À la ligne 5, nous utilisons la méthode`fromPost()` pour extraire la variable de POST. La signification des paramètres de
cette méthode est la même que pour la méthode `fromQuery()`.

T> Avec Laminas, vous ne devez pas accéder aux paramètres de requête via les globales PHP `$_GET` et `$_POST`
T> Au lieu de cela, utilisez l'API fournie par Laminas pour récupérer ces données.

## Transformer les données en réponse HTTP

Même si vous interagissez rarement directement avec les données de réponse HTTP, vous pouvez le faire à l'aide de la méthode
`getResponse()` fournie par la classe de base @`AbstractActionController`[Laminas\Mvc\Controller\AbstractActionController].
La méthode `getResponse()` renvoie une instance de la classe @`Laminas\Http\PhpEnvironment\Response`.
Le tableau 4.4 contient les méthodes les plus importantes de cette classe :

{title="Table 4.4. Méthodes de la classe Laminas\Http\PhpEnvironment\Response."}
|----------------------------------------|--------------------------------------------------------|
| *Nom de la méthode*                          | *Description*                                          |
|----------------------------------------|--------------------------------------------------------|
| `fromString($string)`                  | Remplit l'objet Response d'une chaîne.                  |
|----------------------------------------|--------------------------------------------------------|
| `toString()`                           | Transforme toute la réponse en chaîne de réponse HTTP.       |
|----------------------------------------|--------------------------------------------------------|
| `setStatusCode($code)`                 | Définit le statut code HTTP et (facultativement) son message.        |
|----------------------------------------|--------------------------------------------------------|
| `getStatusCode()`                      | Récupère le status code HTTP.                            |
|----------------------------------------|--------------------------------------------------------|
| `setReasonPhrase($reasonPhrase)`       | Définit le message de statut HTTP.                          |
|----------------------------------------|--------------------------------------------------------|
| `getReasonPhrase()`                    | Récupère le message du status HTTP.                              |
|----------------------------------------|--------------------------------------------------------|
| `isForbidden()`                        | Vérifie si le code de réponse est une 403 Forbidden.          |
|----------------------------------------|--------------------------------------------------------|
| `isNotFound()`                         | Vérifie si le status code indique que la ressource est introuvable (code d'état 404). |
|----------------------------------------|--------------------------------------------------------|
| `isOk()`                               | Vérifie si la réponse est réussie.             |
|----------------------------------------|--------------------------------------------------------|
| `isServerError()`                      | Vérifie si la réponse a un status code 5xx.             |
|----------------------------------------|--------------------------------------------------------|
| `isRedirect()`                         | Vérifie si la réponse a un status code de redirection (303).           |
|----------------------------------------|--------------------------------------------------------|
| `isSuccess()`                          | Vérifie si la réponse a un status code de succès (200)         |
|----------------------------------------|--------------------------------------------------------|
| `setHeaders(Headers $headers)`         | Permet de définir les en-têtes de la réponse HTTP.                        |
|----------------------------------------|--------------------------------------------------------|
| `getHeaders()`                         | Retourne la liste des en-têtes de la réponse HTTP.                  |
|----------------------------------------|--------------------------------------------------------|
| `getCookie()`                          | Récupère l'en-tête du cookie.                               |
|----------------------------------------|--------------------------------------------------------|
| `setContent($value)`                   | Définit le contenu brut de la réponse.                             |
|----------------------------------------|--------------------------------------------------------|
| `getContent()`                         | Retourne le contenu brut de la réponse .                          |
|----------------------------------------|--------------------------------------------------------|
| `getBody()`                            | Obtient et décode le contenu de la réponse.          |
|----------------------------------------|--------------------------------------------------------|

Par exemple, utilisez le code suivant pour définir le statut code 404 d'une réponse:

~~~php
$this->getResponse()->setStatusCode(404);
~~~

Utilisez le code suivant pour ajouter un en-tête à la réponse :

~~~php
$headers = $this->getResponse()->getHeaders();
$headers->addHeaderLine(
             "Content-type: application/octet-stream");
~~~

Utilisez le code suivant pour définir le contenu de la réponse :

~~~php
$this->getResponse()->setContent('Some content');
~~~

## Conteneurs de variable

Une fois que vous avez récupéré les données de la requête HTTP, vous allez faire quelque chose avec ces données (généralement, vous
allez traiter les données avec votre couche modèle) pour les renvoyez depuis la méthode d'action.

Vous pouvez voir que la méthode `indexAction()` du contrôleur Index renvoie une instance de la classe  @`ViewModel`[Laminas\View\Model\ViewModel].
La classe @`ViewModel`[Laminas\View\Model\ViewModel] est une sorte de *conteneur de variable*.
Toutes les variables passées en parametres seront alors automatiquement accessibles par la vue qui y est liée.

Voyons un exemple concret. Nous allons créer une autre méthode d'action dans notre classe `IndexController`, que nous
appellerons `aboutAction()`. L'action "about" affichera la page *A Propos* de notre site. Dans la méthode d'action, nous allons
créer deux variables contenant des informations de notre site, et retourner ces variables pour le rendu dans une vue à l'aide de
l'objet @`ViewModel`[Laminas\View\Model\ViewModel] :

~~~php
// La méthode d'action "about"
public function aboutAction()
{
    $appName = 'HelloWorld';
    $appDescription = 'Un exemple d\'application pour le guide d\'utilisation de Laminas Framework';

    // Renvoie les variables pour afficher le script à l'aide du
    // conteneur de variables ViewModel
    return new ViewModel([
        'appName' => $appName,
        'appDescription' => $appDescription
    ]);
}
~~~

Aux lignes 4-5, nous créons les variables `$appName` et `$appDescription`. Elles stockent respectivement le nom et la description
de notre application.

Dans les lignes 9-12, nous passons les variables que nous avons créées au constructeur de l'objet
@`ViewModel`[Laminas\View\Model\ViewModel] en tant que tableau associatif. Les clés de ce tableau définissent les noms des variables qui,
seront accessibles dans la vue.

La classe @`ViewModel`[Laminas\View\Model\ViewModel] fournit plusieurs méthodes pour définir ou extraire des variables.
La table 4.5 fournit le résumé de ces méthodes:

{title="Table 4.5.  Méthodes de la classe ViewModel"}
|--------------------------------|---------------------------------------------------------------|
| *Nom de la méthode*                  | *Description*                                                 |
|--------------------------------|---------------------------------------------------------------|
| `getVariable($name, $default)` | Retourne une variable en fonction de son nom (ou une valeur par défaut si la variable n'existe pas)..                                              |
|--------------------------------|---------------------------------------------------------------|
| `setVariable($name, $value)`   | Définit une variable.                                              |
|--------------------------------|---------------------------------------------------------------|
| `setVariables($variables, $overwrite)`|  Définit un groupe de variables, en écrasant éventuellement les variables existantes.                                                |
|--------------------------------|---------------------------------------------------------------|
| `getVariables()`               | Retourne toutes les variables sous forme de tableau.                            |
|--------------------------------|---------------------------------------------------------------|
| `clearVariables()`             | Supprime toutes les variables.                                        |
|--------------------------------|---------------------------------------------------------------|

## Afficher les erreurs

Parfois, les choses ne se passent pas comme prévues et une erreur se produit.
Par exemple, vous vous attendez à recevoir une variable GET de la requête HTTP mais elle est manquante ou sa valeur n'est pas valide.
Pour afficher cette erreur, vous générez généralement un [statut code](https://en.wikipedia.org/wiki/List_of_HTTP_status_codes) 4xx
dans la réponse HTTP que vous renvoyez depuis l'action du contrôleur.

Par exemple, dans une application type blog, supposons qu'un utilisateur saisisse l'URL suivante dans la barre de navigation de son
navigateur:  *http://localhost/posts/view?id=10000*. Son intention est d'afficher l'article qui a un ID = 10000.
Si l'article correspondant n'existe pas, nous ne pouvons pas l'afficher et nous allons utiliser le code 400(Page introuvable) comme
code d'état dans la réponse:

~~~php
// L'action "view" affiche un article de blog avec l'ID spécifié
public function viewAction()
{
    // Récupère l'argument ID de la requete GET
    $id = (int)$this->params()->fromQuery('id', -1);

    // Valide l'argument
    if ($id<1) {
        // Erreur - nous ne pouvons pas afficher un tel post
        $this->getResponse()->setStatusCode(404);
        return;
    }

    // On essaye de trouver le message (nous omettons la requête SQL pour plus de simplicité).
    $post = ...
    if (!$post) {
        // Erreur - message non trouvé
        $this->getResponse()->setStatusCode(404);
        return;
    }

    // Suite de l'execution du script
    // ...
}
~~~

Lorsque Laminas se retrouve face à un code 4xx, il redirige l'utilisateur vers une page d'erreur dédiée.
Nous parlerons des pages d'erreur plus tard dans ce chapitre.

Une autre façon de générer une erreur (critique) est de déclancher une exception, par exemple, comme ceci:

~~~php
throw new \Exception("L'article dont l'ID=$id est introuvable");
~~~

Lorsque qu'une exception non gérée par Laminas est rencontrée, s'affiche une autre page d'erreur spécifique avec des informations
sur cette exception.

## Déclaration des controleurs

Toutes les classes contrôleurs appartenant à un module doivent être déclarées dans le fichier de configuration spécifique, le
fichier module.config.php. Si votre contrôleur ne dépend pas de services spécifique (il n'a pas de dépendances), vous pouvez
le déclarer comme celà :

~~~php
<?php
use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    // ...

    'controllers' => [
        'factories' => [
            Controller\IndexController::class => InvokableFactory::class
            // Déclarez les autres contrôleurs ici
        ],
    ],

    // ...
];
~~~

Ligne 7, nous pouvons voir la clé controllers, elle contient en sous-clé des factories.
Pour déclarer un contrôleur, ajoutez une ligne au format clé => valeur.
La clé doit être le nom complet de la classe du contrôleur, comme `\Application\Controller\IndexController` (nous pouvons utiliser
le mot-clé PHP ::class pour la résolution de son nom). La valeur doit correspondre au nom de la classe factory qui génère le
contrôleur.
Dans notre cas, nous utilisons la norme InvokableFactory, mais vous pouvez créer une classe factory spécifique si vous en avez besoin.

I> En utilisant la classe InvokableFactory, vous indiquez à Laminas Framework qu'il peut instancier le contrôleur avec un nouvel
I> opérateur.
I> C'est la manière la plus simple d'instancier un contrôleur. Vous pouvez également déclarer votre propre classe factory pour créer
I> une instance du contrôleur et y injecter des dépendances.

### Déclaration d'un Controller Factory

Si votre classe de contrôleur doit appeler un service (cela arrive très souvent), vous devez demander
ce service au *service manager* (voir le chapitre [Operation du site](#operation)) et le passer au constructeur du contrôleur,
ensuite le contrôleur déclare le service que vous avez passé dans une propriété privée pour un usage interne (également
appelé injection de dépendance).

Cette procédure est généralement implémentée dans une classe factory. Par exemple, supposons que notre classe de
contrôleur doit utiliser un service `CurrencyConverter` qui convertira de l'argent en USD en EUR. La classe
factory de notre contrôleur ressemblera à ci-dessous:

~~~php
<?php
namespace Application\Controller\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Application\Service\CurrencyConverter;
use Application\Controller\IndexController;

// Factory class
class IndexControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container,
                     $requestedName, array $options = null)
    {
        // Obtention de l'instance du service CurrencyConverter auprès du service manager.
        $currencyConverter = $container->get(CurrencyConverter::class);

        // Création d'une instance du contrôleur et transmission de la dépendance au constructeur du contrôleur.
        return new IndexController($currencyConverter);
    }
}
~~~

Ensuite, vous déclarez le contrôleur de la même manière, mais spécifiez la classe factory que nous venons d'écrire:

~~~php
<?php
return [
    // ...

    'controllers' => [
        'factories' => [
            Controller\IndexController::class => Controller\Factory\IndexControllerFactory::class
        ],
    ],

    // ...
];
~~~

I> Si vous connaissez Laminas Framework 2, vous remarquerez peut-être que les choses sont maintenant un peu différentes.
I> Dans ZF2, il y avait la méthode getServiceLocator() dans la classe de base  @`AbstractActionController`[Laminas\Mvc\Controller\AbstractActionController]
I> qui permettait d'obtenir les dépendances du contrôleur sans l'utilisation de classe factory.
I> Dans Laminas, vous devez transmettre les dépendances explicitement. C'est un peu plus galère, mais cela évite les dépendances "cachées"
I> et rend votre code plus clair et plus facile à comprendre.

### LazyControllerAbstractFactory

L'écriture d'une classe factory pour presque tous les contrôleurs peut sembler ennuyeux à première vue.
Si vous êtes paresseux et que vous ne voulez pas le faire, vous pouvez utiliser la classe factory @`LazyControllerAbstractFactory`.

Cette classe factory @`LazyControllerAbstractFactory` utilise *reflexion* pour déterminer les services dont votre contrôleur a
besoin. Vous n'avez qu'à *saisir* les arguments du constructeur du contrôleur, et la classe factory récupérera elle-même les services
demandé pour les transmettre au constructeur.

Par exemple, pour injecter le service `CurrencyConverter` dans votre contrôleur, assurez-vous que son constructeur ressemble à :

~~~php
namespace Application\Controller;

use Application\Service\CurrencyConverter;

class IndexController extends AbstractActionController
{
    // Ici, nous allons déclarer le service pour un usage interne.
    private $currencyConverter;

    // Saisissez les arguments du constructeur pour obtenir les dépendances.
    public function __construct(CurrencyConverter $currencyConverter)
    {
        $this->currencyConverter = $currencyConverter;
    }
}
~~~

Ensuite, vous déclarez le contrôleur de la même manière, mais spécifiez la classe factory @`LazyControllerAbstractFactory` :

~~~php
<?php
use Laminas\Mvc\Controller\LazyControllerAbstractFactory;

return [
    // ...

    'controllers' => [
        'factories' => [
            Controller\IndexController::class => LazyControllerAbstractFactory::class
        ],
    ],

    // ...
];
~~~

## Quand créer un nouveau contrôleur ?

Lorsque la taille de votre site augmente, vous devez créer de nouveaux contrôleurs au lieu de placer toutes les actions sur le controleur
`IndexController`.

T> Il n'est pas recommandé de créer des contrôleurs géants avec des centaines d'actions, car ils sont difficiles à comprendre et
T> à gérer.

Il est recommandé de créer un nouveau contrôleur pour chaque modèle (ou pour les plus importants) de votre domaine de logique métier.

Par exemple, vous pouvez créer un controleur `UserController` pour gérer les utilisateurs de votre site.
Ce contrôleur aurait comme action par défaut "index" qui afficherait une page listant tous les utilisateurs, une action "add" pour
ajouter un nouvel utilisateur, "edit" pour modifier le profil d'un utilisateur et "delete" pour supprimer un utilisateur.

Dans l'idée, vous pouvez aussi créez un controleur `PurchaseController` dont les actions permetteraint de gérer les achats de
produits et la gestion du panier. Un controleur `DownloadController` dont les actions géreraint les téléchargements de fichiers, etc.

## Plugins de Controleur

Un * plugin de contrôleur * est une classe qui étend les fonctionnalités de * tous les contrôleurs *.

I> Sans plugins, pour étendre la fonctionnalité de tous les contrôleurs, vous devez
I> créer une classe de base personnalisée, par exemple `BaseController`, et
I> dériver d'autres contrôleurs de cette classe de base. Cette façon de faire peut également être utilisée, mais du point de vue des créateurs de Laminas, les plugins sont une meilleure
I> solution, car ils utilisent *la composition des classes* [^foo],
I> ce qui offre une meilleure flexibilité par rapport à l'héritage de classe. Vous déclarez votre contrôleur de plugin et il devient automatiquement accessible
I> depuis tous les contrôleurs de votre application (la classe de base @`AbstractActionController` utilise la méthode magique` __call() `de PHP pour les
I> faire appel aux plugins de contrôleurs déclarés).

[^foo]: *La composition des classes* est une relation entre deux classes qui est mieux décrite comme un "has-a"
    et une relation "whole/part". La classe propriétaire contient une référence à une autre classe (le plugin).
    Le propriétaire est responsable de la durée de vie de l'objet qu'il détient.

Il y a plusieurs plugins de contrôleur standard disponibles (tableau 4.6). Nous en avons déjà utilisé un (le plugin @`Params`)
dans l'un de nos exemples précédents.

{title="Table 4.6. Plugins de contrôleur standard"}
|------------------------------------------|------------------------------------------------------|
| *Classe de plugin standard*                  | *Description*                                        |
|------------------------------------------|------------------------------------------------------|
| @`Params`                                 | Permet de récupérer des variables de la requête HTTP, y compris les variables GET et POST.                    |
|------------------------------------------|------------------------------------------------------|
| @`Url`[Laminas\Mvc\Controller\Plugin\Url]                                    | Permet de générer des URL absolues ou relatives depuis un contrôleur.                             |
|------------------------------------------|------------------------------------------------------|
| @`Layout`[Laminas\Mvc\Controller\Plugin\Layout]                                 | Donne accès au modèle de vue layout pour transmettre des données template du layout.                                     |
|------------------------------------------|------------------------------------------------------|
| @`Identity`[Laminas\Mvc\Plugin\Identity\Identity]                               | Renvoie l'identité de l'utilisateur qui s'est connecté.                                            |
|------------------------------------------|------------------------------------------------------|
| @`FlashMessenger`[Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger]                         | Permet de définir des messages "flash" qui sont stockés en session et peuvent être affichés sur une autre page.|
|------------------------------------------|------------------------------------------------------|
| @`Redirect`[Laminas\Mvc\Controller\Plugin\Redirect]                               | Permet de rediriger la requête vers la méthode d'action d'un autre contrôleur. |
|------------------------------------------|------------------------------------------------------|
| @`PostRedirectGet`[Laminas\Mvc\Plugin\Prg\PostRedirectGet]                        | Redirige la requête POST en convertissant toutes les variables POST en GET. |
|------------------------------------------|------------------------------------------------------|
| @`FilePostRedirectGet`[Laminas\Mvc\Plugin\FilePrg\FilePostRedirectGet]                    | Redirige la requête POST, en préservant les fichiers téléchargés.|
|------------------------------------------|------------------------------------------------------|

À l'intérieur de la méthode d'action du contrôleur, vous accédez à un plugin de la façon suivante :

~~~php
// Accès au plugin Url
$urlPlugin = $this->url();

// Accès au plugin Layout
$layoutPlugin = $this->layout();

// Accès au plugin de redirection
$redirectPlugin = $this->redirect();
~~~

Comme alternative, vous pouvez appeler un plugin par son nom complet avec la méthode `plugin()` fournie par le contrôleur de base,
comme ceci :

~~~php
use Laminas\Mvc\Controller\Plugin\Url;

// Dans l'action de votre contrôleur, utilisez la méthode plugin().
$urlPlugin = $this->plugin(Url::class);
~~~

### Écrire son propre plugin de contrôleur

Dans vos sites, vous devrez probablement créer vos propres plugins de contrôleurs.
Par exemple, supposons que vous ayez besoin que tous les contrôleurs soient capables de vérifier
si un utilisateur du site est autorisé à accéder à certaines actions. Cela peut être
implémenté avec la classe `AccessPlugin`.

Le plugin de contrôleur doit alors être dérivé de la classe @`AbstractPlugin`[Laminas\Mvc\Controller\Plugin\AbstractPlugin].
Les plugins sont généralement situé dans leur propre namespace `Plugin`, qui est imbriqué dans le namespace du
`Controller` :

~~~php
<?php
namespace Application\Controller\Plugin;

use Laminas\Mvc\Controller\Plugin\AbstractPlugin;

// Classe Plugin
class AccessPlugin extends AbstractPlugin
{
    // Cette méthode vérifie si l'utilisateur est
    //autorisé à accéder à l'action
    public function checkAccess($actionName)
    {
        // ...
    }
}
~~~

Pour que Laminas Framework ai connaissance de votre plugin, vous devez le déclarer dans votre fichier
*module.config.php* sous la clé `controller_plugins`.
Voir ci-dessous pour l'exemple:

~~~php
<?php
return [
    // ...

    'controller_plugins' => [
        'factories' => [
            Controller\Plugin\AccessPlugin::class => InvokableFactory::class,
        ],
        'aliases' => [
            'access' => Controller\Plugin\AccessPlugin::class,
        ]
    ],

    // ...
];
~~~

I> Veuillez noter que nous déclarons également un alias pour le plugin pour pouvoir obtenir le plugin plus simplement,
I> par son nom abrégé.

Après cela, vous serez en mesure d'accéder à votre plugin personnalisé depuis toutes les actions de votre contrôleur,
comme ceci :

~~~php
// Vérifiez si l'utilisateur du site est autorisé à accédeder à la page "index"
$isAllowed = $this->access()->checkAccess('index');
~~~

## Les Vues

Les vues appartiennent à la couche de présentation de l'application, leur objectif est de produire une sortie HTML
renvoyée par le serveur aux visiteurs du site.

Avec Laminas Framework, vous implémentez une vue en tant que fichier template, qui est un fichier avec l'extension
.phtml ("phtml" signifie PHP + HTML).
Les vues sont nommées ainso car elles contiennent généralement du code HTML mélangé avec des extraits de code PHP utilisés
pour le rendu des pages.

Les vues sont situées généralement dans le sous-répertoire *view* du module (voir figure 4.5) :

![Figure 4.5. Dossier view](../en/images/mvc/views_dir.png)

Q> **Pourquoi les fichiers vues ne sont-ils pas stockés dans le répertoire source du module ?**
Q>
Q> Les vues (fichiers `.phtml`) ne sont pas stockés dans le répertoire `src/`du module car ce ne sont pas des classes PHP
Q> habituelles et elles n'ont pas besoin d'être chargées par une fonction d'autoloading.
Q> Les templates de vues sont résolus par une classe Laminas dédiée appelée *view resolver*.
Q> C'est pourquoi les vues sont stockées dans le dossier `view` du module.

Les vues peuvent avoir des comportements différents, en fonction des variables que vous leur transmettez à partir de la
méthode d'action du contrôleur.
Les données sont transmises pour afficher les vues à l'aide d'un conteneur de variable @`ViewModel`[Laminas\View\Model\ViewModel].

Par exemple, implémentons la vue correspondant à l'action `aboutAction()` de notre contrôleur index.
La page A Propos (about) affiche le titre et quelques informations sur notre application Hello World.

Pour créer le fichier de la vue, dans votre fenêtre NetBeans, accédez au dossier *view/application/index* (voir figure 4.6)
et faites un clic droit sur le nom du dossier "index".
Dans le menu contextuel qui apparaît, sélectionnez l'élément de menu Nouveau->Fichier PHP.

![Figure 4.6.  Menu contextuel](../en/images/mvc/new_php_file.png)

Dans la boîte de dialogue "Nouveau fichier PHP" qui apparaît (figure 4.7), entrez le nom about.phtml et cliquez sur le
bouton Terminer.

![Figure 4.7. Menu contextuel](../en/images/mvc/new_php_file_dialog.png)

La vue *about.phtml*sera créée et affichée dans le volet droit de la fenêtre NetBeans.
Dans ce fichier, entrez ce qui suit :

~~~php
<h1>A Propos</h1>

<p>
    L'application Hello World.
</p>

<p>
    Nom de l'application : <?= $this->escapeHtml($appName); ?>
</p>

<p>
    La description de l'application : <?= $this->escapeHtml($appDescription); ?>.
</p>
~~~

Comme vous pouvez le voir, la vue est une page HTML habituelle avec plusieurs fragments de code PHP.
Un script de vue restitue simplement les données que vous lui transmettez avec le conteneur de variable
@`ViewModel`[Laminas\View\Model\ViewModel].
Par exemple, à la ligne 8, nous obtenons la valeur de la variable `$appName` et l'affichons dans le flux de sortie
standard.

T> Dans la vue, vous pouvez facilement accéder aux variables transmises depuis l'action du contrôleur.
T> Par exemple, pour obtenir la valeur de la variable contenant le nom d'application, utilisez la syntaxe
T> `$appName` or `$this->appName`.
T> Ces deux manières d'accéder à la variable sont valent, la première nécessite moins d'écriture (donc nous
T> l'utiliserons par la suite) mais la seconde permet de la différencier des variables du fichier .phtml.

Notez aussi que nous utilisons l'aide de vue (view helper) @`EscapeHtml` pour *échapper* la chaîne affichée sur la page
afin de rendre le site web résistant aux attaques de pirates.

W> Vous devriez toujours échapper les variables que vous affichée sur vos pages. L'échappement permet d'être sûr
W> qu'aucun code malveillant n'est injecté sur votre page.

I> Dans votre vue, vous pouvez également utiliser des opérations de contrôle de simples (comme if, foreach ou switch)
I> pour faire varier l'apparence de la page en fonction de la valeur d'une variable.

Maintenant, regardons à quoi ressemble notre page A Propos.
Saisissez l'URL "http://localhost/application/about" dans la barre de navigation de votre navigateur.
La page *À propos* devrait apparaître (voir figure 4.8) :

![Figure 4.8. Page A Propos](../en/images/mvc/about_page.png)

T> En général, le code PHP que vous utilisez à l'intérieur des vues doit être aussi simple que possible.
T> Les vues ne modifient généralement pas les données transmises par le contrôleur.
T> Par exemple, une vue peut utiliser le modèle que vous lui passez pour parcourir les lignes d'une table de
T> base de données et afficher les éléments sur une page HTML, mais elle ne doit jamais créer ou modifier une table.

## Les Aides de Vues (View Helpers)

Une *aide de vue* est typiquement une (relativement) simple classe PHP dont le but est de générer le rendu
d'une partie de la vue. Vous pouvez appeler des aides de vue à partir de n'importe quelle vue.
Avec les aides de vue, vous pouvez créer des widgets réutilisables (comme des menus, des barres de navigation,
etc.) pour vos pages web.

I> Les aides de vue sont analogues aux plugins de contrôleurs : les plugins de contrôleurs permettent
I> d'"étendre" la fonctionnalité des contrôleurs, et les aides de vue permettent "d'étendre" la
I> fonctionnalité des vues.

Laminas fournit de nombreuses aides de vue standard prêtes à l'emploi.
Dans le tableau 4.7, certains d'entre elles sont présentées avec une brève description :

{title="Table 4.7. Aides de vue standard"}
|------------------------------------------|------------------------------------------------------|
| *Standard Plugin Class*                  | *Description*                                        |
|------------------------------------------|------------------------------------------------------|
| @`BasePath`                               | Permet de récupérer le chemin de base de l'application Web, qui est le chemin absolu vers `APP_DIR`.             |
|------------------------------------------|------------------------------------------------------|
| @`Url`[Laminas\View\Helper\Url]                                    | Permet de générer des adresses URL absolues ou relatives depuis une vue.                          |
|------------------------------------------|------------------------------------------------------|
| @`ServerUrl`                              | Récupère l'URL de la requête en cours.                 |
|------------------------------------------|------------------------------------------------------|
| @`Doctype`                                | Une aide pour définir et récupérer le doctype HTML de la page.                                     |
|------------------------------------------|------------------------------------------------------|
| @`HeadTitle`                              | Une aide pour définir le titre HTML de la page Web.                                     |
|------------------------------------------|------------------------------------------------------|
| @`HtmlList`                               | Une aide pour générer des listes HTML ordonnées et non ordonnées. |
|------------------------------------------|------------------------------------------------------|
| @`ViewModel`[Laminas\View\Helper\ViewModel]                              | Une aide pour stocker et récupérer la vue     |
|------------------------------------------|------------------------------------------------------|
| @`Layout`[Laminas\View\Helper\Layout]                                 | Récupère le template à appliquer à la vue.                  |
|------------------------------------------|------------------------------------------------------|
| @`Partial`                                | Permet d'afficher une vue "partielle".          |
|------------------------------------------|------------------------------------------------------|
| @`InlineScript`                           | Une aide pour définir et récupérer les éléments de script à inclure dans la section de corps HTML.                      |
|------------------------------------------|------------------------------------------------------|
| @`Identity`[Laminas\View\Helper\Identity]                               | Une aide de vue pour récupérer l'identité de l'utilisateur authentifié. |
|------------------------------------------|------------------------------------------------------|
| @`FlashMessenger`[Laminas\View\Helper\FlashMessenger]                         | Permet de récupérer les messages "flash" stockés en session.                                             |
|------------------------------------------|------------------------------------------------------|
| @`EscapeHtml`                             | Permet d'échapper une variable à afficher dans la page web. |
|------------------------------------------|------------------------------------------------------|

Pour vous donner un exemple d'utilisation d'une aide de vue, nous allons voir ci-dessous comment définir un
titre pour une page web.
Généralement, il est nécessaire de donner un titre différent pour chaque page.
Vous pouvez le faire avec l'aide de vue @`HeadTitle`.
Par exemple, vous pouvez définir le titre de la page *A Propos* en ajoutant le code PHP suivant au début du
de la vue *about.phtml* :

~~~php
<?php
$this->headTitle('A Propos');
?>
~~~

Dans le code ci-dessus, nous appelons l'aide de vue @`HeadTitle` et lui transmettons le titre
de la page ("A Propos") en tant qu'argument. L'assistant de vue @`HeadTitle` définit en interne le texte
de l'élément <title> de votre page Web.
Ensuite, si vous ouvrez la page *A Propos* dans votre navigateur Web, le titre de la page ressemblera à
"A propos - ZF Skeleton Application" (voir la figure 4.9 ci-dessous en exemple) :

![Figure 4.9. Définition du titre de la page A Propos](../en/images/mvc/about_title.png)

I> Nous allons discuter des aides de vue plus en détails et fournir plus d'exemples d'utilisation
I> dans le chapitre [Apparence et Layout](#appearance)

## Nommage des vues

Lorsque vous renvoyez des données avec le conteneur de variables @`ViewModel`[Laminas\View\Model\ViewModel] à partir de l'action de votre
contrôleur, Laminas Framework sait quel est le nom du fichier de vue correspondant.
Par exemple, pour la méthode `aboutAction()` de votre `IndexController`, Laminas utilise automatiquement
la vue *about.phtml*.

I> Laminas détermine le nom de la vue en fonction du nom du module, du contrôleur et de l'action.
I> Par exemple, l'action `IndexController::aboutAction()` appartenant au module `Application` aura
I> par défaut la vue `application/index/about.phtml`.

T> Si votre nom de contrôleur ou d'action se compose de plusieurs mots en camel-case (comme
T> `UserRegistrationController` ou `registrationStep1Action`), la vue correspondante sera
T> *application/user-registration/registration-step-1.phtml* (les noms camel-case sont convertis en
T> minuscules et les mots sont séparés par des tirets).

### Modifier le nom de la vue par défaut

La classe @`ViewModel`[Laminas\View\Model\ViewModel] peut également être utilisée pour remplacer la
résolution de la vue par défaut. En fait, la classe @`ViewModel`[Laminas\View\Model\ViewModel] est plus qu'un
simple conteneur de variables. Elle permet notamment de spécifier quelle vue doit être utilisée pour le
rendu de la page.
Le résumé des méthodes fournies à cette fin est présenté dans le tableau 4.8.

{title="Table 4.8. Méthodes de la classe ViewModel pour définir et récupérer le nom de la vue"}
|--------------------------------|---------------------------------------------------------------|
| Nom de la méthode                  | Description                                                 |
|--------------------------------|---------------------------------------------------------------|
| `setTemplate()`                | Définit le nom de la vue.                                  |
|--------------------------------|---------------------------------------------------------------|
| `getTemplate()`                | Renvoie le nom de la vue.                               |
|--------------------------------|---------------------------------------------------------------|

Pour définir le nom de la vue, vous utilisez la méthode `setTemplate()`.
La méthode `getTemplate()` renvoie le nom de la vue actuellement définie.

L'exemple de code suivant montre comment vous pouvez appeler la méthode `setTemplate()` à partir de la
méthode `indexAction()` de la classe `IndexController` pour forcer Laminas à utiliser le fichier de vues
*about.phtml* pour le rendu de la page d'accueil au lieu du fichier *index.phtml* :

~~~php
// L'action Index affiche la page d'accueil de votre site.
public function indexAction()
{
	// Utilisation d'une vue différente pour le rendu de la page.
	$viewModel = new ViewModel();
	$viewModel->setTemplate('application/index/about');
	return $viewModel;
}
~~~

Dans le code ci-dessus, nous avons créé une instance de la classe ViewModel comme à l'habitude (ligne 5).

Ensuite, nous avons appelé la méthode `setTemplate()` sur l'objet vue (ligne 6) et avons passé le nom de
la vue à utiliser en tant qu'argument.
Le nom de la vue est en fait le chemin relatif vers le fichier `about.phtml` sans l'extension du fichier.

Enfin, nous avons renvoyé l'objet vue depuis l'action (ligne 7).

I> L'appel de la méthode `setTemplate()` dans chaque méthode d'action est facultatif.
I> Si vous ne le faites pas, Laminas déterminera automatiquement le nom de la vue en concaténant le nom du
I> module en cours, le nom du contrôleur et le nom de l'action.

## Résolution des vues (View Resolver)

Lorsque Laminas Framework a le nom de la vue, il ne lui reste plus qu'à déterminer le chemin absolu du
fichier *.phtml* correspondant.
Ceci est également appelé la résolution des vues (*view template resolving*).
Les vues sont résolues avec la classe spéciale de Laminas Framework appelée le *view resolver*.

Dans Laminas, il y a deux types de résolutions en plus : @`TemplatePathStack` et @`TemplateMapResolver`.
Les deux résolveurs prennent un nom de vue en entrée et retourne le chemin du fichier à afficher.
Le nom de la vue est généralement composé du nom du module suivi du nom du contrôleur suivi du nom de la vue,
comme "application/index/about" ou "application/index/index".
Une exception pour "layout/layout", qui n'inclut pas le nom du module.

* Le *template map resolver* utilise un tableau PHP imbriqué pour déterminer le chemin d'accès à une vue
  en fonction d'un nom donné. Cette méthode est rapide, mais vous devez gérer un tableau de
  modèles et le mettre à jour chaque fois que vous ajoutez une nouvelle vue.

* Le *template path stack resolver* suppose que le nom de la vue peut être mappé à la structure de
  répertoire. Par exemple, le nom du modèle "application/index/about" correspond à
  *APP_DIR/module/Application/view/application/index/about.phtml*.
  Cette méthode est plus simple, car vous n'avez pas à gérer de maps.

Les paramètres d'affichage du résolveur sont stockés dans votre fichier *module.config.php* sous la clé *view_manager* :

~~~php
<?php
return [
    //...

    'view_manager' => [
        //...

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

Vous pouvez voir que les paramètres du template map resolver sont stockés sous la clé template_map.
Par défaut, il existe plusieurs vues "standard", qui sont résolus de cette façon :
la vue de la page index, le template (nous en parlerons dans Apparence et Layout) et les vues d'erreur
(nous en parlerons un peu plus tard).
Ces pages standard sont définies avec ce type de résolveur car c'est rapide.

Les paramètres du template path stack resolver sont stockés sous la clé *template_path_stack*.
Vous pouvez voir que ce résolveur recherche vos scripts de vue dans le répertoire "view" de votre module.
C'est pourquoi nous pouvons simplement mettre le fichier *about.phtml* dans ce répertoire et que ZF
trouvera automatiquement le fichier.

Le template map resolver et le template path stack resolver fonctionnent ensemble.
Tout d'abord, le template map resolver essaie de trouver la vue du layout dans son tableau et si la page
n'est pas trouvée, le template path stack resolver est exécuté.

## Désactiver le rendu d'une vue

Parfois, vous devez désactiver le rendu de vue actionné par défaut.
Pour ce faire, renvoyez simplement l'objet @`Response`[Laminas\Http\PhpEnvironment\Response] de l'action du contrôleur.

Par exemple, créons une classe `DownloadController` et ajoutons l'action "file", qui permettrait aux
utilisateurs du site de télécharger des fichiers depuis votre site. Cette action n'a pas besoin
d'une vue *file.phtml* correspondante car elle ne fait que copier le contenu du fichier donné.

Ajoutez le fichier *DownloadController.php* au dossier Controller du module *Application*,
puis placez le code suivant dans le fichier :

~~~php
<?php
namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

/**
 * This is the controller class for managing file downloads.
 */
class DownloadController extends AbstractActionController
{
    /**
     * This is the 'file' action that is invoked
     * when a user wants to download the given file.
     */
    public function fileAction()
    {
        // Get the file name from GET variable
        $fileName = $this->params()->fromQuery('name', '');

        // Take some precautions to make file name secure
        $fileName = str_replace("/", "", $fileName);  // Remove slashes
        $fileName = str_replace("\\", "", $fileName); // Remove back-slashes

        // Try to open file
        $path = './data/download/' . $fileName;
        if (!is_readable($path)) {
            // Set 404 Not Found status code
            $this->getResponse()->setStatusCode(404);
            return;
        }

        // Get file size in bytes
        $fileSize = filesize($path);

        // Write HTTP headers
        $response = $this->getResponse();
        $headers = $response->getHeaders();
        $headers->addHeaderLine(
                 "Content-type: application/octet-stream");
        $headers->addHeaderLine(
                 "Content-Disposition: attachment; filename=\"" .
                $fileName . "\"");
        $headers->addHeaderLine("Content-length: $fileSize");
        $headers->addHeaderLine("Cache-control: private");

        // Write file content
        $fileContent = file_get_contents($path);
        if($fileContent!=false) {
            $response->setContent($fileContent);
        } else {
            // Set 500 Server Error status code
            $this->getResponse()->setStatusCode(500);
            return;
        }

        // Return Response to avoid default view rendering
        return $this->getResponse();
    }
}
~~~

La méthode Action prend le paramètre *name* de la partie query de l'URL (ligne 19),
supprime les barres obliques du nom de fichier (lignes 22-23), ajoute des en-têtes HTTP à l'objet
@`Response`[Laminas\Http\PhpEnvironment\Response] (lignes 39-45) et contenu du fichier (lignes 48-55).
Enfin, il renvoie l'objet @`Response`[Laminas\Http\PhpEnvironment\Response] pour désactiver le rendu de vue par défaut.

Déclarez la classe `DownloadController` en ajoutant la ligne suivante à votre fichier *module.config.php* :

~~~php
<?php
return [
    // ...
    'controllers' => [
        'factories' => [
            // ...
            Controller\DownloadController::class => InvokableFactory::class
        ],
    ],
    // ...
];
~~~

Vous devrez également ajouter une *route* à votre module.config.php (une route indique à Laminas quelle URL
doit correspondre à l'action du contrôleur).
Modifiez la clé des `routes` du fichier de configuration comme suit :

~~~php
<?php
return [
  // ...
  'router' => [
        'routes' => [
            // Add this route for the DownloadController
            'download' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/download[/:action]',
                    'defaults' => [
                        'controller'    => Controller\DownloadController::class,
                        'action'        => 'index',
                    ],
                ],
            ],
        ],
    ],
  // ...
];
~~~

Pour voir comment fonctionne le téléchargement du fichier, créez le dossier *APP_DIR/data/download*
et placez-y un fichier texte nommé *sample.txt*. Ouvrez ensuite votre navigateur Web et tapez l'URL
"http://localhost/download/file?name=sample.txt" dans la barre de navigation de votre navigateur et
appuyez sur la touche Entrée. Le navigateur téléchargera le fichier *sample.txt* et vous proposera de
l'enregistrer à un emplacement donné.

## Stratégies de rendu de vue

Une *stratégie de rendu* détermine comment la page sera affichée.
Par défaut, pour produire la page HTML, la vue *.phtml* est rendu à l'aide de la classe  @`PhpRenderer`
située dans le namespace @`Laminas\View\Renderer`[Laminas\View].
Cette stratégie fonctionne bien dans 99% des cas.
Mais parfois, vous devrez peut-être renvoyer autre chose, par exemple, une réponse JSON ou une réponse de
flux RSS.

I> Une réponse au format JSON est généralement renvoyée lorsque vous implémentez une solution type API
I> (Application Programming Interface). L'API est utilisée pour récupérer certaines données dans un format
I> lisible par machine.
I> Une réponse au format flux RSS est généralement utilisée pour publier des informations fréquemment
I> modifiées, telles que des articles de blog ou des actualités.

Ainsi, Laminas fournit trois stratégies de rendu de vue supplémentaires :

  * celle par défaut (aussi connue comme @`PhpRendererStrategy`[Laminas\View\Strategy\PhpRendererStrategy]).
  * la @`JsonStrategy` qui produit une réponse JSON.
  * et la @`FeedStrategy` qui produit une réponse de flux RSS.

Par exemple, voyons comment utiliser @`JsonStrategy` pour renvoyer une réponse JSON à partir d'une action
du contrôleur.

D'abord, vous devez *déclarer* la stratégie dans le fichier de configuration module.config.php :

~~~php
<?php
return [
    //...

    'view_manager' => [
        //...

        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],
];
~~~

Ensuite renvoyez un @`JsonModel` (au lieu de l'habituel @`ViewModel`[Laminas\View\Model\ViewModel])
depuis la méthode d'action de votre controleur :

~~~php
namespace Application\Controller;

use Laminas\Mvc\Controller\ActionController;
use Laminas\View\Model\JsonModel;

class IndexController extends ActionController
{
    public function getJsonAction()
    {
        return new JsonModel([
            'status' => 'SUCCESS',
            'message'=>'Here is your data',
            'data' => [
                'full_name' => 'John Doe',
                'address' => '51 Middle st.'
            ]
        ]);
    }
}
~~~

Si vous ouvrez cette page dans votre navigateur, vous verrez la réponse JSON :

~~~
{'status':'SUCCESS', 'message':'Here is your data', 'data':{'full_name:'John Doe', 'address':'51 Middle st.'}}
~~~

## Pages d'erreur

Lorsqu'une page est introuvable ou qu'une autre erreur se produit dans votre application, une page d'erreur
standard s'affiche. L'apparence de la page d'erreur est contrôlée par les templates d'erreur.
Il existe deux modèles d'erreur: *error/404* qui est utilisé pour l'erreur "404 Page Not Found" (illustrée
à la figure 4.10), et *error/index* qui s'affiche lorsqu'une erreur générique se produit (par exemple,
lorsqu'une exception non gérée est levée quelque part L'application).

![Figure 4.10. 404 Page d'erreur](../en/images/mvc/error_404.png)

Le fichier *module.config.php* contient plusieurs paramètres sous la clé *view_manager*, que vous pouvez
utiliser pour configurer l'apparence de vos templates d'erreur:

~~~php
<?php
return [
    //...

    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        //...
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            //...
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index'=> __DIR__ . '/../view/error/index.phtml',
        ],
        //...
    ],
];
~~~

* Le paramètre *display_not_found_reason* détermine s'il faut afficher les informations détaillées sur
  l'erreur "Page introuvable".
* Le paramètre *display_exceptions* définit s'il faut afficher les informations sur une exception
  non gérée ainsi que sa stack trace.
* Le *not_found_template* définit le nom de la vue pour l'erreur 404.
* Le paramètre *exception_template* spécifie le nom de la vue pour les exception non gérées.

T> Vous mettez généralement les paramètres *display_not_found_reason* et *display_exceptions* sur
T> false lorsque vous êtes en production car vous ne souhaitez pas que les visiteurs du site voient les
T> détails des erreurs sur votre site. Cependant, vous serez toujours en mesure de récupérer les
T> informations détaillées de ces erreurs dans le fichier `error.log` d'Apache.


## Les Modèles

Un *modèle* est une classe PHP qui contient la logique métier de votre application.
La logique métier est le «cœur» de votre site web. Il implémente l'objectif du fonctionnement du site.
Par exemple, si vous implémentez un site de commerce électronique, vous aurez des modèles qui
implémenteront le catalogue de produits et le panier.

En général, le terme modèle signifie une représentation simplifiée d'un objet ou d'un phénomène de la vie
réelle. Simplifié parce que l'objet de la vie réelle a une quantité infinie de propriétés.
Par exemple, une personne réelle qui visite votre site se compose de milliards d'atomes, et vous ne pouvez
pas tous les décrire.
Au lieu de cela, vous prenez plusieurs propriétés de l'objet, qui sont les plus importantes pour votre
système et ignorent tous les autres.
Par exemple, les propriétés les plus importantes du visiteur du site (du point de vue de l'architecte du
site Web) sont le prénom, le nom, le pays, la ville, le code postal et l'adresse postale.

Les modèles peuvent avoir un comportement.
Par exemple, un modèle Expéditeur peut envoyer des messages électroniques, un modèle de Convertisseur de
devises peut convertir de l'argent, etc.

I> Avec Laminas, vous définissez vos modèles comme des classes PHP habituelles.
I> Les propriétés sont implémentées en tant que variables de classe et les comportements sont implémentés
I> en tant que méthodes de classe.

## Les différents types de modèles

Avec Laminas Framework, il n'existe pas de dossier `Model` unique pour stocker toutes les classes modèles.
Au lieu de cela, par convention, les modèles sont subdivisés en fonction des principaux types suivants
et chaque type est stocké dans son propre sous-dossier (voir le tableau 4.9) :

{title="Table 4.9. Types de modèles et emplacements"}
|--------------------------------|----------------------------------------------------------|
| *Types de classe modèle*                   | *Répertoire*                                              |
|--------------------------------|----------------------------------------------------------|
| Entités                       | `APP_DIR/module/Application/src/Entity`                  |
|--------------------------------|----------------------------------------------------------|
| Entrepôts                   | `APP_DIR/module/Application/src/Repository`              |
|--------------------------------|----------------------------------------------------------|
| Objets valeurs                  | `APP_DIR/module/Application/src/ValueObject`             |
|--------------------------------|----------------------------------------------------------|
| Services                       | `APP_DIR/module/Application/src/Service`                 |
|--------------------------------|----------------------------------------------------------|
| Fabriques                      | Dans un sous-dossier `Factory` sous chaque dossier de classes.|
|                                | Par exemple, les fabriques d'un controleur sont placées dans `APP_DIR/module/Application/src/Controller/Factory`     |
|--------------------------------|----------------------------------------------------------|

I> La séparation des modèles en différents types facilite la conception de votre logique métier.
I> On appelle cela le "Domain Driven Design" (ou DDD).
I> Définit par Eric Evans dans son célèbre livre intitulé *Domain-Driven Design — Tackling Complexity in the Heart of Software*.

Nous décrirons ci-dessous les principaux types de modèles.

### Les Entités

Les *entités* sont destinées à stocker certaines données et ont toujours une propriété *identifiante*,
ce qui vous permet d'identifier les données de manière unique.
Par exemple, une entité `Utilisateur` possèdera toujours une propriété `login` unique qui vous permetra
d'identifier l'utilisateur par cet attribut. Vous pouvez modifier les autres attributs de cette entité,
comme le `prénom` ou l'`adresse`, mais son identifiant ne change jamais.
Les entités sont généralement stockées dans une base de données ou dans un système de fichiers.

Ci-dessous, vous pouvez trouver un exemple d'entité `User`, qui représente un visiteur du site:

~~~php
// L'entité User représente un visiteur du site
class User
{
    // Propriétés
    private $login;     // e.g. "admin"
    private $title;     // e.g. "Mr."
    private $firstName; // e.g. "John"
    private $lastName;  // e.g. "Doe"
    private $country;   // e.g. "USA"
    private $city;      // e.g. "Paris"
    private $postCode;  // e.g. "10543"
    private $address;   // e.g. "Jackson rd."

    // Méthodes
    public function getLogin()
    {
        return $this->login;
    }

    public function setLogin($login)
    {
        $this->login = $login;
    }

    //...
}
~~~

Dans les lignes 5-12, nous définissons les propriétés du modèle `User`.
La meilleure pratique consiste à définir les propriétés en utilisant le type d'accès privé
et à les mettre à la disposition de l'appelant via des méthodes publiques *getter* et *setter*
(comme `getLogin()` et `setLogin()`).

I> Les méthodes du modèle ne sont pas limitées par les getters et les setters.
I> Vous pouvez créer d'autres méthodes qui manipulent les données du modèle.
I> Par exemple, vous pouvez définir la méthode pratique `getFullName()`, qui retournera le nom complet
I> de l'utilisateur ("Mr. John Doe").

### Les Entrepôts

Les classes *Entrepôts* (Repositories en anglais) sont des modèles spécifiques au stockage et et à la
récupération des entités.
Par exemple, un `UserRepository` peut représenter une table de base de données et fournir des méthodes
pour extraire des entités `User`.
Vous utilisez généralement des repository lorsque vous stockez des entités dans une base de données.
Avec les repositories, vous pouvez encapsuler la logique de requête SQL au même endroit, la maintenir
et la tester facilement.

I> Nous en apprendrons davantage sur les repositories dans [Gestion des bases de données avec Doctrine](#doctrine),
I> lorsque nous parlerons de la bibliothèque Doctrine.

### Les Objets Valeurs

Les classes *Value objects* sont en quelques sortes des modèles pour lesquels l'identité n'est pas aussi
importante que pour les entités.
Un objet de valeur est généralement une petite classe identifiée par tous ses attributs.
Il n'a pas d'attribut d'identifiant. Les Value objects ont généralement des méthodes getter, mais n'ont
pas de setters (les value objects sont immuables).

Par exemple, un modèle qui contient un montant en argent peut être traité comme un value object :

~~~php
class MoneyAmount
{
    // Properties
    private $currency;
    private $amount;

    // Constructor
    public function __construct($amount, $currency='USD')
    {
        $this->amount = $amount;
        $this->currency = $currency;
    }

    // Gets the currency code
    public function getCurrency()
    {
        return $this->currency;
    }

    // Gets the money amount
    public function getAmount()
    {
        return $this->amount;
    }
}
~~~

Aux lignes 4-5, nous définissons deux propriétés: `devise` et `montant`.
Le modèle n'a pas de propriété d'identifiant, il est plutôt identifié par toutes les propriétés dans leur
ensemble: si vous changez la `devise` ou le `montant`, vous aurez un objet montant différent.

Dans les lignes 8-12, nous définissons la méthode constructeur, qui initialise les propriétés.

Dans les lignes 15-24, nous définissons des méthodes getter pour les propriétés du modèle.
Notez que nous n'avons pas de méthodes setter (le modèle est immuable).

### Les Services

Les *modèles Service* usually encapsulent généralement certaines fonctionnalités de la logique métier.
Les services ont généralement des noms facilement reconnaissables se terminant par un suffixe "er",
comme `FileUploader` ou `UserManager`.

Ci-dessous, un exemple de service `Mailer` est présenté avec sa classe Value Object.
Il a la méthode `sendMail()` qui prend un objet de valeur `EmailMessage` et qui envoi e-mail en
utilisant la fonction standard PHP `mail()` :

~~~php
<?php

// The Email message value object
class EmailMessage
{
    private $recipient;
    private $subject;
    private $text;

    // Constructor
    public function __construct($recipient, $subject, $text)
    {
        $this->recipient = $recipient;
        $this->subject = $subject;
        $this->text = $text;
    }

    // Getters
    public function getRecipient()
    {
        return $this->recipient;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function getText()
    {
        return $this->text;
    }
}
~~~
~~~php
<?php

// The Mailer service, which can send messages by E-mail
class Mailer
{

    public function sendMail($message)
    {
        // Use PHP mail() function to send an E-mail
        if(!mail($message->getRecipient(), $message->getSubject(),
             $message()->getText()))
        {
            // Error sending message
            return false;
        }

        return true;
    }
}
~~~

T> Dans Laminas Framework, vous déclarez généralement vos modèles Service dans Service Manager.

### Les Fabriques

Les classes *Fabriques*  sont généralement conçues pour instancier d'autres modèles (en particulier les
modèles Service).
Dans les cas les plus simples, vous pouvez créer une instance d'un service sans fabrique, simplement en
utilisant l'opérateur `new`, mais parfois la logique de création de classe peut être assez complexe.
Par exemple, les services dépendent souvent les uns des autres, vous devrez donc peut-être *injecter*
des dépendances à un service.
En outre, il peut parfois être nécessaire d'initialiser le service immédiatement après l'instanciation
en appelant une (ou plusieurs) de ses méthodes.

Les classes fabriques ont généralement des noms se terminant par le suffixe Factory, comme
`CurrencyConverterFactory`, `MailerFactory`, etc.

Pour un exemple concret, imaginons que nous ayons un service `PurchaseManager`, qui peut traiter les achats
de certaines marchandises, et que le service `PurchaseManager` utilise un autre service appelé
`CurrencyConverter`, qui peut se connecter à un système externe fournissant des taux de change.
Écrivons une classe de fabrique pour `PurchaseManager`, qui instancierait le service et lui passerait la
dépendance :

~~~php
<?php
namespace Application\Service\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Application\Service\CurrencyConverter;
use Application\Service\PurchaseManager;

/**
 * This is the factory for PurchaseManager service. Its purpose is to instantiate the
 * service and inject its dependencies.
 */
class PurchaseManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container,
                $requestedName, array $options = null)
    {
        // Get CurrencyConverter service from the service manager.
        $currencyConverter = $container->get(CurrencyConverter::class);

        // Instantiate the service and inject dependencies.
        return new PurchaseManager($currencyConverter);
    }
}
~~~

Dans le code ci-dessus, nous avons la classe `PurchaseManagerFactory` qui implémente l'interface
`Laminas\ServiceManager\Factory\FactoryInterface`.
La classe factory a la méthode `__invoke()` dont le but est d'instancier l'objet.
Cette méthode a l'argument `$container` qui est le gestionnaire de service.
Vous pouvez utiliser `$container` pour extraire les services du gestionnaire de service et les transmettre
à la méthode constructeur du service en cours d'instanciation.

## Determiner le bon type de Modèle

Q> **N'est-ce pas déroutant d'avoir autant de types de modèles ?**
Q>
Q> Eh bien, oui et non. Au début, il peut être un peu difficile de déterminer le bon type de modèle,
Q> mais dès que vous gagnerez en compétences, vous serez capable de le faire intuitivement.
Q> Rappelez-vous simplement que les types de modèles améliorent la structure de vos modèles de domaine.

Lorsque vous écrivez votre propre application, vous pouvez être confus lorsque vous essayez de décider
à quel type de modèle appartient votre classe (qu'il s'agisse d'une entité, d'un value object, d'un
repository, d'un service ou d'une fabrique). Ci-dessous, un algorithme simple est fourni pour vous aider
à déterminer le bon type de modèle lors de l'écriture de votre application :

* Votre classe de modèle est définitivement un *Service*
    * si elle encapsule une logique métier
    * si vous l'appelez de votre classe de contrôleur
    * Si vous pensez que le meilleur nom se termine par le suffixe "er" comme FileUploader ou VersionChecker
* Votre classe de modèle est une *Entité*:
    * si votre modèle est stocké dans une base de données
    * s'il a un attribut ID
    * si elle a à la fois des méthodes getters et setters
* Votre classe de modèle est un *ValueObject*:
    * si changer un attribut rendrait le modèle complètement différent
    * si votre modèle a des getters, mais pas des setters (immuable)
* Votre modèle est un *Repository*:
    * si cela fonctionne avec une base de données pour récupérer des entités
* Votre modèle est une *Fabrique*:
    * s'il peut créer d'autres objets et ne peut rien faire d'autre

Q> **Hmm ... que se passe-t-il si je stocke tous mes modèles dans un seul dossier Model?**
Q>
Q> Bien sûr, vous pouvez, si vous le souhaitez fortement. Mais, lorsque vous utilisez la bibliothèque ORM de
Q> Doctrine, vous remarquerez qu'elle utilise également les principes DDD, de sorte que l'utilisation de DDD
Q> rend votre application bien organisée.

## Les autres types de modèle

Dans votre application, vous diviserez généralement les principaux types de modèles (décrits précedement)
en sous-types. Par exemple, vous aurez:

  * Les *Forms*. Les forms (formulaires) sont des modèles dont le but est de collecter des données saisies
    par l'internaute. Les formulaires sont un sous-type d'entités. Vous stockerez généralement les formulaires
    dans le dossier `APP_DIR/module/Application/src/Form`.

  * Les *Filters*. Les filtres sont conçus pour transformer les données d'entrée. Les filtres sont un
    sous-type de *services*. Vous allez généralement stocker les filtres dans le dossier
    `APP_DIR/module/Application/src/Filter`.

  * Les *Validators*. Les validateurs sont utilisés pour vérifier l'exactitude des données entrées.
    Les validateurs sont également un sous-type de *services*. Vous stockez généralement les validateurs
    dans le dossier `APP_DIR/module/Application/src/Validator`.

  * Les *View Helpers*. Ils encapsulent certaines fonctionnalités de rendu de page. Les View Helpers sont
    similaires aux *services*. Vous allez généralement les stocker dans le dossier `APP_DIR/module/Application/src/View/Helper`.

  * Les *Routes*. Les routes sont un modèle de service spécifique utilisé pour implémenter des règles de mappage
    personnalisés entre les URL et vos contrôleurs. Vous allez généralement stocker vos routes personnalisés
    dans le dossier `APP_DIR/module/Application/src/Route`.

Donc, vous devriez avoir la structure de module suivante :

~~~
/Application/src
	/Controller
		/Factory
        /Plugin
            /Factory
	/Entity
	/Filter
	/Form
    /Repository
	/Route
	/Service
		/Factory
	/Validator
    /ValueObject
	/View
		/Helper
            /Factory
~~~

I> Il est possible d'avoir d'autres sous-types de modèles. Plus votre application est complexe, plus vous
I> avez de sous-types de modèles.

## Contrôleurs légers, gros modèles et vues simples

Lors du développement d'un site à l'aide de la structure Model-View-Controller, il existe un risque de
mauvaise compréhension du rôle des contrôleurs, des vues et des modèles. Cela a pour conséquence de
rendre les contrôleurs énormes et les modèles réduits, ce qui rend difficile le test et la prise en
charge de votre application.
L'objectif de cette section est de vous donner une idée générale du code qui peut être placé dans une
classe de contrôleur, du code qui peut être placé dans une vue et du code qui peut être placé dans une
classe modèle.

### Des Controleurs Légers

L'idée derrière le terme "contrôleur léger" est que typiquement, dans vos classes de contrôleur,
vous mettez seulement le code qui :

* accède aux données de la requete de l'utilisateur (`$_GET`, `$_POST`, `$_FILES` et autres variables PHP);
* vérifie la validité des données d'entrée;
* (optionnellement) faire quelques préparations de base aux données;
* transmettre les données au(x) modèle(s) et récupèrer le résultat renvoyé par le(s) modèle(s);
* et finalement renvoyer les données de sortie via le conteneur de variable `ViewModel`.

Une classe contrôleur devrait éviter :

* de contenir une logique métier complexe, qui est mieux conservée dans les classes de modèles;
* de contenir du code HTML ou tout autre code de balisage du rendu dont la place est dans la Vue.

Pour un exemple de contrôleur "léger", regardez la classe `CurrencyConverterController` ci-dessous.
Ce contrôleur fournit l'action "convert" dont l'objectif est de convertir une somme d'argent de l'euro vers
l'USD. L'utilisateur transmet le montant d'argent via la variable GET "amount".


~~~php
class CurrencyConverterController extends AbstractActionController
{
    // Currency converter model
    private $currencyConverter;

    // Constructor. It's purpose is to "inject" dependencies.
    public function __construct($currencyConverter)
    {
        $this->currencyConverter = $currencyConverter;
    }

    // The "convert" action displays the converted money amount
    public function convertAction()
    {
        // Get the money amount from GET
        $amount = (float)$this->params()->fromQuery('amount', -1);

        // Validate input data
        if($amount<0) {
            // Money amount is missing
            $this->getResponse()->setStatusCode(404);
            return;
        }

        // Pass the data to the CurrencyConverter model
        $convertedAmount = $this->currencyConverter->convertEURtoUSD($amount);

        return new ViewModel([
            'amount'=>$amount,
            'convertedAmount'=>$convertedAmount
        ]);
    }
}
~~~

La méthode action du contrôleur ci-dessus est la suivante:
* Récupère les données transmises par l'utilisateur du site (ligne 16). Ces données font généralement
 partie de l'objet `Request` et peuvent être récupérées à l'aide de la méthode `getRequest()` du contrôleur
 ou du plugin de controleur @`Params`.

* Effectue la vérification de base sur les données transmises par l'utilisateur (ligne 19), et si les
  données sont manquantes (ou non valides), définit un code d'erreur HTTP (ligne 21).

* Transmet le montant en argent au modèle `CurrencyConverter` (line 26) en appelant sa méthode
 `convertEURtoUSD()`. La méthode renvoie ensuite le montant converti.

* Construit le conteneur de variable @`ViewModel`[Laminas\View\Model\ViewModel] et lui transmet les données
  résultantes (ligne 28). Ce conteneur de variables est également accessible dans la vue correspondante,
  responsable de la présentation des données.

### Gros Modèles

Parce que vous devez garder vos contrôleurs aussi légers que possible, la majeure partie de la logique
métier de votre application doit être placée dans des classes modèles.
Dans une application Model-View-Controller correctement conçue, les modèles semblent "énormes".
Une classe de modèle peut contenir le code qui :

* Effectue un filtrage et une validation de données complexes. Étant donné que les données que vous avez
  récupérées dans le contrôleur sont transmises à votre application depuis le monde extérieur, dans votre
  modèle, vous devez faire beaucoup d'efforts pour vérifier les données et vous assurer que les données ne
  causeront pas de dommage à votre système. Cela se traduit par un site web sécurisé résistant aux attaques
  des pirates informatiques.

* Effectue la manipulation des données. Vos modèles doivent manipuler les données : par ex. charger les
  données depuis la base de données, enregistrer des données dans la base ou encore transformer les données.
  Les modèles sont le bon endroit pour stocker des requêtes de base de données, des fonctions de lecture et
  d'écriture de fichiers, etc.

Dans une classe modèle, il n'est pas recommandé de:

* Accédez aux données des requêtes HTTP, `$_GET`, `$_POST` et autres variables PHP.
  C'est le travail du contrôleur d'extraire ces données et les transmettre en entrée au modèle.

* Produire du code HTML ou autre code spécifique à la présentation. Le code de présentation peut varier en
  fonction de la demande de l'utilisateur, il est préférable de le placer dans une vue.

Si vous suivez ces principes, vous constaterez que vos modèles seront faciles à tester car ils auront des
entrées et des sorties clairement identifiées. Vous pourrez écrire un test unitaire qui transmet certaines
données à la fin de l'entrée du modèle, récupère les données de sortie et vérifie que les données
sont correctes.

Si vous n'êtes pas sûr d'où mettre certaines parties de code (dans un contrôleur ou dans un modèle),
demandez-vous : est-ce une logique métier importante qui doit être testée avec soin ?
Si la réponse est oui, vous devriez mettre le code dans un modèle.

### Vue simples

Étant donné que la majeure partie de la logique est stockée dans des modèles, vos vues doivent être aussi
simples que possible pour produire la présentation des données transmises dans le conteneur de variables.
Dans une vue, vous pouvez :

* Garder du code de balisage HTML statique.

* Récupérez des données du conteneur de variables et les afficher.

* Si un contrôleur transmet un certain modèle via un conteneur de variables, interrogez le modèle pour
  obtenir des données (par exemple, vous pouvez extraire des lignes de table d'une table de base de
  données et les afficher).

* Contenir des opérations simples de contrôle de flux PHP, comme `if`, `foreach`, `switch`, etc.
  Cela permet de varier la présentation en fonction des variables transmises par le contrôleur.

La vue n'est pas recommandé pour :

* Accéder aux données de la requête HTTP et aux variables globales PHP.

* Créer des modèles, les manipuler et modifiez l'état de l'application.

Si vous suivez ces principes, vous constaterez que vos vues peuvent facilement être remplacées sans
modifier la logique métier de votre application.
Par exemple, vous pouvez facilement modifier la conception de vos pages web, ou même introduire des
thèmes modifiables.

## Résumé

Un site web basé sur Laminas Framework est juste un programme PHP recevant une requête HTTP du serveur web,
et produisant une réponse HTTP.
L'application utilise la structure Model-View-Controller pour séparer la logique métier de la présentation.
Le but de ceci est de permettre la réutilisation de code et la séparation des préoccupations.

Un contrôleur est un médiateur entre l'application, les modèles et les vues : il reçoit une entrée de la
requête HTTP et utilise le(s) modèle(s) et la vue correspondante pour produire la réponse HTTP nécessaire.
Un contrôleur est une classe PHP habituelle contenant des méthodes d'action.

Les vues sont de simples extraits de code HTML + PHP produisant des résultats HTML renvoyés par le serveur
Web aux visiteurs du site. Vous transmettez les données à afficher dans les vues via le conteneur de
variable @`ViewModel`[Laminas\View\Model\ViewModel].

Un modèle est une classe PHP qui contient la logique métier de votre application.
La logique métier est le «cœur» de votre site Web qui met en œuvre l'objectif de fonctionnement du site.
Les modèles peuvent accéder à la base de données, manipuler des fichiers du disque, se connecter à des
systèmes externes, manipuler d'autres modèles, etc.
