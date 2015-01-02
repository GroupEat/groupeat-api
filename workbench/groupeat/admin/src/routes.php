<?php

Route::group(['prefix' => 'admin'], function()
{
    Route::get('phpinfo', function()
    {
        phpinfo();
    });

    Route::get('db', function()
    {
        $adminerPath = 'packages/groupeat/admin/db/adminer.php';
        $diskPath = public_path($adminerPath);

        if (!File::exists($diskPath))
        {
            Artisan::call('asset:publish', ['--bench' => 'groupeat/admin']);
        }

        $data = [
            DB::getConfig('driver') => DB::getConfig('host'),
            'db' => DB::getConfig('database'),
            'username' => DB::getConfig('username'),
        ];

        $url = URL::to($adminerPath).'?'.http_build_query($data);

        return Redirect::to($url);
    });
});
