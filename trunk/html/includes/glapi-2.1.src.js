	
// Identify browser
var isInternetExplorer=(navigator.userAgent.indexOf("MSIE")>=0);
var isMozilla=(navigator.userAgent.indexOf("Gecko")>=0);
var isOpera=(navigator.userAgent.indexOf("Opera")>=0);

// ### DEBUG FUNCTIONS ###
function $trace(obj) {
	ans='';
	$each(obj, function(value, name) {
		if (ans!='') ans+=', ';
		ans+='['+name+'] = '+value;
	});	
	return ans;
}

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


// Hook chains for later-included scripts
var CBChain = new Class({
    initialize: function(){
        this.chain=[];
    },
	register: function(chain_name, callback) {
		if (!$defined(this.chain[chain_name])) { this.chain[chain_name]=[]; };
		this.chain[chain_name].push(callback);
	},
	call: function(chain_name,p1,p2,p3,p4,p5) {
		if ($defined(this.chain[chain_name])) {
			try {
				this.chain[chain_name].each(function(e){ e(p1,p2,p3,p4,p5); });
			} catch(e) {
			}
		}
	}
});
var callback = new CBChain();

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

// =====================================================
//  Display and handle the message popup window, used
//  to entertain the user while waitting
// =====================================================

var waiterShown=false;
var waiterFX=false;
var waiterDisposer=0;

function initWaiter() {
	waiterFX=new Fx.Styles('waiter', {duration: 400, transition: Fx.Transitions.Back.easeIn,
		onComplete: function() {
			if (!waiterShown) {
				$('waiter_host').setStyles({'display':'none'});
			}
		}
	});
	$('waiter_host').setStyles({'display':'none'});
}

function showStatus(text,timeout) {
	try {
		// Cancel any waiter disposer
		if (waiterDisposer>0) {
		   clearTimeout(waiterDisposer);		
		   waiterDisposer=0;
 	    }
		if (!text) {
			if (waiterShown) {
				waiterShown=false;
				waiterFX.stop();
				waiterFX.start({
//					'width': 10,
//					'height':10	,
					'opacity': 0
				});
			}
		} else {
			$('waiter').setHTML(text);	
			if (!waiterShown) {
				$('waiter_host').setStyles({'display':''});
				$('waiter_host').setStyles({'z-index':lastZ});
				waiterShown=true;
				waiterFX.stop();
				waiterFX.start({
//					'width': 250,
//					'height':70,
					'opacity': 1
				});
			}
			if (timeout) waiterDisposer=setTimeout(function() { showStatus(); }, timeout);
		}
	} catch(e) {
		
	}
}

// =====================================================
//  Create a draggable, popup window with the specified
//  header and content
// =====================================================
var winCache = [];
var winStack = [];
var lastZ=250002;

function rect_collide(r1, r2) {
	if ((r1.l >= r2.l) && (r1.l <= r2.r) && (r1.t >= r2.t) && (r1.t <= r2.b)) {
		return true;
	}
	if ((r2.l >= r1.l) && (r2.l <= r1.r) && (r2.t >= r1.t) && (r2.t <= r1.b)) {
		return true;
	}
	return false
}

function draggable_win_align(win) {
	// If this window collides with another, move it in a position that it doesn't
	var collides = false;
	var w_pos = win.getPosition();
	var w_siz = win.getSize().size;
	var w_rect = {
		l: w_pos.x,
		t: w_pos.y,
		r: w_pos.x+w_siz.x,
		b: w_pos.y+w_siz.y
	};
	var limits = $(document.body).getSize().size;
	$debug('[Align] Rect: '+$trace(w_rect));
	$debug('[Align] Limits: '+$trace(limits));	
	var i;
	winStack.each(function(win) {
		$debug('[Align] Checking :'+win);			
		try{
			var pos = win.getPosition();
			var siz = win.getSize().size;
			var rect = {
				l: pos.x,
				t: pos.y,
				r: pos.x+siz.x,
				b: pos.y+siz.y
			};
			if (rect_collide(rect, w_rect)) {
				collides = {x: pos.x+16, y: pos.y+16};				
				break;
			}
		} catch(e) {
		}
	});
	if (collides != false) {
		win.setStyle('top', collides.y);	
		win.setStyle('left', collides.x);	
		//draggable_win_align(win);
	}
}

function createWindow(header, content, x, y, w, h, guid) {
	
	// If window is not already visible, create a new
	if (!winCache[header]) {
	
		// Create elements
		var eBody = document.createElement('div');
		var eHead = document.createElement('div');
		var eContent = document.createElement('span');
		var linkToggle = document.createElement('a');
		var linkDispose = document.createElement('a');
		
		// Initiate elements
		if (!isInternetExplorer) {
			eBody.setAttribute('class', 'container');
			eHead.setAttribute('class', 'dragger');
			eContent.setAttribute('class', 'content');
			linkToggle.setAttribute('class', 'toggle');
			linkDispose.setAttribute('class', 'dispose');
		} else { /* Internet explorer requires explicit definition */
			eBody.setAttribute('className', 'container');
			eHead.setAttribute('className', 'dragger');
			eContent.setAttribute('className', 'content');
			linkToggle.setAttribute('className', 'toggle');
			linkDispose.setAttribute('className', 'dispose');
		}
		linkToggle.setAttribute('title', 'Minimize');
		linkDispose.setAttribute('title', 'Close');
		eHead.innerHTML = "<span align=\"top\" class=\"left\">&nbsp;</span><span class=\"center\">"+header+"</span><span class=\"right\">&nbsp;</span>";
		
		// Nest elements
		eBody.appendChild(eHead);
		eBody.appendChild(eContent);
		eContent.innerHTML = content;
	
		// Initiate slider
		if (!isMozilla) { /* Slide effect works with bugs on mozilla */
			var slider = new Fx.Slide(eContent);
		}
		linkToggle.setAttribute('href', 'javascript:;');
		linkToggle.innerHTML = '&nbsp;';
		eHead.appendChild(linkToggle);
		$(linkToggle).addEvent('click', function(e){
			e = new Event(e);
			if (!isMozilla) { 
				slider.toggle();
			} else { /* Slide effect works with bugs on mozilla */
				if (eContent.style.display == 'none') {
					eContent.style.display = '';
				} else {
					eContent.style.display = 'none';
				}
			}
			e.stop();
		});
	
		// Initiate disposer
		linkDispose.setAttribute('href', 'javascript:void(null)');
		linkDispose.innerHTML = '&nbsp;';
		eHead.appendChild(linkDispose);
		$(linkDispose).addEvent('click', function(e){
			e = new Event(e);

			// Notify server that the window is closed
			gloryIO('?a=window.closed&guid='+guid,false,true);

			// Cleanup window variables
			delete winCache[header];
			winStack.remove(eBody);

			// Remove body element
			eBody.remove();

			e.stop();
		});
	
		// Prepare Fade Effects	
		$(eHead).addEvent('mousedown', function(e) {
			eBody.setStyles({'opacity': 0.7, 'z-index': lastZ++});
		});
		$(eHead).addEvent('mouseup', function(e) {
			eBody.setStyles({'opacity': 1});
		});
		$(eBody).addEvent('mousedown', function(e) {
			eBody.setStyles({'z-index': lastZ++});
		});
		
		// Move to a specific location (if set)
		if (x) eBody.setStyles({'left':x});
		if (y) eBody.setStyles({'top':y});
		if (w) eBody.setStyles({'width':w});
		if (h) eBody.setStyles({'height':h});
				
		// Create object
		eBody.setStyles({'z-index': lastZ++});
		document.body.appendChild(eBody);

		// Align the window
		draggable_win_align(eBody);

		// Bind to dragger
		var d = new Drag.Move(eBody, {handle: eHead});
				
		// Save usefull variables
		winCache[header] = [eBody, eContent, slider];
		winStack.push(eBody);
		
		// Return content object
		return eBody;
	
	// Else, If it is already visible, show it and update content
	} else {
		
		var eBody = winCache[header][0];
		var eContent = winCache[header][1];
		var slider = winCache[header][2];

		// Display window
		if (!isMozilla) { 
			slider.slideIn();
		} else { /* Slide effect works with bugs on mozilla */
			eContent.style.display = '';
		}
		
		// Update content
		eContent.innerHTML = content;

		// Update sizes (if set)
		if (w) eBody.setStyles({'width':w});
		if (h) eBody.setStyles({'height':h});

	}
}

// ======================================================
//           Main Window area Interface management
// ======================================================

var data_cache = [];
var ex_buffer_data = ""; /* Any extra data required on Grid Area */

function initDisplayBuffer() {
	// Store any data previous initialized in design-time on buffer host
	ex_buffer_data = $('databuffer').innerHTML;	
}

function clearDisplayBuffer() {
	$('databuffer').setHTML('');
}

function displayBuffer(buffer, hLink, hImg, hText) {
	// Reset map to remove all of it's objects
	map_reset();
	wgrid_dispose();
	$('databuffer').setStyle('visibility','visible');
	$('datapane').setStyle('visibility','hidden');
	$('datahost').setStyles({
		'background-image': ''
	});	
	
	var data = buffer;
	if (hText!='') {
		if (hLink!='') {
			if (hImg!='') {
				data+='<span class="maplabel"><a class="navlink" href="javascript:gloryIO(\''+hLink+'\',false,true);"><img align="absmiddle" src="images/'+hImg+'" /></a> '+hText+'</span>';
			} else {
				data+='<span class="maplabel"><a class="navlink" href="javascript:gloryIO(\''+hLink+'\',false,true);"> '+hText+'</a></span>';
			}
		} else {
			if (hImg!='') {
				data+='<span class="maplabel"><img align="absmiddle" src="images/'+hImg+'" /> '+hText+'</span>';
			} else {
				data+='<span class="maplabel"> '+hText+'</span>';
			}
		}
	}

	data+=ex_buffer_data;
	$('databuffer').setHTML(data);
}

// ======================================================
//  Dedicated Data Window (DDW) Functions
// ======================================================

var ddw_visible = false;

function ddwin_change(url) {
	var prepare = ddwin_prepare();
	prepare.chain = function() {
		gloryIO(url,false,true);
	};
}

function ddwin_dispose() {
	if (ddw_visible) {
		var popup = new Fx.Styles('dd_popup', {duration: 500, transition: Fx.Transitions.Cubic.easeOut});
		var content = new Fx.Styles('dd_content', {duration: 500, transition: Fx.Transitions.Cubic.easeOut});
		var iPopup = $('dd_popup');
		var iHost = $('dd_host');
		var iContent = $('dd_content');

		content.start({
			'opacity': 0
		}).chain(function() {
			iContent.setHTML('');
			popup.start({
				'opacity': 0,
				'width': 10,
				'height': 10
			}).chain(function() {
				iHost.setStyles({'display':'none'});
			});
		});
		ddw_visible = false;
	}
}

function ddwin_show(width, height, text) {
	var iPopup = $('dd_popup');
	var iContent = $('dd_content');
	var iHost = $('dd_host');
	var popup = new Fx.Styles('dd_popup', {duration: 500, transition: Fx.Transitions.Cubic.easeOut});
	var content = new Fx.Styles('dd_content', {duration: 500, transition: Fx.Transitions.Cubic.easeOut});
	// If DDWin is visible, perform a transformation of the window
	if (ddw_visible) {
		content.start({
			'opacity': 0
		}).chain(function() {
			popup.start({
			'height': height,
			'width': width,
			'opacity': 1
			}).chain(function() {
				// Update content
				iContent.setHTML("<div style=\"position:relative; width:100%; height:100%\"><span class=\"dd_head\"><a href=\"javascript:ddwin_dispose()\">X</a></span>"+text+"</div>");
				content.start({
					'opacity': 1
				});
			});
		});
	
	// If not visible, prepare a small window and fade in
	} else {
		iHost.setStyles({'display':'', 'z-index':lastZ++});
		iPopup.setStyles({'opacity': 0, 'width': 10, 'height': 10});
		iContent.setStyles({'opacity': 0, 'display': 'none'});
		iContent.setHTML("<div style=\"position:relative; width:100%; height:100%\"><span class=\"dd_head\"><a href=\"javascript:ddwin_dispose()\">X</a></span>"+text+"</div>");
		popup.start({
			'opacity': 1,
			'width': width,
			'height': height
		}).chain(function() {
			iContent.setStyles({'display':''});
			content.start({
				'opacity': 1
			});
		});
		ddw_visible = true;
	}
}

function ddwin_prepare() {
	var iPopup = $('dd_popup');
	var iContent = $('dd_content');
	var iHost = $('dd_host');
	var popup = new Fx.Styles('dd_popup', {duration: 500, transition: Fx.Transitions.Cubic.easeOut});
	var content = new Fx.Styles('dd_content', {duration: 500, transition: Fx.Transitions.Cubic.easeOut});
	var ret_chain = {chain:null}; // We use objects to perform by reference alteration after the function has returned
	
	// If DDWin is not visible, do a clean fade in
	if (!ddw_visible) {
		iHost.setStyles({'display':'', 'z-index':lastZ++});
		iPopup.setStyles({opacity: 0});
		iContent.setStyles({opacity: 1});
		iContent.setHTML('Please&nbsp; <img src="images/UI/loading2.gif" align="absmiddle" /> &nbsp;wait...');
		ddw_visible = true;
		ret_chain=popup.start({
			'opacity': 1,
			'width': 120,
			'height': 20
		}).chain(ret_chain.chain);
		
	// If already exists, fade out the content and reset it
	} else {
		content.start({
			'opacity': 0		  
		}).chain(function () {
			iContent.setHTML('Please&nbsp; <img src="images/UI/loading2.gif" align="absmiddle" /> &nbsp;wait...');
			content.start({
				'opacity': 1			  
			});
			ret_chain=popup.start({
				'width': 120,
				'height': 20
			}).chain(ret_chain.chain);
		});
	}
	
	return ret_chain;
}

/* ==================================================================================================================================== */
/*                                        SECTION : Engine communication
/* ==================================================================================================================================== */

// ======================================================
//  This function handles the Data messages arrived from
//  JSON exchanges
// ======================================================

var msgstack = []	   // If message handling is locked by a time-consuming
var msglocked = false; // function, messages are stacked till it's completed

function lockMessages(lock) {
	msglocked = lock;
	
	// If unlocked, process any stacked messages
	if (!lock) {
		$each(msgstack, function(obj, id) {
			handleMessages(obj);
		});
		msgstack = [];
	}
}

function handleMessages(msg) {
	// If message handling is locked, stack messages
	if (msglocked) {
		msgstack.push(msg);
		return;
	}
	
	var mType='', mText='';
	for (var i=0; i<msg.count; i++) {
		if ($defined(msg.message[i])) {
			mType=msg.message[i][0];
			mText=msg.message[i][1];

			// ## Display a message box ##
			if (mType=='MSGBOX') {
				window.alert(mText);

			// ## Display a popup window ##
			} else if (mType=='POPUP') {
				var width = 310;
				if ($defined(msg.message[i][3])) width=msg.message[i][3];
				var left = (screen.width - width)/2;
				var top = 120;
				var guid = false;
				if ($defined(msg.message[i][4])) if (msg.message[i][4]!=false) left=msg.message[i][4];
				if ($defined(msg.message[i][5])) if (msg.message[i][5]!=false) top=msg.message[i][5];
				if ($defined(msg.message[i][6])) if (msg.message[i][6]!=false) guid=msg.message[i][6];

				// Display window
				createWindow(msg.message[i][2], msg.message[i][1], left, top, width, false, guid);

			// ## Perform a gloryIO Call ##
			} else if (mType=='CALL') {
				var silent=true;
				if ($defined(msg.message[i][2])) silent=msg.message[i][2];
				gloryIO(mText,false,silent);

			// ## Navigate browser into a new location ##
			} else if (mType=='NAVIGATE') {
				window.location='index.php?a='+mText;

			// ## The grid is altered. Perform an update ##
			} else if (mType=='UPDATEGRID') {
				gloryIO('?a=map.grid.get&quick=1',false,true);

			// ## Show/Hide navigation rect ##
			} else if (mType=='RECT') {
				var r = $('grid_rect');
				try {
					if ($defined(r)) {
						if (mText) {
							rectinfo.w=1; rectinfo.h=1; rectinfo.bx=0; rectinfo.by=0; rectinfo.url=''; rectinfo.clickdispose=true;
							rectinfo.silent=false;
							if ($defined(msg.message[i][2])) rectinfo.url=msg.message[i][2];
							if ($defined(msg.message[i][3])) rectinfo.w=msg.message[i][3];
							if ($defined(msg.message[i][4])) rectinfo.h=msg.message[i][4];
							if ($defined(msg.message[i][5])) rectinfo.bx=msg.message[i][5];
							if ($defined(msg.message[i][6])) rectinfo.by=msg.message[i][6];
							if ($defined(msg.message[i][7])) rectinfo.clickdispose=msg.message[i][7];
							if ($defined(msg.message[i][8])) rectinfo.silent=msg.message[i][8];
							r.setStyles({'display':''});
						} else {
							r.setStyles({'display':'none'});
						}
					}
				} catch (e) {
					window.alert('RECT Error'+e);	
				}

			// ## Alter a map object ##
			} else if (mType=='ALTER') {

			// ## Show action grid ##
			} else if (mType=='ACTIONGRID') {

				if ($defined(msg.message[i][1])) {
					wgrid_design(msg.message[i][1],true);
					wgrid_show();
				}

			// ## Set feeder interval ##
			} else if (mType=='POLLINTERVAL') {
				
				feeder_interval = msg.message[i][1];

			// ## Unknown message arrived ##
			} else {
				// Process unknown messages to later-included scripts
				callback.call('message',msg.message[i]);
			}
		}
	}
}

// ======================================================
//  This function executes the basic game I/O operations
//  through a JSON communication interface
// ======================================================
var data_io_time = 0;
function gloryIO(url, data, silent, oncomplete_callback) {	
	try {
		// Reset feeder timer
		reset_feeder();
	
		if (!silent) showStatus('Loading...&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/UI/mouseloading.gif" />');
	//	window.alert(url);
		data_io_time = $time();
		var json = new Json.Remote(url, {
			headers: {'X-StreamID': '41293'},
			onComplete: function(obj) {
				showStatus();
				data_io_time = $time()-data_io_time;

				// Try to detect incoming data mode
				var mode='NONE';
				if ($defined(obj.mode)) {
					mode=obj.mode;
				}
	
				// ==========================
				//   Incoming data handling	
				// ==========================
				
				// ## Popup window ##
				if (mode=='POPUP') {
					// Defaults
					var left = 100;
					var top = 20;
					var width = 310;
					var height = false;
					var guid = false;

					// Try to load data from object
					if ($defined(obj.left)) left=obj.left;
					if ($defined(obj.top)) top=obj.top;
					if ($defined(obj.width)) width=obj.width;
					if ($defined(obj.height)) width=obj.height;
					if ($defined(obj.guid)) guid=obj.guid;
					// Display window
					createWindow(obj.title, obj.text, left, top, width, height, guid);
					
				// ## HTML Data for main window ##
				} else if (mode=='MAIN') {
					// Default
					var head_link = "";
					var head_image = "";
					var title = "";
					
					// Try to load data from object
					if ($defined(obj.head_link)) head_link=obj.head_link;
					if ($defined(obj.head_image)) head_image=obj.head_image;
					if ($defined(obj.title)) title=obj.title;
					
					// Reset data grids used by GRID mode
					nav_grid = [];
					overlay_grid = [];
					resetRegion();

					// Dispose any visible misc layers
					piemenu_dispose();
					disposeActionPane();
					
					// Hide grid and show the new window
					map_curtain(true).chain(function() {
						displayBuffer(obj.text, head_link, head_image, title);
						map_curtain(false);
					});
					
				// ## Placeholder for information mode ##
				} else if (mode=='INFO') {
					
				// ## Placeholder for full-page mode ##
				} else if (mode=='FULL') {
					
				} else if (mode=='BLANK') {
					
				// ## NOTHING handler ##
				} else if (mode=='NONE') {
					
				// ## GRID Data for main window window ##
				} else if (mode=='GRID') {
					/*  Feed data for grid management */

					// Reset any active action range grids 
					resetRegion();
					
					// Default Grid Display Parameters
					var rollback = false;
					var head_link = "";
					var head_image = "";
					var title = "";
					var background = "none.gif";
					
					// Try to load parameter information from respond object
					if ($defined(obj.rollback)) rollback=obj.rollback;
					if ($defined(obj.head_link)) head_link=obj.head_link;
					if ($defined(obj.head_image)) head_image=obj.head_image;
					if ($defined(obj.title)) title=obj.title;
					if ($defined(obj.background)) background=obj.background;
					
					// Store those parameters into a local cache (used by the renderUpdate function
					// to display the data into the main window)
					grid_display['rollback'] = rollback;
					grid_display['head_link'] = head_link;
					grid_display['head_image'] = head_image;
					grid_display['background'] = background;
					grid_display['title'] = title;
										
					// Are there new render coordinates?
					if ($defined(obj.x)) grid_x=obj.x;
					if ($defined(obj.y)) grid_y=obj.y;

					// Is there a map name defined?
					if ($defined(obj.map)) {
						// If not loaded, switch to the new map
						if (map_current!=obj.map) {
							map_curtain(true)
							map_reset();
							map_loadbase(obj.map);
						}
					}
					
					// If we have objects, update map objects
					if ($defined(obj.objects)) {
						
						// Perform some operations on the objects
						$each(obj.objects, function(o,k) {
													
							// Update image paths for static images
							if ($defined(o.image)) {
								var s = new String(o.image);
								if (s.indexOf('.php')>0){
								} else {
									obj.objects[k].image='images/'+o.image;
								}
							}
						});
						map_preloaded_update(obj.objects);	
					}


				// ## Dedicated window (Black pop-in window with dedicated focus) ##
				} else if (mode=='DEDICATED') {
					// Defaults
					var height = 210;
					var width = 400;
					// Try to load data from object
					if ($defined(obj.height)) height=obj.height;
					if ($defined(obj.width)) width=obj.width;
					// Display window
					try {
						ddwin_show(width,height,obj.text);
					}
					catch (e) {
						window.alert('DDWin Show Error: '+e);
					}

				// ## HTML Data for dropdown menu ##
				} else if (mode=='DROPDOWN') {

					// Update piemenu menus and text
					var menus = [];
					var text = [];

					if ($defined(obj.menus)) menus=obj.menus;
					if ($defined(obj.text)) text=obj.text;
					if (pie_wait_icon) {
						piemenu_show(menus, text);
					}

				// ## Error response ##
				} else if (mode=='ERROR') {
					if (!silent) showStatus('<font color=\"red\">'+obj.error+'</font>', 5000);					

				// ## Unknown Interface ##
				} else {
					// Process unknown messages to later-included scripts
					callback.call('ioreply',obj);
				}

				// If we have exchange messages, handle them now
				if ($defined(obj.messages)) {
					handleMessages(obj.messages);
				}
				
				// Notify message operation completion
				try {
					var rexp = /a=(.*)&/i;
					var parts = rexp.exec(url+'&');					
					if (!$chk(parts)) {
						callback.call('iocomplete',false, obj);
					} else {
						callback.call('iocomplete',parts[1], obj);
					}
				} catch (e) {
					window.alert('RegEx Error: '+e);
				}

				// Callback the function we are supposed to call
				if (oncomplete_callback) oncomplete_callback(obj);	
			},
			onFailure: function(obj) {
				window.alert('JSON Error: '+$trace(obj));
				if (!silent) showStatus('<font color=\"red\">Connection failure!</font>', 1000);
				if (oncomplete_callback) oncomplete_callback(false);	
			}
		}).send(data);
	}
	catch (e) {
		window.alert('GloryIO Error: '+obj.message);
		if (!silent) showStatus('<font color=\"red\">Data Error!</font>', 1000);
	}

}

// ======================================================
//  Theese functions handle the main grid data
// ======================================================

var data_grid=false;		// Data Grid 2D Array
var collision_grid=false;	// Attennuation (Collision) grid 2D Array
var data_dictionary=false;	// Translate dictionary 2D Array
var grid_range=false;		// Grid RECT 1D Array
var overlay_grid = false;	// Overlaied objects 2D array
var nav_grid = false;		// Navigation (hover) grid 2D array, including dictionary
var current_map = "";		// Currently loaded map name
var glob_x_base = 0;		// \ 
var glob_y_base = 0;		// -- Top-left map offset corner
var grid_x=0, grid_y=0;		// [IN] Render Coordinates

// Parameters to send on displayBuffer:
var grid_display = {'rollback': false, 'head_link': false, 'head_image': false, 'title': false, 'background': 'none.gif'};
// Float RECT dimensions, offset point, the request URL to send when user clicks somewere and the flag to dispose rect if clicked:
var rectinfo = {w:3,h:3,bx:1,by:2,url:'',clickdispose:false,silent:false};	

/* ==================================================================================================================================== */
/*                                             SECTION : Sprite animator
/* ==================================================================================================================================== */

var fx_sprites_animating=[];

/**
  * Sprite enterFrame Handling
  *
  * This function handles the next frame feeding
  *
  */
function fx_sprite_frame(id) {
	var info = fx_sprites_animating[id];
	
	// Calcuate frame
	var frame = info.frame+1;
	var max_frames = info.animation.length;
	if (frame >= max_frames) frame=0;
	info.frame=frame;
	
	var current_frame = info.animation[frame];
	var ofs_x = current_frame[0]*info.sprite_w;
	var ofs_y = current_frame[1]*info.sprite_h;	
	info.object.setStyles({
		'top': -ofs_y,
		'left': -ofs_x
	});
}


/**
  * Prepare a sprite for animation
  *
  * This function prepares a sprite for animation
  *
  */
function fx_sprite_prepare(object, x_sprites, y_sprites) {
	var dim = $(object).getSize().size;
	var pos = $(object).getPosition();
	var div_mask = new Element('div');	
	var info = {
		'object': object,
		'mask': div_mask ,
		'width': dim.x,
		'height': dim.y,
		'sprite_w': (dim.x/x_sprites),
		'sprite_h': (dim.y/y_sprites),
		'animation': [],
		'frame': 0,
		'timer': 0
	};
	div_mask.setStyles({
		'width': info.sprite_w,
		'height': info.sprite_h,
		'overflow': 'hidden',
		'position': 'absolute',
		'left': pos.x,
		'top': pos.y
	});
	$(object).setStyles({
		'position': 'absolute'		
	});
	div_mask.injectBefore(object);
	$(object).injectInside(div_mask);
	fx_sprites_animating.push(info);
	return div_mask;
}

/**
  * Return the ID of a sprite
  *
  * This function returns the ID of a sprite initialized with fx_sprite_prepare
  *
  */
function fx_sprite_get_id(object) {
	for (var i=0;i<fx_sprites_animating.length;i++) {
		if (fx_sprites_animating[i].object == object) {
			return i;
		} else if (fx_sprites_animating[i].mask == object) {
			return i;
		}
	}
	return -1;
}

/**
  * Start sprite animation
  *
  * This function starts the sprite animation
  *
  */
function fx_sprite_animate(object,frame_rate,animation) {
	var i = fx_sprite_get_id(object);
	if (i<0) return false;
	var info = fx_sprites_animating[i];
	info.animation = animation;
	info.frame = 0;
	if (info.timer!=0) clearInterval(info.timer);
	info.timer = setInterval(fx_sprite_frame, (1000/frame_rate), i);
	return true;
}

/**
  * Start sprite animation
  *
  * This function starts the sprite animation
  *
  */
function fx_sprite_stop(object, frame) {
	var i = fx_sprite_get_id(object);
	if (i<0) return false;
	
	var info = fx_sprites_animating[i];
	clearInterval(info.timer);
	if (!frame) frame = info.animation[0];
	var ofs_x = frame[0]*info.sprite_w;
	var ofs_y = frame[1]*info.sprite_h;	
	info.object.setStyles({
		'top': -ofs_y,
		'left': -ofs_x
	});	
	return true;
}

/* ==================================================================================================================================== */
/*                                             SECTION : Map rendering
/* ==================================================================================================================================== */

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
var map_viewpoint = {x:0,y:0};	// The center of the current view
var map_center_fx = null;		// This holds the last instance of the center Fx class - Used to stop animation
var map_current = '';			// The currently active MAP
var map_playeruid = 0;			// The player's object UniqueID

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
	
	if (map_center_fx) map_center_fx.stop();
	
	map=new Fx.Styles($('datapane'), {duration: 400, unit: 'px', transition: Fx.Transitions.Expo.easeInOut});	
	map.start({
		'left': -x+384,
		'top': -y+256
	});
	map_center_fx = map;
	
	if (update) map_viewpoint = {'x':x, 'y':y};	
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
	if (map_curtain_fx) map_curtain_fx.stop();
	map_curtain_fx=new Fx.Styles($('dataloader'), {duration: 400, unit: 'px', transition: Fx.Transitions.Expo.easeInOut});	

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
	map_current = '';
	map_center_fx = null;
	map_scroll_pos = {x:0,y:0};	
	map_dynamic_objects = [];
	map_objects = [];
	map_info = [];
	map_back = [];
	map_object_index = [];
	map_last_id = 0;
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
	var data = new Json.Remote('data/maps/'+mapname+'.map', {
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
  * Event Callback for objects
  *
  * This function is used as callback for the events on the map objects
  *
  */
function map_objecttrigger(uid, trigger, e) {
	try {
		
		// Get Scroll position
		var scrl = getScrollPosition();	
		var id=map_object_index.indexOf(uid);
		
		if (trigger=='contextmenu') {
			piemenu_dispose();
			if ($defined(map_objects[id].info.guid)) {
				piemenu_init(e.client.x+scrl.x,e.client.y+scrl.y,map_objects[id].info.guid,'MAP');
			}
		} else if (trigger == 'mousemove') {
			if ($defined(map_objects[id].info.name)) {
				
				// Prepare and show hover tip window
				var content = map_objects[id].info.name;
				if ($defined(map_objects[id].info.subname)) content+='<br /><font color="#33FF33" size="1"><em>'+map_objects[id].info.subname+'</em></font>';
				hoverShow(content, e.client.x+scrl.x, e.client.y+scrl.y);
				
				// Display action range
				if ($defined(map_objects[id].info.range)) {
					var pos = map_objects[id].object.getPosition();
					var siz = map_objects[id].object.getSize().size;
					var h = pos.y+(siz.y/2); // Display range only if cursor is below the hald of the char
					if (e.client.y+scrl.y>h) {
						if (!wgrid_visible) {
							wgrid_design(map_objects[id].info.range);
							wgrid_show();
						}
					}
				}
			}
		} else if (trigger == 'click') {
			// If clicked, display action range without mouse position checking
			if ($defined(map_objects[id].info.range)) {
				if (!wgrid_visible) {
					wgrid_design(map_objects[id].info.range);
					wgrid_show();
				}
			
			// Elseways, if we have click-action, perform it now
			} else if ($defined(map_objects[id].info.click)) {
				gloryIO(map_objects[id].info.click, false, false);
			}

		} else if (trigger == 'mouseout') {
			hoverShow(false);
			if (!wgrid_hard_dispose) wgrid_hide();
		}
		
	} catch (e) {
		window.alert('Object Trigger Error: '+e);
	}
}

/**
  * Preload dynamic objecs
  *
  * This function is used to preload any delay-loaded objects (using JSON)
  * Thi is used to make sure the newly objects contain the correct dimensions.
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
			$debug('Loading: '+e.image);
		} else {
			$debug('Already loaded: '+e.image);			
		}
	});	
	
	// Precache all map images
	if (images.length>0) {
		showStatus('Loading new objects...');
		new Asset.images(images, {
			onComplete: function(){
				showStatus();
				map_updatedata(data);
			}
		});
	} else {
		map_updatedata(data);	
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
	
	// Callback to alter/edit object data
	callback.call('object_put', data);
	
	// Create and insert image
	var im = $(document.createElement('img'));
	im.src = data.image;
	$('datapane').appendChild(im);	

	// If the image is directionable, convert the image to sprite
	if ($defined(data.sprite)) im = fx_sprite_prepare(im, data.sprite[0],data.sprite[1]);
	var size = im.getSize().size;

	// Re-map x-y
	var x=data.x*32-data.cx;
	var y=data.y*32-data.cy-size.y;

	// Calculate new Z-Index
	var zindex = (data.y-1)*500+x;
	if (zindex<0) zindex=1;

	// Apply image styles
	im.setStyles({
		'position': 'absolute',
		'left': x,
		'top': y,
		'z-index': zindex
	});
	
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
	
	// Check if this object is the player, and update UID value
	if ($defined(data.player)) map_playeruid=uid;	
	
	// If the object is dynamic, append triggers and allow advanced show effects
	if (data.dynamic) {
		im.addEvent('contextmenu', function(e) {
			var e = new Event(e);
			map_objecttrigger(uid, 'contextmenu', e);						
			e.stop();
		});
		im.addEvent('mousemove', function(e) {
			var e = new Event(e);
			im.setStyles({'opacity':0.7});
			map_objecttrigger(uid, 'mousemove', e);
			e.stop();
		});
		im.addEvent('mouseout', function(e) {
			var e = new Event(e);
			im.setStyles({'opacity':1});
			map_objecttrigger(uid, 'mouseout', e);
			e.stop();
		});
		im.addEvent('mousedown', function(e) {
			var e = new Event(e);
			map_objecttrigger(uid, 'mousedown', e);
			e.stop();
		});
		im.addEvent('mouseup', function(e) {
			var e = new Event(e);
			map_objecttrigger(uid, 'mouseup', e);
			e.stop();
		});
		im.addEvent('click', function(e) {
			var e = new Event(e);
			map_objecttrigger(uid, 'click', e);
			e.stop();
		});
		
		if ($defined(data.fx_show)) {						
			switch (data.fx_show) {
				case 'fade':
					var imfx=new Fx.Styles(im, {wait: false, duration: 400, transition: Fx.Transitions.Quad.easeIn});
					im.setStyles({
						'opacity':0
					});
					imfx.start({
						'opacity':1		   
					});
					break;
				
				case 'pop':
					var imfx=new Fx.Styles(im, {wait: false, duration: 400, transition: Fx.Transitions.Back.easeOut});
					im.setStyles({
						'opacity':0,
						'top':y+32
					});
					imfx.start({
						'opacity':1,
						'top':y
					});
					break;

				case 'drop':
					var imfx=new Fx.Styles(im, {wait: false, duration: 400, transition: Fx.Transitions.Bounce.easeOut});
					im.setStyles({
						'opacity':0,
						'top':y-200
					});
					imfx.start({
						'opacity':1,
						'top':y
					});
					break;				

				case 'zoom':
					var imfx=new Fx.Styles(im, {wait: false, duration: 400,transition: Fx.Transitions.Quad.easeIn});
					im.setStyles({
						'opacity':0,
						'width': 5,
						'height': 5,
						'left': (x+(size.x/2)),
						'top': (y+(size.y/2))
					});
					imfx.start({
						'opacity':1,
						'width': size.x,
						'height': size.y,
						'left': x,
						'top': y
					});
					break;				
			}
		}

		// If we must focus on this item, do it now
		if ($defined(data.focus)) {
			map_center(x+Math.ceil(size.x/2),y+Math.ceil(size.y/2),true);
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

	callback.call('object_remove', data.info);

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
				var imfx=new Fx.Styles(data.object, {wait: false, duration: 400, transition: Fx.Transitions.Quad.easeOut});
				imfx.start({
					'opacity':0
				}).chain(disposer);
				break;
			
			case 'pop':
				var imfx=new Fx.Styles(data.object, {wait: false, duration: 400, transition: Fx.Transitions.Back.easeIn});
				imfx.start({
					'opacity':0,
					'top':data.y+32
				}).chain(disposer);
				break;

			case 'drop':
				var imfx=new Fx.Styles(data.object, {wait: false, duration: 400, transition: Fx.Transitions.Back.easeIn});
				imfx.start({
					'opacity':0,
					'top':data.y-200
				});
				break;

			case 'zoom':
				var imfx=new Fx.Styles(data.object, {wait: false, duration: 400,transition: Fx.Transitions.Quad.easeIn});
				imfx.start({
					'opacity':0,
					'width': 5,
					'height': 5,
					'left': (data.x+(data.width/2)),
					'top': (data.y+(data.height/2))
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
  * Path animation (updateobject Helper)
  *
  * This function moves a map object object on the
  * continuous position given in the grid
  *
  * The directional variable can be 0,1 or 2 and it means:
  *  0 - The object is not directional
  *  1 - The object is 4-side directional (Front,Back,Left,Right)
  *  2 - The object is 8-side directional
  *
  */
var map_fx_pathmove_stack = [];
function map_fx_pathmove(object, path) {
	$trace('Moving path');
	// This function is used to prohibit multiple requests for
	// animation on the same object.
	// This function just chains the concurrent requests and handles
	// it, only when the previouse ones are completed
	if ($defined(map_fx_pathmove_stack[object.info.id])) {
		//$debug('[path] ('+object.info.id+') Appending to stack...');
		map_fx_pathmove_stack[object.info.id].push(
			function() {map_fx_pathmove_thread(object,path); }
		);
	} else {
		//$debug('[path] ('+object.info.id+') Creating stack...');
		map_fx_pathmove_stack[object.info.id] = [
			function() {map_fx_pathmove_thread(object,path); }
		];
		map_fx_pathmove_next(object.info.id);
	}
}
function map_fx_pathmove_next(id) {
	$trace('Moving next');
	//$debug('[path] ('+id+') Next called!');
	if ($defined(map_fx_pathmove_stack[id])) {
		if (map_fx_pathmove_stack[id].length == 0) {
			//$debug('[path] ('+id+') No more. Erasing...!');
			delete map_fx_pathmove_stack[id];
		} else {
			//$debug('[path] ('+id+') We have '+map_fx_pathmove_stack[id].length+' to do');
			var f = map_fx_pathmove_stack[id].shift();
			f();
		}
	}
}
function map_fx_pathmove_build_spritepath(object_info, facing_side) {
	// Default direction grid
	// This one maps side-ID to row number
	var dirgrid = {
		'rd': 3,
		'ru': 3,
		'r': 3,
		'ld': 1,
		'lu': 1,
		'l': 1,
		'u': 2,
		'd': 0
	};
	// Default animation columns
	var ani = {
		'walk': [1,2,3,4],
		'stay': 0
	};
	
	// Check if the object contains custom coordinates
	if ($defined(object_info.info.sprite_direction_grid)) dirgrid=object_info.info.sprite_direction_grid;
	if ($defined(object_info.info.sprite_direction_ani)) ani=object_info.info.sprite_direction_ani;
	$debug('Using dirgrid: '+$trace(dirgrid));
	$debug('And ani: '+$trace(ani));
	
	// Detect the row ID, based on the facing side
	var row=0;
	if (!$defined(dirgrid[facing_side])) {
		if (facing_side.length == 2) {
			// For example: If not found 'rb' => Check for 'r' only
			if ($defined(dirgrid[facing_side[0]])) {
				row = dirgrid[facing_side[0]];
			}
		}
	} else {
		row = dirgrid[facing_side];
	}

	// Create the frames based on the information above
	var frames=[];
	for (var i=0; i<ani.walk.length; i++) {
		frames.push([ani.walk[i], row]);
	}
	$debug('Built frames: '+$trace(frames));
	
	// Find the standing frame
	var stand=[ani.stay, row];
	
	// Return the animation frames and the standing frame
	return [frames, stand];
}
function map_fx_pathmove_thread(object_info, path) {
	$trace('Moving thread');
	var i=0;
	var spath;
	var last_stand_dir = [0,0];
	var id = object_info.info.id;
	var object = object_info.object;
	var last_ani_dir = '';
	var ani_dir = '';
	var directional = 0;
	if ($defined(object_info.info.directional)) directional=object_info.info.directional;

	// Speed is the tiles the player can enter per move
	var speed = 5;
	if ($defined(object_info.info.speed)) speed=object_info.info.speed;	
	var enter_interval = 1000-(speed*100); if (enter_interval<10) enter_interval=10;
	var animation_fps = speed*2; if (animation_fps<1) animation_fps=1;
	
	var px_transition=new Fx.Styles(object, {duration: enter_interval, unit: 'px', transition: Fx.Transitions.linear});
	var walk_step = function() {
		// Check if we have more steps to go
		if (!$defined(path[i])) {
			fx_sprite_stop(object,last_stand_dir);	
			map_fx_pathmove_next(id);
			return;
		}
		var j=i;
		i++;

		// If this object is directional, calculate it's direction
		// and update the image
		if (directional) {
			
			// Calculate previous and next position
			if (j<1) {
				var info = $(object).getStyles('left','top');
				var from_x = Math.round(Number(info.left.replace('px',''))/32);
				var from_y = Math.round(Number(info.top.replace('px',''))/32);
			} else {
				var from_x = path[j-1].x;
				var from_y = path[j-1].y;
			}
			var to_x = path[j].x;
			var to_y = path[j].y;						
			var dir_x = to_x - from_x;
			var dir_y = to_y - from_y;
			
			var dir = []; // Defaults
			last_stand_dir = [0,0];

			if (dir_x>0) {
				if (dir_y>0) {
					// Right-Down
					spath = map_fx_pathmove_build_spritepath(object_info, 'rd');
					dir = spath[0]; last_stand_dir = spath[1];
					ani_dir = 'rd';
				} else if (dir_y<0) {
					// Right-Up
					spath = map_fx_pathmove_build_spritepath(object_info, 'ru');
					dir = spath[0]; last_stand_dir = spath[1];
					ani_dir = 'ru';
				} else {
					// Right
					spath = map_fx_pathmove_build_spritepath(object_info, 'r');
					dir = spath[0]; last_stand_dir = spath[1];
					ani_dir = 'r';
				}
			} else if (dir_x<0) {
				if (dir_y>0) {
					// Left-Down
					spath = map_fx_pathmove_build_spritepath(object_info, 'ld');
					dir = spath[0]; last_stand_dir = spath[1];
					ani_dir = 'ld';
				} else if (dir_y<0) {
					// Left-Up
					spath = map_fx_pathmove_build_spritepath(object_info, 'lu');
					dir = spath[0]; last_stand_dir = spath[1];
					ani_dir = 'lu';
				} else {
					// Left
					spath = map_fx_pathmove_build_spritepath(object_info, 'l');
					dir = spath[0]; last_stand_dir = spath[1];
					ani_dir = 'l';
				}
			} else {
				if (dir_y>0) {
					// Down
					spath = map_fx_pathmove_build_spritepath(object_info, 'd');
					dir = spath[0]; last_stand_dir = spath[1];
					ani_dir = 'd';
				} else if (dir_y<0) {
					// Up
					spath = map_fx_pathmove_build_spritepath(object_info, 'u');
					dir = spath[0]; last_stand_dir = spath[1];
					ani_dir = 'u';
				} else {
					// (Default)
					spath = map_fx_pathmove_build_spritepath(object_info, 'u');
					dir = spath[0]; last_stand_dir = spath[1];
					ani_dir = 'u';
				}
			}
			
			// Animate the object only if the animation is changed
			if (last_ani_dir != ani_dir) {
				fx_sprite_animate(object, animation_fps, dir);
				last_ani_dir = ani_dir;
			}
		}
		
		// Calculate new Z-Index
		var dim = object.getSize().size;
		var zindex = (path[j].y+Math.round(dim.y/32))*500+path[j].x;
		if (zindex<0) zindex=1;
		
		// Update z index
		object.setStyle('z-index',zindex);
		
		// Move object
		px_transition.start({
			'left': path[j].x*32,
			'top': path[j].y*32-dim.y+32
		}).chain(walk_step);
		
	}

	// Wheck if we are repeating the same path
	// (Checking if the last position is the current position)
	var info = $(object).getStyles('left','top');
	var dim = $(object).getSize().size;
	var from_x = Math.round(Number(info.left.replace('px',''))/32);
	var from_y = Math.round(Number(info.top.replace('px',''))/32);
	var to_x = path[path.length-1].x;
	var to_y = path[path.length-1].y-1;
	if ((from_x == to_x) && (from_y == to_y)) {
		return;
	}
	
	// Start walking
	walk_step();
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

	// Callback notification
	callback.call('object_update', {'old':old_data, 'new':data});

	// Store default values if something is missing
	if (!$defined(data.cx)) data.cx=old_data.cx;
	if (!$defined(data.cy)) data.cy=old_data.cy;
	if (!$defined(data.x)) data.x=old_data.info.x;
	if (!$defined(data.y)) data.y=old_data.info.y;
	
	// Update image
	old_data.object.src = data.image;
	var size = old_data.object.getSize().size;

	// Re-map x-y
	var x=data.x*32-data.cx;
	var y=data.y*32-data.cy-size.y;

	// Calculate new Z-Index
	var zindex = (data.y-1)*500+x;
	if (zindex<0) zindex=1;

	// Update cache
	old_data.x = x;
	old_data.y = y;
	old_data.width = size.x;
	old_data.height = size.y;
	old_data.info = data;

	// Check if this object is the player, and update UID value
	if ($defined(data.player)) map_playeruid=uid;	

	// If we have transition, use them.
	if (data.fx_move) {
		switch (data.fx_move) {
			case 'slide':
				var px_transition=new Fx.Styles(old_data.object, {duration: 800, unit: 'px', transition: Fx.Transitions.linear});
				var z_transition=new Fx.Styles(old_data.object, {duration: 800, unit: '', transition: Fx.Transitions.linear});				
				z_transition.start({'z-index':zindex});
				px_transition.start({
						'top': y
				}).chain(function() {
						px_transition.start({'left': x});
				});
				break;

			case 'bounce':
				var px_transition=new Fx.Styles(old_data.object, {duration: 800, unit: 'px', transition: Fx.Transitions.Elastic.easeOut});
				px_transition.start({
						'left': x,
						'top': y
				});
				old_data.object.setStyles({
						'z-index':zindex
				});
				break;

			case 'fade':
				var imfx=new Fx.Styles(old_data.object, {wait: false, duration: 400, transition: Fx.Transitions.Quad.easeOut});
				imfx.start({
					'opacity':0
				}).chain(function(){
					old_data.object.setStyles({
						'left': x,
						'top': y,
						'z-index': zindex
					});			
					imfx.start({
						'opacity':1
					})
				});				
				break;
				
			case 'path':
				if ($defined(data.fx_path)) {
					map_fx_pathmove(old_data, data.fx_path);
					
					// No need to keep the grid in memory any more
					delete data.fx_path;
					break;
				}
				
			default:
				old_data.object.setStyles({
					'left': x,
					'top': y,
					'z-index': zindex
				});			
		}		
	
	// Elseways, just update the object
	} else {		
		// Update object		
		old_data.object.setStyles({
			'left': x,
			'top': y,
			'z-index': zindex
		});
	}
	
	// If we must focus on this item, do it now
	if ($defined(data.focus)) {
		map_center(x+Math.ceil(old_data.width/2),y+Math.ceil(old_data.height/2),true);
	}
	
	// Javascript uses byRef for
	// objects. That means the stored
	// object in the array is now updated
}

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
			e.remove();							   
		});
		wgrid_host.remove();
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
	e.setHTML('&nbsp;');
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


/* ==================================================================================================================================== */
/*                                             SECTION : Info and popup
/* ==================================================================================================================================== */

var hoverInfo={text:'',x:0,y:0,sz:{x:0,y:0}};
function hoverShow(text,x,y,align) {
	var layer = $('hoverLayer');
	if (text) {
		if (hoverInfo.text!=text) {
			hoverInfo.text=text;	
			layer.setHTML(text);
			hoverInfo.sz = layer.getSize().size;
			layer.setStyles({visibility:'visible'});	
		}
		if (hoverInfo.x!=x || hoverInfo.y!=y) {
			hoverInfo.x=x; hoverInfo.y=y;
			
			// Calculate the offsets
			var left = x-(hoverInfo.sz.x/2);
			var top = y-hoverInfo.sz.y-12;
			
			// Calculate the vertical position
			if (top < 0) {
				top += hoverInfo.sz.y+12;
			}
			
			// Render in different ways as specified
			if (!align || (align == 'center')) {
				left=x-(hoverInfo.sz.x/2);
			} else if(align == 'left') {
				left=x;
			} else if(align == 'right') {
				left=x-hoverInfo.sz.x;
			}
			
			layer.setStyles({'left':left, 'top':top, 'z-index': lastZ});	
		}
	} else {
		if (hoverInfo.text!='') {
			hoverInfo={text:'',x:0,y:0,sz:{x:0,y:0}};
			layer.setStyles({visibility:'hidden'});	
		}
	}
}

var dropdownInfo={visible:false};
function dropdownShow(x,y,guid,position,parent_guid) {
	if (!parent_guid) parent_guid=0;
	var layer = $('dropdownLayer');
	layer.setHTML('<img src="images/UI/loading2.gif" align="absmiddle" />');
	layer.setStyles({visibility:'visible', 'left':x-5, 'top':y-5});
	dropdownInfo.visible=true;
	gloryIO('?a=interface.dropdown&guid='+guid+'&pos='+position+'&parent='+parent_guid, false, true);
	layer.focus();
	layer.addEvent('mouseleave', function() {
		disposeDropDown();								
	});
}
function disposeDropDown(){
	var layer = $('dropdownLayer');
	if (dropdownInfo.visible) {
		layer.setStyles({visibility:'hidden'});
		dropdownInfo.visible=false;
	}
}

/* ==================================================================================================================================== */
/*                                           SECTION : Action Region Object
/* ==================================================================================================================================== */

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
		var fx = new Fx.Styles(panel, {duration: 500, transition: Fx.Transitions.Cubic.easeOut});
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
	panel.setHTML(data);
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

	region_showing_fx = new Fx.Styles('actionpane', {duration: 500, transition: Fx.Transitions.Cubic.easeIn,
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
		region_showing_fx.stop();	
		region_showing=false;
		disposeActionPane(true);
	}
}


/* ==================================================================================================================================== */
/*                                      SECTION : Engine polling - Update feeder
/* ==================================================================================================================================== */

var feeder_interval=5000;
var feeder_timer=0;
var feeder_enabled=true;
var iD = 0;

// Called when a gloryIO event occured. This is used so we don't
// overload the server by concurrent requests.
function reset_feeder() {
	if (feeder_timer) clearTimeout(feeder_timer);
	if (feeder_enabled) {
		feeder_timer=setTimeout(feeder, feeder_interval);
	}
}

function feeder() {
	// Every then and now, dump the messages currently
	// stacked and waitting for me to get them

	iD++;
	$('prompt').setHTML('Feeded: '+iD);
	gloryIO('msgfeed.php',false,true,function(e) {
		reset_feeder();
	});
}

/* ==================================================================================================================================== */
/*                                        SECTION : Pie Menu (Replaced Dropdown)
/* ==================================================================================================================================== */

var pie_stack=[];
var pie_info=[];
var pie_wait_icon=null;
var pie_menutext=null;
var pie_visible=false;

function piemenu_dispose() {
	if (pie_stack.length>=0) {
		for (var i=0; i<pie_stack.length; i++) {
			var dispose_ani=new Fx.Styles(pie_stack[i], {duration: 400, transition: Fx.Transitions.Back.easeIn,
				onComplete: function() {
					this.element.remove();
				}
			});
			dispose_ani.start({
				'opacity': 0,
				'left': pie_info.x-17,
				'top': pie_info.y-17
			});
		}
		pie_stack=[];
	}
	pie_info=[];
	
	// Dispose old menu text
	if (pie_menutext) {
		var dispose_ani=new Fx.Styles(pie_menutext, {duration: 400, transition: Fx.Transitions.Back.easeIn,
			onComplete: function() {
				this.element.remove();
			}
		});
		dispose_ani.start({
			'opacity': 0
		});
		pie_menutext=null;
	}
	
	// Just in case we are still waiting for response...
	piemenu_waitdispose();
	pie_visible=false;
}

function piemenu_spawnicon(x,y,icon,tip,url) {
	try {
	var host = $(document.createElement('div'));
	host.setStyles({
		'width': '34px',
		'height': '34px',
		'position': 'absolute',
		'background-repeat': 'no-repeat',
		'background-image': 'url(images/UI/icon-bg.png)',
		'text-align': 'center',
		'left': (pie_info.x-17)+'px',
		'top': (pie_info.y-17)+'px',
		'opacity': 0,
		'z-index': lastZ++
	});

	var host_ani=new Fx.Styles(host, {duration: 400, transition: Fx.Transitions.Back.easeOut});

	var btn_link = $(document.createElement('a'));
	btn_link.setProperty('href','javascript:;');
	btn_link.addEvent('click', function(e) {
		e = new Event(e);
		piemenu_dispose();
		gloryIO(url);
		e.stop();
	});
	btn_link.alt = tip;
	btn_link.title = tip;
	
	var btn_image = $(document.createElement('img'));
	btn_image.src = icon;
	btn_image.border = '0';

	btn_link.appendChild(btn_image);
	host.appendChild(btn_link);
	
	pie_stack.push(host);
	$(document.body).appendChild(host);
	
	// Play the effect
	host_ani.start({
		'opacity': 1,
		'left': x-17+'px',
		'top': y-17+'px'
	});

	
	}	catch (ex) {}
}

function piemenu_show(menu,text) {
	pie_visible=true;
	
	try {
	// Dispose waiting menu
	piemenu_waitdispose();
	hoverShow();

	// Prepare info
	if (pie_info.length==0) return;
	var distance = 5+(menu.length*5);

	// Show text (if text exists)
	if (text!='') {
		
		// Dispose old menu text
		if (pie_menutext) {
			pie_menutext.remove();
			pie_menutext=null;
		}
		
		// Create the element
		pie_menutext = $(document.createElement('div'));
		pie_menutext.setStyles({
			'position': 'absolute',
			'background-color': '#000000',
			'boder-width': '2px',
			'border-style': 'solid',
			'border-color': '#CCCCCC',
			'font-size': '10px',
			'padding' : '2px',
			'opacity' : 0,
			'text-align': 'center',
			'z-index': lastZ++
		});
		var menutext_ani=new Fx.Styles(pie_menutext, {duration: 400, transition: Fx.Transitions.Back.easeIn});

		// If we have a menu, restrain text size
		if (menu.length>0) {
			pie_menutext.setStyles({
				'width': ((distance*2)+60)+'px'
			});
		}
		
		// Import the new item into the body
		pie_menutext.setHTML(text);
		document.body.appendChild(pie_menutext);		
		
		// Move it to the center
		var szinfo = pie_menutext.getSize();
		pie_menutext.setStyles({
			'left': (pie_info.x-(szinfo.size.x/2)),
			'top': (pie_info.y+distance+15)
		});		

		// Play the effect
		menutext_ani.start({
			'opacity': 1
		});
	}

	// Show Pie
	if (menu.length>0) {
		var step = (2*Math.PI)/menu.length;
		var radius = 0;
		for (var i=0; i<menu.length; i++) {
			var item_x = pie_info.x + (distance * Math.cos(radius));
			var item_y = pie_info.y + (distance * Math.sin(radius));
			piemenu_spawnicon(item_x,item_y,menu[i][0],menu[i][1],menu[i][2]);
			radius += step;
		}
	}

	} catch (ex) {}
}

function piemenu_waitdispose() {
	try {
		if (pie_wait_icon) {
			pie_wait_icon.remove();
			pie_wait_icon=null;
		}
	} catch (e) {}
}

function piemenu_wait() {
	piemenu_waitdispose();
	pie_wait_icon = $(document.createElement('img'));
	pie_wait_icon.src = 'images/UI/loading-big.gif';
	$(document.body).appendChild(pie_wait_icon);
	var szinfo = pie_wait_icon.getSize();
	pie_wait_icon.setStyles({
		'left': (pie_info.x-(szinfo.size.x/2))+'px',
		'top': (pie_info.y-(szinfo.size.y/2))+'px',
		'position': 'absolute'
	});
}

function piemenu_init(x,y,guid,position,parent_guid) {
	pie_info = {
		'x': x,
		'y': y
	};
	gloryIO('?a=interface.dropdown&guid='+guid+'&pos='+position+'&parent='+parent_guid, false, true);
	piemenu_wait();
}

/* ==================================================================================================================================== */
/*                                        SECTION : Event handling and initialization
/* ==================================================================================================================================== */

// The overlay item the player has his mouse over (contains the dictionary entry)
var hoveredItem=false;

$(window).addEvent('load', function(e){
	
	/* -=[ PHASE 1 ]=- */

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
	// Disable feeder when we lost focus
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
			window.alert($trace(map_objects));
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
	v_center={x:0,y:0};
	v_last={x:0,y:0};
	map_center(map_viewpoint.x,map_viewpoint.y);
});

$(document).addEvent('mouseup', function(e){
	// Dispose any probably open popups
	piemenu_dispose();	//## Pie Menu
	wgrid_hide();		//## Walking grid
});

$(document).addEvent('contextmenu', function(e){
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

// Initialize mouse handler on window
$(document).addEvent('mousemove', function(e) {
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
	moveto(xP,yP);
	if (!pie_visible) {

		// Collision test with action grids
		hitTestRegion(xP,yP);

		// Display hover info
		if (hoveredItem) {
			$('prompt').setHTML('X: '+xP+', Y: '+yP+' With Zero at: '+glob_x_base+','+glob_y_base+', Overlay: '+Overlay+' Dic:'+DicEntry.d.name);
			hoverShow(DicEntry.d.name, e.event.clientX+scrl.x, e.event.clientY+scrl.y);
		} else {
			$('prompt').setHTML('X: '+xP+', Y: '+yP+' With Zero at: '+glob_x_base+','+glob_y_base);
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

// #################### DEBUG #####################

function display(url) {
	gloryIO('index.php?'+url);
}

