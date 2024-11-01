jQuery(document).ready(function(){
	jQuery('#wcuMailTestForm').submit(function(){
		jQuery(this).sendFormWcu({
			btn: jQuery(this).find('button:first')
		,	onSuccess: function(res) {
				if(!res.error) {
					jQuery('#wcuMailTestForm').slideUp( 300 );
					jQuery('#wcuMailTestResShell').slideDown( 300 );
				}
			}
		});
		return false;
	});
	jQuery('.wcuMailTestResBtn').click(function(){
		var result = parseInt(jQuery(this).data('res'));
		jQuery.sendFormWcu({
			btn: this
		,	data: {mod: 'mail', action: 'saveMailTestRes', result: result}
		,	onSuccess: function(res) {
				if(!res.error) {
					jQuery('#wcuMailTestResShell').slideUp( 300 );
					jQuery('#'+ (result ? 'wcuMailTestResSuccess' : 'wcuMailTestResFail')).slideDown( 300 );
				}
			}
		});
		return false;
	});
	jQuery('#wcuMailSettingsForm').submit(function(){
		jQuery(this).sendFormWcu({
			btn: jQuery(this).find('button:first')
		});
		return false; 
	});
});