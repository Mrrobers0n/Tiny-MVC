<?php

class App {
	protected static $pdo = null;
	public static $execcount = 0;
	
	public function __construct() {
		Loader::init();
	}

	/**
	 * Returns a PDO instance
	 * Uses config database details for connection
	 *
	 * @throws Exception
	 * @return PDO $pdo
	 * @modifier static
	 */
	public static function getPDO() {
		if (self::$pdo !== null && self::$pdo instanceof PDO) {
			return self::$pdo;
		}
		else {
			if (
					Config::DATABASE_HOST != ''
			&&	Config::DATABASE_USERNAME != ''
			&&	Config::DATABASE_PASSWORD != ''
			&&	Config::DATABASE_NAME != '') {
				
				self::$pdo = new PDO('mysql:dbname='.Config::DATABASE_NAME.';host='.Config::DATABASE_HOST, Config::DATABASE_USERNAME, Config::DATABASE_PASSWORD);
				
				return self::$pdo;
			}
			else {
				throw new Exception("Database not fully configured");
			}
		}
	}
	
	/**
	 * Safely filters a HTTPGET request against XSS and SQL Injection
	 *
	 * @return Array
	 **/
	public static function filterGET() {
		$_GET = explode('/', substr($_SERVER['REQUEST_URI'], 1));
		$get = array();
		$i = 0;
		
		foreach($_GET as $val) {
			$get[$i++] = htmlspecialchars(addslashes($val));
		}
		
		return $get;
	}

	/**
	 * Loads a specific file from the app library
	 *
	 * @param $fn
	 * @return void
	 */
	public static function load($fn) {
		if (file_exists(APP_DIR.'/Lib/'.$fn)) {
			include_once(APP_DIR.'/Lib/'.$fn);
		}
	}
}