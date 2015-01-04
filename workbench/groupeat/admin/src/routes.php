<?php

use Groupeat\Documentation\Services\GenerateApiDocumentation;

Route::group(['prefix' => 'admin'], function()
{
    Route::get('phpinfo', function()
    {
        phpinfo();
    });

    Route::get('db', function()
    {
        $data = [
            DB::getConfig('driver') => DB::getConfig('host'),
            'db' => DB::getConfig('database'),
            'username' => DB::getConfig('username'),
        ];

        $url = URL::to('packages/groupeat/admin/db/adminer.php').'?'.http_build_query($data);

        return Redirect::to($url);
    });

    Route::get('docs', function()
    {
        $path = GenerateApiDocumentation::PATH;

        if (!File::exists(public_path($path)))
        {
            artisan('api:doc');
        }

        return Redirect::to($path);
    });
});
