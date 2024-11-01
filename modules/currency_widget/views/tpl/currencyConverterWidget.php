<?php
	$btn_bg_color = isset($this->instance['btn_bg_color']) ? $this->instance['btn_bg_color'] : '#fafafa';
	$btn_bg_color_h = isset($this->instance['btn_bg_color_h']) ? $this->instance['btn_bg_color_h'] : '#fafafa';
	$btn_txt_color = isset($this->instance['btn_txt_color']) ? $this->instance['btn_txt_color'] : 'black';
	$layout = isset($this->instance['layout']) ? $this->instance['layout'] : 'horizontal';
?>

<style>
	#wcuCurrencyConverterWidget_<?php echo $this->randId?>.wcuCurrencyConverterWidget .wcuExchangeIcon {
		color: <?php echo $btn_bg_color ?>;
	}
	#wcuCurrencyConverterWidget_<?php echo $this->randId?>.wcuCurrencyConverterWidget .wcuExchangeIcon:hover {
		color: <?php echo $btn_bg_color_h ?>;
	}
	#wcuCurrencyConverterWidget_<?php echo $this->randId?>.wcuCurrencyConverterWidget .wcuCurrencyConvertBtn {
		background: <?php echo $btn_bg_color ?>;
		color: <?php echo $btn_txt_color ?>;
	}
	#wcuCurrencyConverterWidget_<?php echo $this->randId?>.wcuCurrencyConverterWidget .wcuCurrencyConvertBtn:hover {
		background: <?php echo $btn_bg_color_h ?>;
	}
	#wcuCurrencyConverterWidget_<?php echo $this->randId?> .fnone {
		display:inline-block;
		position:relative;
		float:left !important;
		line-height: 21px;
	}
	#wcuCurrencyConverterWidget_<?php echo $this->randId?> .ddlabel {
		display:inline-block;
		line-height: 21px;
	}
	<?php
		$show_on_widths = isset($this->instance['show_on_widths']) ? $this->instance['show_on_widths'] : false;
		$show_on_screen_compare = isset($this->instance['show_on_screen_compare']) ? $this->instance['show_on_screen_compare'] : false;
		$show_on_screen_value = isset($this->instance['show_on_widths_value']) ? $this->instance['show_on_widths_value'] : false;
	?>
	<?php if ($show_on_widths && $show_on_screen_compare && $show_on_screen_value) { ?>
		<?php if ($show_on_screen_compare === 'less') {
	        $show_on_screen_compare = 'max-width';
	    } else {
	        $show_on_screen_compare = 'min-width';
	    } ?>
		#wcuCurrencyConverterWidget_<?php echo $this->randId?> {
			display:none !important;
		}
		@media (<?php echo $show_on_screen_compare ?>:<?php echo $show_on_screen_value?>px) {
			#wcuCurrencyConverterWidget_<?php echo $this->randId?> {
				display:block !important;
			}
		}
	<?php } ?>
</style>

<?php
$styles['width'] = !empty($this->instance['width']) ? 'width: ' . $this->instance['width'] . ';' : 'width: 100%;';

if (!$this->moduleFlags) {
	$this->instance['layout'] = 'vertical';
	$this->instance['show_flag_dropdown'] = false;
}
?>

<div id="wcuCurrencyConverterWidget_<?php echo $this->randId?>" class="wcuCurrencyConverterWidget wcuCurrencyConverterWidget_<?php echo $layout?>" style="<?php echo $styles['width'];?>">

	<?php if(!empty($this->instance['title'])) {?>
		<div style="margin-bottom: 10px;"><?php echo $this->instance['title']?></div>
	<?php }?>

	<div class="wcuCurrencyConverterShell">

		<?php if ( !empty($this->instance['layout']) && ( $this->instance['layout'] === 'vertical' ) ) {?>

		<div class="wcuCol-md-12 wcuCol-xs-12 wcuMargin">
				<?php echo htmlWcu::input("amount", array(
					'type' => 'text',
					'value' => 1,
					'attrs' => 'placeholder="results" style="width:100%"',
				))?>
				<?php echo htmlWcu::hidden("precision", array(
					'value' => 4,
				))?>
		</div>
		<div class="wcuCol-md-5 wcuCol-xs-12 wcuMargin">
			<?php if ($this->showFlags && isset($this->instance['show_flag_dropdown']) && $this->instance['show_flag_dropdown'] !== 'false') {?>
				<select name="currency_from" class="wcuFlagsSelectBoxDropdown">
					<?php foreach ($this->currenciesOpts as $key => $currency) {?>
							<?php $current = $key == $this->currentCurrency ? 'selected' : ''; ?>
							<option <?php echo $current?> value="<?php echo $key?>" title="<?php echo $currency['flag']?>"><?php echo $currency['name']?></option>
					<?php }?>
				</select>
			<?php } else {?>
				<select name="currency_from">
					<?php foreach ($this->currenciesOpts as $key => $currency) {?>
							<?php $current = $key == $this->currentCurrency ? 'selected' : ''; ?>
							<option <?php echo $current?> value="<?php echo $key?>"><?php echo $currency['name']?></option>
					<?php }?>
				</select>
			<?php }?>
		</div>

		<div class="wcuCol-md-2 wcuCol-xs-12 wcuMargin">
			<div class="wcuExchangeIcon" style="text-align: center;"><i class="fa fa-long-arrow-right" aria-hidden="true"></i></div>
		</div>

		<div class="wcuCol-md-5 wcuCol-xs-12 wcuMargin">
			<?php if ($this->showFlags && isset($this->instance['show_flag_dropdown']) && $this->instance['show_flag_dropdown'] !== 'false') {?>
				<select name="currency_to" class="wcuFlagsSelectBoxDropdown">
					<?php foreach ($this->currenciesOpts as $key => $currency) {?>
							<?php $current = $key == $this->currentCurrency ? 'selected' : ''; ?>
							<option <?php echo $current?> value="<?php echo $key?>" title="<?php echo $currency['flag']?>"><?php echo $currency['name']?></option>
					<?php }?>
				</select>
			<?php } else {?>
				<select name="currency_to">
					<?php foreach ($this->currenciesOpts as $key => $currency) {?>
							<?php $current = $key == $this->currentCurrency ? 'selected' : ''; ?>
							<option <?php echo $current?> value="<?php echo $key?>"><?php echo $currency['name']?></option>
					<?php }?>
				</select>
			<?php }?>
		</div>

		<div class="wcuCol-md-12 wcuCol-xs-12 wcuMargin">
			<?php echo htmlWcu::input("result", array(
				'type' => 'text',
				'value' => '',
				'attrs' => 'placeholder="results" readonly="" class="wcuCol-md-12 wcuCol-xs-12 wcuMargin"',
			))?>
		</div>

		<script type="text/javascript">
			jQuery(".wcuCurrencyConverterWidget .wcuExchangeIcon").click(function(){
				jQuery(this).toggleClass("wcuRotateAnimation");
				var parent = jQuery(this).closest(".wcuCurrencyConverterWidget");
				var from = parent.find('[name="currency_from"]');
				var to = parent.find('[name="currency_to"]');
				from = from.attr("name","currency_to");
				to = to.attr("name","currency_from");
			});
		</script>

	<?php } elseif ( $this->moduleFlags && !empty($this->instance['layout']) && ( $this->instance['layout'] === 'horizontal' ) )  { ?>


		<div class="wcuCol-md-5 wcuCol-xs-12 wcuMargin">

			<?php echo htmlWcu::input("amount", array(
				'type' => 'text',
				'value' => 1,
				'attrs' => 'style="width:100%;"',
			))?>
			<?php echo htmlWcu::hidden("precision", array(
				'value' => 4,
			))?>

			<?php if ($this->showFlags && isset($this->instance['show_flag_dropdown']) && $this->instance['show_flag_dropdown'] !== 'false') {?>
				<select name="currency_from" class="wcuFlagsSelectBoxDropdown" style="margin-top:3px;">
					<?php foreach ($this->currenciesOpts as $key => $currency) {?>
							<?php $current = $key == $this->currentCurrency ? 'selected' : ''; ?>
							<option <?php echo $current?> value="<?php echo $key?>" title="<?php echo $currency['flag']?>"><?php echo $currency['name']?></option>
					<?php }?>
				</select>
			<?php } else {?>
				<select name="currency_from" style="margin-top:3px;">
					<?php foreach ($this->currenciesOpts as $key => $currency) {?>
							<?php $current = $key == $this->currentCurrency ? 'selected' : ''; ?>
							<option <?php echo $current?> value="<?php echo $key?>"><?php echo $currency['name']?></option>
					<?php }?>
				</select>
			<?php }?>
		</div>

		<div class="wcuCol-md-2 wcuCol-xs-12 wcuMargin">
			<div class="wcuExchangeIcon wcuExchangeIconHorizontal" style="text-align: center;"><i class="fa fa-long-arrow-right" aria-hidden="true"></i></div>
		</div>

		<div class="wcuCol-md-5 wcuCol-xs-12 wcuMargin">

			<?php echo htmlWcu::input("result", array(
					'type' => 'text',
					'value' => 1,
					'attrs' => 'style="width:100%;"',
			))?>

			<?php if ($this->showFlags && isset($this->instance['show_flag_dropdown']) && $this->instance['show_flag_dropdown'] !== 'false') {?>
				<select name="currency_to" class="wcuFlagsSelectBoxDropdown" style="margin-top:3px;">
					<?php foreach ($this->currenciesOpts as $key => $currency) {?>
							<?php $current = $key == $this->currentCurrency ? 'selected' : ''; ?>
							<option <?php echo $current?> value="<?php echo $key?>" title="<?php echo $currency['flag']?>"><?php echo $currency['name']?></option>
					<?php }?>
				</select>
			<?php } else {?>
				<select name="currency_to" style="margin-top:3px;">
					<?php foreach ($this->currenciesOpts as $key => $currency) {?>
							<?php $current = $key == $this->currentCurrency ? 'selected' : ''; ?>
							<option <?php echo $current?> value="<?php echo $key?>"><?php echo $currency['name']?></option>
					<?php }?>
				</select>
			<?php }?>
		</div>

		<script type="text/javascript">
			jQuery(".wcuCurrencyConverterWidget .wcuExchangeIcon").click(function(){
				jQuery(this).toggleClass("wcuRotateAnimation");
				var parent = jQuery(this).closest(".wcuCurrencyConverterWidget");
				var from = parent.find('[name="currency_from"]');
				var to = parent.find('[name="currency_to"]');
				from = from.attr("name","currency_to");
				to = to.attr("name","currency_from");
			});
			jQuery(".wcuCurrencyConverterWidget .wcuExchangeIconHorizontal").click(function(){
				jQuery(this).toggleClass("wcuRotateAnimation");
				var parent = jQuery(this).closest(".wcuCurrencyConverterWidget");
				var from = parent.find('[name="amount"]');
				var to = parent.find('[name="result"]');
				from = from.attr("name","result");
				to = to.attr("name","amount");
			});
		</script>

	<?php }?>

		<div class="wcuCol-md-12 wcuCol-xs-12 wcuMargin">
			<?php echo htmlWcu::button(array(
				'value' => __('Convert', WCU_LANG_CODE),
				'attrs' => 'class="wcuCurrencyConvertBtn wcuCol-md-12 wcuCol-xs-12 wcuMargin" onclick="getCurrencyRate(this); return false;"',
			))?>
		</div>

	</div>

	<div style="clear: both;"></div>
</div>

<script type="text/javascript">

	jQuery('#wcuCurrencyConverterWidget_<?php echo $this->randId?> select[name=currency_to] option:eq(1)').attr('selected', 'selected');

	<?php if ($this->showFlags) {?>
		jQuery(document).ready(function(){
			jQuery("#wcuCurrencyConverterWidget_<?php echo $this->randId?> .wcuFlagsSelectBoxDropdown").msDropDown();
			oHandler = jQuery("#wcuCurrencyConverterWidget_<?php echo $this->randId?> [name='currency_to'].wcuFlagsSelectBoxDropdown").data('dd');
			if (oHandler) {
				oHandler.set("selectedIndex", 1);
			}
		});
	<?php }?>

	function getCurrencyRate(btn) {
		var shell = jQuery(btn).parents('.wcuCurrencyConverterShell:first');
		jQuery.sendFormWcu({
			data: {
				mod: 'currency_widget',
				action: 'getCurrencyRate',
				amount: shell.find('input[name="amount"]').val(),
				currency_from: shell.find('[name="currency_from"]').val(),
				currency_to: shell.find('[name="currency_to"]').val()
			},
			onSuccess: function(res) {
				if(!res.error) {
					shell.find('[name="result"]').val(res.data.result);
				}
			}
		});
	}
</script>
