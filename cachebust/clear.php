<?php
$cacheBustFolders = array('css', 'js');
//$cacheBustSitePath = implode('/', array_slice(array_slice(explode('/', $_SERVER['SCRIPT_FILENAME']), 0, -1), 0, -1)).'/';
$cacheBustSitePath = '../';

include_once('cssmin.php');
include_once('id.php');

/* Temp */
$cacheBustId = substr($cacheBustId, 1, -4);
$newCacheBustId = (string) date('YmdHis');
/**/

/* Fake Minification : Just Duplicate a File */
function cacheBustMinFake($src, $target) {
	file_put_contents($target, file_get_contents($src)); 
}

/* CSS Minification : Uses CSSMin */
function cacheBustMinCSS($src, $target) {
	file_put_contents($target, CssMin::minify(file_get_contents($src)));
}

/* JS Minification : Uses Google Closure Compiler Web Services (can be slow) */
function unused_cacheBustMinJS($src, $target) {
	$script = file_get_contents($src);
	$ch = curl_init('http://closure-compiler.appspot.com/compile');
	 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, 'output_info=compiled_code&output_format=text&compilation_level=SIMPLE_OPTIMIZATIONS&js_code=' . urlencode($script));
	$output = curl_exec($ch);
	curl_close($ch);
	file_put_contents($target, $output);
}


/* Browse Folder : Recursive Method */
function cacheBustBrowserFolder($path, $callback = false) {
	$exp = explode('/', $path);
	echo '<li><h1>'.$exp[count($exp)-1].'/</h1>';

	if($dir = opendir($path)) {
	 	echo '<ul>';
		while(false !== ($file = readdir($dir))) {
			if(substr($file, 0, 1) !== '.') {
				if(is_dir($path.'/'.$file)) {
					
					cacheBustBrowserFolder($path.'/'.$file, $callback);
					
				} elseif($callback) {
					$callback($file, $path);
				}
			}
		}
		echo '</ul>';
		closedir($dir);
	}
	echo '</li>';
};

/* CacheBust File */
function cacheBurstFile($file, $path) {
	global $cacheBustId, $newCacheBustId;
	
	$exp = explode('.', $file);
	$ext = array_pop($exp);
	$filename = implode('.', $exp);
	$isMin = $exp[count($exp)-1] === 'min';
	
	if(!$isMin) {
		echo '<li><h2>'.$file.'</h2></li>';
		$oldVersion = $filename.'.'.$cacheBustId.'.min.'.$ext;
		$newVersion = $filename.'.'.$newCacheBustId.'.min.'.$ext;
		
		if(is_file($path.'/'.$oldVersion)) {
			echo '<li><h2><del>'.$oldVersion.'</del></h2></li>';
			unlink($path.'/'.$oldVersion);
		}
		
		
		if($ext === 'css' || $ext === 'js') {
			$minifyMethod = 'cacheBustMin'.(function_exists('cacheBustMin'.strtoupper($ext)) && !isset($_GET['nomin']) ? strtoupper($ext) : 'Fake');
			
			$minifyMethod($path.'/'.$file, $path.'/'.$newVersion);
			echo '<li><h2><ins>'.$newVersion.'</ins></h2></li>';
		}
	} else {
		
		preg_match('/(\.'.date('Y').')/', $file, $matches);
		if(is_file($path.'/'.$file) && count($matches) > 0) {
			echo '<li><h2><del>'.$file.'</del></h2></li>';
			unlink($path.'/'.$file);
		}
	}
};

foreach($cacheBustFolders as $folder) {
	cacheBustBrowserFolder($cacheBustSitePath.$folder, 'cacheBurstFile');
}


/* Apply New Id */
$cacheBustId = '.'.$newCacheBustId.'.min';
file_put_contents('id.php', '<?php $cacheBustId = \''.$cacheBustId.'\'; ?>');
/**/
$output = ob_get_clean();

?>
<!doctype html>
<style type="text/css">
body {
	font-family: Monaco, monospace;
	font-size: 60%;
}
ul {
	list-style: none;
}
h1, h2, h3, h4, h5, h6 {
	font-weight: normal;
}
del {
	text-decoration: line-through;
	color: #AAA;
}
ins {
	text-decoration: none;
	background: #88e649;
	-webkit-box-shadow: #88e649 0 0 0px 3px;
}
</style>
<ul><?= $output ?></ul>