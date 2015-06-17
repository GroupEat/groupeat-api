<?php
namespace Groupeat\Orders\Jobs;

use Carbon\Carbon;
use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Support\Jobs\Abstracts\Job;

class ConfirmGroupOrder extends Job
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
