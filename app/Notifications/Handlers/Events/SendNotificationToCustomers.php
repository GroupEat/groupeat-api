<?php
namespace Groupeat\Notifications\Handlers\Events;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Notifications\Services\SelectCustomersToNotify;
use Groupeat\Notifications\Services\SendGcmNotification;
use Groupeat\Orders\Events\GroupOrderHasBeenCreated;
use Groupeat\Support\Handlers\Events\Abstracts\QueuedHandler;

class SendNotificationToCustomers extends QueuedHandler
{
    private $selectCustomersToNotify;
    private $sendGcmNotification;

    public function __construct(
        SelectCustomersToNotify $selectCustomersToNotify,
        SendGcmNotification $sendGcmNotification
    ) {
        $this->selectCustomersToNotify = $selectCustomersToNotify;
        $this->sendGcmNotification = $sendGcmNotification;
    }

    public function handle(GroupOrderHasBeenCreated $groupOrderHasBeenCreated)
    {
        $groupOrder = $groupOrderHasBeenCreated->getOrder()->groupOrder;

        $this->selectCustomersToNotify->call($groupOrder)->map(function (Customer $customer) {
            $this->sendGcmNotification->call($customer);
        });
    }
}
