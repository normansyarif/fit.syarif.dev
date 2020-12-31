<?php

$data = [];

$weight = 94;
$date = '2020-12-25';


for($i = 0; $i < 372; $i++) {
	$temp = [];
	$temp['date'] = date('Y-m-d', strtotime('+'. $i .' day', strtotime($date)));

	if($i == 0) {
		$temp['weight'] = $weight;
	}else{
		$temp['weight'] = $weight * 0.9989645;
	}
	

	$weight = $temp['weight'];

	array_push($data, $temp);
}

echo json_encode($data);