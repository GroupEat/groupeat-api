<?php
namespace Groupeat\Restaurants\Seeders;

use Carbon\Carbon;
use Groupeat\Restaurants\Entities\ClosingWindow;
use Groupeat\Support\Database\Abstracts\Seeder;

class ClosingWindowsSeeder extends Seeder
{
    protected function insertAdditionalEntries($id)
    {
        $now = Carbon::now();

        ClosingWindow::create([
            'restaurant_id' => $id,
            'from' => $now->toDateTimeString(),
            'to' => $now->copy()->addHour()->toDateTimeString(),
        ]);
    }
}
