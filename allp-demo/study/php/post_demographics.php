<?php

	session_start();
	if(!isset($_SESSION['allp_demo']['exists'])) {
		die();	
	}

	// if successful go to next page
	$_SESSION['allp_demo']['item_order']++;

	header("Location: ../");

?>