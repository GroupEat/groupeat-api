<?php
namespace Groupeat\Admin\Http\V1;

use Groupeat\Admin\Entities\Admin;
use Groupeat\Documentation\Services\GenerateApiDocumentation;
use Groupeat\Support\Http\V1\Abstracts\Controller;

class AdminController extends Controller
{
    public function docs(GenerateApiDocumentation $generateApiDocumentation)
    {
        $this->auth->assertSameType(new Admin);

        return $generateApiDocumentation->getHTML();
    }
}
