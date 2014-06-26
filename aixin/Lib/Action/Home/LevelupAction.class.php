<?php

class LevelupAction extends HomebaseAction {
	
	/**
	 * TODO 升级记录筛选列表
	 */
	public function lists() {
		
        // 记录当前列表页的cookie
        cookie('_currentUrl_',$_SERVER['REQUEST_URI']);
	}
	
	/**
	 * TODO 我要升级页面  先验证二级密码
	 */
	public function levelup() {
		cookie('_currentUrl_',$_SERVER['REQUEST_URI']);
		if ($_SESSION[C('PWDTWO_KEY')]) {
			//验证过二级密码了
			unset($_SESSION[C('PWDTWO_KEY')]);
		}else {
			$this->redirect('Member/viewPwdtwo');//跳转至二级密码验证页面 ~~
		}
		
	}
	
	/**
	 * TODO 付款升级接口
	 */
	public function payToup() {
		$model = New LevelupModel();
		$data = I('post.');
		$data = array_merge($data, array('member_id'=>MID));
		$model->create($data);
		$model->addRecord(); //添加付款升级记录
	}
	
	/**
	 * TODO 积分升级接口
	 */
	public function pointsToup() {
		//判断   所需积分 >= 余额-预提现积分
		$member_M = New MemberModel();
		$cash_M = New CashModel();
		$meinfo = $member_M->find(MID);
		if ($meinfo['level'] >= $this->_cfgs['maxlevel']) $this->error('您已经达到最高级别了!');
		
		$need_pts = get_shouldpay($meinfo['level'], $this->_cfgs['basepoints']);//所需积分
		$ready_pts = $cash_M->getReadyMoney(MID);
		if ($need_pts < $meinfo['points']-$ready_pts) $this->error('积分余额不足, 无法使用积分升级');
		
		$levelup_M = New LevelupModel();
		$data = I('post.');
		$data = array_merge($data, array('member_id'=>MID));
		$levelup_M->create($data);
		$levelup_M->addRecord(); //添加积分升级记录
		
	}
}