<?php
	session_start();
	require "verificaSessao.php";
	require "conexaoMysql.php";

	class Pedido
	{
 		public $npedido;
		public $datapedido;
		public $formapagemento;
		public $cliente;
	}

	function contaProdutos($numProduto){
		$conn = conectaAoMySQL();
		$sql = "
			SELECT COUNT(*)
			FROM Contem
			WHERE pedido =  $numProduto
			GROUP BY pedido";

			if (! $stmt = $conn->prepare($sql))
		        throw new Exception("Falha na operacao prepare: " . $conn->error);
		          
		    // Executa a consulta
		    if (! $stmt->execute())
		        throw new Exception("Falha na operacao execute: " . $stmt->error);

		    // Indica as variáveis PHP que receberão os resultados
		    if (! $stmt->bind_result($quantidade))
		        throw new Exception("Falha na operacao bind_result: " . $stmt->error);

		    $stmt->store_result();

			$stmt->bind_result($quantidade);

			$stmt->fetch();

			return $quantidade;

	}

	function calculaTotal($numPedido){
		$conn = conectaAoMySQL();
		$sql = "
			SELECT SUM(Contem.quantidade * Produto.preco) FROM Produto, Contem WHERE Contem.pedido = $numPedido AND Contem.produto = Produto.id";

			if (! $stmt = $conn->prepare($sql))
		        throw new Exception("Falha na operacao prepare: " . $conn->error);
		          
		    // Executa a consulta
		    if (! $stmt->execute())
		        throw new Exception("Falha na operacao execute: " . $stmt->error);

		    // Indica as variáveis PHP que receberão os resultados
		    if (! $stmt->bind_result($total))
		        throw new Exception("Falha na operacao bind_result: " . $stmt->error);

		    $stmt->store_result();

			$stmt->bind_result($total);

			$stmt->fetch();

			return $total;

	}

	function getPedidos($conn)
	{
		$arrayPedidos = null;

		$email = $_SESSION["email"];
		$sql = "
				SELECT id
				FROM Cliente
				WHERE email = '$email'
			";
			if (! $stmt = $conn->prepare($sql))
		        throw new Exception("Falha na operacao prepare: " . $conn->error);
		          
		    // Executa a consulta
		    if (! $stmt->execute())
		        throw new Exception("Falha na operacao execute: " . $stmt->error);

		    // Indica as variáveis PHP que receberão os resultados
		    if (! $stmt->bind_result($idCliente))
		        throw new Exception("Falha na operacao bind_result: " . $stmt->error);

		    $stmt->store_result();

			$stmt->bind_result($idCliente);

			$stmt->fetch();
 
		 

		$SQL = "
			SELECT npedido, datapedido, formapagemento, cliente
			FROM Pedido 
			WHERE cliente = '$idCliente'
		";
	

		if(! $stmt = $conn->prepare($SQL))
			throw new Exception("Falha na operacao prepare: " . $conn->error);


		  if (! $stmt->execute())
		    throw new Exception("Falha na operacao execute: " . $stmt->error);


		  if (! $stmt->bind_result($npedido,$datapedido, $formapagemento, $cliente))
		    throw new Exception("Falha na operacao bind_result: " . $stmt->error); 

		while ($stmt->fetch()) 
		{
			$pedido = new Pedido();

			$pedido->npedido = $npedido;
			$pedido->datapedido = $datapedido;
			$pedido->formapagemento = $formapagemento;
			$pedido->cliente = $cliente;

			$arrayPedidos[] = $pedido;
		}

		return $arrayPedidos;
	}

	$arrayPedidos = "";
	$msgErro = "";

	try
	{
		$conn = conectaAoMySQL();
		$arrayPedidos = getPedidos($conn);
	}
	
	catch (Exception $e)
	{
	  $msgErro = $e->getMessage();
	}
?>

<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<title>Listagem de Pedidos</title>
		<link rel="icon" type="image/jpg" href="images/carrinhobranco.png" />
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
 		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
 		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
		<link rel="stylesheet" type="text/css" href="css/estilo.css">
		<script src="recuperaPedidos.js"></script>
	</head>
	<body>
		<!-- -->


		<?php $tipo = 'cliente'; include 'navbar.php';?>


		<!-- Pedido Modal -->
		<div class="modal" id="pedidoMod">
		  <div class="modal-dialog modal-lg">
		    <div class="modal-content">

		      <!-- Modal Header -->
		      <div class="modal-header">
		        <h4 class="modal-title">Pedido</h4>
		        <button type="button" class="close" data-dismiss="modal">&times;</button>
		      </div>

		      <!-- Modal body -->
		      <div class="modal-body carrinho">
		        <table class="table">
		        	<thead>
		        		<tr>
		        			<th scope="col">Produto</th>
		        			<th scope="col">Quantidade</th>
		        			<th scope="col">Preço</th>
		        		</tr>
		        	</thead>
		        		<tbody id="detalhesProdutos">
		        			<tr>
	        				<td class="prod-info">
	        					
	        				</td>
	        				<td class="prod-qtd"></td>

							<td>
								<p></p>
							</td>
	        			</tr>
		        	</tbody>
		        	
		        </table>
		        
		      </div>

		      <!-- Modal footer -->
		      <div class="modal-footer">
		        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
		      </div>

		    </div>
		  </div>
		</div>

		<div class="container">
			<table class="table table-responsive">
				<thead>
					<th scope="col">Número do Pedido</th>
					<th scope="col">Data do Pedido</th>
					<th scope="col">Quantidade de Itens do Pedido</th>
					<th scope="col">Valor Total do Pedido</th>
					<th scope="col">Forma de Pagamento</th>
					<th scope="col">Ver Detalhes do Pedido</th>
				</thead>
				<tbody>
					<?php
						if($arrayPedidos != "")
						{
							foreach($arrayPedidos as $pedido)
							{	
								$quantidade = contaProdutos($pedido->npedido);
								$total = calculaTotal($pedido->npedido);
								echo"
								<tr>
									<td>$pedido->npedido</td>
									<td>$pedido->datapedido</td>
									<td> $quantidade</td>
									<td>R$ $total </td>
									<td>$pedido->formapagemento</td>
									<td><button type='button' name='comprar' class='btn btn-danger' type='button' data-toggle='modal' data-target='#pedidoMod' onclick='buscaPedido($pedido->npedido)'> Detalhes</button></td>
									
								</tr>
								";
							}
						} 
					?>
				</tbody>
				
			</table>
		</div>

		<?php include 'footer.php'; ?>
	</body>
</html>