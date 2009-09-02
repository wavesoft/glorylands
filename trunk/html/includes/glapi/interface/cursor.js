/**
  * Cursor management
  */
  
var cursors = [];

function cursor_preload() {
	var e=new Element('img', {src: 'images/UI/cursor/accept.png' });
	e.setStyles({
		'z-index': lastZ+65536,
		'position': 'absolute',
		'display': 'none'
	});
	e.inject($('datapane'));
	cursors.push(e);
}

function cursor_blink(id, x, y) {
	cursors[id].setStyles({
		'left': x,
		'top': y,
		'display': '',
		'opacity': 0
	});
	
	var fx=new Fx.Morph(cursors[id], {wait: false, duration: 400, transition: Fx.Transitions.Quad.easeIn});
	fx.start({
		'opacity':1
	}).chain(function() {
		fx.start({
			'opacity':0
		}).chain(function() {
			cursors[id].setStyle('display', 'none');
		});
	});
}
