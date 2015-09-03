<?php
namespace Groupeat\Documentation\Console;

use Groupeat\Documentation\Services\GenerateApiDocumentation;
use Groupeat\Support\Console\Abstracts\Command;

class ApiDocumentation extends Command
{
    protected $signature = 'docs';
    protected $description = "Generate the API documentation";

    private $generateApiDocumentation;

    public function __construct(GenerateApiDocumentation $generateApiDocumentation)
    {
        parent::__construct();

        $this->generateApiDocumentation = $generateApiDocumentation;
    }

    public function handle()
    {
        $this->generateApiDocumentation->call($this->output);

        $this->info('Documentation generated!');
    }
}
