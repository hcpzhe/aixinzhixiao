<?php
//财务中心
class PointsAction extends HomebaseAction {
	
	/**
	 * 积分奖励明细
	 */
	public function listBonus() {
		$bonus_M = New Model('Bonus');
		$map['member_id'] = MID;
		$list = $this->_lists($bonus_M,$map,null);
		$this->assign('list',$list); //积分奖励列表
		
		$mem_ids = field_unique($list, 'source_id'); //列表中用到的会员ID
		$model = New Model('Member');
		$map = array('id'=>array('in',$mem_ids));
		$memlist = $model->where($map)->getField('id,account,realname');
		$this->assign('memlist',$memlist); //列表用到的会员列表 ID为key索引
		
        cookie('_currentUrl_',$_SERVER['REQUEST_URI']);
		$this->display();
	}
	
	/**
	 * 提现list 筛选列表
	 */
	public function listCash() {
		$status = (int)I('status');
		if ($status > 0) $map['status'] = $status;
		$map['member_id'] = MID;
		$model = New Model('Cash');
		$list = $this->_lists($model,$map,null);
		$this->assign('list',$list); //提现列表
		
        cookie('_currentUrl_',$_SERVER['REQUEST_URI']);
		$this->display();
	}
	
	/**
	 * 提现详细
	 */
	public function readCash() {
		$id = (int)I('get.id');
		if ($id <= 0) $this->error('参数非法');
		
		$map['id'] = $id;
		$map['member_id'] = MID;
		$model = New Model('Cash');
		$info = $model->where($map)->find();
		$this->assign('info',$info);
		
        cookie('_currentUrl_',$_SERVER['REQUEST_URI']);
		$this->display();
	}
	
	/**
	 * 提现页面
	 */
	public function viewCash() {
		$model = new CashModel();
		$readycash = $model->getReadyMoney(MID);//用户预提现金额
		$ablecash = $this->_me['points'] - $readycash; //用户可提现最高金额
		$this->assign('readycash',$readycash);
		$this->assign('ablecash',$ablecash);
		
        cookie('_currentUrl_',$_SERVER['REQUEST_URI']);
		$this->display();
	}
	
	/**
	 * 提现提交接口
	 */
	public function addCash() {
		$data = I('post.');
		$data = array_merge($data, array('member_id'=>MID,'status'=>'1'));
		$model = new CashModel();
		if (false === $model->create()) {
			$this->error($model->getError());
		}
		if (false === $model->addNew()) { //添加提现申请记录
			$this->error($model->getError());
		}
		$this->success('提现请求已经提交, 请通知管理员进行审核!',cookie('_currentUrl_'));
	}
}