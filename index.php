<?php
require_once('Libraries/database.php');
require_once('Libraries/utilities.php');

$query = $_SERVER['REDIRECT_QUERY_STRING'];

//Make sure that the main table has been cleared of hits older than 10 months
// Do this seldomly, once every 1500-3000 hits
if (mt_rand(0,1500)==0) execQuery('houseclean')->closeCursor();

//Simple controller
switch (true) {
	case substr($query,-3,3)=='.js':
		Header('Content-type: text/javascript');
		exit(tpl('magic_script'));
	case preg_match('/^(\d+)x(\d+)$/', $query, $regs):
		if (empty($_SESSION['CAUGHT'])) {
			
			list($size, $width, $height) = $regs;
			
			
			execQuery('add_width', array(':pixels'=>$width))->closeCursor();
			execQuery('add_height', array(':pixels'=>$height))->closeCursor();

			$_SESSION['CAUGHT']=true;
		}
		beAPixelAndDie();
	default:
		$tfn = 'Actions/'.$query.'.php';
		if (file_exists($tfn)) {
			include($tfn);
			exit();
		}
		exit(tpl('default'));
}