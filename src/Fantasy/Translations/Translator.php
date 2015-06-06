<?php

class Fantasy_Translations_Translator
{
	public static function xmlToJson($xml)
	{
		$simple = simplexml_load_string($xml);
		return json_encode($simple, JSON_PRETTY_PRINT);
	}

	public static function xmlToArray($xml)
	{
		$obj = self::xmlToObject($xml);

		$object_to_array = function($obj) use (&$object_to_array) {
			if(is_object($obj)) $obj = (array) $obj;
		    if(is_array($obj)) {
		        $new = array();
		        foreach($obj as $key => $val) {
		            $new[$key] = $object_to_array($val);
		        }
		    }
		    else $new = $obj;
		    return $new;
		};

		return $object_to_array($obj);
	}

	public static function xmlToObject($xml)
	{
		$json = self::xmlToJson($xml);
		return json_decode($json);
	}
}