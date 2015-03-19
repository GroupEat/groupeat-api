<?php
namespace Groupeat\Deploy\Http\V1;

use Groupeat\Support\Http\V1\Abstracts\Controller;

class OpCacheController extends Controller
{
    public function clear()
    {
        if (function_exists('opcache_reset')) {
            opcache_reset();
            echo "Opcache has been cleared for HTTP.";
        } else {
            echo "Opcache not enabled for HTTP.";
        }
    }
}
