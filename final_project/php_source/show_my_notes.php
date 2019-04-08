<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include('config.php');
session_start();
$username = $_SESSION['log_user'];
if(!$stmt = $conn->prepare('SELECT scheduleID, noteID, X(point_location) as lat, Y(point_location) as lng, radius, allowC, access, content from Note WHERE username = ?')){
    echo "prepare failed: (" .$conn->errno . ")" .$conn->error;
}
if(!$stmt->bind_param('s', $username)){
    echo "Binding parameters failed: (" .$stmt->errno . ")" . $stmt->error;
}
if(!$stmt->execute()){
    echo "Execute failed: (" .$stmt->errno . ")" . $stmt->error;
}
$result = $stmt->get_result();
echo "<table border = \"1\">\n";
echo "\t\t<td>Content<td>Location<td>radius<td>Allowed<td>Start time<td>End time<td>Repeat<td>Date<td>Tags<td>Comment<td>Action\n";
echo "\t</tr>\n";
echo "bug";
if(mysqli_num_rows($result) > 0){
    while($note_row = mysqli_fetch_assoc($result)){
        echo "\t\t<td>".$note_row['content'];
        echo "<td>".$note_row['lat'].", ".$note_row['lng'];
        echo "<td>".$note_row['radius'];
        if ($note_row['access'] == 1){
            echo "<td>Everyone";
        }
        else if($note_row['access'] == 2){
            echo "<td>Friend";
        }
        else if($note_row['access'] == 3){
            echo "<td>Owner";
        }
        //show time
        if(!$time = $conn->prepare('SELECT * FROM Schedules WHERE scheduleID = ?')){
            echo "prepare failed: (" .$conn->errno . ")" .$conn->error;
        }       
        if(!$time->bind_param('i', $SID)){
            echo "Binding parameters failed: (" .$time->errno . ")" . $time->error;
        }
        $SID = $note_row['scheduleID'];
        if(!$time->execute()){
            echo "Execute failed: (" .$time->errno . ")" . $time->error;
        }
        $res_SID = mysqli_fetch_array($time->get_result(), MYSQLI_ASSOC);
        echo "<td>".$res_SID['start_time'];
        echo "<td>".$res_SID['end_time'];
        $date = strtotime($res_SID['start_date']);
        $day = date('d', $date);
        $month = date('m-d', $date);
        if($res_SID['week_flag'] == 0){
            if ($res_SID['repeat_flag'] == 0){
                echo "<td>Don't repeat";
                echo "test";
                echo "<td>".$res_SID['start_date'];
            }
            else if($res_SID['repeat_flag'] == 1){
                echo "<td>Daily";
                echo "<td>Everyday";
            }
            else if($res_SID['repeat_flag'] == 2){
                echo "<td>Monthly";
                echo "<td>".$day;
            }
            else if($res_SID['repeat_flag'] == 3){
                echo "<td>Yearly";
                echo "<td>".$month;
            }
        }
        else{
            echo "<td>Weekly";
            $daymap = array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fir', 'Sat');
            echo "<td>".$daymap[$res_SID['weekdays']-1];
        }
                
        if(!$tag = $conn->prepare('SELECT * FROM Has_tag WHERE noteID = ?')){
            echo "prepare failed: (" .$conn->errno . ")" .$conn->error;
        }
        if(!$tag->bind_param('i', $note_row['noteID'])){
            echo "Binding parameters failed: (" .$tag->errno . ")" . $tag->error;
        }
        if(!$tag->execute()){
            echo "Execute failed: (" .$time->errno . ")" . $time->error;
        }
        $tag_reslut = $tag->get_result();
        echo "<td>";
        if(mysqli_num_rows($tag_reslut) > 0){
            while($tag_row = mysqli_fetch_assoc($tag_reslut)){
                echo $tag_row['tag_name']."\n";
            }
        }
                
        if($note_row['allowC'] == 1){
            echo "<td> <a href=\"show_comment.php?noteID=".$note_row['noteID']."\" target=\"_BLANK\">Comment section</a>";
        }
        else{
            echo "<td>comments not allowed";
        }
        echo "<td> <a href=\"delete_schedule.php?scheduleID=".$note_row['scheduleID']."\" target=\"_BLANK\">Delete</a>";
        echo "\t</tr>\n";
    }
}
echo "</table>\n";
