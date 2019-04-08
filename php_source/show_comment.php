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
        <?php
        // put your code here
        include('config.php');
        session_start();
        if ($conn->connect_error){
            die('Connection failed: '. $conn->connect_error);
        }
        $username = $_SESSION['log_user'];
        $noteID = filter_input(INPUT_GET, 'noteID');
        if(!$stmt = $conn->prepare('select username, comments from Comments where noteID = ?')){
            echo "prepare failed: (" .$conn->errno . ")" .$conn->error;
        }
        if(!$stmt->bind_param('i', $noteID)){
            echo "Binding parameters failed: (" .$stmt->errno . ")" . $stmt->error;
        }
        if(!$stmt->execute()){
            echo "Execute failed: (" .$stmt->errno . ")" . $stmt->error;
        }
        else{
            $result = $stmt->get_result();
            echo "<table border = \"1\">\n";
            echo "\t\t<td>User<td>Comment\n";
            echo "\t</tr>\n";
            if(mysqli_num_rows($result) > 0){
                while($row = mysqli_fetch_assoc($result)){
                    echo "\t\t<td>".$row['username'];
                    echo "<td>".$row['comments'];
                    echo "\t</tr>\n";
                }  
            }
            echo "</table>\n";
        }
        
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
                
                mysqli_begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
                if(!$stmt1 = $conn->prepare('INSERT INTO Comments(noteID, username, comments) VALUES (?, ?, ?)')){
                    echo "prepare failed: (" .$conn->errno . ")" .$conn->error;
                }
                $comments = filter_input(INPUT_POST, 'comment');
                $stmt1->bind_param('iss', $noteID, $username, $comments);
                if($stmt1->execute()){
                    mysqli_commit($conn);
                    header('location: show_comment.php?noteID='.$noteID);
                } else {
                    echo htmlspecialchars("Request already sent");
                }
        
            }
        ?>
        <form action = "" method = "post">
            <label>Add comment  </label><input type = "text" name = "comment" class = "box" maxlength="350"/><br /><br />
            <input type = "submit" value = " ADD "/><br />
        </form>
    </body>
</html>
