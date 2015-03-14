<?php
namespace Groupeat\Support\Http\V1;

use Groupeat\Support\Http\V1\Abstracts\Controller;
use Illuminate\Foundation\Application;

class DebugController extends Controller
{
    public function debug(Application $app)
    {
        if (!$app->isLocal()) {
            return;
        }

        //
    }
}
