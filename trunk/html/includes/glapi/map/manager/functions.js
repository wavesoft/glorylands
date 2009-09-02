/**
  *
  * Center map
  *
  * This function slowly scrolls the map and centers it into a specified position
  *
  */
function map_center(x,y,update) {
	if (!x) x=map_viewpoint.x;
	if (!y) x=map_viewpoint.y;	
	
	if (map_center_fx) map_center_fx.cancel();
	
	var dim = $('datahost').getSize();
	
	map=new Fx.Morph($('datapane'), {duration: 400, unit: 'px', transition: Fx.Transitions.Expo.easeInOut});	
	map.start({
		'left': -x+(dim.x/2),
		'top': -y+(dim.y/2)
	}).chain(function() {
		// Trim map object to this view
		map_trim(x,y);
	});
	map_center_fx = map;
	
	if (update) map_viewpoint = {'x':x, 'y':y};	
	
}

/**
  * Check if object is inside a range specified
  */
function map_object_in_rage(o,x1,y1,x2,y2) {
	var ep = [
		{x: o.x,			y: o.y},
		{x: o.x+o.width, 	y: o.y},
		{x: o.x, 			y: o.y+o.height},
		{x: o.x+o.width, 	y: o.y+o.height},
		{x: o.x+o.width/2,	y: o.y+o.height/2}
	];
	for (var i=0;i<5; i++) {
		if ( (ep[i].x >= x1) && (ep[i].x <= x2) &&
			 (ep[i].y >= y1) && (ep[i].y <= y2) ) {
			
			return true;			
		}
	}
	return false;
}

/**
  * Check if object is visible
  *
  * Check if object is inside our view ant return true if it is.
  *
  */
function map_object_is_visible(obj,x,y) {
	if (!x) x=map_viewpoint.x;
	if (!y) y=map_viewpoint.y;	
	
	// Detect the view range
	var windim = $(window).getSize();
	var x1 = x-windim.x/2;
	var x2 = x+windim.x/2;	
	var y1 = y-windim.y/2;
	var y2 = y+windim.y/2;
	
	return ( (obj.x >= x1) && (obj.x <= x2) &&
			 (obj.y >= y1) && (obj.y <= y2) );
}

/**
  * Trim invisible objects
  *
  * This function hides all the objects that are outside of our view
  *
  */
function map_trim(x,y) {
	if (!x) x=map_viewpoint.x;
	if (!y) y=map_viewpoint.y;	
	
	// Detect the view range
	var windim = $(window).getSize();
	var x1 = x-windim.x/2;
	var x2 = x+windim.x/2;	
	var y1 = y-windim.y/2;
	var y2 = y+windim.y/2;
	
	// For each dynamic object, apply visibility
	var obj=null; var visible=false; var ox; var oy;
	for (var i=0; i<map_objects.length; i++) {
		var obj = map_objects[i];
		ox = obj.info.x * 32;
		oy = obj.info.y * 32;
		visible = map_object_in_rage(obj, x1,y1,x2,y2);
		if (visible) {			

			// Fade in object
			obj.object.setStyle('display','');
			if (obj.object.getStyle('opacity')!=1) {
				var fx=new Fx.Morph(obj.object, {wait: false, duration: 400, transition: Fx.Transitions.Quad.easeOut});
				fx.start({
					'opacity':1
				});
			}

		} else {

			// Fade out object
			var fx=new Fx.Morph(obj.object, {wait: false, duration: 400, transition: Fx.Transitions.Quad.easeOut});
			if (obj.object.getStyle('opacity')!=0) {
				fx.start({
					'opacity':0
				}).chain(function(){
					obj.object.setStyle('display','none');
				});
			}
		}
	}
}

/**
  * Scroll map
  *
  * This function scrolls the map among with all it's items
  *
  */
function map_scroll(x,y) {
	
	// Defaults
	if (!$defined(x)) x=map_scroll_pos.x;
	if (!$defined(y)) y=map_scroll_pos.y;
	
	$('datapane').setStyles({
		'left': x,
		'top': y
	});
	return;
	
	// Move foreground
	$each(map_objects, function(e,k){
		e.object.setStyles({
			'left': e.x-x,
			'top': e.y-y
		});
	});
	
	// Move background
	$each(map_back, function(e,k){
		e.object.setStyles({
			'left': e.x-x,
			'top': e.y-y
		});
	});
	
	// Update current scroll position
	map_scroll_pos = {'x':x, 'y':y};
}

/**
  * Map curtain management
  *
  * Map curtain is an element that hides the map rendering procedure
  * and provides an information screen about the loading process
  *
  */
function map_curtain(visible) {
	
	// Halt any ongoing animation and initialize new
	if (map_curtain_fx) map_curtain_fx.cancel();
	map_curtain_fx=new Fx.Morph($('dataloader'), {duration: 400, unit: 'px', transition: Fx.Transitions.Expo.easeInOut});	

	// Perform what is to be done
	if (visible && !map_curtain_status) {
		showStatus();
		map_curtain_status=true;
		map_curtain_fx.element.setStyles({
			'visibility':''								 
		});
		return map_curtain_fx.start({
			'opacity':1,
			'height':512
		});
	} else if (!visible && map_curtain_status) {
		map_curtain_status=false;
		return map_curtain_fx.start({
			'opacity':0,
			'height':1
		}).chain(function() {
			this.element.setStyles({'visibility':'hidden'});
		});
	}
}

/**
  * Find an object
  *
  * This function locates the object UID based on the
  * object's GUID
  *
  */
function map_objectid_fromguid(guid) {
	for (var i=0; i<map_objects.length; i++) {
		if (map_objects[i].info.guid == guid) {
			return map_object_index[i];
		}
	}
	return 0;
}
