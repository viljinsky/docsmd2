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
        include './libs/docsmd/admin-menu.php';
        ?>
        </div>
        
        
        <script>
            
                DocManager(comments,{php_path:'<?=$php_path?>',user_id:279,role_id:3});
                
                Search(searchform,searchresult,'<?=$php_path?>');
                
                Editor(adminmenu,{
                    contenttpl  : '<?=CONTENT_TPL?>',
                    page        : '<?=$page?>.md',
                    linktpl     : '<?=LINK_TPL?>',   
                    contentlink : '<?=$server_link.'/docs/'?>',
                    upload_php  : '<?=$php_path.'upload.php'?>'
                    });
                    
        </script>
        
    </body>
</html>