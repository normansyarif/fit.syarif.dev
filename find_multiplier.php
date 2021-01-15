<?php

$data = [];

$weight = 90;
$date = '2021-01-15';


for($i = 0; $i < 351; $i++) {
	$temp = [];
	$temp['date'] = date('Y-m-d', strtotime('+'. $i .' day', strtotime($date)));

	if($i == 0) {
		$temp['weight'] = $weight;
	}else{
		$temp['weight'] = $weight * 0.999114237;
	}
	

	$weight = $temp['weight'];

	array_push($data, $temp);
}

echo json_encode(array_reverse($data));