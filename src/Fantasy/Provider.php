<?php

use OAuth\Common\Storage\Redis;
use Predis\Client as Predis;

abstract class Fantasy_Provider
{
	protected $service;

	protected $currentUri;

	private $_storage;

	/**
	 * Returns a new instance of the specified fantasy provider
	 * @param $ini_file_location
	 */
	public static function getInstance($provider_name, $config_array = null)
	{
		$provider_class = "Fantasy_Providers_" . ucfirst($provider_name);
		try {
			if (class_exists($provider_class)) {
				$self = new $provider_class($config_array);
				return $self;
			}
		} catch(Exception $e) {
			throw new Exception($e->getMessage());
		}
	}

	public function getUriObject()
	{
		if($this->currentUri) return $this->currentUri;

		$uriFactory = new \OAuth\Common\Http\Uri\UriFactory();
		$currentUri = $uriFactory->createFromSuperGlobalArray($_SERVER);
		$currentUri->setQuery('');
		
		$this->currentUri = $currentUri;
		return $this->currentUri;
	}

	public function initStorage($token, $state = null)
	{
		$key = $token . "_" . $state;
		if($this->_storage[$key]) {
			return $this->_storage;
		}

		$predis = new Predis(array(
			'host' => 'localhost',
			'port' => '6379'
		));

		$this->_storage[$key] = new Redis($predis, $token, $state);

		try {
			$predis->connect();
		} catch(\Predis\Connection\ConnectionException $e) {
			//handle connection exception
		}

		return $this->_storage[$key];
	}

	public function authAppend()
	{
		return "/auth/" . $this->getServiceName();
	}

	public function getService(){}

	protected function getStorageName($config)
	{
		return $this->getServiceName() . (isset($config['tag']) ? '_'.$config['tag'] : '');
	}
}