  
var markers=[];

/** 
  * Set a 32x32 marker on a specified position
  */
function marker_set(category, x,y,image,z) {
	var im = new Element('img', {src: image});
	$debug('Setting '+category+'.'+x+','+y);
	if (!z) z = lastZ++;
	im.inject($('datapane'));
	im.setStyles({
		'position': 'absolute',
		'left': x,
		'top': y,
		'z-index': z
	});
	markers.push({
		'x': x,
		'y': y,
		'image': image,
		'object': im,
		'cat': category
	});
	return markers.length-1;
}

function marker_remove(category,x,y) {
	for (var i=0; i<markers.length; i++) {
		if (markers[i].cat == category) {
			if (!x && !y) {
				markers[i].object.dispose();
				markers.splice(i,1);
				i--;
			} else {
				$debug(markers[i].x+','+markers[i].y+ ' <> '+x+','+y);
				if ((markers[i].x == x) && (markers[i].y == y)) {
					markers[i].object.dispose();
					markers.splice(i,1);
					i--;
				}
			}
		}
	}
}