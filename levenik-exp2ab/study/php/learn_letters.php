<?php

// Create new session if not set		????????????? shouldn't it go back to start
session_start();
if(!isset($_SESSION['exists'])) {
	die();
}

// Get alphabet key from server
$alphabet = getAlphabetKey();

// Echo next item
$item = $_SESSION['section_set'][$_SESSION['item_order'] - 1];


if ($item === 'end') {
	echo json_encode(array(	"item_order" => $_SESSION['item_order'], 
							"item_count" => $_SESSION['item_total'], 
							"message" => "",
							"letter" => "resources/images/svg/enter.svg", 
							"sound" => "resources/audio/game/correct.wav"));
} else if ($item === 'mid') {
	echo json_encode(array(	"item_order" => $_SESSION['item_order'], 
							"item_count" => $_SESSION['item_total'], 
							"message" => "Good job! That was the whole alphabet. Now you will see the alphabet a second time.",
							"letter" => "resources/images/svg/enter.svg", 
							"sound" => "resources/audio/game/correct.wav"));
} else {
	
	$sound = $alphabet[$item-1];
	
	$sound_encoded = encodeSound($sound);
		
	$letter_location = "resources/images/svg/script/$item.svg";
	$sound_location = "resources/audio/letters/$sound" . '_' . $_SESSION['speaker_condition'] . '.wav';

	echo json_encode(array(	"item_order" => $_SESSION['item_order'], 
							"item_count" => $_SESSION['item_total'], 
							"message" => "",
							"letter" => $letter_location, 
							"sound" => $sound_location));
}

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

function encodeSound($actual_sound) {
	$alphabet_encoding = array(	"a" => 1, "E" => 2, "i" => 3, "O" => 4, "u" => 5, 
								"m" => 6, "n" => 7, "b" => 8, "d" => 9, "k" => 10, 
								"f" => 11, "s" => 12, "l" => 13);
	$encoded_sound = $alphabet_encoding[$actual_sound];
	return $encoded_sound;
}

?>