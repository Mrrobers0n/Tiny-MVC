<?php

class AuthComponent extends Component {
	private $username;
	private $password;
	private $pdo;

	private $errors;
	private $data;

	public function login($username, $password) {
		$security = new SecurityComponent();
		$this->username = $username;
		$this->password = $security->hash($password);

		$this->pdo = App::getPDO();

		// If the user exists with the given username/email and password
		if ($this->_checkCredentials()) {
			$data = $this->_getUserData();

			// Controlleer of de gebruiker nog een legitiem account heeft
			if ($this->_checkStatus($data['status'])) {
				$this->loadComponent('Session');
				$this->Session->write('current_user', array('id' => $data['id']));

				return true;
			}
			else {
				return $this->errors;
			}
		}
		else {
			return $this->errors;
		}
	}

	public function createAccount($data) {
		$security = new SecurityComponent();

		// Validate data
		if ($this->_validateUserData($data)) {
			$data['password'] = $security->hash($data['password']);

			$pdo = App::getPDO();
			$sql = "INSERT INTO rb_users(name, email, password, ip_address, roles)
							VALUES(
								'".$data['name']."',
								'".$data['email']."',
								'".$data['password']."',
								'".$_SERVER['REMOTE_ADDR']."',
								'free'
							);";

			$qry = $pdo->exec($sql);
			$uid = $pdo->lastInsertId();
			
			$sql = "INSERT INTO rb_users_rights(uid) VALUES($uid);";
			$qry2 = $pdo->exec($sql);

			// If rows altered returns 1, then we've succesfully created the new user
			if ($qry > 0 && $qry !== false && $qry2 > 0) {
				return true;
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}

	private function _checkCredentials() {
		$sql = "SELECT id FROM rb_users WHERE (username = '$this->username' OR email = '$this->username') AND password = '$this->password' LIMIT 1;";

		$qry = $this->pdo->query($sql);

		if ($qry !== false && $qry->rowCount() > 0)
			return true;
		else {
			$this->errors[] = 'Er is geen account gevonden met de opgegeven gegevens. Controlleer uw gegevens en probeer het nog eens.';
			return false;
		}
	}

	/**
	 * Checks if the user is still authorized to log in
	 *
	 * @param $status
	 * @return bool
	 */
	private function _checkStatus($status) {
		if ($status == 'active') {
			return true;
		}
		else if ($status == 'inactive') {
			$this->errors[] = 'Uw account is niet geactiveerd, dit kan tijdelijk zijn.';
			return false;
		}
		else if ($status == 'deleted') {
			$this->errors[] = 'Er is geen account gevonden met de opgegeven gegevens. Controlleer uw gegevens en probeer het nog eens.';
		}
	}

	/**
	 * Gets the user's data from the database and returns it
	 *
	 * @return mixed
	 */
	private function _getUserData() {
		$sql = "SELECT * FROM rb_users WHERE (username = '$this->username' OR email = '$this->username') AND password = '$this->password' LIMIT 1;";
		$qry = $this->pdo->query($sql);

		$this->data = $qry->fetch();

		return $this->data;
	}

	public function isLoggedIn() {
		$this->loadComponent('Session');

		$user = $this->Session->read('current_user');

		if ($user !== null && isset($user['id'])) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Validates given data for user registration
	 *
	 * @param $data
	 * @return bool
	 */
	private function _validateUserData($data) {
		$errors = array();

		if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
			$errors[] = "Gelieve een geldig e-mail adres op te geven.";
		}
		else {
			$pdo = App::getPDO();
			$sql = "SELECT id FROM rb_users WHERE email = '".trim($data['email'])."' LIMIT 1";

			if ($pdo->query($sql)->rowCount() > 0) {
				$errors[] = 'Dit e-mail adres is al in gebruik';
			}
		}

		if (strlen($data['name']) < 4) {
			$errors[] = "Gelieve een geldige naam op te geven.";
		}

		if (strlen($data['password']) < 6 || strlen($data['password']) > 18) {
			$errors[] = 'Gelieve een wachtwoord te kiezen die minimum 6 tekens en maximum 18 lang is.';
		}
		else if ($data['password'] != $data['password_repeat']) {
			$errors[] = 'Uw 2 opgegeven wachtwoorden komen niet met elkaar overeen.';
		}

		if (count($errors) > 0) {
			$this->errors = $errors;
			return false;
		}
		else {
			return true;
		}
	}

	/**
	 * Returns the errors that occured.
	 *
	 * @return mixed
	 */
	public function getErrors() {
		return $this->errors;
	}

	/**
	 * Returns a User object if logged in, false otherwise
	 *
	 * @return bool|User
	 */
	public function user() {
		if ($this->isLoggedIn()) {
			$this->loadLib('User.php');
			$data = $this->Session->read('current_user');

			return new User($data['id']);
		}
		else {
			return false;
		}
	}
	
	public function logout() {
	    if ($this->isLoggedIn()) {
	        session_destroy();
	        
	        return true;
	    }
	    else {
	        return true;
	    }
	}
}

?>