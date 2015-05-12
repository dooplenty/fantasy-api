<?php
class YahooTest extends BaseTest
{
	public function testYahoo()
	{

		$config = $this->parseIni('yahoo');
		$client = $this->getClient('yahoo', $config);
	}

	public function testGetAuthUrl()
	{
		$config = $this->parseIni('yahoo');
		$client = $this->getClient('yahoo', $config);
		$this->assertContains('request_auth', $client->getAuthUrl());	
	}

	public function testGetRequestToken()
	{
		$config = $this->parseIni('yahoo');
		$client = $this->getClient('yahoo', $config);
		$token = $client->getService()->requestRequestToken();
		$this->assertTrue(is_string($token->getRequestToken()), "Type of value returned is " . gettype($token));
	}
}
