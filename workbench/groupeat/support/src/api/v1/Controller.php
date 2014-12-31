<?php namespace Groupeat\Support\Api\V1;

use Dingo\Api\Routing\ControllerTrait as ApiController;
use Illuminate\Routing\Controller as IlluminateController;

abstract class Controller extends IlluminateController {

    use ApiController;

}
