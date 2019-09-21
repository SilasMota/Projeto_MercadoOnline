<?php

class Endereco
{
    public $rua;
    public $numero;
    public $bairro;
    public $estado;
}

try
{
    require "conexaoMysql.php";
    $conn = conectaAoMySQL();

    $endereco = "";
    $cep = "";
    if (isset($_POST["cep"]))
        $cep = $_POST["cep"];

    $SQL = "
		SELECT rua, bairro, cidade, estado
		FROM Endereco
		WHERE cep = '$cep';
	";

    if (!$result = $conn->query($SQL))
        throw new Exception('Ocorreu uma falha ao buscar o endereco: ' . $conn->error);

    if ($result->num_rows > 0)
    {
        $row = $result->fetch_assoc();

        $endereco = new Endereco();

        $endereco->rua = $row["rua"];
        $endereco->bairro = $row["bairro"];
        $endereco->cidade = $row["cidade"];
        $endereco->estado = $row["estado"];
    }

    $jsonStr = json_encode($endereco);
    echo $jsonStr;
}
catch (Exception $e)
{

    http_response_code(500);

    $msgErro = $e->getMessage();
    echo $msgErro;
}

if ($conn != null)
    $conn->close();

?>