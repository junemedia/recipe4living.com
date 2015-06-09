<html>
<head>
<title>eBook</title>
</head>
<body>
<div align="center">
<iframe src="http://www.myfree.com/cookbooks/<?php echo trim(strtolower($_GET['page'])); ?>.php?subid=<?php echo strtolower($_GET['subid']); ?>" width="900px" height="450px" frameborder="0" scrolling="auto"></iframe>
</div>
<?php
if ($_SERVER['REMOTE_ADDR'] == '198.63.247.2') {
	echo "<!-- aprilfools,cheesecake,christmas,easter,fallcooking,halloween,hanukkah,healthyfish,homemadepizza,r4lphotocontest,stirfry,superbowl,thanksgiving,ultimategrilling,porkchops,budgetfriendly,dietweightloss,valentine -->";
}
?>
<table align="center" width="500px" style="font-size:small;">
<tr>
	<td><a href="http://www.recipe4living.com/terms" target="_blank">Terms of Use</a></td>
	<td><a href="http://www.recipe4living.com/about" target="_blank">About Us</a></td>
	<td><a href="http://www.recipe4living.com/contact" target="_blank">Contact Us</a></td>
	<td><a href="http://www.recipe4living.com/privacy" target="_blank">Privacy Policy</a></td>
	<td><a href="http://www.recipe4living.com/index/unsub" target="_blank">Unsubscribe</a></td>
</tr>
</table>
</body>
</html>
