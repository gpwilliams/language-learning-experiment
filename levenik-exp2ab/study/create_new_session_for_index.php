<?php

	// Save the POST-ed file to the server
	require_once 'database_connection_for_index.php';
	require_once 'php/generate_alphabet.php';
	require_once 'php/generate_word_pictures.php';
	
	// Create New Session in Database
	function createNewSession() {
		$db = new Db();
		
		// Get IP address, from: http://daipratt.co.uk/mysql-store-ip-address/
		// test if it is a shared client
		if (!empty($_SERVER['HTTP_CLIENT_IP'])){
			$ip = $db->quote($_SERVER['HTTP_CLIENT_IP']);
		// is it a proxy address
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
			$ip = $db->quote($_SERVER['HTTP_X_FORWARDED_FOR']);
		} else {
			$ip = $db->quote($_SERVER['REMOTE_ADDR']);
		}
		// get browser and referer
		$agent = $db->quote($_SERVER['HTTP_USER_AGENT']);
		$referer = "''";
		if (isset($_SERVER['HTTP_REFERER'])) {
			$referer = $db->quote($_SERVER['HTTP_REFERER']);
		}
		// create an alphabet key
		$alphabet_key = $db->quote(generateAlphabet());


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
		
		$random_order = array	(1,  2,      4,  5,  6,  7,  8,  9, 10,
								11,     13,     15,     17, 18,     20,
								21, 22,     24, 25, 26, 27,     29, 30,
								    32,     34, 35,     37, 38,     40,
								41    );
		shuffle($random_order);
		
		$_SESSION['training_set'] = $random_order;

		// exposure counts
		$exposure_array = array();
		// push values in array
		for ($k = 0; $k < 42; $k++) {
			$exposure_array[] = 0;
		}
		
		$_SESSION['exposure_counts'] = $exposure_array;
		
		// set initial state
		$_SESSION['state'] = 'START';
		
		// insert the values into the database
		$result = $db -> query("INSERT INTO sessions (session_number, progress, alphabet_key, ip_address, browser, referer) VALUES (NULL," . $db->quote($_SESSION['state']) . "," . $alphabet_key . "," . $ip . "," . $agent . "," . $referer . ")");
		
		// Create a new record of the session
		if ($result === TRUE) {
			// get the new session's ID		
			$sql_id = $db->getInsertId();

			// set participant ID to database session ID
			$_SESSION['session_number'] = $sql_id;
			
			/*if ($_SESSION['session_number']%2 == 0) {
				$_SESSION['picture_condition'] = 1;
			} else {
				$_SESSION['picture_condition'] = 0;
			}*/
			
			// decide on conditions
			
			if (isset($_SESSION["levenik"]["link_l"])) {
				$_SESSION['language_condition'] = $_SESSION["levenik"]["link_l"];
			} else {
				if ($_SESSION['session_number']%2 === 0) {
					$_SESSION['language_condition'] = 'standard';
				} else {
					$_SESSION['language_condition'] = 'dialect';
				}
			}
			
			if (isset($_SESSION["levenik"]["link_o"])) {
				$_SESSION['order_condition'] = $_SESSION["levenik"]["link_o"];
			} else {
				if ($_SESSION['session_number']%8 < 4) {
					$_SESSION['order_condition'] = 'RW';
				} else {
					$_SESSION['order_condition'] = 'WR';
				}
			}
			
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
			
			/*
			if (!isset($_SESSION["levenik"]["link_o"]) || 
				!isset($_SESSION["levenik"]["link_p"]) || 
				!isset($_SESSION["levenik"]["link_s"]) ) {
				switch($_SESSION['session_number']%8) {
					case 0: 
						$_SESSION['picture_condition'] = 1;
						$_SESSION['speaker_condition'] = 'male';
						$_SESSION['order_condition'] = 'RW';
						break;
					case 1: 
						$_SESSION['picture_condition'] = 1;
						$_SESSION['speaker_condition'] = 'female';
						$_SESSION['order_condition'] = 'RW';
						break;
					case 2: 
						$_SESSION['picture_condition'] = 0;
						$_SESSION['speaker_condition'] = 'male';
						$_SESSION['order_condition'] = 'RW';
						break;
					case 3: 
						$_SESSION['picture_condition'] = 0;
						$_SESSION['speaker_condition'] = 'female';
						$_SESSION['order_condition'] = 'RW';
						break;
					case 4: 
						$_SESSION['picture_condition'] = 1;
						$_SESSION['speaker_condition'] = 'male';
						$_SESSION['order_condition'] = 'WR';
						break;
					case 5: 
						$_SESSION['picture_condition'] = 1;
						$_SESSION['speaker_condition'] = 'female';
						$_SESSION['order_condition'] = 'WR';
						break;
					case 6: 
						$_SESSION['picture_condition'] = 0;
						$_SESSION['speaker_condition'] = 'male';
						$_SESSION['order_condition'] = 'WR';
						break;
					case 7: 
						$_SESSION['picture_condition'] = 0;
						$_SESSION['speaker_condition'] = 'female';
						$_SESSION['order_condition'] = 'WR';
						break;
				}
			}
			*/
			
			// Update conditions
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
			
			// set pictures
			generateWordPictures();
					
			// start session item from 1
			$_SESSION['item_order'] = 1;		
			$_SESSION['item_total'] = 1;
			$_SESSION['session_item_order'] = 1;
			
			
			// Create new folder on server
			$_SESSION['folder'] = "../../../private/recordings/" . $_SESSION['session_number'];
			mkdir($_SESSION['folder']);
			$_SESSION['folder'] = "../../../../private/recordings/" . $_SESSION['session_number'];

			// session created so it exists now
			$_SESSION['exists'] = true;
		} else {
			echo "Error creating session: " . $db->error();
		}
			
	}

?>