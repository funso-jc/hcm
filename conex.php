<?php

//Copyright (C) 2000-2006  Antonio Grand�o Botella http://www.antoniograndio.com
//Copyright (C) 2000-2006  Inmaculada Echarri San Adri�n http://www.inmaecharri.com

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

require("final.php");
$link = @mysql_connect($Servidor,$Usuario, $Password,'',65536) or die ("<p /><br /><p /><div style='text-align:center'>Disculpe... En estos momentos no hay conexi�n con el servidor, estamos realizando modificaciones.... int�ntalo m�s tarde. Gracias....</div>");
/*
//***********************************************************

if ($_POST['crearbd']) {include ("crearemp.php");}

//***********************************************************

if ($_GET['emp'] == 1) {
	$_POST['usuario1'] = 'administrador';
	$_POST['empresa1'] = 'nuevocat';
	$_POST['password1'] = "admin";
}

*/
if ($_POST['usuario1']) {
	$_SESSION['idempresa']= 'FUNSOUCLA';
//	echo $bdd.$_POST['empresa1'];
//	mysql_select_db($bdd.$_POST['empresa1'], $link);
//	if (mysql_select_db($bdd.$_POST['empresa1'], $link)) {
	if (mysql_select_db('smobrero', $link)) {

/*
	echo $_POST['usuario1'];
	echo $_POST['empresa1'];
	echo $_POST['password1'];

*/
// 	echo "entre 1";
//		if (!mysql_query("SELECT * FROM grupplan")) {include("plangralconta.php");}
//		$fila = mysql_fetch_array(mysql_query("SELECT * FROM usuarios WHERE usuario = '".$_POST['usuario1']."' AND clave = '".$_POST['password1']."'"));
//		$fila = mysql_fetch_array(mysql_query("SELECT * FROM sgcapass WHERE apellido = '".$_POST['usuario1']."' AND nombre = '".$_POST['password1']."'"));
		$fila = mysql_fetch_array(mysql_query("SELECT * FROM smpass WHERE alias = '".$_POST['usuario1']."' AND password = PASSWORD('".$_POST['password1']."')"));
		if (!$fila) {
//			echo "no fila";
			session_unset();session_destroy();mysql_close($link);return;
		} else {
			$_SESSION['empresa']= $_POST['empresa1'];
			$_SESSION['usuario'] = $_POST['usuario1'];
			$_SESSION['auto'] = $fila['perm'];
		}
	} else {
		session_unset();session_destroy();mysql_close($link);return;
	}
}

if ($_POST['accion'] == 'desc') {

	session_unset();
	mysql_close($link);
	return;

}

if (substr(strrchr($_SERVER['SCRIPT_NAME'], "/"), 1) == "empresa.php"){
	
	session_unset();
	return;
	
}

$sql = "ALTER TABLE `definiti` ADD `bloq` INT( 1 ) NOT NULL";
mysql_query($sql);

if (!$_SESSION['empresa']) {return;}

// mysql_select_db($bdd.$_SESSION['empresa'], $link);
// echo $_SESSION['empresa'];
mysql_select_db('smobrero',$link);

?>