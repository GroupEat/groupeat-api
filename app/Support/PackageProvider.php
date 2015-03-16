<?php namespace Groupeat\Support;

use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Clockwork\Support\Laravel\ClockworkMiddleware;
use Clockwork\Support\Laravel\ClockworkServiceProvider;
use Groupeat\Support\Mail\TransportManager;
use Groupeat\Support\Pipeline\ExecuteCommandInDbTransaction;
use Groupeat\Support\Providers\Abstracts\WorkbenchPackageProvider;
use Groupeat\Support\Services\LogDomainActivity;
use Groupeat\Support\Values\AvailableLocales;
use Groupeat\Support\Values\Environment;
use Illuminate\Contracts\Bus\Dispatcher as CommandDispatcher;
use Illuminate\Contracts\Events\Dispatcher as EventDispatcher;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Mail\MailServiceProvider;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\SyslogHandler;
use Psr\Log\LoggerInterface;
use Swift_Mailer;

class PackageProvider extends WorkbenchPackageProvider
{
    protected function registerPackage()
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

        if ($this->app->environment('production')) {
            $syslog = new SyslogHandler('laravel');
            $syslog->setFormatter(new LineFormatter('%level_name%: %message% %extra%'));

            $this->app[LoggerInterface::class]->pushHandler($syslog);
        }

        $this->replaceSwiftMailer();
    }

    protected function bootPackage()
    {
        include $this->getPackagePath('helpers.php');

        $this->app[EventDispatcher::class]->listen('*', LogDomainActivity::class.'@logEvent');
        $this->app[CommandDispatcher::class]->pipeThrough([
            LogDomainActivity::class,
            ExecuteCommandInDbTransaction::class,
        ]);
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
