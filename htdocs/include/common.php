<?php 
session_start();

if(!isset ($_SESSION["userID"])  ||  !isset ($_SESSION["storeID"]) ||
         !isset ($_SESSION["name"])  ||  !isset ($_SESSION["aStatus"]) || $_SESSION["aStatus"] !=0){
    header("Location: index.php");
    return;
}
?>