<?php
/**
 * Federico Moda
 * User: Robbe Ingelbrecht
 * Date: 10/05/14 14:24
 * 
 * (C) Copyright Federico Moda 2014 - All rights reserved
 */

class Newsletter extends AppModel {

	/**
	 * Inserts a new subscriber
	 *
	 * @param $email
	 * @return bool|string
	 */
	public function newSubscriber($email) {
		if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$this->_pdoCheck();

			$sql = "INSERT INTO fm_newsletter_subscribers(email) VALUES('$email');'";

			if ($this->pdo->exec($sql) > 0)
				return true;
			else
				return false;
		}
		else {
			return 'E-mail not valid';
		}
	}
}