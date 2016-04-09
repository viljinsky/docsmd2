<?php

    include '../connect.php';
    
    $item_id = filter_input(INPUT_GET,'item_id');
    $sql = "select comment_text from topic_item where item_id=$item_id";
    $result = mysql_query($sql);
    if (!$result){
        echo '{"error":1,"message":"'. mysql_error().'"}';
        return;
    }
    
    if (mysql_num_rows($result)>0){
        $data = mysql_fetch_array($result);    
        echo $data['comment_text'];
        return;
    }
    
    echo '{"error":1,"message":"'.mysql_error().'","sql":"'.$sql.'"}';
