<?php

	require_once 'create_new_session_for_index.php';
	require_once 'php/initialise_section.php';
	require_once 'get_url_data.php';

	// Check if there is an existing session and if not create it
	session_start();
	if(!isset($_SESSION['exists'])) {
		session_regenerate_id();
		getURLData();
		createNewSession();
	}
	
	// Connect to database
	$db = new Db();

	// Check progress in database
	$sql_select = "SELECT * FROM sessions WHERE session_number=" . strval($_SESSION['session_number']) . ";";
	$result_rows = $db->select($sql_select);
	if ($result_rows->num_rows > 0) {
		if ($row = $result_rows->fetch_assoc()) {
			// Set progress and start finite state machine
			$_SESSION['state'] = $row['progress'];
			stateMachine();
		}
	} else {
		echo "Error checking progress in session: " . $db->error();	
	}

	// Helper function for deciding whether to transition to another state
	function stateTransition($current_state, $next_state) {
		// if passed all items go to next state
		if ($_SESSION['item_order'] > $_SESSION['item_total']) {
			updateState($next_state);
		} else {
			// else remain on current state
			readfile($current_state);
		}	
	}
	
	// Finite State Machine of Experiment - this determines the order of screens
	function stateMachine() {
		// load appropriate page based on session progress state
		switch($_SESSION['state']) {
			case "START":	
				$_SESSION['section_order'] = 1;
				$_SESSION['section_total'] = 42 - 18;
				stateTransition('pages/demographics.html', 'INSTR_EXPOSURE1');
				break;
				
			case "INSTR_EXPOSURE1":
				stateTransition('pages/before_exposure.html', 'EXPOSURE1');
				break;
			case "EXPOSURE1":
				stateTransition('pages/learn_words.html', 'INSTR_SCRIPT');
				break;
				
			case "INSTR_SCRIPT":
				stateTransition('pages/before_learn_letters.html', 'SCRIPT');
				break;
			case "SCRIPT":
				stateTransition('pages/learn_letters.html', 'INSTR_R_TR11');
				break;
				
				
			case "INSTR_R_TR11":
				stateTransition('pages/before_reading_practice.html', 'R_TR11');
				break;
			case "R_TR11":
				stateTransition('pages/reading.html', 'INSTR_R_TR12');
				break;
			case "INSTR_R_TR12":
				stateTransition('pages/before_second_reading_practice.html', 'R_TR12');
				break;
			case "R_TR12":
				stateTransition('pages/reading.html', 'INSTR_EXPOSURE2');
				break;
				
			case "INSTR_EXPOSURE2":
				stateTransition('pages/before_exposure.html', 'EXPOSURE2');
				break;
			case "EXPOSURE2":
				stateTransition('pages/learn_words.html', 'INSTR_R_TR21');
				break;
			case "INSTR_R_TR21":
				stateTransition('pages/before_reading_practice.html', 'R_TR21');
				break;
			case "R_TR21":
				stateTransition('pages/reading.html', 'INSTR_R_TR22');
				break;
			case "INSTR_R_TR22":
				stateTransition('pages/before_second_reading_practice.html', 'R_TR22');
				break;
			case "R_TR22":
				stateTransition('pages/reading.html', 'INSTR_EXPOSURE3');
				break;	
				
			case "INSTR_EXPOSURE3":
				stateTransition('pages/before_exposure.html', 'EXPOSURE3');
				break;
			case "EXPOSURE3":
				stateTransition('pages/learn_words.html', 'INSTR_R_TR31');
				break;
			case "INSTR_R_TR31":
				stateTransition('pages/before_reading_practice.html', 'R_TR31');
				break;
			case "R_TR31":
				stateTransition('pages/reading.html', 'INSTR_R_TR32');
				break;
			case "INSTR_R_TR32":
				stateTransition('pages/before_second_reading_practice.html', 'R_TR32');
				break;
			case "R_TR32":
				//stateTransition('pages/reading.html', 'INSTR_EXPOSURE4');
				stateTransition('pages/reading.html', 'INSTR_R_TEST');
				break;	
			/*
			case "INSTR_EXPOSURE4":
				stateTransition('pages/before_exposure.html', 'EXPOSURE4');
				break;
			case "EXPOSURE4":
				stateTransition('pages/learn_words.html', 'INSTR_R_TR41');
				break;
			case "INSTR_R_TR41":
				stateTransition('pages/before_reading_practice.html', 'R_TR41');
				break;
			case "R_TR41":
				stateTransition('pages/reading.html', 'INSTR_R_TR42');
				break;
			case "INSTR_R_TR42":
				stateTransition('pages/before_second_reading_practice.html', 'R_TR42');
				break;
			case "R_TR42":
				stateTransition('pages/reading.html', 'INSTR_EXPOSURE5');
				break;	
				
			case "INSTR_EXPOSURE5":
				stateTransition('pages/before_exposure.html', 'EXPOSURE5');
				break;
			case "EXPOSURE5":
				stateTransition('pages/learn_words.html', 'INSTR_R_TR51');
				break;
			case "INSTR_R_TR51":
				stateTransition('pages/before_reading_practice.html', 'R_TR51');
				break;
			case "R_TR51":
				stateTransition('pages/reading.html', 'INSTR_R_TR52');
				break;
			case "INSTR_R_TR52":
				stateTransition('pages/before_second_reading_practice.html', 'R_TR52');
				break;
			case "R_TR52":
				stateTransition('pages/reading.html', 'INSTR_EXPOSURE6');
				break;	
				
			case "INSTR_EXPOSURE6":
				stateTransition('pages/before_exposure.html', 'EXPOSURE6');
				break;
			case "EXPOSURE6":
				stateTransition('pages/learn_words.html', 'INSTR_R_TR61');
				break;
			case "INSTR_R_TR61":
				stateTransition('pages/before_reading_practice.html', 'R_TR61');
				break;
			case "R_TR61":
				stateTransition('pages/reading.html', 'INSTR_R_TR62');
				break;
			case "INSTR_R_TR62":
				stateTransition('pages/before_second_reading_practice.html', 'R_TR62');
				break;
			case "R_TR62":
				stateTransition('pages/reading.html', 'INSTR_R_TEST');
				break;	*/
				
			case "INSTR_R_TEST":
				stateTransition('pages/before_reading_test.html', 'R_TEST');
				break;
			case "R_TEST":
				stateTransition('pages/reading.html', 'QUESTIONNAIRE');
				break;
			
			case "QUESTIONNAIRE":
				stateTransition('pages/questionnaire.html', 'END');
				break;
			case "END":
				stateTransition('pages/end.html', 'END');
				break;
			default:
				break;
		} // switch end
	}	// function end
	
	// Function for updating to another screen/section of the experiment
	function updateState($state) {
		// connect to database
		$db = new Db();
		
		$_SESSION['state'] = $state;
		
		// Update progress on database
		$sql_update = "UPDATE sessions SET progress=" . $db->quote($_SESSION['state']) . " WHERE session_number=" . strval($_SESSION['session_number']) . ";";
		if ($db->query($sql_update) === TRUE) {
			// Successfully updated
			
			// If reached end of experiment
			if ($_SESSION['state'] == 'END') {
				// include end timestamp in database
				$sql_update = "UPDATE sessions SET end_timestamp = current_timestamp WHERE session_number=" . $db->quote($_SESSION['session_number']) . ";";
				if ($db->query($sql_update) === TRUE) {
					//
				} else {
					echo "Error updating record: " . $db->error();	
				}		
			
				// create completion code
				$code = str_pad($_SESSION['session_number'], 5, "0", STR_PAD_LEFT);
				$seed = str_split('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');
				shuffle($seed); // probably optional since array_is randomized; this may be redundant
				foreach (array_rand($seed, 5) as $k) {
					$code .= $seed[$k];
				}
				
				// include completion code in database
				$sql_update = "UPDATE sessions SET completion_code = '$code' WHERE session_number=" . $db->quote($_SESSION['session_number']) . ";";
				if ($db->query($sql_update) === TRUE) {
					//
				} else {
					echo "Error updating record: " . $db->error();	
				}					
			}
				
			// Initialise new section
			$_SESSION['section_order']++;	// move forward in experiment
			$_SESSION['item_order'] = 1;	// reset item count
			initialise_section();			// initialise other properties of section
			stateMachine();					// load page source or move to another state
		} else {
			echo "Error updating record: " . $db->error;	
		}
	}
	
	
?>