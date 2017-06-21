<?php

class Controller {
	protected $view;
	protected $action;
	
	private $extractData = array();
	private $fetchData = array();
	private $cssData = array();
	private $scriptData = array();
	private $imgData = array();
	
	public $models = array();
	
	/**
	 * Components
	 * All to be loaded components should be given by an array
	 **/
	protected $components = array();

	private $components_holder = array();

	/**
	 * Components
	 * All to be loaded components, but that should be generally implemented through the project
	 * @var array
	 **/
	private $components_static = array(
		'Client',
		'Session',
		'Cookie',
		'Security',
		'Request',
		'Time',
	);
	
	/**
	 * Helpers
	 * All to be loaded helpers should be given by an array
	 **/
	protected $helpers = array();

	/**
	 * Helpers
	 * All to be loaded helpers, but that should be generally implemented through the project
	 **/
	private $helpers_static = array(
		'Session',
		'Pagination',
	);
	
	/**
	 * Default view when none is given
	 **/
	public $defaultAction = 'index';
	
	/**
	 * Is called before a view is rendered
	 **/
	public function beforeRender() {}
	
	/**
	 * Is called after a view is rendered
	 **/
	public function afterRender() {}
	
	/**
	 * Is called before a controller action is executed
	 **/
	public function beforeFilter() {}
	
	/**
	 * Is called after a controller action is executed
	 **/
	public function afterFilter() {}
	
	public final function __construct() {

		// Standaard Components laden
		foreach($this->components_static as $component) {
			$this->_loadComponent($component.'Component');
		}

		// Extra Components laden
		foreach($this->components as $component) {
			$this->_loadComponent($component.'Component');
		}
		
		// Models laden
		foreach($this->models as $model) {
			$this->loadModel($model);
		}
	}

	/**
	 * Set the action to be used which view is rendered
	 *
	 * @param $action
	 * @return void
	 */
	final public function setAction($action) {
		$this->action = $action;
	}

	/**
	 * Returns the current action (or page)
	 *
	 * @return mixed
	 */
	final public function getAction() {
		return $this->action;
	}

	/**
	 * Instantiate the view
	 *
	 * @throws NoValidTemplateException
	 * @throws ViewDoesNotExistException
	 * @return void
	 */
	final public function initView() {
		$this->view = new View($this, $this->action);
		
		if (!$this->view->hasValidTemplate()) {
			throw new NoValidTemplateException();
		}
		
		if (!$this->view->exist()) {
			throw new ViewDoesNotExistException();
		}
	}

	/**
	 * Renders the view, if another view has been given by parameter. Then it'll render the given view instead of the action's view
	 *
	 * @param null $view
	 * @return void
	 */
	final public function render($view = null) {
		if ($view === null) {
			if (!$this->view instanceof View) {
				$this->initView();
			}
			$this->_passDataToView();
			$this->view->render();
		}
		else {
			// Bestaande view vernietigen
			unset($this->view);

			// Nieuw instantie makenv van de gevraagde view, en die uitendelijk renderen.
			$this->view = new View($this, $view);
			
			$this->_passDataToView();
			//$this->view->render();
		}
	}
	
	/**
	 * Passes all the setted data to the view before it is rendered
	 *
	 * @return void
	 **/
	final private function _passDataToView() {
		// Alle data doorgeven aan de view
		foreach($this->extractData as $name => $val) {
			$this->view->set($name, $val, true);
		}
		
		// Alle fetch data doorgeven aan de view
		foreach($this->fetchData as $name => $val) {
			$this->view->set($name, $val, false);
		}
		
		// Alle css data doorgeven aan de view
		foreach($this->cssData as $css) {
			$this->view->css($css);
		}
		
		// Alle script data doorgeven aan de view
		foreach($this->scriptData as $script) {
			$this->view->script($script);
		}
		
		// Alle img data doorgeven aan de view
		foreach($this->imgData as $img) {
			$this->view->img($img);
		}

		// Alle standaard Helpers laden
		foreach($this->helpers_static as $helper) {
			$this->view->loadHelper($helper);
		}

		// Alle extra helpers laden
		foreach($this->helpers as $helper) {
			$this->view->loadHelper($helper);
		}
	}

	/**
	 * Set's data to be passed to the view/template
	 *
	 * @param String $name      | The name of the content
	 * @param $value
	 * @param bool $fetch
	 * @return void
	 */
	final public function set($name, $value, $fetch = false) {
		
		if ($fetch === false) {
			$this->extractData[$name] = $value;
		}
		else {
			$this->fetchData[$name] = $value;
		}
	}
	
	final public function css($css) {
		$this->cssData[] = $css;
	}
	
	final public function script($script) {
		$this->scriptData[] = $script;
	}
	
	/**
	 * Returns the default view
	 *
	 * @return String
	 **/
	final public function getDefaultAction() {
		return $this->defaultAction;
	}
	
	/**
	 * Returns the name of the Controller whithout 'Controller'.
	 * For example: PostsController will return Posts
	 *
	 * @return String
	 **/
	final public function getName() {
		return str_replace('Controller', '', get_class($this));
	}

	/**
	 * Loads a component
	 * If there's a standard component, it'll use that one.
	 * If there isn't a standard component but there is one in the app dir. It'll use that one
	 * Else throw an exception
	 *
	 * @param $component
	 * @throws ComponentDoesNotExistException
	 * @return void
	 */
	final private function _loadComponent($component) {
		if (file_exists(LIB_DIR.'/Mvc/Controller/Components/'.$component.'.php')) {
			$this->components_holder[str_replace('Component', '', $component)] = new $component();
		}
		else if (file_exists(APP_DIR.'/Mvc/Controller/Components/'.$component.'.php')) {
			$this->components_holder[str_replace('Component', '', $component)] = new $component();
		}
		else {
			throw new ComponentDoesNotExistException($component);
		}
	}

	/**
	 * Magic get
	 * Returns an instance of Component/Model/...
	 *
	 * @param $name
	 * @return Object/void
	 */
	public function __get($name) {
		// Components
		if (isset($this->components_holder[$name]) && $this->components_holder[$name] instanceof Component) {
			return $this->components_holder[$name];
		}
		else if (isset($this->components_holder[$name]) && !$this->components_holder[$name] instanceof Component) {
			die("$name Component should extend the parent-class Component");
		}
		
		if (isset($this->models[$name]) && $this->models[$name] instanceof Model) {
			return $this->models[$name];
		}
		
		return $this->view;
	}

	/**
	 * Checks if the given model exists and if so instantiates it and stores it in an array
	 * Models can be accessed using "$this->Model" as the magic get-method checks if there is an existing model.
	 *
	 * @param $model
	 * @return void
	 */
	final public function loadModel($model) {
		if (file_exists(APP_DIR.'/Mvc/Model/'.$model.'.php')) {
			$this->models[$model] = new $model();
		}
		else if (file_exists(LIB_DIR.'/Mvc/Model/'.$model.'.php')) {
			$this->models[$model] = new $model();
		}
	}

	/**
	 * Redirects the user
	 *
	 * Possible array options:
	 *    - controller  | Controller to append to the base site path
	 *    - action      | Action to append after the controller
	 *    - args        | Arguments to append after the action (this can both be an array or a single index)
	 *
	 * @param $url
	 * @return void
	 */
	final public function redirect($url) {
		/**
		 * If it isn't an array, then try to redirect with the url given
		 * If it is an array, then set up the url by the given options
		 * 		And then redirect
		 **/
		if (!is_array($url)) {
			
			// Check if http is in front
			// If so just use the url given
			if (strstr($url, 'http://') || strstr($url, 'http://www.')) {
				header('Location: '.$url);
				die();
			}
			else {
				if (strstr($url, str_replace('http://www.', '', Config::SITE_PATH))) {
					header('Location: http://www.'.$url);
					die();
				}
				else {
					header('Location: '.Config::SITE_PATH.$url);
					die();
				}
			}
		}
		else {
			if (isset($url['controller'])) {
				$url = Config::SITE_PATH.$url['controller'].'/';
				$url .= (isset($url['action']) ? $url['action'] : '');
				
				if (isset($url['args'])) {
					if (is_array($url['args'])) {
						foreach($url['args'] as $arg) {
							$url .= '/'.$arg;
						}
					}
					else {
						$url .= '/'.$url['arg'];
					}
				}
			}
			else {	// If the controller isn't given, then we'll redirect automatically to the base url
				$url = Config::SITE_PATH;
			}
			
			header('Location: '.$url);
			die();
		}
	}

	public final function writeToCache($filename, $content, $dir, $interval) {
		$cache = new Cache($filename, $dir, $interval);

		if ($cache->mustCache()) {
			$cache->write($filename, $content, $dir);
		}

		return true;
	}
}