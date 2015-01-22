<?php namespace Groupeat\Restaurants\Seeders;

use Groupeat\Restaurants\Entities\OpeningWindow;
use Groupeat\Support\Database\Seeder;

class OpeningWindowsSeeder extends Seeder {

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
            'starting_at' => '08:30:00',
            'ending_at' => '13:30:00',
        ]);
    }

    private function addWindows($id)
    {
        foreach ([1, 2, 3, 4, 5, 6] as $dayOfWeek)
        {
            OpeningWindow::create([
                'restaurant_id' => $id,
                'dayOfWeek' => $dayOfWeek,
                'starting_at' => '08:30:00',
                'ending_at' => '22:30:00',
            ]);
        }
    }

}
