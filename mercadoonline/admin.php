<?php

	// Arquivo com os dados e função de conexão
	require "conexaoMysql.php";

	class Produtos
{   
    public $id;
    public $nome;
    public $preco;
    public $descricao;
    public $data;
    public $foto;
    public $quantidade;
    public $fabricante;
}

	function getClientes($conn)
	{
	  $arrayProdutos =null;
	  
	  $SQL = "
	    SELECT id,nome, descricao, preco, datains, quantidade, fabricante, foto
		FROM Produto
	  ";
	  
	  // Prepara a consulta
	  if (! $stmt = $conn->prepare($SQL))
	    throw new Exception("Falha na operacao prepare: " . $conn->error);
	      
	  // Executa a consulta
	  if (! $stmt->execute())
	    throw new Exception("Falha na operacao execute: " . $stmt->error);

	  // Indica as variáveis PHP que receberão os resultados
	  if (! $stmt->bind_result($id,$nome, $descricao,$preco , $datains, $quantidade,$fabricante,$foto))
	    throw new Exception("Falha na operacao bind_result: " . $stmt->error);    
	  
	  // Navega pelas linhas do resultado
	  while ($stmt->fetch())
	  {
	    $produto = new Produtos();
	    
	    $produto->id = $id;
	    $produto->nome = $nome;
	    $produto->preco = $preco;
	    $produto->descricao = $descricao;
	    $produto->data = $datains;
	    $produto->quantidade = $quantidade;
	    $produto->fabricante = $fabricante;
	    $produto->foto = $foto;

	    $arrayProdutos[] = $produto;
	  }
	  
	  return $arrayProdutos;
	}

	$arrayProdutos = "";
	$msgErro = "";

	try
	{
	  $conn = conectaAoMySQL();
	  $arrayProdutos = getClientes($conn);  
	}
	catch (Exception $e)
	{
	  $msgErro = $e->getMessage();
	}

?>

<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<title>Mercado Online</title>
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
					<th scope="col">Nome do Produto</th>
					<th scope="col">Descrição</th>
					<th scope="col">Quantidade</th>
					<th scope="col">Fabricante</th>
					<th scope="col">Preço</th>
					<th scope="col">Data de inserçao</th>
					<th scope="col">Excluir</th>
				</thead>
				<tbody>
					<?php
					    if ($arrayProdutos != "")
					    {
					      
					      foreach ($arrayProdutos as $produto)
					      {       
					        echo "
					        <tr>
					          <td>$produto->nome</td>
					          <td>$produto->descricao</td>
					          <td>$produto->quantidade</td>
					          <td>$produto->fabricante</td>
					          <td>R$ $produto->preco</td>
					          <td>$produto->data</td>
					          "?>
					          <td><button class="btn btn-danger" type="button" onclick='confirma(<?php echo $produto->id.','.'"'.$produto->foto.'"'; ?>);' ><span class="fa fa-trash" ></span></button></td>
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
			function confirma(id,foto) {
				var fotost = "&Foto="+foto.toString();
				var ids = id.toString();
				var dir = "exclui-produto.php?Id=";
			  var r = confirm("Deseja Excluir o Produto?");
			  if (r == true) {
			    location.href= dir+ids+fotost;
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