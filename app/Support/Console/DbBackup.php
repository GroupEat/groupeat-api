<?php
namespace Groupeat\Support\Console;

use App;
use Config;
use DB;
use Groupeat\Support\Console\Abstracts\Command;
use Storage;

class DbBackup extends Command
{
    protected $signature = 'db:backup
        {--s3 : Upload the backup file on Amazon S3}';

    protected $description = "Backup the DB and potentially upload the file on Amazon S3";

    public function handle()
    {
        $fileName = App::environment().'_'.date('Y-m-d-H\hi\ms\s').'.sql';
        $relativePath = "backup/database/$fileName";
        $absolutePath = storage_path($relativePath);

        $config = Config::get('database.connections.'.DB::getDefaultConnection());
        $command = 'PGPASSWORD="'.$config['password'].'"'
            .' pg_dump '.$config['database'].' -U '.$config['username'].' -h '.$config['host']
            .' -w > '.$absolutePath;

        $this->line("Exporting to $absolutePath");
        $this->process($command);

        if ($this->option('s3')) {
            $this->line("Uploading backup file to Amazon S3");
            $successful = Storage::disk('s3')->put($relativePath, fopen($absolutePath, 'r'));

            if (!$successful) {
                $this->error("The upload failed, the database has not been saved");
                return $this->fail();
            }
        }
    }
}
