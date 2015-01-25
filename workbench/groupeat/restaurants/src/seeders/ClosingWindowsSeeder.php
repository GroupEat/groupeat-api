<?php namespace Groupeat\Restaurants\Seeders;

use Carbon\Carbon;
use Groupeat\Restaurants\Entities\ClosingWindow;
use Groupeat\Support\Database\Seeder;

class ClosingWindowsSeeder extends Seeder {

    protected function insertAdditionalEntries($id)
    {
        $now = Carbon::now();

        ClosingWindow::create([
            'restaurant_id' => $id,
            'day' => $now->toDateString(),
            'from' => $now->toTimeString(),
            'to' => $now->copy()->addHour()->toTimeString(),
        ]);
    }

}
