<?php

session_start();

date_default_timezone_set('Asia/Jakarta');

if(!isset($_GET['date'])) {
	header("Location: index.php?date=" . date('Y-m-d', time()));
}

$servername = "localhost";
$username = "normansy_db";
$password = "Mikuchan39";
$dbname = "normansy_weight";

$selectedDate = $_GET['date'];

$data = [];

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

for($i = 1; $i > -2; $i--) {
	$temp = [];
	$date = date('Y-m-d', strtotime('-' . $i . ' day', strtotime($selectedDate)));
	$temp['date'] = $date;
	$sql = "SELECT * FROM data WHERE date = '$date'";
	$result = $conn->query($sql);
	$value = $result->fetch_assoc() ?? false;

	if($value) {
		$temp['initial_target'] = $value['initial_target'];
		$temp['real_target'] = $value['real_target'];
		$temp['weight'] = $value['weight'];
		
		$yesterday = date('Y-m-d', strtotime('-1 day', strtotime($date)));
		$yesterdaySql = "SELECT * FROM data WHERE date = '$yesterday'";
		$yesterdayResult = $conn->query($yesterdaySql);
		$yesterdayValue = $yesterdayResult->fetch_assoc() ?? false;
		
		$temp['last_weight'] = null;
		if($yesterdayValue) {
		    if($yesterdayValue['weight'] != null && $temp['weight'] != null) {
		        
		    }
		    $temp['last_weight'] = $yesterdayValue['weight'];
		}
		

		if($temp['weight'] == null || $temp['real_target'] == null || $temp['initial_target'] == null) {
			$temp['passed_initial'] = null;
			$temp['passed_real'] = null;
		}else{
			if($temp['weight'] <= $temp['initial_target']) {
				$temp['passed_initial'] = "true";
			}else{
				$temp['passed_initial'] = "false";
			}
			
			if($temp['weight'] <= $temp['real_target']) {
				$temp['passed_real'] = "true";
			}else{
				$temp['passed_real'] = "false";
			}
		}
		
	}else{
		$temp['initial_target'] = null;
		$temp['real_target'] = null;
		$temp['weight'] = null;
		$temp['passed'] = null;
	}

	if($temp['weight'] != null) {
		$temp['bmi_val'] = round($temp['weight'] / (1.63*1.63), 1);

		if($temp['bmi_val'] < 17) {
			$temp['bmi_cat'] = 'Overly thin';
		}elseif($temp['bmi_val'] >= 17 && $temp['bmi_val'] <= 18.4) {
		    $temp['bmi_cat'] = 'Underweight';
		}elseif($temp['bmi_val'] >= 18.5 && $temp['bmi_val'] <= 25) {
			$temp['bmi_cat'] = 'Healthy';
		}elseif($temp['bmi_val'] > 25 && $temp['bmi_val'] <= 27) {
			$temp['bmi_cat'] = 'Overweight';
		}elseif($temp['bmi_val'] > 27) {
			$temp['bmi_cat'] = 'Obese';
		}
	}

	array_push($data, $temp);

}

$conn->close();

?>

<!DOCTYPE html>
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Weight Tracking</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="bs.css">
	<style type="text/css">
		html, body {
			padding: 0;
			margin: 0;
			font-family: arial;
		}
		.wrapper {
			height: 100vh;
			padding: 0;
		}
		.box {
			height: 30vh;
			background: #263238;
			color: white;
		}
		.today {
			background: #1565C0;
		}
		.control {
			height: 10vh;
			text-align: center;
			background: #607D8B;
			display: grid;
			place-items: center;
		}
		.control-btn {
			
		}
		.control-btn a {
			font-weight: bold;
			text-decoration: none;
			color: white;
		}
		.weight {
			display: grid;
			place-items: center;
			height: 30vh;
			font-size: 2.5em;
			font-weight: bold;
		}
		.status {
			display: grid;
			place-items: center;
			font-size: .9em;
		}
		.status p {
			margin: 10px;
		}

		.nice-green {
			color: #66BB6A;
			font-weight: bold;
		}

		.nice-red {
			color: #FF8A65;
			font-weight: bold;	
		}
	</style>
</head>
<body>

	<?php
	if(isset($_GET['success'])):
	?>
	
	<div class="container-fluid notif" style="position: fixed;z-index: 100">
		<div class="row">
			<div class="col-12 col-md-4 offset-md-4 col-lg-4 offset-lg-4" style="color: white;height: 50px;line-height: 50px;text-align: center;background: #388E3C">
				Berhasil menyimpan data
			</div>
		</div>
	</div>

	<?php
	endif;
	?>

	<div class="container-fluid">
		<div class="row">
		<div class="col-12 col-md-4 offset-md-4 col-lg-4 offset-lg-4 wrapper">
			<?php
			foreach($data as $item):
			?>

			<div class="box <?php echo ($_GET['date'] == $item['date']) ? 'today' : '' ?>">
				<div class="row">
					<div class="col-7 status" style="padding: 0">
						<div>
							<p>Date:
							<br>
							<span style="font-weight: bold"><?php echo date('D, j M Y', strtotime($item['date'])) ?></span></p>
							<p>Initial target:
							<br>
								<span style="font-weight: bold">
								
								<?php 
								if($item['initial_target'] != null) {
									echo round($item['initial_target'], 2) . ' ';
									if($item['passed_initial'] == 'true') {
    									echo '<span class="nice-green">(&#8711; ' . round($item['initial_target'] - $item['weight'], 2) . ')</span>';
    								}else if($item['passed_initial'] == "false"){
    									echo '<span class="nice-red">(&#916; ' . round($item['weight'] - $item['initial_target'], 2) . ')</span>';
    								}else{
    									echo '';
    								}
								}else{
									echo 'TBD';
								}
								
								?>
								
								</span>
							</p>
							<p>Real target:<br>
								<span style="font-weight: bold">
								
								<?php 
								if($item['real_target'] != null) {
									echo round($item['real_target'], 2) . ' ';
									if($item['passed_real'] == 'true') {
    									echo '<span class="nice-green">(&#8711; ' . round($item['real_target'] - $item['weight'], 2) . ')</span>';
    								}else if($item['passed_real'] == "false"){
    									echo '<span class="nice-red">(&#916; ' . round($item['weight'] - $item['real_target'], 2) . ')</span>';
    								}else{
    									echo '';
    								}
								}else{
									echo 'TBD';
								}
								
								?> 
								
								</span>
							</p>
						</div>
					</div>
					<div class="col-5 weight" style="padding: 0">
						<p style="text-align: center;line-height: 20px">
						<?php 
						if($item['weight'] != null) {
							echo trim(round($item['weight'], 2)) . '<span style="font-size: .5em"> kg</span>';
							?>
							<br>
							<span style="font-size: .3em">BMI <?php echo $item['bmi_val'] . ' (' . $item['bmi_cat'] . ')' ?></span>
							<br>
						    <?php
						    if($item['last_weight'] != null && $item['weight'] != null) {
						        if($item['last_weight'] > $item['weight'] ) {
    								echo '<span style="font-size: .3em; position: relative; top: -5px" class="nice-green">(&#8711; ' . round($item['last_weight'] - $item['weight'], 2) . ')</span>';
    							}else{
    								echo '<span style="font-size: .3em; position: relative; top: -5px" class="nice-red">(&#916; ' . round($item['weight'] - $item['last_weight'], 2) . ')</span>';
    							}
						    }
						    
							?>
							<?php
						}
						
						?> 
						</p>
					</div>
				</div>
			</div>

			<?php
			endforeach;
			?>
			<div class="control">
				<div class="control-btn">
					<a href="index.php?date=<?php echo date('Y-m-d', strtotime('-1 day', strtotime($_GET['date']))) ?>" style="margin-right: 10px">Prev</a>
					
					<a href="chart.php" style="margin-left: 10px; margin-right: 10px">Chart</a>
					
					<?php
					if(isset($_SESSION['user'])):
						if($_SESSION['user'] == 'norman'):
						?>
						<a id="add-btn" href="javascript:void(0)" style="margin-left: 10px; margin-right: 10px">Set</a>
						
						<a href="glowup.php" style="margin-left: 10px; margin-right: 10px">Rules</a>
						<?php
						endif;
						endif;
					?>
					
					<a href="index.php?date=<?php echo date('Y-m-d', strtotime('+1 day', strtotime($_GET['date']))) ?>" style="margin-left: 10px">Next</a>
				</div>
			</div>
		</div>
	</div>
	</div>

	<form style="display: none" id="submit-form" action="add.php" method="POST">
		<input type="date" name="date" required value="<?php echo trim($_GET['date']) ?>">
		<input type="number" name="weight" required id="weight">
	</form>

	<script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

	<script type="text/javascript">
		$('#add-btn').click(function() {
			let weight = prompt('Enter your weight in kg');
			if(weight != null) {
				if(!isNaN(parseFloat(weight)) && parseFloat(weight) > 0) {
					$('#weight').val(parseFloat(weight));
					$('#submit-form').submit();
				}
			}
		});

		<?php
		if(isset($_GET['success'])):
		?>
		
		setTimeout(function(){ 
			$( ".notif" ).animate({
			    top: -50
			  }, 500, function() {
			    $(this).remove();
			  });
		}, 1000);

		<?php
		endif;
		?>
		

		
	</script>

</body>
</html>