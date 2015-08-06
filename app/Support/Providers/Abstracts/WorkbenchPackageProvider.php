<?php
namespace Groupeat\Support\Providers\Abstracts;

use Closure;
use File;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\ServiceProvider;

abstract class WorkbenchPackageProvider extends ServiceProvider
{
    protected $defer = false;

    protected $configValues = [
        // Associative array defined by inheritance
    ];

    protected $routeEntities = [
        // Associative array defined by inheritance
    ];

    protected $listeners = [
        // Associative array defined by inheritance
    ];

    public function register()
    {
        $this->bindConfigValuesIfNeeded();
        $this->bindRouteEntitiesIfNeeded();

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
        $this->bindListenersIfNeeded();

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

    protected function bindConfigValuesIfNeeded()
    {
        if (!empty($this->configValues)) {
            foreach ($this->configValues as $valueClass => $configKey) {
                $this->bindConfigValue($valueClass, $configKey);
            }
        }
    }

    protected function bindRouteEntitiesIfNeeded()
    {
        if (!empty($this->routeEntities)) {
            foreach ($this->routeEntities as $entityClass => $routeSegment) {
                $this->app['router']->model($routeSegment, $entityClass);
            }
        }
    }

    /**
     * @param string $valueClass
     * @param string $configKey
     */
    protected function bindConfigValue($valueClass, $configKey)
    {
        $this->bindValue($valueClass, $this->app['config']->get($configKey));
    }

    protected function bindListenersIfNeeded()
    {
        if (!empty($this->listeners)) {
            foreach ($this->listeners as $listenerClassWithMethod => $eventClass) {
                if (!str_contains($listenerClassWithMethod, '@')) {
                    $listenerClassWithMethod .= '@handle';
                }

                $this->app['events']->listen($eventClass, $listenerClassWithMethod);
            }
        }
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
