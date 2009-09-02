/**
  * Stack a map object animation/action
  *
  * This function is used to stack multiple animations
  * and execute them in the correct order.
  *
  */
var map_fxstack_id=[];
var map_fxstack=[];
function map_get_uid(uid) {
	if ($defined(map_fxstack_id[uid])) {
		return map_fxstack_id[uid];
	} else {
		return -1;
	}
}
function map_map_id(uid) {
	var i = map_fxstack_id.indexOf(uid);
	if (i < 0) {
		map_fxstack.push([]);
		map_fxstack_id[uid] = map_fxstack.length-1;
		return map_fxstack.length-1;
	} else {
		return map_fxstack_id[uid];
	}
}
function map_stack_movefx(uid, old_data, data, x, y, zindex, size) {
	//$debug('Stacking object '+uid+' Fx');
	
	// If we are already in position, this is not needed
	var tollerance = 2; // Pixel
	pos = old_data.object.getStyles('left','top');
	pos.left = pos.left.toInt();
	pos.top = pos.top.toInt();
	if ((Math.abs(pos.left-x)<tollerance) && (Math.abs(pos.top-y)<tollerance)) {
		//$debug('Positions same. Rejected '+data.guid);
		return;
	}
	
	var i = map_fxstack_id.indexOf(uid);
	if (i<0) {
		map_fxstack_id.push(uid);
		map_fxstack.push([ [uid, old_data, data, x, y, zindex, size] ]);
		map_stack_movefx_next(uid);
		//$debug('[SFX] New stack #'+(map_fxstack_id.length-1)+' for '+uid);
	} else {
		map_fxstack[i].push([uid, old_data, data, x, y, zindex, size]);
		//$debug('[SFX] Expandig stack #'+i+' for '+uid);		
	}
}
function map_stack_movefx_next(uid) {
	var i = map_fxstack_id.indexOf(uid);
	if (i<0) return;
	
	if (map_fxstack[i].length == 0) {
		map_fxstack_id.splice(i,1);		
		map_fxstack.splice(i,1);
		//$debug('[SFX] No more for stack #'+i+' of '+uid);
	} else {
		//$debug('[SFX] Processing stack #'+i+' of '+uid);
		var stack = map_fxstack[i].shift();
		map_stack_movefx_thread(stack[0],stack[1],stack[2],stack[3],stack[4],stack[5],stack[6]);
	}
}
function map_stack_movefx_thread(uid, old_data, data, x, y, zindex, size) {
	// If we have transition, use them.
	if (data.fx_move) {
		switch (data.fx_move) {
			case 'slide':
				var px_transition=new Fx.Morph(old_data.object, {duration: 800, unit: 'px', transition: Fx.Transitions.linear});
				var z_transition=new Fx.Morph(old_data.object, {duration: 800, unit: '', transition: Fx.Transitions.linear});				
				z_transition.start({'z-index':zindex});
				px_transition.start({
						'top': y
				}).chain(function() {
						px_transition.start({'left': x});
				}).chain(function() {
						map_stack_movefx_next(uid);
				});
				break;

			case 'bounce':
				var px_transition=new Fx.Morph(old_data.object, {duration: 800, unit: 'px', transition: Fx.Transitions.Elastic.easeOut});
				px_transition.start({
						'left': x,
						'top': y
				}).chain(function() {
						map_stack_movefx_next(uid);
				});
				old_data.object.setStyles({
						'z-index':zindex
				});
				break;

			case 'fade':
				var imfx=new Fx.Morph(old_data.object, {wait: false, duration: 400, transition: Fx.Transitions.Quad.easeOut});
				imfx.start({
					'opacity':0
				}).chain(function(){
					old_data.object.setStyles({
						'left': x,
						'top': y,
						'z-index': zindex
					});			
					imfx.start({
						'opacity':1
					}).chain(function(){
						map_stack_movefx_next(uid);
					})
				});				
				break;
				
			case 'path':
				if ($defined(data.fx_path)) {
					//$debug('Pathwalking '+data.guid);
					map_fx_pathmove(old_data, data.fx_path, function(){
						//$debug('*** CALLBACK ***');
						map_stack_movefx_next(uid)
					});
					
					// No need to keep the grid in memory any more
					delete data.fx_path;
					break;
				}
				
			default:
				old_data.object.setStyles({
					'left': x,
					'top': y,
					'z-index': zindex
				});
				map_stack_movefx_next(uid)
		}		
	
	// Elseways, just update the object
	} else {		
		// Update object		
		old_data.object.setStyles({
			'left': x,
			'top': y,
			'z-index': zindex
		});
		map_stack_movefx_next(uid)
	}
}