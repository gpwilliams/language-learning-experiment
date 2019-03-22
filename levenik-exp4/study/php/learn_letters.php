<?php

// Control for alphabet learning section

session_start();
if(!isset($_SESSION['exists'])) {
	die();
}

// Get alphabet key from server
$alphabet = getAlphabetKey();

// Determine next item from session variable
$item = $_SESSION['section_set'][$_SESSION['item_order'] - 1];


if ($item === 'end') {
	// alert to move to next screen
	echo json_encode(array(	"item_order" => $_SESSION['item_order'], 
							"item_count" => $_SESSION['item_total'], 
							"message" => "",
							"letter" => "resources/images/svg/enter.svg", 
							"sound" => "resources/audio/game/correct.wav"));
} else if ($item === 'mid') {
	// display message between alphabet exposures
	echo json_encode(array(	"item_order" => $_SESSION['item_order'], 
							"item_count" => $_SESSION['item_total'], 
							"message" => "Good job! That was the whole alphabet. Now you will see the alphabet a second time.",
							"letter" => "resources/images/svg/enter.svg", 
							"sound" => "resources/audio/game/correct.wav"));
} else {
	
	$sound = $alphabet[$item-1];
	
	$sound_encoded = encodeSound($sound);
		
	$letter_location = "resources/images/svg/script/$item.svg";
	
	// Determine Speaker 
	$speaker = $_SESSION['speaker_condition'];
	if ($_SESSION['social_cue_condition'] === 1) {
		// switch speaker for training
		if ($speaker === 'male') {
			$speaker = 'female';
		} else {
			$speaker = 'male';
		}
	}
	
	
	$sound_location = "resources/audio/letters/$sound" . '_' . $speaker . '.wav';

	// Echo next item
	echo json_encode(array(	"item_order" => $_SESSION['item_order'], 
							"item_count" => $_SESSION['item_total'], 
							"message" => "",
							"letter" => $letter_location, 
							"sound" => $sound_location));
}

// Get alphabet key from server
function getAlphabetKey() {
	// create connection
	require_once 'database_connection.php';
	$db = new Db();
	// attempt query
	$sql = "SELECT alphabet_key FROM sessions WHERE session_number=" . $db->quote($_SESSION['session_number']) . ";";
	$result_rows = $db->select($sql);
	if ($result_rows->num_rows > 0) {
		if ($row = $result_rows->fetch_assoc()) {
			return $row["alphabet_key"];
		}
	} else {
		return $_SESSION['session_number'];
	}
}

// Transform actual sound letter to a letter number
function encodeSound($actual_sound) {
	$alphabet_encoding = array(	"a" => 1, "E" => 2, "i" => 3, "O" => 4, "u" => 5, 
								"m" => 6, "n" => 7, "b" => 8, "d" => 9, "k" => 10, 
								"f" => 11, "s" => 12, "l" => 13, "x" => 14);
	$encoded_sound = $alphabet_encoding[$actual_sound];
	return $encoded_sound;
}

?>