<?php
require_once __DIR__ . '/../../config.php';
// Conexão MySQL
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Recebimento CPD</title>
    <link rel="stylesheet" href="../bootstrap-5.1.3-dist/css/bootstrap.min.css">
    <style>
        body { padding: 20px; }
		
        .status-box { width: 20px; height: 20px; border-radius: 50%; display: inline-block; }
        .pendente { background-color: gray; }
        .erro { background-color: red; }
        .coletor { background-color: orange; }
        .recebido { background-color: blue; }
        .resolvido { background-color: green; }
        .atualizada { background-color: darkgreen; }
        .modal-iframe { width: 100%; height: 400px; border: none; }
		
		.container { 
			max-width: 95% !important;
			margin: 0 auto !important;
		}
		table { 
			width: 100% !important; 
			table-layout: auto; /* Permite que as colunas se ajustem ao conteúdo */
			word-wrap: break-word;
		}
		th, td { 
			font-size: 12px; 
			vertical-align: middle; 
		}
    </style>
</head>
<body>

<div class="container" style="margin-left: 40px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Recebimento - <?= date('d/m/Y') ?> - <?= $lojanome ?></h3>
        <div>
            <label for="filtro">Filtro:</label>
            <select id="filtro" class="form-select d-inline-block w-auto">
                <option value="0">Nenhum</option>
                <option value="1">Recepcionado</option>
                <option value="2">Erro</option>
                <option value="3">Resolvido</option>
                <option value="4">Coletor</option>
                <option value="5">Recebido</option>
                <option value="6">Atualizada</option>
            </select>
        </div>
		<!--<form action="atualizafornecedor.php">
			<input class="btn btn-info" type="submit" value="Atualizar Fornecedor">
		</form>-->
    </div>

    <table class="table table-striped table-bordered" id="tabela-notas">
        <thead class="table-dark">
            <tr>
                <th>Status</th>
                <th>Número</th>
                <th>Série</th>
                <th style="width:30%;">Fornecedor</th>
                <th>Valor</th>
                <th>Chave</th>
                <th>Vencimentos</th>
                <th>Comprador</th>
                <th>Evento</th>
                <th>Histórico</th>
            </tr>
        </thead>
        <tbody id="tbody-notas">
            <!-- Conteúdo será carregado via AJAX -->
        </tbody>
    </table>
</div>

<!-- Modal Template -->
<div class="modal fade" id="modalEvento" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Selecionar Evento</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body" id="modal-body-evento">
        <!-- Conteúdo dinâmico -->
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalHistorico" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Histórico da Nota</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        <iframe id="iframeHistorico" class="modal-iframe"></iframe>
      </div>
    </div>
  </div>
</div>

<script src="../bootstrap-5.1.3-dist/js/bootstrap.bundle.min.js"></script>
<script>
const filtroSelect = document.getElementById('filtro');
const tbody = document.getElementById('tbody-notas');

function carregarNotas() {
    const filtro = filtroSelect.value;

    fetch(`fetch_notas.php?filtro=${filtro}`)
        .then(res => res.json())
        .then(data => {
            tbody.innerHTML = '';
            data.forEach(nota => {
				// IF para verificar o fornecedor e chamar o PHP de atualização
				if (nota.fornecedor === "FORNECEDOR NÃO ENCONTRADO!!!") {
					//fetch('atualizafornecedor.php');
					//window.location.reload();
				}
				
                const tr = document.createElement('tr');

                // Status
                const tdStatus = document.createElement('td');
                const span = document.createElement('span');
                span.classList.add('status-box');
                switch(nota.status_oper) {
                    case 'ERRO': span.classList.add('erro'); break;
                    case 'COLETOR': span.classList.add('coletor'); break;
                    case 'RECEBENDO': span.classList.add('coletor'); break;
                    case 'RECEBIDO': span.classList.add('recebido'); break;
                    case 'RESOLVIDO': span.classList.add('resolvido'); break;
                    case 'ATUALIZADA': span.classList.add('atualizada'); break;
                    default: span.classList.add('pendente');
                }
                tdStatus.appendChild(span);
                tr.appendChild(tdStatus);

                tr.innerHTML += `
                    <td>${nota.numero}</td>
                    <td>${nota.serie}</td>
                    <td>${nota.fornecedor}</td>
                    <td>${nota.valor}</td>
                    <td>${nota.chave}</td>
                    <td>${nota.vencimentos}</td>
                    <td>${nota.comprador ?? ''}</td>
                    <td><button class="btn btn-sm btn-primary btn-evento" data-id="${nota.id}">Evento</button></td>
                    <td><button class="btn btn-sm btn-secondary btn-historico" data-id="${nota.id}">Histórico</button></td>
                `;
                tbody.appendChild(tr);
            });

            // Evento modal
            document.querySelectorAll('.btn-evento').forEach(btn => {
                btn.onclick = () => {
                    const nota = data.find(n => n.id == btn.dataset.id);
                    const body = document.getElementById('modal-body-evento');
                    body.innerHTML = `
                        <p>Nota: ${nota.numero}</p>
                        <p>Fornecedor: ${nota.fornecedor}</p>
                        <p>Comprador: ${nota.comprador}</p>
                        <form action="eventomanual.php" method="get">
                            <input type="hidden" name="id" value="${nota.id}">
                            <input type="hidden" name="filtro" value="${filtro}">
                            <input type="submit" name="opcao" value="ERRO COMERCIAL" class="btn btn-danger">
                            <input type="submit" name="opcao" value="ERRO IMPOSTO" class="btn btn-danger">
                            <input type="submit" name="opcao" value="ERRO XML" class="btn btn-danger">
                            <input type="submit" name="opcao" value="NÃO AGENDADO" class="btn btn-danger">
                            <input type="submit" name="opcao" value="COLETOR" class="btn btn-warning">
                            <input type="submit" name="opcao" value="ATUALIZADA" class="btn btn-success">
                        </form>
                    `;
                    new bootstrap.Modal(document.getElementById('modalEvento')).show();
                };
            });

            document.querySelectorAll('.btn-historico').forEach(btn => {
                btn.onclick = () => {
                    const iframe = document.getElementById('iframeHistorico');
                    iframe.src = `historico.php?id=${btn.dataset.id}`;
                    new bootstrap.Modal(document.getElementById('modalHistorico')).show();
                };
            });

        })
        .catch(err => console.error(err));
}

// Atualiza a cada 30s
carregarNotas();
setInterval(carregarNotas, 30000);

// Atualiza ao mudar o filtro
filtroSelect.onchange = carregarNotas;
</script>
</body>
</html>
