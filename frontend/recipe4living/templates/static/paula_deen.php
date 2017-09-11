<?php

header("Location:http://www.recipe4living.com/articles/get_cookin_with_paula_deen.htm");
exit;

?>

<div id="main-content" class="static">
		<div id="column-container">
			<div id="panel-center" class="column">
				<div class="rounded"><div class="content">
					<script type="text/javascript" language="JavaScript" src="http://getcookin.pauladeen.com/js/eqal-syndicate.php?styled=true"></script>
				</div></div>
			</div>
		
			<div id="panel-right" class="column">
				<div class="ad"><?php $this->_advert('AD_RIGHT1'); ?></div>
	
				<?php $this->_box('reference_guides', array('limit' => 10)); ?>

				<div class="ad"><?php $this->_advert('WEBSITE_RIGHT_BANNER_1'); ?></div>
			</div>
			<div class="clear"></div>
		</div>
	</div>
