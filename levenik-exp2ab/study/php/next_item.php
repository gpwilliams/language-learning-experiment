<?php

	session_start();
	if(!isset($_SESSION['exists'])) {
		die();
	}

	// exposures count
	if ($_SESSION['count_exposures']) {
		if (isset($_SESSION['exposure_counts'])) {
			$word_id =  $_SESSION['section_set'][intval($_SESSION['item_order']-1)];
			$_SESSION['exposure_counts'][$word_id-1]++;
		}
	}
	
	$_SESSION['item_order']++;
	
?>