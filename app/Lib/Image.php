<?php

class Image {
  private $id;
  private $data;

  public function __construct($id) {
    $this->id = $id;

    // If no image was assigned, use the standard (ID=1);
    if ($id == 0)
      $this->id = 1;

    // Load in image data
    $this->_loadData();
  }

  private function _loadData() {
    $sql = "SELECT * FROM rb_images WHERE id = '$this->id' LIMIT 1;";
    $pdo = App::getPDO();

    $qry = $pdo->query($sql);

    $this->data = $qry->fetch(PDO::FETCH_ASSOC);
  }

  private function _getImageUrl() {
    $url = '';

    if ($this->data['host'] == 'local') {
      $url = '/img/uploads/';
      $url .= $this->data['url'];

      return $url;
    }
    else
      return $this->data['url'];
  }

  public function __get($property) {

    if ($property == 'url')
      return $this->_getImageUrl();

    // In normal case
    if (isset($this->data[$property]))
      return $this->data[$property];
    else
      return null;
  }
}