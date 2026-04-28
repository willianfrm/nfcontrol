<?php
require_once __DIR__ . '/../../config.php';

header('Content-Type: application/json');

$idNota = $_POST['idNota'] ?? null;
$resposta = ["sucesso" => false, "mensagem" => ""];

try {
    if (!$idNota) {
        throw new Exception("ID da nota não informado.");
    }

    // Buscar chave da nota pelo ID
    $stmt = $pdo->prepare("SELECT chave FROM nota_fiscal WHERE id = ?");
    $stmt->execute([$idNota]);
    $nota = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$nota) {
        throw new Exception("Nota não encontrada.");
    }

    $chaveNota = $nota['chave'];

    // Atualiza status da nota
    $stmtU = $pdo->prepare("UPDATE nota_fiscal SET status = 'EXCLUIDA' WHERE id = ?");
    $stmtU->execute([$idNota]);

    // Exclui boletos vinculados à nota
    $stmtB = $pdo->prepare("DELETE FROM boleto WHERE nota_fiscal = ?");
    $stmtB->execute([$chaveNota]);

    $resposta["sucesso"] = true;
    $resposta["mensagem"] = "Nota e boletos vinculados foram excluídos.";
} catch (Exception $e) {
    $resposta["mensagem"] = "Erro: " . $e->getMessage();
}

echo json_encode($resposta);
