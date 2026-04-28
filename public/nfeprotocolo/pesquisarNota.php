<?php
require_once __DIR__ . '/../../config.php';

header('Content-Type: application/json');

$numero = $_POST['numero'] ?? '';
$chave  = $_POST['chave'] ?? '';
$cnpj   = $_POST['cnpj'] ?? '';

$resposta = ["sucesso" => false, "notas" => []];

try {
    if (!$numero && !$chave && !$cnpj) {
        throw new Exception("Informe pelo menos um critério de pesquisa.");
    }

    $sql = "SELECT id, numero, serie, fornecedor, chave, valor, data_recebimento, horario_chegada, status
            FROM nota_fiscal
            WHERE 1=1";
    $params = [];

    if ($numero) {
        $sql .= " AND numero = ?";
        $params[] = $numero;
    }
    if ($chave) {
        $sql .= " AND chave = ?";
        $params[] = $chave;
    }
    if ($cnpj) {
        $sql .= " AND cnpj_emit = ?";
        $params[] = $cnpj;
    }

    $sql .= " ORDER BY data_recebimento DESC, horario_chegada DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $notas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($notas) {
        $resposta["sucesso"] = true;
        $resposta["notas"] = $notas;
    } else {
        $resposta["mensagem"] = "Nenhuma nota encontrada.";
    }

} catch (Exception $e) {
    $resposta["mensagem"] = "Erro: " . $e->getMessage();
}

echo json_encode($resposta);
