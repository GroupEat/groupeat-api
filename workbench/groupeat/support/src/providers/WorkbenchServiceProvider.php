<?php namespace Groupeat\Support\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class WorkbenchServiceProvider extends ServiceProvider {

    protected $defer = false;

    protected $filesToRequire = ['helpers', 'routes'];
    protected $autoRequireFiles = true;

    protected $autoRegisterConsoleCommands = true;


    public function boot()
    {
        $name = $this->getPackageName();
        $this->package('groupeat/'.$name, $name, $this->getPackagePath());

        if ($this->autoRequireFiles)
        {
            $this->requireFiles(...$this->filesToRequire);
        }

        if ($this->autoRegisterConsoleCommands)
        {
            $this->registerConsoleCommands(...$this->listConsoleCommandShortNames());
        }
    }

    public function register()
    {
        $this->app['config']->package('groupeat/'.$this->getPackageName(), $this->getPackagePath('config'));
    }

	protected function requireFiles(...$files)
    {
        foreach ($files as $file)
        {
            $path = $this->getPackagePath($file.'.php');

            if (file_exists($path))
            {
                require_once $path;
            }
        }

        return $this;
    }

    protected function listConsoleCommandShortNames()
    {
        $console_path = $this->getPackagePath('console');

        if (is_dir($console_path))
        {
            return array_map(function($file) {
                $filename = pathinfo($file, PATHINFO_FILENAME);
                return str_replace('Command', '', $filename);
            }, File::files($console_path));
        }

        return [];
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
        $workbench_root = base_path('workbench/groupeat/'.$this->getPackageName().'/src');

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
        $className = get_class($this);
        $parts = explode('\\', $className);

        return lcfirst($parts[1]);
    }

}
