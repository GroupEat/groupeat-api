<?php
namespace Groupeat\Support\Database;

use File;

class SeedersOrderer
{
    /**
     * Returns the Seeders in the same order than the Migrations in order
     * to avoid any foreign key problem.
     *
     * @return array
     */
    public static function getList()
    {
        $seeders = [];

        foreach (listGroupeatPackagesWithoutSupport() as $package) {
            $seedersDirectory = app_path("$package/Seeders");

            if (File::isDirectory($seedersDirectory)) {
                foreach (File::files($seedersDirectory) as $file) {
                    $filename = pathinfo($file, PATHINFO_FILENAME);
                    $class = 'Groupeat\\'.ucfirst($package).'\\Seeders\\'.$filename;
                    $seeders[static::getTimestamp($filename)] = $class;
                }
            }
        }

        ksort($seeders);

        return $seeders;
    }

    private static function getTimestamp($seederName)
    {
        $ending = '_'.str_replace('Seeder', 'Migration.php', $seederName);

        $files = File::glob(base_path("database/migrations/*$ending"));

        if (count($files) == 1) {
            $migrationName = pathinfo($files[0], PATHINFO_BASENAME);

            return str_replace($ending, '', $migrationName);
        }
    }
}
