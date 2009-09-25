
/**
  * GloryLands Root Namespace  
  */
var GL = {

};

/**
  * GloryLands Elements Namespace
  */
GL.DOM = {

	elements: [],
	
	register: function(obj) {
		this.elements[obj.linkid] = obj;
	},
	
	get: function(id) {
		return this.elements[id];
	},

	Element: new Class({
		
		/**
		  * The parent object in the object tree
		  */
		parent: null,
		
		/**
		  * Linkage ID
		  */
		linkid: 0,
		
		/**
		  * Initialize a GL object
		  */
		initialize: function(id, vars) {
			for (var v in vars) {
				this[v] = vars[v];
			}
			this.linkid = id;
		},
	
		/**
		  * Run a GL object (This is called when the object is fully loaded)
		  */
		run: function() {
		},
	
		/**
		  * Call a function to my parent
		  */
		call: function(func) {
			var args=[];
			for (var i = 1, l = arguments.length; i < l; i++){
				args.push(arguments[i]);
			}
			GL.IO.call(this.linkid, func, args);
		},
	
		/**
		  * Update a local variable
		  */
		__set: function(name, value) {
			this[name] = value;
		},
	
		/**
		  * Remove a local variable
		  */
		__unset: function(name) {
			delete this[name];
		}
	
	})	
	
};

/**
  * GloryLands Depth Map Namespace
  */
GL.DepthMap = {
	
	elements: [],
	
	Map: new Class({
		
		initialize: function(range, priority) {
			if (!priority) priority=5;
			GLDepthMap.elements.push(this);
		},
		
		allocate: function() {
			
		},
		
		
		
	})
	
};

/**
  * GloryLands Communication namespace
  */
GL.IO = {
	
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

	notify: function(id, func, data) {

	},

	call: function(id, func, data) {
		var rq = new Request.JSON({
			url: 'index.php',
			method: 'post',
			noCache: true,
			headers: {
				'X-GLOO-API': 'yes'
			},
			onSuccess: function(response, text){
				// Error check
				if (response == null) {
					alert('Error while fetching sync data. Text arrived:'+"\n"+text);
					return false;
				}
				
				// Process stream messages
				try {
					if ($defined(response.msg)) GL.IO.process_event_stack(response.msg);				
				} catch(e) {
					
				}
			},
			onFailure: function(error) {
				alert('Error: '+error);
			}
		}).post({'m': 'api', 'id': id, 'f': func, 'd': data});
	}	
	
};

/**
  * GloryLands Core
  */
GL.Core = {
	
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
			GL.DOM.elements[i].run();
		}
		
		setInterval(function(host) {
			GL.IO.call(-1, 'poll',false);
		}, 5000, this);
	},		
	
	
};