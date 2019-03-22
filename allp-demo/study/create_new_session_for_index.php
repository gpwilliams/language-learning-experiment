<?php

	// Save the POST-ed file to the server
	require_once 'php/generate_alphabet.php';
	require_once 'php/generate_word_pictures.php';
	
	// Create New Session in Database
	function createNewSession() {
		// create an alphabet key
		$_SESSION['allp_demo']["alphabet_key"] = generateAlphabet();


		// Decide on training set
		
		// Randomised 12
		/*
		$random_order = array();
		// push values in array
		for ($j = 1; $j <= 42; $j++) {
			$random_order[] = $j;
		}
		shuffle($random_order);
		// remove last twelve
		for ($z = 0; $z < 12; $z++) {
			array_pop($random_order);
		}
		*/
		
		$random_order = array	(2, 11);
		shuffle($random_order);
		
		$_SESSION['allp_demo']['training_set'] = $random_order;

		// exposure counts
		$exposure_array = array();
		// push values in array
		for ($k = 0; $k < 42; $k++) {
			$exposure_array[] = 0;
		}
		
		$_SESSION['allp_demo']['exposure_counts'] = $exposure_array;
		
		// set initial state
		$_SESSION['allp_demo']['state'] = 'START';
		
		// Create a new record of the session
		// set participant ID to database session ID
		$_SESSION['allp_demo']['session_number'] = 1;
		
		// decide on conditions
		
		if (isset($_SESSION['allp_demo']["link_l"])) {
			$_SESSION['allp_demo']['language_condition'] = $_SESSION['allp_demo']["link_l"];
		} else {
			$_SESSION['allp_demo']['language_condition'] = 'standard';
		}
		
		if (isset($_SESSION['allp_demo']["link_o"])) {
			$_SESSION['allp_demo']['order_condition'] = $_SESSION['allp_demo']["link_o"];
		} else {
			$_SESSION['allp_demo']['order_condition'] = 'RW';
		}
		
		if (isset($_SESSION['allp_demo']["link_p"])) {
			$_SESSION['allp_demo']['picture_condition'] = $_SESSION['allp_demo']["link_p"];
		} else {
			$_SESSION['allp_demo']['picture_condition'] = 1;
		}
		
		if (isset($_SESSION['allp_demo']["link_s"])) {
			$_SESSION['allp_demo']['speaker_condition'] = $_SESSION['allp_demo']["link_s"];
		} else {
			$_SESSION['allp_demo']['speaker_condition'] = 'female';
		}
		
		if (isset($_SESSION['allp_demo']["link_w"])) {
			$_SESSION['allp_demo']['orthography_condition'] = $_SESSION['allp_demo']["link_w"];
		} else {
			$_SESSION['allp_demo']['orthography_condition'] = 'transparent';
		}

		// set pictures
		generateWordPictures();
				
		// start session item from 1
		$_SESSION['allp_demo']['item_order'] = 1;		
		$_SESSION['allp_demo']['item_total'] = 1;
		$_SESSION['allp_demo']['session_item_order'] = 1;
		
		// session created so it exists now
		$_SESSION['allp_demo']['exists'] = true;
		
		// correct tracking for demo
		$_SESSION['allp_demo']['correct_items'] = 0;
			
	}

?>