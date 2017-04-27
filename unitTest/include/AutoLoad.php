<?php

function  __autoload($className) {  
    $filePath = dirname(__FILE__)."/{$className}.class.php";  
    require_once($filePath);  

}  
?>