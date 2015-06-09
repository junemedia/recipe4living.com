<?

interface CommentsInterface{
	
	/**
	 *	Get list of all comments, ordered by date by default.
	 *
	 *	@return (array) CommentObject objects
	 */
	function getComments();
	
	/**
	 *	Get number of comments
	 *
	 *	@return int.
	 */
	function getCommentCount();
	
	/**
	 *	Add a comment
	 *
	 *	@args (array) arguments: the columns in the 'comments' table. 
	 */
	function addComment(array $arguments);
	
}