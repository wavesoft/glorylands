function iface_selecttab(tabid) {
	for (var i=1; i<=4; i++) {
		if (i!=tabid) {
			$('tb'+i).setStyle('display', 'none');
			$('tl'+i).removeClass('active');
		} else {
			if ($('tl'+i).hasClass('active')) {
				$('tb'+i).setStyle('display', 'none');
				$('tl'+i).removeClass('active');
			} else {
				$('tb'+i).setStyle('display', '');
				$('tl'+i).addClass('active');
			}
		}
	}
}
$(window).addEvent('domready', function(e) { iface_selecttab(3); });
