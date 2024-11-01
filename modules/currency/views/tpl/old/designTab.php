<p><?php _e('Settings for Currency Switcher for change currency on frontend.', WCU_LANG_CODE)?></p>
<table class="form-table">
	<tbody>
	<?php foreach($this->optionsParams as $index => $optBlock) {?>
		<?php if($index != 'switcher') continue;?>
		<?php foreach($optBlock as $key => $opt) {?>
			<?php
			$display = !empty($opt['row_parent']) ? $this->isDislayElem($optBlock[$opt['row_parent']]['params']['value'], $opt) : '';
			?>
			<tr valign="top" class="single_select_page <?php echo $opt['row_classes']?>" data-target-show="<?php echo $opt['row_show']?>" data-target-hide="<?php echo $opt['row_hide']?>" data-hide-with-all="<?php echo !empty($opt['row_hide_with_all'])?>" style="<?php echo $display?>">
				<th scope="row" class="titledesc">
					<label for=""><?php echo $opt['label']?></label>
				</th>
				<td class="forminp">
					<?php $htmlField = $opt['html'];?>
					<?php echo htmlWcu::$htmlField("{$this->getModule()->optionsDbOpt}[{$index}][{$key}]", $opt['params'])?>
				</td>
			</tr>
		<?php }?>
	<?php }?>
	</tbody>
</table>