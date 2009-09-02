// ======================================================
//           Main Window area Interface management
// ======================================================

var data_cache = [];
var ex_buffer_data = ""; /* Any extra data required on Grid Area */

function initDisplayBuffer() {
	// Store any data previous initialized in design-time on buffer host
	ex_buffer_data = $('databuffer').innerHTML;	
}

function clearDisplayBuffer() {
	$('databuffer').set('html', '');
}

function displayBuffer(buffer, hLink, hImg, hText) {
	// Reset map to remove all of it's objects
	map_reset();
	wgrid_dispose();
	$('databuffer').setStyle('visibility','visible');
	$('datapane').setStyle('visibility','hidden');
	$('datahost').setStyles({
		'background-image': ''
	});	
	
	var data = buffer;
	if (hText!='') {
		if (hLink!='') {
			if (hImg!='') {
				data+='<span class="maplabel"><a class="navlink" href="javascript:gloryIO(\''+hLink+'\',false,true);"><img align="absmiddle" src="images/'+hImg+'" /></a> '+hText+'</span>';
			} else {
				data+='<span class="maplabel"><a class="navlink" href="javascript:gloryIO(\''+hLink+'\',false,true);"> '+hText+'</a></span>';
			}
		} else {
			if (hImg!='') {
				data+='<span class="maplabel"><img align="absmiddle" src="images/'+hImg+'" /> '+hText+'</span>';
			} else {
				data+='<span class="maplabel"> '+hText+'</span>';
			}
		}
	}

	data+=ex_buffer_data;
	$('databuffer').set('html', data);
}
