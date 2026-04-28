<?php
require_once __DIR__ . '/../../config.php';

header('Content-Type: application/json');

// Pega a data enviada pelo front
$dataInput = $_POST['data'] ?? $_GET['data'] ?? date('Y-m-d');

// Converte para formato brasileiro (DD/MM/YYYY)
$dataObj = DateTime::createFromFormat('Y-m-d', $dataInput);
$data = $dataObj ? $dataObj->format('d/m/Y') : $dataInput;

$resposta = ["sucesso" => false, "notas" => []];

try {
    $stmt = $pdo->prepare("SELECT * FROM nota_fiscal WHERE data_recebimento = ? AND status = 'OK' ORDER BY id DESC");
    $stmt->execute([$data]);
    $notas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($notas as $nota) {
        $stmtB = $pdo->prepare("SELECT * FROM boleto WHERE nota_fiscal = ? ORDER BY vencimento");
        $stmtB->execute([$nota['chave']]);
        $boletos = $stmtB->fetchAll(PDO::FETCH_ASSOC);

        $boletosArr = [];
        foreach ($boletos as $b) {
            $boletosArr[] = [
                "valor" => $b['valor'],
                "vencimento" => $b['vencimento']
            ];
        }

        $resposta["notas"][] = [
            "id" => $nota['id'],
            "chave" => $nota['chave'],
            "numero" => $nota['numero'],
            "serie" => $nota['serie'],
            "cnpj_emit" => $nota['cnpj_emit'],
            "mes" => $nota['mes'],
            "ano" => $nota['ano'],
            "fornecedor" => $nota['fornecedor'],
            "valor" => $nota['valor'],
            "data" => $nota['data_recebimento'],
            "horario" => $nota['horario_chegada'],
            "boletos" => $boletosArr
        ];
    }

    $resposta["sucesso"] = true;
} catch (Exception $e) {
    $resposta["mensagem"] = "Erro: " . $e->getMessage();
}

echo json_encode($resposta);
