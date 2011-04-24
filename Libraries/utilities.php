<?php
session_start();

DEFINE('CACHING', true);
function setCache($name, $value) {
	$fn = 'Cache/'.md5($name);
	if (file_exists($fn)) unlink($fn);
	file_put_contents($fn, json_encode($value));
}

function getCache($name, $age, $raw=false) {
	if (!CACHING) return null;
	$fn = 'Cache/'.md5($name);
	if (file_exists($fn)) {
		$stat = stat($fn);
		if ((mktime()-$stat[9]) <= $age) {
			$rv = file_get_contents($fn);
			if ($raw) return $rv;
			return json_decode($rv, true);
		}
	}
	return null;
}

function getCachedImage($name, $age) {
	if (!CACHING) return null;
	$fn = 'Cache/image-'.md5($name).'.png';
	if (file_exists($fn)) {
		$stat = stat($fn);
		if ((mktime()-$stat[9]) <= $age) {
			return file_get_contents($fn);
		}
	}
	return null;
}
function setCachedImage($name, $image) {
	$fn = 'Cache/image-'.md5($name).'.png';
	if (file_exists($fn)) unlink($fn);
	imageSaveAlpha($image, true);
	imagePNG($image, $fn, 9);
}


function tpl($__TEMPLATE, $__LOCALS=array()) {
	extract((array)$__LOCALS);
	ob_start();
	include('Views/'.$__TEMPLATE.'.php');
	return ob_get_clean();
}

function execQuery($stmt, $args=array()) {
	global $Database;
	static $queries = array();
	$queries[$stmt] = $Database->prepare($s=file_get_contents('Models/'.$stmt.'.sql'));
	$queries[$stmt]->execute($args);
	return $queries[$stmt];
}

function beAPixelAndDie() {
	Header('Content-type: image/gif');
	readfile('Static/pixel.gif');
	exit();	
}
function isRunDirectly() {
	$t = debug_backtrace();
	return realPath($t[0]['file']) == realPath($_SERVER['SCRIPT_FILENAME']);
}

function linearInterpolate($set, $count=null) {
	$set = (array)$set;
	ksort($set);
	$keys = array_keys($set);
	sort($keys);
	$min = min($keys);
	$max = max($keys);
	if ($count===null) $count = $max-$min+1;
	$interval = ($max-$min+1)/$count;
	$keypos = 0;
	$returnValue = array();
	
	$numSet = array();
	forEach($set as $index=>$value) $numSet[$index]=$value;
	$set = $numSet;
	
	for ($pos=$min; $pos<=$max; $pos+=$interval) {
		if (in_array($pos, $keys)) {
			$returnValue[$pos]=$set[$pos];
			continue;
		}
		while ($pos>=$keys[$keypos+1]) $keypos++;
		$dx = $keys[$keypos+1]-$keys[$keypos];
		$dy = $set[$keys[$keypos+1].'']-$set[$keys[$keypos].''];
		$px = $pos-$keys[$keypos];
		$returnValue[$pos]=$px*$dy/$dx+$set[$keys[$keypos]];
	}
	return $returnValue;
}

function __processAggregate($set, $pname, $lname) {
	$sum = 0;
	$agg = 0;
	$dset = array();
	$lines = array();
	forEach($set as $item) $sum+=$item->count;
	forEach($set as $item) $dset[$item->pixels] = ($agg+=$item->count)/$sum;
	$keys = array_keys($dset);

	$ospread = abs($dset[min($keys)] - $dset[max($keys)]);
	$kspread = max($keys) - min($keys);
	$zpoint = round($kspread*(1-$ospread)/$ospread+max($keys));
	$dset[$zpoint]=0;
	asort($dset);
	$keys = array_keys($dset);
	
	$ipos = 0;
	
	for ($i=0; $i<=1; $i+=0.1) {
		while ($i > $dset[$keys[$ipos]]) $ipos++;
		if (($i.'')==($dset[$keys[$ipos]].'')) { // wtf, floating point math?
			$lines[]=$keys[$ipos];
			continue;
		}
		$lines[]=round($keys[$ipos-1]+($keys[$ipos]-$keys[$ipos-1])*($i-$dset[$keys[$ipos-1]])/($dset[$keys[$ipos]]-$dset[$keys[$ipos-1]]));
	}
		
	$ret = array();
	$ret[$pname] = $dset;
	$ret[$lname] = $lines;
	return $ret;
}
function getAggregateData($asJSON=true) {
	$dayInSeconds = 24*3600;
	$data = getCache('aggregate-data', $dayInSeconds, $asJSON);
	if (empty($data)) {
		$data = array_merge(
			__processAggregate(execQuery('aggregate_width')->fetchAll(PDO::FETCH_CLASS), 'width', 'vlines'),
			__processAggregate(execQuery('aggregate_height')->fetchAll(PDO::FETCH_CLASS), 'height', 'hlines')
		);
		setCache('aggregate-data', $data);
		if ($asJSON) $data =  json_encode($data);
	}
	return $data;
}
function createFoldmaps() {
	$data = (array)getAggregateData(false);
	
	$heights = linearInterpolate($data['height']);
	$widths = linearInterpolate($data['width']);

	$h = min(2560, max(array_keys($heights)));
	$w = min(2560, max(array_keys($widths)));
	$y0 = min(array_keys($heights));
	$x0 = min(array_keys($widths));

	$lim = imageCreateTrueColor($w, $h);
	$cim = imageCreateTrueColor($w, $h);
	$rim = imageCreateTrueColor($w, $h);
	
	imageAlphaBlending($lim, false);
	imageAlphaBlending($cim, false);
	imageAlphaBlending($rim, false);
	
	$colors = array();
	for ($i=0; $i<128; $i++) 
		$colors[$i]=imageColorAllocateAlpha($lim, 128, 0, 255, $i);
	for ($y=0; $y<$h; $y++) {
		$yWeight = $y<$y0?1:$heights[$y];
		for ($x=0; $x<$w; $x++) {
			$lx = $x;
			$cx = abs($x-($w-1)/2)*2;
			$rx = $w-$x-1;
			$lxWeight = $lx<$x0?1:$widths[$lx];
			$cxWeight = $cx<$x0?1:$widths[$cx];
			$rxWeight = $rx<$x0?1:$widths[$rx];
			
			imageSetPixel($lim, $x, $y, $colors[floor($lxWeight*$yWeight*127)]);
			imageSetPixel($cim, $x, $y, $colors[floor($cxWeight*$yWeight*127)]);
			imageSetPixel($rim, $x, $y, $colors[floor($rxWeight*$yWeight*127)]);
		}
	}
	setCachedImage('foldmap-left', $lim);
	setCachedImage('foldmap-center', $cim);
	setCachedImage('foldmap-right', $rim);
	return (object)array('left'=>$lim, 'center'=>$cim, 'right'=>$rim);
}