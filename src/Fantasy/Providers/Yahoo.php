<?php
use OAuth\OAuth1\Service\Yahoo;
use OAuth\Common\Consumer\Credentials;
use OAuth\ServiceFactory;

class Fantasy_Providers_Yahoo extends Fantasy_Provider
{
	protected $storage;

	/**
	 * Handles retrieving data from the Yahoo fantasy provider
	 */
	public function __construct($configuration)
	{
		$clientId = $configuration['client_id'];
		$clientSecret = $configuration['client_secret'];
		$callback_url = $this->getUriObject()->getAbsoluteUri() . $this->authAppend();
		$credentials = new Credentials($clientId, $clientSecret, $callback_url);
		
		$storage = $this->initStorage('Yahoo', 'app_init');

		$serviceFactory = new \OAuth\ServiceFactory();
		$this->service = $serviceFactory->createService('Yahoo', $credentials, $storage);
	}

	/**
	 * Return service
	 * @return OAuth1\Service\AbstractService
	 */
	public function getService()
	{
		return $this->service;
	}

	/**
	 * Get authorization uri for service
	 * @return OAuth\Common\Http\Uri\Uri
	 */
	public function getAuthorizationUri()
	{
		$token = $this->service->requestRequestToken();
		$url = $this->service->getAuthorizationUri(['oauth_token' => $token->getRequestToken()]);
		return $url;
	}

	public function getServiceName()
	{
		return 'yahoo';
	}
}