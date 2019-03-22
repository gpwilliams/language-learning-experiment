// Cross-browser shims
window.AudioContext = window.AudioContext || window.webkitAudioContext;
navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.msGetUserMedia || navigator.mozGetUserMedia;
window.URL = window.URL || window.webkitURL || window.mozURL || window.msURL;

// Globals
var soundLocation = "";
var timerStart = Date.now() -2500;	// TODO: BETTER WAY?
var gotSound = false;
var nextTimeout = 1;

// First Thing: decide whether to redirect to another page
window.onload = function() {
	initializePage();
};

// Initialize the page by setting up the audio context
function initializePage() {
	// hide controls and show warning
	hideMicControls(true);
	
	// check if browser supports web audio
	try {
		var audio_context = new window.AudioContext;	
		//console.log('Audio context set up.');
		//console.log('navigator.getUserMedia ' + (navigator.getUserMedia ? 'available.' : 'not present!'));
	} catch (e) {
		alert('No web audio support in this browser!');
		tellUserAboutMic();
	}

	// attempt to get user media
	navigator.getUserMedia({audio: true}, function(stream) { startUserMedia(stream, audio_context);}, function(e) {
		//console.log('No live audio input: ' + e);
		tellUserAboutMic();
	});
}


// -------------------------------------------------------------------------
// RecorderJS Management
// -------------------------------------------------------------------------

// Create a Recorder to the audio context
function startUserMedia(stream, audio_context) {		
	var input = audio_context.createMediaStreamSource(stream);
	//console.log('Media stream created.');

	// Firefox Bug (turning off recording in 5 seconds: https://bugzilla.mozilla.org/show_bug.cgi?id=934512)
	// Workaround from: http://stackoverflow.com/questions/22860468/html5-microphone-capture-stops-after-5-seconds-in-firefox
	// the important thing is to save a reference to the MediaStreamAudioSourceNode
	// thus, *window*.source or any other object reference will do
	window.source = input;

	// Uncomment if you want to hear the audio to feedback directly
	//input.connect(audio_context.destination);
	//__log('Input connected to audio context destination.');

	recorder = new Recorder(input);
	//console.log('Recorder initialised.');
	
	// create analyser
	analyser = audio_context.createAnalyser();
	analyser.fftSize = 256;
	var bufferLength = analyser.frequencyBinCount;
	var dataArray = new Uint8Array(analyser.fftSize);
	analyser.getByteTimeDomainData(dataArray);
	
	// connect analyser
	window.source.connect(analyser);
	
	// animate canvas for audio visualisation
	animateCanvases(analyser, dataArray);

	// event listeners for buttons
	document.getElementById('recordButton').addEventListener('click', function() { startRecording(recorder); }, false);
	document.getElementById('nextButton').addEventListener('click', function() { clearRecordings(recorder); pressNextButton(); }, false);
	
	// start experiment	
	hideMicControls(false);
	pressNextButton();
}

// Start a new recording
function startRecording(recorder) {
	// change button properties
	document.getElementById('recordButton').disabled = true;
	document.getElementById('recordButton').innerHTML = "recording...";
	
	// clear anything before
	clearRecordings(recorder);
	// reset sound check
	gotSound = false;
	
	/*
	// check for microphone
	navigator.getUserMedia({audio: true}, function() {}, function(e) {
		//console.log('No live audio input: ' + e);
		tellUserAboutMic();
	});
	*/
	
	// start recording
	recorder && recorder.record();
	
	// show visualization
	//document.getElementById('audioVisualisationCanvas').style = "display: block;";
	//console.log('Recording...');
	
	// show time canvas and reset its animation
	timerStart = Date.now();
	//document.getElementById('timeVisualisationCanvas').style = "display: block;";
	// Stop and save recording after 2.5 secs
	setTimeout(function() { timeUp(recorder); }, 2500);
}

function timeUp(recorder) {
	// stop and submit
	recorder && recorder.stop();
	//console.log('Stopped recording.');
	if (gotSound) {
		submitRecording(recorder);
	} else {
		alert("Sound not loud enough. Please speak louder and clearer into the microphone.");
		// set buttons
		// enable record button
		document.getElementById('recordButton').innerHTML = "record";
		document.getElementById('recordButton').disabled = false;
		document.getElementById('recordButton').hidden = false;
		// disable next button
		document.getElementById('nextButton').disabled = true;
		document.getElementById('nextButton').hidden = true;
	}
}


// Save recording to the server
function saveWAVToServer(recorder) {
	recorder.exportWAV(function(blob) {
		// put blob in FormData
		var data = new FormData();
		data.append('file', blob);
		
		/*var au = document.createElement("audio");
		let url = (window.URL || window.webkitURL).createObjectURL(blob);
		au.src = url;
		au.controls = true;
		document.body.appendChild(au);*/
		
		// send blob data via AJAX
		$.ajax({
			url :  "php/save_file_to_server.php",
			type: 'POST',
			data: data,
			contentType: false,
			processData: false,
			success: function(result) {
				//console.log('Submitted recording to server successfully!');
				finishedUpload(result);
			},    
			error: function() {
				// WHAT TO DO HERE?
				//console.log('Could not submit recording to server!');
				startRecording(recorder);
			}
		});
	}); 
}

// Clear the recording so far
function clearRecordings(recorder) {
	recorder.clear();
	$(document.getElementById('solutionDisplay').childNodes).remove();
}


// -------------------------------------------------------------------------
// Experiment Flow
// -------------------------------------------------------------------------

// Show next item of experiment
function showNextItem() {
	clearDisplay();
	getNewWord();
}

// Get new word from database
function getNewWord() {		
	// get from server? ...
	$.getJSON("php/get_random_word.php", function(data) {
		soundLocation = data.location;
		displayString(data.letters);
		if (data.picture != "") {
			document.getElementById("picture").style.visibility = "visible";
			document.getElementById("picture").src = data.picture;
		} else {
			document.getElementById("picture").style.display = "none";
			document.getElementById("pictureContainer").style.display = "none";
		}
		// set buttons
		// enable record button
		document.getElementById('recordButton').innerHTML = "record";
		document.getElementById('recordButton').disabled = false;
		document.getElementById('recordButton').hidden = false;
		// disable next button
		document.getElementById('nextButton').disabled = true;
		document.getElementById('nextButton').hidden = true;
	}).fail(function() {
		getNewWord();
	});	
}

// Submit recording to server
function submitRecording(recorder) {
	document.getElementById('recordButton').disabled = true;
	document.getElementById('recordButton').innerHTML = "uploading...";
	// actual submit
	saveWAVToServer(recorder);
}

// Procedure after the audio upload was finished
function finishedUpload(experimentState) {
	// update button properites and show visualisation canvas
	document.getElementById('nextButton').hidden = false;
	document.getElementById('recordButton').hidden = true;
	document.getElementById('recordButton').disabled = true;
	
	// DIFFERENT BEHAVIOUR FOR TEST HERE
	if (experimentState === "R_TEST") {
		// show text feedback
		var p = document.createElement('p');
		p.innerHTML = "SUBMITTED!"
		document.getElementById('solutionDisplay').appendChild(p);
		document.getElementById('nextButton').disabled = false;
		nextTimeout = window.setTimeout(function() {
			document.getElementById("picture").style.visibility = "hidden";
			clearDisplay();
		}, 4000);
		
	} else {
		// show text feedback
		var p = document.createElement('p');
		p.innerHTML = "SUBMITTED! Listen to the solution. "
		document.getElementById('solutionDisplay').appendChild(p);
		// play audio feedback
		playbackSolution();

		// enable go to next button when heard recording
		document.getElementById('solutionDisplay').lastChild.addEventListener('ended', function () {	
			document.getElementById('nextButton').disabled = false;
			nextTimeout = window.setTimeout(function() {
				document.getElementById("picture").style.visibility = "hidden";
				clearDisplay();
			}, 4000);
		});
	}
}

// Play solution sound to the user
function playbackSolution() {
	// get url to solution 
	var url = soundLocation;
	// add  playback to page
	var au = document.createElement('audio');
	au.controls = true;
	au.id = 'solutionPlayback';
	au.src = url;
	document.getElementById('solutionDisplay').appendChild(au);
	// autoplay after some time
	document.getElementById('solutionPlayback').addEventListener('canplaythrough', function() {
		window.setTimeout( function() { document.getElementById('solutionPlayback').play(); }, 700);
	});
	// remove elements at end
	document.getElementById('solutionPlayback').addEventListener('ended', function() {
		$(document.getElementById('solutionDisplay').childNodes).remove();
	});
}

// Decide whether to move to next part of experiment after 'next' button was clicked
function pressNextButton() { 
	window.clearTimeout(nextTimeout);
	updateProgressBars(showNextItem, function() { window.location.reload(); } );
}

// -------------------------------------------------------------------------
// Helper Functions
// -------------------------------------------------------------------------

// Reveal controls
function hideMicControls(value) {
	document.getElementById('promptDisplay').hidden = value;
	document.getElementById('controls').hidden = value;
	document.getElementById('missingMicrophoneWarning').hidden = !value;
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

// Show the warning for lack of microphone
function tellUserAboutMic() {
	hideMicControls(true);
	alert("No microphone input!");
}

// Canvas Animations
function animateCanvases(analyser, dataArray) {
	// animate continuously
	window.requestAnimationFrame(function() { 
		return animateCanvases(analyser, dataArray); 
	});

	var audioCanvas = document.getElementById("audioVisualisationCanvas");
	var timeCanvas = document.getElementById("timeVisualisationCanvas");
	// draw on both canvases
	if ((Date.now() - timerStart) <= 2600) {
		drawAudioVisualisation(audioCanvas, audioCanvas.getContext('2d'), analyser, dataArray);
		drawTimeVisualisation(timeCanvas, timeCanvas.getContext('2d'), analyser, dataArray);
	} else {
		audioCanvas.getContext('2d').clearRect(0, 0, audioCanvas.width, audioCanvas.height);
		timeCanvas.getContext('2d').clearRect(0, 0, timeCanvas.width, timeCanvas.height);
	}
}

// Animate audio visualisation
function drawAudioVisualisation(canvas, canvasContext, analyser, dataArray) {
	// resize canvas to % of parent
	//canvas.width = canvas.parentElement.clientWidth * 0.4;
	// intialize box size and spacing
	var boxSize = canvas.height/8;
	var boxGap = boxSize/8;
	// resize canvas to fit a whole number of boxes
	//var remainder = canvas.height % (boxSize + boxGap);
	//canvas.width += boxGap - remainder;
	
	// clear canvas
	canvasContext.fillStyle = 'rgb(255, 255, 255)';
	canvasContext.clearRect(0, 0, canvas.width, canvas.height);

	// define a gradient
	var gradient = canvasContext.createLinearGradient(0, canvas.height, 0, 0);
	gradient.addColorStop(0,"hsl(100, 100%, 50%)");
	gradient.addColorStop(0.25,"hsl(75, 100%, 50%)");
	gradient.addColorStop(0.5,"hsl(50, 100%, 50%)");
	gradient.addColorStop(0.75,"hsl(25, 100%, 50%)");
	gradient.addColorStop(1,"hsl(0, 100%, 50%)");
	canvasContext.fillStyle = gradient;
	
	// get mic data
	analyser.getByteFrequencyData(dataArray);
	
	// create boxes to end
	for (var i = canvas.height - (boxSize + boxGap); i > canvas.height*(1 - (dataArray[0]/255)); i -= boxSize + boxGap) {		
		canvasContext.fillRect(boxGap, i,  canvas.width - boxGap*2, boxSize);
	}
	
	// decide if there was sufficient sound
	if (dataArray[0]/255 > 0.6) {
		gotSound = true;
	}
}

// Animate time visualisation
function drawTimeVisualisation(canvas, canvasContext) {
	// clear canvas
	canvasContext.fillStyle = 'rgb(255, 255, 255)';
	canvasContext.clearRect(0, 0, canvas.width, canvas.height);
	
	// draw circle
	canvasContext.beginPath();
	canvasContext.arc(canvas.width/2, canvas.height/2, (canvas.width/2) -10, 0, 2*Math.PI, false);
	canvasContext.fillStyle = '#eee';
	canvasContext.closePath();
	canvasContext.fill();

	var angle = ((Date.now() - timerStart)/2500)*2*Math.PI;

	// Construct circle from triangles to avoid using buggy arc
	canvasContext.beginPath();
	canvasContext.fillStyle = "red";
	canvasContext.moveTo(canvas.width/2, canvas.height/2);
	canvasContext.lineTo(canvas.width/2, 0);
	for (var i = 0; i < angle; i += Math.PI/32) {
		canvasContext.lineTo((canvas.width/2) + (Math.cos(i-Math.PI/2)*40), (canvas.height/2) + (Math.sin(i-Math.PI/2)*40));
	}
	canvasContext.closePath();
	canvasContext.fill();	
}