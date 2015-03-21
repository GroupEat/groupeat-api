<?php
namespace Groupeat\Devices\Http\V1;

use Groupeat\Devices\Commands\AttachDevice;
use Groupeat\Devices\Entities\Device;
use Groupeat\Devices\Entities\OperatingSystem;
use Groupeat\Support\Http\V1\Abstracts\Controller;
use Symfony\Component\HttpFoundation\Response;

class DevicesController extends Controller
{
    public function operatingSystemsIndex()
    {
        return $this->collectionResponse(OperatingSystem::all(), new OperatingSystemTransformer);
    }

    public function attach()
    {
        $this->dispatch(new AttachDevice(
            $this->auth->customer(),
            $this->json('hardwareId'),
            $this->json('notificationToken'),
            OperatingSystem::findOrFail($this->json('operatingSystemId')),
            $this->json('operatingSystemVersion'),
            $this->json('model'),
            (float) $this->json('latitude'),
            (float) $this->json('longitude')
        ));

        $this->statusCode = Response::HTTP_CREATED;

        return $this->noContentResponse();
    }
}
