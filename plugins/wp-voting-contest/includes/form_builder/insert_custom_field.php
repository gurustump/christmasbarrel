<?php
if(!function_exists('vote_contestant_custom_field_insert')){
    //Function to add a fields to the database
    function vote_contestant_custom_field_insert(){
    	global $wpdb,$current_user; 
        $go_insert = true;
    	$event_id = empty($_REQUEST['event_id']) ? 0 : $_REQUEST['event_id'];
    	$event_name = empty($_REQUEST['event_name']) ? '' : $_REQUEST['event_name'];
        if($_POST['question']!='')
    	   $question = str_replace("'", "&#039", $_POST['question']);
        else{
            $go_insert=false;
        }
        
    	$question_type = $_POST['question_type'];
    	$question_values = empty($_POST['values']) ? NULL : str_replace("'", "&#039;", $_POST['values']);
    	$required = !empty($_POST['required']) ? $_POST['required']:'N';  
        $required_text = $_POST['required_text']; 
    	$admin_only = !empty($_POST['admin_only']) ? $_POST['admin_only']:'N';
       	$sequence = $_POST['sequence'] ?  $_POST['sequence']:'0';
        $system_name = uniqid();
        $admin_view = $_POST['admin_view']; 
        $pretty_view = $_POST['pretty_view'];
        
        if($go_insert){
    		if ($wpdb->query("INSERT INTO " . VOTES_ENTRY_CUSTOM_TABLE . " (question_type, question, system_name, response, required, admin_only,required_text, sequence,wp_user,admin_view,pretty_view)"
    				. " VALUES ('" . $question_type . "', '" . $question . "', '" . $system_name . "', '"  . $question_values . "', '" . $required . "', '" . $admin_only . "', '" . $required_text . "', " . $sequence . ",'".$current_user->ID . "','".$admin_view."','".$pretty_view."')")){?>
    		<div id="message" class="updated fade"><p><strong><?php _e('The Custom Field ','voting-contest'); ?><?php echo htmlentities2($_REQUEST['question']);?> <?php _e('has been added.','voting-contest'); ?></strong></p></div>
    	<?php }else { ?>
    		<div id="message" class="error"><p><strong><?php _e('The Custom Field ','voting-contest'); ?> <?php echo htmlentities2($_REQUEST['question']);?> <?php _e('was not saved.','voting-contest'); ?> <?php $wpdb->print_error(); ?>.</strong></p></div>
    
    <?php
    	   }
        }else{
         ?>
  		<div id="message" class="error"><p><strong><?php _e('Please enter the title for custom field.','voting-contest'); ?> <?php //$wpdb->print_error(); ?>.</strong></p></div>
         <?php   
        }
    }
}


if(!function_exists('vote_contestant_registration_field_insert')){
    //Function to add a fields to the database
    function vote_contestant_registration_field_insert(){
    	global $wpdb,$current_user; 
        $go_insert = true;
    	$event_id = empty($_REQUEST['event_id']) ? 0 : $_REQUEST['event_id'];
    	$event_name = empty($_REQUEST['event_name']) ? '' : $_REQUEST['event_name'];
        if($_POST['question']!='')
    	   $question = str_replace("'", "&#039", $_POST['question']);
        else{
            $go_insert=false;
        }
        
    	$question_type = $_POST['question_type'];
    	$question_values = empty($_POST['values']) ? NULL : str_replace("'", "&#039;", $_POST['values']);
    	$required = !empty($_POST['required']) ? $_POST['required']:'N';  
        $required_text = $_POST['required_text']; 
    	$admin_only = !empty($_POST['admin_only']) ? $_POST['admin_only']:'N';
       	$sequence = $_POST['sequence'] ?  $_POST['sequence']:'0';
         $system_name = uniqid();
         
        if($go_insert){
    		if ($wpdb->query("INSERT INTO " . VOTES_USER_CUSTOM_TABLE . " (question_type, question, system_name, response, required, admin_only,required_text, sequence,wp_user)"
    				. " VALUES ('" . $question_type . "', '" . $question . "', '" . $system_name . "', '" . $question_values . "', '" . $required . "', '" . $admin_only . "', '" . $required_text . "', " . $sequence . ",'".$current_user->ID."')")){?>
    		<div id="message" class="updated fade"><p><strong><?php _e('The Registration Field','voting-contest'); ?> <?php echo htmlentities2($_REQUEST['question']);?><?php _e(' has been added.','voting-contest'); ?></strong></p></div>
    	<?php }else { ?>
    		<div id="message" class="error"><p><strong><?php _e('The Registration Field','voting-contest'); ?> <?php echo htmlentities2($_REQUEST['question']);?><?php _e('  was not saved. ','voting-contest'); ?><?php //$wpdb->print_error(); ?>.</strong></p></div>
    
    <?php
    	   }
        }else{
         ?>
  		<div id="message" class="error"><p><strong><?php _e('Please enter the title for custom field. ','voting-contest'); ?><?php //$wpdb->print_error(); ?>.</strong></p></div>
         <?php   
        }
    }
}