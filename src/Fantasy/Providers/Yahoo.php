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
			$callbackUrl = $configuration['callback'];
		} else {
			$callbackUrl = $this->getUriObject()->getAbsoluteUri() . $this->authAppend();
		}

		$credentials = new Credentials($clientId, $clientSecret, $callbackUrl);
		$storageName = $this->getStorageName($configuration);

		$storage = $this->initStorage($storageName, 'app_init');

		$serviceFactory = new \OAuth\ServiceFactory();
		$this->service = $serviceFactory->createService($this->getServiceName(), $credentials, $storage, null, new OAuth\Common\Http\Uri\Uri("http://fantasysports.yahooapis.com/fantasy/v2/"));

		try {
			$this->checkTokenRefresh();
		} catch (\OAuth\Common\Http\Exception\TokenResponseException $e) {
			throw new Fantasy_Client_Exception_TokenSessionRefreshException("Error refreshing access token from session handle.");
		}
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

		$userGames = $this->service->request("users;use_login=1/games{$extraString};game_codes=nfl", 'GET', null, array('Content-Type: application/xml'));

		$gamesTrans = null;

		if ($userGames) {
			$method = "xmlTo".ucfirst($format);
			$gamesTrans = Fantasy_Translations_Translator::$method($userGames);
		}

		return $gamesTrans;
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
		$userLeagues = $this->service->request("users;use_login=1/games{$extraString}/leagues");

		$leaguesTrans = null;
		if ($userLeagues) {
			$method = "xmlTo".ucfirst($format);
			$leaguesTrans = Fantasy_Translations_Translator::$method($userLeagues);
		}

		return $leaguesTrans;
	}

	/**
	 * Returns all fantasy teams within a league
	 * @param  array $options options to use in fantasy request
	 * @param  string $format  format of data to return
	 * @return mixed
	 */
	public function getLeagueSettings($options, $format = 'array')
	{
		$leagueKey = $options['leagueKey'];
		$leagueSettings = $this->service->request("league/$leagueKey/settings");

		if ($leagueSettings) {
			$method = "xmlTo".ucfirst($format);
			$leagueSettings = Fantasy_Translations_Translator::$method($leagueSettings);
		}

		return $leagueSettings;
	}

	/**
	 * Returns all fantasy teams within a league
	 * @param  array $options options to use in fantasy request
	 * @param  string $format  format of data to return
	 * @return mixed
	 */
	public function getTeams($options, $format = 'array')
	{
		$leagueKey = $options['leagueKey'];
		$leagueTeams = $this->service->request("league/$leagueKey/teams");

		$teamsTrans = null;
		if ($leagueTeams) {
			$method = "xmlTo".ucfirst($format);
			$teamsTrans = Fantasy_Translations_Translator::$method($leagueTeams);
		}

		return $teamsTrans;
	}

	/**
	 * Return all players from a team
	 * @param  array $options options to use to roster request
	 * @param  string $format  format of data to return
	 * @return mixed
	 */
	public function getTeamPlayers($options, $format = 'array')
	{
		$teamKey = $options['teamKey'];
		$teamPlayers = $this->service->request("team/$teamKey/roster/players");

		$playersTrans = null;
		if ($teamPlayers) {
			$method = "xmlTo".ucfirst($format);
			$playersTrans = Fantasy_Translations_Translator::$method($teamPlayers);
		}

		return $playersTrans;
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

		return $extraString;
	}

	/**
	 * Check if have an access token and refresh if needed
	 *
	 * @return void
	 */
	protected function checkTokenRefresh()
	{
		$storage = $this->service->getStorage();
		$serviceName = ucfirst($this->getServiceName());

		if ($storage->hasAccessToken($serviceName)) {
			$token = $storage->retrieveAccessToken($serviceName);

			//need to check if we can refresh yet
			$extraParams = $token->getExtraParams();
			if(!isset($extraParams['oauth_session_handle'])) {
				return;
			}
			
			if ($token->isExpired()) {
				$this->service->refreshAccessToken($token);
			}
		}
	}
}