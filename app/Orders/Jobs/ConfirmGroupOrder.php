<?php
namespace Groupeat\Orders\Jobs;

use Carbon\Carbon;
use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Support\Jobs\Abstracts\Job;

class ConfirmGroupOrder extends Job
{
    private $groupOrder;
    private $preparedAt;

    public function __construct(GroupOrder $groupOrder, Carbon $preparedAt)
    {
        $this->groupOrder = $groupOrder;
        $this->preparedAt = $preparedAt;
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
