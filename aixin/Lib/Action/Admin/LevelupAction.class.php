<?php

class LevelupAction extends AdminbaseAction {
	
	/**
	 * 升级记录筛选列表
	 */
	public function lists($type=1, $status=1, $account=null, $bef=null) {
		$model = New Model('Levelup');
		$member_M = new Model('Member');
		//查询条件
        if(isset($account)) {
            $memmap['account']   =   array('like', '%'.$account.'%');
            $map['member_id'] = $member_M->where($memmap)->getField('id');
            if (empty($map['member_id'])) $this->error('找不到用户',cookie('_currentUrl_'));
        }else {
        	$map['type'] = $type;
        	if ($map['type'] == '2') $map['status'] = '3';
        	else $map['status'] = $status;
        }
        if (isset($bef) && $bef >=0 && $bef <=4) {
        	$map['level_bef'] = $bef;
        }else {
        	//默认查询升级记录
        	$map['level_bef'] = array('in','1,2,3,4');
        }
        
        $list = $this->_lists($model,$map);
        
        $this->assign('list', $list); //升级列表
        $this->assign('status', $map['status']); //用于筛选条件的显示
        $this->assign('type', $map['type']); //用于筛选条件的显示
        $this->assign('account', $account); //用于筛选条件的显示
        
		$mem_ids = field_unique($list, 'member_id,rec_id'); //列表中用到的会员ID
		$map = array('id'=>array('in',$mem_ids));
		$memlist = $member_M->where($map)->getField('id,account,realname');
		$this->assign('memlist',$memlist); //列表用到的会员列表, ID为key索引
        
        // 记录当前列表页的cookie
        cookie('_currentUrl_',$_SERVER['REQUEST_URI']);
        $this->display();
	}
	
	/**
	 * 升级记录详情
	 */
	public function read() {
		$id = (int)I('id');
		if ($id <= 0) $this->error('参数非法');
		
		$model = New Model('Levelup');
		$member_M = new Model('Member');
		
		$info = $model->find($id);
		$map['id'] = array('in',$info['member_id'].','.$info['rec_id']);
		$memlist = $member_M->where($map)->getField('id,account,realname');
		
        $this->assign('list', $info); //升级信息
		$this->assign('memlist',$memlist); //列表用到的会员列表, ID为key索引
		
        // 记录当前列表页的cookie
        cookie('_currentUrl_',$_SERVER['REQUEST_URI']);
        $this->display();
	}
	
	/**
	 * 通过审核接口
	 */
	public function passCheck() {
		$id = (int)I('id');
		if ($id <= 0) $this->error('参数非法');
		
		$model = New LevelupModel();
		$model->remark = I('remark');
		if (false===$model->passCheck($id)) {
			$this->error($model->getError());
		}
		$this->success('审核成功',cookie('_currentUrl_'));
	}
	
	/**
	 * 拒绝审核接口
	 */
	public function denyCheck() {
		//建议, 拒绝的时候给出页面, 让管理员填入拒绝原因,存入remark字段
		$id = (int)I('id');
		
		if ($id <= 0) $this->error('参数非法');
		$model = New LevelupModel();
		
		$model->remark = I('remark');
		if (false===$model->denyCheck($id)) {
			$this->error($model->getError());
		}
		$this->success('拒绝成功',cookie('_currentUrl_'));
	}
}