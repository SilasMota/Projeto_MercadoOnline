

function buscaPedido(numPedido)
{		
    document.getElementById("detalhesProdutos").innerHTML = '';
            $.ajax({

            url: 'buscaPedido.php',
            type: 'POST',
            async: true,
            dataType: 'json',
            data: {'numPedido': numPedido},

            success: function (result)
            {                   
                if (result != "") {

                	for(produto in result.produtos){
                        document.getElementById("detalhesProdutos").innerHTML = document.getElementById("detalhesProdutos").innerHTML + "<tr><td class='prod-info'> <img src='"+result.produtos[produto].foto+"'><h5>"+result.produtos[produto].nome+"</h5><p>"+result.produtos[produto].descricao+"</p></td>"+"<td class='prod-qtd'>"+result.produtos[produto].quantidade+"</td>"+"<td><p>R$ "+result.produtos[produto].preco+"</p></td></tr>";
                    }


                    if(document.getElementById("detalhesFP"))
                    document.getElementById("detalhesFP").innerHTML = result.pedido.formapagamento;

                    if(document.getElementById("detalhesData"))
                    document.getElementById("detalhesData").innerHTML = result.pedido.data;

                    if(document.getElementById("detalhesCID"))
                    document.getElementById("detalhesCID").innerHTML = result.cliente.idCliente;

                    if(document.getElementById("detalhesCPF"))
                    document.getElementById("detalhesCPF").innerHTML = result.cliente.cpfCliente;
                
                    if(document.getElementById("detalhesCNome"))
                    document.getElementById("detalhesCNome").innerHTML = result.cliente.nomeCliente;
                }
            },

            error: function (xhr, textStatus, error)
            {
                
                alert(textStatus + error + xhr.responseText);
            }

        });

    }
			    
