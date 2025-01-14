{mainmatter}

# Introduction à Laminas Framework {#intro}

Dans ce chapitre, vous allez découvrir Laminas Framework, ses principaux principes et composants.

## Qu'est ce que Laminas Framework?

PHP est un langage de développement de site Web populaire. Cependant, écrire des sites Web en PHP pur est difficile.
Si vous codez une application en PHP pur, vous devrez organiser votre code d'une manière ou d'une autre,
collecter et valider les données saisies par les utilisateurs, implémenter un système d'authentification
et de droits d'accès aux différentes pages, gérer la base de données, tester votre code et ainsi de suite.
Au fur et à mesure que votre site grandit, il devient de plus en plus difficile de développer votre site de manière cohérente.
Chaque développeur de votre équipe à sa façon de coder, son style et ses modèles.
Le code devient trop compliqué, lent et difficile à maintenir. Vous fusionnez tout
votre code dans un seul script géant sans séparation des objectifs. Vous devez réinventer la roue
à chaque fois et cela provoque des problèmes de sécurité. De plus, lorsque vous passez au développement d'un
nouveau site, vous remarquerez qu'une grande partie du code que vous avez déjà écrit pour l'ancien site
peut être utilisé à nouveau avec de petites modifications. C'est ce code qui peut être séparé dans une bibliothèque. C'est ainsi que sont nés les frameworks.

I> Un framework est une sorte de bibliothèque, un logiciel (également écrit en PHP) qui est
fourni aux développeurs avec du code et des méthodes standardisées et cohérentes de création
d'applications Web.

Laminas Framework est un framework PHP libre et open-source.
Son développement est guidé (et sponsorisé) par Laminas Technologies, également connu comme le fournisseur
du langage PHP. La première version (Laminas Framework 1) a été publiée en 2007; Laminas Framework 2,
la deuxième version de ce logiciel, a été publié en Septembre 2012.
Laminas Framework (ou bientôt Laminas) a été publié en Juin 2016.

Laminas Framework vous offre les fonctionnalités suivantes :

* Développez votre site web beaucoup plus rapidement que lorsque vous l'écrivez en PHP pur. Laminas fournit
  de nombreux composants qui peuvent être utilisés comme base de code pour créer votre site Web.

* Une coopération plus facile avec les autres membres de votre équipe de développement.
  Le modèle Model-View-Controller utilisé par Laminas permet de séparer la logique métier et la couche de
  présentation de votre site web, rendant sa structure cohérente et maintenable.

* Etendez votre site Web avec le concept de modules. Laminas utilise le terme *module*,
  qui permet de séparer des parties de site, permettant ainsi de réutiliser des modèles, des vues,
  des contrôleurs et autres composants de votre site dans d'autres projets.

* Accéder à la base données de façon orientée objet. Au lieu d'interagir directement avec la base de données
  à l'aide de requêtes SQL, vous pouvez utiliser Doctrine ORM (Object-Relational Mapping) pour gérer la
  structure et les relations entre vos données.
  Avec Doctrine vous associez chaque table de la base de données à une classe PHP (également appelé une classe *entité*)
  et les lignes de ces tables sont mises en correspondance avec une instance de la classe correspondante.
  Doctrine permet de faire abstraction du type de base de données et de rendre le code plus facile à comprendre.

* Ecrire des sites Web sécurisés avec les composants fournis par Laminas tels que les filtres et validateurs sur les données
  saisies dans les formulaires , l'échappement des sorties HTML, les algorithmes de cryptographie,
  les captchas ou les éléments de formulaire CSRF (Cross-Site Request Forgery).

## Un exemple de site PHP sans framework

Pour vous montrer comme il est difficile de construire un site *sans* framework PHP,
nous allons écrire un site très simple composé de trois pages HTML: *Accueil*, *Connexion* et *Déconnexion*.

I> Petite précision, écrire un site avec un framework PHP peut également être difficile, mais avec un framework vous le ferez de manière cohérente et sécurisée.

### Page d'accueil

I> Lorsque vous écrivez un site en PHP, vous mettez votre code dans un fichier avec l'extension *.php*. Un tel fichier s'appelle
un *script* PHP.

Pour commencer, implémentons la page *Home* du site. Pour ce faire, créez un fichier *index.php* à la racine de votre répertoire Apache et placez le code suivant:

T> Pour comprendre le code ci-dessous, vous devez avoir un peu d'expérience avec PHP. Si ce n'est pas le cas, il serait bon de vous référez à un tutoriel PHP comme ceux du [w3schools.com](http://www.w3schools.com/php/).

~~~php
<?php
// index.php
session_start();

// Si l'utilisateur est connecté, on récupère son identité via la session.
$identity = null;
if (isset($_SESSION['identity'])) {
    $identity = $_SESSION['identity'];
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Page d'accueil</title>
    </head>
    <body>
        <h1>Page d'accueil</h1>
        <?php if ($identity==null): ?>
        <a href="login.php">Connexion</a>
        <?php else: ?>
        <strong>Bienvenue, <?= $identity ?></strong> <a href="logout.php">Déconnexion</a>
        <?php endif; ?>

        <p>
            Ceci est un simple site web pour démontrer les avantages d'un framework PHP et les inconvénients du PHP "pur".
        </p>
    </body>
</html>
~~~

Si vous tapez "http://localhost/index.php" dans votre navigateur (comme Google Chrome ou Firefox), vous devriez voir une page comme ci-dessous :

![Une page d'accueil simple](../en/images/intro/simple_home_page.png)

### Page Connexion

Maintenant, implémentons la page *Connexion*. Ce genre de page devrait présenter un formulaire avec les champs *E-mail* et *Mot de passe*. Une fois que l'utilisateur valide le formulaire, il passe l'authentification et son identité est enregistrée dans la session PHP. Le script ressemblerait alors à ceci :

~~~php
<?php
// login.php
session_start();

// Si l'utilisateur est connecté, on le redirige vers index.php
if (isset($_SESSION['identity'])) {
    header('Location: index.php');
    exit;
}

// On vérifie que le formulaire est soumis.
$submitted = false;
if ($_SERVER['REQUEST_METHOD']=='POST') {

    $submitted = true;

    // On récupère les données du formulaire
    $email = $_POST['email'];
    $password = $_POST['password'];

    // On authentifie l'utilisateur
    $authenticated = false;
    if ($email=='admin@example.com' && $password=='Secur1ty') {
        $authenticated = true;

        // On enregistre son identité en session.
        $_SESSION['identity'] = $email;

        // On redirige l'utilisateur vers la page index.php.
        header('Location: index.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Page de connexion</title>
    </head>
    <body>
        <h1>Connexion</h1>
        <?php if ($submitted && !$authenticated): ?>
            <div class="alert">
                Les informations d'identification sont invalides.
            </div>
        <?php endif; ?>
        <form name="login-form" action="/login.php" method="POST">
            <label for="email">E-mail</label>
            <input type="text" name="email">
            <br>
            <label for="password">Mot de passe</label>
            <input type="password" name="password">
            <br>
            <input type="submit" name="submit" value="Connexion">
        </form>
    </body>
</html>
~~~

Si vous ouvrez la page "http://localhost/login.php" dans votre navigateur, vous devriez voir quelque chose comme ca :

![A page de connexion simple](../en/images/intro/simple_login_page.png)

T> Pour vous connecter, utilisez `admin@example.com` and `Secur1ty` comme e-mail et mot de passe.

### Page Déconnexion

Et pour finir, implémentons la page *Déconnexion* qui va effacer l'identité de l'utilisateur enregistré en session:

~~~php
<?php
// logout.php
session_start();

unset($_SESSION['identity']);
header('Location: index.php');
exit;
~~~

T> Le code complet de ce site simple peut être trouvé dans l'exemple
T> [PHP pur](https://github.com/olegkrivtsov/using-laminas-book-samples/tree/master/purephp) fourni avec ce livre.

### Relecture du code

Les scripts ci-dessus ne sont pas seulement un exemple typique d'un site PHP "pur".
Ils sont aussi un exemple de *comment ne pas écrire* vos sites (même les plus simples). Quels sont les problèmes ?

1. Les scripts *index.php* et *login.php* aurait tendance à être fusionner en un seul fichier.
   Vous n'avez aucune séparation des objectifs, ce qui rend votre code trop complexe.
   Intuitivement, vous comprenez qu'il serait être plus pratique de diviser le code qui gère l'authentification de celui qui concerne la présentation (rendu HTML).

2. Les URL de vos pages ne sont pas terribles (par exemple, "http://localhost/index.php"). Il vaudrait mieux cacher cette extension ".php".
   Et que se passe-t-il lorsqu'un utilisateur Web tente de visiter une page qui n'existe pas?
   Il vaudrait mieux rediriger l'utilisateur vers une page d'erreur dans ce cas.

3. Et si ce site devait s'agrandir ? Comment organiseriez-vous votre code? Un script PHP par page ?
   Et si vous voulez réutiliser certains de vos scripts PHP sur d'autres sites web sans modifications? Intuitivement
   vous pourriez comprendre qu'il serait utile d'organiser le code dans une sorte de *module* réutilisable.

4. Les scripts *index.php* et *login.php* contiennent des balises HTML en commun. Pourquoi les copier dans tous les scripts PHP, alors
   que nous souhaitons réutiliser cette disposition sur toutes (ou presque toutes) les pages.

5. Le script *login.php* a des problèmes de sécurité, car nous n'avons implémenté aucune validation des variables POST.
   La session PHP est également soumise au piratage. Et le script PHP *login.php* sera situé dans le répertoire racine du serveur Apache,
   ce qui n'est pas très sécurisé (il serait préférable de le placer dans un endroit inaccessible aux internautes).
   Le fichier *index.php* est également non sécurisé, car nous n'avons pas filtré la sortie PHP (sujette aux attaques XSS).

6. Ces scripts n'utilisent aucune classe PHP. Encapsuler la fonctionnalité dans les classes en théorie rendrait le code bien structuré et facile à maintenir.

7. Dans ces scripts, vous devez écrire votre propre implémentation de l'authentification de l'utilisateur (et ainsi de suite). Pourquoi réinventer la roue et ne pas utiliser une librairie bien conçue pour cela ?

Les problèmes ci-dessus sont facilement résolus lorsque vous écrivez un site Web avec un framework (comme Laminas Framework):

1. Avec Laminas, vous utilisez le modèle de conception *Model-Vue-Controller*, divisant votre code PHP en Modèles (le code responsable de l'authentification irait ici),
    Vues (le code responsable du rendu HTML irait ici) et Controleurs (le code responsable de la récupération des variables POST irait ici).

2. Le routage *Laminas* permet de faire un affichage plus professionnel des URL en masquant les extensions .php. La facon dont les URL doivent être composées sont définies par des règles strictes.
   Si un utilisateur essaie d'accéder à une page inexistante, il est automatiquement redirigé vers une page d'erreur standard.

3. En Laminas, vous pouvez utiliser le concept de *module*. Cela permet de séparer facilement vos modèles, vues et contrôleurs en unité autonome (module) et de réutiliser facilement cette unité dans un autre projet.

4. Dans Laminas, vous pouvez définir une vue (*layout*) commune et la réutiliser sur toutes (ou la plupart) des pages.

5. Laminas vous offre diverses fonctions de sécurité, telles que des filtres et validateurs de formulaires, des échappements de code, des validateurs de session, des algorithmes de cryptographie, etc.
   Dans un site web Laminas, seul l'*index.php* est accessible aux internautes, tous les autres scripts PHP sont situés en dehors du répertoire racine du serveur Apache.

6. Dans un site Laminas, vous mettez votre code dans des classes, ce qui le rend bien organisé.

7. Laminas vous fournit de nombreux composants que vous pouvez utiliser dans votre site Web: un composant pour l'authentification, un composant pour travailler avec des formulaires, etc.

T> Vous avez maintenant une idée des avantages de Laminas Framework et de ce qu'il peut faire pour vous. Dans les sections suivantes, nous décrirons Laminas plus en détail.

## License

Laminas Framework est sous licence BSD-like, cela vous permet de l'utiliser dans des applications commerciales et gratuites.
Vous pouvez même modifier le code de la bibliothèque et le publier sous un autre nom.
La seule chose que vous ne pouvez pas faire est de supprimer le copyright du code.
Si vous utilisez Laminas Framework, il est également recommandé de le mentionner dans la documentation de votre site ou dans votre page 'À propos'.

Ci-dessous, le texte de la licence Laminas Framework est présenté.

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

## Support

Le support est une chose importante à prendre en compte lors de la décision d'utiliser ou non le framework comme base pour votre
site. Le support comprend une documentation bien écrite, des webinaires, des forums communautaires et (facultativement) des
services de soutien commercial, comme des formations et des programmes de certification.

![Site officiel du projet Laminas Framework](../en/images/intro/Laminas_framework_site.png)

*Documentation*. La documentation de Laminas Framework se trouve à [cette adresse](https://framework.Laminas.com/learn).
Elle comprend des tutoriels pour débutants et le manuel du développeur.

*Les référence de l'API* peuvent être trouvées [ici]((https://olegkrivtsov.github.io/laminas-api-reference/html/).

*Forum de la communauté*. Vous pouvez poser une question sur l'utilisation de Laminas Framework sur [StackOverflow](https://stackoverflow.com/search?q=Laminas+framework+3).
Vos questions seront traitées par une grande communauté de développeurs Laminas.

*Les webinaires* sont des tutoriaux vidéo couvrant diverses fonctionnalités de Laminas Framework.
La liste complète des webinaires peut être trouvée [ici](http://www.Laminas.com/en/resources/webinars/framework).

*Des formations* avec des instructeurs sont accessibles [par ici](http://www.Laminas.com/en/services/training).
Ici, vous pouvez apprendre Laminas en faisant des exercices, des mini-projets et en développant du vrai code.

*Un Programme de Certification*. Vous permet de devenir un ingénieur certifié Laminas(ZCE),
qui vous permettra d'améliorer vos compétences en tant que développeur et de trouver plus facilement un emploi dans un marché du travail concurrentiel.
Les certifications peuvent être trouvées [ici](http://www.Laminas.com/en/services/certification).

*Vous voulez plus de ressources Laminas ?* Découvrez cette liste impressionnante (mouhaha) de [ressources Laminas Framework](https://github.com/dignityinside/awesome-zf).

## Code Source du Framework

Le code source de Laminas Framework est stocké dans ce [dépot](https://github.com/laminas) GitHub.
Il y a un dépôt séparé pour chaque composant Laminas.

I> Dans la plupart des cas, vous n'aurez pas besoin de télécharger le code manuellement.
I> Au lieu de cela, vous l'installerez avec le gestionnaire de dépendances Composer. Nous nous familiariserons plus tard avec Composer dans un chapitre intitulé [Laminas Skeleton Application](#skeleton).

### Standardisation du code

C'est une bonne pratique d'introduire une norme de codage commune pour tout votre code.
Cette norme définirait des règles de dénomination de classe, des règles de formatage de code, etc.
Laminas Framework définit cette norme [ici](https://github.com/laminas/laminas/wiki/Coding-Standards).
Tout le code dans Laminas suit les règles décrites dans ce document.

T> Si vous envisagez d'écrire un site Web basé sur Laminas, il est recommandé de suivre la même norme pour votre propre code.
T> Cela rendra votre code cohérent et plus facile à étendre et à prendre en charge par d'autres personnes.

## Systèmes d'exploitation pris en charge

Comme tout site Web PHP, l'application Web basée sur Laminas peut fonctionner sur un serveur Linux et sur tout autre système d'exploitation sur lequel PHP peut s'exécuter.
Pour information, pour créer les exemples illustrant ce livre, l'auteur a utilisé le système d'exploitation Ubuntu Linux.

Si vous ne savez pas encore quel système d'exploitation utiliser pour votre développement Web, il est recommandé d'utiliser Linux,
car la plupart des logiciels serveur fonctionnent sur des serveurs Linux.
Vous pouvez consulter l'[annexe A. Configuration d'un environnement de développement web](#devenv) pour obtenir des informations
sur la configuration de votre environnement de développement.

## Configuration du serveur

Laminas Framework requiert que votre serveur ait la version 5.6 (ou ultérieure) de PHP installée. Notez que c'est une exigence plutôt stricte.
Tous les hébergeurs partagés bon marché et tous les serveurs privés n'ont pas une telle version PHP moderne.

De plus, la méthode recommandée pour installer Laminas (et d'autres composants dépend de votre application) utilise [Composer](http://getcomposer.org/).
Cela oblige à avoir accès à un shell (SSH) pour pouvoir exécuter l'outil de ligne de commande Composer.
Certains hébergeurs fournissent un accès FTP uniquement, de sorte que vous ne pourrez pas installer une application basée sur Laminas
sur ces serveurs de la manière habituelle.

Laminas utilise l'extension de réécriture d'URL pour rediriger les internautes vers le script d'entrée de votre site (vous devez activer
le module `mod_rewrite` d'Apache).
Vous pouvez également avoir besoin d'installer des extensions PHP, comme `memcached`.
Cela peut être une difficulté lors de l'utilisation d'un hébergement mutualisé et nécessite que vous ayez des droits d'administrateur
sur votre serveur.

Donc, si vous envisagez d'utiliser Laminas sur un hébergement Web partagé, réfléchissez à deux fois.
Le meilleur serveur pour installer Laminas est un serveur avec la dernière version de PHP et avec un accès shell pour pouvoir
exécuter Composer et installer des extensions PHP.

Si votre entreprise gère sa propre infrastructure de serveur et peut se permettre de mettre à jour la version de PHP vers la dernière,
vous pouvez installer Laminas sur votre serveur privé.

Une alternative acceptable consiste à installer une application Web basée sur Laminas sur un service d'hébergement basé sur le cloud,
comme [Amazon Web Services](http://aws.amazon.com/). Amazon fournit des instances de serveur Linux dans le cadre du service EC2.
EC2 est plutôt bon marché, et il fournit un [niveau d'utilisation gratuit](http://aws.amazon.com/free/) vous permettant de l'essayer gratuitement pendant un an.
Nous fournissons des instructions pour les débutants sur l'installation d'un site Web Laminas sur le cloud Amazon EC2 dans
l'[annexe E. Installation d'une application Web Laminas sur Amazon EC2](#ec2-tutorial).

## Sécurité

Laminas Framework suit les meilleures pratiques pour vous fournir une base de code sécurisée pour vos sites Web.
Les créateurs de Laminas publient des correctifs de sécurité une fois que la communauté d'utilisateurs a trouvé un problème.
Vous pouvez incorporer ces correctifs avec une seule commande via le gestionnaire de dépendances Composer.

I> La pratique montre que l'utilisation d'un framework pour écrire votre site web est plus sûre que l'utilisation de PHP "pur",
I> car vous n'avez pas besoin de réinventer la roue. La plupart des vulnérabilités de sécurité dans les frameworks sont déjà connues
I> et corrigées par la communauté des utilisateurs.

Laminas fournit les fonctionnalités suivantes permettant de sécuriser votre site Web :

* *Le Script d'entrée* (*index.php*) est le seul fichier PHP accessible aux visiteurs web. Tous les autres fichiers PHP sont situés
  en dehors de la racine du site sur le votre serveur Apache. C'est beaucoup plus sûr que de permettre à tout le monde de visiter l'un de vos scripts PHP.

* *Le Routage* permet de définir des règles strictes sur l'aspect d'une URL de page acceptable. Si un utilisateur du site saisit une URL
  non valide dans la barre de navigation d'un navigateur Web, il est automatiquement redirigé vers une page d'erreur.

* *Les listes de contrôle d'accès  (ACL)* et les *Role-Based Access Control (RBAC)* permettent de définir des règles flexibles pour
  accorder ou refuser l'accès à certaines ressources de votre site Web. Par exemple, un utilisateur anonyme aurait uniquement accès à
  votre page d'index, les utilisateurs authentifiés auraient accès à leur page de profil et l'utilisateur administrateur aurait accès
  au panneau de gestion de site.

* *Les validateurs de formulaires et les filtres* garantissent qu'aucune donnée indésirable n'est collectée via des formulaires Web.
  Les filtres, par exemple, permettent de couper des chaînes ou de supprimer des balises HTML.
  Les validateurs sont utilisés pour vérifier que les données qui ont été soumises via un formulaire sont conformes à certaines règles.
  Par exemple, le validateur de courrier électronique vérifie qu'un champ de courrier électronique contient une adresse de messagerie
  valide et, dans le cas contraire, déclenche une erreur obligeant l'utilisateur du site à corriger l'erreur de saisie.

* *Captcha* et *CSRF* (Cross-Site Request Forgery) Ces éléments de formulaire sont utilisés, respectivement, pour vérifiez
    que vous avez affaire à des humains et à prévenir les attaques pirates.

* *Laminas\Escaper* Ce composant permet de supprimer les balises HTML non désirées des données envoyées aux pages du site.

* *La prise en charge de la cryptographie* vous permet de stocker vos données sensibles (par exemple vos informations d'identification) cryptées à l'aide d'algorithmes de cryptage puissants difficiles à pirater.

## Performance

Laminas fournit les fonctionnalités suivantes pour assurer que ses performances sont acceptables :

  * *Lazy class autoloading.* Les classes ne sont chargées que lorsqu'elles sont nécessaires.
    Vous n'avez pas besoin d'écrire `require_once` pour chaque classe que vous voulez charger.
    Au lieu de cela, le framework découvre automatiquement vos classes à l'aide de la fonction *autoloader*.

  * *Service efficace et chargement de plugin.* En Laminas, les classes sont instanciées uniquement lorsqu'elles en
    ont vraiment besoin de l'être.
    C'est le gestionnaire de service qui s'en charge (le conteneur central pour les services de votre application).

  * *Gestion du cache.* PHP a plusieurs extensions de mise en cache (comme Memcached) qui peuvent être utilisées pour accélérer les sites Web basés sur Laminas.
    La mise en cache enregistre les données fréquemment utilisées dans la mémoire pour accélérer leur récupération.

## Modèles de conception

Les créateurs de Laminas Framework sont de grands fans de différents modèles de conception.
Bien que vous n'ayez pas à comprendre les modèles pour lire ce livre, cette section a pour but
de vous donner une idée des modèles de conception sur lesquels Laminas est basé.

* *Le Modèle MVC (Model-Vue-Controller)*. Le modèle MVC est utilisé dans tous les frameworks PHP récents.
  Dans une application MVC, vous séparez votre code en trois catégories:
  les modèles (la logique métier), les vues (le rendu d'affichage) et les contrôleurs (le code responsable de l'interaction avec l'utilisateur).
  Egalement appelé *la séparation des préoccupations*.
  Avec le modèle MVC, vous pouvez * réutiliser * vos composants.
  Il est également facile de remplacer n'importe quelle partie de cet triptyque.
  Par exemple, vous pouvez facilement remplacer une vue par une autre, sans changer votre logique métier.

* *Le Modèle DDD (Domain Driven Design)*. Avec Laminas Framework, vous aurez par convention la couche modèle divisée en *entités*
  (classes mappées sur les tables de base de données), en *repositories* (classes utilisées pour extraire des entités de base de données),
  en *value objects* (des classes modèles sans identité) et en *services* (classes responsables de la logique métier).

* *Le modèle Aspect Oriented Design.* Dans Laminas, tout est piloté par les événements.
  Lorsqu'un utilisateur du site demande une page, un *événement* est généré (déclenché).
  Un listener (ou un observateur) peut attraper cet événement et faire quelque chose avec. Par exemple, le composant @`Laminas\Router`
  analyse l'URL et détermine la classe de contrôleur à appeler. Lorsque l'événement atteint finalement le rendu de page, une réponse HTTP est générée et l'utilisateur voit la page Web.

* *Le modèle Singleton.* Dans Laminas, il y a l'objet gestionnaire de service qui est le conteneur centralisé de tous les services
  disponibles dans l'application. Chaque service existe dans une seule instance seulement.

* *Les modèles Strategy.* Une stratégie est juste une classe encapsulant un algorithme.
  Et vous pouvez utiliser différents algorithmes basés sur certaines conditions.
  Par exemple, le moteur de rendu des pages a plusieurs stratégies de rendu, ce qui permet de retourner différent types (e moteur de rendu peut générer une page HTML, une réponse JSON, un flux RSS, etc.)

* *Les modèles Adapter.* Des adaptateurs permettent d'adapter une classe générique à un cas d'utilisation concret.
  Par exemple, le composant @`Laminas\Db` fournit un accès générique à la base de données en utilisant des adaptateurs pour chaque base de données supportée (SQLite, MySQL, PostgreSQL, etc.)

* *Les modèles Factory.* Vous pouvez créer une instance d'une classe en utilisant l'opérateur `new`.
  Ou vous pouvez la créer avec une classe factory. Une classe factory est juste une classe encapsulant la création d'autres objets.
  Elles sont utiles, car elles simplifient l'injection de dépendance.
  Leur utilisation simplifie également le test de vos classes modèle et contrôleur.

## Les principaux composants Laminas

Les développeurs de Laminas pensent que le framework devrait être un ensemble de composants isolés avec des dépendances minimales les uns par rapport aux autres. C'est ainsi que Laminas est organisé.

L'idée est de vous laisser utiliser certains composants Laminas seuls, même si vous créez votre site avec un autre framework.
Cela devient encore plus facile, en gardant à l'esprit que chaque composant de Laminas est un package installable par Composer, de sorte que vous pouvez facilement installer n'importe quel composant Laminas avec ses dépendances via une seule commande.

Il existe plusieurs composants Laminas "principaux" qui sont utilisés (explicitement ou implicitement) dans presque toutes les applications Web:

  * @`Laminas\EventManager` Ce composant permet d'envoyer des événements et d'enregistrer des listeners pour y réagir.

  * @`Laminas\ModuleManager`. Dans Laminas, chaque application est composée de modules et ce composant contient des fonctionnalités pour les charger.

  * @`Laminas\ServiceManager`.  C'est le registre centralisé de tous les services disponibles dans l'application, ce qui permet d'accéder aux services depuis n'importe quel point du site Web.

  * @`Laminas\Http` fournit une interface facile pour effectuer des requêtes HTTP (Hyper-Text Transfer Protocol).

  * @`Laminas\Mvc`. Prise en charge du modèle Model-View-Controller et séparation de la logique métier de la présentation.

  * @`Laminas\View`. Fournit un système d'aides de vue (view helpers), de filtres de sortie et d'échappement de contenu de variables.
Utilisé dans la couche de présentation.

  * @`Laminas\Form`. Collecte de données de formulaire Web, filtrage, validation et rendu.

  * @`Laminas\InputFilter`. Fournit la possibilité de définir des règles de validation des données de formulaire.

  * @`Laminas\Filter`. Fournit un ensemble de filtres de données couramment utilisés, tels que le string trimmer.

  * @`Laminas\Validator`. Fournit un ensemble de validateurs couramment utilisés.

## Différences avec Laminas Framework 2

Pour les lecteurs qui ont travaillé avec Laminas Framework 2, cette section vous donnera
quelques informations sur ce qui a changé dans la version 3.

Vous trouvererz ci-dessous, les principales différences techniques entre ZF2 et Laminas :

### Rétrocompatibilité

Laminas est une version évolutive, donc la rétrocompatibilité est préservée dans la plupart des cas.

Cependant, certaines modifications doivent être effectuées pour migrer votre code si vous avez utilisé `ServiceLocatorAwareInterface`
(ce que vous avez probablement fait). Avec Laminas, cette interface a été supprimée et toutes les dépendances doivent maintenant être injectées dans les factories.
Ainsi, vous devrez créer des factories pour la plupart de vos contrôleurs, services, aides de vue (view helpers) et plugins de contrôleur.

### Composants

Dans ZF2, les composants étaient stockés dans un seul dépot GIT.
Avec Laminas, les composants sont stockés dans plusieurs dépots GIT, un dépot par composants (par exemple,
`laminas/laminas-mvc`, `laminas/laminas-servicemanager`, `laminas/laminas-form`, etc).
Cela permet de développer et déployer les composants indépendamment les uns des autres.

Les composants sont encore plus isolés  qu'auparavant et ont des dépendances minimales les uns sur les autres. Le composant @`Laminas\Mvc`
a été divisé en plusieurs nouveaux composants. Par exemple, les fonctionnalités de routage ont été déplacées vers le nouveau composant @`Laminas\Router`.

Il vous est maintenant recommandé de spécifier les noms des composants dont votre application dépend dans le fichier `composer.json`,
 bien qu'il soit toujours possible de dépendre du paquet `laminas/laminas`, qui est un méta-paquet installant *tous* les composants disponibles.

### Le Component Installer

Dans Laminas, un plugin spécial Composant appelé *component installer* a été introduit. Il permet d'installer des
composants en tant que modules ZF. En injectant notamment des informations sur le composant dans le fichier de configuration de l'application.

### La Performance des ServiceManager et EventManager

Les développeurs Laminas ont fait un excellent travail en améliorant les performances des composants @`Laminas\ServiceManager` et @`Laminas\EventManager`.
Ils sont bien plus rapides qu'avant. L'inconvénient est que vous devez effectuer un travail de migration pour utiliser ces nouvelles fonctionnalités
Les noms des contrôleurs et des services doivent maintenant respecter la fonctionnalité PHP 5.5 appelée `::class`.
Par exemple, si précédemment vous avez déclaré votre contrôleur en tant que `Application\Controller\Index`,
vous devrez le renommer en `IndexController::class`. Si précédemment vous nomiez vos services comme vous le souhaitiez, il est
maintenant recommandé de le faire en utilisant `ServiceClassName::class`. Lisez la documentation du composant `Mvc`
pour plus d'informations.

### PSR-4

Dans ZF2, la structure des dossiers recommandée était PSR-0, tandis que dans Laminas, il s'agit de PSR-4.
Cela nécessite un (petit) travail de migration.

### Middleware

Laminas croit que l'avenir de PHP est dans le middleware.
"Le middleware (ou interlogiciel) est, tout simplement, du code assis (???) entre une requête HTTP entrante et la réponse HTTP sortante."
Vous pouvez maintenant enregistrer un listener de middleware dans une application MVC. ?!*%? Si quelqu'un peut proposer une meilleure définition...

### Focus sur la documentation

Maintenant, chaque dossier de composants contient sa propre documentation.
La documentation est maintenant mieux conçue, elle est au format Markdown.

## Résumé

Un framework PHP est une bibliothèque qui vous donne une base de code et définit des façons cohérentes de créer des applications web.
Laminas Framework est un framework de développement web moderne créé par Laminas Technologies, le fournisseur du langage PHP.
Il offre aux développeurs des capacités exceptionnelles pour la création de sites web évolutifs et sécurisés.
Laminas est sous licence BSD-like et peut être utilisé gratuitement dans des applications commerciales et open-source.
