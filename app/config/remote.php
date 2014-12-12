<?php

return [

    /*
	|--------------------------------------------------------------------------
	| Application Domain Name
	|--------------------------------------------------------------------------
	|
	| This is the domain name of the application. Usually it is close to the
    | app.url parameter but it can be different because of the environments.
	|
	*/

    'domain' => 'groupeat.fr',

	/*
	|--------------------------------------------------------------------------
	| Default Remote Connection Name
	|--------------------------------------------------------------------------
	|
	| Here you may specify the default connection that will be used for SSH
	| operations. This name should correspond to a connection name below
	| in the server list. Each connection will be manually accessible.
	|
	*/

	'default' => 'production',

	/*
	|--------------------------------------------------------------------------
	| Remote Server Connections
	|--------------------------------------------------------------------------
	|
	| These are the servers that will be accessible via the SSH task runner
	| facilities of Laravel. This feature radically simplifies executing
	| tasks on your servers, such as deploying out these applications.
	|
	*/

	'connections' => [

		'production' => [
			'host'      => '178.62.158.190',
			'username'  => 'vagrant',
			'password'  => '',
			'key'       => '/home/vagrant/.ssh/id_rsa',
			'keyphrase' => '',
			'root'      => '/home/vagrant',
		],

        'production_root' => [
            'host'      => '178.62.158.190',
            'username'  => 'root',
            'password'  => '',
            'key'       => '/home/vagrant/.ssh/id_rsa',
            'keyphrase' => '',
            'root'      => '/root',
        ],

	],

	/*
	|--------------------------------------------------------------------------
	| Remote Server Groups
	|--------------------------------------------------------------------------
	|
	| Here you may list connections under a single group name, which allows
	| you to easily access all of the servers at once using a short name
	| that is extremely easy to remember, such as "web" or "database".
	|
	*/

	'groups' => [

		'web' => ['production']

	],

    /*
    |--------------------------------------------------------------------------
    | Shippable Deployment Key
    |--------------------------------------------------------------------------
    |
    | This SSH public key is used by Shippable in order to deploy to the
    | production server after a successful build.
    |
    */

    'shippable_key' => "ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQDAXklV4dV/s5V8lgyTLLfqF+sXGWUfhhey6jNBOiObeYrwM3lDilqkC6YIz36kkfxSzmMhROHaiXl+aHcTUPb3UVHr5iKiGuVuIuvTT3XNNzsoo6zcsidKUI1qWm0k4dwd/Jb27B1NflGZcD0QwLyHuN0r4KDg4woxB/NjUhAie/XhIAMi9Xi8x5uAekdp5aVtoBpu5M8GJbwW1vQ3fB6CaXDDlR5rrdY0oyiKcJEVLJuam4g70GIh8b67+gBrD+U4Zs1ntRXE8dW7DLs1vtCw2ECYm9UcBEe5G+rxE5XHN1HfigpNvEmEViPNhdfpfkz8tY1TFWgaddKkEXZKncVR 546a9689adedef14000bbd2d",

];
