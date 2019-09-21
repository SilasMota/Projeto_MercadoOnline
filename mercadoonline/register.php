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
		
	  // Define e inicializa as variáveis
	  $nome = $email = $senha = $dataNasc = $cpf = $enderecoresid = $enderecoentrega = $profissao = $telefone = "";
	  
	  $nome = filtraEntrada($_POST["cnome"]);     
	  $email = filtraEntrada($_POST["email"]);
	  $estadoCivil = filtraEntrada($_POST["estadoCivil"]);
	  $dataNasc = filtraEntrada($_POST["nascimento"]);
	  $senha = filtraEntrada($_POST["senha"]);
	  $cpf = filtraEntrada($_POST["cpf"]);
	  $enderecoresid = filtraEntrada($_POST["cep-resi"]);
	  $enderecoentrega = filtraEntrada($_POST["cep-entrega"]);
	  $profissao = filtraEntrada($_POST["profissao"]);
	  $telefone = filtraEntrada($_POST["cphone"]);

	  
	  try
		{    
	    // Função definida no arquivo conexaoMysql.php
	    $conn = conectaAoMySQL();

	    $sql = "
	      INSERT INTO Cliente (id, email, senha, nome, cpf, datanasc, telefone, profissao, enderecoresid, enderecoentrega)
	      VALUES (null,?, ?, ?, ?, ?, ?, ?, ?, ?);
	    ";

	    // prepara a declaração SQL (stmt é uma abreviação de statement)
	    if (! $stmt = $conn->prepare($sql))
	      throw new Exception("Falha na operacao prepare: " . $conn->error);

	    // Faz a ligação dos parâmetros em aberto com os valores.
	    if (! $stmt->bind_param("sssssssss", $email, $senha, $nome, $cpf, $dataNasc, $telefone, $profissao, $enderecoresid, $enderecoentrega))
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
<html>
<head>
	<title>Criar Cadastro</title>
		<link rel="icon" type="image/jpg" href="images/carrinhobranco.png" />
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
 		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
 		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
		<link rel="stylesheet" type="text/css" href="css/estilo.css">
		<script>

        function buscaEndereco(cep,rua,bairro,cidade,estado)
        {
            if (cep.length != 9)
                return;

            $.ajax({

                url: 'buscaEndereco.php',
                type: 'POST',
                async: true,
                dataType: 'json',
                data: {'cep': cep},

                success: function (result)
                {

                    if (result != "") {
                        document.getElementById(rua).value = result.rua;
                        document.getElementById(bairro).value = result.bairro;
                        document.getElementById(cidade).value = result.cidade;
                        document.getElementById(estado).value = result.estado;
                        
                    }
                },

                error: function (xhr, textStatus, error)
                {
                   alert(textStatus + error + xhr.responseText);
                }

            });

        }

    </script>
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
	      	<?php 
	      		if ($_SERVER["REQUEST_METHOD"] == "POST")
	      			if ($msgErro == "")
	      				echo '<a href="login.php" class="btn btn-danger">Ir para login</a>';
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
					$('#cadastradoOK').modal('show');
				});
				</script> 
	<?php } ?>


	<!-- -->
	<?php $tipo = 'publico'; include 'navbar.php';?>

	<div class="container">

		<form id="register-forms" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
			<p >Cadastre-se:</p>
			<div class="row">
				<div class="form-group col-md-8">
					<label for="email-register">E-mail:</label>
					<input type="email" class="form-control" id="email-register" name="email" placeholder="Ex: cliente@email.com" required>
				</div>

				<div class="form-group col-md-6">
					<label for="pwd-register">Senha:</label>
					<input type="password" name="senha" class="form-control" id="pwd-register" required>
				</div>

				<div class="form-group col-md-8">
					<label for="cnome">Nome:</label>
					<input type="text" class="form-control" id="cnome" name="cnome" placeholder="Ex: Silas Mota" required>
				</div>
							
				<div class="form-group col-md-6">
	  				<label for="cpf">CPF:</label>
			     	<input type="text" class="form-control" id="cpf" name="cpf" placeholder="Apenas números" required>
			    </div>

			    <div class="form-group col-md-4">
				    <label for="nascimento">Data de nascimento:</label>
				    <input type="date" class="form-control" id="nascimento" name="nascimento" required>
			    </div>

			    <div class="form-group col-md-6">
				    <label for="cphone">Telefone de contato:</label>
				    <input type="text" class="form-control" id="cphone" name="cphone" placeholder="(99) 99999-9999" required>
			    </div>	

			    <div class="form-group col-md-6">
				    <label for="profissao">Profissão:</label>
				    <input type="text" class="form-control" id="profissao" name="profissao" placeholder="Ex: Analista de Sistemas">
			    </div>
			</div>
			<hr>
			<p>Endereço residencial:</p>
			<div class="row">
				<div class="form-group col-md-4">
				    <label for="cep-resi">CEP:</label>
				    <input type="text" class="form-control" id="cep-resi" name="cep-resi" onkeyup="buscaEndereco(this.value,'logradouro-resi','bairro-resi','cidade-resi','estado-resi')" required>
			    </div>

			    <div class="form-group col-md-4">
				    <label for="logradouro-resi">Logradouro:</label>
				    <input type="text" class="form-control" id="logradouro-resi" name="logradouro-resi">
			    </div>

			    <div class="form-group col-md-4">
				    <label for="numero-resi">Número:</label>
				    <input type="text" class="form-control" id="numero-resi" name="numero-resi">
			    </div>

			    <div class="form-group col-md-4">
				    <label for="bairro-resi">Bairro:</label>
				    <input type="text" class="form-control" id="bairro-resi" name="bairro-resi">
			    </div>

			    <div class="form-group col-md-4">
				    <label for="cidade-resi">Cidade:</label>
				    <input type="text" class="form-control" id="cidade-resi" name="cidade-resi">
			    </div>

			    <div class="form-group col-md-4">
				    <label for="estado-resi">Estado:</label>
				    <input type="text" class="form-control" id="estado-resi" name="estado-resi">
			    </div>

			</div>
			<hr>
			<p>Endereço de entrega:</p>
			<div class="row">
				<div class="form-group col-md-4">
				    <label for="cep-entrega">CEP:</label>
				    <input type="text" class="form-control" id="cep-entrega" name="cep-entrega" onkeyup="buscaEndereco(this.value,'logradouro-entrega','bairro-entrega','cidade-entrega','estado-entrega')" required>
			    </div>

			    <div class="form-group col-md-4">
				    <label for="logradouro-entrega">Logradouro:</label>
				    <input type="text" class="form-control" id="logradouro-entrega" name="logradouro-entrega">
			    </div>

			    <div class="form-group col-md-4">
				    <label for="numero-entrega">Número:</label>
				    <input type="text" class="form-control" id="numero-entrega" name="numero-entrega">
			    </div>

			    <div class="form-group col-md-4">
				    <label for="bairro-entrega">Bairro:</label>
				    <input type="text" class="form-control" id="bairro-entrega" name="bairro-entrega">
			    </div>

			    <div class="form-group col-md-4">
				    <label for="cidade-entrega">Cidade:</label>
				    <input type="text" class="form-control" id="cidade-entrega" name="cidade-entrega">
			    </div>

			    <div class="form-group col-md-4">
				    <label for="estado-entrega">Estado:</label>
				    <input type="text" class="form-control" id="estado-entrega" name="estado-entrega">
			    </div>

			</div>
			<button class="btn btn-danger btn-block" type="submit">Criar cadastro</button>
			<br>
			<p class="form-group" style="text-align: center;">Já possui cadastro? <a href="login.php">Clique aqui</a></p>
		</form>

	</div>

	


	<?php include 'footer.php'; ?>

</body>
</html>