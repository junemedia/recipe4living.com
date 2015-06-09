<?php

/**
 *	Blogs model
 *
 *	@package BluApplication
 *	@subpackage FrontendModels
 */
class ClientFrontendBlogsModel extends BluModel
{
	/**
	 *	Get "Chew on that" blog posts.
	 *
	 *	@uses SimpleXML
	 *	@access public
	 *	@param int Offset
	 *	@param int Limit
	 *	@return array Blog posts
	 */
	public function getChewonthatBlog($offset = null, $limit = null)
	{
		// Get from cache
		$cacheKey = 'blogs_chewOnThat';
		$blog = $this->_cache->get($cacheKey);
		if ($blog == 'curlfail') return false;
		if ($blog === false) {
			
			// Get from "Chew on that" RSS feed.
			$feed = Utility::curl('http://www.chewonthatblog.com/feed/',null,'',2);
			if ($feed ==false){
				$this->_cache->set($cacheKey, 'curlfail', 60 * 60 * 2);
				$this->_cache->set($cacheKey, $this->_cache->get($cacheKey.'_backup'), 60 * 60 * 2);
				return false;
			}	
			// Convert into XML object
			$feed = new SimpleXMLElement($feed);
			$namespaces = $feed->getNamespaces(true);
			$blog = array(
				'title' => (string) $feed->channel->title,
				'link' => (string) $feed->channel->link
			);
			foreach ($feed->channel->item as $item) {
				
				// Load external namespaces
				$dc = $item->children($namespaces['dc']);
				
				// Parse post
				$post = array(
					'title' => (string) $item->title,
					'link' => (string) $item->link,
					'author' => (string) $dc->creator,
					'date' => (string) $item->pubDate,
					'guid' => (string) $item->guid,
					'description' => trim((string) $item->description)
				);
				
				// ...and its comments
				$wfw = $item->children($namespaces['wfw']);
				if ($postComments = (string) $wfw->commentRss) {
					
					// Load comments RSS
					$commentsFeed = Utility::curl($postComments);
					
					// Convert into XML object
					$commentsFeed = new SimpleXMLElement($commentsFeed);
					$commentNamespaces = $commentsFeed->getNamespaces(true);
					foreach ($commentsFeed->channel->item as $comment) {
						
						// Load namespaces
						$commentDc = $comment->children($commentNamespaces['dc']);
						
						// Parse comment
						$post['comments'][] = array(
							'title' => (string) $comment->title,
							'link' => (string) $comment->link,
							'author' => (string) $commentDc->creator,
							'guid' => (string) $comment->guid,
							'description' => trim((string) $comment->description)
						);
					}
				}
				
				// Append post
				$blog['posts'][] = $post;
			}
			
			// Store in cache
			$this->_cache->set($cacheKey, $blog, 60 * 60 * 2);
			$this->_cache->set($cacheKey.'_backup', $blog);
		}
		
		// Slice and dice
		if (($offset || $limit) && isset($blog['posts']) ) {
			$blog['posts'] = array_slice($blog['posts'], $offset, $limit, true);
		}
		
		// Return
		return $blog;
	}
}

?>
