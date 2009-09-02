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
		e.object.dispose();
	});
	
	// Remove background
	$each(map_back, function(e,k){
		e.object.dispose();
	});
	
	// Reset variables
	map_current = '';
	map_center_fx = null;
	map_scroll_pos = {x:0,y:0};	
	map_dynamic_objects = [];
	map_objects = [];
	map_info = [];
	map_back = [];
	map_object_index = [];
	map_last_id = 1;
	map_viewpoint = {x:0,y:0};
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
	// Hide the data buffer (if exists)
	// and show map
	clearDisplayBuffer();
	$('databuffer').setStyle('visibility','hidden');
	$('datapane').setStyle('visibility','visible');	

	// Download the JSON map
	showStatus('Loading Map...');
	map_current = mapname;
	var data = new Request.JSON({url: 'data/maps/'+mapname+'.gmap',
			onSuccess: function(o) {
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
	showStatus('Loading Graphics...');
	var lt_timer=null; /* Loading Timeout */
	var lt_finalized=false;
	
	// Find out all the images that are required
	var images=map_info.images; /* (1) Overlaies */
	
	// Append the appropriate path on the static images
	$each(images, function(e,k) {
		var s = new String(e);
		if (s.indexOf('.php')>0){
		} else {
			images[k]='images/elements/'+e;
		}
	});

	for (var x=0; x<map_info.background.xsize; x++) { /* (2) Background layers */
		for (var y=0; y<map_info.background.ysize; y++) {
			images.push('data/maps/'+map_info.background.name+'-'+x+'-'+y+'.png');
		};
	};
	images.push('images/tiles/'+map_info.background); /* (3) Background tile */
	
	// Precache all map images
	//new Asset.images(images, {
	ImageLoader.preload(images, {
		onComplete: function(){
			lt_finalized=true;
			if (lt_timer) {clearTimeout(lt_timer);lt_timer=null;};
			map_finalize();			
		},
		onProgress: function(img_id, img_obj) {			
			// If we are completed, this is just a late call..
			// ignore it...
			if (!lt_finalized) {				
				var perc = Math.ceil(100*img_id/images.length);
				if (perc > 100) perc-=100; /* When objects are already cached, the maximum value seems to be 200% */
				showStatus('Loading Graphics ['+perc+' %]');
				
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
	$('datahost').setStyles({
		'background-image': 'url(images/tiles/'+map_info.background.fill+')'						
	});
	
	// Render background
	var zDig=0;
	for (var x=0; x<map_info.background.xsize; x++) { /* (2) Background layers */
		for (var y=0; y<map_info.background.ysize; y++) {
			var src = 'data/maps/'+map_info.background.name+'-'+x+'-'+y+'.png';
			var elm = $(document.createElement('img'));
			elm.setProperty('src',src);
			$('datapane').appendChild(elm);
			elm.setStyles({
				'left':(x*map_info.background.width),
				'top':(y*map_info.background.height),
				'position': 'absolute',
				'width': map_info.background.width,
				'height': map_info.background.height
			});
			map_back.push({object:elm, x:(x*map_info.background.width), y:(y*map_info.background.height)});
		};
	};
	
	// Render static objects
	showStatus('Rendering Map...');
	$each(map_info.objects, function(e) {
		e.image = map_info.images[e.image]; /* Images are stored with their ID */
		map_addobject(e);

	});	
	
	// Raise the curtains!
	map_curtain(false);
	showStatus();
}

/**
  * Preload dynamic objecs
  *
  * This function is used to preload any delay-loaded objects (using JSON)
  * This is used to make sure the newly objects contain the correct dimensions.
  *
  */
  
var map_preloaded_stack = [];

function map_preloaded_update(data) {
	// And place them on map
	var images = [];
	var lt_timer = null;
	var lt_finalized = false;
	
	$each(data, function(e,k) {
		// Accelerate operation by keeping the names of the objects
		// that are already loaded
		if (map_preloaded_stack.indexOf(e.image)<0) {
			images.push(e.image);
			map_preloaded_stack.push(e.image);
		} else {
		}
	});	
	
	// Precache all map images
	if (images.length>0) {
		showStatus('Loading new objects...');
		//new Asset.images(images, {
		ImageLoader.preload(images, {						   
			onComplete: function(){
				showStatus();
				map_updatedata(data);
			},
			onProgress: function(img_id) {
				// Store the object image
				//map_images[images[img_id]] = this;
			}
		});
	} else {
		map_updatedata(data);	
	}

}

