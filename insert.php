<?php

$servername = "localhost";
$username = "normansy_db";
$password = "Mikuchan39";
$dbname = "normansy_weight";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$data = [];

$weight = 90.2;
$date = '2021-01-16';
$stop = '2021-02-01';

for($i = 0; $i <= 366; $i++) {
	$temp = [];
	$dateToStore = date('Y-m-d', strtotime('+'. $i .' day', strtotime($date)));
	$temp['date'] = $dateToStore;

	if($i != 0) {
	    $weightToStore = $weight * 0.99910535;
	}else{
	    $weightToStore = $weight;
	}
	
	$temp['date'] = $dateToStore;
	$temp['weight'] = $weightToStore;
	
	$weight = $weightToStore;

    $sql = "INSERT INTO data (date, initial_target)
	VALUES ('$dateToStore', '$weightToStore')";

	if ($conn->query($sql) === TRUE) {
	  array_push($data, $temp);
	} else {
	  echo 'error';
	}
	
	if($dateToStore == $stop) {
	    break;
	}
	
}

return json_encode($data);

