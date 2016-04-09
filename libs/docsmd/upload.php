<?php
#  Загрузка страницы на сервер
#
#
    include '../config.php';
    

    $filename = filter_input(INPUT_POST,'filename');
    $text = urldecode(filter_input(INPUT_POST, 'text'));
    
    $fp = $content_path.$filename;
    
    if (file_exists($fp)){
        $f = fopen($fp,'w');
        fwrite($f, $text);
        fclose($f);
        echo '{"error":0,"message":"OK"}';
        return;
    } else {
        echo '{"error":1,"message":"Файл '.$fp.' не найден"}';
    }
    
    
