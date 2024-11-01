<?php
class dateWcu {
	static public function _($time = NULL) {
		if(is_null($time)) {
			$time = time();
		}
		return date(WCU_DATE_FORMAT_HIS, $time);
	}
}