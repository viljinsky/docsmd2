<?php
    
    define('CR',"\n\r");
    define('TAB',"\t");
    
    define('HOME', 'Начало');
    define('NEXT','Вперёд');
    define('PRIOR','Назад');
    define('SITE_MAP', 'sitemap');
    

    require_once  'Parsedown.php';
    
    if (!file_exists($content_path.CONTENT_TPL)):
        die($content_path.CONTENT_TPL.' - not found');
    endif;
    

    $map = getMap($content_path.CONTENT_TPL);
    
    
    function getMap($filename){
        $map = array();
        $file = fopen($filename,'r');
        $p = 0 ;
        $last = 0;
        $lastPage = null;
        $L = array();
         while ($str = fgets($file)){

             if (trim($str)==='') { continue; }

             $n = 0;
             for ($i=0;$i<strlen($str);$i++){
                 if($str[$i]!=' '){ break; }
                 $n++;
             }
             $p = $n / 4;    

             if ($p>$last){  array_push($L, $lastPage); }
             for ($i=0;$i<$last-$p;$i++){
                $lastPage = array_pop($L) ;
             }

             list($page,$title)=  explode('=', trim($str));
             $page = trim($page);
             if ($title===''){
                 $title=$page;
             } else {
                 $title=  trim($title);
             }
             if (count($L)===0){
                 $parent=null;
             } else {
                 $parent=$L[count($L)-1];
             }
             $lastPage = $page;

             $last = $p;
             $m = array('page'=>$page,'parent'=>$parent,'title'=>$title);
             $map[] = $m;
        }
        fclose($file);
        return $map;
    }
    
    
    
    function recur($map,$page,$padding=''){
        global $doc_page;
        foreach ($map as $key=>$value){
            if ($value['page']===$page){
                echo $padding.'<a href="'.$doc_page.'?page='.$page.'" title="'.$page.'">'.$value['title'].'</a><br>';
                break;
            }
        }
        foreach ($map as $key=>$value){
            if ($value['parent']===$page){
                recur($map, $value['page'],$padding."\t");
            }
        }
    }

    function messages($map){
        echo '<h1>Последние сообщения</h1>';
        $sql = "select message_id,message_text,user_id from topic_item";
//        $result = mysql_query($sql);
//        if ($result){
//            while ($data=  mysql_fetch_array($result)){
//                list($item_id,$message,$user_id) = $data;
//                echo $item_id.' '.$message.' '.$user_id;
//            }
//        }
        
        return;
    }
    
    function sitemap($map){
        echo '<h1>Карта справочника</h1><pre>';
        foreach ($map as $value){
            if (empty($value['parent'])){
                recur($map, $value['page']);
            }
        }
        echo '</pre>';
    }    
    
    function getPage($map,$page){
        foreach ($map as $value){
            if ($value['page']===$page){
                return $value;
            }
        }
    }
    
    // получает все без парента
    function getIndex($map){
        foreach ($map as $key=>$a):
            if (empty($a['parent'])):
                echo '<a href="?page='.$a['page'].'">'.$a['title'].'</a></br>';
            endif;            
        endforeach;
    }
    
    function getContent($map,$serch){
        $result = '';
        foreach ($map as $key=>$a):
            if ($a['parent']===$serch):
                $result .= '* ['.$a['title'].']['.$a['page'].']'."\n";
            endif;
        endforeach;
        if (strlen($result)>0){
            $result = CR.CR."В этой главе следующие разделы".CR.CR.$result;
        }
        return $result;
    }
    
    function pageTitle($map,$serach){
        foreach ($map as $key=>$a){
            if ($a['page']===$serach){
                return $a['title'];
            }
        }
    }
    
    function nextPage2($map,$page){
        $pageIndex = null;
        foreach ($map as $k=>$v){
            if ($v['page']===$page){
                $pageIndex=$k;
                break;
            }
        }
        $nextPage = null;
        $priorPage=null;
        if (key_exists($pageIndex-1, $map)){
            $priorPage = $map[$pageIndex-1]['page'];
        }
        if (key_exists($pageIndex+1, $map)){
            $nextPage=$map[$pageIndex+1]['page'];
        }
    
        return array('next'=>$nextPage,'prior'=>$priorPage);
    }

    
    function getNav($map,$serch){
        // ищем парент
        global $doc_page;


        $sitemap    = '<a href="'.$doc_page.'?page='.SITE_MAP.'">Карта справочника</a>';
        $messages   = '<a href="'.$doc_page.'?page=messages">Пследние сообщения</a>';
        $findform = '<div style="display:inline; position:relative;right:0;"><input name="serch" placeholder="поиск на составительрасписания..."><button>Найти</button></div>';
        
        $home       = HOME;
        $prior      = PRIOR;
        $next       = NEXT;
        $path       = '';
        
        $a = nextPage2($map, $serch);
        if ($a['prior']!==null){
                $prior = '<a href="'.$doc_page.'?page='.  $a['prior'].'">'.PRIOR.'</a>';            
        }
        if ($a['next']!==null){
                $next = '<a href="'.$doc_page.'?page='.  $a['next'].'">'.NEXT.'</a>';
        }
        
        
        if (isset($serch) && ($m1 = getPage($map, $serch))){
            
//            $home = '<a href="?page='.$m1['parent'].'">home</a>';
            
            // родственники серча
            $a=array();
            $n = 0;
            foreach ($map as $key=>$m2){
                if ($m1['parent']===$m2['parent']){
                    $a[$m2['page']]=$n++;
                }
            }

            
            $path = '';
            while (!empty($m1['parent'])){
                $m1=  getPage($map, $m1['parent']);
                $path ='<a href="'.$doc_page.'?page='.$m1['page'].'">'.$m1['title'].'</a>'.(strlen($path)===0?'':' / ').$path;
            }
        }
        
        echo '<div>'.$sitemap.'</div>'.CR;
        
        
        echo    CR.'<!-- page navigator -->'.CR
                .'<ul class="page-navigator">'
                .'<li><a href="'.$doc_page.'">Главная</a></li>'
                .'<li>'.$prior.'</li>'
                .'<li>'.$next.'</li>'
                .'<li>'.$path.'</li>'
                .'</ul>'.CR
                .'<!-- page navigator -->'.CR.CR;
        
        
        
   }
   
   function serch_form(){
//       echo '<form class="serch-form"><input name="word" placeholder="Поиск по сайту..." required><input type="submit" value="Найти" ></form>';
   }
   
   $page = filter_input(INPUT_GET,'page');
    
   /**
    * Вывод страницы документации
    * @global type $map
    * @global type $content_path
    */
   function page(){
       global $map,$content_path,$page;


    
//    echo '<h1>Руководство пользователя</h1>';
    
    getNav($map, $page);
    
    serch_form();
    
    if (!isset($page)){
        $page = DEFAULT_MD;    
    }
    
    echo '<!-- docpage body -->'.CR.CR;
    echo '<div class="docpage" data-page="'.$page.'">';
    
    if ($page===SITE_MAP){
        sitemap($map);
        
    } else if ($page==='messages'){
        messages($map);
    } else {
        $filename = $content_path.$page.'.md';
        if (!file_exists($filename)){
            echo '<div style="background:red;padding:50px;">Нстраница не найдена</div>';
//            echo '<b>'.pageTitle($map,$page).'</b><br>';
//            echo '<strong>Страница находится в разработке</strong><br>';
//            echo $filename;            
        }  else {
            $parsedown = new Parsedown();
            $text = file_get_contents($filename);
            $link = file_get_contents($content_path.'link.tpl');
            echo $parsedown->text($text."\n".$link);
        }
    }
    echo '</div>';
    echo CR.CR.'<!-- docpage body -->'.CR.CR;

   }
