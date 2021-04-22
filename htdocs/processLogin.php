<?php
session_start();

$name    = $_POST["name"];
$isAdmin = $_POST["aStatus"];
$userID  = $_POST["userID"];

if($isAdmin == 1){
    $_SESSION["name"]    = $name;
    $_SESSION["aStatus"] = $isAdmin;
    $_SESSION["userID"]  = $userID;
    header("Location: uraDashboard.php");
    return;
}

$_SESSION["name"]    = $name;
$_SESSION["storeID"] = $_POST["storeID"];
$_SESSION["aStatus"] = $isAdmin;
$_SESSION["userID"]  = $userID;
header("Location: dashboard.php");
return;
?>