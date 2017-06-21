<?php

class Session {
	public function __construct() {
		// Instantiate session
		if (!isset($_SESSION)) {
			$this->_startSession();
			$this->_hijackCheck();
		}
		else {
			$this->_hijackCheck();
		}
	}
	
	/**
	 * Initializes a session if it hasn't been done yet
	 * If it's the first init of a session, then assign it with some framework vars
	 *
	 **/
	private function _startSession() {
		session_start();
		
		// Assign framework variables if needed
		if (!isset($_SESSION[Config::SESSION_PREFIX.'inited'])) {
			$this->_assignFrameworkVars();
		}
	}
	
	/**
	 * Assign's some framework vars to the session
	 * Also used for session hijack check
	 **/
	private function _assignFrameworkVars() {
		// Time session was inited
		$_SESSION[Config::SESSION_PREFIX.'inited'] = time();
		
		// Assign the user agent
		$_SESSION[Config::SESSION_PREFIX.'user_agent'] = md5(Config::SALT.$_SERVER['HTTP_USER_AGENT']);
		
		// Assign the user's IP
		$_SESSION[Config::SESSION_PREFIX.'ip'] = md5(Config::SALT.$_SERVER['REMOTE_ADDR']);
		
		// Last hijack check
		$_SESSION[Config::SESSION_PREFIX.'hijackcheck'] = time();
	}
	
	/**
	 * Check's if the session hasn't been hijacked yet
	 **/
	private function _hijackCheck() {
	
		// Regenerate session id within half an hour of interval
		if ($_SESSION[Config::SESSION_PREFIX.'hijackcheck'] <= ( time() - 1800)) {
			session_regenerate_id();
			$_SESSION[Config::SESSION_PREFIX.'hijackcheck'] = time();
		}
		
		if ($_SESSION[Config::SESSION_PREFIX.'user_agent'] != md5(Config::SALT.$_SERVER['HTTP_USER_AGENT'])) {
			//die('Session Hijack detected!');
		}
		
		if ($_SESSION[Config::SESSION_PREFIX.'ip'] != md5(Config::SALT.$_SERVER['REMOTE_ADDR'])) {
			//die('Session Hijack detected!');
		}
	}
	
	/**
	 * Stores a given value in a session with the given path
	 * Uses dots (.) or (/) as delimiter
	 **/
	public function write($dir, $val) {
		$path = explode('.', $dir);
		
		if (count($path) <= 1) {
			$path = explode('/', $dir);
		}
		
		$t = count($path);
		
		switch($t) {
			case 0:
			break;
			
			case 1:
				$_SESSION[Config::SESSION_PREFIX.$path[0]] = $val;
			break;
			
			case 2:
				$_SESSION[Config::SESSION_PREFIX.$path[0]][$path[1]] = $val;
			break;
			case 3:
				$_SESSION[Config::SESSION_PREFIX.$path[0]][$path[1]][$path[2]] = $val;
			break;
			case 4:
				$_SESSION[Config::SESSION_PREFIX.$path[0]][$path[1]][$path[2]][$path[3]] = $val;
			break;
			case 5:
				$_SESSION[Config::SESSION_PREFIX.$path[0]][$path[1]][$path[2]][$path[3]][$path[4]] = $val;
			break;
			case 6:
				$_SESSION[Config::SESSION_PREFIX.$path[0]][$path[1]][$path[2]][$path[3]][$path[4]][$path[5]] = $val;
			break;
			
			case 7:
				$_SESSION[Config::SESSION_PREFIX.$path[0]][$path[1]][$path[2]][$path[3]][$path[4]][$path[5]][$path[6]] = $val;
			break;
			
			default:
				return null;
			break;
		}
	}

	/**
	 * Returns the value of asked path
	 *
	 * @param $dir
	 * @return mixed
	 */
	public function read($dir) {
		$path = explode('.', $dir);
		
		if (count($path) <= 1) {
			$path = explode('/', $dir);
		}
		
		$t = count($path);
		
		return $this->_getValue($t, $path);
	}

	/**
	 * Returns the value of the index with the given path
	 * Or null if it's to long or doesn't exist
	 *
	 * @param $t
	 * @param $path
	 * @return null
	 */
	private function _getValue($t, $path) {
		switch($t) {
			case 0:
			
			break;
			
			case 1:
				if (isset($_SESSION[Config::SESSION_PREFIX.$path[0]])) {
					return $_SESSION[Config::SESSION_PREFIX.$path[0]];
				}
			break;
			
			case 2:
				if (isset($_SESSION[Config::SESSION_PREFIX.$path[0]][$path[1]])) {
					return $_SESSION[Config::SESSION_PREFIX.$path[0]][$path[1]];
				}
			break;
			
			case 3:
				if (isset($_SESSION[Config::SESSION_PREFIX.$path[0]][$path[1]][$path[2]])) {
					return $_SESSION[Config::SESSION_PREFIX.$path[0]][$path[1]][$path[2]];
				}
			break;
			
			case 4:
				if (isset($_SESSION[Config::SESSION_PREFIX.$path[0]][$path[1]][$path[2]][$path[3]])) {
					return $_SESSION[Config::SESSION_PREFIX.$path[0]][1][2][3];
				}
			break;
			
			case 5:
				if (isset($_SESSION[Config::SESSION_PREFIX.$path[0]][$path[1]][$path[2]][$path[3]][$path[4]])) {
					return $_SESSION[Config::SESSION_PREFIX.$path[0]][$path[1]][$path[2]][$path[3]][$path[4]];
				}
			break;
			
			case 6:
				if (isset($_SESSION[Config::SESSION_PREFIX.$path[0]][$path[1]][$path[2]][$path[3]][$path[4]][$path[5]])) {
					return $_SESSION[Config::SESSION_PREFIX.$path[0]][$path[1]][$path[2]][$path[3]][$path[4]][$path[5]];
				}
			break;
			
			case 7:
				if (isset($_SESSION[Config::SESSION_PREFIX.$path[0]][$path[1]][$path[2]][$path[3]][$path[4]][$path[5]][$path[6]])) {
					return $_SESSION[Config::SESSION_PREFIX.$path[0]][$path[1]][$path[2]][$path[3]][$path[4]][$path[5]][$path[6]];
				}
			break;
			
			default:
				return null;
			break;
		}
		
		// If nothing has been returned, then the index doesn't exist so return null
		return null;
	}

	/**
	 * Remove an index value with the given path
	 *
	 * @param $dir
	 * @return null
	 */
	public function remove($dir) {
		$path = explode('.', $dir);
		
		if (count($path) <= 1) {
			$path = explode('/', $dir);
		}
		
		$t = count($path);
		
		switch($t) {
			case 0:
			break;
			
			case 1:
				unset($_SESSION[Config::SESSION_PREFIX.$path[0]]);
			break;
			
			case 2:
				unset($_SESSION[Config::SESSION_PREFIX.$path[0]][$path[1]]);
			break;
			case 3:
				unset($_SESSION[Config::SESSION_PREFIX.$path[0]][$path[1]][$path[2]]);
			break;
			case 4:
				unset($_SESSION[Config::SESSION_PREFIX.$path[0]][$path[1]][$path[2]][$path[3]]);
			break;
			case 5:
				unset($_SESSION[Config::SESSION_PREFIX.$path[0]][$path[1]][$path[2]][$path[3]][$path[4]]);
			break;
			case 6:
				unset($_SESSION[Config::SESSION_PREFIX.$path[0]][$path[1]][$path[2]][$path[3]][$path[4]][$path[5]]);
			break;
			
			case 7:
				unset($_SESSION[Config::SESSION_PREFIX.$path[0]][$path[1]][$path[2]][$path[3]][$path[4]][$path[5]][$path[6]]);
			break;
			
			default:
				return null;
			break;
		}
	}
}