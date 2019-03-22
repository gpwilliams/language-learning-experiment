<?php

	// GENERATE ALPHABET	
	function generateAlphabet() {
		// define alphabet values and shuffle them
		$sounds = array('E', 'i', 'O', 'm', 'd', 'k', 's');
		shuffle($sounds);
		// create a single string from the shuffled array
		$alphabet_key = 'flabun';
		for ($i = 0; $i < count($sounds); $i++) {
			$alphabet_key .=  $sounds[$i];
		}
		// return the generated alphabet key
		return $alphabet_key;
	}

?>