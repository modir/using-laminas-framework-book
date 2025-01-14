# L'Application Skeleton de Laminas {#skeleton}

Laminas Framework vous fournit une application modèle plus connue sous l'appelation "application skeleton".
Elle facilite la création de vos nouveaux sites web lorsque l'on part de zéro.
Dans ce chapitre, nous allons voir comment l'installer et comment la faire tourner en créant un hôte virtuel Apache.
Nous vous recommandons de vous reporter à l'[Annexe A. Configuration d'un environnement de développement web](#devenv)
avant de lire ce chapitre pour configurer votre environnement de développement.

## Récupérer l'application Skeleton Laminas

L'application Skeleton est une application simple basée sur Laminas qui contient la plupart des choses nécessaires pour créer
votre propre site.

Le code de l'application est stocké sur GitHub et est accessible par [ce lien](https://github.com/laminas/LaminasSkeletonApplication).
Cependant, il est préférable car plus simple de passer par le gestionnaire de dépendances [Composer](http://getcomposer.org/),
comme illustré ci-dessous.

D'abord, vous devez obtenir la dernière version de Composer. Pour cela saisissez les commandes suivantes :

```
cd ~

php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"

php composer-setup.php

php -r "unlink('composer-setup.php');"
```

Les commandes ci-dessus changent votre répertoire de travail pour être votre répertoire de base,
téléchargent le script d'instalation `composer-setup.php` dans votre répertoire de travail, l'exécutent, et, finalement,
supprime le programme d'installation.

T> Une fois que vous avez exécuté les commandes ci-dessus, vous devriez voir apparaitre un fichier `composer.phar` dans votre répertoire de travail.

Maintenant, tapez la commande suivante à partir de votre invite de commande :

```
php composer.phar create-project -sdev laminas/skeleton-application helloworld
```

La commande ci-dessus télécharge l'application Laminas Skeleton dans le dossier `helloworld` et exécute son programme
d'installation interactif.
Vous allez maintenant devoir répondre à quelques questions en répondant oui (`y`) ou non (`n`) et en appuyant sur Entrée.
Vos réponses permettront au programme d'installation de déterminer les dépendances à installer.
Si vous ne savez pas quoi répondre, répondez 'n' (non), vous pourrez toujours installer ces dépendances plus tard.

Pour une première fois, vous pouvez répondre aux questions comme ceci :

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

Une fois que vous avez répondu aux questions, le programme d'installation va télécharger et installer tous les packages
nécessaires et vous demander dans quel fichier de configuration vous souhaitez injecter les informations sur les modules
installés. Lorsque vous y êtes invité, tapez '1' et appuyez sur Entrée:

```
 Please select which config file you wish to inject 'Laminas\Form' into:
  [0] Do not inject
  [1] config/modules.config.php
  [2] config/development.config.php.dist
  Make your selection (default is 0):1

  Remember this option for other packages of the same type? (y/N) y
```

Ensuite, le programme d'installation vous demandera si vous souhaitez supprimer les fichiers de contrôle de version
du projet. Puisque vous allez probablement utilisez votre application avec votre propre système de contrôle de version
(comme Git), vousn'avez pas besoin des fichiers VCS existants, donc tapez 'y' et appuyez sur Entrée :

```
Do you want to remove the existing VCS (.git, .svn..) history? [Y,n]? y
```

Maintenant, copiez le fichier `composer.phar` dans votre nouveau dossier `helloworld` :

```
cp composer.phar helloworld
```

Une dernière étape, très importante, consiste à activer le *mode de développement* en tapant la commande suivante:

~~~
cd helloworld
php composer.phar development-enable
~~~

I> Le mode développement est généralement utilisé lorsque vous *développez* une application.
I> Lorsque vous activez le mode développement, des fichiers de configuration "développement" sont créés
I> dans le dossier configuration de votre application. Avec ce mode, votre application peut éventuellement charger des
I> modules de "développement" supplémentaires. Enfin, la mise en cache de la configuration est également désactivéee,
I> ce qui vous permet de modifier les fichiers de configuration de votre site et d'en voir immédiatement les modifications.
I>
I> Une fois que vous avez terminez de développer votre application, vous pouvez activer le mode *production* en tapant ce qui suit :
I>
I> `php composer.phar development-disable`

Bravo ! Le plus dur est passé. Regardons maintenant dans le dossier `helloworld`.

## Organisation des Dossiers

Chaque site basé sur Laminas (y compris l'application squeleton) est organisé de la même manière.
Bien sûr, vous pouvez configurer votre application pour utiliser une autre disposition de dossier, mais cela peut rendre
difficile la prise en charge de votre site par d'autres personnes qui ne sont pas familiarisées avec une telle structure
de dossier.

Jetons un coup d'œil à la structure typique des dossiers Laminas (voir figure 2.1) :

![Figure 2.1. Structure typique des dossiers](../en/images/skeleton/skeleton_dir_structure.png)

Comme vous pouvez le voir, dans le répertoire de premier niveau (on l'appellera désormais `APP_DIR`), il y a plusieurs
fichiers :

* `composer.json` est un fichier de configuration au format JSON nécessaire à Composer.

* `composer.lock` qui contient des informations sur les packages installés avec Composer.

* `composer.phar` est une archive PHP exécutable contenant le code de l'outil de gestion des dépendances Composer.

* `docker-compose.yml` et `Dockerfile` sont des fichiers nécessaires à [Docker Container Manager](https://www.docker.com). Sont utilisation est optionnelle et nous n'allons pas traiter le sujet dans ce guide.

* `LICENSE.md` est un fichier texte contenant la licence Laminas (que vous avez pu lire dans [Introduction à Laminas Framework](#intro)).
  La licence ne vous permet pas de supprimer ou modifier ce fichier, alors n'y touchez pas.

* `phpunit.xml.dist` est un fichier de configuration pour [PHPUnit](https://phpunit.de/) (framework de test unitaire).
  Vous utilisez ce fichier lorsque vous souhaitez créer des tests unitaires pour votre site.

* `README.md` est fichier texte contenant une brève description de l'application skeleton. Vous allez généralement
  remplacer le contenu de ce fichier par les informations sur votre site web comme son nom, son fonctionnement et comment
  l'installer.

* `TODO.md` est un fichier auxiliaire qui peut être supprimé en toute sécurité.

* `Vagrantfile` est un fichier auxiliaire qui contient la configuration de [Vagrant](https://www.vagrantup.com/), qui est
  un gestionnaire d'environnement de développement virtuel.
  Vous pouvez ignorer ce fichier si vous ne savez pas ce qu'est Vagrant. Nous n'aborderons pas le sujet.

Et nous avons aussi plusieurs sous-dossiers :

Le répertoire `config` contient les fichiers de configuration de l'application.

Le répertoire `data` contient les données que votre application pourrait créer. il peut également contenir un cache utilisé pour accélérer Laminas Framework.

Le répertoire `module` contient tous les modules de l'application.
Actuellement, il existe un seul module appelé Application. Le module `Application` est le module principal de votre site.
Vous pouvez créer d'autres modules ici si vous le souhaitez. Nous parlerons des modules un peu plus tard.

Le dossier `vendor` va contenir les fichiers des bibliothèques tierses, y compris les fichiers de Laminas Framework.
C'est typiquement Composer qui s'occupe d'y copier les fichiers.

Le dossier `public` contient les données publiques accessibles aux internautes. Comme vous le savez,
les internautes communiquent avec le serveur via le fichier *index.php*, qui est le *point d'entrée* de votre site web.

I> Votre site aura un seul point d'entrée, *index.php*, c'est plus sécurisé que de permettre à quiconque d'accéder à tous vos fichiers PHP.

À l'intérieur du répertoire `public`, vous pouvez également trouver le fichier .htaccess.
Son but principal est de définir des règles de réécriture d'URL.

Le répertoire `public` contient plusieurs sous-dossiers qui sont également accessibles par les internautes :

* `css` contient tous les fichiers CSS.
* `fonts` contient des polices Web spécifiques à l'application.
* `img` contient les images accessibles au public (.JPG, .PNG, .GIF, .ICO, etc.).
* `js` contient les fichiers JavaScript utilisés par vos pages web.
  Généralement, les fichiers de la bibliothèque [jQuery](http://jquery.com/) sont placés ici, mais vous pouvez également
  y placer vos propres fichiers JavaScript.

Q> **Qu'est ce que jQuery ?**
Q>
Q> jQuery est une bibliothèque JavaScript qui a été créée pour simplifier le code JavaScript des pages HTML.
Q> jQuery permet d'attacher facilement des événements à certains éléments HTML, ce qui rend très simple
Q> l'interactivité de vos pages HTML.

Parce que l'application Laminas Skeleton est stockée sur GitHub, à l'intérieur de la structure du répertoire, vous trouverez
un fichier caché `.gitignore`. C'est un fichier du système de contrôle de version [GIT](http://git-scm.com/).
Vous pouvez l'ignorer (ou même le supprimer si vous n'envisagez pas de stocker votre code dans un dépôt GIT).

## Dépendances

Une dépendance est un code tiers utilisé par votre application.
Par exemple Laminas Framework est une dépendance pour votre site.

Dans Composer, toute bibliothèque est appelée *un package*.
Tous les package installables par Composer sont déclarés sur le site [Packagist.org](https://packagist.org/).
Avec Composer, vous pouvez identifier les packages dont votre application a besoin, les télécharger et les installer automatiquement.

Les dépendances de l'application skeleton sont déclarées dans le fichier APP_DIR/composer.json` (voir ci-dessous) :

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

Q> **C'est quoi du JSON?**
Q>
Q>JSON (JavaScript Object Notation), est un format de fichier utilisé pour la représenter de facon lisible par l'homme
Q> des structures simples et de tableaux associatifs imbriqués.
Q> Bien que JSON soit issu du monde JavaScript, il est utilisé en PHP et dans d'autres langues car il est pratique pour stocker
Q> des données de configuration.

Dans ce fichier, nous voyons quelques informations de base sur notre application (son nom, sa description, sa licence,
ses mots-clés et sa page d'accueil).
Vous modifierez ces informations par la suite en fonction de vos futurs projets.
Ces informations sont facultatives, vous pouvez donc même les supprimer en toute sécurité si vous ne prévoyez pas de
publier votre application dans le catalogue `Packagist`.

Ce qui est nous intéresse maintenant, c'est la clé `require`. Elle contient les déclarations des dépendances de notre
application.
Nous voyons que nous avons besoin de la version 5.6 ou supérieur de PHP et de plusieurs composants Laminas Framework,
comme `laminas-mvc`, `laminas-mvc-form`, etc.

Les informations contenues dans le fichier `composer.json` suffisent à localiser les dépendances, à les télécharger et à
les installer dans le sous-répertoire `vendor`.
Si à tout moment vous déterminez que vous devez installer une autre dépendance, vous pouvez le faire en éditant
le `composer.json` et en ajoutant votre dépendance, puis en tapant les commandes suivantes à partir de votre invite de commande :

{line-numbers=off}
~~~
php composer.phar self-update
php composer.phar install
~~~

Les commandes ci-dessus mettront à jour Composer vers sa dernière version disponible, puis installeront vos dépendances.
Petite précision, Composer n'installe pas PHP pour vous, il s'assure simplement que PHP a une version appropriée,
si ce n'est pas le cas, il vous avertira.

Si vous regardez dans le dossier `vendor`, vous verrez qu'il contient beaucoup de fichiers.
Les fichiers Laminas Framework sont situés dans le répertoire `APP_DIR/vendor/laminas/` (figure 2.2).

![Figure 2.2. Dossier Vendor](../en/images/skeleton/vendor_dir.png)

I> Dans d'autres frameworks, une autre méthode (conventionnelle) de dépendance est utilisée.
I> Il vous suffit de télécharger la bibliothèque de dépendances sous forme d'archive, de la décompacter et de la placer
I> quelque part dans la structure de votre application (généralement dans le dossier `vendor`).
I> Cette approche a été utilisée dans la première version de Laminas Framework.
I> Mais, dans Laminas Framework, il est recommandé d'installer ses dépendances avec Composer.

## Hôte virtuel Apache

Maintenant, nous sommes presque prêts à faire tourner notre application skeleton.
La dernière chose que nous allons faire est de configurer un hôte virtuel Apache.
Les hôtes virtuels (virtual hosts) vous permettent d'exécuter plusieurs sites internet sur la même machine.
Ces sites virtuels sont différenciés par un nom de domaine (comme `site.mydomain.com` et `site2.mydomain.com`) ou
par un numéro de port (comme `localhost` et `localhost:8080`).
Les hôtes virtuels fonctionnent de telle manière que les internautes ne savent si les sites fonctionnent sur la même
machine ou sur des machines différentes.

Actuellement, l'application skeleton est à l'intérieur de votre répertoire personnel. Pour en informer Apache,
nous devons éditer le fichier hôte virtuel.

I> Le fichier hôte virtuel peut se trouver sur un chemin différent, selon le type de système d'exploitation.
I> Par exemple, sous Linux Ubuntu, il se trouve dans le fichier `/etc/apache2/sites-available/000-default.conf`.
I> Pour obtenir des informations spécifiques au système d'exploitation et au serveur concernant les hôtes virtuels,
I> reportez-vous à l'annexe A. Configuration de l'environnement de développement Web(#devenv).

Modifions maintenant le fichier hôte virtuel par défaut pour qu'il ressemble à ce qui suit (nous supposons que vous utilisez Apache v2.4):

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

La ligne 1 du fichier permet à Apache d'écouter toutes les adresses IP (*) sur le port 80.

La ligne 2 définit l'adresse e-mail du Webmaster. Si un problème arrive au site, Apache envoie un e-mail
d'alerte à cette adresse. Vous pouvez y entrer votre adresse e-mail.

La ligne 4 définit le répertoire racine du document (`APP_DIR/public`).
Tous les fichiers et répertoires sous la racine du document seront accessibles par les internautes.
Vous devez définir ceci comme étant le chemin absolu du répertoire public de l'application skeleton.
Ainsi, les dossiers et les fichiers dans le répertoire public (comme `index.php`, `css`, `js`, etc.) seront accessibles,
alors que les dossiers et les fichiers au-dessus du répertoire public (comme `config`, `module`, etc.) ne seront pas
accessibles aux internautes, cela améliore la sécurité du site Web.

Les lignes 6 à 10 définissent des règles pour le répertoire racine du document (`APP_DIR/public`).
Par exemple, la directive DirectoryIndex indique à Apache qu'*index.php* doit être utilisé comme fichier d'index par défaut.
La directive `AllowOverride All` permet de définir des règles dans les fichiers .htaccess.
La directive `Require all granted` permet à tout le monde de visiter le site Web.

W> Laminas Framework utilise le module de réécriture d'URL d'Apache pour rediriger les internautes vers le script
W> d'entrée de votre site Web. Assurez-vous que le module `mod_rewrite` est activé sur votre serveur Apache.
W> Pour plus d'informations sur l'activation du module, reportez-vous à l'[annexe A. Configuration de l'environnement de développement Web](#devenv).

T> Après avoir édité le fichier de configuration, n'oubliez pas de redémarrer Apache pour appliquer vos modifications.

## Affichage du site dans votre navigateur

Pour ouvrir le site Web, tapez "http://localhost" dans la barre de navigation de votre navigateur et appuyez sur Entrée.
La figure 2.3 montre le site en action.

Sur la page qui apparaît, vous pouvez voir le menu de navigation en haut.
La barre de navigation ne contient qu'un lien nommé *Home*.
Sous la barre de navigation, vous pouvez voir le titre "Welcome to Laminas Framework".
Sous ce titre, vous pourrez trouver des conseils pour les débutants sur la façon de développer de nouvelles applications
basées sur Laminas.

![Figure 2.3. Laminas Skeleton Application](../en/images/skeleton/Laminas_skeleton_default_view.png)

## Création du projet NetBeans

Maintenant que nous avons mis en place l'application squelette, nous allons vouloir effectuer quelques
changements à l'avenir. Pour naviguer facilement dans la structure du répertoire, éditer les fichiers et
déboguer le site, la pratique courante consiste à utiliser un IDE (Integrated Development Environment).
Dans ce livre, nous utilisons l'EDI NetBeans (voir
[Annexe A. Configuration de l'environnement de développement](#devenv) pour plus d'informations sur
l'installation de NetBeans).

Pour créer un projet NetBeans pour notre application squelette, lancez NetBeans et ouvrez le menu
*Fichier-> Nouveau projet...*. La boîte de dialogue *Nouveau projet* apparaît (voir figure 2.4).

![Figure 2.4. Créer un projet NetBeans - Page choix du projet ](../en/images/skeleton/netbeans_create_project.png)

Dans la page *Choose Project* qui apparaît, vous devez choisir le type projet PHP et dans la liste de droite,
sélectionnez *Application with Existing Sources*
(parce que nous avons déjà le code de l'application squelette).
Cliquez ensuite sur le bouton *Suivant* pour passer à la page suivante (illustrée à la figure 2.5).

![Figure 2.5. Création du projet NetBeans - Nom et Emplacement](../en/images/skeleton/netbeans_create_project_step2.png)

Dans la boite de dialogue *Nom et emplacement*, vous devez entrer le chemin du code (comme
*/home/username/helloworld*), le nom du projet (par exemple, helloworld) et spécifier la version de PHP
utilisée par votre code (PHP 5.6 ou plus). La version PHP est nécessaire pour le vérificateur de syntaxe PHP
NetBeans qui va scanner votre code PHP pour les erreurs et les mettre en évidence.
Appuyez sur le bouton *Suivant* pour passer à la boite de dialogue suivante (illustrée à la figure 2.6).

![Figure 2.6. Création d'un projet NetBeans - Choix de la Configuration](../en/images/skeleton/netbeans_create_project_step3.png)

Dans la page *Run Configuration* page, il est recommandé de spécifier la manière dont vous exécutez le site web
(site web local) et l'URL du site Web (`http://localhost`). Gardez le champ *Index File* vide (car nous
utilisons le mod_rewrite, le chemin d'accès réel à votre fichier `index.php` est masqué par Apache).
Si vous voyez le message d'avertissement "Le fichier d'index doit être spécifié pour exécuter ou déboguer
un projet en ligne de commande", ignorez-le.

Cliquez sur le bouton *Finish* pour créer le projet. Lorsque le projet *helloworld* est créé avec succès,
vous devez voir la fenêtre projet (voir la figure 2.7).

![Figure 2.7. Fenêtre de projet NetBeans](../en/images/skeleton/netbeans_project_window.png)

Dans la fenêtre projet, vous pouvez voir la barre de menus, la barre d'outils, le volet *Projets* où vos
fichiers de projet sont répertoriés et, dans la partie droite de la fenêtre, vous pouvez voir le code du
fichier d'entrée `index.php`.

Reportez-vous à l'[Annexe B. Introduction au développement PHP dans l'EDI NetBeans](#netbeans)
pour plus de conseils sur l'utilisation de NetBeans, y compris le lancement et le débogage
de sites basés sur Laminas.

T> **Il est temps de passer aux choses sérieuses...**
T>
T> Congratulations! We've done the hard work of installing and running
T> the Laminas Skeleton Application, and now it's time to have a rest
T> and read about some advanced things in the last part of this chapter.

## Le fichier .htaccess

Nous avons mentionné le fichier `APP_DIR/public/.htaccess` en parlant de la structure typique des dossiers.
Essayons maintenant de comprendre le rôle de ce fichier.

Le fichier `.htaccess` est en fait un fichier de configuration du serveur Apache permettant de surcharger
la configuration globale de certains serveurs web. Le fichier `.htaccess` est une configuration au niveau
du répertoire, ce qui signifie qu'il n'affecte que son propre dossier et tous ses sous-dossiers.


Le contenu du fichier `.htaccess` est présenté ci-dessous :

~~~text
RewriteEngine On
# La règle suivante indique à Apache que si le nom de fichier
# demandé existe, il suffit de le renvoyer.
RewriteCond %{REQUEST_FILENAME} -s [OR]
RewriteCond %{REQUEST_FILENAME} -l [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^.*$ - [L]

# Ce qui suit renvoit toutes les autres requêtes vers index.php.
# Cette condition garantit que si vous utilisez des alias Apache
# pour effectuer un hébergement virtuel de masse ou si vous avez
# installé le projet dans un sous-répertoire, le chemin de base
# sera ajouté au début pour permettre une résolution correcte du
# fichier index.php; il fonctionnera également dans des
# environnements sans alias, offrant ainsi une solution sur mesure
# et sécurisée.
RewriteCond %{REQUEST_URI}::$1 ^(/.+)/(.*)::\2$
RewriteRule ^(.*) - [E=BASE:%1]
RewriteRule ^(.*)$ %{ENV:BASE}/index.php [L]
~~~

La ligne 1 indique au serveur Apache d'activer le moteur de réécriture d'URL (`mod_rewrite`).
Le moteur de réécriture modifie les demandes d'URL entrantes, en fonction des règles d'expression régulières.
Cela vous permet de mapper des URL arbitraires sur votre structure d'URL interne comme vous le souhaitez.

Les lignes 4 à 7 définissent des règles de réécriture qui indiquent au serveur que si le client
(navigateur web) demande un fichier qui existe dans le répertoire racine du document plutôt que de
renvoyer le contenu de ce fichier en tant que réponse HTTP.
Parce que nous avons notre répertoire `public` à l'intérieur de la racine de l'hôte virtuel,
nous permettons aux utilisateurs du site de voir tous les fichiers dans le répertoire `public`, y compris
`index.php`, les fichiers CSS, JavaScript et les images.

Les lignes 14 à 16 définissent les règles de réécriture qui indiquent à Apache ce qu'il faut faire si
l'utilisateur du site demande un fichier qui n'existe pas à la racine du serveur.
Dans ce cas, l'utilisateur doit être redirigé vers `index.php`.

Le tableau 2.1 contient plusieurs exemples de réécriture d'URL. Les première et deuxième URL pointent
vers des fichiers existants, ainsi `mod_rewrite` renvoie les chemins de fichiers demandés.
L'URL du troisième exemple pointe vers un fichier `htpasswd` inexistant (qui peut être le symptôme d'une
attaque par un pirate informatique) et, en fonction de nos règles de réécriture, le moteur renvoie le
fichier `index.php`.

{title="Table 2.1. Exemples de réécriture d'URL"}
|-------------------------------------|-----------------------------------------|
| **URL demandée**                   | **URL réécrite**                       |
|-------------------------------------|-----------------------------------------|
| `http://localhost/index.php`        | Le fichier existe; retourne le fichier local       |
|                                     | `APP_DIR/public/index.php`              |
|-------------------------------------|-----------------------------------------|
| `http://localhost/css/bootstrap.css`| Le fichier existe; retourne le fichier local       |
|                                     | `APP_DIR/public/css/bootstrap.css`      |
|-------------------------------------|-----------------------------------------|
| `http://localhost/htpasswd`         | Le fichier n'existe pas; renvoie             |
|                                     | `APP_DIR/public/index.php` à la place.     |
|-------------------------------------|-----------------------------------------|

## Bloquer l'accès au site web en fonction de l'adresse IP

Parfois, il peut être nécessaire de bloquer l'accès à votre site depuis de toutes les adresses IP
sauf la vôtre. Par exemple, lorsque vous développez un site web, vous ne voulez pas que quelqu'un voit
votre travail incomplet. En outre, vous ne souhaiterez peut-être pas laisser Google ou d'autres moteurs
de recherche indexer votre site.

Pour interdire l'accès à votre site, vous pouvez modifier l'hôte virtuel et lui ajouter la ligne suivante :

~~~text
Require ip <your_ip_address>
~~~

Q> **Comment puis-je connaitre mon adresse IP ?**
Q>
Q> Vous pouvez utiliser le site [http://www.whatismyip.com](http://www.whatismyip.com/) pour déterminer
Q> votre adresse IP externe. L'adresse IP externe est l'adresse par laquelle d'autres ordinateurs sur
Q> Internet peuvent accéder à votre site.

## Authentification HTTP

Vous pouvez autoriser l'accès à votre site à certains utilisateurs.
Par exemple, lorsque vous présentez votre site à votre patron, vous lui donnez un nom d'utilisateur et
un mot de passe pour vous y connecter.

Pour autoriser l'accès à votre site par nom d'utilisateur et mot de passe, vous pouvez modifier le fichier
virtual host comme ceci :

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

La ligne 5 définit la méthode d'authentification de base. La méthode la plus courante est Basic.
Cependant, il est important de savoir que l'authentification de base envoie le mot de passe du client
au serveur non crypté. Cette méthode ne devrait donc pas être utilisée pour des données hautement sensibles.
Apache prend en charge une autre méthode d'authentification: `AuthType Digest`. Cette méthode est beaucoup
plus sûre. Les navigateurs les plus récents supportent l'authentification Digest.

La ligne 6 définit le texte qui s'affichera à l'utilisateur lorsqu'il essayera de se connecter.

La ligne 7 définit le fichier où les mots de passe seront stockés. Ce fichier doit être créé avec
l'utilitaire `htpasswd`.

La ligne 8 permettra à quiconque répertorié dans le fichier password de se connecter si le mot de passe
saisi est correct.

Pour créer un fichier `passwords`, tapez la commande suivante :

~~~
htpasswd -c /usr/local/apache/passwd/passwords <username>
~~~

Dans la commande ci-dessus, vous devez remplacer l'espace `<username>` par le nom de l'utilisateur.
Vous pouvez choisir un nom arbitraire, par exemple "admin". La commande demandera le mot de passe de
l'utilisateur et écrira le mot de passe dans le fichier:

~~~text
# htpasswd -c /usr/local/apache/passwd/passwords <username>
New password:
Re-type new password:
Adding password for user <username>
~~~

Lorsque l'utilisateur tente de visiter le site, il voit la boîte de dialogue d'authentification HTTP.
Pour se connecter au site, il ou elle doit entrer le nom d'utilisateur et le mot de passe correspondant.

I> Pour plus d'informations sur l'authentification HTTP, vous pouvez consulter la rubrique
I> [Authentication and Authorization](http://httpd.apache.org/docs/current/howto/auth.html) de la documentation Apache

## Gérer plusieurs hôtes virtuels

Lorsque vous développez plusieurs sites sur la même machine, vous devez créer plusieurs hôtes virtuels.
Pour chaque hôte virtuel, vous devez spécifier un nom de domaine (comme `site1.mydomain.com`).
Si vous n'avez pas de nom de domaine, vous pouvez spécifier un autre port (voir l'exemple ci-dessous).

~~~text
# La directive Listen  indique à Apache d'écouter les requêtes sur le port 8080
Listen 8080

<VirtualHost *:8080>
    ...
</VirtualHost>
~~~

Pour accéder au site Web, dans la barre de navigation de votre navigateur, entrez "http://localhost:8080".

T> Après avoir modifié le fichier de configuration de l'hôte virtuel, vous devez redémarrer Apache pour appliquer les modifications.

## Les fichiers Hosts

Lorsque plusieurs sites locaux sont mappés à différents ports, il devient difficile de se souvenir du port
de chaque site. Pour simplifier cela, vous pouvez utiliser un hôte virtuel basé sur le nom et définir un
alias pour votre site dans votre fichier système `hosts`.

Tout d'abord, modifiez votre fichier hôte virtuel Apache pour qu'il soit un hôte virtuel basé sur le nom :

~~~text
<VirtualHost *:80>
    # Ajouter la directive ServerName
	ServerName site1.localhost
	...
</VirtualHost>
~~~

Ensuite, vous devez éditer le fichier `hosts`. Le fichier `hosts` est un fichier système qui contient
des mappages entre les adresses IP et les noms d'hôte. Le fichier `hosts` contient des lignes de texte
consistant en une adresse IP dans le premier champ de texte suivi par un ou plusieurs noms d'hôtes.

Pour ajouter un alias à vos sites locaux, ajoutez des lignes pour chacun de vos sites internet, comme indiqué
dans l'exemple ci-dessous.

~~~text
127.0.0.1            site1.localhost
~~~

Dorénavant, vous serez en mesure d'entrer simplement "site1.localhost" dans la barre d'adresse de votre
navigateur au lieu de vous souvenir du numéro de port.

I> Sous Linux, le fichier hosts se trouve dans `/etc/hosts`.
I> Sous Windows, le fichier se trouve dans  `C:\Windows\System32\drivers\etc\hosts`.
I> Pour éditer le fichier, vous devez être en mode administrateur. Veuillez noter également que certains
I> logiciels anti-virus peuvent bloquer les modifications apportées au fichier hosts, vous
I> devrez ainsi désactiver temporairement votre anti-virus pour éditer le fichier et le réactiver ensuite.

I> Si vous avez acheté un vrai nom de domaine pour votre site web (comme `example.com`), vous n'avez pas
I> besoin de modifier votre fichier `hosts`, car Apache sera capable de résoudre l'adresse IP de votre site
I> web en utilisant le système DNS. Vous modifiez votre fichier hosts uniquement lorsque le système DNS ne
I> sait rien du nom de domaine et ne peut pas résoudre l'adresse IP de votre site Web.

## Usage avancé de Composer

Précédemment, nous avons utilisé Composer pour installer la bibliothèque Laminas Framework.
Décrivons maintenant brièvement quelques exemples d'utilisation avancés de Composer.

Comme nous le savons déjà, la seule clé requise dans le fichier `composer.json` est `require`. Cette clé
indique quels sont les paquetages requis par votre application :

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

### Noms des Packages et Versions

Un nom de package se compose de deux parties : le nom du fournisseur (vendor) et le nom du projet.
Par exemple, le nom du paquet "laminas/laminas-mvc" se compose du nom du fournisseur "laminas"
et du nom du projet "laminas-mvc". Vous pouvez rechercher d'autres packages du fournisseur "laminas"
sur le site [Packagist.org](https://packagist.org/search/?q=laminas) (voir la figure 2.8 pour un exemple).

![Figure 2.8. Vous pouvez rechercher des paquets sur Packagist.org ](../en/images/skeleton/packagist_search.png)

Un package a également un numéro de version associé. Un numéro de version se compose d'un numéro majeur,
d'un numéro mineur, d'un numéro de build facultatif et d'un suffixe de stabilité facultatif
(par exemple b1, rc1). Dans la clé `require`, nous spécifions quelles versions du paquet nous acceptons.
Par exemple, "^5.6" signifie que nous pouvons installer des versions supérieures à "5.6", mais inférieures
à "6.0" (ie que nous ne pouvons installer que les paquets qui ne cassent pas la compatibilité descendante).
Dans le tableau 2.2, les manières possibles de spécifier les versions acceptables sont présentées:


{title="Table 2.2.  Définitions des versions de Package"}
|-------------------------|----------------------------------------------------------------------------|
| *Exemple*    | *Description*                                                              |
|-------------------------|----------------------------------------------------------------------------|
| 3.0.1                   | Version exacte. Dans cet exemple, seule la version 3.0.1 peut être installée.   |
|-------------------------|----------------------------------------------------------------------------|
| >=3.0.1                 | Une version supérieure ou égale peut être installée (3.0.1, 3.2.1, etc.)             |
|-------------------------|----------------------------------------------------------------------------|
| >3.0.1                  | Une version ultérieure peut être installée (3.0.2 etc.)                              |
|-------------------------|----------------------------------------------------------------------------|
| <=3.0.1                 | Une version inférieure ou égale peut être installée (1.0, 1.5, 2.0.0 etc.)             |
|-------------------------|----------------------------------------------------------------------------|
| <3.0.1                  | Une version strictement inférieure peut être installée (1.0, 1.1, 1.9, etc.)                       |
|-------------------------|----------------------------------------------------------------------------|
| !=3.0.1                 | Toutes les versions sauf cette version peuvent être installées.                         |
|-------------------------|----------------------------------------------------------------------------|
| >=3.0,<3.1.0            | Toute version appartenant à cette gamme de versions peut être installée.          |
|-------------------------|----------------------------------------------------------------------------|
| 3.*                     | Toute version ayant un numéro majeur égal à 3 peut être installée (peu importe le numéro mineur).                                                               |
|-------------------------|----------------------------------------------------------------------------|
| ~3.0                    | Toute version à partir de 3.0, mais inférieure à la version majeure suivante (équivalent de > 3.0, <4.0).                                                |
|-------------------------|----------------------------------------------------------------------------|
| ^3.0                    | Toute version à partir de la version 3.0, mais inférieure à la version majeure suivante (équivalente à >= 3.0, <4.0). Semblable à ~3.0, mais il se rapproche de la version sémantique, et permettra toujours des mises à jour sans rupture.           |
|-------------------------|----------------------------------------------------------------------------|

### Installation et mise à jour des paquets

Nous avons vu comment utiliser la commande `php composer.phar install` pour installer nos dépendances.
Dès que vous appelez cette commande, Composer trouve, télécharge et installe les dépendances dans votre
sous-dossier `vendor`.

Q> **L'instalation des dépendances avec Composer est elle sure ?**
Q>
Q> Eh bien, certaines personnes peuvent avoir peur des gestionaires des dépendances du style Composer
Q> car elles pensent que quelqu'un peut mettre à jour les dépendances du système par erreur ou
Q> intentionnellement, provoquant la rupture de l'application. Notez que Composer n'installe jamais ces
Q> systèmes à la place de vos fichiers, mais les installe dans votre répertoire `APP_DIR/vendor/`.

Après l'installation, Composer crée également le fichier `APP_DIR/composer.lock`. Ce fichier contient
maintenant les versions réelles des packages installés. Si vous réexécutez la commande d'installation,
Composer intérogera le fichier `composer.lock`, vérifiera les dépendances déjà installées et, comme tous
les paquets sont déjà installés et à jour, il se fermera simplement sans rien faire.

Supposons maintenant que, à un moment donné, de nouvelles mises à jour de sécurité pour vos paquets de
dépendances soient publiées. Vous voudrez mettre à jour vos paquets pour garder votre site web sécurisé.
Vous pouvez le faire en tapant ce qui suit :

`php composer.phar update`

Si vous souhaitez mettre à jour une seule dépendance, tapez son nom comme suit :

`php composer.phar update laminas/laminas-mvc`

Après la commande `update`, votre fichier `composer.lock` sera également mis à jour.

Q> **Que faire si je souhaite revenir à une version antérieure du package ?**
Q>
Q> Si la procédure de mise à jour a provoqué des problèmes indésirables sur votre système, vous pouvez
Q> revenir en arrière en rétablissant les modifications apportées à votre fichier `composer.lock` et en
Q> réexécutant la commande d'installation. La restauration des modifications de `composer.lock` est facile
Q> si vous utilisez un système de contrôle de version, comme GIT ou SVN. Si vous n'utilisez pas de système
Q> de contrôle de version, faites une copie de sauvegarde de `composer.lock` avant la mise à jour.

### Ajouter une nouvelle dépendance

Si vous souhaitez ajouter une nouvelle dépendance à l'application, vous pouvez éditer `composer.json`
manuellement ou taper une commande `require`. Par exemple, pour installer le module Doctrine ORM sur votre
site (pour ajouter le package "doctrine/doctrine-module" aux dépendances de l'application), tapez ce qui suit :

`php composer.phar require doctrine/doctrine-module 2.*`

La commande ci-dessus édite le fichier `composer.json` et télécharge et installe le paquet.
Nous utiliserons cette commande plus tard dans le chapitre [Managing Database with Doctrine](#doctrine),
lorsque nous nous familiariserons avec la gestion de base de données.

### Paquets Virtuels

Composer peut être utilisé pour exiger certaines fonctionnalités nécessaires à votre système.
Vous avez déjà vu que nous avions besoin de "php:^5.6". Le paquet PHP est un paquet virtuel représentant
PHP lui-même. Vous pouvez également exiger d'autres choses, comme les extensions PHP (voir le tableau 2.3
ci-dessous).

{title="Table 2.3. Paquets Virtuels Composer"}
|------------------------------------------------------------------------------------------------------|
| *Exemple*    | *Description*                                                              |
|------------------------------------------------------------------------------------------------------|
| "php":"^5.6"            | Requiert une version PHP supérieure ou égale à 5.6, mais inférieure à 6.0.         |
|------------------------------------------------------------------------------------------------------|
| ext-dom, ext-pdo-mysql  | Nécessite les extensions PHP DOM et PDO MySQL                                   |
|------------------------------------------------------------------------------------------------------|
| lib-openssl             | Nécessite la bibliothèque OpenSSL                                                    |
|------------------------------------------------------------------------------------------------------|

Vous pouvez utiliser la commande `php composer.phar show --platform` pour afficher la liste des packages
virtuels disponibles pour votre machine.

### Composer et les gestionnaires de version

Si vous utilisez un système de contrôle de version (comme Git), vous serez curieux de savoir ce qui devrait
être stocké dans Git : seulement le code de votre application ou votre code plus toutes les
dépendances installées par Composer dans `APP_DIR/vendor` ?

En général, il n'est pas recommandé de stocker vos dépendances Composer dans le gestionnaire de version
car cela peut rendre votre dossier très gros et trop lent à extraire et à brancher. Au lieu de cela, vous
devez stocker votre fichier `composer.lock` sous le contrôle de version. Le fichier `composer.lock`
garantit que tout le monde va installer les mêmes versions de dépendances que vous.
Ce qui est utile dans les équipes de développement ayant plus d'un développeur car tous les développeurs
doivent avoir le même code pour éviter les problèmes non désirés avec une mauvaise configuration de
l'environnement.

Q> **Et si une dépendance était déclarée obsolète et retirée de Packagist.org ?**
Q>
Q> Eh bien, la possibilité de retrait du paquet est minimale. Tous les paquets sont libres et open-source
Q> et la communauté d'utilisateurs peut toujours restaurer la dépendance même si elle est retirée de
Q> packagist. En passant, le même concept d'installation de dépendances est utilisé sous Linux
Q> (souvenez-vous du gestionnaire APT ou RPM?), est ce que quelqu'un a déjà perdu un paquet Linux ?

Mais il peut arriver que vous deviez stocker certaines bibliothèques dépendantes sous le contrôle de version :

* Si vous devez apporter des modifications personnalisées au code tiers. Par exemple, supposons que vous
  deviez corriger un bug dans une bibliothèque et que vous ne pouvez pas attendre que le fournisseur de la
  bibliothèque le corrige pour vous (ou si le fournisseur de la bibliothèque ne peut pas corriger le bug).
  Dans ce cas, vous devez placer le code de la bibliothèque sous contrôle de version pour vous assurer que
  vos modifications personnalisées ne seront pas perdues.

* Si vous avez écrit un module ou une bibliothèque réutilisable et souhaitez le stocker dans le répertoire
  du fournisseur sans le publier sur *Packagist.org*. Étant donné que vous n'avez pas la possibilité
  d'installer ce code à partir de Packagist, vous devez le stocker sous le contrôle de version.

* Si vous voulez une garantie à 100% qu'un paquet tiers ne sera pas perdu. Bien que le risque soit minime,
  pour certaines applications, il est essentiel d'être autonome et de ne pas dépendre de la disponibilité
  des paquets sur *Packagist.org*.

## Résumé

Dans ce chapitre, nous avons téléchargé le code du projet Laminas Skeleton Application depuis GitHub et
l'avons installé via le gestionnaire de dépendances Composer. Nous avons configuré l'hôte virtuel Apache
pour indiquer au serveur web l'emplacement du répertoire racine de notre site Web.

L'application squelette illustre la structure de dossiers recommandée d'un site web type. Nous avons le
répertoire public contenant les fichiers accessibles au public par les utilisateurs du site, y compris
le fichier point d'entrée `index.php`, les fichiers CSS, les fichiers JavaScript et les images.
Tous les autres dossiers de l'application sont inaccessibles par les utilisateurs du site et contiennent
la configuration de l'application, les données et les modules.

Dans la deuxième partie du chapitre, nous avons discuté de la configuration avancée d'Apache.
Par exemple, vous pouvez protéger votre site Web avec un mot de passe et réserver l'accès à
certaines adresses IP.

Le gestionnaire de dépendances Composer est un outil puissant pour installer les dépendances de votre site.
Par exemple, Laminas Framework peut être considéré comme une dépendance. Tous les paquets installables par
Composer sont enregistrés dans un catalogue centralisé sur le site Packagist.org.
