<?php

// don't include all the ads on login page cause Adsense don't like it

include BLUPATH_TEMPLATES.'/site/ads/zergnet_49586.php';

if ($this->_doc->getTitle() !== 'Login') {
	include BLUPATH_TEMPLATES.'/site/ads/medianet_rail_300x125.php';
}
