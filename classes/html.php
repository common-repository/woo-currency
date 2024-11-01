<?php
class htmlWcu {
    static public $categoriesOptions = array();
    static public $productsOptions = array();
    static public function block($name, $params= array('attrs' => '', 'value' => '')){
        $output = '<p class="toe_'. self::nameToClassId($name). '">'.$params['value'].'</p>';
        //$output .= self::hidden($name, $params);
        return $output;
    }
    static public function nameToClassId($name, $params = array()) {
		if(!empty($params) && isset($params['attrs']) && strpos($params['attrs'], 'id="') !== false) {
			preg_match('/id="(.+)"/ui', $params['attrs'], $idMatches);
			if($idMatches[1]) {
				return $idMatches[1];
			}
		}
        return str_replace(array('[', ']'), '', $name);
    }
    static public function textarea($name, $params = array('attrs' => '', 'value' => '', 'rows' => 3, 'cols' => 50)) {
		$params['attrs'] = isset($params['attrs']) ? $params['attrs'] : '';
        $params['rows'] = isset($params['rows']) ? $params['rows'] : 3;
        $params['cols'] = isset($params['cols']) ? $params['cols'] : 50;
		if(isset($params['required']) && $params['required']) {
			$params['attrs'] .= ' required ';	// HTML5 "required" validation attr
		}
		if(isset($params['placeholder']) && $params['placeholder']) {
			$params['attrs'] .= ' placeholder="'. $params['placeholder']. '"';	// HTML5 "required" validation attr
		}
		if(isset($params['disabled']) && $params['disabled']) {
			$params['attrs'] .= ' disabled ';
		}
		if(isset($params['readonly']) && $params['readonly']) {
			$params['attrs'] .= ' readonly ';
		}
		if(isset($params['auto_width']) && $params['auto_width']) {
			unset($params['rows']);
			unset($params['cols']);
		}
        return '<textarea name="'. $name. '" '
			. (isset($params['attrs']) ? $params['attrs'] : '')
			. (isset($params['rows']) ? ' rows="'. $params['rows']. '"' : '')
			. (isset($params['cols']) ? ' cols="'. $params['cols']. '"' : '')
			. '>'.
			(isset($params['value']) ? $params['value'] : '').
		'</textarea>';
    }
    static public function input($name, $params = array('attrs' => '', 'type' => 'text', 'value' => '')) {
		$params['attrs'] = isset($params['attrs']) ? $params['attrs'] : '';
		$params['attrs'] .= self::_dataToAttrs($params);
		if(isset($params['required']) && $params['required']) {
			$params['attrs'] .= ' required ';	// HTML5 "required" validation attr
		}
		if(isset($params['placeholder']) && $params['placeholder']) {
			$params['attrs'] .= ' placeholder="'. $params['placeholder']. '"';	// HTML5 "required" validation attr
		}
		if(isset($params['disabled']) && $params['disabled']) {
			$params['attrs'] .= ' disabled ';
		}
		if(isset($params['readonly']) && $params['readonly']) {
			$params['attrs'] .= ' readonly ';
		}
		$params['value'] = isset($params['value']) ? $params['value'] : '';
        $out = '';
        if(isset($params['labeled']) && $params['labeled']) {
			$out .= '<label '.(isset($params['label_attrs']) ? $params['label_attrs'] : '').'>';
		}
        if(!isset($params['labeled_right']) && isset($params['labeled']) ) {
            $out .= $params['labeled'];
		}
        $params['type'] = isset($params['type']) ? $params['type'] : 'text';
        $out .= '<input type="'. $params['type'] . '" name="'. $name. '" value="'. $params['value']. '" '. (isset($params['attrs']) ? $params['attrs'] : ''). ' />';
        if(isset($params['labeled_right']) && $params['labeled_right']) {
            $out .= $params['labeled'];
    	}
        if(isset($params['labeled']) && $params['labeled']) {
			$out .= '</label>';
		}
        return $out;
    }
	static private function _dataToAttrs($params) {
		$res = '';
		foreach($params as $k => $v) {
			if(strpos($k, 'data-') === 0) {
				$res .= ' '. $k. '="'. $v. '"';
			}
		}
		return $res;
	}
    static public function text($name, $params = array('attrs' => '', 'value' => '')) {
        $params['type'] = 'text';
        return self::input($name, $params);
    }
	static public function email($name, $params = array('attrs' => '', 'value' => '')) {
        $params['type'] = 'email';
        return self::input($name, $params);
    }
	static public function reset($name, $params = array('attrs' => '', 'value' => '')) {
        $params['type'] = 'reset';
        return self::input($name, $params);
    }
    static public function password($name, $params = array('attrs' => '', 'value' => '')) {
        $params['type'] = 'password';
        return self::input($name, $params);
    }
    static public function hidden($name, $params = array('attrs' => '', 'value' => '')) {
        $params['type'] = 'hidden';
        return self::input($name, $params);
    }
    static public function checkboxHiddenVal($name, $params = array('attrs' => '', 'value' => '', 'checked' => '')) {
		$params['attrs'] = isset($params['attrs']) ? $params['attrs'] : '';
		$checkId = self::nameToClassId($name, $params);
		if(strpos($params['attrs'], 'id="') === false) {
			$checkId .= '_check';
		}
		$hideId = self::nameToClassId($name, $params). '_text';
		$paramsCheck = $paramsHidden = $params;
		if(strpos($params['attrs'], 'id="') === false) {
			$paramsCheck['attrs'] .= ' id="'. $checkId. '"';
		}
		$paramsCheck['attrs'] .= ' data-hideid="'. $hideId. '"';
		$paramsHidden['attrs'] = ' id="'. $hideId. '"';
		$paramsCheck['value'] = isset($paramsCheck['value']) ? $paramsCheck['value'] : '';
		$paramsCheck['checked'] = $paramsCheck['value'] ? '1' : '0';
		$out = self::checkbox(self::nameToClassId($name), $paramsCheck);
		$out .= self::hidden($name, $paramsHidden);
		$out .= '<script type="text/javascript">//<!--
			jQuery(function(){
				jQuery("#'. $checkId. '").change(function(){
					jQuery("#'. $hideId. '").val( (jQuery(this).prop("checked") ? 1 : 0) );
				});
			});
			//--></script>';
		return $out;
	}
    static public function checkbox($name, $params = array('attrs' => '', 'value' => '', 'checked' => '')) {
        $params['type'] = 'checkbox';
        if(isset($params['checked']) && $params['checked'])
            $params['checked'] = 'checked';
        if(!isset($params['value']) || $params['value'] === NULL)
            $params['value'] = 1;
		if(!isset($params['attrs']))
			$params['attrs'] = '';
        $params['attrs'] .= ' '. (isset($params['checked']) ? $params['checked'] : '');
        return self::input($name, $params);
    }
    static public function checkboxlist($name, $params = array('options' => array(), 'attrs' => '', 'checked' => '', 'delim' => '<br />', 'usetable' => 5), $delim = '<br />') {
        $out = '';
        if(!strpos($name, '[]')) {
            $name .= '[]';
        }
        $i = 0;
        if($params['options']) {
            if(!isset($params['delim']))
                $params['delim'] = $delim;
            if($params['usetable']) $out .= '<table><tr>';
            foreach($params['options'] as $v) {
                if($params['usetable']) {
                    if($i != 0 && $i%$params['usetable'] == 0) $out .= '</tr><tr>';
                    $out .= '<td>';
                }
                $out .= self::checkboxHiddenVal($name, array(
                    'attrs' => $params['attrs'],
                    'value' => $v['id'],
                    'checked' => $v['checked']
                ));
                $out .= '&nbsp;'. $v['text']. $params['delim'];
                if($params['usetable']) $out .= '</td>';
                $i++;
            }
            if($params['usetable']) $out .= '</tr></table>';
        }
        return $out;
    }
	static public function timepicker($name, $params = array('attrs' => '', 'value' => '')) {
		if(isset($params['id']) && !empty($params['id']))
            $id = $params['id'];
        else
            $id = self::nameToClassId($name);
		return self::input($name, array(
                'attrs' => 'id="'. $id. '" '. (isset($params['attrs']) ? $params['attrs'] : ''),
                'type' => 'text',
                'value' => $params['value']
        )).'<script type="text/javascript">
            // <!--
                jQuery(document).ready(function(){
                    jQuery("#'. $id. '").timepicker('. json_encode($params). ');
                });
            // -->
        </script>';
	}
    static public function datepicker($name, $params = array('attrs' => '', 'value' => '')) {
        if(isset($params['id']) && !empty($params['id']))
            $id = $params['id'];
        else
            $id = self::nameToClassId($name);
		$params = array_merge(array(
			'dateFormat' => WCU_DATE_FORMAT_JS,
			'changeYear' => true,
			'yearRange' => '1905:'. (date('Y')+5),
		), $params);
        return self::input($name, array(
                'attrs' => 'id="'. $id. '" '. (isset($params['attrs']) ? $params['attrs'] : ''),
                'type' => 'text',
                'value' => $params['value']
        )).'<script type="text/javascript">
            // <!--
                jQuery(document).ready(function(){
                    jQuery("#'. $id. '").datepicker('. json_encode($params). ');
                });
            // -->
        </script>';
    }
    static public function submit($name, $params = array('attrs' => '', 'value' => '')) {
        $params['type'] = 'submit';
        return self::input($name, $params);
    }
    static public function img($src, $usePlugPath = 1, $params = array('width' => '', 'height' => '', 'attrs' => '')) {
        if($usePlugPath) $src = WCU_IMG_PATH. $src;
        return '<img src="'.$src.'" '
				.(isset($params['width']) ? 'width="'.$params['width'].'"' : '')
				.' '
				.(isset($params['height']) ? 'height="'.$params['height'].'"' : '')
				.' '
				.(isset($params['attrs']) ? $params['attrs'] : '').' />';
    }
    static public function selectbox($name, $params = array('attrs' => '', 'options_attrs' => array(), 'option_attrs' => '', 'options' => array(), 'value' => '')) {
        $out = '';
		$params['attrs'] = isset($params['attrs']) ? $params['attrs'] : '';
		$params['attrs'] .= self::_dataToAttrs($params);

		$params['options_attrs'] = isset($params['options_attrs']) ? $params['options_attrs'] : array();

        $params['option_attrs'] = isset($params['option_attrs']) ? $params['option_attrs'] : '';

		if(isset($params['required']) && $params['required']) {
			$params['attrs'] .= ' required ';	// HTML5 "required" validation attr
		}
        if(isset($params['labeled_before']) && $params['labeled_before']) {
            $out .= $params['labeled_before'];
        }
        $out .= '<select name="'. $name. '" '. (isset($params['attrs']) ? $params['attrs'] : ''). '>';
        if(!empty($params['options'])) {
            foreach($params['options'] as $k => $v) {
                $selected = (isset($params['value']) && $k == $params['value'] ? 'selected="true"' : '');
				$opt_attrs = !empty($params['options_attrs'][$k]) ? $params['options_attrs'][$k] : '';
                $out .= '<option value="'. $k. '" '. $selected.  $opt_attrs . $params["option_attrs"] . '>'. $v. '</option>';
            }
        }
        $out .= '</select>';
        if(isset($params['labeled_after']) && $params['labeled_after']) {
            $out .= $params['labeled_after'];
        }
        if(isset($params['labeled']) && $params['labeled']) {
            $out .= $params['labeled'];
        }

        return $out;
    }
    static public function selectlist($name, $params = array('attrs'=>'', 'size'=> 5, 'options' => array(), 'value' => '')) {
        $out = '';
        if(!strpos($name, '[]'))
            $name .= '[]';
        if (!isset($params['size']) || !is_numeric($params['size']) || $params['size'] == '') {
            $params['size'] = 5;
        }
		$params['attrs'] = isset($params['attrs']) ? $params['attrs'] : '';
		$params['attrs'] .= self::_dataToAttrs($params);
        $out .= '<select multiple="multiple" size="'.$params['size'].'" name="'.$name.'" '.$params['attrs'].'>';
		$params['value'] = isset($params['value']) ? $params['value'] : array();
        if(!empty($params['options'])) {
            foreach($params['options'] as $k => $v) {
                $selected = (in_array($k,(array)$params['value']) ? 'selected="true"' : '');
                $out .= '<option value="'.$k.'" '.$selected.'>'.$v.'</option>';
            }
        }
        $out .= '</select>';
        return $out;
    }
    static public function selectlistsortable($name, $params = array('attrs'=>'', 'id' => '', 'size'=> 5, 'options' => array(), 'value' => '')) {
        $out = '';
        if(!strpos($name, '[]'))
            $name .= '[]';
        if (!isset($params['size']) || !is_numeric($params['size']) || $params['size'] == '') {
            $params['size'] = 5;
        }
        $id = isset($params['id']) ? $params['id'] : '';
		$params['attrs'] = isset($params['attrs']) ? $params['attrs'] : '';
		$params['attrs'] .= self::_dataToAttrs($params);
        $out .= '<select multiple="multiple" size="'.$params['size'].'" id="'.$id.'" name="'.$name.'" '.$params['attrs'].'>';
		$params['value'] = isset($params['value']) ? $params['value'] : array();
        if(!empty($params['options'])) {
            foreach($params['options'] as $k => $v) {
                $selected = (in_array($k,(array)$params['value']) ? 'selected="true"' : '');
                $out .= '<option value="'.$k.'" '.$selected.'>'.$v.'</option>';
            }
        }
        $out .= '</select>';
        $out .= '<script type="text/javascript">
                jQuery(document).ready(function(){
                    wcuSelectMultipleSortableFunction("'.$id.'");
                });
                </script>';
        return $out;
    }
	static public function file($name, $params = array()) {
		$params['type'] = 'file';
		return self::input($name, $params);
	}
    static public function ajaxfile($name, $params = array('url' => '', 'value' => '', 'fid' => '', 'buttonName' => '', 'class' => '')) {
		frameWcu::_()->addScript('ajaxupload', WCU_JS_PATH. 'ajaxupload.js');
        $out = '';
		$params['class'] = isset($params['class']) ? $params['class'] : '';
        if(strpos($params['url'], 'pl='. WCU_CODE) === false)
			$params['url'] = uriWcu::_(array('baseUrl' => $params['url'], 'pl' => WCU_CODE));
        $out .= self::button(array('value' => __( empty($params['buttonName']) ? 'Upload' :  $params['buttonName'] ), 'attrs' => 'id="toeUploadbut_'.$name.'" class="button button-large '.$params['class'].'"'));
        $display = (empty($params['value']) ? 'style="display: none;"' : '');
        if(isset($params['preview']) && $params['preview'])
            $out .= self::img($params['value'], 0, array('attrs' => 'id="prev_'.$name.'" '.$display.' class="previewpicture"'));
        $out .= '<span class="delete_option" id="delete_'.$name.'" '.$display.'></span>';
        $out .= '<script type="text/javascript">
                jQuery(document).ready(function(){
                    new AjaxUpload("#toeUploadbut_'.$name.'", {
                        action: "'.$params['url'].'",
                        name: "'. $name. '" '.
                        (empty($params['data']) ? '' : ',  data: '. $params['data']. '').
                        (empty($params['autoWcumit']) ? '' : ',  autoWcumit: "'. $params['autoWcumit']. '"').
                        (empty($params['responseType']) ? '' : ',  responseType: "'. $params['responseType']. '"').
                        (empty($params['onChange']) ? '' : ',  onChange: '. $params['onChange']. '').
                        (empty($params['onSubmit']) ? '' : ',  onSubmit: '. $params['onSubmit']. '').
                        (empty($params['onComplete']) ? '' : ',  onComplete: '. $params['onComplete']. '').
                    '});
                });
            </script>';
        return $out;
    }
    static public function button($params = array('attrs' => '', 'value' => '')) {
        return '<button '. $params['attrs']. '>'. $params['value']. '</button>';
    }
	static public function buttonA($params = array('attrs' => '', 'value' => '')) {
		return '<a href="#" '. $params['attrs']. '>'. $params['value']. '</a>';
	}
    static public function inputButton($params = array('attrs' => '', 'value' => '')) {
		if(!is_array($params))
			$params = array();
		$params['type'] = 'button';
        return self::input('', $params);
    }
    static public function radiobuttons($name, $params = array('attrs' => '', 'options' => array(), 'value' => '', '')) {
        $out = '';
		if(isset($params['options']) && is_array($params['options']) && !empty($params['options'])) {
			$params['labeled'] = isset($params['labeled']) ? $params['labeled'] : false;
			$params['attrs'] = isset($params['attrs']) ? $params['attrs'] : '';
			$params['no_br'] = isset($params['no_br']) ? $params['no_br'] : false;
			foreach($params['options'] as $key => $val) {
				/*$id = self::nameToClassId($key). '_'. mt_rand(1, 999999);
				$attrs = $params['attrs'];
				$attrs .= ' id="'. $id. '"';*/
				$checked =($key == $params['value']) ? 'checked' : '';
                if($params['labeled']) {
					$out .= '<label '.$params['attrs'].'>';
				}
                $out .= self::input($name, array('attrs' => $params['attrs'].' '.$checked, 'type' => 'radio', 'value' => $key));
                if($params['labeled']) {
					$out .= $val. '&nbsp;';
				}
				if($params['labeled']) {
					$out .= '</label>';
				}
				if(!$params['no_br']) {
					$out .= '<br />';
				}
			}
		}
        return $out;
    }
    static public function radiobutton($name, $params = array('attrs' => '', 'value' => '', 'checked' => '')) {
        $params['type'] = 'radio';
		$params['attrs'] = isset($params['attrs']) ? $params['attrs'] : '';
        if(isset($params['checked']) && $params['checked'])
            $params['attrs'] .= ' checked';
        return self::input($name, $params);
    }
    static public function nonajaxautocompleate($name, $params = array('attrs' => '', 'options' => array())) {
        if(empty($params['options'])) return false;
        $out = '';
        $jsArray = array();
        $oneItem = '<div id="%%ID%%"><div class="ac_list_item"><input type="hidden" name="'.$name.'[]" value="%%ID%%" />%%VAL%%</div><div class="close" onclick="removeAcOpt(%%ID%%)"></div><div class="br"></div></div>';
        $tID = $name. '_ac';
        $out .= self::text($tID. '_ac', array('attrs' => 'id="'.$tID.'"'));
        $out .= '<div id="'.$name.'_wraper">';
        foreach($params['options'] as $opt) {
            $jsArray[$opt['id']] = $opt['text'];
            if(isset($opt['checked']) && $opt['checked'] == 'checked') {
                $out .= str_replace(array('%%ID%%', '%%VAL%%'), array($opt['id'], $opt['text']), $oneItem);
            }
        }
        $out .= '</div>';
        $out .= '<script type="text/javascript">
                var ac_values_'.$name.' = '.json_encode(array_values($jsArray)).';
                var ac_keys_'.$name.' = '.json_encode(array_keys($jsArray)).';
                jQuery(document).ready(function(){
                    jQuery("#'.$tID.'").autocomplete(ac_values_'.$name.', {
                        autoFill: false,
                        mustMatch: false,
                        matchContains: false
                    }).result(function(a, b, c){
                        var keyID = jQuery.inArray(c, ac_values_'.$name.');
                        if(keyID != -1) {
                            addAcOpt(ac_keys_'.$name.'[keyID], c, "'.$name.'");
                        }
                    });
                });
        </script>';
        return $out;
    }
    static public function formStart($name, $params = array('action' => '', 'method' => 'GET', 'attrs' => '', 'hideMethodInside' => false)) {
        $params['attrs'] = isset($params['attrs']) ? $params['attrs'] : '';
        $params['action'] = isset($params['action']) ? $params['action'] : '';
        $params['method'] = isset($params['method']) ? $params['method'] : 'GET';
        if(isset($params['hideMethodInside']) && $params['hideMethodInside']) {
            return '<form name="'. $name. '" action="'. $params['action']. '" method="'. $params['method']. '" '. $params['attrs']. '>'.
                self::hidden('method', array('value' => $params['method']));
        } else {
            return '<form name="'. $name. '" action="'. $params['action']. '" method="'. $params['method']. '" '. $params['attrs']. '>';
        }
    }
    static public function formEnd() {
        return '</form>';
    }
    static public function statesInput($name, $params = array('value' => '', 'attrs' => '', 'notSelected' => true, 'id' => '', 'selectHtml' => '')) {
        if(empty($params['selectHtml']) || !method_exists(html, $params['selectHtml']))
            return false;

        $params['notSelected'] = isset($params['notSelected']) ? $params['notSelected'] : true;
        $states = fieldAdapterWcu::getStates($params['notSelected']);

        foreach($states as $sid => $s) {
            $params['options'][$sid] = $s['name'];
        }
        $idSelect = '';
        $idText = '';
        $id = '';
        if(!empty($params['id'])) {
            $id = $params['id'];
        } else {
            $id = self::nameToClassId($name);
        }
        $paramsText = $paramsSelect = $params;
        $paramsText['attrs'] .= 'id = "'. $id. '_text"';
        $paramsSelect['attrs'] .= 'id = "'. $id. '_select"';
        $res = '';
        $res .= self::$params['selectHtml']($name, $paramsSelect);
        $res .= self::text($name, $paramsText);
        if(empty($params['doNotAddJs'])) {
            $res .= '<script type="text/javascript">
                // <!--
                if(!toeStates.length)
                    toeStates = '. utilsWcu::jsonEncode($states). ';
                toeStatesObjects["'. $id. '"] = new toeStatesSelect("'. $id. '");
                // -->
            </script>';
        }
        return $res;
    }
    static public function statesList($name, $params = array('value' => '', 'attrs' => '', 'notSelected' => true, 'id' => '')) {
        $params['selectHtml'] = 'selectbox';
        return self::statesInput($name, $params);
    }
    static public function statesListMultiple($name, $params = array('value' => '', 'attrs' => '', 'notSelected' => true, 'id' => '')) {
        if(!empty($params['value'])) {
            if(is_string($params['value'])) {
                if(strpos($params['value'], ','))
                    $params['value'] = array_map('trim', explode(',', $params['value']));
                else
                    $params['value'] = array(trim($params['value']));
            }
        }
        $params['selectHtml'] = 'selectlist';
        return self::statesInput($name, $params);
    }
    static public function countryList($name, $params = array('value' => '', 'attrs' => '', 'notSelected' => true)) {
        $params['notSelected'] = isset($params['notSelected']) ? $params['notSelected'] : true;
        $params['options'] = fieldAdapterWcu::getCountries($params['notSelected']);
        $params['attrs'] .= ' type="country"';
        return self::selectbox($name, $params);
    }
    static public function countryListMultiple($name, $params = array('value' => '', 'attrs' => '', 'notSelected' => true)) {
        if(!empty($params['value'])) {
            if(is_string($params['value'])) {
                if(strpos($params['value'], ','))
                    $params['value'] = array_map('trim', explode(',', $params['value']));
                else
                    $params['value'] = array(trim($params['value']));
            }
        }
        $params['notSelected'] = isset($params['notSelected']) ? $params['notSelected'] : true;
        $params['options'] = fieldAdapterWcu::getCountries($params['notSelected']);
        $params['attrs'] .= ' type="country"';
        return self::selectlist($name, $params);
    }
    static public function textFieldsDynamicTable($name, $params = array('value' => '', 'attrs' => '', 'options' => array())) {
        $res = '';
        if(empty($params['options']))
            $params['options'] = array(0 => array('label' => ''));
        if(!empty($params['options'])) {
            $pattern = array();
            foreach($params['options'] as $key => $p) {
                $pattern[$key] = htmlWcu::text($name. '[]['. $key. ']');
            }
            $countOptions = count($params['options']);
            $remove = '<a href="#" onclick="toeRemoveTextFieldsDynamicTable(this); return false;">remove</a>';
            $add = '<a href="#" onclick="toeAddTextFieldsDynamicTable(this, '. $countOptions. '); return false;">add</a>';

            $res = '<div class="toeTextFieldsDynamicTable">';
            if(empty($params['value']))
                $params['value'] = array();
            elseif(!is_array($params['value'])) {
                $params['value'] = utilsWcu::jsonDecode($params['value']);
                //$params['value'] = $params['value'][0];
            }
            $i = 0;
            do {
                $res .= '<div class="toeTextFieldDynamicRow">';
                foreach($params['options'] as $key => $p) {
                    switch($countOptions) {
                        case 1:
                            if(isset($params['value'][$i]))
                                $value = is_array($params['value'][$i]) ? $params['value'][$i][$key] : $params['value'][$i];
                            else
                                $value = '';
                            break;
                        case 2:
                        default:
                            $value = isset($params['value'][$i][$key]) ? $params['value'][$i][$key] : '';
                            break;
                    }
                    $paramsForText = array(
                        'value' => $value,
                    );
                    $res .= __($p['label']). htmlWcu::text($name. '['. $i. ']['. $key. ']', $paramsForText);
                }
                $res .= $remove. '</div>';
                $i++;
            } while($i < count($params['value']));
            $res .= $add;
            $res .= '</div>';
        }
        return $res;
    }
    static public function categorySelectlist($name, $params = array('attrs'=>'', 'size'=> 5, 'value' => '')) {
        self::_loadCategoriesOptions();
        if(self::$categoriesOptions) {
            $params['options'] = self::$categoriesOptions;
            return self::selectlist($name, $params);
        }
        return false;
    }
    static public function categorySelectbox($name, $params = array('attrs'=>'', 'size'=> 5, 'value' => '')) {
        self::_loadCategoriesOptions();
        if(!empty(self::$categoriesOptions)) {
            $params['options'] = self::$categoriesOptions;
            return self::selectbox($name, $params);
        }
        return false;
    }
    static public function productsSelectlist($name, $params = array('attrs'=>'', 'size'=> 5, 'value' => '')) {
        self::_loadProductsOptions();
        if(!empty(self::$productsOptions)) {
            $params['options'] = self::$productsOptions;
            return self::selectlist($name, $params);
        }
        return false;
    }
    static public function productsSelectbox($name, $params = array('attrs'=>'', 'size'=> 5, 'value' => '')) {
        self::_loadProductsOptions();
        if(!empty(self::$productsOptions)) {
            $params['options'] = self::$productsOptions;
            return self::selectbox($name, $params);
        }
        return false;
    }
    static protected function _loadCategoriesOptions() {
        if(empty(self::$categoriesOptions)) {
            $categories = frameWcu::_()->getModule('products')->getCategories();
            if(!empty($categories)) {
                foreach($categories as $c) {
                    self::$categoriesOptions[$c->term_taxonomy_id] = $c->cat_name;
                }
            }
        }
    }
    static protected function _loadProductsOptions() {
        if(empty(self::$productsOptions)) {
            $products = frameWcu::_()->getModule('products')->getModel()->get(array('getFields' => 'post.ID, post.post_title'));
            if(!empty($products)) {
                foreach($products as $p) {
                    self::$productsOptions[$p['ID']] = $p['post_title'];
                }
            }
        }
    }
    static public function slider($name, $params = array('value' => 0, 'min' => 0, 'max' => 0, 'step' => 1, 'slide' => '')) {
        $id = self::nameToClassId($name, $params);
        $paramsStr = '';
        if(!isset($params['slide']) || (empty($params['slide']) && $params['slide'] !== false)) { //Can be set to false to ignore function onSlide event binding
            $params['slide'] = 'toeSliderMove';
        }
        if(!empty($params)) {
            $paramsArr = array();
            foreach($params as $k => $v) {
				if(in_array($k, array('attrs')) || strpos($k, '-')) continue;
                $value = (is_numeric($v) || in_array($k, array('slide'))) ? $v : '"'. $v. '"';
                $paramsArr[] = $k. ': '. $value;
            }
            $paramsStr = implode(', ', $paramsArr);
        }

		$res = '<div class="toeSliderContainer">';
        $res .= '<div id="toeSliderDisplay_'. $id. '" class="toeSliderDisplay">'. (empty($params['value']) ? '' : $params['value']). '</div>';
        $res .= '<div id="'. $id. '"></div>';
        $params['attrs'] = 'id="toeSliderInput_'. $id. '"';
        $res .= self::hidden($name, $params);
        $res .= '<script type="text/javascript"><!--
            jQuery(function(){
                jQuery("#'. $id. '").slider({'. $paramsStr. '});
            });
            --></script>';
        $res .= '</div>';
        return $res;
    }
	static public function capcha() {
		return recapchaWcu::_()->getHtml();
	}
	static public function textIncDec($name, $params = array('value' => '', 'attrs' => '', 'options' => array(), 'onclick' => '', 'id' => '')) {
		if(!isset($params['attrs']))
			$params['attrs'] = '';
		$textId = (isset($params['id']) && !empty($params['id'])) ? $params['id'] : 'toeTextIncDec_'. mt_rand(9, 9999);
		$params['attrs'] .= ' id="'. $textId. '"';
		$textField = self::text($name, $params);
		$onClickInc = 'toeTextIncDec(\''. $textId. '\', 1); return false;';
		$onClickDec = 'toeTextIncDec(\''. $textId. '\', -1); return false;';
		if(isset($params['onclick']) && !empty($params['onclick'])) {
			$onClickInc = $params['onclick']. ' '. $onClickInc;
			$onClickDec = $params['onclick']. ' '. $onClickDec;
		}
		$textField .= '<div class="toeUpdateQtyButtonsWrapper"><div class="toeIncDecButton toeIncButton '. $textId. '" onclick="'. $onClickInc. '">+</div>'
				. '<div class="toeIncDecButton toeDecButton '. $textId. '" onclick="'. $onClickDec. '">-</div></div>';
		return $textField;
	}
	static public function colorpicker($name, $params = array('value' => '')) {
		$params['value'] = isset($params['value']) ? $params['value'] : '';
		$params['attrs'] = isset($params['attrs']) ? $params['attrs'] : '';
        $params['label_before_attrs'] = isset($params['label_before_attrs']) ? $params['label_before_attrs'] : '';
        $params['label_after_attrs'] = isset($params['label_after_attrs']) ? $params['label_after_attrs'] : '';
		$nameToClass = str_replace('-', '_', self::nameToClassId($name));
		$textId = self::nameToClassId($name, $params);
		if(strpos($params['attrs'], 'id="') === false) {
			$textId .= '_'. mt_rand(9, 9999);
			$params['attrs'] .= 'id="'. $textId. '"';
		}
		//$pickerId = $textId. '_picker';
		$params['attrs'] .= ' data-default-color="'. $params['value']. '"';

        $out = '';

        if (isset($params['label_before'])) {
            $out .= '<div '.$params['label_before_attrs'].'>'.$params['label_before'].'</div>';
        }

		$out .= self::text($name, $params);

        if (isset($params['label_after'])) {
            $out .= '<div '.$params['label_after_attrs'].'>'.$params['label_after'].'</div>';
        }

		//$out .= '<div style="position: absolute; z-index: 1;" id="'. $pickerId. '"></div>';
		$out .= '<script type="text/javascript">//<!--
			jQuery(function(){
				jQuery("#'. $textId. '").wpColorPicker({
					change: function(event, ui) {
						// Find change functiona for this element, if such exist - triger it
						if(window["wpColorPicker_'. $nameToClass. '_change"]) {
							window["wpColorPicker_'. $nameToClass. '_change"](event, ui);
						}
					}
				});
			});
			//--></script>';
		return $out;
	}
	static public function fontsList($name, $params = array('value' => '')) {
		static $options = array();

		if(empty($options)) {	// Fill them only one time per loading
			foreach(fieldAdapterWcu::getFontsList() as $font)
				$options[ $font ] = $font;
		}
		$params['options'] = array();
		if(isset($params['default'])) {
			$params['options'] = array(WCU_DEFAULT => $params['default']);
		}
		$params['options'] = array_merge($params['options'], $options);
		return self::selectbox($name, $params);
	}

	static public function slideInput($name, $params = array('attrs' => '', 'checked' => false, 'id' => '')) {
		$params = !isset($params) || empty($params) ? array() : $params;
		if(!isset($params['id'])) {
			$params['id'] = self::nameToClassId($name, $params);
			if(strpos($params['attrs'], 'id="') === false) {
				$params['id'] .= '_'. mt_rand(1, 99999);
			}
		}
		$params['checked'] = isset($params['checked']) ? (int) $params['checked'] : 0;
		$params['attrs'] = isset($params['attrs']) && !empty($params['attrs']) ? $params['attrs'] : '';
		$params['attrs'] .= ' id="'. $params['id']. '"';

		return '<a class="toeSlideShellWcu" href="#"'. $params['attrs']. '>
			<span class="toeSlideButtWcu"></span>
			<span class="toeSlideOnWcu">'. __('ON'). '</span>
			<span class="toeSlideOffWcu">'. __('OFF'). '</span>
			<input type="hidden" name="'. $name. '" />
		</a>
		<script type="text/javascript">
		// <!--
			jQuery(function(){
				jQuery("#'. $params['id']. '").slideInput('. $params['checked']. ');
			});
		// -->
		</script>';
	}
	static public function galleryBtn($name, $params = array()) {
		$galleryType = isset($params['galleryType']) ? $params['galleryType'] : 'all';
		$buttonId = self::nameToClassId($name, $params);
		$params['value'] = isset($params['value']) ? $params['value'] : '';
		$params['attrs'] = isset($params['attrs']) ? $params['attrs'] : '';
		if(strpos($params['attrs'], 'id="') === false) {
			$buttonId .= '_'. mt_rand(1, 99999);
			$params['attrs'] .= ' id="'. $buttonId. '"';
		}
		$inputId = $buttonId. '_input';
		$out = self::hidden($name, array('value' => $params['value'], 'attrs' => 'id="'. $inputId. '"'));
		$onChange = isset($params['onChange']) ? $params['onChange'] : '';
		$buttonParams = $params;
		$buttonParams['value'] = isset($params['btnVal']) ? $params['btnVal'] : sprintf(__('Select %s', WCU_LANG_CODE), strFirstUp($galleryType));
		$out .= self::button($buttonParams);
		$out .= '<script type="text/javascript">
		// <!--
			jQuery(function(){
				// Run onChange to make pre-set of required data
				'. ($onChange ? $onChange. '("'. $params['value']. '", null, "'. $buttonId. '");' : ''). '
				jQuery("#'. $buttonId. '").click(function(){
					var button = jQuery(this);
					_custom_media = true;
						wp.media.editor.send.attachment = function(props, attachment){
						if ( _custom_media ) {
							jQuery("#'. $inputId. '").val( attachment.url ).trigger("change");
							'. ($onChange ? $onChange. '(attachment.url, attachment, "'. $buttonId. '");' : ''). '
						} else {
							return _orig_send_attachment.apply( this, [props, attachment] );
						};
					};
					wp.media.editor.open(button);
					jQuery("body").find("#media-attachment-filters").val("'. $galleryType. '").trigger("change");
					return false;
				});
			});
		// -->
		</script>';
		return $out;
	}
	static public function imgGalleryBtn($name, $params = array()) {
		$params['galleryType'] = 'image';
		return self::galleryBtn($name, $params);
	}
	static public function audioGalleryBtn($name, $params = array()) {
		$params['galleryType'] = 'audio';
		return self::galleryBtn($name, $params);
	}
	static public function checkedOpt($arr, $key, $value = true, $default = false) {
		if(!isset($arr[ $key ])) {
			return $default ? true : false;
		}
		return $value === true ? $arr[ $key ] : $arr[ $key ] == $value;
	}
	static public function nonceForAction( $action ) {
		return self::hidden('_wpnonce', array('value' => wp_create_nonce(strtolower($action))));
	}

    static public function selectboximage($name, $params = array('attrs' => '', 'data_img' => array(), 'options' => array(), 'value' => '')) {
        $out = '';
		$params['attrs'] = isset($params['attrs']) ? $params['attrs'] : '';
		$params['attrs'] .= self::_dataToAttrs($params);

		$params['data_img'] = isset($params['data_img']) ? $params['data_img'] : array();

		if(isset($params['required']) && $params['required']) {
			$params['attrs'] .= ' required ';	// HTML5 "required" validation attr
		}
        if(isset($params['labeled_before']) && $params['labeled_before']) {
            $out .= $params['labeled_before'];
        }
        $out .= '<select name="'. $name. '" '. (isset($params['attrs']) ? $params['attrs'] : ''). '>';
        if(!empty($params['options'])) {
            foreach($params['options'] as $k => $v) {
                $selected = (isset($params['value']) && $k == $params['value'] ? 'selected="true"' : '');
				$opt_attrs = !empty($params['data_img'][$k]) ? $params['data_img'][$k] : '';
                $out .= '<option value="'. $k. '" '. $selected.  'title="'. $opt_attrs. '"' . '>'. $v. '</option>';
            }
        }
        $out .= '</select>';
        if(isset($params['labeled_after']) && $params['labeled_after']) {
            $out .= $params['labeled_after'];
        }
        if(isset($params['labeled']) && $params['labeled']) {
            $out .= $params['labeled'];
        }

        return $out;
    }

}
