<?php
namespace Groupeat\Support\Http\V1;

use Groupeat\Support\Http\V1\Abstracts\Controller;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Application;
use Psr\Log\LoggerInterface;

class SupportController extends Controller
{
    public function ping(Application $app)
    {
        $json = $this->json()->all();

        app(LoggerInterface::class)->debug(json_encode(['ping' => $json]));

        return $this->arrayResponse($json);
    }

    public function config(Repository $config)
    {
        $values = [];
        $availableFields = [
            'orders.minimum_foodrush_in_minutes',
            'orders.maximum_foodrush_in_minutes',
            'orders.maximum_preparation_time_in_minutes',
        ];

        foreach ($availableFields as $key) {
            $values[camel_case($key)] = $config->get($key);
        }

        return $this->arrayResponse($values);
    }
}
