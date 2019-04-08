<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include('config.php');
            session_start();
            $username = $_SESSION['log_user'];
            if(!$stmt = $conn->prepare('Select T.friend from(select case when username=? then friend when friend=? then username end as friend from Friend where flag = true) T where T.friend is not NULL')){
                echo "prepare failed: (" .$conn->errno . ")" .$conn->error;
            }
            if(!$stmt->bind_param('ss', $username, $username)){
                echo "Binding parameters failed: (" .$stmt->errno . ")" . $stmt->error;
            }
            if($stmt->execute()){
                $res = $stmt->get_result();
                echo "<table border = \"1\">\n";
                echo "\t\t<td>Friend name<td>status\n";
                echo "\t</tr>\n";
                if(mysqli_num_rows($res) > 0){
                    while($row = mysqli_fetch_assoc($res)){
                        if(!$status = $conn->prepare('Select first_name, last_name, login_status from Users where username=?')){
                            echo "prepare failed: (" .$conn->errno . ")" .$conn->error;
                        }
                        $friend = $row['friend'];
                        if(!$status->bind_param('s', $friend)){
                            echo "Binding parameters failed: (" .$status->errno . ")" . $status->error;
                        }
                        if(!$status->execute()){
                            echo "Execute failed: (" . $status->errno . ")" . $status->error;
                        }
                        $status_res = $status->get_result();
                        $status_row = mysqli_fetch_assoc($status_res);
                        echo "\t\t<td>".$status_row['first_name']." ".$status_row['last_name'];
                        if($status_row['login_status'] == 1){
                            echo "<td>Online";
                        }else{
                            echo "<td>Offline";
                        }
                        echo "\t</tr>\n";
                    }
                }
                echo "</table>\n";
            } else {
                echo "Execute failed: (" .$stmt->errno . ")" . $stmt->error;
            }

