<?php
class ArticleAction extends AdminbaseAction {
	
	/**
	 * TODO 文章筛选列表
	 */
	public function lists() {
		
		$map['status'] = '1';
		
        cookie('_currentUrl_',$_SERVER['REQUEST_URI']);
		
	}
	
	/**
	 * 文章内容页
	 */
	public function read() {
		$id = (int)I('get.id');
		if ($id <= 0) $this->error('参数非法');
		
		$map['status'] = '1';
		
        cookie('_currentUrl_',$_SERVER['REQUEST_URI']);
	}
	
	/**
	 * 更新文章接口
	 */
	public function update() {
		
	}
	
	/**
	 * 新增文章页面
	 */
	public function add() {
		
        cookie('_currentUrl_',$_SERVER['REQUEST_URI']);
	}
	
	/**
	 * 新增文章接口
	 */
	public function insert() {
		
	}
}