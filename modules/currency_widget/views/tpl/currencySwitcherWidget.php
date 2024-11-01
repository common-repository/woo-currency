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
		#wcuCurrencySwitcherWidget_<?php echo $this->randId; ?> {
			display:none !important;
		}
		@media (<?php echo $show_on_screen_compare ?>:<?php echo $show_on_screen_value?>px) {
			#wcuCurrencySwitcherWidget_<?php echo $this->randId; ?>  {
				display:block !important;
			}
		}
	<?php } ?>
</style>

<?php
$formStyles = 'margin-bottom: 20px;';
$formStyles .= !empty($this->instance['width']) ? 'width: ' . $this->instance['width'] . ';' : 'width: 100%;';

if (!$this->moduleFlags) {
	$this->instance['show_flag_dropdown'] = false;
	$this->instance['show_as'] = 'dropdown';
}

?>



<div id="wcuCurrencySwitcherWidget_<?php echo $this->randId?>" class="wcuCurrencySwitcherWidget" style="<?php echo $formStyles?>">

		<?php if ( !empty($this->instance['title']) ) {?>
			<div style="margin-bottom: 10px;"><?php echo $this->instance['title']?></div>
		<?php }?>

		<?php if ($this->showFlags && ( (!empty($this->instance['show_flag_dropdown']) && $this->instance['show_flag_dropdown'] != 'false') || (isset($this->instance['show_as'] ) && ($this->instance['show_as'] === 'flags') ) ) )  {?>

				<?php if ( isset($this->instance['show_as'] ) && ($this->instance['show_as'] === 'dropdown') && isset($this->instance['show_flag_dropdown']) ) {?>

						<div class="wcuFlagsSelectBoxDropdownWrapper">
							<select name="wcu_currency_switcher" class="wcuFlagsSelectBoxDropdown">
								<?php foreach ($this->currenciesOpts as $key => $currency) {?>
										<?php $current = $key == $this->currentCurrency ? 'selected' : ''; ?>
										<option <?php echo $current; ?> value="<?php echo $key; ?>" title="<?php echo $currency['flag']; ?>"><?php echo $currency['name']?></option>
								<?php }?>
							</select>
						</div>

				<?php } else {?>

						<?php foreach ($this->currenciesOpts as $key => $currency) { ?>
							<?php $current = $key == $this->currentCurrency ? 'wcuCurrent' : ''; ?>
								<?php if (!empty($currency['flag'])) {?>
									<img class="wcuCurrencySwitcherWidgetShowAsFlag <?php echo $current?>" data-currency="<?php echo $key;?>" src="<?php echo $currency['flag'];?>">
								<?php } else {?>
									<span class="wcuCurrencySwitcherWidgetShowAsFlag <?php echo $current?>" data-currency="<?php echo $key;?>"><?php echo $key;?></span>
								<?php } ?>
						<?php } ?>

				<?php }?>

		<?php } else { ?>

			<select name="wcu_currency_switcher">
				<?php foreach ($this->currenciesOpts as $key => $currency) {?>
						<?php $current = $key == $this->currentCurrency ? 'selected' : ''; ?>
						<option <?php echo $current?> value="<?php echo $key; ?>"><?php echo $currency['name']; ?></option>
				<?php }?>
			</select>

		<?php }?>

</div>


<script type="text/javascript">
	    if (window.jQuery) {
			jQuery(document).ready(function() {

				<?php if ($this->showFlags && !empty($this->instance['show_flag_dropdown']) && $this->instance['show_flag_dropdown'] != 'false') {?>
				debugger;
					var wcuGlobalFlagsDD = window.wcuGlobalFlagsDD || 0,
						wcuDDList = window.wcuDDList || [];
					jQuery(".wcuFlagsSelectBoxDropdown").each(function() {
						var $this = jQuery(this);
						if (!$this.attr('id') && $this.closest('.ddOutOfVision').length == 0) {
							wcuGlobalFlagsDD++;
							var id = 'wcuFlagsSelectBoxDropdown_'+wcuGlobalFlagsDD;
							$this.attr('id', id);
							wcuDDList[id] = jQuery("#"+id).msDropDown().data('dd');
						}
					});
					window.wcuGlobalFlagsDD = wcuGlobalFlagsDD;
					window.wcuDDList = wcuDDList;
					//jQuery(".wcuFlagsSelectBoxDropdown").msDropDown();
				<?php }?>

				if (!window.wcuWidgetInit) {

					var select = jQuery('.wcuCurrencySwitcherWidget [name="wcu_currency_switcher"]');
					<?php if (!$this->showFlags) {?>
						select.chosen({disable_search: true});
					<?php }?>

					select.on('change', function() {
						wcuUpdateUrlParam('currency', jQuery(this).val());
					});

					jQuery(".wcuCurrencySwitcherWidgetShowAsFlag").click(function(){
						wcuUpdateUrlParam('currency', jQuery(this).attr('data-currency'));
					});
					jQuery( window ).on('resize', function() {
						for(var id in window.wcuDDList) {
							window.wcuDDList[id].destroy();
							wcuDDList[id] = jQuery("#"+id).msDropDown().data('dd');
						}
					});
					window.wcuWidgetInit = true;
				}
			});
		}
</script>
