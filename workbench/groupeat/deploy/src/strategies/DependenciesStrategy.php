<?php namespace Groupeat\Deploy\Strategies;

use Rocketeer\Abstracts\Strategies\AbstractPolyglotStrategy;
use Rocketeer\Interfaces\Strategies\DependenciesStrategyInterface;

class DependenciesStrategy extends AbstractPolyglotStrategy implements DependenciesStrategyInterface
{
    protected $description = 'Install Composer, NPM and Gulp dependencies';

    protected $strategies = ['Composer', 'Npm', 'Bower'];

    public function install()
    {
        foreach (['node_modules', 'public/bower_components'] as $folder)
        {
            $origin = $this->paths->getFolder("current/$folder");
            $source = $this->releasesManager->getCurrentReleasePath($folder);

            $this->copy($origin, $source);
        }

        return $this->executeStrategiesMethod('install');
    }

    public function update()
    {
        return $this->executeStrategiesMethod('update');
    }
}
