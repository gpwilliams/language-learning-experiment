<?php

	session_start();
	if(isset($_SESSION['allp_demo']['exists'])) {	
		echo $_SESSION['allp_demo']['item_order'];
	} else {
		die();
	}
?>