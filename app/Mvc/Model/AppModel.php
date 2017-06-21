<?php

class AppModel extends Model {

	/**
	 * Instantiates a pdo object as a property of the current model
	 * if the pdo property doesn't already refer to a pdo instance.
	 */
	protected function _pdoCheck() {
		if (!$this->pdo instanceof PDO)
			$this->pdo = App::getPDO();
	}
}