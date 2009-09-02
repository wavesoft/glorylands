// The overlay item the player has his mouse over (contains the dictionary entry)
var hoveredItem=false;

$(window).addEvent('resize', function(e) {
	// Re-center map on resize
	setTimeout(map_center, 500);
});

$(window).addEvent('domready', function(e){
		
	// Check if we are being included only as library
	// In that case, we do not initialize ourselves
	if (!$('datapane')) {
		return;
	}
	
	/* -=[ PHASE 1 ]=- */

	// Preload cursors
	cursor_preload();

	// Initialize waiter animation
	initWaiter();
	
	// Initialize datapane
	initDisplayBuffer();
	
	$('datapane').addEvent('contextmenu', function(e) {
		e = new Event(e);
		// Get Scroll position
		var scrl = getScrollPosition();

		if (hoveredItem!=false) {
			// Display the dropdown menu
			piemenu_dispose();
			piemenu_init(e.event.clientX+scrl.x,e.event.clientY+scrl.y,hoveredItem.g,'MAP');
		} else {
			// Clicked over no item
			piemenu_dispose();	
		}
		//window.alert($trace(e));
		e.stop();
	});

	$('datapane').addEvent('mousedown', function(e) {
		// Dispose dropdown (if visible)
		piemenu_dispose();
	});

	$('datapane').addEvent('click', function(e) {
		e = new Event(e);							

		// Get DataPane left offset
		var dpX = $('datapane').getLeft();
		var dpY = $('datapane').getTop();
		
		// Get Scroll position
		var scrl = getScrollPosition();
				
		// Calculate cell X,Y
		var xP = Math.ceil((e.event.clientX-dpX+scrl.x)/32)+glob_x_base-1;
		var yP = Math.ceil((e.event.clientY-dpY+scrl.y)/32)+glob_y_base-1;
		
		// If we have no active rect, hit test regions
		if (rectinfo.url == '') {
			hitTestRegion(xP,yP);

		// Preform grid operations if active
		} else {
			gloryIO(rectinfo.url+'&x='+xP+'&y='+yP);
			if (rectinfo.clickdispose) {
				rectinfo.url='';
				$('grid_rect').setStyles({'display':'none'});
			}
		}
		
		// Dispose dropdown (if visible)
		piemenu_dispose();
	
		// Send movement
		//gloryIO('?a=map.grid.get&x='+xP+'&y='+yP);
		pw_walk_to(map_playeruid, xP,yP, 5, true);

	});
	
	/* -=[ PHASE 2 ]=- */

	// Ger the grid
	gloryIO('?a=map.grid.get');
	
	// Start message feeder
	feeder();
});

$(window).addEvent('focus', function(e){
	 return;
	// Re-Enable feeder when we get focus
	feeder_enabled = true;
	if (feeder_timer==0) {
		feeder_timer=setTimeout(feeder, 1000);	
	}
});
$(window).addEvent('blur', function(e){
	 return;
	// BUGFIX: CPU Usage in idle state
	// Disable feeder when we loose focus
	feeder_enabled = false;
	feeder_timer=0;
});

// Handle Javascript errors
/*
$(window).onerror = function(e) {
	if ($defined(showStatus)) {
		try {
			showStatus('<font color=\"red\">Script Error!</font><br /><small>'+e+'</small>', 1000);
		} catch (er2) {
			window.alert(e);
		}
	} else {
		window.alert(e);
	}
};
*/

// Handle keys
var v_center={x:0,y:0}, v_last={x:0,y:0};
$(document).addEvent('keydown', function(e){
	
	// Check if we are being included only as library
	// In that case, we do not initialize ourselves
	if (!$('datapane')) {
		return;
	}

	e = new Event(e);	
	if (e.code == 27) {
		
		// Dispose rectangle (if visible)
		var r = $('grid_rect');
		if (r) {
			if (r.getStyle('display')!='none') {
				r.setStyles({'display':'none'});	
				rectinfo.url='';
			}
		}
		
		// Dispose dropdown menu
		piemenu_dispose();
		
		// Do not forward the event any further
		e.stop();		
	} else if (e.control) {
		if (e.key == 'b') {
			e.stop();
			gloryIO('?a=interface.inventory');
		} else if (e.key == 'd') {
			e.stop();
			window.alert($trace(map_images));
		}
	} else if (e.key == 'right') {
		v_center.x=200;
		e.stop();
	} else if (e.key == 'left') {
		v_center.x=-200;
		e.stop();
	} else if (e.key == 'up') {
		v_center.y=-200;
		e.stop();
	} else if (e.key == 'down') {
		v_center.y=200;
		e.stop();
	} else if (e.key == 'm') {
		var id=map_object_index.indexOf(map_playeruid);
		if (id>-1) {
			var scrl = getScrollPosition();	
			if ($defined(map_objects[id].info.range)) {
				if (!wgrid_visible) {
					wgrid_design(map_objects[id].info.range);
					wgrid_show();
				}
			}			
		}
	}
	
	if ((v_center.x!=v_last.x) || (v_center.y!=v_last.y)) {
		map_center(map_viewpoint.x+v_center.x,map_viewpoint.y+v_center.y);
		v_last.x=v_center.x;
		v_last.y=v_center.y;
	}

});

$(document).addEvent('keyup', function(e){	
	// Check if we are being included only as library
	// In that case, we do not initialize ourselves
	if (!$('datapane')) {
		return;
	}

	v_center={x:0,y:0};
	v_last={x:0,y:0};
	map_center(map_viewpoint.x,map_viewpoint.y);
});

$(document).addEvent('mouseup', function(e){	
	// Check if we are being included only as library
	// In that case, we do not initialize ourselves
	if (!$('datapane')) {
		return;
	}

	// Dispose any probably open popups
	piemenu_dispose();	//## Pie Menu
	wgrid_hide();		//## Walking grid
});

$(document).addEvent('contextmenu', function(e){
	// Check if we are being included only as library
	// In that case, we do not initialize ourselves
	if (!$('datapane')) {
		return;
	}

	var e = new Event(e);
	// Dispose any probably open popups
	piemenu_dispose();	//## Pie Menu
	wgrid_hide();		//## Walking grid
	//e.stop();
});

var c=null;
function moveto(x,y) {
	if (c == null) {
		c = new Element('img', {src: 'images/UI/cursor/round.png'});
		c.inject($('datapane'));
		c.setStyles({
			'z-index': 65535,
			'position': 'absolute'
		});
	}
	c.setStyles({
		'left': x*32,
		'top': y*32				
	});
}

$(document).addEvent('click', function(e) {
	
	// Check if we are being included only as library
	// In that case, we do not initialize ourselves
	if (!$('datapane')) {
		return;
	}

	e = new Event(e);
	if (e.rightClick) return;
	
	// Get DataPane left offset
	var dpX = $('datapane').getLeft();
	var dpY = $('datapane').getTop();
	
	// Get Scroll position
	var scrl = getScrollPosition();

	// Check if we are out of datahost
	var xP = e.event.clientX;
	var yP = e.event.clientY;
	var dhPos = $('datahost').getPosition();
	var dhSiz = $('datahost').getSize();
	if ((xP<dhPos.x) || (xP>(dhPos.x+dhSiz.x)) ||
		(yP<dhPos.y) || (yP>(dhPos.y+dhSiz.y))) {
		return;
	}

	e.stop();
});

// Initialize mouse handler on window
$(document).addEvent('mousemove', function(e) {
	
	// Check if we are being included only as library
	// In that case, we do not initialize ourselves
	if (!$('datapane')) {
		return;
	}

	try {
	e = new Event(e);
	
	// Get DataPane left offset
	var dpX = $('datapane').getLeft();
	var dpY = $('datapane').getTop();
	
	// Get Scroll position
	var scrl = getScrollPosition();

	// Calculate cell X,Y
	var bxP = Math.ceil((e.event.clientX-dpX+scrl.x)/32)-1;
	var byP = Math.ceil((e.event.clientY-dpY+scrl.y)/32)-1;
	var xP = bxP+glob_x_base;
	var yP = byP+glob_y_base;
	var Overlay = ""; var DicEntry = "";

	// Obdain Hover info from navigation grid
	if ($defined(nav_grid[xP])) {
		if ($defined(nav_grid[xP][yP-1])) {
			Overlay = nav_grid[xP][yP-1];
			DicEntry = nav_grid['dic'][Overlay];
		}
	}

	// Detect hover info
	if (DicEntry.d) {
		hoveredItem=DicEntry;
	} else {
		hoveredItem=false;
	}

	// If we have an open pop-up element, do nothing
	//moveto(xP,yP);
	if (!pie_visible) {

		// Collision test with action grids
		hitTestRegion(xP,yP);

		// Display hover info
		if (hoveredItem) {
			//$('prompt').set('html', 'X: '+xP+', Y: '+yP+' With Zero at: '+glob_x_base+','+glob_y_base+', Overlay: '+Overlay+' Dic:'+DicEntry.d.name);
			hoverShow(DicEntry.d.name, e.event.clientX+scrl.x, e.event.clientY+scrl.y);
		} else {
			//$('prompt').set('html', 'X: '+xP+', Y: '+yP+' With Zero at: '+glob_x_base+','+glob_y_base);
			hoverShow(false);
		}

		// Rectangle handling
		var r = $('grid_rect');
		if (r) {
			if (r.getStyle('display')!='none') r.setStyles({left:(bxP-rectinfo.bx)*32, top:(byP-rectinfo.by)*32, width: rectinfo.w*32, height:rectinfo.h*32, display:''});
		}		
		
	}
	} catch (e) {
	}
});
