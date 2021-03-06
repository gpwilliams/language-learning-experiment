<?php

// TODO: Maybe check if session exists first and create appropriately?
session_start();
if(!isset($_SESSION['allp_demo']['exists'])) {
	die();
}

// Get alphabet key from server
$alphabet = $_SESSION['allp_demo']["alphabet_key"];

// randomise the sequence of presentation
$random_order = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13);
shuffle($random_order);

// generate otuput data
$letters = array();
$sounds = array();
for ($i = 0; $i < count($random_order); $i++) {
	$letter = $random_order[$i];
	$sound = $alphabet[$letter-1];	
	$sound_encoded = encodeSound($sound);
		
	$letters[] = $letter;
	$sounds[] = $sound_encoded;
}

// output
echo json_encode(array("letters" => $letters, "sounds" => $sounds));


function encodeSound($actual_sound) {
	$alphabet_encoding = array(	"a" => 1, "E" => 2, "i" => 3, "O" => 4, "u" => 5, 
								"m" => 6, "n" => 7, "b" => 8, "d" => 9, "k" => 10, 
								"f" => 11, "s" => 12, "l" => 13);
	$encoded_sound = $alphabet_encoding[$actual_sound];
	return $encoded_sound;
}


?>