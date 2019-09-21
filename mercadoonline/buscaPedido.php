<?php

class ProdutosPedido
{   
    public $id;
    public $nome;
    public $preco;
    public $descricao;
    public $foto;
    public $quantidade;
}
class InfoPedido
{
  public $data;
  public $formapagamento;
}
class InfoCliente
{
  public $idCliente;
  public $nomeCliente;
  public $cpfCliente;
}

try
{
    require "conexaoMysql.php";
    $conn = conectaAoMySQL();

    $arrayProdutos =null;
    $numPedido = "";
    if (isset($_POST["numPedido"]))
        $numPedido = $_POST["numPedido"];

    $SQL1 = "
      SELECT datapedido, formapagemento
      FROM Pedido
      WHERE npedido = $numPedido
    ";

     // Prepara a consulta
      if (! $stmt = $conn->prepare($SQL1))
        throw new Exception("Falha na operacao prepare1: " . $conn->error);
          
      // Executa a consulta
      if (! $stmt->execute())
        throw new Exception("Falha na operacao execute: " . $stmt->error);

      // Indica as variáveis PHP que receberão os resultados
      if (! $stmt->bind_result($data,$formapagamento))
        throw new Exception("Falha na operacao bind_result: " . $stmt->error);

    while ($stmt->fetch())
      {
        $pedido = new InfoPedido();
        
        $pedido->data = $data;
        $pedido->formapagamento = $formapagamento;

      }


      $SQL2 = "
      SELECT Cliente.id,Cliente.nome, Cliente.cpf
      FROM Cliente, Pedido
      WHERE Pedido.cliente = Cliente.id AND Pedido.npedido =  $numPedido
          ";

     // Prepara a consulta
      if (! $stmt2 = $conn->prepare($SQL2))
        throw new Exception("Falha na operacao prepare2: " . $conn->error);
          
      // Executa a consulta
      if (! $stmt2->execute())
        throw new Exception("Falha na operacao execute: " . $stmt->error);

      // Indica as variáveis PHP que receberão os resultados
      if (! $stmt2->bind_result($idCliente,$nomeCliente,$cpfCliente))
        throw new Exception("Falha na operacao bind_result: " . $stmt->error);

    while ($stmt2->fetch())
      {
        $cliente = new InfoCliente();
        
        $cliente->idCliente = $idCliente;
        $cliente->nomeCliente = $nomeCliente;
        $cliente->cpfCliente = $cpfCliente;

      }

     $cliente;
       

    $SQL3 = "
  		SELECT Produto.id, Produto.nome, Produto.descricao, Produto.preco, Produto.foto, Contem.quantidade
      FROM Produto , Contem, Pedido
      WHERE (Produto.id = Contem.produto) AND (Pedido.npedido = Contem.pedido) AND (Pedido.npedido = '$numPedido') 
  	";

     // Prepara a consulta
      if (! $stmt = $conn->prepare($SQL3))
        throw new Exception("Falha na operacao prepare3: " . $conn->error);
          
      // Executa a consulta
      if (! $stmt->execute())
        throw new Exception("Falha na operacao execute: " . $stmt->error);

      // Indica as variáveis PHP que receberão os resultados
      if (! $stmt->bind_result($id,$nome, $descricao, $preco, $foto,$quantidade))
        throw new Exception("Falha na operacao bind_result: " . $stmt->error);

    while ($stmt->fetch())
      {
        $produto = new ProdutosPedido();
        
        $produto->id = $id;
        $produto->nome = $nome;
        $produto->descricao = $descricao;
        $produto->preco = $preco;
        $produto->foto = $foto;
        $produto->quantidade = $quantidade;

        $arrayProdutos[] = $produto;
      }

      $resultadoPedido = array('pedido' => $pedido, 'cliente' => $cliente, 'produtos' =>$arrayProdutos);

    echo json_encode($resultadoPedido);
}
catch (Exception $e)
{
    // altera o código de retorno de status para '500 Internal Server Error'.
    // A função http_response_code deve ser chamada antes do script enviar qualquer
    // texto para a saída padrão
    http_response_code(500);

    $msgErro = $e->getMessage();
    echo $msgErro;
}

if ($conn != null)
    $conn->close();

?>