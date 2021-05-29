<?php

namespace ZerosDev\KirimWA\Laravel;

use ZerosDev\KirimWA\Client;
use ZerosDev\KirimWA\Config;
use Illuminate\Support\Facades\Facade as LaravelFacade;

class Facade extends LaravelFacade
{
	protected static function getFacadeAccessor()
	{
		return Client::class;
	}
}