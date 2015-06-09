<div id="search_block" class="rounded-300-outline block">
	<div class="top"></div>
	<div class="content">
		<div class="header">
			<div class="title"><h2>Search Blog</h2></div>
		</div>
			<form method="get" id="searchform" class="search" action="<?php bloginfo('home'); ?>/">
			<div>
				<div class="input-wrapper">
				<input value="<?php the_search_query(); ?>" name="s" id="s"  type="text" class="textinput" autocomplete="off" alt="Enter search keywords..." />
				</div>
				<button class="but-find" value="Search" type="submit"></button>
			</div>
			</form>
		<div class="clear"></div>
	</div>
	<div class="bot"></div>
</div>