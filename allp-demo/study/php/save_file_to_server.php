<?php

	// Save the POST-ed file to the server
	// Based on: http://stackoverflow.com/questions/19015555/pass-blob-through-ajax-to-generate-a-file
	// Create a new participant ID if this is a new session and create a new folder
	session_start();
	if (!isset($_SESSION['allp_demo']['exists'])) {	
		die();
	}
	
	// upload wav
	if(isset($_FILES['file']) and !$_FILES['file']['error']) {
		
		$session = $_SESSION['allp_demo']['session_number'];
		$word_id =  $_SESSION['allp_demo']['section_set'][intval($_SESSION['allp_demo']['item_order'])-1];
		
		// exposures count
		if (isset($_SESSION['allp_demo']['exposure_counts'])) {
			$_SESSION['allp_demo']['exposure_counts'][$word_id-1]++;
		}
		
		// update item order
		$_SESSION['allp_demo']['item_order']++;
		$_SESSION['allp_demo']['session_item_order']++;
		
		echo $_SESSION['allp_demo']['state'];
		

	} else {
		//echo 'You need to post a blob to see this page.';
	}	
	
?>