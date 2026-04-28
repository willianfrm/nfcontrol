<?php
/**************************************************
 * CONEXÃO ORACLE
 **************************************************/
$usuario = '';
$senha   = '@?';
$banco   = '(DESCRIPTION =
              (ADDRESS_LIST =
                (ADDRESS = (PROTOCOL = TCP)(HOST = 0.0.0.0)(PORT = 1521))
              )
              (CONNECT_DATA =
                (SERVICE_NAME = XXXXXX)
              )
            )';

$connOracle = oci_connect($usuario, $senha, $banco, 'AL32UTF8');
if (!$connOracle) {
    $e = oci_error();
    die('Erro Oracle: ' . $e['message']);
}

/**************************************************
 * CONEXÃO MYSQL
 **************************************************/
require_once __DIR__ . '/../../config.php';
$mysql_host = $host;
$mysql_user = $user;
$mysql_pass = $pass;
$mysql_db   = $dbname;

$connMysql = new PDO(
    "mysql:host=$mysql_host;dbname=$mysql_db;charset=utf8",
    $mysql_user,
    $mysql_pass
);
$connMysql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

/**************************************************
 * DATA ATUAL
 **************************************************/
$dataHoje = date('d/m/Y');

/**************************************************
 * BUSCAR NOTAS COM FORNECEDOR NÃO ENCONTRADO
 **************************************************/
$sqlNotas = "
    SELECT id, cnpj_emit
    FROM nota_fiscal
    WHERE fornecedor = 'FORNECEDOR NÃO ENCONTRADO!!!'
      AND data_recebimento = :data
";

$stmtNotas = $connMysql->prepare($sqlNotas);
$stmtNotas->execute(['data' => $dataHoje]);
$notas = $stmtNotas->fetchAll(PDO::FETCH_ASSOC);

if (count($notas) === 0) {
    echo "Nenhum registro para processar hoje.\n";
    exit;
}

/**************************************************
 * PROCESSA NOTA POR NOTA
 **************************************************/
foreach ($notas as $nota) {

    // Limpa CNPJ (só números)
    $cnpj = preg_replace('/\D/', '', $nota['cnpj_emit']);

    if (empty($cnpj)) {
        continue;
    }
	
	//função para corrigir digito verificador com 0 a esquerda no banco do consinco
	$cnpj = preg_replace('/0(\d)$/', '$1', $cnpj);
	//a função acima altera o dv. ex: 05 para 5, 00 para 0, 15 continua 15

    /**********************************************
     * CONSULTA ORACLE
     **********************************************/
    $sqlOracle = "
        SELECT NOMERAZAO
        FROM GE_PESSOA
        WHERE NROCGCCPF || DIGCGCCPF = :cnpj
    ";

    $stmtOracle = oci_parse($connOracle, $sqlOracle);
    oci_bind_by_name($stmtOracle, ':cnpj', $cnpj);
    oci_execute($stmtOracle);

    $resultadoOracle = oci_fetch_assoc($stmtOracle);

    // Se não encontrou fornecedor no Oracle, pula
    if (!$resultadoOracle) {
		echo "Fornecedor não encontrado no CONSINCO.<br>CPF-CNPJ: ".$cnpj."<br>";
        continue;
    }

    $nomeFornecedor = $resultadoOracle['NOMERAZAO'];

    /**********************************************
     * ATUALIZA NOTA_FISCAL
     **********************************************/
    $sqlUpdateNF = "
        UPDATE nota_fiscal
        SET fornecedor = :nome
        WHERE id = :id
    ";

    $stmtUpdateNF = $connMysql->prepare($sqlUpdateNF);
    $stmtUpdateNF->execute([
        'nome' => $nomeFornecedor,
        'id'   => $nota['id']
    ]);

    /**********************************************
     * INSERE / ATUALIZA FORNECEDOR
     **********************************************/
    $sqlFornecedor = "
        INSERT INTO fornecedor (cnpj, nome)
        VALUES (:cnpj, :nome)
        ON DUPLICATE KEY UPDATE nome = VALUES(nome)
    ";

    $stmtFornecedor = $connMysql->prepare($sqlFornecedor);
    $stmtFornecedor->execute([
        'cnpj' => $cnpj,
        'nome' => $nomeFornecedor
    ]);

    echo "Nota ID {$nota['id']} atualizada com fornecedor {$nomeFornecedor}\n";
}

/**************************************************
 * FINALIZA CONEXÃO ORACLE
 **************************************************/
oci_close($connOracle);

echo "Processo finalizado com sucesso.\n";
