<?php namespace Groupeat\Deploy;

use Groupeat\Deploy\Services\GitHubApi;
use Groupeat\Support\Providers\WorkbenchPackageProvider;

class PackageProvider extends WorkbenchPackageProvider {

    protected $console = ['OpCacheReset', 'Push'];

    protected function registerServices()
    {
        $this->app->bind('GitHubApi', function($app, $params)
        {
            return new GitHubApi(
                $params['username'],
                $params['password'],
                $params['output'],
                $params['onError']
            );
        });
    }

}
