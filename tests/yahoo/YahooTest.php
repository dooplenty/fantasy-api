<?php
class YahooTest extends BaseTest
{
	public function testYahoo()
	{

		$config = $this->parseIni('yahoo');
		$client = $this->getClient('yahoo', $config);
		$this->assertTrue($client instanceof Fantasy_Client);
	}

	public function testGetAuthUrl()
	{
		$config = $this->parseIni('yahoo');
		$client = $this->getClient('yahoo', $config);

		$this->assertContains('request_auth', $client->getAuthorizationUri()->getPath());	
	}

	public function testGetRequestToken()
	{
		$config = $this->parseIni('yahoo');
		$client = $this->getClient('yahoo', $config);
		$token = $client->getService()->requestRequestToken();
		$this->assertTrue(is_string($token->getRequestToken()), "Type of value returned is " . gettype($token));
	}

	public function testGetRequestTokenFromStorage()
	{
		$config = $this->parseIni('yahoo');
		$client = $this->getClient('yahoo', $config);
		$storage = $client->getService()->getStorage();
		$this->assertTrue($storage instanceof \OAuth\Common\Storage\TokenStorageInterface);
		$this->assertTrue($storage->hasAccessToken('Yahoo'));
	}

	public function testGetGames()
	{
		$xml = $this->getSample('yahoo', 'games');

		$json = Fantasy_Translations_Translator::xmlToJson($xml);
		$this->assertTrue(is_string($json));

		$php = Fantasy_Translations_Translator::xmlToArray($xml);
		$this->assertTrue(is_array($php));

		$obj = Fantasy_Translations_Translator::xmlToObject($xml);
		$this->assertTrue(is_object($obj));
	}
}
