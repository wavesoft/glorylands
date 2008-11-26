// The currently dragging host element
var qb_currently_dragging=null;
// The list of drop destinations
var droppables = [];

// Function to make an object moevable from the quick bar
function qb_makeqbutton(element, guid, slot) {
	
	// Add context menu and required parameters
	element.addEvent('contextmenu',function(e) {
		var e = new Event(e);
		// Get Scroll position
		var scrl = getScrollPosition();

		piemenu_dispose();
		piemenu_init(e.event.clientX+scrl.x,e.event.clientY+scrl.y,guid,'QUICKBAR',slot)
		e.stop();
	});
	element.setProperty('guid', guid);
	element.setProperty('slot', slot);
	
	// Make this element moeavable
	qb_makedraggable(element, guid, true);
}

function qb_init_droppables() {
	$$('#quickbar div').each(function(drop, index){
		drop.removeEvents();
		drop.addEvents({
			'over': function(el, obj){
				this.setStyle('opacity', 0.5);
			},
			'leave': function(el, obj){
				this.setStyle('opacity', 1);
			},
			'drop': function(el, obj){
				this.setStyle('opacity', 1);
				var guid = el.getProperty('guid');
				var host_guid = el.getProperty('host');
				var host_view = el.getProperty('hostview');
				var slot = drop.getProperty('slot');
				
				// Do not accept elements if I am already filled
				var children = drop.getChildren();
				if (children.length>0) {
					
					// Remove floating
					el.remove();

					// Notify system that a user attempted to mix 2 objects
					var childguid = children[0].getProperty('guid');
					if (childguid != guid) {
						// Notify system for a button change
						gloryIO('?a=quickbar.mix&guid1='+guid+'&guid2='+childguid+'&slot='+slot+'&host='+host_guid+'&view='+host_view);
					}
										
					return;	
				}
							
				// Create new draggable element
				var myobj = new Element('img', {
								'src': el.src								
							});
				
				// Make this element moeavable
				qb_makeqbutton(myobj, guid, slot);
				myobj.inject(drop);
				
				// Store some usefull information on the hosted object
				myobj.setProperty('slot', slot);
				
				// Remove floater and floater host
				el.remove();
				if (qb_currently_dragging) {
					qb_currently_dragging.remove();
					qb_currently_dragging=null;
				}

				// Notify system for a button change
				gloryIO('?a=quickbar.move&guid='+guid+'&slot='+slot+'&host='+host_guid+'&view='+host_view);
			}
		});
	});	
}

// Function to initialize droppable objects
$(window).addEvent('load', function(e) {
	qb_init_droppables();
});

// Function to make an object draggable to the sidebar
// Element	: The HTML DOM element to make draggable
// Guid		: The GUID this item provides
// Moevable : If TRUE the item will be moved instead of cloned
// Host_guid: The GUID of the hosting object (Ex. a container)
// Host_view: The type of the visual representation of the hosting object (Ex. as GUID info, as a container etc...)
function qb_makedraggable(element, guid, moveable, host_guid, host_view) {
	var item = $(element);
	
	if (!moveable) {
		item.addEvent('mousedown', function(e) {
			qb_currently_dragging=null;
			if (e.button!=0) {
				e = new Event(e).stop();
				return;
			}
			e = new Event(e).stop();
			
			var clone = this.clone()
				.setStyles(this.getCoordinates()) // this returns an object with left/top/bottom/right, so its perfect
				.setStyles({'opacity': 0.7, 'position': 'absolute', 'z-index': 500000})
				.setProperty('guid', guid)
				.setProperty('host', host_guid)
				.setProperty('hostview', host_view)
				.addEvent('emptydrop', function() {
					this.remove();
				}).inject(document.body);
	  
			var drag = clone.makeDraggable({
				droppables: $$('#quickbar div')
			}); // this returns the dragged element
	 
			drag.start(e); // start the event manual
		});
	} else {
		item.addEvent('mousedown', function(e) {
			qb_currently_dragging=null;
			if (e.button!=0) {
				e = new Event(e).stop();
				return;
			}
			e = new Event(e);
			
			// Make this object the dragger host
			qb_currently_dragging = this;
			
			// Create clone
			var startcoord = this.getCoordinates();
			var clone = this.clone()
				.setStyles(this.getCoordinates()) // this returns an object with left/top/bottom/right, so its perfect
				.setStyles({'opacity': 0.7, 'position': 'absolute', 'z-index': 500000})
				.setProperty('guid', guid)
				.setProperty('host', host_guid)
				.setProperty('hostview', host_view)
				.addEvent('emptydrop', function() {	
					var ccoord = this.getCoordinates();
					this.remove();
					// Check if we were really dragged and not clicked (moved at least 5 px)
					if ((Math.abs(ccoord.left-startcoord.left)>5) || (Math.abs(ccoord.top-startcoord.top)>5)) {
						qb_currently_dragging.remove();
						qb_currently_dragging=null;
						gloryIO('?a=quickbar.remove&guid='+guid+'&slot='+this.getProperty('slot')+'&host='+host_guid+'&view='+host_view);
					}
				})
				.addEvent('mouseup', function() {	
					var ccoord = this.getCoordinates();
					if ((Math.abs(ccoord.left-startcoord.left)<=5) && (Math.abs(ccoord.top-startcoord.top)<=5)) {
						gloryIO('?a=quickbar.use&guid='+guid+'&slot='+this.getProperty('slot')+'&host='+host_guid+'&view='+host_view);
					}
				})
				.inject(document.body);
			startcoord = this.getCoordinates();
	  
			var drag = clone.makeDraggable({
				droppables: $$('#quickbar div')
			}); // this returns the dragged element
	 
			drag.start(e); // start the event manual
			
		});
	}
}

callback.register('message', function(msg) {
	// ## Handle QBAR messages ##
	if (msg[0] == 'QBAR') {
		$$('#chat_content').each(function(e) {
			$('quickbar_data').setHTML(msg[1]);
			qb_init_droppables();
		});
	}
});
