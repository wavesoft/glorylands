
var edit_icon='';
var edit_attrib_id=0;
var edit_attribs=[];
var edit_elements=[];
var edit_attrib_grid= [
	{	name: 'Physical State',
		id: 'state',
		mods: [
			['Liquid', 'liquid'],
			['Liquid / Goo', 'goo'],
			['Liquid / Sticky', 'sticky'],
			['Liquid / Slippery', 'slippery'],
			['Solid', 'solid'],
			['Solid / Dust', 'dust'],
			['Solid / Sharp', 'sharp'],
			['Solid / Rough', 'rough'],
			['Solid / Unshaped', 'unshaped'],
			['Gas', 'gas'],
			['Gas / Thick', 'gas_thick'],
			['Gas / Thin', 'gas_thin']
		],
		ofs: false
	},
	{	name: 'Classification',
		id: 'class',
		mods: [
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
		],
		ofs: false
	},
	{	name: 'Usability',
		id: 'use',
		mods: [
			['Clickable (Can be clicked)', 'click'],
			['Weapon (Can be used as weapon)', 'weapon'],
			['Shield (Can be used as shield)', 'shield'],
			['Container (Contains sub-items)', 'container'],
			['None (Just stores variables)', 'none'],
		],
		ofs: false
	},
	{	name: 'Can be equipped',
		id:	'equip',
		mods: [
			['Head', 'head'],
			['Hands', 'hands'],
			['Feet', 'feet'],
			['Shoulder', 'shoulder'],
			['Body', 'body'],
			['Cloak', 'cloak'],
			['Shoes', 'shoes'],
			['Fingers', 'fingers'],
			['Neck','neck'],
			['Ears','ears'],
			['Backpack','backpack']
		],
		ofs: false
	},
	{	name: 'Absorb/Cause Damage',
		id: 'damage',
		mods: [
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
		],
		ofs: true
	},
	{	name: 'Modifies Variable',
		id: 'modify',
		mods: true,
		ofs: true
	},
	{	name: 'Effect Expiry',
		id: 'timeout',
		mods: [
			['All', '_ALL_'],
			['All Battle Damage', '_BATTLE_'],
			['All Modifications', '_MODIFY_'],
			['...', false]
		],
		ofs: true
	},
	{	name: 'Has size',
		id: 'size',
		mods: false,
		ofs: true
	},
	{	name: 'Handled by script',
		id: 'call',
		mods: true,
		ofs: true
	},
	{	name: 'Starts Quest',
		id: 'quest',
		mods: true,
		ofs: false
	},
	{	name: 'Requires Lock',
		id: 'lock',
		mods: [
			['Container Template', 'ctemplate'],
			['Item Template', 'itemplate'],
			['Item Instance', 'iinstance'],
			['Spell', 'spell'],
			['Level', 'level'],
			['XP', 'xp'],
			['...', false]
		],
		ofs: true
	},
	{	name: 'Extra Attribute',
		id: 'attrib',
		mods: [
			['Slots', 'slots'],
			['...', false]
		],
		ofs: true
	}
];

/**
  * Attribute translator
  *
  */
function attrib_get_types() {
	var list=[];
	for (var i=0; i<edit_attrib_grid.length; i++) {
		list.push(edit_attrib_grid[i].name); 
	}
	return list;
}

function attrib_get_mods(type) {
	// Get the possible mod values
	var id=attrib_get_type_id(type);
	if (id<0) return false;

	// Get mods
	var mods=edit_attrib_grid[id].mods;
	if (mods==false) {
		return false; /* Missing */
	} else if (mods==true) {
		return true; /* Free */
	} else { /* List */
		var list=[];
		for (var i=0; i<mods.length; i++) {
			list.push(mods[i][0]); 
		}
		return list;
	}
}

function attrib_get_value(type) {
	// Get the possible mod values
	var id=attrib_get_type_id(type);
	if (id<0) return false;

	// Return offset type
	return edit_attrib_grid[id].ofs;
}

function attrib_get_type_id(name) {
	var id=-1;
	for (var i=0; i<edit_attrib_grid.length; i++) {
		if (edit_attrib_grid[i].name == name) {
			id=i;
			break;
		}
	}
	return id;
}

function attrib_get_type_id_byid(item_id) {
	var id=-1;	
	for (var i=0; i<edit_attrib_grid.length; i++) {
		if (edit_attrib_grid[i].id == item_id) {
			id=i;
			break;
		}
	}
	return id;
}

function attrib_get_mod_id(type_id, name) {
	var id=-1;
	var default_id=-1;
	
	var vars=edit_attrib_grid[type_id].mods;
	if (vars == false) {
		return -1;
	} else {
		for (var i=0; i<vars.length; i++) {
			if (vars[i][0] == name) {
				id=i;
				break;
			} else if (vars[i][0] == '...') {
				default_id=i;
			}
		}
		if (id == -1) id=default_id;
	}
	return id;
}

function attrib_get_default_mod(type,preferred) {
	// Get the possible mod values
	var id=attrib_get_type_id(type);
	if (id<0) return false;
	var mods=edit_attrib_grid[id].mods;
	
	// No mods? Return blank
	if (mods == false) {
		return '';
	// Dynamic mods? Return preferred
	} else if (mods == true) {
		return preferred;
	} else {
		// If we have a preferred value, search
		// for it
		if (!preferred) {
		} else {
			for (var i=0; i<mods.length; i++) {
				if (mods[i][0] == preferred) {
					return preferred;
				}
			}
			// If not found, just return the first value
		}
		return mods[0][0];
	}
}

function attrib_get_default_value(type, preferred) {
	// Get the type ID
	var id=attrib_get_type_id(type);
	if (id<0) return false;

	// No preferred value? Return blank
	if (!preferred) {
		return '';
	} else {
		// If no offset value defined, return blank
		if (edit_attrib_grid[id].ofs == false) {
			return '';
		} else {
			return preferred;
		}
	}
}

/**
  * JSON Communication system
  *
  */
var json_timer = null;
var json_msgtimer = null;

function json_clearmessage(timeout) {
	if (!timeout) timeout=5000;
	if (json_msgtimer) clearTimeout(json_msgtimer);
	json_msgtimer = setTimeout(function() {
		$('json_output').setHTML('&nbsp;');
		$('json_output').setStyles({
			'visibility': 'hidden'
		});
	},timeout);
}
  
function json_message(msg,timeout) {
	$('json_output').setHTML(msg);
	$('json_output').setStyles({
		'visibility': 'visible'
	});
	if (!timeout) {		
	} else {
		json_clearmessage();
	}
}

function json_compress_array(dat) {
	// Convert the parameter names from the long
	// to short format
	
	var trans_typ = function(t_id) {
		return edit_attrib_grid[t_id].id;
	}
	var trans_mod = function(t_id, v_mod) {
		var mods=edit_attrib_grid[t_id].mods;
		if (mods == false) {
			return false;
		} else {
			for (var i=0; i<mods.length; i++) {
				if (mods[i][0] == v_mod) {
					return mods[i][1];
				}
			}
			return v_mod;
		}
	}
	var trans_var = function(t_id, v_var) {
		var varmode=edit_attrib_grid[t_id].ofs;
		if (varmode) {
			return v_var;
		} else {			
			return false;
		}
	}
	var trans_perc = function(v_perc) {
		return Number(v_perc.substr(0, v_perc.length-2));
	}
	
	var ans=[];
	for (var i=0; i<dat.length; i++) {
		var t_id = attrib_get_type_id(dat[i].typ);
		ans.push({
			'typ': trans_typ(t_id),
			'mod': trans_mod(t_id, dat[i].mod),
			'ofs': trans_var(t_id, dat[i].ofs),
			'grv': Number(dat[i].grv),
			'drp': trans_perc(dat[i].drp),
			'att': trans_perc(dat[i].att),
			'dir': Number(dat[i].dir),
			'var': trans_perc(dat[i]['var']),
			'use': trans_perc(dat[i]['use'])
		});
	}
	return ans;
}

function json_expand_array(dat) {
	
	var trans_typ = function(t_id) {		
		return edit_attrib_grid[t_id].name;
	}
	var trans_mod = function(t_id, v_mod) {
		var mods=edit_attrib_grid[t_id].mods;
		if (mods == false) {
			return false;
		} else {
			for (var i=0; i<mods.length; i++) {
				if (mods[i][1] == v_mod) {
					return mods[i][0];
				}
			}
			return v_mod;
		}
	}
	var trans_var = function(t_id, v_var) {
		var varmode=edit_attrib_grid[t_id].ofs;
		if (varmode) {
			return v_var;
		} else {			
			return false;
		}
	}
	var trans_plusmin = function(t_var) {
		if (t_var > 0) {
			return '+'+t_var;
		} else {
			return t_var;
		}
	}
	
	var ans=[];
	for (var i=0; i<dat.length; i++) {
		var t_id = attrib_get_type_id_byid(dat[i].typ);
		ans.push({
			'typ': trans_typ(t_id),
			'mod': trans_mod(t_id, dat[i].mod),
			'ofs': trans_var(t_id, dat[i].ofs),
			'grv': dat[i].grv,
			'drp': dat[i].drp+' %',
			'att': dat[i].att+' %',
			'dir': trans_plusmin(dat[i].dir),
			'var': dat[i]['var']+' %',
			'use': dat[i]['use']+' %'
		});
	}
	return ans;
}

/**
  * Item Editor functions
  *
  */
  
function iedit_save(filename) {
	json_message('Saving...');
	var json = new Json.Remote('feed.php?a=save&f='+filename, {
		headers: {'X-Request': 'JSON'},
		onComplete: function(obj) {
			json_message('Saved!',1000);
		},
		onFailure: function(err) {
			window.alert('failed'+err);
		}
	}).send({
		'name': $('item_name').value,
		'keywords': $('item_keyword').value,
		'desc': $('item_desc').value,
		'grid': json_compress_array(edit_attribs),
		'icon':edit_icon
	});
}

function iedit_publish(filename, publish_data) {
	json_message('Publishing...');
	var json = new Json.Remote('feed.php?a=publish&f='+filename, {
		headers: {'X-Request': 'JSON'},
		onComplete: function(obj) {
			json_message('Published!',1000);
		},
		onFailure: function(err) {
			window.alert('failed'+err);
		}
	}).send({
		'name': $('item_name').value,
		'keywords': $('item_keyword').value,
		'desc': $('item_desc').value,
		'grid': json_compress_array(edit_attribs),
		'icon':edit_icon,
		'pub': publish_data
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
			edit_icon=obj.icon;
			$('ui_itemimage').src = '../../images/inventory/'+obj.icon;
			
			obj.grid=json_expand_array(obj.grid);
			for (var i=0; i<obj.grid.length; i++) {
				var id = iedit_spawn_attrib();
				iedit_apply_config(id, obj.grid[i]);				
			}			
			json_message('Loaded!',1000);
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
	edit_icon='';
	$('item_name').value = '';
	$('item_keyword').value = '';
	$('item_desc').value = '';
	$('ui_itemimage').src='images/blank128.png';
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
		var itm=attrib_get_types();
	 	var list=iedit_bind_list(itm,host);
		
		list.addEvent('blur', function(e) {
			var typ = edit_attribs[id]['typ'];
			var mod = attrib_get_default_mod(typ, edit_attribs[id]['mod']);
			var val = attrib_get_default_value(typ, edit_attribs[id]['ofs']);
			edit_elements[id][2].setHTML(iedit_nbspize(mod));
			edit_elements[id][3].setHTML(iedit_nbspize(val));
		});
	} else if (attrib == 'mod') {
		var typ = edit_attribs[id]['typ'];
		var mods = attrib_get_mods(typ);

		if (mods == false) {
			iedit_bind_blank(host);
		} else if (mods == true) {
			iedit_bind_input(host);
		} else {
			var list=iedit_bind_list(mods,host)
			list.addEvent('change', function(e) {
				if (this.value == '...') {
					iedit_bind_input(host);
				}
			});
			list.addEvent('blur', function(e) {
				var typ = edit_attribs[id]['typ'];
				var val = attrib_get_default_value(typ, edit_attribs[id]['ofs']);
				edit_elements[id][3].setHTML(iedit_nbspize(val));
			});
		}
	} else if (attrib == 'ofs') {
		var typ=edit_attribs[id]['typ'];
		var value=attrib_get_value(typ);
		if (value == false) {
			iedit_bind_blank(host);
		} else {
			iedit_bind_input(host);
		}
	} else if (attrib == 'grv') {
		var itm=[];
		for (var i=0; i<=10; i++) {
			itm.push(i);
		}
		iedit_bind_list(itm,host)		
	} else if ( (attrib == 'att') || (attrib == 'var') || (attrib == 'drp') || (attrib == 'use')) {
		var itm=[];
		for (var i=0; i<=100; i+=2) {
			itm.push(i+' %');
		}
		iedit_bind_list(itm,host)		
	} else if (attrib == 'dir') {
		var itm=[
			'+1',
			'0',
			'-1'
		];
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
		'x-attrib': 'var'
	});
	e_parm.setHTML('0 %');
	e_parm.setStyle('width', 78);
	e_parm.addEvent('click', function(e) {
		iedit_edit_attrib(this);
	});
	e_parm.inject(e_host);
	elm_list.push(e_parm);

	var e_parm = new Element('span', {
		'x-entry': i,
		'x-attrib': 'use'
	});
	e_parm.setHTML('100 %');
	e_parm.setStyle('width', 78);
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
		'var': '0 %',
		'use': '100 %'
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
	edit_attribs[index].dir = config.dir;
	edit_attribs[index]['var'] = config['var']
	edit_attribs[index].use = config.use;
	
	// Modify view :: Dynamic elements
	edit_elements[index][1].setHTML(edit_attribs[index]['typ']);

	var typ = edit_attribs[index]['typ'];
	var mod = attrib_get_default_mod(typ, edit_attribs[index]['mod']);
	var val = attrib_get_default_value(typ, edit_attribs[index]['ofs']);
	edit_elements[index][2].setHTML(iedit_nbspize(mod));
	edit_elements[index][3].setHTML(iedit_nbspize(val));
	
	// Static elements
	edit_elements[index][4].setHTML(edit_attribs[index]['var']);
	edit_elements[index][5].setHTML(edit_attribs[index]['use']);
	edit_elements[index][6].setHTML(edit_attribs[index]['grv']);
	edit_elements[index][7].setHTML(edit_attribs[index]['drp']);
	edit_elements[index][8].setHTML(edit_attribs[index]['att']);
	edit_elements[index][9].setHTML(edit_attribs[index]['dir']);
}

/**
  * UI Management
  *
  */

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

function ui_publish() {
	iedit_publish('test', {id: 1000});
}

function ui_save() {
	iedit_save('test');
}

function ui_load() {
	iedit_load('test');
}

function ui_realign() {
	// Center some objects
	var s=$('ui_browseimage').getSize().size;
	var w=$(window).getSize().size;
	$('ui_browseimage').setStyles({
		'left': (w.x-s.x)/2,
		'top': (w.y-s.y)/2
	});	
}

// ### Image browser ###
var page=0;
var images=[];
var last_filter='';

function ui_browse_cancel() {
	$('ui_browseimage').setStyles({
		'display': 'none'
	});
	ui_browse_reset();
}

function ui_browse_open() {
	$('ui_browseimage').setStyles({
		'display': ''
	});
	page=0;
	$('ui_text_search').value = '*';
	$('ui_browser_page').setHTML('Page <b>'+page+'</b>');
	ui_realign();
	ui_browse_reset();
	ui_browse_refresh('*',0);
}

function ui_browse_reset(waitani) {
	for (var i=0; i<images.length; i++) {
		images[i].remove();
	}
	images=[];
	if (!waitani) {
	} else {
		var e=new Element('img', {
			'src' : 'images/loading.gif'
		});
		e.inject($('ui_browseimage_content'));
		images.push(e);		
	}
}

function ui_browse_search(filter) {
	ui_browse_reset(true);
	page=0;
	ui_browse_refresh('*'+filter+'*');
}

function ui_browse_next() {
	ui_browse_reset(true);
	page++;
	$('ui_browser_page').setHTML('Page <b>'+page+'</b>');
	ui_browse_refresh(last_filter);	
}

function ui_browse_prev() {
	ui_browse_reset(true);
	page--;
	if (page<0) page=0;
	$('ui_browser_page').setHTML('Page <b>'+page+'</b>');
	ui_browse_refresh(last_filter);	
}

function ui_browse_refresh(filter) {
	json_message('Updating list...');
	if (filter == '') $filter='*';
	var json = new Json.Remote('feed.php?a=images&f='+filter+'&p='+page, {
		headers: {'X-Request': 'JSON'},
		onComplete: function(obj) {
			json_message('List arrived',1000);
			ui_browse_reset();
			for (var i=0; i<obj.length; i++) {
				var im = new Element('img', {
					'src': '../../images/inventory/'+obj[i],
					'border' :0
				});
				var a = new Element('a', {
					'href': 'javascript:;',
					'title': obj[i]
				});
				a.addEvent('click', function(e) {
					edit_icon=this.getProperty('title');
					$('ui_itemimage').src='../../images/inventory/'+edit_icon;
					ui_browse_cancel();
				});
				im.inject(a);
				a.inject($('ui_browseimage_content'));
				images.push(im);
				images.push(a);
			}
		},
		onFailure: function(err) {
			window.alert('failed'+err);
		}
	}).send();	
	last_filter=filter;
}

/**
  * Default browser actions
  *
  */

$(window).addEvent('load', function(e) {
	ui_realign();
});

$(window).addEvent('resize', function(e) {
	ui_realign();
});

$(window).addEvent('beforeunload', function(e) {
	//return "The page you are editting is not saved!";
});