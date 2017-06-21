<?php
require_once(LIB_DIR.'/Mvc/View/Cache.php');

class View {
	
	protected $controller;
	protected $action;
	
	protected $fetchData = array();
	protected $extractData = array();
	
	protected $cssData = array();
	protected $scriptData = array();
	protected $imgData = array();
	
	protected $cache = false;
	
	protected $helpers = array();
	
	public function __construct(Controller $controller, $action) {
		$this->controller = $controller;	
		$this->action = $action;
	}

	/**
	 * Renders the template and then the view inside the template.
	 * Use $this->fetch('content') inside the template where the content of the view should come.
	 *
	 * @throws NoValidTemplateException
	 * @throws ViewDoesNotExistException
	 * @return void
	 */
	public function render() {
		// Controleer er een geldige template is gegeven
		if ($this->hasValidTemplate()) {
			// Controleren o de view bestaat
			if ($this->exist()) {
				$viewtype = $this->_getViewType($this->getViewContent(true));
				$content = $this->getViewContent();
				$this->set('content', $content);
				
				if ($viewtype == 'html') {
					if (hasDevAcces()) {
						// Teplate includen
						include_once(APP_DIR.'/Mvc/View/Templates/'.Config::TEMPLATE.'/template.php');
					}
					else {
						// Teplate includen
						include_once(APP_DIR.'/Mvc/View/Templates/'.Config::TEMPLATE.'/template.php');
					}
				}
				else {
					echo $content;
				}
			}
			else {
				throw new ViewDoesNotExistException();
			}
		}
		else {
			if (!$this->hasValidTemplate()) {
				throw new NoValidTemplateException();
			}
			else {
				/*$content = $this->getViewContent();
				$this->set('content', $content);
				
				echo $content;*/
			}
		}
	}
	
	/**
	 * Checks if the view exists
	 *
	 * @return bool
	 **/
	public function exist() {
		if (file_exists(APP_DIR.'/Mvc/View/'.$this->controller->getName().'/'.$this->action.'.php')) {
			return true;
		}
		else if (file_exists(LIB_DIR.'/Mvc/View/'.$this->controller->getName().'/'.$this->action.'.php')) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Checks if there is a valid template configured
	 *
	 * @return bool
	 */
	public function hasValidTemplate() {
		if (file_exists(APP_DIR.'/Mvc/View/Templates/'.Config::TEMPLATE.'/template.php')) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Get's the content of the view and returns it
	 *
	 * @param bool $source
	 * @return String
	 */
	private function getViewContent($source = false) {
		if (!$source) {
			ob_start();
			extract($this->extractData);
			// If the view exists
			if (file_exists(APP_DIR.'/Mvc/View/'.$this->controller->getName().'/'.$this->action.'.php')) {
				// If caching is enabled
				if ($this->cache !== false) {
					$cache = new Cache($this->action.'.cphp', 'View/'.$this->controller->getName().'/', $this->cache);
					
					// Check if the view should be cleared and cached again
					if ($cache->mustCache()) {
						include_once(APP_DIR.'/Mvc/View/'.$this->controller->getName().'/'.$this->action.'.php');
						$content = ob_get_contents(); ob_get_clean();
						
						// Cache the view
						$cache->cache($content);
						
						// Return the cached content
						return $cache->getContent();
					}
					else {
						// Return the cached content 
						return $cache->getContent();
					}
				}
				else {
					include_once(APP_DIR.'/Mvc/View/'.$this->controller->getName().'/'.$this->action.'.php');
				}
			}
			else {
				include_once(LIB_DIR.'/Mvc/View/'.$this->controller->getName().'/'.$this->action.'.php');
			}
			
			$html = ob_get_contents(); ob_get_clean();
			
			return $html;
		}
		else {
			if (file_exists(APP_DIR.'/Mvc/View/'.$this->controller->getName().'/'.$this->action.'.php')) {
				return file_get_contents(APP_DIR.'/Mvc/View/'.$this->controller->getName().'/'.$this->action.'.php');
			}
			else {
				return file_get_contents(LIB_DIR.'/Mvc/View/'.$this->controller->getName().'/'.$this->action.'.php');
			}
		}
	}

	/**
	 * Set content to extract to the view when it's rendered.
	 * Give a third param as true ($extractable) if the data should be extracted as a $var inside the view.
	 * Otherwise use $this->fetch() to get the data
	 *
	 * @param $name
	 * @param $value
	 * @param bool $extractable
	 * @return void
	 */
	public function set($name, $value, $extractable = false) {
		if ($extractable === false) {
			$this->fetchData[$name] = $value;
		}
		else {
			$this->extractData[$name] = $value;
		}
	}

	/**
	 *
	 *
	 * @param $type
	 * @param array $options
	 * @return void
	 */
	public function setViewType($type, $options = array()) {
		switch($type) {
			case 'page':
			break;
			
			case 'xml':
				$header = "Content-type: text/xml";
				
				if (isset($options['charset'])) {
					$header .= ";charset=utf-8";
				}
			break;
		}
		
		header($header);
	}

	/**
	 * Returns the asked data
	 *
	 * @param $name
	 * @return mixed
	 */
	public function fetch($name) {
		if (isset($this->fetchData[$name])) {
			return $this->fetchData[$name];
		}
		else {
			return null;
		}
	}

	/**
	 * Adds a css-file to be outputed OR outputs all given CSS files
	 * If a param is given then it's a css file to be added otherwise output all the css files
	 *
	 * @param null $arg
	 * @return string
	 */
	public function css($arg = null) {
		if ($arg !== null) {
			// Indien een array, door alle items loopen.
			if (is_array($arg)) {
				foreach($arg as $css) {
					$this->cssData[] = $css;
				}
			}
			else {
				$this->cssData[] = $arg;
			}
		}
		else {
			$ret = '';

			// Loop backwards to get the template files defined earlier then the view defined ones
			for($i = count($this->cssData)-1; $i >= 0; $i--) {
				if (!strstr($this->cssData[$i], 'http://')) {
					$ret .= '<link rel="stylesheet" href="/css/'.$this->cssData[$i].'" />'."\n";
				}
				else {
					$ret .= '<link rel="stylesheet" href="'.$this->cssData[$i].'" />'."\n";
				}
			}
			
			return $ret;
		}
	}

	/**
	 * Adds a jscript-file to be outputed OR outputs all given JavaScript files
	 * If a param is given then it's a jscript file to be added otherwise output all the jscript files
	 *
	 * @param null $arg
	 * @return string
	 */
	public function script($arg = null) {
		if ($arg !== null) {
			// Indien een array, door alle items loopen.
			if (is_array($arg)) {
				foreach($arg as $css) {
					$this->scriptData[] = $css;
				}
			}
			else {
				$this->scriptData[] = $arg;
			}
		}
		else {
			$ret = '';

			// Loop backwards to get the template files defined earlier then the view defined ones
			for($i = count($this->scriptData)-1; $i >= 0; $i--) {
				if (!strstr($this->scriptData[$i], 'http://')) {
					$ret .= '<script type="text/javascript" src="/js/'.$this->scriptData[$i].'" /></script>'."\n";
				}
				else {
					$ret .= '<script type="text/javascript" src="'.$this->scriptData[$i].'" /></script>'."\n";
				}
			}
			
			return $ret;
		}
	}

	/**
	 * Outputs an image with the given options
	 * Possible options:
	 * 	-	title				| Title of the img
	 * 	- alt					| Alt-text for the img
	 *
	 * @param $arg
	 * @param null $options
	 * @return string
	 */
	public function img($arg, $options = null) {
		$ret = '';
		$opts = '';
		
		if ($options !== null && is_array($options)) {
			
			// Title element
			if (isset($options['title'])) {
				$opts .= 'title="'.$options['title'].'" ';
				
				// Indien er geen alt is meegegeven, title gebruiken als alt
				if (!isset($options['alt'])) {
					$opts .= 'alt="'.$options['title'].'" ';
				}
			}
			
			// alt element
			if (isset($options['alt'])) {
				$opts .= 'alt="'.$options['alt'].'" ';
				
				// Indien er geen title is meegegeven, title gebruiken als alt
				if (!isset($options['title'])) {
					$opts .= 'title="'.$options['alt'].'" ';
				}
			}
		}
		
		if (!strstr($arg, 'http://')) {
			$ret .= '<img src="/img/'.$arg.'" '.$opts.' />'."\n";
		}
		else {
			$ret .= '<img src="'.$arg.'" '.$opts.' />'."\n";
		}
		
		return $ret;
	}

	/**
	 * Returns the type of the view for output headers
	 * Possible viewtypes:
	 * 	-	html					| Normal HTML view
	 * 	-	xml						| Xml file
	 * 	-	json					| Json file
	 * 	-	js						| JavaScript file
	 * 	-	css						| Cascading Style Sheet
	 * 	-	ajax					| Normal HTML view without template for ajax-requests
	 *
	 * @param $view
	 * @return string
	 */
	private function _getViewType($view) {
		$view = strtolower($view);
		$ret = 'html';
		$cache = array();
		$type = array();
		
		// Controleren of er een andere doctype is opgegeven
		if (preg_match("/doctype: \<\<(.*)\>\>/", $view, $type)) {
			$ret = $type[1];
			
			// Content-Type veranderen a.d.h.v. de pagina
			switch($ret) {
				case 'xml':
					header("Content-type: text/xml; charset=utf-8");
				break;
				
				case 'json':
					header("Content-type: application/json");
				break;
				
				case 'js':
					header("Content-type: application/x-javascript");
				break;
				
				case 'css':
					header("Content-type: text/css");
				break;
				
				case 'ajax':
					
				break;
			}
		}
		
		// Caching eigenschappen checken
		if (preg_match("/caching: \<\<(.*)\>\>/", $view, $cache)) {
			$this->cache = $cache[1];
		}
		
		return $ret;
	}

	/**
	 * Loads a helper
	 * If there's a standard helper, it'll use that one.
	 * If there isn't a standard helper but there is one in the app dir. It'll use that one
	 * Else throw an exception
	 *
	 * @param $helper
	 * @throws ComponentDoesNotExistException
	 * @return void
	 */
	final public function loadHelper($helper) {
		if (!strstr(strtolower($helper), 'helper')) {
			$helper .= 'Helper';
		}
		
		if (file_exists(LIB_DIR.'/Mvc/View/Helpers/'.$helper.'.php')) {
			$this->helpers[str_replace('Helper', '', $helper)] = new $helper();
		}
		else if (file_exists(APP_DIR.'/Mvc/View/Helpers/'.$helper.'.php')) {
			$this->helpers[str_replace('Helper', '', $helper)] = new $helper();
		}
		else {
			throw new ComponentDoesNotExistException($helper);
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
		// Helpers
		if (isset($this->helpers[$name]) && $this->helpers[$name] instanceof Helper) {
			return $this->helpers[$name];
		}
	}

	/**
	 * Returns the action of the view
	 *
	 * @return mixed
	 */
	public function getAction() {
		return $this->action;
	}

	/**
	 * Returns the name of the controller
	 *
	 * @return String
	 */
	public function getController() {
		return $this->controller->getName();
	}
}