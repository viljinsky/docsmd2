<?php
// include_once '../connect.php';
include_once './Parsedown.php';

$parse = new Parsedown();

/**
 * Получение единицы сообщений по $item_id
 */

function get_message($item_id){
    global $parse;

    $sql = "select a.user_id,a.topic_id,a.comment_time,a.comment_text,"
      ."concat(u.last_name,' ',u.first_name),a.replay_to,'TOPIC_NAME',"
      ."(select count(*) from topic_item where user_id=a.user_id) "
      ."from topic_item a inner join users u on a.user_id=u.user_id\n"
      ."  where a.item_id=$item_id";
    $result=mysql_query($sql);
    if (!$result):
        echo $sql.' '.mysql_error();
        return;
    endif;

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

}

get_message($item_id);
