<?php
require_once('Controller/Controller.php');
require_once('View/View.php');
require_once('Model/Model.php');

class Loader {

	/**
	 * Handles everything from the controller to model to view
	 * Checks if everything is right
	 * Try's to call the controller action
	 * Call necessary methods
	 * Renders the view
	 */
	public static function init() {
		$_GET = App::filterGET();
		
		// Checken of er params zijn meegegeven
		try {
			if (count($_GET) == 0) {
				$_GET[0] = '';
			}
			
			// Is de eerste param een controller ? Anders een pageView
			if (self::isController($_GET[0])) {
				$controllerName = self::formatAsController($_GET[0]);
				$controller = self::loadController($controllerName);
			}
			else {
				// Er is sprake van een pageview
				$controllerName = 'PagesController';
				$controller = self::loadController($controllerName);
			}
			
			$action = self::getAction($controller);
			$controller->setAction($action);

			// Try to exec the action
			try {
				self::dispatchAction($controller, $action);
			}
			catch(ActionDoesNotExistException $ex) {

				echo $action;
				// Action bestaat niet
				$controller = self::loadController('ErrorController');
				
				// Als development is ingeschakeld, dan de ware error tonen, anders een 404 pagina
				if (Config::DEVELOPMENT)
					$action = self::formatAsAction('invalidAction');
				else
					$action = self::formatAsAction('notFound');
					
				$controller->setAction($action);
				self::dispatchAction($controller, $action);
			}
			catch(MissingArgumentsException $ex) {
				$controller = self::loadController('ErrorController');
				
				// Als development is ingeschakeld, dan de ware error tonen, anders een 404 pagina
				if (Config::DEVELOPMENT)
					$action = self::formatAsAction('missingArguments');
				else
					$action = self::formatAsAction('notFound');
					
				$controller->setAction($action);
				self::dispatchAction($controller, $action);
			}
			
			// Try to render the view
			try {
				$controller->render();
			}
			catch(ViewDoesNotExistException $ex) {
				// View bestaat niet
				$controller = self::loadController('ErrorController');
				if (Config::DEVELOPMENT)
					$action = self::formatAsAction('invalidView');
				else
					$action = self::formatAsAction('notFound');
				
				$controller->setAction($action);
				self::dispatchAction($controller, $action);
				
				$controller->render();
			}
			
		}
		catch(NoValidTemplateException $ex) {
			echo 'Invalid template';
		}
		catch(IsNotControllerException $ex) {
			echo 'Controller not found';
		}
	}

	/**
	 * Checks if a given str is a controller
	 *
	 * @param $str
	 * @return bool
	 */
	private static function isController($str) {
		$str = self::formatAsController($str);
		
		if (file_exists(APP_DIR.'/Mvc/Controller/'.$str.'.php')) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Checks if a given str is a view in the pages folder.
	 *
	 * @param $view
	 * @return bool
	 */
	private static function isPageView($view) {
		if (file_exists(APP_DIR.'/Mvc/View/'.$view.'.php')) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Checks if a given view exists within the given controller.
	 *
	 * @param $controller
	 * @param $view
	 * @return bool
	 */
	private static function isView($controller, $view) {
		if (file_exists(APP_DIR.'/Mvc/View/'.$controller.'/'.$view.'.php')) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Returns an instance of the specified controller
	 *
	 * @param $str
	 * @throws ControllerNotExistsException
	 * @throws IsNotControllerException
	 * @return Controller
	 */
	private static function loadController($str) {
		$str = self::formatAsController($str);
		$app_controller = file_exists(APP_DIR.'/Mvc/Controller/'.$str.'.php');
		$lib_controller = file_exists(LIB_DIR.'/Mvc/Controller/'.$str.'.php');
		
		if ( $app_controller || $lib_controller ) {
			if ($app_controller) {
				require_once(APP_DIR.'/Mvc/Controller/'.$str.'.php');
			}
			else {
				require_once(LIB_DIR.'/Mvc/Controller/'.$str.'.php');
			}
	
			$controller = new $str();
			
			if (!$controller instanceof Controller) {
				throw new IsNotControllerException();
			}
			else {
				return $controller;
			}
		}
		else {
			throw new ControllerNotExistsException($str);
		}
	}
	
	/**
	 * Renders the content of the specified view
	 *
	 * @throws ViewDoesNotExistException
	 **/
	private static function renderView($controller, $view) {
		if (file_exists(APP_DIR.'/Mvc/View/'.$controller.'/'.$view.'.php')) {
			require_once(APP_DIR.'/Mvc/View/'.$controller.'/'.$view.'.php');
		}
		else {
			throw new ViewDoesNotExistException();
		}
	}

	/**
	 * Formats a given str as a controller
	 *
	 * @param $str
	 * @return String
	 */
	private static function formatAsController($str) {
		if (strpos($str, 'Controller') === false) {
			$str = ucfirst(strtolower($str)).'Controller';
		}
		
		return $str;
	}

	/**
	 * Formats a given string as an action (Capitalcase)
	 *
	 * @param $str
	 * @return String
	 */
	private static function formatAsAction($str) {
		if (strpos($str, '_') !== false) {
			do {
				$uPos = strpos($str, '_');
				$str = substr_replace($str, '', $uPos, 1);
				
				$str = substr_replace($str, strtoupper(substr($str, $uPos, 1)), $uPos, 1);
			}
			while(strpos($str, '_') !== false);
			
			return $str;
		}
		elseif (strpos($str, '-') !== false) {
			do {
				$uPos = strpos($str, '-');
				$str = substr_replace($str, '', $uPos, 1);

				$str = substr_replace($str, strtoupper(substr($str, $uPos, 1)), $uPos, 1);
			}
			while(strpos($str, '-') !== false);

			return $str;
		}
		else {
			return $str;
		}
	}

	/**
	 * Get's all parameters from the url besides the controller and action param
	 *
	 * @param $get
	 * @param Controller $controller
	 * @internal param $_GET
	 * @return Array
	 */
	private static function getParams($get, Controller $controller = null) {
		if (!$controller instanceof PagesController) {
			unset($get[0]); unset($get[1]);
		}
		else {
			unset($get[0]);
		}
		
		$params = array();
		
		foreach($get as $param) {
			if ($param != "")
				$params[] = $param;
		}
		
		return $params;
	}

	/**
	 * Returns the action
	 *
	 * @param Controller $controller
	 * @return String
	 */
	private static function getAction(Controller $controller) {
		// Als er een tweede param is meegegeven
		// Gebruik dan die als action
		if (isset($_GET[1]) && $_GET[1] != '' && $controller->getName() != 'Pages') {
			$action = self::formatAsAction($_GET[1]);
		}
		else {
			// Als er een eerste param is gegeven
			if (isset($_GET[0]) && $_GET[0] != '') {
				// Als de eerste param een action is
				$action = self::formatAsAction($_GET[0]);

				if (method_exists($controller, $action)) {
					$action = self::formatAsAction($_GET[0]);
				}
				else {
					$action = self::formatAsAction($controller->getDefaultAction());
//					$action = '';
				}
			}
			else {
				$action = self::formatAsAction($controller->getDefaultAction());
			}
			
		}
		
		return $action;
	}

	/**
	 * Executes an action with all it's required parameters
	 *
	 * @param $controller
	 * @param null $action
	 * @throws MissingArgumentsException
	 * @throws ActionDoesNotExistException
	 * @return void
	 */
	private static function dispatchAction($controller, $action = null) {
		//$_GET = self::filterGET();
		// Is er een action meegegeven, anders standaard actie gebruiken
		if ($action === null) {
			$action = self::getAction($controller);
		}
		
		// Als de action niet bestaat een ActionDoesNotExistException throwen
		if (!method_exists($controller, $action)) {
			throw new ActionDoesNotExistException();
			//sreturn false;
		}
		
		// Call the before filter method
		$controller->beforeFilter();
		
		// Checken welke parameters de methode heeft, en welke vereist zijn en welke niet.
		$ref = new ReflectionMethod($controller, $action);
		$params = $ref->getParameters();
		$required = 0;
		
		// Als er parameters vereist zijn
		if (count($params) > 0) {
			foreach($params as $param) {
				if (!$param->isOptional()) {
					$required++;
				}
			}
								
			$params = self::getParams($_GET, $controller);
			
			// Als het aantal gegeven parameters kleiner is dan het aantal vereiste parameters, dan zijn er parameters te kort.
			if (count($params) < $required) {
				throw new MissingArgumentsException();
			}
			else {
				switch(count($params)) {
					case 1:
						$controller->{$action}($params[0]);
					break;
								
					case 2:
						$controller->{$action}($params[0], $params[1]);
					break;
								
					case 3:
						$controller->{$action}($params[0], $params[1], $params[2]);
					break;
								
					case 4:
						$controller->{$action}($params[0], $params[1], $params[2], $params[3]);
					break;
										
					default:
						call_user_func_array(array($controller, $action), $params);
					break;
				}
			}
		}
		else {
			// Voer een action uit zonder parameters
			$controller->{$action}();
		}
		
		// Call the afterFilter method
		$controller->afterFilter();
	}
}

/**
 * Loads a class in case it's included manually
 */
function __autoload($class) {
	
	// Controllers
	if (strpos($class, 'Controller') !== false) {
		if (file_exists(APP_DIR.'/Mvc/Controller/'.$class.'.php')) {
			include_once(APP_DIR.'/Mvc/Controller/'.$class.'.php');
		}
	}
	// Exceptions
	else if (strpos($class, 'Exception') !== false) {
		if (file_exists(LIB_DIR.'/Mvc/Exceptions/'.$class.'.php')) {
			include_once(LIB_DIR.'/Mvc/Exceptions/'.$class.'.php');
		}
	}
	// Helpers
	else if (strpos($class, 'Helper') !== false) {
		if (file_exists(APP_DIR.'/Mvc/View/Helpers/'.$class.'.php')) {
			include_once(APP_DIR.'/Mvc/View/Helpers/'.$class.'.php');
		}
		else if (file_exists(LIB_DIR.'/Mvc/View/Helpers/'.$class.'.php')) {
			include_once(LIB_DIR.'/Mvc/View/Helpers/'.$class.'.php');
		}
	}
	// Components
	else if (strpos($class, 'Component') !== false) {
		if (file_exists(APP_DIR.'/Mvc/Controller/Components/'.$class.'.php')) {
			include_once(APP_DIR.'/Mvc/Controller/Components/'.$class.'.php');
		}
		else if (file_exists(LIB_DIR.'/Mvc/Controller/Components/'.$class.'.php')) {
			include_once(LIB_DIR.'/Mvc/Controller/Components/'.$class.'.php');
		}
	}
	else {
		// Config files
		if (file_exists(APP_DIR.'/Config/'.$class.'.php')) {
			include_once(APP_DIR.'/Config/'.$class.'.php');
		}
		
		// Models
		if (file_exists(APP_DIR.'/Mvc/Model/'.$class.'.php')) {
			include_once(APP_DIR.'/Mvc/Model/'.$class.'.php');
		}
		else if (file_exists(LIB_DIR.'/Mvc/Model/'.$class.'.php')) {
			include_once(LIB_DIR.'/Mvc/Model/'.$class.'.php');
		}
	}
}

/**
 * Exception handler
 * Handles an uncaught exception
 */
function exceptionHandler($ex) {
	echo '<div class="exception">';
		echo '<div class="title">';
			switch($ex->getCode()) {
				case 1001:
					echo 'MySQL database nog niet correct ingesteld!';
				break;
				
				default:
					echo 'Er is een onverwachte fout opgetreden.';
				break;
			}
		echo '</div>';
		echo '<div class="message">';
			echo $ex->getMessage().'<br />';
			
			// Gedetailleerde gegevens voor de developer alleen tonen
			if (Config::DEVELOPMENT) {
				echo 'In bestand "'.$ex->getFile().'", op regel <b>'.$ex->getLine().'</b>';
			}
		echo '</div>';
	echo '</div>';
}

set_exception_handler('exceptionHandler');