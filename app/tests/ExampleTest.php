<?php

class ExampleTest extends TestCase {

	/**
	 * A basic functional test example.
	 *
	 * @return void
	 */
	public function testBasicExample()
	{
        var_dump($_SERVER);
        var_dump($_ENV);
        var_dump(php_uname());

        $this->client->request('GET', '/');

		$this->assertTrue($this->client->getResponse()->isOk());

        Artisan::call('groupeat:migrate');

        var_dump((string) User::first());
	}

}
