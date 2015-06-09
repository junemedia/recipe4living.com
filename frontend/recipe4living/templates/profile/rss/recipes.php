
	<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1">
		<channel>
			<title><?= htmlentities($userRecipesTitle); ?></title>
			<link><?= SITEINSECUREURL.$userRecipesLink; ?></link>
			<description><?= htmlentities($userRecipesDescription); ?> </description>
			<language><?= $locale; ?></language>
			<lastBuildDate><?= date('r'); ?></lastBuildDate>
			<?php foreach ($userRecipes as $item) { ?>
			<item>
				<title><![CDATA[<?= htmlentities($item['title']); ?>]]></title>
				<link><?= SITEINSECUREURL.$item['link']; ?></link>
				<description><![CDATA[<?= $item['teaser']; ?>]]></description>	<? /* Should put htmlentities on this too, but need to fix Text::escape_smart_characters first */ ?>
				<pubDate><?= date('r', strtotime($item['date'])); ?></pubDate>
				<dc:creator><?= $item['author']['username']; ?></dc:creator>
				<dc:date><?= date('Y-m-d H:i:s', strtotime($item['date'])); ?></dc:date>
				<guid isPermaLink="false"><?= SITEINSECUREURL.$item['link']; ?></guid>
			</item>
			<?php } ?>
		</channel>
	</rss>
