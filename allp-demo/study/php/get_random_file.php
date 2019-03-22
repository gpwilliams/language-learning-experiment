<?php

session_start();
if(!isset($_SESSION['allp_demo']['exists'])) {	
	// handle completely new session here
	die();
}

require_once 'database_connection.php';
// Create connection
$db = new Db();

// Get a random word spelling from the database
$selected_word = '';
$selected_word_id = 0;

// Attempt query
$sql = "SELECT * FROM word_list WHERE word_id=" . $db->quote($_SESSION['allp_demo']['section_set'][intval($_SESSION['allp_demo']['item_order']-1)]) . ";";
$result_rows = $db->select($sql);
if ($result_rows->num_rows > 0) {
	//mysqli_data_seek($result_rows, intval($_SESSION['allp_demo']['section_set'][intval($_SESSION['allp_demo']['item_order']-1)]) -1);
	if($row = $result_rows->fetch_assoc()) {
		$selected_word = $row["word"];
		$selected_word_id = $row["word_id"];
	
		// store in session
		$_SESSION['allp_demo']['answer'] = $selected_word_id;

		// file location
		$selected_word_id = str_pad($selected_word_id, 2, "0", STR_PAD_LEFT);

		$location = "resources/audio/words/$selected_word_id" . '_' . $selected_word . '_' . $_SESSION['allp_demo']['speaker_condition'] . '.wav';

		if ($_SESSION['allp_demo']['picture_condition'] == 1) {
			$picture = "resources/images/word_pictures/" . $_SESSION['allp_demo']['pictures'][$selected_word_id-1] . ".png";
		} else {
			$picture = "";
		}

		echo json_encode(array('location' => $location, 'picture' => $picture ));
	}
} else {
	//echo "no results";
}

?>