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
		// there are 2 state machines based on order condition - one for "Reading First, Writing Second" and one for "Writing First, Reading Second" experiment order
		if (true/*$_SESSION['order_condition'] == 'RW'*/) {
			// load appropriate page based on session progress state
			switch($_SESSION['state']) {
				case "START":	
					$_SESSION['section_order'] = 1;
					$_SESSION['section_total'] = 24;
					stateTransition('pages/demographics.html', 'INSTR_EXPOSURE1');
					break;
					
				case "INSTR_EXPOSURE1":
					// TWO OPTIONS (NORMAL INSTRUCTION OR DIALECT INSTRUCTION WITH PICTURES
					if ($_SESSION['social_cue_condition'] === 0) {
						stateTransition('pages/before_exposure_1_2.html', 'EXPOSURE1');
					} else {
						stateTransition('pages/before_exposure_3_4.html', 'EXPOSURE1');
					}
					break;
				case "EXPOSURE1":
					stateTransition('pages/learn_words.html', 'INSTR_EXPOSURE2');
					break;
					
				case "INSTR_EXPOSURE2":
					stateTransition('pages/encouraging_exposure_first.html', 'EXPOSURE2');
					break;
				case "EXPOSURE2":
					stateTransition('pages/learn_words.html', 'INSTR_EXPOSURE3');
					break;

				case "INSTR_EXPOSURE3":
					stateTransition('pages/encouraging_exposure_second.html', 'EXPOSURE3');
					break;
				case "EXPOSURE3":
					stateTransition('pages/learn_words.html', 'INSTR_EXP_TEST');
					break;
					
					
				case "INSTR_EXP_TEST":
					stateTransition('pages/before_exposure_test.html', 'EXP_TEST');
					break;
				case "EXP_TEST":
					stateTransition('pages/learn_words_test.html', 'INSTR_SCRIPT');
					break;
					
				
					
				case "INSTR_SCRIPT":
					// TWO OPTIONS (NORMAL INSTRUCTION OR DIALECT INSTRUCTION WITH PICTURES
					if ($_SESSION['social_cue_condition'] === 0) {
						stateTransition('pages/before_learn_letters_1_2.html', 'SCRIPT');
					} else {
						stateTransition('pages/before_learn_letters_3_4.html', 'SCRIPT');
					}
					break;
				case "SCRIPT":
					if ($_SESSION['order_condition'] === 'RW') {
						stateTransition('pages/learn_letters.html', 'INSTR_R_TR1');
					} else {
						stateTransition('pages/learn_letters.html', 'INSTR_W_TR1');
					}
					break;
					
					
				case "INSTR_R_TR1":
					stateTransition('pages/before_reading_practice_first.html', 'R_TR1');
					break;
				case "R_TR1":
					if ($_SESSION['order_condition'] === 'RW') {
						stateTransition('pages/reading.html', 'INSTR_W_TR1');
					} else {
						stateTransition('pages/reading.html', 'INSTR_W_TR2');
					}
					break;
				case "INSTR_W_TR1":
					stateTransition('pages/before_writing_practice_first.html', 'W_TR1');
					break;
				case "W_TR1":
					if ($_SESSION['order_condition'] === 'RW') {
						stateTransition('pages/writing.html', 'INSTR_R_TR2');
					} else {
						stateTransition('pages/writing.html', 'INSTR_R_TR1');
					}
					break;
					
				
				case "INSTR_R_TR2":
					stateTransition('pages/before_reading_practice_second.html', 'R_TR2');
					break;
				case "R_TR2":
					if ($_SESSION['order_condition'] === 'RW') {
						stateTransition('pages/reading.html', 'INSTR_W_TR2');
					} else {
						stateTransition('pages/reading.html', 'INSTR_W_TEST');
					}
					break;
				case "INSTR_W_TR2":
					stateTransition('pages/before_writing_practice_second.html', 'W_TR2');
					break;
				case "W_TR2":
					if ($_SESSION['order_condition'] === 'RW') {
						stateTransition('pages/writing.html', 'INSTR_R_TEST');
					} else {
						stateTransition('pages/writing.html', 'INSTR_R_TR2');
					}
					break;	
					
					
				case "INSTR_R_TEST":
					// TWO OPTIONS (NORMAL INSTRUCTION OR DIALECT INSTRUCTION WITH PICTURES
					if ($_SESSION['social_cue_condition'] === 0) {
						stateTransition('pages/before_reading_test_1_2.html', 'R_TEST');
					} else {
						stateTransition('pages/before_reading_test_3_4.html', 'R_TEST');
					}
					break;
				case "R_TEST":
					if ($_SESSION['order_condition'] === 'RW') {
						stateTransition('pages/reading.html', 'INSTR_W_TEST');
					} else {
						stateTransition('pages/reading.html', 'QUESTIONNAIRE');
					}
					break;
				case "INSTR_W_TEST":
					// TWO OPTIONS (NORMAL INSTRUCTION OR DIALECT INSTRUCTION WITH PICTURES
					if ($_SESSION['social_cue_condition'] === 0) {
						stateTransition('pages/before_writing_test_1_2.html', 'W_TEST');
					} else {
						stateTransition('pages/before_writing_test_3_4.html', 'W_TEST');
					}
					break;
				case "W_TEST":
					if ($_SESSION['order_condition'] === 'RW') {
						stateTransition('pages/writing.html', 'QUESTIONNAIRE');
					} else {
						stateTransition('pages/writing.html', 'INSTR_R_TEST');
					}
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
		} // condition end
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

				$sql_update = "UPDATE prolific SET completion_code = '$code' WHERE session_number=" . $db->quote($_SESSION['session_number']) . ";";
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