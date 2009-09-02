// Helper function: Identify scroll position
function getScrollPosition () {
	var x = 0;
	var y = 0;

	if( typeof( window.pageYOffset ) == 'number' ) {
		x = window.pageXOffset;
		y = window.pageYOffset;
	} else if( document.documentElement && ( document.documentElement.scrollLeft || document.documentElement.scrollTop ) ) {
		x = document.documentElement.scrollLeft;
		y = document.documentElement.scrollTop;
	} else if( document.body && ( document.body.scrollLeft || document.body.scrollTop ) ) {
		x = document.body.scrollLeft;
		y = document.body.scrollTop;
	}

	var position = {
		'x' : x,
		'y' : y
	}

	return position;
}
