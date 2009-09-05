var drag_startcoord = {};
var drag_ables = [];
var drag_exclude = null;
var drag_element = null;
var drag_floating = null;

function drag_eventhandler_click(elm) {
	// Get some information about the element
	var count = elm.getProperty('count');
	if (!count) count=1;
	var ccoord = $(elm).getCoordinates();
	var guid = elm.getProperty('guid');
	var slot_element = elm.getParent();
	var slot=0;
	var container=0;
	if (slot_element) {
		if (slot_element.getProperty('slot')) {
			slot=slot_element.getProperty('slot');
			container=slot_element.getProperty('container');
		}
	}

	// Call the click event
	gloryIO('?a=dragdrop&mode=click&guid='+guid+'&slot='+slot+'&container='+container+'&count='+count, false, true, function(data) {
		if (data.mode == 'DRAG') {
			if (data.action == 'REPLACE') {
				srcelm.setProperties({
					'src': data.item.src,
					'title': data.item.name,
					'guid': data.item.guid
				});
				srcelm.setStyles({
					'width': 38,
					'height': 38,
					'padding': 1
				});
				if (!isInternetExplorer) {
					srcelm.setAttribute('class', 'drag_able '+data.item.guid);
				} else {
					srcelm.setAttribute('className', 'drag_able '+data.item.guid);
				}									
			} else {
				drag_callback(data.action, srcelm);
			}
		}					
	});
	
	// Dispose the floating draggable item (if any)
	if (drag_floating) {
		drag_floating.dispose();
		drag_floating=null;
	}

}

function drag_eventhandler_enter(elm, target) {
	if (target != drag_exclude) {
		target.setStyle('opacity', 0.5);
	}
}

function drag_eventhandler_leave(elm, target) {
	if (target != drag_exclude) {
		target.setStyle('opacity', 1);	
	}
}

function drag_eventhandler_drop(elm, target, e) {
	
	// If we have no target, we are empty dropped...
	if (!target || (target == drag_exclude)) {

		// Get some information about the element
		var count = elm.getProperty('count');
		if (!count) count=1;
		var ccoord = $(elm).getCoordinates();
		var guid = elm.getProperty('guid');
		var slot_element = elm.getParent();
		var slot=0;
		var container=0;
		if (slot_element) {
			if (slot_element.getProperty('slot')) {
				slot=slot_element.getProperty('slot');
				container=slot_element.getProperty('container');
			}
		}
	
		// Done with me
		elm.dispose();
		
		// Check if we were really dragged and not clicked (moved at least 5 px)
		if ((Math.abs(ccoord.left-drag_startcoord.left)>5) || (Math.abs(ccoord.top-drag_startcoord.top)>5)) {
			
			// Local copy to use within this namespace
			var srcelm = elm;						
									
			// Make the item shaded
			elm.setStyle('opacity',0.5);
		
			// Notify GUID removal
			gloryIO('?a=dragdrop&mode=remove&guid='+guid+'&slot='+slot+'&container='+container+'&count='+count, false, true, function(data) {
				if (data.mode == 'DRAG') {
					drag_callback(data.action, srcelm);
				}					
			});
		
		// If not, it means we were clicked
		} else {
			// Notify GUID click
			drag_eventhandler_click(elm);
		}
		
	// Elseways, we have a target, so process the dropping into the new target
	} else {
		
		target.setStyle('opacity', 1);
		
		// Get some container info
		var guid = elm.getProperty('guid');
		var srcelm = drag_element;
		var count = srcelm.getProperty('count');

		// Get information provided in the class
		var container = target.getProperty('container');
		var slot = target.getProperty('slot');
		var c_info = target.getProperty('class').split(' ');
		var c_slot = c_info[1];
		var c_container = c_info[2];
		if (!slot) slot = c_slot;
		if (!container) container = c_container;

		// Do not accept elements if I am already filled
		var children = target.getChildren();
		if (children.length>0) {
			
			// Remove floating
			elm.dispose();

			// Notify system that a user attempted to mix 2 objects
			var child = children[0];
			var childguid = children[0].getProperty('guid');
			if (childguid != guid) {
				
				// Notify system for a button change
				/* ### */
				var host = target;
				gloryIO('?a=dragdrop&mode=mix&guid='+guid+'&target='+childguid+'&slot='+slot+'&container='+container+'&count='+count, false, true, function(data) {
					if (data.mode == 'DRAG') {
						if (data.action == 'REPLACE') {
							srcelm.dispose();
							child.setProperties({
								'src': data.item.src,
								'title': data.item.name,
								'guid': data.item.guid
							});
							child.setStyles({
								'width': 38,
								'height': 38,
								'padding': 1									
							});
							if (!isInternetExplorer) {
								child.setAttribute('class', 'drag_able '+data.item.guid);
							} else {
								child.setAttribute('className', 'drag_able '+data.item.guid);
							}									
						} else if (data.action == 'CANCEL')  {									
						} else {
							drag_callback(data.action, srcelm, child);
						}
					}					
				});
			}
								
			return;	
		}
					
		// Create new draggable element on me
		var myobj = elm.clone();
		myobj.setStyles({
			'position':'',
			'width': 38,
			'height': 38,
			'padding': 1
		});
		myobj.inject(target);
						
		// Remove floater
		elm.dispose();
		
		// Refresh droppables/draggables
		drag_update();

		// Notify system for a button change
		/* ### */
		if (!count) count=1;
		var src_parent = srcelm.getParent();
		var src_slot = src_parent.getProperty('slot');
		if (!src_slot) src_slot=-1;
		var src_container = src_parent.getProperty('container');
		if (!src_container) src_container=-1;
		gloryIO('?a=dragdrop&mode=move&guid='+guid+'&slot='+slot+'&container='+container+'&fromslot='+src_slot+'&fromcontainer='+src_container+'&count='+count, false,true,  function(data) {
			if (data.mode == 'DRAG') {
				drag_callback(data.action, srcelm, myobj);
			}					
		});
		//setTimeout(drag_callback ,1000, 'MOVE',drag_element,myobj);

	}

	// The floating dragged has now completed it's purpose
	drag_floating=null;

}

function drag_callback(mode, src, dst) {
	try {
		if (mode=='MOVE') {
			if ($defined(src)) src.dispose();
			dst.setStyle('opacity',1);	
		} else if (mode=='COPY') {	
			src.setStyle('opacity',1);
			dst.setStyle('opacity',1);	
		} else if (mode=='CANCEL') {
			if ($defined(dst)) dst.dispose();
			src.setStyle('opacity',1);
		} else if (mode=='DELETE') {
			if ($defined(src)) src.dispose();
			if ($defined(dst)) dst.dispose();
		}	
	} catch(e) {
	}
}		

function drag_update() {
	
	// Find all draggables
	var drag_ables_new=[];
	$$('.drag_able').each(function(e) {
								   
		// Check if this item already exists
		var i = drag_ables.indexOf(e);
		if (i>=0) {
			drag_ables_new.push(e);
			return; // already exists
		}
		drag_ables_new.push(e);
		
		// Store some usefull info inside the element
		var info = e.getProperty('class').split(' ');
		var guid=info[1];
		if (guid) e.setProperty('guid',guid);
		var tip=e.getProperty('tip');		

		// If we have title, add the hover window
		if (tip) {
			e.setProperty('title','');
			e.addEvent('mousemove', function(e) {
				var e = new Event(e);
				var scrl = getScrollPosition();
				var tip = this.getProperty('tip');
				//window.alert(tip+', '+Number(e.client.x+scrl.x)+', '+Number(e.client.y+scrl.y));
				hoverShow(tip, e.client.x+scrl.x+10, e.client.y+scrl.y, 'left');
				e.stop();
			});
			e.addEvent('mouseout', function(e) {											
				hoverShow();
			});
		}
		
		// Add a dropdown menu
		e.addEvent('contextmenu', function(e) {
			var e = new Event(e);
			var scrl = getScrollPosition();
			var guid = this.getProperty('guid');
			var slot_element = this.getParent();
			var slot=0;
			var container=0;
			if (slot_element) {
				if (slot_element.getProperty('slot')) {
					slot=slot_element.getProperty('slot');
					container=slot_element.getProperty('container');
				}
			}
			piemenu_init(e.client.x+scrl.x, e.client.y+scrl.y, guid, slot, container);
			e.stop();
		});

		// Add a click event
		e.addEvent('click', function(e) {
			drag_eventhandler_click(this);
		});

		// Make the item draggable
		e.addEvent('mousedown', function(e) {
										 
			// Make sure we are clicked with left click
			e = new Event(e);
			if (e.rightClick) {
				e.stop();
				return;
			}			
			
			// Make sure we have no two or more simultanously dragging elements
			if (drag_floating) {
				drag_floating.dispose();
				drag_floating=null;
			}

			// Exlude our parent
			drag_exclude = $(this).getParent();
			drag_element = this;
			
			// Create clone
			drag_startcoord = $(this).getCoordinates();
			var clone = $(this).clone()
				.setStyles(drag_startcoord) // this returns an object with left/top/bottom/right, so its perfect
				.setStyles({'opacity': 0.7, 'position': 'absolute', 'z-index': lastZ})
				.inject(document.body);
			
			var drag = new Drag.Move(clone, {
				droppables: '.drag_host',
				snap: 6,				
				onDrop: drag_eventhandler_drop,
				onEnter: drag_eventhandler_enter,
				onLeave: drag_eventhandler_leave,

			}); // this returns the dragged element
	 		
			// Store the floating clone
			drag_floating = clone;
			
			e.stop();
			drag.start(e); // start the event manually
			
		});
		
	});
	drag_ables=drag_ables_new;
}

var qb_hosts = [];
var qb_items = [];
var qb_itemdesc = [];

function qb_items_build() {
	var host = $('qbar_host');
	for (var i=1; i<=21; i++) {
		var e = new Element('div');
		if (!isInternetExplorer) {
			e.setAttribute('class', 'drag_host '+i+' 0');
		} else {
			e.setAttribute('className', 'drag_host '+i+' 0');
		}
		e.inject(host);
		qb_hosts[i] = e;
		qb_items[i]=false;
		qb_itemdesc[i]=false;
	}
	drag_update();
}

function qb_items_reset() {
	for (var i=1; i<=21; i++) {
	  try {
		if (qb_items[i]) {
			qb_items[i].dispose();
		}
	  } catch(e) {
	  }
	  qb_items[i]=false;
	}
}

function qb_spawn_item(slot, image, name, guid, tip) {
	if (slot<1) return;
	var host = qb_hosts[slot];
	var e = new Element('img', {
		'src':image,
		'title': name,
		'width': 38,
		'height': 38,
		'padding': 1
	});

	if (!isInternetExplorer) {
		e.setAttribute('class', 'drag_able '+guid);
	} else {
		e.setAttribute('className', 'drag_able '+guid);
	}
	
	if ($defined(tip)) {
		e.setProperty('tip',tip);
	}
	
	// Store the new item
	qb_items[slot]=e;	
	e.inject(host);
}


$(window).addEvent('domready', function() {
	qb_items_build();
	drag_update();
});

callback.register('message', function(msg) {
	// ## Handle QBAR messages ##
	if (msg[0] == 'QBAR') {
		try {
			qb_items_reset();
			$each(msg[1], function(e,slot) {
				if ($defined(e.image)) {
					qb_spawn_item(slot, e.image, e.name, e.guid, e.tip);
				}
			});
			drag_update();
		} catch (e) {
			window.alert('QBInit error: '+e);	
		}
	}
});

callback.register('iocomplete', function() {
	// After each message, check and update droppabkes
	drag_update();
});