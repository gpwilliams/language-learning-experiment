<?php

	session_start();
	if (!isset($_SESSION['allp_demo']['exists'])) {
		die();
	}

	// get completion code
	$code = $_SESSION['allp_demo']['completion_code'];
	$results = (1 - ($_SESSION['allp_demo']['correct_items']/$_SESSION['allp_demo']['final_number_of_items']))*100;
	
	
	printf("<p><b>We estimate that overall you achieved %.2f%% mastery in the language.</b></p>"
				, $results); 
?>