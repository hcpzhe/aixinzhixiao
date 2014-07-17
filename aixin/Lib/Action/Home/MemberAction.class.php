<?php
class MemberAction extends HomebaseAction {
	
	//模板变量已赋值过 $_ME记录了当前用户所有资料
	
	//资料查看页面
	public function info() {
		$model = new Model('Member');
		$map = array('id'=>array('in',$this->_me['parent_id'].','.$this->_me['parent_aid']));
		$memlist = $model->where($map)->getField('id,account,realname');
		$this->assign('memlist',$memlist); //页面用到的会员列表, ID为key索引
		
        // 记录当前列表页的cookie
        cookie('_currentUrl_',$_SERVER['REQUEST_URI']);
		$this->display();
	}
	
	//资料修改接口
	public function update() {
		$data = I('param.');
		$update = array();
		$update['realname'] = $data['realname'];
		$update['tel'] = $data['tel'];
		$update['idcard'] = $data['idcard'];
		$update['address'] = $data['address'];
		$update['bank_card'] = $data['bank_card'];
		$update['bank_name'] = $data['bank_name'];
		$update['bank_address'] = $data['bank_address'];
		$model = new Model('Member');
		if (false ===$model->data($update)->save()) $this->error('资料更新失败,请联系制作人员');
		$this->success();
	}
	
	//修改登录密码接口
	public function changePwd() {
		
	}
	
	//修改二级密码接口
	public function changePwdtwo() {
		
	}
	
	//二级密码验证页面
	public function viewPwdtwo() {
		$this->display();
	}
	
	//TODO 验证二级密码接口
	public function checkPwdtwo() {
		
		//验证成功后记录session
		$_SESSION[C('PWDTWO_KEY')]	=	true;
		$this->success('二级密码验证成功',cookie('_currentUrl_'));
	}
	
	//图谱 只显示1层
	public function atlas() {
		
	}
}