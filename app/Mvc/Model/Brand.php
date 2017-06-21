<?php
/**
 * Federico Moda
 * User: Robbe Ingelbrecht
 * Date: 27/04/14 18:22
 *
 * (C) Copyright Federico Moda 2014 - All rights reserved
 */

class Brand extends AppModel {

	public function getAllParentBrands() {
		$sql = "SELECT id, name FROM fm_brands WHERE brandspage = 1 AND deleted = 0";
		$pdo = App::getPDO();

		return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getItemsCount($bn) {
		$this->_pdoCheck();

		$sql = "SELECT fm_store_items.id
						FROM
							fm_store_items
								INNER JOIN fm_store_items_brands ON fm_store_items.id = fm_store_items_brands.item_id
								INNER JOIN fm_brands ON fm_store_items_brands.brand_id = fm_brands.id
						WHERE
							fm_brands.name = '$bn'
							AND
							show_by_brands = 1
						ORDER BY fm_store_items.id DESC";

		$qry = $this->pdo->query($sql);

		return $qry->rowCount();
	}

	/**
	 * Inserts a new brand into fm_brands
	 * If the brand show valid to criteria
	 *
	 * return bool/String
	 **/
	public function insertNewBrand($bn) {
    $regEx = '/^[\pL0-9\-\ \.]*$/';

    // Match bn with regex
    // Not getting to special on brandnames
    if (preg_match($regEx, $bn)) {
      $this->_pdoCheck();

      $sql = "INSERT INTO fm_brands(name) VALUES('$bn');";

      $ret = $this->pdo->exec($sql);

      if ($ret > 0) {
          return true;
      }
      else {
          return 'Error in query-syntax';
      }
    }
    else {
      return 'Brand-namen kunnen enkel (al dan niet met accent) alfabetische letters, cijfers , spaties( ), mintekens en punten(.) bevatten';
    }
	}

	public function removeBrand($bID) {
		$this->_pdoCheck();

		$sql = "UPDATE fm_brands SET deleted = 1 WHERE id = '$bID' LIMIT 1;";
		$this->pdo->exec($sql);

		return true;
	}

	public function setBrandStatus($brandID, $status) {
		if (!is_numeric($status))
			$status = 1;

		$this->_pdoCheck();

		$sql = "UPDATE fm_brands SET brandspage = '$status' WHERE id = '$brandID' LIMIT 1;";

		return $this->pdo->exec($sql);
	}
}