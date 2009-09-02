// ======================================================
//  Dedicated Data Window (DDW) Functions
// ======================================================

var ddw_visible = false;

function ddwin_change(url) {
	var prepare = ddwin_prepare();
	prepare.chain = function() {
		gloryIO(url,false,true);
	};
}

function ddwin_dispose() {
	if (ddw_visible) {
		var popup = new Fx.Morph('dd_popup', {duration: 500, transition: Fx.Transitions.Cubic.easeOut});
		var content = new Fx.Morph('dd_content', {duration: 500, transition: Fx.Transitions.Cubic.easeOut});
		var iPopup = $('dd_popup');
		var iHost = $('dd_host');
		var iContent = $('dd_content');

		content.start({
			'opacity': 0
		}).chain(function() {
			iContent.set('html', '');
			popup.start({
				'opacity': 0,
				'width': 10,
				'height': 10
			}).chain(function() {
				iHost.setStyles({'display':'none'});
			});
		});
		ddw_visible = false;
	}
}

function ddwin_show(width, height, text) {
	var iPopup = $('dd_popup');
	var iContent = $('dd_content');
	var iHost = $('dd_host');
	var popup = new Fx.Morph('dd_popup', {duration: 500, transition: Fx.Transitions.Cubic.easeOut});
	var content = new Fx.Morph('dd_content', {duration: 500, transition: Fx.Transitions.Cubic.easeOut});
	// If DDWin is visible, perform a transformation of the window
	if (ddw_visible) {
		content.start({
			'opacity': 0
		}).chain(function() {
			popup.start({
			'height': height,
			'width': width,
			'opacity': 1
			}).chain(function() {
				// Update content
				iContent.set('html', "<div style=\"position:relative; width:100%; height:100%\"><span class=\"dd_head\"><a href=\"javascript:ddwin_dispose()\">X</a></span>"+text+"</div>");
				content.start({
					'opacity': 1
				});
			});
		});
	
	// If not visible, prepare a small window and fade in
	} else {
		iHost.setStyles({'display':'', 'z-index':lastZ++});
		iPopup.setStyles({'opacity': 0, 'width': 10, 'height': 10});
		iContent.setStyles({'opacity': 0, 'display': 'none'});
		iContent.set('html', "<div style=\"position:relative; width:100%; height:100%\"><span class=\"dd_head\"><a href=\"javascript:ddwin_dispose()\">X</a></span>"+text+"</div>");
		popup.start({
			'opacity': 1,
			'width': width,
			'height': height
		}).chain(function() {
			iContent.setStyles({'display':''});
			content.start({
				'opacity': 1
			});
		});
		ddw_visible = true;
	}
}

function ddwin_prepare() {
	var iPopup = $('dd_popup');
	var iContent = $('dd_content');
	var iHost = $('dd_host');
	var popup = new Fx.Morph('dd_popup', {duration: 500, transition: Fx.Transitions.Cubic.easeOut});
	var content = new Fx.Morph('dd_content', {duration: 500, transition: Fx.Transitions.Cubic.easeOut});
	var ret_chain = {chain:null}; // We use objects to perform by reference alteration after the function has returned
	
	// If DDWin is not visible, do a clean fade in
	if (!ddw_visible) {
		iHost.setStyles({'display':'', 'z-index':lastZ++});
		iPopup.setStyles({opacity: 0});
		iContent.setStyles({opacity: 1});
		iContent.set('html', 'Please&nbsp; <img src="images/UI/loading2.gif" align="absmiddle" /> &nbsp;wait...');
		ddw_visible = true;
		ret_chain=popup.start({
			'opacity': 1,
			'width': 120,
			'height': 20
		}).chain(ret_chain.chain);
		
	// If already exists, fade out the content and reset it
	} else {
		content.start({
			'opacity': 0		  
		}).chain(function () {
			iContent.set('html', 'Please&nbsp; <img src="images/UI/loading2.gif" align="absmiddle" /> &nbsp;wait...');
			content.start({
				'opacity': 1			  
			});
			ret_chain=popup.start({
				'width': 120,
				'height': 20
			}).chain(ret_chain.chain);
		});
	}
	
	return ret_chain;
}

