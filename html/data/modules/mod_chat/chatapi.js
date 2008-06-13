// JavaScript Document

callback.register('message', function(msg) {
	// ## Handle CHAT messages ##
	if (msg[0] == 'CHAT') {
		$$('#chat_content').each(function(e) {
			if (msg[2].toLowerCase() == 'system') {
				e.innerHTML+='<br /><font color="gold">'+msg[1]+'</font>';
			} else {
				e.innerHTML+='<br /><b>['+msg[2]+']</b>'+': '+msg[1];
			}
			e.scrollTop = e.scrollHeight;	// FF
			setTimeout(function() { e.scrollTop = e.scrollHeight; }, 10); // IE
		});
	}
});

function chat_send(text) {
	gloryIO('?a=chat.send&text='+escape(text), false, true);
}