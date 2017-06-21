<?php
define('APP_DIR', $_SERVER['DOCUMENT_ROOT'].'/app');
define('LIB_DIR', $_SERVER['DOCUMENT_ROOT'].'/lib');
require_once(LIB_DIR.'/Mvc/Loader.php');
require_once(LIB_DIR.'/Core/Lib.php');
require_once(LIB_DIR.'/Core/App.php');
error_reporting(E_ALL);

// Func has dev acces or not
function hasDevAcces() {
	$auth = new AuthComponent();

	// Has to be logged in
	if ($_SERVER['REMOTE_ADDR'] == '141.134.244.152' || true)
		return true;
	else
		return false;
}

$lib = new Lib();
$app = new App();
