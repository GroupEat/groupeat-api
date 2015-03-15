<?php
namespace Groupeat\Documentation\Console;

use Groupeat\Documentation\Services\GenerateApiDocumentation;
use Groupeat\Support\Console\Command;

class GenerateApiDocumentationCommand extends Command
{
    protected $name = 'api:docs';
    protected $description = "Generate the API documentation";

    private $generateApiDocumentation;

    public function __construct(GenerateApiDocumentation $generateApiDocumentation)
    {
        parent::__construct();

        $this->generateApiDocumentation = $generateApiDocumentation;
    }

    public function fire()
    {
        $this->generateApiDocumentation->call($this->output);

        $this->info('Documentation generated!');
    }
}
