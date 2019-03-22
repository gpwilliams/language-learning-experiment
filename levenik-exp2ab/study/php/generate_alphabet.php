<?php

	// GENERATE ALPHABET	
	function generateAlphabet() {
		// define alphabet values and shuffle them
		$sounds = array('a', 'E', 'i', 'O', 'u', 'm', 'n', 'b', 'd', 'k', 'f', 's', 'l');
		shuffle($sounds);
		// create a single string from the shuffled array
		$alphabet_key = '';
		for ($i = 0; $i < count($sounds); $i++) {
			$alphabet_key .=  $sounds[$i];
		}
		// return the generated alphabet key
		return $alphabet_key;
	}

?>