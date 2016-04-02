<?php
namespace Groupeat\Admin\Http\V1;

use Carbon\Carbon;
use Groupeat\Admin\Entities\Admin;
use Groupeat\Admin\Jobs\MakeUpGroupOrder;
use Groupeat\Devices\Entities\Device;
use Groupeat\Documentation\Services\GenerateApiDocumentation;
use Groupeat\Notifications\Services\SendNotification;
use Groupeat\Notifications\Values\Notification;
use Groupeat\Notifications\Values\SilentNotification;
use Groupeat\Orders\Http\V1\GroupOrderTransformer;
use Groupeat\Restaurants\Entities\Restaurant;
use Groupeat\Restaurants\Support\DiscountRate;
use Groupeat\Support\Http\V1\Abstracts\Controller;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{
    public function docs(GenerateApiDocumentation $generateApiDocumentation)
    {
        $this->auth->assertSameType(new Admin);

        return $generateApiDocumentation->getHTML();
    }

    public function sendNotification(Device $device, SendNotification $sendNotification)
    {
        $this->auth->assertSameType(new Admin);

        $title = $this->optionalJson('title');
        $message = $this->optionalJson('message');

        if ($title || $message) {
            $notification = new Notification(
                $device,
                $this->json('timeToLiveInSeconds'),
                $this->json('additionalData'),
                $title,
                $message
            );
        } else {
            $notification = new SilentNotification(
                $device,
                $this->json('timeToLiveInSeconds'),
                $this->json('additionalData')
            );
        }

        return $sendNotification->call($notification, true);
    }

    public function makeUpGroupOrder(Restaurant $restaurant)
    {
        $this->auth->assertSameType(new Admin);
        $groupOrder = $this->dispatch(new MakeUpGroupOrder(
            $restaurant,
            new DiscountRate($this->json('initialDiscountRate')),
            new Carbon($this->json('endingAt'))
        ));

        return $this->itemResponse($groupOrder, new GroupOrderTransformer)
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
