<?php
include("head.php");
include("paginar.php");
if (!$link OR !$_SESSION['empresa']) {
	include("noempresa.php");
	exit;
}
$mostrarregresar=0;
?>
<script src="ajaxpr2.js" type="text/javascript"></script>
<?
if ($accion == 'Anadir') 
	$onload="onload=\"foco('lacedula')\"";
else
	if ($accion =='EscogeRetiro')
		$onload="onload=\"foco('ret_socio')\"";
	else 
		if ($accion == 'Buscar') 
			$onload="onload=\"foco('elretiro')\""; 
		else $onload="onload=\"foco('cedula')\"";
?>
<body <?php if (!$bloqueo) {echo $onload;}?>>

<?php
$readonly=" readonly='readonly'";
include("arriba.php");
$menu61=1;include("menusizda.php");
$cedula = $_GET['cedula'];
$ip = $_SERVER['HTTP_CLIENT_IP'];
if (!$ip) {$ip = $_SERVER['REMOTE_ADDR'];}


if ($accion == "Verificar") {	
	$lafactura=$_POST['factura'];
	$sql="select * from confarm where factura = '$lafactura' ";
	$resultado=mysql_query($sql);
	if (mysql_num_rows($resultado) > 0)
		echo "<h1>El numero de factura ha sido registrado previamente</h1>";
	else $accion = "Registrar";
}
if ($accion == "VerFactura") {	
	$lafactura=$_GET['factura'];
	$lafoto='fotos/'.$lafactura.'.jpg';
	echo "<br><br><img src='".$lafoto."' width='156' height='156' border='0' />";
}
if ($accion == "Registrar") {	
	$ip = $_SERVER['HTTP_CLIENT_IP'];
	if (!$ip) {$ip = $_SERVER['REMOTE_ADDR'];}
	echo "Creando registro de consumo <strong>". $_POST['factura'] ."</strong><br>";
	$cedula=$_POST['cedula'];
	$fecha=$_POST['fecha'];
	$monto=$_POST['monto'];
	$factura=$_POST['factura'];
	$farmacia=$_POST['farmacia'];
	$hoy = date("Y-m-d");
	$a=explode("/",$fecha); 
	$b=$a[2]."-".$a[1]."-".$a[0];
	
	$sql="insert into confarm (cedula, fecha, monto, factura, fregistro, codfar, ip, forma) values ('$cedula', '$b', '$monto','$factura','$hoy', '$farmacia', '$ip', 'Farmacia')";
//	echo $sql;
	$resultado=mysql_query($sql);	
	$accion="Buscar";

}
//----------------------------
if ($accion == 'Buscar')  {
	extract($_POST);
	$lacedula = trim($_POST['cedula']);
	if (! $cedula) {
		$lacedula = $_SESSION['cedulasesion']; 
		}
	else 
		$_SESSION['cedulasesion']=$_POST['cedula'];
	if ($lacedula) { //  != ' ') {
		$sql="SELECT * FROM obreros where cedula = '$lacedula'";
		$result=mysql_query($sql);
		$row= mysql_fetch_assoc($result);
		echo "<input type = 'hidden' value ='".$row['cedula']."' name='cedula'>"; 
		$cedula=$row['cedula'];
		$accion = 'Editar'; 

	echo '<div id="div1">';
	$sql="SELECT * FROM obreros WHERE cedula = '$cedula'";
	$result = mysql_query($sql) or die ('Error 200-1 <br>'.$sql.'<br>'.mysql_error());
	echo "<input type = 'hidden' value ='".$cedula."' name='cedula'>";
	$temp = "";
	echo "<form enctype='multipart/form-data' action='regfar.php?accion=Verificar' name='form1' method='post' onsubmit='return valsoc(form1)'>";
	pantalla_prestamo($result,$cedula);
	$elstatus=$_SESSION['elstatus'];

		$conta = $_GET['conta'];
		if (!$_GET['conta']) 
			$conta = 1;
		$estacedula=$row['cedula'];
		$sql = "SELECT cedula, substr(fecha,1,4) as ano, sum(monto) as subtotal, count(cedula) as cuantos FROM confarm, instituto WHERE (cedula = '$estacedula') and (codfar = codmed) and (forma = 'Farmacia') GROUP BY ano ORDER BY fecha DESC ";
		$rs = mysql_query($sql);
		$suma=0;
		echo "<table class='basica 100 hover' width='400'><tr>";
		echo '<th width="80" colspan="1"></th><th width="80">A�o</th><th width="100">Nro. de Facturas</th><th width="100">SubTotal</th></tr>';

//		if (pagina($numasi, $conta, 20, "Resumen de Consumos de Farmacia", $ord)) {$fin = 1;}
// 		bucle de listado
		while($row=mysql_fetch_assoc($rs)) {
			echo "<tr>";

		echo "<td class='centro'><a href='regfar.php?ano=".trim($row['ano'])."&accion=ConsultaAno&cedula=".trim($row['cedula'])."&'><img src='imagenes/page_wizard.gif' width='16' height='16' border='0' title='Ver Detalle' alt='Ver Detalle' /></a></td>";
		echo "<td class='centro'>";
		echo $row['ano']."</td>";
		echo "</td>";
		echo "<td align='right'>".number_format($row['cuantos'],0,'.',',')."</td>";
		echo "<td align='right'>";
		echo number_format($row['subtotal'],2,'.',',');
		$suma+=$row['subtotal'];
		echo "</tr>";
		}
		echo "<tr><td colspan='3' align='right'>";
		echo 'SubTotal'."</td>";
		echo "<td align='right'>";
		echo number_format($suma,2,'.',',');
		echo "</tr>";
		
		echo "</table>";

	}
}	// fin de ($accion == 'Buscar') 
		
if ($accion == "ConsultaAno") {	
	$ano=$_GET['ano'];
	$estacedula=$_GET['cedula'];
		$sql = "SELECT * FROM confarm, instituto WHERE (cedula = '$estacedula') and (codfar = codmed) and (substr(fecha,1,4)='$ano') and (forma = 'Farmacia') ORDER BY fecha DESC ";
//		echo $sql;
		$rs = mysql_query($sql);
		echo "<table class='basica 100 hover' width='750'><tr>";
		echo '<th colspan="1"></th><th width="80">Fecha</th><th width="100">Nro.Factura</th><th width="280">Farmacia</th><th width="100">Monto</th><th width="100">Registrado el</th></tr>';

//		if (pagina($numasi, $conta, 20, "Consumos de Farmacia", $ord)) {$fin = 1;}
// 		bucle de listado
		$suma=0;
		while($row=mysql_fetch_assoc($rs)) {
			echo "<tr>";

			echo "<td class='centro'><a href='regfar.php?factura=".trim($row['factura'])."&accion=VerFactura'><img src='imagenes/page_wizard.gif' width='16' height='16' border='0' title='Ver Factura' alt='Ver Factura' /></a></td>";
			echo "<td class='centro'>";
			echo convertir_fechadmy($row['fecha'])."</td>";
			echo "<td class='centro'>";
			echo $row['factura'];
			echo "</td>";
			echo "<td class='centro'>".$row['instituto']."</td>";
			echo "<td align='right'>";
			echo number_format($row['monto'],2,'.',',');
			$suma+=$row['monto'];
			echo "</td>";
			echo "<td align='right'>".convertir_fechadmy($row['fregistro'])."</td>";
			echo "</tr>";
		}
		echo "<tr><td colspan='4' align='right'>";
		echo 'SubTotal'."</td>";
		echo "<td align='right'>";
		echo number_format($suma,2,'.',',');
		echo "</tr>";
	echo "</table>";
}

if (!$accion) {
	echo "<form action='regfar.php?accion=Buscar' name='form1' method='post'>";
    echo '  C&eacute;dula ';
	echo '<input name="cedula" type="text" id="cedula" value=""  size="10" maxlength="10" />';
	echo "<input type = 'submit' value = 'Buscar'>";
	$_SESSION['numeroarenovar']='';
	$_SESSION['cedulasesion']=''; 
	echo '</form>';
}	// fin de (!$accion) 
if ($accion == 'Ver') {
	echo "<div align='center' id='div1'>";
	$mostrarregresar=1;
	$cedula=$_GET['cedula'];
	$nropre=$_GET['nropre'];
	mostrar_prestamo($cedula,$nropre);
	echo "</div>";
}	// fin de ($accion == 'Ver')

if ($accion == "Editar") {	// muestra datos para prestamo
/*
	echo '<div id="div1">';
	$sql="SELECT * FROM obreros WHERE cedula = '$cedula'";
	$result = mysql_query($sql) or die ('Error 200-1 <br>'.$sql.'<br>'.mysql_error());
	echo "<input type = 'hidden' value ='".$cedula."' name='cedula'>";
	$temp = "";
	echo "<form enctype='multipart/form-data' action='regfar.php?accion=Verificar' name='form1' method='post' onsubmit='return valsoc(form1)'>";
	pantalla_prestamo($result,$cedula);
*/
	$elstatus=$_SESSION['elstatus'];
	echo '<fieldset><legend>Informaci�n para Registro </legend>';
	echo '<td>Seleccione Tipo</td>';
   	echo '<td class="rojo">';
	echo "<input type = 'hidden' value ='".$cedula."' name='cedula'>";
	echo '<select name="farmacia" size="1">';
	$sql="select * from instituto where tipo=3 and status=1 order by instituto";
	$resultado=mysql_query($sql);
	while ($fila2 = mysql_fetch_assoc($resultado)) {
		echo '<option value="'.$fila2['codmed'].'">'.$fila2['codmed'].' - '.$fila2['instituto'].'</option>'; }
	echo '</select> *'; 
	echo '</td>';
	echo 'Fecha Factura';
	$fecha=date("d")."/".date('m')."/".date("Y"); 
	$hoy = date("d/m/Y");
	$hoy1 = mktime(0,0,0,date("m"),date("d"),date("Y")); 
	$h = date("d/m/Y",$hoy1);
	$mas = $hoy1+7257600;  
	$meses = date("d/m/Y",$mas); 
	escribe_formulario(fecha, form1.fecha, 'd/m/yyyy', $fecha, '', $meses, '0', '10'); 
	echo "<br>";
	echo "Monto: ";
	echo "<input type = 'text' size='12' maxlength='12' name='monto' tabindex='3' value ='0.00'>";
	echo " # Factura: ";
	echo "<input type = 'text' size='12' maxlength='12' name='factura' tabindex='4' value =''>";
	echo "<br><input type = 'submit' value = 'Registrar'></form>\n"; 
	echo '</fieldset>';
	echo '</div>';
} 	// fin de ($accion == "Editar")
if ($mostrarregresar==1) { // ($accion == "Buscar") or ($accion == "Ver") or ($accion="EscogePrestamo")) {
	echo '<form enctype="multipart/form-data" name="formdepie" method="post" action="regfar.php?accion=Buscar">';
	echo '<input type = "hidden" value ="'.$_SESSION['cedulasesion'].'" name="cedula" id="cedula">';
// 	echo 'la cedula '.$_SESSION['cedulasesion'];
	echo '<div style="clear:both"></div>';
	echo '<p /><div class="noimpri" style="clear:both;text-align:center">';
	echo '<input type="submit" name="boton" value="regresar" tabindex="3">';
	echo '</div>';
	echo '</form>';
}
else 
	include("pie.php");
?>
</body></html>


<?php
//----------------------------------------------
function pantalla_prestamo($result,$cedula)
{
	$deci=$_SESSION['deci'];
	$sep_decimal=$_SESSION['sep_decimal'];
	$sep_miles=$_SESSION['sep_miles'];
	$fila = mysql_fetch_assoc($result);
	echo "<input type = 'hidden' value ='".$fila['ced_prof']."' name='cedula'>";
	if ($accion == 'Editar') { $lectura = 'readonly = "readonly"'; $activada="disabled" ; } else {$lectura=''; $activada='';}
	if ($accion == 'Anadir') {
		$elcodigo=nuevo_codigo(); 
		$ingreso=date("d/m/Y", time());
		}
	else  $elcodigo=$fila['cod_prof'];
	$lectura = 'readonly = "readonly"'; $activada="disabled" ; 
//	<form id="form1" name="form1" method="post" action="">
?>
  <label><fieldset><legend>Informaci�n Personal </legend>
  <table width="639" border="1">
    <tr>
 		<td colspan="1" width="130">C�dula <?php echo '<strong>'.$fila['cedula'].'</strong>';?></td>
		<td colspan="3" width="127">Socio <?php echo '<strong>'.$fila['ape_nom'] .'</strong>'?></td>
	</tr>
</table>
</fieldset>�

<?php
}
/*
CREATE TABLE `confarm` (
`cedula` VARCHAR( 20 ) NOT NULL ,
`fecha` DATE NOT NULL ,
`monto` DECIMAL( 12, 2 ) NOT NULL ,
`factura` VARCHAR( 10 ) NOT NULL ,
`fregistro` DATE NOT NULL ,
`ip` VARCHAR( 100 ) NOT NULL ,
`registro` BIGINT NOT NULL ,
PRIMARY KEY ( `registro` ) ,
INDEX ( `cedula` )
) ENGINE = MYISAM ;
*/
?>
