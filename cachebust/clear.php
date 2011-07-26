<?php
// Silence : use a buffer
ob_start();

// Little config
$cacheBustFolders = array('css', 'js');
$cacheBustIsForced = strpos($_SERVER['REQUEST_URI'], 'cachebust/clear.php') !== false;
$cacheBustSitePath = $cacheBustIsForced ? '../' : '';
$cacheBustMin = $cacheBustIsForced;

// Read the last ID
include_once('id.php');

// Dependency
include_once('cssmin.php');


/* Temp */
$cacheBustId = substr($cacheBustId, 1, -4);
$newCacheBustId = (string) date('YmdHis');
/**/

// Fake Minification : Just Duplicate a File
function cacheBustMinFake($src, $target) {
	file_put_contents($target, file_get_contents($src)); 
}

// CSS Minification : Uses CSSMin
function cacheBustMinCSS($src, $target) {
	file_put_contents($target, CssMin::minify(file_get_contents($src)));
}

// JS Minification : Uses Google Closure Compiler Web Services (can be slow)
// Disabled for now
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

// Browse Folder : Recursive Method
function cacheBustBrowserFolder($path, $callback = false) {
	$exp = explode('/', $path);
	echo '<li><h2>'.$exp[count($exp)-1].'/</h2>';

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

// CacheBust File
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
			$minifyMethod = 'cacheBustMin'.(function_exists('cacheBustMin'.strtoupper($ext)) && $cacheBustMin ? strtoupper($ext) : 'Fake');
			
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
}

// Do the thing
foreach($cacheBustFolders as $folder) {
	cacheBustBrowserFolder($cacheBustSitePath.$folder, 'cacheBurstFile');
}


/* Apply New Id */
$cacheBustId = '.'.$newCacheBustId.'.min';
file_put_contents($cacheBustSitePath.'cachebust/id.php', '<?php $cacheBustId = \''.$cacheBustId.'\'; ?>');
/**/

// Close Buffer
$output = ob_get_clean();

// Show what happened only if it was asked
if($cacheBustIsForced) {
?>
<!doctype html>
<style type="text/css">
html {
	margin: 0;
	padding: 0;
	background: #444;
}

body {
	margin: 20px auto;
	padding: 40px;
	width: 700px;
	background: #EEE;
	color: #222;
	font-family: Monaco, monospace;
	font-size: 60%;
	
	-moz-box-shadow: rgba(0, 0, 0, 0.5) 0 3px 8px;
	-webkit-box-shadow: rgba(0, 0, 0, 0.5) 0 3px 8px;
	-o-box-shadow: rgba(0, 0, 0, 0.5) 0 3px 8px;
	-ms-box-shadow: rgba(0, 0, 0, 0.5) 0 3px 8px;
	box-shadow: rgba(0, 0, 0, 0.5) 0 3px 8px;
	
	-moz-border-radius: 3px;
	-webkit-border-radius: 3px;
	-o-border-radius: 3px;
	-ms-border-radius: 3px;
	border-radius: 3px;
}

h1 {
	margin: 0;
	text-align: center;	
}

h1, h2, h3, h4, h5, h6 {
	font-weight: normal;
}

ul {
	list-style: none;
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
<h1>CacheBust Report</h1>
<ul><?= $output ?></ul>
<?php } ?>