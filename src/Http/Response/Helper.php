<?php

namespace One\Http\Response;

use One\Http\Request\Request;

class Helper
{
	/**
	 * @var \One\Http\Response\Response
	 */
	private static $obj;

	/**
	 * singletone
	 * @return \One\Http\Response\Response
	 */
	public static function generate(?Request $request)
	{
		if ( is_null(self::$obj) && is_null($request) ) {
			throw new \Exception("Request has not be send!");
		}

		if ( is_null(self::$obj) ) {
			self::$obj = new Response(request: $request);
		}

		return self::$obj;
	}
}