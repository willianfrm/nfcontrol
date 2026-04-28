<?php
require_once __DIR__ . '/../../config.php';
$db1 = $dbname;

// Definindo a variável de mensagem
$mensagem = '';

// Conectar ao banco
function conectar($db) {
    global $host, $user, $pass;
    return mysqli_connect($host, $user, $pass, $db);
}

$conn1 = conectar($db1);

// Função para cadastrar fornecedor
function cadastrarFornecedor($cnpj, $nome) {
    global $conn1;
    $query = "INSERT INTO fornecedor (cnpj, nome) VALUES ('$cnpj', '$nome')";
    $ok1 = mysqli_query($conn1, $query);
	// Atualizando notas desse fornecedor
	$updateNF = "UPDATE nota_fiscal SET fornecedor = '$nome' WHERE cnpj_emit = '$cnpj'";
	mysqli_query($conn1, $updateNF);
    return $ok1;
}

// Lidar com o envio do formulário de cadastro
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cnpj']) && isset($_POST['nome'])) {
    $cnpj = $_POST['cnpj'];
    $nome = $_POST['nome'];
	$nome = strtoupper($nome); //deixar o nome em letras maiuscula

    if (cadastrarFornecedor($cnpj, $nome)) {
        $mensagem = '<div class="alert alert-success">Fornecedor cadastrado com sucesso!</div>';
    } else {
        $mensagem = '<div class="alert alert-danger">Erro ao cadastrar fornecedor. Verifique os dados e tente novamente.</div>';
    }
}

// Função para buscar fornecedores
$searchQuery = '';
if (isset($_GET['buscar']) && $_GET['buscar'] != '') {
    $searchTerm = mysqli_real_escape_string($conn1, $_GET['buscar']);
    $searchQuery = "WHERE nome LIKE '%$searchTerm%' OR cnpj LIKE '%$searchTerm%'";
}

// Consulta de fornecedores (com filtro de busca)
$sql = "SELECT * FROM fornecedor $searchQuery LIMIT 10";
$result = mysqli_query($conn1, $sql);

// Atualizar nome do fornecedor
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editar_cnpj']) && isset($_POST['editar_nome'])) {
    $cnpj = $_POST['editar_cnpj'];
    $novoNome = strtoupper($_POST['editar_nome']); // manter padrão em maiúsculas

    $updateQuery = "UPDATE fornecedor SET nome = '$novoNome' WHERE cnpj = '$cnpj'";

    $ok1 = mysqli_query($conn1, $updateQuery);

    if ($ok1) {
        $mensagem = '<div class="alert alert-success">Fornecedor atualizado com sucesso!</div>';
    } else {
        $mensagem = '<div class="alert alert-danger">Erro ao atualizar fornecedor.</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro e Consulta de Fornecedores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .status-box { width: 20px; height: 20px; border-radius: 4px; display: inline-block; }
        .status-ERRO { background-color: red; }
        .status-COLETOR { background-color: orange; }
        .status-RECEBIDO, .status-RECEBENDO { background-color: green; }
        .status-RESOLVIDO { background-color: blue; }
        .status-ATUALIZADA { background-color: purple; }
        .status-default { background-color: grey; }
    </style>
</head>
<body class="bg-light">
    <div class="container py-4">
        <h3 class="mb-4">Cadastro de Fornecedor</h3>
        <?= $mensagem ?>
        <form method="POST">
            <div class="mb-3">
                <label for="cnpj" class="form-label">CNPJ</label>
                <input type="text" name="cnpj" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="nome" class="form-label">Nome</label>
                <input type="text" name="nome" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Cadastrar</button>
        </form>

        <hr>

        <h4 class="mb-4">Consulta de Fornecedores</h4>
        <form method="GET" class="mb-3">
            <div class="d-flex">
                <input type="text" name="buscar" class="form-control me-2" placeholder="Buscar por nome ou CNPJ" value="<?= $_GET['buscar'] ?? '' ?>">
                <button type="submit" class="btn btn-info">Buscar</button>
            </div>
        </form>

        <table class="table table-bordered table-sm table-hover bg-white">
            <thead class="table-dark">
                <tr>
                    <th>CNPJ</th>
                    <th>Nome</th>
                    <th>Alterar</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?= $row['cnpj'] ?></td>
                        <td><?= $row['nome'] ?></td>
                        <td>
                            <!-- Botão para abrir o modal -->
                            <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['cnpj'] ?>">Alterar</button>
                        </td>
                    </tr>

                    <!-- Modal para editar fornecedor -->
                    <div class="modal fade" id="modalEdit<?= $row['cnpj'] ?>" tabindex="-1" aria-labelledby="modalEditLabel<?= $row['cnpj'] ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalEditLabel<?= $row['cnpj'] ?>">Editar Fornecedor</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="POST">
                                        <input type="hidden" name="editar_cnpj" value="<?= $row['cnpj'] ?>">
                                        <div class="mb-3">
                                            <label for="editar_nome" class="form-label">Nome</label>
                                            <input type="text" name="editar_nome" class="form-control" value="<?= $row['nome'] ?>" required>
                                        </div>
                                        <button type="submit" class="btn btn-success">Salvar Alterações</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- JS do Bootstrap (corrigido a ordem e inicialização) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
</body>
</html>

