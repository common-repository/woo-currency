<?php
class admin_navViewWcu extends viewWcu {
	public function getBreadcrumbs() {
		$this->assign('breadcrumbsList', dispatcherWcu::applyFilters('mainBreadcrumbs', $this->getModule()->getBreadcrumbsList()));
		return parent::getContent('adminNavBreadcrumbs');
	}
}
