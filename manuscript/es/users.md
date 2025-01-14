# Manejo de usuarios, autenticación y filtrado de acceso {#users}

La mayoría de los sitios en internet permiten a sus visitantes registrarse y crear un perfil. Luego,
el visitante puede iniciar sesión y tener una experiencia personalizada. Por ejemplo, en los sitios
webs de comercio electrónico un usuario registrado puede comprar bienes, manejar su carro de compras
y pagar con la tarjeta de crédito.

En este capítulo, aprenderemos a implementar la autenticación usando un usuario y una contraseña en un
sitio web desarrollado con Laminas. Veremos como manejar usuarios (agregar, editar, ver y cambiar/reiniciar la contraseña)
en nuestra aplicación web y guardar la contraseña de usuario en la base de datos con seguridad. Aprenderemos también
como implementar filtros de acceso y permitir que ciertas páginas sean vistas solamente por usuarios autorizados.

Como ya sabemos bastante sobre Laminas por la lectura de los capítulos anteriores en este omitiremos
algunas cosas obvias y nos concentraremos solamente sobre los *conceptos*. Es recomendable que revisemos
el bundled de ejemplo *Demostración de Usuario* que esta en este libro, el ejemplo es un sitio web completo que podemos ejecutar y ver
con todos los elementos en acción.

Los componentes de Laminas que revisamos en este capítulo son:

|--------------------------------|---------------------------------------------------------------|
| *Componente*                   | *Descripción*                                                 |
|--------------------------------|---------------------------------------------------------------|
| `Laminas\Authentication`          | Provee la característica de autenticación de usuario.         |
|--------------------------------|---------------------------------------------------------------|
| `Laminas\Crypt`                   | Provee la funcionalidad de codificación de la contraseña.     |
|--------------------------------|---------------------------------------------------------------|
| `Laminas\Math`                    | Provee la funcionalidad para generar tokens seguros y aleatorios. |
|--------------------------------|---------------------------------------------------------------|

## Obtener el ejemplo de demostración de usuario desde GitHub

Para la demostración, en este capítulo, crearemos un sitio web realista, *User Demo*,
que muestra como:

  * Crear un nuevo modulo llamado *User*. Este modulo contendrá la funcionalidad para la autenticación del usuario
    y el manejo de usuarios.
  * Crear una entidad `User`.
  * Implementar un almacenamiento de usuario en una base de datos de una manera segura.
  * Implementar la autenticación de usuario (con un usuario y una contraseña).
  * Implementar el filtro de acceso para limitar ciertas páginas solo a usuarios autenticados.
  * Implementar una interfaz de manejo de usuarios que permita agregar, editar, ver y cambiar la contraseña de un usuario.
  * Construir un menú con elementos diferenciados basado en si un usuario ha iniciado sesión o no.

Para descargar la aplicación de demostración debemos visitar [esta página](https://github.com/olegkrivtsov/using-laminas-book-samples)
y hacer clic en el botón *Clonar o Descargar* para obtener el código en un archivo ZIP.
Cuando la descarga esta completada desempaquetamos el archivo en algún directorio.

Luego navegamos hasta el directorio `userdemo` que contiene el código fuente
de la aplicación web de demostración de usuarios:

~~~text
/using-laminas-book-samples
  /userdemo
  ...
~~~

El *Demo de usuario* es un sitio web que se puede instalar en nuestra computadora.

I> Las instrucciones detalladas de como instalar el *Demo de usuario* están en el
I> archivo *README.md* ubicado en el carpeta de ejemplo.

## Crear el módulo del usuario

En el *Demo de usuario* de ejemplo, creamos un nuevo modulo llamado *User* y agregamos toda la funcionalidad
relacionada con el manejo de usuario y su autenticación a este modulo. Si nos resulta nuevo
el concepto de módulos podemos revisar el capítulo [Crear un nuevo modulo](#modules).

El módulo *User* tendrá muy pocas dependencias de otros módulos
del sitio web. La idea detrás del modulo *User* es darnos una unidad reusable
que podamos usar en nuestra propia aplicación web sin ningún cambio o con pocos.

T> Idealmente debemos poder usar el modulo *User* en nuestro sitio web sin ningún cambio. Pero, en la vida real
T> probablemente tendremos que agregar algunos campos a la tabla `user`, modificar el flujo de creación de un usuario
T> modificar el algoritmo de filtro de acceso. En este caso, tendremos que adaptar el código de el modulo *User*
T> para ajustarlo a nuestras necesidades.

El modulo *User* tendrá la siguiente estructura (ver figura 16.1):

![Figura 16.1 Estructura del Módulo de Usuario](../en/images/users/user_module_structure.png)

Vamos a describir rápidamente que clases tenemos dentro de nuestro directorio module.

Tenemos dos controladores:

  * El *UserController* contendrá la funcionalidad para el manejo de usuario (añadir, editar, cambio de contraseña, etc).
  * El *AuthController* implementará la funcionalidad de autenticación de usuario (login/logout).

Tendremos una entidad de Doctrine:

  * La entidad *User* será usada para almacenar información sobre el usuario en la base de datos (correo electrónico, nombre completo, contraseña, etc).

Se usan cuatro formularios para capturar la información:

  * El *LoginForm* se usa para capturar la información de autenticación (nombre de usuario y contraseña).
  * El *PasswordChangeForm* se usa para capturar la información de cambio o reinicio de la contraseña de usuario.
  * El *PasswordResetForm* se usa para captura el correo electrónico de la persona que olvido su contraseña y desea reiniciarla.
  * El *UserForm* se usa para capturar la información del usuario (correo electrónico, nombre completo, contraseña, etc).

Tendremos varios servicios:

  * El servicio *AuthAdapter* que implementa el algoritmo de autenticación. Este
    revisa si el nombre de usuario (dirección de correo electrónico) y la contraseña
    son correctas. Para ejecutar este proceso recupera la información de la base
    de datos.
  * El servicio *AuthManager* ejecuta propiamente la autenticación (login/logout). Este además implementa
    el filtro de acceso, permitiendo o denegando el acceso a usuarios no autenticados sobre determinadas páginas web.
  * El *UserManager* contendrá la lógica del negocio para el manejo de usuarios (añadir, editar, cambiar la contraseña).

Más controladores y servicios serán instanciados con factories. Podemos encontrar las clases factory en el
subdirectorio *Factory*.

Dentro del directorio *view*, tendremos varias plantillas de vistas que imprimen el código HTML de las páginas webs
presentes en la interfaz de usuario de nuestro módulo.

Como es usual, dentro del directorio *config*, tendremos el archivo *module.config.php*
que contendrá las rutas y el registro para nuestros controladores y servicios. Este
también contendrá la llave *access_filter* que define que páginas web del modulo serán accesibles
por usuarios no autenticados (esta llave es leída por el servicio *AuthManager*).

Como puedes ver, el modulo *User* es un típico modulo de Laminas con la estructura propia de un patron MVC.

## Configurar la base de datos

Necesitamos crear una base de datos de ejemplo que llamaremos "userdemo". La base de datos tendrá una sola
tabla llamada `user` en al que se guardará la información asociada con los usuarios de nuestro sitio web (ver figura 16.2).

![Figura 16.2 User table](../en/images/users/userdemo_database_schema.png)

La tabla `user` contiene los siguientes campos:

  * El `id` es un campo entero auto-incremental (llave primaria).
  * El `email` es una campo tipo cadena de caracteres que contiene el correo
    electrónico del usuario. Cada usuario tendrá un correo electrónico único así
    que este campo es también único.
  * La cadena de caracteres `full_name` contiene el estatus de usuario ("active" o "retired"). Los usuarios "retired" no
    pueden iniciar sesión.
  * El `date_created` contiene el día y la hora cuando se creo el usuario.
  * Los campos `pwd_reset_token` y `pwd_reset_token_creation_date` se usan para el reinicio de la contraseña (cuando un
    usuario olvido su contraseña y necesita reiniciarla).

T> Para tu propio sitio web probablemente necesites agregar más campos a la tabla `user`.
T> En este ejemplo, solo definimos un conjunto mínimo de campos.

Podemos crear la tabla `user` con la siguiente sentencia SQL:

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

Podemos encontrar una migración, que crea la tabla `user`, en la aplicación de ejemplo "User Demo".

T> Si eres nuevo en las migraciones revisa el capítulo [Migración de bases de datos](#migrations).

## Implementar una entidad

El ejemplo *User Demo* usa el ORM Doctrine para manejar la base de datos. Nosotros ya hemos aprendido a usar
Doctrine en [Manejo de base de datos con ORM Doctrine](#doctrine).

Para almacenar la información sobre usuarios en la base de datos crearemos la entidad `User`. La entidad `User` representa
a la tabla `user`. Es una típica clase entidad de Doctrine.

Se crea el archivo *User.php* dentro del directorio *Entity* bajo el directorio principal del módulo y se coloca
el siguiente código dentro:

~~~php
<?php
namespace User\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * This class represents a registered user.
 * @ORM\Entity()
 * @ORM\Table(name="user")
 */
class User
{
    // User status constants.
    const STATUS_ACTIVE       = 1; // Active user.
    const STATUS_RETIRED      = 2; // Retired user.

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
     * Returns user ID.
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets user ID.
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Returns email.
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Sets email.
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Returns full name.
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * Sets full name.
     * @param string $fullName
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;
    }

    /**
     * Returns status.
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Returns possible statuses as array.
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
     * Returns user status as string.
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
     * Sets status.
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Returns password.
     * @return string
     */
    public function getPassword()
    {
       return $this->password;
    }

    /**
     * Sets password.
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Returns the date of user creation.
     * @return string
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Sets the date when this user was created.
     * @param string $dateCreated
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
    }

    /**
     * Returns password reset token.
     * @return string
     */
    public function getResetPasswordToken()
    {
        return $this->passwordResetToken;
    }

    /**
     * Sets password reset token.
     * @param string $token
     */
    public function setPasswordResetToken($token)
    {
        $this->passwordResetToken = $token;
    }

    /**
     * Returns password reset token's creation date.
     * @return string
     */
    public function getPasswordResetTokenCreationDate()
    {
        return $this->passwordResetTokenCreationDate;
    }

    /**
     * Sets password reset token's creation date.
     * @param string $date
     */
    public function setPasswordResetTokenCreationDate($date)
    {
        $this->passwordResetTokenCreationDate = $date;
    }
}
~~~

Como podemos observar en el código de arriba, la entidad *User* es una típica entidad de Doctrine
que tiene propiedades anotadas, métodos getter y setter para recuperar o asignar estas propiedades.

## Agregar el UserController

La clase `UserController` contendrá varios métodos diseñados para proveer una interface administrativa
para el manejo de usuarios registrados. Esta tendrá las siguientes acciones:

  * La acción `indexAction()` mostrará una página que contiene la lista de los usuarios (ver figura 16.3).
    Escribe "http://localhost/users" en la barra de navegación del navegador web para ver esta página.
  * `addAction()` mostrará una página que permite crear un nuevo usuario (ver figura 16.4).
    Escribe "http://localhost/users/add" en la barra de navegación del navegador web para ver esta página.
  * La acción `editAction` mostrara una página para actualizar un usuario existente (ver figura 16.5).
    Escribe "http://localhost/users/edit/&lt;id&gt;" en la barra de navegación del navegador web para ver esta página.
  * `viewAction` permitirá ver una usuario existente (ver figura 16.6).
    Escribe "http://localhost/users/view/&lt;id&gt;" en la barra de navegación del navegador web para ver esta página.
  * La acción `changePasswordAction()` dará al administrador la capacidad de cambiar la contraseña
    de un usuario existente (ver figura 16.7).
    Escribe "http://localhost/users/changePassword/&lt;id&gt;" en la barra de navegación del navegador web para ver esta página.
  * La acción `resetPasswordAction()` permitirá al usuario reiniciar su contraseña (ver figura 16.8).
    Escribe "http://localhost/reset-password" en la barra de navegación del navegador web para ver esta página.

![Figura 16.3 Página que permite ver la lista de usuarios](../en/images/users/users_page.png)

![Figura 16.4 Agregar un nuevo usuario](../en/images/users/add_user_page.png)

![Figura 16.5 Editar un usuario que ya existe](../en/images/users/edit_user_page.png)

![Figura 16.6 Perfil del usuario](../en/images/users/view_user_page.png)

![Figura 16.7 Cambiar la contraseña del usuario](../en/images/users/change_password_page.png)

![Figura 16.8 Página para reiniciar la contraseña](../en/images/users/reset_password_page.png)

La clase controlador `UserController` está diseñada para ser tan pequeña como sea posible.
Esta contiene solo el código responsable de revisar la información entrante, instanciar los
modelos necesarios, pasar los datos entrantes al modelo y regresar la información saliente
para mostrar en la plantilla de vista. Porque es una típica clase controlador y porque podemos
ver su código completo en el ejemplo *User Demo* no la describiremos aquí con más detalle.

## Agregar el Servicio UserManager

El `UserController` trabaja en paralelo con el servicio *UserManager*, que contiene toda la lógica de negocio relacionada con el manejo de usuario.
El servicio permite al administrador crear y actualizar usuarios, cambiar y reiniciar sus contraseñas. Describimos algunas partes de él
con más detalles omitiendo otras partes obvias (siempre podemos ver el código completo en el ejemplo *User Demo*).

### Crear un nuevo usuario y guardar su contraseña encriptada

El método `addUser` del `UserManager` permite agregar un nuevo usuario. Su aspecto es este:

~~~php
/**
 * This method adds a new user.
 */
public function addUser($data)
{
    // Do not allow several users with the same email address.
    if($this->checkUserExists($data['email'])) {
        throw new \Exception("User with email address " .
                    $data['$email'] . " already exists");
    }

    // Create new User entity.
    $user = new User();
    $user->setEmail($data['email']);
    $user->setFullName($data['full_name']);

    // Encrypt password and store the password in encrypted state.
    $bcrypt = new Bcrypt();
    $passwordHash = $bcrypt->create($data['password']);
    $user->setPassword($passwordHash);

    $user->setStatus($data['status']);

    $currentDate = date('Y-m-d H:i:s');
    $user->setDateCreated($currentDate);

    // Add the entity to the entity manager.
    $this->entityManager->persist($user);

    // Apply changes to database.
    $this->entityManager->flush();

    return $user;
}
~~~

Como podemos ver con este método primero revisamos si otro usuario con la misma dirección
de correo electrónico ya existe (linea 7) e impedimos su creación mediante el lanzamiento de una excepción.

Si el usuario, es decir el correo electrónico, no existe creamos una nueva entidad `User` (linea 13) y colocamos
sus propiedades adecuadamente.

Lo que es interesante aquí es como guardamos la contraseña del usuario en la base de datos. Por razones de
seguridad no guardamos la contraseña tal cual sino que calculamos un hash de ella con la clase `Bcrypt` que esta
en `Laminas\Crypt` (lines 18-19), un componente de Laminas Framework.

T> Podemos instalar `Laminas\Crypt` con el siguiente comando:
T>
T>   `php composer.phar require laminas/laminas-crypt`
T>
T> El componente `Laminas\Crypt` también necesita que tengamos instalada la extensión de PHP `mcrypt`.

W> El algoritmo *Bcrypt* es un algoritmo de hashing ampliamente usado y recomendado por la comunidad de seguridad
W> para guardar la contraseña del usuario.
W> Cifrar la contraseña con `Bcrypt` se considera hoy en día como seguro. Algunos desarrolladores aun cifran
W> las contraseñas con MD5 o SHA1 con Sal, pero esto ya no se considera seguro (los hashes MD5 y SHA1 pueden ser descifrados).

### Validar la contraseña encriptada

Cuando un usuario inicia sesión necesitamos revisar si el hash de la contraseña almacenado en la base de datos
es el mismo que el hash generado con la contraseña introducida por el visitante. Podemos hacer esto con la ayuda
del método `verify()` provisto por la clase `Bcrypt`, de la siguiente manera:

~~~php
/**
 * Checks that the given password is correct.
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

### Crear el usuario administrador

El otro elemento importante que hay que notar en el `UserManager` es como creamos el usuarios Admin.

I> El usuario Admin es un usuario inicial que se crea automáticamente cuando no existen usuarios
I> en la base de datos permitiendo que iniciemos sesión por primera vez.

~~~php
/**
 * This method checks if at least one user presents, and if not, creates
 * 'Admin' user with email 'admin@example.com' and password 'Secur1ty'.
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

Le colocamos al usuario Admin el correo electrónico `admin@example.com` y la contraseña `Secur1ty`,
así podemos iniciar sesión por primera vez con estas credenciales.

### Reiniciar la contraseña

Algunos usuarios olvidan su contraseña. Si esto ocurre necesitamos permitir que el usuario *reiniciar* su contraseña
de forma segura. El reinicio de la contraseña funciona de la siguiente manera:

  * Un *token de reinicio de contraseña* aleatorio se genera y guarda en la base de datos.
  * El token de reinicio de contraseña se envía al correo electrónico del usuario como parte de un mensaje.
  * El usuario revisa su buzón de entrada y hace clic en el enlace de reinicio de contraseña que esta en el mensaje.
  * El sitio web valida el token de reinicio de contraseña y revisa si este ha vencido.
  * El usuario es dirigido al formulario que le permite colocar una nueva contraseña.

I> Generalmente no guardamos el *token* «crudo» de reinicio de contraseña en la
I> base de datos. En su lugar guardamos un *hash* del *token*. Esto se hace
I> por razones de seguridad. Incluso si en un ataque se roba la base de datos
I> ellos no serán capaces de reiniciar la contraseña de los usuarios.

El algoritmo que genera el token de reinicio de contraseña se implementa dentro del método
`generatePassworkResetToken()` del `UserManager`. Para generar una cadena de caracteres aleatoria
usamos la clase `Rand` provista por el componente `Laminas\Math`.

~~~php
/**
 * Generates a password reset token for the user. This token is then stored in database and
 * sent to the user's E-mail address. When the user clicks the link in E-mail message, he is
 * directed to the Set Password page.
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

I> Configurar un servidor de correo electrónico para nuestro sitio web consiste
I> generalmente en contratar algún servicio de correo como [SendGrid](https://sendgrid.com/)
I> o [Amazon SES](https://aws.amazon.com/en/ses).

La validación del token de reinicio de contraseña está implementado dentro del
método `validatePasswordResetToken()`. Revisamos que el *hash* del token es el
mismo que el guardado en base de datos y que el token no ha vencido (el token
vence un día después de su creación).

~~~php
/**
 * Checks whether the given password reset token is a valid one.
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

Finalmente `setPasswordByToken()` permite colocar una nueva contraseña para el usuario.

~~~php
/**
 * This method sets new password by password reset token.
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

## Implementar la autenticación del usuario

La *autenticación* es el proceso mediante el cual un usuario provee su nombre de usuario y contraseña y se revisa
si estas credenciales son correctas. La autenticación típicamente significa que se revisa en la base de datos el
nombre de usuario dado y si existe revisamos si el hash calculado para la contraseña dada coincide con el hash
de la contraseña guardado en la base de datos.

I> Normalmente no guardamos contraseñas en crudo dentro la base de datos. En su lugar, guardamos un *hash* de
I> la contraseña. Se hace así por razones de seguridad.

Una vez que el algoritmo de autenticación determina que el usuario y la contraseña son correctos regresa una *identity* de usuario,
un identificador (ID) único para el usuario. La *identity* es normalmente guardada en la sesión, de esta manera el usuario no necesita
autenticarse con cada petición HTTP.

En Laminas hay un componente especial que permite implementar la autenticación del usuario, `Laminas\Authentication`.
Podemos instalar este componente con Composer escribiendo el siguiente comando:

```
php composer.phar require laminas/laminas-authentication
```

T> Para que la autenticación funcione necesitamos tener instalado el componente `Laminas\Session` y el administrador de sesión configurado.
T> La información sobre como hacer esto esta en el capítulo [Trabajar con sesiones](#session).

### AuthenticationService

El componente `Laminas\Authentication` provee una clase de servicio especial llamada `AuthenticationService` que
se encuentra en el espacio de nombres `Laminas\Authentication`. Los métodos más útiles de este servicio se muestran
en la tabla 16.1 de abajo:

{title="Table 16.1. Métodos de la clase AuthenticationService"}
|--------------------------------|---------------------------------------------------------------|
| *Método*                       | *Descripción*                                                 |
|--------------------------------|---------------------------------------------------------------|
| `authenticate()`               | Ejecuta la autenticación de usuario usando el adaptador.      |
|--------------------------------|---------------------------------------------------------------|
| `getAdapter()`                 | Trae el adaptador de autenticación.                           |
|--------------------------------|---------------------------------------------------------------|
| `setAdapter()`                 | Coloca el adaptador de autenticación que implementa el actual algoritmo de autenticación. |
|--------------------------------|---------------------------------------------------------------|
| `getStorage()`                 | Regresa el administrador de almacenamiento.                       |
|--------------------------------|---------------------------------------------------------------|
| `setStorage()`                 | Coloca el administrador de almacenamiento.                        |
|--------------------------------|---------------------------------------------------------------|
| `hasIdentity()`                | Regresa `true` si la identidad de usuario ya ha sido guardada en la sesión. |
|--------------------------------|---------------------------------------------------------------|
| `getIdentity()`                | Recupera la identidad de usuario desde la sesión.             |
|--------------------------------|---------------------------------------------------------------|
| `clearIdentity()`              | Remueve la identidad de usuario de la sesión.                 |
|--------------------------------|---------------------------------------------------------------|

Como podemos ver al principio de la tabla, podemos usar el método `authenticate()` para ejecutar la autenticación.
Además, podemos usar los métodos `hasIdentity()`, `getIdentity()` y `clearIdentity()` para respectivamente probar,
recuperar y limpiar la identidad de usuario.

Sin embargo el servicio `AuthenticationService` es muy 'genérico', este no conoce nada sobre como
comparar el nombre de usuario y la contraseña contra la base de datos. Tampoco sabe nada sobre como
guardar la identidad de usuario en la sesión. Este diseño permite implementar el algoritmo de autenticación
y el almacenamiento apropiado.

El componente `Laminas\Authentication` provee varios *adaptadores de autenticación* que implementan
algunos algoritmos de autenticación estándar (ver figura 16.9) y varios *administradores de almacenamiento*
que permiten guardar y recuperar la identidad de usuarios (ver figura 16.10).

![Figura 16.9 Adaptadores estandar de autenticación](../en/images/users/std_auth_adapters.png)

![Figura 16.10 Administradores estandar de almacenamiento](../en/images/users/std_auth_storage_handlers.png)

Para nuestros propósitos podemos usar el administrador de almacenamiento `Session` sin necesidad de ningún
cambio en el código. Sin embargo, el adaptador de autenticación estándar no es apropiado para nosotros
por este razón usaremos Doctrine ORM. Tenemos que escribir nuestro propio adaptador. Felizmente, esto
es bastante simple de hacer.

### Escribir el adaptador de autenticación

Un adaptador de autenticación debe implementar a la interfaz `AdapterInterface`, la que tiene solo
el método `authenticate()`. Este método debería revisar el correo electrónico del usuario y la contraseña
contra la base de datos. Lo haremos de la siguiente manera:

  * Buscar el usuario con el `email` (pensamos en el correo electrónico como el nombre de usuario).
  * Si el usuario con el `email` no existe regresamos un error.
  * Revisamos el `status` del el usuario. Si el usuario esta "retired" prohibimos el acceso.
  * Calculamos el hash de la contraseña y la comparamos contra el hash del usuario almacenado en la base
    de datos.
  * Si el hash de la contraseña no coincide regresamos un error.
  * Si la contraseña es correcta regresamos un estado de éxito.

El método `authenticate()` regresa una instancia de la clase `Laminas\Authentication\Result`. La clase
`Result` contiene el estado de la autenticación, el mensaje de error y la identidad del usuario.

El adaptador puede además tener métodos adicionales. Por ejemplo, agregamos los métodos `setEmail()`
y `setPassword` que usaremos para pasar el correo electrónico y la contraseña al adaptador.

Para crear el adaptador de autenticación agregamos el archivo *AuthAdapter.php* al directorio
*Service* del modulo dentro de la carpeta src.

I> El ejemplo *User Demo* creamos un modulo separado llamado *User* y agregamos la funcionalidad
I> relacionada con la autenticación y el administrador de usuario para ese modulo.

Coloca el siguiente código dentro del archivo:

~~~php
<?php
namespace User\Service;

use Laminas\Authentication\Adapter\AdapterInterface;
use Laminas\Authentication\Result;
use Laminas\Crypt\Password\Bcrypt;
use User\Entity\User;

/**
 * Adapter used for authenticating user. It takes login and password on input
 * and checks the database if there is a user with such login (email) and password.
 * If such user exists, the service returns his identity (email). The identity
 * is saved to session and can be retrieved later with Identity view helper provided
 * by Laminas.
 */
class AuthAdapter implements AdapterInterface
{
    /**
     * User email.
     * @var string
     */
    private $email;

    /**
     * Password
     * @var string
     */
    private $password;

    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * Constructor.
     */
    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Sets user email.
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Sets password.
     */
    public function setPassword($password)
    {
        $this->password = (string)$password;
    }

    /**
     * Performs an authentication attempt.
     */
    public function authenticate()
    {
        // Check the database if there is a user with such email.
        $user = $this->entityManager->getRepository(User::class)
                ->findOneByEmail($this->email);

        // If there is no such user, return 'Identity Not Found' status.
        if ($user==null) {
            return new Result(
                Result::FAILURE_IDENTITY_NOT_FOUND,
                null,
                ['Invalid credentials.']);
        }

        // If the user with such email exists, we need to check if it is active or retired.
        // Do not allow retired users to log in.
        if ($user->getStatus()==User::STATUS_RETIRED) {
            return new Result(
                Result::FAILURE,
                null,
                ['User is retired.']);
        }

        // Now we need to calculate hash based on user-entered password and compare
        // it with the password hash stored in database.
        $bcrypt = new Bcrypt();
        $passwordHash = $user->getPassword();

        if ($bcrypt->verify($this->password, $passwordHash)) {
            // Great! The password hash matches. Return user identity (email) to be
            // saved in session for later use.
            return new Result(
                    Result::SUCCESS,
                    $this->email,
                    ['Authenticated successfully.']);
        }

        // If password check didn't pass return 'Invalid Credential' failure status.
        return new Result(
                Result::FAILURE_CREDENTIAL_INVALID,
                null,
                ['Invalid credentials.']);
    }
}
~~~

### Crear la Factory para el AuthenticationService

Una vez que hemos implementado el adaptador podemos crear el `AuthenticationService`.
El `AuthenticationService` de Laminas debe ser registrado en el administrador de servicio antes de poder usarlo.
Primero que todo, crearemos una factory para él. Agregar el archivo *AuthenticationServiceFactory.php*
dentro del directorio *Service/Factory* y colocar el código siguiente:

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
 * The factory responsible for creating of authentication service.
 */
class AuthenticationServiceFactory implements FactoryInterface
{
    /**
     * This method creates the Laminas\Authentication\AuthenticationService service
     * and returns its instance.
     */
    public function __invoke(ContainerInterface $container,
                    $requestedName, array $options = null)
    {
        $sessionManager = $container->get(SessionManager::class);
        $authStorage = new SessionStorage('Laminas_Auth', 'session', $sessionManager);
        $authAdapter = $container->get(AuthAdapter::class);

        // Create the service and inject dependencies into its constructor.
        return new AuthenticationService($authStorage, $authAdapter);
    }
}
~~~

En la factory hacemos lo siguiente. Primero, creamos una instancia del administrador de sesiones (se debe tener
configurado el administrador de sesiones) y creamos una instancia del administrador de almacenamiento de `Session`. Luego,
creamos una instancia del `AuthAdapter`. Finalmente, instanciamos el `AuthenticationService` e inyectamos las
dependencias (administrador de almacenamiento y el adaptador).

Registramos el `AuthenticationService` en nuestro archivo de configuración de la siguiente manera:

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

### Agregar AuthController

La clase `AuthController` tendrá dos acciones:

  * El `loginAction()` permite iniciar sesión en el sitio web (ver figuras 16.11 y 16.12)
    Podemos acceder a esta página escribiendo la URL "http://localhost/login" en la barra de navegación del navegador web.

  * El `logoutAction()` permite cerrar la sessión en el sitio web.
    Podemos acceder a esta página escribiendo la URL "http://localhost/logout" en la barra de navegación del navegador web.

![Figura 16.11 Página de Inicio de Sesión](../en/images/users/login_page.png)

![Figura 16.12 Página de Inicio de Sesión - Credenciales Invalidas](../en/images/users/login_page_errors.png)

El código del controlador `AuthController` se presenta a abajo:

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
 * This controller is responsible for letting the user to log in and log out.
 */
class AuthController extends AbstractActionController
{
    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * Auth manager.
     * @var User\Service\AuthManager
     */
    private $authManager;

    /**
     * Auth service.
     * @var \Laminas\Authentication\AuthenticationService
     */
    private $authService;

    /**
     * User manager.
     * @var User\Service\UserManager
     */
    private $userManager;

    /**
     * Constructor.
     */
    public function __construct($entityManager, $authManager, $authService, $userManager)
    {
        $this->entityManager = $entityManager;
        $this->authManager = $authManager;
        $this->authService = $authService;
        $this->userManager = $userManager;
    }

    /**
     * Authenticates user given email address and password credentials.
     */
    public function loginAction()
    {
        // Retrieve the redirect URL (if passed). We will redirect the user to this
        // URL after successfull login.
        $redirectUrl = (string)$this->params()->fromQuery('redirectUrl', '');
        if (strlen($redirectUrl)>2048) {
            throw new \Exception("Too long redirectUrl argument passed");
        }

        // Check if we do not have users in database at all. If so, create
        // the 'Admin' user.
        $this->userManager->createAdminUserIfNotExists();

        // Create login form
        $form = new LoginForm();
        $form->get('redirect_url')->setValue($redirectUrl);

        // Store login status.
        $isLoginError = false;

        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {

            // Fill in the form with POST data
            $data = $this->params()->fromPost();

            $form->setData($data);

            // Validate form
            if($form->isValid()) {

                // Get filtered and validated data
                $data = $form->getData();

                // Perform login attempt.
                $result = $this->authManager->login($data['email'],
                        $data['password'], $data['remember_me']);

                // Check result.
                if ($result->getCode()==Result::SUCCESS) {

                    // Get redirect URL.
                    $redirectUrl = $this->params()->fromPost('redirect_url', '');

                    if (!empty($redirectUrl)) {
                        // The below check is to prevent possible redirect attack
                        // (if someone tries to redirect user to another domain).
                        $uri = new Uri($redirectUrl);
                        if (!$uri->isValid() || $uri->getHost()!=null)
                            throw new \Exception('Incorrect redirect URL: ' . $redirectUrl);
                    }

                    // If redirect URL is provided, redirect the user to that URL;
                    // otherwise redirect to Home page.
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
     * The "logout" action performs logout operation.
     */
    public function logoutAction()
    {
        $this->authManager->logout();

        return $this->redirect()->toRoute('login');
    }
}
~~~

El método `loginAction()` acepta por GET el parámetro `redirectUrl`. La "redirección del URL" es una conveniente
característica que trabaja junto con el *filtro de acceso* que nosotros describiremos luego en este capítulo. Cuando
el visitante del sitio intenta acceder a la página web el filtro de acceso prohíbe el acceso a usuarios no autenticados,
el usuario es redireccionado a la página de "Login" y se pasa el URL de la página original como "redirección del URL".
Cuando el usuario inicia sesión es redireccionado a la página original automáticamente mejorando la experiencia del usuario.

### Agregar la plantilla a la página de inicio de sesión

La plantilla (el archivo *.phtml*) para el inicio de sesión se ve de la siguiente manera:

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

T> La plantilla de vista usa la plantilla de página *Sign In* que viene con el Framework
T> de CSS Bootstrap. Puedes encontrar la plantilla original [aquí](https://getbootstrap.com/examples/signin/).

### Agregar el servicio AuthManager

El `AuthController` trabaja de la mano con el servicio `AuthManager`. La lógica de negocio principal detrás
de la autenticación se implementa en el servicio. Vamos a describir al `AuthManager` en detalle.

El servicio `AuthManager` tiene los siguientes métodos responsables de la autenticación:

  * El método `login()`.
  * El método `logout()`.

El método `login()` (ver abajo) usa el `AuthenticationService` y el `AuthAdapter` de Laminas que escribimos al principio
para ejecutar la autenticación del usuario. El método adicionalmente acepta el argumento `$remenberMe` que permite
extender la vida de la cookie de sesión hasta 30 días.

~~~php
/**
 * Performs a login attempt. If $rememberMe argument is true, it forces the session
 * to last for one month (otherwise the session expires on one hour).
 */
public function login($email, $password, $rememberMe)
{
    // Check if user has already logged in. If so, do not allow to log in
    // twice.
    if ($this->authService->getIdentity()!=null) {
        throw new \Exception('Already logged in');
    }

    // Authenticate with login/password.
    $authAdapter = $this->authService->getAdapter();
    $authAdapter->setEmail($email);
    $authAdapter->setPassword($password);
    $result = $this->authService->authenticate();

    // If user wants to "remember him", we will make session to expire in
    // one month. By default session expires in 1 hour (as specified in our
    // config/global.php file).
    if ($result->getCode()==Result::SUCCESS && $rememberMe) {
        // Session cookie will expire in 1 month (30 days).
        $this->sessionManager->rememberMe(60*60*24*30);
    }

    return $result;
}
~~~

El método `logout()` remueve la identidad del usuario de la sesión y de esta manera el visitante deja de estar autenticado.

~~~php
/**
 * Performs user logout.
 */
public function logout()
{
    // Allow to log out only when user is logged in.
    if ($this->authService->getIdentity()==null) {
        throw new \Exception('The user is not logged in');
    }

    // Remove identity from session.
    $this->authService->clearIdentity();
}
~~~

## Filtro de acceso

La última cosa que se implementa en el modulo de usuarios es el *filtro de acceso*. El filtro de acceso
se usa para restringir el acceso a determinadas páginas permitiendo el acceso solo a usuarios autorizados.

El filtro de acceso trabaja de la siguiente manera:

  * Cuando alguien intenta acceder a una página web, revisamos la llave `access_filter` de la configuración
    y determinamos si se permite el acceso a la página a cualquiera o solo a usuarios autorizados.

  * Si la página esta disponible para cualquiera se permite al visitante ver la página.

  * Si la página puede ser accedida solo por usuarios autorizados, revisamos si el usuario esta autorizado o no.

  * Si el usuario no esta autorizado se redirecciona a la página de login y se piden las credenciales.

  * Una vez que el usuario inicia sesión se le redirecciona a la página original.

El filtro de acceso esta diseñado para funcionar en uno de estos dos modos: restrictive (por defecto) y permissive.
En el modo restrictive el filtro prohíbe el acceso no autorizado a cualquier página que no esta listada en la llave `access_filter`.

La llave de configuración `access_filter` se encuentra dentro del archivo *module.config.php* y será usada
por el filtro de acceso. La llave contiene una lista de controladores y nombres de acciones, para cada acción
se permitirá o ver la página a cualquiera o ver la página solo a los usuarios autorizados. Un ejemplo de la
estructura de la llave se muestra abajo:

~~~php
// The 'access_filter' key is used by the User module to restrict or permit
// access to certain controller actions for unauthenticated visitors.
'access_filter' => [
    'options' => [
        // The access filter can work in 'restrictive' (recommended) or 'permissive'
        // mode. In restrictive mode all controller actions must be explicitly listed
        // under the 'access_filter' config key, and access is denied to any not listed
        // action for not logged in users. In permissive mode, if an action is not listed
        // under the 'access_filter' key, access to it is permitted to anyone (even for
        // not logged in users. Restrictive mode is more secure and recommended to use.
        'mode' => 'restrictive'
    ],
    'controllers' => [
        Controller\IndexController::class => [
            // Allow anyone to visit "index" and "about" actions
            ['actions' => ['index', 'about'], 'allow' => '*'],
            // Allow authorized users to visit "settings" action
            ['actions' => ['settings'], 'allow' => '@']
        ],
    ]
],
~~~

Dentro de la llave `access_filter` tenemos dos subllaves:

  * La llave `options` puede ser usado para definir el modo en que los filtros funcionan ("restrictive" o "permissive").
  * La llave `controllers` lista los controladores y sus acciones especificando el tipo de acceso para cada acción. El
    carácter asterisco (*) significa que cualquiera es capaz de acceder a la página web. El carácter arroba (@) significa
    que solo los usuarios autorizados son capaces de acceder a la página.

I> La implementación del filtro de acceso es muy simple. Este no puede, por ejemplo, permitir el acceso basado en nombres
I> o por roles de usuarios. Sin embargo, podemos fácilmente modificar y extenderla como deseemos. Si planeas introducir
I> un control de acceso basado en roles (RBAC), revisa la documentación para el componente de Laminas Framework `Laminas\Permissions\Rbac`.

### Agregar el listener event dispatch

Para implementar el filtro de control de acceso, usaremos un listener event. Podemos familiarizados con
los listener event revisando el capítulo [Crear un nuevo modulo](#modules).

Prestaremos antención especialmente al evento *Dispatch*. El evento *Dispatch* es lanzado después del evento *Route*,
cuando el controllador y la acción son determinados. Para implementar el listener modificamos el archivo *Module.php*
del modulo *User* de la siguiente manera:

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
     * This method returns the path to module.config.php file.
     */
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     * This method is called once the MVC bootstrapping is complete and allows
     * to register event listeners.
     */
    public function onBootstrap(MvcEvent $event)
    {
        // Get event manager.
        $eventManager = $event->getApplication()->getEventManager();
        $sharedEventManager = $eventManager->getSharedManager();
        // Register the event listener method.
        $sharedEventManager->attach(AbstractActionController::class,
                MvcEvent::EVENT_DISPATCH, [$this, 'onDispatch'], 100);
    }

    /**
     * Event listener method for the 'Dispatch' event. We listen to the Dispatch
     * event to call the access filter. The access filter allows to determine if
     * the current visitor is allowed to see the page or not. If he/she
     * is not authorized and is not allowed to see the page, we redirect the user
     * to the login page.
     */
    public function onDispatch(MvcEvent $event)
    {
        // Get controller and action to which the HTTP request was dispatched.
        $controller = $event->getTarget();
        $controllerName = $event->getRouteMatch()->getParam('controller', null);
        $actionName = $event->getRouteMatch()->getParam('action', null);

        // Convert dash-style action name to camel-case.
        $actionName = str_replace('-', '', lcfirst(ucwords($actionName, '-')));

        // Get the instance of AuthManager service.
        $authManager = $event->getApplication()->getServiceManager()->get(AuthManager::class);

        // Execute the access filter on every controller except AuthController
        // (to avoid infinite redirect).
        if ($controllerName!=AuthController::class &&
            !$authManager->filterAccess($controllerName, $actionName)) {

            // Remember the URL of the page the user tried to access. We will
            // redirect the user to that URL after successful login.
            $uri = $event->getApplication()->getRequest()->getUri();
            // Make the URL relative (remove scheme, user info, host name and port)
            // to avoid redirecting to other domain by a malicious user.
            $uri->setScheme(null)
                ->setHost(null)
                ->setPort(null)
                ->setUserInfo(null);
            $redirectUrl = $uri->toString();

            // Redirect the user to the "Login" page.
            return $controller->redirect()->toRoute('login', [],
                    ['query'=>['redirectUrl'=>$redirectUrl]]);
        }
    }
}
~~~

### Implementar el algoritmo de control de acceso

El event listener `onDispatch()` llama al método `filterAccess()` del servicio `AuthManager` para determinar
si la página puede ser vista o no. Mostramos abajo el código del método `filterAccess()`:

~~~php
/**
 * This is a simple access control filter. It allows vistors to visit certain pages only,
 * the rest requiring the user to be authenticated.
 *
 * This method uses the 'access_filter' key in the config file and determines
 * whenther the current visitor is allowed to access the given controller action
 * or not. It returns true if allowed; otherwise false.
 */
public function filterAccess($controllerName, $actionName)
{
    // Determine mode - 'restrictive' (default) or 'permissive'. In restrictive
    // mode all controller actions must be explicitly listed under the 'access_filter'
    // config key, and access is denied to any not listed action for unauthenticated users.
    // In permissive mode, if an action is not listed under the 'access_filter' key,
    // access to it is permitted to anyone (even for not logged in users.
    // Restrictive mode is more secure and recommended to use.
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
                    return true; // Anyone is allowed to see the page.
                else if ($allow=='@' && $this->authService->hasIdentity()) {
                    return true; // Only authenticated user is allowed to see the page.
                } else {
                    return false; // Access denied.
                }
            }
        }
    }

    // In restrictive mode, we forbid access for unauthorized users to any
    // action not listed under 'access_filter' key (for security reasons).
    if ($mode=='restrictive' && !$this->authService->hasIdentity())
        return false;

    // Permit access to this page.
    return true;
}
~~~

### Probar el filtro de acceso

Para probar el filtro de acceso, intentamos visitar la página "http://localhost/users" o "http://localhost/settings" sin haber iniciado sesión.
El filtro de acceso redireccionará a la página de *Login*. Sin embargo, podemos visitar sin problemas la página "http://localhost/about", esta
está abierta para cualquiera.

## Identity Controller Plugin y el View Helper

Una de las últimas cosas que discutiremos es como revisar en nuestro sitio web si el usuario ha iniciado sesión
o no y recuperar la identidad del usuario. Podemos hacer esto con la ayuda del controller plugin `Identity`
y el view helper `Identity`.

I> Para usar el plugin `Identity` se necesita instalar mediante Composer el paquete `laminas/laminas-mvc-plugins`, de la siguiente manera:
I>
I> `php composer.phar require laminas/laminas-mvc-plugins`

En nuestro método de acción del controlador podemos revisar si el usuario ha iniciado sesión de la siguiente manera:

~~~php
if ($this->identity()!=null) {
    // User is logged in

    // Retrieve user identity
    $userEmail = $this->identity();
}
~~~

Desde nuestra plantilla de vista podemos usar el view helper `Identity` para el mismo propósito.

~~~php
// Echo user identity
<?= $this->escapeHtml($this->identity()) ?>
~~~

## Resumen

En este capítulo aprendimos sobre el manejo de usuarios, autenticación de usuarios y filtro de acceso.

El manejo de usuarios significa proveer una interfaz de usuario para agregar, ver y cambiar la contraseña del usuario.

La autenticación es el proceso en el cual un usuario provee su nombre de usuario y contraseña y se determina si estas
credenciales son correctas. Laminas provee un servicio especial llamado `AuthenticationService` que podemos usar para este
propósitos, pero primero necesitamos implementar un adaptador de autenticación.

El filtro de acceso permite conceder acceso a determinadas página solo a usuarios autorizados. Tu puedes implementar
un filtro de acceso con la ayuda de un event listener.
