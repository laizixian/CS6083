<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php
    include('config.php');
    session_start();
    
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        if ($conn->connect_error){
            die('Connection failed: '. $conn->connect_error);
        }
        //check the username and password
        $stmt = $conn->prepare('SELECT * FROM Users WHERE username=? AND pass_word=?');
        $stmt->bind_param('ss', $username, $password);
        $username = filter_input(INPUT_POST, 'username');
        $password = filter_input(INPUT_POST, 'password');
        $stmt->execute();
        $res = $stmt->get_result();
        $count = mysqli_num_rows($res);
        
        //if the info is all correct
        if($count == 1){
            //create session var and set login status to online
            $_SESSION['log_user'] = $username;
            mysqli_begin_transaction($conn, MYSQLI_TRANS_START_READ_WRITE);
            $change_status = $conn->prepare('UPDATE Users SET login_status = ? WHERE username = ?');
            $num = 1;
            $change_status->bind_param('is', $num, $username);
            $change_status->execute();
            mysqli_commit($conn);
            header('location: HomePage.php');
        }else{
            $error = 'username or password is invalid';
        } 
    }
?>
<html>
   
   <head>
      <title>Login Page</title>
   </head>
   
   <body bgcolor = "#FFFFFF">
	
      <div align = "left">
         <div style = "width:300px; border: solid 1px #333333; " align = "center">
            <div style = "background-color:#000000; color:#FFFFFF; padding:3px;"><b>Login</b></div>
				
            <div style = "margin:30px">
               
               <form action = "" method = "post">
                   <label>username  </label><input type = "text" name = "username" class = "box" required=""/><br /><br />
                   <label>password  </label><input type = "password" name = "password" class = "box" required=""/><br/><br />
                  <input type = "submit" value = " Submit "/><br />
               </form>
               
                <div style = "font-size:11px; color:#cc0000; margin-top:10px"><?php echo htmlspecialchars($error); ?></div>
                <a href="register.php">Register</a>
            </div>
				
         </div>
			
      </div>

   </body>
</html>
