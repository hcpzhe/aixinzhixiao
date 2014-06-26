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
		$hash = array(
			'source_id'=>array(
				'model'=>'Member',
				'field'=>'account',
				'theme'=>'%account%'
			)
		);
		$model = New Model();
		foreach ($list as &$row) {
			$tmp = $model->table(C('DB_PREFIX').'Member')->where('id='.$row['source_id'])->getField('account');
			$row['source_id_view'] = $tmp;
		}
		unset($row); //清除指针
		
		$this->assign('list',$list);
		dump($list);
		$this->display();
	}
	
	/**
	 * 提现list 筛选列表
	 */
	public function listCash() {
		
	}
	
	/**
	 * 提现详细
	 */
	public function readCash() {
		
	}
	
	/**
	 * 提现页面
	 */
	public function viewCash() {
		
	}
	
	/**
	 * 提现提交接口
	 */
	public function addCash() {
		//添加的时候要判断   用户当前可用积分 > apply_money
	}
}