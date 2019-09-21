<?php
	session_start();
	require "conexaoMysql.php";

	if ($_SERVER["REQUEST_METHOD"] == "POST") 
	{
		$msgErro = '';

		$conn = conectaAoMySQL();
		$sql = "
		            SELECT email, senha FROM Cliente
		            WHERE email = ? AND senha = ?;
		        ";

        $usuario = $_POST["username"];
        $senha = $_POST["senha"];

		if (! $stmt = $conn->prepare($sql))
		      throw new Exception("Falha na operacao prepare: " . $conn->error);


		if(!$stmt->bind_param("ss",$usuario,$senha))
			throw new Exception("Falha na operacao bind_param: " . $stmt->error);

		if (!$stmt->execute())
		      throw new Exception("Falha na operacao execute: " . $stmt->error);

		$stmt->store_result();

		$stmt->bind_result($usuario,$senha);

		$stmt->fetch();

		if($stmt->num_rows == 1){
			$_SESSION["email"] = $usuario;
			$_SESSION["senha"] = $senha;
		    header('Location: cliente.php');
		} else $msgErro = "Login ou senha inválidos";
	}
?>

<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<title>Mercado Online - Login</title>
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

		<div class="modal" id="loginERRO">
	  <div class="modal-dialog">
	    <div class="modal-content">

	      <!-- Modal body -->
	      <div class="modal-body">
	      	<?php 
	      		if ($_SERVER["REQUEST_METHOD"] == "POST")
	      		  	echo "Impossível Realizar Login".$msgErro;
	      	?>
	      </div>

	      <!-- Modal footer -->
	      <div class="modal-footer">
	      	<?php 
	      		if ($_SERVER["REQUEST_METHOD"] == "POST")
	      			
	      				echo '<button type="button" class="btn btn-danger" data-dismiss="modal">OK</button>';
	      	?>
	      </div>

	    </div>
	  </div>
	</div>
	
	<?php if (($_SERVER["REQUEST_METHOD"] == "POST") && $msgErro != "") {?>
				<script> 
				$(document).ready(function(){
					$('#loginERRO').modal('show');
				});
				</script> 
	<?php } ?>

		<!-- -->
		<?php $tipo = 'publico'; include 'navbar.php';?>

		<!-- Cadastro -->
		<div id="logreg-forms" style="min-height: 720px;" >
	        <form class="form-signin" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" >
	            <h1 class="h3 mb-3 font-weight-normal" style="text-align: center"> Login do cliente: </h1>
	            <div class="form-group">
	            	<label for="inputEmail">E-mail:</label>
	            	<input type="email" id="inputEmail" class="form-control" name="username" placeholder="Ex: cliente@email.com" required="" autofocus="">
	        	</div>	
	        	<div class="form-group">
	            	<label for="inputPassword">Senha:</label>
	            	<input type="password" id="inputPassword" class="form-control" name="senha" placeholder="Senha" required="">
	            </div>
	            <button class="btn btn-danger btn-block" type="submit" ><i class="fas fa-sign-in-alt"></i> Entrar</button>
	           
	            <hr>
	            <!-- Novo cadastro  -->
	            <label for="btn-signup">Novo no Mercado Online?</label>
	            <a class="btn btn-secondary btn-block" style="color: white;" id="btn-signup" href="register.php"><i class="fas fa-user-plus"></i> Crie sua conta</a>
            </form>

                        
            
            
   		 </div>
    
    	<?php include 'footer.php'; ?>
	
	</body>
</html>