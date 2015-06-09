
	<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1">
		<channel>
			<title><?= htmlentities($userCommentsTitle); ?></title>
			<link><?= SITEINSECUREURL; ?></link>
			<description><?= htmlentities($userCommentsDescription); ?> </description>
			<language><?= $locale; ?></language>
			<lastBuildDate><?= date('r'); ?></lastBuildDate>
			<?php foreach ($userComments['review'] as $item) { ?>
			<item>
				<title><![CDATA[<?= htmlentities('Recipe: '.$item['item']['title']); ?>]]></title>
				<link><?= SITEINSECUREURL.$item['item']['link']; ?></link>
				<description><![CDATA[<?= $item['body']; ?>]]></description>
				<pubDate><?= date('r', strtotime($item['date'])); ?></pubDate>
				<dc:creator><?= $user['username']; ?></dc:creator>
				<dc:date><?= date('Y-m-d H:i:s', strtotime($item['date'])); ?></dc:date>
				<guid isPermaLink="false"><?= SITEINSECUREURL.$item['item']['link']; ?></guid>
			</item>
			<?php } ?>
		</channel>
	</rss>
