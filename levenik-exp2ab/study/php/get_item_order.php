<?php

	session_start();
	if(isset($_SESSION['exists'])) {	
		echo $_SESSION['item_order'];
	} else {
		die();
	}
?>