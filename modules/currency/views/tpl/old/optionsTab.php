<table class="form-table">
	<tbody>
	<?php foreach($this->optionsParams as $index => $optBlock) {?>
		<?php if($index != 'options') continue;?>
		<?php foreach($optBlock as $key => $opt) {?>
			<?php
			$display = !empty($opt['row_parent']) ? $this->isDislayElem($optBlock[$opt['row_parent']]['params']['value'], $opt) : '';
			?>
			<tr valign="top" class="single_select_page <?php echo $opt['row_classes']?>" data-target-show="<?php echo $opt['row_show']?>" style="<?php echo $display?>">
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