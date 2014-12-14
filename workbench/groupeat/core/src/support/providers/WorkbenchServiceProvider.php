<?php namespace Groupeat\Core\Support\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class WorkbenchServiceProvider extends ServiceProvider {

    protected $defer = false;

    protected $filesToRequire = ['helpers', 'routes'];
    protected $autoRequireFiles = true;

    protected $autoRegisterCommands = true;


    public function boot()
    {
        $name = $this->getPackageName();
        $this->package('groupeat/'.$name, $name, $this->getPackagePath());

        if ($this->autoRequireFiles)
        {
            $this->requireFiles(...$this->filesToRequire);
        }

        if ($this->autoRegisterCommands)
        {
            $this->registerCommands(...$this->listCommandShortNames());
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

    protected function listCommandShortNames()
    {
        $commands_path = $this->getPackagePath('commands');

        if (is_dir($commands_path))
        {
            return array_map(function($file) {
                $filename = pathinfo($file, PATHINFO_FILENAME);
                return str_replace('Command', '', $filename);
            }, File::files($commands_path));
        }

        return [];
    }

    protected function registerCommands(...$commandShortNames)
    {
        $names = [];

        foreach ($commandShortNames as $commandShortName)
        {
            $className = 'Groupeat\\'.ucfirst($this->getPackageName()).'\\Commands\\'.$commandShortName.'Command';
            $name = 'groupeat.commands.'.strtolower($commandShortName);

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
