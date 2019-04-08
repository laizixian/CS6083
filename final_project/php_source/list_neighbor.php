<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if(!$neighbor = $conn->prepare('SELECT NID, N_name FROM Neighborhood')){
                    echo "prepare failed: (" .$conn->errno . ")" .$conn->error;
                }       
                if(!$neighbor->execute()){
                    echo "Execute failed: (" .$neighbor->errno . ")" . $neighbor->error;
                }
                $neighbor_res = $neighbor->get_result();
                echo "<input type=\"radio\" name=neighbors value=NULL checked>Do not set<br>";
                if(mysqli_num_rows($neighbor_res) > 0){
                    while($neighbor_row = mysqli_fetch_assoc($neighbor_res)){
                        echo "<input type=\"radio\" name=neighbors value=".$neighbor_row['NID']."> ".$neighbor_row['N_name']."<br>";
                    }
                }
