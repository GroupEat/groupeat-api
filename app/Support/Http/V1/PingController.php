<?php
namespace Groupeat\Support\Http\V1;

use Groupeat\Support\Http\V1\Abstracts\Controller;
use Illuminate\Foundation\Application;

class PingController extends Controller
{
    public function ping(Application $app)
    {
        return $this->arrayResponse(['data' => 'pong']);
    }
}
