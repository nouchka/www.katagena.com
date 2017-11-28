<?php
header ( 'Location: https://nouchka.katagena.com/' );
?>
<!doctype html>
<html class="no-js" lang="">
<head>
<meta charset="utf-8">
<meta http-equiv="x-ua-compatible" content="ie=edge">
<title>katagena.com</title>
<meta name="author" content="Jean-Avit Promis">
<meta name="description" content="katagena.com website">
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
body {
	background-color: #86b15c;
}

a {
	text-decoration: none;
	color: black;
}
</style>
</head>
<body>
<?php
require '../vendor/autoload.php';
use AntonioTajuelo\Gtm\Gtm;

Gtm::renderContainer ( 'GTM-T9CJCZK' );
Gtm::datalayerPush ( [ 
		'website' => 'home' 
] );
?>
	<!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

	<p align="center">
		<iframe width="560" height="315"
			src="https://www.youtube.com/embed/1d2S_EciiVY?autoplay=1&rel=0&amp;controls=0&amp;showinfo=0"
			frameborder="0" allowfullscreen></iframe>
	</p>
	<p align="center">
		<a href="https://japromis.katagena.com">resume</a> <a
			href="https://nouchka.katagena.com">blog</a> <a
			href="https://static.katagena.com/kobe/">kobe</a> <a
			href="https://static.katagena.com/lol/">league</a> <a
			href="https://static.katagena.com/ireland/2005/07/21/stage-en-irlande-dans-la-butler-gallery/index.html">ireland</a>
	</p>


</body>
</html>
