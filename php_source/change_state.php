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
        if ($conn->connect_error){
            die('Connection failed: '. $conn->connect_error);
        }
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
                
            mysqli_begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
            if(!$stmt1 = $conn->prepare('UPDATE Users SET state=? WHERE username = ?')){
                echo "prepare failed: (" .$conn->errno . ")" .$conn->error;
            }
            $username = $_SESSION['log_user'];
            $state = filter_input(INPUT_POST, 'state');
            $stmt1->bind_param('ss', $state, $username);
            if($stmt1->execute()){
                mysqli_commit($conn);
                header('location: HomePage.php'.$noteID);
            } else {
                echo htmlspecialchars("Failed");
            }
        
        }
        ?>
        <form action = "" method = "post">
            <label>New state  </label><input type = "text" name = "state" class = "box" maxlength="50"/><br /><br />
            <input type = "submit" value = " Change "/><br />
        </form>
    </body>
</html>
