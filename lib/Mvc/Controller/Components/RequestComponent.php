<?php

class RequestComponent extends Component{
	
	private $post = false;
	private $get = false;
	private $ajax = false;
	
	public $data = array();

	/**
	 * Constructor
	 */
	public function __construct() {
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$this->post = true;
			$this->data = $_POST;
		}
		else if ($_SERVER['REQUEST_METHOD'] == 'GET') {
			$this->get = true;
			$this->data = $_GET;
		}
		
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			$this->ajax = true;
		}
	}

	/**
	 * Checks whether the current HTTP-Request is a GET
	 *
	 * @return bool
	 */
	public function isGet() {
		if ($this->get) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Checks whether the current HTTP-Request is a POST
	 *
	 * @return bool
	 */
	public function isPost() {
		if ($this->post) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Checks whether the current HTTP-Request is requested via an ajax-request
	 * @return bool
	 */
	public function isAjaxRequest() {
		if ($this->ajax) {
			return true;
		}
		else {
			return false;
		}
	}
	
}