<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        // put your code here
        session_start();
        include('config.php');
        $username = $_SESSION['log_user'];
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $lat = filter_input(INPUT_POST, 'lat');
            $lng = filter_input(INPUT_POST, 'lng');
            $date = filter_input(INPUT_POST, 'date');
            $time = filter_input(INPUT_POST, 'time');
            $time_stamp = $date." ".$time;
            if(!$change = $conn->prepare('UPDATE Users SET point_location=point(?,?), time_stamp=? where username = ?')){
                echo "prepare failed: (" .$conn->errno . ")" .$conn->error;
            }    
            if(!$change->bind_param('ddss', $lat, $lng, $time_stamp, $username)){
                echo "Binding parameters failed: (" .$change->errno . ")" . $change->error;
            }
            if(!$change->execute()){
                echo "Execute failed: (" .$change->errno . ")" . $change->error;
            }
            else{
                mysqli_commit($conn);
            }
        }
        ?>
        <form action="" method="POST">
            <h5>Set Location</h5>
            <label>Latitude  </label><input type="number" name = "lat" step="0.000001" required=""/>
            <label>Longitude  </label><input type="number" name = "lng" step="0.000001" required=""/>
            <h5>Set datetime</h5>
            <label>Date  </label><input type = "date" name = "date" required=""/>
            <br />
            <label>Time  </label><input type = "time" name = "time" required=""/>
            <br /><br />
            <input type="submit" name="submit"/>
        </form>
        <h3><a href="HomePage.php">Home Page</a></h3>
    </body>
</html>
