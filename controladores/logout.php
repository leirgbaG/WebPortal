<?php
session_start();
include_once("../clases/conexion.php");
session_destroy();
header("Location: login");
exit;
?>