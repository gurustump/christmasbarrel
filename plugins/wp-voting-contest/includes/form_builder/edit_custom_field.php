<?php
//Function for editing existing questions
if(!function_exists('vote_contestant_custom_field_edit')){
function vote_contestant_custom_field_edit(){
	global $wpdb; 
	$question_id = $_REQUEST['field_id'];
	$sql = "SELECT * FROM " . VOTES_ENTRY_CUSTOM_TABLE . " WHERE id = '" . $question_id . "'";
	$questions = $wpdb->get_results($sql);
	if ($wpdb->num_rows > 0) {
		foreach ($questions as $question) {
			$question_id = $question->id;
			$question_name = stripslashes($question->question);
			$question_values = stripslashes($question->response);
			$question_type = stripslashes($question->question_type);
			$required = stripslashes($question->required);
			$sequence = $question->sequence;
			$required_text = $question->required_text;
			$admin_only = $question->admin_only;
            $admin_view = $question->admin_view;
            $pretty_view = $question->pretty_view ;
			$system_name = $question->system_name;
            $wp_user = $question->wp_user;
            
?>

<div class="metabox-holder">
  <div class="postbox">
		<div class="handlediv" title="<?php _e('Click to toggle','voting-contest'); ?>"><br>
		</div>
		<h3 class="hndle">
	  <?php _e('Edit Contestant Custom Fields','voting-contest'); ?>
		</h3>
	  <div class="inside">
			<form id="edit-new-question-form" name="newquestion" method="post" action="<?php echo $_SERVER["REQUEST_URI"]?>">

			<table class="form-table">
				<tbody>
					<tr>
						<th>
		  				<label for="question"><?php _e('Field Name','voting-contest'); ?></label>
						</th>
						<td>
		  				<input name="question" id="question" size="50" value="<?php echo $question_name; ?>" type="text"/>
                        <br />
                        <span class="description"><?php _e('Custom Field name','voting-contest'); ?></span>
						</td>
					</tr>
                   <?php if($system_name != "contestant-desc"): ?>
					<tr>
				  	<th id="question-type-select">
		  				<label for="question_type"><?php _e('Type','voting-contest'); ?></label>
						</th>
						<td>
		 				<?php
						$q_values	=	array(
							array('id'=>'TEXT','text'=> __('Text')),
							array('id'=>'TEXTAREA','text'=> __('Text Area')),
							array('id'=>'SINGLE','text'=> __('Radio Button')),
							array('id'=>'DROPDOWN','text'=> __('Drop Down')),
							array('id'=>'MULTIPLE','text'=> __('Checkbox'))
							);
						echo wp_votes_select_input( 'question_type', $q_values,  $question_type, 'id="question_type"');
					?>
                    <br/>
                        <span class="description"><?php _e('Type of the Custom Field','voting-contest'); ?></span>
					</td>
				</tr>
                <?php else: ?>
                        <input type="hidden" name="question_type" id="question_type" value="TEXTAREA" />
                <?php endif; ?>
                
                <?php if($system_name != "contestant-desc"): ?>
				<tr id="add-question-values">
					<th>
		  			<label for="values"><?php _e('Values','voting-contest'); ?></label>
					</th>
					<td>
		  			<input name="values" id="values" size="50" value="<?php echo $question_values; ?>" type="text" />
					<br />
						<span class="description"><?php _e('A comma seperated list of values. Eg. black, blue, red','voting-contest'); ?></span>
					</td>
				</tr>
                <?php endif; ?>
				<tr>
					<th>
						<label for="required"><?php _e('Required','voting-contest'); ?></label>
					</th>
					<td>
		  		<?php
						$values=array(
							array('id'=>'Y','text'=> __('Yes','voting-contest')),
							array('id'=>'N','text'=> __('No','voting-contest'))
						);
						if ($system_question == true && ($system_name =='fname'||$system_name =='lname'||$system_name =='email')){
								$values=array(array('id'=>'Y','text'=> __('Yes','voting-contest')));
						}
							echo wp_votes_select_input('required', $values, $required); 
						?><br />
						<span class="description"><?php _e('Mark this question as required (Mandatory).','voting-contest'); ?></span>
					</td>
				</tr>
				<tr>
					<th>
		  			<label for="admin_only">
							<?php _e('Show in contestant form','voting-contest'); ?>
		  			</label>
					</th>
					<td>						
					<?php
						$values=array(
							array('id'=>'Y','text'=> __('Yes','voting-contest')),
							array('id'=>'N','text'=> __('No','voting-contest'))
						);
						if ($system_question == true && ($system_name =='fname'||$system_name =='lname'||$system_name =='email')){
							$values=array(array('id'=>'N','text'=> __('No','voting-contest')));
						}
						echo wp_votes_select_input('admin_only', $values, $admin_only);
						?> <br />
	                       <span class="description"><?php _e('YES: Shows custom field in contestant form.  NO: Shows custom field in admin only','voting-contest'); ?></span>
					</td>
				</tr>
                
               <tr>
						<th>
							<label class="inline" for="admin_view"><?php _e('Show in Contest description page','voting-contest'); ?></label>
						</th>
						<td>
						<?php
						$values=array(
							array('id'=>'Y','text'=> __('Yes','voting-contest')),
							array('id'=>'N','text'=> __('No','voting-contest'))
						);
						if ($system_question == true && ($system_name =='fname'||$system_name =='lname'||$system_name =='email')){
							$values=array(array('id'=>'N','text'=> __('No','voting-contest')));
						}
						echo wp_votes_select_input('admin_view', $values, $admin_view);
						?>
                            <br />
	                       <span class="description"><?php _e('YES: Shows custom field details in Contestant description page.','voting-contest'); ?></span>
						</td>
				</tr> 
                
                <tr>
						<th>
							<label class="inline" for="admin_view"><?php _e('Show in PrettyPhoto Slideshow','voting-contest'); ?></label>
						</th>
						<td>
						<?php
						$values=array(
							array('id'=>'Y','text'=> __('Yes','voting-contest')),
							array('id'=>'N','text'=> __('No','voting-contest'))
						);
						if ($system_question == true && ($system_name =='fname'||$system_name =='lname'||$system_name =='email')){
							$values=array(array('id'=>'N','text'=> __('No','voting-contest')));
						}
						echo wp_votes_select_input('pretty_view', $values, $pretty_view);
						?>
                            <br />
	                       <span class="description"><?php _e('YES: Shows custom field details in PrettyPhoto Slideshow','voting-contest'); ?></span>
						</td>
				</tr> 
                
                <?php if($system_name != "contestant-desc"): ?>   
				<tr>
					<th>
						<label for="required_text">
							<?php _e('Required Text','voting-contest'); ?>
						</label>
					</th>
					<td>
						<input name="required_text" id="required_text" size="50" value="<?php echo $required_text; ?>" type="text" />	<br /><span class="description"><?php _e('Text to display if field is empty. (Validation Error Message)','voting-contest'); ?></span>
					</td>
				</tr>
				<tr>
					<th>
		  			<label for="sequence">
							<?php _e('Order/Sequence','voting-contest'); ?>
						</label>
					</th>
					<td>
		  			<input name="sequence" id="sequence" size="50" value="<?php echo $sequence; ?>" type="text" />
                     <br /><span class="description"><?php _e('Order the view of the field by numeric values Ex:(Entering 1- will show first, 2- will be shown second.. etc)','voting-contest'); ?></span>
					</td>
				</tr>
                <?php endif; ?>
			</tbody>
		</table>
		<p class="submit-footer">
			<input name="edit_action" value="update" type="hidden">
			<input type="hidden" name="action" value="edit_question">
			<input name="question_id" value="<?php echo $question_id; ?>" type="hidden">
			<input class="button-primary" name="Submit" value="<?php _e('Update Field','voting-contest'); ?>" type="submit">
			<?php //wp_nonce_field( 'espresso_form_check', 'edit_question' ) ?>
		</p>
	</form>
	</div>
 </div>
</div>
<?php
		}
	}else{
		 _e('Nothing found!','voting-contest');
	}
}
}

//Function for editing existing questions
if(!function_exists('vote_contestant_registration_field_edit')){
function vote_contestant_registration_field_edit(){
	global $wpdb;
	$question_id = $_REQUEST['field_id'];
	$sql = "SELECT * FROM " . VOTES_USER_CUSTOM_TABLE . " WHERE id = '" . $question_id . "'";
	$questions = $wpdb->get_results($sql);
	if ($wpdb->num_rows > 0) {
		foreach ($questions as $question) {
			$question_id = $question->id;
			$question_name = stripslashes($question->question);
			$question_values = stripslashes($question->response);
			$question_type = stripslashes($question->question_type);
			$required = stripslashes($question->required);
			$sequence = $question->sequence;
			$required_text = $question->required_text;
			$admin_only = $question->admin_only;
			$system_name = $question->system_name;
            $wp_user = $question->wp_user;
            
?>

<div class="metabox-holder">
  <div class="postbox">
		<div class="handlediv" title="<?php _e('Click to toggle','voting-contest'); ?>"><br>
		</div>
		<h3 class="hndle">
	  <?php _e('Edit Registration Fields','voting-contest'); ?>
		</h3>
	  <div class="inside">
			<form id="edit-new-question-form" name="newquestion" method="post" action="<?php echo $_SERVER["REQUEST_URI"]?>">

			<table class="form-table">
				<tbody>
					<tr>
						<th>
		  				<label for="question"><?php _e('Field Name','voting-contest'); ?></label>
						</th>
						<td>
		  				<input name="question" id="question" size="50" value="<?php echo $question_name; ?>" type="text"/>
                         <br />
                            <span class="description"><?php _e('Custom Field name','voting-contest'); ?></span>
						</td>
					</tr>
                    
					<tr>
				  	<th id="question-type-select">
		  				<label for="question_type"><?php _e('Type','voting-contest'); ?></label>
						</th>
						<td>
		 				<?php
						$q_values	=	array(
							array('id'=>'TEXT','text'=> __('Text')),
							array('id'=>'TEXTAREA','text'=> __('Text Area')),
							array('id'=>'SINGLE','text'=> __('Radio Button')),
							array('id'=>'DROPDOWN','text'=> __('Drop Down')),
							array('id'=>'MULTIPLE','text'=> __('Checkbox'))
							);
					       
                        
						echo wp_votes_select_input( 'question_type', $q_values,  $question_type, 'id="question_type"');
					?> <br/>
                        <span class="description"><?php _e('Type of the Custom Field','voting-contest'); ?></span>
					</td>
				    </tr>                  
                    
                    
				    <tr id="add-question-values">
    					<th>
    		  			<label for="values"><?php _e('Values','voting-contest'); ?></label>
    					</th>
    					<td>
    		  			<input name="values" id="values" size="50" value="<?php echo $question_values; ?>" type="text" />
    					<br />
    						<span class="description"><?php _e('A comma seperated list of values. Eg. black, blue, red','voting-contest'); ?></span>
    					</td>
				    </tr>
                                        
				<tr>
					<th>
						<label for="required"><?php _e('Required','voting-contest'); ?></label>
					</th>
					<td>
		  		<?php
						$values=array(
							array('id'=>'Y','text'=> __('Yes','voting-contest')),
							array('id'=>'N','text'=> __('No','voting-contest'))
						);
						if ($system_question == true && ($system_name =='fname'||$system_name =='lname'||$system_name =='email')){
								$values=array(array('id'=>'Y','text'=> __('Yes','voting-contest')));
						}
							echo wp_votes_select_input('required', $values, $required); 
						?><br />
						<span class="description"><?php _e('Mark this question as required. (Mandatory)','voting-contest'); ?></span>
					</td>
				</tr>
                 
				<tr>
					
                    <th>                    
                        
    		  			<label for="admin_only">
    							<?php _e('Show in Registration form','voting-contest'); ?>
    		  			</label>
                                        
					</th>
                    
					<td>						
					<?php
						$values=array(
							array('id'=>'Y','text'=> __('Yes')),
							array('id'=>'N','text'=> __('No'))
						);
						if ($system_question == true && ($system_name =='fname'||$system_name =='lname'||$system_name =='email')){
							$values=array(array('id'=>'N','text'=> __('No')));
						}
						echo wp_votes_select_input('admin_only', $values, $admin_only);
					?>
                    
                    <br />
                    
                    
                    <span class="description"><?php _e('YES: Shows custom field in Registration form. ','voting-contest'); ?></span>
                    
                    
					</td>
				</tr>
                
				<tr>
					<th>
						<label for="required_text">
							<?php _e('Required Text','voting-contest'); ?>
						</label>
					</th>
					<td>
						<input name="required_text" id="required_text" size="50" value="<?php echo $required_text; ?>" type="text" /><br /><span class="description"><?php _e('Text to display if field is empty. (Validation Error Message)','voting-contest'); ?></span>
					</td>
				</tr>
				<tr>
					<th>
		  			<label for="sequence">
							<?php _e('Order/Sequence','voting-contest'); ?>
						</label>
					</th>
					<td>
		  			<input name="sequence" id="sequence" size="50" value="<?php echo $sequence; ?>" type="text" />
                    <br /><span class="description"><?php _e('Order the view of the field by numeric values Ex:(Entering 1- will show first, 2- will be shown second.. etc)','voting-contest'); ?></span>
					</td>
				</tr>
                
			</tbody>
		</table>
		<p class="submit-footer">
			<input name="edit_action" value="update" type="hidden">
			<input type="hidden" name="action" value="edit_question">
			<input name="question_id" value="<?php echo $question_id; ?>" type="hidden">
			<input class="button-primary" name="Submit" value="<?php _e('Update Field','voting-contest'); ?>" type="submit">
			<?php //wp_nonce_field( 'espresso_form_check', 'edit_question' ) ?>
		</p>
	</form>
	</div>
 </div>
</div>
<?php
		}
	}else{
		 _e('Nothing found!','voting-contest');
	}
}
}
