<?php

use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Support\Tests\TestCase;

class UsersSeederTest extends TestCase {

    public function test_no_user_credentials_in_rdbms_without_seeding()
    {
        artisan('db-install');

        $this->assertNull(UserCredentials::all()->first());
    }

	public function test_user_credentials_in_rdbms_after_seeding()
	{
		artisan('db-install', ['--with-seeds' => true, '--entries' => 5]);

        $this->assertInstanceOf('Groupeat\Auth\Entities\UserCredentials', UserCredentials::all()->first());
	}

}
