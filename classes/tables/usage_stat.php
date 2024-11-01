<?php
class tableUsage_statWcu extends tableWcu {
    public function __construct() {
        $this->_table = '@__usage_stat';
        $this->_id = 'id';     
        $this->_alias = 'wcu_usage_stat';
        $this->_addField('id', 'hidden', 'int', 0, __('id', WCU_LANG_CODE))
			->_addField('code', 'hidden', 'text', 0, __('code', WCU_LANG_CODE))
			->_addField('visits', 'hidden', 'int', 0, __('visits', WCU_LANG_CODE))
			->_addField('spent_time', 'hidden', 'int', 0, __('spent_time', WCU_LANG_CODE))
			->_addField('modify_timestamp', 'hidden', 'int', 0, __('modify_timestamp', WCU_LANG_CODE));
    }
}