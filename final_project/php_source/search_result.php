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
        <h3><a href="HomePage.php">Home Page</a></h3>
        <h4>Search Result</h4>
        <?php
        // put your code here
            include("config.php");
            session_start();
            $search_key = filter_input(INPUT_GET, 'search_text');
            $pattern = str_replace(" ", ".+", $search_key);
            $username = $_SESSION['log_user'];
            if(!$user_info = $conn->prepare('SELECT X(point_location) as lat, Y(point_location) as lng, state, time_stamp FROM Users WHERE username = ?')){
                echo "prepare failed: (" .$conn->errno . ")" .$conn->error;
            }
            if(!$user_info->bind_param('s', $username)){
                echo "Binding parameters failed: (" .$user_info->errno . ")" . $user_info->error;
            }
            if(!$user_info->execute()){
                echo "Execute failed: (" .$user_info->errno . ")" . $user_info->error;
            }
            $res = $user_info->get_result();
            $row = mysqli_fetch_array($res, MYSQLI_ASSOC);
            $user_location_x = $row['lat'];
            $user_location_y = $row['lng'];
            $user_state = $row['state'];
            $user_timestamp = $row['time_stamp'];
            
            if(!$stmt = $conn->prepare('call search_note(?, point(?, ?), ?, ?, ?)')){
                echo "prepare failed: (" .$conn->errno . ")" .$conn->error;
            }
            if(!$stmt->bind_param('sddsss', $username, $user_location_x, $user_location_y, $user_state, $user_timestamp, $pattern)){
                echo "Binding parameters failed: (" .$stmt->errno . ")" . $stmt->error;
            }
            if(!$stmt->execute()){
                echo "Execute failed: (" .$stmt->errno . ")" . $stmt->error;
            }
            $result = $stmt->get_result();
            echo "<table border = \"1\">\n";
            echo "\t\t<td>Owner<td>Notes<td>Comment\n";
            echo "\t</tr>\n";
            if(mysqli_num_rows($result) > 0){
                while($note_row = mysqli_fetch_assoc($result)){
                    echo "\t\t<td>".$note_row['username'];
                    echo "<td>".$note_row['content'];
                    if($note_row['allowC'] == 1){
                        echo "<td> <a href=\"show_comment.php?noteID=".$note_row['noteID']."\" target=\"_BLANK\">Comment section</a>";
                    }
                    else{
                        echo "<td>comments not allowed";
                    }
                    echo "\t</tr>\n";
                }
            }
            echo "</table>\n";
        ?>
    </body>
</html>
