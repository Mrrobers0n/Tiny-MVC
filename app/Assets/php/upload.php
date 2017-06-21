<?php require_once "../../Config/Config.php" ?>
<?php require_once "../../Lib/upload.class.php" ?>
<?php require_once "../../Lib/SimpleImage.php" ?>

<?php
$SimpleImage = new SimpleImage();

/**
 * Handle file uploads via XMLHttpRequest
 */
class qqUploadedFileXhr
{
	/**
	 * Save the file to the specified path
	 * @param $path
	 * @return boolean TRUE on success
	 */
	function save($path)
	{
		$input = fopen("php://input", "r");
		$temp = tmpfile();
		//$temp = $this->resize($temp);
		$realSize = stream_copy_to_stream($input, $temp);
		fclose($input);
		if ($realSize != $this->getSize()) {
			return false;
		}
		$target = fopen($path, "w");
		fseek($temp, 0, SEEK_SET);
		stream_copy_to_stream($temp, $target);
		fclose($target);
		return true;
	}

	function getName()
	{
		return $_GET['qqfile'];
	}

	function getSize()
	{
		if (isset($_SERVER["CONTENT_LENGTH"])) {
			return (int)$_SERVER["CONTENT_LENGTH"];
		} else {
			throw new Exception('Getting content length is not supported.');
		}
	}

	function resize($path)
	{
		$imageObj = new SimpleImage();
		$imageObj->load($path);
		if ($imageObj->getWidth() > $imageObj->getHeight()) {
			$imageObj->resizeToWidth(800);
		} else if ($imageObj->getHeight() > $imageObj->getWidth()) {
			$imageObj->resizeToHeight(600);
		} else if ($imageObj->getHeight() == $imageObj->getWidth()) {
			$imageObj->resize(600, 600);
		}
		$imageObj->save($path, IMAGETYPE_JPEG);
	}
}

/**
 * Handle file uploads via regular form post (uses the $_FILES array)
 */
class qqUploadedFileForm
{
	/**
	 * Save the file to the specified path
	 * @param $path
	 * @return boolean TRUE on success
	 */
	function save($path)
	{
		if (!move_uploaded_file($_FILES['qqfile']['tmp_name'], $path)) {
			return false;
		}
		return true;
	}

	function getName()
	{
		return $_FILES['qqfile']['name'];
	}

	function getSize()
	{
		return $_FILES['qqfile']['size'];
	}
}

class qqFileUploader
{
	private $allowedExtensions = array();
	private $sizeLimit = 2621440;
	private $file;

	function __construct(array $allowedExtensions = array(), $sizeLimit = 10485760)
	{
		$allowedExtensions = array_map("strtolower", $allowedExtensions);
		$this->allowedExtensions = $allowedExtensions;
		$this->sizeLimit = $sizeLimit;
		$this->checkServerSettings();
		if (isset($_GET['qqfile'])) {
			$this->file = new qqUploadedFileXhr();
		} elseif (isset($_FILES['qqfile'])) {
			$this->file = new qqUploadedFileForm();
		} else {
			$this->file = false;
		}
	}

	private function checkServerSettings()
	{
		// $postSize = $this->toBytes(ini_get('post_max_size'));
		// $uploadSize = $this->toBytes(ini_get('upload_max_filesize'));
		// if ($postSize < $this->sizeLimit || $uploadSize < $this->sizeLimit){
		// $size = max(1, $this->sizeLimit / 1024 / 1024) . 'M';
		// die("{'error':'increase post_max_size and upload_max_filesize to $size'}");
		// }
	}

	private function toBytes($str)
	{
		$val = trim($str);
		$last = strtolower($str[strlen($str) - 1]);
		switch ($last) {
			case 'g':
				$val *= 1024;
			case 'm':
				$val *= 1024;
			case 'k':
				$val *= 1024;
		}
		return $val;
	}

	/**
	 * Returns array('success'=>true) or array('error'=>'error message')
	 */
	function handleUpload($uploadDirectory, $replaceOldFile = FALSE)
	{
		if (!is_writable($uploadDirectory)) {
			return array('error' => "Oei, er is bij ons een technisch probleem opgelopen. Probeer het later nog eens");
		}
		if (!$this->file) {
			return array('error' => 'Er zijn geen afbeeldingen geüpload');
		}
		$size = $this->file->getSize();
		if ($size == 0) {
			return array('error' => 'Uw bestand is leeg');
		}
		if ($size > $this->sizeLimit) {
			return array('error' => 'Uw afbeelding is te groot, je mag maximum afbeeldingen van 2,6mb uploaden.');
		}
		$pathinfo = pathinfo($this->file->getName());
//		$cID = isset($_SESSION['carID']) ? $_SESSION['carID'] : $_SESSION['car']['id'];
//		$filename = $_SESSION['user']['id'] . '_' . $cID . '_' . time(); // $pathinfo['filename'];
		$filename = md5(uniqid());
		$ext = $pathinfo['extension'];
		if ($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)) {
			$these = implode(', ', $this->allowedExtensions);
			return array('error' => 'Enkel afbeeldingen van volgende types worden toegelaten ' . $these . '.');
		}
		if (!$replaceOldFile) {
			/// don't overwrite previous files that were uploaded
			while (file_exists($uploadDirectory . $filename . '.' . $ext)) {
				$filename .= rand(10, 99);
			}
		}
		// MAX 27 images
//		$images = $this->getImages();
			$primary = null;
//		if ($images >= 21) {
//			return array('error' => 'U mag maar maximum 21 afbeeldingen uploaden!');
//		} else if ($images == 0) {
//			$primary = true;
//		} else {
//			$primary = false;
//		}

		if ($this->file->save($uploadDirectory . $filename . '.' . $ext)) {
			$this->file->resize($uploadDirectory . $filename . '.' . $ext);
			$this->addToDatabase($filename, $ext, $primary);
			return array('success' => true);
		} else {
			return array('error' => 'Could not save uploaded file.' .
					'The upload was cancelled, or server error encountered');
		}
	}

	function addToDatabase($filename, $ext, $primary)
	{
		$pdo = new PDO('mysql:dbname='.Config::DATABASE_NAME.';host='.Config::DATABASE_HOST, Config::DATABASE_USERNAME, Config::DATABASE_PASSWORD);
		$sql = "INSERT INTO rb_images(url, host) VALUES('$filename".'.'.$ext."', 'local');";

		$pdo->exec($sql);
	}

	function getImages()
	{

//		$usr = new user();
//		$cID = isset($_SESSION['carID']) ? $_SESSION['carID'] : $_SESSION['car']['id'];
//		$car = new car($usr);
//
//		return $car->getImages($cID, true);
		return array();
	}
}

// list of valid extensions, ex. array("jpeg", "xml", "bmp")
$allowedExtensions = array("jpg", "jpeg", "png", "gif");
// max file size in bytes
$sizeLimit = 10 * 1024 * 256;
//$cID = isset($_SESSION['carID']) ? $_SESSION['carID'] : $_SESSION['car']['id'];
//$gid = $user->getGroupID(true);
//$dir = str_replace('dev2/public_html', '', $_SERVER['DOCUMENT_ROOT']) . "media/data/$gid/$cID/";
//if (!is_dir($dir)) {
//	mkdir($dir, 0775, true);
//}
$dir = '../img/uploads/';
$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
// $result = $uploader->handleUpload($_SERVER['DOCUMENT_ROOT'].'../../media/data/'.$user->getGroupID().'/'.$cID.'/')
$result = $uploader->handleUpload($dir);
// to pass data through iframe you will need to encode all html tags
echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
