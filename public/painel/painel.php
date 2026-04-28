<!doctype html>
<html>
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	 <meta http-equiv="refresh" content="30">

    <!-- Bootstrap CSS -->
    <link href="\bootstrap-5.1.3-dist\css\bootstrap.min.css" rel="stylesheet">
	
	<style type="text/css">
		html { height: 100%; }
		body { background-color: #fff;/*#bdbdbd*/ height: 100%; margin-left: 12px; margin-right: 12px; }
		div { background-color: #fff; }
		.height100cab { height: calc(100% - 52px); } /*72px é a borda (50px logo + 2px borda)*/
		.height100 { height: 100%; }
		.height2row { height: calc(100% /2); }
		.bd_coletor { border-top: 3px solid #21aaff; }
		.bd_erro { border-top: 3px solid red; }
		.bd_process { border-top: 3px solid black; }
		.bd_recebido { border-top: 3px solid green; }
		.bd_historico { border-top: 3px solid gray; }
		.bd_pnesq { border-top: 3px solid #1f3665; }
		.bd_box { border-right: 1px solid #dbdbdb; border-left: 1px solid #dbdbdb; border-bottom: 1px solid #dbdbdb; }
		.pd_box { padding-top: 3px; padding-bottom: 3px; }
		.pd_comp { padding: 3px; }
		.rowmargin {margin-left: 0px; margin-right: 0px;}
		.divtitulo {margin-bottom: -5px; font-size: 12px; color: grey;}
		.bd_baixo{ border-bottom: 1px solid grey; }
		.bd_baixo_verd{ border-bottom: 2px solid green; }
		.bd_baixo_verm{ border-bottom: 2px solid red; }
		.bd_baixo_azul{ border-bottom: 2px solid #21aaff; }
		.bd_baixo_azul_esc{ border-bottom: 2px solid #025dbf; }
		.fontegrd { font-size: 22px; }
		
		
		
		.borda { border: 1px solid black; }
		.bd_esq { border-left: 1px solid black; }
		.bd_dir { border-right: 1px solid black; }
		.bd_lat { border-right: 1px solid black; border-left: 1px solid black; }
		.bd_cim { border-top: 1px solid black; }
		.bd_bai { border-bottom: 1px solid black; }
		.tx_cent { text-align: center; }
		.cor_cabecalho { background-color: #4d80b0; }
		.cor_valores { background-color: #afd3e0; }
		.cor_falta { background-color: #eb2f2f; }
		.cor_sobra { background-color: #2feb3b; }
		.cor_ok { background-color: #42a4ff; }
		.btn_contar { with: 100%; height: 100%; border-radius: 5px; background-color: #bfd4db; border: 1px solid black; }
		.btn_finalizar { padding: 12px; margin: 8px; border-radius: 5px; background-color: #ff7729; border: 1px solid black; }
		.btn_iniciar { padding: 12px; margin: 8px; border-radius: 5px; background-color: #29ff3e; border: 1px solid black; }
		.cx_texto { width: 100%; box-sizing: border-box; }
		.mg_2 { margin-top: 2px; margin-bottom: 2px; }
	</style>
	
    <title>Painel de Recebimento</title>
  </head>
  <body>
	
	<?php
	
	function calculaHora($chegada, $saida)
	{
		$entrada = $chegada;
		$saida = $saida;
		$hora1 = explode(":",$entrada);
		$hora2 = explode(":",$saida);
		$acumulador1 = ($hora1[0] * 3600) + ($hora1[1] * 60) + $hora1[2];
		$acumulador2 = ($hora2[0] * 3600) + ($hora2[1] * 60) + $hora2[2];
		$resultado = $acumulador2 - $acumulador1;
		$hora_ponto = floor($resultado / 3600);
		$resultado = $resultado - ($hora_ponto * 3600);
		$min_ponto = floor($resultado / 60);
		$resultado = $resultado - ($min_ponto * 60);
		$secs_ponto = $resultado;
		//Grava na variável resultado final
		if($hora_ponto<10) { $hora_ponto = "0".$hora_ponto; }
		if($min_ponto<10) { $min_ponto = "0".$min_ponto; }
		$tempo = $hora_ponto."h".$min_ponto."m";//.":".$secs_ponto;
		return $tempo;
	}
	
	function retornaListaForn($fornrecebimento){
		for($i=0; $i<count($fornrecebimento);$i++){
			//echo '<div class="row rowmargin bd_baixo">';
			//echo '<div class="col-4"> <div class="divtitulo">FORNECEDOR<br></div>'.$fornrecebimento[$i][0].' </div>';
			if($fornrecebimento[$i][1]=="ERRO"){
				echo '<tr class="rowmargin bd_baixo_verm">';
				echo '<td class=""> <div class="divtitulo">FORNECEDOR<br></div>'.$fornrecebimento[$i][0].' </td>';
				echo '<td class="" style="color: red;"> <div class="divtitulo">SITUAÇÃO<br></div>'.$fornrecebimento[$i][1].' - '.$fornrecebimento[$i][6].' </td>';
			}elseif($fornrecebimento[$i][1]=="OK - AGUARD. RECEBIMENTO"){
				echo '<tr class="rowmargin bd_baixo_azul">';
				echo '<td class=""> <div class="divtitulo">FORNECEDOR<br></div>'.$fornrecebimento[$i][0].' </td>';
				echo '<td class="" style="color: #21aaff"> <div class="divtitulo">SITUAÇÃO<br></div>'.$fornrecebimento[$i][1].' </td>';
			}elseif($fornrecebimento[$i][1]=="RECEBENDO - CONFERINDO"){
				echo '<tr class="rowmargin bd_baixo_azul_esc">';
				echo '<td class=""> <div class="divtitulo">FORNECEDOR<br></div>'.$fornrecebimento[$i][0].' </td>';
				echo '<td class="" style="color: #025dbf"> <div class="divtitulo">SITUAÇÃO<br></div>'.$fornrecebimento[$i][1].' </td>';
			}else{
				echo '<tr class="rowmargin bd_baixo">';
				echo '<td class=""> <div class="divtitulo">FORNECEDOR<br></div>'.$fornrecebimento[$i][0].' </td>';
				echo '<td class=""> <div class="divtitulo">SITUAÇÃO<br></div>'.$fornrecebimento[$i][1].' </td>';
			}
			echo '<td class=""> <div class="divtitulo">NOTAS<br></div>'.$fornrecebimento[$i][2].' </td>';
			//echo '<td class=""> <div class="divtitulo">CHEGADA<br></div>'.$fornrecebimento[$i][3].' </td>';
			echo '<td class=""> <div class="divtitulo">CHEGADA<br></div>'.substr($fornrecebimento[$i][3], 0, 5).' </td>';
			echo '<td class=""> <div class="divtitulo">TEMPO DE ESPERA<br></div>'.calculaHora($fornrecebimento[$i][3],date('H:i:s')).' </td>';
			//echo '<td class=""> <div class="divtitulo">LOJA<br></div>'.$fornrecebimento[$i][4].' </td>';
			
			echo '</tr>';
		}
	}
	
	require_once __DIR__ . '/../../config.php';
	
	$fornrecebimento = array();
	$listacomerro = array();
	
	$data = date('d/m/Y');
	$nome_forn = 40; //tamanho maximo de caracteres do nome do fornecedor		
	//LISTA DE EXCEÇÕES PARA FORNECEDORES QUE NÃO IRIÃO APARECER NO PAINEL (INSERIR CNPJ NA LISTA) (uso exclusivo de cliente)
	$listaexecoes = array("880017000180", "46395687003985");
	
	$sql = "SELECT *, '".$lojanome."' as LOJA from ".$dbname.".nota_fiscal where data_recebimento='".$data."' and status='OK' ORDER BY horario_chegada ASC";
	
	$conn = mysqli_connect($host, $user, $pass)
		or die('Could not connect: ' . mysqli_error());
	
	//$sql = "SELECT * FROM nota_fiscal WHERE data_recebimento='".$data."' AND status='OK'";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		
		$ultimoforn = array(
		$lojanome => array()
		);
		
		while($row = $result->fetch_assoc()) {
			//caso o fornecedor esteja na lista de exceções continua para próxima nota
			if(in_array($row["cnpj_emit"], $listaexecoes)){
				continue;
			}
			
			//convertendo nome do status do banco com da aplicação
			$status = "RECEPCIONADO";
			if($row["status_oper"]=="ATUALIZADA" || $row["status_oper"]=="RECEBIDO" ){
				$status = "RECEBIDO";
			}elseif($row["status_oper"]=="ERRO"){
				$status = "ERRO";
			}elseif($row["status_oper"]=="COLETOR"){
				$status = "AGUARD. RECEBIMENTO";
			}elseif($row["status_oper"]=="RECEBENDO"){
				$status = "CONFERINDO";
			}elseif($row["status_oper"]=="IMPORTADA"){
				$status = "PROCESSANDO - CPD";
			}
			
			//mostrar tipo de erro
			$statuserro = "";
			if($status=="ERRO"){
				if($row["erro"]=="COM")
					$statuserro = "COMERCIAL";
				else if($row["erro"]=="IMP")
					$statuserro = "IMPOSTO";
				else if($row["erro"]=="XML")
					$statuserro = "XML";
				else if($row["erro"]=="AGE")
					$statuserro = "NÃO AGENDADO";
			}
			
			//continua no mesmo fornecedor
			if(isset($ultimoforn[$row["LOJA"]][5]) && $ultimoforn[$row["LOJA"]][5]==$row["cnpj_emit"]){
				$ultimoforn[$row["LOJA"]][2] += 1; //adiciona +1 nota
					
				if($ultimoforn[$row["LOJA"]][1] != "ERRO" && $status=="ERRO"){
					$ultimoforn[$row["LOJA"]][1] = "ERRO";
				}elseif($ultimoforn[$row["LOJA"]][1] == "RECEPCIONADO" && $status != "RECEPCIONADO"){
					$ultimoforn[$row["LOJA"]][1] = $status;
				}
				
			}else{ //inicia um novo fornecedor
				//encerra o ultimo fornecedor
				if(isset($ultimoforn[$row["LOJA"]][1]) && $ultimoforn[$row["LOJA"]][1]!="RECEBIDO"){
					if($ultimoforn[$row["LOJA"]][1]=="ERRO")
						$listacomerro[] = $ultimoforn[$row["LOJA"]];
					else
						$fornrecebimento[] = $ultimoforn[$row["LOJA"]];
				}
				$ultimoforn[$row["LOJA"]] = array();
				
				//iniciando novo fornecedor [0]nome [1]status [2]n° notas [3]hr chegada [4]loja [5]cnpj
				$ultimoforn[$row["LOJA"]] = array(substr($row["fornecedor"], 0, $nome_forn), $status, 1, $row["horario_chegada"], $row["LOJA"], $row["cnpj_emit"], $statuserro );
			}
		}
		//se não tiver mais notas, encerra o ultimo fornecedor
		if(isset($ultimoforn[$lojanome][1]) && $ultimoforn[$lojanome][1]!="RECEBIDO"){
			if($ultimoforn[$lojanome][1]=="ERRO")
				$listacomerro[] = $ultimoforn[$lojanome];
			else
				$fornrecebimento[] = $ultimoforn[$lojanome];
			
		}
	}
	
	//cabeçalho
	echo '<div class="borda tx_cent cor_cabecalho" style="color: white">
		<table width=100%>
			<tr>
				<td class="cor_cabecalho"> <h2>RECEBIMENTO - '.$lojanome.'</h2> </td>
				<td class="cor_cabecalho"> <h2>'.$data.'</h2> </td>
			</tr>
		</table>
	</div>';
	
	echo '<div class="height100cab">'; //abre painel principal meio
	
		echo '<div class="height100 pd_comp">'; //abre painel esquerdo
			echo '<div class="height100 bd_pnesq bd_box">';
			echo '<h6><b>RECEBIMENTO AGORA</b></h6>';
				echo '<table class="fontegrd" width=100%>';
				
				retornaListaForn($listacomerro);
				retornaListaForn($fornrecebimento);
				
				echo '</table>';
			echo '</div>';
		echo '</div>'; //fecha painel esquerdo
		
		//echo '</div>';
	
	echo '</div>'; //fecha painel principal meio
	
	?>
	
    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="\bootstrap-5.1.3-dist\js\bootstrap.bundle.min.js"></script>
  </body>
</html>