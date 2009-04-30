// Fight management //

var blink_element = null;
var blink_timer = 0;
var blink_state = false;
var blink_fx = null;

var fight_players=[];
var fight_fx=[];

function battle_fx_preload() {	
	var images = [
		'images/UI/fx/crystal-01.png', 'images/UI/fx/fire-01.png', 'images/UI/fx/lightning-01.png',
		'images/UI/fx/shadow.png', 'images/UI/fx/snap-01.png', 'images/UI/fx/water-01.png'
	];
	
	// Precache all map images
	new Asset.images(images, {
	});	
}

function battle_fx_apply(element, fx) {
	var pos = $(element).getStyles('left','top');
	pos.left = Number(pos.left.replace('px',''));
	pos.top = Number(pos.top.replace('px',''));
	var size = $(element).getSize().size;
	var host = $$('.fight_frame')[0];
	var direction = $(element).getProperty('fx_direction');
	
	var fx_nudge = function(nudge_direction) {
		var fx1=new Fx.Styles(element, {duration: 100, transition: Fx.Transitions.Cubic.easeIn});
		var fx2=new Fx.Styles(element, {duration: 100, transition: Fx.Transitions.Cubic.easeOut});
		fx1.start({
			'left': pos.left+10*direction*nudge_direction
		}).chain(function() {
			fx2.start(pos);
		});		
	}

	var fx_image = function(image, duration) {
		var e=new Element('img', {
			'src': 'images/fx/'+image,
			'width': size.x,
			'height': size.y
		});
		e.inject(host);
		var fx=new Fx.Styles(e, {'duration': duration, transition: Fx.Transitions.linear});
		e.setStyles({
			'left': pos.left,
			'top': pos.top,
			'position': 'absolute'
		});
		fx.start({
			'opacity': 0
		}).chain(function(){
			e.remove();
		});
	}

	var fx_image_zoom = function(image, duration) {
		var e=new Element('img', {
			'src': 'images/fx/'+image,
			'width': size.x,
			'height': size.y
		});
		e.inject(host);
		var fx=new Fx.Styles(e, {'duration': duration/2, transition: Fx.Transitions.linear});
		e.setStyles({
			'left': pos.left+(size.x)/2+5,
			'top': pos.top+(size.y)/2+5,
			'width': 10,
			'height': 10,
			'opacity': 0,
			'position': 'absolute'
		});
		fx.start({
			'opacity': 1,
			'left': pos.left,
			'top': pos.top,
			'width': size.x,
			'height': size.y
		}).chain(function(){
			fx.start({
				'opacity': 0
			}).chain(function() {
				e.remove();
			});			
		});
	}
	
	var fx_image_scroll = function(image, duration) {
		var e=new Element('img', {
			'src': 'images/fx/'+image,
			'width': size.x,
			'height': size.y
		});
		var d=new Element('div');
		e.inject(d);
		d.inject(host);
		
		var phase_duration = duration/3;
		var fx=new Fx.Styles(d, {'duration': phase_duration, transition: Fx.Transitions.linear});
		var fxe=new Fx.Styles(e, {'duration': phase_duration, transition: Fx.Transitions.linear});
		d.setStyles({
			'left': pos.left,
			'top': pos.top+size.y-1,
			'position': 'absolute',
			'width': size.x,
			'height': 1,
			'overflow': 'hidden'
		});
		e.setStyles({
			'margin-top': size.y
		});
		fxe.start({
			'margin-top': 0
		});
		fx.start({
			'height': size.y,
			'top': pos.top
		}).chain(function(){
			fx.start({
				'height': 1
			}).chain(function(){
				e.remove();
				d.remove();
			});
		});
	}

	var fx_image_tremble = function(image, duration) {				
		var e=new Element('img', {
			'src': 'images/fx/'+image,
			'width': size.x,
			'height': size.y,
			'zoomstate': 0
		});
		e.inject(host);
		
		var zoom_fx = function(elm) {
			if (!elm) elm = this.element;
			var dim = elm.getStyles('left','top');
			dim.left = Number(dim.left.replace('px',''));
			dim.top = Number(dim.top.replace('px',''));
			var siz = elm.getSize().size;
			var state = elm.getProperty('zoomstate');
			if (state == 0) {
				zfx.start({
					'width': siz.x-10,
					'height': siz.y-10,
					'left': dim.left+5,
					'top': dim.top+10
				});
				elm.setProperty('zoomstate', 1);
			} else {
				zfx.start({
					'width': siz.x+10,
					'height': siz.y+10,
					'left': dim.left-5,
					'top': dim.top-10
				});
				elm.setProperty('zoomstate', 0);
			}
		}
		
		var fx=new Fx.Styles(e, {'duration': duration, transition: Fx.Transitions.linear});
		var zfx=new Fx.Styles(e, {'duration': 50, transition: Fx.Transitions.linear, onComplete: zoom_fx});

		e.setStyles({
			'left': pos.left,
			'top': pos.top,
			'position': 'absolute'
		});

		zoom_fx(e);
		fx.start({
			'opacity': 0
		}).chain(function(){
			e.remove();
		});
	}
	
	switch(fx) {	
		case 'attack':  fx_nudge(1);
						break;

		case 'snap':	fx_image('snap-01.png',100);
						fx_nudge(-1);
						break;

		case 'fire':	fx_image_tremble('fire-01.png',500);
						break;

		case 'water':	fx_image_scroll('water-01.png',500);
						fx_nudge(-1);
						break;

		case 'thunder':	fx_image_tremble('lightning-01.png',500);
						break;

		case 'show':	fx_image_zoom('crystal-01.png',500);
						break;


	}
}

function blink_start(element) {
	blink_element = element;
	blink_fx=new Fx.Styles(element, {duration: 200, transition: Fx.Transitions.linear, 
		onComplete: function() {
			if (blink_element != null) {
				if (!blink_state) {
					blink_fx.start({'opacity': 1});
					blink_state=true;
				} else {
					blink_fx.start({'opacity': 0.5});
					blink_state=false;
				}
			}
		}
	});
	blink_fx.start({'opacity': 0.5});
}

function blink_stop() {
	if (blink_element == null) return;
	blink_element.setStyle('opacity', 1);
	blink_fx.stop();
	clearInterval(blink_timer);
	blink_element = null;
	blink_timer = 0;
	blink_state = false;
	blink_fx = null;	
}

function battle_trigger() {
	var fx_chain=[];
	var fx_step = function() {
		var e=fx_chain.shift(); if ($chk(e)) e.start({ 
			'opacity': 1,
			'left': e.element.getProperty('fx_left')
		});
	}
	var pos_top = 0;
	var pos_left = 0;
	var pos_direction = 0;
	var register = function(el) {
		fight_players.push(el);
		el.addEvent('click', function(e) {
			var e = new Event(e);
			battle_fx_apply(el,'attack');
			var fxe = ['fire','water','thunder','snow'];
			battle_fx_apply(fight_players[Math.floor(Math.random()*6)],fxe[Math.floor(Math.random()*fxe.length)]);
			e.stop();
		});
		el.addEvent('contextmenu', function(e) {
			var e = new Event(e);
			battle_fx_apply(el,'attack');
			battle_fx_apply(fight_players[Math.floor(Math.random()*6)],'snap');
			e.stop();
		});
		var siz = el.getSize().size;
		el.setStyles({
			'cursor': 'pointer',
			'opacity': 0,
			'left': pos_left+pos_direction*400,
			'top': pos_top-siz.y
		});
		el.setProperties({
			'fx_direction': pos_direction,
			'fx_left': pos_left
		});
		pos_left += pos_direction*(siz.x+5);
		var efx = new  Fx.Styles(el, {duration: 200, transition: Fx.Transitions.linear, onComplete: fx_step});
		fx_chain.push(efx);
	}

	pos_top = 260;
	pos_left = 650;
	pos_direction = -1;
	$$('.fight_frame .near img.member').each(register);

	pos_top = 190;
	pos_left = 16;
	pos_direction = 1;
	$$('.fight_frame .far img.member').each(register);
	setTimeout(fx_step, 500);
}

callback.register('iocomplete', function(cmd) {
	if (cmd == 'fight.main') {
		// The iocomplete callback, after a 'MAIN' event, is called right
		// before the curtain animation. This means that the DOM structure
		// of the MAIN container is not yet built. It will be built
		// about 500ms after the call (When the curtain FX is completed)		
		setTimeout(function() {
			battle_trigger();
		}, 600);
	}
});

callback.register('message', function(msg) {
	// ## Handle FIGHT_ANIMATE messages ##
	if (msg[0] == 'FIGHT_ANIMATE') {
		
		var vAtkGuid = msg[1];
		var vAtkFX = msg[2];
		var vDefGuid = msg[3];
		var vDefFx = msg[4];		
		
		var vAtkElm = null;
		var vDefElm = null;

		// Locate the targeted element
		for (var i=0; i<fight_players.length; i++) {
			if ($chk(fight_players[i])) {
				var guid = fight_players[i].getProperty('guid');
				if (guid == vAtkGuid) {
					vAtkElm = fight_players[i];
				} else if (guid == vDefGuid) {
					vDefElm = fight_players[i];
				}
			}
		}
		
		// Perform the animation
		if (vAtkElm!=null) battle_fx_apply(vAtkElm, vAtkFX);
		if (vDefElm!=null) battle_fx_apply(vDefElm, vDefFx);

	// ## Handle FIGHT_ANIMATE messages ##
	} else if (msg[0] == 'FIGHT_') {
		
		var vAtkGuid = msg[1];
		var vAtkFX = msg[2];
		var vDefGuid = msg[3];
		var vDefFx = msg[4];		
		
		var vAtkElm = null;
		var vDefElm = null;

		// Locate the targeted element
		for (var i=0; i<fight_players.length; i++) {
			if ($chk(fight_players[i])) {
				var guid = fight_players[i].getProperty('guid');
				if (guid == vAtkGuid) {
					vAtkElm = fight_players[i];
				} else if (guid == vDefGuid) {
					vDefElm = fight_players[i];
				}
			}
		}
		
		// Perform the animation
		if (vAtkElm!=null) battle_fx_apply(vAtkElm, vAtkFX);
		if (vDefElm!=null) battle_fx_apply(vDefElm, vDefFx);

	// ## Handle FIGHT_LOG messages ##
	} else if (msg[0] == 'FIGHT_LOG') {
		
		var vLog = $('sidebar_icon');
		var vFrom = msg[1];
		var vText = msg[2];

		// Write the log
		
	// ## Handle FIGHT_SYNC messages ##
	} else if (msg[0] == 'FIGHT_SYNC') {
		
		var vTimeout = msg[1];

		// Write the log

	}
});

// Preload battle FX as long as we are loaded
battle_fx_preload();