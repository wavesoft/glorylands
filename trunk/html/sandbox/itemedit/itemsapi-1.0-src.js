
var edit_attrib_id=0;
var edit_attribs=[];
var edit_elements=[];
var edit_attrib_grid={
	'Physical State': [
		['Liquid', false],
		['-- Goo', false],
		['Solid', false],
		['-- Sand / Dust', false],
		['-- Sharp', false],
		['-- Rough', false],
		['-- Unshaped', false],
		['Gas', false]		
	],
	'Classification': [
		['Armor', false],
		['Weapon', false],
		['Consumable', false],
		['Container', false],
		['Gem', false],
		['Reagent', false],
		['Projectile', false],
		['Goods', false],
		['Recepie', false],
		['Key', false],
		['Usable', false]
	],
	'Equipability': [
		['Head', false],
		['Hands', false],
		['Feet', false],
		['Shoulder', false],
		['Body', false],
		['Cloak',false],
		['Shoes',false],
		['Fingers',false],
		['Neck',false],
		['Ears',false]
	],
	'Battle Damage': [
		['Air', true],
		['Fire', true],
		['Water', true],
		['Ice', true],
		['Spirit', true],
		['Death', true],
		['Life', true],
		['Earth', true],
		['Moon', true],
		['Sun', true],
		['Machine', true],
		['Nature', true],
		['Lightning', true],
		['Physical', true]
	],
	'Variable Modifier': [
		['...', true]
	],
	'Effect Timeout': [
		['All', true],
		['All Battle Damage', true],
		['All Modifications', true],
		['...', true]
	],
	'Size': [
		[false, true]
	],
	'Script call': [
		['...', false]
	],
	'Locked': [
		['Container', true],
		['Spell', true],
		['Level', true],
		['XP', true],
		['...', true],
	],
	'Attribute': [
		['Slots', true],
		['...', true]
	]
};
var edit_attrib_translate= [
	{	name: 'Physical State',
		id: 'state',
		mods: [
			['Liquid', 'liquid'],
			['-- Goo', 'goo'],
			['Solid', 'solid'],
			['-- Sand / Dust', 'sand'],
			['-- Sharp', 'sharp'],
			['-- Rough', 'rough'],
			['-- Unshaped', 'unshaped'],
			['Gas', 'gas']
		],
		vars: false
	},
	'Classification': [
		'class', [
			['Armor', 'armor'],
			['Weapon', 'weapon'],
			['Consumable', 'consumable'],
			['Container', 'container'],
			['Gem', 'gem'],
			['Reagent', 'reagent'],
			['Projectile', 'projectile'],
			['Goods', 'good'],
			['Recepie', 'recepie'],
			['Key', 'key'],
			['Usable', 'usable']
		]
	],
	'Equipability': [
		'equip', [
			['Head', 'head'],
			['Hands', 'hands'],
			['Feet', 'feet'],
			['Shoulder', 'shoulder'],
			['Body', 'body'],
			['Cloak', 'cloak'],
			['Shoes', 'shoes'],
			['Fingers', 'fingers'],
			['Neck','neck'],
			['Ears','ears']
		]
	],
	'Battle Damage': [
		'battle', [
			['Air', 'air'],
			['Fire', 'fire'],
			['Water', 'water'],
			['Ice', 'ice'],
			['Spirit', 'spirit'],
			['Death', 'death'],
			['Life', 'life'],
			['Earth', 'earth'],
			['Moon', 'moon'],
			['Sun', 'sun'],
			['Machine', 'machine'],
			['Nature', 'nature'],
			['Lightning', 'lightning'],
			['Physical', 'physical']
		]
	],
	'Variable Modifier': [
		['...', true]
	],
	'Effect Timeout': [
		['All', true],
		['All Battle Damage', true],
		['All Modifications', true],
		['...', true]
	],
	'Size': [
		[false, true]
	],
	'Script call': [
		['...', false]
	],
	'Locked': [
		['Container', true],
		['Spell', true],
		['Level', true],
		['XP', true],
		['...', true],
	],
	'Attribute': [
		['Slots', true],
		['...', true]
	]
};

/**
  * JSON Communication system
  *
  */
var json_timer = null;
var json_msgtimer = null;

function json_clearmessage() {
	if (json_msgtimer) clearTimeout(json_msgtimer);
	json_msgtimer = setTimeout(function() {
		$('json_output').setHTML('&nbsp;');
		$('json_output').setStyles({
			'visibility': 'hidden'
		});
	},5000);
}
  
function json_message(msg) {
	$('json_output').setHTML(msg);
	$('json_output').setStyles({
		'visibility': 'visible'
	});
	json_clearmessage();
}

function iedit_save(filename) {
	json_message('Saving...');
	var json = new Json.Remote('feed.php?a=save&f='+filename, {
		headers: {'X-Request': 'JSON'},
		onComplete: function(obj) {
			json_message('Saved!');
		},
		onFailure: function(err) {
			window.alert('failed'+err);
		}
	}).send({
		'name': $('item_name').value,
		'keywords': $('item_keyword').value,
		'desc': $('item_desc').value,
		'grid': edit_attribs
	});
}

function iedit_load(filename) {
	json_message('Loading...');
	var json = new Json.Remote('feed.php?a=load&f='+filename, {
		headers: {'X-Request': 'JSON'},
		onComplete: function(obj) {
			iedit_reset();
			$('item_name').value = obj.name;
			$('item_keyword').value = obj.keywords;
			$('item_desc').value = obj.desc;
			for (var i=0; i<obj.grid.length; i++) {
				var id = iedit_spawn_attrib();
				iedit_apply_config(id, obj.grid[i]);				
			}			
			json_message('Loaded!');
		},
		onFailure: function(err) {
			window.alert('failed'+err);
		}
	}).send();
}

function iedit_reset(){	
	for (var i=0; i<edit_elements.length; i++) {
		for (var j=0; j<edit_elements[i].length; j++) {			
			edit_elements[i][j].remove();
		}
	}
	edit_attrib_id=0;
	edit_attribs=[];
	edit_elements=[];	
	$('item_name').value = '';
	$('item_keyword').value = '';
	$('item_desc').value = '';
}

function iedit_nbspize(txt) {
	if (txt.trim() == '') return '&nbsp;';
	return txt;
}

function iedit_indexof(id) {
	for (var i=0; i<edit_attribs.length; i++) {
		if (edit_attribs[i].id == id) {
			return i;
			break;
		}
	}
	return -1;
}

function iedit_bind_list(elements,host) {
	var main=new Element('select');
	var width=host.getSize().size.x;
	main.setStyle('width', width);
	main.addEvent('click', function(e) {
		new Event(e).stop();
	});
	for (var i=0; i<elements.length; i++) {
		var elm=elements[i];
		if (typeof(elm)=='Array') {
			var name=elm[0];
			var value=name;
			if (elements.length>1) value=elm[1];		
		} else {
			var name=elm;
			var value=elm;
		}
		var row=new Element('option',{
			'value': value
		});
		row.setHTML(name);		
		row.inject(main);
	}
	
	var v=host.getText();
	if ((v==' ')||(v=='&nbsp;')) v='';
	host.setHTML('');
	main.value=v;
	main.inject(host);
	main.focus();
	main.addEvent('blur', function(e){
		var v=iedit_nbspize(this.value);
		var id=iedit_indexof(host.getProperty('x-entry'));
		if (id<0) return;
		var attrib=host.getProperty('x-attrib');
		host.setHTML(v);
		edit_attribs[id][attrib]=v;
	});
	return main;
}

function iedit_bind_input(host) {
	var main=new Element('input',{'type': 'text'});
	
	var width=host.getSize().size.x-6;
	main.setStyle('width', width);
	main.addEvent('click', function(e) {
		new Event(e).stop();
	});	
	
	var v=host.getText().trim();
	if ((v==' ')||(v=="&nbsp;")||(v=='&nbsp;')) v='';
	host.setHTML('');
	main.value=v;
	main.inject(host);
	main.focus();
	setTimeout(function(){main.select();},50);
	main.addEvent('blur', function(e){
		var v=iedit_nbspize(this.value.trim());
		var id=iedit_indexof(host.getProperty('x-entry'));
		if (id<0) return;
		var attrib=host.getProperty('x-attrib');
		host.setHTML(v);
		edit_attribs[id][attrib]=v;
	});
	return main;
}

function iedit_bind_blank(host) {
	var main=new Element('input',{'type': 'text', 'readonly': true, 'value': '---'});
	
	var width=host.getSize().size.x-6;
	main.addEvent('click', function(e) {
		new Event(e).stop();
	});	
	main.setStyles({				   
		'width': width,
		'background-color': '#CCCCCC'
	});
	
	var v=host.getText().trim();
	if ((v==' ')||(v=="&nbsp;")||(v=='&nbsp;')) v='';
	host.setHTML('');
	main.inject(host);
	main.focus();
	setTimeout(function(){main.select();},50);
	main.addEvent('blur', function(e){
		host.setHTML(iedit_nbspize(v));
	});
	return main;
}

function iedit_edit_attrib(host) {
	var attrib = host.getProperty('x-attrib');
	var id = iedit_indexof(host.getProperty('x-entry'));
	if (id<0) return;
	//window.alert('called from '+host+' with entry '+id+' editting as "'+attrib+'"');	
	if (attrib == 'typ') {
		var itm=[];
		for (nam in edit_attrib_grid) {
			itm.push(nam);
		}
	 	var list=iedit_bind_list(itm,host);
		list.addEvent('blur', function(e) {									   
			var ans = edit_attribs[id]['typ'];
			var vars=edit_attrib_grid[ans];		
			if ((vars.length == 1) && (vars[0][0] == '...')) {
				edit_elements[id][2].setHTML(iedit_nbspize(edit_attribs[id]['mod']));
				if (vars[0][1] == false) {
					edit_elements[id][3].setHTML('&nbsp;');
				} else {
					edit_elements[id][3].setHTML(iedit_nbspize(edit_attribs[id]['ofs']));
				}
			} else if ((vars.length == 1) && (vars[0][0] == false)) {
				edit_elements[id][2].setHTML('&nbsp;');
				if (vars[0][1] == false) {
					edit_elements[id][3].setHTML('&nbsp;');
				} else {
					edit_elements[id][3].setHTML(iedit_nbspize(edit_attribs[id]['ofs']));
				}
			} else {
				edit_elements[id][2].setHTML(iedit_nbspize(vars[0][0]));
				if (vars[0][1] == false) {
					edit_elements[id][3].setHTML('&nbsp;');
				} else {
					edit_elements[id][3].setHTML(edit_attribs[id]['ofs']);					
				}
			}
		});
	} else if (attrib == 'mod') {
		var c_typ=edit_attribs[id]['typ'];
		var vars=edit_attrib_grid[c_typ];		
		if ((vars.length == 1) && (vars[0][0] == '...')) {
			iedit_bind_input(host);
		} else if ((vars.length == 1) && (vars[0][0] == false)) {
			iedit_bind_blank(host);
		} else {
			var itm=[];
			for (var i=0; i<vars.length; i++) {
				itm.push(vars[i][0]);
			}
			var list=iedit_bind_list(itm,host)
			list.addEvent('change', function(e) {
				if (this.value == '...') {
					iedit_bind_input(host);
				}
			});
		}
	} else if (attrib == 'ofs') {
		var c_typ=edit_attribs[id]['typ'];
		var vars=edit_attrib_grid[c_typ];		
		if (vars.length == 1) {
			if (vars[0][1] == false) {
				iedit_bind_blank(host);
			} else {
				iedit_bind_input(host);
			}
		} else {	
			var found=false;
			var f_default=false;
			for (var i=0; i<vars.length; i++) {
				if (vars[i][0] == edit_attribs[id]['mod']) {
					found=vars[i][1];
					break;
				} else if (vars[i][0] == '...') {
					f_default=vars[i][1];
				}
			}
			if (!found) found=f_default;
			
			if (!found) {
				iedit_bind_blank(host);
			} else {				
				iedit_bind_input(host);
			}
		}
	} else if (attrib == 'grv') {
		var itm=[];
		for (var i=0; i<=10; i++) {
			itm.push(i);
		}
		iedit_bind_list(itm,host)		
	} else if (attrib == 'drp') {
		var itm=[];
		for (var i=0; i<=100; i+=2) {
			itm.push(i+' %');
		}
		iedit_bind_list(itm,host)
	} else if (attrib == 'att') {
		var itm=[];
		for (var i=0; i<=100; i+=2) {
			itm.push(i+' %');
		}
		iedit_bind_list(itm,host)		
	} else if (attrib == 'dir') {
		var itm=[
			'+10',
			'+5.50',
			'+5',
			'+4.50',
			'+4',
			'+3.50',
			'+3',
			'+2.50',
			'+2',
			'+1',
			'+0.5',
			'+0.25',
			'+0.10',
			'+0.05',
			'0',
			'-0.05',
			'-0.10',
			'-0.25',
			'-0.5',
			'-1',
			'-2',
			'-2.50',
			'-3',
			'-3.50',
			'-4',
			'-4.50',
			'-5',
			'-5.50',
			'-10'
		];
		iedit_bind_list(itm,host)		
	} else if (attrib == 'var') {
		var itm=[];
		for (var i=0; i<=100; i+=2) {
			itm.push(i+' %');
		}
		iedit_bind_list(itm,host)		
	}
}

function iedit_spawn_attrib(startedit) {
	var i = edit_attrib_id;
	edit_attrib_id++;
	
	var elm_list=[];
	
	var e_host = new Element('div', {
		'x-entry': i
	});
	
	var e_input = new Element('input', {
		'type': 'checkbox',
		'value': 'true',
		'class': 'chb',
		'className': 'chb',
		'x-entry': i
	});
	e_input.inject(e_host);
	elm_list.push(e_input);
	
	var e_parm = new Element('span', {
		'x-entry': i,
		'x-attrib': 'typ'
	});
	e_parm.setHTML('Classification');
	e_parm.setStyle('width', 220);
	e_parm.addEvent('click', function(e) {
		iedit_edit_attrib(this);
	});
	e_parm.inject(e_host);
	elm_list.push(e_parm);
	
	var e_parm = new Element('span', {
		'x-entry': i,
		'x-attrib': 'mod'
	});
	e_parm.setHTML('&nbsp;');
	e_parm.setStyle('width', 220);
	e_parm.addEvent('click', function(e) {
		iedit_edit_attrib(this);
	});
	e_parm.inject(e_host);
	elm_list.push(e_parm);

	var e_parm = new Element('span', {
		'x-entry': i,
		'x-attrib': 'ofs'
	});
	e_parm.setHTML('0');
	e_parm.setStyle('width', 100);
	e_parm.addEvent('click', function(e) {
		iedit_edit_attrib(this);
	});
	e_parm.inject(e_host);
	elm_list.push(e_parm);

	var e_parm = new Element('span', {
		'x-entry': i,
		'x-attrib': 'grv'
	});
	e_parm.setHTML('5');
	e_parm.setStyle('width', 50);
	e_parm.addEvent('click', function(e) {
		iedit_edit_attrib(this);
	});
	e_parm.inject(e_host);
	elm_list.push(e_parm);

	var e_parm = new Element('span', {
		'x-entry': i,
		'x-attrib': 'drp'
	});
	e_parm.setHTML('0 %');
	e_parm.setStyle('width', 80);
	e_parm.addEvent('click', function(e) {
		iedit_edit_attrib(this);
	});
	e_parm.inject(e_host);
	elm_list.push(e_parm);

	var e_parm = new Element('span', {
		'x-entry': i,
		'x-attrib': 'att'
	});
	e_parm.setHTML('0 %');
	e_parm.setStyle('width', 80);
	e_parm.addEvent('click', function(e) {
		iedit_edit_attrib(this);
	});
	e_parm.inject(e_host);
	elm_list.push(e_parm);

	var e_parm = new Element('span', {
		'x-entry': i,
		'x-attrib': 'dir'
	});
	e_parm.setHTML('-1');
	e_parm.setStyle('width', 80);
	e_parm.addEvent('click', function(e) {
		iedit_edit_attrib(this);
	});
	e_parm.inject(e_host);
	elm_list.push(e_parm);

	var e_parm = new Element('span', {
		'x-entry': i,
		'x-attrib': 'var'
	});
	e_parm.setHTML('0 %');
	e_parm.setStyle('width', 80);
	e_parm.addEvent('click', function(e) {
		iedit_edit_attrib(this);
	});
	e_parm.inject(e_host);
	elm_list.push(e_parm);
	
	e_host.inject($('attrib_list'));
	elm_list.push(e_host);

	edit_elements.push(elm_list);
	edit_attribs.push({
		'id': i,
		'typ': 'Classification',
		'mod': '',
		'ofs': '0',
		'grv': '5',
		'drp': '0 %',
		'att': '0 %',
		'dir': '-1',
		'var': '0 %'
	});

	if (startedit) iedit_edit_attrib(elm_list[1]);
	return i;
}

function iedit_apply_config(index, config) {
	// Get the attributes (leaving ID unharmed)
	edit_attribs[index].typ = config.typ;
	edit_attribs[index].mod = config.mod;
	edit_attribs[index].ofs = config.ofs;
	edit_attribs[index].grv = config.grv;
	edit_attribs[index].drp = config.drp;
	edit_attribs[index].att = config.att;
	
	// Modify view :: Dynamic elements
	edit_elements[index][1].setHTML(edit_attribs[index]['typ']);

	var vars=edit_attrib_grid[config.typ];		
	if ((vars.length == 1) && (vars[0][0] == '...')) {
		edit_elements[index][2].setHTML(iedit_nbspize(edit_attribs[index]['mod']));
		if (vars[0][1] == false) {
			edit_elements[index][3].setHTML('&nbsp;');
		} else {
			edit_elements[index][3].setHTML(iedit_nbspize(edit_attribs[index]['ofs']));
		}
	} else if ((vars.length == 1) && (vars[0][0] == false)) {
		edit_elements[index][2].setHTML('&nbsp;');
		if (vars[0][1] == false) {
			edit_elements[index][3].setHTML('&nbsp;');
		} else {
			edit_elements[index][3].setHTML(iedit_nbspize(edit_attribs[index]['ofs']));
		}
	} else {
		edit_elements[index][2].setHTML(edit_attribs[index]['mod']);
		if (vars[0][1] == false) {
			edit_elements[index][3].setHTML('&nbsp;');
		} else {
			edit_elements[index][3].setHTML(edit_attribs[index]['ofs']);
		}
	}	
	
	// Static elements
	edit_elements[index][4].setHTML(edit_attribs[index]['grv']);
	edit_elements[index][5].setHTML(edit_attribs[index]['drp']);
	edit_elements[index][6].setHTML(edit_attribs[index]['att']);
	edit_elements[index][7].setHTML(edit_attribs[index]['dir']);
	edit_elements[index][8].setHTML(edit_attribs[index]['var']);
}

function attrib_new() {
	iedit_spawn_attrib(true);
}

function attrib_delete() {
	$$('.itemlist_host .chb').each(function(e) {
		if (e.checked) {
			var id = iedit_indexof(e.getProperty('x-entry'));
			if (id<0) return false;
			for (var i=0; i<edit_elements[id].length; i++) {
				edit_elements[id][i].remove();
			}
			edit_elements.splice(id,1);
			edit_attribs.splice(id,1);
		}
	});
}

function ui_save() {
	iedit_save('test');
}

function ui_load() {
	iedit_load('test');
}

$(window).addEvent('load', function(e) {
	var id = iedit_spawn_attrib();
	iedit_apply_config(id, {
		'typ': 'Physical State',
		'mod': 'Sand / Dust',
		'ofs': '',
		'grv': '10',
		'drp': '50 %',
		'att': '10 %',
		'dir': '0',
		'var': '10 %'
	});
});

$(window).addEvent('beforeunload', function(e) {
	return "The page you are editting is not saved!";
});