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
        
        
        <style>
            body,html {height: 100%;margin: 0;padding: 0;}           
            .page-cover {min-height: 100%;}
            header,footer{background: #69c;}
            .hfooter{height: 100px;}
            header{padding: 20px;}
            footer{position: relative; height: 100px; margin-top: -100px;}
        </style>
    </head>
    <body>
        <div class="page-cover">
            <header>
            <?php
                session_start();
                $tmp_user = filter_input(INPUT_GET,'user_id');
                if (isset($tmp_user)){
                    $_SESSION['user_id']=$tmp_user;
                    switch ($tmp_user){
                        case 276: $_SESSION['role_id'] = 3; break;
                        case 277: $_SESSION['role_id'] = 1; break;
                        case 278: $_SESSION['role_id'] = 1; break;
                        case 279: $_SESSION['role_id'] = 1; break;
                        default : $_SESSION['role_id'] = -1;
                    }
                }
                if (!isset($_SESSION['user_id'])){
                    $_SESSION['user_id']=0;
                    $_SESSION['role_id']=0;
                }
                $user_id = intval($_SESSION['user_id']);
                $role_id = intval($_SESSION['role_id']);

                echo '<div>user_id: '.$user_id.' role_id: '.$role_id.'</div>';
            ?>

            <div id="admin">
                <a href="./?user_id=276" >admin</a>&nbsp;
                <a href="./?user_id=277">Иванов</a>&nbsp;
                <a href="./?user_id=278">Петрович</a>&nbsp;
                <a href="./?user_id=279">Сидоров</a>&nbsp;
                <a href="./?user_id=-1">no user</a>
            </div>

                
            </header>    
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



            <div id="comments" class="comments"></div>
            <div class="hfooter"></div>
        
        </div>
        
        <footer>
           2016 &copy; Ильинский В.В.
        <div id="adminmenu">
        <?php
            if ($role_id==3){
                include './libs/docsmd/admin-menu.php';
            }
        ?>
        </div>
        
        </footer>     
        
        <script>
            
                DocManager(comments,{
                    php_path:'<?=$php_path?>',
                    user_id:  <?=$user_id?>,
                    role_id:  <?=$role_id?>
                });
                
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