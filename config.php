<?php

return [

	/**
	 * Default API host without trailing slash
	 * */
	'default_host'	=> 'http://yourwaserver.com',

	/**
	 * List senders
	 * 
	 * Example:
	 * 
	 * '628123456789' => [
			'host'	=> 'http://yourwaserver.com:3000',
			'key'	=> 'XXXXXXXXXXXXXXXXXX'
		]
	 * 
	 * To follow default host configuration, just put port number in 'host' key
	 * Example:
	 * 
	 * '628123456789' => [
			'host'	=> ':3000',
			'key'	=> 'XXXXXXXXXXXXXXXXXX'
		]
	 * 
	 * */
	'senders'	=> [

		'6281234567890' => [
			'host'	=> ':3001',
			'key'	=> 'your api key here'
		],

	],

];