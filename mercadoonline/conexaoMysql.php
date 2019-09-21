<?php

define("HOST", "fdb20.awardspace.net"); 
define("USER", "3058801_mercadoonline");
define("PASSWORD", "trabalhos18!"); 
define("DATABASE", "3058801_mercadoonline");

function conectaAoMySQL()
{
	$conn = new mysqli(HOST, USER, PASSWORD, DATABASE);
	if ($conn->connect_error)
	throw new Exception('Falha na conexão com o MySQL: ' . $conn->connect_error);

	return $conn;   
}

?>