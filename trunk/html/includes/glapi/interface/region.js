var regions = [];
var visibleRegionID = -1;
var activeEvent = false;
var region_showing = false;
var region_showing_fx = null;

// Stack a region on region management system
function stackRegion(chunk) {
	//window.alert('Stack: '+$trace(chunk));
	regions.push(chunk);	
}

// Reset region stack
function resetRegion() {
	disposeActionPane();
	regions = [];
	visibleRegionID = -1;
}

// Display a region object if it hits the x/y coordinates
function hitTestRegion(x,y) {
	
	// If we already have a visible region object do not show seconds,
	// and also check if the mouse has moven out before the show animation was completed
	if (visibleRegionID>-1) {
			if (region_showing) {
				if ((x!=regions[visibleRegionID].show.x) || (y!=regions[visibleRegionID].show.y)) {
					abortActionPaneRender();	
				}
			}
			return;
	}
	
	// Check regions for collision
	for (var i=0; i<regions.length; i++) {
		if ((regions[i].show.x == x) && (regions[i].show.y == y)) {
			showRegion(i);
			return;
		}
	}
}

// Display a region object
function showRegion(index) {
	renderActionRange(regions[index]);
	visibleRegionID = index;
	hoverShow(false);
}

// Disposes a region object
function disposeActionPane(quick) {
	// Exit if no panel is visible
	if (visibleRegionID==-1) return;
	
	var panel = $('actionpane');
	
	if (!quick) {
		var fx = new Fx.Morph(panel, {duration: 500, transition: Fx.Transitions.Cubic.easeOut});
		fx.start({
			'opacity': 0
		}).chain(function() {
			panel.setStyles({visibility:'hidden'});	
		});	
	} else {
		panel.setStyles({visibility:'hidden'});	
	}
	visibleRegionID = -1;
}

// Converts the given array into an HTML representation
// of action range
function renderActionRange(chunk) {
	
	/* Chunk structure (JSON Object): 
	
	chunk.grid  = (.i .c) [x,y]	: Contains the grid information
	chunk.show	= (.x .y)		: Contains the X/Y coordinates of the mouse location that will
								  display the region object
	chunk.x.m	= (int)			: Minimum X Value
	chunk.x.M	= (int)			: Maximum X Value
	chunk.y.m	= (int)			: Minimum Y Value
	chunk.y.M	= (int)			: Maximum Y Value
	chunk.point.x = (int)		: The "pin point"'s X offset
	chunk.point.y = (int)		: The "pin point"'s Y offset	
	chunk.action = (str)		: The base url
	
	*/

	// Dispose hover
	hoverShow();

	// TODO: Probably convert to DOM?

	try {
	var x=0; var y=0;	
	var HTML = '<table cellspacing="0" cellpadding="0">';
	for (y=chunk.y.m; y<=chunk.y.M; y++) {
		HTML+='<tr>';
		for (x=chunk.x.m; x<=chunk.x.M; x++) {
			if ($defined(chunk.grid[y])) {
  			  if ($defined(chunk.grid[y][x])) {
				  
				var cell_style='';
				if ($defined(chunk.grid[y][x].c)) cell_style+='background-color:' + chunk.grid[y][x].c+'; ';
				if ($defined(chunk.grid[y][x].b)) cell_style+='background-image:url(' + chunk.grid[y][x].b+'); ';
				HTML+='<td><a href="javascript:void(0);" onclick=";gloryIO(\'?a='+chunk.action+'&id='+chunk.grid[y][x].i+'\');" class="actgrid_link" style="'+cell_style+'">';
				if ($defined(chunk.grid[y][x].t)) {
					HTML+=chunk.grid[y][x].t;
				} else {
					HTML+='&nbsp;';
				}
				HTML+='</a></td>';
			  } else {
				HTML+='<td><div class="actgrid_div">&nbsp;</div></td>';  
			  }
			} else {
				HTML+='<td><div class="actgrid_div">&nbsp;</div></td>';
			}
		}
		HTML+='</tr>';
	}
	HTML += '</table>';
	
	renderActionPane(HTML, chunk.point.x - glob_x_base, chunk.point.y - glob_y_base);
	} catch (e) {
	window.alert('RenderActionRange Error: '+e);	
	}
}

function renderActionPane(data,x,y) {
	var panel = $('actionpane');
	var dpX = $('datapane').getLeft();
	var dpY = $('datapane').getTop();
	panel.set('html', data);
	panel.setStyles({visibility:'visible', opacity: 0, 'left': (x*32-12)+dpX, 'top': (y*32-12)+dpY});
	
	// Halt events that hits this element
	panel.addEvent('click', function(e){
		e = new Event(e);
		e.stop();
	});
	panel.addEvent('mouseleave', function(e){
		e = new Event(e);
		disposeActionPane();
		e.stop();
	});

	region_showing_fx = new Fx.Morph('actionpane', {duration: 500, transition: Fx.Transitions.Cubic.easeIn,
		onComplete: function() {
			region_showing=false;
		}
	});
	region_showing=true;
	region_showing_fx.start({
		'opacity': 0.8
	}).chain(function() {
		hoverShow(false);
	});	
	
}

// Aborts region display
function abortActionPaneRender() {
	if (region_showing) {
		region_showing_fx.cancel();	
		region_showing=false;
		disposeActionPane(true);
	}
}
