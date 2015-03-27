<?php
namespace Groupeat\Restaurants\Seeders;

use Groupeat\Restaurants\Entities\OpeningWindow;
use Groupeat\Support\Database\Abstracts\Seeder;

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
            'restaurantId' => $id,
            'dayOfWeek' => 0,
            'start' => '08:30:00',
            'end' => '13:30:00',
        ]);

        // Always opened
        foreach ([0, 1, 2, 3, 4, 5, 6] as $dayOfWeek) {
            OpeningWindow::create([
                'restaurantId' => $id + 1,
                'dayOfWeek' => $dayOfWeek,
                'start' => '00:00:00',
                'end' => '23:59:59',
            ]);
        }
    }

    private function addWindows($id)
    {
        foreach ([1, 2, 3, 4, 5, 6] as $dayOfWeek) {
            OpeningWindow::create([
                'restaurantId' => $id,
                'dayOfWeek' => $dayOfWeek,
                'start' => '08:30:00',
                'end' => '22:30:00',
            ]);
        }
    }
}
