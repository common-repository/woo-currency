<?php
class mailModelWcu extends modelWcu {
	public function testEmail($email) {
		$email = trim($email);
		if(!empty($email)) {
			if($this->getModule()->send($email, 
				__('Test email functionality', WCU_LANG_CODE),
				sprintf(__('This is a test email for testing email functionality on your site, %s.', WCU_LANG_CODE), WCU_SITE_URL))
			) {
				return true;
			} else {
				$this->pushError( $this->getModule()->getMailErrors() );
			}
		} else
			$this->pushError (__('Empty email address', WCU_LANG_CODE), 'test_email');
		return false;
	}
}