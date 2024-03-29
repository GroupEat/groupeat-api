<?php namespace Groupeat\Support;

use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Groupeat\Support\Pipeline\ExecuteJobInDbTransaction;
use Groupeat\Support\Providers\Abstracts\WorkbenchPackageProvider;
use Groupeat\Support\Queue\DatabaseConnector;
use Groupeat\Support\Services\LogActivity;
use Groupeat\Support\Values\AvailableLocales;
use Groupeat\Support\Values\Environment;
use Illuminate\Contracts\Bus\Dispatcher as JobDispatcher;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Events\Dispatcher as EventDispatcher;
use Illuminate\Queue\QueueServiceProvider;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\SyslogHandler;
use Psr\Log\LoggerInterface;

class PackageProvider extends WorkbenchPackageProvider
{
    protected $configValues = [
        AvailableLocales::class => 'app.available_locales',
    ];

    protected function registerPackage()
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        $this->bindValue(
            Environment::class,
            $this->app->environment()
        );

        $this->replaceApiExceptionHandler();
        $this->registerLocalPackages();
        $this->registerPapertrailLogger();
        $this->replaceDatabaseQueueConnectorIfNeeded();
    }

    protected function bootPackage()
    {
        include $this->getPackagePath('helpers.php');

        $this->app[EventDispatcher::class]->listen('*', LogActivity::class.'@logEvent');
        $this->app[JobDispatcher::class]->pipeThrough([
            LogActivity::class,
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
        }
    }

    private function registerPapertrailLogger()
    {
        if (!$this->app->isLocal()) {
            $syslog = new SyslogHandler('laravel');
            $syslog->setFormatter(new LineFormatter('%level_name%: %message% %extra%'));

            $this->app[LoggerInterface::class]->getMonolog()->pushHandler($syslog);
        }
    }

    private function replaceDatabaseQueueConnectorIfNeeded()
    {
        if (ends_with($_SERVER['SCRIPT_FILENAME'], 'codecept')) {
            $this->app['config']->set('queue.default', 'database');
            $this->app->register(QueueServiceProvider::class);

            $this->app['queue']->addConnector('database', function () {
                return new DatabaseConnector($this->app['db']);
            });
        }
    }
}
