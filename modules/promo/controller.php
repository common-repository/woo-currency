<?php
class promoControllerWcu extends controllerWcu {
    public function welcomePageSaveInfo() {
		$res = new responseWcu();
		installerWcu::setUsed();
		if($this->getModel()->welcomePageSaveInfo(reqWcu::get('get'))) {
			$res->addMessage(__('Information was saved. Thank you!', WCU_LANG_CODE));
		} else {
			$res->pushError($this->getModel()->getErrors());
		}
		$originalPage = reqWcu::getVar('original_page');
		$http = isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) ? 'https://' : 'http://';
		if(strpos($originalPage, $http. $_SERVER['HTTP_HOST']) !== 0) {
			$originalPage = '';
		}
		redirectWcu($originalPage);
	}
	public function sendContact() {
		$res = new responseWcu();
		$time = time();
		$prevSendTime = (int) get_option(WCU_CODE. '_last__time_contact_send');
		if($prevSendTime && ($time - $prevSendTime) < 5 * 60) {	// Only one message per five minutes
			$res->pushError(__('Please don\'t send contact requests so often - wait for response for your previous requests.'));
			$res->ajaxExec();
		}
        $data = reqWcu::get('post');
        $fields = $this->getModule()->getContactFormFields();
		foreach($fields as $fName => $fData) {
			$validate = isset($fData['validate']) ? $fData['validate'] : false;
			$data[ $fName ] = isset($data[ $fName ]) ? trim($data[ $fName ]) : '';
			if($validate) {
				$error = '';
				foreach($validate as $v) {
					if(!empty($error))
						break;
					switch($v) {
						case 'notEmpty':
							if(empty($data[ $fName ])) {
								$error = $fData['html'] == 'selectbox' ? __('Please select %s', WCU_LANG_CODE) : __('Please enter %s', WCU_LANG_CODE);
								$error = sprintf($error, $fData['label']);
							}
							break;
						case 'email':
							if(!is_email($data[ $fName ])) 
								$error = __('Please enter valid email address', WCU_LANG_CODE);
							break;
					}
					if(!empty($error)) {
						$res->pushError($error, $fName);
					}
				}
			}
		}
		if(!$res->error()) {
			$msg = 'Message from: '. get_bloginfo('name').', Host: '. $_SERVER['HTTP_HOST']. '<br />';
			$msg .= 'Plugin: '. WCU_WP_PLUGIN_NAME. '<br />';
			foreach($fields as $fName => $fData) {
				if(in_array($fName, array('name', 'email', 'subject'))) continue;
				if($fName == 'category')
					$data[ $fName ] = $fData['options'][ $data[ $fName ] ];
                $msg .= '<b>'. $fData['label']. '</b>: '. nl2br($data[ $fName ]). '<br />';
            }
			if(frameWcu::_()->getModule('mail')->send('support@woobewoo.zendesk.com', $data['subject'], $msg, $data['name'], $data['email'])) {
				update_option(WCU_CODE. '_last__time_contact_send', $time);
			} else {
				$res->pushError( frameWcu::_()->getModule('mail')->getMailErrors() );
			}
			
		}
        $res->ajaxExec();
	}
	public function addNoticeAction() {
		$res = new responseWcu();
		$code = reqWcu::getVar('code', 'post');
		$choice = reqWcu::getVar('choice', 'post');
		if(!empty($code) && !empty($choice)) {
			$optModel = frameWcu::_()->getModule('options')->getModel();
			switch($choice) {
				case 'hide':
					$optModel->save('hide_'. $code, 1);
					break;
				case 'later':
					$optModel->save('later_'. $code, time());
					break;
				case 'done':
					$optModel->save('done_'. $code, 1);
					if($code == 'enb_promo_link_msg') {
						$optModel->save('add_love_link', 1);
					}
					break;
			}
			$this->getModel()->saveUsageStat($code. '.'. $choice, true);
			$this->getModel()->checkAndSend( true );
		}
		$res->ajaxExec();
	}
	public function addTourStep() {
		$res = new responseWcu();
		if($this->getModel()->addTourStep(reqWcu::get('post'))) {
			$res->addMessage(__('Information was saved. Thank you!', WCU_LANG_CODE));
		} else
			$res->pushError($this->getModel()->getErrors());
		$res->ajaxExec();
	}
	public function closeTour() {
		$res = new responseWcu();
		if($this->getModel()->closeTour(reqWcu::get('post'))) {
			$res->addMessage(__('Information was saved. Thank you!', WCU_LANG_CODE));
		} else
			$res->pushError($this->getModel()->getErrors());
		$res->ajaxExec();
	}
	public function addTourFinish() {
		$res = new responseWcu();
		if($this->getModel()->addTourFinish(reqWcu::get('post'))) {
			$res->addMessage(__('Information was saved. Thank you!', WCU_LANG_CODE));
		} else
			$res->pushError($this->getModel()->getErrors());
		$res->ajaxExec();
	}
	public function saveDeactivateData() {
		$res = new responseWcu();
		if($this->getModel()->saveDeactivateData(reqWcu::get('post'))) {
			$res->addMessage(__('Thank you for Feedback!', WCU_LANG_CODE));
		} else
			$res->pushError($this->getModel()->getErrors());
		$res->ajaxExec();
	}
	public function enbStatsOpt() {
		$res = new responseWcu();
		frameWcu::_()->getModule('options')->getModel()->save('send_stats', 1);
		$res->ajaxExec();
	}
	/**
	 * @see controller::getPermissions();
	 */
	public function getPermissions() {
		return array(
			WCU_USERLEVELS => array(
				WCU_ADMIN => array('welcomePageSaveInfo', 'sendContact', 'addNoticeAction',
					'addStep', 'closeTour', 'addTourFinish', 'saveDeactivateData', 'enbStatsOpt')
			),
		);
	}
}