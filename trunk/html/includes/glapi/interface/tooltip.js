var hoverInfo={text:'',x:0,y:0,sz:{x:0,y:0}};
function hoverShow(text,x,y,align) {
	var layer = $('hoverLayer');
	if (text) {
		
		// HoverInfo is broken in two phase-functions
		// This happens because there is no simple way to delay the thread
		// untill all the images from the main text are loaded.
		
		var p1 = function() {
			if (hoverInfo.text!=text) {
				
				// Set the text
				hoverInfo.text=text;				
				layer.set('html', text);
				
				// Check for imags that exists inside the DOM content
				var images = layer.getElements('img');
				var waitfor = 0;
				for (var i=0; i<images.length; i++) {
					
					// Is the image already completed? (Only a few bytes)
					if (!images[i].complete) {
						
						// Still loading? Count this image as "in-load" process
						waitfor++;
						images[i].addEvent('load', function(e) {
							// The image is loaded. Decrease the in-load image number
							waitfor--;
							
							// Are we through with loading images? Start phase 2
							if (waitfor==0) p2();
						});
					} else {
					}
				}
				
				// No images? Start phase 2
				if (waitfor==0) p2();
			}
		};
		var p2 = function() {
			
			// Get the dimensions of the object
			hoverInfo.sz = layer.getSize();
			layer.setStyles({visibility:'visible'});	
			
			// If we are actually moved since the last time, proceed...
			if (hoverInfo.x!=x || hoverInfo.y!=y) {
				hoverInfo.x=x; hoverInfo.y=y;
				
				// Calculate the offsets
				var left = x-(hoverInfo.sz.x/2);
				var top = y-hoverInfo.sz.y-12;
				
				// Calculate the vertical position
				if (top < 0) {
					top += hoverInfo.sz.y+24;
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
		};
		p1();
	} else {
		if (hoverInfo.text!='') {
			hoverInfo={text:'',x:0,y:0,sz:{x:0,y:0}};
			layer.setStyles({visibility:'hidden'});	
		}
	}
}
