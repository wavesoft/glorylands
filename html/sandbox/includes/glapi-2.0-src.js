// JavaScript Document

function $trace(obj) {
	ans='';
	$each(obj, function(value, name) {
		if (ans!='') ans+=', ';
		ans+='['+name+'] = '+value;
	});	
	return ans;
}

$(document).addEvent('keydown', function(e){
	var e=new Event(e);
	if (e.key == 'right') {
		e.stop();
		map_scroll(map_scroll_pos.x+32,map_scroll_pos.y);
	} else if (e.key == 'left') {
		e.stop();
		map_scroll(map_scroll_pos.x-32,map_scroll_pos.y);
	} else if (e.key == 'up') {
		e.stop();
		map_scroll(map_scroll_pos.x,map_scroll_pos.y-32);
	} else if (e.key == 'down') {
		e.stop();
		map_scroll(map_scroll_pos.x,map_scroll_pos.y+32);
	} else if (e.key == 'd') {
		e.stop();
		window.alert($trace(map_object_index));
		window.alert($trace(map_objects));
	}
});

$(document).addEvent('mouseup', function(e){
	var e=new Event(e);
	/*
	var dpX = $('datapane').getLeft();
	var dpY = $('datapane').getTop();

	$('zp').setStyles({
		'left': (e.event.clientX-dpX-16),
		'top': (e.event.clientY-dpY-16)
	});
	//map_moveobject('m',Math.ceil((e.event.clientX-dpX)/32),Math.ceil((e.event.clientY-dpY)/32));
	//map_reset();

	map_curtain(true).chain(function(){
		map_reset();
		map_loadbase('luskan');
		map_addobject({x:5,y:6,image:'images/column.png', cx:32, cy:160});
		map_addobject({x:5,y:2,image:'images/ancetre-chinois.png', title:'Player', guid:3312, cx:19, cy:69, dynamic: true});
	});
	*/
	
	var data = new Json.Remote('maps/feed.php', {
			onComplete: function(o) {
				map_updatedata(o);
			},
			onFailure: function(e) {
				window.alert(e.message);
			}
	}).send();
	e.stop();
});

function map_status(text) {
	$('dataloader_text').setHTML(text);	
}

$(window).addEvent('load', function(e){										
	map_curtain(true);
	map_loadbase('luskan');
	map_addobject({x:5,y:6,image:'images/column.png', cx:32, cy:160});
	map_addobject({x:5,y:2,image:'images/ancetre-chinois.png', title:'Player', guid:3312, cx:19, cy:69, dynamic: true});

	var data = new Json.Remote('maps/feed.php', {
			onComplete: function(o) {
				map_updatedata(o);
			},
			onFailure: function(e) {
				window.alert(e.message);
			}
	}).send();
});

var lastZ = 250000;

/***********************************
   Map Rendering System
************************************/

var map_dynamic_objects = [];	// Delay-load objects
var map_objects = [];			// All the objects
var map_object_index = [];		// Holds the unique IDs for the previous array
var map_info = [];				// The map information
var map_back = [];				// Background objects
var map_curtain_status = false;	// The last status of the map curtain
var map_curtain_fx = null;		// This holds the last instance of the curtain Fx class - Used to stop animation
var map_scroll_pos = {x:0,y:0};	// The current scroll position
var map_last_id = 0;			// Used to provide unique IDs while storing objects

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
	if (map_curtain_fx) map_curtain_fx.stop();
	map_curtain_fx=new Fx.Styles($('dataloader'), {duration: 400, unit: 'px', transition: Fx.Transitions.Expo.easeInOut});	

	// Perform what is to be done
	if (visible && !map_curtain_status) {
		map_status('');
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
  * Reset Map System
  *
  * This function removes all the map grid objects (dynamic or static)
  * including the background elements, and resets all the variables
  *
  */
function map_reset() {
	// Remove foreground
	$each(map_objects, function(e,k){
		e.object.remove();
	});
	
	// Remove background
	$each(map_back, function(e,k){
		e.object.remove();
	});
	
	// Reset variables
	map_scroll_pos = {x:0,y:0};	
	map_dynamic_objects = [];
	map_objects = [];
	map_info = [];
	map_back = [];
	map_object_index = [];
	map_last_id = 0;
}

/**
  * Load base map file :: Download file
  *
  * This function loads the base map file that contains
  * the background, collision and processing information
  * This function is splitted into 3 steps:
  *  1) Downloading the file
  *  2) Preloading the images
  *  3) Initializing the static objects & background
  *
  */
function map_loadbase(mapname) {	
	// Download the JSON map
	map_status('Loading Map...');
	var data = new Json.Remote('maps/'+mapname+'.php', {
			onComplete: function(o) {
				// Store map info
				map_info = o;				
				
				// Preload graphics
				map_preload();
			},
			onFailure: function(e) {
				map_info = {'name': mapname, 'error': e.message}
			}
	}).send();
}

/**
  * Load base map file :: Preload images
  *
  * This is the second step of the map loading procedure.
  * It pre-caches all the required graphics.
  * This function shall not be called directly
  *
  */
function map_preload() {
	map_status('Loading Graphics...');
	var lt_timer=null; /* Loading Timeout */
	var lt_finalized=false;
	
	// Find out all the images that are required
	var images=map_info.images; /* (1) Overlaies */
	for (var x=0; x<map_info.background.xsize; x++) { /* (2) Background layers */
		for (var y=0; y<map_info.background.ysize; y++) {
			images.push(map_info.background.name+'-'+x+'-'+y+'.png');
		};
	};
	images.push(map_info.background); /* (3) Background tile */
	
	// Precache all map images
	new Asset.images(images, {
		onComplete: function(){
			lt_finalized=true;
			if (lt_timer) {clearTimeout(lt_timer);lt_timer=null;};
			map_finalize();			
		},
		onProgress: function(img_id) {
			// If we are completed, this is just a late call..
			// ignore it...
			if (!lt_finalized) {
				var perc = Math.ceil(100*img_id/images.length);
				if (perc > 100) perc-=100; /* When objects are already cached, the maximum value seems to be 200% */
				map_status('Loading Graphics ['+perc+' %]');
				
				// More than a second of delay between two images is too much
				// Probably it is stucked 
				// BUGFIX: 1) Unreasonable stops on 99% on IE
				//         2) Blocks when file does not exists
				if (lt_timer) {clearTimeout(lt_timer); lt_timer=null;};
				lt_timer=setTimeout(map_finalize, 2000);
			}
		}
	});
}

/**
  * Load base map file :: Preload images
  *
  * This is the third and final step of the map loading procedure.
  * It puts all the static objects into the map
  * This function shall not be called directly
  *
  */
function map_finalize() {
	
	// Update datapane background
	$('datapane').setStyles({
		'background-image': 'url(../images/tiles/'+map_info.background.fill+')'						
	});
	
	// Render background
	var zDig=0;
	for (var x=0; x<map_info.background.xsize; x++) { /* (2) Background layers */
		for (var y=0; y<map_info.background.ysize; y++) {
			var src = map_info.background.name+'-'+x+'-'+y+'.png';
			var elm = $(document.createElement('img'));
			elm.src = src;
			$('datapane').appendChild(elm);
			elm.setStyles({
				'left':(x*map_info.background.width),
				'top':(y*map_info.background.height),
				'z-index':zDig--,
				'position': 'absolute'
			});
			map_back.push({object:elm, x:(x*map_info.background.width), y:(y*map_info.background.height)});
		};
	};
	
	// Render static objects
	map_status('Rendering Map...');
	$each(map_info.objects, function(e) {
		e.image = map_info.images[e.image]; /* Images are stored with their ID */
		map_addobject(e);
	});	
	
	// Raise the curtains!
	map_curtain(false);
}

/**
  * Event Callback for objects
  *
  * This function is used as callback for the events on the map objects
  *
  */
function map_objecttrigger(id, trigger, e) {
	if (trigger=='contextmenu') {
		window.alert(map_objects[id].info.title+' ID='+id+' UID='+map_object_index[id]);
	}
}

/**
  * Place/Update dynamic map objects
  *
  * This function places new dynamic objects on the map or
  * removes or updates old ones.
  *
  * The changes are detected using differential check with
  * the data previously passed on the function. 
  * The primary key for the difference check operation is the
  * 'id' fiend of the data entries
  *
  */
function map_updatedata(data) {
	
	// We don't have previous data?
	if (map_dynamic_objects.length == 0) {
		
		// Store the data provided
		map_dynamic_objects = data;
		
		// And place them on map
		$each(data, function(e,k) {
			map_dynamic_objects[k].index = map_addobject(e);
		});
	
	// We DO have previous data?
	} else {
		
		// Perform a differential check and find
		// removed, new and changed objects
		var ignore = []
		var removed = [];
		var changed = [];
				
		// Check removed or changed
		for (var i=0; i<map_dynamic_objects.length; i++) {
			var found = false;
			var idcheck = false;
			for (var j=data.length-1; j>=0; j--) {
				// Do we have an ID? Use ID as difference check key...
				if ($defined(data[j].id) && $defined(map_dynamic_objects[i].id)) {
					if (data[j].id == map_dynamic_objects[i].id) {
						// We found it
						found = true;				
						// So, ignore this item
						ignore.push(j);
						// But hold the object to check differencies
						idcheck=data[j];
						break;
					}
				} else {
					// No ID? Use [x,y,image] as difference check key...
					if ((data[j].x == map_dynamic_objects[i].x) && (data[j].y == map_dynamic_objects[i].y) && (data[j].image == map_dynamic_objects[i].image)) {
						// We found it
						found = true;
						// So, ignore this item
						ignore.push(j);
						break;
					}
				}
			}
			
			if (!found) {
				// Not found on new data? The object is removed
				removed.push(map_dynamic_objects[i].index);
			} else if (idcheck!=false) {
				// Update dynamic objects
				// (We cannot know if an object has actually changed or not, since
				//  not all of the variables are used by the map system)
				changed.push({index:map_dynamic_objects[i].index, cdata:idcheck});
			}
		}
		
		// [ Now that we are out of loops and locked arrays, perform all the required operations ]
				
		// #1) Remove all disposed objects
		for (var i=removed.length-1; i>=0; i--) {
			map_removeobject(removed[i]);
		}

		// Reset map_dynamic_objects and prepare it for new stacking
		map_dynamic_objects = [];

		// #2) Update all altered objects
		for (var i=0; i<changed.length; i++) {
			changed[i].cdata.index = changed[i].index;
			map_dynamic_objects.push(changed[i].cdata);

			map_updateobject(changed[i].index,changed[i].cdata);			
		}

		// #3) Create all new items
		//     Note: All what is left on the data array are new items
		for (var i=0; i<data.length; i++) {
			if (ignore.indexOf(i)<0) {
				var dat = data[i];
				var index = map_addobject(dat);
				dat.index = index;
				map_dynamic_objects.push(dat);
			}
		}

	}
	
}

/**
  * Place a map object
  *
  * This function places a new object on the map grid
  *
  */
function map_addobject(data) {
	// Store default values if something is missing
	if (!$defined(data.cx)) data.cx=0;
	if (!$defined(data.cy)) data.cy=0;
	
	// Re-map x-y
	var x=data.x*32-data.cx;
	var y=data.y*32-data.cy;

	// Calculate new Z-Index
	var y32bit = Math.round(y/32);
	var zindex = y32bit*500+x;
	if (zindex<0) zindex=1;
	
	// Create and insert image
	var im = $(document.createElement('img'));
	im.src = data.image;
	$('datapane').appendChild(im);
	im.setStyles({
		'position': 'absolute',
		'left': x-map_scroll_pos.x,
		'top': y-map_scroll_pos.y,
		'z-index': zindex
	});
	var size = im.getSize().size;
	
	// Cache all the instances
	var uid=map_last_id++;
	var id=map_objects.length;
	map_object_index[id]=uid;
	map_objects[id]={
		'info': data,
		'x': x,
		'y': y,
		'width': size.x,
		'height': size.y,
		'cx': data.cx,
		'cy': data.cy,
		'object': im
	}

	// If the object is dynamic, append triggers and allow advanced show effects
	if (data.dynamic) {
		im.addEvent('contextmenu', function(e) {
			var e = new Event(e);
			map_objecttrigger(id, 'contextmenu', e);						
			e.stop();
		});
		im.addEvent('mousemove', function(e) {
			var e = new Event(e);
			im.setStyles({'opacity':0.7});
			map_objecttrigger(id, 'mousemove', e);
			e.stop();
		});
		im.addEvent('mouseout', function(e) {
			var e = new Event(e);
			im.setStyles({'opacity':1});
			map_objecttrigger(id, 'mouseout', e);
			e.stop();
		});
		im.addEvent('mousedown', function(e) {
			var e = new Event(e);
			map_objecttrigger(id, 'mousedown', e);
			e.stop();
		});
		im.addEvent('mouseup', function(e) {
			var e = new Event(e);
			map_objecttrigger(id, 'mouseup', e);
			e.stop();
		});
		
		if ($defined(data.fx_show)) {						
			switch (data.fx_show) {
				case 'fade':
					imfx=new Fx.Styles(im, {wait: false, duration: 400, transition: Fx.Transitions.Quad.easeIn});
					im.setStyles({
						'opacity':0
					});
					imfx.start({
						'opacity':1		   
					});
					break;
				
				case 'pop':
					imfx=new Fx.Styles(im, {wait: false, duration: 400, transition: Fx.Transitions.Back.easeOut});
					im.setStyles({
						'opacity':0,
						'top':y+32-map_scroll_pos.y
					});
					imfx.start({
						'opacity':1,
						'top':y-map_scroll_pos.y
					});
					break;

				case 'drop':
					imfx=new Fx.Styles(im, {wait: false, duration: 400, transition: Fx.Transitions.Bounce.easeOut});
					im.setStyles({
						'opacity':0,
						'top':y-200-map_scroll_pos.y
					});
					imfx.start({
						'opacity':1,
						'top':y-map_scroll_pos.y
					});
					break;				

				case 'zoom':
					imfx=new Fx.Styles(im, {wait: false, duration: 400,transition: Fx.Transitions.Quad.easeIn});
					im.setStyles({
						'opacity':0,
						'width': 5,
						'height': 5,
						'left': (x+(size.x/2)-map_scroll_pos.x),
						'top': (y+(size.y/2)-map_scroll_pos.y)
					});
					imfx.start({
						'opacity':1,
						'width': size.x,
						'height': size.y,
						'left': x-map_scroll_pos.x,
						'top': y-map_scroll_pos.y
					});
					break;				
			}
		}
	}
	
	// Return the new structure
	return uid;
}

/**
  * Remove an object from map
  *
  * This function removes an object from map.
  * It also performs disposal animation, based on the information
  * stored in the map_objects array
  *
  */
function map_removeobject(uid, nofx) {
	var id=map_object_index.indexOf(uid);
	var data = map_objects[id];
	if (!data) return;
	
	// Function that will be chained or directly
	// executed and will remove the object
	var disposer = function() {
		// Remove the object
		data.object.remove();
		map_objects.splice(id,1);
		map_object_index.splice(id,1);
	}
	
	// Do the dispose effect (if specified)
	if (data.info.fx_hide && !nofx) {
		switch (data.info.fx_hide) {
			case 'fade':
				imfx=new Fx.Styles(data.object, {wait: false, duration: 400, transition: Fx.Transitions.Quad.easeOut});
				imfx.start({
					'opacity':0
				}).chain(disposer);
				break;
			
			case 'pop':
				imfx=new Fx.Styles(data.object, {wait: false, duration: 400, transition: Fx.Transitions.Back.easeIn});
				imfx.start({
					'opacity':0,
					'top':data.y+32-map_scroll_pos.y
				}).chain(disposer);
				break;

			case 'drop':
				imfx=new Fx.Styles(data.object, {wait: false, duration: 400, transition: Fx.Transitions.Back.easeIn});
				imfx.start({
					'opacity':0,
					'top':data.y-200-map_scroll_pos.y
				});
				break;

			case 'zoom':
				imfx=new Fx.Styles(data.object, {wait: false, duration: 400,transition: Fx.Transitions.Quad.easeIn});
				imfx.start({
					'opacity':0,
					'width': 5,
					'height': 5,
					'left': (data.x+(data.width/2)-map_scroll_pos.x),
					'top': (data.y+(data.height/2)-map_scroll_pos.y)
				});
				break;				

			default:
				disposer();
		}
	} else {
		disposer();	
	}
}
 
/**
  * Update a map object
  *
  * This function updates a map object. If the object contains
  * animation effects, they will be applied after the local data
  * cache update
  *
  */
function map_updateobject(uid,data) {
	var id=map_object_index.indexOf(uid);
	var old_data = map_objects[id];
	if (!old_data) return;
		
	// Store default values if something is missing
	if (!$defined(data.cx)) data.cx=old_data.cx;
	if (!$defined(data.cy)) data.cy=old_data.cy;
	if (!$defined(data.x)) data.x=old_data.info.x;
	if (!$defined(data.y)) data.y=old_data.info.y;
	
	// Re-map x-y
	var x=data.x*32-data.cx;
	var y=data.y*32-data.cy;

	// Calculate new Z-Index
	var y32bit = Math.round(y/32);
	var zindex = y32bit*500+x;
	if (zindex<0) zindex=1;
	
	// Update image
	old_data.object.src = data.image;

	// Update cache
	old_data.x = x;
	old_data.y = y;
	old_data.info = data;
	
	// If we have transition, use them.
	if (data.fx_move) {
		switch (data.fx_move) {
			case 'slide':
				px_transition=new Fx.Styles(old_data.object, {duration: 800, unit: 'px', transition: Fx.Transitions.linear});
				z_transition=new Fx.Styles(old_data.object, {duration: 800, unit: '', transition: Fx.Transitions.linear});				
				px_transition.start({
						'left': x-map_scroll_pos.x
				}).chain(function() {
						px_transition.start({'top': y-map_scroll_pos.y});
						z_transition.start({'z-index':zindex});
				});
				break;

			case 'bounce':
				px_transition=new Fx.Styles(old_data.object, {duration: 800, unit: 'px', transition: Fx.Transitions.Elastic.easeOut});
				px_transition.start({
						'left': x-map_scroll_pos.x,
						'top': y-map_scroll_pos.y
				});
				old_data.object.setStyles({
						'z-index':zindex
				});
				break;

			case 'fade':
				imfx=new Fx.Styles(old_data.object, {wait: false, duration: 400, transition: Fx.Transitions.Quad.easeOut});
				imfx.start({
					'opacity':0
				}).chain(function(){
					old_data.object.setStyles({
						'left': x-map_scroll_pos.x,
						'top': y-map_scroll_pos.y,
						'z-index': zindex
					});			
					imfx.start({
						'opacity':1
					})
				});				
				break;

			default:
				old_data.object.setStyles({
					'left': x-map_scroll_pos.x,
					'top': y-map_scroll_pos.y,
					'z-index': zindex
				});			
		}		
	
	// Elseways, just update the object
	} else {		
		// Update object		
		old_data.object.setStyles({
			'left': x-map_scroll_pos.x,
			'top': y-map_scroll_pos.y,
			'z-index': zindex
		});
	}
	
	
	// Javascript uses byRef for
	// objects. That means the stored
	// object in the array is now updated
}
 