<?php
class mailViewWcu extends viewWcu {
	public function getTabContent() {
		frameWcu::_()->getModule('templates')->loadJqueryUi();
		frameWcu::_()->addScript('admin.'. $this->getCode(), $this->getModule()->getModPath(). 'js/admin.'. $this->getCode(). '.js');
		
		$this->assign('options', frameWcu::_()->getModule('options')->getCatOpts( $this->getCode() ));
		$this->assign('testEmail', frameWcu::_()->getModule('options')->get('notify_email'));
		return parent::getContent('mailAdmin');
	}
}
