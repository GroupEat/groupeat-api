<?php

class ExampleTest extends TestCase {

	public function testBasicExample()
	{
		var_dump(App::environment());

		var_dump($_SERVER);

        $this->client->request('GET', '/');

		$this->assertTrue($this->client->getResponse()->isOk());

        artisan('groupeat:migrate');

        $this->assertInstanceOf('User', User::first());
	}

}
