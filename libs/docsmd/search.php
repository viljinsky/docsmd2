<?php

    include '../config.php';
    
    include './pattern.php';
    
    function fsearch($word){
        
        global $content_path,$doc_page,$map;

        echo 'Вы искали <strong>'.$word.'</strong><br>';
        $count = 0;
        if (strlen($word)>0){
            $word= mb_strtolower($word,'UTF-8');
            foreach ($map as $value){
                $page = $value['page'];

                $filename = $content_path.$page.'.md';
                if (file_exists($filename)){
                    $txt = mb_strtolower(file_get_contents($filename),'UTF-8');

                    if (preg_match("/$word/", $txt,$matches)>0){
                        $count++;
                        echo '<div class="search-item"><a href="'.$doc_page.'?page='.$value['page'].'">'.$value['title'].'</a><br>';
                        echo print_r($matches);
                        echo "<br><br>";
                        echo $txt;
                        echo '</div>';
                    }

                }
            }
        }    
        echo '<div>Всего найдено '.$count.'</div>';

    }
    
    $search = urldecode(filter_input(INPUT_GET,'search'));
    fsearch($search);
    
