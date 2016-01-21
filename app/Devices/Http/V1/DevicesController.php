<?php
namespace Groupeat\Devices\Http\V1;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Devices\Jobs\AttachDevice;
use Groupeat\Devices\Entities\Device;
use Groupeat\Devices\Entities\Platform;
use Groupeat\Devices\Jobs\UpdateDevice;
use Groupeat\Support\Http\V1\Abstracts\Controller;
use Symfony\Component\HttpFoundation\Response;

class DevicesController extends Controller
{
    public function platformsIndex()
    {
        return $this->collectionResponse(Platform::all(), new PlatformTransformer);
    }

    public function index(Customer $customer)
    {
        $this->auth->assertSame($customer);

        return $this->collectionResponse(Device::where('customerId', $customer->id)->get());
    }

    public function attach(Customer $customer)
    {
        $this->auth->assertSame($customer);

        $device = $this->dispatch(new AttachDevice(
            $customer,
            $this->json('UUID'),
            $this->json('notificationToken'),
            Platform::findByLabelOrFail($this->json('platform')),
            $this->json('platformVersion'),
            $this->json('model'),
            $this->json('location') ? getPointFromLocationArray($this->json('location')) : null
        ));

        return $this->itemResponse($device)->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(Customer $customer, Device $device)
    {
        $this->auth->assertSame($customer);

        $device = $this->dispatch(new UpdateDevice(
            $device,
            $customer,
            $this->json('platformVersion') ?: '',
            $this->json('notificationToken') ?: '',
            $this->json('notificationId') ?: '',
            $this->json('location') ? getPointFromLocationArray($this->json('location')) : null
        ));

        return $this->itemResponse($device);
    }
}
