<?php
require("final.php");
/*
$Usuario="root";
$Password="";
$Servidor="localhost";
$BasedeDatos="fastcardweb";
*/
$link = @mysql_connect($Servidor,$Usuario, $Password,'',65536) or die ("<p /><br /><p /><div style='text-align:center'>En estos momentos no hay conexión con el servidor, inténtalo más tarde.</div>");
mysql_select_db($bdd, $link);
$sql="SELECT * FROM familiar WHERE cedulafam = '' and cedula != '' order by cedula";
$rs=mysql_query($sql);
$cuantos=mysql_num_rows($rs);
set_time_limit($cuantos);
echo 'Registros: '.$cuantos;
echo '<table border=1>';
$cuantos=0;
$ultima='';
$continua=1;
while($row=mysql_fetch_assoc($rs)) {
	$cuantos++;
	if ($cuantos > 4) {
		echo '</tr>'; $cuantos=1;
	}
	if ($cuantos ==1) echo '<tr>';
	echo '<td>';
	echo $row['id_familiar']. ' / ';
	echo $row['cedula']. ' / ';
	$ced=$row['cedula'];
	$posicion = $row['id_familiar'];
	$sql2="select * from obreros where cedula='$ced'";
	$filas=mysql_query($sql2);
	$fila=mysql_fetch_assoc($filas);
	echo $fila['ape_nom'].'-'.$row['ape_nomb'];
	if ($ultima == $row['cedula']) $continua++; else $continua=1;
	$sigue=trim($row['cedula']).$continua;
	$comando="update familiar set cedulafam = '$sigue' where id_familiar='$posicion'";
	echo $comando;
	echo ' / '.$sigue;
	$ultima=$row['cedula'];
	$rss=mysql_query($comando);
	echo '</td>';
}
echo '</tr></table>';

?>
