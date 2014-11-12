<?php
//Function for adding new questions
if(!function_exists('vote_contestant_custom_field_new')){
function vote_contestant_custom_field_new(){
	$values=array(
		array('id'=>'Y','text'=> __('Yes','voting-contest')),
		array('id'=>'N','text'=> __('No','voting-contest'))
	); 
?>
<div class="metabox-holder">
  <div class="postbox">
		<div title="<?php _e('Click to toggle','voting-contest'); ?>" class="handlediv"><br /></div>
	  <h3 class="hndle"><?php _e('Add New Contestant Custom Fields','voting-contest'); ?></h3>
   <div class="inside">
			<p class="intro"><?php _e('Add fields using the form below.','voting-contest'); ?></p>
			<form name="newquestion" method="post" action="" id="new-question-form">

			<table class="form-table">
				<tbody>
					<tr>
						<th>
							<label for="question"><?php _e('Field Name'); ?><em title="<?php _e('This field is required','voting-contest') ?>"> *</em></label>
						</th>
						<td>
							<input class="question-name"  name="question" id="question" size="50" value="" type="text" /><br />
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
						echo wp_votes_select_input( 'question_type', $q_values, '', 'id="question_type"');
                        
					?>  <br/>
                        <span class="description"><?php _e('Type of the Custom Field','voting-contest'); ?></span>
						</td>
					</tr>
					<tr id="add-question-values">
						<th>
							<label for="values"><?php _e('Values','voting-contest'); ?></label>
						</th>
						<td>
							<input name="values" id="values" size="50" value="" type="text" /><br />
							<span class="description"><?php _e('A comma seperated list of values. Eg. black, blue, red','voting-contest'); ?></span>
						</td>
					</tr>
					<tr>
						<th>
							<label class="inline" for="required"><?php _e('Required:','voting-contest'); ?></label>
						</th>
						<td>
							<?php
							echo wp_votes_select_input('required', $values, 'N');
						?><br />
						<span class="description"><?php _e('Mark this question as required (Mandatory).','voting-contest'); ?></span>
						</td>
					</tr>
					<tr>
						<th>
							<label class="inline" for="admin_only"><?php _e('Show in contestant form','voting-contest'); ?></label>
						</th>
						<td>
							<?php echo wp_votes_select_input('admin_only', $values, 'N');?>
                            <br />
	                       <span class="description"><?php _e('YES: Shows custom field in contestant form.  NO: Shows custom field in admin only','voting-contest'); ?></span>
						</td>
					</tr>
                    
                    <tr>
						<th>
							<label class="inline" for="admin_view"><?php _e('Show in Contest description page','voting-contest'); ?></label>
						</th>
						<td>
							<?php echo wp_votes_select_input('admin_view', $values, 'N');?>
                            <br />
	                       <span class="description"><?php _e('YES: Shows custom field details in Contestant description page.','voting-contest'); ?></span>
						</td>
					</tr>
                    
                    <tr>
						<th>
							<label class="inline" for="admin_view"><?php _e('Show in PrettyPhoto Slideshow','voting-contest'); ?></label>
						</th>
						<td>
							<?php echo wp_votes_select_input('pretty_view', $values, 'N');?>
                            <br />
	                       <span class="description"><?php _e('YES: Shows custom field details in PrettyPhoto Slideshow.','voting-contest'); ?></span>
						</td>
					</tr>
                    
					<tr>
						<th>
							<label for="required_text"><?php _e('Required Text','voting-contest'); ?></label>
						</th>
						<td>
		 					<input name="required_text" id="required_text" size="50" type="text" />
							<br /><span class="description"><?php _e('Text to display if field is empty. (Validation Error Message)','voting-contest'); ?></span>
						</td>
					</tr>
					<tr>
						<th>
							<label for="sequence"><?php   _e('Order/Sequence','voting-contest'); ?></label>
						</th>
						<td>
		  				<input name="sequence" id="sequence" size="50" value="<?php if(isset($sequence)) echo $sequence; ?>" type="text" />           	
                          <br /><span class="description"><?php _e('Order the view of the field by numeric values Ex:(Entering 1- will show first, 2- will be shown second.. etc)','voting-contest'); ?></span>
						</td>
					</tr>
				</tbody>
			</table>
		<p class="submit-footer">
			<input name="action" value="insert" type="hidden" />
			<input class="button-primary" name="Submit" value="<?php _e('Add Custom Fields','voting-contest'); ?>" type="submit" />
			<?php //wp_nonce_field( 'espresso_form_check', 'add_new_question' ); ?>
		</p>
		</form>
	</div>
</div>
</div>
<?php
    }
}

if(!function_exists('vote_contestant_registration_field_insert_new')){
function vote_contestant_registration_field_insert_new(){
	$values=array(
		array('id'=>'Y','text'=> __('Yes','voting-contest')),
		array('id'=>'N','text'=> __('No','voting-contest'))
	);
?>
<div class="metabox-holder">
  <div class="postbox">
		<div title="<?php _e('Click to toggle','voting-contest'); ?>" class="handlediv"><br /></div>
	  <h3 class="hndle"><?php _e('Add New Registration Fields','voting-contest','voting-contest'); ?></h3>
   <div class="inside">
			<p class="intro"><?php _e('Add registration fields using the form below.'); ?></p>
			<form name="newquestion" method="post" action="" id="new-question-form">

			<table class="form-table">
				<tbody>
					<tr>
						<th>
							<label for="question"><?php _e('Field Name','voting-contest'); ?><em title="<?php _e('This field is required') ?>"> *</em></label>
						</th>
						<td>
							<input class="question-name"  name="question" id="question" size="50" value="" type="text" />
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
						echo wp_votes_select_input( 'question_type', $q_values, '', 'id="question_type"');
                        
					?>   <br/>
                        <span class="description"><?php _e('Type of the Custom Field','voting-contest'); ?></span>
						</td>
					</tr>
					<tr id="add-question-values">
						<th>
							<label for="values"><?php _e('Values','voting-contest'); ?></label>
						</th>
						<td>
							<input name="values" id="values" size="50" value="" type="text" /><br />
							<span class="description"><?php _e('A comma seperated list of values. Eg. black, blue, red','voting-contest'); ?></span>
						</td>
					</tr>
					<tr>
						<th>
							<label class="inline" for="required"><?php _e('Required:','voting-contest'); ?></label>
						</th>
						<td>
							<?php
							echo wp_votes_select_input('required', $values, 'N');
						?><br />
						<span class="description"><?php _e('Mark this question as required. (Mandatory)','voting-contest'); ?></span>
						</td>
					</tr>
					<tr>
						<th>
							<label class="inline" for="admin_only"><?php _e('Show in Registration form','voting-contest'); ?></label>
						</th>
						<td>
							<?php echo wp_votes_select_input('admin_only', $values, 'N');?>
                            <br /><span class="description"><?php _e('YES: Shows custom field in Registration form. ','voting-contest'); ?></span>
						</td>
					</tr>
					<tr>
						<th>
							<label for="required_text"><?php _e('Required Text','voting-contest'); ?></label>
						</th>
						<td>
		 					<input name="required_text" id="required_text" size="50" type="text" />
							<br /><span class="description"><?php _e('Text to display if field is empty. (Validation Error Message)','voting-contest'); ?></span>
						</td>
					</tr> 
					<tr>
						<th>
							<label for="sequence"><?php   _e('Order/Sequence','voting-contest'); ?></label>
						</th>
						<td>
		  				<input name="sequence" id="sequence" size="50" value="<?php if(isset($sequence)) echo $sequence; ?>" type="text" />
                        <br /><span class="description"><?php _e('Order the view of the field by numeric values Ex:(Entering 1- will show first, 2- will be shown second.. etc)','voting-contest'); ?></span>
						</td>
					</tr>
				</tbody>
			</table>
		<p class="submit-footer">
			<input name="action" value="insert" type="hidden" />
			<input class="button-primary" name="Submit" value="<?php _e('Add Custom Fields','voting-contest'); ?>" type="submit" />
			<?php //wp_nonce_field( 'espresso_form_check', 'add_new_question' ); ?>
		</p>
		</form>
	</div>
</div>
</div>
<?php
    }
}
