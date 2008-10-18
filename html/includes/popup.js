// PopUp Window provider
var popUpWin=0;

function popUpWindow(URLStr, width, height, sizable, scrollable) {
  if(popUpWin) {
    if(!popUpWin.closed) popUpWin.close();
  }
  var left = (screen.width - width)/2;
  var top = (screen.height - height)/2;
  
  var v_sizable = 'yes';
  var v_scrollable = 'yes';
  if (!sizable) v_sizable='no';
  if (!scrollable) v_scrollable='no';

  popUpWin = open(URLStr, 'popUpWin', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars='+v_scrollable+',resizable='+v_sizable+',copyhistory=yes,width='+width+',height='+height+',left='+left+', top='+top+',screenX='+left+',screenY='+top+'');
}

