<?php
use OAuth\OAuth1\Service\Yahoo;
use OAuth\Common\Storage\Session;
use OAuth\Common\Consumer\Credentials;
use OAuth\ServiceFactory;

class Fantasy_Providers_Yahoo extends Fantasy_Provider
{
	/**
	 * Handles retrieving data from the Yahoo fantasy provider
	 */
	public function __construct($configuration)
	{
		$clientId = $configuration['client_id'];
		$clientSecret = $configuration['client_secret'];
		$credentials = new Credentials($clientId, $clientSecret, $this->getUriObject()->getAbsoluteUri());
		$storage = new Session();

		$serviceFactory = new \OAuth\ServiceFactory();
		$this->service = $serviceFactory->createService('Yahoo', $credentials, $storage);
	}

	public function getService()
	{
		return $this->service;
	}
}