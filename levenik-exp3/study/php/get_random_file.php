<?php

// Get the next word pronunciation (audio file) from the database

session_start();
if(!isset($_SESSION['exists'])) {
	die();
}

require_once 'database_connection.php';
// Create connection
$db = new Db();

$selected_word = '';
$selected_word_id = 0;

// Get information about the required item
$sql = "SELECT * FROM word_list WHERE word_id=" . $db->quote($_SESSION['section_set'][intval($_SESSION['item_order']-1)]) . ";";
$result_rows = $db->select($sql);
if ($result_rows->num_rows > 0) {
	if($row = $result_rows->fetch_assoc()) {
		$selected_word = $row["word"];
		$selected_word_id = $row["word_id"];
	
		// store answer in session for future use
		$_SESSION['answer'] = $selected_word_id;

		// Get file location
		$selected_word_id = str_pad($selected_word_id, 2, "0", STR_PAD_LEFT);

		$location = "resources/audio/words/$selected_word_id" . '_' . $selected_word . '_' . $_SESSION['speaker_condition'] . '.wav';

		// Return image data if in the image condition
		if ($_SESSION['picture_condition'] == 1) {
			$picture = "resources/images/word_pictures/" . $_SESSION['pictures'][$selected_word_id-1] . ".png";
		} else {
			$picture = "";
		}

		// Output
		echo json_encode(array('location' => $location, 'picture' => $picture ));
	}
} else {
	//echo "no results";
}

?>