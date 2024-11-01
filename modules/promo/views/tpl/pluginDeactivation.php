<style type="text/css">
	.wcuDeactivateDescShell {
		display: none;
		margin-left: 25px;
		margin-top: 5px;
	}
	.wcuDeactivateReasonShell {
		display: block;
		margin-bottom: 10px;
	}
	.wcuDeactivateReasonShell [type="radio"] {
		display: inline-block !important;
	    width: auto !important;
	    height: 16px !important;
	}
	#wcuDeactivateWnd input[type="text"] {
		width:100% !important;
		height: 35px !important;
	}
	.wcuDeactivateSkipDataBtn {
		padding:7.5px 0px;
		margin:0px !important;
	}
	#wcuDeactivateWnd textarea {
		width: 100%;
	}
	#wcuDeactivateWnd h4 {
		line-height: 1.53em;
	}
	#wcuDeactivateWnd + .ui-dialog-buttonpane .ui-dialog-buttonset {
		float: none;
	}
	.wcuDeactivateSkipDataBtn {
		float: right;
		margin-top: 15px;
		text-decoration: none;
		color: #777 !important;
	}
</style>
<div id="wcuDeactivateWnd" style="display: none;" title="<?php _e('Your Feedback', WCU_LANG_CODE)?>">
	<h4><?php printf(__('If you have a moment, please share why you are deactivating %s', WCU_LANG_CODE), WCU_WP_PLUGIN_NAME)?></h4>
	<form id="wcuDeactivateForm">
		<label class="wcuDeactivateReasonShell">
			<?php echo htmlWcu::radiobutton('deactivate_reason', array(
				'value' => 'not_working',
			))?>
			<?php _e('Couldn\'t get the plugin to work', WCU_LANG_CODE)?>
			<div class="wcuDeactivateDescShell">
				<?php printf(__('If you have a question, <a href="%s" target="_blank">contact us</a> and will do our best to help you'), 'https://woobewoo.com/contact-us/?utm_source=plugin&utm_medium=deactivated_contact&utm_campaign=woocurrency')?>
			</div>
		</label>
		<label class="wcuDeactivateReasonShell">
			<?php echo htmlWcu::radiobutton('deactivate_reason', array(
				'value' => 'found_better',
			))?>
			<?php _e('I found a better plugin', WCU_LANG_CODE)?>
			<div class="wcuDeactivateDescShell">
				<?php echo htmlWcu::text('better_plugin', array(
					'placeholder' => __('If it\'s possible, specify plugin name', WCU_LANG_CODE),
				))?>
			</div>
		</label>
		<label class="wcuDeactivateReasonShell">
			<?php echo htmlWcu::radiobutton('deactivate_reason', array(
				'value' => 'not_need',
			))?>
			<?php _e('I no longer need the plugin', WCU_LANG_CODE)?>
		</label>
		<label class="wcuDeactivateReasonShell">
			<?php echo htmlWcu::radiobutton('deactivate_reason', array(
				'value' => 'temporary',
			))?>
			<?php _e('It\'s a temporary deactivation', WCU_LANG_CODE)?>
		</label>
		<label class="wcuDeactivateReasonShell">
			<?php echo htmlWcu::radiobutton('deactivate_reason', array(
				'value' => 'other',
			))?>
			<?php _e('Other', WCU_LANG_CODE)?>
			<div class="wcuDeactivateDescShell">
				<?php echo htmlWcu::text('other', array(
					'placeholder' => __('What is the reason?', WCU_LANG_CODE),
				))?>
			</div>
		</label>
		<?php echo htmlWcu::hidden('mod', array('value' => 'promo'))?>
		<?php echo htmlWcu::hidden('action', array('value' => 'saveDeactivateData'))?>
	</form>
	<a href="" class="wcuDeactivateSkipDataBtn"><?php _e('Skip & Deactivate', WCU_LANG_CODE)?></a>
</div>
