<?php
class currency_switcherWcu extends moduleWcu
{
	public function init() {
		parent::init();
		add_action('wp_footer', array($this, 'showModule'));
		add_shortcode(
			WCU_SHORTCODE_FRONTEND_SWITCHER,
			function() {
				$previewOptions = array('shortcode' => true );
				return frameWcu::_()->getModule('currency_switcher')->drawModule(array(), true);
			}
		);
		$proModule = frameWcu::_()->getModule( 'promo' )->getProOptions();
		if ( !empty( $proModule ) ) {
			add_shortcode(
				WCU_SHORTCODE_FRONTEND_SWITCHER_EXTENDED,
				function () {
					return frameWcu::_()->getModule( 'currency_switcher' )->drawModule( true, true, 'extended' );
				}
			);
		}
	}

	public function showModule() {
		echo $this->drawModule();
	}

	/**
	 * DIsplay main module functionality
	 *
	 * @param array $previewOptions
	 * @param bool $isShortcode
	 *
	 * @return string
	 */
    public function drawModule($previewOptions = array(), $isShortcode = false, $mode = '' ) {
		if ($previewOptions) {
			$show = true;
        } else {
            $show = frameWcu::_()->getModule('currency')->getShowModule('currency_switcher');
		}

        if ($show) {
            return $this->getView()->getCurrencySwitcher($previewOptions, $isShortcode, $mode);
        } else {
    		return '';
        }
    }
    public function getDefaultOptions() {
        return array(
            'design_tab' => array(
                'enable' => '0',
                'type' => 'floating',
                'design' => 'Classic',
                'toggle_switcher' => 'on_click',
                'show' => 'currency_symbols',
                'icon_size' => 'm',
                'show_flags_simple' => '',
                'show_symbol_rotating' => '0',
				'show_flags_rotating' => '1',
				'show_flags_rotating_pro' => '0',
                'show_flags_floating_order' => '',
                'show_rates_floating_order' => '',
                'switcher_flag_opening_button' => '',
                'layout' => 'horizontal',
                'show_floating_order' => array('name','title','symbol'),
                'side_simple' => 'left',
                'side_floating' => 'left',
                'side_rotating' => 'left',
                'cur_currency_top' => '1',
                'horizontal_offset_desktop' => '0',
                'horizontal_offset_desktop_dimension' => '%',
                'horizontal_offset_mobile' => '0',
                'horizontal_offset_mobile_dimension' => 'px',
                'vertical_offset_desktop' => '50',
                'vertical_offset_desktop_dimension' => '%',
                'vertical_offset_mobile' => '0',
                'vertical_offset_mobile_dimension' => 'px',
                'switcher_opening_button' => 'currency_codes',
                'switcher_opening_button_text' => 'Change currency',
                'switcher_button_show_current' => '0',
                'icon_type' => 'rectangular',
                'icon_spacing' => '0',
                'show_border' => '0',
				'simple_opacity_panel' => '100',
				'floating_opacity_panel' => '100',
				'floating_opacity_button' => '100',
                'border_radius' => '0',
                'border_radius_dimension' => 'px',

				'floating_panel_header_show' => '1',
				'floating_panel_header_text' => 'Choose currency',
				'floating_panel_header_txt_color' => '#ffffff',
				'floating_panel_header_bg_color' => '#1e73be',

				'floating_opening_btn_font' => 'sans-serif',
				'floating_opening_btn_size' => '14',
				'floating_opening_btn_bold' => '1',
				'floating_opening_btn_italic' => '0',

				'floating_panel_header_font' => 'sans-serif',
				'floating_panel_header_size' => '14',
				'floating_panel_header_bold' => '1',
				'floating_panel_header_italic' => '0',

				'floating_panel_txt_font' => 'sans-serif',
				'floating_panel_txt_size' => '14',
				'floating_panel_txt_bold' => '1',
				'floating_panel_txt_italic' => '0',

				'floating_icon_size' => 'm',

                'bor_color' => '#1e73be',
                'txt_color' => '#ffffff',
                'txt_color_h' => '#ffffff',
                'txt_color_cur' => '#ffffff',
                'bg_color' => '#1e73be',
                'bg_color_h' => '#194e9e',
                'bg_color_cur' => '#1a54ad',
                'rot_block_txt_color' => '#ffffff',
                'rot_block_txt_color_h' => '#ffffff',
                'rot_block_txt_color_cur' => '#ffffff',
                'rot_block_bg_color' => '#1e73be',
                'rot_block_bg_color_h' => '#194e9e',
                'rot_block_bg_color_cur' => '#1a54ad',
            ),
            'display_rules_tab' => array(
                'show_on' => 'both',
                'show_on_screen' => '0',
                'show_on_screen_compare' => 'more',
                'show_on_screen_value' => '760',
				'switcher_shortcode_frontend' => '<code>[' . WCU_SHORTCODE_FRONTEND_SWITCHER . ']</code>',
				'switcher_shortcode_frontend_php' => '<code>'.htmlentities("<?php echo do_shortcode('[" . WCU_SHORTCODE_FRONTEND_SWITCHER . "]')?>").'</code>',
                'switcher_shortcode_frontend_extended' => '<code>[' . WCU_SHORTCODE_FRONTEND_SWITCHER_EXTENDED . ']</code>',
                'switcher_shortcode_frontend_extended_php' => '<code>'.htmlentities("<?php echo do_shortcode('[" . WCU_SHORTCODE_FRONTEND_SWITCHER_EXTENDED . "]')?>").'</code>',
                'switcher_shortcode' => '<code>[' . WCU_SHORTCODE_SWITCHER . ']</code>',
                'switcher_shortcode_php' => '<code>'.htmlentities("<?php echo do_shortcode('[" . WCU_SHORTCODE_SWITCHER . "]')?>").'</code>',
                'display_by_default' => 'enable',
                'pages_to_show' => array(),
                'product_categories_to_show' => array(),
                'custom_post_types_to_show' => array(),
				'pages_to_show_checkbox' => '0',
                'product_categories_to_show_checkbox' => '0',
                'custom_post_types_to_show_checkbox' => '0',
	            'show_popup_message' => '0',
            ),
        );
    }
    public function getOptionsParams()
    {
		$fontFamilyList = frameWcu::_()->getModule('currency')->getFontFamilyList();
		$optionsPro = frameWcu::_()->getModule('options_pro');
		$optionsProFlag = frameWcu::_()->getModule('flags');
        $proModule = false;
		$flagModule = false;
        if ( !empty( $optionsPro ) ) {
            $proModule = true;
        }
        if ( !empty( $optionsProFlag ) ) {
            $flagModule = true;
        }
        // to find params description and hooks @see currencyWcu::getOptionsParams
        return array(
            'design_tab' => array(
                'enable' => array(
                    'html' => 'checkboxHiddenVal',
                    'row_classes' => '',
                    'row_show' => '',
                    'row_hide' => '',
                    'tooltip' => __('Currency Switcher allows you to switch the currency of the products according to the selected settings.', WCU_LANG_CODE),
                    'label' => __('Enable switcher', WCU_LANG_CODE),
                    'params' => array(
                        'value'=>'1',
                    ),
                ),
                'type' => array(
                    'html' => 'selectbox',
                    'row_classes' => '',
                    'row_show' => '',
                    'row_hide' => '',
                    'tooltip' => __('Select currency switcher type.', WCU_LANG_CODE),
                    'label' => __('Type', WCU_LANG_CODE),
                    'params' => array(
                        'options' => array(
                            'simple' => __('Simple', WCU_LANG_CODE),
                            'floating' => __('Floating', WCU_LANG_CODE),
                            'rotating' => __('Rotating', WCU_LANG_CODE),
                        ),
                        'data-target-toggle' => '.wcuSwEnableDesign, .wcuSwEnablePosition, .wcuSwEnableShow, .wcuSwEnableLayout, .wcuSwEnableCurCurrency, .wcuSwEnableHorizOffset, .wcuSwEnableOpacity, .wcuSwEnableHeader',
                    ),
                ),
                'design' => $proModule ? frameWcu::_()->getModule('options_pro')->getProCsDesign() : array(
                    'html' => 'selectbox',
                    'row_classes' => 'wcuSwOptionsDesign wcuSwEnableDesign',
                    'row_parent' => 'type',
                    'row_show' => 'simple',
                    'row_hide' => '',
                    'tooltip' => __('Select design of panel for Currency Switcher Simple.', WCU_LANG_CODE),
                    'label' => __('Design', WCU_LANG_CODE).' <sup>PRO</sup>',
                    'params' => array(
                        'value' => 'Classic',
                        'options_attrs' => array(
                            'Dropdown' => "disabled",
                        ),
                        'options' => array(
                            'Classic' => __('Classic', WCU_LANG_CODE),
                            'Dropdown' => __('Dropdown', WCU_LANG_CODE).' <sup>PRO</sup>',
                        ),
                        'data-target-toggle' => '.wcuSwEnableSwitcher, .wcuSwEnableOpacitySimple',
                    ),
                ),
                'toggle_switcher' => array(
                    'html' => 'radiobuttons',
                    'row_classes' => 'wcuSwOptionsToggleSwitcher wcuSwEnableSwitcher',
                    'row_parent' => 'design',
                    'row_show' => '',
                    'row_hide' => 'Classic',
                    'row_hide_with_all' => true,
                    'tooltip' => __('Show panel by mouse hover or click.', WCU_LANG_CODE),
                    'label' => __('Toggle Switcher', WCU_LANG_CODE),
                    'params' => array(
                        'attrs' => 'class="wcuSwitcherRadioLabel"',
                        'no_br'	=> true,
                        'options' => array(
                            'on_click' => __('on click', WCU_LANG_CODE),
                            'on_hover' => __('on hover', WCU_LANG_CODE),
							'full_size' => __('full size view', WCU_LANG_CODE),
                        ),
                        'labeled' => array(
                            'on_click' => __('on click', WCU_LANG_CODE),
                            'on_hover' => __('on hover', WCU_LANG_CODE),
							'full_size' => __('full size view', WCU_LANG_CODE),
                        ),
                    ),
                ),
                'cur_currency_top' => array(
                    'html' => 'checkboxHiddenVal',
                    'row_classes' => 'wcuSwEnableCurCurrency',
                    'row_parent' => 'type',
                    'row_show' => '',
                    'row_hide' => 'simple',
                    'tooltip' => __('Show current currency at the top of the panel.', WCU_LANG_CODE),
                    'label' => __('Move current currency to top', WCU_LANG_CODE),
                    'params' => array(
                        'value'=>'1',
                    ),
                ),
                'show_symbol_rotating' => array(
                    'html' => 'checkboxHiddenVal',
                    'row_classes' => 'wcuSwEnableShow',
                    'row_parent' => 'type',
                    'row_show' => 'rotating',
                    'tooltip' => __('Show currency symbols in Currency Switcher Rotating.', WCU_LANG_CODE),
                    'label' => __('Show currency symbols', WCU_LANG_CODE),
                ),
				'show_flags_rotating' => array(
                    'html' => 'checkboxHiddenVal',
                    'row_classes' => 'wcuSwEnableShow',
                    'row_parent' => 'type',
                    'row_show' => !$flagModule ? 'hidden' : 'rotating',
                    'tooltip' => __('Show currency flag in Currency Switcher Rotating.', WCU_LANG_CODE),
                    'label' => __('Show flags in Rotating', WCU_LANG_CODE),
                    'params' => array(
                        'attrs' => !$flagModule ? 'disabled' : '',
                    ),
                ),
				'show_flags_rotating_pro' => array(
                    'html' => 'checkboxHiddenVal',
                    'row_classes' => 'wcuSwEnableShow',
                    'row_parent' => 'type',
                    'row_show' => $flagModule ? 'hidden' : 'rotating',
                    'tooltip' => __('Show currency flag in Currency Switcher Rotating.', WCU_LANG_CODE),
                    'label' => __('Show flags in Rotating', WCU_LANG_CODE).' <sup>PRO</sup>',
                    'params' => array(
                        'attrs' => 'disabled ',
                    ),
                ),
                'show_flags_simple' => array(
                    'html' => 'checkboxHiddenVal',
                    'row_classes' => 'wcuSwEnableShow',
                    'row_parent' => 'type',
                    'row_show' => $flagModule ? 'hidden' : 'simple',
                    'tooltip' => __('Show currency flag in Currency Switcher Simple.', WCU_LANG_CODE),
                    'label' => __('Show flags in Simple', WCU_LANG_CODE).' <sup>PRO</sup>',
                    'params' => array(
                        'attrs' => 'disabled ',
                    ),
                ),
                'show' => array(
                    'html' => 'radiobuttons',
                    'row_classes' => 'wcuSwEnableShow',
                    'row_parent' => 'type',
                    'row_show' => 'simple',
                    'row_hide' => '',
                    'tooltip' => __('Choose your preferred currency display.', WCU_LANG_CODE),
                    'label' => __('Show', WCU_LANG_CODE),
                    'params' => array(
                        'attrs' => 'class="wcuSwitcherRadioLabel"',
                        'no_br'	=> true,
                        'options' => $flagModule ? array(
                            'flags' => __('Flags', WCU_LANG_CODE),
                            'currency_codes' => __('Currency codes', WCU_LANG_CODE),
                            'currency_symbols' => __('Currency symbols', WCU_LANG_CODE),
                        ) : array(
                            'currency_codes' => __('Currency codes', WCU_LANG_CODE),
                            'currency_symbols' => __('Currency symbols', WCU_LANG_CODE),
                        ),
                        'labeled' => $flagModule ? array(
                            'flags' => __('Flags', WCU_LANG_CODE),
                            'currency_codes' => __('Currency codes', WCU_LANG_CODE),
                            'currency_symbols' => __('Currency symbols', WCU_LANG_CODE),
                        ) : array(
                            'currency_codes' => __('Currency codes', WCU_LANG_CODE),
                            'currency_symbols' => __('Currency symbols', WCU_LANG_CODE),
                        ),
                    ),
                ),
                'show_flags_floating_order' => array(
                    'html' => 'checkboxHiddenVal',
                    'row_classes' => 'wcuSwEnableShow',
                    'row_parent' => 'type',
                    'row_show' => $flagModule ? 'hidden' : 'floating',
                    'tooltip' => __('Show currency flag in order list for Currency Switcher Floating.', WCU_LANG_CODE),
                    'label' => __('Show flags in order', WCU_LANG_CODE).' <sup>PRO</sup>',
                    'params' => array(
                        'attrs' => 'disabled ',
                    ),
                ),
                'show_rates_floating_order' => array(
                    'html' => 'checkboxHiddenVal',
                    'row_classes' => 'wcuSwEnableShow',
                    'row_parent' => 'type',
                    'row_show' => $proModule ? 'hidden' : 'floating',
                    'tooltip' => __('Show currency rates in order list for Currency Switcher Floating.', WCU_LANG_CODE),
                    'label' => __('Show rates in order', WCU_LANG_CODE).' <sup>PRO</sup>',
                    'params' => array(
                        'attrs' => 'disabled ',
                    ),
                ),
                'show_floating_order' => array(
                    'html' => 'selectlistsortable',
                    'row_classes' => 'wcuSwEnableCurCurrency',
                    'row_parent' => 'type',
                    'row_show' => 'floating',
                    'row_hide' => '',
                    'tooltip' => __('Set the display order and what to display in the list.', WCU_LANG_CODE),
                    'label' => __('Show floating order', WCU_LANG_CODE),
                    'params' => array(
                        'id' => 'wcuShowFloatingOrder',
                        'attrs' => 'style="height:130px;"',
                        'options' => $proModule ? frameWcu::_()->getModule('options_pro')->getProCsFloatingOrder() : array(
                            'name' => __('Currency codes', WCU_LANG_CODE),
                            'title' => __('Titles', WCU_LANG_CODE),
                            'symbol' => __('Currency symbols', WCU_LANG_CODE),
                        ),
                    ),
                ),
                'icon_size' => $proModule ? frameWcu::_()->getModule('options_pro')->getProCsIconSize() : array(
                    'html' => 'radiobuttons',
                    'row_classes' => 'wcuSwEnableDesign',
                    'row_parent' => 'type',
                    'row_show' => 'simple',
                    'row_hide' => '',
                    'tooltip' => __('Choose icon size for Currency Switcher Simple.', WCU_LANG_CODE),
                    'label' => __('Icon size', WCU_LANG_CODE).' <sup>PRO</sup>',
                    'params' => array(
                        'value' => 'm',
                        'attrs' => 'class="wcuSwitcherRadioLabel" disabled',
                        'no_br'	=> true,
                        'options' => array(
                            'proS' => __('S', WCU_LANG_CODE),
                            'm' => __('M', WCU_LANG_CODE),
                            'proL' => __('L', WCU_LANG_CODE),
                        ),
                        'labeled' => array(
                            'proS' => __('S', WCU_LANG_CODE),
                            'm' => __('M', WCU_LANG_CODE),
                            'proL' => __('L', WCU_LANG_CODE),
                        ),
                    ),
                ),
                'layout' => array(
                    'html' => 'radiobuttons',
                    'row_classes' => 'wcuSwEnableLayout',
                    'row_parent' => 'type',
                    'row_show' => 'simple',
                    'row_hide' => '',
                    'tooltip' => __('Choose panel layout design. Set the horizontal и vertical offset value according to the selected position of layout design.', WCU_LANG_CODE),
                    'label' => __('Layout', WCU_LANG_CODE),
                    'params' => array(
                        'attrs' => 'class="wcuSwitcherRadioLabel"',
                        'no_br'	=> true,
                        'options' => array(
                            'horizontal' => __('Horizontal', WCU_LANG_CODE),
                            'vertical' => __('Vertical', WCU_LANG_CODE),
                        ),
                        'labeled' => array(
                            'horizontal' => __('Horizontal', WCU_LANG_CODE),
                            'vertical' => __('Vertical', WCU_LANG_CODE),
                        ),
                    ),
                ),
                'side_simple' => array(
                    'html' => 'radiobuttons',
                    'row_classes' => 'wcuSwEnablePosition',
                    'row_parent' => 'type',
                    'row_show' => 'simple',
                    'row_hide' => '',
                    'tooltip' => __('Set the horizontal и vertical offset value according to the selected position of layout design', WCU_LANG_CODE),
                    'label' => __('Position', WCU_LANG_CODE),
                    'params' => array(
                        'attrs' => 'class="wcuSwitcherRadioLabel"',
                        'no_br'	=> true,
                        'options' => array(
                            'left' => __('left', WCU_LANG_CODE),
                            'right' => __('right', WCU_LANG_CODE),
                        ),
                        'labeled' => array(
                            'left' => __('left', WCU_LANG_CODE),
                            'right' => __('right', WCU_LANG_CODE),
                        ),
                    ),
                ),
                'side_rotating' => array(
                    'html' => 'radiobuttons',
                    'row_classes' => 'wcuSwEnablePosition',
                    'row_parent' => 'type',
                    'row_show' => 'rotating',
                    'row_hide' => '',
                    'tooltip' => __('Choose basic position of panel. Set the horizontal and vertical offset value according to the selected position of layout design.', WCU_LANG_CODE),
                    'label' => __('Position', WCU_LANG_CODE),
                    'params' => array(
                        'attrs' => 'class="wcuSwitcherRadioLabel"',
                        'no_br'	=> true,
                        'options' => array(
                            'left' => __('left', WCU_LANG_CODE),
                            'right' => __('right', WCU_LANG_CODE),
                        ),
                        'labeled' => array(
                            'left' => __('left', WCU_LANG_CODE),
                            'right' => __('right', WCU_LANG_CODE),
                        ),
                    ),
                ),
                'side_floating' => array(
                    'html' => 'radiobuttons',
                    'row_classes' => 'wcuSwEnablePosition',
                    'row_parent' => 'type',
                    'row_show' => 'floating',
                    'row_hide' => '',
                    'tooltip' => __('Choose basic position of panel. Set the horizontal and vertical offset value according to the selected position of layout design.', WCU_LANG_CODE),
                    'label' => __('Position', WCU_LANG_CODE),
                    'params' => array(
                        'attrs' => 'class="wcuSwitcherRadioLabel"',
                        'no_br'	=> true,
                        'options' => array(
                            'left' => __('left', WCU_LANG_CODE),
                            'right' => __('right', WCU_LANG_CODE),
                            'top' => __('top', WCU_LANG_CODE),
                            'bottom' => __('bottom', WCU_LANG_CODE),
                        ),
                        'labeled' => array(
                            'left' => __('left', WCU_LANG_CODE),
                            'right' => __('right', WCU_LANG_CODE),
                            'top' => __('top', WCU_LANG_CODE),
                            'bottom' => __('bottom', WCU_LANG_CODE),
                        ),
                    ),
                ),
                'switcher_flag_opening_button' => array(
                    'html' => 'checkboxHiddenVal',
                    'row_classes' => 'wcuSwEnableShow',
                    'row_parent' => 'type',
                    'row_show' => $proModule ? 'hidden' : 'floating',
                    'tooltip' => __('Show currency flag in opening button.', WCU_LANG_CODE),
                    'label' => __('Show flags in button', WCU_LANG_CODE).' <sup>PRO</sup>',
                    'params' => array(
                        'attrs' => 'disabled ',
                    ),
                ),
                'switcher_opening_button' => array(
                    'html' => 'radiobuttons',
                    'row_classes' => 'wcuSwEnableDesign hideIfFullSizeView',
                    'row_parent' => 'type',
                    'row_show' => 'floating',
                    'row_hide' => '',
                    'inrow' => 'open',
                    'tooltip' => __('Choose your preferred currency display.', WCU_LANG_CODE),
                    'label' => __('Switcher opening button', WCU_LANG_CODE),
                    'params' => $proModule ? frameWcu::_()->getModule('options_pro')->getProCsFloatingOpeningButton() : array(
                        'attrs' => 'class="wcuSwitcherRadioLabel"',
                        'no_br'	=> true,
                        'options' => array (
                            'currency_codes' => __('currency codes', WCU_LANG_CODE),
                            'currency_symbols' => __('currency symbols', WCU_LANG_CODE),
                            'text' => __('text', WCU_LANG_CODE),
                        ),
                        'labeled' => array (
                            'currency_codes' => __('currency codes', WCU_LANG_CODE),
                            'currency_symbols' => __('currency symbols', WCU_LANG_CODE),
                            'text' => __('text', WCU_LANG_CODE),
                        ),
                    ),
                ),
                'switcher_opening_button_text' => array(
                    'html' => 'input',
                    'row_classes' => 'wcuSwEnableDesign hideIfFullSizeView',
                    'row_parent' => 'type',
                    'row_show' => 'floating',
                    'row_hide' => '',
                    'inrow' => 'close',
                    'params' => array(
                        'label_attrs' => 'class="wcuSwitcherInputLabel" ',
                        'attrs' => 'class="wcuSwitcherInput" style="width:200px;"',
                    ),
                ),

				'floating_opening_btn_font' => array(
					'html' => 'selectbox',
					'row_classes' => 'wcuSwEnablePosition hideIfFullSizeView',
                    'row_parent' => 'type',
                    'row_show' => 'floating',
					'row_hide' => '',
					'inrow' => 'open',
					'tooltip' => __('Select switcher opening button font setting.', WCU_LANG_CODE),
					'label' => __('Switcher opening button font setting', WCU_LANG_CODE),
					'params' => array(
						'attrs' => "style='width:100px'",
						'options' => $fontFamilyList,
					),
				),
				'floating_opening_btn_size' => array(
					'html' => 'input',
					'row_classes' => 'wcuSwEnablePosition hideIfFullSizeView',
					'row_parent' => 'type',
					'row_show' => 'floating',
					'row_hide' => '',
					'inrow' => 'middle',
					'params' => array(
						'label_attrs' => 'class="wcuSwitcherInputLabel"',
						'attrs' => 'class="wcuSwitcherInput"',
						'labeled' => __('px', WCU_LANG_CODE).'&emsp;',
						'labeled_right' => true,
					),
				),
				'floating_opening_btn_bold' => array(
					'html' => 'checkboxHiddenVal',
					'row_classes' => 'wcuSwEnablePosition hideIfFullSizeView',
                    'row_parent' => 'type',
                    'row_show' => 'floating',
					'row_hide' => '',
					'inrow' => 'middle',
					'params' => array(
						'value' => '1',
						'labeled' => '&emsp;'.__('Bold', WCU_LANG_CODE).'&emsp;',
						'labeled_right' => true,
					),
				),
				'floating_opening_btn_italic' => array(
					'html' => 'checkboxHiddenVal',
					'row_classes' => 'wcuSwEnablePosition hideIfFullSizeView',
					'row_parent' => 'type',
					'row_show' => 'floating',
					'row_hide' => '',
					'inrow' => 'close',
					'params' => array(
						'value'=>'1',
						'labeled' => '&emsp;'.__('Italic', WCU_LANG_CODE).'&emsp;',
						'labeled_right' => true,
					),
				),

				'floating_icon_size' => array(
                    'html' => 'radiobuttons',
					'row_classes' => 'wcuSwEnablePosition hideIfFullSizeView',
					'row_parent' => 'type',
					'row_show' => 'floating',
					'row_hide' => '',
                    'tooltip' => __('Choose size for opening button.', WCU_LANG_CODE),
                    'label' => __('Panel opening button size', WCU_LANG_CODE),
                    'params' => array(
                        'attrs' => 'class="wcuSwitcherRadioLabel" ',
                        'no_br'	=> true,
                        'options' => array(
                            's' => __('S', WCU_LANG_CODE),
                            'm' => __('M', WCU_LANG_CODE),
                            'l' => __('L', WCU_LANG_CODE),
                        ),
                        'labeled' => array(
                            's' => __('S', WCU_LANG_CODE),
                            'm' => __('M', WCU_LANG_CODE),
                            'l' => __('L', WCU_LANG_CODE),
                        ),
                    ),
                ),

                'switcher_button_show_current' => array(
                    'html' => 'checkboxHiddenVal',
                    'row_classes' => 'wcuSwEnableDesign hideIfFullSizeView',
                    'row_parent' => 'type',
                    'row_show' => 'floating',
                    'row_hide' => '',
                    'tooltip' => __('Show only current currency in opening button.', WCU_LANG_CODE),
                    'label' => __('Show current currency only', WCU_LANG_CODE),
                    'params' => array(
                        'value'=>'1',
                    ),
                ),
                'horizontal_offset_desktop' => array(
                    'html' => 'input',
                    'row_classes' => 'wcuSwEnableHorizOffset',
                    'row_parent' => 'type',
                    'row_show' => '',
                    'row_hide' => 'rotating',
                    'inrow' => 'open',
                    'tooltip' => __('Change horizontal panel offset (only for top and bottom positions if type Floating or Rotating)', WCU_LANG_CODE),
                    'label' => __('Horizontal offset', WCU_LANG_CODE),
                    'params' => array(
                        'label_attrs' => 'class="wcuSwitcherInputLabel"',
                        'attrs' => 'class="wcuSwitcherInput"',
                        'labeled' => __('desktop', WCU_LANG_CODE),
                    ),
                ),
                'horizontal_offset_desktop_dimension' => array(
                    'html' => 'radiobuttons',
                    'row_classes' => 'wcuSwEnableHorizOffset',
                    'row_parent' => 'type',
                    'row_show' => '',
                    'row_hide' => 'rotating',
                    'inrow' => 'middle',
                    'params' => array(
                        'attrs' => 'class="wcuSwitcherRadioLabel"',
                        'no_br'	=> true,
                        'options' => array(
                            'px' => __('px', WCU_LANG_CODE),
                            '%' => __('%', WCU_LANG_CODE),
                        ),
                        'labeled' => array(
                            'px' => __('px', WCU_LANG_CODE),
                            '%' => __('%', WCU_LANG_CODE),
                        ),
                    ),
                ),
                'horizontal_offset_mobile' => array(
                    'html' => 'input',
                    'row_classes' => 'wcuSwEnableHorizOffset',
                    'row_parent' => 'type',
                    'row_show' => '',
                    'row_hide' => 'rotating',
                    'inrow' => 'middle',
                    'params' => array(
                        'label_attrs' => 'style="margin-left:30px;" class="wcuSwitcherInputLabel"',
                        'attrs' => 'class="wcuSwitcherInput"',
                        'labeled' => __('mobile', WCU_LANG_CODE),
                    ),
                ),
                'horizontal_offset_mobile_dimension' => array(
                    'html' => 'radiobuttons',
                    'row_classes' => 'wcuSwEnableHorizOffset',
                    'row_parent' => 'type',
                    'row_show' => '',
                    'row_hide' => 'rotating',
                    'inrow' => 'close',
                    'params' => array(
                        'attrs' => 'class="wcuSwitcherRadioLabel"',
                        'no_br'	=> true,
                        'options' => array(
                            'px' => __('px', WCU_LANG_CODE),
                            '%' => __('%', WCU_LANG_CODE),
                        ),
                        'labeled' => array(
                            'px' => __('px', WCU_LANG_CODE),
                            '%' => __('%', WCU_LANG_CODE),
                        ),
                    ),
                ),
                'vertical_offset_desktop' => array(
                    'html' => 'input',
                    'row_classes' => '',
                    'row_parent' => '',
                    'row_show' => '',
                    'row_hide' => '',
                    'inrow' => 'open',
                    'label' => __('Vertical offset', WCU_LANG_CODE),
                    'tooltip' => __('Change vertical panel offset (only for left and right positions if type Floating or Rotating)', WCU_LANG_CODE),
                    'params' => array(
                        'label_attrs' => 'class="wcuSwitcherInputLabel"',
                        'attrs' => 'class="wcuSwitcherInput"',
                        'labeled' => __('desktop', WCU_LANG_CODE),
                    ),
                ),
                'vertical_offset_desktop_dimension' => array(
                    'html' => 'radiobuttons',
                    'row_classes' => '',
                    'row_parent' => '',
                    'row_show' => '',
                    'row_hide' => '',
                    'inrow' => 'middle',
                    'params' => array(
                        'attrs' => 'class="wcuSwitcherRadioLabel"',
                        'no_br'	=> true,
                        'options' => array(
                            'px' => __('px', WCU_LANG_CODE),
                            '%' => __('%', WCU_LANG_CODE),
                        ),
                        'labeled' => array(
                            'px' => __('px', WCU_LANG_CODE),
                            '%' => __('%', WCU_LANG_CODE),
                        ),

                    ),
                ),
                'vertical_offset_mobile' => array(
                    'html' => 'input',
                    'row_classes' => '',
                    'row_parent' => '',
                    'row_show' => '',
                    'row_hide' => '',
                    'inrow' => 'middle',
                    'params' => array(
                        'label_attrs' => 'style="margin-left:30px;" class="wcuSwitcherInputLabel"',
                        'attrs' => 'class="wcuSwitcherInput"',
                        'labeled' => __('mobile', WCU_LANG_CODE),
                    ),
                ),
                'vertical_offset_mobile_dimension' => array(
                    'html' => 'radiobuttons',
                    'row_classes' => '',
                    'row_parent' => '',
                    'row_show' => '',
                    'row_hide' => '',
                    'inrow' => 'close',
                    'params' => array(
                        'attrs' => 'class="wcuSwitcherRadioLabel"',
                        'no_br'	=> true,
                        'options' => array(
                            'px' => __('px', WCU_LANG_CODE),
                            '%' => __('%', WCU_LANG_CODE),
                        ),
                        'labeled' => array(
                            'px' => __('px', WCU_LANG_CODE),
                            '%' => __('%', WCU_LANG_CODE),
                        ),
                    ),
                ),
                'icon_type' => $proModule ? frameWcu::_()->getModule('options_pro')->getProCsIconType() : array(
                    'html' => 'radiobuttons',
                    'row_classes' => 'wcuSwEnableDesign wcuHidden',
                    'row_parent' => 'type',
                    'row_show' => 'simple',
                    'row_hide' => '',
                    'tooltip' => __('Choose design of currency switcher blocks.', WCU_LANG_CODE),
                    'label' => __('Icon type', WCU_LANG_CODE).' <sup>PRO</sup>',
                    'params' => array(
                        'attrs' => 'class="wcuSwitcherRadioLabel" disabled',
                        'value' => 'rectangular',
                        'no_br'	=> true,
                        'options' => array(
                            'rectangular' => __('rectangular', WCU_LANG_CODE),
                            //'circleicon' => __('circle', WCU_LANG_CODE),
                        ),
                        'labeled' => array(
                            'rectangular' => __('rectangular', WCU_LANG_CODE),
                            //'circleicon' => __('circle', WCU_LANG_CODE),
                        ),
                        'data-target-toggle' => '.wcuSwDisableIconType',
                    ),
                ),
                'icon_spacing' => array(
                    'html' => 'input',
                    'row_classes' => 'wcuSwEnableDesign',
                    'row_parent' => 'type',
                    'row_show' => 'simple',
                    'row_hide' => '',
                    'tooltip' => __('Set the distance between currency blocks (margin)', WCU_LANG_CODE),
                    'label' => __('Icon spacing', WCU_LANG_CODE),
                    'params' => array(
                        'labeled_right' => true,
                        'label_attrs' => 'class="wcuSwitcherInputLabel"',
                        'attrs' => 'class="wcuSwitcherInput" style="margin-left:0px"',
                        'labeled' => __('px ', WCU_LANG_CODE),
                    ),
                ),
				'simple_opacity_panel' => array(
                    'html' => 'input',
                    'row_classes' => 'wcuSwEnableDesign',
                    'row_parent' => 'type',
                    'row_show' => '',
                    'row_hide' => 'floating',
                    'tooltip' => __('Change the level of transparency for the panel.', WCU_LANG_CODE),
                    'label' => __('Transparent', WCU_LANG_CODE),
                    'params' => array(
                        'label_attrs' => 'class="wcuSwitcherInputLabel"',
                        'attrs' => 'class="wcuSwitcherInput"',
                        'labeled' => __('panel', WCU_LANG_CODE),
						'placeholder' => '0 - 100',
                    ),
                ),
				'floating_opacity_panel' => array(
                    'html' => 'input',
					'row_classes' => 'wcuSwEnablePosition',
                    'row_parent' => 'type',
                    'row_show' => 'floating',
                    'row_hide' => '',
                    'inrow' => 'open',
                    'tooltip' => __('Change the level of transparency for the panel and opening button.', WCU_LANG_CODE),
                    'label' => __('Transparent', WCU_LANG_CODE),
                    'params' => array(
                        'label_attrs' => 'class="wcuSwitcherInputLabel"',
                        'attrs' => 'class="wcuSwitcherInput"',
                        'labeled' => __('panel', WCU_LANG_CODE),
						'placeholder' => '0 - 100',
                    ),
                ),
				'floating_opacity_button' => array(
                    'html' => 'input',
					'row_classes' => 'wcuSwEnablePosition',
                    'row_parent' => 'type',
                    'row_show' => 'floating',
                    'row_hide' => '',
                    'inrow' => 'middle',
                    'label' => __('Transparent', WCU_LANG_CODE),
                    'params' => array(
                        'label_attrs' => 'class="wcuSwitcherInputLabel"',
                        'attrs' => 'class="wcuSwitcherInput"',
                        'labeled' => __('button', WCU_LANG_CODE),
						'placeholder' => '0 - 100',
                    ),
                ),
                'show_border' => array(
                    'html' => 'checkboxHiddenVal',
                    'row_classes' => '',
                    'row_show' => 'all',
                    'row_hide' => '',
                    'tooltip' => __('Show panel outer border.', WCU_LANG_CODE),
                    'label' => __('Show border', WCU_LANG_CODE),
                    'params' => array(
                        'value'=>'1',
                    ),
                ),
                'border_radius' => array(
                    'html' => 'input',
                    'row_classes' => 'wcuSwEnableDesign',
                    'row_parent' => 'type',
                    'row_show' => 'simple',
                    'row_hide' => '',
                    'inrow' => 'open',
                    'tooltip' => __('Set panel border-radius.', WCU_LANG_CODE),
                    'label' => __('Border-radius', WCU_LANG_CODE),
                    'params' => array(
                        'label_attrs' => 'class="wcuSwitcherInputLabel"',
                        'attrs' => 'class="wcuSwitcherInput" style="margin-left:0px"',
                    ),
                ),
                'border_radius_dimension' => array(
                    'html' => 'radiobuttons',
                    'row_classes' => '',
                    'row_parent' => '',
                    'row_show' => '',
                    'row_hide' => '',
                    'inrow' => 'close',
                    'params' => array(
                        'attrs' => 'class="wcuSwitcherRadioLabel"',
                        'no_br'	=> true,
                        'options' => array(
                            'px' => __('px', WCU_LANG_CODE),
                            '%' => __('%', WCU_LANG_CODE),
                        ),
                        'labeled' => array(
                            'px' => __('px', WCU_LANG_CODE),
                            '%' => __('%', WCU_LANG_CODE),
                        ),
                    ),
                ),
                'bor_color' => array(
                    'html' => 'colorpicker',
                    'row_classes' => '',
                    'row_parent' => '',
                    'row_show' => '',
                    'row_hide' => '',
                    'tooltip' => __('Choose panel border color.', WCU_LANG_CODE),
                    'label' => __('Border color', WCU_LANG_CODE),
                ),
				'floating_panel_header_show' => array(
                    'html' => 'checkboxHiddenVal',
					'row_classes' => 'wcuSwEnablePosition',
                    'row_parent' => 'type',
                    'row_show' => 'floating',
                    'row_hide' => '',
                    'tooltip' => __('Show panel title.', WCU_LANG_CODE),
                    'label' => __('Show header', WCU_LANG_CODE),
                    'params' => array(
                        'value'=>'1',
                    ),
                ),
				'floating_panel_header_text' => array(
                    'html' => 'input',
					'row_classes' => 'wcuSwEnablePosition',
                    'row_parent' => 'type',
                    'row_show' => 'floating',
                    'row_hide' => '',
                    'tooltip' => __('Type your text for panel title.', WCU_LANG_CODE),
					'label' => __('Panel header text', WCU_LANG_CODE),
                    'params' => array(
                        'attrs' => 'style="width:200px;"',
                    ),
                ),
				'floating_panel_header_txt_color' => array(
                    'html' => 'colorpicker',
					'row_classes' => 'wcuSwEnablePosition',
                    'row_parent' => 'type',
                    'row_show' => 'floating',
                    'row_hide' => '',
                    'tooltip' => __('Choose text color for panel title.', WCU_LANG_CODE),
                    'label' => __('Panel header text color', WCU_LANG_CODE),
                ),
				'floating_panel_header_bg_color' => array(
                    'html' => 'colorpicker',
					'row_classes' => 'wcuSwEnablePosition',
                    'row_parent' => 'type',
                    'row_show' => 'floating',
                    'row_hide' => '',
                    'tooltip' => __('Choose background color for panel title.', WCU_LANG_CODE),
                    'label' => __('Panel header background color', WCU_LANG_CODE),
                ),



				'floating_panel_header_font' => array(
					'html' => 'selectbox',
					'row_classes' => 'wcuSwEnablePosition',
                    'row_parent' => 'type',
                    'row_show' => 'floating',
					'row_hide' => '',
					'inrow' => 'open',
					'tooltip' => __('Select panel header font setting.', WCU_LANG_CODE),
					'label' => __('Panel header font setting', WCU_LANG_CODE),
					'params' => array(
						'attrs' => "style='width:100px'",
						'options' => $fontFamilyList,
					),
				),
				'floating_panel_header_size' => array(
					'html' => 'input',
					'row_classes' => 'wcuSwEnablePosition',
					'row_parent' => 'type',
					'row_show' => 'floating',
					'row_hide' => '',
					'inrow' => 'middle',
					'params' => array(
						'label_attrs' => 'class="wcuSwitcherInputLabel"',
						'attrs' => 'class="wcuSwitcherInput"',
						'labeled' => __('px', WCU_LANG_CODE).'&emsp;',
						'labeled_right' => true,
					),
				),
				'floating_panel_header_bold' => array(
					'html' => 'checkboxHiddenVal',
					'row_classes' => 'wcuSwEnablePosition',
                    'row_parent' => 'type',
                    'row_show' => 'floating',
					'row_hide' => '',
					'inrow' => 'middle',
					'params' => array(
						'value' => '1',
						'labeled' => '&emsp;'.__('Bold', WCU_LANG_CODE).'&emsp;',
						'labeled_right' => true,
					),
				),
				'floating_panel_header_italic' => array(
					'html' => 'checkboxHiddenVal',
					'row_classes' => 'wcuSwEnablePosition',
					'row_parent' => 'type',
					'row_show' => 'floating',
					'row_hide' => '',
					'inrow' => 'close',
					'params' => array(
						'value'=>'1',
						'labeled' => '&emsp;'.__('Italic', WCU_LANG_CODE).'&emsp;',
						'labeled_right' => true,
					),
				),

				'floating_panel_txt_font' => array(
					'html' => 'selectbox',
					'row_classes' => 'wcuSwEnablePosition',
					'row_parent' => 'type',
					'row_show' => 'floating',
					'row_hide' => '',
					'inrow' => 'open',
					'tooltip' => __('Select panel text font setting.', WCU_LANG_CODE),
					'label' => __('Panel text font setting', WCU_LANG_CODE),
					'params' => array(
						'attrs' => "style='width:100px'",
						'options' => $fontFamilyList,
					),
				),
				'floating_panel_txt_size' => array(
					'html' => 'input',
					'row_classes' => 'wcuSwEnablePosition',
					'row_parent' => 'type',
					'row_show' => 'floating',
					'row_hide' => '',
					'inrow' => 'middle',
					'params' => array(
						'label_attrs' => 'class="wcuSwitcherInputLabel"',
						'attrs' => 'class="wcuSwitcherInput"',
						'labeled' => __('px', WCU_LANG_CODE).'&emsp;',
						'labeled_right' => true,
					),
				),
				'floating_panel_txt_bold' => array(
					'html' => 'checkboxHiddenVal',
					'row_classes' => 'wcuSwEnablePosition',
					'row_parent' => 'type',
					'row_show' => 'floating',
					'row_hide' => '',
					'inrow' => 'middle',
					'params' => array(
						'value' => '1',
						'labeled' => '&emsp;'.__('Bold', WCU_LANG_CODE).'&emsp;',
						'labeled_right' => true,
					),
				),
				'floating_panel_txt_italic' => array(
					'html' => 'checkboxHiddenVal',
					'row_classes' => 'wcuSwEnablePosition',
					'row_parent' => 'type',
					'row_show' => 'floating',
					'row_hide' => '',
					'inrow' => 'close',
					'params' => array(
						'value'=>'1',
						'labeled' => '&emsp;'.__('Italic', WCU_LANG_CODE).'&emsp;',
						'labeled_right' => true,
					),
				),

                'txt_color' => array(
                    'html' => 'colorpicker',
                    'row_classes' => '',
                    'row_parent' => '',
                    'row_show' => '',
                    'row_hide' => '',
                    'tooltip' => __('Choose panel text color.', WCU_LANG_CODE),
                    'label' => __('Text color', WCU_LANG_CODE),
                    'inrow' => 'open',
                    'params' => array(
                        'label_before' => __('static', WCU_LANG_CODE),
                        'label_before_attrs' => 'class="wcuColorPickerLabelBefore"',
                    ),
                ),
                'txt_color_h' => array(
                    'html' => 'colorpicker',
                    'row_classes' => '',
                    'row_parent' => '',
                    'row_show' => '',
                    'row_hide' => '',
                    'inrow' => 'middle',
                    'params' => array(
                        'label_before' => __('hover', WCU_LANG_CODE),
                        'label_before_attrs' => 'class="wcuColorPickerLabelBefore"',
                    ),
                ),
                'txt_color_cur' => array(
                    'html' => 'colorpicker',
                    'row_classes' => '',
                    'row_parent' => '',
                    'row_show' => '',
                    'row_hide' => '',
                    'inrow' => 'close',
                    'params' => array(
                        'label_before' => __('selected', WCU_LANG_CODE),
                        'label_before_attrs' => 'class="wcuColorPickerLabelBefore"',
                    ),
                ),
                'bg_color' => array(
                    'html' => 'colorpicker',
                    'row_classes' => '',
                    'row_parent' => '',
                    'row_show' => '',
                    'row_hide' => '',
                    'tooltip' => __('Choose panel background color.', WCU_LANG_CODE),
                    'label' => __('Background color', WCU_LANG_CODE),
                    'inrow' => 'open',
                    'params' => array(
                        'label_before' => __('static', WCU_LANG_CODE),
                        'label_before_attrs' => 'class="wcuColorPickerLabelBefore"',
                    ),
                ),
                'bg_color_h' => array(
                    'html' => 'colorpicker',
                    'row_classes' => '',
                    'row_parent' => '',
                    'row_show' => '',
                    'row_hide' => '',
                    'inrow' => 'middle',
                    'params' => array(
                        'label_before' => __('hover', WCU_LANG_CODE),
                        'label_before_attrs' => 'class="wcuColorPickerLabelBefore"',
                    ),
                ),
                'bg_color_cur' => array(
                    'html' => 'colorpicker',
                    'row_classes' => '',
                    'row_parent' => '',
                    'row_show' => '',
                    'row_hide' => '',
                    'inrow' => 'close',
                    'params' => array(
                        'label_before' => __('selected', WCU_LANG_CODE),
                        'label_before_attrs' => 'class="wcuColorPickerLabelBefore"',
                    ),
                ),
                'rot_block_txt_color' => array(
                    'html' => 'colorpicker',
                    'row_classes' => 'wcuSwEnableDesign',
                    'row_parent' => 'type',
                    'row_show' => 'rotating',
                    'row_hide' => '',
                    'tooltip' => __('Choose rotating block text color.', WCU_LANG_CODE),
                    'label' => __('Rotating block text color', WCU_LANG_CODE),
                    'inrow' => 'open',
                    'params' => array(
                        'label_before' => __('static', WCU_LANG_CODE),
                        'label_before_attrs' => 'class="wcuColorPickerLabelBefore"',
                    ),
                ),
                'rot_block_txt_color_h' => array(
                    'html' => 'colorpicker',
                    'row_classes' => 'wcuSwEnableDesign',
                    'row_parent' => 'type',
                    'row_show' => 'rotating',
                    'row_hide' => '',
                    'inrow' => 'middle',
                    'params' => array(
                        'label_before' => __('hover', WCU_LANG_CODE),
                        'label_before_attrs' => 'class="wcuColorPickerLabelBefore"',
                    ),
                ),
                'rot_block_txt_color_cur' => array(
                    'html' => 'colorpicker',
                    'row_classes' => 'wcuSwEnableDesign',
                    'row_parent' => 'type',
                    'row_show' => 'rotating',
                    'row_hide' => '',
                    'inrow' => 'close',
                    'params' => array(
                        'label_before' => __('selected', WCU_LANG_CODE),
                        'label_before_attrs' => 'class="wcuColorPickerLabelBefore"',
                    ),
                ),
                'rot_block_bg_color' => array(
                    'html' => 'colorpicker',
                    'row_classes' => 'wcuSwEnableDesign',
                    'row_parent' => 'type',
                    'row_show' => 'rotating',
                    'row_hide' => '',
                    'tooltip' => __('Choose rotating block background color.', WCU_LANG_CODE),
                    'label' => __('Rotating block background color', WCU_LANG_CODE),
                    'inrow' => 'open',
                    'params' => array(
                        'label_before' => __('static', WCU_LANG_CODE),
                        'label_before_attrs' => 'class="wcuColorPickerLabelBefore"',
                    ),
                ),
                'rot_block_bg_color_h' => array(
                    'html' => 'colorpicker',
                    'row_classes' => 'wcuSwEnableDesign',
                    'row_parent' => 'type',
                    'row_show' => 'rotating',
                    'row_hide' => '',
                    'inrow' => 'middle',
                    'params' => array(
                        'label_before' => __('hover', WCU_LANG_CODE),
                        'label_before_attrs' => 'class="wcuColorPickerLabelBefore"',
                    ),
                ),
                'rot_block_bg_color_cur' => array(
                    'html' => 'colorpicker',
                    'row_classes' => 'wcuSwEnableDesign',
                    'row_parent' => 'type',
                    'row_show' => 'rotating',
                    'row_hide' => '',
                    'inrow' => 'close',
                    'params' => array(
                        'label_before' => __('selected', WCU_LANG_CODE),
                        'label_before_attrs' => 'class="wcuColorPickerLabelBefore"',
                    ),
                ),
            ),
            'display_rules_tab' => array(
                'show_on' => array(
                    'html' => 'selectbox',
                    'row_classes' => '',
                    'row_show' => 'all',
                    'row_hide' => '',
                    'tooltip' => __('Select the devices on which the panel should be displayed.', WCU_LANG_CODE),
                    'label' => __('Show on', WCU_LANG_CODE),
                    'params' => array(
                        'options' => array(
                            'both' => __('Mobile and Desktop', WCU_LANG_CODE),
                            'mobiles' => __('mobiles', WCU_LANG_CODE),
                            'desktops' => __('desktops', WCU_LANG_CODE),
                        ),
                        'data-target-toggle' => '.wcuSwEnable, .wcuSwRotating',
                    ),
                ),
                'show_on_screen' => array(
                    'html' => 'checkboxHiddenVal',
                    'row_classes' => '',
                    'row_show' => 'all',
                    'row_hide' => '',
                    'inrow' => 'open',
                    'tooltip' => __('If this option is selected, the panel will be displayed only under the selected conditions.', WCU_LANG_CODE),
                    'label' => __('Show on screen size', WCU_LANG_CODE),
                    'attrs' => 'style="margin-right: 10px;"',
                    'params' => array(
                        'value'=>'1',
                    ),
                ),
                'show_on_screen_compare' => array(
                    'html' => 'selectbox',
                    'row_classes' => '',
                    'row_show' => 'all',
                    'row_hide' => '',
                    'inrow' => 'middle',
                    'label' => __('Show on', WCU_LANG_CODE),
                    'params' => array(
                        'options' => array(
                            'less' => __('less', WCU_LANG_CODE),
                            'more' => __('more', WCU_LANG_CODE),
                        ),
                        'labeled_before' => '&emsp;'.__('width', WCU_LANG_CODE),
                        'labeled_after' => __('than', WCU_LANG_CODE),
                        'attrs' => 'style="margin:0px 20px; width:80px;"',
                        'data-target-toggle' => '.wcuSwEnable, .wcuSwRotating',
                    ),
                ),
				'show_on_screen_value' => array(
                    'html' => 'input',
					'row_classes' => '',
                    'row_parent' => '',
                    'row_show' => '',
                    'row_hide' => '',
                    'inrow' => 'close',
                    'params' => array(
                        'label_attrs' => 'style="" class="wcuSwitcherInputLabel"',
                        'attrs' => 'class="wcuSwitcherInput"',
                        'labeled' => __('px', WCU_LANG_CODE),
                        'labeled_right' => true,
                    ),
                ),
                'display_by_default' => array(
                    'html' => 'selectbox',
					'row_classes' => '',
                    'row_parent' => '',
                    'row_show' => 'all',
                    'row_hide' => '',
                    'tooltip' => __('Select pages from the list on which you want to display the panel or select "Enable" to display the panel on each page.', WCU_LANG_CODE),
                    'label' => __('Display everywhere', WCU_LANG_CODE),
                    'attrs' => 'style="margin-right: 10px;"',
                    'params' => array(
                        'options' => array(
                            'enable' => __('Enable', WCU_LANG_CODE),
                            'disable' => __('Disable', WCU_LANG_CODE),
                        ),
                    ),
                ),
				'pages_to_show_checkbox' => array(
                    'html' => 'checkboxHiddenVal',
					'row_classes' => 'wcuToShowSelectList wcuShowCheckbox',
                    'row_parent' => '',
                    'row_show' => '',
                    'row_hide' => '',
					'tooltip' => __('Select pages from the list on which you want to hide or display module. If you turn Display everywhere on - module will be hidden on selected pages. If Display everywhere off - module will be displayed only on the selected pages.', WCU_LANG_CODE),
					'inrow' => 'open',
                    'label' => __('Pages', WCU_LANG_CODE),
					'attrs' => 'style="margin-right: 10px;"',
                    'params' => array(
                        'value'=>'1',
                    ),
                ),
				'pages_to_show' => array(
                    'html' => 'selectlist',
					'row_classes' => 'wcuToShowSelectList',
                    'row_parent' => '',
                    'row_show' => '',
                    'row_hide' => '',
					'inrow' => 'close',
                    'label' => __('Pages', WCU_LANG_CODE),
                    'params' => array(
                        'options' => frameWcu::_()->getModule('currency')->getAllPagesListForSelectByType('page'),
                    ),
                ),
				'product_categories_to_show_checkbox' => array(
                    'html' => 'checkboxHiddenVal',
					'row_classes' => 'wcuToShowSelectList wcuShowCheckbox',
                    'row_parent' => '',
                    'row_show' => '',
                    'row_hide' => '',
					'tooltip' => __('Select product categories from the list on which you want to hide or display module. If you turn Display everywhere on - module will be hidden on selected pages. If Display everywhere off - module will be displayed only on the selected pages.', WCU_LANG_CODE),
					'inrow' => 'open',
                    'label' => __('Product categories', WCU_LANG_CODE),
					'attrs' => 'style="margin-right: 10px;"',
                    'params' => array(
                        'value'=>'1',
                    ),
                ),
                'product_categories_to_show' => array(
                    'html' => 'selectlist',
					'row_classes' => 'wcuToShowSelectList',
					'row_parent' => '',
					'row_show' => '',
					'row_hide' => '',
					'inrow' => 'close',
                    'label' => __('Product categories', WCU_LANG_CODE),
                    'params' => array(
                        'options' => frameWcu::_()->getModule('currency')->getAllProductCategories(),
                    ),
                ),
				'custom_post_types_to_show_checkbox' => array(
                    'html' => 'checkboxHiddenVal',
					'row_classes' => 'wcuToShowSelectList wcuShowCheckbox',
                    'row_parent' => '',
                    'row_show' => '',
                    'row_hide' => '',
					'tooltip' => __('Custom post type from the list on which you want to hide or display module. If you turn Display everywhere on - module will be hidden on selected pages. If Display everywhere off - module will be displayed only on the selected pages.', WCU_LANG_CODE),
					'inrow' => 'open',
                    'label' => __('Custom post types', WCU_LANG_CODE),
                    'params' => array(
                        'value'=>'1',
                    ),
                ),
                'custom_post_types_to_show' => array(
                    'html' => 'selectlist',
					'row_classes' => 'wcuToShowSelectList',
					'row_parent' => '',
					'row_show' => '',
                    'row_hide' => '',
					'inrow' => 'close',
                    'label' => __('Custom post types', WCU_LANG_CODE),
                    'params' => array(
                        'options' => frameWcu::_()->getModule('currency')->getAllPostTypes(),
                    ),
                ),
	            'show_popup_message' => $proModule && method_exists(frameWcu::_()->getModule('options_pro'), 'getProShowPopupSwitcherMessage') ? frameWcu::_()->getModule('options_pro')->getProShowPopupSwitcherMessage() : array(
		            'html' => 'checkboxHiddenVal',
		            'row_classes' => '',
		            'row_show' => '',
		            'row_hide' => '',
		            'tooltip' => __('Show popup message "Do you want change currency?". Read more in the <a href="https://woobewoo.com/documentation/currency-switcher-mode/">documentation</a>.', WCU_LANG_CODE),
		            'label' => __('Show popup message for switcher', WCU_LANG_CODE) . ' <sup>PRO</sup>' . ($proModule ? (method_exists(frameWcu::_()->getModule('options_pro'), 'getProShowPopupSwitcherMessage') ? '' : __(' (since version 1.4.0)')) : ''),
		            'params' => array(
			            'value'=>'1',
			            'attrs' => 'disabled',
		            ),
	            ),
				'switcher_shortcode_frontend' => array(
                    'html' => 'block',
					'row_classes' => 'wcuToShowSelectList wcuShowCheckbox',
                    'row_parent' => '',
                    'row_show' => '',
                    'row_hide' => '',
					'tooltip' => __('The shortcode can take parameters, read more in the documentation.', WCU_LANG_CODE),
                    'label' => __('Shortcode', WCU_LANG_CODE),
                ),
				'switcher_shortcode_frontend_php' => array(
                    'html' => 'block',
					'row_classes' => 'wcuToShowSelectList wcuShowCheckbox',
                    'row_parent' => '',
                    'row_show' => '',
                    'row_hide' => '',
					'tooltip' => __('The shortcode can take parameters, read more in the documentation.', WCU_LANG_CODE),
                    'label' => __('PHP Shortcode', WCU_LANG_CODE),
                ),
                'switcher_shortcode_frontend_extended' => array(
                    'html' => 'block',
					'row_classes' => 'wcuToShowSelectList wcuShowCheckbox',
                    'row_parent' => '',
                    'row_show' => '',
                    'row_hide' => '',
					'tooltip' => __('<p>The shortcode can take parameters, read more in the documentation</p>. <img src="' . WCU_IMG_PATH . 'switcher-extended.png" />', WCU_LANG_CODE),
                    'label' => __('Shortcode extended <sup>PRO</sup>', WCU_LANG_CODE),
                ),
				'switcher_shortcode_frontend_extended_php' => array(
                    'html' => 'block',
					'row_classes' => 'wcuToShowSelectList wcuShowCheckbox',
                    'row_parent' => '',
                    'row_show' => '',
                    'row_hide' => '',
					'tooltip' => __('<p>The shortcode can take parameters, read more in the documentation.</p> <img src="' . WCU_IMG_PATH . 'switcher-extended.png"/>', WCU_LANG_CODE),
                    'label' => __('PHP Shortcode extended <sup>PRO</sup>', WCU_LANG_CODE),
                ),
                'switcher_shortcode' => array(
                    'html' => 'block',
                    'row_classes' => 'wcuToShowSelectList wcuShowCheckbox',
                    'row_parent' => '',
                    'row_show' => '',
                    'row_hide' => '',
                    'tooltip' => __('Display frontend switcher with view like widget analog. The shortcode can take parameters, read more in the documentation.', WCU_LANG_CODE),
                    'label' => __('Shortcode (widget verstion)', WCU_LANG_CODE),
                ),
                'switcher_shortcode_php' => array(
                    'html' => 'block',
                    'row_classes' => 'wcuToShowSelectList wcuShowCheckbox',
                    'row_parent' => '',
                    'row_show' => '',
                    'row_hide' => '',
                    'tooltip' => __('The shortcode can take parameters, read more in the documentation.', WCU_LANG_CODE),
                    'label' => __('PHP Shortcode (widget version)', WCU_LANG_CODE),
                ),
            ),
        );
    }

    public function getCurrencyModule() {
        return frameWcu::_()->getModule('currency');
    }
}
