<?php if(!empty($item['title']) && !empty($step)){?>
	<div id="recipe-title" class="rounded">
		<div class="content">
			<span class="item"><h1 style="font-size: 1.25em; line-height: 1.25em; color:#ffffff; margin-bottom: 0;" class="fn"><?= $step; ?></h1></span>
		</div>
	</div>
	<div id="recipe-title" style="padding-left:16px;margin-top:20px;">
		<div class="content">
			<span class="item"><h1 style="font-size: 1.25em; line-height: 1.25em; color: #ffffff; margin-bottom: 0;" class="fn"><?= $item['title']; ?></h1></span>
		</div>
	</div>
<?php }
elseif(!empty($item['title'])){?>
	<div id="recipe-title" class="rounded">
		<div class="content">
			<span class="item">
			<?php 
			$format = $this->_doc->getFormat();
			$controls = (isset($_GET['controls']))?$_GET['controls']:1;
			if ($format == 'print' && $controls == 1) {?>
				<h1 style="font-size: 1.25em; line-height: 1.25em; color: #000000; margin-bottom: 0;" itemprop="name" class="fn"><?= $item['title']; ?></h1>
			<?php }else{?>
				<h1 style="font-size: 1.25em; line-height: 1.25em; color: #ffffff; margin-bottom: 0;" itemprop="name" class="fn"><?= $item['title']; ?></h1>
			<?php }?>
			</span>
		</div>
	</div>
<?php }
elseif(!empty($step)){?>
	<div id="recipe-title" class="rounded">
		<div class="content">
			<span class="item"><h1 style="font-size: 1.25em; line-height: 1.25em; color: #ffffff; margin-bottom: 0;" class="fn"><?= $step; ?></h1></span>
		</div>
	</div>
<?php }?>
