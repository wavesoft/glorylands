	
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

// Hook chains for later-included scripts
var CBChain = new Class({
    initialize: function(){
        this.chain=[];
    },
	register: function(chain_name, callback) {
		if (!$defined(this.chain[chain_name])) { this.chain[chain_name]=[]; };
		this.chain[chain_name].push(callback);
	},
	call: function(chain_name,parm) {
		if ($defined(this.chain[chain_name])) {
			this.chain[chain_name].each(function(e){ e(parm); });
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
var lastZ=1000;
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

			// Remove body element
			eBody.remove();

			// Cleanup window variables
			winCache[header] = false;
						
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
	
		// Bind to dragger
		var d = new Drag.Move(eBody, {handle: eHead});
		
		// Save usefull variables
		winCache[header] = [eBody, eContent, slider];
		
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

var data_cache = new Array();
var ex_buffer_data = ""; /* Any extra data required on Grid Area */

function initDisplayBuffer() {
	// Store any data previous initialized in design-time on datapane
	ex_buffer_data = $('datapane').innerHTML;	
}

function displayBuffer(buffer, cached, hLink, hImg, hText) {
	var data = buffer;
	if (cached) {
		data_cache.push($('datapane').innerHTML);
		data+='<span class="maplabel"><a class="navlink" href="javascript:restoreview();" title="Return to previous window"><img align="absmiddle" src="images/UI/navbtn_back.gif" /></a> Return</span>';
	} else {
		data_cache = new Array();	
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
	}
	data+=ex_buffer_data;
	$('datapane').innerHTML = data;
}
function restoreview() {
	if (data_cache.length<1) return;
	var data = data_cache.pop();
	$('datapane').innerHTML = data;
}


// ======================================================
//  Dedicated Data Window (DDW) Functions
// ======================================================

var ddw_visible = false;

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
		iHost.setStyles({'display':''});
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
	
	// If DDWin is not visible, do a clean fade in
	if (!ddw_visible) {
		iHost.setStyles({'display':''});
		iPopup.setStyles({opacity: 0});
		iContent.setStyles({opacity: 1});
		iContent.setHTML('Please&nbsp; <img src="images/UI/loading2.gif" align="absmiddle" /> &nbsp;wait...');
		popup.start({
			'opacity': 1			
		});
		ddw_visible = true;
	// If already exists, fade out the content and reset it
	} else {
		content.start({
			'opacity': 0		  
		}).chain(function () {
			iContent.setHTML('Please&nbsp; <img src="images/UI/loading2.gif" align="absmiddle" /> &nbsp;wait...');
			content.start({
				'opacity': 1			  
			});
		});
	}
}

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
					window.alert(e);	
				}

			// ## Set action ranges ##
			} else if (mType=='RANGE') {

				if ($defined(msg.message[i][1])) stackRegion(msg.message[i][1]);

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
			headers: {'X-Request': 'JSON'},
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
					var rollback = true;
					var head_link = "";
					var head_image = "";
					var title = "";
					
					// Try to load data from object
					if ($defined(obj.rollback)) rollback=obj.rollback;
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
					
					// Display data buffer
					displayBuffer(obj.text, rollback, head_link, head_image, title);
					
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

					// This process is time-consuming
					// Prevent messages from being executed now
					lockMessages(true);
					
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

					// Is there overlay data defined?
					if ($defined(obj.data)) {
						// If yes, update overlay cache
						overlay_grid = obj.data;
					}
					
					// Is there navigation grid defined?
					if ($defined(obj.nav)) {
						// If yes, update navigation grid cache
						nav_grid = obj.nav;
					}
					
					// Are there new render coordinates?
					if ($defined(obj.x)) grid_x=obj.x;
					if ($defined(obj.y)) grid_y=obj.y;
	
					// Is there a map name defined?
					if ($defined(obj.map)) {
						// If not loaded, switch to this map
						if (current_map!=obj.map) {
							loadGrid(obj.map);
						// If loaded, update view
						} else {
							showStatus("Updating Grid");
							setTimeout(renderUpdate,100);
						}
					} else {
						// If not defined, just update current view
						setTimeout(renderUpdate,100);
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
						window.alert(e);
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


				// ## Unknown Interface ##
				} else {
					// Process unknown messages to later-included scripts
					callback.call('ioreply',obj);
				}
				
				// If we have exchange messages, handle them now
				if ($defined(obj.messages)) {
					handleMessages(obj.messages);
				}
				
				// Callback the function we are supposed to call
				if (oncomplete_callback) oncomplete_callback(obj);	
			},
			onFailure: function(obj) {
				if (!silent) showStatus('<font color=\"red\">Connection failure!</font>', 1000);
				if (oncomplete_callback) oncomplete_callback(false);	
			}
		}).send(data);
	}
	catch (e) {
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

function gridClick(x,y) {
	gloryIO('?a=map.grid.get&x='+x+'&y='+y);
}

// GRID Load system Step 1: Load map JSON file
function loadGrid(map) {
	current_map=map;
	showStatus('Loading Map...');
	
	// Download the map JSON data without bothering PHP
	var data = new Json.Remote('data/maps/'+map+'.jmap', {
			onComplete: function(o) {
				// Data Arrived. Store all the incormation into local cache
				data_grid = o.grid;
				collision_grid = o.zid;
				data_dictionary = o.dic;
				grid_range = o.range;
				
				// Build grid from the dictionary
				showStatus('Loading Graphics...');
				setTimeout(processDictionary, 100);
			},
			onFailure: function(e) {
				showStatus('<font color=\"red\">Map Transaction Error!</font>', 1000);
				data_grid = false;
				collision_grid = false;
				data_dictionary = false;
			}
	}).send();
}

// GRID Load system Step 2: Translate dictionary and load all the images
function processDictionary() {
	// Build a reverse-dictionary
	var images = new Array();
	for (img in data_dictionary) {
		if ($defined(img)) {
			//im = new String(img);
			//img = im.substring(0, img.length-3)+'png';
			images.push('images/tiles/'+img);
		} else {
			images.push('images/tiles/blank.gif');
		}
	}
	data_dictionary = images;
	showStatus('Loading Graphics...');
	
	// Load all dictionary images
	var ovfTimer = 0;
	new Asset.images(images, {
		onComplete: function(){
			showStatus('Updating Grid');
			setTimeout(renderUpdate, 100);
			if (ovfTimer>0) clearTimeout(ovfTimer);
		},
		onProgress: function(img_id) {
			var perc = Math.ceil(100*img_id/images.length);
			if (perc > 100) perc-=100; /* When objects are already cached, the maximum value seems to be 200% */
			showStatus('Loading Graphics ['+perc+' %]');
			
			// More than a second of delay between two images is too much
			// Probably it is stucked 
			// BUGFIX: 1) Unreasonable stops on 99% on IE
			//         2) Blocks when file does not exists
			if (ovfTimer>0) clearTimeout(ovfTimer);
			ovfTimer=setTimeout(renderUpdate, 2000);
		}
	});

}

// Helping function to peform a sort on the first element
// of an array
function level2_sort(a,b) {
    return a[0] - b[0];
}

// GRID Load system Step 3:
// Render the current grid and overlaies
function renderUpdate() {
	var grid_w = 24;
	var grid_h = 16;

	// Detect corner
	var bx = grid_x - (grid_w/2);
	var by = grid_y - (grid_h/2);
	
	// Check for data existance
	if (!grid_range || !$defined(grid_range.x)) {
		showStatus('<font color=\"red\">Map cannot be loaded!</font><br />', 5000);
		return false;	
	}
	
	// Validate data range
	if (bx<grid_range.x.m) bx=grid_range.x.m;
	if (bx+grid_w>grid_range.x.M) bx=grid_range.x.M-grid_w;
	if (by<grid_range.y.m) by=grid_range.y.m;
	if (by+grid_h>grid_range.y.M) by=grid_range.y.M-grid_h;

	// Store Global x/y positions
	glob_x_base = bx;
	glob_y_base = by;

	// Render part
	var data = '<table cellspacing="0" cellpadding="0" id="tbl" style="background-image: url(images/tiles/'+grid_display['background']+');">';
	for (var y=by; y<by+grid_h; y++) {
		data+="<tr>";
		for (var x=bx; x<bx+grid_w; x++) {
//			data+='<td><div onclick="gridClick('+x+','+y+');">';
			data+='<td><div>';
			
			// Prepare Z-Buffer Cache
			var images = new Array();
			
			// Push Grid Elements
			if ($defined(data_grid[y])) {
				if ($defined(data_grid[y][x])) {
					$each(data_grid[y][x], function(img, id) {
						images.push([id, data_dictionary[img]]);
					});
				}
			}
			// Push Overlay Elements
			if ($defined(overlay_grid[y])) {
				if ($defined(overlay_grid[y][x])) {
					$each(overlay_grid[y][x], function(img, id) {
						images.push([id, 'images/tiles/'+img]);
					});
				}
			}
			
			// Display images
			if (images.length>0) {
				images.sort(level2_sort); /* Z-Index is built here */
				images.each(function(e){ data+='<img src="'+e[1]+'">'; });
			}
			data+='</div></td>';
		}
		data+="</tr>";
	}
	data+="</table>";
	data+='<div id="grid_rect" class="dbf_container" style="border-width: 2px; border-style: solid; border-color: #FF0000; position: absolute; display: none"></div>';
	
	// Store buffer
	displayBuffer(data,
	  	grid_display['rollback'],
		grid_display['head_link'],
		grid_display['head_image'],
		grid_display['title']
	);
	showStatus();
	
	// Free any locked messages
	lockMessages(false);
}

// ======================================================
//  Information hover and popup window handling
// ======================================================

var hoverInfo={text:'',x:0,y:0,sz:{x:0,y:0}};
function hoverShow(text,x,y) {
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
			layer.setStyles({'left':x-(hoverInfo.sz.x/2), 'top':y-hoverInfo.sz.y-12});	
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

// ======================================================
//  Action Panel Management functions
// ======================================================

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
	window.alert(e);	
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


// ======================================================
//  Periodical Message Popper (Message-only Data feedback)
// ======================================================
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

// ======================================================
//  PieMenu functions (replacing dropdownMenu)
// ======================================================
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
	btn_link.setAttribute('href','javascript:;');
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

// ======================================================
//  Basic site initialization functions
// ======================================================

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
			//gridClick(xP,yP);
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

// Handle ESC key to cancel any active operation
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
		e.stop();
		if (e.key == 'b') {
			gloryIO('?a=interface.inventory');
		}
	}
});

$(document).addEvent('mouseup', function(e){
	// Dispose dropdown menu
	piemenu_dispose();	
});

$(document).addEvent('contextmenu', function(e){
	var e = new Event(e);
	// Disable right click on the document
	piemenu_dispose();	
	e.stop();
});

// Initialize mouse handler on window
$(document).addEvent('mousemove', function(e) {
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
});

// #################### DEBUG #####################

function display(url) {
	gloryIO('index.php?'+url);
}

