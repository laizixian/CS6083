<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include('config.php');
session_start();
//change the login status to offline
mysqli_begin_transaction($conn, MYSQLI_TRANS_START_READ_WRITE);
$change_status = $conn->prepare('UPDATE Users SET login_status = ? WHERE username = ?');
$num = 0;
$username = $_SESSION['log_user'];
$change_status->bind_param('is', $num, $username);
$change_status->execute();
mysqli_commit($conn);

session_unset();
mysqli_close($conn);
if(session_destroy()){
    header("Location: login.php");
}
?>