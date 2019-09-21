

function buscaProduto(busca)
{		
	if(busca.length == 0){
		document.getElementById("lista").innerHTML = "";
		document.getElementById("destaques").innerHTML = "<h3 class='row'>Novidades</h3>";
		document.getElementById("destaques").style.borderBottom = "1px solid lightgrey";
	}else {
		document.getElementById("destaques").innerHTML = "";
		document.getElementById("destaques").style.border = "none";
	}

            $.ajax({

            url: 'buscaProduto.php',
            type: 'POST',
            async: true,
            dataType: 'json',
            data: {'busca': busca},

            success: function (result)
            {                   
                if (result != "") {
                	document.getElementById("lista").innerHTML = "";
                	i = 0;
                	tipo = "";
             			for(produto in result){
             				if(busca.length == 0 && i < 3){
             					tipo = "destaques"
             					i = i +1;
             				} else {
             					tipo = "lista"
             				}
                      	document.getElementById(tipo).innerHTML = document.getElementById(tipo).innerHTML + " <div class='prod'><div class='corpo-prod'><img src='"  + result[produto].foto + "' class='rounded img-fluid'><div class='decricao-prod'><h5>"  + result[produto].nome + "</h5><p> "  + result[produto].descricao + "</p><p>R$ "  + result[produto].preco + "</p></div></div><button type='button' name='comprar' class='btn btn-danger btn-block' type='button' onclick='adcionaProdutoCarrinho("+result[produto].id+");'> Comprar </button></div>";
             			}  
              }
            },

            error: function (xhr, textStatus, error)
            {n
                
                alert(textStatus + error + xhr.responseText);
            }

        });

    }

window.onload =function() {	buscaProduto(""); };
			    
