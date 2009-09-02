/* ==================================================================================================================================== */
/*                                             SECTION : Sprite animator
/* ==================================================================================================================================== */

var fx_sprites_animating=[];

/**
  * Sprite enterFrame Handling
  *
  * This function handles the next frame feeding
  *
  */
function fx_sprite_frame(id) {
	if (!$defined(id)) return;
	var info = fx_sprites_animating[id];
	
	// Calcuate frame
	var frame = info.frame+1;
	var max_frames = info.animation.length;
	if (frame >= max_frames) {
		frame=0;
		if (info.callback) info.callback(info.object);
		if (!info.loops) {			
		} else {
			info.loops--;
			if (info.loops == 0) {
				fx_sprite_stop(info.object);
				return;
			}
		}
	}
	info.frame=frame;
	
	var current_frame = info.animation[frame];
	var ofs_x = current_frame[0]*info.sprite_w;
	var ofs_y = current_frame[1]*info.sprite_h;	
	info.object.setStyles({
		'top': -ofs_y,
		'left': -ofs_x
	});
}

/**
  * Convert a sprited image back to it's original form
  *
  */
function fx_sprite_undo(object) {
	var pos = $(object).getStyles('left','top');
	var image = $(object).getChildren()[0];
	var host = $(object).getParent();	
	object.dispose();
	image.inject(host);
	image.setStyles({
		'left': pos.left.toInt(),
		'top': pos.top.toInt()
	});
	return image;
}


/**
  * Prepare a sprite for animation
  *
  * This function prepares a sprite for animation
  *
  */
function fx_sprite_prepare(object, x_sprites, y_sprites) {
	var style = $(object).getStyles('left','top');
	var dim = $(object).getSize();
	var pos = {x: style.left.toInt(), y: style.top.toInt()};
	if (isNaN(pos.x)) pos.x=0;
	if (isNaN(pos.y)) pos.y=0;
	var div_mask = new Element('div');	
	var info = {
		'object': object,
		'mask': div_mask ,
		'width': dim.x,
		'height': dim.y,
		'sprite_w': Math.round(dim.x/x_sprites),
		'sprite_h': Math.round(dim.y/y_sprites),
		'animation': [],
		'frame': 0,
		'timer': 0,
		'loops': 0,
		'callback': null
	};
	div_mask.setStyles({
		'width': info.sprite_w,
		'height': info.sprite_h,
		'overflow': 'hidden',
		'position': 'absolute',
		'left': pos.x,
		'top': pos.y
	});
	$(object).setStyles({
		'position': 'absolute'		
	});
	div_mask.injectBefore(object);
	$(object).injectInside(div_mask);
	fx_sprites_animating.push(info);
	return div_mask;
}

/**
  * Update a sprite for animation
  *
  * This function prepares a sprite for animation
  *
  */
function fx_sprite_update(mask_object, new_image_object, x_sprites, y_sprites) {
	var image = $(mask_object).getChildren()[0]	
	var id = fx_sprite_get_id(mask_object);	
	
	// Insert and remove DOM element in order to get
	// the proper dimensions
	new_image_object.inject(mask_object);	
	var dim = $(new_image_object).getSize();
	new_image_object.dispose();
	
	var info = fx_sprites_animating[id]; 
		
	info.width = dim.x;
	info.height = dim.y;
	info.sprite_w = Math.round(dim.x/x_sprites);
	info.sprite_h = Math.round(dim.y/y_sprites);
	
	mask_object.setStyles({
		'width': info.sprite_w,
		'height': info.sprite_h
	});
	image.setStyles({
		'left': 0,
		'top': 0
	});

	// Instead of cloning the image, update only the SRC
	image.src = new_image_object.src;

	fx_sprites_animating[id] = info;
	return mask_object;
}

/**
  * Return the ID of a sprite
  *
  * This function returns the ID of a sprite initialized with fx_sprite_prepare
  *
  */
function fx_sprite_get_id(object) {
	for (var i=0;i<fx_sprites_animating.length;i++) {
		if (fx_sprites_animating[i].object == object) {
			return i;
		} else if (fx_sprites_animating[i].mask == object) {
			return i;
		}
	}
	return -1;
}

/**
  * Start sprite animation
  *
  * This function starts the sprite animation
  *
  */
function fx_sprite_animate(object,frame_rate,animation,loops,end_callback) {
	var i = fx_sprite_get_id(object);
	if (i<0) return false;
	var info = fx_sprites_animating[i];
	info.animation = animation;
	info.frame = 0;
	info.loops = loops;
	info.callback = end_callback;
	if (info.timer!=0) Interval.erase(info.timer);
	info.timer = Interval.add(fx_sprite_frame, (1000/frame_rate), i);
	return true;
}

/**
  * Start sprite animation
  *
  * This function starts the sprite animation
  *
  */
function fx_sprite_stop(object, frame) {
	var i = fx_sprite_get_id(object);
	if (i<0) return false;
	
	var info = fx_sprites_animating[i];
	Interval.erase(info.timer);
	if (!frame) frame = info.animation[0];
	var ofs_x = frame[0]*info.sprite_w;
	var ofs_y = frame[1]*info.sprite_h;	
	info.object.setStyles({
		'top': -ofs_y,
		'left': -ofs_x
	});	
	return true;
}
