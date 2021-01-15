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

    .bg1, .bg2, .bg3, .bg4, .bg0 { 
      -webkit-box-flex: 0;
      -ms-flex: 0 0 20%;
      flex: 0 0 20%;
      max-width: 20%;
      height: 10px; 
      position: relative;
      width: 100%;
      min-height: 1px;
      padding-right: 15px;
      padding-left: 15px;
    }
    
    .bg0 {
        border-top-left-radius: 5px;
        border-bottom-left-radius: 5px;
        background-color: #7c00a0;
    }

    .bg1 { 
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
    
    /*Override*/
    .r {
      margin-left: 0 !important;
    }
    
    .ui-state-default {
      border: 2px solid white  !important;
      background: cusotm !important;
    }
    
    .ui-slider .ui-slider-handle {
      width: 0.5em !important;
      height: 2em !important;
    }
    
    .ui-corner-all, .ui-corner-bottom, .ui-corner-right, .ui-corner-br {
      border-radius: 10px !important;
    }
    
    .ui-slider-horizontal .ui-slider-handle {
      top: -0.65em !important;
      pointer-events: none !important;
      margin: 0 !important;
    }
  </style>

</head>
<body>

<div class="container">
    <div class="row">
    <div class="col-12 col-md-6 offset-md-3 col-lg-6 offset-lg-3 wrapper">
          <canvas id="line-chart" width="500" height="400" style="margin-bottom: 30px"></canvas>  
          
          <div id="slider">
              <div class="r">
                <div class="bg0"></div>
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
                            <td>Lost</td>
                            <td class="separator">:</td>
                            <td><span id="progress"></span> kg</td>
                        </tr>
                        <tr>
                            <td>Still to go</td>
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
                            <td colspan="3">You are <span style="font-weight: bold" id="bmi_c"></span></td>
                        </tr>
                    </table>
                </div>
            </div>
            
        </div>
    </div>
    
 </div>

  <script type="text/javascript">
    $(function() { 
        
      let goal = 66;
      let tall = 163;
      
      tall /= 100;
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
    
    let startWeight = weight[0];
    
    let weightStrike = weight[0];
    let strike = 0;
    let bestStrike = 0;
    
    if(weight.length > 1) {
        for(let i = 1; i < weight.length; i++) {
            if(weight[i] < weightStrike) {
                strike++;
                
                if(strike > bestStrike) {
                    bestStrike = strike;
                }
                
            }else{
                strike = 0;
            }
            weightStrike = weight[i];
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

      if(bmi >= 9 && bmi < 17) {
        // underweight 2
        let satuBMIMewakili = 2.53164556962;
        persen = ((bmi-9)*satuBMIMewakili);
        $('#bmi_c').html('Overly thin');
        $('.ui-state-default').css('background', '#7c00a0');
      }else if(bmi >= 17 && bmi <= 18.4) {
        // underweight 1
        let satuBMIMewakili = 14.2857142857;
        persen = 20 + ((bmi-17)*satuBMIMewakili);
        $('#bmi_c').html('Underweight');
        $('.ui-state-default').css('background', '#2196F3');
      }else if(bmi > 18.4 && bmi <= 25) {
        // normal
        let satuBMIMewakili = 3.07692307692;
        persen = 40 + ((bmi-18.5)*satuBMIMewakili);
        $('#bmi_c').html('Healthy');
        $('.ui-state-default').css('background', '#388E3C');
      }else if(bmi > 25 && bmi <= 27) {
          // obese 1
          let satuBMIMewakili = 10.5263157895;
          persen = 60 + ((bmi-25.1)*satuBMIMewakili);
          $('#bmi_c').html('Overweight');
          $('.ui-state-default').css('background', '#FBC02D');
      }else if(bmi > 27 && bmi <= 35) {
        // obese 2
        let satuBMIMewakili = 2.53164556962;
        persen = 80 + ((bmi-27.1)*satuBMIMewakili);
        $('#bmi_c').html('Obese');
        $('.ui-state-default').css('background', '#E64A19');
      }
      
      $('#bmi_n').html(Math.round((bmi + Number.EPSILON) * 10) / 10);
      
      $('#weight_n').html(lastWeight + ' kg');
      $('#best').html(Array.min(weight) + ' kg');
      $('#strike').html(strike);
      $('#best_strike').html(bestStrike);
      $('#progress').html(Math.round(((startWeight - lastWeight) + Number.EPSILON) * 100) / 100);  
      $('#to_goal').html(Math.round(((lastWeight - goal) + Number.EPSILON) * 100) / 100);

      console.log(startWeight - lastWeight);
    
      persen = persen + '%';

      $('.ui-slider-handle').css('left', persen);

    });

    

  </script>


</body>
</html>