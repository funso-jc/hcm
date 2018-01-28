<?php
/*  
     This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
include("head.php");
if (!$link OR !$_SESSION['empresa']) {
	include("noempresa.php");
	exit;
}
	*/ 
session_start();
extract($_GET);
extract($_POST);
extract($_SESSION);
include("conex.php");

if (!$link OR !$_SESSION['empresa']) {
    include("head.php");
	//header("location: noempresa.php");
	exit;
}

define('FPDF_FONTPATH','fpdf/font/');
require('fpdf/mysql_table.php');
include("fpdf/comunes.php");
require('funciones.php');

$desde=$_GET['desde'];
$hasta=$_GET['hasta'];

$sql="select *, date_format(fecha,'%Y') AS anoretiro from confarm, obreros where (forma = 'HCM') and (fecha >= '$desde' and fecha <= '$hasta') and obreros.cedula = confarm.cedula order by anoretiro, fecha";//   limit 30"; //  limit 20";
// echo $sql;
$asocio=mysql_query($sql);
$columna=3;
$rpl=300; 	// registros por listado
$crl=0;		// contador de registros por listado
$col_listado=0;
$header[0]='Fecha';
$header[1]='Cedula';
$header[2]='Apellidos y Nombres';
$header[3]='Beneficiario';
$header[4]='Concepto';
$header[5]='Institucion';
$header[6]='Monto';
$header[7]='Factura';
$alto=3;
$salto=$alto;
$w=array(8,20,40,40,20,50,20,20); // ,25,25,25,25,25,25);
$p[0]=20;
for ($posicion=1;$posicion<count($w);$posicion++) 
	$p[$posicion]=$p[$posicion-1]+$w[$posicion-1];

$pdf=new PDF('L','mm','Letter');
$pdf->Open();
// $registros=mysql_num_rows($a_amor);
// set_time_limit($registros);
$sintitulo=false;
$primeravez = true;
$activos = $jubilados = 0;
$rsocio = mysql_fetch_assoc($asocio);
$fecha=$rsocio[anoretiro]; // explode('-',$rsocio[fechareti]);
$ano=$fecha[0];
$linea=encabeza_l_prestamos($header,$w,$p,$pdf,$salto,$alto,$ano,$desde,$hasta);
$paso=0;
$ttsocio = $ttucla = 0;
$tasocio = $taucla = 0;
$ultimoano = $fecha;
mysql_data_seek ($asocio, 0);		// volver al principio de la busqueda
while ($rsocio = mysql_fetch_assoc($asocio)){
	$linea+=$salto;
	$pdf->SetY($linea);
	$cont++;
//	$fecha=explode('-',$rsocio[fechareti]);
	$ano=$rsocio[anoretiro]; // explode('-',$rsocio[fechareti]);
	if ($fecha==$ano) {
		if ($paso == 0) {
			$paso = 1;
			$linea+=$alto;
			$pdf->SetY($linea);
			$pdf->SetFont('Arial','B',10);
			$pdf->SetX($p[0]);
			$lafecha=$rsocio[anoretiro]; // explode('-',$rsocio['fechareti']);
			$pdf->Cell(0,$alto,'Año '.$lafecha,0,0,'L',0);
			$pdf->SetFont('Arial','',7);
			$linea+=$alto;
			$pdf->SetY($linea);
			$pdf->SetX($p[1]);
			imprimir($p,$w,$alto,$rsocio,$pdf,$ttsocio,$ttucla,$tasocio,$taucla);
		}
		else {
			imprimir($p,$w,$alto,$rsocio,$pdf,$ttsocio,$ttucla,$tasocio,$taucla);
		}
	}
	else 
	{
	if ($linea>=160) {
		$linea+=$alto;
		$pdf->SetY($linea);
		$pdf->SetX($p[0]);
		$pdf->Cell(0,0,'  ',1,0,'L',0);
		$linea=encabeza_l_prestamos($header,$w,$p,$pdf,$salto,$alto,$fecha,$desde,$hasta);
		$sintitulo=false;
		$linea-=$alto;
		}
		$linea+=$alto;
		$pdf->SetY($linea);
		$pdf->SetFont('Arial','B',7);
		$pdf->SetX($p[5]);		$pdf->Cell($w[5],$alto,'Total Año '.$ultimoano,0,0,'R');
		$pdf->SetX($p[6]);		$pdf->Cell($w[6],$alto,number_format($tasocio,0,'.',','),0,0,'R');
//		$pdf->SetX($p[6]);		$pdf->Cell($w[6],$alto,number_format($taucla,2,'.',','),0,0,'R');
		$pdf->SetFont('Arial','',7);
		$fecha=$rsocio[anoretiro]; // explode('-',$rsocio[fechareti]);
		$ultimoano = $fecha;
			
		$tasocio = $taucla = 0;

		$linea+=$alto;
		$pdf->SetY($linea);
		$pdf->SetFont('Arial','B',10);
		$pdf->SetX($p[0]);
//		$lafecha=explode('-',$rsocio['anoretiro']);
		$lafecha=$rsocio['anoretiro'];
		$pdf->Cell(0,$alto,'Año '.$lafecha,0,0,'L',0);
		$pdf->SetFont('Arial','',7);
		$linea+=$salto;
		$pdf->SetY($linea);
		$fecha=$rsocio[anoretiro]; // explode('-',$rsocio[fechareti]);

		$pdf->SetFont('Arial','',7);
		$linea+=$salto;
		$pdf->SetY($linea);
		imprimir($p,$w,$alto,$rsocio,$pdf,$ttsocio,$ttucla,$tasocio,$taucla);
	}
	if ($linea>=160) {
		$linea+=$alto;
		$pdf->SetY($linea);
		$pdf->SetX($p[0]);
		$pdf->Cell(0,0,'  ',1,0,'L',0);
		$linea=encabeza_l_prestamos($header,$w,$p,$pdf,$salto,$alto,$fecha,$desde,$hasta);
		$sintitulo=false;
		$linea-=$alto;
		}
}
		$linea+=$alto;
		$pdf->SetY($linea);
		$pdf->SetFont('Arial','B',7);
		$pdf->SetX($p[5]);		$pdf->Cell($w[5],$alto,'Total Año '.$ultimoano,0,0,'R');
		$pdf->SetX($p[6]);		$pdf->Cell($w[6],$alto,number_format($tasocio,0,'.',','),0,0,'R');
//		$pdf->SetX($p[6]);		$pdf->Cell($w[6],$alto,number_format($ttucla,2,'.',','),0,0,'R');
		$pdf->SetFont('Arial','',7);
			
//		$ttsocio = $ttucla = 0;

		$linea+=$alto;
		$pdf->SetY($linea);
		$pdf->SetFont('Arial','B',7);
		$pdf->SetX($p[5]);		$pdf->Cell($w[5],$alto,'Total Socios ',0,0,'R');
		$pdf->SetX($p[6]);		$pdf->Cell($w[6],$alto,number_format($ttsocio,0,'.',','),0,0,'R');
//		$pdf->SetX($p[6]);		$pdf->Cell($w[6],$alto,number_format($taucla,2,'.',','),0,0,'R');
		$pdf->SetFont('Arial','',7);
		$ultimoano = $fecha;

/*
$linea+=$alto;
$pdf->SetY($linea);
$pdf->SetX($p[0]);
$pdf->Cell(0,0,'  ',1,0,'L',0);
$linea+=$alto;
$pdf->SetY($linea);
$pdf->SetX($p[3]);		$pdf->Cell($w[3]+$w[4],$alto,'Total Socios Activos',0,0,'R');
$pdf->SetX($p[5]);		$pdf->Cell($w[5],$alto,number_format($activos,0,'.',','),0,0,'R');
$linea+=$alto;
$pdf->SetY($linea);
$pdf->SetX($p[3]);		$pdf->Cell($w[3]+$w[4],$alto,'Total Socios Jubilados',0,0,'R');
$pdf->SetX($p[5]);		$pdf->Cell($w[5],$alto,number_format($jubilados,0,'.',','),0,0,'R');
$linea+=$alto;
$pdf->SetY($linea);
$pdf->SetFont('Arial','B',7);
$pdf->SetX($p[3]);		$pdf->Cell($w[3]+$w[4],$alto,'Total Socios',0,0,'R');
$pdf->SetX($p[5]);		$pdf->Cell($w[5],$alto,number_format($activos+$jubilados,0,'.',','),0,0,'R');
$pdf->SetFont('Arial','',7);
*/
$pdf->Output();
set_time_limit(30);
// $pdf->SetX($p[6]);		$pdf->Cell($w[6],$alto,number_format($tcuota,2,'.',','),0,0,'R');

////////////////////////////////////////////////////
function imprimir($p,$w,$alto,$rsocio,$pdf,&$ttsocio,&$ttucla,&$tasocio,&$taucla){
if ($linea>=160) {
	$linea+=$alto;
	$pdf->SetY($linea);
	$pdf->SetX($p[0]);
	$pdf->Cell(0,0,'  ',1,0,'L',0);
	$linea=encabeza_l_prestamos($header,$w,$p,$pdf,$salto,$alto,$fecha,$desde,$hasta);
	$sintitulo=false;
	$linea-=$alto;
}
$pdf->SetX($p[0]);		$pdf->Cell($w[0],$alto,convertir_fechadmy($rsocio["fecha"]),0,0,'C');
$pdf->SetX($p[1]);		$pdf->Cell($w[1],$alto,$rsocio["cedula"],0,0,'C',0); 
$ced2=$rsocio["cedula"];
$sql2="select ape_nom from obreros where cedula ='$ced2'";
$a2=mysql_query($sql2);
$rsocio2=mysql_fetch_assoc($a2);
$pdf->SetX($p[2]);		$pdf->Cell($w[2],$alto,$rsocio2["ape_nom"],0,0,'LRTB',0);  
if ($rsocio['titular'] == 0) {
	$ced2=$rsocio["cedula"];
	$ced3=$rsocio["beneficiario"];
	$sql2="select ape_nomb from familiar where cedula ='$ced2' and cedulafam='$ced3'";
	$a2=mysql_query($sql2);
	$rsocio2=mysql_fetch_assoc($a2);
	$pdf->SetX($p[3]);		$pdf->Cell($w[3],$alto,$rsocio2["ape_nomb"],0,0,'LRTB');
}
$pdf->SetX($p[4]);		$pdf->Cell($w[4],$alto,$rsocio["concepto"],0,0,'L');
$ced2=$rsocio["codfar"];
// $sql2="select instituto from instituto where codmed ='$ced2' "; // and tipo='4'";
$sql2="select descripcion from clinicas where rif ='$ced2' "; // and tipo='4'";
//echo $sql2;
$a2=mysql_query($sql2);
$rsocio2=mysql_fetch_assoc($a2);
$pdf->SetX($p[5]);		$pdf->Cell($w[5],$alto,$rsocio2["descripcion"],0,0,'L',0); 
$pdf->SetX($p[6]);		$pdf->Cell($w[6],$alto,number_format($rsocio["monto"],2,'.',','),0,0,'R');
$pdf->SetX($p[7]);		$pdf->Cell($w[7],$alto,$rsocio["factura"],0,0,'R');
$ttsocio+=$rsocio["monto"];
// $ttucla+=$rsocio["ret_ucla"];
$tasocio+=$rsocio["monto"];
// $taucla+=$rsocio["ret_ucla"];
}
////////////////////////////////////////////////////
function encabeza_l_prestamos($header,$w,$p,&$pdf,$salto,$alto,$elano,$desde,$hasta)
{
$pdf->AddPage();
$linea=25;
$pdf->SetY($linea);
$pdf->SetX(0);
$pdf->SetFont('Arial','B',14);
$hoy = date("d")."-".date('m')."-".date("Y"); 
$pdf->MultiCell(0,0,"Relacion H.C.M del ".convertir_fechadmy($desde). ' al '.convertir_fechadmy($hasta),0,C,0);
$pdf->SetY($linea);
$pdf->SetFont('Arial','',7);
$linea+=5;
$pdf->SetY($linea);
//$header=array($$arrtitulo);
//Colores, ancho de línea y fuente en negrita
$pdf->SetFillColor(200,200,200);
$pdf->SetTextColor(0);
$pdf->SetDrawColor(0,0,0);
$pdf->SetLineWidth(.2);
$pdf->SetFont('Arial','B',6);
//Cabecera
for($i=0;$i<count($w);$i++){
	$pdf->SetY($linea);
	$pdf->SetX($p[$i]);
	$pdf->Cell($w[$i],$alto,$header[$i],1,0,'C',1);
}
//Restauración de colores y fuentes
$pdf->SetFillColor(224,235,255);
$pdf->SetTextColor(0);
$pdf->SetFont('Arial','',7);
$linea+=$salto;
$linea+=$salto;
$pdf->SetY($linea);
$pdf->SetX($p[0]);
$pdf->Cell(0,0,'  ',1,0,'L',0);
return $linea;
}
?>
