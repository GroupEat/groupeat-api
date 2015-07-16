<?php
namespace Groupeat\Support\Http\V1;

use Groupeat\Support\Http\V1\Abstracts\Controller;
use Illuminate\Foundation\Application;
use Psr\Log\LoggerInterface;

class PingController extends Controller
{
    public function ping(Application $app)
    {
        $json = $this->json()->all();

        app(LoggerInterface::class)->debug(json_encode(['ping' => $json]));

        return $this->arrayResponse(['data' => $json]);
    }
}
