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
            'restaurantId' => $id,
            'start' => $now->toDateTimeString(),
            'end' => $now->copy()->addHour()->toDateTimeString(),
        ]);
    }
}
