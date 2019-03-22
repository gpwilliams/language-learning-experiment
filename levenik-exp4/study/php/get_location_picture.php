<?php

	// Get location of speaker

	session_start();
	if(isset($_SESSION['exists'])) {
		
		$location = '';
		
		if (strlen($_SESSION['dialect_location_condition']) > 1) {
			// default is non-dialect picture
			if ($_SESSION['dialect_location_condition'] === 'coastal-village.jpg') {
				$location = 'mountain-village.jpg';
			} else {
				$location = 'coastal-village.jpg';
			}
			
			
			// if dialect condition and in exposure
			if ($_SESSION['language_condition'] === 'dialect') {
				if (($_SESSION['state'] === 'INSTR_EXPOSURE1') || ($_SESSION['state'] === 'INSTR_EXPOSURE2') 
					|| ($_SESSION['state'] === 'INSTR_EXPOSURE3') || ($_SESSION['state'] === 'INSTR_EXP_TEST') 
					|| ($_SESSION['state'] === 'EXPOSURE1') || ($_SESSION['state'] === 'EXPOSURE2') 
					|| ($_SESSION['state'] === 'EXPOSURE3') || ($_SESSION['state'] === 'EXP_TEST') 
				) 
				{
						// in dialect training screen for dialect training condition
						if ($_SESSION['dialect_location_condition'] === 'coastal-village.jpg') {
							$location = 'coastal-village.jpg';
						} else {
							$location = 'mountain-village.jpg';
						}
			
				}
			}
			
			// dialect training
			if (
					($_SESSION['dialect_training_condition'] === 1 ) &&
					( ($_SESSION['state'] === 'INSTR_R_TR2') || ($_SESSION['state'] === 'INSTR_W_TR2') 
					 || ($_SESSION['state'] === 'W_TR2')  || ($_SESSION['state'] === 'R_TR2') 
					)
				) {
					// in dialect training screen for dialect training condition
					if ($_SESSION['dialect_location_condition'] === 'coastal-village.jpg') {
						$location = 'coastal-village.jpg';
					} else {
						$location = 'mountain-village.jpg';
					}
					
			}
		}

		echo $location;
	} else {
		die();
	}
?>