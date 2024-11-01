<?php
class promoViewWcu extends viewWcu {
    public function displayAdminFooter() {
		$this->assign('pluginSlug', frameWcu::_()->getModule('adminmenu')->getMainSlug());
        parent::display('adminFooter');
    }
	public function getOverviewTabContent() {
		frameWcu::_()->getModule('templates')->loadJqueryUi();
		
		frameWcu::_()->getModule('templates')->loadSlimscroll();
		frameWcu::_()->addScript('admin.overview', $this->getModule()->getModPath(). 'js/admin.overview.js');
		frameWcu::_()->addStyle('admin.overview', $this->getModule()->getModPath(). 'css/admin.overview.css');
		$this->assign('mainLink', $this->getModule()->getMainLink());
		$this->assign('faqList', $this->getFaqList());
		$this->assign('serverSettings', $this->getServerSettings());
		$this->assign('news', $this->getNewsContent());
		$this->assign('contactFields', $this->getModule()->getContactFormFields());
		return parent::getContent('overviewTabContent');
	}
	public function getFaqList() {
		return array();
	}
	public function getMostFaqList() {
		return array();
	}
	public function getNewsContent() {
		return '';
		/*$getData = wp_remote_get('http://woobewoo.com/news/main.html');
		$content = '';
		if($getData 
			&& is_array($getData) 
			&& isset($getData['response']) 
			&& isset($getData['response']['code']) 
			&& $getData['response']['code'] == 200
			&& isset($getData['body'])
			&& !empty($getData['body'])
		) {
			$content = $getData['body'];
		} else {
			$content = sprintf(__('There were some problems while trying to retrieve our news, but you can always check all list <a target="_blank" href="%s">here</a>.', WCU_LANG_CODE), 'http://woobewoo.com/news');
		}
		return $content;*/
	}
	public function getServerSettings() {
		global $wpdb;
		return array(
			'Operating System' => array('value' => PHP_OS),
            'PHP Version' => array('value' => PHP_VERSION),
            'Server Software' => array('value' => $_SERVER['SERVER_SOFTWARE']),
			'MySQL' => array('value' =>  $wpdb->db_version()),
            'PHP Allow URL Fopen' => array('value' => ini_get('allow_url_fopen') ? __('Yes', WCU_LANG_CODE) : __('No', WCU_LANG_CODE)),
            'PHP Memory Limit' => array('value' => ini_get('memory_limit')),
            'PHP Max Post Size' => array('value' => ini_get('post_max_size')),
            'PHP Max Upload Filesize' => array('value' => ini_get('upload_max_filesize')),
            'PHP Max Script Execute Time' => array('value' => ini_get('max_execution_time')),
            'PHP EXIF Support' => array('value' => extension_loaded('exif') ? __('Yes', WCU_LANG_CODE) : __('No', WCU_LANG_CODE)),
            'PHP EXIF Version' => array('value' => phpversion('exif')),
            'PHP XML Support' => array('value' => extension_loaded('libxml') ? __('Yes', WCU_LANG_CODE) : __('No', WCU_LANG_CODE), 'error' => !extension_loaded('libxml')),
            'PHP CURL Support' => array('value' => extension_loaded('curl') ? __('Yes', WCU_LANG_CODE) : __('No', WCU_LANG_CODE), 'error' => !extension_loaded('curl')),
		);
	}
	public function showWelcomePage() {
		frameWcu::_()->getModule('templates')->loadJqueryUi();
		frameWcu::_()->addStyle('sup.bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css');
		frameWcu::_()->addStyle('admin.welcome', $this->getModule()->getModPath(). 'css/admin.welcome.css');
		$goToAdminLink = frameWcu::_()->getModule('options')->getTabUrl('currency');
		$skipTutorLink = uriWcu::_(array('baseUrl' => $goToAdminLink, 'skip_tutorial' => 1));
		$this->assign('skipTutorLink', $this->_makeWelcomeLink( $skipTutorLink ));
		$this->assign('faqList', $this->getMostFaqList());
		$this->assign('mainLink', $this->getModule()->getMainLink());
		parent::display('welcomePage');
	}
	private function _makeWelcomeLink($link) {
		return uriWcu::_(array('baseUrl' => $link, 'from' => 'welcome-page', 'pl' => WCU_CODE));
	}
	public function getTourHtml() {
		$this->assign('contactFormLink', $this->getModule()->getContactLink());
		$this->assign('finishSiteLink', $this->getModule()->generateMainLink('utm_source=plugin&utm_medium=final_step_b_step&utm_campaign=woocurrency'));
		return parent::getContent('adminTour');
	}
	public function getPluginDeactivation() {
		return parent::getContent('pluginDeactivation');
	}
}
