<?php

class Fantasy_Provider
{
	protected $configuration;

	/**
	 * Create a new config Fantasy_Provider configuration. Accepts and ini file location
	 * @param $ini_file_location
	 */
	public function __construct($ini_file_location = null)
	{

		if($ini_file_location) {
			$ini = parse_ini_file($ini_file_location, true);
			if(is_array($ini) && count($ini)) {
				$this->configuration = $ini;
			}
		}
	}
}