<?php
	class Produto
	{   
	    public $id;
	    public $nome;
	    public $preco;
	    public $descricao;
	    public $foto;
	}

	$conn = conectaAoMySQL();

	function getProduto($id){	     
		$conn = conectaAoMySQL();
	     $SQL = "
			SELECT id,nome, descricao, preco, foto
			FROM Produto
			WHERE id = $id
		";

		$result = $conn->query($SQL);
		if (! $result)
			throw new Exception('Ocorreu uma falha ao gerar o relatorio de testes: ' . $conn->error);

		if ($result->num_rows > 0)
		{
			while ($row = $result->fetch_assoc())
			{
				$produto = new Produto();

				$produto->id = $row["id"];
				$produto->nome = $row["nome"];
				$produto->descricao = $row["descricao"];
				$produto->preco = $row["preco"];
				$produto->foto = $row["foto"];

			}
		}
		return $produto;

	}

	try
		{
		$email = $_SESSION["email"];
		$sql = "
				SELECT nome
				FROM Cliente
				WHERE email = '$email'
			";
			if (! $stmt = $conn->prepare($sql))
		        throw new Exception("Falha na operacao prepare: " . $conn->error);
		          
		    // Executa a consulta
		    if (! $stmt->execute())
		        throw new Exception("Falha na operacao execute: " . $stmt->error);

		    // Indica as variáveis PHP que receberão os resultados
		    if (! $stmt->bind_result($nomeCliente))
		        throw new Exception("Falha na operacao bind_result: " . $stmt->error);

		    $stmt->store_result();

			$stmt->bind_result($nomeCliente);

			$stmt->fetch();
	  	}
		catch (Exception $e)
		{
			$msgErro = $e->getMessage();
		}

?>
<nav id="navbar" class="navbar navbar-expand-lg navbar-dark bg-danger fixed-top">
		  <a id="logo" class="navbar-brand logo" href="<?php if($tipo == 'publico') echo 'index.php'; else if($tipo == 'cliente') echo 'cliente.php'; else if($tipo == 'admin') echo'admin.php'; ?>" ><span class="fa fa-shopping-cart"></span> <span>Mercado Online</span></a>

		  <!-- botão de menu -->
		  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
		    <span class="navbar-toggler-icon"></span>
		  </button>


		  <div class="collapse navbar-collapse" id="navbarSupportedContent">
		  

		    <!-- area busca -->
		    <form class="form-inline my-2 my-sm-0 busca">
		      <input class="form-control mr-sm-2 barra" type="search" placeholder="Procure aqui" aria-label="Search" name="busca"  onkeyup="buscaProduto(this.value)" style="padding: 10px; display: <?php if((($tipo == 'publico') || ($tipo == 'cliente')) && ($busca == 1)) echo 'inline-block'; else echo "none";?>;">
		    </form>

		    <!-- Botao Carrinho -->
		    <button class="btn btn-outline-light my-sm-0 fas fa-shopping-cart" style="padding: 10px; display: <?php if(($tipo == 'publico') || ($tipo == 'cliente')) echo 'inline-block'; else echo "none";?>; " type="button" data-toggle="modal" data-target="#myModal"></button>

		    <!-- autentificação publica -->
		    <div class="autentifica" style="display: <?php if($tipo == 'publico') echo 'inline-block'; else echo "none";?>">
			    <a class="btn btn-outline-light my-2 my-sm-0" href="login.php">Login</a>
			    <a class="btn btn-outline-light my-2 my-sm-0" href="register.php" >Cadastrar-se</a>
			</div>

			 <!-- autentificação cliente-->
		    <div class="autentifica" style="display: <?php if($tipo == 'cliente') echo 'inline-block'; else echo "none";?>">
			   <div class="dropdown ">
				  <button type="button" class="btn btn-outline-light dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
				    <?php echo $nomeCliente; ?>
				  </button>
				  <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg-left" aria-labelledby="dropdownMenuButton">
				    <a class="dropdown-item" href="concluir-pedido.php">Concluir Pedido em Andamento</a>
				    <a class="dropdown-item" href="pedidos-cliente.php">Meus Pedidos</a>
				    <a class="dropdown-item" href="alterar_cadastro_cliente.php">Meus Dados</a>
				    <a class="dropdown-item" href="index.php">Sair</a>
				  </div>
				</div>

			</div>

			<!-- autentificação admin-->
		    <div class="autentifica" style="display: <?php if($tipo == 'admin') echo 'inline-block'; else echo "none";?>">
			   <div class="dropdown ">
				  <button type="button" class="btn btn-outline-light dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
				    Administrador
				  </button>
				  <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg-left" aria-labelledby="dropdownMenuButton">
				    <a class="dropdown-item" href="cadastro-produto.php">Cadastrar Produto</a>
				    <a class="dropdown-item" href="listagem-pedidos.php">Lista de Pedidos</a>
				    <a class="dropdown-item" href="listagem-clientes.php">Lista de Clientes</a>
				  </div>
				</div>

			</div>
		  </div>
		</nav>

		<!-- Carrinho Modal -->
		<div class="modal" id="myModal">
		  <div class="modal-dialog modal-lg">
		    <div class="modal-content">

		      <!-- Modal Header -->
		      <div class="modal-header">
		        <h4 class="modal-title">Carrinho</h4>
		        <button type="button" class="close" data-dismiss="modal">&times;</button>
		      </div>

		      <!-- Modal body -->
		      <div class="modal-body carrinho table-responsive">
		        <table class="table">
		        	<thead>
		        		<tr>
		        			<th scope="col">Produto</th>
		        			<th scope="col">Quantidade</th>
		        			<th scope="col">Preço</th>
		        			<th scope="col">Excluir</th>
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
							        				<td class='prod-qtd'><input type='number' onchange='alteraQuantidade(".$produtoId.",this.value);' name='quantidade' class='quantidade form-control' min='1' value='". $quantidade."'></td>
													<td>
														<p>R$ $produto->preco</p>
													</td>
													 <td><button class='btn btn-danger' type='button' onclick='removeProdutoCarrinho(".$produtoId .");' ><span class='fa fa-trash' ></span></button></td>
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
		        	</tfoot>
		        </table>
		      </div>

		      <div>
			      
			      
			  </div>

		      <!-- Modal footer -->
		      <div class="modal-footer">
		        <a  class="btn btn-danger" href="concluir-pedido.php">Concluir Pedido</a>
		      </div>

		    </div>
		  </div>
		</div>

		