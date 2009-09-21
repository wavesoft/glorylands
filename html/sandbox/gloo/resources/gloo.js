
var GLOOElements = {};
var GLOOElement = new Class({
	
	/**
	  * The parent object in the object tree
	  */
	parent: null,
	
	/**
	  * Linkage ID
	  */
	linkid: 0,
	
	/**
	  * Initialize a GLOO object
	  */
	initialize: function(id, vars) {
		for (var v in vars) {
			this[v] = vars[v];
		}
		this.linkid = id;
	},

	/**
	  * Run a GLOO object (This is called when the object is fully loaded)
	  */
	run: function() {
	}
});

var GLOOCore = {
	
	objects: [],
	
	register: function(obj) {
		this.objects[obj.linkid] = obj;
	},
	
	get: function(id) {
		return this.objects[id];
	},
	
	run: function() {
		for (var i=0; i<this.objects.length; i++) {
			this.objects[i].run();
		}
	}
	
};