<?php
namespace Groupeat\Admin\Console;

use Groupeat\Admin\Services\GenerateAdminerFiles;
use Groupeat\Support\Console\Abstracts\Command;

class Adminer extends Command
{
    protected $name = 'adminer';
    protected $description = "Generate the Adminer files to manage the DB";

    private $generateAdminerFiles;

    public function __construct(GenerateAdminerFiles $generateAdminerFiles)
    {
        parent::__construct();

        $this->generateAdminerFiles = $generateAdminerFiles;
    }

    public function fire()
    {
        $this->generateAdminerFiles->call($this->getOutput());
    }
}
