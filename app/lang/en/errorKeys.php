<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Error keys from validation errors
	|--------------------------------------------------------------------------
	|
	| The API must sent an error key when something wrong happens. When this
	| is due to some validation errors, we need to make an error key from the
	| failed rules.
	|
	*/

	'min' => 'tooShort',
	'regex' => 'formatIsInvalid',
	'unique' => 'alreadyTaken',

];
