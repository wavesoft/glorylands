// Image preloading wrapper (keeps all the images together)
var GLImageLoader = new Class({
	initialize: function() {
		this.elements = {};
	},
	clear: function() {
		for (var i=0; i<this.elements.length; i++) {
			try {
				this.elements[i].dispose();
			} catch(e) {
			}
		}
		this.elements = [];
	},
	get: function(image) {
		var img = this.elements[image].clone();
		if (!img) {
			img = new Element('img', {src: image});
			$debug('!!! Dummyload of image '+image);
		}
		return img;
	},
    preload: function(image_list, opt){
		var images = Asset.images(image_list, opt);
		for (var i=0; i<images.length; i++) {
			this.elements[image_list[i]] = images[i];
		}
	}
});
var ImageLoader = new GLImageLoader();
