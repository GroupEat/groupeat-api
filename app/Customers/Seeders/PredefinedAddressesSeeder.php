<?php
namespace Groupeat\Customers\Seeders;

use Groupeat\Customers\Entities\PredefinedAddress;
use Groupeat\Support\Database\Abstracts\Seeder;
use Phaza\LaravelPostgis\Geometries\Point;

class PredefinedAddressesSeeder extends Seeder
{
    protected function insertAdditionalEntries($id)
    {
        PredefinedAddress::create([
            'street' => "Boulevard des Maréchaux",
            'details' => "Foyer de l'ENSTA ParisTech",
            'city' => "Palaiseau",
            'postcode' => 91120,
            'state' => "Essonne",
            'country' => "France",
            'location' => new Point(48.7110028, 2.21874),
        ]);

        PredefinedAddress::create([
            'street' => "Avenue Augustin Fresnel",
            'details' => "BôBar",
            'city' => "Palaiseau",
            'postcode' => 91120,
            'state' => "Essonne",
            'country' => "France",
            'location' => new Point(48.71167, 2.2110769),
        ]);

        PredefinedAddress::create([
            'street' => "2 Avenue Augustin Fresnel",
            'details' => "Institut d'Optique",
            'city' => "Palaiseau",
            'postcode' => 91120,
            'state' => "Essonne",
            'country' => "France",
            'location' => new Point(48.7138983, 2.2034917),
        ]);
    }
}
