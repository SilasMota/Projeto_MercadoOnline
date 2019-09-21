<?php

	$numproduto ="";

	if(isset($_POST["numero"])){

		$numproduto = $_POST["numero"];
		$numproduto = strval($numproduto);

		if(isset($_COOKIE["produtos"])){
			$produtos = $_COOKIE["produtos"];
			$produtos = json_decode($produtos,true);

			unset($produtos[$numproduto]);

			if(!empty($produtos)){
				$produtos = json_encode($produtos);
			 	setcookie("produtos",$produtos, time() + (86400 * 30));
			 }else{
			 	setcookie("produtos", null, -1);
			 	$produtos = json_encode($produtos);
			 }

	        echo $produtos;
	    } 
	}
?>