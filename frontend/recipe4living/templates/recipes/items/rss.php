
	<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1">
		<channel>
			<title>Recipe4living - <?= htmlentities($listingTitle); ?></title>
			<link><?= SITEINSECUREURL.$this->_baseUrl; ?></link>
			<description><?= htmlentities($description); ?> </description>
			<language><?= $locale; ?></language>
			<lastBuildDate><?= date('r'); ?></lastBuildDate>
			<?php foreach ($items as $item) { ?>
			<item>
				<title><![CDATA[<?= $item['title']?>]]></title>
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
