<?php
// Configuración de la base de datos
define ('DB_HOST','localhost');
define ('DB_USER','root');
define ('DB_PASS','');
define ('DB_NAME','biblio_t1');

// Nombre de la session (puede dejar este mismo)
$usuarios_sesion="pwd";

// Clase Conexion unificada
class Conexion extends mysqli{
 public $enlace;
 
 function __construct(){
   $this->enlace=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
 }
 
 function __destruct(){
   mysqli_close($this->enlace);
 }
}

// Función para formatear fechas del chat
function formatDate($date){
	return date('g:i a', strtotime($date));
}

// Función para obtener conexión del chat
function getChatConnection(){
	$host = DB_HOST; 
	$user = DB_USER;
	$pass = DB_PASS; 
	$db_name = DB_NAME; 
	
	return new mysqli($host,$user,$pass,$db_name);
}
?>


