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
		
		$this->display();
	}
	
	/**
	 * 提现list 筛选列表
	 */
	public function listCash() {
		$status = (int)I('get.status');
		if ($status > 0) $map['status'] = $status;
		$map['member_id'] = MID;
		$model = New Model('Cash');
		$list = $this->_lists($model,$map,null);
		$this->assign('list',$list); //提现列表
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
		$this->display();
	}
	
	/**
	 * 提现页面
	 */
	public function viewCash() {
		$this->display();
	}
	
	/**
	 * 提现提交接口
	 */
	public function addCash() {
		//添加的时候要判断   用户当前可用积分 > apply_money
	}
}