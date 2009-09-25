
var GLUICore = new Class({Extends: GL.DOM.Element, 
					  
	msgbox: function(text) {
		window.alert(text);
	},
	
	run: function() {

	}
		
});

var GLUIPanel = new Class({Extends: GL.DOM.Element, 

	element: null,	
	e_title: null,
	e_text: null,

	__set: function(name, value) {
		this[name] = value;
		switch (name) {
		
			case 'left':
			case 'top':
			case 'right':
			case 'bottom':
			case 'width':
			case 'height':			
				this.element.setStyle(name, value);
				break;
			
			case 'title':				
				this.e_title.set('html', value);
				break;

			case 'text':				
				this.e_text.set('html', value);
				break;

		}
	},

	run: function() {
		
		// Predefine some variables
		if (!$defined(this.text)) this.text='';
		if (!$defined(this.title)) this.title='';
		
		var handler = new Element('div', {
			html: this.title
		});
		handler.setStyles({
			'font-family': 'Arial',
			'font-zie': '10px',
			'padding': '2px',
			'margin': '0px',
			'cursor': 'move',
			'background-color': '#333333',
			'color': '#FFFFFF'
		});
		this.e_title = handler;
				
		var text = new Element('div', {
			html: this.text
		});
		this.e_text = handler;
		
		this.element = new Element('span');
		this.element.setStyles({
			'border': 'solid 1px #666666',
			'font-family': 'Arial',
			'font-zie': '10px',
			'padding': '5px',
			'margin': '5px',
			'position': 'absolute',
			'max-width': '400px',
			'background-color': '#FFFFFF'
		});
		
		handler.inject(this.element);
		text.inject(this.element);
													   
		$(document.body).appendChild(this.element);
		
		if ($defined(this.left)) this.element.setStyle('left', this.left);
		if ($defined(this.top)) this.element.setStyle('top', this.top);
		if ($defined(this.right)) this.element.setStyle('right', this.right);
		if ($defined(this.bottom)) this.element.setStyle('bottom', this.bottom);
		
		var host = this;
		var sync_test = function() {
			var pos = host.element.getPosition();
			host.call('position', pos);
		};
		this.element.makeDraggable({handle: handler, onComplete: sync_test});
		
	}

});