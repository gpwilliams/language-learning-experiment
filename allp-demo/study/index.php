<?php

	require_once 'create_new_session_for_index.php';
	require_once 'php/initialise_section.php';

	// Check if there is an existing session 
	session_start();
	if(!isset($_SESSION['allp_demo']['exists'])) {
		session_regenerate_id();		// NEW
		createNewSession();
	}
	
	// Check progress
	if (!isset($_SESSION['allp_demo']['state'])) {
		$_SESSION['allp_demo']['state'] = "START";
	}
	stateMachine();

	function stateTransition($current_state, $next_state) {
		if ($_SESSION['allp_demo']['item_order'] > $_SESSION['allp_demo']['item_total']) {
			updateState($next_state);
		} else {
			readfile($current_state);
		}	
	}
	
	function stateMachine() {
		// redirect to appropriate page based on session info
		if ($_SESSION['allp_demo']['order_condition'] == 'RW') {
			switch($_SESSION['allp_demo']['state']) {
				case "START":	
					$_SESSION['allp_demo']['section_order'] = 1;
					$_SESSION['allp_demo']['section_total'] = 15;
					stateTransition('pages/ethics.html', 'DEMOGRAPHICS');
					//stateTransition('pages/ethics.html', 'END');
					break;
				case "DEMOGRAPHICS":
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
					stateTransition('pages/learn_letters.html', 'INSTR_R_TR1');
					break;
					
					
				case "INSTR_R_TR1":
					stateTransition('pages/before_reading_practice.html', 'R_TR1');
					break;
				case "R_TR1":
					stateTransition('pages/reading.html', 'INSTR_W_TR1');
					break;
				case "INSTR_W_TR1":
					stateTransition('pages/before_writing_practice.html', 'W_TR1');
					break;
				case "W_TR1":
					stateTransition('pages/writing.html', 'INSTR_R_TEST');
					break;
					
				case "INSTR_R_TEST":
					stateTransition('pages/before_reading_test.html', 'R_TEST');
					break;
				case "R_TEST":
					stateTransition('pages/reading.html', 'INSTR_W_TEST');
					break;
				case "INSTR_W_TEST":
					stateTransition('pages/before_writing_test.html', 'W_TEST');
					break;
				case "W_TEST":
					stateTransition('pages/writing.html', 'QUESTIONNAIRE');
					break;
				
				case "QUESTIONNAIRE":
					stateTransition('pages/questionnaire.html', 'END');
					break;
				case "END":
					stateTransition('pages/end.html', 'END');
					break;
				default:
					break;
			}
		} else {
			switch($_SESSION['allp_demo']['state']) {
				case "START":	
					$_SESSION['allp_demo']['section_order'] = 1;
					$_SESSION['allp_demo']['section_total'] = 15;
					stateTransition('pages/ethics.html', 'DEMOGRAPHICS');
					//stateTransition('pages/ethics.html', 'END');
					break;
				case "DEMOGRAPHICS":
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
					stateTransition('pages/learn_letters.html', 'INSTR_W_TR1');
					break;
					
				case "INSTR_W_TR1":
					stateTransition('pages/before_writing_practice.html', 'W_TR1');
					break;
				case "W_TR1":
					stateTransition('pages/writing.html', 'INSTR_R_TR1');
					break;
				case "INSTR_R_TR1":
					stateTransition('pages/before_reading_practice.html', 'R_TR1');
					break;
				case "R_TR1":
					stateTransition('pages/reading.html', 'INSTR_W_TEST');
					break;

				case "INSTR_W_TEST":
					stateTransition('pages/before_writing_test.html', 'W_TEST');
					break;
				case "W_TEST":
					stateTransition('pages/writing.html', 'INSTR_R_TEST');
					break;
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
			}	// switch end
		}	// condition end
	}	// function end
	
	function updateState($state) {

		// Update progress
		$_SESSION['allp_demo']['state'] = $state;
		
			
		if ($_SESSION['allp_demo']['state'] == 'END') {
			// create completion code
			$code = str_pad($_SESSION['allp_demo']['session_number'], 5, "0", STR_PAD_LEFT);
			$seed = str_split('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');
			shuffle($seed); // probably optional since array_is randomized; this may be redundant
			foreach (array_rand($seed, 5) as $k) {
				$code .= $seed[$k];
			}
			
			$_SESSION['allp_demo']['completion_code'] = $code;			
		}
		
		
		// Initialise new section
		$_SESSION['allp_demo']['section_order']++;	
		$_SESSION['allp_demo']['item_order'] = 1;
		initialise_section();
		stateMachine();
	}
	
	
?>