<?php 
if(!function_exists('votes_fieldcontestant')){
    function votes_fieldcontestant(){
	global $wpdb;
	//Update the questions when re-ordering
	if (!empty($_REQUEST['update_sequence'])) {
	    $rows = explode(",", $_POST['row_ids']);
	    for ($i = 0; $i < count($rows); $i++) {
		    $wpdb->query("UPDATE " . VOTES_ENTRY_CUSTOM_TABLE . " SET sequence=" . $i . " WHERE id='" . $rows[$i] . "'");
	    }
	    die();
	}
    
	wp_enqueue_script('jquery');
	wp_enqueue_script('postbox');
	wp_enqueue_script('dataTables', VOTES_PATH . 'scripts/jquery.dataTables.min.js', array('jquery'));
	wp_register_script('jquery.validate.js', (VOTES_PATH. "scripts/jquery.validate.min.js"), false, '1.8.1');
	wp_enqueue_script('jquery.validate.js');
	wp_register_style('votesadmin', VOTES_PATH . 'css/admin-styles.css');
	wp_enqueue_style('votesadmin');
	wp_localize_script( 'dataTables', 'VOTES_LINKS', array('ajaxurl' => admin_url('admin-ajax.php'), 'plugin_url' => VOTES_PATH) );  
	// get counts
	$sql = "SELECT id FROM " . VOTES_ENTRY_CUSTOM_TABLE." where delete_time=0";
	$wpdb->get_results($sql);
	$total_questions = $wpdb->num_rows; 
	$wpdb->get_results($sql);
	$total_self_questions = $wpdb->num_rows;       
	?>
	<div class="wrap">	
	    <h2><?php echo _e('Manage Contestant Custom Fields','voting-contest') ?>
		<?php
		if (!isset($_REQUEST['action']) || ($_REQUEST['action'] != 'edit_question' && $_REQUEST['action'] != 'new_question')) {
			echo '<a href="admin.php?page=fieldcontestant&action=new_question" class="button add-new-h2" style="margin-left: 20px;">' . __('Add New Field','voting-contest') . '</a>';
		}
		?>
	    </h2>
	    
	    <?php ob_start();?>
	    <div class="meta-box-sortables ui-sortables">
	    <?php
	    //Update the question
	    if (isset($_REQUEST['edit_action']) && $_REQUEST['edit_action'] == 'update') {
		    require_once(VOTES_ABSPATH . 'includes/form_builder/update_custom_field.php');
		    vote_contestant_custom_field_update();
	    }
		
	    //Figure out which view to display
	    if (isset($_REQUEST['action'])) { 		 
		switch ($_REQUEST['action']) { 
		    case 'insert':
			if (file_exists(VOTES_ABSPATH . 'includes/form_builder/insert_custom_field.php')) {
				require_once(VOTES_ABSPATH . 'includes/form_builder/insert_custom_field.php');
				vote_contestant_custom_field_insert();
			} else {
				?>
				<div id="message" class="updated fade">
					<p><strong>
							<?php _e('This function is not available.','voting-contest'); ?>
						</strong></p>
				</div>
				<?php
			}
		    break;
		    case 'new_question':
			if (file_exists(VOTES_ABSPATH . 'includes/form_builder/new_custom_field.php')) {
				require_once(VOTES_ABSPATH . 'includes/form_builder/new_custom_field.php');
				vote_contestant_custom_field_new(); 
			}else {
				?>
				<div id="message" class="updated fade">
					<p><strong>
							<?php _e('This function is not available.','voting-contest'); ?>
						</strong></p>
				</div>
				<?php
			}
		    break;
		    case 'edit_fields':
			require_once(VOTES_ABSPATH."includes/form_builder/edit_custom_field.php");
			vote_contestant_custom_field_edit();
		    break;
		    case 'delete_fields':
			if (file_exists(VOTES_ABSPATH . 'includes/form_builder/delete_custom_field.php')) {
				require_once(VOTES_ABSPATH . 'includes/form_builder/delete_custom_field.php');
				vote_contestant_custom_field_delete();
			} else {
				?>
				<div id="message" class="updated fade">
					<p><strong>
							<?php _e('This function is not available.','voting-contest'); ?>
					    </strong>
					</p>
				</div>
				<?php
			}
		    break;
		}
	    }
	    ?>
	</div>
    
	<form id="form1" name="form1" method="post" action="<?php echo $_SERVER["REQUEST_URI"] ?>">
	    <table id="table" class="widefat manage-questions">
		<thead>
		    <tr>
			<th class="manage-column" id="cb" scope="col" ></th>
    
			<th class="manage-column column-title" id="values" scope="col" title="Click to Sort" style="width:25%;">
				<?php _e('Field Name','voting-contest'); ?>
			</th>
			<th class="manage-column column-title" id="values" scope="col" title="Click to Sort" style="width:15%;">
				<?php _e('Values','voting-contest'); ?>
			</th>
    
			<th class="manage-column column-title" id="values" scope="col" title="Click to Sort"  style="width:10%;">
				<?php _e('Type','voting-contest'); ?>
			</th>
			<th class="manage-column column-title" id="values" scope="col" title="Click to Sort" style="width:10%;">
				<?php _e('Required','voting-contest'); ?>
			</th>
    
			<th class="manage-column column-title" id="values" scope="col" title="Click to Sort" style="width:10%;">
				<?php _e('Show in Form','voting-contest'); ?>
			</th>
    
			<th class="manage-column column-title" id="values" scope="col" title="Click to Sort" style="width:10%;">
				<?php _e('Show in Description','voting-contest'); ?>
			</th>
			<th class="manage-column column-title" id="values" scope="col" title="Click to Sort" style="width:10%;">
				<?php _e('Order Sequence','voting-contest'); ?>
			</th>
		    </tr>
		</thead>
		<tbody>
			<?php
			$sql = "SELECT * FROM " . VOTES_ENTRY_CUSTOM_TABLE;
			$sql .= " WHERE delete_time=0 ";
			$sql .= " ORDER BY sequence";
			if ( is_super_admin() ) {
			    $questions = $wpdb->get_results($sql);
			    if ($wpdb->num_rows > 0) {
				    foreach ($questions as $question) {
					$question_id = $question->id;
					$question_name = stripslashes($question->question);
					$values = stripslashes($question->response);
					$question_type = stripslashes($question->question_type);
					$required = stripslashes($question->required);
					$system_name = $question->system_name;
					$sequence = $question->sequence;
					$admin_only = $question->admin_only;
					$admin_view = $question->admin_view;
					$wp_user = $question->wp_user == 0 ? 1 : $question->wp_user;
					?>
                    <?php $cursor_move = ($system_name != "contestant-desc")?'style="cursor: move"':""; ?>
                    <?php $tr_id = ($system_name != "contestant-desc")?$question_id:"contestants-desc"; ?>
                    
					<tr <?php echo $cursor_move; ?> id="<?php echo $tr_id ?>">
					    <td class="checkboxcol">
                        <input name="row_id" type="hidden" value="<?php echo $question_id ?>" />
                         <?php if($system_name != "contestant-desc"): ?>                            
						    <input  style="margin:7px 0 22px 8px; vertical-align:top;" name="checkbox[<?php echo $question_id ?>]" type="checkbox" class="question_checkbox"  title="Delete <?php echo $question_name ?>" />
                         <?php endif; ?>
					    </td>
					    
					    <td class="post-title page-title column-title"><strong><a href="admin.php?page=fieldcontestant&amp;action=edit_fields&amp;field_id=<?php echo $question_id ?>"><?php echo $question_name ?></a></strong>
						<div class="row-actions">
                            <?php $separator_ed = ($system_name != "contestant-desc")?"|":""; ?>
							<span class="edit"><a href="admin.php?page=fieldcontestant&amp;action=edit_fields&amp;field_id=<?php echo $question_id ?>"><?php _e('Edit','voting-contest'); ?></a> <?php echo $separator_ed ?> </span>
                            <?php if($system_name != "contestant-desc"): ?>
							<span class="delete"><a onclick="return confirmDelete('single');" class="submitdelete"  href="admin.php?page=fieldcontestant&amp;action=delete_fields&amp;field_id=<?php echo $question_id ?>"><?php _e('Delete','voting-contest'); ?></a></span>
                            <?php endif; ?>
						</div>
					    </td>
					    <td class="author column-author"><?php echo $values ?></td>
					    <?php 
					    if($question_type=='SINGLE'){
					    $question_type = 'RADIO';
					    }else if($question_type=='MULTIPLE')
					    $question_type = 'CHECKBOX';
					    ?>
					    <td class="author column-author"><?php echo $question_type ?></td>
					    <td class="author column-author"><?php echo $required ?></td>
					    <td class="author column-author"><?php echo $admin_only ?></td>
					    <td class="author column-author"><?php echo $admin_view ?></td>
					    <td class="author column-author"><?php echo $sequence ?></td>
					</tr>
				    <?php
				    }
			    }
		    }
		    ?>
		</tbody>
	    </table>
    
	    <div>
		<p><input type="checkbox" name="sAll" onclick="selectAll(this)" class="select_checkbox" />
		    <strong>
			    <?php _e('Check All','voting-contest'); ?>
		    </strong>
		    <input type="hidden" name="action" value="delete_fields" />
		    <input style="margin-left: 10px;" name="delete_question" type="submit" class="button-secondary" id="delete_question" value="<?php _e('Delete Field','voting-contest'); ?>" style="margin-left:10px 0 0 10px;" onclick="return confirmDelete('multiple');">
		    <a  style="margin-left:5px"class="button-primary" href="admin.php?page=fieldcontestant&amp;action=new_question"><?php _e('Add New Field','voting-contest'); ?></a>
		</p>
	    </div>
	</form>
	    <?php
	    $main_post_content = ob_get_clean();
	    echo $main_post_content;
	    ?>							
    
	</div>
	    <script type="text/javascript">
	    jQuery(document).ready(function($) {
		jQuery('#new-question-form').validate({
		    rules: {
			    question: "required",
			    values: "required",
			    sequence:{number: true}
		    },
		    messages: {
			   question: "<?php _e('Please add a field name','voting-contest'); ?>",
			values: "<?php _e('Please add a list of values for the field','voting-contest'); ?>",
			sequence:{number: "Please enter the numeric values"}
		    }
		});
			
		/* show the table data */
		var mytable = jQuery('#table').dataTable({
		    "bStateSave": true,
		    "sPaginationType": "full_numbers",
	    
		    "fnDrawCallback": function( oSettings ) {
			 jQuery('.question_checkbox').attr('checked',false);
			 if(jQuery('.select_checkbox').is(':checked')){
			   jQuery('.select_checkbox').attr('checked',false);
			 }
		    },
		    "oLanguage": {	"sSearch": "<strong><?php _e('Live Search Filter', 'voting-contest'); ?>:</strong>",
			    "sZeroRecords": "<?php _e('No Records Found!', 'voting-contest'); ?>" },
		    "aoColumns": [
			    { "bSortable": false },
			    null,
			    null,
			    null,
			    null,
			    <?php echo function_exists('espresso_is_admin') && espresso_is_admin() == true ? 'null,' : ''; ?>
			    null,null,null
    
		    ]
    
		});
    
		var startPosition;
		var endPosition;
		jQuery("#table tbody").sortable({
		    cursor: "move",
            items: 'tr[id!=contestants-desc]',
		    start:function(event, ui){    
			    startPosition = ui.item.prevAll().length + 1;
		    },
		    update: function(event, ui) {
			    endPosition = ui.item.prevAll().length + 1;
			    var row_ids="";
			    jQuery('#table tbody input[name="row_id"]').each(function(i){
				    row_ids= row_ids + ',' + jQuery(this).val();
			    });
			    jQuery.post(VOTES_LINKS.ajaxurl, { action: "update_sequence", row_ids: row_ids, update_sequence: "true"} );
		    }
		});
		postboxes.add_postbox_toggles('form_builder');    
	    });
						    
	    // Remove li parent for input 'values' from page if 'text' box or 'textarea' are selected
	    var selectValue = jQuery('select#question_type option:selected').val();
	    //alert(selectValue + ' - this is initial value');
	    // hide values field on initial page view
	    if(selectValue == 'TEXT' || selectValue == 'TEXTAREA' || selectValue == 'DATE'){
		    jQuery('#add-question-values').hide();
		    // we don't want the values field trying to validate if not displayed, remove its name
		    jQuery('#add-question-values td input').attr("name","notrequired") 
	    }			
	    jQuery('select#question_type').bind('change', function() {
		var selectValue = jQuery('select#question_type option:selected').val();
			  
		if (selectValue == 'TEXT' || selectValue == 'TEXTAREA' || selectValue == 'DATE') {
		    jQuery('#add-question-values').fadeOut('slow');
		    // we don't want the values field trying to validate if not displayed, remove its name
		    jQuery('#add-question-values td input').attr("name","notrequired") 
		} else{
		    jQuery('#add-question-values').fadeIn('slow');
		    // add the correct name value back in so we can run validation check.
		    jQuery('#add-question-values td input').attr("name","values");			    
		}
	    });
		      
	    //Select All
	    function selectAll(x) {
		if(x.checked){
		for(var i=0,l=x.form.length; i<l; i++)
		if(x.form[i].type == 'checkbox' && x.form[i].name != 'sAll')
		x.form[i].checked=true
		}else{
		  for(var i=0,l=x.form.length; i<l; i++)
		  if(x.form[i].type == 'checkbox' && x.form[i].name != 'sAll')
		    x.form[i].checked=false
		}
	    }
	    
	    function confirmDelete(seld){
		if(seld=='multiple'){
		    if(jQuery('.question_checkbox').is(':checked')){
			if (confirm('<?php _e("Are you sure want to delete?","voting-contest"); ?>')){
			  return true;
			}
		    }else{
			alert('<?php _e("Select atleast one field to delete!","voting-contest"); ?>');
		    }
		    return false;
		}else{return true;}
	    }
			    
	    </script>
	 <?php 
    } 
}
    
    
 //Registration Custom fields   
if(!function_exists('votes_registrationcontestant')){
    function votes_registrationcontestant(){
	global $wpdb;
	//Update the questions when re-ordering
	if (!empty($_REQUEST['update_sequence'])) {
	    $rows = explode(",", $_POST['row_ids']);
	    for ($i = 0; $i < count($rows); $i++) {
		    $wpdb->query("UPDATE " . VOTES_USER_CUSTOM_TABLE . " SET sequence=" . $i . " WHERE id='" . $rows[$i] . "'");
	    }
	    die();
	}
    
	wp_enqueue_script('jquery');
	wp_enqueue_script('postbox');
	wp_enqueue_script('dataTables', VOTES_PATH . 'scripts/jquery.dataTables.min.js', array('jquery'));
	wp_register_script('jquery.validate.js', (VOTES_PATH. "scripts/jquery.validate.min.js"), false, '1.8.1');
	wp_enqueue_script('jquery.validate.js');
	wp_register_style('votesadmin', VOTES_PATH . 'css/admin-styles.css');
	wp_enqueue_style('votesadmin');
	wp_localize_script( 'dataTables', 'VOTES_LINKS', array('ajaxurl' => admin_url('admin-ajax.php'), 'plugin_url' => VOTES_PATH) );  
	// get counts
	$sql = "SELECT id FROM " . VOTES_USER_CUSTOM_TABLE . " where delete_time=0";
	$wpdb->get_results($sql);
	$total_questions = $wpdb->num_rows; 
	$wpdb->get_results($sql);
	$total_self_questions = $wpdb->num_rows;       
	?>
	<div class="wrap">	
	    <h2><?php echo _e('Manage Registration Custom Fields','voting-contest'); ?>
		    <?php
		    if (!isset($_REQUEST['action']) || ($_REQUEST['action'] != 'edit_question' && $_REQUEST['action'] != 'new_question')) {
			    echo '<a href="admin.php?page=fieldregistration&action=new_question" class="button add-new-h2" style="margin-left: 20px;">' . __('Add New Field','voting-contest') . '</a>';
		    }
		    ?>
	    </h2>
	    
	    <?php ob_start();?>
	    <div class="meta-box-sortables ui-sortables">
		<?php
		    //Update the question
		    if (isset($_REQUEST['edit_action']) && $_REQUEST['edit_action'] == 'update') {
			    require_once(VOTES_ABSPATH . 'includes/form_builder/update_custom_field.php');
			    vote_contestant_registration_field_update();
		    }
		
		    //Figure out which view to display
		    if (isset($_REQUEST['action'])) { 		 
			switch ($_REQUEST['action']) {
			    
			    case 'insert':
				if (file_exists(VOTES_ABSPATH . 'includes/form_builder/insert_custom_field.php')) {
				    require_once(VOTES_ABSPATH . 'includes/form_builder/insert_custom_field.php');
				    vote_contestant_registration_field_insert();
				} else {
				    ?>
				    <div id="message" class="updated fade">
					<p><strong>
					    <?php _e('This function is not available.','voting-contest'); ?>
					</strong></p>
				    </div>
				    <?php
				}
			    break;
			
			    case 'new_question':
				if (file_exists(VOTES_ABSPATH . 'includes/form_builder/new_custom_field.php')) {
				    require_once(VOTES_ABSPATH . 'includes/form_builder/new_custom_field.php');
				    vote_contestant_registration_field_insert_new(); 
				}else {
				    ?>
				    <div id="message" class="updated fade">
					<p><strong>
					    <?php _e('This function is not available.','voting-contest'); ?>
					</strong></p>
				    </div>
				    <?php
				}
			    break;
			
			    case 'edit_fields':
				require_once(VOTES_ABSPATH."includes/form_builder/edit_custom_field.php");
				vote_contestant_registration_field_edit();
			    break;
			
			    case 'delete_fields':
				if (file_exists(VOTES_ABSPATH . 'includes/form_builder/delete_custom_field.php')) {
				    require_once(VOTES_ABSPATH . 'includes/form_builder/delete_custom_field.php');
				    vote_contestant_registration_field_delete();
				} else {
				    ?>
				    <div id="message" class="updated fade">
					<p><strong>
					    <?php _e('This function is not available.','voting-contest'); ?>
					</strong></p>
				    </div>
				    <?php
				}
			    break;
			}
		    }
		?>
	    </div>
    
	    <form id="form1" name="form1" method="post" action="<?php echo $_SERVER["REQUEST_URI"] ?>">
		<table id="table" class="widefat manage-questions">
		    <thead>
			    <tr>
				    <th class="manage-column" id="cb" scope="col" ></th>
    
				    <th class="manage-column column-title" id="values" scope="col" title="Click to Sort" style="width:25%;">
					    <?php _e('Field Name','voting-contest'); ?>
				    </th>
				    <th class="manage-column column-title" id="values" scope="col" title="Click to Sort" style="width:15%;">
					    <?php _e('Values','voting-contest'); ?>
				    </th>
    
				    <th class="manage-column column-title" id="values" scope="col" title="Click to Sort"  style="width:10%;">
					    <?php _e('Type','voting-contest'); ?>
				    </th>
				    <th class="manage-column column-title" id="values" scope="col" title="Click to Sort" style="width:10%;">
					    <?php _e('Required','voting-contest'); ?>
				    </th>
				    <th class="manage-column column-title" id="values" scope="col" title="Click to Sort" style="width:10%;">
					    <?php _e('Show in Form','voting-contest'); ?>
				    </th>
				    <th class="manage-column column-title" id="values" scope="col" title="Click to Sort" style="width:10%;">
					    <?php _e('Order Sequence','voting-contest'); ?>
				    </th>
			    </tr>
		    </thead>
		    <tbody>
			<?php
			$sql = "SELECT * FROM " . VOTES_USER_CUSTOM_TABLE;
			$sql .= " WHERE delete_time=0 ";
			$sql .= " ORDER BY sequence";
			$questions = $wpdb->get_results($sql);
			    if ( is_super_admin() ) {
				if ($wpdb->num_rows > 0) {
				    foreach ($questions as $question) {
					$question_id = $question->id;
					$question_name = stripslashes($question->question);
					$values = stripslashes($question->response);
					$question_type = stripslashes($question->question_type);
					$required = stripslashes($question->required);
					$system_name = $question->system_name;
					$sequence = $question->sequence;
					$admin_only = $question->admin_only;
					$wp_user = $question->wp_user == 0 ? 1 : $question->wp_user;
					?>
					<tr style="cursor: move" id="<?php echo $question_id ?>">
					    <td class="checkboxcol"><input name="row_id" type="hidden" value="<?php echo $question_id ?>" />
							    <input  style="margin:7px 0 22px 8px; vertical-align:top;" name="checkbox[<?php echo $question_id ?>]" type="checkbox" class="question_checkbox" title="Delete <?php echo $question_name ?>">
					    </td>
	
					    <td class="post-title page-title column-title"><strong><a href="admin.php?page=fieldregistration&amp;action=edit_fields&amp;field_id=<?php echo $question_id ?>"><?php echo $question_name ?></a></strong>
						    <div class="row-actions">
                                
                                
							    <span class="edit"><a href="admin.php?page=fieldregistration&amp;action=edit_fields&amp;field_id=<?php echo $question_id ?>"><?php _e('Edit','voting-contest'); ?></a> | </span>
                                
                                
							    <span class="delete"><a onclick="return confirmDelete('single');" class="submitdelete"  href="admin.php?page=fieldregistration&amp;action=delete_fields&amp;field_id=<?php echo $question_id ?>"><?php _e('Delete','voting-contest'); ?></a></span>
                                
                                
						    </div>
					    </td>
					    <td class="author column-author"><?php echo $values ?></td>
					    <?php 
					    if($question_type=='SINGLE'){
						$question_type = 'RADIO';
					    }else if($question_type=='MULTIPLE')
						$question_type = 'CHECKBOX';
						
					    ?>
					    <td class="author column-author"><?php echo $question_type ?></td>
					    <td class="author column-author"><?php echo $required ?></td>
					    <td class="author column-author"><?php echo $admin_only ?></td>
					    <td class="author column-author"><?php echo $sequence ?></td>
					    </tr>
					<?php
				    }
				}
			    }
			    ?>
		    </tbody>
		</table>
		<div>
			<p><input type="checkbox" name="sAll" onclick="selectAll(this)" class="select_checkbox" />
				<strong>
					<?php _e('Check All','voting-contest'); ?>
				</strong>
				<input type="hidden" name="action" value="delete_fields" />
				<input style="margin-left: 10px;" name="delete_question" type="submit" class="button-secondary" id="delete_question" value="<?php _e('Delete Field','voting-contest'); ?>" style="margin-left:10px 0 0 10px;" onclick="return confirmDelete('multiple');">
				<a  style="margin-left:5px"class="button-primary" href="admin.php?page=fieldregistration&amp;action=new_question"><?php _e('Add New Field','voting-contest'); ?></a>
			</p>
		</div>
	    </form>
	    <?php
	    $main_post_content = ob_get_clean();
	    echo $main_post_content;
	    ?>							
	    </div>
	    <script type="text/javascript">
	    jQuery(document).ready(function($) {
		// Add new question or question group form validation
		jQuery('#new-question-form').validate({
		    rules: {
			question: "required",
			values: "required",
			sequence:{number: true}
		    },
		    messages: {
			question: "<?php _e('Please add a field name','voting-contest'); ?>",
			values: "<?php _e('Please add a list of values for the field','voting-contest'); ?>",
			sequence:{number: "Please enter the numeric values"}
            
		    }
		});
		      
		/* show the table data */
		var mytable = jQuery('#table').dataTable( {
		    "bStateSave": true,
		    "sPaginationType": "full_numbers",
		    "fnDrawCallback": function( oSettings ) {
			  jQuery('.question_checkbox').attr('checked',false);
			  if(jQuery('.select_checkbox').is(':checked')){
			    jQuery('.select_checkbox').attr('checked',false);
			  }
		    },
		    
		    "oLanguage": {	"sSearch": "<strong><?php _e('Live Search Filter','voting-contest'); ?>:</strong>",
			    "sZeroRecords": "<?php _e('No Records Found!','voting-contest'); ?>" },
		    "aoColumns": [
			    { "bSortable": false },
			    null,
			    null,
			    null,
			    null,
			    null,
			    null
		    ]
    
		});
    
		var startPosition;
		var endPosition;
		jQuery("#table tbody").sortable({
		    cursor: "move",
		    start:function(event, ui){
			    startPosition = ui.item.prevAll().length + 1;
		    },
		    update: function(event, ui) {
			    endPosition = ui.item.prevAll().length + 1;
			    //alert('Start Position: ' + startPosition + ' End Position: ' + endPosition);
			    var row_ids="";
			    jQuery('#table tbody input[name="row_id"]').each(function(i){
				    row_ids= row_ids + ',' + jQuery(this).val();
			    });
			    jQuery.post(VOTES_LINKS.ajaxurl, { action: "update_sequence", row_ids: row_ids, update_sequence: "true"} );
		    }
		});
			postboxes.add_postbox_toggles('form_builder');
	    });
						    
	    // Remove li parent for input 'values' from page if 'text' box or 'textarea' are selected
	    var selectValue = jQuery('select#question_type option:selected').val();
	    // hide values field on initial page view
	    if(selectValue == 'TEXT' || selectValue == 'TEXTAREA' || selectValue == 'DATE'){
		jQuery('#add-question-values').hide();
		// we don't want the values field trying to validate if not displayed, remove its name
		jQuery('#add-question-values td input').attr("name","notrequired") 
	    }
					    
	    jQuery('select#question_type').bind('change', function() {
		var selectValue = jQuery('select#question_type option:selected').val();     
		if (selectValue == 'TEXT' || selectValue == 'TEXTAREA' || selectValue == 'DATE') {
			jQuery('#add-question-values').fadeOut('slow');
			// we don't want the values field trying to validate if not displayed, remove its name
			jQuery('#add-question-values td input').attr("name","notrequired") 
		} else{
			jQuery('#add-question-values').fadeIn('slow');
			// add the correct name value back in so we can run validation check.
			jQuery('#add-question-values td input').attr("name","values");		    
		}
	    });
    
	    //Select All
	    function selectAll(x) {
	       if(x.checked){
		for(var i=0,l=x.form.length; i<l; i++)
		if(x.form[i].type == 'checkbox' && x.form[i].name != 'sAll')
		x.form[i].checked=true
		}else{
		  for(var i=0,l=x.form.length; i<l; i++)
		  if(x.form[i].type == 'checkbox' && x.form[i].name != 'sAll')
		    x.form[i].checked=false
		}
	    }
	    
	    function confirmDelete(seld){
		if(seld=='multiple'){
			if(jQuery('.question_checkbox').is(':checked')){
			 if (confirm('<?php _e("Are you sure want to delete?","voting-contest"); ?>')){
				  return true;
				}
			 }else{
				alert('<?php _e("Select atleast one field to delete!","voting-contest"); ?>');
			 }
	    return false;
		}else{return true;}
	    }
			    
	    </script>
	 <?php 
    } 
}
    
