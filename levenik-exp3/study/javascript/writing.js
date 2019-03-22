// Code for writing task
var typedString = new Array();
var heardAudio = false;
var nextTimeout = 1;
var sending = false;

window.onload = function() {
	initialisePage();
};

// Get current progress and set up first trial
function initialisePage() {
	fillKeyboard();
	pickRandomAudio();
}

function fillKeyboard() {
	// ask server for a list of letter and sound values (in numbers)
	$.getJSON("php/generate_keyboard.php", function(data) {
		// iterate over it and fill in the keyboard
		var keys = document.getElementsByClassName('letterKey');
		for (var i = 0; i < keys.length; i++) {
			keys[i].src = 'resources/images/svg/script/' + String(data.letters[i]) + '.svg';
			keys[i].id = String(data.letters[i]);
		}
	});		
}

// ---------------------------------------------------------------------------------
// KEYBOARD EVENTS
// ---------------------------------------------------------------------------------

// Attach the given new letter to the typing field as an img
function type(key) {
	if (typedString.length < 10) {
		// create img element with the key's source and attach to field
		var letter = document.createElement('img');
		letter.src = key.src;
		document.getElementById('typingField').appendChild(letter);
		// update input string
		typedString.push(key.id);
	}
}

// Remove the last letter from the field
function backspacePressed() {
	// erase last child of field and slice input string
	$(document.getElementById('typingField').lastChild).remove();
	typedString.pop();
}

// Display string on typing field
function displayString(string) {
	for (var i = 0; i < string.length; i++) {
		// loop through string and append img as you go
		// create img element with the key's source and attach to field
		var letter = document.createElement('img');
		letter.src = document.getElementById(string[i]).src;
		document.getElementById('typingField').appendChild(letter);
		// update input string
		typedString.push(string[i]);
	}
}

function displaySolutionString(string) {
	for (var i = 0; i < string.length; i++) {
		// loop through string and append img as you go
		// create img element with the key's source and attach to field
		var letter = document.createElement('img');
		letter.src = document.getElementById(string[i]).src;
		document.getElementById('solutionField').appendChild(letter);
	}
}

// Clear typing field and user input
function clearString() {
	typedString = [];
	$(document.getElementById('typingField').childNodes).remove();
}

function clearSolutionString() {
	$(document.getElementById('solutionField').childNodes).remove();
}

// Send string to server's database
function enterPressed() {
	/*if (typedString.length == 0) {
		typedString.push(0);	// allow empty input
	}*/
	//if (heardAudio) {
		if ((typedString.length > 0) && (!sending)) {
			document.getElementById('enterButton').disabled = true;
			sending = true;
			heardAudio = false;
			document.getElementById('nextButton').disabled = true;	// disable button until sound finished
			hideKeyboard(true);
			// blink feedback
			$('#display').fadeOut(200).fadeIn(200);
			// post attempt
			$.post( "php/post_word_input.php", { input: typedString }, function( data ) {
				// update progress - TODO: SERVER SIDE
				var obj = JSON.parse(data);

				// remove audio controls
				document.getElementById('audioPrompt').controls = false;

				// DIFFERENT FOR TEST STATES
				if (obj.state === "W_TEST") {
					// TEST
					// message reveal and hide keyboard
					var messageElement = document.getElementById('message');
					messageElement.innerHTML = "SUBMITTED!";
					clearString();
					setTimeout(	function() { 	// doesn't really make sense - but maybe it stops duplicate trials?
						sending = false;
						document.getElementById('nextButton').disabled = false;
					}, 500);
					nextTimeout = window.setTimeout(function() {
						document.getElementById('display').hidden = true; // hide display
					}, 3000);
				} else {
					// TRAINING
					// message reveal and hide keyboard
					var messageElement = document.getElementById('message');
					if (obj.feedback !== "1") {
						messageElement.innerHTML = "WRONG!";
						messageElement.style.color = "#ff0000";
						document.getElementById('typingField').style.border = "1px solid #ff0000";
						document.getElementById('sfxIncorrect').play();
						document.getElementById('solution').hidden = false;
						displaySolutionString(obj.answer); // show answer string in typing field
					} else {
						messageElement.innerHTML = "CORRECT!";
						messageElement.style.color = "#32CD32";
						document.getElementById('typingField').style.border = "1px solid #32cd32";
						document.getElementById('sfxCorrect').play();
						clearString();
						displayString(obj.answer); // show answer string in typing field
					}	

					// play answer string sound
					setTimeout(	function() { 
						var audioPrompt = document.getElementById("audioPrompt");
						var end = function() {
							document.getElementById('nextButton').disabled = false;
							
							nextTimeout = window.setTimeout(function() {
								clearString();	// remove string from typing field
								clearSolutionString();
								// hide displays
								document.getElementById('display').hidden = true;
								document.getElementById('solution').hidden = true;								
							}, obj.answer.length*500);
											
							audioPrompt.removeEventListener('ended', end, false);
						};
						audioPrompt.addEventListener('ended', end, false);	
						audioPrompt.pause();
						audioPrompt.currentTime = 0;
						audioPrompt.play();
						
						sending = false;
					}, 1000);
				}
			});
		} else {
			alert("Please input some text with the keyboard on the screen.");
		}
	//} else {
	//	alert("You need to hear the audio first.");
	//}
}

// Check if reached end of task to redirect or show next trial
function nextPressed() {
	window.clearTimeout(nextTimeout);
	clearString();	// remove string from typing field
	clearSolutionString();
	// hide solution field
	document.getElementById('solution').hidden = true;
	
	document.getElementById('display').hidden = false;
	// re-enable enter button
	document.getElementById('enterButton').disabled = false;
	// pick a new trial
	pickRandomAudio();
}

// Switch keyboard with next button
function hideKeyboard(value) {
	if (value) {
		$('#leftKeyboard').hide();
		$('#rightKeyboard').hide();
		$('#nextButton').show();
	} else {
		$('#leftKeyboard').show();
		$('#rightKeyboard').show();
		$('#nextButton').hide();
	}
}

// ---------------------------------------------------------------------------------
// Helper Functions
// ---------------------------------------------------------------------------------

// Get a random audio file from server and load into audio element
function pickRandomAudio() {
	// check if reached end of task and update progress
	updateProgressBars(
		function() {
			$.getJSON("php/get_random_file.php", function(data) {
				// message change
				document.getElementById('message').innerHTML = "TYPE YOUR GUESS:";
				document.getElementById('message').style.color = "black";
				document.getElementById('typingField').style.border = "1px solid black";
				// reveal the keyboard back up again
				hideKeyboard(false);
					
				if (data.picture != "") {
					document.getElementById("picture").src = data.picture;
				} else {
					document.getElementById("picture").style.display = "none";
				}
				
				// set source to audio element
				document.getElementById('audioPrompt').src = data.location;
				document.getElementById('audioPrompt').controls = true;
				// custom autoplay
				document.getElementById("audioPrompt").addEventListener('canplaythrough', autoplay);
				
				var audioPrompt = document.getElementById("audioPrompt");
				var end = function() {
					heardAudio = true;
					audioPrompt.removeEventListener('ended', end, false);
				};
				audioPrompt.addEventListener('ended', end, false);	
			}).fail(function() { pickRandomAudio(); });
		},
		function() { window.location.reload(); }
	);
}

// Custom autoplay functionality (because autoplay does not work on mobiles)
function autoplay() {
	// play after short delay
	setTimeout( function() { 
		document.getElementById("audioPrompt").play(); 
	}, 600);
	// remove to avoid multiple replays
	document.getElementById("audioPrompt").removeEventListener('canplaythrough', autoplay, false);
}