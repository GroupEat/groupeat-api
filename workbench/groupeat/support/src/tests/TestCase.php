<?php namespace Groupeat\Support\Tests;

use Illuminate\Foundation\Testing\TestCase as IlluminateTestCase;

abstract class TestCase extends IlluminateTestCase {

	/**
	 * Creates the application.
	 *
	 * @return \Symfony\Component\HttpKernel\HttpKernelInterface
	 */
	public function createApplication()
	{
		$unitTesting = true;

		$testEnvironment = !empty($_SERVER['SHIPPABLE']) ? 'building' : 'testing';

        return require __DIR__.'/../../../../../bootstrap/start.php';
	}

}