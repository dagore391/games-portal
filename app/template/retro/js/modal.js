function showFullImage(imageElement) {
	$('#modal-wrap').fadeIn();
	$('#modal-image').attr('src', $(imageElement).attr('src'));
	$('.close-modal').click(function() {
		$('#modal-wrap').fadeOut();
	});
}

function showFullImageSrc(imageId) {
	$('#modal-wrap').fadeIn();
	$('#modal-image').attr('src', $('#' + imageId).attr('src'));
	$('.close-modal').click(function() {
		$('#modal-wrap').fadeOut();
	});
}