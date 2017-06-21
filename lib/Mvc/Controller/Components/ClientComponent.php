<?php

class ClientComponent extends Component {
	
	private $browser;
	private $device;
	
	
	public function __construct() {
		$this->browser = $this->_getBrowser();
		$this->device = $this->_getDevice();
	}
	
	/**
	 * Returns the current user's browser
	 *
	 * @return String
	 **/
	public function getBrowser() {
		if ($this->browser !== null) {
			return $this->browser;
		}
		else {
			$this->browser = $this->_getBrowser();
			return $this->browser;
		}
	}
	
	/**
	 * Returns the current user's browser
	 * Or returns 'unknown' if the browser couldn't be identified.
	 *
	 * @return String
	 **/
	private function _getBrowser() {
		$browser = null;
		$useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
		$browsers = array(
			'msie',						// Internet Explorer
			'firefox'	,				// Mozilla Firefox
			'chrome',					// Google Chrome
			'safari',					// Safari
			'opera',					// Opera
			'netscape'				// Netscape
		);
		
		$t = count($browsers);
		
		for($i=0; $i < $t; $i++) {
			// If we have a match
			// Set the browser to the one that matches
			// Break the loop
			if (strstr($useragent, $browsers[$i]) !== false) {
				$browser = $browsers[$i];
				break;
			}
		}
		
		return $browser;
	}
	
	/**
	 * Returns the current user's device
	 *
	 * @return String
	 **/
	public function getDevice() {
		if ($this->device !== null) {
			return $this->device;
		}
		else {
			$this->device = $this->_getDevice();
			return $this->device;
		}
	}
	
	/**
	 * Returns the current user's device
	 * Or returns 'unknown' if the device couldn't be identified.
	 *
	 * @return String
	 **/
	private function _getDevice() {
		$useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
		$devices = array(
			'windows',						
			'apple' => array(
				'ipod',
				'ipad',
				'iphone'
			),							
			'android',							
			'linux',						
		);
		
		$ret = null;
		
		foreach($devices as $key => $device) {
			if (!is_array($device)) {
				if (strstr($useragent, $device) !== false) {
					$ret = $device;
					break;
				}
			}
			else {
				foreach($device as $subdevice) {
					if (strstr($useragent, $subdevice) !== false) {
						$ret = $key.'/'.$subdevice;
						break;
					}
				}
				
				if ($ret === null) {
					// If device is an array (instead of single string)
					// Loop through each index and find a match
					if (is_array($device)) {
						// Find match
						foreach($device as $dev) {
							if (strstr($useragent, $dev) !== false) {
								$ret = $dev;
								break;
							}
						}
					}
					else {
						if (strstr($useragent, $device) !== false) {
							$ret = $device;
							break;
						}
					}
				}
				else {
					break;
				}
			}
		}
		
		return $ret;
	}
}