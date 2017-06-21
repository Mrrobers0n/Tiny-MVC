<?php
/**
 * Source
 * User: Robbe Ingelbrecht
 * Date: 11/11/13 17:46
 * 
 * (C) Copyright Source 2013 - All rights reserved
 */

class CookieComponent extends Component {
	private $cookie;

	/**
	 * "Opens" or instantiates a new Cookie object
	 * Stores the instance in a private property
	 *
	 * @param $name
	 * @param null $value
	 * @param null $expire
	 * @return bool
	 */
	public function open($name, $value = null, $expire = null) {
		$this->cookie = new Cookie($name, $value, $expire);

		// Small check if there hasn't been an error
		if ($this->cookie instanceof Cookie)
			return true;
		else
			return false;
	}

	public function read() {
		if ($this->cookie instanceof Cookie)
			return $this->cookie->read();
		else
			return null;
	}

	/**
	 * Magic Call Method
	 * Attempts to call the method in the cookie class
	 *
	 * @param $method
	 * @param $args
	 * @return void
	 */
	public function __call($method, $args) {
		if ($this->cookie instanceof Cookie)
			call_user_func_array(array($this->cookie, $method), $args);
		else
			die("Please, open a cookie first");
	}
}

/**
 * Class Cookie
 *
 * Represents one cookie
 * The cookie is seen as the object
 */
class Cookie {
	private $name;                                // Name of the cookie
	private $value;                               // Value of the cookie
	private $expire;                              // Expire-date of the cookie (in a timestamp)

	private $path = '/';                          // (Domain)path wherein the cookie's available. Ex. http://www.example.com/path/
	private $domain = Config::SITE_PATH;          // Domain in which the cookie's available
	private $secure = false;                      // Us a (S)HTTP-Connection or not
	private $httponly = true;                     // Makes a cookie only available over HTTP (Ex. JavaScript can't read/write to the cookie)

	private $errors = array();                    // All errors are stored in this property

	/**
	 * Constructor
	 *
	 * @param null $name
	 * @param null $value
	 * @param null $expire
	 * @param null $extra
	 */
	public function __construct($name = null, $value = null, $expire = null, $extra = null) {

		// In case name's an array
		// Then all data has been given with an array
		// Which is also supported
		if ($name !== null && is_array($name)) {
			$this->name = $name['name'];
			$this->value = $name['value'];
			$this->expire = $name['expire'];

			if (isset($name['path']))
				$this->path = $name['path'];

			if (isset($name['domain']))
				$this->domain = $name['domain'];

			if (isset($name['secure']))
				$this->secure = $name['secure'];

			if (isset($name['httponly']))
				$this->httponly = $name['httponly'];
		}
		else {
			$this->name = $name;
			$this->value = $value;
			$this->expire = $expire;

			if (isset($extra['path']))
				$this->path = $name['path'];

			if (isset($extra['domain']))
				$this->domain = $name['domain'];

			if (isset($extra['secure']))
				$this->secure = $name['secure'];

			if (isset($extra['httponly']))
				$this->httponly = $name['httponly'];
		}
	}

	/**
	 * Writes to a new or an existing cookie
	 * If the cookie already exists, then it's first erased.
	 *
	 * @return void
	 */
	public function write() {
		// Check if we have enough data
		if ($this->_isWriteAble()) {
			if ($this->_doesExist()) {
				$this->clearCookie();

				$this->_createNewCookie();
			}
			else
				$this->_createNewCookie();
		}
	}

	/**
	 * Returns the value from the cookie
	 * If the Cookie exists, otherwise returns null
	 *
	 * @return null|String
	 */
	public function read() {
		if (isset($_COOKIE[$this->name])) {
			return $_COOKIE[$this->name];
		}
		else {
			return null;
		}
	}

	/**
	 * Set's the name of the cookie
	 *
	 * @param $str
	 * @return void
	 */
	public function setName($str) {
		$this->name = $str;
	}

	/**
	 * Set's the value of the cookie
	 *
	 * @param $val
	 * @return void
	 */
	public function setValue($val) {
		$this->value = $val;
	}

	/**
	 * Sets the expiration date or timestamp of the cookie
	 *
	 * @param $timestamp
	 * @return void
	 */
	public function setExpirationDate($timestamp) {
		if (is_numeric($timestamp))
			$this->expire = $timestamp;
		else
			$this->expire = time() + 3600;
	}

	/**
	 * Sets the path of the cookie
	 *
	 * @param $path
	 * @return void
	 */
	public function setPath($path) {
		$this->path = $path;
	}

	/**
	 * Sets the domain name of the cookie
	 *
	 * @param $str
	 * @return void
	 */
	public function setDomain($str) {
		if (strstr($str, Config::SITE_PATH))
			$this->domain = $str;
		else
			$this->domain = Config::SITE_PATH;
	}

	/**
	 * Sets whether the cookie should use a Secure-HTTP Connection or not
	 *
	 * @param $bool
	 * @return void
	 */
	public function setSecure($bool) {
		if ($bool !== true && $bool !== false)
			$this->secure = false;
		else
			$this->secure = $bool;
	}

	/**
	 * Set's whether the cookie should only be available over HTTP-connections
	 * For example; if set to true; JavaScript (client-side) can't read nor write to the cookie
	 *
	 * @param $bool
	 * @return void
	 */
	public function setHTTPOnly($bool) {
		if ($bool !== true && $bool !== false)
			$this->httponly = false;
		else
			$this->httponly = $bool;
	}

	/**
	 * Checks if all required data for writing/creating a new cookie is there
	 * If there's something missing, a false-bool is returned and the error logged.
	 *
	 * @return bool
	 */
	private function _isWriteAble() {
		$ok = true;

		// There should be a name
		if (strlen($this->name) <= 0) {
			$ok = false;
			$this->_logError('The name of the cookie should atleast be 1 char long');
		}

		// There should be a value
		if (strlen($this->value) <= 0) {
			$ok = false;
			$this->_logError('The value of the cookie should atleast be 1 char long');
		}

		// There should be a valid expiration timestamp
		if (!is_numeric($this->expire) || $this->expire <= time() && $this->expire !== 0) {
			$ok = false;
			$this->_logError('The cookie should have a valid expiration date');
		}

		return $ok;
	}

	/**
	 * Checks whether the cookie does exist or not
	 *
	 * @return bool
	 */
	private function _doesExist() {
		if (isset($_COOKIE[$this->name])) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Clears the cookie
	 *
	 * @return void
	 */
	private function clearCookie() {
		unset($_COOKIE[$this->name]);
		setcookie($this->name, '');
	}

	/**
	 * Creates a new cookie
	 * With the given data
	 *
	 * @return bool
	 */
	private function _createNewCookie() {
		if ($this->_isWriteAble()) {
			$check = setcookie(
				$this->name,
				$this->value,
				$this->expire,
				$this->path
			);

			return $check;
		}
		else
			return false;
	}

	/**
	 * Logs an error into an error-array
	 * Later to be used for looping or returning all the errors
	 *
	 * @param $error
	 * @return void
	 */
	private function _logError($error) {
		if (!empty($error))
			$this->errors[] = $error;
	}
}