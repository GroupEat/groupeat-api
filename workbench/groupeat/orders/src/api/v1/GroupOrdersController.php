<?php namespace Groupeat\Orders\Api\V1;

use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Support\Api\V1\Controller;
use Input;

class GroupOrdersController extends Controller {

    public function show(GroupOrder $groupOrder)
    {
        return $this->itemResponse($groupOrder);
    }

    public function index()
    {
        $query = GroupOrder::with('restaurant');

        if (Input::has('opened'))
        {
            $query->opened();
        }

        if (Input::has('around'))
        {
            $query->around(Input::get('latitude'), Input::get('longitude'));
        }

        return $this->collectionResponse($query->get(), new GroupOrderTransformer);
    }

}
