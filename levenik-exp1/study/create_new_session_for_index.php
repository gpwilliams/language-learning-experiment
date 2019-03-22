<?php

	require_once 'database_connection_for_index.php';
	require_once 'php/generate_alphabet.php';
	require_once 'php/generate_word_pictures.php';
	
	// Create and Initialise New Session
	function createNewSession() {
		// Create database object
		$db = new Db();
		
		// Get IP address, referer and browser info; from: http://daipratt.co.uk/mysql-store-ip-address/
		$ip = "''";
		// test if it is a shared client
		/*
		if (!empty($_SERVER['HTTP_CLIENT_IP'])){
			$ip = $db->quote($_SERVER['HTTP_CLIENT_IP']);
		// is it a proxy address
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
			$ip = $db->quote($_SERVER['HTTP_X_FORWARDED_FOR']);
		} else {
			$ip = $db->quote($_SERVER['REMOTE_ADDR']);
		}
		*/
		// get browser and referer
		$agent = $db->quote($_SERVER['HTTP_USER_AGENT']);
		$referer = "''";
		/*
		if (isset($_SERVER['HTTP_REFERER'])) {
			$referer = $db->quote($_SERVER['HTTP_REFERER']);
		}
		*/
		
		// Create a random alphabet key for experiment
		$alphabet_key = $db->quote(generateAlphabet());


		// Decide on training set
		
		// I. Randomise stimuli completely
		/*
		$random_order = array();
		// push values in array
		for ($j = 1; $j <= 42; $j++) {
			$random_order[] = $j;
		}
		shuffle($random_order);
		// remove last twelve for testing section
		for ($z = 0; $z < 12; $z++) {
			array_pop($random_order);
		}
		*/
		
		
		// II. Regular stimuli randomisation, without assuring equal distribution of dialect words
		/*
		$random_order = array	(1,  2,      4,  5,  6,  7,  8,  9, 10,
								11,     13,     15,     17, 18,     20,
								21, 22,     24, 25, 26, 27,     29, 30,
								    32,     34, 35,     37, 38,     40,
								41    );
		shuffle($random_order);
		$_SESSION['training_set'] = $random_order;
		*/
		
		// III. Randomisation of stimuli, assuring equal distribution of dialect words
		$random_order = array();
		// long training (x2)
		for ($training_repeat = 0; $training_repeat < 2; $training_repeat++) {
			// IDs for words
			$no_dialect = array	(    2,      4,      6,          9, 10,
										13,             17, 18,     20,
									22,     24,                 29,    
											34,             38,        
								41    );
			$with_dialect = array (1, 5, 7, 8, 11, 15, 21, 25, 26, 27, 30, 32, 35, 37, 40);

			shuffle($no_dialect);
			shuffle($with_dialect);
			
			// make each 10 word block have 5 dialect and 5 non-dialect words each
			for ($section_index = 0; $section_index < 3; $section_index++) {
				$section_stimuli = array();
				for ($s = 0; $s < 5; $s++) {
					$section_stimuli[] = array_pop($no_dialect);
					$section_stimuli[] = array_pop($with_dialect);
				}
				shuffle($section_stimuli);
				$random_order = array_merge($random_order, $section_stimuli);
			}
		}
		// Store training set
		$_SESSION['training_set'] = $random_order;

		// Initialise counter for word exposures
		$exposure_array = array();
		// push values in array
		for ($k = 0; $k < 42; $k++) {
			$exposure_array[] = 0;
		}
		$_SESSION['exposure_counts'] = $exposure_array;
		
		// Set Initial State
		$_SESSION['state'] = 'START';
		
		// Insert the values into the database
		$result = $db -> query("INSERT INTO sessions (session_number, progress, alphabet_key, browser) VALUES (NULL," . $db->quote($_SESSION['state']) . "," . $alphabet_key . ","  . $agent . ")");
		
		// Create a new record of the session and finish initialisation
		if ($result === TRUE) {
			// get the new session's ID		
			$sql_id = $db->getInsertId();

			// set participant ID to database session ID
			$_SESSION['session_number'] = $sql_id;
			

			// Decide on conditions
			// If they were already set, skip
			// If not, generate from session number		
			if (isset($_SESSION["levenik"]["link_l"])) {
				$_SESSION['language_condition'] = $_SESSION["levenik"]["link_l"];
			} else {
				if ($_SESSION['session_number']%2 === 0) {
					$_SESSION['language_condition'] = 'standard';
				} else {
					$_SESSION['language_condition'] = 'dialect';
				}
			}
			
			$_SESSION['order_condition'] = 'RR';
			
			if (isset($_SESSION["levenik"]["link_p"])) {
				$_SESSION['picture_condition'] = $_SESSION["levenik"]["link_p"];
			} else {
				if ($_SESSION['session_number']%4 < 2) {
					$_SESSION['picture_condition'] = 1;
				} else {
					$_SESSION['picture_condition'] = 0;
				}
			}
			
			if (isset($_SESSION["levenik"]["link_s"])) {
				$_SESSION['speaker_condition'] = $_SESSION["levenik"]["link_s"];
			} else {
				if ($_SESSION['session_number']%16 < 8) {
					$_SESSION['speaker_condition'] = 'male';
				} else {
					$_SESSION['speaker_condition'] = 'female';
				}
			}
			
			if (isset($_SESSION["levenik"]["link_w"])) {
				$_SESSION['orthography_condition'] = $_SESSION["levenik"]["link_w"];
			} else {
				if ($_SESSION['session_number']%32 < 16) {
					$_SESSION['orthography_condition'] = 'transparent';
				} else {
					$_SESSION['orthography_condition'] = 'opaque';
				}
			}
			
			// Update conditions in database
			$sql_update = "UPDATE sessions SET picture_condition=" . $db->quote($_SESSION['picture_condition']) . " WHERE session_number=" . strval($_SESSION['session_number']) . ";";
			if ($db->query($sql_update) === TRUE) {
				// Successfully updated
			} else {
				echo "Error updating record: " . $db->error;	
			}
			
			$sql_update = "UPDATE sessions SET speaker_condition=" . $db->quote($_SESSION['speaker_condition']) . " WHERE session_number=" . strval($_SESSION['session_number']) . ";";
			if ($db->query($sql_update) === TRUE) {
				// Successfully updated
			} else {
				echo "Error updating record: " . $db->error;	
			}
			
			$sql_update = "UPDATE sessions SET order_condition=" . $db->quote($_SESSION['order_condition']) . " WHERE session_number=" . strval($_SESSION['session_number']) . ";";
			if ($db->query($sql_update) === TRUE) {
				// Successfully updated
			} else {
				echo "Error updating record: " . $db->error;	
			}
			
			$sql_update = "UPDATE sessions SET language_condition=" . $db->quote($_SESSION['language_condition']) . " WHERE session_number=" . strval($_SESSION['session_number']) . ";";
			if ($db->query($sql_update) === TRUE) {
				// Successfully updated
			} else {
				echo "Error updating record: " . $db->error;	
			}
			
			$sql_update = "UPDATE sessions SET orthography_condition=" . $db->quote($_SESSION['orthography_condition']) . " WHERE session_number=" . strval($_SESSION['session_number']) . ";";
			if ($db->query($sql_update) === TRUE) {
				// Successfully updated
			} else {
				echo "Error updating record: " . $db->error;	
			}
			
			// Prolific ID
			if (isset($_SESSION["levenik"]["prolific_id"])) {
				$sql_update = "UPDATE sessions SET prolific_id=" . $db->quote($_SESSION["levenik"]["prolific_id"]) . " WHERE session_number=" . strval($_SESSION['session_number']) . ";";
				if ($db->query($sql_update) === TRUE) {
					// Successfully updated
				} else {
					echo "Error updating record: " . $db->error;	
				}
			}
			// Prolific Session
			if (isset($_SESSION["levenik"]["prolific_session"])) {
				$sql_update = "UPDATE sessions SET prolific_session=" . $db->quote($_SESSION["levenik"]["prolific_session"]) . " WHERE session_number=" . strval($_SESSION['session_number']) . ";";
				if ($db->query($sql_update) === TRUE) {
					// Successfully updated
				} else {
					echo "Error updating record: " . $db->error;	
				}
			}
			
			// Randsomise word-picture associations
			generateWordPictures();
					
			// Start session item from 1
			$_SESSION['item_order'] = 1;		
			$_SESSION['item_total'] = 1;
			$_SESSION['session_item_order'] = 1;
			
			
			// Create new folder on server to store audio files
			$_SESSION['folder'] = "../../../private/recordings_reading_only/" . $_SESSION['session_number'];
			mkdir($_SESSION['folder']);
			$_SESSION['folder'] = "../../../../private/recordings_reading_only/" . $_SESSION['session_number'];

			// session created so it exists now
			$_SESSION['exists'] = true;
		} else {
			echo "Error creating session: " . $db->error();
		}
			
	}

?>