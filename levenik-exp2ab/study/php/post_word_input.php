<?php

	// Create a new participant ID if new session and create a new folder
	session_start();
	if (!isset($_SESSION['exists'])) {
		die();	
	}

	// Check whether the right thing was posted
	if ((isset($_POST["input"])) && (isset($_SESSION['answer'])) ) {
		require_once 'database_connection.php';
		
		$db = new Db();
	
		// Get input data
		if (is_array($_POST["input"])) {
			$input = $_POST["input"];
			array_splice($input, 10);
			// sanitise elements of array
			for ($i = 0; $i < count($input); $i++){
				if (is_numeric($input[$i]) && (intval($input[$i]) >= 0) && (intval($input[$i]) <= 13)) {
					$input[$i] = $db->sanitiseMySQL($input[$i]);
					$input[$i] = intval($input[$i]);
				} else {
					die();
				}
			}
		} else {
			die();
		}
		
		
		$session = $_SESSION['session_number'];
		// Determine order of item
		$order = $db->quote($_SESSION['item_order']);

		// Translate word
		// Get alphabet key from server
		$key = "";
		// Attempt query
		$sql = "SELECT alphabet_key FROM sessions WHERE session_number=" . $db->quote($_SESSION['session_number']) . ";";
		$result_rows = $db->select($sql);
		if ($result_rows->num_rows > 0) {
			if($row = $result_rows->fetch_assoc()) {
				$key = $row["alphabet_key"];
			}
		} else {
			//echo "no results";
		}
		
		// convert from word number to word itself
		$answer = $_SESSION["answer"];
		// Attempt query
		$sql = "SELECT * FROM word_list WHERE word_id=" . $db->quote($_SESSION["answer"]) . ";";
		$result_rows = $db->select($sql);
		if ($result_rows->num_rows > 0) {
			if($row = $result_rows->fetch_assoc()) {
				$answer = $row["word"];
				if ($_SESSION['orthography_condition'] === 'opaque') {
					$answer = $row["opaque_spelling"];
				}
			}
		} else {
			//echo "no results";
		}
		
		$final_word = translateInput($input, $key);
		
		
		// check if this word has been seen before
		$word_seen = false;
		// Attempt query
		$sql = "SELECT section FROM writing_task WHERE target='" . $answer . "' AND session_number=" . $session . ";";
		$result_rows = $db->select($sql);
		if ($result_rows->num_rows > 0) {
			$word_seen = true;
		} else {
			//echo "no results";
			$word_seen = false;
		}
			
	
		// Calculate edit distance
		$distance = levenshtein($final_word, $answer);
		// Normalize edit distance
		if ((strlen($answer) <= 0) || ($distance < 0)) {
			// bug cases
			$distance = 1;
		}
		else if (strlen($final_word) > strlen($answer))
			$distance /= strlen($final_word);
		else
			$distance /= strlen($answer);
		// Determine if completely correct
		$correct = 0;
		if ($distance == 0) {
			$correct = 1;
		}
		
		// TODO: this is NOT enough. You need to sanitise and validate
		$final_word = $db->quote($final_word);
		
		// Get alphabet key from server
		$key = "";
		// Attempt query
		$sql = "SELECT alphabet_key FROM sessions WHERE session_number=" . $db->quote($_SESSION['session_number']) . ";";
		$result_rows = $db->select($sql);
		if ($result_rows->num_rows > 0) {
			if($row = $result_rows->fetch_assoc()) {
				$key = $row["alphabet_key"];
			}
		} else {
			//echo "no results";
		}
		
		$alphabet_coding = array(	$key[0] => 1, $key[1] => 2, $key[2] => 3, $key[3] => 4, $key[4] => 5, 
								$key[5] => 6, $key[6] => 7, $key[7] => 8, $key[8] => 9, $key[9] => 10, 
								$key[10] => 11, $key[11] => 12, $key[12] => 13);
		
		
		$section = $_SESSION['state'];

		if ($_SESSION['picture_condition'] == 1) {
			$picture = $_SESSION['pictures'][$_SESSION["answer"]-1];
		} else {
			$picture = '000';
		}
		
		// check if this word has been submitted before in this section
		$sql = "SELECT * FROM writing_task WHERE word_id=" . $_SESSION["answer"] . " AND session_number=" . $session . " AND section=" . $db->quote($section) . ";";
		$result_rows = $db->select($sql);
		if ($result_rows->num_rows > 0) {

		} else {
			// fine to put it in

			// Create a new record
			$sql_insert = "INSERT INTO writing_task (session_number, section, participant_input, word_id, target, section_trial_id, edit_distance, correct, novel_word_for_task, word_length, picture_id, session_trial_id, exposure_count) VALUES (" . $session . ",'" . $section  . "'," . $final_word . ",'" . $_SESSION["answer"] . "','" . $answer . "'," . $order . "," . $distance . "," . $correct . ",'" . $word_seen . "','" . strlen($answer) . "','" . $picture . "','" . $_SESSION['session_item_order'] . "'," . $_SESSION['exposure_counts'][$_SESSION["answer"]-1] . ");";
			if ($db->query($sql_insert) === TRUE) {	
				
			} else {
				//echo "Error creating record: " . $db->error;	
			}
		
			// exposures count
			if (isset($_SESSION['exposure_counts'])) {
				$_SESSION['exposure_counts'][$_SESSION["answer"]-1]++;
			}	
		}
		
		// outside here to potentially avoid getting stuck on one item
		$_SESSION['item_order']++;
		$_SESSION['session_item_order']++;
		
		// against duplicates
		unset($_SESSION['answer']);
		
		// results
		echo json_encode(array('state' => $_SESSION['state'], 'answer' => translateWord($answer, $alphabet_coding), 'feedback' => strval($correct)));
		
	} else {
		//echo "You can't see this page without posting data.";
	}
	
	// encode word
	function translateInput($input_word, $key) {
		$translated_word = '';
		
		for ($i = 0; $i < count($input_word); $i++){
			if ($input_word[$i] == 0) {		// empty input
				return '';
			}
			
			$translated_word .= $key[$input_word[$i]-1];
		}

		return $translated_word;
	}
	
	// encode word
	function translateWord($actual_word, $key) {
		$translated_word = array();
		
		for ($i = 0; $i < strlen($actual_word); $i++) {
			$translated_word[] = $key[$actual_word[$i]];
		}

		return $translated_word;
	}
	
?>