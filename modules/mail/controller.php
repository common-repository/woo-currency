<?php
class mailControllerWcu extends controllerWcu {
	public function testEmail() {
		$res = new responseWcu();
		$email = reqWcu::getVar('test_email', 'post');
		if($this->getModel()->testEmail($email)) {
			$res->addMessage(__('Now check your email inbox / spam folders for test mail.'));
		} else 
			$res->pushError ($this->getModel()->getErrors());
		$res->ajaxExec();
	}
	public function saveMailTestRes() {
		$res = new responseWcu();
		$result = (int) reqWcu::getVar('result', 'post');
		frameWcu::_()->getModule('options')->getModel()->save('mail_function_work', $result);
		$res->ajaxExec();
	}
	public function saveOptions() {
		$res = new responseWcu();
		$optsModel = frameWcu::_()->getModule('options')->getModel();
		$submitData = reqWcu::get('post');
		if($optsModel->saveGroup($submitData)) {
			$res->addMessage(__('Done', WCU_LANG_CODE));
		} else
			$res->pushError ($optsModel->getErrors());
		$res->ajaxExec();
	}
	public function getPermissions() {
		return array(
			WCU_USERLEVELS => array(
				WCU_ADMIN => array('testEmail', 'saveMailTestRes', 'saveOptions')
			),
		);
	}
}
