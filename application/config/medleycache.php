<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config = [
	'telegram' => [
		'channel' => '@dummy_channel_name',
		'bot' => [
			'name' => '@dummy_bot_name',
			'token' => 'retrieve_token_from_botfather',
		],
	],
	'metacache' => 'file', /*I recommend to use Redis*/
];
