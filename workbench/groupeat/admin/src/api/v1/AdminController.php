<?php namespace Groupeat\Admin\Api\V1;

use App;
use Auth;
use Groupeat\Admin\Entities\Admin;
use Groupeat\Support\Api\V1\Controller;
use Input;

class AdminController extends Controller {

    public function docs()
    {
        Auth::assertSameType(new Admin);

        $forceRegenerate = App::isLocal() && ((bool) Input::get('regenerate'));

        return app('GenerateApiDocumentationService')->getHTML($forceRegenerate);
    }

}
