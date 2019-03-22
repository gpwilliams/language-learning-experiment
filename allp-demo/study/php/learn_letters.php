<?php

// Create new session if not set		????????????? shouldn't it go back to start
session_start();
if(!isset($_SESSION['allp_demo']['exists'])) {
	die();
}

// Get alphabet key from server
$alphabet = $_SESSION['allp_demo']["alphabet_key"];

// Echo next item
$item = $_SESSION['allp_demo']['section_set'][$_SESSION['allp_demo']['item_order'] - 1];


if ($item === 'end') {
	echo json_encode(array(	"item_order" => $_SESSION['allp_demo']['item_order'], 
							"item_count" => $_SESSION['allp_demo']['item_total'], 
							"message" => "",
							"letter" => "resources/images/svg/enter.svg", 
							"sound" => "resources/audio/game/correct.wav"));
} else if ($item === 'mid') {
	echo json_encode(array(	"item_order" => $_SESSION['allp_demo']['item_order'], 
							"item_count" => $_SESSION['allp_demo']['item_total'], 
							"message" => "Good job! That was the whole alphabet. Now you will see the alphabet a second time.",
							"letter" => "resources/images/svg/enter.svg", 
							"sound" => "resources/audio/game/correct.wav"));
} else {
	
	$sound = $alphabet[$item-1];

	$letter_location = "resources/images/svg/script/$item.svg";
	$sound_location = "resources/audio/letters/$sound" . '_' . $_SESSION['allp_demo']['speaker_condition'] . '.wav';

	echo json_encode(array(	"item_order" => $_SESSION['allp_demo']['item_order'], 
							"item_count" => $_SESSION['allp_demo']['item_total'], 
							"message" => "",
							"letter" => $letter_location, 
							"sound" => $sound_location));
}

?>