
	<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1">
		<channel>
			<title><?= htmlentities($userArticleImagesTitle); ?></title>
			<link><?= SITEINSECUREURL; ?></link>
			<description><?= htmlentities($userArticleImagesDescription); ?> </description>
			<language><?= $locale; ?></language>
			<lastBuildDate><?= date('r'); ?></lastBuildDate>
			<?php foreach ($userArticleImages as $item) { ?>
			<item>
				<title><![CDATA[<?= htmlentities('Recipe: '.$item['item']['title']); ?>]]></title>
				<link><?= SITEINSECUREURL.$item['link']; ?></link>
				<dc:creator><?= $user['username']; ?></dc:creator>
				<guid isPermaLink="false"><?= SITEINSECUREURL.$item['link']; ?></guid>
			</item>
			<?php } ?>
		</channel>
	</rss>
