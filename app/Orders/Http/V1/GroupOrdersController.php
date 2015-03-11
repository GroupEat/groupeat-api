<?php
namespace Groupeat\Orders\Http\V1;

use Auth;
use Carbon\Carbon;
use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Support\Http\V1\Controller;
use Input;

class GroupOrdersController extends Controller
{
    public function index()
    {
        $query = GroupOrder::with('restaurant');

        if ((bool) Input::get('joinable')) {
            $query->joinable();
        }

        if ((bool) Input::get('around')) {
            $query->around(Input::get('latitude'), Input::get('longitude'));
        }

        return $this->collectionResponse($query->get(), new GroupOrderTransformer);
    }

    public function show(GroupOrder $groupOrder)
    {
        return $this->itemResponse($groupOrder);
    }

    public function confirm(GroupOrder $groupOrder)
    {
        Auth::assertSame($groupOrder->restaurant);

        app('ConfirmGroupOrderService')->call(
            $groupOrder,
            Carbon::createFromFormat(
                Carbon::DEFAULT_TO_STRING_FORMAT,
                Input::json('preparedAt')
            )->second(0)
        );
    }
}
