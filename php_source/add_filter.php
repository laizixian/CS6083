<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $username = $_SESSION['log_user'];
    $tag = filter_input(INPUT_POST, 'tags');
    $neighbor = filter_input(INPUT_POST, 'neighbors');
    $start_time = filter_input(INPUT_POST, 'start_time');
    $end_time = filter_input(INPUT_POST, 'end_time');
    $start_date = filter_input(INPUT_POST, 'start_date');
    $repeat = filter_input(INPUT_POST, 'repeat');
    $weekflag = 0;
    $weekday = 0;
    if($repeat == 4){
        $weekflag = 1;
        $repeat = NULL;
        $weekday = filter_input(INPUT_POST, 'week');
    }
    
    $access = filter_input(INPUT_POST, 'access');
    $state = filter_input(INPUT_POST, 'state');
    if($state == "NULL"){
        $state = NULL;
    }
    //add schedule
    mysqli_begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
    if(!$schedule = $conn->prepare('insert into Schedules(start_time, end_time, start_date, repeat_flag, weekdays, week_flag) value(?, ?, ?, ?, ?, ?)')){
        echo "prepare failed: (" .$conn->errno . ")" .$conn->error;
    }
    if(!$schedule->bind_param('sssiii', $start_time, $end_time, $start_date, $repeat, $weekday, $weekflag)){
        echo "Binding parameters failed: (" .$schedule->errno . ")" . $schedule->error;
    }
    if(!$schedule->execute()){
        echo "Execute failed: (" .$schedule->errno . ")" . $schedule->error;
    }
    
    //add filter
    if($neighbor == "NULL" && $tag == "NULL"){
        echo "echo";
        if(!$rule = $conn->prepare('insert into Rules(username, scheduleID, friend_flag, state) values (?, (select last_insert_id()), ?, ?)')){
            echo "prepare failed: (" .$conn->errno . ")" .$conn->error;
        }
        if(!$rule->bind_param('sis', $username, $access, $state)){
            echo "Binding parameters failed: (" .$rule->errno . ")" . $rule->error;
        }
    }
    else if($tag == "NULL"){
        if(!$rule = $conn->prepare('insert into Rules(username, scheduleID, NID, friend_flag, state) values (?, (select last_insert_id()), ?, ?, ?)')){
            echo "prepare failed: (" .$conn->errno . ")" .$conn->error;
        }
        if(!$rule->bind_param('siis', $username, $neighbor, $access, $state)){
            echo "Binding parameters failed: (" .$rule->errno . ")" . $rule->error;
        }
    }
    else if($neighbor == "NULL"){
        if(!$rule = $conn->prepare('insert into Rules(username, tag_name, scheduleID, friend_flag, state) values (?, ?, (select last_insert_id()), ?, ?)')){
            echo "prepare failed: (" .$conn->errno . ")" .$conn->error;
        }
        if(!$rule->bind_param('ssis', $username, $tag, $access, $state)){
            echo "Binding parameters failed: (" .$rule->errno . ")" . $rule->error;
        }
    }
    else{
        if(!$rule = $conn->prepare('insert into Rules(username, tag_name, scheduleID, NID, friend_flag, state) values (?, ?, (select last_insert_id()), ?, ?, ?)')){
            echo "prepare failed: (" .$conn->errno . ")" .$conn->error;
        }
        if(!$rule->bind_param('ssiii', $username, $tag, $neighbor, $access, $state)){
            echo "Binding parameters failed: (" .$rule->errno . ")" . $rule->error;
        }
    }

    if(!$rule->execute()){
        echo "Execute failed: (" .$rule->errno . ")" . $rule->error;
        mysqli_rollback($conn);
    }
    else{
        mysqli_commit($conn);
        header('Location: filters.php');
    }
    
    
}
