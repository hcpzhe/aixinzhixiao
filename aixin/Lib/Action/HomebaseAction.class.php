<?php
abstract class HomebaseAction extends Action {
	
	protected $_cfgs;
	protected $_me;
	
	protected function _initialize() {
		import('ORG.Util.Cookie');
//		if (C('USER_AUTH_ON') && !in_array(MODULE_NAME, explode(',', C('NOT_AUTH_MODULE')))) {
//			if(!isset($_SESSION[C('USER_AUTH_KEY')])) {
//				$this->redirect(C('USER_AUTH_GATEWAY'));
//			}
//		}
		define(MID, $_SESSION[C('USER_AUTH_KEY')]);
		$model = New ConfigModel();
		$this->_cfgs = $model->getHash();
		$this->assign('_CFG', $this->_cfgs);
		
		$member_M = New Model('Member');
		$this->_me = $member_M->find(MID);
		if (empty($this->_me)) $this->redirect(C('USER_AUTH_GATEWAY'));
		$this->assign('_ME',$this->_me);
	}
	
}