// Identify browser
var isInternetExplorer=(navigator.userAgent.indexOf("MSIE")>=0);
var isMozilla=(navigator.userAgent.indexOf("Gecko")>=0);
var isOpera=(navigator.userAgent.indexOf("Opera")>=0);

// JavaScript Document
function $trace(obj,short) {

	var ident = function(v) {
		var parts = new String(v);
		var ans = '';
		parts = parts.split("\n");
		$each(parts, function(e){
			ans+='	'+e+'\n';
		});
		return ans;
	}
	
	ans='';
	$each(obj, function(value, name) {
		if (ans!='') ans+=",\n ";
		ans+='['+name+'] = ';
		if (!short) {
			if (typeof(value)=='object') {
				ans+=" {\n"+ident($trace(value))+"\n}";
			} else if (typeof(value)=='array') {
				ans+=" [\n"+ident($trace(value))+"\n]";
			} else {
				ans+=value;
			}
		} else {
			ans+=value;
		}
	});	
	
	return ans;
}

var tiles = [];
var images = [];
var paint_image = '';
var painting = false;
var paint_x=0, paint_y=0;
var last_x=0, last_y=0;

function paint(x,y) {
	var im = $(document.createElement('img'));
	$('content').appendChild(im);	
	im.src=paint_image;
	im.setStyles({
		'left': x*32,
		'top': y*32
	});
	images.push(im);
}


/**
  * JSON Communication system
  *
  */
var json_timer = null;
var json_msgtimer = null;

function json_clearmessage() {
	if (json_msgtimer) clearTimeout(json_msgtimer);
	json_msgtimer = setTimeout(function() {
		$('json_output').setHTML('');
		$('json_output').setStyles({
			'visibility': 'hidden'
		});
	},5000);
}
  
function json_message(msg) {
	$('json_output').setHTML(msg);
	$('json_output').setStyles({
		'visibility': 'visible'
	});
	json_clearmessage();
}
  
function json_save(){
	json_message('Saving...');
	try {
		// Build request
		var rqdata = [];
		for (var grid_id=0; grid_id<3; grid_id++) {
			var gdata = [];	
			for (coord in paint_grid[grid_id]) {				
				var p = new String(coord);
				if (p.indexOf(',')>0){
					p = p.split(',');
					if (paint_grid[grid_id][coord]!=null) {
						gdata.push({'x':p[0], 'y':p[1], 's': paint_grid[grid_id][coord].src});
					}
				}
			}
			rqdata.push(gdata);
		}

		var json = new Json.Remote('feed.php?a=save', {
			headers: {'X-Request': 'JSON'},
			onComplete: function(obj) {
				json_message('Saved!');
			},
			onFailure: function(err) {
				json_message('Error! '+err);
			}
		}).send(rqdata);
	} catch (e) {
		json_message('Error! '+e.message);
	}
}

function json_load() {
	json_message('Loading...');
	try {
		var json = new Json.Remote('feed.php?a=load', {
			headers: {'X-Request': 'JSON'},
			onComplete: function(obj) {
			
				json_message('Rendering...');
	
				// We got the object. Do the loading...
				paint_reset();
				for (var i=0; i<3; i++) {
					paint_layer = i+1;
					$each(obj[i], function(e) {
						paint_put(e.x,e.y,e.s);
				   });
				}
				
				json_message('Loaded!');
			
			},
			onFailure: function(err) {
				json_message('Error! '+err);
			}
			
		}).send();
	} catch (e) {
		json_message('Error! '+e.message);
	}
}


/**
  * Interface button feedback handler
  *
  */

function ui_new() {
	if (window.confirm('Do you really want to erase this map? This action is not undoeable!')) {
		paint_reset();
	}
}

function ui_save() {
	json_save();
}

function ui_load() {
	json_load();
}

function ui_objects() {
	
}

function ui_clear() {
	$('tiles_clear').setStyles({'background-color':'#FFFFFF'});
	$('tiles_put').setStyles({'background-color':''});
	brush_erase=true;
}

function ui_put() {
	$('tiles_clear').setStyles({'background-color':''});
	$('tiles_put').setStyles({'background-color':'#FFFFFF'});
	brush_erase=false;
}

/**
  * Dropdown menu handler
  *
  */
var ddmenu_object = null;

function dropdown_show(x,y,menus) {
	var e = document.createElement('div');
	if (!isInternetExplorer) { e.setAttribute('class', 'dropdownmenu') } else { e.setAttribute('className', 'dropdownmenu') };
	$each(menus, function(m) {
		var a = document.createElement('a');
		a.href = m[0];
		a.innerHTML=m[1];
		e.appendChild(a);
	});
	ddmenu_object=e;
	$(document.body).appendChild(e);
	e.addEvent('mousedown',function(e){var e=new Event(e); e.stop()});
	e.addEvent('mouseup',function(e){var e=new Event(e); setTimeout(dropdown_dispose,10); e.stop()});
	e.setStyles({
		'left': x,
		'top': y
	});
}

function dropdown_dispose() {
	if (ddmenu_object){
		ddmenu_object.remove();
		ddmenu_object=null;
	}
}

/**
  * Tiles loader system
  *
  */
var tiles_base = '';
var tiles_cache = [];

function tloader_download(tileset) {
	tiles_base = tileset;
	
	tloader_reset();
	selection_reset();
	
	$('tiles_set').setStyles({'display':'none'});
	$('tiles_status').setStyles({'display':''});
	$('tiles_status').setHTML('<center>Downloading information...</center>');

	var data = new Json.Remote('feed.php?base='+tileset, {
			onComplete: function(o) {
				tloader_preload(o);
			},
			onFailure: function(e) {
				window.alert(e.message);
			}
	}).send();	
}

function tloader_preload(images) {
	var lt_timer=null; /* Loading Timeout */
	var lt_finalized=false;

	new Asset.images(images, {
		onComplete: function(e){
			lt_finalized=true;
			if (lt_timer) {clearTimeout(lt_timer);lt_timer=null;};
			tloader_renderimages(images);
		},
		onProgress: function(img_id) {
			// If we are completed, this is just a late call..
			// ignore it...
			if (!lt_finalized) {
				var perc = Math.ceil(100*img_id/images.length);
				if (perc > 100) perc-=100; /* When objects are already cached, the maximum value seems to be 200% */
				$('tiles_status').setHTML('<div class="progress_bar"><div style="width: '+perc+'%;">&nbsp;</div></div>');
				
				// More than a second of delay between two images is too much
				// Probably it is stucked 
				// BUGFIX: 1) Unreasonable stops on 99% on IE
				//         2) Blocks when file does not exists
				if (lt_timer) {clearTimeout(lt_timer); lt_timer=null;};
				lt_timer=setTimeout(function(){ tloader_renderimages(images); }, 2000);
			}
		}
	});
}

function tloader_renderimages(images) {
	$('tiles_status').setHTML('<center>Displaying...</center>');
	var cur_image=0;
	var timer = setInterval(function() {
		for (var i=cur_image; i<cur_image+8; i++) {
			tloader_spawnimage(images[i]);
		}
		cur_image=i;
		if (i>=images.length) {
			$('tiles_status').setStyles({'display':'none'});
			$('tiles_set').setStyles({'display':''});
			clearInterval(timer);
		}
	},10);
}

function tloader_reset() {
	$each(tiles_cache, function(e){
		e.remove();
	});
	tiles_cache=[];
}

function tloader_spawnimage(src) {
	var im = $(document.createElement('img'));
	$('tiles_host').appendChild(im);	
	im.src=src;
	tiles_cache.push(im);
}

/**
  * Painting system
  *
  */

// ### Object area painting ###
var objpaint_elements = [];
var objpaint_grid = [[],[],[]];
var objpaint_layer = 1;

function objpaint_spawn(image,layer) {
	var im = $(document.createElement('img'));
	$('content_layer'+layer).appendChild(im);	
	im.src='../../images/tiles/'+image;
	objpaint_elements.push(im);
	return im;
}

function objpaint_reset() {
	$each(objpaint_elements, function(e){
		e.remove();								   
   });
	objpaint_elements = [];
	objpaint_grid = [[],[],[]];
}

function objpaint_updateblock(x,y,image,layer) {
	var id=x+','+y;
	if ($defined(objpaint_grid[layer-1][id])) {
		objpaint_grid[layer-1][id].src='../../images/tiles/'+image;
		return objpaint_grid[layer-1][id];		
	} else {
		objpaint_grid[layer-1][id] = objpaint_spawn(image,layer);
		return objpaint_grid[layer-1][id];
	}
}

function objpaint_put(x,y,image) {
	var im = objpaint_updateblock(x,y,image,paint_layer);
	im.setStyles({
		'left': x*32,
		'top': y*32
    });
}

// ### Main area painting ###
var paint_elements = [];
var paint_grid = [[],[],[]];
var paint_layer = 1;

function paint_spawn(image,layer) {
	var im = $(document.createElement('img'));
	$('content_layer'+layer).appendChild(im);	
	im.src='../../images/tiles/'+image;
	paint_elements.push(im);
	return im;
}

function paint_reset() {
	$each(paint_elements, function(e){
		try {
			e.remove();								   
		} catch(e) {
			// If the object is removed, the instances will
			// be removed. If this hapens, the previous object
			// will refer to nothing and raise an exception.
		}
	});
	paint_elements = [];
	paint_grid = [[],[],[]];
}

function paint_updateblock(x,y,image,layer) {
	var id=x+','+y;
	if ($defined(paint_grid[layer-1][id])) {
		paint_grid[layer-1][id].src='../../images/tiles/'+image;
		return paint_grid[layer-1][id];		
	} else {
		paint_grid[layer-1][id] = paint_spawn(image,layer);
		return paint_grid[layer-1][id];
	}
}

function paint_clear(x,y) {
	var id=x+','+y;
	if ($defined(paint_grid[paint_layer-1][id])) {
		paint_grid[paint_layer-1][id].remove();
		paint_grid[paint_layer-1][id]=null;
	}	
}

function paint_put(x,y,image) {
	var im = paint_updateblock(x,y,image,paint_layer);
	im.setStyles({
		'left': x*32,
		'top': y*32
    });
}

/**
  * Brush handling system
  *
  */
var brush_elements = [];
var brush_position = {x:0,y:0};
var brush_selection = {x:0,y:0,w:0,h:0};
var brush_dragging = false;
var brush_dragused = false;
var brush_erase = false;

function _brush_clean() {
	for (var y=brush_selection.y; y<brush_selection.y+brush_selection.h; y++) {
		for (var x=brush_selection.x; x<brush_selection.x+brush_selection.w; x++) {
			paint_clear(x+scroll_offset.x,y+scroll_offset.y);
		}
	}
}

function _brush_blit(sel,dest) {
	var sx=sel.x;
	var sy=sel.y;	
	
	for (var y=dest.y; y<dest.y+dest.h; y++) {
		for (var x=dest.x; x<dest.x+dest.w; x++) {
			paint_put(x+scroll_offset.x,y+scroll_offset.y,tiles_base+'-'+sx+'-'+sy+'.png');
			sx++;
			if (sx>=sel.x+sel.w) {
				sx=sel.x;
			}
		}
		sy++;	
		if (sy>=sel.y+sel.h) {
			sy=sel.y;
		}
	}
}

function _brush_stretch() {
	var dst_center = {x:brush_selection.x,y:brush_selection.y,w:brush_selection.w,h:brush_selection.h};
	var src_center = {x:selection.x,y:selection.y,w:selection.w,h:selection.h};
	var corners = {tl:false, tr:false, bl:false, br:false};

	// Add some helping coordinates
	brush_selection.r=brush_selection.x+brush_selection.w-1;
	brush_selection.b=brush_selection.y+brush_selection.h-1;
	brush_selection.c={x:brush_selection.x+Math.floor(brush_selection.w/2),y:brush_selection.y+Math.floor(brush_selection.h/2)};
	selection.r=selection.x+selection.w-1;
	selection.b=selection.y+selection.h-1;
	selection.c={x:selection.x+Math.floor(selection.w/2),y:selection.y+Math.floor(selection.h/2)};

	if (selection.w>2) {		
		if (selection.h>2) {
			// * * *
			// *   *
			// * * *
			
			// 4 Corners
			_brush_blit({x:selection.x, y:selection.y, h:1,w:1},
						{x:brush_selection.x, y:brush_selection.y, h:1,w:1});
			_brush_blit({x:selection.r, y:selection.y, h:1,w:1},
						{x:brush_selection.r, y:brush_selection.y, h:1,w:1});
			_brush_blit({x:selection.x, y:selection.b, h:1,w:1},
						{x:brush_selection.x, y:brush_selection.b, h:1,w:1});
			_brush_blit({x:selection.r, y:selection.b, h:1,w:1},
						{x:brush_selection.r, y:brush_selection.b, h:1,w:1});

			// 4 Sides
			_brush_blit({x:selection.x, y:selection.y+1, h:selection.h-2,w:1},
						{x:brush_selection.x, y:brush_selection.y+1, h:brush_selection.h-2,w:1});
			_brush_blit({x:selection.r, y:selection.y+1, h:selection.h-2,w:1},
						{x:brush_selection.r, y:brush_selection.y+1, h:brush_selection.h-2,w:1});
			_brush_blit({x:selection.x+1, y:selection.y, h:1,w:selection.w-2},
						{x:brush_selection.x+1, y:brush_selection.y, h:1,w:brush_selection.w-2});
			_brush_blit({x:selection.x+1, y:selection.b, h:1,w:selection.w-2},
						{x:brush_selection.x+1, y:brush_selection.b, h:1,w:brush_selection.w-2});
			
			// Resize center
			src_center.x+=1;
			src_center.y+=1;
			src_center.w-=2;
			src_center.h-=2;
			dst_center.x+=1;
			dst_center.y+=1;
			dst_center.w-=2;
			dst_center.h-=2;
			
		} else {
			// * - *
			// *   *
			// * - *

			// 2 Sides Left/Right
			_brush_blit({x:selection.x, y:selection.y, h:selection.h,w:1},
						{x:brush_selection.x, y:brush_selection.y, h:brush_selection.h,w:1});
			_brush_blit({x:selection.r, y:selection.y, h:selection.h,w:1},
						{x:brush_selection.r, y:brush_selection.y, h:brush_selection.h,w:1});

			// Resize center
			src_center.x+=1;
			src_center.w-=2;
			dst_center.x+=1;
			dst_center.w-=2;
		}
	} else {
		if (selection.h>2) {
			// * * *
			// |   |
			// * * *
			
			// 2 Sides Up/Down
			_brush_blit({x:selection.x, y:selection.y, h:1,w:selection.w},
						{x:brush_selection.x, y:brush_selection.y, h:1,w:brush_selection.w});
			_brush_blit({x:selection.x, y:selection.b, h:1,w:selection.w},
						{x:brush_selection.x, y:brush_selection.b, h:1,w:brush_selection.w});
			
			// Resize center
			src_center.y+=1;
			src_center.h-=2;
			dst_center.y+=1;
			dst_center.h-=2;
		} else {

		}
	}

	// Render center
	_brush_blit(src_center,dst_center);
}

function _brush_fill() {
	_brush_blit(selection,brush_selection);
}

function _brush_copy_once() {
	for (var y=selection.y; y<selection.y+selection.h; y++) {
		for (var x=selection.x; x<selection.x+selection.w; x++) {
			paint_put(
				brush_selection.x+(x-selection.x),
				brush_selection.y+(y-selection.y),
				tiles_base+'-'+x+'-'+y+'.png');			
		}
	}
}

function brush_apply(layer) {
	if (!brush_erase) {
		if (brush_dragused) {
			if ((brush_selection.w==1)&&(brush_selection.h==1)) {
				// Used rectangle but canceled? Return..
				return;
			} else {			
				_brush_stretch();
			}
		} else {
			_brush_copy_once();
		}
	} else {
		_brush_clean();	
	}
}

function brush_selection_updateview() {
	if ((brush_selection.w==1)&&(brush_selection.h==1)) {
		$('content_selection').setStyles({
			'visibility':'hidden'
		});	
		brush_show();
	} else {
		$('content_selection').setStyles({
			'visibility':'visible',
			'left': brush_selection.x*32,
			'top': brush_selection.y*32,
			'width': brush_selection.w*32,			
			'height': brush_selection.h*32
		});
		brush_dragused=true; /* The drag is used */
		brush_hide();
	}
}

function brush_selection_put(x,y) {
	brush_position.x=x;
	brush_position.y=y;
	brush_selection.x=x;
	brush_selection.y=y;
	brush_selection.w=1;
	brush_selection.h=1;
	
	brush_dragused = false; /* Reset "drag is used" flag */
	brush_selection_updateview();
}

function brush_selection_drag(x,y) {
	var dragging = false;
	
	if (x<brush_position.x) {
		dragging=true;
		brush_selection.x=x;
		brush_selection.w=brush_position.x-x+1;
	} else if (x>brush_position.x) {
		dragging=true;
		brush_selection.x=brush_position.x;
		brush_selection.w=x-brush_position.x+1;
	} else {
		brush_selection.x=brush_position.x;
		brush_selection.w=1;
	}
	if (y<brush_position.y) {
		dragging=true;
		brush_selection.y=y;
		brush_selection.h=brush_position.y-y+1;
	} else if (y>brush_position.y) {
		dragging=true;
		brush_selection.y=brush_position.y;
		brush_selection.h=y-brush_position.y+1;
	} else {
		brush_selection.y=brush_position.y;
		brush_selection.h=1;
	}
	
	brush_selection_updateview();
}

function brush_reset() {
	$each(brush_elements, function(e){
		e.o.remove();
	});
	brush_elements=[];
}

function brush_show() {
	$each(brush_elements, function(e){
		e.o.setStyles({
			'visibility':'visible'			  
 	    });
	});	
}

function brush_hide() {
	$each(brush_elements, function(e){
		e.o.setStyles({
			'visibility':'hidden'
 	    });
	});	
}

function brush_spawnimage(image) {
	var im = $(document.createElement('img'));
	$('content_data').appendChild(im);	
	im.src='../../images/tiles/'+image;
	im.setStyles({'opacity':0.5});
	return im;
}

function brush_updateview(){
	// Clear brush
	brush_reset();
	
	// Load brush info from the selection
	for (var y=selection.y; y<selection.y+selection.h; y++) {
		for (var x=selection.x; x<selection.x+selection.w; x++) {
			var im = brush_spawnimage(tiles_base+'-'+x+'-'+y+'.png');
			brush_elements.push({'o':im, 'x':(x-selection.x)*32, 'y':(y-selection.y)*32});
		}
	}
	
	// Update brush position
	brush_move(brush_position.x,brush_position.y);
}

function brush_move(x,y) {
	$each(brush_elements, function(e){
		e.o.setStyles({
			'left':e.x+x*32,
			'top':e.y+y*32
		});
	});
	brush_position.x=x;
	brush_position.y=y;
}

/**
  * Selection handling system
  *
  */
var selection = {x:0,y:0,w:0,h:0};
var selection_dragbase = {x:0, y:0};
var selection_dragging = false;

function selection_reset(){
	selection = {x:0,y:0,w:1,h:1};
	selection_dragbase = {x:0, y:0};
	selection_dragging = false;
	selection_updateview();
}

function selection_updateview(){
	$('tiles_select').setStyles({
		'left': selection.x*33,
		'top': selection.y*33,
		'width': selection.w*33,
		'height': selection.h*33
	});
}
function selection_start(x,y) {
	selection.x=x;
	selection.y=y;
	selection.w=1;
	selection.h=1;
	selection_dragbase.x=x;
	selection_dragbase.y=y;
	
	selection_updateview();	
	brush_updateview();
}
function selection_dragto(x,y) {
	if (x<selection_dragbase.x) {
		selection.x=x;
		selection.w=selection_dragbase.x-x+1;
	} else if (x>selection_dragbase.x) {
		selection.x=selection_dragbase.x;
		selection.w=x-selection_dragbase.x+1;
	} else {
		selection.x=selection_dragbase.x;
		selection.w=1;
	}
	if (y<selection_dragbase.y) {
		selection.y=y;
		selection.h=selection_dragbase.y-y+1;
	} else if (y>selection_dragbase.y) {
		selection.y=selection_dragbase.y;
		selection.h=y-selection_dragbase.y+1;
	} else {
		selection.y=selection_dragbase.y;
		selection.h=1;
	}
	
	selection_updateview();
	brush_updateview();
}


/**
  *  Area resizing and scrolling
  */

var scroll_active = false;
var scroll_hit = {x:0, y:0};
var scroll_base = {x:0, y:0};
var scroll_offset = {x:0, y:0};

function scroller_setpos(x,y) {
	if (x>0) x=0;
	if (y>0) y=0;
	
	x=Math.round(x/32)*32;
	y=Math.round(y/32)*32;
	
	scroll_offset.x = -Math.round(x/32);
	scroll_offset.y = -Math.round(y/32);
	
	$('content_layer1').setStyles({'left':x,'top':y});
	$('content_layer2').setStyles({'left':x,'top':y});
	$('content_layer3').setStyles({'left':x,'top':y});
	$('content_objects').setStyles({'left':x,'top':y});
	$('spacer').setStyles({
		'left':0, 'top':0, 'width':x+758, 'height':y+502
	});
}

function scroller_start(x,y) {
	var pos = $('content_layer1').getPosition();
	scroll_hit.x=x;
	scroll_hit.y=y;
	scroll_base.x=pos.x;
	scroll_base.y=pos.y;
	$('msg').setHTML('Started: '+$trace(scroll_hit)+' Base:'+$trace(scroll_base));
}

function scroller_move(x,y) {
	scroller_setpos(scroll_base.x+(x-scroll_hit.x),scroll_base.y+(y-scroll_hit.y));
}


/**
================================================================================================================================
================================================================================================================================
**/

$(window).addEvent('load', function(e){	

	tloader_download('z-field-ext');

	$('content_host').addEvent('mousemove', function(e){
		var e = new Event(e);
		
		var offset = $('content_host').getSize().scroll;
		var position = e.client;
		var x = e.client.x+offset.x-$('content_host').getLeft();
		var y = e.client.y+offset.y-$('content_host').getTop();
		
		x=Math.floor(x/32);
		y=Math.floor(y/32);
		paint_x=x;
		paint_y=y;
		
		if (scroll_active) {
			scroller_move(e.client.x,e.client.y);
		} else if (brush_dragging) {
			brush_selection_drag(x,y);
		} else {
			brush_move(x,y);
			brush_selection_put(x,y);
		}
		e.stop();
	});
	
	$('content_host').addEvent('mousedown', function(e){
		var e = new Event(e);
		dropdown_dispose();	
		if (e.event.button == 1) {
			scroller_start(e.client.x,e.client.y);
			scroll_active = true;
		} else if (e.event.button == 0) {
			brush_dragging = true;			
		}
		e.stop();
	})
	
	$('content_host').addEvent('mouseup', function(e){
		var e = new Event(e);
		if (brush_dragging) {
			if (e.shift) {
				paint_layer=3;
			} else if (e.control) {
				paint_layer=1;
			} else {
				paint_layer=2;
			}
			brush_apply();
		}
		brush_dragging = false;
		scroll_active = false;
		e.stop();
	});
	
	$('tiles_host').addEvent('mouseup', function(e){
		var e = new Event(e);
		selection_dragging=false;
		ui_put();
		e.stop();
	});

	$('tiles_host').addEvent('mousedown', function(e){
		var e = new Event(e);
		if (!e.rightClick) {
			dropdown_dispose();											   
			var offset = $('tiles_host').getSize().scroll;
			var position = e.client;
			var x = e.client.x+offset.x-$('tiles_host').getLeft();
			var y = e.client.y+offset.y-$('tiles_host').getTop();
			
			if (x<1068) { /* Scrollbar */
				if (e.shift) {
					selection_dragto(Math.floor(x/33), Math.floor(y/33));
				} else {
					selection_start(Math.floor(x/33), Math.floor(y/33));
					selection_dragging=true;
				}
			}
		}
		e.stop();
	});

	$('tiles_select').addEvent('contextmenu', function(e){
		var e = new Event(e);
		dropdown_show(e.client.x,e.client.y,[
			['javascript:ui_setbackground()', 'Use background'],
			['javascript:ui_defkey()', 'Define shortcut'],
			['javascript:ui_defobject()', 'Define object by selection']
		]);
		e.stop();
	});

	$('tiles_host').addEvent('mousemove', function(e){
		var e = new Event(e);
		if (!e.rightClick) {
			if (selection_dragging) {
				var offset = $('tiles_host').getSize().scroll;
				var position = e.client;
				var x = e.client.x+offset.x-$('tiles_host').getLeft();
				var y = e.client.y+offset.y-$('tiles_host').getTop();
				if (x<1068) { /* Scrollbar */
					selection_dragto(Math.floor(x/33), Math.floor(y/33));
				}
			}
		}
		e.stop();
	});

	$(document).addEvent('mousedown', function(e){
		var e = new Event(e);
		dropdown_dispose();	
		e.stop();
	});
	
	$(document).addEvent('mouseup', function(e){
		var e = new Event(e);
		brush_dragging = false;
		scroll_active = false;											 
		e.stop();
	 });

});

