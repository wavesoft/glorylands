
function inv_equip_reset() {
	for (var i=0; i<=10; i++) {
		$('inveq_'+i).emtpy();
	}
}

function inv_equip_add(slot, image, name, guid, tip) {
	if (slot<1) return;
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
	e.inject($('inveq_'+slot));	
}

callback.register('message', function(msg) {
	// ## Handle INVENTORY messages ##
	if (msg[0] == 'INVENTORY') {
		inv_equip_reset();
		$each(msg[1], function(e,slot) {
			if ($defined(e.image)) {
				inv_equip_add(slot, e.image, e.name, e.guid, e.tip);
			}
		});
		drag_update();
	}
});

$(window).addEvent('domready', function(e) {
	drag_update();
});