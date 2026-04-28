<?php require_once __DIR__ . '/../../config.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Recebimento NFS</title>
  <link href="../bootstrap-5.1.3-dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body style="background-color:#f0f0f0;">

<div class="container mt-4">
  <h3 class="mb-3">Recebimento NFS Protocolo - <?= $lojanome ?></h3>

  <!-- Barra de ações -->
  <div class="mb-3">
    <button class="btn btn-primary" id="btnAtualizar">Atualizar</button>
    <button class="btn btn-secondary" id="btnPesquisar">Pesquisar</button>
    <button class="btn btn-info" id="btnRelatorio">Relatório</button>
  </div>

  <!-- Campo único -->
  <form id="formEntrada" onsubmit="return false;">
    <div class="row mb-3">
      <div class="col-md-6">
        <label class="form-label">Entrada</label>
        <input type="text" id="entrada" class="form-control" placeholder="Bipe a chave da nota" required autofocus>
      </div>
      <div class="col-md-3">
        <label class="form-label">Data</label>
        <input type="date" id="dataNota" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
      </div>
    </div>
  </form>

  <!-- Detalhes da nota (card azul escuro) -->
  <div id="detalhesNota" class="card text-white mb-3 d-none" style="background-color:#0d6efd;">
    <div class="card-body">
      <h5 class="card-title">Nota em edição</h5>
      <p id="infoNota"></p>
      <h6>Boletos adicionados:</h6>
      <ul id="listaBoletos" class="list-group"></ul>
      <button class="btn btn-danger btn-sm mt-2" id="btnCancelar">Cancelar</button>
    </div>
  </div>

  <!-- Lista de notas -->
  <h4 class="mt-4">Notas do dia</h4>
  <table class="table table-striped">
    <thead>
      <tr>
        <th>Número</th>
        <th>Série</th>
        <th style="width:30%;">Fornecedor</th>
        <th style="word-break:break-all;">Chave</th>
        <th style="width:10%;">Valor</th>
        <th>Chegada</th>
        <th>Boletos</th>
        <th>Ações</th>
      </tr>
    </thead>
    <tbody id="listaNotas"></tbody>
  </table>
</div>

<!-- Modal Pesquisar -->
<div class="modal fade" id="modalPesquisar" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Pesquisar Nota</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-2">
          <label>Número da Nota</label>
          <input type="text" id="pesqNumero" class="form-control">
        </div>
        <div class="mb-2">
          <label>Chave</label>
          <input type="text" id="pesqChave" class="form-control">
        </div>
        <div class="mb-2">
          <label>CNPJ Fornecedor</label>
          <input type="text" id="pesqCnpj" class="form-control">
        </div>
        <button class="btn btn-primary mt-2" id="btnExecutarPesquisa">Pesquisar</button>
        <div id="resultadoPesquisa" class="mt-3"></div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Boletos -->
<div class="modal fade" id="modalBoletos" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Boletos da Nota</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="conteudoBoletos"></div>
    </div>
  </div>
</div>

<script src="../bootstrap-5.1.3-dist/js/bootstrap.bundle.min.js"></script>
<script>
let notaAtual = null;

function carregarNotas(){
  let data = $("#dataNota").val();
  $.post("listarNotas.php", { data: data }, function(resposta){
    if(resposta.sucesso){
      $("#listaNotas").empty();
      resposta.notas.forEach(nota => {
        let row = `
          <tr>
            <td>${nota.numero}</td>
            <td>${nota.serie}</td>
            <td>${nota.fornecedor}</td>
            <td style="word-break:break-all;">${nota.chave}</td>
            <td>R$ ${nota.valor}</td>
            <td>${nota.data} ${nota.horario}</td>
            <td><button class="btn btn-sm btn-info" onclick='verBoletos(${JSON.stringify(nota.boletos)})'>Boletos</button></td>
            <td><button class="btn btn-danger btn-sm" onclick="excluirNota(${nota.id})">Excluir</button></td>
          </tr>`;
        $("#listaNotas").append(row);
      });
    } else {
      $("#listaNotas").html("<tr><td colspan='8'>Nenhuma nota encontrada.</td></tr>");
    }
  }, "json");
}

function verBoletos(boletos){
  let html = boletos.length > 0 
    ? boletos.map(b => `<p>Venc: ${b.vencimento} - R$ ${b.valor}</p>`).join("")
    : "<p>-- Sem Boletos --</p>";
  $("#conteudoBoletos").html(html);
  var modal = new bootstrap.Modal(document.getElementById('modalBoletos'));
  modal.show();
}

function excluirNota(idNota){
if (confirm("Tem certeza que deseja excluir esta nota?")) {
  $.post("excluirNota.php", { idNota: idNota }, function(resposta){
    if(resposta.sucesso){
      alert("Nota excluída!");
      carregarNotas();
    } else {
      alert(resposta.mensagem);
    }
  }, "json");
}
}

// Atualizar
$("#btnAtualizar").click(function(){
  carregarNotas(); // ou location.reload();
});

// Pesquisar
$("#btnPesquisar").click(function(){
  $("#pesqNumero").val("");
  $("#pesqChave").val("");
  $("#pesqCnpj").val("");
  $("#resultadoPesquisa").html("");
  var modal = new bootstrap.Modal(document.getElementById('modalPesquisar'));
  modal.show();
});

$("#btnExecutarPesquisa").click(function(){
  let numero = $("#pesqNumero").val().trim();
  let chave  = $("#pesqChave").val().trim();
  let cnpj   = $("#pesqCnpj").val().trim();

  $.post("pesquisarNota.php", { numero: numero, chave: chave, cnpj: cnpj }, function(resposta){
    if(resposta.sucesso){
      let html = "<table class='table table-sm'><thead><tr><th>Número</th><th>Série</th><th>Fornecedor</th><th>Chave</th><th>Data de Recebimento</th><th>Status</th></tr></thead><tbody>";
      resposta.notas.forEach(n => {
        html += `<tr>
          <td>${n.numero}</td>
          <td>${n.serie}</td>
          <td>${n.fornecedor}</td>
          <td style="word-break:break-all;">${n.chave}</td>
		  <td>${n.data_recebimento}</td>
          <td>${n.status}</td>
        </tr>`;
      });
      html += "</tbody></table>";
      $("#resultadoPesquisa").html(html);
    } else {
      $("#resultadoPesquisa").html("<p>"+resposta.mensagem+"</p>");
    }
  }, "json");
});

// Relatório
$("#btnRelatorio").click(function(){
  let data = $("#dataNota").val();
  data = data.split('-').reverse().join('/');
  $.post("listarNotas.php", { data: data }, function(resposta){
    if(resposta.sucesso){
      let relatorio = "<h3>Relatório de Notas - " + data + "</h3>";
      relatorio += "<table border='1' cellspacing='0' cellpadding='5'><thead><tr><th>Número</th><th>Série</th><th>Fornecedor</th><th>Chave</th><th>Valor</th><th>Chegada</th></tr></thead><tbody>";
      resposta.notas.forEach(n => {
        relatorio += `<tr>
          <td>${n.numero}</td>
          <td>${n.serie}</td>
          <td>${n.fornecedor}</td>
          <td>${n.chave}</td>
          <td>R$ ${n.valor}</td>
          <td>${n.data} ${n.horario}</td>
        </tr>`;
      });
      relatorio += "</tbody></table>";
      let w = window.open("", "Relatório");
	  //Criamos o estilo para diminuir a escala e remover margens inúteis
		let style = `
		<style>
		  @page { 
			size: auto; 
			margin: 10mm; /* Diminui as margens da folha */
		  }
		  body { 
			font-family: Arial, sans-serif; 
			font-size: 12px; /* Fonte menor para caber mais dados */
		  }
		  table { 
			width: 100%; 
			border-collapse: collapse; 
			/* OPCIONAL: Usa zoom ou scale para reduzir tudo em 10-20% */
			zoom: 0.9; 
		  }
		  th, td { font-size: 10px; padding: 4px; }
		  h3 { margin-bottom: 5px; }
		</style>`;
	  /////////////////////////////////////////////////
      w.document.write(style + relatorio);
      w.print();
    } else {
      alert("Nenhuma nota encontrada para relatório.");
    }
  }, "json");
});

$(document).ready(function(){
  carregarNotas();

  $("#formEntrada").on("submit", function(){
    let entrada = $("#entrada").val().trim();
    let data = $("#dataNota").val();
    if(!entrada) return;

    if(notaAtual){
      if(entrada === notaAtual.chave){
        if(notaAtual.mover){
          $.post("moverNota.php", { chave: notaAtual.chave, id_mover: notaAtual.id_mover }, function(r){
            if(r.sucesso){
              $.post("inserirNota.php", { chave: notaAtual.chave, boletos: notaAtual.boletos }, function(r2){
                if(r2.sucesso){
                  notaAtual = null;
                  $("#detalhesNota").addClass("d-none");
                  $("#entrada").val("").focus().attr("placeholder","Bipe a chave da nota");
                  carregarNotas();
                } else {
                  alert(r2.mensagem);
                }
              }, "json");
            } else {
              alert(r.mensagem);
            }
          }, "json");
        } else {
          $.post("inserirNota.php", { chave: notaAtual.chave, boletos: notaAtual.boletos }, function(r){
            if(r.sucesso){
              notaAtual = null;
              $("#detalhesNota").addClass("d-none");
              $("#entrada").val("").focus().attr("placeholder","Bipe a chave da nota");
              carregarNotas();
            } else {
              alert(r.mensagem);
            }
          }, "json");
        }
      } else {
        $.post("inserirBoleto.php", { codigoBoleto: entrada }, function(resposta){
          if(resposta.sucesso){
            notaAtual.boletos.push(resposta.boleto);
            $("#entrada").val("").attr("placeholder","Adicionar boletos ou chave da nota para finalizar");
            $("#listaBoletos").append(
              `<li class="list-group-item">
                 Venc: ${resposta.boleto.vencimento} - R$ ${resposta.boleto.valor}
               </li>`
            );
          } else {
            alert(resposta.mensagem);
          }
        }, "json");
      }
      return;
    }

    $.post("checarNota.php", { chave: entrada, data: data }, function(resposta){
      if(resposta.codigo == 0){
        notaAtual = {
          chave: entrada,
          numero: resposta.numero,
          serie: resposta.serie,
          fornecedor: resposta.fornecedor,
          data: resposta.data,
          horario: resposta.horario,
          boletos: []
        };

        $("#infoNota").html(
          "<strong>N° NFE:</strong> " + resposta.numero +
          " - <strong>Série:</strong> " + resposta.serie + "<br>" +
          "<strong>Fornecedor:</strong> " + resposta.fornecedor + "<br>" +
          "<strong>Chegada:</strong> " + resposta.data + " " + resposta.horario +
          "<br><em>Bipe boletos ou bipar a chave novamente para finalizar.</em>"
        );

        $("#listaBoletos").empty();
        $("#detalhesNota").removeClass("d-none");
        $("#entrada").val("").attr("placeholder","Adicionar boletos ou chave da nota para finalizar");
      }
      else if(resposta.codigo == 1){
        alert("Nota já protocolada na data atual!");
      }
       else if(resposta.codigo == 4){
        if(confirm("Nota já lançada em outra data. Deseja mover para a data atual?")){
          // Em vez de chamar moverNota.php aqui,
          // tratamos como uma nova nota em edição
          notaAtual = {
            chave: entrada,
            numero: resposta.numero,
            serie: resposta.serie,
            fornecedor: resposta.fornecedor,
            data: resposta.data,
            horario: resposta.horario,
            boletos: [],
            mover: true,       // flag indicando que é um caso de mover
            id_mover: resposta.id
          };

          $("#infoNota").html(
            "<strong>N° NFE:</strong> " + resposta.numero +
            " - <strong>Série:</strong> " + resposta.serie + "<br>" +
            "<strong>Fornecedor:</strong> " + resposta.fornecedor + "<br>" +
            "<strong>Chegada:</strong> " + resposta.data + " " + resposta.horario +
            "<br><em>Bipe boletos ou bipar a chave novamente para finalizar (mover).</em>"
          );

          $("#listaBoletos").empty();
          $("#detalhesNota").removeClass("d-none");
          $("#entrada").val("").focus().attr("placeholder","Adicionar boletos ou chave da nota para finalizar");
        }
      }
    }, "json");
  });
  
	// Função para verificar se data selecionada é a atual
	function verificarData(){
	  let dataSelecionada = $("#dataNota").val();
	  let hoje = new Date().toISOString().split('T')[0]; // formato YYYY-MM-DD

	  if(dataSelecionada !== hoje){
		// Bloqueia input e avisa
		$("#entrada").prop("disabled", true);
		$("#entrada").attr("placeholder", "Só é permitido adicionar notas na data atual");
	  } else {
		// Reabilita input
		$("#entrada").prop("disabled", false);
		$("#entrada").attr("placeholder", "Bipe a chave da nota");
	  }

	  // Atualiza listagem
	  carregarNotas();
	}

	// Executa ao alterar data
	$("#dataNota").on("change", verificarData);

	// Executa na carga inicial
	verificarData();

  // Botão cancelar
  $("#btnCancelar").click(function(){
    notaAtual = null;
    $("#detalhesNota").addClass("d-none");
    $("#entrada").val("").attr("placeholder","Bipe a chave da nota");
    $("#listaBoletos").empty();
  });

  $("#btnAtualizar").click(function(){
    carregarNotas();
  });
});
</script>
</body>
</html>
