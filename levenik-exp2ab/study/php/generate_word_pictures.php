<?php

	// GENERATE PICTURE TO WORD CORRESPONDENCES	
	function generateWordPictures() {
		
		// get pictures from folder
		$dir = 'resources/images/word_pictures/';	// set directory of files
		$pictures = glob($dir . '*.png');	// get an array of the files
		$another = array();
		foreach ($pictures as $filename) {
			$another[] = substr($filename, - 7, 3);
		}
		shuffle($another);

		// put it into the session
		$_SESSION['pictures'] = $another;
		
		// create a single string from the shuffled array
		$pictures_key = '';
		for ($i = 0; $i < count($another); $i++) {
			$pictures_key .=  $another[$i] . ';';
		}
		// return the generated picture key
		return $pictures_key;
	}

?>