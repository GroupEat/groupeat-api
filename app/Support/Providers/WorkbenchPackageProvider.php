<?php
namespace Groupeat\Support\Providers;

use Illuminate\Support\ServiceProvider;

abstract class WorkbenchPackageProvider extends ServiceProvider
{
    const FILTERS = 'filters';
    const HELPERS = 'helpers';
    const ROUTES = 'routes';

    protected $defer = false;

    protected $require = [];
    protected $console = [];

    public function register()
    {
        // Implemented by inheritance
    }

    public function boot()
    {
        $name = lcfirst($this->getPackageName());
        $this->loadViewsFrom($this->getPackagePath('views'), $name);
        $this->loadTranslationsFrom($this->getPackagePath('lang'), $name);

        $this->requireFiles(...$this->require);
        $this->registerConsoleCommands(...$this->console);
    }

    protected function requireFiles(...$files)
    {
        foreach ($files as $file) {
            $path = $this->getPackagePath("$file.php");

            include $path;
        }

        return $this;
    }

    protected function registerConsoleCommands(...$commandShortNames)
    {
        $names = [];

        foreach ($commandShortNames as $commandShortName) {
            $className = 'Groupeat\\'.ucfirst($this->getPackageName()).'\\Console\\'.$commandShortName.'Command';
            $name = 'groupeat.console.'.strtolower($commandShortName);

            $this->app[$name] = $this->app->share(function ($app) use ($className) {
                return new $className();
            });

            $names[] = $name;
        }

        $this->commands($names);

        return $this;
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