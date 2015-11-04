<?php namespace Groupeat\Mailing;

use Groupeat\Mailing\Support\TransportManager;
use Groupeat\Support\Providers\Abstracts\WorkbenchPackageProvider;
use Groupeat\Support\Values\Environment;
use Illuminate\Mail\MailServiceProvider;
use Swift_Mailer;

class PackageProvider extends WorkbenchPackageProvider
{
    protected function registerPackage()
    {
        if ($this->app[Environment::class]->isLocal()) {
            $this->app->register(MailServiceProvider::class);

            $this->app['mailer']->setSwiftMailer(
                new Swift_Mailer(
                    (new TransportManager($this->app))->driver()
                )
            );
        }
    }
}
