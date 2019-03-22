<?php

	// Initialise Current Experiment Section
	// Primarily determine order of items

	function initialise_section() {	
		// If at beginning of section
		if (intval($_SESSION['item_order']) === 1) {
			$_SESSION['count_exposures'] = true;
			switch ($_SESSION['state']) {
				case 'SCRIPT':
					$_SESSION['count_exposures'] = false;
					// randomise the sequence of presentation and store it in session
					$random_order = array();
					$letters_order = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13);
					// first script demo
					shuffle($letters_order);
					for ($i = 0; $i < count($letters_order); $i++) {
						$random_order[] = $letters_order[$i];
						$random_order[] = $letters_order[$i];
					}
					$random_order[] = 'mid';
					// second script demo
					shuffle($letters_order);
					for ($i = 0; $i < count($letters_order); $i++) {
						$random_order[] = $letters_order[$i];
						$random_order[] = $letters_order[$i];
					}
					$random_order[] = 'end';

					$_SESSION['section_set'] = $random_order;
					$_SESSION['item_total'] = count($_SESSION['section_set']);
					break;
					
					
				case 'EXPOSURE1':
					exposure_screen_init(1);
					break;
				case 'R_TR1':
					reading_training_init(1, 10);
					break;
				case 'W_TR1':				
					writing_training_init(1, 10);
					break;
					
				case 'EXPOSURE2':
					exposure_screen_init(1);
					break;
				case 'R_TR2':
					reading_training_init(11, 20);
					break;
				case 'W_TR2':				
					writing_training_init(11, 20);
					break;
					
				case 'EXPOSURE3':
					exposure_screen_init(1);
					break;
				case 'R_TR3':
					reading_training_init(21, 30);
					break;
				case 'W_TR3':				
					writing_training_init(21, 30);
					break;
					
				case 'EXPOSURE4':
					exposure_screen_init(1);
					break;
				case 'R_TR4':
					reading_training_init(31, 40);
					break;
				case 'W_TR4':				
					writing_training_init(31, 40);
					break;
					
				case 'EXPOSURE5':
					exposure_screen_init(1);
					break;
				case 'R_TR5':
					reading_training_init(41, 50);
					break;
				case 'W_TR5':				
					writing_training_init(41, 50);
					break;
					
				case 'EXPOSURE6':
					exposure_screen_init(1);
					break;
				case 'R_TR6':
					reading_training_init(51, 60);
					break;
				case 'W_TR6':				
					writing_training_init(51, 60);
					break;
					
				case 'R_TEST':	
					$random_order = array();
					for ($j = 1; $j <= 42; $j++) {
						$random_order[] = $j;
					}
					shuffle($random_order);

					$_SESSION['section_set'] = $random_order;
					$_SESSION['item_total'] = count($_SESSION['section_set']);
					break;
				case 'W_TEST':		
					$random_order = array();
					for ($j = 1; $j <= 42; $j++) {
						$random_order[] = $j;
					}
					shuffle($random_order);

					$_SESSION['section_set'] = $random_order;
					$_SESSION['item_total'] = count($_SESSION['section_set']);
					break;
				default:
					$_SESSION['count_exposures'] = false;
					$_SESSION['item_total'] = 1;
					break;
			}
		}
	}
	
	// Initialise an Exposure screen
	function exposure_screen_init($repeats) {
		$random_order = array();
		$new_order = array();

		$random_order = array_slice($_SESSION['training_set'], 0 , 30);
		
		shuffle($random_order);
		// each word in training set is shown twice in sequence	
		for ($i = 0; $i < count($random_order); $i++) {
			for ($j = 0; $j < $repeats; $j++) {
				$new_order[] = $random_order[$i];
			}
		}
		
		$_SESSION['section_set'] = $new_order;
		$_SESSION['item_total'] = count($_SESSION['section_set']);
	}
	
	// Initialise a Reading training screen
	function reading_training_init($beginning, $end) {
		$new_order = array();

		for ($i = $beginning - 1; $i < $end; $i++) {
			$new_order[] = $_SESSION['training_set'][$i];
		}
		shuffle($new_order);
		
		$_SESSION['section_set'] = $new_order;
		$_SESSION['item_total'] = count($_SESSION['section_set']);
	}
	
	// Initialise a Writing training screen
	function writing_training_init($beginning, $end) {
		$new_order = array();

		for ($i = $beginning - 1; $i < $end; $i++) {
			$new_order[] = $_SESSION['training_set'][$i];
		}
		shuffle($new_order);
		
		$_SESSION['section_set'] = $new_order;
		$_SESSION['item_total'] = count($_SESSION['section_set']);	
	}
	
?>