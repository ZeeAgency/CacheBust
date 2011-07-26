<?php
// $isDev : Could be set by framework, host detection or whatever…
$isDev = true;

// Dev Environment : Forces New Version Generation and No-Minification for Debugging
if(isset($isDev) && $isDev) {

	// Could be easiest…
	// ie. $cacheBustPath = 'http://'.$_SERVER['HTTP_HOST'].'/';
	$cacheBustPath = 
		'http://'.
		$_SERVER['HTTP_HOST'].
		implode('/', array_slice(explode('/', $_SERVER['REQUEST_URI']), 0, -1)).
		'/cachebust/clear.php?nomin';
		
	// Do the CacheBusting
	file_get_contents($cacheBustPath);
}

// Most important part...
include_once('cachebust/id.php');
?>
<!doctype html>
<html>
<head>
	<title>CacheBust How-to</title>
	<link rel="stylesheet" href="css/style<?= $cacheBustId ?>.css"/>
</head>
<body>
	<h1>CacheBust How-to</h1>
	<ol>
		<li>Make sure the <code>css</code>, <code>js</code> and <code>cachebust</code> folders are read-and-write-ables</li>
		<li>Include the CacheBust library like it is in this <code>index.php</code> file</li>
		<li>Set <var>$isDev</var> to reflect the working environment</li>
		<li>If <var>$isDev</var> is <code>true</code> then every page refresh will generate new file versions</li>
		<li>If <var>$isDev</var> is <code>false</code> then you must hit <code>/cachebust/clear.php</code> to force generation</li>
		<li>For now, only CSS minification is supported. By default, this minification is only done when <var>$isDev</var> is <code>false</code>.</li>
	</ol>
	
	<script src="js/script<?= $cacheBustId ?>.js"></script>
</body>
</html>