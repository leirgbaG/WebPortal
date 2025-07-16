<?php
include_once("usuario.php");
include_once("noticia.php");
include_once("documento.php");
include_once("peticion.php");

//  Conexión a base de datos.
$cnx = new PDO("mysql:host=127.0.0.1;dbname=portalweb_sacn2", "root", "starlight.21");
$cnx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$rutaBase = "Upload/";
$rutaMult = "Upload/Multimedia/";
$rutaDocs = "Upload/Documentos/";


?>