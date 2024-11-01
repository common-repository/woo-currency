<div class="wcuAdminFooterShell">
	<div class="wcuAdminFooterCell">
		<?php echo WCU_WP_PLUGIN_NAME?>
		<?php _e('Version', WCU_LANG_CODE)?>:
		<a target="_blank" href="http://wordpress.org/plugins/<?php echo $this->pluginSlug?>/changelog/"><?php echo WCU_VERSION?></a>
	</div>
	<div class="wcuAdminFooterCell">|</div>
	<?php  if(!frameWcu::_()->getModule(implode('', array('l','ic','e','ns','e')))) {?>
	<div class="wcuAdminFooterCell">
		<?php _e('Go', WCU_LANG_CODE)?>&nbsp;<a target="_blank" href="<?php echo $this->getModule()->getMainLink();?>"><?php _e('PRO', WCU_LANG_CODE)?></a>
	</div>
	<div class="wcuAdminFooterCell">|</div>
	<?php } ?>
	<div class="wcuAdminFooterCell">
		<a target="_blank" href="http://wordpress.org/support/plugin/<?php echo $this->pluginSlug?>"><?php _e('Support', WCU_LANG_CODE)?></a>
	</div>
	<div class="wcuAdminFooterCell">|</div>
	<div class="wcuAdminFooterCell">
		Add your <a target="_blank" href="http://wordpress.org/support/view/plugin-reviews/<?php echo $this->pluginSlug?>?filter=5#postform">&#9733;&#9733;&#9733;&#9733;&#9733;</a> on wordpress.org.
	</div>
</div>