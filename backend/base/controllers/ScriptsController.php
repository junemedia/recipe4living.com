<?php

/**
 *	Scripts
 */
class ScriptsController extends ClientBackendController {
	
	/**
	 *	Enabled scripts.
	 *
	 *	Run from BackendScriptsModel.
	 */
	public static $scripts = array(
		'parenting_without_a_manual'	// Fix 'Parenting without a manual' guid links.
	);
	
	/**
	 *	Scripts overview page.
	 */
	public function view(){
		
		/* Get arguments  */
		$args = $this->_args;
		
		/* Execute script */
		if (Utility::iterable($args) && ($script = array_shift($args)) && in_array($script, self::$scripts)){
			
			/* Get model */
			$scriptsModel = BluApplication::getModel('scripts');
			
			/* Execute */
			$success = $scriptsModel->$script();
			
			/* Display result */
			echo $success ? 'Success: ' : 'Failed: ';
			echo $script;
			echo '<br />';
			
		}
		
		/* Display links */
		foreach(self::$scripts as $script){
			echo '<a href="/oversight/scripts/'.$script.'">'.$script.'</a><br />';
		}
		
	}
	
}

?>