<?php

require("conexao.php");

$sql = "SELECT * FROM produtos";
$resultado = mysqli_query($conexao, $sql);

while($produto = mysqli_fetch_assoc($resultado)){
    echo $produto["nome"];
    echo " - R$ " . $produto["preco"];
    echo "<br>";
}