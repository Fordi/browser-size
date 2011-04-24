<?php
$dayInSeconds = 24*3600;

$data = getCache('month-data', $dayInSeconds, true);
if (empty($data)) {
	$data = (object)array('height'=>array(), 'width'=>array());
	forEach(execQuery('month_width_data')->fetchAll(PDO::FETCH_CLASS) as $item)
		$data->width[$item->pixels] = $item->count;
	forEach(execQuery('month_height_data')->fetchAll(PDO::FETCH_CLASS) as $item)
		$data->height[$item->pixels] = $item->count;
	setCache('month-data', $data);
	$data =  json_encode($data);
}
echo tpl('json', array('json'=>$data));
