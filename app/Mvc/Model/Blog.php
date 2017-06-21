<?php

class Blog extends AppModel {

	public function __construct() {
		parent::__construct();

		App::load('Image.php');
	}

	public function getBlogPostCount() {
		$sql = "SELECT COUNT(id) AS blogposts FROM rb_blog_posts WHERE status = 'active' LIMIT 1";
		$pdo = App::getPDO();

		$qry = $pdo->query($sql)->fetch(PDO::FETCH_ASSOC);

		return $qry['blogposts'];
	}

	public function getAllCategories() {
		$sql = "SELECT * FROM rb_blog_cats WHERE status != 'deleted';";

		$qry = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

		// Add postcount for each categorie
		foreach($qry as $i => $cat) {
			$sql = "SELECT COUNT(id) AS postcount FROM rb_blog_posts WHERE id_cat = '".$cat['id']."' AND status = 'active';";
			$postcount = $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC)['postcount'];

			$qry[$i]['postcount'] = $postcount;
		}

		return $qry;
	}

	public function insertNewBlogPost($data) {
		$status = ($data['action'] == 'post') ? 'active' : 'hidden';

		$check = $this->_checkPostData($data);

		// Small check before inserting
		if ($check['passed'] == false)
			return $check['messages'];

		// If an image was selected
		if (isset($data['primary_img_id']) && $data['primary_img_id'] != 0) {
			$id_img = $data['primary_img_id'];
		}
		// If an image was uploaded
		elseif ($data['uploaded_img'] == true) {
			$sql = "SELECT id FROM rb_images WHERE status = 'active' ORDER BY id DESC LIMIT 1;";
			$qry = $this->pdo->query($sql);

			$id_img = $qry->fetch(PDO::FETCH_ASSOC)['id'];
		}
		else {
			$id_img = '';
		}

		$sql = "INSERT INTO rb_blog_posts(id_cat, id_author, id_primary_img, title, content, tags, status)

						VALUES(
							'".$data['categorie']."',
							'".$data['id_author']."',
							'".$id_img."',
							'".$data['title']."',
							'".$data['content']."',
							'".$data['tags']."',
							'".$status."'
						)
						";

		$qry = $this->pdo->exec($sql);

		return $qry;
	}

	public function editExistingBlogPost($data) {
		$status = ($data['action'] == 'post') ? 'active' : 'hidden';

		// If an image was selected
		// If an image was selected
		if (isset($data['primary_img_id']) && $data['primary_img_id'] != 0) {
			$id_img = $data['primary_img_id'];
		}
		// If an image was uploaded
		elseif ($data['uploaded_img'] == true) {
			$sql = "SELECT id FROM rb_images WHERE status = 'active' ORDER BY id DESC LIMIT 1;";
			$qry = $this->pdo->query($sql);

			$id_img = $qry->fetch(PDO::FETCH_ASSOC)['id'];
		}
		else {
			// Fetch currenct img_id
			$sql = "SELECT id_primary_img FROM rb_blog_posts WHERE id = '".$data['id']."' LIMIT 1;";
			$id_img = $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC)['id_primary_img'];
		}

		$sql = "UPDATE rb_blog_posts
						SET
							title = '".$data['title']."',
							content = '".$data['content']."',
							tags = '".$data['tags']."',
							status = '".$status."',
							id_primary_img = '$id_img'
						WHERE
							id = '".$data['id']."'
						LIMIT 1;
						";

//		die($sql);

		$qry = $this->pdo->exec($sql);

		return $qry;
	}

	private function _checkPostData($data) {
		$return = array();
		$return['passed'] = true;
		$return['messages'] = array();

		if (strlen($data['title']) <= 2) {
			$return['messages'][] = 'Gelieve een geldige titel in te voeren!';
			$return['passed'] = false;
		}

		if (strlen($data['content']) <= 2) {
			$return['messages'][] = 'Gelieve een volwaardig bericht in te voeren!';
			$return['passed'] = false;
		}

		return $return;
	}

	public function removeBlogPost($id) {
		$sql = "UPDATE rb_blog_posts SET status = 'deleted' WHERE id ='$id' LIMIT 1";

		return $this->pdo->exec($sql);
	}

	public function getFirstTwoBlogPosts($amount = 2) {
		$sql = "SELECT * FROM rb_blog_posts WHERE status = 'active' AND id_cat != 3 ORDER BY id DESC LIMIT $amount;";

		$qry = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

		// Conver all id_primary_img to Image objects
		foreach($qry as $i => $post) {
			$qry[$i]['image'] = new Image($post['id_primary_img']);
			$qry[$i]['categorie'] = $this->_getCategorie($post['id_cat']);
		}

		return $qry;
	}

	private function _getCategorie($id) {
		$sql = "SELECT name FROM rb_blog_cats WHERE id = '$id' LIMIT 1;";

		return $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC)['name'];
	}

	public function getBlogPosts($cat, $page = 1) {
		return $this->find(array(
			'select' => array('rb_blog_posts.*'),
			'from' => 'rb_blog_posts INNER JOIN rb_blog_cats ON rb_blog_posts.id_cat = rb_blog_cats.id',
			'where' => array('rb_blog_cats.name' => $cat, 'rb_blog_posts.status' => 'active'),
			'orderby' => 'rb_blog_posts.id DESC',
			'pagination' => array('page' => $page, 'results_per_page' => 8)
		));
	}

	public function getHiddenBlogPosts($page = 1) {
		return $this->find(array(
			'select' => array('rb_blog_posts.*'),
			'from' => 'rb_blog_posts INNER JOIN rb_blog_cats ON rb_blog_posts.id_cat = rb_blog_cats.id',
			'where' => array('rb_blog_posts.status' => 'hidden'),
			'orderby' => 'rb_blog_posts.id DESC',
			'pagination' => array('page' => $page, 'results_per_page' => 8)
		));
	}

	public function getBlogPost($id) {
		$sql = "SELECT * FROM rb_blog_posts WHERE id = '$id' LIMIT 1;";

		return $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
	}

	public function formatBlogUrl($post) {
		$id = $post['id'];
		$title = $post['title'];

		$title = str_replace(' ', '-', $title);

//		die(var_dump($post));

		return $id.'-'.$title;
	}

	public function updateViews($id, $amount = 1) {
		$sql = "UPDATE rb_blog_posts SET views = views + $amount WHERE id = '$id' LIMIT 1";

		return $this->pdo->exec($sql);
	}
}