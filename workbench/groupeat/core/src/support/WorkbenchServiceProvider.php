<?php namespace Groupeat\Core\Support;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class WorkbenchServiceProvider extends ServiceProvider {

    protected $defer = false;

    protected $filesToRequire = ['helpers', 'routes'];
    protected $autoRequireFiles = true;

    protected $autoRegisterCommands = true;


	public function register()
	{
        if ($this->autoRequireFiles)
        {
            $this->requireFiles(...$this->filesToRequire);
        }

        if ($this->autoRegisterCommands)
        {
            $this->registerCommands(...$this->listCommandShortNames());
        }
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
            $className = 'Groupeat\\'.$this->getPackageName().'\\Commands\\'.$commandShortName.'Command';
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
        $folder = strtolower($this->getPackageName());

        $workbench_root = base_path('workbench/groupeat/'.$folder.'/src');

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

        return $parts[1];
    }

}
