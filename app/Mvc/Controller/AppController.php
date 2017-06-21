<?php

class AppController extends Controller {

	public $components = array('Auth');

	public $helpers = array('');

	public function beforeFilter() {
		App::load('GoogleAds.php');
		App::load('Functions.php');
		App::load('Article.php');

		$this->set('doc_title', 'Welkom');
		
		$meta = 	'<meta name="keywords" content="Robbe, Ingelbrecht, Webdeveloper, webdevelopment, porftolio, website" />'."\n";
		$meta .=	'<meta name="description" content="Robbe Ingelbrecht is een jonge webdeveloper die zijn passie heeft gevonden in het programmeren, hij programmeert nu al sinds zijn 12 jaar en is zeer gedreven'
																	.'om bij te leren." />'."\n";
		$this->set('meta', $meta, true);

		$this->set('PROJECT_VERSION', Config::PROJECT_VERSION, true);
		$this->set('FRAMEWORK_VERSION', Config::FRAMEWORK_VERSION, true);

		$this->set('clientinfo', array($this->Client->getDevice(), $this->Client->getBrowser()));

		// Set the action
		$this->set('page', $this->getAction(), true);

		// Als er geen www. in de url staat, dan doorsturen naar url met www.
		if (!strstr($_SERVER['HTTP_HOST'], 'www.')) {
			$this->Session->setFlash("U bent naar onze homepagina doorgestuurd, doordat 'www' in de url ontbrak. Het kan dus perfect zijn dat de pagina die u zocht wel degelijk bestaat", array('div' => "alert alert-success"));
			$this->redirect(Config::SITE_PATH);
		}

		// Get weather data datetime

		if ($this->Auth->isLoggedIn()) {
			$this->set('logged_in', true);
			$this->set('logged_in', true, true);
			$this->set('user', $this->Auth->user());
			$this->set('user', $this->Auth->user(), true);
		}
		else {
			$this->set('logged_in', false);
		}
	}
}