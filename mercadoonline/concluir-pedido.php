<?php
session_start();
require "verificaSessao.php";
require "conexaoMysql.php";
$conn = conectaAoMySQL();



	$msgErro = "";
	$conn = conectaAoMySQL();
		try
		{
		$email = $_SESSION["email"];
		$sql = "
				SELECT id, enderecoentrega
				FROM Cliente
				WHERE email = '$email'
			";
			if (! $stmt = $conn->prepare($sql))
		        throw new Exception("Falha na operacao prepare: " . $conn->error);
		          
		    // Executa a consulta
		    if (! $stmt->execute())
		        throw new Exception("Falha na operacao execute: " . $stmt->error);

		    // Indica as variáveis PHP que receberão os resultados
		    if (! $stmt->bind_result($idCliente,$enderecoentrega))
		        throw new Exception("Falha na operacao bind_result: " . $stmt->error);

		    $stmt->store_result();

			$stmt->bind_result($idCliente,$enderecoentrega);

			$stmt->fetch();
	  	}
		catch (Exception $e)
		{
			$msgErro = $e->getMessage();
		}

		//Criação do pedido
if (($_SERVER["REQUEST_METHOD"] == "POST") && isset($_COOKIE["produtos"])) {
		try
		{
			
			$op = $_POST["opcoesPagamento"];
			$data = date('Y-m-d');

			$sql = "
				INSERT INTO Pedido(npedido, datapedido, formapagemento,cliente)
				VALUES (null, '$data' , '$op', $idCliente)
			";

			if(! $conn->query($sql))
				throw new Exception("Falha na inserção do pedido: " . $conn->error);
		}
		catch (Exception $e)
		{
			$msgErro = $e->getMessage();
		}

		try
		{
		$sql = "
				SELECT max(npedido)
				FROM Pedido 
			";
			if (! $stmt = $conn->prepare($sql))
		        throw new Exception("Falha na operacao prepare: " . $conn->error);
		          
		    // Executa a consulta
		    if (! $stmt->execute())
		        throw new Exception("Falha na operacao execute: " . $stmt->error);

		    // Indica as variáveis PHP que receberão os resultados
		    if (! $stmt->bind_result($idPedido))
		        throw new Exception("Falha na operacao bind_result: " . $stmt->error);

		    $stmt->store_result();

			$stmt->bind_result($idPedido);

			$stmt->fetch();
	  	}
		catch (Exception $e)
		{
			$msgErro = $e->getMessage();
		}

		
		//Ligando Produto ao pedido criado

		$idProduto = '';
		
		try
		{
		$cookies = $_COOKIE["produtos"];
		$aCookies = json_decode($cookies,true);
		if($cookies != ''){
			
			foreach ($aCookies as $produtoId => $quantidade) {
				$sql = "
			    	INSERT INTO Contem (pedido,produto,quantidade)
			    	VALUES ($idPedido,$produtoId,'$quantidade')
			    ";
				if(! $conn->query($sql))

				throw new Exception("Falha na alteração de dados: " . $conn->error);
			}
		}
	}
		catch (Exception $e)
		{
			$msgErro = $e->getMessage();
		}

		setcookie("produtos", null, -1);
	} else if($_SERVER["REQUEST_METHOD"] == "POST") 
				$msgErro = "Não há produtos no carrinho";

	//Tratando o endereco de entrega
		class Endereco
		{
		    public $rua;
		    public $numero;
		    public $bairro;
		    public $estado;
		}

		try
		{
		    
		    $conn = conectaAoMySQL();

		    $endereco = "";
		    $cep = "";

		    $SQL = "
				SELECT rua, bairro, cidade, estado
				FROM Endereco
				WHERE cep = '$enderecoentrega'
			";

		    if (!$result = $conn->query($SQL))
		        throw new Exception('Ocorreu uma falha ao buscar o endereco: ' . $conn->error);

		    if ($result->num_rows > 0)
		    {
		        $row = $result->fetch_assoc();

		        $endereco = new Endereco();

		        $endereco->rua = $row["rua"];
		        $endereco->bairro = $row["bairro"];
		        $endereco->cidade = $row["cidade"];
		        $endereco->estado = $row["estado"];
		    }
		}
		catch (Exception $e)
		{
		    $msgErro = $e->getMessage();
		}

	

?>

<!DOCTYPE html>
<html lang="pt-br">
	<head>
        <title>Concluir Pedido</title>
		<link rel="icon" type="image/jpg" href="images/carrinhobranco.png" />
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
 		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
 		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
		<link rel="stylesheet" type="text/css" href="css/estilo.css">
		<script src="carrinho.js"></script>

	</head>
	<body>
		<!--Modal alert -->
		<div class="modal" id="pedidoOK">
		  <div class="modal-dialog">
		    <div class="modal-content">

		      <!-- Modal body -->
		      <div class="modal-body">
		      	<?php 
		      		if ($_SERVER["REQUEST_METHOD"] == "POST")
		      			if ($msgErro == "")
		      				echo "Pedido Realizado com sucesso";
		      			else 
		      				echo "Impossível Realizar Pedido: ".$msgErro;
		      	?>
		      </div>

		      <!-- Modal footer -->
		      <div class="modal-footer">
		      	<?php 
		      		if ($_SERVER["REQUEST_METHOD"] == "POST")
		      			if ($msgErro == "")
		      				echo '<a href="cliente.php" class="btn btn-danger">OK</a>';
		      			else 
		      				echo '<button type="button" class="btn btn-danger" data-dismiss="modal">OK</button>';
		      	?>
			      </div>

			    </div>
			  </div>
			</div>

			<?php if ($_SERVER["REQUEST_METHOD"] == "POST") {?>
					<script> 
					$(document).ready(function(){
						$('#pedidoOK').modal('show');
					});
					</script> 
		<?php } ?>

		<?php $tipo = 'cliente'; include 'navbar.php';?>

		<div class="container">
				 <table class="carrinho table">
		        	<thead>
		        		<tr>
		        			<th scope="col">Produto</th>
		        			<th scope="col">Preço</th>
		        		</tr>
		        	</thead>
		        	<tbody id="produtosCarrinho">
	        			<?php
	        				$cookies = $_COOKIE["produtos"];
	        				$aCookies = json_decode($cookies,true);
	        				$total = 0;
	        				if($cookies != ''){
	        					foreach ($aCookies as $produtoId => $quantidade) {

	        						if($produtoId !=""){
		        						$produto = getProduto($produtoId);

		        						echo "
		        								<tr>
			        								<td class='prod-info'>
							        					<img src= $produto->foto>
							        					<h5>$produto->nome</h5>
														<p>$produto->descricao</p>
							        				</td>
							        				<td class='prod-qtd'><input type='number' name='quantidade' class='quantidade form-control' min='1' value='". $quantidade."'></td>
													<td>
														<p>R$ $produto->preco</p>
													</td>
												</tr>
		        						";
		        						$total = $total + ($produto->preco * $quantidade);
		        					}
		        				}
		        			}
	        				
	        			?>
		        	</tbody>
		        	
		        	<tfoot>
		        		<td></td>
		        		<td style="float: right;"><h3>Total:</h3></td>
		        		<td><h3>R$<?php echo "<span id='valorTotal'>$total</span>" ?></h3></td>

		        		<tr>
		        			<th scope="row">Endereço de Entrega</th>
		        			<td><?php echo "$endereco->rua $endereco->bairro $endereco->cidade $endereco->estado"; ?></td>
		        		</tr>
		        	</tfoot>
		        </table>
		        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
			        <div style="margin: 30px;">
			      	<div class="form-check form-check-inline">
					  <input class="form-check-input" type="radio" name="opcoesPagamento" id="credito" value="Cartão de crédito" required>
					  <label class="form-check-label" for="Cartao de Credito">Cartão de Crédito</label>
					</div>

					<div class="form-check form-check-inline">
					  <input class="form-check-input" type="radio" name="opcoesPagamento" id="debito" value="Debito em conta" required>
					  <label class="form-check-label" for="Debito em Conta">Débito em Conta</label>
					</div>

					<div class="form-check form-check-inline">
					  <input class="form-check-input" type="radio" name="opcoesPagamento" id="boleto" value="Boleto" required>
					  <label class="form-check-label" for="Boleto">Boleto</label>
					</div>

			      </div>
				
		      	<button <?php  ?>type="submit" class="btn btn-danger w-100">Concluir Pedido</button>
		      <form>
		</div>
	
	</body>
</html>