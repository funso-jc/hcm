<?php 

$remote_host = $_SERVER['REMOTE_HOST'];
if (!$remote_host) {$remote_host = $_SERVER['REMOTE_ADDR'];}
if (!$remote_host) {$remote_host = $_SERVER['HTTP_CLIENT_IP'];}
$request_method = $_SERVER['REQUEST_METHOD'];
$request_uri = $_SERVER['REQUEST_URI'];
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$date = date( "d/M/Y:H:i:s"); 

$log = "$remote_host [$date] (".$_SESSION['empresa']."/".$_SESSION['usuario'].") $request_method $request_uri\n"; 

if($f = @fopen("log.txt","a")) { 
	fputs($f, $log); 
	fclose($f); 
} 

?> 

