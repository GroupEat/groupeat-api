<?php namespace Groupeat\Support;

use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Clockwork\Support\Laravel\ClockworkMiddleware;
use Clockwork\Support\Laravel\ClockworkServiceProvider;
use Groupeat\Support\Mail\TransportManager;
use Groupeat\Support\Providers\WorkbenchPackageProvider;
use Groupeat\Support\Values\AvailableLocales;
use Groupeat\Support\Values\Environment;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Mail\MailServiceProvider;
use Swift_Mailer;

class PackageProvider extends WorkbenchPackageProvider
{
    protected $require = [self::HELPERS, self::ROUTES];
    protected $console = ['DbInstall'];

    public function register()
    {
        $this->bindValueFromConfig(
            AvailableLocales::class,
            'app.available_locales'
        );

        $this->bindValue(
            Environment::class,
            $this->app->environment()
        );

        if ($this->app->isLocal()) {
            $this->app->register(IdeHelperServiceProvider::class);
            $this->app->register(ClockworkServiceProvider::class);
            $this->app[Kernel::class]->pushMiddleware(ClockworkMiddleware::class);
        }

        $this->replaceSwiftMailer();
    }

    private function replaceSwiftMailer()
    {
        $this->app->register(MailServiceProvider::class);

        $this->app['mailer']->setSwiftMailer(
            new Swift_Mailer(
                (new TransportManager($this->app))->driver()
            )
        );
    }
}
