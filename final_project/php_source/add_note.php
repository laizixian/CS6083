<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $username = $_SESSION['log_user'];
    //add Schedule
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
    //add Note
    $lat = filter_input(INPUT_POST, 'lat');
    $lng = filter_input(INPUT_POST, 'lng');
    $radius = filter_input(INPUT_POST, 'radius');
    $content = filter_input(INPUT_POST, 'content');
    $allowC = filter_input(INPUT_POST, 'comment');
    $access = filter_input(INPUT_POST, 'access');
    if(!$add_note = $conn->prepare('INSERT INTO Note(username, scheduleID, point_location, radius, content, allowC, access) values (?, (select last_insert_id()), point(?, ?), ?, ?, ?, ?)')){
        echo "prepare failed: (" .$conn->errno . ")" .$conn->error;
    }
    if(!$add_note->bind_param('sddisii', $username, $lat, $lng, $radius, $content, $allowC, $access)){
        echo "Binding parameters failed: (" .$add_note->errno . ")" . $add_note->error;
    }
    if(!$add_note->execute()){
        echo "Execute failed: (" .$add_note->errno . ")" . $add_note->error;
    }
    //add tag 
    if(!$tag = $conn->prepare('SELECT * FROM Tag')){
        echo "prepare failed: (" .$conn->errno . ")" .$conn->error;
    }       
    if(!$tag->execute()){
        echo "Execute failed: (" .$tag->errno . ")" . $tag->error;
    }
    $tag_res = $tag->get_result();
    if(!$add_tag = $conn->prepare('INSERT INTO Has_tag(noteID, tag_name) values ((select last_insert_id()), ?)')){
        echo "prepare failed: (" .$conn->errno . ")" .$conn->error;
    }
    if(!$add_tag->bind_param('s', $tag_name)){
        echo "Binding parameters failed: (" .$add_tag->errno . ")" . $add_tag->error;
    }
    if(mysqli_num_rows($tag_res) > 0){
        while($tag_row = mysqli_fetch_assoc($tag_res)){
            $tag_name = filter_input(INPUT_POST, $tag_row['tag_name']);
            if($tag_name != NULL){
                $add_tag->execute();
            }
        }
    }
    mysqli_commit($conn);
    header("Location: my_note.php");
    
    
    
}
