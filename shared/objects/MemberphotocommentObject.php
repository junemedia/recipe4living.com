<?php

/**
 *	A member photo comment.
 */
class MemberphotocommentObject extends StandardcommentObject {

	/**
	 *	Required by CommentObject.
	 */
	public function getThing(){
		$photosModel = $this->getModel('photos');
		return $photosModel->getPhoto('member', $this->thingID);
	}
	
}

?>