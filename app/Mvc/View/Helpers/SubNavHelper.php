<?php
/**
 * Source
 * User: Robbe Ingelbrecht
 * Date: 28/10/13 16:41
 * 
 * (C) Copyright Source 2013 - All rights reserved
 */

class SubNavHelper extends Helper {
	private $controller = null;
	private $subnav = null;

	public function getNavigation($controller) {
		$this->controller = $controller;

		if ($this->_hasSubNav()) {
			echo $this->subnav->getHtml();
		}
	}

	private function _hasSubNav() {
		if (class_exists(ucfirst($this->controller).'SubNav')) {
			return true;
		}
		else {
			return false;
		}
	}
}

interface iSubNav {
	public function getHtml();
}

class IndexSubNav implements iSubNav {

	public function getHtml() {
		$ret = 'lol';
	}
}