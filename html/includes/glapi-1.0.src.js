
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
var lastZ=10;
function createWindow(header, content, x, y, w, h) {
	
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
		linkToggle.setAttribute('href', 'javascript:void(null)');
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
					data+='<span class="maplabel"><a class="navlink" href="javascript:gloryIO(\''+hLink+'\');"><img align="absmiddle" src="images/'+hImg+'" /></a> '+hText+'</span>';
				} else {
					data+='<span class="maplabel"><a class="navlink" href="javascript:gloryIO(\''+hLink+'\');"> '+hText+'</a></span>';
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
	// If DDWin is visible, perform a transmutation of the window
	if (ddw_visible) {
		content.start({
			'opacity': 0
		}).chain(function() {
			popup.start({
			'height': width,
			'width': height,
			'opacity': 1
			}).chain(function() {
				// Update content
				iPopup.setHTML("<div style=\"position:relative; width:100%; height:100%\"><span class=\"dd_head\"><a href=\"javascript:ddwin_dispose()\">X</a></span>"+text+"</div>");
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

function handleMessages(msg) {
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
				if ($defined(msg.message[i][4])) left=msg.message[i][4];
				if ($defined(msg.message[i][5])) top=msg.message[i][5];
				// Display window
				createWindow(msg.message[i][2], msg.message[i][1], left, top, width);

			// ## Perform a gloryIO Call ##
			} else if (mType=='CALL') {
				var silent=true;
				if ($defined(msg.message[i][2])) silent=msg.message[i][2];
				gloryIO(mText,false,silent);

			// ## Navigate browser into a new location ##
			} else if (mType=='NAVIGATE') {
				window.location='index.php?a='+mText;

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
		if (!silent) showStatus('Loading...&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/UI/mouseloading.gif" />');
	//	window.alert(url);
		data_io_time = $time();
		var json = new Json.Remote(url, {
			headers: {'X-Request': 'JSON'},
			onComplete: function(obj) {
				showStatus();
				data_io_time = $time()-data_io_time;

				// If we have exchange messages, handle them now
				if ($defined(obj.messages)) {
					handleMessages(obj.messages);
				}

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
					// Try to load data from object
					if ($defined(obj.left)) left=obj.left;
					if ($defined(obj.top)) top=obj.top;
					if ($defined(obj.width)) width=obj.width;
					// Display window
					createWindow(obj.title, obj.text, left, top, width);
					
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
					// Display data buffer
					displayBuffer(obj.text, rollback, head_link, head_image, title);
					
				} else if (mode=='INFO') {
					
				} else if (mode=='FULL') {
					
				} else if (mode=='BLANK') {
					
				} else if (mode=='NONE') {
					
				// ## GRID Data for main window window ##
				} else if (mode=='GRID') {
					// Feed data for grid management
					
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

					// Update dropdown menu text
					if ($defined(obj.text) && dropdownInfo.visible) {
						$('dropdownLayer').setHTML(obj.text);
					}

				// ## Unknown Interface ##
				} else {
					// Process unknown messages to later-included scripts
					callback.call('ioreply',obj);
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
function dropdownShow(x,y,guid) {
	var layer = $('dropdownLayer');
	layer.setHTML('<img src="images/UI/loading2.gif" align="absmiddle" />');
	layer.setStyles({visibility:'visible', 'left':x+5, 'top':y+5});
	dropdownInfo.visible=true;
	gloryIO('?a=interface.dropdown&guid='+guid, false, true);
}
function disposeDropDown(){
	var layer = $('dropdownLayer');
	if (dropdownInfo.visible) {
		layer.setStyles({visibility:'hidden'});
		dropdownInfo.visible=false;
	}
}

// ======================================================
//  Periodical Message Popper (Message-only Data feedback)
// ======================================================
var feeder_interval=5000;
var feeder_timer=0;
var feeder_enabled=true;
var iD = 0;
function feeder() {
	// Every then and now, dump the messages currently
	// stacked and waitting for me to get them

	iD++;
	$('prompt').setHTML('Feeded: '+iD);
	gloryIO('msgfeed.php',false,true,function(e) {
		if (feeder_timer) clearTimeout(feeder_timer);
		if (feeder_enabled) {
			feeder_timer=setTimeout(feeder, feeder_interval);
		}
	});
}

// ======================================================
//  Basic site initializatoin functions
// ======================================================

// The overlay item the player has his mouse over (contains the dictionary entry)
var hoveredItem=false;

$(window).addEvent('load', function(e){
	
	/* -=[ PHASE 1 ]=- */

	// Initialize waiter animation
	initWaiter();
	
	// Initialize mouse handler on datapane
	$('datapane').addEvent('mousemove', function(e) {
		e = new Event(e);							
		
		// Get DataPane left offset
		var dpX = $('datapane').getLeft();
		var dpY = $('datapane').getTop();
		// Calculate cell X,Y
		var bxP = Math.ceil((e.event.clientX-dpX)/32)-1;
		var byP = Math.ceil((e.event.clientY-dpY)/32)-1;
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
		
		if (DicEntry.d) {
			$('prompt').setHTML('X: '+xP+', Y: '+yP+' With Zero at: '+glob_x_base+','+glob_y_base+', Overlay: '+Overlay+' Dic:'+DicEntry.d.name);
			hoveredItem=DicEntry;
			hoverShow(DicEntry.d.name, e.event.clientX, e.event.clientY);			
		} else {
			$('prompt').setHTML('X: '+xP+', Y: '+yP+' With Zero at: '+glob_x_base+','+glob_y_base);
			hoveredItem=false;
			hoverShow(false);
		}
		
		// Rectangle handling
		var r = $('grid_rect');
		if (r) {
			if (r.getStyle('display')!='none') r.setStyles({left:(bxP-rectinfo.bx)*32, top:(byP-rectinfo.by)*32, width: rectinfo.w*32, height:rectinfo.h*32, display:''});
		}		
		
	});
	$('datapane').addEvent('contextmenu', function(e) {
		e = new Event(e);
		if (hoveredItem!=false) {
			// Display the dropdown menu
			dropdownShow(e.event.clientX,e.event.clientY,hoveredItem.g);	
		} else {
			// Clicked over no item
			disposeDropDown();	
		}
		//window.alert($trace(e));
		e.stop();
	});
	$('datapane').addEvent('click', function(e) {
		e = new Event(e);							

		// Get DataPane left offset
		var dpX = $('datapane').getLeft();
		var dpY = $('datapane').getTop();
		// Calculate cell X,Y
		var xP = Math.ceil((e.event.clientX-dpX)/32)+glob_x_base-1;
		var yP = Math.ceil((e.event.clientY-dpY)/32)+glob_y_base-1;
	
		if (rectinfo.url == '') {
			gridClick(xP,yP);
		} else {
			gloryIO(rectinfo.url+'&x='+xP+'&y='+yP);
			if (rectinfo.clickdispose) {
				rectinfo.url='';
				$('grid_rect').setStyles({'display':'none'});
			}
		}
		
		// Dispose dropdown (if visible)
		disposeDropDown();
	});

	
	/* -=[ PHASE 2 ]=- */

	// Ger the grid
	gloryIO('?a=map.grid.get');
	
	// Start message feeder
	feeder();
});

$(window).addEvent('focus', function(e){
	// Re-Enable feeder when we get focus
	feeder_enabled = true;
	if (feeder_timer==0) {
		feeder_timer=setTimeout(feeder, 1000);	
	}
});
$(window).addEvent('blur', function(e){
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
$(window).addEvent('keydown', function(e){
	e = new Event(e);	
	if (e.code == 27) {
		var r = $('grid_rect');
		if (r) {
			if (r.getStyle('display')!='none') {
				r.setStyles({'display':'none'});	
				rectinfo.url='';
			}
		}
		e.stop();		
	}
});

// #################### DEBUG #####################

function display(url) {
	gloryIO('index.php?'+url);
}

