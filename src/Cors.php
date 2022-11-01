<?php 

namespace One;

/**
 * 
 */
final class Cors {

	use ConfigTrait;

	public static function set() {
		if (!empty( self::$conf['allowed_origins'] ))
			header('Access-Control-Allow-Origin: '.implode(',', self::$conf['allowed_origins'] ));

		if (!empty( self::$conf['allowed_headers'] ))
			header('Access-Control-Allow-Headers: '.implode(',', self::$conf['allowed_headers'] ));

		if (!empty( self::$conf['allowed_methods'] ))
			header('Access-Control-Allow-Methods: '.implode(',', self::$conf['allowed_methods'] ));

		if (!empty( self::$conf['exposed_headers'] ))
			header('Access-Control-Expose-Headers: '.implode(',', self::$conf['exposed_headers'] ));

		header('Access-Control-Max-Age: '.self::$conf['max_age']);
		header('Access-Control-Allow-Credentials: '.self::$conf['supports_credentials']);
	}

}