<?php
namespace Groupeat\Support\Providers\Abstracts;

use Closure;
use File;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\ServiceProvider;

abstract class WorkbenchPackageProvider extends ServiceProvider
{
    protected $defer = false;

    public function register()
    {
        $this->registerPackage();
    }

    public function boot()
    {
        $name = lcfirst($this->getPackageName());
        $this->loadViewsFrom($this->getPackagePath('views'), $name);
        $this->loadTranslationsFrom($this->getPackagePath('lang'), $name);

        $this->includeRoutes();
        $this->registerConsoleCommands();
        $this->registerJobsMapping();

        $this->bootPackage();
    }

    protected function registerPackage()
    {
        // Implemented by inheritance
    }

    protected function bootPackage()
    {
        // Implemented by inheritance
    }

    /**
     * @param string $valueClass
     * @param string $configKey
     */
    protected function bindValueFromConfig($valueClass, $configKey)
    {
        $this->bindValue($valueClass, $this->app['config']->get($configKey));
    }

    /**
     * @param string $valueClass
     * @param mixed  $value
     */
    protected function bindValue($valueClass, $value)
    {
        $this->app->instance(
            $valueClass,
            new $valueClass($value)
        );
    }

    protected function bindValueFromCallback($valueClass, Closure $callback)
    {
        $this->app->singleton(
            $valueClass,
            function ($app) use ($valueClass, $callback) {
                return new $valueClass($callback($app));
            }
        );
    }

    /**
     * @param string $eventClass
     * @param string $handlerClass
     * @param string $method
     */
    protected function listen($eventClass, $handlerClass, $method = 'handle')
    {
        $this->app['events']->listen($eventClass, "$handlerClass@$method");
    }

    private function includeRoutes()
    {
        $routesPath = $this->getPackagePath('routes.php');

        if (file_exists($routesPath)) {
            include $routesPath;
        }
    }

    protected function registerConsoleCommands()
    {
        $namespacePrefix = 'Groupeat\\'.$this->getPackageName().'\Console\\';
        $consoleCommandPaths = File::files($this->getPackagePath('Console'));

        $consoleCommandNamespaces = array_map(function ($consoleCommandPath) use ($namespacePrefix) {
            $className = pathinfo($consoleCommandPath, PATHINFO_FILENAME);

            return $namespacePrefix.$className;
        }, $consoleCommandPaths);

        $this->commands($consoleCommandNamespaces);
    }

    protected function registerJobsMapping()
    {
        $maps = [];
        $jobsPaths = File::files($this->getPackagePath('Jobs'));
        $namespacePrefix = 'Groupeat\\'.$this->getPackageName();

        foreach ($jobsPaths as $jobsPath) {
            $method = 'handle';
            $className = pathinfo($jobsPath, PATHINFO_FILENAME);
            $jobsClass = $namespacePrefix.'\Jobs\\'.$className;
            $handlerClass = $namespacePrefix.'\Jobs\\'.$className.'Handler';

            $maps[$jobsClass] = "$handlerClass@$method";
        }

        $this->app[Dispatcher::class]->maps($maps);
    }

    protected function getPackagePath($file = '')
    {
        $workbench_root = base_path("app/{$this->getPackageName()}");

        if (empty($file)) {
            return $workbench_root;
        } else {
            return $workbench_root.'/'.$file;
        }
    }

    protected function getPackageName()
    {
        $parts = explode('\\', static::class);

        return $parts[1];
    }
}
