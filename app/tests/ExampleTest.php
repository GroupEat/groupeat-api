<?php

class ExampleTest extends TestCase {

	public function testBasicExample()
	{
		$this->client->request('GET', '/');

		$this->assertTrue($this->client->getResponse()->isOk());

        artisan('groupeat:migrate');

        $this->assertInstanceOf('User', User::first());
	}

}
