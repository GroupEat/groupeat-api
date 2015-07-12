<?php namespace Groupeat\Messaging;

use Groupeat\Messaging\Services\SendSmsThroughAndroidGateway;
use Groupeat\Support\Providers\Abstracts\WorkbenchPackageProvider;

class PackageProvider extends WorkbenchPackageProvider
{
    protected function registerPackage()
    {
        $this->app->singleton(SendSmsThroughAndroidGateway::class, function ($app) {
            $config = $app['config']->get('messaging.sms_gateway');

            return new SendSmsThroughAndroidGateway($config['email'], $config['password'], $config['device']);
        });
    }
}
