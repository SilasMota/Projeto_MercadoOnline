<?php

	$numproduto ="";

	if(isset($_POST["numero"])){

		$numproduto = $_POST["numero"];
		$quantidade = $_POST["quantidade"];
		$numproduto = strval($numproduto);

		if(isset($_COOKIE["produtos"]) && $quantidade >= 0){
			$produtos = $_COOKIE["produtos"];
			$produtos = json_decode($produtos,true);

			$produtos[$numproduto] = $quantidade;
			
			$produtos = json_encode($produtos);

			setcookie("produtos",$produtos, time() + (86400 * 30));

	        echo $produtos;
	    } else {
	    	echo $_COOKIE["produtos"];
	    }
	}
?>