<?php
namespace Groupeat\Orders\Http\V1;

use Groupeat\Orders\Jobs\ConfirmGroupOrder;
use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Support\Http\V1\Abstracts\Controller;

class GroupOrdersController extends Controller
{
    public function index()
    {
        $query = GroupOrder::with('restaurant');

        if ((bool) $this->get('joinable')) {
            $query->joinable();
        }

        if ((bool) $this->get('around')) {
            $query->around($this->get('latitude'), $this->get('longitude'));
        }

        return $this->collectionResponse($query->get(), new GroupOrderTransformer);
    }

    public function show(GroupOrder $groupOrder)
    {
        return $this->itemResponse($groupOrder);
    }

    public function confirm(GroupOrder $groupOrder)
    {
        $this->auth->assertSame($groupOrder->restaurant);

        $this->dispatch(new ConfirmGroupOrder($groupOrder, $this->json('preparedAt')));
    }
}
