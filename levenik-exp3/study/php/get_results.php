<?php

	// Get results for ending screen and print them

	session_start();
	if (!isset($_SESSION['exists'])) {
		die();
	}

	require_once 'database_connection.php';
    // Create connection
    $db = new Db();
	
	// Calculate overall performance on writing task
	$results = "";
	$score = 0;
	$sql = "SELECT edit_distance FROM writing_task WHERE session_number=" . $db->quote($_SESSION['session_number']) . " AND section='W_TEST';";
	$result_rows = $db->select($sql);
	if ($result_rows->num_rows > 0) {
		while ($row = $result_rows->fetch_assoc()) {
			$score += $row["edit_distance"];
		}
		$results = ( 1 - ($score/$result_rows->num_rows) )*100;
	} else {
		$results = 'unknown';	
	}
	
	// Get completion code from database
	$code = "";
	$sql = "SELECT completion_code FROM sessions WHERE session_number=" . $db->quote($_SESSION['session_number']) . ";";
	$result_rows = $db->select($sql);
	if ($result_rows->num_rows > 0) {
		while ($row = $result_rows->fetch_assoc()) {
			$code = $row["completion_code"];
		}
	} else {

	}
	
	// Print results and completion code to user
	printf("<p><b>We estimate that overall you achieved %.2f%% mastery in the language.</b></p>"
			. "<p><b>Your completion code is: " . $code . "</b></p>"
	
			,$results); 
?>