<?php

/**
 *	Migration
 */
class Recipe4livingMigrationController extends MigrationController
{
	/**
	 *	Up the memory
	 *
	 *	@access public
	 *	@param array Arguments
	 */
	public function __construct($args)
	{
		parent::__construct($args);

		// Bump up the memory limit
		//ini_set('memory_limit', '512M');
	}

	/**
	 *	Do initial import
	 *
	 *	@access public
	 */
	public function import()
	{
		// Get source database: Alan's computer.
		//$source = MSSQLDatabase::getInstance('192.168.0.187', 'root', 'Puli0bee', 'ALAN\SQLEXPRESS');
		$source = Database::getInstance('localhost', 'root', 'Puli0bee', 'recipe4living_migrate');	// Until Max gets round to setting up the mssql extension on local.
		
		// Do import
		$migrationModel = BluApplication::getModel('migration');
		if ($migrationModel->import($source)) {
			echo 'Imported!';
		}
	}
	
	/**
	 *	Migrate ingredients
	 *
	 *	@access public
	 */
	public function migrateIngredients()
	{
		$migrationModel = BluApplication::getModel('migration');
		if ($migrationModel->migrateIngredients()) {
			echo 'Migrated ingredients!';
		}
	}
	
	/**
	 *	Migrate users
	 *
	 *	@access public
	 */
	public function migrateUsers()
	{
		if ($csv = Csv::read('/tmp/users.csv')) {
			
			$database = BluApplication::getDatabase();
			foreach ($csv->get() as $row) {
				
				// Get email/username
				if (!$email = $row[7] ? $row[7] : ($row[8] ? $row[8] : $row[9])) {
					continue;
				}
				$username = $row[1];
				
				$query = 'INSERT INTO `users`
					SET `oldUserId` = '.(int) $row[0].',
						`username` = "'.$database->escape($username).'",
						`email` = "'.$database->escape($email).'",
						`firstname` = "'.$database->escape($row[3]).'",
						`lastname` = "'.$database->escape($row[4]).'",
						`deleted` = '.(int) $row[12].',
						`displayname` = "'.$database->escape($row[21]).'"
					ON DUPLICATE KEY UPDATE
						`oldUserId` = '.(int) $row[0];
				$database->setQuery($query);
				$database->query();
				
				if (!$userId = $database->getInsertID()) {
					$query = 'SELECT u.id
						FROM `users` AS `u`
						WHERE u.oldUserId = '.(int) $row[0];
					$database->setQuery($query);
					$userId = (int) $database->loadResult();
				}
				
				$query = 'INSERT INTO `userInfo`
					SET `userId` = '.(int) $userId.',
						`joined` = "'.$database->escape($row[18]).'"
					ON DUPLICATE KEY UPDATE
						`joined` = "'.$database->escape($row[18]).'"';
				$database->setQuery($query);
				$database->query();
			}
			
			echo 'Users migrated';
		}
	}
	
	/**
	 *	Migrate users again
	 *
	 *	@access public
	 */
	public function migrateUsersAgain()
	{
		if ($csv = Csv::read('/tmp/members.csv')) {
			
			$database = BluApplication::getDatabase();
			foreach ($csv->get() as $row) {
				
				// Bump 'em in.
				$query = 'INSERT INTO `users`
					SET `oldUserId` = '.(int) $row[0].',
						`username` = "'.$database->escape($row[1]).'",
						`email` = "'.$database->escape($row[2]).'",
						`deleted` = '.(int) (!(bool) $row[3]).'
					ON DUPLICATE KEY UPDATE
						`oldUserId` = '.(int) $row[0];
				$database->setQuery($query);
				if (!$database->query()) {
					continue;
				}
				
				// Fetch the new user ID
				if (!$userId = $database->getInsertID()) {
					$query = 'SELECT u.id
						FROM `users` AS `u`
						WHERE u.oldUserId = '.(int) $row[0];
					$database->setQuery($query);
					$userId = (int) $database->loadResult();
				}
				
				$query = 'INSERT INTO `userInfo`
					SET `userId` = '.(int) $userId.',
						`joined` = "'.$database->escape($row[4]).'"
					ON DUPLICATE KEY UPDATE
						`joined` = "'.$database->escape($row[4]).'"';
				$database->setQuery($query);
				$database->query();
			}
			
			echo 'Users migrated again';
		}
	}
	
	/**
	 *	Get oldUserId to userId mapping
	 *
	 *	@access protected
	 *	@return array
	 */
	protected function _getOldUserMapping()
	{
		$database = BluApplication::getDatabase();
		$query = 'SELECT u.oldUserId, u.id
			FROM `users` AS `u`';
		$database->setQuery($query);
		return $database->loadResultAssocArray('oldUserId', 'id');
	}
	
	/**
	 *	Get oldArticleId to articleId mapping
	 *
	 *	@access protected
	 *	@return array
	 */
	protected function _getOldArticleMapping()
	{
		$database = BluApplication::getDatabase();
		$query = 'SELECT a.oldArticleId, a.id
			FROM `articles` AS `a`';
		$database->setQuery($query);
		return $database->loadResultAssocArray('oldArticleId', 'id');
	}
	
	/**
	 *	Migrate item authors
	 *
	 *	@access public
	 */
	public function migrateItemAuthors()
	{
		if ($csv = Csv::read('/tmp/EciContentToUser.csv')) {
			
			$userMapping = $this->_getOldUserMapping();
			$articleMapping = $this->_getOldArticleMapping();
			
			// Screw the model layer, we're doing things our way.
			$database = BluApplication::getDatabase();
			foreach ($csv->get() as $row) {
				
				// Can't find the item.
				if (!isset($articleMapping[$row[0]])) {
					continue;
				}
				
				$query = 'UPDATE `articles`
					SET `author` = '.(int) $userMapping[$row[1]].'
					WHERE `id` = '.(int) $articleMapping[$row[0]].'
						AND `author` = 0';
				$database->setQuery($query);
				$database->query();
			}
			
			echo 'Recipe authors fixed';
		}
	}
	
	/**
	 *	Migrate recipe boxes
	 *
	 *	@access public
	 */
	public function migrateRecipeBoxes()
	{
		if ($csv = Csv::read('/tmp/EciContentBookmark.csv')) {
			
			$userMapping = $this->_getOldUserMapping();
			$articleMapping = $this->_getOldArticleMapping();
			
			$data = $csv->get();
			$total = count($data);
			$crappy = array();
			$crappy2 = array();
			foreach ($data as $key => $row) {
				
				// Can't find the user
				if (!isset($userMapping[$row[0]])) {
					$crappy[] = $row[0];
					unset($data[$key]);
				}
				
				// Can't find the article
				if (!isset($articleMapping[$row[1]])) {
					$crappy2[] = $row[1];
					unset($data[$key]);
				}
			}
			
			$database = BluApplication::getDatabase();
			foreach ($data as $row) {
				$query = 'INSERT IGNORE INTO `userSaves`
					SET `userId` = '.(int) $userMapping[$row[0]].',
						`objectType` = "recipebox",
						`objectId` = '.(int) $articleMapping[$row[1]];
				$database->setQuery($query);
				$database->query();
			}
			
			echo 'Recipe boxes migrated';
			var_dump($total.' entries to parse', count($crappy).' duff users', count($crappy2).' duff recipes');
		}
	}
	
	/**
	 *	Migrate recipe notes
	 *
	 *	@access public
	 */
	public function migrateRecipeNotes()
	{
		if ($csv = Csv::read('/tmp/EciContentNote.csv')) {
			
			$userMapping = $this->_getOldUserMapping();
			$articleMapping = $this->_getOldArticleMapping();
			
			$database = BluApplication::getDatabase();
			$data = $csv->get();
			$total = count($data);
			$userDuff = 0;
			$itemDuff = 0;
			foreach ($data as $row) {
				
				// Duff
				if (!isset($userMapping[$row[0]])) {
					$userDuff++;
					continue;
				}
				if (!isset($articleMapping[$row[1]])) {
					$itemDuff++;
					continue;
				}
				
				// Store as recipe box too
				$query = 'INSERT IGNORE INTO `userSaves`
					SET `userId` = '.(int) $userMapping[$row[0]].',
						`objectType` = "recipebox",
						`objectId` = '.(int) $articleMapping[$row[1]];
				$database->setQuery($query);
				$database->query();
				
				// Add recipe note as comment
				$query = 'INSERT IGNORE INTO `userSaves`
					SET `userId` = '.(int) $userMapping[$row[0]].',
						`objectType` = "recipe_note",
						`objectId` = '.(int) $articleMapping[$row[1]].',
						`comment` = "'.$database->escape($row[2]).'"';
				$database->setQuery($query);
				$database->query();
			}
			
			echo 'Recipe notes migrated';
			var_dump($total.' entries to parse', $userDuff.' duff users', $itemDuff.' duff items');
		}
	}
	
	/**
	 *	Migrate user shopping list
	 *
	 *	@access public
	 */
	public function migrateRecipeShoppinglists()
	{
		if ($csv = Csv::read('/tmp/EciUserShoppingList.csv')) {
			
			$userMapping = $this->_getOldUserMapping();
			$articleMapping = $this->_getOldArticleMapping();
			
			$database = BluApplication::getDatabase();
			$data = $csv->get();
			$total = count($data);
			$userDuff = 0;
			$itemDuff = 0;
			foreach ($data as $row) {
				
				// Duff
				if (!isset($userMapping[$row[1]])) {
					$userDuff++;
					continue;
				}
				if (!isset($articleMapping[$row[2]])) {
					$itemDuff++;
					continue;
				}
				
				$query = 'INSERT IGNORE INTO `userSaves`
					SET `userId` = '.(int) $userMapping[$row[1]].',
						`objectType` = "shopping_list",
						`objectId` = '.(int) $articleMapping[$row[2]];
				$database->setQuery($query);
				$database->query();
			}
			
			echo 'Shopping lists migrated';
			var_dump($total.' entries to parse', $userDuff.' duff users', $itemDuff.' duff recipes');
		}
	}
	
	/**
	 *	Migrate article votes
	 *
	 *	@access public
	 */
	public function migrateItemRatings()
	{
		// Truncate table
		$database = BluApplication::getDatabase();
		$query = 'TRUNCATE TABLE `articleRatings`';
		$database->setQuery($query);
		$database->query();
		
		if ($csv = Csv::read('/tmp/EciContentVote.csv')) {
			
			$userMapping = $this->_getOldUserMapping();
			$articleMapping = $this->_getOldArticleMapping();
			
			$data = $csv->get();
			$total = count($data);
			$usersDuff = 0;
			$itemsDuff = 0;
			foreach ($data as $row) {
				
				// Duff
				if (!isset($articleMapping[$row[1]])) {
					$itemsDuff++;
					continue;
				}
				if (!isset($userMapping[$row[2]])) {
					$usersDuff++;
					continue;
				}
				
				// Dates
				$time = strtotime(substr($row[4], 0, -4));
				$date = date('Y-m-d H:i:s', $time);
				
				$query = 'INSERT INTO `articleRatings`
					SET `articleId` = '.(int) $articleMapping[$row[1]].',
						`userId` = '.(int) $userMapping[$row[2]].',
						`rating` = '.(int) $row[3].',
						`date` = "'.$database->escape($date).'"';
				$database->setQuery($query);
				$database->query();
			}
			
			echo 'Item ratings migrated';
			var_dump($total.' entries to parse', $usersDuff.' duff users', $itemsDuff.' duff items');
		}
	}
	
	/**
	 *	Import USDA FOOD_DES table
	 *
	 *	@access public
	 */
	public function importIngredients()
	{
		$ingredients = Csv::read('/tmp/FOOD_DES.csv')->get();
		$ingredientGroups = Csv::read('/tmp/FD_GROUP.csv')->get();
		
		$metaModel = BluApplication::getModel('meta');
		$database = BluApplication::getDatabase();
		$database->allowErrors(true);
		
		// Create meta groups
		$metaGroups = array();
		foreach ($ingredientGroups as $group) {
			$metaGroups[$group[0]] = $metaModel->addMetaGroup($group[1]);
			$metaModel->addLanguageMetaGroup($metaGroups[$group[0]], 'EN', 'USDA - '.$group[1], 'USDA - '.$group[1]);
		}
		
		// Create meta values
		foreach ($ingredients as $ingredient) {
			$valueId = $metaModel->addMetaValue($metaGroups[$ingredient[1]], $ingredient[2], array(), true);
			if (!$metaModel->addLanguageMetaValue($valueId, 'EN', $ingredient[2])) {
				$metaModel->deleteMetaValue($valueId);
			}
		}
		
		// Flush groups
		foreach ($metaGroups as $groupId) {
			$metaModel->flushGroup($groupId);
		}
		
		var_dump($database->returnErrorStack());
		
		// Finish
		echo 'Ingredients imported.';
	}
	
	/**
	 *	Fix meta ingredients
	 */
	public function fixIngredients()
	{
		$migrationModel = BluApplication::getModel('migration');
		$migrationModel->fixIngredients();
		echo 'Done.';
	}
	
	/**
	 *	Map USDA FOOD_DES Nutr_No's to meta values.
	 *
	 *	@access public
	 */
	public function mapUsdaMeta()
	{
		$ingredients = Csv::read('/tmp/FOOD_DES.csv')->get();
		$database = BluApplication::getDatabase();
		foreach ($ingredients as $ingredient) {
			$query = 'INSERT INTO `usdaMeta`
				SET `NDB_No` = '.(int) $ingredient[0].',
					`metaValue` = (SELECT mv.id
						FROM `metaValues` AS `mv`
						WHERE `internalName` = "'.$database->escape($ingredient[2]).'")';
			$database->setQuery($query);
			$database->query();
		}
		echo 'Mapped USDA ingredients with meta values';
	}
	
	/**
	 *	Match user ingredients to USDA ingredients
	 *
	 *	@access public
	 */
	public function mapUserIngredients()
	{
		$migrationModel = BluApplication::getModel('migration');
		$migrationModel->mapUserIngredients();
		echo 'Done.';
	}
	
	/**
	 *	Prepare meta values for Encyclopedia of Tips first letters
	 *
	 *	@access public
	 */
	public function prepareQuicktips()
	{
		$migrationModel = BluApplication::getModel('migration');
		$migrationModel->prepareQuicktips();
		echo 'Done.';
	}
	
	/**
	 *	Interface for create quicktip items
	 *
	 *	@access public
	 */
	public function migrateQuicktips()
	{
		// Get encyclopedia
		$itemsModel = BluApplication::getModel('items');
		$itemId = $itemsModel->getItemId('encyclopedia_of_tips');
		$item = $itemsModel->getItem($itemId);
		
		// Load template
		include(BLUPATH_TEMPLATES.'/migration/encyclopedia.php');
	}
	
	/**
	 *	Inner interface for creating quicktip items
	 *
	 *	@access public
	 */
	public function migrateQuicktip()
	{
		// Create
		if (Request::getBool('submit')) {
			
			$section = Request::getString('section');
			$title = Request::getString('title');
			$slug = Request::getString('slug');
			$body = Request::getString('body', null, null, true);
			
			$migrationModel = BluApplication::getModel('migration');
			if ($migrationModel->makeQuicktip($section, $title, $body, $slug)) {
				Messages::addMessage('Made quicktip <code>'.$title.'</code>.');
			} else {
				Messages::addMessage('Could not make quicktip, please try again.', 'error');
			}
		}
		
		// Load template
		Template::set('showTopNav', false);
		include(BLUPATH_TEMPLATES.'/migration/encyclopedia_form.php');
	}

}

?>
