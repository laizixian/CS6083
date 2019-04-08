<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>Friends</title>
    </head>
    <body>
        <h3><a href="HomePage.php">Home page</a></h3>
        <h4>Friend list</h4>
        <?php
            include('friend_list.php');
        ?>
        <h4>Friend Request</h4>
        <?php
            include('firend_request.php');
        ?>
        <h4>Add Friend</h4>
        <?php
            include('config.php');
            session_start();
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                if ($conn->connect_error){
                    die('Connection failed: '. $conn->connect_error);
                 }
                 mysqli_begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
                if(!$stmt = $conn->prepare('INSERT INTO Friend(username, friend, flag) VALUES (?, ?, ?)')){
                    echo "prepare failed: (" .$conn->errno . ")" .$conn->error;
                }
                $username = $_SESSION['log_user'];
                $friend = filter_input(INPUT_POST, 'username');
                $flag = 0;
                $stmt->bind_param('ssi', $username, $friend, $flag);
                if($stmt->execute()){
                    mysqli_commit($conn);
                    header('location: add_friend.php');
                } else {
                    echo htmlspecialchars("Request already sent");
                }
        
            }
        ?>
        <form action = "" method = "post">
            <label>Friend username  </label><input type = "text" name = "username" class = "box"/><br /><br />
            <input type = "submit" value = " Request "/><br />
        </form>
    </body>
</html>
