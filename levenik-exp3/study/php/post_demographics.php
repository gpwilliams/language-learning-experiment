<?php

	// Post input from demographics questionnaire to server database

	session_start();
	if(!isset($_SESSION['exists'])) {
		die();	
	}

	require_once 'database_connection.php';
	$db = new Db();

	if(isset($_POST["age"]) && isset($_POST["gender"]) && isset($_POST["englishrating"])) {
		
		// Sanitise data
		$age = intval($db->sanitiseMySQL($_POST["age"]));
		$gender = $db->sanitiseMySQL($_POST["gender"]);
		$english = intval($db->sanitiseMySQL($_POST["englishrating"]));

		if(($age != '') && ($gender != '')) {
			// upload to database
			$sql_insert = "UPDATE sessions SET age=$age, gender='$gender', english=$english WHERE session_number=" . $db->quote($_SESSION["session_number"]) . ";";
			if ($db->query($sql_insert) === TRUE) {

			} else {
				//echo "Error creating record: " . $db->error;	
			}
			
			// Store their confidence in English language	
			$sql_insert = "INSERT INTO sessions_languages (session_number, language, self_rating) VALUES (" . $db->quote($_SESSION["session_number"]) . "," . $db->quote("English") . "," . $english . ");";
			if ($db->query($sql_insert) === TRUE) {	

			} else {
				//echo "Error creating record: " . $db->error;	
			}
			
			// Create a new record for each language they know
			if (isset($_POST["language"])) {
				$language = $_POST["language"];
				
				for ($i = 0; $i < count($language); $i++) {
					$lang = $db->sanitiseMySQL($language[$i]);
					
					$rating = 0;
					if (isset($_POST["rating" . $i])) {
						$rating =  intval($db->sanitiseMySQL($_POST["rating" . $i]));
					}
				
					// Create a new record
					$sql_insert = "INSERT INTO sessions_languages (session_number, language, self_rating) VALUES (" . $db->quote($_SESSION["session_number"]) . "," . $db->quote($lang) . "," . $rating . ");";
					if ($db->query($sql_insert) === TRUE) {	
	
					} else {
						//echo "Error creating record: " . $db->error;	
					}
				}				
			}
			
			
			// if successful go to next page
			$_SESSION['item_order']++;
		}
	}
	
	// Gether Prolific ID explicitly
	if(isset($_POST["prolific_id"])) {
		$prolific_id = $db->sanitiseMySQL($_POST["prolific_id"]);
		
		if ($prolific_id != '') {
			$sql = "UPDATE sessions SET prolific_id=" .  $db->quote($prolific_id) . " WHERE session_number=" . $db->quote($_SESSION["session_number"]) . ";";
			if ($db->query($sql) === TRUE) {

			} else {
				//echo "Error creating record: " . $db->error;	
			}
		}
	}
	
	header("Location: ../");

?>