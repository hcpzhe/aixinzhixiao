<?php
//会员
class MessageAction extends AdminbaseAction {
	
	/**
	 * 所有会员留言
	 */
	public function lists() {
		$model = new Model('Message');
		//$model_m = new Model('Member');
		$list = $model->table('ax_member member, ax_message message')->where('member.id = message.member_id')->field('member.account as account, member.realname as realname, message.id as id, message.content as content,message.send_time as send_time,message.reply as reply,message.status as status')->order('message.send_time desc' )->select();
        $this->assign('list', $list); //会员留言    
        // 记录当前列表页的cookie
        cookie(C('CURRENT_URL_NAME'),$_SERVER['REQUEST_URI']);
         $this->display();
	}
	
	/**
	 * 会员留言详细
	 * @param  $id		
	 */
	public function info() {
		$id = (int)I('id');
		if ($id <= 0) $this->error('参数非法');
		$model = new Model('Message');
		$info = $model->where('id='.$id)->find();
		$this->assign('info',$info);       //留言信息
	    $model_m = new Model('Member');
		$memlist = $model_m->where('id='.$info['member_id'])->getField('id,account,realname,tel,address');
		$this->assign('memlist',$memlist); //列表用到的会员信息
		
        // 记录当前列表页的cookie
        cookie(C('CURRENT_URL_NAME'),$_SERVER['REQUEST_URI']);
		$this->display();
	}
	
	/**
	 * 会员留言 回复、信息更新 接口
	 * @param  $id		
	 */
	public function update() {
		$id = (int)I('param.id');
		if ($id <= 0) $this->error('参数非法');
		$newdata = array();
		$newdata['id'] = I('param.id');
		$newdata['reply'] = I('param.reply');
		$newdata['status'] = I('param.status');
		$model = new MessageModel();
		if (false === $model->create($newdata)) $this->error($model->getError());
		if (false === $model->where('id='.$id)->save()) $this->error('更新失败');
		$this->success('更新成功');
	}
	

	
}