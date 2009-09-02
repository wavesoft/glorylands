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

			// Check if we are included as library
			// If we are included as library, skip the actions that
			// cannot be processed
			if ($defined($('datapane'))) {
				
				// ## The grid is altered. Perform an update ##
				if (mType=='UPDATEGRID') {
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
					
					//$debug('Altering:'+"\n"+$trace(msg.message[i][1]));
					if ($defined(msg.message[i][1].guid)) {
						var uid = map_objectid_fromguid(msg.message[i][1].guid);
						if (uid == 0) continue;
						map_updateobject(uid, msg.message[i][1]);
					}
					
				// ## Animate a map object ##
				} else if (mType=='ANIMATE') {
	
					var uid = map_objectid_fromguid(msg.message[i][1]);
					if (uid == 0) continue;
					var animation = [];
					var frame_rate = 25;
					var loops = 1;
					if ($defined(msg.message[i][2])) animation=msg.message[i][2];
					if ($defined(msg.message[i][3])) frame_rate=msg.message[i][3];
					if ($defined(msg.message[i][4])) loops=msg.message[i][4];
					
					var id=map_object_index.indexOf(uid);
					var object = map_objects[id].object;
					if (!object) continue;
					
					if (animation == []) {
						fx_sprite_stop(object);
					} else {
						fx_sprite_animate(object, frame_rate, animation, loops);
					}
	
				// ## Show action grid ##
				} else if (mType=='ACTIONGRID') {
	
					if ($defined(msg.message[i][1])) {
						wgrid_design(msg.message[i][1],true);
						wgrid_show();
					}
	
				// ## Set feeder interval ##
				} else if (mType=='POLLINTERVAL') {
					
					feeder_interval = msg.message[i][1];
				}
			}
			
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

			// ## Unknown message arrived ##
			} else {
				// Process unknown messages to later-included scripts
				callback.call('message',msg.message[i]);
			}
		}
	}
}
