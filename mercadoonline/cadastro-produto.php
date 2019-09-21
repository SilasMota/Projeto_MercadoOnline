<?php
	require "conexaoMysql.php";
	function filtraEntrada($dado)
	{
	  $dado = trim($dado);
	  $dado = stripslashes($dado);
	  $dado = htmlspecialchars($dado);
	  
	  return $dado;
	}

	$msgErro = "";

	if ($_SERVER["REQUEST_METHOD"] == "POST") 
	{
		$formatosPermitidos = array('jpg','jpeg','png','gif');
		$extensao = pathinfo($_FILES['foto']['name'],PATHINFO_EXTENSION);
		if(in_array($extensao, $formatosPermitidos)){
			$pasta = "images/";
			$novoNome = uniqid().".$extensao";
			$caminho =  $pasta.$novoNome;
			
			move_uploaded_file($_FILES['foto']['tmp_name'], $caminho);
		}

		$nome = $fabricante = $descricao = $preco = $quantidade = '';

		$nome = filtraEntrada($_POST["nome-id"]);
		$fabricante = filtraEntrada($_POST["fabricante"]);
		$preco = filtraEntrada($_POST["preco"]);
		$quantidade = filtraEntrada($_POST["quantidade"]);
		$descricao = filtraEntrada($_POST["descricao"]);
		$foto = $caminho;

		try
			{    
		    // Função definida no arquivo conexaoMysql.php
		    $conn = conectaAoMySQL();

		    $data = date('Y-m-d');

		    $sql = "
		      INSERT INTO Produto (id,nome, descricao, fabricante, preco, quantidade, foto,datains)
		      VALUES (null,?, ?, ?, ?, ?, ?,'$data');
		    ";

		    // prepara a declaração SQL (stmt é uma abreviação de statement)
		    if (! $stmt = $conn->prepare($sql))
		      throw new Exception("Falha na operacao prepare: " . $conn->error);

		    // Faz a ligação dos parâmetros em aberto com os valores.
		    if (! $stmt->bind_param("sssdis", $nome, $descricao, $fabricante, $preco, $quantidade, $foto))
		      throw new Exception("Falha na operacao bind_param: " . $stmt->error);
		        
		    if (! $stmt->execute())
		      throw new Exception("Falha na operacao execute: " . $stmt->error);
		    
		    $formProcSucesso = true;
		  }
		catch (Exception $e)
		{
			$msgErro = $e->getMessage();
		}
	}
	
?>


<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<title>Cadastro de Produtos</title>
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
		<div class="modal" id="cadastradoOK">
	  <div class="modal-dialog">
	    <div class="modal-content">

	      <!-- Modal body -->
	      <div class="modal-body">
	      	<?php 
	      		if ($_SERVER["REQUEST_METHOD"] == "POST")
	      			if ($msgErro == "")
	      				echo "Cadastro Realizado com sucesso";
	      			else 
	      				echo "Impossível Realizar Cadastro".$msgErro;
	      	?>
	      </div>

	      <!-- Modal footer -->
	      <div class="modal-footer">
	      	<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
	      </div>
	    </div>
	  </div>
	</div>

	<?php if ($_SERVER["REQUEST_METHOD"] == "POST") {?>
				<script> 
				$(document).ready(function(){
					$('#cadastradoOK').modal('show');
				});
				</script> 
	<?php } ?>
		<!-- -->


		<?php $tipo = 'admin'; include 'navbar.php';?>

		<div class="container">
			<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data">
				<div class="form-gorup form-row">
					<label for="nome-id">Nome do Produto</label>
					<input type="text" class="form-control" name="nome-id" id="nome-id">
				</div>

				<div class="fabricante">
					<label for="nome-id">Fabricante</label>
					<input type="text" class="form-control" name="fabricante" id="fabricante">
				</div>

				<div class="form-group">
				    <label for="descricao">Descrição do Produto</label>
					<textarea class="form-control" id="descricao" name="descricao" rows="4"></textarea>
				</div>

				<div class="preco">
					<label for="nome-id">Preço</label>
					<input type="text" class="form-control" name="preco" id="preco">
				</div>

				<div class="quantidade">
					<label for="nome-id">Quantidade</label>
					<input type="number" class="form-control" name="quantidade" id="quantidade">
				</div>

				<div class="form-group">
			    	<label for="foto">Foto do Produto</label>
			    	<input type="file" class="form-control-file" name="foto" id="foto">
			  	</div>

			  	<button type="submit" class="btn btn-danger w-100">Cadastrar</button>

			</form>
		</div>

		<?php include 'footer.php'; ?>	
	</body>
</html>