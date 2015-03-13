<?php
namespace Groupeat\Deploy\Http\V1;

use Groupeat\Support\Http\V1\Abstracts\Controller;

class OpCacheController extends Controller
{
    public function reset()
    {
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
    }
}
