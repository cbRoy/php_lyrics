var hidden = false;
function getElementsByClass(searchClass, domNode, tagName) {
	if (domNode == null) domNode = document;
	if (tagName == null) tagName = '*';
	var el = new Array();
	var tags = domNode.getElementsByTagName(tagName);
	var tcl = " "+searchClass+" ";
	for(i=0,j=0; i<tags.length; i++) {
		var test = " " + tags[i].className + " ";
		if (test.indexOf(tcl) != -1)
		el[j++] = tags[i];
	}
	return el;
}

function toggle(clicked,host){
	var div = getElementsByClass("lyrics",null,"div");
	for(var i=0; i<div.length; i++){
		div[i].style.display = 'none';
	}
	var show_div = document.getElementById(host);
	show_div.style.display = 'block';
	var hosts = getElementsByClass("host",null,"a");
	for(var i=0; i<hosts.length; i++){
		hosts[i].style.fontWeight='normal';
	}
	clicked.style.fontWeight='bold';
}
