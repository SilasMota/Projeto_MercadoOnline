<?php
function filtraEntrada($dado)
  {
    $dado = trim($dado);
    $dado = stripslashes($dado);
    $dado = htmlspecialchars($dado);
    
    return $dado;
  }
class Produtos
{   
    public $id;
    public $nome;
    public $preco;
    public $descricao;
    public $foto;
    public $data;
}

try
{
    require "conexaoMysql.php";
    $conn = conectaAoMySQL();

    $arrayProdutos =null;
    $busca = "";
    if (isset($_POST["busca"]))
        $busca = filtraEntrada($_POST["busca"]);

    $SQL = "
		SELECT id,nome, descricao, preco, foto, datains
		FROM Produto
		WHERE (nome like ?) or (descricao like ?)
        ORDER BY datains DESC
	";

     // Prepara a consulta
      if (! $stmt = $conn->prepare($SQL))
        throw new Exception("Falha na operacao prepare: " . $conn->error);
      
      $buscaP = "%".$busca."%";
      if (! $stmt->bind_param("ss",$buscaP,$buscaP))
        throw new Exception("Falha na operacao bind_param: " . $stmt->error);

      // Executa a consulta
      if (! $stmt->execute())
        throw new Exception("Falha na operacao execute: " . $stmt->error);

      // Indica as variáveis PHP que receberão os resultados
      if (! $stmt->bind_result($id,$nome, $descricao, $preco, $foto, $data))
        throw new Exception("Falha na operacao bind_result: " . $stmt->error);

    while ($stmt->fetch())
      {
        $produto = new Produtos();
        
        $produto->id = $id;
        $produto->nome = $nome;
        $produto->descricao = $descricao;
        $produto->preco = $preco;
        $produto->foto = $foto;
        $produto->data = $data;

        $arrayProdutos[] = $produto;
      }

      if($arrayProdutos != null){
          foreach ($arrayProdutos as $produto) {
              $produto = json_encode($produto);
          }
      }

    echo json_encode($arrayProdutos);
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