<?php
abstract class AdminbaseAction extends Action {
	
	protected $_cfgs;
	
	protected function _initialize() {
		import('ORG.Util.Cookie');
		// 用户权限检查
		if (C('USER_AUTH_ON') && !in_array(MODULE_NAME, explode(',', C('NOT_AUTH_MODULE')))) {
			import('ORG.Util.RBAC');
			if (!RBAC::AccessDecision(GROUP_NAME)) {
				//检查认证识别号
				if (!$_SESSION [C('USER_AUTH_KEY')]) {
					//跳转到认证网关
					$this->redirect(C('USER_AUTH_GATEWAY'));
				}
				// 没有权限 抛出错误
				if (C('RBAC_ERROR_PAGE')) {
					// 定义权限错误页面
					redirect(C('RBAC_ERROR_PAGE'));
				} else {
					if (C('GUEST_AUTH_ON')) {
						$this->assign('jumpUrl', U(C('USER_AUTH_GATEWAY')));
					}
					// 提示错误信息
					$this->error(L('_VALID_ACCESS_'));
				}
			}
		}
		
		$model = New ConfigModel();
		$this->_cfgs = $model->getHash();
		$this->assign('_CFG', $this->_cfgs);
	}
	
	
}