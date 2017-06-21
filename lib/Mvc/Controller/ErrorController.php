<?php

class ErrorController extends Controller {

	public function missingArguments() {
		$this->set('doc_title', 'Missing arguments', true);
	}

	public function invalidAction() {
		$this->set('doc_title', 'Invalid Action', true);
	}

	public function invalidView() {
		$this->set('doc_title', 'Invalid view', true);
	}

	public function notFound() {
		App::load('GoogleAds.php');
		App::load('Functions.php');

		$this->set('doc_title', 'Pagina niet gevonden!', true);
	}
}