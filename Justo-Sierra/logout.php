<?php
session_start();
$_SESSION = array(); 
session_destroy();

// Redirige al login.php (que está en la misma carpeta raíz)
header("Location: login.php");
exit;
?>