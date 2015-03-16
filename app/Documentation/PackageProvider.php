<?php namespace Groupeat\Documentation;

use Groupeat\Documentation\Services\GenerateApiDocumentation;
use Groupeat\Documentation\Values\OrderedPackages;
use Groupeat\Support\Providers\Abstracts\WorkbenchPackageProvider;

class PackageProvider extends WorkbenchPackageProvider
{
    protected function registerPackage()
    {
        $this->bindValueFromConfig(
            OrderedPackages::class,
            'documentation.order'
        );
    }
}
