jQuery(document).ready(function(){
	jQuery('.woobewoo-plugin select').chosen(g_wcuChosenOptions);	
	jQuery('#wcuSettingsSaveBtn').click(function(){
		jQuery('#wcuSettingsForm').submit();
		return false;
	});
	jQuery('#wcuSettingsForm').submit(function(){
		jQuery(this).sendFormWcu({
			btn: jQuery('#wcuSettingsSaveBtn')
		});
		return false;
	});
	/*Connected options: some options need to be visible  only if in other options selected special value (e.g. if send engine SMTP - show SMTP options)*/
	var $connectOpts = jQuery('#wcuSettingsForm').find('[data-connect]');
	if($connectOpts && $connectOpts.length) {
		var $connectedTo = {};
		$connectOpts.each(function(){
			var connectToData = jQuery(this).data('connect').split(':')
			,	$connectTo = jQuery('#wcuSettingsForm').find('[name="opt_values['+ connectToData[ 0 ]+ ']"]')
			,	connected = $connectTo.data('connected');
			if(!connected) connected = {};
			if(!connected[ connectToData[1] ]) connected[ connectToData[1] ] = [];
			connected[ connectToData[1] ].push( this );
			$connectTo.data('connected', connected);
			if(!$connectTo.data('binded')) {
				$connectTo.change(function(){
					var connected = jQuery(this).data('connected')
					,	value = jQuery(this).val();
					if(connected) {
						for(var connectVal in connected) {
							if(connected[ connectVal ] && connected[ connectVal ].length) {
								var show = connectVal == value;
								for(var i = 0; i < connected[ connectVal ].length; i++) {
									show
									? jQuery(connected[ connectVal ][ i ]).show()
									: jQuery(connected[ connectVal ][ i ]).hide();
								}
							}
						}
					}
				});
				$connectTo.data('binded', 1);
			}
			$connectedTo[ connectToData[ 0 ] ] = $connectTo;
		});
		for(var connectedName in $connectedTo) {
			$connectedTo[ connectedName ].change();
		}
	}
});
