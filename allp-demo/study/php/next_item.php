<?php

	session_start();
	if(!isset($_SESSION['allp_demo']['exists'])) {
		die();
	}

	// exposures count
	if ($_SESSION['allp_demo']['count_exposures']) {
		if (isset($_SESSION['allp_demo']['exposure_counts'])) {
			$word_id =  $_SESSION['allp_demo']['section_set'][intval($_SESSION['allp_demo']['item_order']-1)];
			$_SESSION['allp_demo']['exposure_counts'][$word_id-1]++;
		}
	}
	
	$_SESSION['allp_demo']['item_order']++;
	
?>