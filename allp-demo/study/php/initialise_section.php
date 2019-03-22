<?php

	function initialise_section() {	
		if (intval($_SESSION['allp_demo']['item_order']) === 1) {
			$_SESSION['allp_demo']['count_exposures'] = true;
			switch ($_SESSION['allp_demo']['state']) {
				case 'SCRIPT':
					$_SESSION['allp_demo']['count_exposures'] = false;
					// randomise the sequence of presentation and store it in session
					$random_order = array();
					$letters_order = array(1, 2, 3, 4, 5, 6);
					// first half
					shuffle($letters_order);
					for ($i = 0; $i < count($letters_order); $i++) {
						$random_order[] = $letters_order[$i];
						$random_order[] = $letters_order[$i];
					}
					$random_order[] = 'mid';
					// second half
					shuffle($letters_order);
					for ($i = 0; $i < count($letters_order); $i++) {
						$random_order[] = $letters_order[$i];
						$random_order[] = $letters_order[$i];
					}
					$random_order[] = 'end';

					$_SESSION['allp_demo']['section_set'] = $random_order;
					$_SESSION['allp_demo']['item_total'] = count($_SESSION['allp_demo']['section_set']);
					break;
					
					
				case 'EXPOSURE1':
					exposure_screen_init(1);
					break;
				case 'R_TR1':
					reading_training_init(1, 2);
					break;
				case 'W_TR1':				
					writing_training_init(1, 2);
					break;
					
				case 'EXPOSURE2':
					exposure_screen_init(1);
					break;
				case 'R_TR2':
					reading_training_init(1, 2);
					break;
				case 'W_TR2':				
					writing_training_init(1, 2);
					break;
					
				case 'EXPOSURE3':
					exposure_screen_init(1);
					break;
				case 'R_TR3':
					reading_training_init(1, 2);
					break;
				case 'W_TR3':				
					writing_training_init(1, 2);
					break;
					
				case 'R_TEST':	
					$random_order = $_SESSION['allp_demo']['training_set'];
					$random_order[] = 42;

					shuffle($random_order);

					$_SESSION['allp_demo']['section_set'] = $random_order;
					$_SESSION['allp_demo']['item_total'] = count($_SESSION['allp_demo']['section_set']);
					break;
				case 'W_TEST':		
					// for demo
					$_SESSION['allp_demo']['correct_items'] = 0;
					
					$random_order = $_SESSION['allp_demo']['training_set'];
					$random_order[] = 42;
					
					// for demo
					$_SESSION['allp_demo']['final_number_of_items'] = count($random_order);
					
					shuffle($random_order);

					$_SESSION['allp_demo']['section_set'] = $random_order;
					$_SESSION['allp_demo']['item_total'] = count($_SESSION['allp_demo']['section_set']);
					break;
				default:
					$_SESSION['allp_demo']['count_exposures'] = false;
					$_SESSION['allp_demo']['item_total'] = 1;
					break;
			}
		}
	}
	
	function exposure_screen_init($repeats) {
		$random_order = array();
		$new_order = array();

		$random_order = $_SESSION['allp_demo']['training_set'];
		shuffle($random_order);
		// double training set	
		for ($i = 0; $i < count($random_order); $i++) {
			for ($j = 0; $j < $repeats; $j++) {
				$new_order[] = $random_order[$i];
			}
		}
		
		$_SESSION['allp_demo']['section_set'] = $new_order;
		$_SESSION['allp_demo']['item_total'] = count($_SESSION['allp_demo']['section_set']);
	}
	
	function reading_training_init($beginning, $end) {
		$new_order = array();

		// get
		for ($i = $beginning - 1; $i < $end; $i++) {
			$new_order[] = $_SESSION['allp_demo']['training_set'][$i];
		}
		shuffle($new_order);
		
		$_SESSION['allp_demo']['section_set'] = $new_order;
		$_SESSION['allp_demo']['item_total'] = count($_SESSION['allp_demo']['section_set']);
	}
	
	function writing_training_init($beginning, $end) {
		$new_order = array();

		// get
		for ($i = $beginning - 1; $i < $end; $i++) {
			$new_order[] = $_SESSION['allp_demo']['training_set'][$i];
		}
		shuffle($new_order);
		
		$_SESSION['allp_demo']['section_set'] = $new_order;
		$_SESSION['allp_demo']['item_total'] = count($_SESSION['allp_demo']['section_set']);	
	}
	
?>