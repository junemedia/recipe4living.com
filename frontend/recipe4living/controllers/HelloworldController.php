<?php

/**
 * Demo Hello World controller
 **/

class Recipe4LivingHelloworldController extends ClientFrontendController
{
	public function view() { 
		$value = Request::getString('message');
		$helloModel = BluApplication::getModel('helloworld');
	}

	public function stuff() {
		echo "Sure is some stuff.";
	}
}
