<?php
namespace Groupeat\Support\Services;

use Groupeat\Support\Commands\Abstracts\Command;
use Groupeat\Support\Entities\Abstracts\Entity;
use Groupeat\Support\Events\Abstracts\Event;
use Illuminate\Support\Collection;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use ReflectionMethod;

class LogDomainActivity
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function logEvent($event)
    {
        if ($event instanceof Event) {
            $this->log($event);
        }
    }

    public function handle(Command $command, $next)
    {
        $this->log($command);

        return $next($command);
    }

    private function log($activity)
    {
        $class = get_class($activity);
        $logData = $this->getLogDataFor($activity);

        $this->logger->info($class.' '.json_encode($logData));
    }

    private function getLogDataFor($activity)
    {
        $getMethodPrefix = 'get';
        $class = get_class($activity);

        $methodNames = Collection::make((new ReflectionClass($class))->getMethods())
            ->filter(function (ReflectionMethod $reflectionMethod) {
                return $reflectionMethod->isPublic();
            })
            ->map(function (ReflectionMethod $reflectionMethod) {
                return $reflectionMethod->name;
            })
            ->filter(function ($methodName) use ($getMethodPrefix) {
                return starts_with($methodName, $getMethodPrefix)
                    && !str_contains(lcfirst($methodName), 'password');
            });

        $logData = [];

        foreach ($methodNames as $methodName) {
            $name = lcfirst(substr($methodName, strlen($getMethodPrefix)));
            $value = $activity->$methodName();

            if ($value instanceof Entity) {
                $value = $value->getKey();
            }

            $logData[$name] = $value;
        }

        return $logData;
    }
}
