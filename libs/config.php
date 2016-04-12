<?php

//------------------------------------------------------------------------------
//
//                  Настройки сервера базы данных
//
//------------------------------------------------------------------------------


define('HOST', 'localhost');
define('USER', 'test3');
define('PASSWORD', 'test3');
define('DATABASE','docsmd');


/** Ссылка на сайт */
$server_link='//localhost/docsmd2/';

/** Путь сервера к папке сайта */
$server_path = "d:\\development\\web\php\\docsmd2\\" ;

//------------------------------------------------------------------------------
//
//                           Настройки документации
//                           
//                server_link
//                    |
//                    |--docs
//                    |    |
//                    |    +----images
//                    |    |
//                    |    +----tmp
//                    |    |
//                    |    +----index.md
//                    |    +----content.tpl
//                    |    +----link.tpl
//                    |    |
//                    |    +----*.md  
//                    |    +----*.md  
//                    |
//                    +--libs
//                    |    |
//                    |    +--connect.php
//                    |    |
//                    |    +--config.php
//                    |    |
//                    |    +--docsmd
//                    |    |    |
//                    |    |    +---- *.php
//                    |    |    +---- *.php
//                    |    |
//                    |    +--auth
//                    |    |    |
//                    |    |    +---- *.php
//                    |    |    +---- *.php
//                    |
//                    +---index.php             
//                           
//------------------------------------------------------------------------------

/**Путь к библиотеке документации*/ 
$php_path = $server_link.'libs/docsmd/';


/** Ссылка на папку содержимого документации */
define('CONTENT_PATH', $server_path.'docs'.DIRECTORY_SEPARATOR);

/** путь к процессору документации - pattern.php*/
define('DOC_PAGE','./index.php');

/** Путь к илюстрациям документации */
$image_path = CONTENT_PATH.'images'.DIRECTORY_SEPARATOR;



/** Имя файла для хранения линков */
define('LINK_TPL', 'link.tpl');

/** Имя файла для храннеия контекста документации */
define('CONTENT_TPL','content.tpl' );

/** Имя страницы документации по умолчанию */
define('DEFAULT_MD','index');


/** адресс, прописываемый в link.tpl для иллюстраций */
//$image_path_link =  $server_link.'help/images/';
$image_path_link = './docs/images/';

/** Путь для размещения скриншортов пользователей */
$screenshort_path = $server_path
                    .'docs'.DIRECTORY_SEPARATOR
                    .'tmp'.DIRECTORY_SEPARATOR;

/** Путь для размещения имиджей пользователей */
$screenshort_link = './docs/tmp/';