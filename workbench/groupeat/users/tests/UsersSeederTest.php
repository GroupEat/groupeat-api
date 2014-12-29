<?php

use Groupeat\Users\Entities\User;
use Groupeat\Support\Tests\TestCase;

class UsersSeederTest extends TestCase {

    public function test_no_users_in_rdbms_without_seeding()
    {
        artisan('db-install');

        $this->assertNull(User::all()->first());
    }

	public function test_users_in_rdbms_after_seeding()
	{
		artisan('db-install', ['--with-seeds' => true]);

        $this->assertInstanceOf('Groupeat\Users\Entities\User', User::all()->first());
	}

}
