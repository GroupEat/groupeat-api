<?php namespace Groupeat\Documentation\Console;

use Groupeat\Support\Console\Command;

class GenerateApiDocumentationCommand extends Command {

	protected $name = 'api:docs';
	protected $description = "Generate the API documentation";


	public function fire()
	{
        $generator = $this->getLaravel()->make('GenerateApiDocumentationService');

        $generator->call($this->output);

        $this->info('Documentation generated!');
	}

}
