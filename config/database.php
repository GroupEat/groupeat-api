<?php

return [

    /*
    |--------------------------------------------------------------------------
    | PDO Fetch Style
    |--------------------------------------------------------------------------
    |
    | By default, database results will be returned as instances of the PHP
    | stdClass object; however, you may desire to retrieve records in an
    | array format for simplicity. Here you can tweak the fetch style.
    |
    */

    'fetch' => PDO::FETCH_CLASS,

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => 'pgsql',

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [

        'pgsql' => [
            'driver'   => 'pgsql',
            'host'     => '127.0.0.1',
            'database' => 'groupeat',
            'username' => env('PGSQL_USER', 'groupeat'),
            'password' => env('PGSQL_PASSWORD', 'groupeat'),
            'charset'  => 'utf8',
            'prefix'   => '',
            'schema'   => 'public',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Fake entries number
    |--------------------------------------------------------------------------
    |
    | Number of fake entries to create when seeding the database in
    | the development or test environment.
    |
    */

    'entries' => 5,

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Migration Order
    |--------------------------------------------------------------------------
    |
    | The order in which the migrations have to be run to create the database.
    |
    */

    'order' => [
        \Groupeat\Settings\Migrations\SettingsMigration::class,
        \Groupeat\Customers\Migrations\CustomersMigration::class,
        \Groupeat\Auth\Migrations\UserCredentialsMigration::class,
        \Groupeat\Auth\Migrations\PasswordResetTokensMigration::class,
        \Groupeat\Customers\Migrations\CustomerAddressesMigration::class,
        \Groupeat\Customers\Migrations\PredefinedAddressesMigration::class,
        \Groupeat\Admin\Migrations\AdminsMigration::class,
        \Groupeat\Restaurants\Migrations\CategoriesMigration::class,
        \Groupeat\Restaurants\Migrations\RestaurantsMigration::class,
        \Groupeat\Restaurants\Migrations\CategoryRestaurantMigration::class,
        \Groupeat\Restaurants\Migrations\RestaurantAddressesMigration::class,
        \Groupeat\Restaurants\Migrations\OpeningWindowsMigration::class,
        \Groupeat\Restaurants\Migrations\ClosingWindowsMigration::class,
        \Groupeat\Restaurants\Migrations\FoodTypesMigration::class,
        \Groupeat\Restaurants\Migrations\ProductsMigration::class,
        \Groupeat\Restaurants\Migrations\ProductFormatsMigration::class,
        \Groupeat\Orders\Migrations\GroupOrdersMigration::class,
        \Groupeat\Orders\Migrations\OrdersMigration::class,
        \Groupeat\Orders\Migrations\OrderProductFormatMigration::class,
        \Groupeat\Orders\Migrations\DeliveryAddressesMigration::class,
        \Groupeat\Devices\Migrations\PlatformsMigration::class,
        \Groupeat\Devices\Migrations\DevicesMigration::class,
        \Groupeat\Devices\Migrations\StatusesMigration::class,
        \Groupeat\Settings\Migrations\CustomerSettingMigration::class,
        \Groupeat\Notifications\Migrations\NotificationsMigration::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer set of commands than a typical key-value systems
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'cluster' => false,

        'default' => [
            'host'     => '127.0.0.1',
            'port'     => 6379,
            'database' => 0,
        ],

    ],

];
