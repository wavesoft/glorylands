// JavaScript Document

var tiles = [];
var images = [];
var paint_image = '';
var painting = false;
var paint_x=0, paint_y=0;
var last_x=0, last_y=0;

function carry_image(src) {
	$('pointer').src = src;
	paint_image = src;
}

function paint(x,y) {
	var im = $(document.createElement('img'));
	$('content').appendChild(im);	
	im.src=paint_image;
	im.setStyles({
		'left': x*32,
		'top': y*32
	});
	images.push(im);
}

function spawn_image(src) {
	var im = $(document.createElement('img'));
	$('tiles_host').appendChild(im);	
	im.src=src;
	im.addEvent('click', function(e){
		carry_image(src);
	});
	tiles.push(im);
}

$(window).addEvent('load', function(e){

	var data = new Json.Remote('feed.php', {
			onComplete: function(o) {
				$each(o, function(e) {
					spawn_image(e);				  
				});
			},
			onFailure: function(e) {
				window.alert(e.message);
			}
	}).send();

	$('content_host').addEvent('mousemove', function(e){
		var e = new Event(e);
	
		var hsX = $('content_host').getLeft();
		var hsY = $('content_host').getTop();
		var dpX = $('content').getLeft()-hsX;
		var dpY = $('content').getTop()-hsY;
		var x = e.event.clientX-hsX+dpY;
		var y = e.event.clientY-hsY+dpX;
		x=Math.floor(x/32);
		y=Math.floor(y/32);
		paint_x=x;
		paint_y=y;
		
		$('pointer').setStyles({
			'left': x*32,
			'top': y*32
		});
		
		if (painting) {
			if ((last_x!=x) || (last_y!=y)) {
				paint(x,y);
				last_x=x;
				last_y=y;
			}
		}
		e.stop();
	});
	
	$('content_host').addEvent('mousedown', function(e){
		var e = new Event(e);
		painting=true;
		last_x=paint_x;
		last_y=paint_y;
		paint(paint_x,paint_y);
		e.stop();
	})
	
	$('content_host').addEvent('mouseup', function(e){
		var e = new Event(e);
		painting=false;
		e.stop();
	})

});

