<?php

class ExampleTest extends TestCase {

	/**
	 * A basic functional test example.
	 *
	 * @return void
	 */
	public function testBasicExample()
	{
        var_dump(App::environment());

        $this->client->request('GET', '/');

		$this->assertTrue($this->client->getResponse()->isOk());
	}

}
