<?php

class ConfigAction extends AdminbaseAction {
	
	public function info() {
        // 记录当前列表页的cookie
        $model = new Model('Config');
        $map = array();
        $map['name'] = array('not in','basepoints,maxlevel');
        $list = $model->where($map)->select();
        $this->assign('list',$list);
        
        cookie('_currentUrl_',$_SERVER['REQUEST_URI']);
        $this->display();
	}
	
	/**
	 * 更新接口
	 * 需用用ajax来组合数据进行更新 
	 */
	public function update() {
		$datas = I('newdata');
		$model = new ConfigModel();
		$model->startTrans();
		foreach ($datas as $row) {
			$tmpdata = array('cfgval'=>$row['cfgval']);
			if (false === $model->where('id='.$row['id'])->save($tmpdata)) {
				$model->rollback();
				$this->error('更新出错!!');
			}
		}
		$this->success('更新成功');
	}
	
}