<?php
/**
 * Created by PhpStorm.
 * User: Johnny
 * Date: 4/28/14
 * Time: 10:38 AM
 */

class Item extends AppModel {
	public $useTable = 'fm_store_items';
	public $primaryKey  = 'id';

	private $errors = array();

	/**
	 * Returns an array holding all the criteria valid fm_store_items results
	 * Uses brand-names to filter results
	 *
	 * @param array $brands          ~ Brand names
	 * @param int $page
	 * @internal param null $limit ~ SQL LIMIT Clause (ex. 20/5,25/..)
	 * @return array
	 */
	public function getItemsByBrand(Array $brands, $page = 1) {
		$this->_pdoCheck();

		// Get WHERE-Clause
		$where = $this->_fmSqlWhereClauseByBrands($brands);

		/*$limit = ($limit !== null) ? 'LIMIT '.$limit : null;

		$sql = "SELECT fm_store_items.id
						FROM
							fm_store_items
								INNER JOIN fm_store_items_brands ON fm_store_items.id = fm_store_items_brands.item_id
								INNER JOIN fm_brands ON fm_store_items_brands.brand_id = fm_brands.id
						WHERE
							$where
						ORDER BY fm_store_items.id DESC
						$limit;";*/

		$items = $this->find(array(
			'select' => array('fm_store_items.id'),
			'from' => 'fm_store_items INNER JOIN fm_store_items_brands ON fm_store_items.id = fm_store_items_brands.item_id INNER JOIN fm_brands ON fm_store_items_brands.brand_id = fm_brands.id',
			'where' => array($where, 'show_by_brands' => 1),
			'orderby' => 'fm_store_items.id DESC',
			'pagination' => array('page' => $page, 'results_per_page' => 9)
		));

		/*$qry = $this->pdo->query($sql);
		$items = $qry->fetchAll(PDO::FETCH_ASSOC);*/

		return $this->_toArticles($items, true);
	}

	public function getAllItems($page = 1, $type = 'all') {
		$where = null;

		switch($type) {
			case 'all':
				$where = array('status != \'deleted\'');
				break;

			case 'promos':
				$where = array('promotion' => 1, 'status != \'deleted\'');
				break;

			case 'collections':
				$where = array('collection' => 1, 'status != \'deleted\'');
				break;

			case 'brands':
				$where = array('show_by_brands' => 1, 'status != \'deleted\'');
				break;
		}

		$items = $this->find(array(
			'select' => array('fm_store_items.id'),
			'from' => 'fm_store_items',
			'where' => $where,
			'orderby' => 'fm_store_items.id DESC',
			'pagination' => array('page' => $page, 'results_per_page' => 9)
		));

		return $this->_toArticles($items, true);
	}

	public function getItemByID($itemID) {
		$this->_pdoCheck();

		$sql = "SELECT items.id
						FROM fm_store_items AS items
							INNER JOIN fm_images ON items.id_primary_image = fm_images.id
						WHERE
							items.id = '$itemID'
						LIMIT 1;";

		$qry = $this->pdo->query($sql);

		// If we have at least one result
		// Then return our item as an Article Object
		if ($qry !== false && $qry->rowCount() > 0) {
			$item = $qry->fetch(PDO::FETCH_ASSOC);

			return $this->_toArticles($item);
		}
		else {
			return false;
		}
	}

	/**
	 * Format's an SQL WHERE-Clause by a given array with brand-names equal to our db
	 *
	 * @param array $brands
	 * @return string
	 */
	private function _fmSqlWhereClauseByBrands(Array $brands) {
		$ret = '';

		for($i=0;$i<count($brands);$i++) {
			$ret .= 'fm_brands.name = \''.$brands[$i].'\''.(($i == count($brands)-1) ? null : ' OR ');
		}

		return $ret;
	}

	/**
	 * Instantiates a new Article object for each individual item
	 * Returns an array holding Article instances
	 *
	 * @param array $items
	 * @param bool $array
	 * @return Article array
	 */
	private function _toArticles(Array $items, $array = false) {
		$ret = array();

		// Return empty array if no items are given
		// Avoid index not found err
		if (count($items) <= 0 && !is_array($items))
			return array();

		if (count($items) > 1) {
			foreach($items as $i => $data)
				$ret[] = new Article($data['id']);
		}
		else {
			// If it's an associative array use 'id' as index
			// Otherwise a multi-dimensional array and start index 0 -> 'id'
			if (isset($items['id']))
				$ret = ($array === false) ?  new Article($items['id']) : array(new Article($items['id']));
			else {
				if (isset($items[0]['id']))
					$ret = ($array === false) ? new Article($items[0]['id']) : array(new Article($items[0]['id']));
			}
		}

		return $ret;
	}

	public function getLatestPromotionItems($limit = 5) {
		$this->_pdoCheck();

		$sql = "SELECT items.id FROM fm_store_items AS items WHERE status = 'instore' AND nice2see = 0  ORDER BY id DESC LIMIT $limit;";
		$qry = $this->pdo->query($sql);

		if ($qry !== false && $qry->rowCount() > 0)
			return $this->_toArticles($qry->fetchAll(PDO::FETCH_ASSOC), true);
		else
			return array();
	}

	/**
	 * Returns all articles that are marked as promotion
	 *
	 * @param int $page
	 * @return Article
	 */
	public function getAllPromotions($page = 1) {
		$this->_pdoCheck();

		$articles = $this->find(array(
			'select' => array('items.id'),
			'from' => 'fm_store_items AS items',
			'where' => array('promotion' => 1, 'status' => 'instore'),
			'orderby' => 'id DESC',
			'pagination' => array('page' => $page, 'results_per_page' => 8)
		));

		return $this->_toArticles($articles, true);
	}

	/**
	 * Returns all articles that are marked as collection
	 *
	 * @param int $page
	 * @return Article
	 */
	public function getAllCollections($page = 1) {
		$this->_pdoCheck();

		$articles = $this->find(array(
			'select' => array('items.id'),
			'from' => 'fm_store_items AS items',
			'where' => array('collection' => 1, 'status' => 'instore'),
			'orderby' => 'id DESC',
			'pagination' => array('page' => $page, 'results_per_page' => 8)
		));

		return $this->_toArticles($articles, true);
	}

	/**
	 * Attempts to insert a new item/article
	 * Validates the given data
	 * Adds all brands and images that are given with
	 *
	 * @param $data
	 * @return array|bool
	 */
	public function insertNewItem($data) {
		if ($this->_validateItemData($data)) {
			// Insert Article to database
			$sql = "INSERT INTO fm_store_items
							(name, description, description_short, price, price_new, percent, date_added, status, promotion, collection, show_by_brands, nice2see)

							VALUES(
								'".$data['name']."',
								'".$data['description']."',
								'".substr($data['description_short'],0,80)."',
								'".$data['price']."',
								'".($data['price_new'] == '' ? "NULL" : $data['price_new'])."',
								'".($data['percent'] == '' ? "NULL" : $data['percent'])."',
								'".date("Y-m-d G:i:s")."',
								'instore',
								'".(isset($data['promotion']) ? 1 : 0)."',
								'".(isset($data['collection']) ? 1 : 0)."',
								'".(isset($data['show_by_brands']) ? 1 : 0)."',
								'".(isset($data['nice2see']) ? 1 : 0)."'
							);";

			$qry = $this->pdo->exec($sql);

			// If Our query inserted at least 1 row != 0
			if ($qry > 0) {
				$itemID = $this->pdo->lastInsertId();
				$this->_insertBrands($data, $itemID);
				$this->_insertImages($data, $itemID);
			}
			return true;
		}
		else {
			return $this->errors;
		}
	}

	public function saveExistingItem($data, $itemID) {
		if ($this->_validateItemData($data)) {
			$sql = "UPDATE fm_store_items
						SET
							name = '".$data['name']."',
							description = '".$data['description']."',
							description_short = '".$data['description_short']."',
							price = '".$data['price']."',
							price_new = '".$data['price_new']."',
							percent = '".($data['percent'] == '' ? "NULL" : $data['percent'])."',
							date_added = '".$data['date_added']."',
							promotion = '".$data['promotion']."',
							collection = '".$data['collection']."',
							show_by_brands = '".$data['show_by_brands']."',
							nice2see = '".$data['nice2see']."'
						WHERE
							fm_store_items.id = $itemID
						LIMIT 1;";

			$ret = $this->pdo->exec($sql);

			$sql = "DELETE FROM fm_store_items_brands WHERE item_id = $itemID";
			$this->pdo->exec($sql);
			$this->_insertBrands($data, $itemID);

			return true;
		}
		else {
			return $this->errors;
		}
	}

	/**
	 * Change('s) the status of an existing item
	 *
	 * @param $status
	 * @param $itemID
	 * @return int
	 */
	public function setItemStatus($status, $itemID) {
		$this->_pdoCheck();

		$sql = "UPDATE fm_store_items SET status = '$status' WHERE id = '$itemID' LIMIT 1;";
		$ret = $this->pdo->exec($sql);

		if ($status == 'deleted') {
			$sql = "DELETE FROM fm_store_items WHERE id = '$itemID' LIMIT 1;";
			$this->pdo->exec($sql);

			$sql = "DELETE FROM fm_store_items_brands WHERE item_id = '$itemID' LIMIT 1;";
			$this->pdo->exec($sql);

			$sql = "DELETE FROM fm_store_items_images WHERE item_id = '$itemID' LIMIT 1;";
			$this->pdo->exec($sql);
		}

		return $ret;
	}

	/**
	 * Validates the given data-array for a new Item
	 * Returns true/false valid/invalid
	 *
	 * @param $data
	 * @return bool
	 */
	private function _validateItemData(&$data) {
		$ret = true;

		// Remove all "art_" indexes
		$temp = array();
		foreach($data as $i => $val) {
			// If the indexes are price or price_new
			// Then replace , with . for Float-numbers
			if ($i == 'art_price' || $i == 'art_price_new')
				$val = str_replace(',', '.', $val);

			$temp[str_replace('art_', '', $i)] = $val;
		}
		// Replace data with our temp array without art_ prefixes
		$data = $temp;

		if (!isset($data['name']) || strlen($data['name']) <= 3) {
			$this->errors[] = 'Gelieve een naam van min. 4 tekens op te geven';
			$ret = false;
		}

		if (!isset($data['description']) || strlen($data['description']) <= 5) {
			$this->errors[] = 'Gelieve een beschrijving van min. 5 tekens lang op te geven';
			$ret = false;
		}

		if (!isset($data['description_short']) || strlen($data['description_short']) <= 5) {
			$this->errors[] = 'Gelieve een korte beschrijving op te geven, deze wordt als eerst gezien.';
			$ret = false;
		}

		if (!isset($data['price']) || !is_numeric($data['price'])) {
			$this->errors[] = 'Gelieve een geldige, in getallen geschreven, prijs op te geven.';
		}

		return $ret;
	}

	/**
	 * Inserts all brands in the database linked to the given Item ID
	 *
	 * @param $data
	 * @param $itemID
	 * @return int
	 */
	private function _insertBrands($data, $itemID) {
		$brands = array();

		// Loop through data and add each brandID that is given
		for($i=0;$i<50;$i++) {
			if (isset($data['brand_'.$i]))
				$brands[] = $i;
		}

		// Format VALUES() SQL
		$brands_sql = '';
		for($i=0; $i < count($brands); $i++) {
			$brand = $brands[$i];
			$brands_sql .= "($itemID, $brand)" .(($i < count($brands)-1) ? ',' : '');

			unset($brand);
		}

		$sql = "INSERT INTO fm_store_items_brands
						(item_id, brand_id)

		VALUES _BRANDS_;";

		$sql = str_replace('_BRANDS_', $brands_sql, $sql);

		$this->_pdoCheck();

		// Exec and return rows altered
		return $this->pdo->exec($sql);
	}

	/**
	 * Inserts all images into the database
	 * Links the images with the item it is given with
	 *
	 * @param $data
	 * @param $itemID
	 * @return int
	 */
	private function _insertImages($data, $itemID) {
		$imgs = array();

		for($i=0; $i < 50; $i++) {
			if (isset($data['img'.$i]) && !empty($data['img'.$i])) {
				$sql = "INSERT INTO fm_images (url, host) VALUES('".$data['img'.$i]."', 'remote');";
				$this->pdo->exec($sql);

				$imgs[] = $this->pdo->lastInsertId();
			}
		}

		// Add Local Images
		for($i=1;$i<=50;$i++) {
			if (isset($data['img_local'.$i]) && !empty($data['img_local'.$i]))
				$imgs[] = $data['img_local'.$i];
		}

		// Format VALUES() SQL
		$imgs_sql = '';
		for($i=0; $i < count($imgs); $i++) {
			$img = $imgs[$i];
			$imgs_sql .= "($itemID, $img)" .(($i < count($imgs)-1) ? ',' : '');

			// Set First image as primary img for item
			if ($i == 0) {
				$primImgSql = "UPDATE fm_store_items SET id_primary_image = $img WHERE id = $itemID LIMIT 1;";
				$this->pdo->exec($primImgSql);
			}

			unset($img);
		}

		$sql = "INSERT INTO fm_store_items_images
						(item_id, img_id)

						VALUES _IMGS_;";

		$sql = str_replace('_IMGS_', $imgs_sql, $sql);

		$this->_pdoCheck();

		// Exec and return rows altered
		return $this->pdo->exec($sql);
	}

	public function getLastUploadedLocalImages($max = 20) {
		$this->_pdoCheck();

		$sql = "SELECT * FROM fm_images WHERE deleted = 0 AND host = 'local' ORDER BY id DESC LIMIT $max;";
		$qry = $this->pdo->query($sql);

		return $qry->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getSearchResults($keyw) {
		$this->_pdoCheck();

		$keyw = trim(urldecode($keyw));

		$sql = "SELECT fm_store_items.id
						FROM fm_store_items
							INNER JOIN fm_store_items_brands ON fm_store_items.id = fm_store_items_brands.item_id
							INNER JOIN fm_brands ON fm_store_items_brands.brand_id = fm_brands.id
						WHERE
									fm_store_items.name LIKE '%$keyw%'
							OR  fm_store_items.description LIKE '%$keyw%'
							OR  fm_store_items.description_short LIKE '%$keyw%'
							OR  fm_brands.name LIKE '%$keyw%'
						GROUP BY fm_store_items.id";

		$data = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $this->_toArticles($data, true);
	}

	public function getPortfolioItems($page = 1) {
		$items =  $this->find(array(
			'select' => array('*'),
			'from' => 'fm_store_items',
			'where' => array('nice2see' => 1),
			'orderby' => 'id DESC',
			'pagination' => array('page' => $page, 'results_per_page' => 7)
		));

		return $this->_toArticles($items, true);
	}
}