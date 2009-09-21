
var GLUICore = new Class({Extends: GLOOElement, 
					  
	msgbox: function(text) {
		window.alert(text);
	},
	
	run: function() {
		alert('Running with parent: '+this.parent);
		if (this.parent != null) {
			alert('My parent has test='+this.parent.test);
		}
	}
	
});
