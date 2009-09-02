// JavaScript Document
var mf_counter_url='';
var mf_counter_element=null;
var mf_coutner_count=0;

function mf_count_dispose() {
	$('mf_counter_host').setStyles({
		'visibility': 'hidden'
	});
	$(document).removeEvent('mouseup', mf_count_dispose);
}

function mf_count_items(element, url, count) {
	mf_coutner_count=count;
	mf_counter_url=url;
	for (var i=1; i<21; i++) {
		if (i<=count) {
			$('fm_count_'+i).setProperty('class','');
		} else {
			$('fm_count_'+i).setProperty('class','disabled');
		}
	}
	
	var parent_pos = $(element).getParent().getParent().getPosition();
	var pos = $(element).getPosition();
	$('mf_counter_host').setStyles({
		'left': pos.x-parent_pos.x+200,
		'top': pos.y-parent_pos.y+50,
		'visibility': 'visible'
	});
	$('mf_counter_host').addEvent('mouseup', function(e){
		var e = new Event(e);
		e.stop();
	});
	$(document).addEvent('mouseup', mf_count_dispose);
}
function mf_count_set(count) {
	if (count>mf_coutner_count) count=mf_coutner_count;
	gloryIO(mf_counter_url+'&count='+count);
}
function mf_count_move(count) {
	if (count>mf_coutner_count) count=mf_coutner_count;
	$('mf_counter').set('html', count);
	for (var i=1; i<21; i++) {
		if (i<=count) {
			$('fm_count_'+i).setProperty('class','active');
		} else {
			if (i>mf_coutner_count) {
				$('fm_count_'+i).setProperty('class','disabled');
			} else {
				$('fm_count_'+i).setProperty('class','');
			}
		}
	}
}