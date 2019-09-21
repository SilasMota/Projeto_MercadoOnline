<?php

class Produto
{   
    public $id;
    public $nome;
    public $preco;
    public $descricao;
    public $foto;
    public $data;
}

try{
   
    require "conexaoMysql.php";
    $conn = conectaAoMySQL();
     $id = $_POST["pnum"];
    
     $SQL = "
      SELECT id,nome, descricao, preco, foto
      FROM Produto
      WHERE id = $id
    ";

    $result = $conn->query($SQL);
    if (! $result)
      throw new Exception('Ocorreu uma falha ao gerar o relatorio de testes: ' . $conn->error);

    if ($result->num_rows > 0)
    {
      while ($row = $result->fetch_assoc())
      {
        $produto = new Produto();

        $produto->id = $row["id"];
        $produto->nome = $row["nome"];
        $produto->descricao = $row["descricao"];
        $produto->preco = $row["preco"];
        $produto->foto = $row["foto"];

      }
    }
    echo json_encode($produto);

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