<?php
session_start();
session_destroy();
$_SESSION['connecter']=false;
header("Location:index.php");
?>