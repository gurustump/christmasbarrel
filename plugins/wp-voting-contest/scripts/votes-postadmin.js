jQuery(function(){
	jQuery('#votes_expiration').datetimepicker({
		timeFormat: "HH:mm",
		dateFormat: "mm-dd-yy"
	});
	jQuery('#no_expiration').click(function(){
		jQuery('#votes_expiration').val('0');
	});
	jQuery('#post').submit(function() {
		if(jQuery('#votes_expiration').val() == ""){
			jQuery('.spinner').hide();
			jQuery('#publish').removeClass('button-primary-disabled');
			alert('Enter the Expiration Date');
			return false;
		}
	});
	
});