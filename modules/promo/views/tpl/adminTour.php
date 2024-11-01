<div id="woobewoo-admin-tour" class="">
	<div id="woobewoo-welcome-first_welcome">
		<div class="woobewoo-tour-content">
			<h3><?php printf(__('Welcome to %s plugin!', WCU_LANG_CODE), WCU_WP_PLUGIN_NAME)?></h3>
			<p><?php printf(__('Thank you for choosing our %s plugin. Just click here to start using it - and we will show you it\'s possibilities and powerfull features.', WCU_LANG_CODE), WCU_WP_PLUGIN_NAME)?></p>
		</div>
		<div class="woobewoo-tour-btns">
			<a href="#" class="close"><?php _e('Close', WCU_LANG_CODE)?></a>
			<a href="<?php echo frameWcu::_()->getModule('options')->getTabUrl();?>" class="button button-primary woobewoo-tour-next-btn"><?php _e('Next', WCU_LANG_CODE)?></a>
		</div>
	</div>
</div>