<?php
/*
 * Copyright 2015 Dooplenty
 */
class BaseTest extends PHPUnit_Framework_TestCase
{
	public function __construct()
	{
		@session_start();
		parent::__construct();
	}

	public function getClient($provider, $config_array = null)
	{
		$client = new Fantasy_Client($provider, $config_array);
		return $client;
	}

	protected function parseIni($provider)
	{
		$ini_settings = parse_ini_file(__DIR__.'/'.$provider.'/test.ini');
		return $ini_settings;
	}
}