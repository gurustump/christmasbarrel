jQuery(function(){
    
    
    $('#vote_startdate').datetimepicker({
        timeFormat: "HH:mm",
        dateFormat: "mm-dd-yy"
    });
    $('#vote_enddate').datetimepicker({
        timeFormat: "HH:mm",
        dateFormat: "mm-dd-yy"
    });

    $('#vote_startdate_ex').datetimepicker({
        timeFormat: "HH:mm",
        dateFormat: "mm-dd-yy"
    });
    $('#vote_enddate_ex').datetimepicker({
        timeFormat: "HH:mm",
        dateFormat: "mm-dd-yy"
    });

    var startDateTextBox = $('#votes_starttime');
    var endDateTextBox = $('#votes_expiration');
    startDateTextBox.datetimepicker({ 
            timeFormat: "HH:mm",
            dateFormat: "mm-dd-yy",
            onClose: function(dateText, inst) {
                if (endDateTextBox.val() != '') {
                    var testStartDate = startDateTextBox.datetimepicker('getDate');
                    var testEndDate = endDateTextBox.datetimepicker('getDate');
                    if (testStartDate > testEndDate)
                        endDateTextBox.datetimepicker('setDate', testStartDate);
                }
                else {
                    endDateTextBox.val(dateText);
                }
            },
            onSelect: function (selectedDateTime){
                if(endDateTextBox.val() == '') {
                    startDateTextBox.datetimepicker('option', 'maxDate', '' );
                }
                endDateTextBox.datetimepicker('option', 'minDate', startDateTextBox.datetimepicker('getDate') );
            }
        });
        endDateTextBox.datetimepicker({ 
            timeFormat: "HH:mm",
            dateFormat: "mm-dd-yy",
            onClose: function(dateText, inst) {
                if (startDateTextBox.val() != '') {
                    var testStartDate = startDateTextBox.datetimepicker('getDate');
                    var testEndDate = endDateTextBox.datetimepicker('getDate');
                    if (testStartDate > testEndDate)
                        startDateTextBox.datetimepicker('setDate', testEndDate);
                }
                else {
                    startDateTextBox.val(dateText);
                }
            },
            onSelect: function (selectedDateTime){
                startDateTextBox.datetimepicker('option', 'maxDate', endDateTextBox.datetimepicker('getDate') );
            }
        });
        
        jQuery('.clearendtime').click(function() {
            startDateTextBox.datetimepicker('option', 'maxDate', '' );
        });
        jQuery('#no_expiration').click(function() {
            jQuery('#votes_expiration').val('');
            startDateTextBox.datetimepicker('option', 'maxDate', '' );
        });
        jQuery('.clearstarttime').click(function() {
            endDateTextBox.datetimepicker('option', 'minDate', '');
        });

    
    jQuery('.cleartime').click(function(){
        jQuery(this).siblings('.datetimepicker').val('');
    });
    
        
    jQuery('#tax_activationcount').keyup(function () { 
        this.value = this.value.replace(/[^0-9\.]/g,'');
    });
    
    
    jQuery('#votes_delete_form').submit(function(){
        if(jQuery('#vote_startdate').val() || jQuery('#vote_enddate').val()){
            if(!isNaN(jQuery('#vote_startdate').val()) || !isNaN(jQuery('#vote_enddate').val())){
                alert('Enter the Valid Date');
                return false;
            }
            
        }else if( jQuery('#vote_contest_term').val() < 1){
            alert('Invalid Data');
                  return false;
        }
    });
    
    
    /*jQuery('#votes_export_form').submit(function(){
        if(jQuery('#vote_startdate').val() || jQuery('#vote_enddate').val()){
            if(!isNaN(jQuery('#vote_startdate').val()) || !isNaN(jQuery('#vote_enddate').val())){
                alert('Enter the Valid Date');
                return false;
            }
            
        }else if( jQuery('#vote_contest_term').val() < 1){
            alert('Invalid Data');
                  return false;
        }
    }); */
    
    
    var wa_required = ["existing_contest_term", 
    "mapped_contest_term"];
        
    jQuery('#move_contest_form').submit(function(){
       
        for(var i = 0; i < wa_required.length; i++ ){
            var wa_fields = jQuery("#"+wa_required[i]);            
            if(wa_fields.val() <= 0 ){
                alert('Required Field Missing');
                return false;
            }
        }
        if(!jQuery("#move_contest_form input[type=checkbox]:checked").length){
            alert('Required Field Missing');
                return false;
        }
     return true;
    });
        //var versionjq = jQuery.fn.jquery;
    //if(parseFloat(versionjq) >= parseFloat('1.10.0')){
     // var funct_name = 'on';    
    //}else{
      var funct_name = 'live';
    //}
    
    jQuery('#select_all_post')[funct_name]('change', function(){
        if(jQuery('#select_all_post').attr('checked'))
            jQuery('.selected-post').attr('checked',' checked');
        else
            jQuery('.selected-post').removeAttr('checked');
        
    });
    jQuery('#existing_contest_term').change(function(){
        var selected_term = jQuery('#existing_contest_term').val();
        jQuery.ajax({
            cache: true,
            url: ajaxurl,
            data:{
                action: 'votesbulkmove',
                term_id: selected_term
            },
            type: 'GET',
            success: function( result ) {
                jQuery('#selected_term_post_listing').html(result);
            }   
            
        });
    });
    
    //jQuery('.votes-color-field').wpColorPicker();
    
});
