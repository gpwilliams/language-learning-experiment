<?php

	// Get a word spelling from the database for the next word

	session_start();
	if (!isset($_SESSION['exists'])) {	
		die();
	}

	require_once 'database_connection.php';
    // Create connection
    $db = new Db();

	$selected_word = '';
	$selected_spelling = '';
	$selected_word_id = 0;
	
	// Attempt query
    $sql = "SELECT * FROM word_list";
    $result_rows = $db->select($sql);
    if ($result_rows->num_rows > 0) {
		// if there is a result pick the row appropriate for the this item and output the word
		mysqli_data_seek($result_rows, intval($_SESSION['section_set'][intval($_SESSION['item_order']-1)]) -1);
		if($row = $result_rows->fetch_assoc()) {
			$selected_word = $row["word"];
			$selected_word_id = $row["word_id"];
			
			// choose spelling based on condition
			$selected_spelling = $row["word"];
			if ($_SESSION['orthography_condition'] === 'opaque') {
				$selected_spelling = $row["opaque_spelling"];
			}
			
			// choose dialect version instead if it exists
			if ($_SESSION['language_condition'] === 'dialect') {
				if (isset($_GET["section"])) {
					if ($_GET["section"] === "exposure") {
						if (strlen($row["dialect_version"]) > 1) {
							$selected_word = $row["dialect_version"];
						}
					}
				}
			}
			
			// dialect training
			if (
					($_SESSION['dialect_training_condition'] === 1 ) &&
					( ($_SESSION['state'] === 'R_TR2') || ($_SESSION['state'] === 'W_TR2') )
				) {
					// in dialect training screen for dialect training condition
					if (strlen($row["dialect_version"]) > 1) {
						$selected_word = $row["dialect_version"];
					}
					
					if ($_SESSION['orthography_condition'] === 'opaque') {
						if (strlen($row["opaque_dialect_spelling"]) > 1) {
							$selected_spelling = $row["opaque_dialect_spelling"];
						}
					} else {
						if (strlen($row["dialect_version"]) > 1) {
							$selected_spelling = $row["dialect_version"];
						}
					}
			}
			
			
		}
    } else {
		//echo "no results";
    }
	
	// Get alphabet key from server
	$key = "";
	$sql = "SELECT alphabet_key FROM sessions WHERE session_number=" . $db->quote($_SESSION['session_number']) . ";";
	$result_rows = $db->select($sql);
	if ($result_rows->num_rows > 0) {
		if($row = $result_rows->fetch_assoc()) {
			$key = $row["alphabet_key"];
		}
	} else {
		//echo "no results";
	}

	// Determine Speaker 
	$speaker = $_SESSION['speaker_condition'];
	if ($_SESSION['social_cue_condition'] === 1) {
		// not in exposure
		if (!isset($_GET["section"])) {
			if (
					($_SESSION['dialect_training_condition'] === 1 ) &&
					( ($_SESSION['state'] === 'R_TR2') || ($_SESSION['state'] === 'W_TR2') )
				) {
					// in dialect training screen for dialect training condition
					$speaker = $_SESSION['speaker_condition'];	
			} else {		
				// switch speaker for training
				if ($speaker === 'male') {
					$speaker = 'female';
				} else {
					$speaker = 'male';
				}
			}
		}
	}
	
	// Determine audio file location
	$selected_word_id = str_pad($selected_word_id, 2, "0", STR_PAD_LEFT);
	
	$location = "resources/audio/words/$selected_word_id" . '_' . $selected_word . '_' . $speaker . '.wav';

	// Determine translation mechanism for alphabet key
	$alphabet_coding = array();
	for ($z = 0; $z < strlen($key); $z++) {
		$alphabet_coding[$key[$z]] = $z + 1;
		// handle extra letter 'x' - outdated?
		/*if ($key[$z] === 'k') {
			$alphabet_coding['x'] = $z + 1;
		}*/
	}
	
	// Determine picture to show if in the picture condition
	if ($_SESSION['picture_condition'] == 1) {
		$picture = "resources/images/word_pictures/" . $_SESSION['pictures'][$selected_word_id-1] . ".png";
	} else {
		$picture = "";
	}
	
	// Output
	echo json_encode(array(	'letters' => translateWord($selected_spelling, $alphabet_coding), 
							'location' => $location,
							'picture' => $picture ));
	
	// Encode word sound into alphabet numbers
	function translateWord($actual_word, $key) {
		$translated_word = array();
		
		for ($i = 0; $i < strlen($actual_word); $i++){
			$translated_word[] = $key[$actual_word[$i]];
		}

		return $translated_word;
	}
	
?>