<?php
/*
 * MUST_VALIDATE	必须验证 不管表单是否有设置该字段
 * VALUE_VALIDATE	值不为空的时候才验证
 * EXISTS_VALIDATE	表单存在该字段就验证   (默认)
 */
// 提现模型
class CashModel extends Model {
	
	protected $_validate	=	array(
		array('member_id','require','会员编号必须'),
		array('real_name','require','开户姓名必须'),
		array('bank_name','require','开户银行必须'),
		array('bank_card','require','银行卡号必须'),
		array('bank_address','require','开户地址必须'),
		array('apply_money','require','申请提现金额必须'),
		
		array('status',array(1,2,3),'提现状态非法',self::VALUE_VALIDATE,'in'),//1-未审 2-审核未通过 3-已审
	);

	protected $_auto		=	array(
		array('create_time','time',self::MODEL_INSERT,'function'),
	);
	
	/**
	 * 获取预用预提现金额   (申请但未审核的)
	 * @param $mid member_id
	 */
	public function getReadyMoney($mid) {
		$map = array();
		$map['member_id'] = $mid;
		$map['status'] = 1;
		return $this->where($map)->sum('apply_money');
	}
	
	/**
	 * 通过    通过后, 要更新用户积分
	 * @param  $id
	 */
	public function passCheck($id) {
		$this->startTrans();
		$data = array('status'=>'3','check_time'=>time(),'remark'=>$this->remark);
		if (false === $this->where('id='.$id)->setField($data)) {
			$this->error = '审核失败, 升级记录更新错误';
			return false;
		}
		$info = $this->find($id);
		//存在受益人, 则更新受益人积分
		if ($info['member_id'] > 0) {
			$member_M = New Model('Member');
			if (false === $member_M->where('id='.$info['member_id'])->setDec('points',$info['apply_money'])) {
				$this->rollback();
				$this->error = '审核失败, 用户积分更新错误';
				return false;
			}
		}else {
			$this->rollback();
			$this->error = '审核失败, 找不到对应用户';
			return false;
		}
		
		$this->commit();
		return true;
	}
	
	/**
	 * 拒绝
	 * @param  $id
	 */
	public function denyCheck($id) {
		$data = array('status'=>'2','check_time'=>time(),'remark'=>$this->remark);
		if (false === $this->where('id='.$id)->setField($data)) {
			$this->error = '审核失败, 升级记录更新错误';
			return false;
		}
		return true;
	}
}
