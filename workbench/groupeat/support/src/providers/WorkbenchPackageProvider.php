<?php namespace Groupeat\Support\Providers;

use Illuminate\Support\ServiceProvider;

class WorkbenchPackageProvider extends ServiceProvider {

    const FILTERS = 'filters';
    const HELPERS = 'helpers';
    const ROUTES = 'routes';

    protected $defer = false;

    protected $require = [];
    protected $console = [];


    public function boot()
    {
        $name = $this->getPackageName();
        $this->package("groupeat/$name", $name, $this->getPackagePath());

        $this->requireFiles(...$this->require);
        $this->registerConsoleCommands(...$this->console);
    }

    public function register()
    {
        $this->app['config']->package("groupeat/{$this->getPackageName()}", $this->getPackagePath('config'));

        $this->registerServices();
    }

    protected function registerServices()
    {
        // Implemented by inheritance
    }

	protected function requireFiles(...$files)
    {
        foreach ($files as $file)
        {
            $path = $this->getPackagePath("$file.php");

            require_once $path;
        }

        return $this;
    }

    protected function registerConsoleCommands(...$commandShortNames)
    {
        $names = [];

        foreach ($commandShortNames as $commandShortName)
        {
            $className = 'Groupeat\\'.ucfirst($this->getPackageName()).'\\Console\\'.$commandShortName.'Command';
            $name = 'groupeat.console.'.strtolower($commandShortName);

            $this->app[$name] = $this->app->share(function($app) use ($className)
            {
                return new $className;
            });

            $names[] = $name;
        }

        $this->commands($names);

        return $this;
    }

    protected function getPackagePath($file = '')
    {
        $workbench_root = base_path("workbench/groupeat/{$this->getPackageName()}/src");

        if (empty($file))
        {
            return $workbench_root;
        }
        else
        {
            return $workbench_root.'/'.$file;
        }
    }

    protected function getPackageName()
    {
        $parts = explode('\\', static::class);

        return lcfirst($parts[1]);
    }

}
