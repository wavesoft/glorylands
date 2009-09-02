// Hook chains for later-included scripts
var CBChain = new Class({
    initialize: function(){
        this.chain=[];
    },
	register: function(chain_name, callback) {
		if (!$defined(this.chain[chain_name])) { this.chain[chain_name]=[]; };
		this.chain[chain_name].push(callback);
	},
	call: function(chain_name,p1,p2,p3,p4,p5) {
		if ($defined(this.chain[chain_name])) {
			try {
				this.chain[chain_name].each(function(e){ e(p1,p2,p3,p4,p5); });
			} catch(e) {
			}
		}
	}
});
var callback = new CBChain();
