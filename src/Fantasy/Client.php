<?php
/*
 * Copyright 2015 Dooplenty LLC.
 */

require_once realpath(dirname(__FILE__) . '/../../autoload.php');

class Fantasy_Client
{
	protected $provider;

	public function __construct($provider, $config_array = null)
	{

		$this->provider = Fantasy_Provider::getInstance($provider, $config_array);
		
	}

	public function getProvider()
	{
		return $this->provider;
	}

	public function __call($method, $args)
	{
		if(method_exists($this->provider, $method)) {
			try {
				return call_user_func_array(array($this->provider, $method), $args);
			} catch (\OAuth\Common\Http\Exception\TokenResponseException $e) {
				throw new Fantasy_Client_Exception_TokenNotFoundException('Error completing request.');
			} catch (Exception $e) {
				throw new Fantasy_Client_Exception('Error completing request.');
			}
		}
	}
}
?>