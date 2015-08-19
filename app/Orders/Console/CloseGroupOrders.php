<?php
namespace Groupeat\Orders\Console;

use Carbon\Carbon;
use Groupeat\Orders\Services\CloseGroupOrdersWithElapsedFoodrush;
use Groupeat\Support\Console\Abstracts\Command;

class CloseGroupOrders extends Command
{
    protected $signature = 'group-orders:close
        {--minutes= : The minutes to add to the current time (for test purpose only)}';

    protected $description = "Closes the joinable group orders with elapsed foodrush";

    private $closeGroupOrdersWithElapsedFoodrush;

    public function __construct(CloseGroupOrdersWithElapsedFoodrush $closeGroupOrdersWithElapsedFoodrush)
    {
        parent::__construct();

        $this->closeGroupOrdersWithElapsedFoodrush = $closeGroupOrdersWithElapsedFoodrush;
    }

    public function handle()
    {
        if ($this->option('minutes')) {
            Carbon::setTestNow(Carbon::now()->addMinutes($this->option('minutes')));
        }

        $nb = $this->closeGroupOrdersWithElapsedFoodrush->call($this->getOutput());

        Carbon::setTestNow(null);
    }
}
