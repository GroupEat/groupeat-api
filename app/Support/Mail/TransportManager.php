<?php
namespace Groupeat\Support\Mail;

use Illuminate\Mail\TransportManager as IlluminateTransportManager;
use Psr\Log\LoggerInterface;

class TransportManager extends IlluminateTransportManager
{
    protected function createLogDriver()
    {
        return new LogTransport($this->app[LoggerInterface::class]);
    }
}
