/**
  * Object tracing
  * This function returns the object or array structure as
  * string.
  */
function $trace(obj,level) {
	ans='';
	$each(obj, function(value, name) {
		if ($type(value) != 'function') {
			if (ans!='') ans+=', ';
			if (!level) {
				ans+='['+name+'] = '+value;
			} else {
				ans+='['+name+'] = {'+$trace(value,level-1)+'}';
			}
		}
	});	
	return ans;
}

/**
  * Debug message
  * This function displays a debug message on the debug terminal
  */
var debug_obj = false;
function $debug(text) {
	if (!debug_obj) {
		debug_obj = new Element('pre');
		debug_obj.inject($(document.body));
		debug_obj.setStyles({
			'position':'absolute',
			'right':10,
			'bottom':20,
			'width': 300,
			'height':100,
			'font-size':10,
			'overflow': 'auto',
			'z-index': 100000000,
			'color': '#333333'
		});
	}
	debug_obj.innerHTML+=text+"\n";
	debug_obj.scrollTop = debug_obj.scrollHeight;	// FF
	setTimeout(function() { debug_obj.scrollTop = debug_obj.scrollHeight; }, 10); // IE
}
