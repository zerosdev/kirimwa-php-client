<?php

namespace ZerosDev\KirimWA;

use Exception, InvalidArgumentException;
use ReflectionProperty;
use ReflectionException;

class Config
{
	/**
     * @var boolean Mark configuration has been loaded or not
     */
	public static $configLoaded = false;


	/**
     * @var string Global API baseurl
     */
	public static $default_host;


	/**
     * @var array List of senders
     */
	public static $senders;


	/**
     * @param array $configs Apply configuration to system
     * 
     */
	public static function apply(array $configs = [])
	{
		foreach( $configs as $key => $value ) {
			try {
				$prop = new ReflectionProperty(Config::class, $key);
				if( $prop->isStatic() ) {
					self::${$key} = $value;
				}
			} catch(Exception $e) {
				if( !($e instanceof ReflectionException) ) {
					throw $e;
				}
			}
		}

		self::$configLoaded = true;
	}

	/**
     * @param string $path Path to configuration file
     * 
     * @return void|InvalidArgumentException
     */
	public static function load(string $path)
	{
		if( file_exists($path) && is_readable($path) ) {
			self::apply(require($path));
			return;
		}

		throw new InvalidArgumentException("Failed to load config from: {$path}");
	}
}