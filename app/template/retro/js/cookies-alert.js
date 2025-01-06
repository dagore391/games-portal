$(document).ready(function(){   
	setTimeout(function () {
		$(".cookies-alert-box").fadeIn(300);
	}, 1000);
	$(".cookies-alert-agree").click(function() {
		$(".cookies-alert-box").fadeOut(300);
	}); 
}); 