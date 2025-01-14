# Управление пользователями и аутентификация {#users}

Большинство веб-сайтов в Интернете позволяют своим посетителям зарегистрироваться и создать профиль. После
этого пользователь при заходе на сайт увидит персонализированную информацию. Например, на сайте электронной
коммерции зарегистрированный пользователь может покупать товары, управлять своей корзиной и оформлять платежи
с помощью кредитной карты.

В этой главе вы узнаете о том, как реализовать аутентификацию пользователя с помощью логина и пароля на laminas-сайте.
Мы покажем, как управлять пользователями (добавлять, изменять, просматривать и менять/сбрасывать пароль) через
веб-приложение и безопасно хранить пользовательские пароли в базе данных. Кроме того, вы узнаете, как реализовать
фильтр доступа и делать определенные страницы доступными только для вошедших пользователей.

Так как после прочтения предыдущих глав вы уже наверняка хорошо знакомы с Laminas, в этой главе мы опустим
описание некоторых очевидных вещей и сконцентрируемся лишь на *концептуальных* моментах. Рекомендуем обратиться
к примеру *User Demo*, который идет вместе с этой книгой, - готовому веб-сайту, который вы можете запустить
и посмотреть на рассматриваемый нами функционал в действии. Весь код, представленный в этой главе, является
частью этого приложения.

Компоненты Laminas, рассматриваемые в этой главе:

|--------------------------------|---------------------------------------------------------------|
| *Компонент*                    | *Описание*                                                    |
|--------------------------------|---------------------------------------------------------------|
| `Laminas\Authentication`          | Предоставляет механизмы аутентификации пользователя.          |
|--------------------------------|---------------------------------------------------------------|
| `Laminas\Crypt`                   | Предоставляет функциональность для шифрования паролей.        |
|--------------------------------|---------------------------------------------------------------|
| `Laminas\Math`                    | Предоставляет функциональность для генерации случайных безопасных токенов. |
|--------------------------------|---------------------------------------------------------------|

## Загрузка примера User Demo с GitHub

В качестве демонстрации, в этой главе мы напишем реальный веб-сайт *User Demo*, в процессе создания
которого узнаем, как:

  * Создать новый модуль с именем *User*. Этот модуль будет хранить функциональность для аутентификации пользователей и управления ими.
  * Создать сущность `User`.
  * Реализовать безопасное хранение пароля пользователя.
  * Реализовать аутентификацию пользователя (с помощью логина и пароля).
  * Реализовать фильтр для того, чтобы разрешить доступ к определенным страницам только аутентифицированным пользователям.
  * Реализовать UI управления пользователями, который позволит добавлять, изменять и просматривать пользователя, а также менять его пароль.
  * Инициализировать пункты главного меню различным способом, в зависимости от того, зашел ли пользователь под своими идентификационными данными.

Для загрузки приложения *User Demo*, зайдите на [эту страницу](https://github.com/olegkrivtsov/using-laminas-book-samples) и
нажмите кнопку *Clone or Download*, чтобы скачать код в виде ZIP-архива. После завершения загрузки, распакуйте архив веб-приложение
какой-либо каталог.

Затем перейдите в каталог `userdemo`, содержащий исходный код
веб-приложения *User Demo*:

~~~text
/using-laminas-book-samples
  /userdemo
  ...
~~~

I> Детальные инструкции о том, как установить образец *User Demo* можно найти в файле *README.md*, расположенном в каталоге примера *User Demo*.

## Создание модуля User

Итак, первое, что мы создадим для приложения *User Demo* - новый модуль с именем *User*. В
этот модуль мы добавим всю функциональность, связанную с управлением пользователями и аутентификацией.
Если вы еще не знакомы с концепцией модулей, обратитесь к главе [Создание нового модуля](#modules).

Модуль *User* будет иметь очень малое количество зависимостей от других модулей веб-сайта.
Цель создания этого модуля - сделать многократно используемый блок, который вы можете использовать
в своем веб-приложении без каких-либо изменений (либо с небольшими модификациями).

T> В идеале, вы сможете использовать модуль *User* при разработке своего сайта, не внося в модуль никаких изменений.
T> Однако, для реальных сайтов вам, возможно, придется добавить какие-нибудь поля в таблицу `user`, изменить
T> рабочий процесс создания пользователя или модифицировать алгоритм фильтрации доступа. В таком случае вам нужно
T> будет адаптировать код под свои нужды.

Модуль *User* будет иметь следующую структуру (см. рисунок 16.1 ниже):

![Рисунок 16.1. Структура модуля User](../en/images/users/user_module_structure.png)

Ниже мы вкратце опишем классы, которые будут находиться в каталоге модуля.

У нас будет два контроллера:

  * *UserController* будет содержать функциональность для управления пользователями (добавление, изменение, смена пароля и т.д.)
  * *AuthController* будет реализовывать функциональность для аутентификации (вход в аккаунт и выход из него)

Будет одна сущность Doctrine:

  * Сущность *User* будет использоваться для хранения информации о пользователе в базе данных (адрес эл. почты, полное имя, пароль и т.д.).

Четыре формы для сбора данных:

  * *LoginForm* будет использоваться для сбора данных аутентификации (логина и пароля).
  * *PasswordChangeForm* будет использоваться для сбора данных для изменения или сброса пароля пользователя.
  * *PasswordResetForm* будет использоваться для получения адреса электронной почты человека, который забыл свой пароль и хочет его восстановить.
  * *UserForm* будет использоваться для сбора данных о пользователе (адрес эл. почты, полное имя, пароль и т.д.).


И несколько сервисов:

  * Сервис *AuthAdapter* будет реализовывать алгоритм аутентификации. Он будет проверять, корректны ли
    логин (адрес эл. почты) и пароль пользователя. Для осуществления этой проверки, он будет извлекать из
	базы данных информацию о пользователе.
  * Сервис *AuthManager* будет совершать фактическую аутентификацию (вход в аккаунт на сайте и выход). Помимо
    этого, он будет реализовывать фильтр доступа, который может отказать не вошедшим на сайт пользователям в
	доступе к определенным веб-страницам.
  * *UserManager* будет содержать бизнес-логику управления пользователями (добавление, изменение, смена пароля).

Большинство контроллеров и сервисов будут инстанцированы с помощью фабрик. Классы фабрик можно найти под
подкаталогами *Factory*.

Внутри каталога *view* будет находиться несколько шаблонов представлений, которые будут визуализировать
HTML-разметку страниц пользовательского интерфейса из нашего модуля.

Как и обычно, в каталоге *config* будет находиться файл *module.config.php*, который будет содержать маршруты и регистрацию для
наших контроллеров и сервисов. Он также будет содержать ключ *access_filter*, определяющий, какие веб-страницы будут доступны
не вошедшему на сайт пользователю (этот ключ будет считываться сервисом *AuthManager*).

Как видите, модуль *User* - это обычный модуль Laminas, имеющий структуру, которая соответствует шаблону проектирования MVC.

## Создание базы данных

Для нашего приложения нам необходимо будет создать базу данных "userdemo" . Эта БД будет содержать одну единственную
таблицу `user` для хранения данных, связанных с пользователями нашего сайта (см. рисунок 16.2 ниже).

![Рисунок 16.2. Таблица User](../en/images/users/userdemo_database_schema.png)

Эта таблица содержит следующие поля:

  * `id` - автоматически инкрементируемое integer-поле (первичный ключ).
  * `email` - строковое поле, содержащее адрес эл. почты пользователя. Так как у каждого пользователя будет уникальный электронный адрес, это поле тоже будет уникальным.
  * `full_name` - строковое поле, которое будет содержать полное имя пользователя (например, "John Doe").
  * integer-поле `status` будет содержать статус пользователя ("активный" или "неактивные"). Неактивные пользователи не могут зайти на сайт под своим аккаунтом.
  * `date_created`, содержащее дату и время создания пользователя.
  * поля `pwd_reset_token` и `pwd_reset_token_creation_date`, используемые для сброса пароля (когда пользователь забыл свой пароль и хочет его восстановить).

T> При разработке своего сайта вы наверняка захотите добавить больше полей к таблице `user`.
В нашем примере мы определяем только некоторый минимальный набор полей.

Таблица `user` создается с помощью следующего оператора SQL:

~~~
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(128) NOT NULL,
  `full_name` varchar(512) NOT NULL,
  `password` varchar(256) NOT NULL,
  `status` int(11) NOT NULL,
  `date_created` datetime NOT NULL,
  `pwd_reset_token` varchar(32) DEFAULT NULL,
  `pwd_reset_token_creation_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_idx` (`email`)
);
~~~

Миграцию, которая создает таблицу `user`, можно найти в образце *User Demo*.

T> Если вы еще не знакомы с миграциями, обратитесь к главе [Миграции баз данных](#migrations).

## Реализация сущности User

Приложение *User Demo* использует ORM Doctrine для управления базой данных. О том, как использовать
Doctrine, мы уже узнали в главе [Управление базой данных с помощью ORM Doctrine](#doctrine).

Для хранения в базе данных информации о пользователях, мы создадим сущность `User`. Она сопоставляется
с таблицей БД `user` и является типичным классом сущности Doctrine.

Создайте файл *User.php* в каталоге *Entity* под корневым каталогом модуля. Поместите в этот файл
следующий код:

~~~php
<?php
namespace User\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Этот класс представляет собой зарегистрированного пользователя.
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
     * Устанавливает статус.
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
     * Задает дату создания пользователя.
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
}
~~~

Как можете понять из фрагмента кода выше, сущность *User* - это типичная сущность Doctrine, содержащая
свойства с аннотациями, а также геттеры и сеттеры для извлечения/задания этих свойств.

## Добавление UserController

Класс `UserController` будет содержать несколько методов действия, предназначенных для
предоставления интерфейса администратора. У него будут следующие действия:

  * Действие `indexAction()`, которое будет отображать веб-страницу, содержащую список пользователей (см. рисунок 16.3).
    Наберите в адресной строке своего браузера "http://localhost/users", чтобы перейти на эту страницу.
  * Действие `addAction()`, которое будет отображать страницу, на которой можно создать нового пользователя (см. рисунок 16.4).
    Наберите в адресной строке своего браузера "http://localhost/users/add", чтобы перейти на эту страницу.
  * Действие `editAction()`, которое будет отображать страницу для обновления существующего пользователя (см. рисунок 16.5).
    Наберите в адресной строке своего браузера "http://localhost/users/edit/&lt;id&gt;", чтобы перейти на эту страницу.
  * Действие `viewAction()`, позволяющее просмотреть существующего пользователя (см. рисунок 16.6).
    Наберите в адресной строке своего браузера "http://localhost/users/view/&lt;id&gt;", чтобы перейти на эту страницу.
  * Действие `changePasswordAction()`, которое предоставит администратору возможность сменить пароль существующего пользователя (см. рисунок 16.7).
    Наберите в адресной строке своего браузера "http://localhost/users/changePassword/&lt;id&gt;", чтобы перейти на эту страницу.
  * Действие `resetPasswordAction()`, которое позволит пользователю сбросить свой пароль (см. рисунок 16.8).
    Наберите в адресной строке своего браузера "http://localhost/reset-password", чтобы перейти на эту страницу.

![Рисунок 16.3. Страница со списком пользователей](../en/images/users/users_page.png)

![Рисунок 16.4. Страница добавления нового пользователя](../en/images/users/add_user_page.png)

![Рисунок 16.5. Страница изменения существующего пользователя](../en/images/users/edit_user_page.png)

![Рисунок 16.6. Страница профиля пользователя](../en/images/users/view_user_page.png)

![Рисунок 16.7. Страница изменения пароля](../en/images/users/change_password_page.png)

![Рисунок 16.8. Страница сброса пароля](../en/images/users/reset_password_page.png)

Контроллер `UserController` рассчитан быть как можно более "тонким". Он содержит только код, отвечающий за
проверку входных данных, инстанцирование нужных моделей, передачу входных данных моделям и возврат данных
для визуализации в шаблоне представления. Так как класс `UserController` является типичным классом контроллера
(а также потому, что весь его код вы можете найти в примере *User Demo*), мы не будем описывать его более детально.

## Добавление сервиса UserManager

`UserController` работает в паре с сервисом *UserManager*, содержащим всю бизнес-логику, связанную с управлением пользователями.
Этот сервис позволяет создавать и обновлять пользователей, а также изменять и сбрасывать пароль пользователя. Некоторые части данного
сервиса мы рассмотрим более детально, а некоторые, наиболее очевидные, пропустим (напоминаем, что готовый код можно посмотреть в
образце *User Demo*).

### Создание нового пользователя и хранение пароля в зашифрованном виде

Добавить нового пользователя с помощью метода `addUser()` класса `UserManager`. Выглядит он следующим образом:

~~~php
/**
 * Этот метод добавляет нового пользователя.
 */
public function addUser($data)
{
    // Не допускаем создание нескольих пользователей с одинаковым адресом эл. почты.
    if($this->checkUserExists($data['email'])) {
        throw new \Exception("User with email address " .
                    $data['$email'] . " already exists");
    }

    // Создаем новую сущность User.
    $user = new User();
    $user->setEmail($data['email']);
    $user->setFullName($data['full_name']);

    // Зашифровываем пароль и храним его в зашифрованном состоянии.
    $bcrypt = new Bcrypt();
    $passwordHash = $bcrypt->create($data['password']);
    $user->setPassword($passwordHash);

    $user->setStatus($data['status']);

    $currentDate = date('Y-m-d H:i:s');
    $user->setDateCreated($currentDate);

    // Добавляем сущность в менеджер сущностей.
    $this->entityManager->persist($user);

    // Применяем изменения к базе данных.
    $this->entityManager->flush();

    return $user;
}
~~~

В данном методе мы первым делом проверяем, существует ли другой пользователь с таким же адресом
электронной почты (строка 7). Если существует, мы запрещаем создание нового пользователя, выбросив
исключение.

Если пользователя с таким адресом не существует, мы создаем новую сущность `User` (строка 13) и задаем
ее свойства.

Обратите внимание на то, как мы сохраняем в базу данных пароль пользователя. Из соображений безопасности
мы не сохраняем его как есть, а вычисляем его хэш с помощью класса `Bcrypt`, предоставляемого компонентом
Laminas Framework под названием `Laminas\Crypt` (строки 18-19).

T> `Laminas\Crypt` можно установить следующей командой:
T>
T>   `php composer.phar require laminas/laminas-crypt`
T>
T> Компонент `Laminas\Crypt` также требует установленного PHP-расширения `mcrypt`.

W> *Bcrypt* - широко используемый алгоритм хэширования, рекомендуемый сообществом информационной безопасности для хранения паролей пользователей.
W> Шифрование паролей с помощью `Bcrypt` на данный момент считается безопасным. Некоторые разработчики до сих пор используют
W> MD5 или SHA1 с солью, но эти алгоритмы больше не считаются безопасными (MD5 и SHA1 могут быть взломаны).

### Валидация зашифрованного пароля

Когда пользователь заходит на свой аккаунт, вам необходимо сравнить хэш, хранящийся в БД,
и хэш, вычисленный от введенного пользователем пароля. Это можно сделать с помощью метода
`verify()` класса `Bcrypt`, как показано ниже:

~~~php
/**
 * Проверяет, что заданный пароль является корректным..
 */
public function validatePassword($user, $password)
{
    $bcrypt = new Bcrypt();
    $passwordHash = $user->getPassword();

    if ($bcrypt->verify($password, $passwordHash)) {
        return true;
    }

    return false;
}
~~~

### Создание пользователя Admin

Следующим важным аспектом `UserManager` является создание пользователя Admin.

I> Admin - это первоначальный пользователь, создающийся автоматически, когда
I> в базе данных еще нет пользователей, и позволяющий вам в первый раз войти на сайт.

~~~php
/**
 * Этот метод проверяет, существует ли хотя бы один пользователь, и, если таковых нет, создает
 * пользователя 'Admin' с эл. адресом 'admin@example.com' и паролем 'Secur1ty'.
 */
public function createAdminUserIfNotExists()
{
    $user = $this->entityManager->getRepository(User::class)->findOneBy([]);
    if ($user==null) {
        $user = new User();
        $user->setEmail('admin@example.com');
        $user->setFullName('Admin');
        $bcrypt = new Bcrypt();
        $passwordHash = $bcrypt->create('Secur1ty');
        $user->setPassword($passwordHash);
        $user->setStatus(User::STATUS_ACTIVE);
        $user->setDateCreated(date('Y-m-d H:i:s'));

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
~~~

Мы задаем адрес эл. почты пользователя Admin как `admin@example.com`, а пароль - как `Secur1ty`. Таким образом,
вы можете первый раз войти на сайт с помощью этих идентификационных данных.

### Сброс пароля пользователя

Иногда пользователи забывают свои пароли. В таком случае нам нужно предоставить им возможность *сбросить*
пароль - безопасно его изменить. Сброс пароля работает следующим образом:

  * Генерируется случайный *токен сброса пароля* и его хэш сохраняется в базу данных.
  * Токен сброса пароля отсылается на e-mail пользователя как часть эл. письма.
  * Пользователь проверяет свой почтовый ящик и кликает по ссылке сброса пароля в полученном сообщении.
  * Веб-сайт валидирует токен сброса пароля и проверяет, что срок его действия еще не истек.
  * Пользователь направляется на форму ввода нового пароля.

I> Вы обычно не храните токены сброса пароля в чистом виде в БД. Вместо этого вы храните *хэш* токена. Это делается в целях безопасности.
I> Даже если некий злоумышленик украдет БД, они не смогут сбросить пароли пользователей.

Алгоритм генерации токена сброса пароля реализован внутри метода `generatePasswordResetToken()` класса `UserManager`.
Чтобы сгенерировать случайную строку, мы используем класс `Rand`, предоставляемый компонентом `Laminas\Math`.

~~~php
/**
 * Генерирует для пользователя токен сброса пароля. Этот токен хранится в базе данных и
 * отсылается на адрес эл. почты пользователя. Когда пользователь нажимает на ссылку в сообщении,
 * он направляется на страницу Set Password.
 */
public function generatePasswordResetToken($user)
{
    if ($user->getStatus() != User::STATUS_ACTIVE) {
        throw new \Exception('Cannot generate password reset token for inactive user ' . $user->getEmail());
    }

    // Generate a token.
    $token = Rand::getString(32, '0123456789abcdefghijklmnopqrstuvwxyz', true);

    // Encrypt the token before storing it in DB.
    $bcrypt = new Bcrypt();
    $tokenHash = $bcrypt->create($token);

    // Save token to DB
    $user->setPasswordResetToken($tokenHash);

    // Save token creation date to DB.
    $currentDate = date('Y-m-d H:i:s');
    $user->setPasswordResetTokenCreationDate($currentDate);

    // Apply changes to DB.
    $this->entityManager->flush();

    // Send an email to user.
    $subject = 'Password Reset';

    $httpHost = isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:'localhost';
    $passwordResetUrl = 'http://' . $httpHost . '/set-password?token=' . $token . "&email=" . $user->getEmail();

    // Produce HTML of password reset email
    $bodyHtml = $this->viewRenderer->render(
            'user/email/reset-password-email',
            [
                'passwordResetUrl' => $passwordResetUrl,
            ]);

    $html = new MimePart($bodyHtml);
    $html->type = "text/html";

    $body = new MimeMessage();
    $body->addPart($html);

    $mail = new Mail\Message();
    $mail->setEncoding('UTF-8');
    $mail->setBody($body);
    $mail->setFrom('no-reply@example.com', 'User Demo');
    $mail->addTo($user->getEmail(), $user->getFullName());
    $mail->setSubject($subject);

    // Setup SMTP transport
    $transport = new SmtpTransport();
    $options   = new SmtpOptions($this->config['smtp']);
    $transport->setOptions($options);

    $transport->send($mail);
}
~~~

I> Настройка почтовой системы для веб-сервера, как правило, требует покупки платной подписки
I> почтового сервиса (такого как [SendGrid](https://sendgrid.com/) или [Amazon SES](https://aws.amazon.com/en/ses))

Валидация токена сброса пароля реализована внутри метода `validatePasswordResetToken()`.
Мы сверяем передаваемый токен с тем, что мы сохранили в базе данных, а также проверяем,
что срок действия токена (1 день после создания) не истек .

~~~php
/**
 * Проверяем, действителен ли токен сброса пароля.
 */
public function validatePasswordResetToken($email, $passwordResetToken)
{
    // Find user by email.
    $user = $this->entityManager->getRepository(User::class)
            ->findOneByEmail($email);

    if($user==null || $user->getStatus() != User::STATUS_ACTIVE) {
        return false;
    }

    // Check that token hash matches the token hash in our DB.
    $bcrypt = new Bcrypt();
    $tokenHash = $user->getPasswordResetToken();

    if (!$bcrypt->verify($passwordResetToken, $tokenHash)) {
        return false; // mismatch
    }

    // Check that token was created not too long ago.
    $tokenCreationDate = $user->getPasswordResetTokenCreationDate();
    $tokenCreationDate = strtotime($tokenCreationDate);

    $currentDate = strtotime('now');

    if ($currentDate - $tokenCreationDate > 24*60*60) {
        return false; // expired
    }

    return true;
}
~~~

И наконец, `setPasswordByToken()` позволяет установить новый пароль для пользователя.

~~~php
/**
 * Этот метод устанавливает новый пароль по токену сброса пароля.
 */
public function setNewPasswordByToken($email, $passwordResetToken, $newPassword)
{
    if (!$this->validatePasswordResetToken($email, $passwordResetToken)) {
       return false;
    }

    // Find user with the given email.
    $user = $this->entityManager->getRepository(User::class)
            ->findOneByEmail($email);

    if ($user==null || $user->getStatus() != User::STATUS_ACTIVE) {
        return false;
    }

    // Set new password for user
    $bcrypt = new Bcrypt();
    $passwordHash = $bcrypt->create($newPassword);
    $user->setPassword($passwordHash);

    // Remove password reset token
    $user->setPasswordResetToken(null);
    $user->setPasswordResetTokenCreationDate(null);

    $this->entityManager->flush();

    return true;
}
~~~

## Реализация аутентификации пользователя

*Аутентификация* - это процесс, при котором пользователь предъявляет свои логин и пароль, а вы решаете, корректны
ли его учетные данные. Аутентификация обычно подразумевает, что вы проверяете свою базу данных на наличие
заданного логина, и, если такой логин существует, проверяете, совпадает ли хэш, вычисленный по заданному паролю
с хэшем пароля, хранимым в базе данных.

I> Как правило, пароль как он есть не хранится в базе данных. Вместо этого хранится его *хэш*. Это делается из соображений безопасности.

После того, как алгоритм аутентификации определяет, что логин и пароль верны, он возвращает *личность* пользователя -
его уникальный ID. Личность, как правило, хранится в сессии, так что посетителю сайта не нужно проходить аутентификацию
для каждого HTTP-запроса.

В Laminas для реализации аутентификации пользователя существует специальный компонент - `Laminas\Authentication`.
Его можно установить с помощью Composer, набрав следующую команду:

```
php composer.phar require laminas/laminas-authentication
```

T> Для работы механизмов аутентификации, вам также потребуются установленный компонент `Laminas\Session` и настроенный менеджер сессий. За
T> информацией о том, как это сделать, обратитесь к главе [Работа с сессиями](#session).

### AuthenticationService

Компонент `Laminas\Authentication` предоставляет специальный класс сервиса `AuthenticationService`, "живущий"
в пространстве имен `Laminas\Authentication`. Наиболее полезные методы этого сервиса показаны в таблице 16.1 ниже.

{title="Таблица 16.1. Методы класса AuthenticationService"}
|--------------------------------|---------------------------------------------------------------|
| *Метод*                        | *Описание*                                                    |
|--------------------------------|---------------------------------------------------------------|
| `authenticate()`               | Выполняет аутентификация пользователя, используя адаптер.     |
|--------------------------------|---------------------------------------------------------------|
| `getAdapter()`                 | Получает адаптер аутентификации.                               |
|--------------------------------|---------------------------------------------------------------|
| `setAdapter()`                 | Задает адаптер, реализующий алгоритм аутентификации.          |
|--------------------------------|---------------------------------------------------------------|
| `getStorage()`                 | Возвращает обработчик хранилищ.                              |
|--------------------------------|---------------------------------------------------------------|
| `setStorage()`                 | Задает обработчик хранилищ.                                  |
|--------------------------------|---------------------------------------------------------------|
| `hasIdentity()`                | Возвращает `true`, если личность пользователя уже хранится в сессии. |
|--------------------------------|---------------------------------------------------------------|
| `getIdentity()`                | Извлекает из сессии личность пользователя.                    |
|--------------------------------|---------------------------------------------------------------|
| `clearIdentity()`              | Удаляет личность пользователя из сессии.                      |
|--------------------------------|---------------------------------------------------------------|

Как видите из данной таблицы, для выполнения аутентификации можно использовать метод `authenticate()`.
Помимо этого, можно использовать методы `hasIdentity()`, `getIdentity()` и `clearIdentity()` для,
соответственно, проверки существования, извлечения и удаления личности пользователя.

Однако, сервис `AuthenticationService` очень 'универсален' - он ничего не знает о том, как
сверять логин и пароль с БД. Он также не умеет сохранять личность пользователя в сессию.
Этот сервис предназначен для реализации любых подходящих алгоритма аутентификации и хранилища.

Компонент `Laminas\Authentication` предоставляет несколько *адаптеров аутентификации*, реализующих некоторые
стандартные алгоритмы аутентификации (см. рисунок 16.9) и несколько *обработчиков хранилищ*, позволяющих
сохранять и извлекать личность пользователя (см. рисунок 16.10).

![Рисунок 16.9. Стандартные адаптеры аутентификации](../en/images/users/std_auth_adapters.png)

![Рисунок 16.10. Стандартные обработчики хранилищ](../en/images/users/std_auth_storage_handlers.png)

Для наших целей мы может использовать обработчик хранилищ `Session`, не внося никаких изменений в его код. Однако,
стандартные адаптеры аутентификации нам не подходят, так как мы используем ORM Doctrine. Нам придется написать свой
соббственный адаптер аутентификации. К счастью, это довольно просто.

### Написание адаптера аутентификации

Адаптер аутентификации должен реализовывать интерфейс `AdapterInterface`, который имеет один единственный
метод `authenticate()`. Этот метод должен сверять с базой данных адрес эл. почты и пароль пользователя.
Мы сделаем это следующим образом:

  * Находим пользователя с заданным адресом эл. почты (логин пользователя - его e-mail).
  * Если пользователя с таким адресом не существует - возвращаем статус ошибки.
  * Проверяем статус пользователя. Если статус - "неактивный", запрещаем ему заходить на учетную запись.
  * Вычисляем хэш пароля и сравниваем его с хэшем, хранимым в БД, который относится к найденному пользователю.
  * Если хэши паролей не совпадают, возвращаем статус ошибки.
  * Если пароль верен, возвращаем статус успеха.

Метод `authenticate()` возвращает экземпляр класса `Laminas\Authentication\Result`. Класс
`Result` содержит статус аутентификации, сообщение об ошибке и личность пользователя.

Адаптер может иметь и другие методы: например, `setEmail()` и `setPassword()`, которые мы будем
использовать для передачи адаптеру эл. адреса и пароля пользователя.

Чтобы создать адаптер аутентификации, добавьте файл *AuthAdapter.php* в каталог *Service* исходного каталога модуля.

I> В приложении *User Demo* мы создаем отдельный модуль *User* и добавляем в него всю функциональность, относящуюся к
I> аутентификации и управлению пользователями.

Поместите в этот файл следующий код:

~~~php
<?php
namespace User\Service;

use Laminas\Authentication\Adapter\AdapterInterface;
use Laminas\Authentication\Result;
use Laminas\Crypt\Password\Bcrypt;
use User\Entity\User;

/**
 * Это адаптер, используемый для аутентификации пользователя. Он принимает логин (адрес эл. почты)
 * и пароль, и затем проверяет, есть ли в базе данных пользователь с такими учетными данными.
 * Если такой пользователь существует, сервис возвращает его личность (эл. адрес). Личность
 * сохраняется в сессии и может быть извлечена позже с помощью помощника представления Identity,
 * предоставляемого Laminas.
 */
class AuthAdapter implements AdapterInterface
{
    /**
     * E-mail пользователя.
     * @var string
     */
    private $email;

    /**
     * Пароль.
     * @var string
     */
    private $password;

    /**
     * Менеджер сущностей.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * Конструктор.
     */
    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Задает эл. адрес пользователя.
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Устанавливает пароль.
     */
    public function setPassword($password)
    {
        $this->password = (string)$password;
    }

    /**
     * Выполняет попытку аутентификации.
     */
    public function authenticate()
    {
        // Проверяем, есть ли в базе данных пользователь с таким адресом.
        $user = $this->entityManager->getRepository(User::class)
                ->findOneByEmail($this->email);

        // Если такого пользователя нет, возвращаем статус 'Identity Not Found'.
        if ($user == null) {
            return new Result(
                Result::FAILURE_IDENTITY_NOT_FOUND,
                null,
                ['Invalid credentials.']);
        }

        // Если пользователь с таким адресом существует, необходимо проверить, активен ли он.
        // Неактивные пользователи не могут входить в систему.
        if ($user->getStatus()==User::STATUS_RETIRED) {
            return new Result(
                Result::FAILURE,
                null,
                ['User is retired.']);
        }

        // Теперь необходимо вычислить хэш на основе введенного пользователем пароля и сравнить его
        // с хэшем пароля из базы данных.
        $bcrypt = new Bcrypt();
        $passwordHash = $user->getPassword();

        if ($bcrypt->verify($this->password, $passwordHash)) {
            // Отлично! Хэши паролей совпадают. Возвращаем личность пользователя (эл. адрес) для
            // хранения в сессии с целью последующего использования.
            return new Result(
                    Result::SUCCESS,
                    $this->email,
                    ['Authenticated successfully.']);
        }

        // Если пароль не прошел проверку, возвращаем статус ошибки 'Invalid Credential'.
        return new Result(
                Result::FAILURE_CREDENTIAL_INVALID,
                null,
                ['Invalid credentials.']);
    }
}
~~~

### Создание фабрики для AuthenticationService

После реализации адаптера мы наконец можем создать `AuthenticationService`. Перед тем,
как вы сможете использовать этот сервис, его нужно зарегистрировать в менеджере сервисов.
Сперва мы создадим для него фабрику. Добавьте файл *AuthenticationServiceFactory.php* в
каталог *Service/Factory* и поместите в него следующий код:

~~~php
<?php
namespace User\Service\Factory;

use Interop\Container\ContainerInterface;
use Laminas\Authentication\AuthenticationService;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Session\SessionManager;
use Laminas\Authentication\Storage\Session as SessionStorage;
use User\Service\AuthAdapter;

/**
 * Это фабрика, отвечающая за создание сервиса аутентификации.
 */
class AuthenticationServiceFactory implements FactoryInterface
{
    /**
     * Этот метод создает сервис Laminas\Authentication\AuthenticationService
     * и возвращает его экземпляр.
     */
    public function __invoke(ContainerInterface $container,
                    $requestedName, array $options = null)
    {
        $sessionManager = $container->get(SessionManager::class);
        $authStorage = new SessionStorage('Laminas_Auth', 'session', $sessionManager);
        $authAdapter = $container->get(AuthAdapter::class);

        // Создаем сервис и внедряем зависимости в его конструктор.
        return new AuthenticationService($authStorage, $authAdapter);
    }
}
~~~

В описании фабрики мы первым делом создаем экземпляр менеджера сессий (у вас уже должен быть
созданный менеджер сессий) и экземпляр обработчика хранилищ `Session`. После этого мы создаем
экземпляр `AuthAdapter`. Затем мы инстанцируем `AuthenticationService` и внедряем в него
зависимости (обработчик хранилищ и адаптер).

Зарегистрируйте `AuthenticationService` в файле конфигурации *module.config.php* как показано ниже:

~~~php
<?php
return [
    'service_manager' => [
        'factories' => [
            \Laminas\Authentication\AuthenticationService::class
                => Service\Factory\AuthenticationServiceFactory::class,
            // ...
        ],
    ],
];
~~~

### Добавление AuthController

Класс `AuthController` будет иметь два действия:

  * Действие `loginAction()`, позволяющее войти на сайт (см. рисунки 16.11 и 16.12).
    Вы можете перейти на эту страницу, набрав в адресной страке своего браузера "http://localhost/login".

  * Действие `logoutAction()`, позволяющее выходить из своей учетной записи.
    Вы можете перейти на эту страницу, набрав в адресной строке своего браузера "http://localhost/logout".

![Рисунок 16.11. Страница входа на сайт](../en/images/users/login_page.png)

![Рисунок 16.12. Страница входа на сайт - недействительные учетные данные](../en/images/users/login_page_errors.png)

Ниже представлен код класса контроллера `AuthController`:

~~~php
<?php

namespace User\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\Authentication\Result;
use Laminas\Uri\Uri;
use User\Form\LoginForm;
use User\Entity\User;

/**
 * Этот контейнер отвечает для предоставлению пользователю возможности входа в систему и выхода из нее.
 */
class AuthController extends AbstractActionController
{
    /**
     * Менеджер сущностей.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * Менеджер аутентификации.
     * @var User\Service\AuthManager
     */
    private $authManager;

    /**
     * Сервис аутентификации.
     * @var \Laminas\Authentication\AuthenticationService
     */
    private $authService;

    /**
     * Менеджер пользователей.
     * @var User\Service\UserManager
     */
    private $userManager;

    /**
     * Конструктор.
     */
    public function __construct($entityManager, $authManager, $authService, $userManager)
    {
        $this->entityManager = $entityManager;
        $this->authManager = $authManager;
        $this->authService = $authService;
        $this->userManager = $userManager;
    }

    /**
     * Аутентифицирует пользователя по заданным эл. адресу и паролю.
     */
    public function loginAction()
    {
        // Извлекает URL перенаправления (если таковой передается). Мы перенаправим пользователя
        // на данный URL после успешной аутентификации.
        $redirectUrl = (string)$this->params()->fromQuery('redirectUrl', '');
        if (strlen($redirectUrl)>2048) {
            throw new \Exception("Too long redirectUrl argument passed");
        }

        // Проверяем, есть ли вообще в базе данных пользователи. Если их нет,
        // создаем пользователя 'Admin'.
        $this->userManager->createAdminUserIfNotExists();

        // Создаем форму входа на сайт.
        $form = new LoginForm();
        $form->get('redirect_url')->setValue($redirectUrl);

        // Храним статус входа на сайт.
        $isLoginError = false;

        // Проверяем, заполнил ли пользователь форму
        if ($this->getRequest()->isPost()) {

            // Заполняем форму POST-данными
            $data = $this->params()->fromPost();

            $form->setData($data);

            // Валидируем форму
            if($form->isValid()) {

                // Получаем отфильтрованные и валидированные данные
                $data = $form->getData();

                // Выполняем попытку входа в систему.
                $result = $this->authManager->login($data['email'],
                        $data['password'], $data['remember_me']);

                // Проверяем результат.
                if ($result->getCode() == Result::SUCCESS) {

                    // Получаем URL перенаправления.
                    $redirectUrl = $this->params()->fromPost('redirect_url', '');

                    if (!empty($redirectUrl)) {
                        // Проверка ниже нужна для предотвращения возможных атак перенаправления
                        // (когда кто-то пытается перенаправить пользователя на другой домен).
                        $uri = new Uri($redirectUrl);
                        if (!$uri->isValid() || $uri->getHost()!=null)
                            throw new \Exception('Incorrect redirect URL: ' . $redirectUrl);
                    }

                    // Если задан URL перенаправления, перенаправляем на него пользователя;
                    // иначе перенаправляем пользователя на страницу Home.
                    if(empty($redirectUrl)) {
                        return $this->redirect()->toRoute('home');
                    } else {
                        $this->redirect()->toUrl($redirectUrl);
                    }
                } else {
                    $isLoginError = true;
                }
            } else {
                $isLoginError = true;
            }
        }

        return new ViewModel([
            'form' => $form,
            'isLoginError' => $isLoginError,
            'redirectUrl' => $redirectUrl
        ]);
    }

    /**
     * Действие "logout" выполняет операцию выхода из аккаунта.
     */
    public function logoutAction()
    {
        $this->authManager->logout();

        return $this->redirect()->toRoute('login');
    }
}
~~~

Метод `loginAction()` принимает GET-параметр `redirectUrl`. "URL перенаправления" - это удобный
механизм, работающий в паре с *фильтром доступа*, который мы опишем позже в этой главе. Когда
посетитель сайта пытается перейти на веб-страницу, доступ к которой фильтр доступа запрещает
для не вошедших на сайт пользователей, он перенаправляется на страницу "Login" путем передачи URL
исходной страницы в качестве "URL перенаправления". После того, как пользователь войдет на сайт,
он автоматически будет перенаправлен обратно на исходную страницу. Такой подход значительно
улучшает впечатление от сайта.

### Добавление шаблона представления для страницы Login

Шаблон представления (файл *.phtml*) для нашей страницы *Login* выглядит так:

~~~php
<?php
$this->headTitle('Sign in');

$this->mainMenu()->setActiveItemId('login');

$form->get('email')->setAttributes([
    'class'=>'form-control',
    'placeholder'=>'Email address',
    'required' => true,
    'autofocus' => true
    ])
    ->setLabelAttributes([
        'class' => 'sr-only'
    ]);

$form->get('password')->setAttributes([
    'class'=>'form-control',
    'placeholder'=>'Password',
    'required' => true,
    ])
    ->setLabelAttributes([
        'class' => 'sr-only'
    ]);
?>

<div class="row">
    <div class="col-md-offset-4 col-md-3">
        <form class="form-signin" method="post">
            <h2 class="form-signin-heading">Please sign in</h2>
            <?php if ($isLoginError): ?>
            <div class="alert alert-warning" role="alert">
                Incorrect login and/or password.
                <a href="<?= $this->url('reset-password') ?>">Forgot password?</a>
            </div>
            <?php endif; ?>
            <?= $this->formLabel($form->get('email')); ?>
            <?= $this->formElement($form->get('email')); ?>
            <?= $this->formLabel($form->get('password')); ?>
            <?= $this->formElement($form->get('password')); ?>
            <div class="checkbox">
                <label>
                    <?= $this->formElement($form->get('remember_me')); ?> Remember me
                </label>
            </div>
            <?= $this->formElement($form->get('redirect_url')); ?>
            <?= $this->formElement($form->get('csrf')) ?>
            <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
        </form>
    </div>
</div>
~~~

T> Шаблон представления использует шаблон страницы *Sign In*, предоставляемый CSS-фреймворком Bootstrap.
T> Исходный шаблон можно найти [здесь](https://getbootstrap.com/examples/signin/).

### Добавление сервиса AuthManager

`AuthController` работает в паре с сервисом `AuthManager`. Основная бизнес-логика аутентификации
реализована в сервисе. Давайте детально рассмотрим `AuthManager`.

Так, у сервиса `AuthManager` есть следующие отвечающие за аутентификацию методы:

  * метод `login()`
  * метод `logout()`.

Метод `login()` (см. ниже) использует предоставляемый Laminas `AuthenticationService` и написанный нами
ранее `AuthAdapter` для выполнения аутентификации пользователя. Этот метод также принимает аргумент
`AuthAdapter`, который позволяет продлить время "жизни" cookie-данных сессии на срок до 30 дней.

~~~php
/**
 * Совершает попытку входа на сайт. Если значение аргумента $rememberMe равно true, сессия
 * будет длиться один месяц (иначе срок действия сессии истечет через один час).
 */
public function login($email, $password, $rememberMe)
{
    // Проверяем, вошел ли пользователь в систему. Если так, не позволяем
    // ему войти дважды.
    if ($this->authService->getIdentity()!=null) {
        throw new \Exception('Already logged in');
    }

    // Аутентифицируем пользователя.
    $authAdapter = $this->authService->getAdapter();
    $authAdapter->setEmail($email);
    $authAdapter->setPassword($password);
    $result = $this->authService->authenticate();

    // Если пользователь хочет, чтобы его "запомнили", мы зададим срок действия
    // сессии, равный одному месяцу. По умолчанию, срок действия сессии истекает
    // через 1 час (как указано в файле config/global.php).
    if ($result->getCode()==Result::SUCCESS && $rememberMe) {
        // Срок действия cookie сессии закончится через 1 месяц (30 дней).
        $this->sessionManager->rememberMe(60*60*24*30);
    }

    return $result;
}
~~~

Метод `logout()` удаляет личность пользователя из сессии, тем самым пользователь становится неавторизованным.

~~~php
/**
 * Осуществляет выход пользователя из системы.
 */
public function logout()
{
    // Позволяет выйти из учетной записи только авторизованному пользователю.
    if ($this->authService->getIdentity()==null) {
        throw new \Exception('The user is not logged in');
    }

    // Удаляем личность из сессии.
    $this->authService->clearIdentity();
}
~~~

## Фильтрация доступа

Последнее, что мы реализуем в модуле `User` - это *фильтр доступа*. Он будет использоваться
для запрета не вошедшим на сайт пользователям доступа к определенным страницам.

Алгоритм работы фильтра доступа описан ниже:

  * Когда кто-то пытается обратиться к веб-странице, мы проверяем, во-первых, ключ конфигурации приложения
    `access_filter`, а во-вторых, то, доступна ли эта страница всем или только вошедшим на сайт пользователям.

  * Если страница доступна всем, позволяем посетителю сайта перейти на страницу.

  * Если страница доступна только вошедшим на сайт пользователям, проверяем, вошел ли пользователь в систему.

  * Если пользователь не вошел на сайт, перенаправляем его на страницу *Login* и предлагаем войти в аккаунт.

  * После того как пользователь войдет на сайт, перенаправляем его обратно на исходную страницу.

Фильтр доступа предназначен для работы в двух режимах: ограничительном (по умолчанию) и разрешающем. В ограничительном режиме
фильтр запрещает не вошедшим пользователям доступ ко всем страницам, которые не указаны под ключом `access_filter`.

Ключ конфигурации `access_filter`, находящийся в файле *module.config.php*, будет использоваться фильтром доступа. Он будет
содержать список имен контроллеров и действий и для каждого действия либо разрешать доступ к странице всем, либо разрешать
доступ к странице только для вошедших пользователей. Пример структуры этого ключа представлен ниже:

~~~php
// Ключ 'access_filter' используется модулем User, чтобы разрешить или запретить доступ к
// определенным действиям контроллера для не вошедших на сайт пользователей.
'access_filter' => [
    'options' => [
        // Фильтр доступа может работать в 'ограничительном' (рекомендуется) или 'разрешающем'
        // режиме. В ограничительном режиме все действия контроллера должны быть явно перечислены
        // под ключом конфигурации 'access_filter', а доступ к любому не перечисленному действию
        // для неавторизованных пользователей запрещен. В разрешающем режиме, даже если действие не
        // указано под ключом 'access_filter', доступ к нему разрешен для всех (даже для
        // неавторизованных пользователей. Рекомендуется использовать более безопасный ограничительный режим.
        'mode' => 'restrictive'
    ],
    'controllers' => [
        Controller\IndexController::class => [
            // Позволяем всем обращаться к действиям "index" и "about".
            ['actions' => ['index', 'about'], 'allow' => '*'],
            // Позволяем вошедшим на сайт пользователям обращаться к действию "settings".
            ['actions' => ['settings'], 'allow' => '@']
        ],
    ]
],
~~~

Под ключом `access_filter` находятся два подключа:

  * Ключ `options`, который можно использовать для определения режима, в котором функционирует фильтр ("ограничительный" или "разрешающий").
  * Ключ `controllers` содержит список контроллеров и их действий с указанием типа доступа для каждого действия. Звездочка (*) означает, что
    доступ к веб-странице есть у всех. Символ "коммерческое at" (@) означает, что доступ к странице есть только у авторизованных пользователей.

I> Реализация фильтра доступа очень проста. Он не может, например, разрешать доступ в зависимости от логина или роли пользователя. Однако,
I> вы легко можете изменять и расширять фильтр как пожелаете. Если вы планируете ввести управление доступом на основе ролей (Role Based
I> Access Control - RBAC), обратитесь к главе [Контроль доступа на основе ролей](#roles).

### Добавление обработчика события Dispatch

Для реализации фильтрации доступа мы используем обработчик событий. Вы могли ознакомиться с обработкой событий в
главе [Создание нового модуля](#modules)

В частности, мы будем обрабатывать событие *Dispatch*. Оно вызывается после события *Route*, когда контроллер
и действие уже определены. Чтобы реализовать обработчик, мы изменим файл *Module.php* модуля *User* следующим
образом:

~~~php
<?php
namespace User;

use Laminas\Mvc\MvcEvent;
use Laminas\Mvc\Controller\AbstractActionController;
use User\Controller\AuthController;
use User\Service\AuthManager;

class Module
{
    /**
     * Этот метод возвращает путь к файлу module.config.php file.
     */
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     * Этот метод вызывается после завершения самозагрузки MVC и позволяет
     * регистрировать обработчики событий.
     */
    public function onBootstrap(MvcEvent $event)
    {
        // Получаем менеджер событий.
        $eventManager = $event->getApplication()->getEventManager();
        $sharedEventManager = $eventManager->getSharedManager();
        // Регистрируем метод-обработчик.
        $sharedEventManager->attach(AbstractActionController::class,
                MvcEvent::EVENT_DISPATCH, [$this, 'onDispatch'], 100);
    }

    /**
     * Метод-обработчик для события 'Dispatch'. Мы обрабатываем событие Dispatch
     * для вызова фильтра доступа. Фильтр доступа позволяет определить,
     * может ли пользователь просматривать страницу. Если пользователь не
     * авторизован, и у него нет прав для просмотра, мы перенаправляем его
     * на страницу входа на сайт.
     */
    public function onDispatch(MvcEvent $event)
    {
        // Получаем контроллер и действие, которому был отправлен HTTP-запрос.
        $controller = $event->getTarget();
        $controllerName = $event->getRouteMatch()->getParam('controller', null);
        $actionName = $event->getRouteMatch()->getParam('action', null);

        // Конвертируем имя действия с пунктирами в имя в верблюжьем регистре.
        $actionName = str_replace('-', '', lcfirst(ucwords($actionName, '-')));

        // Получаем экземпляр сервиса AuthManager.
        $authManager = $event->getApplication()->getServiceManager()->get(AuthManager::class);

        // Выполняем фильтр доступа для каждого контроллера кроме AuthController
        // (чтобы избежать бесконечного перенаправления).
        if ($controllerName!=AuthController::class &&
            !$authManager->filterAccess($controllerName, $actionName)) {

            // Запоминаем URL страницы, к которой пытался обратиться пользователь. Мы перенаправим пользователя
            // на этот URL после успешной авторизации.
            $uri = $event->getApplication()->getRequest()->getUri();
            // Делаем URL относительным (убираем схему, информацию о пользователе, имя хоста и порт),
            // чтобы избежать перенаправления на другой домен недоброжелателем.
            $uri->setScheme(null)
                ->setHost(null)
                ->setPort(null)
                ->setUserInfo(null);
            $redirectUrl = $uri->toString();

            // Перенаправляем пользователя на страницу "Login".
            return $controller->redirect()->toRoute('login', [],
                    ['query'=>['redirectUrl'=>$redirectUrl]]);
        }
    }
}
~~~

### Реализация алгоритма фильтрации доступа

Обработчик событий `onDispatch()` вызывает метод `filterAccess()` сервиса `AuthManager`, чтобы определить,
можно ли пользователю просматривать страницу. Код метода `filterAccess()` представлен ниже:

~~~php
/**
 * Это простой фильтр контроля доступа. Он может ограничить доступ к определенным страницам
 * для неавторизованных пользователей.
 *
 * Этот метод использует ключ 'access_filter' в файле конфигурации и определяет,
 * разрешен ли текущему посетителю доступ к заданному действию контроллера. Если
 * разрешен, он возвращает true, иначе - false.
 */
public function filterAccess($controllerName, $actionName)
{
    // Определяем режим - 'ограничительный' (по умолчанию) или 'разрешающий'. В ограничительном
    // режиме все действия контроллеров должны быть явно перечислены под ключом конфигурации 'access_filter',
    // и для неавторизованных пользователей доступ будет запрещен к любому не указанному в этом списке действию.
    // В разрешающем режиме, если действие не указано под ключом 'access_filter' доступ к нему все равно
    // разрешен для всех (даже для неавторизованных пользователей). Рекомендуется использовать более безопасный
    // ограничительный режим.
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
                    return true; // Все могут просматривать страницу.
                else if ($allow=='@' && $this->authService->hasIdentity()) {
                    return true; // Только аутентифицированный пользователь может просматривать страницу.
                } else {
                    return false; // В доступе отказано.
                }
            }
        }
    }

    // В ограничительном режиме мы запрещаем неавторизованным пользователям доступ к любому действию,
    // не перечисленному под ключом 'access_filter' (из соображений безопасности).
    if ($mode=='restrictive' && !$this->authService->hasIdentity())
        return false;

    // Разрешаем доступ к этой странице.
    return true;
}
~~~

### Тестирование фильтра доступа

Для проверки работы фильтра доступа попробуйте открыть страницу "http://localhost/users" или "http://localhost/settings", когда вы не авторизованы.
Фильтр доступа перенаправит вас на страницу *Login*. Однако, вы без проблем можете перейти на страницу "http://localhost/about" - она открыта для всех.

## Плагин контроллера Identity и помощник представления Identity

И последнее, что мы обсудим - как проверить, авторизован ли пользовать, и как получать его
личность. Это можно сделать с помощью плагина контроллера `Identity` и помощника представления `Identity`.

I> Для использования плагина `Identity` вам нужно установить пакет `laminas/laminas-mvc-plugins` с помощью Composer следующей командой:
I>
I> `php composer.phar require laminas/laminas-mvc-plugins`

В методе действия контроллера вы можете проверять, вошел ли пользователь в систему, следующим образом:

~~~php
if ($this->identity()!=null) {
    // Пользователь вошел на свой аккаунт.

    // Извлекаем личность пользователя.
    $userEmail = $this->identity();
}
~~~

В шаблоне представления для этой цели можно использовать помощник представления `Identity`:

~~~php
// Выводим личность пользователя.
<?= $this->escapeHtml($this->identity()) ?>
~~~

## Выводы

В этой главе мы узнали об управлении пользователями, аутентификации пользователей и фильтрации доступа.

Управление пользователями подразумевает предоставление UI для добавления, изменения и просмотра пользователей,
а также смены их паролей.

Аутентификация - это процесс, при котором пользователь предъявляет свои логин и пароль, а вы решаете, корректны
ли эти данные. Laminas предоставляет специальный сервис `AuthenticationService`, который можно использовать для этих
целей, однако сначала вам необходимо реализовать адаптер аутентификации.

Фильтрация доступа позволяет разрешать доступ к определенным страницам только авторизованным пользователям. Эту
фильтрацию можно реализовать с помощью обработчика событий.
