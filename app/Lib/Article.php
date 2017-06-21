<?php

class Article {
	private $id;						// Article ID (fm_store_items.id)
	private $data;					// Article data holder (array)
	private $data_img;			// Article images holder (array)

	private static $pdo;			// PDO instance holder

	public function __construct($artID) {
		if (is_numeric($artID))
			$this->id = $artID;
		else
			$this->id = -1;

		// If local property 'pdo' isn't a PDO instance yet
		// Then instantiate one
		if (!self::$pdo instanceof PDO)
			self::$pdo = App::getPDO();

		// Load in data
		$this->_fetchData();
		$this->data_img = $this->_fetchImages();

		// Fetch brands
		$this->data['brands'] = $this->_fetchBrands();
	}

	/**
	 * Fetches all data bound to this Article ID
	 * Stores the data in private property 'data'
	 *
	 * Or declares it false if no data was found
	 */
	private function _fetchData() {
		$sql = "SELECT
							fm_store_items.*,
							fm_images.url AS primary_image_url
						FROM
							fm_store_items
								INNER JOIN fm_images ON fm_images.id = fm_store_items.id_primary_image
						WHERE
							fm_store_items.id = '$this->id';
						LIMIT 1;";

		$qry = self::$pdo->query($sql);

		if ($qry !== false && $qry->rowCount() > 0) {
			$this->data = $qry->fetchAll(PDO::FETCH_ASSOC);
			$this->data = $this->data[0];
		}
		else {
			$this->data = false;
		}
	}

	private function _fetchImages() {
		$sql = "SELECT *
						FROM
							fm_store_items_images INNER JOIN fm_images ON fm_store_items_images.img_id = fm_images.id
						WHERE
							item_id = '$this->id';";

		$qry = self::$pdo->query($sql);

		if ($qry !== false && $qry->rowCount() > 0)
			return $qry->fetchAll(PDO::FETCH_ASSOC);
		else
			return array();
	}

	private function _fetchBrands() {
		$sql = "SELECT fm_brands.name, fm_brands.id
						FROM fm_store_items_brands
							INNER JOIN fm_brands ON fm_store_items_brands.brand_id = fm_brands.id
						WHERE
							item_id = $this->id
						ORDER BY fm_store_items_brands.id ASC;";

		$qry = self::$pdo->query($sql);

		if ($qry !== false && $qry->rowCount() > 0)
			return $qry->fetchAll(PDO::FETCH_ASSOC);
		else
			return array();
	}

	public function getID() {
		return $this->id;
	}

	public function getName() {
		return $this->data['name'];
	}

	public function getDescription() {
		return $this->data['description'];
	}

	public function getDescriptionShort() {
		return $this->data['description_short'];
	}

	public function getCurrentPrice() {
		if ($this->data['price_new'] == null)
			return $this->data['price'];
		else
			return $this->data['price_new'];
	}

	public function getPrice() {
		return $this->data['price'];
	}

	public function getNewPrice() {
		return $this->data['price_new'];
	}

	public function getStatus() {
		return $this->data['status'];
	}

	public function getPrimaryImgUrl() {
		$url = $this->data['primary_image_url'];

		if (!strstr($url, 'http://') && !strstr($url, 'https://'))
			$url = Config::SITE_PATH.'/img/uploads/'.$url;

		return $url;
	}

	public function getBrand() {
		return $this->data['brands'][0]['name'];
	}

	public function getImages() {
		$imgs = $this->data_img;

		foreach($imgs as $i => $img)
			if ($img['host'] == 'local')
				$imgs[$i]['url'] = Config::SITE_PATH.'/img/uploads/'.$img['url'];

		return $imgs;
	}

	public function getPercent() {
		return $this->data['percent'];
	}

	public function isPromotion() {
		if ($this->data['promotion'] == 1)
			return true;
		else
			return false;
	}

	public function isCollection() {
		if ($this->data['collection'])
			return true;
		else
			return false;
	}

	public function isBrands() {
		if ($this->data['show_by_brands'])
			return true;
		else
			return false;
	}

	public function hasBrand($brandID) {
		foreach($this->data['brands'] as $brand) {
			if ($brand['id'] == $brandID)
				return true;

		}

		return false;
	}

	public function isNice2See() {
		if($this->data['nice2see'])
			return true;
		else
			return false;
	}

	////////////// Magic Methods /////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Magic get method
	 *
	 * @param $val
	 * @return null
	 */
	public function __get($val) {
		$ret = null;		// To return

		// If it exists as a field in our data-array
		if (isset($this->data[$val]))
			$ret =  $this->data[$val];


		return $ret;
	}

	/**
	 * Magic toString method
	 *
	 * @return string
	 */
	public function __toString() {
		return null;
	}
}

