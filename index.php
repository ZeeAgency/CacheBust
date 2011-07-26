<?php
// $isDev : Could be set by Framework, Host Detection or Whatever...
$isDev = true;

// Dev Environment : Forces New Version Generation without Minification for Debugging
if(isset($isDev) && $isDev) {
	include_once('cachebust/clear.php');
}

// Most Important Part, Get the Current ID
include_once('cachebust/id.php');
// Then, you just have to put $cacheBustId before files extensions

?>
<!doctype html>
<html>
<head>
	<title>CacheBust How-to</title>
	<!-- Notice the implementation -->
	<link rel="stylesheet" href="css/style<?= $cacheBustId ?>.css"/>
</head>
<body>
	<!-- Documentation -->
	<h1>CacheBust How-to</h1>
	<ol>
		<li>Make sure the <code>css</code>, <code>js</code> and <code>cachebust</code> folders are read-and-write-ables</li>
		<li>Include the CacheBust library like it is in this <code>index.php</code> file</li>
		<li>Set <var>$isDev</var> to reflect the working environment</li>
		<li>If <var>$isDev</var> is <code>true</code> then every page refresh will generate new file versions</li>
		<li>If <var>$isDev</var> is <code>false</code> then you must hit <code>/cachebust/clear.php</code> to force generation</li>
		<li>For now, only CSS minification is supported. By default, this minification is only done when <var>$isDev</var> is <code>false</code>.</li>
	</ol>
	
	<h1>Example</h1>
	<pre>
&lt;!doctype html&gt;
<em>&lt;html&gt;</em>
  <em>&lt;link</em> href="<var>stylesheet</var>" href="<var>css/style<strong>&lt;?= $cacheBustId ?&gt;</strong>.css</var>"<em>/&gt;</em>
  <em>&lt;script</em> src="<var>js/script<strong>&lt;?= $cacheBustId ?&gt;</strong>.js</var>"<em>&gt;&lt;/script&gt;</em>
<em>&lt;/html&gt;</em>
</pre>
	
	
	<!-- Notice the implementation -->
	<script src="js/script<?= $cacheBustId ?>.js"></script>
</body>
</html>