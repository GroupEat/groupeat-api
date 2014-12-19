<?php namespace Groupeat\Core\Support\Database;

use Illuminate\Support\Facades\File;

class SeedersOrderer {

    /**
     * Returns the Seeders in the same order than the Migrations in order
     * to avoid any foreign key problem
     *
     * @return array
     */
    public static function getList()
    {
        $seeders = [];

        foreach (listGroupeatPackagesWithoutCore() as $package)
        {
            $seedersDirectory = workbench_path($package, 'seeders');

            if (File::isDirectory($seedersDirectory))
            {
                foreach (File::files($seedersDirectory) as $file)
                {
                    $filename = pathinfo($file, PATHINFO_FILENAME);
                    $class = 'Groupeat\\'.ucfirst($package).'\\Seeders\\'.$filename;
                    $seeders[static::getTimestamp($package, $filename)] = $class;
                }
            }
        }

        ksort($seeders);

        return $seeders;
    }

    private static function getTimestamp($package, $seederName)
    {
        $ending = '_Create'.str_replace('Seeder', 'Table.php', $seederName);

        $files = File::glob(workbench_path($package, 'migrations/*'.$ending));

        if (count($files) == 1)
        {
            $migrationName = pathinfo($files[0], PATHINFO_BASENAME);

            return str_replace($ending, '', $migrationName);
        }
    }

}
