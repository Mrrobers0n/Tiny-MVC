<?php

class ImageModel extends AppModel {

  public function __construct() {
    parent::__construct();

    if (!class_exists('Image'))
      App::load('Image.php');
  }

  public function getImage($id) {
    $sql = "SELECT id FROM rb_images WHERE id = '$id' LIMIT 1;";
    $id = $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC)['id'];

    return new Image($id);
  }

  public function getAllImages($orderby = 'ASC') {
    $sql = "SELECT id FROM rb_images WHERE status = 'active' ORDER BY id $orderby;";
    $data = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    foreach($data as $i => $img) {
      $data[$i] = new Image($img['id']);
    }

    return $data;
  }

  public function getLimitedImages($limit = 10, $orderby = 'DESC') {
    $sql = "SELECT id FROM rb_images WHERE status = 'active' ORDER BY id $orderby LIMIT $limit;";
    $data = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    foreach($data as $i => $img) {
      $data[$i] = new Image($img['id']);
    }

    return $data;
  }

  public function removeImage($id) {
    $sql = "UPDATE rb_images SET status = 'deleted' WHERE id = '$id' LIMIT 1;'";

    return $this->pdo->exec($sql);
  }

  public function editImage($id, $data) {
    $sql = "UPDATE rb_images
            SET
              name = '".$data['name']."',
              alt = '".$data['alt']."'
            WHERE
              id = '$id'
            LIMIT 1;
            ";

    $qry = $this->pdo->exec($sql);

    return $qry;
  }
}