<?php namespace Groupeat\Support;

use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Clockwork\Support\Laravel\ClockworkMiddleware;
use Clockwork\Support\Laravel\ClockworkServiceProvider;
use Groupeat\Support\Mail\TransportManager;
use Groupeat\Support\Pipeline\ExecuteJobInDbTransaction;
use Groupeat\Support\Providers\Abstracts\WorkbenchPackageProvider;
use Groupeat\Support\Services\LogDomainActivity;
use Groupeat\Support\Values\AvailableLocales;
use Groupeat\Support\Values\Environment;
use Illuminate\Contracts\Bus\Dispatcher as JobDispatcher;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Events\Dispatcher as EventDispatcher;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Mail\MailServiceProvider;
use Illuminate\Queue\QueueServiceProvider;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\SyslogHandler;
use Psr\Log\LoggerInterface;
use Swift_Mailer;

class PackageProvider extends WorkbenchPackageProvider
{
    protected $configValues = [
        AvailableLocales::class => 'app.available_locales',
    ];

    protected function registerPackage()
    {
        $this->bindValue(
            Environment::class,
            $this->app->environment()
        );

        $this->replaceApiExceptionHandler();
        $this->registerLocalPackages();
        $this->registerPapertrailLogger();
        $this->replaceSwiftMailer(); // TODO: check if it can be deferred
    }

    protected function bootPackage()
    {
        include $this->getPackagePath('helpers.php');

        $this->app[EventDispatcher::class]->listen('*', LogDomainActivity::class.'@logEvent');
        $this->app[JobDispatcher::class]->pipeThrough([
            LogDomainActivity::class,
            ExecuteJobInDbTransaction::class,
        ]);
    }

    private function replaceApiExceptionHandler()
    {
        $this->app->singleton('api.exception', function ($app) {
            return $app[ExceptionHandler::class];
        });
    }

    private function registerLocalPackages()
    {
        if ($this->app->isLocal()) {
            $this->app->register(IdeHelperServiceProvider::class);
            $this->app->register(ClockworkServiceProvider::class);
            $this->app[Kernel::class]->pushMiddleware(ClockworkMiddleware::class);
        }
    }

    private function registerPapertrailLogger()
    {
        if (!$this->app->isLocal()) {
            $syslog = new SyslogHandler('laravel');
            $syslog->setFormatter(new LineFormatter('%level_name%: %message% %extra%'));

            $this->app[LoggerInterface::class]->pushHandler($syslog);
        }
    }

    private function replaceSwiftMailer()
    {
        $this->app->register(QueueServiceProvider::class);
        $this->app->register(MailServiceProvider::class);

        $this->app['mailer']->setSwiftMailer(
            new Swift_Mailer(
                (new TransportManager($this->app))->driver()
            )
        );
    }
}
