<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>Decline Friend</title>
    </head>
    <body>
        <h3>Friend_declined</h3>
        <?php
        // put your code here
        include('config.php');
        session_start();
        $friend = filter_input(INPUT_GET, 'name');
        $username = $_SESSION['log_user'];
        mysqli_begin_transaction($conn, MYSQLI_TRANS_START_READ_WRITE);
        if(!$stmt = $conn->prepare('DELETE FROM Friend where (username = ? and friend = ?) or (friend = ? and username = ?)')){
            echo "prepare failed: (" .$conn->errno . ")" .$conn->error;
        }
        if(!$stmt->bind_param('ssss', $friend, $username, $username, $friend)){
            echo "Binding parameters failed: (" .$stmt->errno . ")" . $stmt->error;
        }
        if(!$stmt->execute()){
            echo "Execute failed: (" .$stmt->errno . ")" . $stmt->error;
        }
        mysqli_commit($conn);
        ?>
    </body>
</html>
