<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php
    include('config.php');
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        if ($conn->connect_error){
            die('Connection failed: '. $conn->connect_error);
        }
        mysqli_begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
        $stmt = $conn->prepare('INSERT INTO Users(username, last_name, first_name, pass_word, email) VALUES (?, ?, ?, ?, ?)');
        $username = filter_input(INPUT_POST, 'username');
        $password = filter_input(INPUT_POST, 'password');
        $last_name = filter_input(INPUT_POST, 'last_name');
        $first_name = filter_input(INPUT_POST, 'first_name');
        $email = filter_input(INPUT_POST, 'email');
        $stmt->bind_param('sssss', $username, $last_name, $first_name, $password, $email);
        if($stmt->execute()){
            mysqli_commit($conn);
            header('location: register_success.php');
        } else {
            echo htmlspecialchars("Username already used, place use another username");
        }
        
    }
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Register</title>
    </head>
    <body>
        <form action = "" method = "post">
            <label>Username  </label><input type = "text" name = "username" class = "box" required/><br /><br />
            <label>Last name  </label><input type = "text" name = "last_name" class = "box" required/><br /><br />
            <label>First name  </label><input type = "text" name = "first_name" class = "box" required/><br /><br />
            <label>Email  </label><input type="email" name = "email" class = "box" required/><br /><br />
            <label>Password  </label><input type = "password" name = "password" class = "box" required/><br/><br />
            <input type = "submit" value = " Register "/><br />
        </form>
    </body>
</html>
