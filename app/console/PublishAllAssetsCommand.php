<?php

use Groupeat\Support\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class PublishAllAssetsCommand extends Command {

	protected $name = 'asset:publish-all';
	protected $description = "Publish assets from vendor and groupeat packages";


	public function fire()
	{
        if ($this->argument('package'))
        {
            $this->comment("Publish only the goupeat/{$this->argument('package')} package");
        }
        else
        {
            $this->call('asset:publish');
        }

        foreach(listGroupeatPackages() as $package)
        {
            if (!$this->argument('package') || $this->argument('package') == $package)
            {
                $publicFolderPath = workbench_path($package, '../public');

                if (File::isDirectory($publicFolderPath))
                {
                    $this->call('asset:publish', ['--bench' => "groupeat/$package"]);
                }
            }
        }
	}

    protected function getArguments()
    {
        return [
            ['package', InputArgument::OPTIONAL, 'Publish only the given GroupEat package.'],
        ];
    }

}
