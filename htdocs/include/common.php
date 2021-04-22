<?php 
/***
to auto-load class definitions from PHP files
***/
// spl_autoload_register(function($class) {
//     $path = "./" . $class . ".php";
//     require_once $path; 
// });

session_start();

if(!isset ($_SESSION["userID"])|| !isset ($_SESSION["aStatus"]) || $_SESSION["aStatus"] !=0){
    header("Location: index.php");
    return;
}
?>