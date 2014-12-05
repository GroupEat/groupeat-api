<?php

class TestCase extends Illuminate\Foundation\Testing\TestCase {

	/**
	 * Creates the application.
	 *
	 * @return \Symfony\Component\HttpKernel\HttpKernelInterface
	 */
	public function createApplication()
	{
		$unitTesting = true;

		$testEnvironment = !empty($_SERVER['SHIPPABLE']) ? 'building' : 'testing';

		return require __DIR__.'/../../bootstrap/start.php';
	}

}
