<?php
class MemberAction extends HomebaseAction {
	
	//资料查看页面
	public function info() {
		
	}
	
	//资料修改接口
	public function update() {
		
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