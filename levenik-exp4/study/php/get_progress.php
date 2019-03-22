<?php

	// Get session progress variables

	session_start();
	
	if(isset($_SESSION['exists'])) {
	
		echo json_encode(array	(	"item_order" => $_SESSION['item_order'], 
									"item_total" => $_SESSION['item_total'],
									"section_order" => $_SESSION['section_order'], 
									"section_total" => $_SESSION['section_total']
								)
		);
	} else {
		die();
	}
?>