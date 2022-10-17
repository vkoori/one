<?php

namespace One\Http\Request;

class Helper
{
	/**
	 * @var \One\Http\Request\Request
	 */
	private static $obj;

	/**
	 * singletone
	 * @return \One\Http\Request\Request
	 */
	public static function generate()
	{
		if ( is_null(self::$obj) ) {
			self::$obj = new Request();
		}

		return self::$obj;
	}
}