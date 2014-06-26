<?php
/*
 * MUST_VALIDATE	必须验证 不管表单是否有设置该字段
 * VALUE_VALIDATE	值不为空的时候才验证
 * EXISTS_VALIDATE	表单存在该字段就验证   (默认)
 */
// 升级模型
class LevelupModel extends Model {
	protected $_validate	=	array(
		array('member_id','require','会员编号必须'),
		array('should_pay','require','应付金额必须'),
		array('real_pay','require','实际打款金额必须'),
		array('pay_time','require','打款时间必须'),
		
		array('status',array(1,2,3),'升级状态非法',self::VALUE_VALIDATE,'in'),//1-待审核, 2-拒绝, 3-通过
		array('type',array(1,2),'升级类型非法',self::VALUE_VALIDATE,'in'),//1-付款升级 2-积分升级
	);

	protected $_auto		=	array(
		array('create_time','time',self::MODEL_INSERT,'function'),
	);
	
	/**
	 * 需要预先使用create或data方法 对字段赋值
	 * TODO 升级入库的时候就要处理受益人ID, 便于管理员审核的时候看到受益人是谁, 如果上级没有收益用户, rec_id为0
	 * 区分 付款升级,待审  和   积分升级,通过
	 */
	public function addRecord() {
		
	}
	
	/**
	 * 根据申请用户 获取受益人ID !核心算法!
	 * @param  $id 申请用户ID
	 */
	private function getRec($id) {
		$member_M = New Model('Member');
		$meminfo = $member_M->field('id, parent_id, level')->find($id);
		//根据用户当前级别, 决定从第几层父级开始查找受益人 level+1
		$level = $meminfo['level'] + 1;//从第几层父级开始查找
		return $this->_getRecLoop($meminfo['parent_id'], $level);
	}
	/**
	 * 递归查找受益人
	 * @param  $pid 父ID
	 * @param  $times 从第几层父级开始 
	 * @param  $now 递归用参数
	 */
	private function _getRecLoop($pid , $times , $now=0) {
		$now++;
		$member_M = New Model('Member');
		if ($pid == 0) {
			return 0; //没有父级的时候, 返回0, 找不到受益人
		}elseif ($now >= $times) {
			//开始查找
			$pinfo = $member_M->field('id, parent_id, level')->find($pid); //父
			if ($pinfo['level'] >= $times) {
				return $pinfo['id'];
			}else {
				return $this->_getRecLoop($pinfo['parent_id'],$times,$now);
			}
		}else {
			//不满足层级, 进入下一层
			$nextpid = $member_M->where('id='.$pid)->getField('parent_id');
			return $this->_getRecLoop($nextpid,$times,$now);
		}
	}
	
	/**
	 * 通过审核
	 * @param  $id levelup主键ID
	 */
	public function passCheck($id) {
		$this->startTrans();
		$data = array('status'=>'3','check_time'=>time());
		if (false === $this->where('id='.$id)->setField($data)) {
			$this->rollback();
			$this->error = '审核失败, 升级记录更新错误';
			return false;
		}
		$info = $this->find($id);
		//存在受益人, 则更新受益人积分
		if ($info['rec_id'] > 0) {
			$member_M = New Model('Member');
			if (false === $member_M->where('id='.$info['rec_id'])->setInc('points',$info['should_pay'])) {
				$this->rollback();
				$this->error = '审核失败, 受益人积分更新错误';
				return false;
			}
		}
		
		//在bonus表中记录
		$data = array(
			'member_id' => $info['rec_id'],
			'source_id' => $info['id'],
			'bonus' => $info['should_pay'],
			'create_time' => time()
		);
		$bonus_M = New Model('Bonus');
		if (false === $bonus_M->data($data)->add()) {
			$this->rollback();
			$this->error = '审核失败, 奖金记录添加错误';
			return false;
		}
		$this->commit();
		return true;
	}
	
	/**
	 * 拒绝审核
	 * @param  $id levelup主键ID
	 */
	public function denyCheck($id) {
		$data = array('status'=>'2','check_time'=>time());
		if (false === $this->where('id='.$id)->setField($data)) {
			$this->error = '审核失败, 升级记录更新错误';
			return false;
		}
		return true;
	}
}
