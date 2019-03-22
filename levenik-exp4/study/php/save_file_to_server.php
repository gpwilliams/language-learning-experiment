<?php

	// Save the POST-ed sound  file to the server
	// Based on: http://stackoverflow.com/questions/19015555/pass-blob-through-ajax-to-generate-a-file

	session_start();
	if (!isset($_SESSION['exists'])) {	
		die();
	}
	
	// Upload wav
	if(isset($_FILES['file']) and !$_FILES['file']['error']) {
		
		require_once 'database_connection.php';

		// record in database
		$db = new Db();
		$session = $_SESSION['session_number'];
		$word_id =  $_SESSION['section_set'][intval($_SESSION['item_order'])-1];
		
		// convert from word number to word itself
		$target = '';
		$sql = "SELECT * FROM word_list WHERE word_id=" . intval($word_id) . ";";
		$result_rows = $db->select($sql);
		if ($result_rows->num_rows > 0) {
			if($row = $result_rows->fetch_assoc()) {
				$target = $row["word"];
				
				if (strlen($row["dialect_version"]) > 1) {
					// if dialect condition and in exposure
					if ($_SESSION['language_condition'] === 'dialect') {
						if (($_SESSION['state'] === 'EXPOSURE1') || ($_SESSION['state'] === 'EXPOSURE2') 
							|| ($_SESSION['state'] === 'EXPOSURE3') || ($_SESSION['state'] === 'EXP_TEST') ) 
						{
								// in dialect training screen for dialect training condition
								$target = $row["dialect_version"];
						}
					}
					
					// dialect training
					if (
							($_SESSION['dialect_training_condition'] === 1 ) &&
							( ($_SESSION['state'] === 'R_TR2') || ($_SESSION['state'] === 'W_TR2') )
						) {
							// in dialect training screen for dialect training condition
							$target = $row["dialect_version"];
					}
				} 
				
				
			}
		} else {
			//echo "no results";
		}
		
		$section = $_SESSION['state'];

		// check if this word has been seen before
		$word_seen = false;
		$sql = "SELECT section FROM reading_task WHERE target=" . $db->quote($target) . " AND session_number=" . $session . ";";
		$result_rows = $db->select($sql);
		if ($result_rows->num_rows > 0) {
			$word_seen = true;
		} else {
			$word_seen = false;
		}
		
		// Generate name for new file
		date_default_timezone_set('Europe/London');
		$item = $_SESSION['section_set'][intval($_SESSION['item_order']-1)];
		$time = date('Y-m-d') . '-' . date('H-i-s');
		$fname = $session . '_' . $section . '_' . $_SESSION['item_order'] .  '__' . $time .  '__' . $item . '_' . $target . '.wav';
		
		// Upload file
		move_uploaded_file($_FILES['file']['tmp_name'], $_SESSION['folder'] . '/' . $fname);

		// Determine picture seen
		if ($_SESSION['picture_condition'] == 1) {
			$picture = $_SESSION['pictures'][$word_id-1];
		} else {
			$picture = '000';
		}
		$item_order = $_SESSION['item_order'];
		
		// Create a new record in database for audio input
		$sql_insert = "INSERT INTO reading_task (session_number, section, word_id, word_length, target, section_trial_id, novel_word_for_task, picture_id, session_trial_id, exposure_count) VALUES (" . $session . ",'" . $section . "'," . $word_id . ",'"  . strlen($target) . "','". $target . "'," . $item_order . ",'" . $word_seen . "','" . $picture . "','" . $_SESSION['session_item_order'] . "'," . $_SESSION['exposure_counts'][$word_id-1] . ");";
		if ($db->query($sql_insert) === TRUE) {
			//echo "Record created successfully";
		} else {	
			//echo "Error creating record: " . $db->error;	
		}
		
		// Update exposures count
		if (isset($_SESSION['exposure_counts'])) {
			$_SESSION['exposure_counts'][$word_id-1]++;
		}
		
		// Update item order
		$_SESSION['item_order']++;
		$_SESSION['session_item_order']++;
		
		// Return section name to determine whether to show feedback
		echo $_SESSION['state'];
	} else {
		//echo 'You need to post a blob to see this page.';
	}	
	
?>