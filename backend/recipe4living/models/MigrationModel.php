<?php

/**
 *	Migration
 *
 *	@package BluApplication
 *	@subpackage BackendModels
 */
class ClientBackendMigrationModel extends BackendMigrationModel
{
	/**
	 *	Datasource
	 *
	 *	@access protected
	 *	@var MSSQLDatabase
	 */
	protected $_source;
	
	/**
	 *	Content-to-article mapping
	 *
	 *	@access protected
	 *	@var array
	 */
	protected $_contentArticleMapping;
	
	/**
	 *	Do import
	 *
	 *	@access public
	 *	@param MSSQLDatabase Source
	 *	@return bool Success
	 */
	public function import($source)
	{
		$this->_source = $source;
		
		//$this->_createUserTables();
		//$this->_migrateUserData();
		
		//$this->_createArticleTables();
		//$this->_migrateArticleData();
		
		//$this->_createCommentTables();
		//$this->_migrateCommentData();
		
		//$this->_createMetaTables();
		//$this->_migrateMetaData();
		
		//$this->_createReportsTables();
		
		return true;	// Lies.
	}
	
	/**
	 *	Prepare meta data
	 *
	 *	@access protected
	 */
	protected function _createMetaTables()
	{
		// Meta groups
		$query = 'CREATE TABLE `metaGroups` (
			`id` INT(8) NOT NULL AUTO_INCREMENT,
			`internalName` VARCHAR(255) NOT NULL COMMENT "Internal name",
			`sequence` INT(8) DEFAULT 0 COMMENT "Priority",
			PRIMARY KEY (`id`)
		) DEFAULT CHARSET="utf8" ENGINE="MyISAM" COMMENT "Groups of meta values."';
		$this->_db->setQuery($query);
		$this->_db->query();
		
		$query = 'CREATE TABLE `languageMetaGroups` (
			`id` INT(8) NOT NULL,
			`lang` VARCHAR(3) DEFAULT "EN" COMMENT "Language code",
			`name` VARCHAR(255),
			`description` TEXT,
			`slug` VARCHAR(255) NOT NULL,
			PRIMARY KEY (`id`, `lang`),
			UNIQUE INDEX `uniqueSlug` (`lang`, `slug`)
		) DEFAULT CHARSET="utf8" ENGINE="MyISAM" COMMENT "Text for meta groups."';
		$this->_db->setQuery($query);
		$this->_db->query();
		
		// Meta values
		$query = 'CREATE TABLE `metaValues` (
			`id` INT(8) NOT NULL AUTO_INCREMENT,
			`internalName` VARCHAR(255) NOT NULL COMMENT "Internal name",
			`groupId` INT(8) NOT NULL COMMENT "Belonging meta group.",
			`imageName` VARCHAR(255) COMMENT "Basename of file",
			`sequence` INT(8) DEFAULT 0 COMMENT "Priority",
			`display` TINYINT(1) NOT NULL DEFAULT 1 COMMENT "Whether displayed or not",
			`default` TINYINT(1) NOT NULL DEFAULT 0 COMMENT "Shows on first filter load",
			`featured` TINYINT(1) NOT NULL DEFAULT 0,
			`internal` TINYINT(1) NOT NULL DEFAULT 0 COMMENT "Whether value is for internal use only"
			PRIMARY KEY (`id`),
			INDEX (`sequence`)
		) DEFAULT CHARSET="utf8" ENGINE="MyISAM"';
		$this->_db->setQuery($query);
		$this->_db->query();
		
		$query = 'CREATE TABLE `languageMetaValues` (
			`id` INT(8) NOT NULL,
			`lang` VARCHAR(3) DEFAULT "EN" COMMENT "Language code",
			`name` VARCHAR(255),
			`description` TEXT,
			`slug` VARCHAR(255) NOT NULL,
			`keywords` VARCHAR(255) DEFAULT NULL COMMENT "Document meta keywords",
			`pageTitle` TEXT DEFAULT NULL COMMENT "Text used as the title for the document",
			`listingTitle` TEXT DEFAULT NULL COMMENT "Custom title for listings page",
			`pageDescription` VARCHAR(255) NOT NULL COMMENT "Custom description for document meta tag",
			PRIMARY KEY (`id`, `lang`),
			UNIQUE INDEX `uniqueSlug` (`lang`, `slug`)
		) DEFAULT CHARSET="utf8" ENGINE="MyISAM" COMMENT "Text for meta values"';
		$this->_db->setQuery($query);
		$this->_db->query();
		
		$query = 'CREATE TABLE `metaSelectors` (
			`id` int(8) NOT NULL AUTO_INCREMENT,
			`internalName` VARCHAR(255) NOT NULL COMMENT "Name for internal tracking",
			`display` TINYINT(1) NOT NULL DEFAULT 1 COMMENT "Whether to display on frontend.",
			`imageName` VARCHAR(255) DEFAULT NULL COMMENT "Filename of supporting image",
			`sequence` INT(8) NOT NULL DEFAULT 0 COMMENT "Order in which to display the selector on the frontend.",
			PRIMARY KEY  (`id`),
			KEY `internalName` (`internalName`)
		) ENGINE="MyISAM" DEFAULT CHARSET="utf8" COMMENT "Groupings of meta values"';
		$this->_db->setQuery($query);
		$this->_db->query();
		
		$query = 'CREATE TABLE `languageMetaSelectors` (
			`id` INT(8) NOT NULL COMMENT "Foreign key for metaSelectors.id",
			`lang` VARCHAR(3) NOT NULL DEFAULT "EN" COMMENT "Language code",
			`name` VARCHAR(255) NOT NULL,
			`description` TEXT,
			`slug` VARCHAR(255) NOT NULL,
			`keywords` VARCHAR(255) DEFAULT NULL COMMENT "Document meta keywords",
			`pageTitle` TEXT COMMENT "Document title",
			`listingTitle` TEXT COMMENT "Custom title for products listing page",
			`pageDescription` VARCHAR(255) DEFAULT NULL COMMENT "Custom description for document meta tag.",
			PRIMARY KEY  (`id`, `lang`),
			UNIQUE KEY `uniqueSlugs` (`lang`, `slug`)
		) ENGINE="MyISAM" DEFAULT CHARSET="utf8" COMMENT "Text for meta selectors"';
		$this->_db->setQuery($query);
		$this->_db->query();
		
		$query = 'CREATE TABLE `metaSelectorValues` (
			`selectorId` INT(8) NOT NULL COMMENT "Foreign key for metaSelectors.id",
			`groupId` INT((8) NOT NULL COMMENT "Foreign key for metaGroups.id",
			`valueId` INT((8) DEFAULT NULL COMMENT "Foreign key for metaValues.id",
			`valueRaw` DOUBLE DEFAULT NULL COMMENT "Raw meta value (range groups).",
			KEY `selector` (`selectorId`)
		) ENGINE="MyISAM" DEFAULT CHARSET="utf8" COMMENT "Mapping table between meta selectors and meta values"';
		$this->_db->setQuery($query);
		$this->_db->query();
	}
	
	/**
	 *	Migrate meta data
	 *
	 *	@access protected
	 */
	protected function _migrateMetaData()
	{
		
	}
	
	/**
	 *	Prepare article data
	 *
	 *	@access protected
	 */
	protected function _createArticleTables()
	{
		// Articles
		$query = 'CREATE TABLE `articles` (
			`id` INT(8) NOT NULL AUTO_INCREMENT,
			`type` ENUM("article", "question", "quicktip", "recipe") NOT NULL DEFAULT "article" COMMENT "Type of article",
			`title` VARCHAR(255) COMMENT "Article name",
			`author` INT(8) NOT NULL COMMENT "User who submitted article",
			`body` TEXT COMMENT "Article content",
			`teaser` MEDIUMTEXT DEFAULT "" COMMENT "Article description",
			`keywords` VARCHAR(255) COMMENT "Page keywords",
			`date` DATETIME NOT NULL COMMENT "Date of creation",
			`live` TINYINT(1) NOT NULL DEFAULT 0 COMMENT "Whether article is live",
			`featured` INT(8) NOT NULL DEFAULT 0 COMMENT "Feature-level of article",
			`slug` VARCHAR(255) NOT NULL COMMENT "Article slug",
			PRIMARY KEY (`id`),
			INDEX (`type`),
			FULLTEXT INDEX (`title`, `body`, `teaser`, `keywords`, `slug`),
			INDEX (`date`),
			INDEX (`live`),
			INDEX (`featured`),
			UNIQUE INDEX (`slug`)
		) DEFAULT CHARSET="utf8" ENGINE="MyISAM"';
		$this->_db->setQuery($query);
		$this->_db->query();
		
		// Images
		$query = 'CREATE TABLE `articleImages` (
			`articleId` INT(8) NOT NULL,
			`filename` VARCHAR(255) NOT NULL COMMENT "Basename of file",
			`title` VARCHAR(255) DEFAULT "" COMMENT "Image title",
			`description` TEXT DEFAULT "" COMMENT "Image description",
			`minidescription` VARCHAR(255) DEFAULT "" COMMENT "Alternative text",
			`sequence` INT(2) DEFAULT 0 COMMENT "Priority of image",
			PRIMARY KEY (`articleId`, `filename`),
			INDEX (`articleId`),
			INDEX (`sequence`)
		) DEFAULT CHARSET="utf8" ENGINE="MyISAM" COMMENT "Images for articles"';
		$this->_db->setQuery($query);
		$this->_db->query();
		
		// Ratings
		$query = 'CREATE TABLE `articleRatings` (
			`articleId` INT(8) NOT NULL,
			`userId` INT(8) NOT NULL COMMENT "User who submitted rating",
			`rating` INT(2) NOT NULL,
			`date` DATETIME NOT NULL COMMENT "Date of creation",
			PRIMARY KEY (`articleId`, `userId`),
			INDEX (`date`)
		) DEFAULT CHARSET="utf8" ENGINE="MyISAM" COMMENT "User ratings for articles"';
		$this->_db->setQuery($query);
		$this->_db->query();
		
		// Views
		$query = 'CREATE TABLE `articleViews` (
			`articleId` INT(8) NOT NULL COMMENT "Foreign key for articles.id",
			`views` INT(8) NOT NULL DEFAULT 0 COMMENT "View count",
			`date` DATETIME NOT NULL COMMENT "Last viewed",
			PRIMARY KEY (`articleId`)
		) DEFAULT CHARSET="utf8" ENGINE="MyISAM" COMMENT "View count for articles"';
		$this->_db->setQuery($query);
		$this->_db->query();
		
		// Links (also works for related articles - just automatically insert the related article's link in the admin)
		$query = 'CREATE TABLE `articleLinks` (
			`articleId` INT(8) NOT NULL,
			`href` VARCHAR(255) NOT NULL COMMENT "Location of link",
			`title` VARCHAR(255) NOT NULL,
			`description` VARCHAR(255),
			`sequence` INT(2) DEFAULT 0 COMMENT "Priority of link",
			PRIMARY KEY (`articleId`, `href`),
			INDEX (`articleId`),
			INDEX (`sequence`)
		) DEFAULT CHARSET="utf8" ENGINE="MyISAM" COMMENT "Links for articles"';
		$this->_db->setQuery($query);
		$this->_db->query();
		
		// Meta value mappings
		$query = 'CREATE TABLE `articleMetaValues` (
			`articleId` INT(8) NOT NULL,
			`groupId` INT(8) COMMENT "Foreign key for metaGroups.id",
			`valueId` INT(8) DEFAULT NULL COMMENT "Foreign key for metaValues.id",
			`rawValue` DOUBLE DEFAULT NULL COMMENT "Raw meta value",
			PRIMARY KEY (`articleId`, `groupId`, `valueId`, `rawValue`),
			INDEX (`articleId`),
			INDEX (`groupId`),
			INDEX (`valueId`),
			INDEX (`rawValue`)
		) DEFAULT CHARSET="utf8" ENGINE="MyISAM" COMMENT "Mapping table between articles and meta values"';
		$this->_db->setQuery($query);
		$this->_db->query();
	}
	
	/**
	 *	Migrate article data
	 *
	 *	@access protected
	 */
	protected function _migrateArticleData()
	{
		// Get models
		$itemsModel = BluApplication::getModel('items');
		
		// Get base article data
		$query = 'SELECT c.*
			FROM `dbo_content` AS `c`';
		$this->_source->setQuery($query, 0, 50);
		$articles = $this->_source->loadAssocList('content_id');
		
		// Yoink.
		foreach ($articles as $article) {
			
			// Pull out XML, eurgh
			$articleXml = new SimpleXMLElement($article['content_html']);
			
			// Insert article
			$query = 'INSERT INTO `articles`
				SET `id` = '.(int) $article['content_id'].',
					`type` = "recipe",
					`author` = asdfaf,
					`date` = "'.$this->_db->escape(Text::asXml($articleXml->info->publish_date)).'",
					`slug` = ""';
			$this->_db->setQuery($query);
			$this->_db->query();
			$itemsModel->editItem($article['content_id'], $article['content_title'], Text::asXml($articleXml->content->body), Text::asXml($articleXml->info->big_blurb), null, null);
			$itemsModel->setLive($article['content_id']);
			
			// Get their view count details
			$query = 'SELECT COUNT(v.*) AS `views`, MAX(v.dateCreated) AS `lastDate`
				FROM `dbo_EciContentView` AS `v`
				WHERE v.contentId = '.(int) $article['content_id'];
			$this->_source->setQuery($query);
			$views = $this->_source->loadAssoc();
			
			// Update our view counts
			$query = 'INSERT INTO `articleViews`
				SET `articleId` = '.(int) $article['content_id'].',
					`views` = '.(int) $views['views'].',
					`date` = "'.$this->_db->escape($views['lastDate']).'"';
			$this->_db->setQuery($query);
			$this->_db->query();
			
			// Insert votes record
			$query = 'INSERT INTO `articleVotes`
				SET `articleId` = '.(int) $article['content_id'].',
					`votes` = 0';
			$this->_db->setQuery($query);
			$this->_db->query();
			
			// Add images
			// @todo
			// @see BackendItemsModel::addImage
			
			// Add links
			// @todo
			// @see BackendItemsModel::addLink
			
			// Add meta values (if they don't already exist), and assign to the article.
			// @todo
			
			// Add ratings
			// @todo
			
			
		}
	}
	
	/**
	 *	Prepare comment data
	 *
	 *	@access protected
	 */
	protected function _createCommentTables()
	{
		// Comments
		$query = 'CREATE TABLE `comments` (
			`id` INT(8) NOT NULL AUTO_INCREMENT,
			`type` ENUM("review", "note") NOT NULL DEFAULT "review" COMMENT "Type of comment",
			`body` TEXT COMMENT "Comment content",
			`objectType` ENUM("article"),
			`objectId` INT(8) NOT NULL COMMENT "Identifier of commented object.",
			`userId` INT(8) COMMENT "Commentor",
			`date` DATETIME NOT NULL COMMENT "Date of creation",
			`live` TINYINT(1) NOT NULL DEFAULT 1 COMMENT "Live",
			PRIMARY KEY (`id`),
			INDEX (`type`),
			FULLTEXT INDEX (`body`),
			INDEX `object` (`objectType`, `objectId`),
			INDEX (`userId`),
			INDEX (`date`),
			INDEX (`live`)
		) DEFAULT CHARSET="utf8" ENGINE="MyISAM"';
		$this->_db->setQuery($query);
		$this->_db->query();
		
		// Ratings
		$query = 'CREATE TABLE `commentRatings` (
			`commentId` INT(8) NOT NULL,
			`userId` INT (8) NOT NULL COMMENT "User who submitted rating",
			`rating` DOUBLE NOT NULL,
			`date` DATETIME NOT NULL COMMENT "Date of creation",
			PRIMARY KEY (`commentId`, `userId`),
			INDEX (`date`)
		) DEFAULT CHARSET="utf8" ENGINE="MyISAM" COMMENT "User ratings for comments"';
		$this->_db->setQuery($query);
		$this->_db->query();
	}
	
	/**
	 *	Migrate comment data
	 *
	 *	@access protected
	 */
	protected function _migrateCommentData()
	{
		
	}
	
	/**
	 *	Prepare user data
	 *
	 *	@access protected
	 */
	protected function _createUserTables()
	{
		// Users
		$query = 'CREATE TABLE `users` (
			`id` INT(8) NOT NULL AUTO_INCREMENT COMMENT "Identifier for user",
			`type` ENUM("member", "administrator", "publisher") NOT NULL DEFAULT "member" COMMENT "User privileges",
			`username` VARCHAR(255) NOT NULL,
			`password` VARCHAR(255) NOT NULL,
			`email` VARCHAR(255) DEFAULT "",
			`firstname` VARCHAR(255) DEFAULT "",
			`lastname` VARCHAR(255) DEFAULT "",
			`deleted` TINYINT(1) NOT NULL DEFAULT 0,
			`displayname` VARCHAR(255) DEFAULT "" COMMENT "Friendly name",
			`rating` INT(8) NOT NULL DEFAULT 0 COMMENT "User ranking",
			PRIMARY KEY (`id`),
			UNIQUE INDEX (`username`),
			UNIQUE INDEX (`email`),
			FULLTEXT INDEX `fullname` (`username`, `firstname`, `lastname`, `displayname`),
			INDEX (`deleted`)
		) DEFAULT CHARSET="utf8" ENGINE="MyISAM"';
		$this->_db->setQuery($query);
		$this->_db->query();
		
		// Relatively static data
		$query = 'CREATE TABLE `userInfo` (
			`userId` INT(8) NOT NULL COMMENT "Foreign key for users.id",
			`image` VARCHAR(255) DEFAULT NULL COMMENT "Basename of file",
			`private` TINYINT(1) NOT NULL DEFAULT 0 COMMENT "Row of data is private",
			`joined` DATETIME NOT NULL COMMENT "Date joined",
			`about` TEXT COMMENT "Personal statement",
			`favouriteFoods` TEXT,
			PRIMARY KEY (`userId`),
			FULLTEXT INDEX (`favouriteFoods`)
		) DEFAULT CHARSET="utf8" ENGINE="MyISAM" COMMENT "Relatively static user data"';
		$this->_db->setQuery($query);
		$this->_db->query();
		
		/*
		// Meta value mappings...maybe?
		$query = 'CREATE TABLE `userMetaValues` (
			`userId` INT(8) NOT NULL,
			`groupId` INT(8) COMMENT "Foreign key for metaGroups.id",
			`valueId` INT(8) COMMENT "Foreign key for metaValues.id",
			`rawValue` DOUBLE COMMENT "Raw meta value",
			PRIMARY KEY (`userId`, `groupId`, `valueId`, `rawValue`),
			INDEX (`userId`),
			INDEX (`groupId`),
			INDEX (`valueId`),
			INDEX (`rawValue`)
		) DEFAULT CHARSET="utf8" ENGINE="MyISAM"';
		$this->_db->setQuery($query);
		$this->_db->query();
		*/
		
		// User article saves and user shopping-lists.
		$query = 'CREATE TABLE `userSaves` (
			`userId` INT(8) NOT NULL,
			`objectType` ENUM("recipebox", "shopping_list"),
			`objectId` INT(8) NOT NULL COMMENT "Identifier of commented object.",
			`comment` VARCHAR(255) DEFAULT "" COMMENT "Personal reminder of bookmark",
			PRIMARY KEY (`userId`, `objectType`, `objectId`),
			INDEX (`userId`),
			INDEX `object` (`objectType`, `objectId`),
			INDEX (`objectType`)
		) DEFAULT CHARSET="utf8" ENGINE="MyISAM" COMMENT "Mapping table between users and articles"';
		$this->_db->setQuery($query);
		$this->_db->query();
	}
	
	/**
	 *	Migrate user data
	 *
	 *	@access protected
	 */
	protected function _migrateUserData()
	{
		
	}
	
	/**
	 *	Prepare reports data
	 *
	 *	@access protected
	 */
	protected function _createReportsTables()
	{
		// Reports
		$query = 'CREATE TABLE `reports` (
			`id` INT(8) NOT NULL AUTO_INCREMENT,
			`objectType` ENUM("comment"),
			`objectId` INT(8) NOT NULL COMMENT "Identifier of reported object",
			`reporter` INT(8) NOT NULL COMMENT "User who reported",
			`time` DATETIME NOT NULL COMMENT "Time of report",
			`reason` TEXT DEFAULT NULL,
			`status` ENUM("pending", "viewed", "replied", "resolved") NOT NULL DEFAULT "pending",
			PRIMARY KEY (`id`),
			INDEX `object` (`objectType`, `objectId`),
			UNIQUE INDEX `uniqueReport` (`objectType`, `objectId`, `reporter`)
		) DEFAULT CHARSET="utf8" ENGINE="MyISAM" COMMENT "Reported content"';
		$this->_db->setQuery($query);
		$this->_db->query();
	}
	
	/**
	 *	Create box tables
	 *
	 *	@access protected
	 */
	protected function _createBoxTables()
	{
		// Box out
		$query = 'CREATE TABLE `boxOut` (
			`id` int(8) NOT NULL AUTO_INCREMENT,
			`internalName` VARCHAR(255) NOT NULL,
			`type` VARCHAR(255) NOT NULL,
			`slug` VARCHAR(255) NOT NULL,
			PRIMARY KEY  (`id`),
			UNIQUE KEY `slug` (`slug`)
		) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8';
		$this->_db->setQuery($query);
		$this->_db->query();
		
		// Box out content
		$query = 'CREATE TABLE `boxOutContent` (
			`id` INT(8) NOT NULL AUTO_INCREMENT,
			`lang` VARCHAR(3) NOT NULL DEFAULT "EN" COMMENT "Language code",
			`boxId` INT(8) DEFAULT NULL,
			`date` DATETIME NOT NULL,
			`subtitle` VARCHAR(255) DEFAULT NULL,
			`link` VARCHAR(255) DEFAULT NULL,
			`title` VARCHAR(255) DEFAULT NULL,
			`text` TEXT,
			`info` VARCHAR(255) DEFAULT NULL,
			`sequence` INT(8) NOT NULL DEFAULT 0 COMMENT "Priority",
			`imageName` VARCHAR(255) DEFAULT NULL COMMENT "Filename of image",
			PRIMARY KEY  (`id`),
			KEY `sequence` (`sequence`)
		) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8';
		$this->_db->setQuery($query);
		$this->_db->query();
		
		// Box out site mapping
		$query = 'CREATE TABLE `boxOutSiteMapping` (
			`id` INT(8) NOT NULL,
			`siteId` VARCHAR(30) NOT NULL,
			PRIMARY KEY  (`id`, `siteId`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8';
		$this->_db->setQuery($query);
		$this->_db->query();
	}
	
	/**
	 *	Create constant contact tables
	 *
	 *	@access protected
	 */
	public function _createMaillistTables()
	{
		// Plugins
		$query = 'CREATE TABLE `plugins` (
			`id` VARCHAR(50) NOT NULL,
			`type` ENUM("payment", "maillist", "warehouse", "accounting", "systems", "shipping", "feed") NOT NULL DEFAULT "payment",
			`name` VARCHAR(50) DEFAULT NULL,
			`enabled` TINYINT(1) DEFAULT 0,
			`sequence` INT(8) UNSIGNED DEFAULT NULL,
			`settings` TEXT,
			`siteId` VARCHAR(30) NOT NULL,
			PRIMARY KEY  (`id`, `type`, `siteId`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8';
		$this->_db->setQuery($query);
		$this->_db->query();
		
		// Local maillists
		$query = 'CREATE TABLE `mailLists` (
			`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			`name` VARCHAR(100) DEFAULT NULL,
			`providerRef` VARCHAR(20) DEFAULT NULL COMMENT "Internal provider list reference",
			PRIMARY KEY (`id`)
		) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8';
		$this->_db->setQuery($query);
		$this->_db->query();
		
		$query = 'CREATE TABLE `mailSubscribers` (
			`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			`name` VARCHAR(255) NOT NULL DEFAULT "",
			`email` VARCHAR(255) NOT NULL DEFAULT "",
			`status` ENUM("subscribed", "unsubscribed", "admin") DEFAULT NULL,
			`validated` TINYINT(1) DEFAULT 0,
			`joinDate` DATE DEFAULT NULL,
			`custom1` VARCHAR(255) DEFAULT NULL,
			`custom2` VARCHAR(255) DEFAULT NULL,
			`custom3` VARCHAR(255) DEFAULT NULL,
			`custom4` VARCHAR(255) DEFAULT NULL,
			`updateDate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			`unsubscribeDate` DATETIME NOT NULL,
			PRIMARY KEY (`id`),
			UNIQUE KEY (`email`)
		) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8';
		$this->_db->setQuery($query);
		$this->_db->query();
		
		$query = 'CREATE TABLE `mailListSubscriptions` (
			`listId` INT(11) NOT NULL,
			`subscriberId` INT(11) NOT NULL,
			`status` ENUM("subscribed", "unsubscribed") DEFAULT NULL,
			PRIMARY KEY (`listId`, `subscriberId`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8';
		$this->_db->setQuery($query);
		$this->_db->query();
		
		// Enable local mailing lists
		$query = 'INSERT INTO `mailLists`
			SET `id` = 1,
				`name` = "General Interest",
				`providerRef` = 1';
		$this->_db->setQuery($query);
		$this->_db->query();
		
		// Enable Constant contact
		$query = 'INSERT INTO `plugins`
			SET `id` = "constantcontact",
				`type` = "maillist",
				`name` = "Constant Contact",
				`enabled` = 1,
				`sequence` = 0,
				`settings` = "'.$this->_db->escape(serialize(array())).'",
				`siteId` = "'.$this->_db->escape(BluApplication::getSetting('siteId')).'"';
		$this->_db->setQuery($query);
		$this->_db->query();
	}
	
	/**
	 *	Migrate ingredients from metaValues to articleMetaValues
	 *
	 *	@access public
	 *	@return bool Success
	 */
	public function migrateIngredients()
	{
		// Enable group type
		$query = 'ALTER TABLE `metaGroups`
			MODIFY COLUMN `type` ENUM("pick", "numberpick", "numberrange", "keywords") NOT NULL DEFAULT "pick" COMMENT "Meta value type"';
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			return false;
		}
		
		// Change mapping table column type, default values, add fulltext index
		$query = 'ALTER TABLE `articleMetaValues`
			DROP PRIMARY KEY,
			DROP INDEX `rawValue`,
			MODIFY COLUMN `rawValue` VARCHAR(255) DEFAULT NULL COMMENT "Raw meta value",
			MODIFY COLUMN `valueId` INT(8) DEFAULT NULL COMMENT "Foreign key for metaValues.id",
			ADD FULLTEXT INDEX `keywords` (`rawValue`)';
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			return false;
		}
		
		// Fix rawValue column data
		$query = 'UPDATE `articleMetaValues` AS `amv`
				LEFT JOIN `metaGroups` AS `mg` ON amv.groupId = mg.id
			SET amv.rawValue = DEFAULT
			WHERE mg.type != "numberpick"';
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			return false;
		}
		
		$query = 'UPDATE `articleMetaValues` AS `amv`
				LEFT JOIN `metaGroups` AS `mg` ON amv.groupId = mg.id
			SET amv.valueId = DEFAULT
			WHERE mg.type = "numberpick"';
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			return false;
		}
		
		// Get ingredients group ID
		$query = 'SELECT lmg.id
			FROM `languageMetaGroups` AS `lmg`
			WHERE lmg.slug = "ingredients"';
		$this->_db->setQuery($query);
		$ingredientsGroupId = $this->_db->loadResult();
		
		// Change group type
		$query = 'UPDATE `metaGroups`
			SET `type` = "keywords"
			WHERE `id` = '.(int) $ingredientsGroupId;
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			return false;
		}
		
		// Copy ingredient value over to destination table
		$query = 'UPDATE `articleMetaValues` AS `amv`
				LEFT JOIN `languageMetaValues` AS `lmv` ON amv.valueId = lmv.id
			SET amv.rawValue = lmv.name,
				amv.valueId = DEFAULT
			WHERE amv.groupId = '.(int) $ingredientsGroupId;
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			return false;
		}
		
		// Remove original bloated ingredients
		$query = 'DELETE mv.*, lmv.*
			FROM `metaValues` AS `mv`
				LEFT JOIN `languageMetaValues` AS `lmv` ON mv.id = lmv.id
			WHERE mv.groupId = '.(int) $ingredientsGroupId;
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			return false;
		}
		
		// Return
		return true;
	}
	
	/**
	 *	Add a user
	 *
	 *	@access public
	 *	@param string Username
	 *	@param string Email
	 *	@param string Location
	 *	@param string Date joined
	 */
	public function bodgeUser($username, $email, $location, $joined)
	{
		$query = 'INSERT INTO `users`
			SET `type` = "member",
				`username` = "'.$this->_db->escape($username).'",
				`email` = "'.$this->_db->escape($email).'",
				`location` = "'.$this->_db->escape($location).'"';
		$this->_db->setQuery($query);
		$this->_db->query();
		
		$userId = $this->_db->getInsertID();
		
		$query = 'INSERT INTO `userInfo`
			SET `userId` = '.(int) $userId.',
				`joined` = "'.$this->_db->escape($joined).'"';
		$this->_db->setQuery($query);
		$this->_db->query();
	}
	
	/**
	 *	Fix ingredients
	 */
	public function fixIngredients()
	{
		$query = 'SELECT a.id
			FROM `articles` AS `a`
			WHERE a.ingredients IS NULL';
		$this->_db->setQuery($query);
		$articles = $this->_db->loadResultAssocArray('id', 'id');
		foreach ($articles as $articleId) {
			
			$query = 'SELECT amv.rawValue
				FROM `articleMetaValues` AS `amv`
				WHERE amv.articleId = '.(int) $articleId.'
					AND amv.groupId = 6
					AND amv.valueId IS NULL';
			$this->_db->setQuery($query);
			$ingredients = $this->_db->loadResultArray();
			
			if (!empty($ingredients)) {
				$ingredients = implode("\n", $ingredients);
				$query = 'UPDATE `articles`
					SET `ingredients` = "'.$this->_db->escape($ingredients).'"
					WHERE `id` = '.(int) $articleId;
				$this->_db->setQuery($query);
				$this->_db->query();
				
				$query = 'DELETE FROM `articleMetaValues`
					WHERE `articleId` = '.(int) $articleId.'
						AND `groupId` = 6
						AND `valueId` IS NULL';
				$this->_db->setQuery($query);
				$this->_db->query();
				
				$this->_cache->delete('item_'.$articleId);
			}
		}
	}
	
	/**
	 *	Create USDA FOOD_DES table
	 *
	 *	@access public
	 *	@return bool Success
	 */
	public function createFoodDesTable()
	{
		$query = 'CREATE TABLE `usdaNutrDef` (
			`Nutr_No` int(3) unsigned zerofill NOT NULL,
			`Units` varchar(7) default NULL,
			`Tagname` varchar(20) default NULL,
			`NutrDesc` varchar(60) default NULL,
			`Decimal` smallint(1) unsigned default NULL,
			`SR_Order` int(10) unsigned default NULL,
			PRIMARY KEY  (`Nutr_No`),
			UNIQUE KEY `Nutro_No` (`Nutr_No`),
			KEY `Nutro_No_2` (`Nutr_No`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT="USDA NUTR_DEF"';
		$this->_db->setQuery($query);
		return $this->_db->query();
	}
	
	/**
	 *	Create USDA NUTR_DEF table
	 *
	 *	@access public
	 *	@return bool Success
	 */
	public function createNutrDefTable()
	{
		$query = 'CREATE TABLE `usdaNutrDef` (
			`Nutr_No` int(3) unsigned zerofill NOT NULL,
			`Units` varchar(7) default NULL,
			`Tagname` varchar(20) default NULL,
			`NutrDesc` varchar(60) default NULL,
			`Decimal` smallint(1) unsigned default NULL,
			`SR_Order` int(10) unsigned default NULL,
			PRIMARY KEY  (`Nutro_No`),
			UNIQUE KEY `Nutro_No` (`Nutro_No`),
			KEY `Nutro_No_2` (`Nutro_No`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT="USDA NUTR_DEF"';
		$this->_db->setQuery($query);
		return $this->_db->query();
	}
	
	/**
	 *	Create USDA NUT_DATA table
	 *
	 *	@access public
	 *	@return bool Success
	 */
	public function createNutDataTable()
	{
		$query = 'CREATE TABLE `usdaNutData` (
			`NDB_No` int(5) unsigned zerofill NOT NULL,
			`Nutr_No` int(3) unsigned zerofill NOT NULL,
			`Nutr_Val` double unsigned default NULL,
			`Num_Data_Pts` int(4) unsigned default NULL,
			`Std_Error` double unsigned default NULL,
			`Src_Cd` int(2) unsigned default NULL,
			`Deriv_Cd` varchar(4) default NULL,
			`Ref_NDB_No` int(5) unsigned zerofill default NULL,
			`Add_Nutr_Mark` varchar(1) default NULL,
			`Num_Studies` int(3) unsigned default NULL,
			`Min` double unsigned default NULL,
			`Max` double unsigned default NULL,
			`DF` double unsigned default NULL,
			`Low_EB` double default NULL,
			`Up_EB` double unsigned default NULL,
			`Stat_Cmt` varchar(10) default NULL,
			PRIMARY KEY  (`NDB_No`,`Nutr_No`),
			KEY `Num_Data_Pts` (`Num_Data_Pts`),
			KEY `Num_Studies` (`Num_Studies`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT="USDA NUT_DATA"';
		$this->_db->setQuery($query);
		return $this->_db->query();
	}
	
	/**
	 *	Create USDA Nutr_No <-> meta value mapping table
	 *
	 *	@access public
	 *	@return bool Success
	 */
	public function createNutrMetaTable()
	{
		$query = 'CREATE TABLE `usdaMeta` (
			`NDB_No` int(5) unsigned zerofill NOT NULL,
			`metaValue` int(8) unsigned NOT NULL,
			PRIMARY KEY  (`NDB_No`,`metaValue`),
			UNIQUE KEY `NDB_No` (`NDB_No`,`metaValue`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT="USDA Ingredient-metavalue mapping"';
		$this->_db->setQuery($query);
		return $this->_db->query();
	}
	
	/**
	 *	Create USDA WEIGHT table
	 *
	 *	@access public
	 *	@return bool Success
	 */
	public function createWeightTable()
	{
		$query = 'CREATE TABLE `usdaWeight` (
			`id` int(3) unsigned NOT NULL auto_increment,
			`NDB_No` int(5) unsigned zerofill NOT NULL default "00000",
			`Seq` int(2) unsigned NOT NULL default 0,
			`Amount` double default NULL,
			`Msre_Desc` varchar(80) default NULL,
			`Gm_Wgt` double default NULL,
			`Num_Data_Pts` int(3) default NULL,
			`Std_Dev` double default NULL,
			PRIMARY KEY  (`id`),
			UNIQUE KEY `NDB_No` (`NDB_No`,`Seq`)
		) ENGINE=MyISAM AUTO_INCREMENT=13088 DEFAULT CHARSET=utf8 COMMENT="USDA WEIGHT"';
		$this->_db->setQuery($query);
		return $this->_db->query();
	}
	
	/**
	 *	Recipe ingredients mapping table
	 *
	 *	@access public
	 */
	public function createRecipeIngredientsTable()
	{
		$query = 'CREATE TABLE `recipeIngredients` (
			`articleId` int(8) unsigned NOT NULL,
			`NDB_No` int(5) unsigned zerofill NOT NULL,
			`amount` double unsigned default NULL,
			`weightId` int(3) unsigned NOT NULL,
			PRIMARY KEY  (`articleId`,`NDB_No`),
			KEY `recipe` (`articleId`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT="Recipe ingredient (amount) mapping"';
		$this->_db->setQuery($query);
		return $this->_db->query();
	}
}

?>