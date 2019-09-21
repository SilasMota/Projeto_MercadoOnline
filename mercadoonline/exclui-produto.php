<?php

// Inclui o arquivo com os dados e funções de conexão
require "conexaoMysql.php";

// Valida uma string removendo alguns caracteres
// especiais que poderiam ser provenientes
// de ataques do tipo HTML/CSS/JavaScript Injection
function filtraEntrada($dado) 
{
	$dado = trim($dado);               // remove espaços no inicio e no final da string
	$dado = stripslashes($dado);       // remove contra barras: "cobra d\'agua" vira "cobra d'agua"
	$dado = htmlspecialchars($dado);   // caracteres especiais do HTML (como < e >) são codificados

	return $dado;
}

 

if (isset($_GET["Id"]))
{
	try
	{
		// Função definida no arquivo conexaoMysql.php
		$conn = conectaAoMySQL();
		$foto = $_GET["Foto"];

		unlink($foto);

		$id = filtraEntrada($_GET["Id"]);
		
		$sql = "
			DELETE 
			FROM Produto
			WHERE id = '$id' 
		";

		if (! $conn->query($sql))
			throw new Exception("Falha na remocao: " . $conn->error);
    
		// Redireciona para o script mostraClientes
		header("Location: admin.php");
	}
	catch (Exception $e)
	{
    echo "Nao foi possivel excluir o cliente: ", $e->getMessage();
	}
}
  
?>
