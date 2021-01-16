<?php

session_start();

if(!isset($_SESSION['user'])){
	header('Location: index.php');
}

if($_SESSION['user'] != 'norman') {
	header('Location: index.php');
}

$servername = "localhost";
$username = "normansy_db";
$password = "Mikuchan39";
$dbname = "normansy_weight";

$date = $_POST['date'];
$weight = $_POST['weight'];

$lastDate = '2021-12-31';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "UPDATE data SET weight=$weight WHERE date='$date'";

if (!$conn->query($sql)) {
  echo "Error";
  die();
}

//-----------------------------

$sql = "SELECT * FROM data WHERE date = '$date'";
$result = $conn->query($sql);
$value = $result->fetch_assoc() ?? false;

$real_target = null;

if($value) {
	if($value['weight'] == null || $value['real_target'] == null) {
		echo 'Comparison values are empty';
		die();
	}else{
	    $real_target = 0;
	    $target = [$value['weight'], $value['initial_target']];
	    $target = min($target);
	    
	    if($target <= round($value['real_target'], 2)) {
	        $real_target = $target * 0.99910535;
	    }else{
	        $real_target = $value['real_target'];
	    }
	    
	}
}else{
	echo 'Error';
	die();
}

//------------------------------

if($date != $lastDate) {

	$tomorrow = date('Y-m-d', strtotime('+1 day', strtotime($date)));

	$sql = "UPDATE data SET real_target=$real_target WHERE date='$tomorrow'";

	if (!$conn->query($sql)) {
	  echo "Error";
	  die();
	}	
}

$conn->close();

header("Location: index.php?date=" . $date . '&success=1');