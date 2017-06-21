<?php require_once "include_phpuploader.php" ?>
<?php require_once "../../Config/Config.php" ?>
<?php require_once "../../Lib/SimpleImage.php" ?>
<?php
$pdo = new PDO('mysql:dbname='.Config::DATABASE_NAME.';host='.Config::DATABASE_HOST, Config::DATABASE_USERNAME, Config::DATABASE_PASSWORD);

$SimpleImage = new SimpleImage();

set_time_limit(3600);

$uploader=new PhpUploader();

$uploader->PreProcessRequest();



$mvcfile=$uploader->GetValidatingFile();

if($mvcfile->FileName=="thisisanotvalidfile")
{
	$uploader->WriteValidationError("My custom error : Invalid file name. ");
	exit(200);
}


if( $uploader->SaveDirectory )
{
	if(!$uploader->AllowedFileExtensions)
	{
		$uploader->WriteValidationError("When using SaveDirectory property, you must specify AllowedFileExtensions for security purpose.");
		exit(200);
	}

	$cwd=getcwd();
	chdir( dirname($uploader->_SourceFileName) );
	if( ! is_dir($uploader->SaveDirectory) )
	{
		$uploader->WriteValidationError("Invalid SaveDirectory ! not exists.");
		exit(200);
	}
	chdir( $uploader->SaveDirectory );
	$wd=getcwd();
	chdir($cwd);

	$fn = substr(md5(time().$mvcfile->FileName),0,15).'_'.strtolower(str_replace(array(' '), array('_'), $mvcfile->FileName));
	$targetfilepath=  "$wd/" . $fn;
	if( file_exists ($targetfilepath) )
		unlink($targetfilepath);

	$mvcfile->CopyTo( $targetfilepath );
	$sql = "INSERT INTO fm_images(url, host) VALUES('$fn', 'local');";
	$pdo->exec($sql);

	$SimpleImage->load($targetfilepath);

	// If it's a 800x600-like resolution
	if ($SimpleImage->getWidth() > $SimpleImage->getHeight()) {
		$SimpleImage->resizeToWidth(800);
	}
	elseif ($SimpleImage->getHeight() > $SimpleImage->getWidth()) {
		$SimpleImage->resizeToHeight(600);
	}
	else {
		$SimpleImage->resize(600, 600);
	}

	$SimpleImage->save($targetfilepath, IMAGETYPE_JPEG, 87);
}

$uploader->WriteValidationOK("");

?>