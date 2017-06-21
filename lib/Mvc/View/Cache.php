<?php

class Cache {
	
	private $filename;
	private $folder;
	private $interval;
	
	private $dir;
	private $filedir;
	
	public function __construct($fn, $folder, $interval = Config::CACHE_ST_INTERVAL) {
		$this->filename = $fn;
		$this->folder = $folder;
		$this->interval = $interval;
	}
	
	public function getContent() {
		ob_start();
		include_once($this->filedir);
		
		$html = ob_get_contents(); ob_get_clean();
		
		return $html;
	}

	/**
	 * Checks if the cached file should be overwritten by a new one
	 *
	 * @return bool
	 */
	public function mustCache() {
		if ($this->_checkDir($this->folder)) {
			if ($this->_checkFile($this->filename)) {
				$fd = filemtime($this->filedir);
				
				// If the caching interval period is over, then cache the "new" content
				if ($fd < (time()-$this->interval)) {
					return true;
				}
				else {
					return false;
				}
			}
			else {
				return true;
			}
		}
		else {
			return true;
		}
	}

	/**
	 * Caches content into a file
	 *
	 * @param $content
	 */
	public function cache($content) {
		$fb = fopen($this->filedir, 'w');
		fwrite($fb, $content, strlen($content));
	}

	/**
	 * Checks if the given file exists within the cache
	 *
	 * @param $filename
	 * @return bool
	 */
	private function _checkFile($filename) {
		if (file_exists($this->dir.$filename)) {
			$this->filedir = $this->dir.$filename;
			return true;
		}
		else {
			$this->filedir = $this->dir.$filename;
			return false;
		}
	}

	/**
	 * Checks if the for the file exists and attempts to create if if it doesn't
	 *
	 * @param $folder
	 * @return bool
	 */
	private function _checkDir($folder) {
		$dir = APP_DIR.'/'.Config::CACHE_PATH.'/'.$folder;
		
		if (empty($folder)) {
			return false;
		}
		
		if (!is_dir($dir)) {
			if(mkdir($dir, 0755)) {
				$this->dir = $dir;
				return true;
			}
			else {
				return false;
			}
		}
		else {
			$this->dir = $dir;
			return true;
		}
	}

	/**
	 * Write a file to the cache
	 *
	 * @param $fn             | Filename
	 * @param $content        | Content of the file
	 * @param string $dir     | Directory (Folder of the file)  ex. dir = 'xml/' -> Cache/xml/
	 * @return bool           |
	 */
	public static function write($fn, $content, $dir = '') {
		//self::_checkDir($dir);
		$dir = APP_DIR.'/'.Config::CACHE_PATH.'/'.$dir;

		if (!is_dir($dir)) {
			mkdir($dir, 0755);
		}

		$fs = fopen($dir.$fn, 'w');
		$result = fwrite($fs, $content);
		fclose($fs);

		if ($result === false) {
			return false;
		}
		else {
			return true;
		}
	}

	/**
	 * Read a file from cache
	 *
	 * @param $fn
	 * @param string $dir
	 * @return bool|string
	 */
	public static function read($fn, $dir = '') {
		$dir = APP_DIR.'/'.Config::CACHE_PATH.'/'.$dir;

		if (file_exists($dir.$fn)) {
			$ret = file_get_contents($dir.$fn);
		}
		else {
			$ret = null;
		}

		return $ret;
	}
}