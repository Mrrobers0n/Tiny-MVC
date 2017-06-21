<?php
/**
 * Created by PhpStorm.
 * User: Pascale
 * Date: 11/01/14
 * Time: 16:48
 */

class User {
	private $uid;
	private $udata;

	private $pdo;

	public function __construct($uid) {
		$this->pdo = App::getPDO();

		$this->uid = $uid;
		$this->udata = $this->_getUserData();
	}

	/**
	 * Retrieves and returns the user's data from the db by ID
	 *
	 * @return mixed
	 */
	private function _getUserData() {
		$sql = "SELECT * FROM rb_users WHERE id = '$this->uid' LIMIT 1;";
		$qry = $this->pdo->query($sql);

		return $qry->fetch();
	}

	public function is($role) {
		$roles = explode(';', $this->udata['roles']);

		if (in_array($role, $roles))
			return true;
		else
			return false;
	}
	
	public function getID() {
	    return $this->udata['id'];
	}
	
	public function __get($field) {
	    if (isset($this->udata[$field]))
	        return $this->udata[$field];
	    else
	        return null;
	}

} 