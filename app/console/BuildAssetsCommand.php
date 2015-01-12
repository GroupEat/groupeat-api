<?php

use Groupeat\Support\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class BuildAssetsCommand extends Command {

    protected $name = 'asset:build';
    protected $description = "Publish and compile the assets";


    public function fire()
    {
        if ($this->option('install'))
        {
            $this->info('Installing NPM dependencies');
            $this->process('npm install');

            $this->info('Installing Bower dependencies');
            $this->process('bower install');
        }

        $this->call('asset:publish-all', ['package' => $this->argument('package')]);
        $this->info('Calling gulp sass task');
        $this->process('gulp sass');

        if ($this->option('watch'))
        {
            $this->process('gulp watch');
        }
    }

    protected function getArguments()
    {
        return [
            ['package', InputArgument::OPTIONAL, 'Publish only the given GroupEat package.'],
        ];
    }

    protected function getOptions()
    {
        return [
            ['install', 'i', InputOption::VALUE_NONE, 'Install the NPM and bower dependencies.', null],
            ['watch', 'w', InputOption::VALUE_NONE, 'Launch the Gulp watch task.', null],
        ];
    }


}
