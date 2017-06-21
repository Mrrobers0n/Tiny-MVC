<?php

class Lib {

	/**
	 * Includes a file or returns false if file doesn't exist
	 * Files should be in lib-dir
	 *
	 * @param $path
	 * @return void/bool
	 */
	public static function import($path) {
		if (substr($path, 0, 1) != '/') {
			$path = '/'.$path;
		}
		
		if (file_exists(LIB_DIR.$path)) {
			include_once(LIB_DIR.$path);
		}
		else {
			return false;
		}
	}
}

?>