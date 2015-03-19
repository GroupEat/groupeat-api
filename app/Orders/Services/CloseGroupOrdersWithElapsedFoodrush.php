<?php
namespace Groupeat\Orders\Services;

use Carbon\Carbon;
use Groupeat\Orders\Entities\GroupOrder;
use Symfony\Component\Console\Output\OutputInterface;

class CloseGroupOrdersWithElapsedFoodrush
{
    /**
     * @param OutputInterface $output
     *
     * @return int The number of group orders that have been closed
     */
    public function call(OutputInterface $output = null)
    {
        $model = new GroupOrder;

        $query = $model->joinable()->where($model->getTableField('ending_at'), '<', Carbon::now());

        $nb = $query->get()
            ->each(function (GroupOrder $groupOrder) use ($output) {
                $output->writeln("Closing the {$groupOrder->toShortString()}.");
                $groupOrder->close();
            })->count();

        $output->writeln("$nb group orders have been closed.");
    }
}
