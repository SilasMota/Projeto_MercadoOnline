<?php
	require "conexaoMysql.php";
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
		<script src="carrinho.js"></script>
		<script src="js/recuperaProdutos.js"></script>
	</head>
	<body>

		<!-- -->


		<?php $tipo = 'publico'; $busca = 1; include 'navbar.php';?>

		<div id="container" class="container">
			<div id="destaques" class="destaques"></div>
			<div id="lista" class="row"></div>
		</div>

		<?php include 'footer.php'; ?>
		
	</body>
</html>