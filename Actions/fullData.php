<?php
$dayInSeconds = 24*3600;
$monthInSeconds = $dayInSeconds*365.25/12;
$data = getCache('full-data', $monthInSeconds, true);
if (empty($data)) {
	//Make sure that the archive is up to date
	execQuery('houseclean');
	$data = (object)array('height'=>array(), 'width'=>array());
	forEach(execQuery('all_width')->fetchAll(PDO::FETCH_CLASS) as $item)
		$data->width[$item->pixels] = $item->count;
	forEach(execQuery('all_height')->fetchAll(PDO::FETCH_CLASS) as $item)
		$data->height[$item->pixels] = $item->count;
	setCache('full-data', $data);
	$data =  json_encode($data);		
}
exit(tpl('json', array('json'=>$data)));