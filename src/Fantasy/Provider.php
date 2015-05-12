<?php

abstract class Fantasy_Provider
{
	protected $service;

	protected $currentUri;

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
	}

	public function getService(){}
}