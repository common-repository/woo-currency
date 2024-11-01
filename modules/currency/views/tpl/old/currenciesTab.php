<?php echo htmlWcu::button(array('attrs' => 'class="wcuAddCurrency"', 'value' => __('Add currency', WCU_LANG_CODE)))?>
<div class="wcuCurrenciesList">
	<div class="wcuCurrencyHeader row">
		<div class="col-md-1"></div>
		<div class="col-md-1"><?php _e('Name', WCU_LANG_CODE) ?></div>
		<div class="col-md-1"><?php _e('Title', WCU_LANG_CODE) ?></div>
		<div class="col-md-1"><?php _e('Symbol', WCU_LANG_CODE) ?></div>
		<div class="col-md-1"><?php _e('Symbol', WCU_LANG_CODE) ?></div>
		<div class="col-md-1"><?php _e('Position', WCU_LANG_CODE) ?></div>
		<div class="col-md-2"><?php _e('Actions', WCU_LANG_CODE) ?></div>
		<div style="clear: both;"></div>
	</div>

	<?php foreach($this->currencies as $params) {?>
		<?php
		$example = empty($params) ? 'wcuCurrencyItemExample' : '';
		$display = empty($params) ? 'display: none;' : '';
		$disabled = empty($params) ? 'disabled="disabled" ' : '';
		?>
		<div class="<?php echo $example ?> wcuCurrencyItem row" <?php echo $example ? 'style="display: none;"' : '' ?>>
			<div class="col-md-1">
				<?php echo htmlWcu::button(array(
					'value' => !empty($params['etalon']) ? __('Main Currency', WCU_LANG_CODE) : __('Set as Main', WCU_LANG_CODE),
					'attrs' => $disabled.' class="wcuCurrencyEtalon" data-main="'.__('Main Currency', WCU_LANG_CODE).'" data-def="'.__('Set as Main', WCU_LANG_CODE).'"',
				))?>
				<?php echo htmlWcu::hidden("{$this->dbPrefix}[etalon][]", array(
					'value' => !empty($params['etalon']) ? $params['etalon'] : 0,
					'attrs' => $disabled . 'class="wcuIsEtalon"'
				))?>
			</div>
			<div class="col-md-1">
				<?php echo htmlWcu::selectbox("{$this->dbPrefix}[name][]", array(
					'value' => !empty($params['name']) ? $params['name'] : $this->defCur,
					'options' => $this->getModule()->getCurrencyNames(),
					'attrs' => $disabled . '',
					'data-def' => $this->defCur,
				))?>
			</div>
			<div class="col-md-1">
				<?php echo htmlWcu::input("{$this->dbPrefix}[title][]", array(
					'type' => 'text',
					'value' => !empty($params['title']) ? $params['title'] : $this->defCur,
					'attrs' => $disabled . '',
				))?>
			</div>
			<div class="col-md-1">
				<?php echo htmlWcu::selectbox("{$this->dbPrefix}[symbol][]", array(
					'value' => !empty($params['symbol']) ? $params['symbol'] : $this->getModel()->getCurrencySymbol($this->defCur),
					'options' => $this->getModule()->getCurrencySymbolsList(),
					'attrs' => $disabled . ''
				))?>
			</div>
			<div class="col-md-1">
				<?php echo htmlWcu::selectbox("{$this->dbPrefix}[position][]", array(
					'value' => !empty($params['position']) ? $params['position'] : $this->defPos,
					'options' => $this->getModule()->getCurrencyPositions(),
					'attrs' => $disabled . '',
					'data-def' => $this->defPos,
				))?>
			</div>
			<div class="col-md-1">
				<?php echo htmlWcu::input("{$this->dbPrefix}[rate][]", array(
					'type' => 'text',
					'value' => !empty($params['rate']) ? $params['rate'] : 1,
					'attrs' => $disabled . 'class="wcuRate"',
				))?>
			</div>
			<div class="col-md-2">
				<?php echo htmlWcu::button(array(
					'value' => __('Get rate', WCU_LANG_CODE),
					'attrs' => $disabled . 'class="wcuCurrencyConvert"',
				))?>
				<?php echo htmlWcu::button(array(
					'value' => __('Remove', WCU_LANG_CODE),
					'attrs' => $disabled . 'class="wcuCurrencyRemove"',
				))?>
			</div>
			<div style="clear: both;"></div>
		</div>
	<?php }?>
</div>
