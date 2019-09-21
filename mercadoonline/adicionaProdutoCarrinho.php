<?php

	$numproduto ="";

	if(isset($_POST["numero"])){

		$numproduto = $_POST["numero"];
		$numproduto = strval($numproduto);

		if(isset($_COOKIE["produtos"])){
			$produtos = $_COOKIE["produtos"];
			$produtos = json_decode($produtos,true);

			if(array_key_exists($numproduto, $produtos)){
				$produtos[$numproduto] = $produtos[$numproduto] + 1; 
			} else {
				$produtos[$numproduto] = 1;
			}
			$produtos = json_encode($produtos);

			setcookie("produtos",$produtos, time() + (86400 * 30));

	        echo $produtos;
	    } else{ 
	    	$produtos = array($numproduto => 1);
	    	$produtos = json_encode($produtos);
	    	setcookie("produtos",$produtos, time() + (86400 * 30));
	    	echo $produtos;
	    }
	}
?>