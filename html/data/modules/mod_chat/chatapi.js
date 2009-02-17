// Create elements
var eChatFloat = false; 
var eChatText = false;

$(window).addEvent('load', function(e) {

	try {
	// Create some chat required elements
	eChatFloat = $(document.createElement('div'));
	eChatFloat.setStyles({'position':'absolute', 'visibility':'hidden'});
	eChatFloat.setHTML('\
		<table class="modchat_popup" cellspacing="0" cellpadding="0">\
		<tr>\
			<td class="tl" width="17">&nbsp;</td>\
			<td class="t" colspan="2">&nbsp;</td>\
			<td class="tr" width="17">&nbsp;</td>\
		</tr>\
		<tr>\
			<td class="l">&nbsp;</td>\
			<td class="m" colspan="2" align="center">\
			<span id="modchat_floattext"></span>\
			</td>\
			<td class="r">&nbsp;</td>\
		</tr>\
		<tr>\
			<td class="bl" width="17">&nbsp;</td>\
			<td class="b" width="45"><div class="arrow">&nbsp;</div></td>\
			<td class="b">&nbsp;</td>\
			<td class="br  width="17"">&nbsp;</td>\
		</tr>\
		</table>\
   ');
	document.body.appendChild(eChatFloat);
	
	$$('#chat_input').each(function(e) {

	});
	
	// Obdain the float element from the text above
	eChatText = $('modchat_floattext');
	
	} catch (e) {
		window.alert('ChatAPI Init Error: '+e.message);
	}
	
});

function find_overlay_info(objectname) {
	var result = false;
	$each(map_objects, function(obj) {
		if ($defined(obj.info.name)) {
			if (obj.info.name == objectname) {				
				// Get DataPane left offset
				var dpX = $('datapane').getLeft();
				var dpY = $('datapane').getTop();
				var objH = obj.object.getSize().size.y/2;
				
				result = {'x':obj.x+dpX-glob_x_base, 'y':obj.y+dpY-glob_y_base-objH, 'guid':obj.info.guid};	
			}
		}
	});
	return result;
}


function find_overlay_info_depreciated_and_will_be_deleted(objectname) {	
	// Traverse into overlay grid to fild an object with the name defined
	var elog=''; var found_obj=false;
	$each(nav_grid, function(y_obj, x) {				
		elog+='Row x:'+x+"\n";
		if ($defined(y_obj)) $each(y_obj, function(zid, y) {							
			if ($defined(zid)) {
				var dic_entry = nav_grid['dic'][zid];
				if ($defined(dic_entry)) {
					if (dic_entry.d.name == objectname) {
						if (!found_obj) {
							// Get DataPane left offset
							var dpX = $('datapane').getLeft();
							var dpY = $('datapane').getTop();
							
							// Return the actual position
							found_obj = {'x':(x-glob_x_base)*32+dpX+24, 'y':(y-glob_y_base-1)*32+dpY, 'guid': dic_entry.g};
						}
						return;
					}
				}
			}
		}); /* each */
	}); /* each */
	return found_obj;
}

var activeTimer = 0;
function displayBubble(user, text) {
	var hideBubble = false;
	var fadeFX=new Fx.Styles(eChatFloat, {duration: 400, transition: Fx.Transitions.Back.easeIn,
		onComplete: function() {
			if (hideBubble) {
				eChatFloat.setStyles({lvisibility:'hidden'});
				hideBubble=false;
			}
		}
	});
	var gps = find_overlay_info(user);
	if (gps == false) {
	} else {
		try {
			eChatText.setHTML('<span class="user">['+user+']</span> '+text);
			eChatFloat.setStyles({'left': gps.x, 'top': gps.y, 'visibility':'visible', 'opacity':0, 'z-index': lastZ++});
			hideBubble=false;
			fadeFX.stop();
			fadeFX.start({'opacity': 1});
			
			if (activeTimer!=0) clearTimeout(activeTimer);
			activeTimer = setTimeout(function() {
				hideBubble=true;
				fadeFX.start({'opacity': 0});
			}, 3000);
		} catch (e) {
			window.alert('DisplayBuffle Error'+e);
		}
	}
}

callback.register('message', function(msg) {
	// ## Handle CHAT messages ##
	if (msg[0] == 'CHAT') {
		$$('#chat_content').each(function(e) {
			var e = $(e);
			var HTML = e.innerHTML;			
			if (msg[2].toLowerCase() == 'system') {
				e.setHTML(HTML+'<p><font color="gold">'+msg[1]+'</font></p>');
			} else {
				e.setHTML(HTML+'<p><strong>['+msg[2]+']</strong>'+': '+msg[1]+'</p>');
			}
			
			displayBubble(msg[2],msg[1]);
			
			e.scrollTop = e.scrollHeight;	// FF
			setTimeout(function() { e.scrollTop = e.scrollHeight; }, 10); // IE
		});
	}
});

function chat_send(text) {
	gloryIO('?a=chat.send&text='+text, false, true);
}
