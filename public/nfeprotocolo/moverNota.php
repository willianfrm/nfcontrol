<?php
require_once __DIR__ . '/../../config.php';

header('Content-Type: application/json');

$chave = $_POST['chave'] ?? '';
$id_mover = $_POST['id_mover'] ?? null;

$resposta = ["sucesso" => false, "mensagem" => ""];

try {
    if (!$chave || !$id_mover) {
        throw new Exception("Dados insuficientes para mover nota.");
    }

    // Buscar nota antiga
    $stmt = $pdo->prepare("SELECT * FROM nota_fiscal WHERE id = ?");
    $stmt->execute([$id_mover]);
    $notaAntiga = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$notaAntiga) {
        throw new Exception("Nota antiga não encontrada.");
    }

    // Atualizar status da nota antiga para MOVIDA
    $stmtU = $pdo->prepare("UPDATE nota_fiscal SET status = 'MOVIDA' WHERE id = ?");
    $stmtU->execute([$id_mover]);

    // Apagar boletos vinculados à nota antiga
    $stmtDel = $pdo->prepare("DELETE FROM boleto WHERE nota_fiscal = ?");
    $stmtDel->execute([$notaAntiga['chave']]);

    $resposta["sucesso"] = true;
    $resposta["mensagem"] = "Nota antiga marcada como MOVIDA e boletos removidos.";
} catch (Exception $e) {
    $resposta["mensagem"] = "Erro: " . $e->getMessage();
}

echo json_encode($resposta);
