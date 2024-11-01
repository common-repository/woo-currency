<?php
class modulesModelWcu extends modelWcu {
	public function __construct() {
		$this->_setTbl('modules');
	}
    public function get($d = array()) {
        if(isset($d['id']) && $d['id'] && is_numeric($d['id'])) {
            $fields = frameWcu::_()->getTable('modules')->fillFromDB($d['id'])->getFields();
            $fields['types'] = array();
            $types = frameWcu::_()->getTable('modules_type')->fillFromDB();
            foreach($types as $t) {
                $fields['types'][$t['id']->value] = $t['label']->value;
            }
            return $fields;
        } elseif(!empty($d)) {
            $data = frameWcu::_()->getTable('modules')->get('*', $d);
            return $data;
        } else {
            return frameWcu::_()->getTable('modules')
                ->innerJoin(frameWcu::_()->getTable('modules_type'), 'type_id')
                ->getAll(frameWcu::_()->getTable('modules')->alias().'.*, '. frameWcu::_()->getTable('modules_type')->alias(). '.label as type');
        }
    }
    public function put($d = array()) {
        $res = new responseWcu();
        $id = $this->_getIDFromReq($d);
        $d = prepareParamsWcu($d);
        if(is_numeric($id) && $id) {
            if(isset($d['active']))
                $d['active'] = ((is_string($d['active']) && $d['active'] == 'true') || $d['active'] == 1) ? 1 : 0;           //mmm.... govnokod?....)))
           /* else
                 $d['active'] = 0;*/
            
            if(frameWcu::_()->getTable('modules')->update($d, array('id' => $id))) {
                $res->messages[] = __('Module Updated', WCU_LANG_CODE);
                $mod = frameWcu::_()->getTable('modules')->getById($id);
                $newType = frameWcu::_()->getTable('modules_type')->getById($mod['type_id'], 'label');
                $newType = $newType['label'];
                $res->data = array(
                    'id' => $id, 
                    'label' => $mod['label'], 
                    'code' => $mod['code'], 
                    'type' => $newType,
                    'active' => $mod['active'], 
                );
            } else {
                if($tableErrors = frameWcu::_()->getTable('modules')->getErrors()) {
                    $res->errors = array_merge($res->errors, $tableErrors);
                } else
                    $res->errors[] = __('Module Update Failed', WCU_LANG_CODE);
            }
        } else {
            $res->errors[] = __('Error module ID', WCU_LANG_CODE);
        }
        return $res;
    }
    protected function _getIDFromReq($d = array()) {
        $id = 0;
        if(isset($d['id']))
            $id = $d['id'];
        elseif(isset($d['code'])) {
            $fromDB = $this->get(array('code' => $d['code']));
            if(isset($fromDB[0]) && $fromDB[0]['id'])
                $id = $fromDB[0]['id'];
        }
        return $id;
    }
}
