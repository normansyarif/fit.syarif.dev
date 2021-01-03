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
    .separator {
        padding-left: 3px;
        padding-right: 5px;
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
            
            <div class="row" style="margin: 30px 10px 0 10px">
                <div class="col-12">
                    <label style="font-weight: bold; font-style: italic">Stats</label>
                </div>
                <div class="col-6">
                    <table>
                        <tr>
                            <td>Weight</td>
                            <td class="separator">:</td>
                            <td><span id="weight_n"></span></td>
                        </tr>
                        <tr>
                            <td>Your best</td>
                            <td class="separator">:</td>
                            <td><span id="best"></span></td>
                        </tr>
                        <tr>
                            <td>Progress</td>
                            <td class="separator">:</td>
                            <td><span id="progress"></span> kg</td>
                        </tr>
                        <tr>
                            <td>To goal</td>
                            <td class="separator">:</td>
                            <td><span id="to_goal"></span> kg</td>
                        </tr>
                    </table>
                </div>
                <div class="col-6">
                    <table>
                        <tr>
                            <td>Strike</td>
                            <td class="separator">:</td>
                            <td><span id="strike"></span> d</td>
                        </tr>
                        <tr>
                            <td>Best strike</td>
                            <td class="separator">:</td>
                            <td><span id="best_strike"></span> d</td>
                        </tr>
                        <tr>
                            <td>BMI</td>
                            <td class="separator">:</td>
                            <td> <span id="bmi_n"></span></td>
                        </tr>
                        <tr>
                            <td>You are</td>
                            <td class="separator">:</td>
                            <td><span style="font-weight: bold" id="bmi_c"></span></td>
                        </tr>
                    </table>
                </div>
            </div>
            
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
    
    let startWeight = 94;
    let goal = 64;
    let tall = 1.63;
    
    let weightStrike = weight[0];
    let strike = 0;
    let bestStrike = 0;
    
    
    for(let i = 1; i < weight.length; i++) {
        if(weight[i] < weightStrike) {
            strike++;
            weightStrike = weight[i];
            
            if(strike > bestStrike) {
                bestStrike = strike;
            }
            
        }else{
            strike = 0;
        }
    }

      let lastWeight = weight[weight.length - 1];

      let bmi = lastWeight / (tall*tall);

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
        $('#bmi_n').html(Number((Math.round(bmi * 10) / 10).toFixed(1)));
        $('#bmi_c').html('Obese');
      }
      
      $('#weight_n').html(lastWeight + ' kg');
      $('#best').html(Array.min(weight) + ' kg');
      $('#strike').html(strike);
      $('#best_strike').html(bestStrike);
      $('#progress').html(Number(Math.round(startWeight - lastWeight).toFixed(2)));  
      $('#to_goal').html(Number(Math.round(lastWeight - goal).toFixed(2)));
    
      persen = persen + '%';

      $('.ui-slider-handle').css('left', persen);

    });

    

  </script>


</body>
</html>