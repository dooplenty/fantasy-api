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
		if (isset($configuration['callback'])) {
			$callback_url = $configuration['callback'];
		} else {
			$callback_url = $this->getUriObject()->getAbsoluteUri() . $this->authAppend();
		}

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

		$user_games = $this->service->request("users;use_login=1/games{$extraString};game_codes=nfl", 'GET', null, array('Content-Type: application/xml'));

		$games = null;

		if ($user_games) {
			$method = "xmlTo".ucfirst($format);
			$user_games = Fantasy_Translations_Translator::$method($user_games);

			$games = $user_games['users']['user']['games']['game'];
		}

		return $games;
	}

	/**
	 * Return all leagues for user
	 * @param  array $options options to pass to league request (i.e. is_available => 1)
	 * @param  string $format  options to use in fantasy request
	 * @return mixed
	 */
	public function getLeagues($options, $format = 'array')
	{
		$extraString = $this->getExtraString($options);
		$user_leagues = $this->service->request("users;use_login=1/games{$extraString}/leagues");

		$leagues = null;
		if ($user_leagues) {
			$method = "xmlTo".ucfirst($format);
			$leagues_array = Fantasy_Translations_Translator::$method($user_leagues);

			$leagues = $leagues_array['users']['user']['games']['game'];
		}

		return $leagues;
	}

	/**
	 * Returns all fantasy teams within a league
	 * @param  array $options options to use in fantasy request
	 * @param  string $format  format of data to return
	 * @return mixed
	 */
	public function getTeams($options, $format = 'array')
	{
		$leagueKey = $options['league_key'];
		$user_teams = $this->service->request("league/$leagueKey/teams");

		$teams = null;
		if ($user_teams) {
			$method = "xmlTo".ucfirst($format);
			$teams_array = Fantasy_Translations_Translator::$method($user_teams);

			$teams = $teams_array['league']['teams']['team'];
		}

		return $teams;
	}

	/**
	 * Returns the properly format parameters
	 * @param  array $options options to format
	 * @return string 		  query string in format ;key=value
	 */
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