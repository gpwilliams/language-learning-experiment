// Code for exposure phase screen
var soundLocation = "";

window.onload = function init() {
	initializePage();
};

// Initialize the page by setting up the audio context
function initializePage() {
	document.getElementById("nextButton").addEventListener("click", pressedNextButton, false);
	document.getElementById('wordAudio').addEventListener('ended', audioFinished, false);
	
	updateProgressBars( function() { showNextItem(); },
						function() { window.location.reload(); });
}

// Play audio for word when pressed next button
function pressedNextButton() {
	var audio = document.getElementById('wordAudio');
		
	if (audio.currentTime === 0) {
		document.getElementById("nextButton").disabled = true;
		audio.play();
	}
}

// Show next item of experiment
function showNextItem() {
	clearDisplay();	
	// get word from server
	$.getJSON("php/get_random_word.php", { section: "exposure" }, function(data) {
		
		soundLocation = data.location;
		
		// display written word
		//displayString(data.letters);
		
		// decide whether to show picture
		if (data.picture != "") {
			document.getElementById("picture").src = data.picture;
		} else {
			document.getElementById("picture").style.display = "none";
			document.getElementById("pictureDisplay").style.display = "none";
		}
		// update audio and next button
		document.getElementById('wordAudio').src = soundLocation;
		document.getElementById('nextButton').disabled = false;
	});	
}

// Progress to next item when audio finishes playing
function audioFinished() {
	$.ajax("php/next_item.php").done(function() {
		updateProgressBars( 
			function() { 	
				showNextItem();
			},
			function() { window.location.reload(); }
		);
	});
}

// Display given string on text display
function displayString(string) {
	for (var i = 0; i < string.length; i++) {
		var letter = document.createElement('img');
		letter.src = "resources/images/svg/script/" + string[i] + ".svg";
		document.getElementById('promptDisplay').appendChild(letter);
	}
}

// Clear the text display
function clearDisplay() {
	// remove string images from display
	$(document.getElementById('promptDisplay').childNodes).remove();
}
