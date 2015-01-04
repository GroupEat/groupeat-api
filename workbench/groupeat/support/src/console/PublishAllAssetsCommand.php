<?php namespace Groupeat\Support\Console;

use File;

class PublishAllAssetsCommand extends Command {

	protected $name = 'assets:publish-all';
	protected $description = "Publish assets from vendor and groupeat packages";


	public function fire()
	{
        $this->call('asset:publish');

        foreach(listGroupeatPackages() as $package)
        {
            $publicFolderPath = workbench_path($package, '../public');

            if (File::isDirectory($publicFolderPath))
            {
                $this->call('asset:publish', ['--bench' => "groupeat/$package"]);
            }
        }
	}

}
