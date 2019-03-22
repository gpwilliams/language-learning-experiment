// Code for information screens.
// Simply progress experiment when the next button is pressed.
window.onload = function() {
	updateProgressBars(function() { }, function() { window.location.reload(); } );
}

function nextButtonPressed() {
	$.ajax('php/next_item.php').done(function(data) {
		window.location.reload();
	});	
}