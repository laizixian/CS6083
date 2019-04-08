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
        <h4>Filter List</h4>
        <?php
        // put your code here
        include('config.php');
        session_start();
        $username = $_SESSION['log_user'];
        if(!$filters = $conn->prepare('SELECT ruleID, tag_name, scheduleID, NID, friend_flag, state FROM Rules WHERE username = ?')){
            echo "prepare failed: (" .$conn->errno . ")" .$conn->error;
        }
        if(!$filters->bind_param('s', $username)){
            echo "Binding parameters failed: (" .$filters->errno . ")" . $filters->error;
        }
        if(!$filters->execute()){
            echo "Execute failed: (" .$filters->errno . ")" . $filters->error;
        }
        
        $res = $filters->get_result();
        echo "<table border = \"1\">\n";
        echo "\t\t<td>Filter tag<td>Filter state<td>Neighborhood<td>See<td>"
        . "Start time<td>End time<td>Repeat<td>Date<td>Action\n";
        echo "\t</tr>\n";
        if(mysqli_num_rows($res) > 0){
            while($row = mysqli_fetch_assoc($res)){
                //show the tag and state****************************************
                echo "\t\t<td>".$row['tag_name'];
                echo "<td>".$row['state'];
                //show the neighborhood name ***********************************
                if(!$nieghbor = $conn->prepare('SELECT n_name FROM Neighborhood WHERE NID = ?')){
                    echo "prepare failed: (" .$conn->errno . ")" .$conn->error;
                }       
                if(!$nieghbor->bind_param('i', $NID)){
                    echo "Binding parameters failed: (" .$nieghbor->errno . ")" . $nieghbor->error;
                }
                $NID = $row['NID'];
                if(!$nieghbor->execute()){
                    echo "Execute failed: (" .$nieghbor->errno . ")" . $nieghbor->error;
                }
                $res_NID = mysqli_fetch_array($nieghbor->get_result(), MYSQLI_ASSOC);
                echo "<td>".$res_NID['n_name'];
                
                //show the access
                if ($row['friend_flag'] == 1){
                    echo "<td>Everyone";
                }
                else if($row['friend_flag'] == 2){
                    echo "<td>Friend";
                }
                else if($row['friend_flag'] == 3){
                    echo "<td>Owner";
                }
                
                //show the time
                if(!$time = $conn->prepare('SELECT * FROM Schedules WHERE scheduleID = ?')){
                    echo "prepare failed: (" .$conn->errno . ")" .$conn->error;
                }       
                if(!$time->bind_param('i', $SID)){
                    echo "Binding parameters failed: (" .$time->errno . ")" . $time->error;
                }
                $SID = $row['scheduleID'];
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
                echo "<td> <a href=\"delete_schedule.php?scheduleID=".$row['scheduleID']."\" target=\"_BLANK\">Delete</a>";
                
                echo "\t</tr>\n";
            }
        }
        echo "</table>\n";
        ?>
        <h4>Add Filter</h4>
        <?php
            include('add_filter.php');
        ?>
        <form action = "" method = "post">
            <h5>Select tag</h5>
            <?php
                if(!$tag = $conn->prepare('SELECT * FROM Tag')){
                    echo "prepare failed: (" .$conn->errno . ")" .$conn->error;
                }       
                if(!$tag->execute()){
                    echo "Execute failed: (" .$tag->errno . ")" . $tag->error;
                }
                $tag_res = $tag->get_result();
                echo "<input type=\"radio\" name=tags value=NULL checked>Do not set<br>";
                if(mysqli_num_rows($tag_res) > 0){
                    while($tag_row = mysqli_fetch_assoc($tag_res)){
                        echo "<input type=\"radio\" name=tags value=".$tag_row['tag_name']."> ".$tag_row['tag_name']."<br>";
                    }
                }
            ?>
            <h5>Select neighborhood</h5>
            <?php
                include('list_neighbor.php');
            ?>
            <h5>Set Schedule</h5>
            <label>Start time  </label><input type = "time" name = "start_time" required=""/>
            <br />
            <label>End time  </label><input type = "time" name = "end_time" required=""/>
            <br />
            <label>Start date  </label><input type = "date" name = "start_date" required=""/>
            <br />
            <input type="radio" name=repeat value=0 checked> Do not Repeat
            <input type="radio" name=repeat value=1> Daily
            <input type="radio" name=repeat value=2> Monthly
            <input type="radio" name=repeat value=3> Yearly
            <input type="radio" name=repeat value=4> Weekly<br />
            <input type="radio" name=week value=1 checked> Sun
            <input type="radio" name=week value=2> Mon
            <input type="radio" name=week value=3> Tue
            <input type="radio" name=week value=4> Wed
            <input type="radio" name=week value=5> Thu
            <input type="radio" name=week value=6> Fri
            <input type="radio" name=week value=7> Sat
            <h5>Set access</h5>
            <input type="radio" name=access value=1 checked=""> Everyone
            <input type="radio" name=access value=2> Friend
            <input type="radio" name=access value=3> Private
            <h5>Set state</h5>
            <input type="text" name="state" class="box" maxlength="50" value=NULL><br />
            <input type = "submit" value = " Add "/><br />
        </form>
        <h3><a href="HomePage.php">Home Page</a></h3>
    </body>
</html>
