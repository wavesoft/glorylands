/* Identify Browser */
var isInternetExplorer=(navigator.userAgent.indexOf("MSIE")>=0);
var isMozilla=(navigator.userAgent.indexOf("Gecko")>=0);
var isOpera=(navigator.userAgent.indexOf("Opera")>=0);

// ### DEBUG FUNCTIONS ###
function $trace(obj,separator) {
	if (!separator) separator=', ';
	ans='';
	$each(obj, function(value, name) {
		if (ans!='') ans+=separator;
		ans+='['+name+'] = '+value;
	});	
	return ans;
}

/**
  * Node structures and global variables
  */
var NDNode = new Class({
	// Coding info
	name: new String(),
	operator: new String(),
	operands: new Array(),
	
	// Identification and syntax selection
	type: new String(),
	subtype: new String(),
	nestlevel: new Number(0),
	id: new Number(0),
	parent: null,
	
	// HTML-Only information
	element_head: null,      /* The element used to display the head */
	element_op: null,        /* The element that shows the operation */
	element_children: null,  /* The element that hosts the children */
	element_host: null,      /* The overall element that wraps the above */
	has_op: false,			 /* True if the node accepts Operands */
	child_info: []			 /* The children information (tag,type,name) this node can have */
});

var nd_root = null;

// ================[ SYNTAX MANAGEMENT ]=============================================================================== //

var syntax_functions = [];
var syntax_function_info = [];
var syntax_vars = [];
var syntax_var_info = [];
var syntax_objects = [];
var syntax_object_info = [];
var syntax_constants = [];
var syntax_constant_info = [];

/**
  * Download and initialize syntax information
  */

function syntax_download() {
	var rq = new Json.Remote('feed.php?a=syntax', {
		onComplete: function(result) {
			if ($defined(result.func)) syntax_functions=result.func;
			if ($defined(result.func_inf)) syntax_function_info=result.func_inf;
			if ($defined(result.cvar)) syntax_vars=result.cvar;
			if ($defined(result.cvar_inf)) syntax_var_info=result.cvar_inf;
			if ($defined(result.obj)) syntax_objects=result.obj;
			if ($defined(result.obj_inf)) syntax_object_info=result.obj_inf;
		}
	}).send();	
}

/**
  * Detect what type is this name
  */
function syntax_get_type(node) {
	var id=0;
	if (node.parent.type == 'object') {
		var obj_id = syntax_objects.indexOf(node.parent.name);
		if (obj_id<0) return "unknown";
		
		id=syntax_object_info[obj_id].func.indexOf(node.name);
		if (id>=0) return {'type': "function", 'subtype':syntax_object_info[obj_id].func_inf[id]['return']};
		id=syntax_object_info[obj_id].cvar.indexOf(node.name);
		if (id>=0) return {'type': "var", 'subtype':syntax_object_info[obj_id].cvar_inf[id].type};
	} else if (node.name.substr(0,1)=='"') {
		return {'type': "const", 'subtype':"string"};
	} else if (!isNaN(node.name))  {
		return {'type': "const", 'subtype':"number"};
	} else if (node.name == 'true') {
		return {'type': "const", 'subtype':"boolean"};
	} else if (node.name == 'false') {
		return {'type': "const", 'subtype':"boolean"};
	} else {
		id=syntax_functions.indexOf(node.name);
		if (id>=0) return {'type': "function", 'subtype':syntax_function_info[id]['return']};
		id=syntax_vars.indexOf(node.name);
		if (id>=0) return {'type': "var", 'subtype':syntax_var_info[id].type};
		id=syntax_objects.indexOf(node.name);
		if (id>=0) return {'type': "object", 'subtype':''};
		
		// Not found? Guess it as variable and store it on local variable store
		syntax_vars.push(node.name);
		syntax_var_info.push({desc:'Local variable', type:''});
		return {'type': "var", 'subtype':''};
	}
}

/**
  * Get what operands are compatible with this type
  */
function syntax_get_node_info(node) {
	var id=0, args=[];
	if (node.parent.type == 'object') {
		var obj_id = syntax_objects.indexOf(node.parent.name);
		if (node.type == 'function') {
			id=syntax_object_info[obj_id].func.indexOf(node.name);
			return {
				'desc': syntax_object_info[obj_id].func_inf[id].desc,
				'args': syntax_object_info[obj_id].func_inf[id].args,
				'op': false
			};
		} else if (node.type == 'var') {
			id=syntax_object_info[obj_id].cvar.indexOf(node.name);
			if ($defined(syntax_object_info[obj_id].func_inf[id].args)) {
				args=syntax_object_info[obj_id].func_inf[id].args
			} else {
				args=[['','','']];
			}
			return {
				'desc': syntax_object_info[obj_id].func_inf[id].desc,
				'args': args,
				'op': true
			};
		}
	} else {
		if (node.type == 'function') {
			id=syntax_functions.indexOf(node.name);
			return {
				'desc': syntax_function_info[id].desc,
				'args': syntax_function_info[id].args,
				'op': false
			};
		} else if (node.type == 'var') {
			id=syntax_vars.indexOf(node.name);
			if ($defined(syntax_var_info[id].args)) {
				args=syntax_var_info[id].args
			} else {
				args=[['','','']];
			}
			return {
				'desc': syntax_var_info[id].desc,
				'args': args,
				'op': true
			};
		} else if (node.type == 'object') {
			id=syntax_objects.indexOf(node.name);
			return {
				'desc': syntax_object_info[id].desc,
				'args': [['','','']],
				'op': false
			};
		} else if (node.type == 'const') {
			return {
				'desc': 'Constant value: '+node.name,
				'args': [],
				'op': false
			}
		}
	}
}

/**
  * Detect what operations are valid for this node
  */
function syntax_get_valid_op(node) {
	if (node.parent.type == 'function') {
		if (node.type == 'function') {
			return [];
		} else if (node.type == 'var') {
			return [['+',1],['-',1],['/',1],['*',1],['^',1],['==',1],['!=',1],['>',1],['<',1],['>=',1],['<=',1],['&&',1],['||',1],
		            ['--',0],['++',0]];
		}
	} else {
		if (node.type == 'function') {
			return [];
		} else if (node.type == 'var') {
			return [['=',1],['+=',1],['-=',1],['/=',1],['*=',1],
		            ['--',0],['++',0]];
		} else if (node.type == 'object') {
			return [];
		} else if (node.type == 'const') {
			return [];
		}
	}
}

/**
  * Find what syntax is compatible for this node
  */
function syntax_get_compatible(node) {
	var ans=[];
	if (node.parent.type == 'object') {
		var obj_id = syntax_objects.indexOf(node.parent.name);
		for (var i=0; i<syntax_object_info[obj_id].func.length; i++) {
			ans.push([syntax_object_info[obj_id].func[i],'function']);
		}
		for (var i=0; i<syntax_object_info[obj_id].cvar.length; i++) {
			ans.push([syntax_object_info[obj_id].cvar[i],'var']);
		}
	} else {
		for (var i=0; i<syntax_functions.length; i++) {
			ans.push([syntax_functions[i],'function']);
		}
		for (var i=0; i<syntax_vars.length; i++) {
			ans.push([syntax_vars[i],'var']);
		}
		for (var i=0; i<syntax_objects.length; i++) {
			ans.push([syntax_objects[i],'object']);
		}
	}
	return ans;
}

/**
  * Request cache
  */
var rq_cache = [];
var rq_uri = [];

function rq_get_info(mode, node, callback) {
	var uri = 'feed.php?a=list&m='+mode+'&t='+node.type+'&st='+node.subtype;
	var index = rq_uri.indexOf(uri);
	if (index<0) {
		var rq = new Json.Remote(uri, {
			onComplete: function(result) {
				rq_cache.push(result);
				rq_uri.push(uri);
				callback(result);
			}
		}).send();
	} else {
		callback(rq_cache[index]); 
	}
}

// ================[ SUPPORTING FUNCTIONS ]=============================================================================== //

/**
  * Exclude all element nodes and build the pure structure
  */
function nd_get_struct(node) {	
	// Handle children
	var ops=[];
	var i=0;
	for (i=0; i<node.operands.length; i++) {
		ops.push(nd_get_struct(node.operands[i]));
	}
	
	return {
		'nm': node.name.toString(),
		'op': node.operator.toString(),
		'ch': ops,
		'tp': node.type.toString(),
		'st': node.subtype.toString()
	};
}

/**
  * Spawn a new Node and return the reference ID
  */
function nd_node_spawn(parent) {
	var nd = new NDNode();
	nd.operands=[];
	nd.type='unknown';
	nd.subtype='';
	nd.operand='';
	if (parent) {
		nd.parent = parent;
		nd.nestlevel = parent.nestlevel+1;
		parent.operands.push(nd);
	}
	return nd;
}

/**
  * IE Workaround functions
  *
  */
function set_class(element, CSSclass) {
	if (!isInternetExplorer) {
		element.setAttribute('class', CSSclass);
	} else {
		element.setAttribute('className', CSSclass);		
	}
}

/**
  * Error handling function
  *
  * Used to display or hide the errors based on the 
  * debug level used
  * 
  * Error levels are:
  *  0 = Debug message
  *  1 = Warning
  *  2 = Error
  *  3 = Critical Error
  *
  */
function nd_error(desc,level) {
	window.alert(desc);	
}

// ================[ NODE EDIT DROPDOWN MENU ]=============================================================================== //

/**
  * Display a dropdown menu with compatible classes
  */
var nd_ddmenu_elements=[];

function nd_dispose_dropdown() {
	$each(nd_ddmenu_elements, function(e) {
	   e.remove();
	});
	nd_ddmenu_elements=[];
}

function nd_spawn_element_text(info,text_element,node) {
	var e = $(document.createElement('a'));
	e.setProperties({
		'href': 'javascript:;',
		'title': info.type
	});
	set_class(e,info.type);
	e.setHTML(info.name);
	e.addEvent('click',function(e){
		text_element.value = info.name;
		nd_edit_dispose(e);
	});
	nd_ddmenu_elements.push(e);
	return e;
}

function nd_spawn_element_op(operation,node) {
	var e = $(document.createElement('a'));
	e.setProperties({
		'href': 'javascript:;'
	});
	e.setHTML(operation);
	e.addEvent('click',function(ev){
		node.operator=operation;
		nd_dispose_dropdown();
		nd_update_node_view(node);
		new Event(ev).stop();
	});
	nd_ddmenu_elements.push(e);
	return e;
}

function nd_edit_dropdown(node, element, mode) {
	// Dispose any previous dropdown
	nd_dispose_dropdown();
	
	// Select default mode, if not specified
	if (!mode) mode='HEAD';
	
	// Build the menu
	var pos = element.getPosition();
	var siz = element.getSize().size;
	var e = $(document.createElement('div'));
	var w=siz.x;
	if (w<50) w=50;
	set_class(e,'compatible_dropdown');
	$(document.body).appendChild(e);
	e.setStyles({
		'left': pos.x,
		'top': pos.y+siz.y,
		'width': 'inherit',
		'height': 200
	});
	
	// Interrupt mousedown events
	e.addEvent('mousedown',function(ev){
		new Event(ev).stop();
	});
	
	// Spawn children
	if (mode == 'HEAD') {	
		var items = syntax_get_compatible(node);
		for (var i=0; i<items.length; i++) {
			var elm=nd_spawn_element_text({'name':items[i][0],'type':items[i][1]},element,node);
			e.appendChild(elm);
		}

	} else if (mode == 'OP') {
		var items = syntax_get_valid_op(node);
		for (var i=0; i<items.length; i++) {
			var elm=nd_spawn_element_op(items[i][0],node);
			e.appendChild(elm);
		}

	}
	
	// Store menu element for disposal
	nd_ddmenu_elements.push(e);
}

// ================[ NODE UPDATING DYNAMICS ]================================================================================ //

/**
  * Remove operands from a node
  */
function nd_remove_operands(node) {
	node.element_children.getChildren().each(function(e){
		e.remove();
	});
	node.operands=[];
}

/**
  * Get a list of compatible classes for this node
  */
function nd_get_compatible(node) {
	
}

/**
  * Update node view
  */
function nd_update_node_view(node) {
	// Do we have a node name and head element? Apply changes...
	if ((node.name!='') && (node.element_head!=null)) {
		
		// Apply text
		node.element_head.setHTML(node.name);
		
		// Update style
		var CSSclass = node.type;
		if (node.subtype!='') CSSclass+=' '+node.subtype;
		set_class(node.element_head, CSSclass);		
	} else if (node.name == '') {
		
		// No node name? Dispose node
		nd_remove_operands(node);
		nd_remove_node(node,true);
		return false;
	}
	
	// Get node info
	var info = syntax_get_node_info(node);
	if (!info) {
	
	} else {
		// Update node info
		if ($defined(info.desc)) node.element_head.setProperty('title', info.desc);
		if ((node.operator!='') && (node.element_op!=null)) {
			node.element_op.setHTML(node.operator);
			set_class(node.element_op,'op');
		}

		// Set operand
		var op_info = syntax_get_valid_op(node);
		var extra_nodes = 0;
		if ($defined(op_info)) {
			for (var i=0; i<op_info.length; i++) {
				if (op_info[i][0] == node.operator) {
					extra_nodes = op_info[i][1];
					break;	
				}
			}
		}

		// Destructive rebuild only if new nodes are required
		var current_children = node.operands.length;
		var required_children = info.args.length;		
		if (required_children<extra_nodes) {
			required_children=extra_nodes;
		} else {
			extra_nodes=0;
		}
		
		if (current_children != required_children) {		
			// Create children based on what we have
			nd_remove_operands(node);
			if (required_children == 0) {
				// No args? Remove operand too
				node.element_op.setStyle('display','none');
				node.element_children.setStyle('display','none');
			} else {
				// We have args? Check if we need op, and hide/show appropriately
				if (!info.op) {
					node.element_op.setStyle('display','none');
				} else {
					node.element_op.setStyle('display','');
				}
				
				// Show children and render them
				node.element_children.setStyle('display','');			
				$each(info.args, function(arg) {
					if (arg[0]!='') {					
						nd_create_placeholder(node, arg[0], arg[2]);
					} else {
						nd_create_placeholder(node);
					}
				});
				for (var i=0; i<extra_nodes; i++) {
					nd_create_placeholder(node);
				}
			}
		}
	}
}

/**
  * Detect node type by node name
  *
  * This function is used when the manual editor
  * completes editing
  */
function nd_detect_node_type(node) {		
	type_info = syntax_get_type(node);
	node.type = type_info.type;
	if (!$defined(type_info.subtype)) {
		node.subtype = '';
	} else {
		node.subtype = type_info.subtype;
	}
	node.operator='';
}

// ================[ OPERATION EDITING ]===================================================================================== //

/** 
  * Initialize operation system
  */
function nd_edit_op(node) {
	
	var op_dispose = function(e) {
		// When we loose focus, conver the element into a static one
		
		// Remove hooked events
		$(document).removeEvent('mousedown', op_dispose);

		// Dispose editting and stop event 
		nd_dispose_dropdown();		
		new Event(e).stop();
	};
	$(document).addEvent('mousedown', op_dispose);
	
	// Spawn a dropdown menu
	nd_edit_dropdown(node, node.element_op, 'OP');	
}

// ================[ NODE EDITING ]========================================================================================== //
  
/**
  * Remove a node
  */
function nd_remove_node(node,add_placeholder) {
	$each(node.operands, function(e) {
		nd_remove_node(e,false);
	});
	try { node.element_head.remove(); } catch(e) {};
	try { node.element_op.remove(); } catch(e) {};
	try { node.element_children.remove(); } catch(e) {};
	if (!add_placeholder) {
	} else {
		nd_create_placeholder(node.parent);
	}
}
  
/**
  * Spawn a node editting system
  */
var nd_edit_dispose=null; /* Mousedown Interrupt handler */

function nd_edit(node) {
	// Create the editor
	var el_txt = $(document.createElement('input'));
	var start_text = node.element_head.innerHTML;
	if (start_text=='&nbsp;') start_text='';
	el_txt.setProperties({
		'type': 'text',
		'value': start_text
	});
	
	// Reset head element
	node.element_head.setHTML('');
	node.element_head.appendChild(el_txt);

	nd_edit_dispose = function(e) {
		/* When we loose focus, convert the element into a static one */
		// Stop event
		new Event(e).stop();
		
		// Remove textbox and dropdown
		nd_dispose_dropdown();
		try { el_txt.remove(); } catch (ex) {};
		
		// Check for updates and perform one if needed
		var text = el_txt.value;
		if (start_text!=text) {
			if (text=='') text='&nbsp;';
			node.name=text;
			nd_detect_node_type(node);
			nd_update_node_view(node);
		} else {
			node.element_head.setHTML(node.name);
		}
		
		// Remove hooked events
		$(document).removeEvent('mousedown', nd_edit_dispose);
	};
	$(document).addEvent('mousedown', nd_edit_dispose);
	el_txt.addEvent('click', function(e) {
		// Interrupt click event
		new Event(e).stop();
	});
	el_txt.addEvent('mousedown', function(e) {
		// Interrupt mousedown event
		new Event(e).stopPropagation();
	});
	el_txt.addEvent('keypress', function(e) {
		// Enter equals with disposal
		if (e.keyCode == 13) { nd_edit_dispose(e); };
	});
	
	// Spawn a dropdown menu
	nd_edit_dropdown(node, el_txt, 'HEAD');
	
	// Focus editor
	el_txt.focus();
	el_txt.select();
}

// ================[ NODE CREATING ]========================================================================================== //

/**
  * Spawn a node structured element
  *
  * This function creates the structure and the first operand 
  * of a new node
  *
  * This function replaces a placeholder that was made using the 
  * nd_create_placeholder function
  */
function nd_create_node(replace_node) {
	// Create and prepare the UL and children
	var e_ul = $(document.createElement('ul'));
	var e_head = $(document.createElement('li'));
	var e_op = $(document.createElement('li'));
	var e_child = $(document.createElement('li'));
	set_class(e_ul,'node');
	set_class(e_head,'unknown');
	set_class(e_op,'unknown');
	set_class(e_child,'bordered');
	e_op.setHTML('&nbsp;');
	e_child.setHTML('');
	
	// Nest children
	e_ul.appendChild(e_head);
	e_ul.appendChild(e_op);
	e_ul.appendChild(e_child);
	
	// Replace node
	e_ul.injectBefore(replace_node.element_head);
	replace_node.element_head.remove();
	
	replace_node.element_host = e_ul;
	replace_node.element_head = e_head;
	replace_node.element_children = e_child;
	replace_node.element_op = e_op;
	
	replace_node.operands=[];
	replace_node.operation='';
	replace_node.type='unknown';

	// Bind some events
	e_head.addEvent('click', function(e){
		nd_edit(replace_node);
	});
	e_op.addEvent('click', function(e) {
		nd_edit_op(replace_node);
	});
	
	// Start editing node
	nd_edit(replace_node);
	if (replace_node.parent.subtype=='group') nd_create_placeholder(replace_node.parent);
}

/**
  * Spawn a "Create subnode" button
  *
  * This function creates a placeholder and a trigger
  * for new item spawning
  */
function nd_create_placeholder(parent, prefix, prefix_title) {
	// Create and prepare the new element
	var elm = $(document.createElement('div'));
	elm.setHTML('&nbsp;');
	set_class(elm,'addnode');
	
	// Create and prepare the new node
	var ph_node = nd_node_spawn(parent);
	ph_node.type = 'spacer';
	ph_node.element_head = elm;
	
	// Create more info, if specified
	var c_parent = parent.element_children;
	if ($defined(prefix)) {
		var pfix = $(document.createElement('div'));
		if ($defined(prefix_title)) pfix.setProperty('title',prefix_title);
		pfix.setHTML(prefix+': ');
		set_class(pfix,'prefix');		
		c_parent.appendChild(pfix);
		c_parent = pfix;
	}
	
	// Bind some events
	elm.addEvent('click', function(e){
		nd_create_node(ph_node);
	});
	
	// Store the new node
	c_parent.appendChild(elm);
}

// ================[ INITIALIZATION ]========================================================================================= //

/**
  * Initialize system
  */
$(window).addEvent('load', function(e) {
	// Get the root node
	var root_node = $('ndgroup');
	if (!root_node) { nd_error('Cannot find #ndgroup! Invalid Document used with NDScript Library!',3); return; };
	
	// Build the first node
	var node = nd_node_spawn();
	node.name = '_root';
	node.type = 'root';
	node.subtype = 'group';
	node.element_children = root_node;
	node.parent = node; /* Resurse parents if we reach zero */
	node.nestlevel=0;
	nd_root = node;
	
	// Make a "Create new node" button
	nd_create_placeholder(nd_root);
	
	// Download syntax
	syntax_download();
	
});

$(document).addEvent('keypress', function(e) {
	if (e.keyCode == 113) { /* F2 */
		var struct=nd_get_struct(nd_root);
		$('dump').setHTML(Json.toString(struct));
		var json = new Json.Remote('feed.php?a=compile').send(struct);
								   
		new Event(e).stop();
	}
});