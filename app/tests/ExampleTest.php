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

        $this->client->request('GET', '/');

		$this->assertTrue($this->client->getResponse()->isOk());

        artisan('groupeat:migrate');

        var_dump((string) User::first());
	}

}
