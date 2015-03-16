<?php
namespace Groupeat\Orders\Commands;

use Carbon\Carbon;
use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Support\Commands\Abstracts\Command;

class ConfirmGroupOrder extends Command
{
    private $groupOrder;

    /**
     * @var Carbon
     */
    private $preparedAt;

    /**
     * @param GroupOrder $groupOrder
     * @param string     $preparedAt
     */
    public function __construct(GroupOrder $groupOrder, $preparedAt)
    {
        $this->groupOrder = $groupOrder;

        $this->preparedAt = Carbon::createFromFormat(
            Carbon::DEFAULT_TO_STRING_FORMAT,
            $preparedAt
        )->second(0);
    }

    public function getGroupOrder()
    {
        return $this->groupOrder;
    }

    public function getPreparedAt()
    {
        return $this->preparedAt;
    }
}
