<?php require_once(dirname(__FILE__) . "/giveaway_offer.php");?>

<?php

function getRequestArray($queryString){
    $r = array();
    $a = explode("&",$queryString);
    foreach($a as $k=>$v){
        $row = explode("=", $v);
        $r[$row[0]] = $row[1];
    }
    return $r;
}

?>
	<div id="main-content" class="static">
		<div id="column-container">
			<div id="panel-center" class="column" >
				<div id="contact" class="rounded" style="width:762px;font: Arial, Helvetica, sans-serif;">
					<h2 style="font-size:15px;background-color:#ED1D24;color:white;height:27px;text-aling:left;padding-left:10px;padding-top:5px;">Thank you for entering. Good luck!</h2>
					<div>
						<table border="0">
							<tr>
								<td width="412px">
									<!--<h1 style="color: #333;padding-left:10px;max-width:380px;">Contest - Enter to win a $200 Espresso Machine from Bialetti!</h1>
									<p style="font: 12px Arial, Helvetica, sans-serif;padding-left:10px;max-width:360px;">
									We're giving away a premium espresso machine from <a href="http://www.bialetti.com" target="_blank">Bialetti</a> 
									just for signing up for the Recipe4Living Daily Recipes newsletter! You'll get your caffeine fix 
									with his high-end machine. Enter your information below for a chance to win! Winnter will be 
									chosen on October 31, 2013.
									</p>-->
									<div style="height:730px;">								
									<h3 style="margin:15px;">
					Thank you for entering. Good luck!<br><br>
					You will receive a welcome e-mail confirming your newsletter subscription soon.
					</h3><script>dataLayer.push({'event': 'giveawayrecipe4living'});</script>
									</div>
								</td>
								<!--<td width="350px" valign="top"><img src="http://pics.recipe4living.com/giveaway/082713-R4L-bialetti_giveaway.jpg"></img></td>-->
							</tr>
                            <tr><td>
                                    <?php
                                        $queryString = $_SERVER['QUERY_STRING'];
                                        $params = getRequestArray($queryString);
                                        //print_r($params); exit;
                                    
                                        if(isset($params['offid'])){
                                            echo "<div>" . $offer[$params['offid']]['subcampaignId'] . "</div>";
                                            echo "<div>" . $offer[$params['offid']]['desc'] . "</div>";
                                        }else{
                                            echo "<!-- no offid supplied -->";
                                        }
                                    ?>
                            </td></tr>
						</table>
					</div>
				</div>
			</div>
			<div id="panel-left" class="column">
				<?php $this->leftnav(); ?>
			</div>
			<div class="clear"></div>
		</div>
	</div>

<?php if (strtolower(trim($_GET['cp'])) == 'y') { ?>
<iframe src="http://sinettrk.com/p.ashx?o=13290&t=<?php echo trim($_GET['e']); ?>" height="1" width="1" frameborder="0"></iframe>
<iframe src="http://sinettrk.com/p.ashx?o=13304&t=<?php echo trim($_GET['e']); ?>" height="1" width="1" frameborder="0"></iframe>
<iframe src="http://sinettrk.com/p.ashx?o=13324&t=<?php echo trim($_GET['e']); ?>" height="1" width="1" frameborder="0"></iframe>
<iframe src="http://sinettrk.com/d.ashx?ckm_offer_id=13419&email_address=<?php echo trim($_GET['e']); ?>&first_name=<?php if(isset($_GET['fname']))echo trim($_GET['fname']); ?>" height="1" width="1" frameborder="0"></iframe>
<?php } else { ?>
<!-- no cake pixel since it's dupes -->
<?php } ?>



