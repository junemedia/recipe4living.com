


function navOn(to,cols) {
	if (cols == undefined) cols = 3;
	$('navpop_'+to).style.display='block';
	addClassName($('navbut_'+to),'on');
}
function navOff(to,special) {
	if (special!=undefined) {
		for (var i = 0; i < special.length; i++) {
			var el = $(special[i]);
				//alert(el.inuse);
			if (el.inuse == true) {
				return;
			}
		}
	}
	$('navpop_'+to).style.display='none';
	removeClassName($('navbut_'+to),'on');
}

addClassName = function(el,name) {
	if (el.addClassName != undefined) return el.addClassName(name);
	el.className += ' '+name;
}
removeClassName = function(el,name) {
	if (el.removeClassName != undefined) return el.removeClassName(name);
	var e = el.className.split(' ');
	var n = '';
	for (var i = 0; i < e.length; i++) {
		if (e[i] != name) n += ' '+e[i];
	}
	el.className = n;
	return false;
}

function row_hover(el,on) {
	var cn = el.className;
	var current = false;
	// Go through all class names, getting row_*
	var e = cn.split(' ');
	for (var i = 0; i < e.length; i++) {
		if (e[i].match(/^(row_|ticket_)/)) {
			current = e[i];
			continue;
		}
	}
	if (current == false) return;

	// Get colour, alt
	cn = current.split('_');
	base = cn[0]+'_'+cn[1];
	if (cn[2] == 'alt')
		base += '_'+cn[2];

	updated = base;
	if (on) {
		updated = base + '_hover';
	}

	removeClassName(el,current);
	addClassName(el,updated);
}