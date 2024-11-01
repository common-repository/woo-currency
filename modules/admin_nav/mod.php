<?php
class admin_navWcu extends moduleWcu {
	public function getBreadcrumbsList() {
		$res = array(
			array('label' => WCU_WP_PLUGIN_NAME, 'url' => frameWcu::_()->getModule('adminmenu')->getMainLink()),
		);
		// Try to get current tab breadcrumb
		$activeTab = frameWcu::_()->getModule('options')->getActiveTab();
		if(!empty($activeTab) && $activeTab != 'main_page') {
			$tabs = frameWcu::_()->getModule('options')->getTabs();
			if(!empty($tabs) && isset($tabs[ $activeTab ])) {
				if(isset($tabs[ $activeTab ]['add_bread']) && !empty($tabs[ $activeTab ]['add_bread'])) {
					if(!is_array($tabs[ $activeTab ]['add_bread']))
						$tabs[ $activeTab ]['add_bread'] = array( $tabs[ $activeTab ]['add_bread'] );
					foreach($tabs[ $activeTab ]['add_bread'] as $addForBread) {
						$res[] = array(
							'label' => $tabs[ $addForBread ]['label'], 'url' => $tabs[ $addForBread ]['url'],
						);
					}
				}
				$res[] = array(
					'label' => $tabs[ $activeTab ]['label'], 'url' => $tabs[ $activeTab ]['url'],
				);
				if($activeTab == 'statistics') {
					$statTabs = frameWcu::_()->getModule('statistics')->getStatTabs();
					$currentStatTab = frameWcu::_()->getModule('statistics')->getCurrentStatTab();
					if(isset($statTabs[ $currentStatTab ])) {
						$res[] = array(
							'label' => $statTabs[ $currentStatTab ]['label'], 'url' => $statTabs[ $currentStatTab ]['url'],
						);
					}
				}
			}
		}
		return $res;
	}
}

