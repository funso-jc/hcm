<?php

//Copyright (C) 2000-2006  Antonio Grandío Botella http://www.antoniograndio.com
//Copyright (C) 2000-2006  Inmaculada Echarri San Adrián http://www.inmaecharri.com

//This file is part of Catwin.

//CatWin is free software; you can redistribute it and/or modify
//it under the terms of the GNU General Public License as published by
//the Free Software Foundation; either version 2 of the License, or
//(at your option) any later version.

//CatWin is distributed in the hope that it will be useful,
//but WITHOUT ANY WARRANTY; without even the implied warranty of
//MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//GNU General Public License for more details:
//http://www.gnu.org/copyleft/gpl.html

//You should have received a copy of the GNU General Public License
//along with Catwin Net; if not, write to the Free Software
//Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

include("head.php");
include("paginar.php");

if (!$link OR !$_SESSION['empresa']) {
	include("noempresa.php");
	exit;
}
?>

<body <?php if($accion == "Anadir" OR $accion == "Editar") {echo "onload=\"foco('nombre')\"";}?>>

<?php
include("arriba.php");
$menu12=2;include("menusizda.php");

$codigo = $_GET['codigo'];
// echo $accion. ' - ' .$codigo.'-'.$nombre.'-'.$saldoi;
if ($accion == 'Anadir1') {
	extract($_POST);
	$codigo = $_POST['codigo'];
	if ($codigo) {
		$fecha=date('Y-m-d H:i:s');
		$sql="INSERT INTO instituto (codmed, instituto, msas, colegio, codesp, tipo, status, costo1, costo2, costo3, costo4, costo5, costo6) VALUES ('$codigo', '$nombre', '$msas', '$colegio', '$especialidad', '$clasifica', '1', '$costo1', '$costo2', '$costo3', '$costo4', '$costo5', '$costo6')" ;
// 		echo $sql;
		if (! mysql_query($sql))
			die ("<p />El usuario $usuario no tiene permisos para añadir institutos o ID ya existente.");
		else echo 'Instituto agregado satisfactoriamente...<br>';
			 // nivel != 0
	}
		$accion="";
}	// accion

if ($accion == 'Editar1') {
	extract($_POST);
	$codigo = $_POST['registro'];
	$num = 1;
	$sql="UPDATE instituto SET instituto = '$nombre', msas ='$msas', colegio = '$colegio',
	codesp = '$especialidad', tipo='$clasifica', costo1='$costo1', costo2='$costo2',
	costo3='$costo3',  costo4='$costo4', costo5='$costo5', costo6='$costo6', 
	costo7='$costo7',  costo8='$costo8', costo9='$costo9', costo10='$costo10', 
	costo11='$costo11',  costo12='$costo12', costo13='$costo13', costo14='$costo14', 
	costo15='$costo15',  costo16='$costo16', costo17='$costo17', costo18='$costo18', 
 	costo19='$costo19',  costo20='$costo20', costo21='$costo21', costo22='$costo22', 
 	costo23='$costo23',  costo24='$costo24', costo25='$costo25', costo26='$costo26', 
 	costo27='$costo27',  costo28='$costo28', costo29='$costo29', costo30='$costo30'
	WHERE codmed= '$codigo' " ;
		mysql_query($sql) or die ("<p />El usuario $usuario no tiene permisos para modificar institutos");
}

if ($accion == 'Borrar') {
	extract($_POST);
	$registro = $_POST['registro'];
	$sql="DELETE FROM instituto WHERE codmed= '$registro'";
	mysql_query($sql) or die ("<p />El usuario $usuario no tiene permisos para borrar institutos");
	$accion="";

}

// <table class='basica 100 hover' width='100%'>
?>

<div id='div1'>

<table class='basica 100 hover' width='100%'>
<tr>
<?php
// <th>Registro</th><th>Nombre<br />[ <a href='regitems.php?accion=Anadir'>Añadir Instituto</a> ]</th></tr>
	echo '<th><a href=?ord=cod_med>R.IF.</a></th><th><a href=?ord=instituto>Nombre / Institución</a>';
	echo '[ <a href="regitems.php?accion=Anadir">           Nuevo Institución</a> ]</th>';
	echo '<th>Especialidad</th><th>Clasificación</th></tr>';

$ord='';
$conta = $_GET['conta'];
if (!$_GET['conta']) {
	$conta = 1;
}

/*
$m = microtime();
$comienzo = explode(" ", $m);
*/
/* MANERA 1 
$sql = "SELECT cue_codigo FROM sgcaf810 ORDER BY cue_codigo";
$rs = mysql_query($sql);
$numasi = mysql_num_rows($rs);  */
/* MANERA 2 */
$sql = "SELECT COUNT(codmed) AS cuantos FROM instituto" ;
$rs = mysql_query($sql);
$row= mysql_fetch_array($rs);
$numasi = $row[cuantos]; 

/*
$final = explode(" ", microtime());
$tiempo = ($final[1] + $final[0]) - ($comienzo[1] - $comienzo[0]); 
echo "Esta página fue generada en $tiempo segundos";
*/

$sql = "SELECT * FROM instituto, especialidad where codesp=especialidad.codigo ORDER BY instituto ";
$rs = mysql_query($sql." LIMIT ".($conta-1).", 20");

if (pagina($numasi, $conta, 20, "Institutos", $ord)) {$fin = 1;}


// bucle de listado

/*
while($row=mysql_fetch_array($rs)) {
	echo "<tr>";
//	echo "<td class='centro'>";
//	echo $row['registro'];
//	echo "</td>";
	echo "<td><a href='regitems.php?accion=Editar&registro=".$row['codmed']."'>";
	echo $row['codmed']."</a>";
	echo "</td><td>".$row['nombre']."</td>";
	echo "</tr>";
*/
	while($row=mysql_fetch_array($rs)) {
		echo "<tr>";
		echo "<td class='centro'>";
		echo "<a href='regitems.php?accion=Editar&rif=".$row['codmed']."'>";
		echo $row['codmed']."</a></td>";
		echo "<td class='centro'>";
		echo "<a href='regitems.php?accion=Editar&rif=".$row['codmed']."'>";
		echo trim($row['instituto']). "</a></td>";
		echo "<td class='centro'>";
		echo trim($row['nombre']). "</td>";
		echo "<td class='centro'>";
		if (trim($row['tipo']) == 1)
			echo ' Especialista ';
		else if (trim($row['tipo']) == 2)
			echo ' Laboratorio ';
		else if (trim($row['tipo']) == 3)
			echo ' Hospitalización ';
		else if (trim($row['tipo']) == 4)
			echo 'Farmacia' ;
		else echo 'No definido';
			
		echo "</td>";
		echo "</tr>";

}

echo "</table>";

pagina($numasi, $conta, 10, "Institutos", $ord);

?>

</div><div id='div2'>

<?php

if ($accion == "Anadir2") {
	extract($_POST);
	$codigo = $_POST['codigo'];
	if ($codigo) {
		$sql="SELECT * FROM instituto WHERE codmed = '$codigo'";
		$rs=mysql_query($sql) or die ("<p />El usuario $usuario no tiene permisos para modificar institutos");
		$fila = mysql_fetch_array($rs);
		if (mysql_num_rows($rs) > 0) {
			echo "<label>Nombre</label><br /><input type = 'text' value ='".$fila['nombre']."' size='40' maxlength='100' name='nombre' readonly='readonly'><br />";
			echo "<label>MSAS</label><br /><input type = 'text' value ='".$fila['msas']."' size='10' maxlength='10' name='msas' readonly='readonly'><br />";
			echo "<label>Colegio</label><br /><input type = 'text' value ='".$fila['colegio']."' size='10' maxlength='10' name='colegio' readonly='readonly'><br />";
		echo '<select name="clasifica" size="1">';
		echo '<option value="1">Especialista</option>'; 
		echo '<option value="2">Laboratorio</option>'; 
		echo '<option value="3">Hospitalizacion</option>'; 
		echo '<option value="4">Farmacia</option>'; 
		echo '</select> <br>'; 
/*

		$sql="select sugerido from textosug where tipo='Costos'";
		$resultado=mysql_query($sql);
		$valor=0;
		while ($fila2 = mysql_fetch_assoc($resultado)) {
			$valor++;
			echo "<label>".$fila2['sugerido']."      </label>";
			echo "<input type = 'text' value ='".$fila['costo'.$valor]."' size='10' maxlength='10' name='costo".$valor."'><br />";
		}
*/
		}
	else {
		echo "<form action='regitems.php?accion=Anadir1' name='form1' method='post' onsubmit='return formcliente(form1)'>";		
		echo '<input type="hidden" name = "codigo" value ="'.$codigo.'">';		 
//		echo "<label>Código de Cuenta</label><br /><input type = 'text' size='40' maxlength='40' name='codigo'><br />";
		echo "<label>Nombre</label><br /><input type = 'text' size='40' maxlength='100' name='nombre'><br />";
		echo "<label>MSAS</label><br /><input type = 'text' size='10' maxlength='10' name='msas'><br />";
		echo "<label>Colegio</label><br /><input type = 'text' size='10' maxlength='10' name='colegio'><br />";

		echo '<select name="clasifica" size="1">';
		echo '<option value="1">Especialista</option>'; 
		echo '<option value="2">Laboratorio</option>'; 
		echo '<option value="3">Hospitalizacion</option>'; 
		echo '<option value="4">Farmacia</option>'; 
		echo '</select> '; 

		$sql="select * from especialidad order by nombre";
		$resultado=mysql_query($sql);
		echo '<select name="especialidad" size="1">';
		while ($fila2 = mysql_fetch_assoc($resultado)) {
			echo '<option value="'.$fila2['codigo'].'">'.$fila2['codigo'].' - '.$fila2['nombre'].'</option>'; }
		echo '</select> '; 
		echo '<label><h1>Recuerde modificar para colocar los costos</h1>';
		echo "<input type = 'submit' value = 'Añadir'>";
		echo "</form>\n";
		}
	}
}
if ($accion == "Anadir") {
/* readonly='readonly' */
	echo "<form action='regitems.php?accion=Anadir2' name='form1' method='post' onsubmit='return formclicodigo(form1)'>";
	echo "<label>RIF </label><br /><input type = 'text' size='20' maxlength='20' name='codigo'><br />";
	echo "<input type = 'submit' value = 'Añadir'>";
	echo "</form>\n";

}

if ($accion == "Editar") {
	$registro = $_GET['rif'];
	$sql="SELECT * FROM instituto WHERE codmed = '$registro'" ;
//	echo $sql;
	$result = mysql_query($sql); 
	$fila = mysql_fetch_assoc($result);
	$temp = "";
	echo "<form action='regitems.php?accion=Editar1' name='form1' method='post' onsubmit='return formcliente(form1)'>";
	echo "<input type = 'hidden' value ='".$fila['codmed']."' name='registro'>";
	echo "<label>Nombre</label><br /><input type = 'text' value ='".$fila['instituto']."' size='40' maxlength='100' name='nombre'><br />";
	echo "<label>MSAS</label><br /><input type = 'text' value ='".$fila['msas']."' size='10' maxlength='10' name='msas'><br />";

		echo '<select name="clasifica" size="1">';
		echo '<option value="1">Especialista</option>'; 
		echo '<option value="2">Laboratorio</option>'; 
		echo '<option value="3">Hospitalizacion</option>'; 
		echo '<option value="4">Farmacia</option>'; 
		echo '</select> '; 

		$sql="select * from especialidad order by nombre";
		$resultado=mysql_query($sql);
		echo '<select name="especialidad" size="1">';
		while ($fila2 = mysql_fetch_assoc($resultado)) {
			echo '<option value="'.$fila2['codigo'].'">'.$fila2['codigo'].' - '.$fila2['nombre'].'</option>'; }
		echo '</select> '; 
//
		if ($fila['tipo']!=2) {
			$sql="select sugerido from textosug where tipo='Costos'";
			$resultado=mysql_query($sql);
			$valor=0;
			while ($fila2 = mysql_fetch_assoc($resultado)) {
				$valor++;
				echo "<label>".$fila2['sugerido']."      </label>";
				echo "<input type = 'text' value ='".$fila['costo'.$valor]."' size='10' maxlength='10' name='costo".$valor."'><br />";
			}
		}
		else {
			$sql="select sugerido from textosug where tipo='CostosL'";
			$resultado=mysql_query($sql);
			$valor=0;
			while ($fila2 = mysql_fetch_assoc($resultado)) {
				$valor++;
				echo "<label>".$fila2['sugerido']."      </label>";
				echo "<input type = 'text' value ='".$fila['costo'.$valor]."' size='10' maxlength='10' name='costo".$valor."'><br />";
			}
		}
		

//
	echo "<input type = 'submit' value = 'Confirmar cambios'></form>\n";
	if (!$temp) {
		echo "<p /><form action='regitems.php?accion=Borrar' name='form2' method='post'>\n";
		echo "<input type = 'hidden' value ='".$fila['codmed']."' name='registro'>";
		echo "<input type='hidden' name='codigo' value=".$codigo.">\n";
		echo "<input type='submit' value='Borrar Instituto' onclick='return borrar_Cliente()'></form>\n";
	}

}

?>

</div>

<?php include("pie.php");?></body></html>
