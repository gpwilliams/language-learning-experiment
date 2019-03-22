<?php

	session_start();
	
	if(!isset($_SESSION['exists'])) {
		die();
	}
	
	$_SESSION['item_order']+=100;

?>