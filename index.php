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
        <link rel="stylesheet" href="libs/docsmd/style.css">
        <script src="libs/docsmd/script.js"></script>
        <script src="libs/docsmd/docseditor.js"></script>
    </head>
    <body>
        <?php
            session_start();
            $tmp_role= filter_input(INPUT_GET,'role_id');
            if (isset($tmp_role)){
                $_SESSION['role_id']=$tmp_role;
            }
            if (!isset($_SESSION['role_id'])){
                $_SESSION['role_id']=0;
            }
            $role_id = intval($_SESSION['role_id']);
            
            echo '<div> роле'.$role_id.'</div>';
        ?>
        
        <div id="admin">
            <a href="./?role_id=0" >гость</a>&nbsp;<a href="./?role_id=1">пользователь</a>&nbsp;<a href="./?role_id=3">админ</a>
        </div>
        
        <form id="searchform" class="search-form">
            <input name="search"  placeholder="Поиск по сайту..." required>
            <input type="submit" value="Найти" >
        </form> 
            
            
        <div id="searchresult"></div>
               
        
        <?php
        include './libs/config.php';
        include './libs/docsmd/pattern.php';
        echo page();
        ?>
        
        
        
        <div id="comments"></div>
        
        
        <div id="adminmenu">
        <?php
            if ($role_id==3){
                include './libs/docsmd/admin-menu.php';
            }
        ?>
        </div>
        
        
        <script>
            
                DocManager(comments,{php_path:'<?=$php_path?>',user_id:279,role_id:'<?=$role_id?>'});
                
                Search(searchform,searchresult,'<?=$php_path?>');
                
                Editor(adminmenu,{
                    contenttpl  : '<?=CONTENT_TPL?>',
                    page        : '<?=$page?>.md',
                    linktpl     : '<?=LINK_TPL?>',   
                    contentlink : '<?=$server_link.'/docs/'?>',
                    php_path    : '<?=$php_path?>'
                    });
                    
        </script>
        
        
    </body>
</html>