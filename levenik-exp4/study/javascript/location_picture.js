// Location picture handling
$.ajax('php/get_location_picture.php').done(function(data) {
	if (data !== "") {
		document.getElementById("locationPicture").src = 'resources/images/' + data;
	} else {
		document.getElementById("locationPicture").parentNode.removeChild(document.getElementById("locationPicture"));
	}
});	