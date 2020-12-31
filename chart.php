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

$sql = "SELECT * FROM data WHERE weight is not null ORDER BY date ASC";
$result = $conn->query($sql);

$data = [];

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    $temp = [];
    $temp['date'] = $row['date'];
    $temp['weight'] = $row['weight'];
    $temp['initial_target'] = round($row['initial_target'], 2);
    array_push($data, $temp);
  }
}

$conn->close();


?>

<!DOCTYPE html>
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Weight Tracking</title>
	<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <link rel="stylesheet" href="ui/jquery-ui.css">
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="ui/jquery-ui.js"></script>

  <style type="text/css">
    .container { padding: 2vw; }

    #slider {
      padding-left: 1%;
      padding-right: 1%; 
      border: none !important;
    }

    .bg1, .bg2, .bg3, .bg4, .bg5 { 
      -webkit-box-flex: 0;
      -ms-flex: 0 0 25%;
      flex: 0 0 25%;
      max-width: 25%;
      height: 10px; 
      position: relative;
      width: 100%;
      min-height: 1px;
      padding-right: 15px;
      padding-left: 15px;
    }

    .bg1 { 
        border-top-left-radius: 5px;
        border-bottom-left-radius: 5px;
        background-color: #2196F3; 
        
    }

    .bg2 { background-color: #388E3C; }

    .bg3 {
      background-color: #FBC02D
    }

    .bg4 {
      background-color: #E64A19;
      border-top-right-radius: 5px;
        border-bottom-right-radius: 5px;
    }

    .r {
      display: -webkit-box;
      display: -ms-flexbox;
      display: flex;
      -ms-flex-wrap: wrap;
      flex-wrap: wrap;
      margin-right: -15px;
      margin-left: -15px;
    }
  </style>

</head>
<body>

<div class="container">
    <div class="row">
		<div class="col-12 col-md-4 offset-md-4 col-lg-4 offset-lg-4 wrapper">
	        <canvas id="line-chart" width="500" height="400" style="margin-bottom: 30px"></canvas>	
	        
	        <div id="slider">
              <div class="r">
                <div class="bg1"></div>
                <div class="bg2"></div>
                <div class="bg3"></div>
                <div class="bg4"></div>
              </div>
            </div>
            <p style="text-align: center;margin-top: 30px">Current Weight: <span id="weight_n"></span></p>
            <p style="text-align: center">Your Best: <span id="best"></span></p>
            <p style="text-align: center">BMI: <span id="bmi_n"></span></p>
             <p style="text-align: center;font-weight: bold; margin-bottom: 50px" id="bmi_c"></p>
        </div>
    </div>
    
 </div>

  <script type="text/javascript">
    $(function() { 

      let date = <?php echo json_encode(array_column($data, 'date')) ?>;
      let weight = <?php echo json_encode(array_column($data, 'weight')) ?>;
      let initial_target = <?php echo json_encode(array_column($data, 'initial_target')) ?>;

      new Chart(document.getElementById("line-chart"), {
      type: 'line',
      data: {
        labels: date,
        datasets: [
            { 
                data: initial_target,
                label: "Initial Target",
                borderColor: "#B0BEC5",
                fill: false
           },
           { 
                data: weight,
                label: "Weight",
                borderColor: "#3e95cd",
                fill: false
           }
        ]
      },
      options: {
        title: {
          display: true,
          text: 'Weight progress so far'
        }
      }
    });
    
    Array.min = function( array ){
        return Math.min.apply( Math, array );
    };

      let lastWeight = weight[weight.length - 1];

      let bmi = lastWeight / (1.63*1.63);

      console.log(bmi);

      $("#slider").slider(); 

        // Underweight
      // 0 - 25%
      // 15 - 18.4

      // Healthy
      // 26 - 50%
      // 18.5 - 22.9

      // Overweight
      // 51 - 75%
      // 23 - 24.9

      // Obese
      // 76 - 100%
      // 25 - 35

      let persen = 0;

      if(bmi >= 0 && bmi < 18.5) {
        // underweight
        let satuBMIMewakili = 7.35294117647;
        persen = ((bmi-15)*satuBMIMewakili);
        $('#bmi_n').html((Math.round(bmi * 10) / 10).toFixed(1));
        $('#bmi_c').html('Underweight');
      }else if(bmi >= 18.5 && bmi <= 22.9) {
        // healthy
        let satuBMIMewakili = 5.68181818182;
        persen = 25 + ((bmi-18.5)*satuBMIMewakili);
        $('#bmi_n').html((Math.round(bmi * 10) / 10).toFixed(1));
        $('#bmi_c').html('Healthy');
      }else if(bmi > 22.9 && bmi <= 24.9) {
        // overweight
        let satuBMIMewakili = 13.1578947368;
        persen = 50 + ((bmi-23)*satuBMIMewakili);
        $('#bmi_n').html((Math.round(bmi * 10) / 10).toFixed(1));
        $('#bmi_c').html('Overweight');
      }else if(bmi > 24.9 && bmi <= 40) {
        // obese
        let satuBMIMewakili = 2.5;
        persen = 75 + ((bmi-25)*satuBMIMewakili);
        $('#bmi_n').html((Math.round(bmi * 10) / 10).toFixed(1));
        $('#bmi_c').html('Obese');
      }
      
      $('#weight_n').html(lastWeight + ' kg');
      $('#best').html(Array.min(weight) + ' kg');

      persen = persen + '%';

      $('.ui-slider-handle').css('left', persen);

    });

    

  </script>


</body>
</html>