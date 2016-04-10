<?php

include_once '../config.php';


if (!file_exists(CONTENT_PATH.CONTENT_TPL)){
    $txt = file_get_contents('test.txt');// "index;Содержание;\n    page1;page1;\n    page2;page2;\nimages;immages;\n    img_curriculum_small;curriculum_small";
    $handle = fopen(CONTENT_PATH.CONTENT_TPL,'w');
    fwrite($handle, $txt);
    fclose($handle);
    echo 'Создан шаблон content.tpl<br>';
}

include './site-map.php';

// почитать имена файлов изображений
function image_list($image_path){
    $a = array();
    $f =  scandir($image_path);
    foreach ($f as $file):
        if(preg_match('/\.png/', $file)):
            $s = explode('.', $file);
            $a[] = $s[0];
        endif;
    endforeach;
    return $a;
}




//Заполняем линки страниц из содержания.

function page_generator($map){

    $filename =CONTENT_PATH.LINK_TPL;
    if (file_exists($filename)) {unlink ($filename);}

    echo 'Добавление линков<br>';
    $handle = fopen($filename, 'w');
    foreach ($map as $v){
        echo '['.$v['page'].']: '.DOC_PAGE.'?page='.$v['page']."<br>";
        fwrite($handle,'['.$v['page'].']:'."\t\t\t".' '.DOC_PAGE.'?page='.$v['page']."\n");
    }
    fclose($handle);

    //Создать страницы если не созданы;
    echo 'Добавление сраниц<br>';
    foreach ($map as $v){
        $filename = CONTENT_PATH.$v['page'].'.md';
        if ($v['parent']==='images'):
            continue;
        endif;
        if (!file_exists($filename)){
            echo 'Создание страницы "'.$filename.'"<br>';
            $f=  fopen($filename,'w');
            fwrite($f, '# '.$v['title'].CR.CR);
            fwrite($f, '*Страница создана автоматически 1*'.CR.CR);
            // добавть заголовки подстатей
            fwrite($f, getContent($map, $v['page']));
            fclose($f);
        }
    }

    echo 'OK<br>';

}

function image_generator($map){
    global $image_path,$image_path_link;
    // Создать страницы
    $image_list = image_list($image_path);
    foreach ($image_list as $image):
        $pp = getPage($map, $image);
        if ($pp===null){
            continue;
        }

        $filename = CONTENT_PATH.$image.'.md';
        if (!file_exists($filename)){
            $f = fopen($filename,'w');


            fwrite($f, '# '.$pp['title'].CR.CR);
    //        fwrite($f, $filename.CR.CR);
            fwrite($f, ' ссылка в md :'.CR.'`!['.$pp['title'].']['.$image.'.png]`'.CR);
            fwrite($f, '!['.$image.']['.$image.'.png]'.CR);
            fclose($f);
        }
        echo $image.'<br>';
    endforeach;


    // дописать ссылки
    $f = fopen(CONTENT_PATH.LINK_TPL, 'a');
    fwrite($f,CR);
    foreach ($image_list as $image):
        $pp = getPage($map, $image);
        if ($pp===null){
            continue;
        }
        fwrite($f, '['.$image.'.png]: '."\t\t\t".$image_path_link.$image.'.png'.' "'.$pp['title'].'"'."\n");
    endforeach;
    fclose($f);

}

page_generator($map);

image_generator($map);