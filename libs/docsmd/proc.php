<?php

include '../connect.php';

include_once './Parsedown.php';

$command = filter_input(INPUT_POST,'command');
if (isset($command)){
    switch ($command){
        case 'add':
        case 'replay':
            add_message();
            break;
        case 'edit':
            edit_message();
            break;
        case 'delete':
            echo delete_message();
            break;
        case 'read':
            readmessages();
            break;
        case 'quotes':
            quotes();
            break;
        case 'upload_attach':
            echo upload_attach();
            break;
        case 'upload_page':
            upload_page();
            break;
    }
}

/**
 * Загруска страицы документации
 * @return type
 */
function upload_page(){

    $filename = filter_input(INPUT_POST,'filename');
    $text = urldecode(filter_input(INPUT_POST, 'text'));
    
    $fp = CONTENT_PATH.$filename;
    
    if (file_exists($fp)){
        $f = fopen($fp,'w');
        fwrite($f, $text);
        fclose($f);
        echo '{"error":0,"message":"OK"}';
    } else {
        echo '{"error":1,"message":"Файл '.$fp.' не найден"}';
    }
    
}

function upload_attach(){
    global $screenshort_link,$screenshort_path;
    $item_id = filter_input(INPUT_POST,'item_id');
    $tempfile = $_FILES['screenshort']['tmp_name'];
    $filename = urldecode($_FILES['screenshort']['name']);

    // закодированное имя файла
    $src = uniqid();

    if ( move_uploaded_file($tempfile,$screenshort_path.$src)){


        $sql = "insert into topic_images (item_id,src,filename) \n"
              ."values ($item_id,'$src','$filename')";

        $result = mysql_query($sql);
        if ($result){
            $image_id = mysql_insert_id();
            return '{"error"   : 0,"message":"OK",'
                   .'"image_id": '.$image_id.','
                   .'"filename": "'.$filename.'",'
                   .'"src"     : "'.$screenshort_link.$src.'" }';
        } 
    } 
//    return '{"error":1,"message":"'.  mysql_error().'"}';
}


function quotes(){
    $item_id = filter_input(INPUT_POST,'item_id');
    $sql = "select comment_text from topic_item where item_id=$item_id";
    $result = mysql_query($sql) or die('quotes : '.  mysql_error());
    
    if (mysql_num_rows($result)>0){
        $data = mysql_fetch_array($result);    
        echo $data['comment_text'];
        return;
    }
    
    echo '{"error":1,"message":"'.mysql_error().'","sql":"'.$sql.'"}';
}

    
function get_item_attachment($item_id){
    global $screenshort_link;
    $html = '';
    $result = mysql_query("select image_id,filename,src from topic_images where item_id=$item_id") 
            or die("Ошибка get_item_images :"+  mysql_error());
    if (mysql_num_rows($result)>0){
        $html.='<div>Прикреплено</div>';
        while ($data=  mysql_fetch_array($result)){
            list($image_id,$filename,$src)=$data;
            $html.="<div>$image_id $filename $src  <a href='".$screenshort_link.$src."' target='_blank'>$filename</a>"
                 ."&nbsp;<a href='#' title='Удалить'>...</a>"
                    . "</div>";

        }
        return $html;
    }
    return '<div>нет прикреплений</div>';
}    

function read_message_item($item_id){

    $parse = new Parsedown();

    $sql = "select a.user_id,a.topic_id,a.comment_time,a.comment_text,"
      ."concat(u.last_name,' ',u.first_name),a.replay_to,'TOPIC_NAME',"
      ."(select count(*) from topic_item where user_id=a.user_id) "
      ."from topic_item a inner join users u on a.user_id=u.user_id\n"
      ."  where a.item_id=$item_id";

    $result=mysql_query($sql) or die("read_message_item : ".  mysql_error());

    $data = mysql_fetch_array($result);

    list($user_id,$comment_id,$comment_time,$comment_text,$user_name,$replay_to,
            $topic_name,$message_count)=$data;

    //$comment_text = str_replace("\n",'<br>',$comment_text);

    $comment_text = $parse->text($comment_text);

    $attr = 'data-user-id="'.$user_id.'" data-comment-id="'.$item_id.'" ';        
        if (isset($replay_to)){
            $attr.=' data-replay-to="'.$replay_to.'"';
    }

    include './message_text.php';
    echo get_item_attachment($item_id);

}


function add_message(){

    $topic_name=  urldecode(filter_input(INPUT_POST,'topic_name'));
    $message = htmlspecialchars(urldecode(filter_input(INPUT_POST, 'message')),ENT_QUOTES);
    $user_id = filter_input(INPUT_POST, 'user_id');
    $replay_to = filter_input(INPUT_POST,'replay_to');

    if (!isset($replay_to) || $replay_to===''):
        $replay_to='null';
    endif;

    $result = mysql_query("select topic_id from topic where topic_name='$topic_name'");
    if (mysql_num_rows($result)===0){
        $sql = "insert into topic (topic_name) values('$topic_name')";
        mysql_query($sql) or die('sql: '.$sql."\n message: ".mysql_error());
        $sql = "select max(topic_id) from topic";
        $result = mysql_query($sql) or die("sql: ".$sql."\n message:".mysql_error());
    }
    list($topic_id) = mysql_fetch_array($result);

    $sql = "insert into topic_item (topic_id,replay_to,comment_text,user_id) "
          ."values ($topic_id,$replay_to,'$message',$user_id)";

    if (!mysql_query($sql)):
        echo mysql_error().' '.$sql;
    endif;

    $item_id = mysql_insert_id();
    read_message_item($item_id);

}


function edit_message(){

    $item_id = filter_input(INPUT_POST,'item_id');
    $message = htmlspecialchars(urldecode(filter_input(INPUT_POST,'message')),ENT_QUOTES);

    $sql = "update topic_item set comment_text = '$message' where item_id=$item_id";
    if (!mysql_query($sql)):
        return mysql_error()+' '+$sql;
    endif;

    read_message_item($item_id);
}

function delete_message(){
    $item_id= filter_input(INPUT_POST,'item_id');

    $sql = "delete from topic_item where replay_to=$item_id";
    mysql_query($sql);

    $sql = "delete from topic_item where item_id=$item_id";
    if (mysql_query($sql)){
        return '{"error":0,"message":"Сообщение удалено"}';
    } else {
        return '{"error":1,"sql":"'.$sql.'","message":"'.mysql_error().'"}';
    }

} 

/**
 * Чтение списка сообщений
 * @return type
 */    
    

    
function readmessages(){
    
    $page = urldecode(filter_input(INPUT_POST, 'page'));

    $sql = "select count(*),b.topic_id \n"
          ."from topic_item a inner join topic b on a.topic_id=b.topic_id \n"
          ."where b.topic_name='$page' group by b.topic_id";
    $result = mysql_query($sql);
    if (!$result){
        echo '{"error":1,"message":"'.  mysql_error().'","sql":"'.$sql.'"}';
        return;
    }
    if (mysql_num_rows($result)===0){
        echo '<h2>Комментариев ещё никто не писал</h2>';
        echo '<div class="comments-inner">';
        echo '</div>';
        return;        
    }

    $data = mysql_fetch_array($result);
    list($count,$topic_id)= $data;


    echo '<h2>Комментарии ('.$count.')</h2>';

    $sql = "select "
          ." a.user_id,a.item_id,a.comment_time,a.comment_text,"
          ." concat(u.last_name,' ',u.first_name) as user_name,a.replay_to,b.topic_name,\n "
          ."(select count(*) from topic_item where user_id=a.user_id) as message_count \n"  
          ."from topic_item a inner join topic b on b.topic_id=a.topic_id "
          ."left join users u on u.user_id=a.user_id "  
          ."where b.topic_id=$topic_id  \n"
          ."order by ifnull(a.replay_to,a.item_id),item_id"  ;

    // echo $sql;

    $result = mysql_query($sql) or die(mysql_error());

    $parse = new Parsedown();

    echo '<div class="comments-inner">';

    while ($data = mysql_fetch_array($result)){
        list($user_id,$item_id,$comment_time,$comment_text,$user_name,$replay_to,$topic_name,$message_count)=$data;

//        $comment_text = str_replace("\n",'<br>',$comment_text);
        
        $comment_text = $parse->text($comment_text);
//        echo $comment_text;

        $attr = 'data-user-id="'.$user_id.'" data-comment-id="'.$item_id.'"';        
        if (isset($replay_to)){
            $attr .= ' data-replay-to="'.$replay_to.'"';
        }

//------------------- topic item ----------------------------        
        echo '<div style="position:relative;">';
        include './message_text.php';
        echo get_item_attachment($item_id);
        echo '</div>';
//-----------------------------------------------        
    }

    echo '</div>';

}

//----------------------------------------------------------------------------
    