// ======================================================
//  This function executes the basic game I/O operations
//  through a JSON communication interface
// ======================================================
var data_io_time = 0;
function gloryIO(url, data, silent, oncomplete_callback) {	
	$debug('[I/O] <a href="'+url+'">'+url+'</a>');
	try {
		// Check if we are running in library mode
		if (!$defined($('datapane'))) {
			library_mode = true;
			
			// Append library mode definition on URL
			if (url.indexOf('?')>=0) {
				url+='&lib=1';
			} else {
				url+='?lib=1';
			}
		} else {
			library_mode = false;
			// Start feeder timer, if not in library mode
			reset_feeder();
		}		
	
		if (!silent) showStatus('Loading...&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/UI/mouseloading.gif" />');
		//window.alert(url);
		//$debug('&lt&lt; '+url);
		data_io_time = $time();
		var json = new Request.JSON({'url': url,
			onSuccess: function(obj) {
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
	
				// Check if we are being included only as library
				// In that case, do not handle actions that cannot be processed
				if (!library_mode) {
					// ## HTML Data for main window ##
					if (mode=='MAIN') {
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
					}
				}
				
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
										
				// ## Placeholder for information mode ##
				} else if (mode=='INFO') {
					
				// ## Placeholder for full-page mode ##
				} else if (mode=='FULL') {
					
				} else if (mode=='BLANK') {
					
				// ## NOTHING handler ##
				} else if (mode=='NONE') {					

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
					if ($defined(obj.number)) {						
						// ID: 001 = Session Lost
						if (obj.number == 101) {
							alert('Game session is lost! You are logged out from the game.');
							window.location='index.php?a=interface.entry';
						}
					} else {
						if (!silent) showStatus('<font color=\"red\">'+obj.error+'</font>', 5000);
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
		}).get(data);
	}
	catch (e) {
		window.alert('GloryIO Error: '+e.message);
		if (!silent) showStatus('<font color=\"red\">Data Error!</font>', 1000);
	}

}
