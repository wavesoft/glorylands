var dropdownInfo={visible:false};
function dropdownShow(x,y,guid,position,parent_guid) {
	if (!parent_guid) parent_guid=0;
	var layer = $('dropdownLayer');
	layer.set('html', '<img src="images/UI/loading2.gif" align="absmiddle" />');
	layer.setStyles({visibility:'visible', 'left':x-5, 'top':y-5});
	dropdownInfo.visible=true;
	gloryIO('?a=interface.dropdown&guid='+guid+'&pos='+position+'&parent='+parent_guid, false, true);
	layer.focus();
	layer.addEvent('mouseleave', function() {
		disposeDropDown();								
	});
}
function disposeDropDown(){
	var layer = $('dropdownLayer');
	if (dropdownInfo.visible) {
		layer.setStyles({visibility:'hidden'});
		dropdownInfo.visible=false;
	}
}
