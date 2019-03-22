<?php
		session_start();

		$order = "";
		if (isset($_GET["o"])) {
			$order = sanitiseString($_GET["o"]);
			if (($order === "RW") || ($order === "rw")) {
				$_SESSION["allp_demo"]["link_o"] = "RW";
			} else if (($order === "WR") || ($order === "wr")) {
				$_SESSION["allp_demo"]["link_o"] = "WR";
			} else {
				//echo "Bad condition input.";
				//die();
			}
		}
		
		$speaker = "";
		if (isset($_GET["s"])) {
			$speaker = sanitiseString($_GET["s"]);
			if (($speaker === "M") || ($speaker === "m")) {
				$_SESSION["allp_demo"]["link_s"] = "male";
			} else if (($speaker === "F") || ($speaker === "f")) {
				$_SESSION["allp_demo"]["link_s"] = "female";
			} else {
				//echo "Bad condition input.";
				//die();
			}
		}
		
		$picture = 1;
		if (isset($_GET["p"])) {
			$picture = intval(sanitiseString($_GET["p"]));
			if ($picture === 0) {
				$_SESSION["allp_demo"]["link_p"] = 0;
			} else if ($picture === 1) {
				$_SESSION["allp_demo"]["link_p"] = 1;
			} else {
				//echo "Bad condition input.";
				//die();
			}
		}
		
		$language = "";
		if (isset($_GET["l"])) {
			$language = sanitiseString($_GET["l"]);
			if (($language === "S") || ($language === "s")) {
				$_SESSION["allp_demo"]["link_l"] = "standard";
			} else if (($language === "D") || ($language === "d")) {
				$_SESSION["allp_demo"]["link_l"] = "dialect";
			} else {
				//echo "Bad condition input.";
				//die();
			}
		}
		
		$orthography = "";
		if (isset($_GET["w"])) {
			$orthography = sanitiseString($_GET["w"]);
			if (($orthography === "O") || ($orthography === "o")) {
				$_SESSION["allp_demo"]["link_w"] = "opaque";
			} else if (($orthography === "T") || ($orthography === "t")) {
				$_SESSION["allp_demo"]["link_w"] = "transparent";
			} else {
				//echo "Bad condition input.";
				//die();
			}
		}
		
		readfile("welcome.html");

		function sanitiseString($var) {
			$var = stripslashes($var);
			$var = strip_tags($var);
			$var = htmlentities($var);
			return $var;
		}
?>