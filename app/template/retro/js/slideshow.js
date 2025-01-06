var slideIndex = 1;
var timeOutId;
showDivs(slideIndex);

function plusDivs(idx) {
    showDivs(slideIndex += idx);
}

function showDivs(idx) {
    clearTimeout(timeOutId);
    var items = document.getElementsByClassName('slideshow-box');
    if(idx == undefined){ idx = ++slideIndex; }
    if(idx > items.length) { slideIndex = 1; } 
    if(idx < 1) { slideIndex = items.length; }
    for(var i = 0; i < items.length; i++) {
         items[i].style.display = 'none'; 
    }
	if(items.length > 0) {
		items[slideIndex-1].style.display = 'block';
	}
    timeOutId = setTimeout(showDivs, 6000);
}
