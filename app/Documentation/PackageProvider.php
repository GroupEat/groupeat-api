<?php namespace Groupeat\Documentation;

use Groupeat\Documentation\Services\GenerateApiDocumentation;
use Groupeat\Documentation\Values\OrderedPackages;
use Groupeat\Support\Providers\WorkbenchPackageProvider;

class PackageProvider extends WorkbenchPackageProvider
{
    protected $console = ['GenerateApiDocumentation'];

    public function register()
    {
        parent::register();

        $this->bindValueFromConfig(
            OrderedPackages::class,
            'documentation.order'
        );
    }
}
