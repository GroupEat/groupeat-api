<?php

class ExampleTest extends TestCase {

	public function testBasicExample()
	{
		$this->client->request('GET', '/');

		$this->assertTrue($this->client->getResponse()->isOk());
	}

}
