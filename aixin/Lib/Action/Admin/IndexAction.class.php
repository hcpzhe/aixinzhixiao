<?php
class IndexAction extends AdminbaseAction {
    public function index(){
		echo U('Index',array('b'=>'asd'));
    }
    
    public function haha () {
    	var_dump(htmlspecialchars($_GET['b']));
    	var_dump($this->_get("b"));
    }
    
}