<?php namespace Groupeat\Documentation;

use Groupeat\Documentation\Services\GenerateApiDocumentation;
use Groupeat\Documentation\Values\OrderedPackages;
use Groupeat\Support\Providers\Abstracts\WorkbenchPackageProvider;

class PackageProvider extends WorkbenchPackageProvider
{
    protected $configValues = [
        OrderedPackages::class => 'documentation.order',
    ];
}
