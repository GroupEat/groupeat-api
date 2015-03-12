<?php
namespace Groupeat\Restaurants\Seeders;

use Groupeat\Restaurants\Entities\OpeningWindow;
use Groupeat\Support\Database\Seeder;

class OpeningWindowsSeeder extends Seeder
{
    protected function makeEntry($id, $max)
    {
        $this->addWindows($id);
    }

    protected function insertAdditionalEntries($id)
    {
        $this->addWindows($id);

        OpeningWindow::create([
            'restaurant_id' => $id,
            'dayOfWeek' => 0,
            'from' => '08:30:00',
            'to' => '13:30:00',
        ]);

        // Always opened
        foreach ([0, 1, 2, 3, 4, 5, 6] as $dayOfWeek) {
            OpeningWindow::create([
                'restaurant_id' => $id + 1,
                'dayOfWeek' => $dayOfWeek,
                'from' => '00:00:00',
                'to' => '23:59:59',
            ]);
        }
    }

    private function addWindows($id)
    {
        foreach ([1, 2, 3, 4, 5, 6] as $dayOfWeek) {
            OpeningWindow::create([
                'restaurant_id' => $id,
                'dayOfWeek' => $dayOfWeek,
                'from' => '08:30:00',
                'to' => '22:30:00',
            ]);
        }
    }
}
