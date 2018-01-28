﻿<?php
// include("head.php");
@session_start();
	
	// PHP5 Implementation - uses MySQLi.
	// mysqli('localhost', 'yourUsername', 'yourPassword', 'yourDatabase');
	require("final.php");
	$db = new mysqli($Servidor,$Usuario, $Password, $bdd);
	
	if(!$db) {
		// Show error if we cannot connect.
		echo 'ERROR: Could not connect to the database.';
	} else {
		// Is there a posted query string?
		if(isset($_POST['queryString'])) {
			$queryString = $db->real_escape_string($_POST['queryString']);
			
			// Is the string length greater than 0?
			
			if(strlen($queryString) >0) {
				// Run the query: We use LIKE '$queryString%'
				// The percentage sign is a wild-card, in my example of countries it works like this...
				// $queryString = 'Uni';
				// Returned data = 'United States, United Kindom';
				
				// YOU NEED TO ALTER THE QUERY TO MATCH YOUR DATABASE.
				// eg: SELECT yourColumnName FROM yourTable WHERE yourColumnName LIKE '$queryString%' LIMIT 10

				$filtro="SELECT concepto FROM confarm WHERE ((concepto LIKE '$queryString%') and (forma = 'HCM')) group by concepto order by concepto LIMIT 10";
				
				$query = $db->query($filtro);
// 				$query = $db->query("SELECT cue_codigo, cue_nombre FROM sgcaf810 WHERE ((cue_codigo LIKE '$queryString%') or (cue_nombre LIKE '$queryString%')) LIMIT 10");
//				$query = $db->query("SELECT cue_codigo, cue_nombre FROM sgcaf810 WHERE ((cue_codigo LIKE '$queryString%') or (cue_nombre LIKE '$queryString%')) and (cue_nivel = 6) LIMIT 10");
				
		// and (cue_nivel = ".$_SESSION['maxnivel'].")) 
				if($query) {
					// While there are results loop through them - fetching an Object (i like PHP5 btw!).
					while ($result = $query ->fetch_object()) {
						// Format the results, im using <li> for the list, you can change it.
						// The onClick function fills the textbox with the result.
						
						// YOU MUST CHANGE: $result->value to $result->your_colum
	         			echo '<li onClick="fill2(\''.$result->concepto.'\');">'.$result->concepto.'</li>';
	         		}
				} else {
					echo 'ERROR: There was a problem with the query.'.$filtro;
				}
			} else {
				// Dont do anything.
			} // There is a queryString.
		} else {
			echo 'There should be no direct access to this script!';
		}
	}
?>