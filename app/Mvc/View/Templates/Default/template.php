<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php echo $this->fetch('meta'); ?>
	<title><?php echo $this->fetch('doc_title');?> - Federico Moda</title>
	<?php
	// JS-Bestanden
	$this->script(array(
		'core.js',
		'jquery.colorbox-min.js',
		'bootstrap.min.js',
		'http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js',
		'http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js',
	));
	// CSS-Bestanden
	$this->css(array(
		'mainstyle.css',
		'colorbox.css',
		'bootstrap.min.css',
		'http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/themes/smoothness/jquery-ui.css',
	));
	?>

	<?=$this->css();?>
	<?=$this->script();?>

	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
	<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
	<![endif]-->

	<style type="text/css">
		body {
			background: url('../img/sitecontent/body_background2.png') repeat;
		}

		div.sitecontent {
			min-height: 633px;
			background: #fff;
			box-shadow: 2px 2px 2px #EEE;
		}

		/****** Main Navigation ******/
		header {
			height: 110px;
			width: 100%;
			background: url('../img/sitecontent/header_backgr.gif') repeat-x;
		}

		header div.logo {
			margin-top: 8px;
			float: left;
		}

		header nav.main {
			float: left;
			position: relative;
			top: 37px;
		}

		header nav.main li {
			float: left;
			list-style: none;
		}

		header nav.main li a {
			display: block;
			padding: 3px 12px;
			color: #004D5E;
			font-weight: bold;
			font-size: 18px;
			text-shadow: 1px 1px 1px #9BEEFF;
		}

		/****** Footer ******/

		footer {
			width: 100%;
			background: #EEE;
			border-top: 4px solid #dddddd;
			min-height: 235px;
			color: #555;
		}

		footer.main {
			padding-top: 0;
		}
		footer.main div.container {
			margin-top: 15px;
			background: #f6f6f6;
			min-height: 200px;
			border-radius: 7px;
		}

		/***** Item's block *****/
		.inner {
			padding: 5px;
		}

		.menu {}

		.menu .header {
			background: #141414;
			z-index: 12;
			color: #fff;
			/*max-width: 430px;*/
			margin-left: 2px;
		}

		.menu .header h3 {
			margin: 0;
			padding: 5px;
		}

		.menu .content {
			background: url('../img/sitecontent/menu_header.png') left top no-repeat #fff;
			z-index: 10;
			margin-top: -4px;
			padding: 5px;
		}

		.carousel-caption {
			left: 0;
			right: 0;
			bottom: 0px;
			background: rgba(0,0,0,0.65);
		}

		.carousel-indicators {
			bottom: 0px;
		}
	</style>
</head>
<body>
	<!-- Header & Main Nav -->
	<header>
		<div class="container">
			<div class="row">
				<div class="col-md-2 logo">
					<img src="/img/sitecontent/logo.png" alt="Federico Moda Logo Etalage" title="Federico Moda homepage" />
				</div>
				<nav class="col-md-10 main">
					<ul>
						<li class="active"><a href="<?=Config::SITE_PATH?>">Home</a></li>
						<li class="active"><a href="<?=Config::SITE_PATH?>">Merken</a></li>
						<li class="active"><a href="<?=Config::SITE_PATH?>">Promoties......</a></li>
						<li class="active"><a href="<?=Config::SITE_PATH?>">Contact/Ligging</a></li>
					</ul>
				</nav>
				<div class="clear"></div>
			</div>
		</div>
	</header>

	<div class="container sitecontent">
		<?=$this->fetch('content');?>
	</div>

	<footer class="main">
		<div class="container">
			<div class="row">
				<div class="col-md-3">
					<div style="position: relative;top:170px;">
						Federico Moda
					</div>
				</div>
				<div class="col-md-3">

				</div>
				<div class="col-md-3">

				</div>
				<div class="col-md-3">
					<div style="position: relative;top:150px;left: 80%">
						<a href="https://www.facebook.com/federico.moda?fref=federico-moda.be" title="Bezoek onze FB pagina!" target="_blank"><img src="/img/icons/fb.png" /></a>
					</div>
				</div>
			</div>
		</div>
	</footer>
</body>
</html>