<?php

session_start();

// if (!isset ($_SESSION["userID"])){
//     $_SESSION = [];
//     header("Location: index.php");
//     return;
// }
// else
// {
unset($_SESSION["name"]);
unset($_SESSION["aStatus"]);
unset($_SESSION["userID"]);
unset($_SESSION["storeID"]);
$_SESSION = [];
header("Location: index.php");
return;
// }
?>