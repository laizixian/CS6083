<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
    include('config.php');
    session_start();
    
    $check_user = $_SESSION['log_user'];
    $stmt = $conn->prepare('SELECT first_name, last_name FROM Users WHERE username=?');
    $stmt->bind_param('s', $check_user);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = mysqli_fetch_array($res, MYSQLI_ASSOC);
    $First_name = $row['first_name'];
    $Last_name = $row['last_name'];
    
    if(!isset($_SESSION['log_user'])){
        header('location: login.php');
    }
?>