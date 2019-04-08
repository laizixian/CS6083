<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include('config');
session_start();
$username = $_SESSION['log_user'];
$stmt = $conn->prepare('SELECT state FROM Users WHERE username=?');
$stmt->bind_param('s', $username);
$stmt->execute();
$res = $stmt->get_result();
$row = mysqli_fetch_array($res, MYSQLI_ASSOC);
echo $row['state'];
