<?php

	// Arquivo com os dados e função de conexão
	require "conexaoMysql.php";

	class Cliente 
	{
		public $id;
		public $nome;
		public $cpf;
		public $telefone;
		public $endereco;
		public $email;
	}

	function getClientes($conn)
	{
	  $arrayClientes =null;
	  
	  $SQL = "
	    SELECT id, nome, email, cpf, enderecoresid, telefone
	    FROM Cliente
	  ";
	  
	  // Prepara a consulta
	  if (! $stmt = $conn->prepare($SQL))
	    throw new Exception("Falha na operacao prepare: " . $conn->error);
	      
	  // Executa a consulta
	  if (! $stmt->execute())
	    throw new Exception("Falha na operacao execute: " . $stmt->error);

	  // Indica as variáveis PHP que receberão os resultados
	  if (! $stmt->bind_result($id,$nome, $email, $cpf, $endereco, $telefone))
	    throw new Exception("Falha na operacao bind_result: " . $stmt->error);    
	  
	  // Navega pelas linhas do resultado
	  while ($stmt->fetch())
	  {
	    $cliente = new Cliente();
	    
	    $cliente->id = $id;
	    $cliente->nome = $nome;
	    $cliente->cpf = $cpf;
	    $cliente->telefone = $telefone;
	    $cliente->endereco = $endereco;
	    $cliente->email = $email;

	    $arrayClientes[] = $cliente;
	  }
	  
	  return $arrayClientes;
	}

	$arrayClientes = "";
	$msgErro = "";

	try
	{
	  $conn = conectaAoMySQL();
	  $arrayClientes = getClientes($conn);  
	}
	catch (Exception $e)
	{
	  $msgErro = $e->getMessage();
	}

?>

<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<title>Listagem de Clientes</title>
		<link rel="icon" type="image/jpg" href="images/carrinhobranco.png" />
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
 		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
 		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
		<link rel="stylesheet" type="text/css" href="css/estilo.css">
	</head>
	<body>
		<!-- -->


		<?php $tipo = 'admin'; include 'navbar.php';?>

		<div class="container">
			<table class="table table-responsive">
				<thead>
					<th scope="col">Nome do Cliente</th>
					<th scope="col">CPF do Cliente</th>
					<th scope="col">Telefone</th>
					<th scope="col">Endereço</th>
					<th scope="col">E-mail</th>
					<th scope="col">Ver Pedidos do Cliente</th>
					<th scope="col">Excluir</th>
				</thead>
				<tbody>
					<?php
					    if ($arrayClientes != "")
					    {
					      
					      foreach ($arrayClientes as $cliente)
					      {       
					        echo "
					        <tr>
					          <td>$cliente->nome</td>
					          <td>$cliente->cpf</td>
					          <td>$cliente->telefone</td>
					          <td>$cliente->endereco</td>
					          <td>$cliente->email</td>
					          <td><a name='comprar' class='btn btn-danger' href='admin-ver-pedidos-cliente.php?email=$cliente->email'>Pedidos</a></td>"?>
					          <td><button class="btn btn-danger" type="button" onclick='confirma(<?php echo $cliente->id ?>);' ><span class="fa fa-trash" ></span></button></td>
					          <?php
					        echo"</tr>      
					        ";
					      }
					    }
							
					?>    
				</tbody>
				
			</table>

			<p id="demo"></p>

			<script>
			function confirma(id) {
				var ids = id.toString();
				var dir = "exclui-cliente.php?Id=";
			  var r = confirm("Deseja Excluir o Cliente?");
			  if (r == true) {
			    location.href= dir.concat(ids);
			  }
			 
			}
			</script>
			
			<?php
  
			  if ($msgErro != "")
			    echo "<p class='text-danger'>A operação não pode ser realizada: $msgErro</p>";
			  
			  ?>
		</div>

		<?php include 'footer.php'; ?>	
	</body>
</html>