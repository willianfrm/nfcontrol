<?php
require_once __DIR__ . '/../../config.php';

$id = $_GET['id'];
$filtro = $_GET['filtro'];
$opcao = $_GET['opcao'];
$erro = "";

if($opcao == "ERRO COMERCIAL"){
	$opcao = "ERRO";
	$erro = "COM";
}else if($opcao == "ERRO IMPOSTO"){
	$opcao = "ERRO";
	$erro = "IMP";
}else if($opcao == "ERRO XML"){
	$opcao = "ERRO";
	$erro = "XML";
}else if($opcao == "NÃO AGENDADO"){
	$opcao = "ERRO";
	$erro = "AGE";
}

$conn = mysqli_connect($host, $user, $pass, $dbname)//+++++++++++++++++++++++++++++++++++++++++++++++++++
    or die('Could not connect: ' . mysqli_error());

if($opcao=="ERRO"){
	$sql = "Update nota_fiscal Set status_oper='".$opcao."', erro='".$erro."' WHERE id='".$id."'";
	$conn->query($sql);
	$sqlevent = "Insert Into evento (id_nota, status, horario) Values (".$id.",'ERRO ".$erro."','".date('H:i:s')."')";
	$conn->query($sqlevent);
}else{
	$sql = "Update nota_fiscal Set status_oper='".$opcao."' WHERE id='".$id."'";
	$conn->query($sql);
	$sqlevent = "Insert Into evento (id_nota, status, horario) Values (".$id.",'".$opcao."','".date('H:i:s')."')";
	$conn->query($sqlevent);
}

if($filtro==0){
	header("Location: index.php");
}else{
	header("Location: index.php?filtro=".$filtro);
}

?>