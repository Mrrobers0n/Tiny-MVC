<?php

class Client {
	private $browser;
	
	
	public function __construct() {
		$this->browser = $this->getBrowser();
	}
	
	public function getBrowser() {
		$browsers = array('msie', 'firefox', 'chrome', 'opera','safari');
		$browser_info = strtolower($_SERVER['HTTP_USER_AGENT']);
		$browser = 'unknown';
		
		for($i=0; $i < count($browsers); $i++) {
			if (strstr($browser_info, $browsers[$i]) !== false) {
				$browser = $browsers[$i];
				break;
			}
		}
		
		return $browser;
	}
	
	public function getDevice() {
		$devices = array('iphone','ipad','android','silk','blackberry', 'touch', 'windows', 'apple');
		$browser_info = strtolower($_SERVER['HTTP_USER_AGENT']);
		$device = 'unkown';
		
		for($i=0; $i < count($devices); $i++) {
			if (strstr($browser_info, $devices[$i]) !== false) {
				$device = $devices[$i];
				break;
			}
		}
		
		return $device;
	}
}