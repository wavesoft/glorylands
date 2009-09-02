var pie_stack=[];
var pie_info=[];
var pie_wait_icon=null;
var pie_menutext=null;
var pie_visible=false;

function piemenu_dispose() {
	if (pie_stack.length>=0) {
		for (var i=0; i<pie_stack.length; i++) {
			var dispose_ani=new Fx.Morph(pie_stack[i], {duration: 400, transition: Fx.Transitions.Back.easeIn,
				onComplete: function() {
					this.element.dispose();
				}
			});
			dispose_ani.start({
				'opacity': 0,
				'left': pie_info.x-17,
				'top': pie_info.y-17
			});
		}
		pie_stack=[];
	}
	pie_info=[];
	
	// Dispose old menu text
	if (pie_menutext) {
		var dispose_ani=new Fx.Morph(pie_menutext, {duration: 400, transition: Fx.Transitions.Back.easeIn,
			onComplete: function() {
				this.element.dispose();
			}
		});
		dispose_ani.start({
			'opacity': 0
		});
		pie_menutext=null;
	}
	
	// Just in case we are still waiting for response...
	piemenu_waitdispose();
	pie_visible=false;
}

function piemenu_spawnicon(x,y,icon,tip,url) {
	try {
	var host = $(document.createElement('div'));
	host.setStyles({
		'width': '34px',
		'height': '34px',
		'position': 'absolute',
		'background-repeat': 'no-repeat',
		'background-image': 'url(images/UI/icon-bg.png)',
		'text-align': 'center',
		'left': (pie_info.x-17)+'px',
		'top': (pie_info.y-17)+'px',
		'opacity': 0,
		'z-index': lastZ++
	});

	var host_ani=new Fx.Morph(host, {duration: 400, transition: Fx.Transitions.Back.easeOut});

	var btn_link = $(document.createElement('a'));
	btn_link.setProperty('href','javascript:;');
	btn_link.addEvent('click', function(e) {
		e = new Event(e);
		piemenu_dispose();
		gloryIO(url);
		e.stop();
	});
	btn_link.alt = tip;
	btn_link.title = tip;
	
	var btn_image = $(document.createElement('img'));
	btn_image.src = icon;
	btn_image.border = '0';

	btn_link.appendChild(btn_image);
	host.appendChild(btn_link);
	
	pie_stack.push(host);
	$(document.body).appendChild(host);
	
	// Play the effect
	host_ani.start({
		'opacity': 1,
		'left': x-17+'px',
		'top': y-17+'px'
	});

	
	}	catch (ex) {}
}

function piemenu_show(menu,text) {
	pie_visible=true;
	
	try {
	// Dispose waiting menu
	piemenu_waitdispose();
	hoverShow();

	// Prepare info
	if (pie_info.length==0) return;
	var distance = 5+(menu.length*5);

	// Show text (if text exists)
	if (text!='') {
		
		// Dispose old menu text
		if (pie_menutext) {
			pie_menutext.dispose();
			pie_menutext=null;
		}
		
		// Create the element
		pie_menutext = $(document.createElement('div'));
		pie_menutext.setStyles({
			'position': 'absolute',
			'background-color': '#000000',
			'boder-width': '2px',
			'border-style': 'solid',
			'border-color': '#CCCCCC',
			'font-size': '10px',
			'padding' : '2px',
			'opacity' : 0,
			'text-align': 'center',
			'z-index': lastZ++
		});
		var menutext_ani=new Fx.Morph(pie_menutext, {duration: 400, transition: Fx.Transitions.Back.easeIn});

		// If we have a menu, restrain text size
		if (menu.length>0) {
			pie_menutext.setStyles({
				'width': ((distance*2)+60)+'px'
			});
		}
		
		// Import the new item into the body
		pie_menutext.set('html', text);
		document.body.appendChild(pie_menutext);		
		
		// Move it to the center
		var szinfo = pie_menutext.getSize();
		pie_menutext.setStyles({
			'left': (pie_info.x-(szinfo.x/2)),
			'top': (pie_info.y+distance+15)
		});		

		// Play the effect
		menutext_ani.start({
			'opacity': 1
		});
	}

	// Show Pie
	if (menu.length>0) {
		var step = (2*Math.PI)/menu.length;
		var radius = 0;
		for (var i=0; i<menu.length; i++) {
			var item_x = pie_info.x + (distance * Math.cos(radius));
			var item_y = pie_info.y + (distance * Math.sin(radius));
			piemenu_spawnicon(item_x,item_y,menu[i][0],menu[i][1],menu[i][2]);
			radius += step;
		}
	}

	} catch (ex) {}
}

function piemenu_waitdispose() {
	try {
		if (pie_wait_icon) {
			pie_wait_icon.dispose();
			pie_wait_icon=null;
		}
	} catch (e) {}
}

function piemenu_wait() {
	piemenu_waitdispose();
	pie_wait_icon = $(document.createElement('img'));
	pie_wait_icon.src = 'images/UI/loading-big.gif';
	$(document.body).appendChild(pie_wait_icon);
	var szinfo = pie_wait_icon.getSize();
	pie_wait_icon.setStyles({
		'left': (pie_info.x-(szinfo.x/2))+'px',
		'top': (pie_info.y-(szinfo.y/2))+'px',
		'position': 'absolute'
	});
}

function piemenu_init(x,y,guid,position,parent_guid) {
	pie_info = {
		'x': x,
		'y': y
	};
	gloryIO('?a=interface.dropdown&guid='+guid+'&pos='+position+'&parent='+parent_guid, false, true);
	piemenu_wait();
}
