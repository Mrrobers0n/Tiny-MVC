<?php
Lib::import('Session/Session.php');

class SessionHelper extends Helper {
	// Session instantiate
	private $session;
	
	public function __construct() {
		$this->session = new Session();
	}
	
	/**
	 * Get's all the flash messages
	 * Shows the valid ones
	 * Deletes the expired ones
	 *
	 * @return String $html
	 **/
	public function getFlash() {
		$i = 0;
		$html = '';
		$messages = $this->session->read('flashMessages');
		
		if ($messages !== null && is_array($messages) && count($messages) > 0) {
			foreach($messages as $i => $message) {
				$html .= '<div class="'.$message['div'].'">';
				
					// If there's an until time set, then check if it hasn't expired yet
					if ($message['until'] !== null && $message['until'] > time()) {
						$html .= $message['message'];
					}
					else if ($message['amount'] > 0) {
						// Decrement the amount of times to show
						$this->session->write('flashMessages.'.$i.'.amount', $message['amount']-1);
						$html .= $message['message'];
						
						// If the amount -1 = 0 then remove the message already
						if (($message['amount'] - 1) < 1) {
							$this->session->remove('flashMessages.'.$i);
						}
					}
					else {
						// No options are valid, so the message may be removed.
						$this->session->remove('flashMessages.'.$i);
					}
					
				$html .= '</div>';
			}
		}
		
		return $html;
	}
}