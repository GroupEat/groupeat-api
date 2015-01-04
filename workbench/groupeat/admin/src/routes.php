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

        $url = URL::to('packages/groupeat/admin/db/adminer.php').'?'.http_build_query($data);

        return Redirect::to($url);
    });
});
