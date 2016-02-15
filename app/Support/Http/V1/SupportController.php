<?php
namespace Groupeat\Support\Http\V1;

use Groupeat\Orders\Values\MaximumFoodrushInMinutes;
use Groupeat\Orders\Values\MaximumOrderFlowInMinutes;
use Groupeat\Orders\Values\MaximumPreparationTimeInMinutes;
use Groupeat\Orders\Values\MinimumFoodrushInMinutes;
use Groupeat\Support\Http\V1\Abstracts\Controller;
use Illuminate\Foundation\Application;
use Psr\Log\LoggerInterface;

class SupportController extends Controller
{
    public function ping(Application $app)
    {
        $json = $this->allJson();

        app(LoggerInterface::class)->debug(json_encode(['ping' => $json]));

        return $this->arrayResponse($json);
    }

    public function config(Application $app)
    {
        $values = [];
        $parameterClasses = [
            MinimumFoodrushInMinutes::class,
            MaximumFoodrushInMinutes::class,
            MaximumPreparationTimeInMinutes::class,
            MaximumOrderFlowInMinutes::class,
        ];

        foreach ($parameterClasses as $parameterClass) {
            $parameter = app($parameterClass);
            $values[camel_case($parameter->name())] = $parameter->value();
        }

        return $this->arrayResponse($values);
    }
}
