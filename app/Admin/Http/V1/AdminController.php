<?php
namespace Groupeat\Admin\Http\V1;

use Auth;
use Groupeat\Admin\Entities\Admin;
use Groupeat\Support\Http\V1\Controller;

class AdminController extends Controller
{
    public function docs()
    {
        Auth::assertSameType(new Admin);

        return app('GenerateApiDocumentationService')->getHTML();
    }
}
