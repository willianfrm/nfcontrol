<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config.php';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die(json_encode([]));
}

$filtro = isset($_GET['filtro']) ? (int)$_GET['filtro'] : 0;
$data = date('d/m/Y');

$sql = "SELECT * FROM nota_fiscal WHERE data_recebimento='$data' AND status='OK'";

switch($filtro){
    case 1: $sql .= " AND (status_oper IS NULL OR status_oper='IMPORTADA')"; break;
    case 2: $sql .= " AND status_oper='ERRO'"; break;
    case 3: $sql .= " AND status_oper='RESOLVIDO'"; break;
    case 4: $sql .= " AND (status_oper='COLETOR' OR status_oper='RECEBENDO')"; break;
    case 5: $sql .= " AND status_oper='RECEBIDO'"; break;
    case 6: $sql .= " AND status_oper='ATUALIZADA'"; break;
}

$sql .= " ORDER BY id DESC";

// Buscar notas
$result = $conn->query($sql);
$notas = [];

while($row = $result->fetch_assoc()){
    // Buscar vencimentos
    $vencimentos = [];
    $resBoleto = $conn->query("SELECT vencimento FROM boleto WHERE nota_fiscal='".$row['chave']."'");
    while($b = $resBoleto->fetch_assoc()){
        $vencimentos[] = $b['vencimento'];
    }
    $row['vencimentos'] = count($vencimentos) ? implode(' - ', $vencimentos) : 'SEM BOLETO';
    $notas[] = $row;
}

echo json_encode($notas);
