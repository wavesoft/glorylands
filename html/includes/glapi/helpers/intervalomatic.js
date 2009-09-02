// Interval-o-matic
// This class provides a cross-browser implementation of 
// setInterval / clearInterval 
// (Because IE do not support parameters on setInterval)

var GLInterval = new Class({
	intervals: [],
	last_id: 0,
	initialize: function() {
		setInterval(this.interval, 100);
	},
	add: function(func_ref, delay, param) {
		this.last_id++
		var obj = {
			'func': func_ref,
			'delay': Math.round(delay/100),
			'count': 0,
			'param': param,
			'id': this.last_id
		};
		
		this.intervals.push(obj);
		return obj.id;
	},
	erase: function(id) {
		for (var i in this.intervals) {
			if ($type(i) != 'function') {
				if (this.intervals[i].id == id) {
					this.intervals.splice(i,1);
					return;
				}
			}
		}
	},
	interval: function() {
		var intervals = Interval.intervals;
		intervals.each(function(o,i) {
			o.count++;
			if (o.count >= o.delay) {
				o.count = 0;
				o.func(o.param);
			}
		});
	}
});
var Interval = new GLInterval();
