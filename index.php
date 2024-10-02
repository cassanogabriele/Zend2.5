<a href="http://icyber-corp.gabriel-cassano.be/" style="display:none;">ICYBER-CORP.</a>
<a href="http://homesweethomedesign.gabriel-cassano.be/" style="display:none;">Home Sweet Home Design</a>
<a href="http://invokingdemons.gabriel-cassano.be/" style="display:none;">invoking demons</a>

<?php 
// Display all errors when APPLICATION_ENV is development.
/*
 * if ($_SERVER['APPLICATION_ENV'] == 'development') {*/
    //error_reporting(E_ALL);
    //ini_set("display_errors", 1);
/*}
*/
/* This makes our life easier when dealing with paths. Everything is relative to the application 
   root now.
*/
chdir(dirname(__DIR__).'/web/');

// Decline static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server' && is_file(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))) {
    return false;
}

// Setup autoloading
require 'init_autoloader.php';

// Run the application!
Zend\Mvc\Application::init(require 'config/application.config.php')->run();
function redirect($filename) {
    if (!headers_sent())
        header('Location: '.$filename);
    else {
        echo '<script type="text/javascript">';
        echo 'window.location.href="'.$filename.'";';
        echo '</script>';
        echo '<noscript>';
        echo '<meta http-equiv="refresh" content="0;url='.$filename.'" />';
        echo '</noscript>';
    }
}
redirect('http://mycdcollection.gabriel-cassano.be/public/');












