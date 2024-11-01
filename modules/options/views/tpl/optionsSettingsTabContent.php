<section class="woobewoo-bar">
	<ul class="woobewoo-bar-controls">
		<li title="<?php _e('Save all options')?>">
			<button class="button button-primary" id="wcuSettingsSaveBtn" data-toolbar-button>
				<i class="fa fa-fw fa-save"></i>
				<?php _e('Save', WCU_LANG_CODE)?>
			</button>
		</li>
	</ul>
	<div style="clear: both;"></div>
	<hr />
</section>
<section>
	<form id="wcuSettingsForm" class="wcuInputsWithDescrForm">
		<div class="woobewoo-item woobewoo-panel">
			<div id="containerWrapper">
				<a href="<?php echo frameWcu::_()->getModule('currency')->getCurrencyTabUrl()?>" class="button button-primary"><?php _e('Go to Woo Currency Settings')?></a><br />
				<span class="description"><?php _e("To start work with Woo Currencies - go to it's section in your Woo Store")?></span>
				<table class="form-table">
					<?php foreach($this->options as $optCatKey => $optCatData) { ?>
						<?php if(isset($optCatData['opts']) && !empty($optCatData['opts'])) { ?>
							<?php foreach($optCatData['opts'] as $optKey => $opt) { ?>
								<?php
									$htmlType = isset($opt['html']) ? $opt['html'] : false;
									if(empty($htmlType)) continue;
									$opt['attrs'] = isset($opt['attrs']) && !empty($opt['attrs']) ? $opt['attrs'] : '';
									$htmlOpts = array('value' => $opt['value'], 'attrs' => 'data-optkey="'. $optKey. '" ' . $opt['attrs']);
									if(in_array($htmlType, array('selectbox', 'selectlist')) && isset($opt['options'])) {
										if(is_callable($opt['options'])) {
											$htmlOpts['options'] = call_user_func( $opt['options'] );
										} elseif(is_array($opt['options'])) {
											$htmlOpts['options'] = $opt['options'];
										}
									}
									if(isset($opt['pro']) && !empty($opt['pro'])) {
										$htmlOpts['attrs'] .= ' class="wcuProOpt"';
									}
								?>
								<tr
									<?php if(isset($opt['connect']) && $opt['connect']) { ?>
										data-connect="<?php echo $opt['connect'];?>" style="display: none;"
									<?php }?>
								>
									<th scope="row" class="col-w-30perc">
										<?php echo $opt['label']?>
										<?php if(!empty($opt['changed_on'])) {?>
											<br />
											<span class="description">
												<?php
												$opt['value']
													? printf(__('Turned On %s', WCU_LANG_CODE), dateWcu::_($opt['changed_on']))
													: printf(__('Turned Off %s', WCU_LANG_CODE), dateWcu::_($opt['changed_on']))
												?>
											</span>
										<?php }?>
										<?php if(isset($opt['pro']) && !empty($opt['pro'])) { ?>
											<span class="wcuProOptMiniLabel">
												<a href="<?php echo $opt['pro']?>" target="_blank">
													<?php _e('PRO option', WCU_LANG_CODE)?>
												</a>
											</span>
										<?php }?>
									</th>
									<td class="col-w-1perc">
										<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_html($opt['desc'])?>"></i>
									</td>
									<td class="col-w-1perc">
										<?php echo htmlWcu::$htmlType('opt_values['. $optKey. ']', $htmlOpts)?>
									</td>
									<td class="col-w-60perc">
										<div id="wcuFormOptDetails_<?php echo $optKey?>" class="wcuOptDetailsShell">
										<?php switch($optKey) {

										}?>
										<?php
											if(isset($opt['add_sub_opts']) && !empty($opt['add_sub_opts'])) {
												if(is_string($opt['add_sub_opts'])) {
													echo $opt['add_sub_opts'];
												} elseif(is_callable($opt['add_sub_opts'])) {
													echo call_user_func_array($opt['add_sub_opts'], array($this->options));
												}
											}
										?>
										</div>
									</td>
								</tr>
							<?php }?>
						<?php }?>
					<?php }?>
				</table>
				<div style="clear: both;"></div>
			</div>
		</div>
		<?php echo htmlWcu::hidden('mod', array('value' => 'options'))?>
		<?php echo htmlWcu::hidden('action', array('value' => 'saveGroup'))?>
	</form>
</section>
