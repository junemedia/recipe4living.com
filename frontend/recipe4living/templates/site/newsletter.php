<?php /*<script>
	function hideItem(divID) {
		refID = document.getElementById(divID);
		refID.style.display = "none";
		if (readCookie('nlcookie') != true) {
			createCookie('nlcookie', true, 365);
		}
	}
	function createCookie(name,value,days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
		document.cookie = name+"="+value+expires+"; path=/";
	}
	function readCookie(name) {
		var nameEQ = name + "=";
		var ca = document.cookie.split(';');
		for(var i=0;i < ca.length;i++) {
			var c = ca[i];
			while (c.charAt(0)==' ') c = c.substring(1,c.length);
			if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
		}
		return ca;
	}
</script>

<div id="newsletter_signup">
<iframe style="overflow-y:hidden;overflow-x:hidden;background-color:white;color:white;border-top-left-radius: 5px;border-bottom-right-radius: 5px;border-top-right-radius: 5px;border-bottom-left-radius: 5px;" align="center" onload="NewsletterSignupOnloadCall();" id="frmid1" src="/r4l/signup.php" frameborder="0" width="298px"></iframe>
<script type="text/javascript">
function NewsletterSignupOnloadCall() {
	obj = document.getElementById('frmid1');
	obj.style.height = 0;
    obj.style.height = obj.contentWindow.document.body.scrollHeight + 20 + 'px';
    self.scroll(0,0);
}
</script>
</div>
<script>
if (readCookie('nlcookie') == 'true') {
	hideItem('newsletter_signup');
}
</script>
<br>
*/?>