<?php

class Component {
	private $components = array();

	/**
	 * Magic get
	 * Returns an instance of Component/Model/...
	 *
	 * @param $name
	 * @return Object/void
	 */
	public function __get($name) {
		// Components
		if (isset($this->components[$name]) && $this->components[$name] instanceof Component) {
			return $this->components[$name];
		}
		
		if (isset($this->models[$name]) && $this->models[$name] instanceof Model) {
			return $this->models[$name];
		}
		
		return $this->view;
	}

	/**
	 * Loads a componenent
	 * Stores it in a public array
	 *
	 * @param $component
	 * @throws ComponentDoesNotExistException
	 * @return void
	 */
	public function loadComponent($component) {
		
		// Append 'Component' if neccesary
		if (!strstr('component', strtolower($component))) {
			$component .= 'Component';
		}
		
		if (file_exists(LIB_DIR.'/Mvc/Controller/Components/'.$component.'.php')) {
			$this->components[str_replace('Component', '', $component)] = new $component();
		}
		else if (file_exists(APP_DIR.'/Mvc/Controller/Components/'.$component.'.php')) {
			$this->components[str_replace('Component', '', $component)] = new $component();
		}
		else {
			throw new ComponentDoesNotExistException($component);
		}
	}


	public function loadLib($fn, $dir = '') {
		$dir = APP_DIR.'/Mvc/Controller/Components/Lib/'.$dir;

		if (file_exists($dir.$fn)) {
			include_once($dir.$fn);
		}
	}
	
}