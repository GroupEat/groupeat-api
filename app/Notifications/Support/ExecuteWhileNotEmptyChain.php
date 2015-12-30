<?php
namespace Groupeat\Notifications\Support;

use Closure;
use Illuminate\Support\Collection;
use Psr\Log\LoggerInterface;

class ExecuteWhileNotEmptyChain
{
    private $logger;
    private $logPrefix;
    private $logContext;
    private $isFirstCall = true;
    private $result;

    public function __construct(LoggerInterface $logger, string $logPrefix, array $logContext = [])
    {
        $this->logger = $logger;
        $this->logPrefix = $logPrefix;
        $this->logContext = $logContext;
        $this->result = new Collection;
    }

    public function get()
    {
        return $this->result;
    }

    public function next(Closure $callback, string $step = '')
    {
        if (!$this->isFirstCall && $this->result->isEmpty()) {
            return $this;
        }

        $this->result = $this->isFirstCall ? $callback() : $callback($this->result);

        if ($step) {
            $this->log($step, $this->result);
        }

        if ($this->isFirstCall) {
            $this->isFirstCall = false;
        }

        return $this;
    }

    public function log(string $step, Collection $result)
    {
        $this->logger->debug($this->logPrefix.'#'.$step.': '.$result->toJson(), $this->logContext);
    }
}
