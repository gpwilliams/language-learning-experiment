<?php

	session_start();
	
	if(isset($_SESSION['allp_demo']['exists'])) {
	
		echo json_encode(array	(	"item_order" => $_SESSION['allp_demo']['item_order'], 
									"item_total" => $_SESSION['allp_demo']['item_total'],
									"section_order" => $_SESSION['allp_demo']['section_order'], 
									"section_total" => $_SESSION['allp_demo']['section_total']
								)
		);
	} else {
		die();
	}
?>