<?php namespace Groupeat\Documentation;

use Groupeat\Documentation\Services\GenerateApiDocumentation;
use Groupeat\Support\Providers\WorkbenchPackageProvider;

class PackageProvider extends WorkbenchPackageProvider {

    protected $console = ['GenerateApiDocumentation'];


    public function boot()
    {
        parent::boot();

        $this->app->bind('GenerateApiDocumentationService', function($app)
        {
            return new GenerateApiDocumentation(
                $app['files'],
                $app['config']->get('documentation::order')
            );
        });
    }

}
