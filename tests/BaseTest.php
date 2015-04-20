<?php
/*
 * Copyright 2015 Dooplenty
 */
class BaseTest extends PHPUnit_Framework_TestCase
{
	public function __construct()
	{
		parent::__construct();
	}

	public function getClient($provider)
	{
		$client = new Fantasy_Client($provider);
		return $client;
	}
}