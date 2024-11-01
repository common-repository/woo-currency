<style>
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
		#wcuCurrencyRatesWidget_<?php echo $this->randId?> {
			display:none !important;
		}
		@media (<?php echo $show_on_screen_compare ?>:<?php echo $show_on_screen_value?>px) {
			#wcuCurrencyRatesWidget_<?php echo $this->randId?> {
				display:block !important;
			}
		}
	<?php } ?>
</style>

<?php
$excluded = !empty($this->instance['exclude']) ? $this->instance['exclude'] : '';
$styles['width'] = !empty($this->instance['width']) ? 'width: ' . $this->instance['width'] . ';' : 'width: 100%;';

if (!$this->moduleFlags) {
	$this->instance['show_flag_dropdown'] = false;
	$this->instance['show_flag_currency_list'] = false;
}
?>
<?php $excluded = !empty($excluded) ? implode(',', $excluded) : ''; ?>
<div id="wcuCurrencyRatesWidget_<?php echo $this->randId?>" class="wcuCurrencyRatesWidget" data-exclude="<?php echo $excluded; ?>" style="margin-bottom: 20px; <?php echo $styles['width'];?>">

	<?php if(!empty($this->instance['title'])) {?>
		<div style="margin-bottom: 10px;"><?php echo $this->instance['title']?></div>
	<?php }?>

	<?php if ($this->showFlags && !empty($this->instance['show_flag_dropdown']) && $this->instance['show_flag_dropdown'] != 'false') {?>
		<div class="wcuFlagsSelectBoxDropdownWrapper">
			<select name="wcu_currency_item" class="wcuFlagsSelectBoxDropdown">
				<?php foreach ($this->currenciesOpts as $key => $currency) {?>
						<?php $current = $key == $this->currentCurrency ? 'selected' : ''; ?>
						<option <?php echo $current?> value="<?php echo $key?>" title="<?php echo $currency['flag']?>"><?php echo $currency['name']?></option>
				<?php }?>
			</select>
		</div>
	<?php } else {?>
		<select name="wcu_currency_item">
				<?php foreach ($this->currenciesOpts as $key => $currency) {?>
						<?php $current = $key == $this->currentCurrency ? 'selected' : ''; ?>
						<option <?php echo $current?> value="<?php echo $key?>" ><?php echo $currency['name']?></option>
				<?php }?>
		</select>
	<?php }?>

	<table class="wcuCurrencyRatesList">
		<?php foreach($this->currenciesOpts as $key => $item) {?>
			<?php $display = $key == $this->currentCurrency ? 'display: none;' : ''?>
				<tr data-currency="<?php echo $key?>" style="<?php echo $display?>">
					<?php if ($this->showFlags && !empty($this->instance['show_flag_currency_list']) && $this->instance['show_flag_currency_list'] != 'false' ) {?>
						<?php if (!empty($item['flag'])) {?>
							<td><img src="<?php echo $item['flag'] ?>"></td>
						<?php }?>
						<?php $wcuCurrencyRatesFlag = 'wcuCurrencyRatesListRateFlag'; ?>
					<?php }?>
					<td><?php echo $key?>:</td>
					<td class="<?php if ($this->showFlags && !empty($wcuCurrencyRatesFlag)) echo $wcuCurrencyRatesFlag;?> wcuCurrencyRatesListRate"><div class="wcuCurrencyRateVal"></div></td>
				</tr>
		<?php }?>
	</table>

</div>

<script type="text/javascript">
	jQuery(document).ready(function() {

		<?php if ($this->showFlags) {?>
			jQuery(".wcuFlagsSelectBoxDropdown").msDropDown();
		<?php }?>

		var select = jQuery('.wcuCurrencyRatesWidget [name="wcu_currency_item"]'),
			list = jQuery('.wcuCurrencyRatesList tr');

		select.on('change', function() {
			var shell = jQuery(this).closest('.wcuCurrencyRatesWidget'),
				current = shell.find('[name="wcu_currency_item"]').val();

			list.find('.wcuCurrencyRateVal').html('loading...');
			jQuery.sendFormWcu({
				data: {
					mod: 'currency_widget',
					action: 'getCurrencyRatesList',
					current: current,
					exclude: shell.data('exclude')
				},
				onSuccess: function(res) {
					if(!res.error) {
						list.each(function() {
							var self = jQuery(this);

							if(self.data('currency') == current) {
								self.hide();
							} else {
								self.show();
							}
							self.find('.wcuCurrencyRateVal').html(res.data.rates[self.data('currency')]);
						});
					}
				}
			});
		});
		select.trigger('change');
	});
</script>
