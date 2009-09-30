
/**
  * GloryLands Root Namespace  
  */
var GL = {
	
	version: '0.1b'
	
};

/**
  * GloryLands Elements Namespace
  */
GL.DOM = {

	/**
	  * The elements that are registered on GL DOM
	  */
	elements: [],
	
	/**
	  * Stack a new element on the GL DOM
	  */
	register: function(obj) {
		this.elements[obj.linkid] = obj;
	},
	
	/**
	  * Get an element by it's link ID
	  */
	get: function(id) {
		return this.elements[id];
	},

	/**
	  * Basic DOM Element Class
	  *
	  * Every DOM element that provides some functionality should extend this class.
	  */
	Element: new Class({
		
		/**
		  * The parent object in the object tree
		  */
		parent: null,
		
		/**
		  * Link ID
		  *
		  * This ID is the same between the JS and the
		  * respective PHP class. It is used for
		  * variable synchronization.
		  *
		  */
		linkid: null,
		
		/**
		  * Initialize a GL object
		  */
		initialize: function(vars, id) {
			if (!id && (id !== 0)) {
				// Instanced manually (JS Request)
				for (var v in vars) {
					this[v] = vars[v];
				}
				this._run();
			} else {
				// Instanced by the GL.DOM.Register() (PHP Request)
				for (var v in vars) {
					this[v] = vars[v];
				}
				this.linkid = id;
			}
		},

		/**
		  * Forward a function call to my PHP instance
		  *
		  * This function is blocking and waits for a response before 
		  * continues with the script.
		  */
		call: function(func) {
			if (this.linkid == null) return;
			var args=[];
			for (var i = 1, l = arguments.length; i < l; i++){
				args.push(arguments[i]);
			}
			GL.IO.call(this.linkid, func, args, true);
		},

		/**
		  * Notify a function call to my PHP instance
		  *
		  */
		notify: function(func) {
			if (this.linkid == null) return;
			var args=[];
			for (var i = 1, l = arguments.length; i < l; i++){
				args.push(arguments[i]);
			}
			GL.IO.call(this.linkid, func, args);
		},
		
		/**
		  * Run a GL object (This is called when the object is fully loaded)
		  */
		_run: function() {
			// (Inheritable)
		},
	
		/**
		  * Update a local variable
		  */
		_set: function(name, value) {
			this[name] = value;
		},
	
		/**
		  * Remove a local variable
		  */
		_unset: function(name) {
			delete this[name];
		}
	
	})	
	
};

/**
  * GloryLands Depth Map Namespace
  */
GL.DepthMap = {
	
	/**
	  * The registered depth map management classes
	  */
	managers: [],
	
	/**
	  * Depth manager sorting function	  
	  */
	sort_priority: function(a, b) {
		return a.priority - b.priority;
	},
	
	/**
	  * Allocate a new depth range manager
	  */
	allocate: function(range, priority, prototype) {
		if (!priority) priority=5;
		if (!prototype) prototype=GL.DepthMap.Manager;
		
		// Create a new manager based on the prototype provided and store it
		// on the local manager store.
		var manager= new prototype(range, priority);
		GL.DepthMap.managers.push(manager);
		
		// Sort depth map objects
		GL.DepthMap.managers.sort(GL.DepthMap.sort_priority);
		for (var i=0; i<GL.DepthMap.managers.length; i++) {
			GL.DepthMap.managers[i].update(i);
		}

		// Returnt he manager instance
		return manager;
	},

	/**
	  * Allocate a new 3D Depth range manager
	  */
	allocate_3d: function(max_x, max_y, max_z, priority, prototype) {
		if (!priority) priority=5;
		if (!prototype) prototype=GL.DepthMap.Manager;
		
		// Calculate the range
		if (!max_x) max_x=1;
		if (!max_y) max_y=1;
		if (!max_z) max_z=1;
		var range=max_y*max_x*max_z;
		
		// Create the manager
		var manager = GL.DepthMap.allocate(range, priority, prototype);
		
		// Store the manager information
		manager.max_x = max_x;
		manager.max_y = max_y;
		manager.max_z = max_z;
		
		// Returnt the manager instance
		return manager;
	},

	/**
	  * The basic depth map manager class
	  *
	  * This class provides depth swapping, groupping and priorities
	  * in order to implement a developer-friendly depth managing system.
	  */
	Manager: new Class({

		range: 0,
		priority: 5,		
		rootid: 0,
		lastid: 0,
		bindings: [],

		initialize: function(range, priority) {
			this.range = range;
			this.priority = priority;
		},

		/**
		  * Bind an element on an index
		  */
		bind: function(element, index) {
			if (!index) index=this.get();
			var ref = index-this.rootid;
			this.bindings.push({
				'elm': element,
				'ref': ref
			});
			element.setStyle('z-index', index);
		},

		/**
		  * Update my root index
		  * (Called after a re-arrange)
		  */
		update: function(my_index) {
			// Update the root index
			if (my_index == 0) {
				this.rootid = 0;
			} else {
				var previous = GL.DepthMap.managers[my_index-1];
				this.rootid = previous.rootid + previous.range;
			}
			
			// Update binded elements
			for (var i=0; i<this.bindings.length; i++) {
				this.bindings[i].elm.setStyle('z-index', this.rootid+this.bindings[i].ref);
			}
		},

		/**
		  * Get the top of the currently used depth indexes
		  */
		top: function() {
			return this.rootid+this.lastid;
		},

		/**
		  * Get the maximum depth index
		  */
		max: function() {
			return this.rootid+this.range;
		},

		/**
		  * Get the minimum depth index
		  */
		max: function() {
			return this.rootid;
		},

		/**
		  * Get a free depth index
		  *
		  * If the depth range is exhausted, the depth counter
		  * is re-set and the same depth IDs will be given.
		  *
		  * This should not be a problem since most of the browsers
		  * will automatically re-arange the depths as they should
		  *  and preserve the layout.
		  */
		get: function() {
			this.lastid++;
			if (this.lastid>this.range) this.lastid=0;
			var id = this.rootid+this.lastid;
			return id;
		},

		/**
		  * Faux-3D Support
		  *
		  * The following functions and variables provides a simple way to access Faux-3D 
		  * index calculations based on an object's X,Y,Z and Height information
		  */
		max_x: 0,
		max_y: 0,
		max_z: 0,
			
		/**
		  * 3D depth calculation mapping
		  */
		get_3d: function(x,y,z,h) {
			if (!h) h=0;
			if (!z) z=0;
			return (y*max_x)+x-((z+h)*max_x);			
		}		

	}),
	
};

/**
  * GloryLands Communication namespace
  */
GL.IO = {		
	
	ani_glass: null,
	
	hourglass: function(visible) {
		if (GL.IO.ani_glass == null) {
			// Create a new image element
			GL.IO.ani_glass = new Element('img', {src: '/gl-sf/images/UI/hourglass.gif'});
			
			// Add it into the body
			GL.IO.ani_glass.inject($(document.body));
			GL.IO.ani_glass.setStyles({
				'position': 'absolute',
				'right': 5,
				'top': 5,
				'display': 'none'
			});
			
			// Make the hourglass top-most
			GL.Core.topmost.bind(GL.IO.ani_glass);
		}
		
		// Check what to do with the hourglass
		if (visible) {
			GL.IO.ani_glass.setStyle('display', 'block');
		} else {
			GL.IO.ani_glass.setStyle('display', 'none');			
		}
	},
	
	handle_event: function(ev) {
		if ($defined(ev.id) && $defined(ev.c) && $defined(ev.p)) {
			var obj = GL.DOM.get(ev.id);
			if ($defined(obj[ev.c])) obj[ev.c].run(ev.p, obj);
		}
	},
	
	process_event_stack: function(stack) {
		for (var i=0; i<stack.length; i++) {
			GL.IO.handle_event(stack[i]);
		}
	},

	active_calls: 0,
	
	call: function(id, func, data, blocking) {
		if (!blocking) blocking=false;
		GL.IO.hourglass(true);
		GL.IO.active_calls++;
		var rq = new Request.JSON({
			url: 'index.php',
			method: 'post',
			noCache: true,
			async : !blocking,
			headers: {
				'X-GLOO-API': 'yes'
			},
			onSuccess: function(response, text){
				if (GL.IO.active_calls-- == 1) GL.IO.hourglass(false);

				// Error check
				if (response == null) {
					alert("Unable to analyze incoming API Data format\nText arrived:\n"+text);
					return false;
				}
				
				// Process stream messages
				try {
					if ($defined(response.msg)) GL.IO.process_event_stack(response.msg);				
				} catch(e) {
					
				}
			},
			onFailure: function(error) {
				if (GL.IO.active_calls-- == 1) GL.IO.hourglass(false);
				alert('Error: '+error);
			}
		}).post({'m': 'api', 'id': id, 'f': func, 'd': data});
	}	
	
};

/**
  * GloryLands Core
  */
GL.Core = {
	
	topmost: new GL.DepthMap.allocate(100, 10),
	
	addslashes: function(str) {
		str=str.replace(/\\/g,'\\\\');
		str=str.replace(/\'/g,'\\\'');
		str=str.replace(/\"/g,'\\"');
		str=str.replace(/\0/g,'\\0');
		return str;
	},

	stripslashes: function(str) {
		str=str.replace(/\\'/g,'\'');
		str=str.replace(/\\"/g,'"');
		str=str.replace(/\\0/g,'\0');
		str=str.replace(/\\\\/g,'\\');
		return str;
	},
	
	run: function() {
		for (var i=0; i<GL.DOM.elements.length; i++) {
			GL.DOM.elements[i]._run();
		}
		
		setInterval(function(host) {
			GL.IO.call(-1, 'poll',false);
		}, 5000, this);
	},		
	
	
};