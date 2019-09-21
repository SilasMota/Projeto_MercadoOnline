<?php
	session_start();
	require "verificaSessao.php";
	require "conexaoMysql.php";

	function filtraEntrada($dado)
	{
	  $dado = trim($dado);
	  $dado = stripslashes($dado);
	  $dado = htmlspecialchars($dado);
	  
	  return $dado;
	}

	function endereco($cep,$logadouro,$numero,$bairro,$cidade,$estado){
		return filtraEntrada($cep).', '.filtraEntrada($logadouro).', '.filtraEntrada($numero).', '.filtraEntrada($numero).', '.filtraEntrada($bairro).', '.filtraEntrada($cidade).', '.filtraEntrada($estado);
	}

	$msgErro = "";

	class Cliente 
	{
		public $id;
		public $nome;
		public $cpf;
		public $telefone;
		public $enderecoresid;
		public $enderecoentrega;
		public $email;
		public $senha;
		public $profissao;
		public $datanasc;

	}
	  $conn = conectaAoMySQL();

	  $arrayClientes =null;

	  $email = $_SESSION["email"];

	  $SQL = "
	    SELECT id, nome, email, cpf, enderecoresid, telefone, enderecoentrega, datanasc, senha, profissao
	    FROM Cliente
	    WHERE email = '$email'

	  ";
	  
	  // Prepara a consulta
	  if (! $stmt = $conn->prepare($SQL))
	    throw new Exception("Falha na operacao prepare: " . $conn->error);
	      
	  // Executa a consulta
	  if (! $stmt->execute())
	    throw new Exception("Falha na operacao execute: " . $stmt->error);

	  // Indica as variáveis PHP que receberão os resultados
	  if (! $stmt->bind_result($id,$nome, $email, $cpf, $enderecoresid, $telefone,$enderecoentrega,$datanasc,$senha, $profissao))
	    throw new Exception("Falha na operacao bind_result: " . $stmt->error);    
	  
	  // Navega pelas linhas do resultado
	  while ($stmt->fetch())
	  {
	    $cliente = new Cliente();
	    
	    $cliente->id = $id;
	    $cliente->nome = $nome;
	    $cliente->cpf = $cpf;
	    $cliente->telefone = $telefone;
	    $cliente->enderecoresid = $enderecoresid;
	    $cliente->email = $email;
	    $cliente->enderecoentrega = $enderecoentrega;
	    $cliente->datanasc = $datanasc;
	    $cliente->senha = $senha;
	    $cliente->profissao = $profissao;


	  }
	 
	$arrayClientes = "";
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
	      UPDATE Cliente SET email= ?, senha= ?, nome= ?, datanasc= ?, telefone= ?, profissao= ?, enderecoresid= ?, enderecoentrega = ?
	      WHERE id = $cliente->id
	      
	    ";

	    // prepara a declaração SQL (stmt é uma abreviação de statement)
	    if (! $stmt = $conn->prepare($sql))
	      throw new Exception("Falha na operacao prepare: " . $conn->error);

	    // Faz a ligação dos parâmetros em aberto com os valores.
	    if (! $stmt->bind_param("ssssssss", $email, $senha, $nome, $dataNasc, $telefone, $profissao, $enderecoresid, $enderecoentrega))
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
	      				echo "Alteração Realizada com sucesso";
	      			else 
	      				echo "Impossível Realizar Alteração".$msgErro;
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
					$('#cadastradoOK').modal('show');
				});
				</script> 
	<?php } ?>


	<!-- -->
	<?php $tipo = 'cliente'; include 'navbar.php';?>

	<div class="container">

		<form id="register-forms" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
			<p >Cadastre-se:</p>
			<div class="row">
				<div class="form-group col-md-8">
					<label for="email-register">E-mail:</label>
					<input type="email" class="form-control" id="email-register" name="email" value="<?php echo $cliente->email;?>" required>
				</div>

				<div class="form-group col-md-6">
					<label for="pwd-register">Senha:</label>
					<input type="password" name="senha" class="form-control" id="pwd-register" value="<?php echo $cliente->senha;?>" required>
				</div>

				<div class="form-group col-md-8">
					<label for="cnome">Nome:</label>
					<input type="text" class="form-control" id="cnome" name="cnome" value="<?php echo $cliente->nome;?>" required>
				</div>
							
				<div class="form-group col-md-6">
	  				<label for="cpf">CPF:</label>
			     	<input type="text" class="form-control" id="cpf" name="cpf" value="<?php echo $cliente->cpf;?>" required disabled>
			    </div>

			    <div class="form-group col-md-4">
				    <label for="nascimento">Data de nascimento:</label>
				    <input type="date" class="form-control" id="nascimento" name="nascimento" value="<?php echo $cliente->datanasc ;?>" required>
			    </div>

			    <div class="form-group col-md-6">
				    <label for="cphone">Telefone de contato:</label>
				    <input type="text" class="form-control" id="cphone" name="cphone" value="<?php echo $cliente->telefone;?>" required>
			    </div>	

			    <div class="form-group col-md-6">
				    <label for="profissao">Profissão:</label>
				    <input type="text" class="form-control" id="profissao" name="profissao" value="<?php echo $cliente->profissao;?>">
			    </div>
			</div>
			<hr>
			<p>Endereço residencial:</p>
			<div class="row">
				<div class="form-group col-md-4">
				    <label for="cep-resi">CEP:</label>
				    <input type="text" class="form-control" id="cep-resi" name="cep-resi" value="<?php echo $cliente->enderecoresid;?>" onkeyup="buscaEndereco(this.value,'logradouro-resi','bairro-resi','cidade-resi','estado-resi')" required>
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
				    <input type="text" class="form-control" id="cep-entrega" name="cep-entrega" value="<?php echo $cliente->enderecoentrega;?>" onkeyup="buscaEndereco(this.value,'logradouro-entrega','bairro-entrega','cidade-entrega','estado-entrega')" required>
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
			<button class="btn btn-danger btn-block" type="submit">Alterar dados</button>
			
		</form>

	</div>

	<script type="text/javascript">
		window.onload =function() { 
			buscaEndereco(document.getElementById("cep-resi").value,'logradouro-resi','bairro-resi','cidade-resi','estado-resi');
			buscaEndereco(document.getElementById("cep-entrega").value,'logradouro-entrega','bairro-entrega','cidade-entrega','estado-entrega');
	};
	</script>


	<?php include 'footer.php'; ?>

</body>
</html>