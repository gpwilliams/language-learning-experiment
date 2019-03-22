<?php
		
		function getURLData() {
		
			// First, get experiment conditions from hyperlink and store in session for future use
			$order = "";
			if (isset($_GET["o"])) {
				$order = sanitiseString($_GET["o"]);
				if (($order === "RW") || ($order === "rw")) {
					$_SESSION["levenik"]["link_o"] = "RW";
				} else if (($order === "WR") || ($order === "wr")) {
					$_SESSION["levenik"]["link_o"] = "WR";
				} else {
					//echo "Bad condition input.";
					//die();
				}
			}
			
			$speaker = "";
			if (isset($_GET["s"])) {
				$speaker = sanitiseString($_GET["s"]);
				if (($speaker === "M") || ($speaker === "m")) {
					$_SESSION["levenik"]["link_s"] = "male";
				} else if (($speaker === "F") || ($speaker === "f")) {
					$_SESSION["levenik"]["link_s"] = "female";
				} else {
					//echo "Bad condition input.";
					//die();
				}
			}
			
			$picture = 1;
			if (isset($_GET["p"])) {
				$picture = intval(sanitiseString($_GET["p"]));
				if ($picture === 0) {
					$_SESSION["levenik"]["link_p"] = 0;
				} else if ($picture === 1) {
					$_SESSION["levenik"]["link_p"] = 1;
				} else {
					//echo "Bad condition input.";
					//die();
				}
			}
			
			$language = "";
			if (isset($_GET["l"])) {
				$language = sanitiseString($_GET["l"]);
				if (($language === "S") || ($language === "s")) {
					$_SESSION["levenik"]["link_l"] = "standard";
				} else if (($language === "D") || ($language === "d")) {
					$_SESSION["levenik"]["link_l"] = "dialect";
				} else {
					//echo "Bad condition input.";
					//die();
				}
			}
			
			$orthography = "";
			if (isset($_GET["w"])) {
				$orthography = sanitiseString($_GET["w"]);
				if (($orthography === "O") || ($orthography === "o")) {
					$_SESSION["levenik"]["link_w"] = "opaque";
				} else if (($orthography === "T") || ($orthography === "t")) {
					$_SESSION["levenik"]["link_w"] = "transparent";
				} else {
					//echo "Bad condition input.";
					//die();
				}
			}
			
			$social_cue = 0;
			if (isset($_GET["cue"])) {
				$social_cue = intval(sanitiseString($_GET["cue"]));
				if ($social_cue === 0) {
					$_SESSION["levenik"]["link_cue"] = 0;
				} else if ($social_cue === 1) {
					$_SESSION["levenik"]["link_cue"] = 1;
				} else {
					//echo "Bad condition input.";
					//die();
				}
			}
			
			$dialect_training = 0;
			if (isset($_GET["dt"])) {
				$dialect_training = intval(sanitiseString($_GET["dt"]));
				if ($dialect_training === 0) {
					$_SESSION["levenik"]["link_dt"] = 0;
				} else if ($dialect_training === 1) {
					$_SESSION["levenik"]["link_dt"] = 1;
				} else {
					//echo "Bad condition input.";
					//die();
				}
			}
			
			$dialect_location = 0;
			if (isset($_GET["dl"])) {
				$dialect_location = intval(sanitiseString($_GET["dl"]));
				if ($dialect_location === 0) {
					$_SESSION["levenik"]["link_dl"] = 0;
				} else if ($dialect_location === 1) {
					$_SESSION["levenik"]["link_dl"] = 1;
				} else {
					//echo "Bad condition input.";
					//die();
				}
			}
			
			// Get Prolific information if provided in link
			$prolific_id = "";
			if (isset($_GET["PROLIFIC_PID"])) {
				$prolific_id = sanitiseString($_GET["PROLIFIC_PID"]);
				$_SESSION["levenik"]["prolific_id"] = $prolific_id;
			}
			
			$prolific_session = "";
			if (isset($_GET["SESSION_ID"])) {
				$prolific_session= sanitiseString($_GET["SESSION_ID"]);
				$_SESSION["levenik"]["prolific_session"] = $prolific_session;
			}

		}
		
		// Helper function for sanitising input
		function sanitiseString($var) {
			$var = stripslashes($var);
			$var = strip_tags($var);
			$var = htmlentities($var);
			return $var;
		}
?>