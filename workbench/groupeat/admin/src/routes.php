<?php

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

        if (App::isLocal())
        {
            $data['passwd'] = DB::getConfig('password');
        }

        if (Input::get('testing'))
        {
            $data['db'] = $data['db'].'-testing';
        }

        // 'packages/groupeat/admin/db/adminer.php'
        $url = URL::to('admin/db/connection').'?'.http_build_query($data);

        return Redirect::to($url);
    });

    Route::any('db/connection', function()
    {
        include '/home/vagrant/groupeat/current/workbench/groupeat/admin/src/resources/adminer.php';
    });

    Route::get('docs', function()
    {
        return App::make('GenerateApiDocumentationService')->getHTML();
    });
});
