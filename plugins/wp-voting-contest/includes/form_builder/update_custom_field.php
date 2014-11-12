<?php
if(!function_exists('vote_contestant_custom_field_update')){
//Function to update questions in the database
function vote_contestant_custom_field_update(){
	global $wpdb;
    $go_insert=true;
    if($_POST['question']!='')
    	   	$question_text = str_replace("'", "&#039", $_POST['question']);
    else{
        $go_insert=false;
    }

	$question_id = $_POST['question_id'];
	$question_type = $_POST['question_type'];
	$sequence = $_POST['sequence'];
	$values = empty($_POST['values']) ? '' : str_replace("'", "&#039;", $_POST['values']);
	$required = $_POST['required'];
	$required_text = $_POST['required_text'];
	$admin_only = $_POST['admin_only'];
    $admin_view = $_POST['admin_view']; 
    $pretty_view = $_POST['pretty_view']; 
    if($go_insert){
	$wpdb->query("UPDATE " . VOTES_ENTRY_CUSTOM_TABLE . " SET question_type = '" . $question_type . "', question = '" . $question_text . "', response = '" . $values . "', required = '" . $required . "',admin_only = '" . $admin_only . "', required_text = '" . $required_text . "',pretty_view = '" . $pretty_view  . "', sequence = '" . $sequence . "',admin_view = '" . $admin_view . "' WHERE id = '" . $question_id . "'");
    }else
    {
     ?>
  		<div id="message" class="error"><p><strong><?php _e('Please enter the title for custom field. ','voting-contest'); ?><?php //$wpdb->print_error(); ?>.</strong></p></div>
    <?php     
    }
}
}

if(!function_exists('vote_contestant_registration_field_update')){
//Function to update questions in the database
function vote_contestant_registration_field_update(){
	global $wpdb;
    $go_insert=true;
    if($_POST['question']!='')
    	   	$question_text = str_replace("'", "&#039", $_POST['question']);
    else{
        $go_insert=false;
    }

	$question_id = $_POST['question_id'];
	$question_type = $_POST['question_type'];
	$sequence = $_POST['sequence'];
	$values = empty($_POST['values']) ? '' : str_replace("'", "&#039;", $_POST['values']);
	$required = $_POST['required'];
	$required_text = $_POST['required_text'];
	$admin_only = $_POST['admin_only'];
     
    if($go_insert){
	$wpdb->query("UPDATE " . VOTES_USER_CUSTOM_TABLE . " SET question_type = '" . $question_type . "', question = '" . $question_text . "', response = '" . $values . "', required = '" . $required . "',admin_only = '" . $admin_only . "', required_text = '" . $required_text . "', sequence = '" . $sequence . "' WHERE id = '" . $question_id . "'");
    }else
    {
     ?>
  		<div id="message" class="error"><p><strong><?php _e('Please enter the title for custom field. ','voting-contest'); ?> <?php //$wpdb->print_error(); ?>.</strong></p></div>
    <?php     
    }
}
}