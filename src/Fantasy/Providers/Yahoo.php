<?php
use OAuth\OAuth1\Service\Yahoo;
use OAuth\Common\Consumer\Credentials;
use OAuth\ServiceFactory;

class Fantasy_Providers_Yahoo extends Fantasy_Provider
{
	protected $storage;

	protected $format = 'array';

	/**
	 * Handles retrieving data from the Yahoo fantasy provider
	 */
	public function __construct($configuration)
	{
		$clientId = $configuration['client_id'];
		$clientSecret = $configuration['client_secret'];
		$callback_url = $this->getUriObject()->getAbsoluteUri() . $this->authAppend();
		$credentials = new Credentials($clientId, $clientSecret, $callback_url);

		$storageName = $this->getStorageName($configuration);

		$storage = $this->initStorage($storageName, 'app_init');

		$serviceFactory = new \OAuth\ServiceFactory();
		$this->service = $serviceFactory->createService($this->getServiceName(), $credentials, $storage, null, new OAuth\Common\Http\Uri\Uri("http://fantasysports.yahooapis.com/fantasy/v2/"));
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
		$uri = $this->service->getAuthorizationUri(['oauth_token' => $token->getRequestToken()]);
		return $uri;
	}

	public function getServiceName()
	{
		return 'yahoo';
	}

	/**
	 * Return fantasy games user has authorized
	 * @param  array $options additional options to append to query for games
	 * @param string $format format to return
	 * @return mixed
	 */
	public function getGames($options = array(), $format = 'array')
	{
		$extraString = $this->getExtraString($options);

		$user_games = $this->service->request("users;use_login=1/games${$extraString};game_codes=nfl", 'GET', null, array('Content-Type: application/xml'));

		$games = null;

		if ($user_games) {
			$method = "xmlTo".ucfirst($format);
			$user_games = Fantasy_Translations_Translator::$method($games);

			$games = $user_games['users']['user']['games']['game'];
		}

		return $games;
	}

	public function getLeagues($options, $format = 'array')
	{
		$extraString = $this->getExtraString($options);
		$leagues = $this->service->request("users;use_login=1/games{$extraString}/leagues");
	}

	protected function getExtraString($options)
	{
		$extraString = get_class($this).time();
		global $$extraString;

		array_walk($options, function($value, $key, $extraString){
			global $$extraString;
			$$extraString .= ";$key=$value";
		}, $extraString);
	}
}