<script type="text/javascript"> 
__KEY = 810058309;
gCIM = null;
(function() {
	var a=window.location.search.substr(1).split('&');
	for(var i=0,b;(b=a[i]);i++){var k=b.split('=');a[k[0]]=k[1]}
	if(!a)return;
	
	if(a.c) { __KEY = a.c; }
	
	gCIM = {
		network: a.network,
		lang: a.lang || '',
		domain: a.domain,
		version: a.version || '',
		script: a.script || 'CIM',
		protocol: window.location.protocol,
		hostname: 'cim.meebo.com'
	};
	
	if(gCIM.domain) { document.domain = gCIM.domain; }
})();
var ie = false;</script> 
<!--[if IE 6]><script>ie=6</script><![endif]--> 
<!--[if gte IE 7]><script>ie=7</script><![endif]--> 
 

<script type="text/javascript"> 
if (gCIM) {
	var prefix = gCIM.protocol + '//' + gCIM.hostname + '/cim/';
	document.write('<script type="text/javascript" src="' + prefix + 'init.php?h=' + encodeURIComponent(window.location.hostname) + '&s=' + (gCIM.protocol == 'https:' ? 1 : 0) + '&c=' + __KEY + '&' + window.location.search.substring(1) + '"></scr' + 'ipt>');
}
</script> 
