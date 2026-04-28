<?php
require_once __DIR__ . '/../config.php';
$id = $_GET['id'];

$conn = mysqli_connect($host, $user, $pass, $dbname)//+++++++++++++++++++++++++++++++++++++++++++++++++++
    or die('Could not connect: ' . mysqli_error());
	
echo '<link href="\formatacao.css" rel="stylesheet">';

$sql = "SELECT * FROM evento WHERE id_nota=".$id;
$result = $conn->query($sql);

if ($result->num_rows > 0) {
	
	$sql2 = "SELECT numero FROM nota_fiscal WHERE id=".$id;
	$result2 = $conn->query($sql2);
	$num_nota = $result2->fetch_assoc();
	
	echo '<table id="titulo">';
	echo '<tr id="titulo">';
	echo '<th id="titulo">HISTORICO NFE '.$num_nota["numero"].'</th>';
	echo '</tr>';
	echo '</table>';

	echo '<table>';

	echo '<tr id="titulo">';
	echo '<th id="titulo">Evento</th>';
	echo '<th id="titulo">Horario</th>';
	echo '</tr>';
	

  while($row = $result->fetch_assoc()) {
	  echo '<tr>';
	  
	  echo '<td>'.$row["status"].'</td>';
	  echo '<td>'.$row["horario"].'</td>';
	  
	  echo '</tr>';
   }
    echo '</table>';
} else {
  echo '<b>NOTA FISCAL SEM EVENTOS!</b>';
}

?>