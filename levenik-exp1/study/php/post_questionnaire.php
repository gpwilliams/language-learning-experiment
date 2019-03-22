<?php

	// Post questionnaire input to database

	session_start();
	if(!isset($_SESSION['exists'])) {
		die();	
	}

	require_once 'database_connection.php';
	$db = new Db();

	if(isset($_POST["fun"]) && isset($_POST["noise"])) {
		$fun = intval($db->sanitiseMySQL($_POST["fun"]));
		$noise = intval($db->sanitiseMySQL($_POST["noise"]));
		
		if(($fun != '') && ($noise != '')) {

			// upload to database
			$sql_insert = "UPDATE sessions SET fun=$fun, noise='$noise' WHERE session_number=" . $db->quote($_SESSION["session_number"]) . ";";
			if ($db->query($sql_insert) === TRUE) {

			} else {
				//echo "Error creating record: " . $db->error;	
			}
			
			// if successful go to next page
			$_SESSION['item_order']++;
		}
	}
	
	header("Location: ../");

?>