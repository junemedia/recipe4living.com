<?php

/**
 *	Scripts
 */
class BackendScriptsModel extends BluModel {

	/**
	 *	Fix 'Parenting without a Manual' links. (GUID field).
	 */
	public function parenting_without_a_manual(){
		
		/* Get blog id. */
		$query = 'SELECT wpb.blog_id
			FROM `wp_blogs` AS `wpb`
			WHERE wpb.path REGEXP "^/bloggers/parentingwithoutamanual/$"';		
		$this->_db->setQuery($query, 0, 1);
		$blog_id = (int) $this->_db->loadResult();
		if (!$blog_id){
			return false;
		}
		
		/* Check if blog needs changing. */
		$query = 'SELECT COUNT(*)
			FROM `wp_'.$blog_id.'_posts` AS `wpp`
			WHERE wpp.guid REGEXP "^http://workitmom\.com/bloggers/(.*)/"
				AND wpp.guid NOT REGEXP "^http://workitmom\.com/bloggers/parentingwithoutamanual/"';		
		$this->_db->setQuery($query, 0, 1);
		$needsFixing = (int) $this->_db->loadResult();
		if (!$needsFixing){
			return true;
		}
		
		/* Fix (MySQL doesn't support Regex replacement...) */
		$success = array();
		$old_names = array('mentalspa', 'catchyourbreath');
		foreach($old_names as $name){
			
			/* Build query */
			$from_str = 'http://workitmom.com/bloggers/'.$name.'/';
			$to_str = 'http://workitmom.com/bloggers/parentingwithoutamanual/';
			$query = 'UPDATE `wp_'.$blog_id.'_posts` AS `wpp`
				SET wpp.guid = REPLACE(wpp.guid, "'.$from_str.'", "'.$to_str.'")';
			
			/* Execute query */
			$this->_db->setQuery($query);
			$success[$name] = $this->_db->loadSuccess();
			
		}
		
		/* Return */
		return !in_array(false, $success);
		
	}
	
	/**
	 *	Delete users with firstname and time interval
	 *
	 *	@access public
	 */
	public function deleteNaughtyUsers($firstName, $withinDays)
	{
		
		$query = 'UPDATE users 
SET terminatedtime = 1
WHERE DATE_SUB(NOW(), INTERVAL 1 DAY) <= joined
AND firstname IN ("acidity", "accutane", "acne", "advanced", "allegra", "allergic", "allergy", "alternative", "ampicillin", "antibiotic", "antibiotics", "antidepressant", "anxiety", "arthritis", "asthma", "best", "blood", "body", "bodybuilding", "building", "buspar", "buy", "cancer", "celebrex", "celexa", "cephalexin", "cheap", "cheapest", "cholesterol", "cialis", "clomid", "cosmetic", "cure", "cymbalta", "dental", "depressant", "depressants", "depression", "diabetes", "diabetic", "diflucan", "discount", "doxycycline", "ear", "elavil", "erectile", "exercise", "fast", "fluoxetine", "foot", "free", "fungus", "gastric", "general", "generic", "hdl", "heart", "herbal", "herpes", "high", "hives", "how", "hyperacidity", "hypercholesterolemia", "imitrex", "impotence", "lamisil", "lasix", "legal", "levaquin", "lexapro", "lisinopril", "list", "loss", "lost", "low", "lower", "lysine", "man", "medication", "medicine", "meridia", "muscle", "nail", "narcotic", "natural", "neck", "neuropathy", "neutrogena", "newest", "nexium", "online", "oral", "order", "pain", "prescription", "phentermine", "plavix", "prednisone", "prevacid", "professional", "propecia", "prozac", "purchase", "quick", "rash", "seroquel", "sexual", "skin", "sleeping", "soma", "steroid", "stimulant", "strep", "stress", "surgeon", "symptoms", "synthroid", "topamax", "tramadol", "treatment", "ultram", "valium", "valtrex", "viagra", "weight", "weightloss", "what", "where", "whitening", "xanax", "xenical", "zithromax", "zoloft", "zovirax", "zyban")
OR firstname IN ("ambien", "klonopin", "ativan", "levitra", "no")';
		$this->_db->setQuery($query);
		$this->_db->query();
	}
}

?>