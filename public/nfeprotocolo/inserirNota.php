<?php
require_once __DIR__ . '/../../config.php';
require_once 'NotaFiscal.php';
require_once 'Boleto.php';

header('Content-Type: application/json');

$chave = $_POST['chave'] ?? '';
$boletos = $_POST['boletos'] ?? [];

$resposta = ["sucesso" => false, "mensagem" => ""];

try {
    // Criar objeto NotaFiscal
    $nota = new NotaFiscal($chave);

    // Inserir nota no banco
    $stmt = $pdo->prepare("INSERT INTO nota_fiscal (chave, numero, serie, cnpj_emit, mes, ano, fornecedor, valor, data_recebimento, horario_chegada, status) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'OK')");
    $stmt->execute([
        $nota->chave,
        $nota->numero,
        $nota->serie,
        $nota->cnpj_emit,
        $nota->mes,
        $nota->ano,
        $nota->fornecedor,
        0, // valor inicial
        $nota->data,
        $nota->horario
    ]);

    // Pegar ID da nota inserida
    $idNota = $pdo->lastInsertId();

    $valorTotal = 0;

    // Inserir boletos vinculados
    foreach ($boletos as $b) {
		$valorboletoFormatado = str_replace('.', ',', $b['valor']);
        $stmtB = $pdo->prepare("INSERT INTO boleto (nota_fiscal, valor, vencimento) VALUES (?, ?, ?)");
        $stmtB->execute([$nota->chave, $valorboletoFormatado, $b['vencimento']]);
        $valorTotal += $b['valor'];
    }
	
	//trocando ponto por virgula
	$valorFormatado = str_replace('.', ',', $valorTotal);
    // Atualizar valor da nota
    $stmtU = $pdo->prepare("UPDATE nota_fiscal SET valor = ? WHERE id = ?");
    $stmtU->execute([$valorFormatado, $idNota]);

    $resposta["sucesso"] = true;
    $resposta["mensagem"] = "Nota inserida com sucesso!";
} catch (Exception $e) {
    $resposta["mensagem"] = "Erro: " . $e->getMessage();
}

echo json_encode($resposta);
