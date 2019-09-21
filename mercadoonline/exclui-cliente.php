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

		$id = filtraEntrada($_GET["Id"]);
		$sql = "
			DELETE 
			FROM Cliente
			WHERE id = '$id' 
		";

		if (! $conn->query($sql))
			throw new Exception("Falha na remocao do cliente" . $conn->error);
    
		$sql2 = "
			DELETE 
			FROM Pedido
			WHERE cliente = '$id' 
		";

		if (! $conn->query($sql2))
			throw new Exception("Falha na remocao dos pedidos " . $conn->error);
    
		// Redireciona para o script mostraClientes
		header("Location: listagem-clientes.php");
	}
	catch (Exception $e)
	{
    echo "Nao foi possivel excluir o cliente: ", $e->getMessage();
	}
}
  
?>
