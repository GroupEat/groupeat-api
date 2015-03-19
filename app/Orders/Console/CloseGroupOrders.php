<?php
namespace Groupeat\Orders\Console;

use Carbon\Carbon;
use Groupeat\Orders\Services\CloseGroupOrdersWithElapsedFoodrush;
use Groupeat\Support\Console\Abstracts\Command;
use Symfony\Component\Console\Input\InputOption;

class CloseGroupOrders extends Command
{
    protected $name = 'group-orders:close';
    protected $description = "Closes the joinable group orders with elapsed foodrush";

    private $closeGroupOrdersWithElapsedFoodrush;

    public function __construct(CloseGroupOrdersWithElapsedFoodrush $closeGroupOrdersWithElapsedFoodrush)
    {
        parent::__construct();

        $this->closeGroupOrdersWithElapsedFoodrush = $closeGroupOrdersWithElapsedFoodrush;
    }

    public function fire()
    {
        if ($this->option('minutes')) {
            Carbon::setTestNow(Carbon::now()->addMinutes($this->option('minutes')));
        }

        $nb = $this->closeGroupOrdersWithElapsedFoodrush->call($this->getOutput());

        Carbon::setTestNow(null);
    }

    protected function getOptions()
    {
        return [
            ['minutes', 'm', InputOption::VALUE_REQUIRED, 'The minutes to add to the current time (for test purpose)'],
        ];
    }
}
