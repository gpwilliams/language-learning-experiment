<?php

	session_start();
	if(!isset($_SESSION['allp_demo']['exists'])) {
		die();	
	}

	$_SESSION['allp_demo']['item_order']++;
	
	header("Location: ../");

?>