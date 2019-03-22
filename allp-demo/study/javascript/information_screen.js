window.onload = function() {
	updateProgressBars(function() { }, function() { window.location.reload(); } );
}

function nextButtonPressed() {
	$.ajax('php/next_item.php').done(function(data) {
		window.location.reload();
	});	
}