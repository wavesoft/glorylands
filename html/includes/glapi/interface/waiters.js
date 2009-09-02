// =====================================================
//  Display and handle the message popup window, used
//  to entertain the user while waitting
// =====================================================

var waiterShown=false;
var waiterFX=false;
var waiterDisposer=0;

function initWaiter() {
	waiterFX=new Fx.Morph('waiter', {duration: 400, transition: Fx.Transitions.Back.easeIn,
		onComplete: function() {
			if (!waiterShown) {
				$('waiter_host').setStyles({'display':'none'});
			}
		}
	});
	$('waiter_host').setStyles({'display':'none'});
}

function showStatus(text,timeout) {
	try {
		// Cancel any waiter disposer
		if (waiterDisposer>0) {
		   clearTimeout(waiterDisposer);		
		   waiterDisposer=0;
 	    }
		if (!text) {
			if (waiterShown) {
				waiterShown=false;
				waiterFX.cancel();
				waiterFX.start({
					'opacity': 0
				});
			}
		} else {
			$('waiter').set('html', text);	
			if (!waiterShown) {
				$('waiter_host').setStyles({'display':''});
				$('waiter_host').setStyles({'z-index':lastZ});
				waiterShown=true;
				waiterFX.cancel();
				waiterFX.start({
					'opacity': 1
				});
			}
			if (timeout) waiterDisposer=setTimeout(function() { showStatus(); }, timeout);
		}
	} catch(e) {
		
	}
}