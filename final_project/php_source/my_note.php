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
        <h3>My notes list</h3>
        <?php
        // put your code here
        include(config.php);
        session_start();
        include('show_my_notes.php');
        ?>
        <h4>Add Note</h4>
        <?php
            include('add_note.php');
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
                if(mysqli_num_rows($tag_res) > 0){
                    while($tag_row = mysqli_fetch_assoc($tag_res)){
                        //echo "<input type=\"checkbox\" name=tag value=".$tag_row['tag_name']."> ".$tag_row['tag_name']."<br>";
                        echo "<input type=\"checkbox\" name=".$tag_row['tag_name']." value=".$tag_row['tag_name']."> ".$tag_row['tag_name']."<br>";
                    }
                }
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
            <h5>Set Location</h5>
            <label>Latitude  </label><input type="number" name = "lat" step="0.000001" required=""/>
            <label>Longitude  </label><input type="number" name = "lng" step="0.000001" required=""/>
            <h5>Set Radius</h5>
            <label>Radius  </label><input type="number" name = "radius" step="1" required=""/>
            <h5>Set Content</h5>
            <label>Content  </label><input type="text" name = "content" maxlength="350" required=""/>
            <h5>Set Access</h5>
            <input type="radio" name=access value=1 checked=""> Everyone
            <input type="radio" name=access value=2> Friend
            <input type="radio" name=access value=3> Private
            <h5>Set Comment</h5>
            <input type="radio" name=comment value=1 checked=""> Allow
            <input type="radio" name=comment value=0> Do not allow
            <br /><br /><input type = "submit" value = " Add "/><br />
        </form>
    </body>
</html>
