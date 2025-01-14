# Database Migrations

When you are developing your website, you are constantly modifying your database schema: some tables
are being added, others are being modified, unneeded tables are being deleted. Managing database schema
may become a very complex task, because you need to keep it up-to-date on your development,
testing and production servers. You can greatly simplify
the task of managing database schema with the concept of *migrations*.

A migration is some kind of atomic change of state of the database schema. You can apply a
migration to upgrade the schema to its newer state, or you can revert a migration to
downgrade schema to its previous state. You create new migrations as you develop your website,
and you can have as many migrations as you need. All migrations form a database schema change
history, which is very convenient, because you know when and for what purpose you changed the
schema.

## Installing Doctrine\Migrations Component

Doctrine provides a special component `Doctrine\Migrations` that you can use
for implementing database migrations in your website.
To install `Doctrine\Migrations` component, type the following command:

~~~
php composer.phar require doctrine/migrations
~~~

The command above will download and install the component files to the `vendor` directory
and modify your `composer.json` file as follows:

~~~
{
    ...
    "require": {
        ...
        "doctrine/migrations": "^1.4",
        ...
    },
    ...
}
~~~

## Configuring Migrations

Before you can use migrations, you'll need to provide the configuration describing
what database connection to use, in which table to store migration history, where to
store migration classes, etc. To do that,
add the following lines to your `config/autoload/global.php` file:

~~~php
<?php
return [
    'doctrine' => [
        // migrations configuration
        'migrations_configuration' => [
            'orm_default' => [
                'directory' => 'data/Migrations',
                'name'      => 'Doctrine Database Migrations',
                'namespace' => 'Migrations',
                'table'     => 'migrations',
            ],
        ],
    ],
    // ...
];
~~~

As you can see, we have the `doctrine` key and its `migrations_configuration` subkey (line 5). Under this subkey we
provide the configuration for migrations:

  * In line 6, we provide the name of entity manager to use (`orm_default`).

  * In line 7, we tell Doctrine that we want to store migrations under the `APP_DIR/data/Migrations` directory.

  * In line 8, we provide a user-friendly name for our migrations.

  * In line 9, we tell Doctrine that we want that our migration classes live in `Migrations` namespace.
    You can specify a namespace of your choice.

  * In line 10, we tell Doctrine that want to store migration history inside of `migrations` database table.
    Doctrine will create and manage that table automatically.

## Creating Migrations

A migration is a change set upgrading or downgrading the schema to its next or previous state, respectively.
You generate a new empty migration with the help of the following commands:

~~~
cd APP_DIR
./vendor/bin/doctrine-module migrations:generate
~~~

The commands above make the application directory the current working directory and then
run the `migrations:generate` console command.

> `DoctrineModule` and `DoctrineORMModule` provide several console commands that you can use for various
> database maintenance tasks (like generating or executing migrations). For the list of available commands,
> you can use the `list` command:
>
> `./vendor/bin/doctrine-module list`

Once you run the `migrations:generate` command, you will be able to find the newly created migration under the `APP_DIR/data/Migrations` directory.
The file has a name like `VersionYYYYMMDDHHIISS.php`, where `YYYY` is current year, `MM` is current month, `DD` is current day,
`HH`, `II` and `SS` represent current hour, minute and second, respectively.

If you look into the newly created file, you will find the following content:

~~~php
<?php

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160901114333 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
~~~

> If you do not see the newly created migration in NetBeans IDE, you need to open the menu *Source* and select the
> *Scan for external changes* menu item.

As you can see from the code above, a migration is a usual PHP class inherited from `Doctrine\DBAL\Migrations\AbstractMigration`
base class. Every migration should have *at least* two methods: `up()` and `down()`. The `up()` method upgrades the schema to a newer state,
the `down()` method downgrades the schema from its newer state to the previous state. Both `up()` and `down()` methods have a single
argument of type `Doctrine\DBAL\Schema\Schema`, which can be used for actual database schema modifications.

> The `Schema` class is a part of `Doctrine\DBAL` component. For more information about the methods it provides, please
> refer to Doctrine DBAL documentation. Another, even better way is to look at the code inside your `vendor/doctrine/dbal` directory.

A migration class may optionally have the following (overridden) methods (table 13.1):

| *Method*                       | *Description*                                                 |
|--------------------------------|---------------------------------------------------------------|
| `isTransactional()`            | If this function returns true (default) the migration will be executed in one transaction, |
|                                | otherwise non-transactional state will be used to execute each of the migration SQLs.|
| `getDescription()`             | This method should return a string describing the migration (for what purpose this schema change is done) |
| `preUp(Schema $schema)`        | This method will be executed before upgrading the schema.     |
| `postUp(Schema $schema)`       | This method will be executed after upgrading the schema.      |
| `preDown(Schema $schema)`      | This method will be executed before downgrading the schema.   |
| `postDown(Schema $schema)`     | This method will be executed after downgrading the schema.    |

Table 13.1. Methods a migration class may have

The `AbstractMigration` base class also provides the following useful methods (table 13.2):

| *Method*                       | *Description*                                                 |
|--------------------------------|---------------------------------------------------------------|
| `addSql($sql, array $params = [], array $types = [])` | This method allows to execute an arbitrary SQL request. |
| `write($message)`              | This helper method prints a (debug or explanatory) message to screen. |
| `throwIrreversibleMigrationException($message = null)` | This helper method is typically called inside of `down()` method to signal that the migration cannot be undone. |

Table 13.2. Methods provided by the base migration class

As you can see from table 13.2, you can also modify the schema by calling `addSql()` method. This method
can be used to create a table, to update a table or to remove a table. It can also be used, for example, to insert
some data to a table (however, inserting data is not a schema change).

> Doctrine migrations are designed for schema changes, not for inserting data to the database. Although,
> inserting some initial data to database is useful in some cases.

Now that you know how to create a migration, let's create a couple of migrations for our *Blog* sample.

### Creating the Initial Migration

The first migration we will create is the initial migration. This migration will be applied to empty database
schema and will create four tables: `post`, `comment`, `tag` and `post_tag`.

Modify the migration class we have created in the previous section to look like below:

~~~php
<?php

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * A migration class. It either upgrades the databases schema (moves it to new state)
 * or downgrades it to the previous state.
 */
class Version20160901114333 extends AbstractMigration
{
    /**
     * Returns the description of this migration.
     */
    public function getDescription()
    {
        $description = 'This is the initial migration which creates blog tables.';
        return $description;
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // Create 'post' table
        $table = $schema->createTable('post');
        $table->addColumn('id', 'integer', ['autoincrement'=>true]);
        $table->addColumn('title', 'text', ['notnull'=>true]);
        $table->addColumn('content', 'text', ['notnull'=>true]);
        $table->addColumn('status', 'integer', ['notnull'=>true]);
        $table->addColumn('date_created', 'datetime', ['notnull'=>true]);
        $table->setPrimaryKey(['id']);
        $table->addOption('engine' , 'InnoDB');

        // Create 'comment' table
        $table = $schema->createTable('comment');
        $table->addColumn('id', 'integer', ['autoincrement'=>true]);
        $table->addColumn('post_id', 'integer', ['notnull'=>true]);
        $table->addColumn('content', 'text', ['notnull'=>true]);
        $table->addColumn('author', 'string', ['notnull'=>true, 'lenght'=>128]);
        $table->addColumn('date_created', 'datetime', ['notnull'=>true]);
        $table->setPrimaryKey(['id']);
        $table->addOption('engine' , 'InnoDB');

        // Create 'tag' table
        $table = $schema->createTable('tag');
        $table->addColumn('id', 'integer', ['autoincrement'=>true]);
        $table->addColumn('name', 'string', ['notnull'=>true, 'lenght'=>128]);
        $table->setPrimaryKey(['id']);
        $table->addOption('engine' , 'InnoDB');

        // Create 'post_tag' table
        $table = $schema->createTable('post_tag');
        $table->addColumn('id', 'integer', ['autoincrement'=>true]);
        $table->addColumn('post_id', 'integer', ['notnull'=>true]);
        $table->addColumn('tag_id', 'integer', ['notnull'=>true]);
        $table->setPrimaryKey(['id']);
        $table->addOption('engine' , 'InnoDB');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('post_tag');
        $schema->dropTable('tag');
        $schema->dropTable('comment');
        $schema->dropTable('post');
    }
}
~~~

In the code above we have three methods:

  * the `getDescription()` method provides the description of the migration.
  * the `up()` method upgrades the schema to its newer state by adding new tables.
  * the `down()` method drops tables thus downgrading the schema to its previous state.

### Adding Another Migration

Now assume we decided to improve the performance of our database by adding indexes to our tables.

> If you want to learn about database indexes in more details and why indexes are so helpful, you can refer to an excellent tutorial
> called [Use the Index, Luke](http://use-the-index-luke.com/).

We can also improve data integrity by adding foreign keys. To do this, we have to add another migration. Generate another
empty migration with the `migrations:generate` console command. Modify the code to look like below:

~~~php
<?php

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * A migration class. It either upgrades the databases schema (moves it to new state)
 * or downgrades it to the previous state.
 */
class Version20160901114938 extends AbstractMigration
{
    /**
     * Returns the description of this migration.
     */
    public function getDescription()
    {
        $description = 'This migration adds indexes and foreign key constraints.';
        return $description;
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // Add index to post table
        $table = $schema->getTable('post');
        $table->addIndex(['date_created'], 'date_created_index');

        // Add index and foreign key to comment table
        $table = $schema->getTable('comment');
        $table->addIndex(['post_id'], 'post_id_index');
        $table->addForeignKeyConstraint('post', ['post_id'], ['id'], [], 'comment_post_id_fk');

        // Add indexes and foreign keys to post_tag table
        $table = $schema->getTable('post_tag');
        $table->addIndex(['post_id'], 'post_id_index');
        $table->addIndex(['tag_id'], 'tag_id_index');
        $table->addForeignKeyConstraint('post', ['post_id'], ['id'], [], 'post_tag_post_id_fk');
        $table->addForeignKeyConstraint('tag', ['tag_id'], ['id'], [], 'post_tag_tag_id_fk');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $table = $schema->getTable('post_tag');
        $table->removeForeignKey('post_tag_post_id_fk');
        $table->removeForeignKey('post_tag_tag_id_fk');
        $table->dropIndex('post_id_index');
        $table->dropIndex('tag_id_index');

        $table = $schema->getTable('comment');
        $table->dropIndex('post_id_index');
        $table->removeForeignKey('comment_post_id_fk');

        $table = $schema->getTable('post');
        $table->dropIndex('date_created_index');
    }
}
~~~

> You can find the migrations we have just created inside of the *Blog* sample bundled with this book.

## Executing Migrations

Once our migration classes are ready, you can apply the migrations to database. To do that, you use the
`migrations:migrate` console command as follows:

~~~
./vendor/bin/doctrine-module migrations:migrate
~~~

The command above applies all available migrations in turn. It writes the IDs of the applied migration to
the `migrations` database table. After that, the `migrations` table will look as follows:

~~~
mysql> select * from migrations;
+----------------+
| version        |
+----------------+
| 20160901114333 |
| 20160901114938 |
+----------------+
2 rows in set (0.00 sec)
~~~

If you want to upgrade or downgrade to some specific version, specify the migration ID as the `migrations:migrate`
command's argument as follows:

~~~
./vendor/bin/doctrine-module migrations:migrate 20160901114333
~~~

> You can also use 'prev', 'next' and 'first' aliases as version IDs which respectively move database to its previous state, next state
> or to the state before the first migration (empty database).

So, with migrations you can easily move through migration history and change the database schema as needed.
Be careful though that migrations may remove some of your data, so apply them wisely.

## Summary

In this chapter, we have learned about database migrations. Doctrine library provides you a component called `Doctrine\Migrations`
which allows to implement and apply migrations.

A migration is some kind of change set which can be applied to database schema. You can apply a
migration to upgrade the schema to its newer state, or you can revert a migration to
downgrade schema to its previous state.

Migrations are useful, because they allow to store the history of schema changes and
apply changes in a standard way. With migrations, you can easily keep your schema up-to-date on every development
machine, and on testing, staging and production servers.
