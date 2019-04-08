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
        include('config.php');
        session_start();
        mysqli_begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
        if(!$stmt = $conn->prepare('DELETE from Schedules WHERE scheduleID = ?')){
            echo "prepare failed: (" .$conn->errno . ")" .$conn->error;
        }
        
        $ruleID = filter_input(INPUT_GET, 'scheduleID');
        if(!$stmt->bind_param('i', $ruleID)){
            echo "Binding parameters failed: (" .$stmt->errno . ")" . $stmt->error;
        }
        if(!$stmt->execute()){
            echo "Execute failed: (" .$stmt->errno . ")" . $stmt->error;
        }
        else{
            mysqli_commit($conn);
            echo "<h4>Delete Successful</h4>";
        }
        ?>
    </body>
</html>
