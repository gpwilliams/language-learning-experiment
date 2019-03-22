<?php

	session_start();
	if (!isset($_SESSION['allp_demo']['exists'])) {	
		die();
	}

	require_once 'database_connection.php';
    // Create connection
    $db = new Db();

	// Get a random word spelling from the database
	$selected_word = '';
	$selected_spelling = '';
	$selected_word_id = 0;
	
	// Attempt query
    $sql = "SELECT * FROM word_list";
    $result_rows = $db->select($sql);
    if ($result_rows->num_rows > 0) {
		//if there is a result pick a random row and output the word
		mysqli_data_seek($result_rows, intval($_SESSION['allp_demo']['section_set'][intval($_SESSION['allp_demo']['item_order']-1)]) -1);
		if($row = $result_rows->fetch_assoc()) {
			$selected_word = $row["word"];
			$selected_word_id = $row["word_id"];
			
			// choose spelling
			$selected_spelling = $row["word"];
			if ($_SESSION['allp_demo']['orthography_condition'] === 'opaque') {
				$selected_spelling = $row["opaque_spelling"];
			}
			
			// choose dialect version instead if it exists
			if ($_SESSION['allp_demo']['language_condition'] === 'dialect') {
				if (isset($_GET["section"])) {
					if ($_GET["section"] === "exposure") {
						if (strlen($row["dialect_version"]) > 1) {
							$selected_word = $row["dialect_version"];
						}
					}
				}
			}
		}
    } else {
		//echo "no results";
    }
	
	// Get alphabet key
	$key = $_SESSION['allp_demo']["alphabet_key"];


	// file location
	$selected_word_id = str_pad($selected_word_id, 2, "0", STR_PAD_LEFT);
	
	$location = "resources/audio/words/$selected_word_id" . '_' . $selected_word . '_' . $_SESSION['allp_demo']['speaker_condition'] . '.wav';

	$alphabet_coding = array();
	for ($z = 0; $z < strlen($key); $z++) {
		$alphabet_coding[$key[$z]] = $z + 1;
		// handle extra letter 'x'
		if ($key[$z] === 'k') {
			$alphabet_coding['x'] = $z + 1;
		}
	}
	
	/*
	$alphabet_coding = array(	$key[0] => 1, $key[1] => 2, $key[2] => 3, $key[3] => 4, $key[4] => 5, 
								$key[5] => 6, $key[6] => 7, $key[7] => 8, $key[8] => 9, $key[9] => 10, 
								$key[10] => 11, $key[11] => 12, $key[12] => 13);
	*/
	
	
	if ($_SESSION['allp_demo']['picture_condition'] == 1) {
		$picture = "resources/images/word_pictures/" . $_SESSION['allp_demo']['pictures'][$selected_word_id-1] . ".png";
	} else {
		$picture = "";
	}
	
	echo json_encode(array(	'letters' => translateWord($selected_spelling, $alphabet_coding), 
							'location' => $location,
							'picture' => $picture ));
	
	// encode word
	function translateWord($actual_word, $key) {
		$translated_word = array();
		
		for ($i = 0; $i < strlen($actual_word); $i++){
			$translated_word[] = $key[$actual_word[$i]];
		}

		return $translated_word;
	}
	
?>