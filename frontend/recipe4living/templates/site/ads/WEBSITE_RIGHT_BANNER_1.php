<?php

// don't include all the ads on login page cause Adsense don't like it

include BLUPATH_TEMPLATES.'/site/ads/zergnet_49586.php';

if ($this->_doc->getTitle() !== 'Login') {
	// nothing at this time
}
