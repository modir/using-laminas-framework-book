# Контроль доступа на основе ролей {#roles}

Если вы помните, в предыдущей главе мы создали вебсайт User Demo, который позволял управлять пользователями
и разрешать доступ к некоторым веб страницам только для пользователей вошедших на сайт.
В этой главе мы расширим пример User Demo и покажем как реализовать Контроль доступа на основе ролей (Role-Based Access Control, RBAC).
RBAC позволяет разрешать или запрещать доступ для некоторых пользователей к определенным веб страницам на основе *ролей* и *привелегий (permissions)*.

Так как вы уже многое знаете о Laminas из предыдущих глав, в этой главе мы опустим обсуждение некоторых очевидных вещей
и сконцентрируемся на *концептуальных* моментах. Рекомендуется обратиться к коду примера
*Role Demo*, поставляемого с этой книгой, который является целым вебсайтом, который вы можете запустить и посмотреть в действии.
Весь код, обсуждаемый в данной главе, является частью этого примера.

Компоненты Laminas, обсуждаемые в данной главе :

|--------------------------------|---------------------------------------------------------------|
| *Компонент*                    | *Описание*                                                    |
|--------------------------------|---------------------------------------------------------------|
| @`Laminas\Permissions\Rbac`        | Предоставляет реализацию контейнера RBAC.                       |
|--------------------------------|---------------------------------------------------------------|
| @`Laminas\Cache`                   | Предоставляет возможность хранения данных в кэше.             |
|--------------------------------|---------------------------------------------------------------|

## Загрузка примера Role Demo с GitHub

В качестве демонстрации в данной главе мы создадим настоящий вебсайт *Role Demo*, который показывает как:

 * реализовать роли и привилегии в вашем вебсайте
 * организовать роли в иерархию внутри базы данных
 * управлять ролями и привилегиями в пользовательском интерфейсе
 * использовать компонент @`Laminas\Permissions\Rbac`, чтобы реализовать контроль доступа на основе ролей
 * использовать динамические утверждения (dynamic assertions), чтобы реализовать сложные правила доступа

Пример *Role Demo* будет базироваться на примере *User Demo*, созданном в предыдущей главе.

Чтобы загрузить пример *Role Demo*, посетите [эту страницу](https://github.com/olegkrivtsov/using-laminas-book-samples)
и щелкните кнопку *Clone or Download*, чтобы загрузить код ZIP-архивом.
Когда загрузка закончится, распакуйте код в какую либо директорию.

Затем перейдите в директорию `roledemo`, содержащую код приложения *Role Demo*:

~~~text
/using-laminas-book-samples
  /roledemo
  ...
~~~

*Role Demo* - это вебсайт, который может быть установлен на вашу машину.

I> Детальные инструкции о том как установить пример *Role Demo* могут быть найдены в файле *README.md*, расположенном в директории примера.

## Введение в RBAC

Laminas предоставляет специальный компонент @`Laminas\Permissions\Rbac`, реализующий контейнер для ролей и привилегий.

Для установки компонента @`Laminas\Permissions\Rbac` в ваше веб-приложение, воспользуйтесь следующей командой:

```
php composer.phar require laminas/laminas-permissions-rbac
```

### Роли и привилегии

*Роль* – это группа пользователей. Например, в приложении Blog могут быть следующие роли:
Viewer (читатель), Author (автор), Editor (редактор) и Administrator (администратор).

{title="Таблица 17.1. Примеры ролей на сайте Blog"}
|--------------------------------|---------------------------------------------------------------|
| *Имя роли*                     | *Описание*                                                    |
|--------------------------------|---------------------------------------------------------------|
| `Viewer`                       | Может читать любой пост.                                      |
|--------------------------------|---------------------------------------------------------------|
| `Author`                       | Может просматривать посты, а также создавать их, редактировать и опубликовывать. |
|--------------------------------|---------------------------------------------------------------|
| `Editor`                       | Может просматривать посты, а также редактировать и опубликовывать их. |
|--------------------------------|---------------------------------------------------------------|
| `Administrator`                | Может делать все то же, что могут Viewer и Editor, а также удалять посты. |
|--------------------------------|---------------------------------------------------------------|

*Пользователю* может быть присвоена одна или несколько ролей. Например, пользователь Иван может быть
одновременно и читателем и редактором.

Роль может *наследовать* привилегии от других ролей. Другими словами, роли можно организовать в иерархию, в
которой родительские роли наследуют привилегии дочерних. Например, в нашем приложении Blog роль Administrator будет
наследовать привилегии от роли Editor (см. рисунок 17.1 ниже). Это обусловлено тем, что администратор может делать
все то же, что и редактор, плюс удалять посты. Роли Editor и Author будут наследовать привилегии от роли Viewer.

![Рисунок 17.1 Иерархия ролей на сайте Blog](../en/images/roles/role_hierarchy_in_a_blog_app.png)

Роли могут быть присвоены несколько *привилегий*. Привилегия – это типовое действие в системе. Ниже приведены
несколько примеров привилегий на сайте Blog:

{title="Таблица 17.2. Примеры привилегии на сайте Blog"}
|--------------------------------|---------------------------------------------------------------|
| *Имя привилегии*               | *Описание*                                                    |
|--------------------------------|---------------------------------------------------------------|
| `post.view`                    | Просматривать любой пост.                                     |
|--------------------------------|---------------------------------------------------------------|
| `post.edit`                    | Редактировать любой пост.                                     |
|--------------------------------|---------------------------------------------------------------|
| `post.own.edit`                | Редактировать только собственные посты.                       |
|--------------------------------|---------------------------------------------------------------|
| `post.publish`                 | Опубликовывать любой пост.                                    |
|--------------------------------|---------------------------------------------------------------|
| `post.own.publish`             | Опубликовывать только собственные посты.                      |
|--------------------------------|---------------------------------------------------------------|
| `post.delete`                  | Удалять любой пост.                                           |
|--------------------------------|---------------------------------------------------------------|

Например, роли Viewer будет присвоена привилегия `post.view`. Роли Editor будут присвоены привилегии
`post.edit` и `post.publish`. Роли Author будут присвоены привилегии `post.own.edit` и `post.own.publish`.
Наконец, роли Administrator будет присвоена привилегия `post.delete`.

### Контейнер RBAC

В Laminas в качестве контейнера для ролей и привилегий можно использовать класс @`Rbac` из пространства имен @`Laminas\Permissions\Rbac`.
С помощью этого контейнера вы можете хранить в памяти роли, организованные в иерархию, и с присвоенными им привилегиями.

В качестве примера, создадим контейнер @`Rbac` для приложения Blog и заполним его ролями и привилегиями:

~~~php
use Laminas\Permissions\Rbac\Rbac;

// Создаем новый контейнер Rbac.
$rbac = new Rbac();

// Сообщаем Rbac, что нужно создать родительские роли, если их еще нет
$rbac->setCreateMissingRoles(true);

// Создаем иерархию ролей
$rbac->addRole('Viewer', ['Editor', 'Author']);
$rbac->addRole('Editor', ['Administrator']);
$rbac->addRole('Author');
$rbac->addRole('Administrator');

// Присваиваем привилегии роли Viewer.
$rbac->getRole('Viewer')->addPermission('post.view');

// Присваиваем привилегии роли Author.
$rbac->getRole('Author')->addPermission('post.own.edit');
$rbac->getRole('Author')->addPermission('post.own.publish');

// Присваиваем привилегии роли Editor.
$rbac->getRole('Editor')->addPermission('post.edit');
$rbac->getRole('Editor')->addPermission('post.publish');

// Присваиваем привилегии роли Administrator.
$rbac->getRole('Administrator')->addPermission('post.delete');
~~~

Как видите, роль добавляется в контейнер @`Rbac` с помощью метода `addRole()`. Этот метод
принимает два аргумента: имя роли, которую мы хотим создать, и имя (или имена) ее родительской
роли (или родительских ролей). Если родительских ролей еще не существует, они создаются
автоматически (для этой цели используется метод `setCreateMissingRoles()`).

Привилегии присваиваются созданной роли с помощью метода роли `addPermission()`.

### Проверка привилегий

После того, как вы настроите контейнер, вы можете запросить информацию о том, имеет ли
роль определенную привилегию. Сделать это можно с помощью метода `isGranted()`, как показано ниже:

~~~php
// Метод ниже вернет false, так как читатель не может удалять посты
$rbac->isGranted('Viewer', 'post.delete');

// Метод ниже вернет true, потому что администраторы могут удалять посты
$rbac->isGranted('Administrator', 'post.delete');
~~~

Метод `isGranted()` проверяет роль и ее дочерние роли и ищет заданную привилегию. Если эту привилегию
удается найти, метод возвращает `true`, иначе – `false`.


## Роли по умолчанию в примере Role Demo

Так как наше приложение Role Demo задумано с целью быть основой для ваших собственных, более сложных
вебсайтов, в примере Role Demo у нас будут только простые роли по умолчанию: Administrator и Guest.

I> Вы сможете добавлять больше ролей через пользовательский интерфейс вебсайта.

У нас будут следующие стандартные привилегии:

{title="Таблица 17.3. Привилегии по умолчанию для вебсайта Role Demo"}
|--------------------------------|---------------------------------------------------------------|
| *Имя привилегии*               | *Описание*                                                    |
|--------------------------------|---------------------------------------------------------------|
| `user.manage`                  | Управлять пользователями (добавлять/изменять/удалять).        |
|--------------------------------|---------------------------------------------------------------|
| `role.manage`                  | Управлять ролями (добавлять/изменять/удалять).                |
|--------------------------------|---------------------------------------------------------------|
| `permission.manage`            | Управлять привилегиями (добавлять/изменять/удалять).          |
|--------------------------------|---------------------------------------------------------------|
| `profile.any.view`             | Просматривать профиль любого пользователя в системе.          |
|--------------------------------|---------------------------------------------------------------|
| `profile.own.view`             | Просматривать собственный профиль.                            |
|--------------------------------|---------------------------------------------------------------|

Три первые привилегии позволят администратору управлять пользователями, ролями и привилегиями через пользовательский интерфейс.

Две последние привилегии (`profile.any.view` и `profile.own.view`) по большей части для демонстрации того,
как работает Rbac, и в теории могут быть удалены с вашего собственного вебсайта, если у вас отсутствуют страницы профилей пользователей.

Привилегия `profile.any.view` предоставляет администратору доступ к странице *http://localhost/application/settings/&lt;user_id&gt;*,
являющейся профилем пользователя с заданным ID.

И наконец, привилегия `profile.own.view` предоставляет гостям доступ к их собственным страницам пользователя *http://localhost/application/settings*.

I> Вы сможете создавать больше привилегий через пользовательский интерфейс вебсайта.

## Введение в динамические утверждения

Вы могли заметить, что для примера Blog у нас было две "особенных" привилегии – `post.own.edit` and `post.own.publish`.
Они являются особенными, потому что позволяют автору редактировать *только* те посты, что были созданы непосредственно им.

Чтобы "проверить", соответствует ли на самом деле такая роль пользователю, нам дополнительно нужно определить, действительно
ли пост принадлежит этому пользователю. Это называется *динамическим утверждением*.

Для вебсайта Role Demo у нас тоже будет особенная привилегия – `profile.own.view`. Ее особенность заключается в том, что
она позволяет пользователю просматривать профиль, чьим владельцем является этот пользователь.

Для реализации динамических утверждений в примере *Role Demo* мы будем использовать специальный сервис, называющийся *менеджер утверждений*.
Он будет реализован как класс `RbacAssertionManager`, "живущий" в пространстве имен `Application\Service`, и будет выглядеть следующим образом:

~~~php
<?php
namespace Application\Service;

use Laminas\Permissions\Rbac\Rbac;
use User\Entity\User;

/**
 * Этот сервис используется для вызова определяемых пользователем динамических утверждений RBAC.
 */
class RbacAssertionManager
{
    /**
     * Менеджер сущностей.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * Служба аутентификации.
     * @var Laminas\Authentication\AuthenticationService
     */
    private $authService;

    /**
     * Конструирует сервис.
     */
    public function __construct($entityManager, $authService)
    {
        $this->entityManager = $entityManager;
        $this->authService = $authService;
    }

    /**
     * Этот метод используется для динамичечких утверждений.
     */
    public function assert(Rbac $rbac, $permission, $params)
    {
        $currentUser = $this->entityManager->getRepository(User::class)
                ->findOneByEmail($this->authService->getIdentity());

        if ($permission=='profile.own.view' && $params['user']->getId()==$currentUser->getId())
            return true;

        return false;
    }
}
~~~

Как можете видеть из фрагмента кода сверху, у данного класса есть метод `assert()`, принимающий три аргумента:

  * `$rbac` – контейнер для наших ролей и привилегий;
  * `$permission` – имя привилегии, которую нужно проверить;
  * `$params` – массив параметров (он может быть использован, например, для передачи пользователю владения постом в блоге).


В методе `assert()` мы можем получить информацию о залогиненном в текущий момент пользователе и сравнить ее с информацией
о совершающем переход пользователе. Таким образом, мы сможем вернуть `true`, если пользователь пытается открыть свой собственный профиль,
и `false` в противном случае.

T> Теоретически, на вашем сайте у вас может быть много менеджеров утверждений (например, если у вашего модуля Blog
T> есть какие-либо динамические утверждения, вы можете создать и зарегистрировать менеджер утверждений для этого модуля.

## Создание базы данных

В нашем примере Role Demo мы будем хранить ирархию ролей в базе данных "roledemo".
Мы создадим следующие таблицы (см. рисунок 17.2):

  * таблица `role` будет содержать данные, связанные с ролью (ее имя и описание)
  * таблица `role_hierarchy` будет содержать связи наследования между ролями
  * таблица `permission` будет содержать привилегии
  * таблица `role_permission` позволит присваивать привилегии ролям
  * таблица `user_role` позволит присваивать роли пользователям
  * таблица `user` будет содежать данные о пользователях (эту таблицу мы создали ранее в примере *User Demo*)


![Рисунок 17.2 Схема базы данных примера Role Demo](../en/images/roles/roledemo_db_schema.png)

Миграцию базы данных, создающую эти таблицы, вы можете найти в приложении *Role Demo*. Чтобы запустить миграции,
введите следующую команду:

~~~php
./vendor/bin/doctrine-module migrations:migrate
~~~

T> Если вы еще не знакомы с миграциями, обратитесь к главе [Миграции баз данных](#migrations).

## Создание сущностей

Пример *Role Demo* использует ORM Doctrine для управления базами данных. Мы уже научились пользоваться
Doctrine в [Управление базой данной с помощью ORM Doctrine](#doctrine).

Для хранения информации о ролях и привилегиях мы создадим сущности `Role` и `Permission`. Сущности `Role`
соответствует таблица БД `role`, а сущности `Permission` – таблица `permission`. Это типичные классы
сущностей Doctrine.

Создайте файл *Role.php* внутри каталога *Entity* под корневой директорией модуля *User*. Поместите в этот
файл следующий код:

~~~php
<?php
namespace User\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Этот класс представляет роль.
 * @ORM\Entity()
 * @ORM\Table(name="role")
 */
class Role
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(name="name")
     */
    protected $name;

    /**
     * @ORM\Column(name="description")
     */
    protected $description;

    /**
     * @ORM\Column(name="date_created")
     */
    protected $dateCreated;

    /**
     * @ORM\ManyToMany(targetEntity="User\Entity\Role")
     * @ORM\JoinTable(name="role_hierarchy",
     *      joinColumns={@ORM\JoinColumn(name="child_role_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="parent_role_id", referencedColumnName="id")}
     *      )
     */
    private $parentRoles;

    /**
     * @ORM\ManyToMany(targetEntity="User\Entity\Role")
     * @ORM\JoinTable(name="role_hierarchy",
     *      joinColumns={@ORM\JoinColumn(name="parent_role_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="child_role_id", referencedColumnName="id")}
     *      )
     */
    protected $childRoles;

    /**
     * @ORM\ManyToMany(targetEntity="User\Entity\Permission")
     * @ORM\JoinTable(name="role_permission",
     *      joinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="permission_id", referencedColumnName="id")}
     *      )
     */
    private $permissions;

    /**
     * Конструктор.
     */
    public function __construct()
    {
        $this->parentRoles = new ArrayCollection();
        $this->childRoles = new ArrayCollection();
        $this->permissions = new ArrayCollection();
    }

    /**
     * Возвращает ID роли.
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Задает ID роли.
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
    }

    public function getParentRoles()
    {
        return $this->parentRoles;
    }

    public function getChildRoles()
    {
        return $this->childRoles;
    }

    public function getPermissions()
    {
        return $this->permissions;
    }

    public function addParent(Role $role)
    {
        if ($this->getId() == $role->getId()) {
            return false;
        }
        if (!$this->hasParent($role)) {
            $this->parentRoles[] = $role;
            return true;
        }
        return false;
    }

    public function clearParentRoles()
    {
        $this->parentRoles = new ArrayCollection();
    }

    public function hasParent(Role $role)
    {
        if ($this->getParentRoles()->contains($role)) {
            return true;
        }
        return false;
    }
}
~~~

Как вы могли определить по приведенному выше коду, сущность *Role* – это типичная сущность Doctrine
с аннотированными свойствами и методами getter и setter для извлечения/задания этих свойств.

Далее, создайте файл *Permission.php* внутри каталога *Entity* под корневой директорией модуля *User*. Поместите в этот
файл следующий код:

~~~php
<?php
namespace User\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Этот класс представляет привилегию.
 * @ORM\Entity()
 * @ORM\Table(name="permission")
 */
class Permission
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(name="name")
     */
    protected $name;

    /**
     * @ORM\Column(name="description")
     */
    protected $description;

    /**
     * @ORM\Column(name="date_created")
     */
    protected $dateCreated;

    /**
     * @ORM\ManyToMany(targetEntity="User\Entity\Role")
     * @ORM\JoinTable(name="role_permission",
     *      joinColumns={@ORM\JoinColumn(name="permission_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     *      )
     */
    private $roles;

    /**
     * Конструктор.
     */
    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
    }

    public function getRoles()
    {
        return $this->roles;
    }
}
~~~

Наконец, мы немного расширим уже созданную в примере *User Demo* сущность `User` и добавим свойство и методы,
связанные с ролями:

~~~php
<?php
namespace User\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Этот класс представляет зарегистрированного пользователя.
 * @ORM\Entity()
 * @ORM\Table(name="user")
 */
class User
{
    // Константы статуса пользователя.
    const STATUS_ACTIVE       = 1; // Активный пользователь.
    const STATUS_RETIRED      = 2; // Неактивный пользователь.

    /**
     * @ORM\Id
     * @ORM\Column(name="id")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(name="email")
     */
    protected $email;

    /**
     * @ORM\Column(name="full_name")
     */
    protected $fullName;

    /**
     * @ORM\Column(name="password")
     */
    protected $password;

    /**
     * @ORM\Column(name="status")
     */
    protected $status;

    /**
     * @ORM\Column(name="date_created")
     */
    protected $dateCreated;

    /**
     * @ORM\Column(name="pwd_reset_token")
     */
    protected $passwordResetToken;

    /**
     * @ORM\Column(name="pwd_reset_token_creation_date")
     */
    protected $passwordResetTokenCreationDate;

    /**
     * @ORM\ManyToMany(targetEntity="User\Entity\Role")
     * @ORM\JoinTable(name="user_role",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     *      )
     */
    private $roles;

    /**
     * Конструктор.
     */
    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    /**
     * Возвращает ID пользователя.
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Задает ID пользователя.
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Возвращает адрес эл. почты.
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Задает адрес эл. почты.
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Возвращает полное имя.
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * Задает полное имя.
     * @param string $fullName
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;
    }

    /**
     * Возвращает статус.
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Возвращает возможные статусы в виде массива.
     * @return array
     */
    public static function getStatusList()
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_RETIRED => 'Retired'
        ];
    }

    /**
     * Возвращает статус пользователя в виде строки.
     * @return string
     */
    public function getStatusAsString()
    {
        $list = self::getStatusList();
        if (isset($list[$this->status]))
            return $list[$this->status];

        return 'Unknown';
    }

    /**
     * Задает статус.
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Возвращает пароль.
     * @return string
     */
    public function getPassword()
    {
       return $this->password;
    }

    /**
     * Задает пароль.
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Возвращает дату создания пользователя.
     * @return string
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Задает дату, когда был создан этот пользователь.
     * @param string $dateCreated
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
    }

    /**
     * Возвращает токен сброса пароля.
     * @return string
     */
    public function getResetPasswordToken()
    {
        return $this->passwordResetToken;
    }

    /**
     * Задает токен сброса пароля.
     * @param string $token
     */
    public function setPasswordResetToken($token)
    {
        $this->passwordResetToken = $token;
    }

    /**
     * Возвращает дату создания токена сброса пароля.
     * @return string
     */
    public function getPasswordResetTokenCreationDate()
    {
        return $this->passwordResetTokenCreationDate;
    }

    /**
     * Задает дату создания токена сброса пароля.
     * @param string $date
     */
    public function setPasswordResetTokenCreationDate($date)
    {
        $this->passwordResetTokenCreationDate = $date;
    }

    /**
     * Возвращает массив ролей, присвоенных этому пользователю.
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Возвращает строку имен присвоенных ролей.
     */
    public function getRolesAsString()
    {
        $roleList = '';

        $count = count($this->roles);
        $i = 0;
        foreach ($this->roles as $role) {
            $roleList .= $role->getName();
            if ($i<$count-1)
                $roleList .= ', ';
            $i++;
        }

        return $roleList;
    }

    /**
     * Присваивает пользователю роль.
     */
    public function addRole($role)
    {
        $this->roles->add($role);
    }
}
~~~

## Управление ролями

В примере Role Demo мы создадим удобный пользовательский интерфейс для управления ролями.

T> Вы можете получить доступ к странице управления ролями, войдя как `admin@example.com` и открыв
T> меню Admin -> Manage Roles.

Управление ролями будет реализовано внутри контроллера `RoleController`, находящегося в пространстве
имен `User\Controller`. Методы действия `RoleController` перечислены в таблице 17.4:

{title="Таблица 17.4. Действия контроллера RoleController"}
|--------------------------------|---------------------------------------------------------------|
| *Имя действия*                 | *Описание*                                                    |
|--------------------------------|---------------------------------------------------------------|
| `addAction()`                  | Позволяет добавить новую роль.                                |
|--------------------------------|---------------------------------------------------------------|
| `deleteAction()`               | Удаляет существующую роль.                                    |
|--------------------------------|---------------------------------------------------------------|
| `editAction()`                 | Позволяет редактировать существующую роль.                    |
|--------------------------------|---------------------------------------------------------------|
| `editPermissionsAction()`      | Позволяет присвоить роли привилегии.                          |
|--------------------------------|---------------------------------------------------------------|
| `indexAction()`                | Отображает список существующих ролей.                         |
|--------------------------------|---------------------------------------------------------------|
| `viewAction()`                 | Отображает детали роли.                                       |
|--------------------------------|---------------------------------------------------------------|


`RoleController` работает в паре с `RoleManager`, находящемся в пространстве имен `User\Service`.

В `RoleController` и `RoleManager` нет ничего нового и ничего особенного, так что здесь мы не будем
их обсуждать, а просто приведем несколько скриншотов получившегося пользовательского интерфейса.

T> Полный код классов `RoleController` и `RoleManager` вы можете найти в примере Role Demo.

![Рисунок 17.3 Список ролей](../en/images/roles/roledemo_roles_index.png)

![Рисунок 17.4 Создать новую роль](../en/images/roles/roledemo_roles_add.png)

![Рисунок 17.5 Посмотреть детали роли](../en/images/roles/roledemo_roles_view.png)

![Рисунок 17.6 Изменить существующую роль](../en/images/roles/roledemo_roles_edit.png)

![Рисунок 17.7 Присвоить роли привилегии](../en/images/roles/roledemo_roles_edit_permissions.png)

## Управление привилегиями

В примере Role Demo мы создадим удобный пользовательский интерфейс для управления привилегиями.
Он будет полезен, если вы планируете добавлять новые привилегии или удалять существующие.

T> Вы можете получить доступ к странице управления привилегиями, войдя как `admin@example.com` и открыв
T> меню Admin -> Manage Permissions.

Управление привилегиями будет реализовано внутри контроллера `PermissionController`, находящегося в пространстве
имен `User\Controller`. Методы действия `PermissionController` перечислены в таблице 17.5:

{title="Таблица 17.5. Действия контроллера PermissionController"}
|--------------------------------|---------------------------------------------------------------|
| *Имя действия*                 | *Описание*                                                    |
|--------------------------------|---------------------------------------------------------------|
| `addAction()`                  | Позволяет добавить новую привилегию.                          |
|--------------------------------|---------------------------------------------------------------|
| `deleteAction()`               | Удаляет существующую привилегию.                              |
|--------------------------------|---------------------------------------------------------------|
| `editAction()`                 | Позволяет редактировать существующую привилегию.              |
|--------------------------------|---------------------------------------------------------------|
| `indexAction()`                | Отображает список существующих привилегий.                    |
|--------------------------------|---------------------------------------------------------------|
| `viewAction()`                 | Отображает детали привилегии.                                 |
|--------------------------------|---------------------------------------------------------------|


`PermissionController` работает в паре с `PermissionManager`, находящемся в пространстве имен `User\Service`.

В `PermissionController` и `PermissionManager` нет ничего нового и ничего особенного, так что здесь мы не будем
их обсуждать, а просто приведем несколько скриншотов получившегося пользовательского интерфейса.

T> Полный код классов `PermissionController` и `PermissionManager` вы можете найти в примере Role Demo.

![Рисунок 17.8 Список привилегий](../en/images/roles/roledemo_permissions_index.png)

![Рисунок 17.9 Создать новую пивилегию](../en/images/roles/roledemo_permissions_add.png)

![Рисунок 17.10 Посмотреть детали привилегии](../en/images/roles/roledemo_permissions_view.png)

![Рисунок 17.11 Изменить существующую привилегию](../en/images/roles/roledemo_permissions_edit.png)

## Назначение ролей пользователю

На сайте Role Demo вы можете присвоить роли пользователю через удобный пользовательский интерфейс.
Откройте меню *Admin -> Manage Users* и нажмите *Edit* на выбранном вами пользователе. На появившейся
странице выберите роли, которые вы хотите присвоить этому пользователю и нажмите кнопку *Save*.

В этом функционале нет ничего нового и ничего особенного, так что здесь мы не будем
его обсуждать, а просто приведем скриншот получившегося пользовательского интерфейса.

![Рисунок 17.12 Присвоить роли пользователю](../en/images/roles/roledemo_users_edit.png)

## Реализация RbacManager

Следующее, что мы обсудим, это набор функций для создания контейнера `Rbac`, целью которого
является загрузка иерархии ролей из базы данных и кэширование данных в кэше файловой системы.

I> *Кэш* позволяет хранить часто используемые данные в быстродействующей памяти. Например, извлечение
I> ролей и привилегий из базы данных может быть довольно медленным, в то время как хранение
I> заранее вычисленной иерархии ролей может быть быстрее.

### Настройка кеширования

Для начала давайте настроим кэширование. Для этого вам нужно установить компоненты Laminas\Cache` и `Laminas\Serializer` с
помощью следующих команд:

~~~
php composer.phar require laminas/laminas-cache
php composer.phar require laminas/laminas-serializer
~~~

Затем измените файл *config/autoload/global.php*, добавив следующие строки:

~~~php
use Laminas\Cache\Storage\Adapter\Filesystem;

return [
    //...
    // Настройка кэша.
    'caches' => [
        'FilesystemCache' => [
            'adapter' => [
                'name'    => Filesystem::class,
                'options' => [
                    // Store cached data in this directory.
                    'cache_dir' => './data/cache',
                    // Store cached data for 1 hour.
                    'ttl' => 60*60*1
                ],
            ],
            'plugins' => [
                [
                    'name' => 'serializer',
                    'options' => [
                    ],
                ],
            ],
        ],
    ],
    //...
];
~~~

Это позволит вам использовать кэш `Filesystem` и хранить кэшированные
данные в каталоге *APP_DIR/data/cache*.

T> Если вы хотите больше узнать о кэшировании, пожалуйста, обратитесь к документации компонента
T> Laminas @`Laminas\Cache`.

### Пишем сервис RbacManager

Функциональным назначением сервиса `RbacManager` будет создание контейнера @`Rbac` и загрузка
ролей и привилегий из базы данных. Если нужная информация уже была сохранена в кэш, сервис
загрузит ее оттуда, а не из БД.

Другими задачами сервиса `RbacManager` будут использование менеджера утверждений, написанного нами ранее,
и проверка на динамические утверждения.

Класс `RbacManager` будет иметь два метода:

  * метод `init()` будет использоваться для загрузки иерархии ролей из базы данных и ее сохранения в кэш;
  * метод `isGranted()` будет использоваться для запроса у контейнера @`Rbac` информации о том, есть ли
	у заданного *пользователя* заданная привилегия (и проверки менеджера(-ов) утверждений на динамические утверждения).

Класс `RbacManager` будет считывать конфигурацию и искать ключ `rbac_manager`.
Этот ключ должен содержать подключ `assertions`, в котором вы можете зарегистрировать все имеющиеся у вас менеджеры утверждений.

~~~php
return [
    //...

    // Этот ключ хранит конфигурацию для менеджера RBAC.
    'rbac_manager' => [
        'assertions' => [Service\RbacAssertionManager::class],
    ],
];
~~~

Код класса `RbacManager`, "живущего" в пространстве имен `User\Service`, представлен ниже.

~~~php
<?php
namespace User\Service;

use Laminas\Permissions\Rbac\Rbac;
use Laminas\Permissions\Rbac\Role as RbacRole;
use User\Entity\User;
use User\Entity\Role;
use User\Entity\Permission;

/**
 * Этот сервис отвечает за инициализацию RBAC (Role-Based Access Control – контроль доступа на основе ролей).
 */
class RbacManager
{
    /**
     * Менеджер сущностей Doctrine.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * Сервис RBAC.
     * @var Laminas\Permissions\Rbac\Rbac
     */
    private $rbac;

    /**
     * Сервис аутентификации.
     * @var Laminas\Authentication\AuthenticationService
     */
    private $authService;

    /**
     * Кэш файловой системы.
     * @var Laminas\Cache\Storage\StorageInterface
     */
    private $cache;

    /**
     * Менеджеры утверждений.
     * @var array
     */
    private $assertionManagers = [];

    /**
     * Конструирует сервис.
     */
    public function __construct($entityManager, $authService, $cache, $assertionManagers)
    {
        $this->entityManager = $entityManager;
        $this->authService = $authService;
        $this->cache = $cache;
        $this->assertionManagers = $assertionManagers;
    }

    /**
     * Инициализирует контейнер RBAC.
     */
    public function init($forceCreate = false)
    {
        if ($this->rbac!=null && !$forceCreate) {
            // Уже инициализирован; ничего не делаем.
            return;
        }

        // Если пользователь хочет, чтобы мы заново инициализировали контейнер RBAC, очищаем кэш.
        if ($forceCreate) {
            $this->cache->removeItem('rbac_container');
        }

        // Пробуем загрузить контейнер Rbac из кэша.
        $this->rbac = $this->cache->getItem('rbac_container', $result);
        if (!$result)
        {
            // Создаем контейнер Rbac.
            $rbac = new Rbac();
            $this->rbac = $rbac;

            // Конструируем иерархию ролей, загружая роли и привилегии из базы данных.

            $rbac->setCreateMissingRoles(true);

            $roles = $this->entityManager->getRepository(Role::class)
                    ->findBy([], ['id'=>'ASC']);
            foreach ($roles as $role) {

                $roleName = $role->getName();

                $parentRoleNames = [];
                foreach ($role->getParentRoles() as $parentRole) {
                    $parentRoleNames[] = $parentRole->getName();
                }

                $rbac->addRole($roleName, $parentRoleNames);

                foreach ($role->getPermissions() as $permission) {
                    $rbac->getRole($roleName)->addPermission($permission->getName());
                }
            }

            // Сохраняем контейнер Rbac в кэш.
            $this->cache->setItem('rbac_container', $rbac);
        }
    }

    /**
     * Проверяет, есть ли привилегия у данного пользователя.
     * @param User|null $user
     * @param string $permission
     * @param array|null $params
     */
    public function isGranted($user, $permission, $params = null)
    {
        if ($this->rbac==null) {
            $this->init();
        }

        if ($user==null) {

            $identity = $this->authService->getIdentity();
            if ($identity==null) {
                return false;
            }

            $user = $this->entityManager->getRepository(User::class)
                    ->findOneByEmail($identity);
            if ($user==null) {
                // Упс... Эта личность есть в сессии, но в базе данных такого пользователя не существует.
                // Мы генерируем исключение, так как, возможно, это является проблемой безопасности.
                throw new \Exception('There is no user with such identity');
            }
        }

        $roles = $user->getRoles();

        foreach ($roles as $role) {
            if ($this->rbac->isGranted($role->getName(), $permission)) {

                if ($params==null)
                    return true;

                foreach ($this->assertionManagers as $assertionManager) {
                    if ($assertionManager->assert($this->rbac, $permission, $params))
                        return true;
                }
            }

            $parentRoles = $role->getParentRoles();
            foreach ($parentRoles as $parentRole) {
                if ($this->rbac->isGranted($parentRole->getName(), $permission)) {
                    return true;
                }
            }
        }

        return false;
    }
}
~~~

Фабрика для класса `RbacManager` будет выглядеть следующим образом:

~~~php
<?php
namespace User\Service\Factory;

use Interop\Container\ContainerInterface;
use User\Service\RbacManager;
use Laminas\Authentication\AuthenticationService;

/**
 * Это фабричный класс для сервиса RbacManager. Целями данной фабрики являются
 * инстанцирование сервиса и передача ему зависимостей (внедрение зависимостей).
 */
class RbacManagerFactory
{
    /**
     * Этот метод создает сервис RbacManager и возвращает его экземпляр.
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $authService = $container->get(\Laminas\Authentication\AuthenticationService::class);
        $cache = $container->get('FilesystemCache');

        $assertionManagers = [];
        $config = $container->get('Config');
        if (isset($config['rbac_manager']['assertions'])) {
            foreach ($config['rbac_manager']['assertions'] as $serviceName) {
                $assertionManagers[$serviceName] = $container->get($serviceName);
            }
        }

        return new RbacManager($entityManager, $authService, $cache, $assertionManagers);
    }
}
~~~

## Добавление страницы Not Authorized

Теперь создадим страницу *Not Authorized* (см. рисунок 17.13), на которую мы будем перенаправлять пользователей, если
они пытаются перейти на веб-страницу, к которой у них нет доступа.

![Рисунок 17.13 Страница Not Authorized](../en/images/roles/roledemo_notauthorized_page.png)

Добавьте следующий маршрут в файл *module.config.php* модуля *User*:

~~~
return [
    'router' => [
        'routes' => [
            'not-authorized' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/not-authorized',
                    'defaults' => [
                        'controller' => Controller\AuthController::class,
                        'action'     => 'notAuthorized',
                    ],
                ],
            ],
        ],
    ],
];
~~~

Затем добавьте метод `notAuthorizedAction()` в `AuthController` в модуле `User`:

~~~
/**
 * Отображает страницу "Not Authorized".
 */
public function notAuthorizedAction()
{
    $this->getResponse()->setStatusCode(403);

    return new ViewModel();
}
~~~

Наконец, добавьте шаблон представления *not-authorized.phtml* под каталог *user/auth* под директорией *view* модуля
*User*:

~~~
<?php
$this->headTitle("Not Authorized");
?>

<h1>Not Authorized</h1>

<div class="alert alert-warning">Sorry, you have no permission to see this page.</div>
~~~

Теперь, если вы введете следующий URL в адресную строку своего браузера, вы увидите страницу *Not Authorized*:
"http://localhost/not-authorized".

## Модифицируем сервис AuthManager

Следующее, что мы сделаем, – модифицируем фильтр доступа в сервисе `AuthManager`, который мы
написали для примера *User Demo*. В частности, мы хотим изменить метод `filterAccess()`.
Мы хотим, чтобы этот метод использовал наш класс `RbacManager`.

Но сначала изменим формат ключа `access_filter` в конфигурации. Мы хотим, чтобы этот ключ позволял
разрешать доступ:

 * всем, если мы поставим звездочку (`*`);
 * любому аутентифицированному пользователю, если мы поставим коммерческое at (`@`);
 * конкретному аутентифицированному пользователю с заданным адресом эл. почты `личности`, если мы поставим (`@identity`)
 * любому аутентифицированному пользователю с заданной `привилегией`, если мы поставим знак плюса и имя привилегии (`+permission`).

Вот так, например, будет выглядеть ключ `access_filter` для модуля *User*:

~~~php
<?php
return [
    //...

    // Ключ 'access_filter' используется модулем User для запрета или разрешения
    // доступа к определенным действиям контроллера для неавторизованных посетителей.
    'access_filter' => [
        'controllers' => [
            Controller\UserController::class => [
                // Дать доступ к действиям "resetPassword", "message" и "setPassword" всем.
                ['actions' => ['resetPassword', 'message', 'setPassword'], 'allow' => '*'],
                // Дать доступ к действиям "index", "add", "edit", "view", "changePassword"
                // пользователям с привилегией "user.manage".
                ['actions' => ['index', 'add', 'edit', 'view', 'changePassword'],
                 'allow' => '+user.manage']
            ],
            Controller\RoleController::class => [
                // Разрешить доступ аутентифицированным пользователям с привилегией "role.manage".
                ['actions' => '*', 'allow' => '+role.manage']
            ],
            Controller\PermissionController::class => [
                // Разрешить доступ аутентифицированным пользователям с привилегией "permission.manage".
                ['actions' => '*', 'allow' => '+permission.manage']
            ],
        ]
    ],

    //...
];
~~~

Ниже вы можете найти исходный код метода `filterAccess()` сервиса `AuthManager`:

~~~php
/**
 * Это простой фильтр контроля доступа. Он может ограничивать доступ к определенным страницам
 * для неавторизованных пользователей.
 *
 * Данный метод использует ключ в файле конфигурации и определяет, разрешен ли
 * текущему посетителю доступ к заданному действию контроллера или нет. Если разрешен,
 * он возвращает true, в противном случае – false.
 */
public function filterAccess($controllerName, $actionName)
{
    // Определяем режим – 'ограничительный' (по умолчанию) или 'разрешительный'. В ограничительном
    // режиме все действия контроллера должны быть явно перечислены под ключом конфигурации 'access_filter',
    // и доступ к любому не указанному действию для неавторизованных пользователей запрещен.
    // В разрешительном режиме, если действие не указано под ключом 'access_filter', доступ к нему
    // разрешен для всех (даже для незалогиненных пользователей).
    // Ограничительный режим является более безопасным, и рекомендуется использовать его.
    $mode = isset($this->config['options']['mode'])?$this->config['options']['mode']:'restrictive';
    if ($mode!='restrictive' && $mode!='permissive')
        throw new \Exception('Invalid access filter mode (expected either restrictive or permissive mode');

    if (isset($this->config['controllers'][$controllerName])) {
        $items = $this->config['controllers'][$controllerName];
        foreach ($items as $item) {
            $actionList = $item['actions'];
            $allow = $item['allow'];
            if (is_array($actionList) && in_array($actionName, $actionList) ||
                $actionList=='*') {
                if ($allow=='*')
                    // Все могут просматривать эту страницу.
                    return self::ACCESS_GRANTED;
                else if (!$this->authService->hasIdentity()) {
                    // Только аутентифицированные пользователи могут просматривать страницу.
                    return self::AUTH_REQUIRED;
                }

                if ($allow=='@') {
                    // Любой аутентифицированный пользователь может просматривать страницу.
                    return self::ACCESS_GRANTED;
                } else if (substr($allow, 0, 1)=='@') {
                    // Только пользователи с определенной привилегией могут просматривать страницу.
                    $identity = substr($allow, 1);
                    if ($this->authService->getIdentity()==$identity)
                        return self::ACCESS_GRANTED;
                    else
                        return self::ACCESS_DENIED;
                } else if (substr($allow, 0, 1)=='+') {
                    // Только пользователи с этой привилегией могут просматривать страницу.
                    $permission = substr($allow, 1);
                    if ($this->rbacManager->isGranted(null, $permission))
                        return self::ACCESS_GRANTED;
                    else
                        return self::ACCESS_DENIED;
                } else {
                    throw new \Exception('Unexpected value for "allow" - expected ' .
                            'either "?", "@", "@identity" or "+permission"');
                }
            }
        }
    }

    // В ограничительном режиме мы требуем аутентификации для любого действия, не
    // перечисленного под ключом 'access_filter' и отказываем в доступе авторизованным пользователям
    // (из соображений безопасности).
    if ($mode=='restrictive') {
        if(!$this->authService->hasIdentity())
            return self::AUTH_REQUIRED;
        else
            return self::ACCESS_DENIED;
    }

    // Разрешить доступ к этой странице.
    return self::ACCESS_GRANTED;
}
~~~

Как вы могли видеть из кода выше, метод возвращает одну из трех констант:

  * `ACCESS_GRANTED`, если пользователю разрешено просматривать данную страницу;
  * `AUTH_REQUIRED`, если пользователю сперва нужно пройти аутентификацию;
  * `ACCESS_DENIED`, если пользователю запрещено просматривать данную страницу.

## Модифицируем обработчик события Dispatch

Далее мы модифицируем класс `Module`, находящийся в пространстве имен `User` и
в частности его метод `onDispatch()`. Главной целью этого является сделать так, что, если
фильтр доступа возвращает `ACCESS_DENIED`, мы перенаправляем пользователя на страницу *Not Authorized*.

~~~php
<?php
namespace User;

use Laminas\Mvc\MvcEvent;
use Laminas\Mvc\Controller\AbstractActionController;
use User\Controller\AuthController;
use User\Service\AuthManager;

class Module
{
    //...

    /**
     * Метод обработчика событий для события 'Dispatch'. Мы обрабатываем событие Dispatch
     * и вызываем фильтр доступа. Фильтр доступа позволяет определить, разрешено ли текущему
     * посетителю просматривать страницу или нет. Если он не авторизован и доступ к странице
     * для него запрещен, мы перенаправляем такого пользователя на страницу входа на сайт.
     */
    public function onDispatch(MvcEvent $event)
    {
        // Получаем контроллер и действие, к которому был отправлен HTTP-запрос.
        $controller = $event->getTarget();
        $controllerName = $event->getRouteMatch()->getParam('controller', null);
        $actionName = $event->getRouteMatch()->getParam('action', null);

        // Конвертируем написанное через дефис имя действия в верблюжий регистр.
        $actionName = str_replace('-', '', lcfirst(ucwords($actionName, '-')));

        // Получаем экземпляр сервиса AuthManager.
        $authManager = $event->getApplication()->getServiceManager()->get(AuthManager::class);

        // Применяем фильтр доступа к каждому контроллеру кроме AuthController
        // (во избежание бесконечного перенаправления).
        if ($controllerName!=AuthController::class)
        {
            $result = $authManager->filterAccess($controllerName, $actionName);

            if ($result==AuthManager::AUTH_REQUIRED) {
                // Запоминаем URL страницы, на которую пытался перейти пользователь. Мы
                // перенаправим пользователя на этот URL после его успешного входа на сайт.
                $uri = $event->getApplication()->getRequest()->getUri();
                // Делаем URL-адрес относительным (убираем схему, сведения о пользователе, имя хоста и порт),
                // чтобы избежать перенаправления на другой домен злоумышленниками.
                $uri->setScheme(null)
                    ->setHost(null)
                    ->setPort(null)
                    ->setUserInfo(null);
                $redirectUrl = $uri->toString();

                // Перенаправляем пользователя на страницу "Login".
                return $controller->redirect()->toRoute('login', [],
                        ['query'=>['redirectUrl'=>$redirectUrl]]);
            }
            else if ($result==AuthManager::ACCESS_DENIED) {
                // Перенаправляем пользователя на страницу "Not Authorized".
                return $controller->redirect()->toRoute('not-authorized');
            }
        }
    }
}
~~~

## Добавляем плагин контроллера Access и помощник представления

Для обращения к `RbacManager` внутри контроллеров и шаблонов представлений, нам нужно создать
специальный плагин контроллера (который мы назовем `Access`) и специальный помощник представления
(который мы тоже назовем `Access`).

### Плагин контроллера Access

Иногда требуется проверить какую-либо привилегию внутри контроллера. Например, это понадобится для
привилегии `profile.own.view`, которая использует динамические утверждения. Для этой цели мы создадим
плагин контроллера Access.

Код данного плагина будет находиться внутри файла `AccessPlugin.php` в каталоге *Controller/Plugin* корневой
директории модуля *User*:

~~~php
<?php
namespace User\Controller\Plugin;

use Laminas\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Этот плагин контроллера используется для контроля доступа на основе ролей (RBAC).
 */
class AccessPlugin extends AbstractPlugin
{
    private $rbacManager;

    public function __construct($rbacManager)
    {
        $this->rbacManager = $rbacManager;
    }

    /**
     * Проверяет наличие заданной привилегии у залогиненного в текущий момент пользователя.
     * @param string $permission Имя привилегии.
     * @param array $params Опциональные параметры (используются только если привилегия связана с утверждением).
     */
    public function __invoke($permission, $params = [])
    {
        return $this->rbacManager->isGranted(null, $permission, $params);
    }
}
~~~

Фабрика плагина `Access` выглядит таким образом:

~~~php
<?php
namespace User\Controller\Plugin\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use User\Service\RbacManager;
use User\Controller\Plugin\AccessPlugin;

/**
 * Это фабрика для AccessPlugin. Ее целями являются инстанцирование плагина
 * и внедрение зависимостей в его конструктор.
 */
class AccessPluginFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $rbacManager = $container->get(RbacManager::class);

        return new AccessPlugin($rbacManager);
    }
}
~~~

Плагин регистрируется внутри файла *module.config.php* следующим образом:

~~~php
// Мы регистрируем предоставляемые модулем плагины контроллера под этим ключом.
'controller_plugins' => [
    'factories' => [
        Controller\Plugin\AccessPlugin::class => Controller\Plugin\Factory\AccessPluginFactory::class,
    ],
    'aliases' => [
        'access' => Controller\Plugin\AccessPlugin::class,
    ],
],
~~~

Таким образом, вы легко можете вызвать этот плагин в вашем действии контроллера:

~~~php
if (!$this->access('profile.own.view', ['user'=>$user])) {
    return $this->redirect()->toRoute('not-authorized');
}
~~~

### Помощник представления Access

Иногда может потребоваться обратиться к `RbacManager` внутри шаблона представления. Например,
вам может понадобиться спрятать или, наоборот, показать какой-нибудь HTML-блок в зависимости от
привилегий текущего пользователя.

Код помощника представления будет находиться в файле `Access.php` в каталоге *View/Helper*
корневой директории модуля *User*:

~~~php
<?php
namespace User\View\Helper;

use Laminas\View\Helper\AbstractHelper;

/**
 * Этот помощник представления используется для проверки привилегий пользователя.
 */
class Access extends AbstractHelper
{
    private $rbacManager = null;

    public function __construct($rbacManager)
    {
        $this->rbacManager = $rbacManager;
    }

    public function __invoke($permission, $params = [])
    {
        return $this->rbacManager->isGranted(null, $permission, $params);
    }
}
~~~

Фабрика помощника представления `Access` выглядит следующим образом:

~~~php
<?php
namespace User\View\Helper\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use User\Service\RbacManager;
use User\View\Helper\Access;

/**
 * Это фабрика для помощника представления Access. Ее целями являются инстанцирование помощника
 * и внедрение зависимостей в его конструктор.
 */
class AccessFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $rbacManager = $container->get(RbacManager::class);

        return new Access($rbacManager);
    }
}
~~~

Помощник представления зарегистрирован в файле конфигурации *module.config.php:

~~~php
// Мы регистрируем предоставляемые модулем помощники представления под этим ключом.
'view_helpers' => [
    'factories' => [
        View\Helper\Access::class => View\Helper\Factory\AccessFactory::class,
    ],
    'aliases' => [
        'access' => View\Helper\Access::class,
    ],
],
~~~

Таким образом, вы легко можете вызвать помощник представления из любого из ваших шаблонов представления:

~~~php
if ($this->access('profile.own.view, ['user'=>$user]))) {
   // что-то делаем...
}
~~~

## Использование модуля User

Поздравляем, наш сайт *Role Demo* почти готов. Давайте кратко обговорим то, каким образом вы
обычно будете использовать его, если планируете создать свой собственный вебсайт на его основе.

Первым делом вам необходимо создать все необходимые роли и привилегии через написанный нами удобный
пользовательский интерфейс. Присвойте роль (или несколько ролей) каждому пользователю сайта.

Затем измените файл конфигурации *module.config.php* в модуле и добавьте два ключа:

  * ключ `rbac_manager`, который будет содержать настройки для `RbacManager` (в частности,
    конфигурацию менеджера(-ов));

  Пример этого ключа представлен ниже:

~~~php
// Этот ключ хранит конфигурацию для менеджера RBAC.
'rbac_manager' => [
    'assertions' => [Service\RbacAssertionManager::class],
],
~~~

  * и ключ `access_filter`, который будет хранить правила доступа для страниц вашего вебсайта. Как правило, он выглядит так:

~~~php
'access_filter' => [
    'options' => [
        'mode' => 'restrictive'
    ],
    'controllers' => [
        Controller\IndexController::class => [
            // Разрешить всем доступ к действиям "index" и "about"
            ['actions' => ['index', 'about'], 'allow' => '*'],
            // Разрешить авторизованным пользователям доступ к действию "settings"
            ['actions' => ['settings'], 'allow' => '@']
        ],
    ]
],
~~~

Символы `*` и `@` в подключах `allow` являются не единственными опциями. Ниже показано,
как могут выглядеть подключи `allow`. Так, мы разрешаем доступ к странице:

 * всем, если мы поставим звездочку (`*`);
 * любому аутентифицированному пользователю, если мы поставим коммерческое at (`@`);
 * конкретному аутентифицированному пользователю с заданным адресом эл. почты `личности`, если мы поставим (`@identity`)
 * любому аутентифицированному пользователю с заданной `привилегией`, если мы поставим знак плюса и имя привилегии (`+permission`).

Если ваш вебсайт содержит динамические утверждения, расширьте метод `assert()` существующего
класса `RbacAssertionManager` (или напишите и зарегистрируйте свой собственный менеджер утверждений):

~~~php
public function assert(Rbac $rbac, $permission, $params)
{
    $currentUser = $this->entityManager->getRepository(User::class)
            ->findOneByEmail($this->authService->getIdentity());

    if ($permission=='post.own.edit' && $params['post']->getUser()->getId()==$currentUser->getId())
        return true;

    if ($permission=='post.own.publish' && $params['post']->getUser()->getId()==$currentUser->getId())
        return true;

    return false;
}
~~~

Если вы хотите проверить привилегии в действии контроллера, можно использовать плагин контроллера `Access` следующим образом:

~~~php
if (!$this->access('profile.own.view', ['user'=>$user])) {
    return $this->redirect()->toRoute('not-authorized');
}
~~~

Если вы хотите проверить привилегии в шаблоне представления, можете использовать помощник представления `Access`:

~~~php
if ($this->access('profile.own.view', ['user'=>$user))) {
   // что-то делаем...
}
~~~

Вот и все! Несложно, не правда ли?

## Выводы

В этой главе мы реализовали вебсайт *Role Demo*, который демонстрирует использование ролей и
привилегий в Laminas.

Роль – это, по сути, группа пользователей. Пользователю могут быть присвоены одна или сразу несколько ролей.

Роли могут быть присвоены одна или несколько привилегий. Привилегия – это простое единичное действие в системе.

Динамическое утверждение – это дополнительное правило, связанное с привилегией.

Роли могут быть организованы в иерархию, где родительские роли
наследуют привилегии от дочерних.
