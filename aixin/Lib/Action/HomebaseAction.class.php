<?php
abstract class HomebaseAction extends Action {
	function _initialize() {
		import('ORG.Util.Cookie');
		if (C('USER_AUTH_ON') && !in_array(MODULE_NAME, explode(',', C('NOT_AUTH_MODULE')))) {
			if(!isset($_SESSION[C('USER_AUTH_KEY')])) {
				$this->redirect(C('USER_AUTH_GATEWAY'));
			}
		}
	}
}