<?php
class admin_navControllerWcu extends controllerWcu {
	public function getPermissions() {
		return array(
			WCU_USERLEVELS => array(
				WCU_ADMIN => array()
			),
		);
	}
}