<?php

use Groupeat\Support\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class BuildAssetsCommand extends Command {

    protected $name = 'asset:build';
    protected $description = "Publish and compile the assets";


    public function fire()
    {
        $this->call('asset:publish-all', ['package' => $this->argument('package')]);
        $this->info('Calling gulp sass task...');
        $this->process('gulp sass');
    }

    protected function getArguments()
    {
        return [
            ['package', InputArgument::OPTIONAL, 'Publish only the given GroupEat package.'],
        ];
    }


}
