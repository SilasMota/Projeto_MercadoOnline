
function getProduto(pnum,quantidade) {
  $.ajax({

            url: 'buscaProdutoCarrinho.php',
            type: 'POST',
            async: true,
            dataType: 'json',
            data: {'pnum': pnum},

            success: function (result)
            {                   
                 document.getElementById("produtosCarrinho").innerHTML = document.getElementById("produtosCarrinho").innerHTML + "<td class='prod-info'><img src="+result.foto+"><h5>"+result.nome+"</h5><p>"+result.descricao+"</p></td><td class='prod-qtd'><input type='number' onchange='alteraQuantidade("+pnum+",this.value);' name='quantidade' class='quantidade form-control' min='1' value='"+quantidade+"'></td><td><p>R$ "+result.preco+"</p></td><td><button class='btn btn-danger' type='button' onclick='removeProdutoCarrinho("+pnum+");' ><span class='fa fa-trash' ></span></button></td>"
                 total = parseFloat(document.getElementById("valorTotal").innerHTML) + (parseFloat(result.preco) * parseFloat(quantidade));
                 document.getElementById("valorTotal").innerHTML = total.toFixed(2);
            },

            error: function (xhr, textStatus, error)
            {
                
                alert(textStatus + error + xhr.responseText);
            }

        });
}

function adcionaProdutoCarrinho(numero){
  document.getElementById("produtosCarrinho").innerHTML = ""
  document.getElementById("valorTotal").innerHTML = "0.00";
  $.ajax({

              url: 'adicionaProdutoCarrinho.php',
              type: 'POST',
              async: true,
              dataType: 'json',
              data: {'numero': numero},

              success: function (result)
              {                   
                  if (result != "") {
                    document.getElementById("produtosCarrinho").innerHTML = document.getElementById("produtosCarrinho").innerHTML + "<tr>"
                    for(idProduto in result){
                      
                      quantidade = result[idProduto];

                      getProduto(idProduto,quantidade);  
                    }
                  } document.getElementById("produtosCarrinho").innerHTML = document.getElementById("produtosCarrinho").innerHTML + "</tr>"
              },

              error: function (xhr, textStatus, error)
              {
                  
                  alert(textStatus + error + xhr.responseText);
              }

          });
  	
    $('#myModal').modal('show');
}

function removeProdutoCarrinho(numero){
  document.getElementById("produtosCarrinho").innerHTML = ""
  document.getElementById("valorTotal").innerHTML = "0.00";
    $.ajax({

              url: 'removeProdutoCarrinho.php',
              type: 'POST',
              async: true,
              dataType: 'json',
              data: {'numero': numero},

              success: function (result)
              {                                   
                if (result != "") {
                    document.getElementById("produtosCarrinho").innerHTML = document.getElementById("produtosCarrinho").innerHTML + "<tr>"
                    for(idProduto in result){
                      
                      quantidade = result[idProduto];

                      getProduto(idProduto,quantidade);  
                    }
                  } document.getElementById("produtosCarrinho").innerHTML = document.getElementById("produtosCarrinho").innerHTML + "</tr>"
                  
              },

              error: function (xhr, textStatus, error)
              {
                  
                  alert(textStatus + error + xhr.responseText);
              }

          });
}

function alteraQuantidade(numero,quantidade){
  document.getElementById("produtosCarrinho").innerHTML = ""
  document.getElementById("valorTotal").innerHTML = "0.00";
    $.ajax({

              url: 'alteraQuantidadeCarrinho.php',
              type: 'POST',
              async: true,
              dataType: 'json',
              data: {'numero': numero,'quantidade':quantidade},

              success: function (result)
              {                                   
                if (result != "") {
                    document.getElementById("produtosCarrinho").innerHTML = document.getElementById("produtosCarrinho").innerHTML + "<tr>"
                    for(idProduto in result){
                      
                      quantidade = result[idProduto];

                      getProduto(idProduto,quantidade);  
                    }
                  } document.getElementById("produtosCarrinho").innerHTML = document.getElementById("produtosCarrinho").innerHTML + "</tr>"
                  
              },

              error: function (xhr, textStatus, error)
              {
                  
                  alert(textStatus + error + xhr.responseText);
              }

          });
}
