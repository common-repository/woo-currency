<?php
class optionsControllerWcu extends controllerWcu {
	public function saveGroup() {
		$res = new responseWcu();
		if($this->getModel()->saveGroup(reqWcu::get('post'))) {
			$res->addMessage(__('Done', WCU_LANG_CODE));
		} else
			$res->pushError ($this->getModel('options')->getErrors());
		return $res->ajaxExec();
	}
	public function getPermissions() {
		return array(
			WCU_USERLEVELS => array(
				WCU_ADMIN => array('saveGroup')
			),
		);
	}
}

