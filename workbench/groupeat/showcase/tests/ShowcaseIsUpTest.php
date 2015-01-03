<?php

use Groupeat\Support\Tests\TestCase;

class ShowCaseIsUpTest extends TestCase {

	public function test_root_route_is_working()
	{
		$this->client->request('GET', '/');

        var_dump($this->client->getResponse());

		$this->assertTrue($this->client->getResponse()->isOk());
	}

}
