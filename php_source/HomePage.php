<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php
    include('session.php');
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Home_Page</title>
    </head>
    <body>
        <h2>Welcome <?php echo htmlspecialchars("$Last_name $First_name")?></h2>
        <h4>Current State</h4>
        <?php include('show_states.php')?>
        <a href="change_state.php">Change</a>
        <h4>Note List</h4>
        <?php include('show_notes.php'); ?>
        <form action="search_result.php" method="GET">
            <input type="search" name="search_text" required="" />
            <input type="submit" name="submit"/>
        </form>
        
        <h4><a href="my_note.php">My Notes</a></h4>
        <h4><a href="filters.php">Filters</a></h4>
        <h3><a href="friends.php">Friend</a></h3>
        <h3><a href="Change.php">Change location and time</a></h3>
        <h3><a href="logout.php">Log out</a></h3>
    </body>
</html>
