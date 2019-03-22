function updateProgressBars(continueAction, finishedAction) {
	var task = document.getElementById('taskProgress');
	var study = document.getElementById("studyProgress");
	
	$.getJSON('php/get_progress.php').done(function(data) {
		// update progress bars only if they exist
		if (task != null) {
			updateProgressBar(task, data.item_order, data.item_total);
		}
		if (study != null) {
			updateProgressBar(study, data.section_order, data.section_total);
		}
		
		// perform a completion check and return
		if (data.item_order > data.item_total) {
			finishedAction();
		}
		else {
			continueAction();
		}
	}).fail( function() { return false; });	// return false if there was an error
}

function updateProgressBar(bar, order, total) {
	bar.max = total;
	bar.value = Number(order) - 1;	// minus 1 to look empty at beginning and nearly full at end
	bar.getElementsByTagName('span')[0].innerHTML = String(order - 1) + "/" + String(total);
}