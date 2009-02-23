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

function get_basename(url) {
	var parts = new String(url);
	parts = parts.split("/");
	return parts[parts.length-1];
}

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
		$('json_output').setHTML('&nbsp;');
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
  
function json_save(action, filename, msg_start, msg_complete, extra_data){
	if (!$defined(action)) action='save';
	if (!$defined(msg_start)) msg_start='Saving...';
	if (!$defined(msg_complete)) msg_complete='Saved!';

	json_message(msg_start);
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
						gdata.push({'x':p[0], 'y':p[1], 's': get_basename(paint_grid[grid_id][coord].src)});
					}
				}
			}
			rqdata.push(gdata);
		}				

		var json = new Json.Remote('feed.php?a='+action+'&f='+filename, {
			headers: {'X-Request': 'JSON'},
			onComplete: function(obj) {
				json_message(msg_complete);
			},
			onFailure: function(err) {
				json_message('Error! '+err);
			}
		}).send({'map':rqdata, 'background':paint_background, 'objects':object_datagrid, 'data':extra_data, 'zgrid':cgrid_datagrid});
	} catch (e) {
		json_message('Error! '+e.message);
	}
}

function json_regdefobj(grid, name){
	json_message('Defining...');
	try {
		var json = new Json.Remote('feed.php?a=rdef', {
			headers: {'X-Request': 'JSON'},
			onComplete: function(obj) {
				json_message('Defined!');
				oloader_update();
			},
			onFailure: function(err) {
				json_message('Error! '+err);
			}
		}).send({'grid':grid, 'name':name});
	} catch (e) {
		json_message('Error! '+e.message);
	}
}

function json_load(filename) {
	json_message('Loading...');
	try {
		var json = new Json.Remote('feed.php?a=load&f='+filename, {
			headers: {'X-Request': 'JSON'},
			onComplete: function(data) {
			
				json_message('Rendering...');
	
				// We got the object. Do the loading...
				paint_reset();
				cgrid_reset();
				object_resetgrid();

				// First, render the map
				var obj = data.map;
				for (var i=0; i<3; i++) {
					paint_layer = i+1;
					$each(obj[i], function(e) {
						paint_put(e.x-scroll_offset.x,e.y-scroll_offset.y,e.s);
				   });
				}

				// Then, render the objects
				var obj = data.objects;
				$each(obj, function(e) {
					object_instance(e);
				});
				
				// Then, render the z-grid
				if ($defined(data.zgrid)) cgrid_render(data.zgrid);
				
				// And finally, update the background
				paint_setbackground(data.background);

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

function json_defobj(grid) {
	json_message('Defining object...');
	try {
		var json = new Json.Remote('feed.php?a=define', {
			headers: {'X-Request': 'JSON'},
			onComplete: function(data) {
				json_message('Defined!');
				oloader_update();
			},
			onFailure: function(err) {
				json_message('Error! '+err);
			}
			
		}).send(grid);
	} catch (e) {
		json_message('Error! '+e.message);
	}
}

/**
  * Interface Window Handler
  *
  */

//### Save/Open window ###
var win_opensave_elements = [];
var win_opensave_callback = null;

function win_opensave_save(filename) {
	win_opensave_hide();
	win_opensave_callback(filename);
}

function win_opensave_cancel() {
	win_opensave_hide();
}

function win_opensave_addfile(filename) {
	var im = $(document.createElement('a'));
	im.setHTML(filename);
	im.href='javascript:;';
	im.addEvent('click', function(e) {
		var e = new Event(e);
		win_opensave_save(filename);
		e.stop();
	});
	$('ui_opensave_data').appendChild(im);
	win_opensave_elements.push(im);
}

function win_opensave_cleanup() {
	$each(win_opensave_elements, function(e){
		e.remove();
	});
	win_opensave_elements = [];
}

function win_opensave_hide() {
	$('ui_opensave').setStyle('visibility','hidden');
}

function win_opensave_show(title, folder, callback) {
	$('ui_opensave').setStyle('visibility','visible');
	win_opensave_cleanup();
	win_opensave_callback=callback;
	
	$('win_opensave_filename').value = ui_lastfile;
	$('ui_opensave_data').setHTML('Loading...');
	$('win_opensave_header').setHTML(title);
	
	var json = new Json.Remote('feed.php?a=filelist&f='+folder, {
		headers: {'X-Request': 'JSON'},
		onComplete: function(obj) {
			$('ui_opensave_data').setHTML('');
			if (obj != false) {
				$each(obj, win_opensave_addfile);
			}
		},
		onFailure: function(err) {
			json_message('Error! '+err);
		}
	}).send();

}


//### Interface window for object parameters ###
var win_objparm_active_infobj = null;
var win_objparm_inputs = [];
var win_objparm_objects = [];

function win_objparm_show(infobj) {
	$('ui_objinfo').setStyle('visibility','visible');
	win_objparm_active_infobj = infobj;
	win_objparm_reset();
	win_objparm_spawncontrol('Object is dynamic','dynamic',infobj['dynamic'],'checkbox');
	$each(infobj, function(v,id) {
		if ((id!='x') && (id!='y') && (id!='z') && (id!='image') && (id!='dynamic') && (id!='cx') && (id!='cy')) {
			win_objparm_spawncontrol(id,id,v);
		}
	});
}

function win_objparm_reset() {
	$each(win_objparm_objects, function(e) {
		try {
			e[0].remove();
			e[1].remove();
			e[2].remove();
			e[3].remove();
		} catch (e) {
		}
	});
	win_objparm_objects = [];
	win_objparm_inputs = [];
}

function win_objparm_removeparm(id) {
	$each(win_objparm_inputs, function(elm, i) {
		var name = elm.getProperty('name');
		if (name == id) {
			win_objparm_objects[i][0].remove();
			win_objparm_objects[i][1].remove();
			win_objparm_objects[i][2].remove();
			win_objparm_objects[i][3].remove();
			win_objparm_objects.splice(i,1);
			win_objparm_inputs.splice(i,1);
			delete win_objparm_active_infobj[name];
			return;	
		}
	});	
}

function win_objparm_spawncontrol(name, id, value, type) {
	var o = $(document.createElement('div'));
	var s = $(document.createElement('strong'));
	var i = $(document.createElement('input'));
	var x = $(document.createElement('img'));
	
	if (!$defined(value)) value='';
	if (!$defined(type)) type='text';
	
	s.setHTML(name+':');
	i.setProperties({
		'type': type,
		'name': id,
		'value': value
	});
	
	if (type == 'checkbox') i.setProperty('checked', value);
	
	x.setProperties({
		'src': 'images/edit_remove.png',
		'title': 'Remove this parameter'
	});
	
	x.addEvent('click', function(e) {
		var e = new Event(e);
		win_objparm_removeparm(id);
		e.stop();
	});
	
	o.appendChild(x);
	o.appendChild(s);
	o.appendChild(i);
	
	$('ui_objinfo_data').appendChild(o);
	win_objparm_inputs.push(i);
	win_objparm_objects.push([i,s,o,x]);
}

function win_editobj_addparm() {
	var pname = window.prompt("Enter the parameter name:");
	if (!pname) return;
	win_objparm_spawncontrol(pname,pname,win_objparm_active_infobj[pname]);	
}

function win_editobj_save() {
	$('ui_objinfo').setStyle('visibility','hidden');
	$each(win_objparm_inputs, function(elm) {
		var name = elm.getProperty('name');
		var value = elm.getProperty('value');
		var type = elm.getProperty('type');		
		if (type == 'text') {
			win_objparm_active_infobj[name]=value;
		} else if  (type == 'checkbox') {
			win_objparm_active_infobj[name]=elm.getProperty('checked');
		}
		
		// Unset 'dynamic' variable if false
		if ((name == 'dynamic') && !win_objparm_active_infobj[name]) {
			delete win_objparm_active_infobj[name];
		}
	});
	win_objparm_active_infobj=null;
}

function win_editobj_cancel() {
	$('ui_objinfo').setStyle('visibility','hidden');
	win_objparm_active_infobj=null;
}

/**
  * Interface button feedback handler
  *
  */
var ui_lastfile='';

function ui_new() {
	if (window.confirm('Do you really want to erase this map? This action is not undoeable!')) {
		scroller_setpos(0,0);
		paint_reset();
		paint_setbackground('');
		object_resetgrid();
		cgrid_reset();
	}
}

function ui_save() {
	win_opensave_show('Save map','saved',function(fname){
		ui_lastfile=fname;
		json_save('save',fname);
	});
}

function ui_load() {
	win_opensave_show('Load map','saved',function(fname){
		ui_lastfile=fname;
		json_load(fname);
		ui_put();
	});
}

function ui_compile() {
	win_opensave_show('Compie map', 'compile', function(name){
		json_save('compile', name, 'Compiling...','Compiled!', {'title':name});
	});
}

function ui_cgrid_put() {
	$('tiles_clear').setStyles({'background-color':''});
	$('tiles_put').setStyles({'background-color':''});
	$('objects_clear').setStyles({'background-color':''});
	$('objects_edit').setStyles({'background-color':''});
	$('objects_put').setStyles({'background-color':''});
	$('cgrid_clear').setStyles({'background-color':''});
	$('objects_region').setStyles({'background-color':''});
	$('cgrid_put').setStyles({'background-color':'#FFFFFF'});
	brush_erase=false;
	brush_put=false;
	object_put=false;
	object_edit=false;
	object_erase=false;
	object_regdef=false;
	cgrid_put=true;
	cgrid_erase=false;
	$('content_collision').setStyle('display','');
	brush_yoffset=0;
	brush_reset();
}

function ui_cgrid_erase() {
	$('tiles_clear').setStyles({'background-color':''});
	$('tiles_put').setStyles({'background-color':''});
	$('objects_clear').setStyles({'background-color':''});
	$('objects_edit').setStyles({'background-color':''});
	$('objects_put').setStyles({'background-color':''});
	$('cgrid_clear').setStyles({'background-color':'#FFFFFF'});
	$('cgrid_put').setStyles({'background-color':''});
	$('objects_region').setStyles({'background-color':''});
	brush_erase=false;
	brush_put=false;
	object_put=false;
	object_edit=false;
	object_erase=false;
	object_regdef=false;
	cgrid_put=false;
	cgrid_erase=true;
	$('content_collision').setStyle('display','');
	brush_yoffset=0;
	brush_reset();
}

function ui_clear() {
	$('tiles_clear').setStyles({'background-color':'#FFFFFF'});
	$('tiles_put').setStyles({'background-color':''});
	$('objects_clear').setStyles({'background-color':''});
	$('objects_edit').setStyles({'background-color':''});
	$('objects_put').setStyles({'background-color':''});
	$('cgrid_clear').setStyles({'background-color':''});
	$('cgrid_put').setStyles({'background-color':''});
	$('objects_region').setStyles({'background-color':''});
	brush_erase=true;
	brush_put=false;
	object_put=false;
	object_edit=false;
	object_erase=false;
	object_regdef=false;
	cgrid_put=false;
	cgrid_erase=false;
	$('content_collision').setStyle('display','none');
	brush_yoffset=0;
	brush_reset();
}

function ui_put() {
	$('tiles_clear').setStyles({'background-color':''});
	$('tiles_put').setStyles({'background-color':'#FFFFFF'});
	$('objects_clear').setStyles({'background-color':''});
	$('objects_edit').setStyles({'background-color':''});
	$('objects_put').setStyles({'background-color':''});
	$('cgrid_clear').setStyles({'background-color':''});
	$('cgrid_put').setStyles({'background-color':''});
	$('objects_region').setStyles({'background-color':''});
	brush_erase=false;
	brush_put=true;
	object_put=false;
	object_edit=false;
	object_erase=false;
	object_regdef=false;
	cgrid_put=false;
	cgrid_erase=false;
	$('content_collision').setStyle('display','none');
	brush_yoffset=0;
	brush_updateview();
}

function ui_objclear() {
	$('objects_clear').setStyles({'background-color':'#FFFFFF'});
	$('objects_edit').setStyles({'background-color':''});
	$('objects_put').setStyles({'background-color':''});
	$('tiles_clear').setStyles({'background-color':''});
	$('tiles_put').setStyles({'background-color':''});
	$('cgrid_clear').setStyles({'background-color':''});
	$('cgrid_put').setStyles({'background-color':''});
	$('objects_region').setStyles({'background-color':''});
	brush_erase=false;
	brush_put=false;
	object_put=false;
	object_edit=false;
	object_erase=true;
	object_regdef=false;
	cgrid_put=false;
	cgrid_erase=false;
	$('content_collision').setStyle('display','none');
	brush_reset();
}

function ui_objput() {
	$('objects_clear').setStyles({'background-color':''});
	$('objects_edit').setStyles({'background-color':''});
	$('objects_put').setStyles({'background-color':'#FFFFFF'});
	$('tiles_clear').setStyles({'background-color':''});
	$('tiles_put').setStyles({'background-color':''});
	$('cgrid_clear').setStyles({'background-color':''});
	$('cgrid_put').setStyles({'background-color':''});
	$('objects_region').setStyles({'background-color':''});
	brush_erase=false;
	brush_put=false;
	object_put=true;
	object_edit=false;
	object_erase=false;
	object_regdef=false;
	cgrid_put=false;
	$('content_collision').setStyle('display','none');
	cgrid_erase=false;
}

function ui_objedit() {
	$('objects_clear').setStyles({'background-color':''});
	$('objects_put').setStyles({'background-color':''});
	$('objects_edit').setStyles({'background-color':'#FFFFFF'});
	$('cgrid_clear').setStyles({'background-color':''});
	$('cgrid_put').setStyles({'background-color':''});
	$('tiles_clear').setStyles({'background-color':''});
	$('tiles_put').setStyles({'background-color':''});
	$('objects_region').setStyles({'background-color':''});
	brush_erase=false;
	brush_put=false;
	object_put=false;
	object_edit=true;
	object_erase=false;
	object_regdef=false;
	cgrid_put=false;
	cgrid_erase=false;
	$('content_collision').setStyle('display','none');
	brush_reset();
}

function ui_objdefregion() {
	$('objects_clear').setStyles({'background-color':''});
	$('objects_put').setStyles({'background-color':''});
	$('objects_edit').setStyles({'background-color':''});
	$('objects_region').setStyles({'background-color':'#FFFFFF'});
	$('cgrid_clear').setStyles({'background-color':''});
	$('cgrid_put').setStyles({'background-color':''});
	$('tiles_clear').setStyles({'background-color':''});
	$('tiles_put').setStyles({'background-color':''});
	brush_erase=false;
	brush_put=false;
	object_put=false;
	object_edit=false;
	object_erase=false;
	object_regdef=true;
	cgrid_put=false;
	cgrid_erase=false;
	$('content_collision').setStyle('display','none');
	brush_reset();
}

function ui_setbackground() {
	paint_setbackground('../../images/tiles/'+tiles_base+'-'+selection.x+'-'+selection.y+'.png');
}

function ui_defobject() {
	var name = window.prompt('Enter an object name:');
	if (!name) return;
	
	var grid = [];
	var i=0;j=0;
	for (var y=selection.y; y<selection.y+selection.h; y++) {
		grid[i]=[];
		j=0;
		for (var x=selection.x; x<selection.x+selection.w; x++) {
			grid[i][j]=tiles_base+'-'+x+'-'+y+'.png';
			j++;
		}
		i++;
	}
	json_defobj({'grid':grid,'name':objects_active+'-'+name});
}

/**
  * Dropdown menu handler
  *
  */
var ddmenu_object = null;

function dropdown_show(x,y,menus) {
	var e = $(document.createElement('div'));
	if (!isInternetExplorer) { e.setAttribute('class', 'dropdownmenu') } else { e.setAttribute('className', 'dropdownmenu') };
	$each(menus, function(m) {
		var a = $(document.createElement('a'));
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

	var data = new Json.Remote('feed.php?a=tiles&base='+tileset, {
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
	$('tiles_status').setHTML('<center>Rendering...</center>');
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
  * Objects loader system
  *
  */
var objects_cache = [];
var objects_active = '';

function oloader_update() {
	oloader_reset();
	oloader_download(objects_active);	
}

function oloader_download(objectset) {
	oloader_reset();
	
	objects_active = objectset;
	$('objects_set').setStyles({'display':'none'});
	$('objects_status').setStyles({'display':''});
	$('objects_status').setHTML('<center>Downloading information...</center>');

	var data = new Json.Remote('feed.php?a=objects&base='+objectset, {
			onComplete: function(o) {
				if (o.length>0) {
					oloader_preload(o);
				} else {
					$('objects_status').setStyles({'display':'none'});
					$('objects_set').setStyles({'display':''});					
				}
			},
			onFailure: function(e) {
				window.alert(e.message);
			}
	}).send();	
}

function oloader_preload(images) {
	var lt_timer=null; /* Loading Timeout */
	var lt_finalized=false;

	new Asset.images(images, {
		onComplete: function(e){
			lt_finalized=true;
			if (lt_timer) {clearTimeout(lt_timer);lt_timer=null;};
			oloader_renderimages(images);
		},
		onProgress: function(img_id) {
			// If we are completed, this is just a late call..
			// ignore it...
			if (!lt_finalized) {
				var perc = Math.ceil(100*img_id/images.length);
				if (perc > 100) perc-=100; /* When objects are already cached, the maximum value seems to be 200% */
				$('objects_status').setHTML('<div class="progress_bar"><div style="width: '+perc+'%;">&nbsp;</div></div>');
				
				// More than a second of delay between two images is too much
				// Probably it is stucked 
				// BUGFIX: 1) Unreasonable stops on 99% on IE
				//         2) Blocks when file does not exists
				if (lt_timer) {clearTimeout(lt_timer); lt_timer=null;};
				lt_timer=setTimeout(function(){ oloader_renderimages(images); }, 2000);
			}
		}
	});
}

function oloader_renderimages(images) {
	$('objects_status').setHTML('<center>Rendering...</center>');
	var cur_image=0;
	var timer = setInterval(function() {
		for (var i=cur_image; i<cur_image+8; i++) {
			object_store(images[i]);
		}
		cur_image=i;
		if (i>=images.length) {
			$('objects_status').setStyles({'display':'none'});
			$('objects_set').setStyles({'display':''});
			clearInterval(timer);
		}
	},10);
}

function oloader_reset() {
	try {
		$each(objects_cache, function(e){
			e.remove();
		});
	} catch (e) {		
	}
	objects_cache=[];
}

function object_store(img) {
	var a = $(document.createElement('a'));
	var i = $(document.createElement('img'));
	a.appendChild(i);	
	a.href="javascript:;"
	
	i.setProperties({
		'border': 0,
		'src': img
	});

	$('objects_host').appendChild(a);
	var siz = i.getSize().size;
	a.addEvent('click', function(e) {
		var e = new Event(e);
		object_select(img,siz.y);
		ui_objput();
		e.stop();
	});
	
	objects_cache.push(i);
	objects_cache.push(a);
}

/**
  * Undo system
  *
  */

/**
  * Objects system
  *
  */
var object_put = false;
var object_edit = false;
var object_regdef = false;
var object_erase = false;
var object_active = "";
var object_grid = [];
var object_datagrid = [];

function object_regiondef() {
	var name = window.prompt('Define the object name:');
	if (!name) return;
	name = objects_active+'-'+name;
	
	var cl=brush_selection.x+scroll_offset.x;
	var cr=cl+brush_selection.w;
	var ct=brush_selection.y+scroll_offset.y;
	var cb=ct+brush_selection.h;
		
	var obj_grid=[];
	for (var l=1; l<3; l++) {
		obj_grid[l]=[];
		for (var y=ct; y<cb; y++) {
			obj_grid[l][y]=[];
			for (var x=cl; x<cr; x++) {
				var id=x+','+y;
				obj_grid[l][y][x]=false;
				if ($defined(paint_grid[l][id])) {
					obj_grid[l][y][x]=paint_grid[l][id].src;
				}
			}
		}
	}
	
	json_regdefobj(obj_grid,name);
}

function object_select(img, yoffset) {
	object_active = img;
	brush_selection_useobject(img);
	brush_yoffset=Math.floor(yoffset/32);
	brush_show();
}

function object_resetgrid() {
	$each(object_grid, function(e){
		e.remove();
	});
	object_grid = [];
	object_datagrid = [];
}

function object_instance_bybrush() {
	object_instance({'x':brush_position.x+scroll_offset.x, 'y':brush_position.y+scroll_offset.y+brush_yoffset});
}

function object_remove(obj) {
	var id = object_grid.indexOf(obj);
	if (id<0) return;
	object_grid[id].remove();
	object_grid.splice(id,1);
	object_datagrid.splice(id,1);
}

function object_instance(data) {
	var o = $(document.createElement('img'));

	if (!$defined(data.image)) data.image=object_active;
	if (!$defined(data.x)) data.x=0;
	if (!$defined(data.y)) data.y=0;
	
	$('content_objects').appendChild(o);
	o.setProperties({
		'border': 0,
		'src': data.image
	});
		
	var siz = o.getSize().size;
	o.setStyles({
		'left': data.x*32,
		'top': data.y*32-siz.y
	});
	
	// Calculate the place point (used by the compiled map to render correctly the depth)
	if (!$defined(data.cx)) data.cx=0;
	if (!$defined(data.cy)) data.cy=Math.ceil(siz.y/32);
	
	// Calculate z-index
	var zypos = data.y;
	if ($defined(data.z)) zypos+=data.z;		
	var zindex = zypos*500+brush_position.x;
	if (zindex<0) zindex=1;
	
	o.addEvent('mouseenter', function(e){
		var e = new Event(e);
		if (object_edit || object_erase) {
			o.setStyles({
				'border': 'solid 1px #FF9900',
				'background-color': '#FEEBD6'
			});
		}
		e.stop();
    });
	o.addEvent('mouseleave', function(e){
		var e = new Event(e);
		if (object_edit || object_erase) {
			o.setStyles({
				'border': '',
				'background-color': ''
			});
		}
		e.stop();
    });
	o.addEvent('click', function(e){
		var e = new Event(e);
		if (object_edit) {
			var id = object_grid.indexOf(o);
			win_objparm_show(object_datagrid[id]);
		} else if (object_erase) {
			object_remove(o);
		}
		e.stop();									  
    });

	//if ($defined(data.z)) o.setStyle('z-index', data.z);
	o.setStyle('z-index', zindex);
	
	object_grid.push(o);
	object_datagrid.push(data);
}

/**
  * Painting system
  *
  */

var paint_elements = [];
var paint_grid = [[],[],[]];
var paint_layer = 1;
var paint_background = '';

function paint_setbackground(img) {
	if (img == '') img='images/grid.gif';
	$('content_host').setStyles({
		'background-image': 'url('+img+')'
	});
	paint_background = img;	
}

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

function paint_clear(sx,sy) {
	var x = sx+scroll_offset.x;
	var y = sy+scroll_offset.y;
	var id=x+','+y;
	if ($defined(paint_grid[paint_layer-1][id])) {
		paint_grid[paint_layer-1][id].remove();
		paint_grid[paint_layer-1][id]=null;
	}	
}

function paint_put(sx,sy,image) {
	var x = sx+scroll_offset.x;
	var y = sy+scroll_offset.y;
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
var brush_put = true;
var brush_yoffset = 0;

function _brush_clean() {
	for (var y=brush_selection.y; y<brush_selection.y+brush_selection.h; y++) {
		for (var x=brush_selection.x; x<brush_selection.x+brush_selection.w; x++) {
			paint_clear(x,y);
		}
	}
}

function _brush_blit(sel,dest) {
	var sx=sel.x;
	var sy=sel.y;	
	
	for (var y=dest.y; y<dest.y+dest.h; y++) {
		for (var x=dest.x; x<dest.x+dest.w; x++) {
			paint_put(x,y,tiles_base+'-'+sx+'-'+sy+'.png');
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

function brush_selection_useobject(img) {
	brush_reset();
	var im = brush_spawnimage(img);
	brush_elements.push({'o':im, 'x':0, 'y':0});
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
	im.src=image;
	im.setStyles({'opacity':0.5});
	return im;
}

function brush_updateview(){
	// Clear brush
	brush_reset();
	
	// Load brush info from the selection
	for (var y=selection.y; y<selection.y+selection.h; y++) {
		for (var x=selection.x; x<selection.x+selection.w; x++) {
			var im = brush_spawnimage('../../images/tiles/'+tiles_base+'-'+x+'-'+y+'.png');
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
	$('content_collision').setStyles({'left':x,'top':y});
	
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
  *  Collision grid handling
  */
var cgrid_put = false;
var cgrid_erase = false;
var cgrid_elements = [];
var cgrid_data = [];
var cgrid_datagrid = [];
var cgrid_attennuation = 100;

function cgrid_setatt(att,keepon) {
	cgrid_attennuation=att;
	$$('.slider a').each(function(e){
		e.setStyle('background-image','');
  	});
	keepon.setStyle('background-image','url(images/selection-on.png)');
	ui_cgrid_put();
}
 
function cgrid_reset() {
	$each(cgrid_elements, function(e) {
	   try {
			e.remove();							   
	   } catch (e) {		   
	   }
	});
	cgrid_elements = [];
	cgrid_data = [];
	cgrid_datagrid = [];
}

function cgrid_draw(x,y,value) {
	if (!$defined(cgrid_data[y])) {
		cgrid_data[y]=[];
		cgrid_datagrid[y]=[];
	}
	if (!$defined(cgrid_data[y][x])) {
		var elm = cgrid_spawn();
	} else {
		var elm = cgrid_data[y][x];
	}
	
	cgrid_datagrid[y][x]=value;
	cgrid_data[y][x]=elm;
	
	if (value<1){
		elm.setHTML('1/'+(1/value));
	} else {
		elm.setHTML(value);
	}
	
	b='#003366';
	if (value==1/4) b='#66FFFF';
	if (value==1/3) b='#00FF00';
	if (value==1/2) b='#66FF99';
	if (value==1) b='#FFFF66';
	if (value==2) b='#FFCC00';
	if (value==3) b='#FF9900';
	if (value==4) b='#FF0000';
	
	elm.setStyles({
		'left': x*32,
		'top': y*32,
		'opacity': 0.7,
		'background-color': b
	});
}

function cgrid_clear(x,y) {
	if (!$defined(cgrid_data[y])) return;
	if (!$defined(cgrid_data[y][x])) return;	
	cgrid_data[y][x].remove();
	delete cgrid_data[y][x];
	delete cgrid_datagrid[y][x];
}

function cgrid_spawn() {
	var o = $(document.createElement('div'));
	$('content_collision').appendChild(o);
	cgrid_elements.push(o);
	return o;
}

function cgrid_apply_brush() {	
	try {
		for (var y=brush_selection.y; y<brush_selection.y+brush_selection.h; y++) {
			for (var x=brush_selection.x; x<brush_selection.x+brush_selection.w; x++) {
					if (cgrid_put) {
						if (cgrid_attennuation == 0) {
							cgrid_clear(x+scroll_offset.x,y+scroll_offset.y);
						} else {
							cgrid_draw(x+scroll_offset.x,y+scroll_offset.y,cgrid_attennuation);
						}
					}
				if (cgrid_erase) cgrid_clear(x+scroll_offset.x,y+scroll_offset.y);
			}
		}
	} catch (e) {
		window.alert(e.message);
	}
}

function cgrid_render(grid) {
	$each(grid, function(xgrid,y) {
		if ($defined(xgrid)) if (xgrid!=false) {
			$each(xgrid, function(value,x) {
				if ($defined(value)) if (value!=false) {
					cgrid_draw(x,y,value);
				}
			});
		}
	});
}

/**
================================================================================================================================
================================================================================================================================
**/

$(window).addEvent('load', function(e){	

	tloader_download('z-field-ext');
	oloader_download('furniture');
	cgrid_setatt(0,$$('.slider a')[0]);

	$('content_host').addEvent('mousemove', function(e){
		var e = new Event(e);
		var p = getScrollPosition();
		
		var offset = $('content_host').getSize().scroll;
		var position = e.client;
		var x = e.client.x+offset.x-$('content_host').getLeft()+p.x;
		var y = e.client.y+offset.y-$('content_host').getTop()+p.y;
		
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
		var p = getScrollPosition();
		if (e.event.button == 1) {
			scroller_start(e.client.x+p.x,e.client.y+p.y);
			scroll_active = true;
		} else if (e.event.button == 0) {
			if (e.alt) {
				scroller_start(e.client.x+p.x,e.client.y+p.y);
				scroll_active = true;
			} else {
				if (brush_erase || brush_put || cgrid_put || cgrid_erase || object_regdef) {
					brush_dragging = true;			
				} else {
					
				}
			}
		}
		e.stop();
	})
	
	$('content_host').addEvent('mouseup', function(e){
		var e = new Event(e);		
		if (brush_erase || brush_put || cgrid_put || cgrid_erase || object_regdef) {
			if (brush_dragging) {
				if (e.shift) {
					paint_layer=3;
				} else if (e.control) {
					paint_layer=1;
				} else {
					paint_layer=2;
				}
				
				if (brush_erase || brush_put) {
					brush_apply();
				} else if (cgrid_put || cgrid_erase) {
					cgrid_apply_brush();
				} else if (object_regdef) {
					object_regiondef();
				}
			}
			brush_dragging = false;
		} else if (object_put && (e.event.button == 0) && !e.alt) {
			object_instance_bybrush();
		}
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
		var p = getScrollPosition();
		if (!e.rightClick) {
			dropdown_dispose();											   
			var offset = $('tiles_host').getSize().scroll;
			var position = e.client;
			var x = e.client.x+offset.x-$('tiles_host').getLeft()+p.x;
			var y = e.client.y+offset.y-$('tiles_host').getTop()+p.y;
			
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
		var p = getScrollPosition();
		dropdown_show(e.client.x+p.y,e.client.y+p.y,[
			['javascript:ui_setbackground()', 'Use background'],
			['javascript:ui_defkey()', 'Define shortcut'],
			['javascript:ui_defobject()', 'Define object by selection']
		]);
		e.stop();
	});

	$('tiles_host').addEvent('mousemove', function(e){
		var e = new Event(e);
		var p = getScrollPosition();
		if (!e.rightClick) {
			if (selection_dragging) {
				var offset = $('tiles_host').getSize().scroll;
				var position = e.client;
				var x = e.client.x+offset.x-$('tiles_host').getLeft()+p.x;
				var y = e.client.y+offset.y-$('tiles_host').getTop()+p.y;
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
		//e.stop();
	});
	
	$(document).addEvent('mouseup', function(e){
		var e = new Event(e);
		brush_dragging = false;
		scroll_active = false;
		//e.stop();
	 });

	$(document).addEvent('contextmenu', function(e){
		var e = new Event(e);
		e.stop();
	 });

	$(document).addEvent('keyup', function(e){
		var e=new Event(e);
		e.stop();
	});

	$(document).addEvent('keydown', function(e){
		var e=new Event(e);
		if (e.control) {
			if (e.key == 's') {
				ui_save();
				e.stop();
			} if (e.key == 'o') {
				ui_load();
				e.stop();
			} if (e.key == 'n') {
				ui_new();
				e.stop();
			} else if (e.key == 'g') {
				ui_cgrid_put();
				e.stop();
			}
		}
	});

});
