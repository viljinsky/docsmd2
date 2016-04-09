<?php

    include_once '../config.php';
    include '../connect.php';
    
    function upload_image($screenshort_path){
        global $screenshort_link;
        $item_id = filter_input(INPUT_POST,'item_id');
        $tempfile = $_FILES['screenshort']['tmp_name'];
        $filename = urldecode($_FILES['screenshort']['name']);

        // закодированное имя файла
        $src = uniqid();

        if ( move_uploaded_file($tempfile,$screenshort_path.$src)){


            $sql = "insert into topic_images (item_id,src,filename) \n"
                  ."values ($item_id,'$src','$filename')";

            $result = mysql_query($sql);
            if (!$result){
                echo '{"error":1,"message":"'.  mysql_error().'","sql":"'.$sql.'"}';
                return;
            }

            $image_id = mysql_insert_id();
            return '{"error"   : 0,"message":"OK",'
                   .'"image_id": '.$image_id.','
                   .'"filename": "'.$filename.'",'
                   .'"src"     : "'.$screenshort_link.$src.'"}';
        }
        return '{"error":1,"message":"'.  mysql_error().'"}';
    
    }
    
    echo upload_image($screenshort_path);
    