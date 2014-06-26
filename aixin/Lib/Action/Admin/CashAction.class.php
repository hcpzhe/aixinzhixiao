<?php

class CashAction extends AdminbaseAction {
	
	/**
	 * TODO 记录筛选列表
	 */
	public function lists() {
		
        // 记录当前列表页的cookie
        cookie('_currentUrl_',$_SERVER['REQUEST_URI']);
	}
	
	/**
	 * 记录详情
	 */
	public function read() {
		
	}
	
	/**
	 * 通过审核接口
	 */
	public function passCheck() {
		$id = (int)I('param.id');
		
		if ($id <= 0) $this->error('参数非法');
		$model = New CashModel();
		
		$model->remark = I('param.remark'); //TODO 可能存不进去, 待测试
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
		$id = (int)I('param.id');
		
		if ($id <= 0) $this->error('参数非法');
		$model = New CashModel();
		
		$model->remark = I('param.remark'); //TODO 可能存不进去, 待测试
		if (false===$model->denyCheck($id)) {
			$this->error($model->getError());
		}
		$this->success('拒绝成功',cookie('_currentUrl_'));
	}
}