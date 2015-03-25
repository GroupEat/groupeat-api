<?php
namespace Groupeat\Devices\Http\V1;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Devices\Commands\AttachDevice;
use Groupeat\Devices\Entities\Device;
use Groupeat\Devices\Entities\Platform;
use Groupeat\Support\Http\V1\Abstracts\Controller;
use Symfony\Component\HttpFoundation\Response;

class DevicesController extends Controller
{
    public function platformsIndex()
    {
        return $this->collectionResponse(Platform::all(), new PlatformTransformer);
    }

    public function attach(Customer $customer)
    {
        $this->auth->assertSame($customer);

        $this->dispatch(new AttachDevice(
            $customer,
            $this->json('UUID'),
            $this->json('notificationToken'),
            Platform::findByLabelOrFail($this->json('platform')),
            $this->json('version'),
            $this->json('model'),
            (float) $this->json('latitude'),
            (float) $this->json('longitude')
        ));

        $this->statusCode = Response::HTTP_CREATED;

        return $this->noContentResponse();
    }
}
