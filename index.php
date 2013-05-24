<?php
/* This is useless for CacheBust use * /
if(fopen('cachebust/id.php', 'a+')) {
	die('ok');
}

/**/

// $isDev : Could be set by Framework, Host Detection or Whatever...
$isDev = true;

// Dev Environment : Forces Generation without Minification
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
		<li>To force cacheburst in production environment, just hit <code>/cachebust/clear.php</code>.</li>
	</ol>

	<h1>PHP Code</h1>

	<pre>
<strong>&lt;?php</strong>
<i>// $isDev : Could be set by Framework, Host Detection or Whatever...</i>
<var>$isDev</var> = <em>true</em>;

<i>// Dev Environment : Forces Generation without Minification</i>
<em>if</em>(<em>isset</em>(<var>$isDev</var>) && <var>$isDev</var>) {
  <em>include_once</em>(<var>'cachebust/clear.php'</var>);
}

<i>// Most Important Part, Get the Current ID</i>
<em>include_once</em>(<var>'cachebust/id.php'</var>);
<i>// Then, you just have to put $cacheBustId before files extensions</i>
<strong>?&gt;</strong>
</pre>
	<h1>HTML Code</h1>
	<pre>
&lt;!doctype html&gt;

<em>&lt;link</em> href="<var>stylesheet</var>" href="<var>css/style<strong>&lt;?= $cacheBustId ?&gt;</strong>.css</var>"<em>/&gt;</em>

<em>&lt;script</em> src="<var>js/script<strong>&lt;?= $cacheBustId ?&gt;</strong>.js</var>"<em>&gt;&lt;/script&gt;</em>
</pre>


	<!-- Notice the implementation -->
	<script src="js/script<?= $cacheBustId ?>.js"></script>
</body>
</html>