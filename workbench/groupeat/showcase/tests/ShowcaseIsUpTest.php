<?php

use Groupeat\Core\Support\Tests\TestCase;

class ShowCaseIsUpTest extends TestCase {

	public function test_root_route_is_working()
	{
		$this->client->request('GET', '/');

		$this->assertTrue($this->client->getResponse()->isOk());
	}

}
