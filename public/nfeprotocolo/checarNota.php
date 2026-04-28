<?php
require_once __DIR__ . '/../../config.php';
require_once 'NotaFiscal.php';

header('Content-Type: application/json');

$chave = $_POST['chave'] ?? '';
$dataAtual = $_POST['data'] ?? date('d/m/Y');

// Converte para formato brasileiro (DD/MM/YYYY)
$dataObj = DateTime::createFromFormat('Y-m-d', $dataAtual);
$data = $dataObj ? $dataObj->format('d/m/Y') : $dataAtual;

$resposta = [
    "codigo" => 0,
    "numero" => "",
    "serie" => "",
    "fornecedor" => "",
    "data" => "",
    "horario" => "",
    "id" => null
];

try {
    // Verifica se já existe nota com essa chave e status OK
    $stmt = $pdo->prepare("SELECT * FROM nota_fiscal WHERE chave = ? AND status = 'OK'");
    $stmt->execute([$chave]);
    $nota = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($nota) {
        if ($nota['data_recebimento'] == $data) {
            // Já lançada na data atual
            $resposta["codigo"] = 1;
        } else {
            // Já lançada em outra data com status OK → pode mover
            $resposta["codigo"] = 4;
            $resposta["id"] = $nota['id'];
			//demais informações para o card
			$notaObj = new NotaFiscal($chave);
			$resposta["numero"] = $notaObj->numero;
			$resposta["serie"] = $notaObj->serie;
			$resposta["fornecedor"] = $notaObj->fornecedor;
			$resposta["data"] = $notaObj->data;
			$resposta["horario"] = $notaObj->horario;
        }
    } else {
        // Nota não lançada (ou só existem registros MOVIDA/EXCLUIDA) → nova nota
        $notaObj = new NotaFiscal($chave);

        $resposta["codigo"] = 0;
        $resposta["numero"] = $notaObj->numero;
        $resposta["serie"] = $notaObj->serie;
        $resposta["fornecedor"] = $notaObj->fornecedor;
        $resposta["data"] = $notaObj->data;
        $resposta["horario"] = $notaObj->horario;
    }

} catch (Exception $e) {
    $resposta = ["codigo" => -1, "erro" => $e->getMessage()];
}

echo json_encode($resposta);
