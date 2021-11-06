<?php


require_once 'core/mvc_loader.php';

global $Winter_MVC;

if(empty($Winter_MVC))
{
    define( 'WINTER_MVC_PATH', dirname( __FILE__ ) );
    
    $Winter_MVC = new MVC_Loader();
    
}

?>