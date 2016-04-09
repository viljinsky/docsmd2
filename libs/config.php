<?php

# Настройки сервера базы данных

define('HOST', 'localhost');
define('USER', 'test3');
define('PASSWORD', 'test3');
define('DATABASE','docsmd');

# Настройки документации


/** Ссылка на сайт */
$server_link='//localhost/docsmd2/';

/**Путь к библиотеке документации*/ 
$php_path = $server_link.'libs/docsmd/';

/** Путь сервера к папке сайта */
//$server_path=dirname(__FILE__).DIRECTORY_SEPARATOR;
//$server_path="D:\\development\\web\php\\parsetest\\";
$server_path = "d:\\development\\web\php\\docsmd2\\" ;//$_SERVER['DOCUMENT_ROOT'].'docsmd'.DIRECTORY_SEPARATOR;


/** Ссылка на папку содержимого документации */
$content_path = $server_path.'docs'.DIRECTORY_SEPARATOR;
/** Ссылка на папку содержимого документации */
define('CONTENT_PATH', $content_path);

/** путь к процессору документации - pattern.php*/
//$doc_page =$server_link.'index.php';
$doc_page = './index.php';

define('DOC_PAGE','./index.php');

/** Путь к илюстрациям документации */
$image_path = $content_path.'images'.DIRECTORY_SEPARATOR;



/** Имя файла для хранения линков */
define('LINK_TPL', 'link.tpl');

/** Имя файла для храннеия контекста документации */
define('CONTENT_TPL','content2.tpl' );

/** Имя страницы документации по умолчанию */
define('DEFAULT_MD','index');

/** Путь к библиотеки идентификации */
$auth_path = $server_link.'libs/auth/';

/** адресс, прописываемый в link.tpl для иллюстраций */
//$image_path_link =  $server_link.'help/images/';
$image_path_link = './docs/images/';

/** Путь для размещения скриншортов пользователей */
$screenshort_path = $server_path
                    .'docs'.DIRECTORY_SEPARATOR
                    .'tmp'.DIRECTORY_SEPARATOR;

/** Путь для размещения имиджей пользователей */
$screenshort_link = './docs/tmp/';






