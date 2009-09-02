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
					var siz = map_objects[id].object.getSize();
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

