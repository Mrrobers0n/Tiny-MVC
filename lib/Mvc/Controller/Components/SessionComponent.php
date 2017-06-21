<?php
Lib::import('Session/Session.php');

class SessionComponent extends Component {
	// Session instantiate
	private $session;
	
	public function __construct() {
		$this->session = new Session();
	}

	/**
	 * Reads a value from the given index and path
	 *
	 * @param $dir
	 * @return mixed
	 */
	public function read($dir) {
		return $this->session->read($dir);
	}

	/**
	 * Stores a value within the given index and path
	 *
	 * @param $path
	 * @param $val
	 */
	public function write($path, $val) {
		$this->session->write($path, $val);
	}
	
	/**
	 * Set a flash message
	 * A message that only shows itself for a given amount or during a given period.
	 *
	 * Opts:
	 *  - index("amount")     | Amount of times a flash-message should be shown (in page views)
	 *  - index("until")      | A timestamp until the flash-message should be shown
	 *  - index("div")        | The class of the wrapper-div
	 *
	 **/
	public function setFlash($message, $opts = null) {
		$options = array();
		
		// Handle options
		if ($opts != null && is_array($opts)) {
			// Times a flash message should be shown
			$options['amount'] = (isset($opts['amount'])) ? $opts['amount'] : Config::FLASH_DEFAULT_SHOWTIMES;
			
			// If there's a date (timestamp) given until the message should be shown, then we'll use that, otherwise amount;
			$options['until'] = (isset($opts['until'])) ? $opts['until'] : null;
			
			// If there isn't a div given, then use a div with a standard class
			$options['div'] = (isset($opts['div'])) ? $opts['div'] : Config::FLASH_DEFAULT_CLASS;
		}
		else {
			// Times a flash message should be shown
			$options['amount'] = Config::FLASH_DEFAULT_SHOWTIMES;
			
			// If there's a date (timestamp) given until the message should be shown, then we'll use that, otherwise amount;
			$options['until'] =  null;
			
			// If there isn't a div given, then use a div with a standard class
			$options['div'] = Config::FLASH_DEFAULT_CLASS;
		}
		
		if ($this->read('flashMessages') !== null) {
			$prev = $this->read('flashMessages');
			$options['message'] = $message;
			
			$prev[] = $options;
			
			$this->write('flashMessages', $prev);
		}
		else {
			$flash = array();
			$options['message'] = $message;
			
			$flash[] = $options;
			$this->write('flashMessages', $flash);
		}
		
	}
}