<?php
require_once 'Boleto.php';
header('Content-Type: application/json');

$codigoBoleto = $_POST['codigoBoleto'] ?? '';
$valor = $_POST['valor'] ?? null;
$vencimento = $_POST['vencimento'] ?? null;

$resposta = ["sucesso" => false, "mensagem" => ""];

try {
    if (!empty($codigoBoleto)) {
        $boleto = new Boleto($codigoBoleto);
    } elseif (!empty($valor) && !empty($vencimento)) {
        $boleto = new Boleto($valor, $vencimento);
    } else {
        throw new Exception("Dados do boleto inválidos.");
    }

    $resposta["sucesso"] = true;
    $resposta["mensagem"] = "Boleto adicionado!";
    $resposta["boleto"] = [
        "valor" => $boleto->valor,
        "vencimento" => $boleto->vencimento
    ];
} catch (Exception $e) {
    $resposta["mensagem"] = "Erro: " . $e->getMessage();
}

echo json_encode($resposta);
