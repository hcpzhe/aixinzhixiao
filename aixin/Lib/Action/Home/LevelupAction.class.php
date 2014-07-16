<?php

class LevelupAction extends HomebaseAction {
	
	/**
	 * 升级记录筛选列表
	 * 状态 1-待审核, 2-拒绝, 3-通过
	 */
	public function lists($status=1) {
		$map = array();
		$map['member_id'] = MID;
		$map['status'] = $status;
		$this->assign('status',$map['status']);
		
		$model = new Model('Levelup');
		$list = $this->_lists($model,$map,'level_bef desc,id desc');
		$this->assign('list',$list);
		
		$mem_ids = field_unique($list, 'rec_id'); //列表中用到的会员ID
		$map = array('id'=>array('in',$mem_ids));
		$memlist = $model->where($map)->getField('id,account,realname');
		$this->assign('memlist',$memlist); //列表中用到的会员列表, ID为key索引
		
        // 记录当前列表页的cookie
        cookie('_currentUrl_',$_SERVER['REQUEST_URI']);
        $this->display();
	}
	
	/**
	 * 我要升级页面  先验证二级密码
	 * 收款账户显示的为公司账户, 由公司审核后,受益人获取积分
	 */
	public function levelup() {
		cookie('_currentUrl_',$_SERVER['REQUEST_URI']);
		if ($_SESSION[C('PWDTWO_KEY')]) {
			//验证过二级密码了
			unset($_SESSION[C('PWDTWO_KEY')]);
		}else {
			$this->redirect('Member/viewPwdtwo');//跳转至二级密码验证页面 ~ 验证成功后返回至_currentUrl_
		}
		$member_M = New MemberModel();
		$meinfo = $member_M->find(MID);
		if ($meinfo['level'] >= $this->_cfgs['maxlevel']) $this->error('您已经达到最高级别了!', U('Index/welcome'));
		
		$need_pts = get_shouldpay($meinfo['level'], $this->_cfgs['basepoints']);//所需积分
		$this->assign('nedd_money',$need_pts);
		
		$levelup_M = New LevelupModel();
		$rec_id = $levelup_M->getRec(MID);//受益人ID
		$rec_info = $member_M->find($rec_id);
		$this->assign('rec_info',$rec_info);//受益人信息
		
		$this->display();
	}
	
	/**
	 * 付款升级接口
	 */
	public function payToup() {
		$model = New LevelupModel();
		$data = I('post.');
		$data = array_merge($data, array('member_id'=>MID));
		$model->create($data);
		if (false === $model->addRecord()) { //添加积分升级记录
			$this->error($model->getError());
		}
		$this->success('升级请求已经提交, 请通知管理员进行审核!',cookie('_currentUrl_'));
	}
	
	/**
	 * 积分升级接口
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
		$data = array();
		$data['member_id'] = MID;
		$data['type'] = 2;
		$data['remark'] = I('remark');
		$levelup_M->create($data);
		if (false === $levelup_M->addRecord()) { //添加积分升级记录
			$this->error($levelup_M->getError());
		}
		$this->success('升级成功, 请刷新网站!',cookie('_currentUrl_'));
	}
}