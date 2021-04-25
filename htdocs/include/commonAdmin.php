<?php 
session_start();

// If nouserID or admin status or if admin status = 0 meaning normal user
if(!isset ($_SESSION["userID"]) || !isset ($_SESSION["aStatus"]) || $_SESSION["aStatus"] !=1){
    header("Location: index.php");
    return;
}

?>