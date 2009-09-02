/* ==================================================================================================================================== */
/*                                       SECTION : Walk collision grid
/* ==================================================================================================================================== */
var wgrid_elements=[];
var wgrid_host=null;
var wgrid_pos={'x':0,'y':0};
var wgrid_last_design_pos={'x':0,'y':0};
var wgrid_visible=false;
var wgrid_dispose_timer=null;
var wgrid_hard_dispose=false;

function wgrid_show() {
	// If we are about to hide the window, but this event
	// (probably from the grid elements) is called, stop disposion
	if (wgrid_dispose_timer!=null) {
		clearTimeout(wgrid_dispose_timer);
		wgrid_dispose_timer=null;
	}
	
	// If really hidden, show it
	if (wgrid_host==null) return;
	if (wgrid_visible) return;
	wgrid_host.setStyle('display','');
	wgrid_visible=true;
}

function wgrid_hide() {
	if (!wgrid_visible) return;
	wgrid_dispose_timer = window.setTimeout(function(){
		try {
			wgrid_host.setStyle('display','none');
			wgrid_visible=false;
		} catch(e) {
		}
	},200);
}

function wgrid_dispose(partial_dispose) {
	try {
		$each(wgrid_elements, function(e){
			e.dispose();							   
		});
		wgrid_host.dispose();
	} catch(e){
	}
	if (wgrid_dispose_timer!=null) {
		clearTimeout(wgrid_dispose_timer);
		wgrid_dispose_timer=null;
	}
	wgrid_elements=[];
	wgrid_host=null;
	wgrid_pos={'x':0,'y':0};
	wgrid_visible=false;
	if (!$defined(partial_dispose)) {
		wgrid_last_design_pos={'x':0,'y':0};	
	}
}

/**
  * Build the walking grid
  *
  */
function wgrid_put(x,y,href,data) {
	// Create and initialize element
	var e = $(document.createElement('a'));
	e.setProperty('href','javascript:;');
	e.set('html', '&nbsp;');
	e.addEvent('click', function(e){
		var e=new Event(e);
		wgrid_dispose();
		gloryIO(href,{'id':data.id},true);
		e.stop();
	});
	
	wgrid_host.appendChild(e);
	e.setStyles({
		'left': x*32,
		'top':y*32,
		'display': 'block',
		'width':32,
		'height':32,
		'background-color': '#00FF00',
		'position': 'absolute',
		'opacity': 0.5,
		'text-decoration': 'none'
	});
	
	if ($defined(data.color)) e.setStyle('background-color', data.color);
	if ($defined(data.title)) e.setProperty('title', data.title);
			
	// Store elements for removal
	wgrid_elements.push(e);
}

/**
  * Build and show the action grid
  *
  * If hard_dispose is true, the action grid will not be disposed
  * if the mouse cursor moves away of the region
  *
  */
function wgrid_design(data,hard_dispose) {
	var enter_grid = [];
		
	// Dispose old grid
	wgrid_hard_dispose = hard_dispose;
	wgrid_dispose(true);
	
	// Prepare the host
	wgrid_host = $(document.createElement('div'));
	wgrid_host.setStyles({
		'position': 'absolute',
		'visibility': 'visible',
		'background-color': '#000000',
		'left':0,
		'top':0,
		'z-index': lastZ++
	});
	$('datapane').appendChild(wgrid_host);

	// Do not use mouse to dispose grid in hard dispose mode
	if (!hard_dispose) {
		wgrid_host.addEvent('mouseleave',function(e){
			wgrid_hide();										
		});
		wgrid_host.addEvent('mouseenter',function(e){
			wgrid_show();
		});
	}

	// Render the elements
	$each(data.grid, function(e) {
		wgrid_put(e.x,e.y,data.base,e);
	});
	
	wgrid_visible=true;
}