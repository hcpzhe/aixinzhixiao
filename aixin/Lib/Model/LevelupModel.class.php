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
		array('apply_time','time',self::MODEL_INSERT,'function'),
	);
	
	/**
	 * 需要预先使用create或data方法 对字段赋值
	 * 升级入库的时候就要处理受益人ID, rec_id通过getRec()获取
	 * 区分 付款升级,待审  和   积分升级,通过
	 */
	public function addRecord() {
		//用户当前级别为最高级别, 则不能升级
		$config_M = New ConfigModel();
		$configs = $config_M->getHash();
		$member_M = New MemberModel();
		$level = $member_M->where('id='.$this->member_id)->getField('level');//当前级别
		if ($level >= $configs['maxlevel']) {
			$this->error = '已升至最高级别, 无需升级!';
			return false;
		}
		//判断是否已经提交过升级, 并且没有被拒绝
		$levelup_M = new Model('Levelup');//这里需要重新实例化, 以免影响$this的操作
		$map = array();
		$map['member_id'] = $this->member_id;
		$map['level_bef'] = $level;
		$map['status'] = 2;//被拒绝的
		$beenbool = $levelup_M->where($map)->count();
		if ($beenbool>0) {
			//已经提交过升级, 并且没有被拒绝, 不再重复提交, 以免管理员重复审核导致数据出错
			return true;
		}
		
		$this->level_bef = $level;
		$this->level_aft = $level+1;
		$this->should_pay = get_shouldpay($level, $configs['basepoints']);
		$this->rec_id = $this->getRec($this->member_id);
		if ($this->type == '2') $this->status = '3';
		else {$this->type = '1'; $this->status = '1';}
		
		return $this->add();
	}
	
	/**
	 * 根据申请用户 获取受益人ID !核心算法!
	 * @param  $id 申请用户ID
	 */
	public function getRec($id) {
		$member_M = New MemberModel();
		$meminfo = $member_M->field('id, parent_id, parent_aid, level')->find($id);
		//level为0时,即新注册用户, 受益人为推荐人
		if ($meminfo['level'] == '0') return $meminfo['parent_id'];
		
		
		//根据用户当前级别, 决定从第几层父级开始查找受益人 level+1
		$level = $meminfo['level'] + 1;//从第几层父级开始查找
		return $this->_getRecLoop($meminfo['parent_aid'], $level);
	}
	/**
	 * 递归查找受益人
	 * @param  $pid 区域父ID
	 * @param  $times 从第几层父级开始 
	 * @param  $now 递归用参数
	 */
	private function _getRecLoop($pid , $times , $now=0) {
		$now++;
		$member_M = New MemberModel();
		if ($pid == 0) {
			return 0; //没有父级的时候, 返回0, 找不到受益人
		}elseif ($now >= $times) {
			//开始查找
			$painfo = $member_M->field('id, parent_aid, level')->find($pid); //父
			if ($painfo['level'] >= $times) {
				return $painfo['id'];
			}else {
				return $this->_getRecLoop($painfo['parent_aid'],$times,$now);
			}
		}else {
			//不满足层级, 进入下一层
			$nextpid = $member_M->where('id='.$pid)->getField('parent_aid');
			return $this->_getRecLoop($nextpid,$times,$now);
		}
	}
	
	/**
	 * 通过审核
	 * @param  $id levelup主键ID
	 */
	public function passCheck($id) {
		$member_M = New Model('Member');
		$info = $this->find($id);
		//判断级别
		$config_M = New ConfigModel();
		$configs = $config_M->getHash();
		$level = $member_M->where('id='.$info['member_id'])->getField('level');//当前级别
		if ($level >= $configs['maxlevel']) {
			$this->remark = $this->error = '已升至最高级别, 无需升级!';
			$this->denyCheck($id);
			return false;
		}
		
		$this->startTrans();
		$data = array('status'=>'3','check_time'=>time(),'remark'=>$this->remark);
		if (false === $this->where('id='.$id)->setField($data)) {
			$this->rollback();
			$this->error = '审核失败, 升级记录更新错误';
			return false;
		}
		
		//更新用户级别
		if (false === $member_M->where('id='.$info['member_id'])->setInc('level')) {
			$this->rollback();
			$this->error = '审核失败, 用户级别更新错误';
			return false;
		}
		
		//存在受益人, 则更新受益人积分
		if ($info['rec_id'] > 0) {
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
		$data = array('status'=>'2','check_time'=>time(),'remark'=>$this->remark);
		if (false === $this->where('id='.$id)->setField($data)) {
			$this->error = '审核失败, 升级记录更新出错';
			return false;
		}
		return true;
	}
}
