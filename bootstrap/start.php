<?php

/*
|--------------------------------------------------------------------------
| Detect Testing Environment
|--------------------------------------------------------------------------
|
| The application can be tested both on local environment or on the
| Shippable platform. We need to detect the current environment in
| order to load the correct configuration variables.
|
*/

$testEnvironment = !empty($_SERVER['SHIPPABLE']) ? 'building' : 'testing';

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all of the various parts.
|
*/

$app = new Illuminate\Foundation\Application;

/*
|--------------------------------------------------------------------------
| Detect The Application Environment
|--------------------------------------------------------------------------
|
| Laravel takes a dead simple approach to your application environments
| so you can just specify a machine name for the host that matches a
| given environment, then we will automatically detect it for you.
|
*/

if (!function_exists('loadProductionVariables'))
{
    function loadProductionVariables()
    {
        $path = realpath(__DIR__ . '/../.env.production.php');

        if (file_exists($path))
        {
            $variables = include $path;

            foreach ($variables as $key => $value)
            {
                $_SERVER[$key] = $value;
            }
        }
    }
}

$env = $app->detectEnvironment(function()
{
    $hostname = gethostname();

    if ($hostname == 'PizzeriaDev')
    {
        return 'local';
    }
    if ($hostname == 'PizzeriaProd')
    {
        loadProductionVariables();

        return 'production';
    }
    else
    {
        return 'building';
    }
});

/*
|--------------------------------------------------------------------------
| Bind Paths
|--------------------------------------------------------------------------
|
| Here we are binding the paths configured in paths.php to the app. You
| should not be changing these here. If you need to change these you
| may do so within the paths.php file and they will be bound here.
|
*/

$app->bindInstallPaths(require __DIR__.'/paths.php');

/*
|--------------------------------------------------------------------------
| Load The Application
|--------------------------------------------------------------------------
|
| Here we will load this Illuminate application. We will keep this in a
| separate location so we can isolate the creation of an application
| from the actual running of the application with a given request.
|
*/

$framework = $app['path.base'].'/vendor/laravel/framework/src';

require $framework.'/Illuminate/Foundation/start.php';

/*
|--------------------------------------------------------------------------
| Set locale from accet-language header if defined
|--------------------------------------------------------------------------
|
| If the request contains the accept-language header, we will try to use it
| to set the application locale.
|
*/

if ($languageHeader = $app['request']->headers->get('accept-language'))
{
    $localeFound = false;
    $availableLocales = $app['config']->get('app.available_locales');
    $languages = explode(';', $languageHeader);

    foreach ($languages as $language)
    {
        if ($localeFound)
        {
            break;
        }

        foreach ($availableLocales as $locale)
        {
            if (str_contains($language, $locale))
            {
                $localeFound = true;
                $app['config']->set('app.locale', $locale);
                $app['config']->set('app.user_locale', $locale);

                break;
            }
        }
    }
}

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/

return $app;
