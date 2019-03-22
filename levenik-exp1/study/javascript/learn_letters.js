// Code for letter learning screen
var experimentGlobals = {message: "", letter: "", sound: ""};

window.onload = function() {
	updateProgressBars(function() {getLetter();}, function() { window.location.reload(); });

	// adjust user interface and events
	document.getElementById("nextButton").addEventListener("click", pressedNextButton, false);
	document.getElementById('letterAudio').addEventListener('ended', audioFinished, false);
	
	document.getElementById('letterCard').style.display = "inline-block";
	document.getElementById('letterImage').src = "resources/images/svg/cross.svg";
};

function pressedNextButton() {
	document.getElementById("letterImage").src = experimentGlobals.letter;
	document.getElementById("letterAudio").src = experimentGlobals.sound;
	
	// outside, because we need input to play a sound in android
	var audio = document.getElementById('letterAudio');
		
	if (audio.currentTime === 0) {
		document.getElementById("nextButton").disabled = true;
		audio.play();
	}
}

function getLetter() {
	// get the current letter and sound locations from the server
	$.getJSON("php/learn_letters.php", function(data) {
		//document.getElementById("letterImage").src = data.letter;		
		//document.getElementById("letterAudio").src = data.sound;
		experimentGlobals.letter = data.letter;
		experimentGlobals.sound = data.sound;
		experimentGlobals.message = data.message;
		document.getElementById("nextButton").disabled = false;
	});
}

// Progress to next item when audio finishes playing
function audioFinished(message) {
	
	// message for midway
	if (experimentGlobals.message !== "") {
		alert(experimentGlobals.message);
	}
	
	$.ajax("php/next_item.php").done(function() {
		// hide item
		setTimeout(function() { 
			document.getElementById('letterImage').src = "resources/images/svg/cross.svg";
			updateProgressBars(function() {getLetter();}, function() { window.location.reload(); });
		}, 1000);
	});
}