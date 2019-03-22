// Globals
var soundLocation = "";

// First Thing: decide whether to redirect to another page
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

// play audio
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
	$.getJSON("php/get_random_word.php", { section: "exposure" }, function(data) {
		
		soundLocation = data.location;
		
		// display written word
		//displayString(data.letters);
		
		if (data.picture != "") {
			document.getElementById("picture").src = data.picture;
		} else {
			document.getElementById("picture").style.display = "none";
			document.getElementById("pictureDisplay").style.display = "none";
		}
		document.getElementById('wordAudio').src = soundLocation;
		document.getElementById('nextButton').disabled = false;
	});	
}

// progress to next item
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
