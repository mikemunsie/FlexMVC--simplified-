<!doctype html>
<html lang="en">
	<head>
		<?php flexMVC::include_basehref(); ?>
		<link rel="SHORTCUT ICON" href="_public/images/favicon.png"/>
		<title><?php echo(flexMVC::$title); ?></title>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    	<meta name="viewport" content="width=device-width, minimum-scale=1, maximum-scale=1.0" />
		<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
		<meta charset="utf-8" />
		<meta name="keywords" content="FlexMVC" />
		<meta name="description" content="FlexMVC PHP MVVM Framework" />
		<meta property="og:title" content="FlexMVC" />
		<meta property="og:type" content="website" />
		<meta property="og:image" content="http://mikemunsie.com/_public/images/facebook_share.png" />
		<meta property="og:site_name" content="FlexMVC PHP MVVM Framework" />	
		<link rel="stylesheet" type="text/css" media="all" href="_public/stylesheets/style.css"/>
		<?php \flexMVC::output_css(); ?>
	</head>
	<body class="<?php echo(flexMVC::$bodyclass); ?>">
		<?php echo($content); ?>
		<?php \flexMVC::output_scripts(); ?>
	</body>
</html>
