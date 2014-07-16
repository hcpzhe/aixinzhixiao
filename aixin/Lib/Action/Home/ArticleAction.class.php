<?php
class ArticleAction extends HomebaseAction {
	
	/**
	 * 文章筛选列表
	 */
	public function lists() {
		$map['status'] = '1';
		$cat = I('get.category');
		$map['status'] = $cat;
		$model = new Model('Article');
		$list = $this->_lists($model,$map);
		$this->assign('list',$list);
		
        cookie('_currentUrl_',$_SERVER['REQUEST_URI']);
		$this->display();
	}
	
	/**
	 * 文章内容页
	 */
	public function read() {
		$id = (int)I('get.id');
		if ($id <= 0) {
			$cat = I('get.category');
			//如果传入category参数
			//取指定分类的第一条数据
			if (empty($cat)) $this->error('参数非法');
			$map['category'] = $cat;
		} else {
			$map['id'] = $id;
		}
		$map['status'] = '1';
		
		$model = New Model('Article');
		$info = $model->where($map)->find();
		$this->assign('info',$info);
        cookie('_currentUrl_',$_SERVER['REQUEST_URI']);
	}
}