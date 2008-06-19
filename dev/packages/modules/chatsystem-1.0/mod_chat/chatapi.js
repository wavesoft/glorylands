// JavaScript Document

callback.register('message', function(msg) {
	if (msg[0] == 'CHAT') {
		$$('#chat_content').each(function(e) {
			e.innerHTML+='<br /><b>['+msg[2]+']</b>'+': '+msg[1];
			setTimeout(function() { e.scrollTop = e.scrollHeight; }, 10);
		});
	}
});

function chat_send(text) {
	gloryIO('?a=chat.send&text='+escape(text), false, true);
}