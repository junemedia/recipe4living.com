<?php
/**
 * LiveRamp Match Partner tags
 */
$lr_ei_tagid = '424276';
$lr_rc_tagid = '424316';

// open the tag...
$lr_tag = '<iframe name="_rlcdn" width=0 height=0 frameborder=0 src="';

// if user is logged in, serve match partner tag
if (isset($currentUser['email']) && $currentUser['email'] != '') {
	$lr_tag .= '//ei.rlcdn.com/' . $lr_ei_tagid . '.html';
	$lr_tag .= '?s=' . sha1(strtolower($currentUser['email']));
}

// otherwise serve recookier tag
else {
	$lr_tag .= '//rc.rlcdn.com/' . $lr_rc_tagid . '.html';
}

// ...close the tag
$lr_tag .= '"></iframe>';

echo $lr_tag;
