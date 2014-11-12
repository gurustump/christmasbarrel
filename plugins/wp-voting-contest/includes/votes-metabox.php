<?php
add_action('admin_menu', 'votes_meta_box');
add_action('add_meta_boxes', 'wp_votes_add_contestants_metaboxes');
add_action( 'admin_footer', 'votes_admin_clear_errors' );
add_action( 'admin_notices', 'votes_admin_notice_handler' ); 
add_action('save_post', 'wp_votes_save_custom_details' );
/*
 *  Displays the votes polled for the particular post in admin end.
 */
if (!function_exists('votesstatus_meta_box')) {
    function votesstatus_meta_box() {
        global $post,$wpdb;
        
        $new_sql = "SELECT SUM(votes)  FROM " . VOTES_TBL ." WHERE post_id =".$post->ID;
        $cnt     =  $wpdb->get_var($new_sql);             
        
        ?>
        <h1> <?php echo  $cnt ? $cnt.' ' : '0'.' '; _e('Votes','voting-contest'); ?> </h1> 
        <?php $cnt = ($cnt == null)?0:$cnt; ?>
        <input type="hidden" value="<?php echo $cnt; ?>" name="votes_counter" />
        <?php
    }
}
if(!function_exists('votesexpiration_meta_box')){
    function votesexpiration_meta_box(){
        global $post;
        $exp = get_post_meta($post->ID, VOTES_EXPIRATIONFIELD);
        /*$date = DateTime::createFromFormat('Y-m-d H:i:s', $exp[0]);
        echo '->>'.$date->format('m-d-Y H:i:s'); 
        echo $exp[0].'==='.strtotime('03-27-2012').'<<';
        echo $exp[0].strtotime('2012-03-27 00:00:00').'<<';*/
        if($exp[0] == '0' || !$exp){
            $votexpiration = $exp[0];
        }else{
            $votexpiration = date('m-d-Y H:i', strtotime( str_replace('-', '/',$exp[0] )) );
        }
        ?>
            <?php _e('Select the Expiration Date  ','voting-contest'); ?><input type="text" readonly="readonly" name="votes_expiration" id="votes_expiration" value="<?php echo $votexpiration; ?>"/> 
            <input type="hidden" value="<?php echo $exp[0]; ?>" name="votes_exp_stored" id="votes_exp_stored"/>
            <p><i>Note: Set to 0 (or) Click <input class="button" type="button" name="no_expiration" id="no_expiration" value="<?php _e('No Expiry','voting-contest'); ?>"/> <?php _e('for no expiration','voting-contest'); ?></i></p>
        <?php
    }
}
if (!function_exists('votes_meta_box')) {
    function votes_meta_box() {
        add_meta_box('votesstatus', __('Votes For this Contestant','voting-contest'), 'votesstatus_meta_box', VOTES_TYPE, 'normal', 'high');
        /* add_meta_box('votesexpiration', __('Expiration For this Contestant'), 'votesexpiration_meta_box', VOTES_TYPE, 'normal', 'high');*/
    }
}
if(!function_exists('wp_votes_add_contestants_metaboxes')){
    function wp_votes_add_contestants_metaboxes()
    {
      add_meta_box('votecustomfields', __('Custom Fields','voting-contest'), 'wp_votes_add_contestants_customfield_metaboxes', VOTES_TYPE, 'normal', 'high');
    }
}
if (!function_exists('wp_votes_add_contestants_customfield_metaboxes')) {
    function wp_votes_add_contestants_customfield_metaboxes() {
        global $post,$wpdb;
            wp_register_style('votesadmin', VOTES_PATH . 'css/admin-styles.css');
            wp_enqueue_style('votesadmin');
        $sql = "SELECT * FROM " . VOTES_ENTRY_CUSTOM_TABLE . " where delete_time = 0 order by sequence";
        $questions = $wpdb->get_results($sql);
        $sql1 = "SELECT * FROM " . VOTES_POST_ENTRY_TABLE . " WHERE post_id_map  = '".$post->ID."'";
        $custom_entries = $wpdb->get_results($sql1);
        if(!empty($custom_entries)){
            $field_values = $custom_entries[0]->field_values;
            $field_val = unserialize($field_values);  
        }
        if(!empty($questions)){
        foreach($questions as $custom_fields){
            if($custom_fields->system_name != "contestant-desc"):
        ?>
        <div class="contestants-row">
            <div class="contestants-label">
                <label> <?php 
                    if($custom_fields->question_type=='TEXT' || $custom_fields->question_type=='TEXTAREA'){
                        echo 'Enter the '.$custom_fields->question;
                    }else{
                       echo 'Select the '.$custom_fields->question; 
                    }?>
                <?php if($custom_fields->required=='Y'){
                       $class = "required_post_entries";     
                ?>
                      <span class="required-mark">*</span></label>
                <?php }else{$class='';} ?>
            </div>
            <div class="contestants-field">
            <?php if($custom_fields->question_type=='TEXT'){ ?>
                <input style="width: 35%;" class="<?php echo $class; ?>" type="<?php echo $custom_fields->question_type; ?>" id="<?php echo $custom_fields->system_name; ?>" value="<?php echo $field_val[$custom_fields->system_name]; ?>" name="<?php echo $custom_fields->system_name; ?>" />
                 <input type="hidden" value="<?php echo ($custom_fields->required_text!='')?$custom_fields->required_text:$custom_fields->question.' Field Required' ?>" class="val_<?php echo $custom_fields->system_name; ?>" />   
            <?php }elseif($custom_fields->question_type=='TEXTAREA'){  ?>
                    <textarea class="<?php echo $class; ?>" style="width: 35%;" rows="1" id="<?php echo $custom_fields->system_name; ?>" name="<?php echo $custom_fields->system_name; ?>" ><?php echo $field_val[$custom_fields->system_name]; ?></textarea> 
                <input type="hidden" value="<?php echo ($custom_fields->required_text!='')?$custom_fields->required_text:$custom_fields->question.' Field Required' ?>" class="val_<?php echo $custom_fields->system_name; ?>" /> 
            <?php }elseif($custom_fields->question_type=='SINGLE'){  ?>
                  <?php $values = explode(',',$custom_fields->response); 
                    foreach($values as $val){
                  ?>
                    <span id="add_contestant_radio"> 
                    <input  class="reg_radio_<?php echo $custom_fields->system_name; ?>  <?php echo $class; ?>" class="stt_float"  type="radio" <?php if(is_array($field_val[$custom_fields->system_name]) || $field_val[$custom_fields->system_name]==$val){if(in_array($val,$field_val[$custom_fields->system_name])||$field_val[$custom_fields->system_name]==$val){echo "checked";}} ?> name="<?php echo $custom_fields->system_name; ?>[]" value="<?php echo $val; ?>" id="<?php echo $custom_fields->system_name; ?>" /> <span class="question_radio <?php echo $custom_fields->system_name; ?>" ><?php echo $val; ?></span>
                    </span>
                      <input type="hidden" value="<?php echo ($custom_fields->required_text!='')?$custom_fields->required_text:$custom_fields->question.' Field Required' ?>" class="val_<?php echo $custom_fields->system_name; ?>" /> 
                  <?php } ?>
            <?php  }elseif($custom_fields->question_type=='MULTIPLE'){  ?>
                  <?php $values = explode(',',$custom_fields->response); 
                  foreach($values as $val){
                    ?>
                  <span id="add_contestant_radio"> 
                  <input type="checkbox" class="<?php echo $class; ?> reg_check_<?php echo $custom_fields->system_name; ?>" <?php if(is_array($field_val[$custom_fields->system_name]) || $field_val[$custom_fields->system_name]==$val){if(in_array($val,$field_val[$custom_fields->system_name]) || $field_val[$custom_fields->system_name]==$val){echo "checked";}} ?> name="<?php echo $custom_fields->system_name; ?>[]" value="<?php echo $val; ?>" id="<?php echo $custom_fields->system_name; ?>" />
                  <span class="question_check <?php echo $custom_fields->system_name; ?>" ><?php echo $val; ?></span>  </span>
                  <input type="hidden" value="<?php echo ($custom_fields->required_text!='')?$custom_fields->required_text:$custom_fields->question.' Field Required' ?>" class="val_<?php echo $custom_fields->system_name; ?>" /> 
            <?php } ?>
            <?php  } elseif($custom_fields->question_type=='DROPDOWN'){  ?>
            <?php $values = explode(',',$custom_fields->response); ?>
                    <select style="width: 35%;padding:1px;" name="<?php echo $custom_fields->system_name; ?>" class="<?php echo $class; ?>" id="<?php echo $custom_fields->system_name; ?>">
                    <option value="">Select</option>
                    <?php foreach($values as $val){ ?>
                          <option value="<?php echo $val; ?>" <?php echo ($field_val[$custom_fields->system_name]==$val)?'Selected':''; ?> ><?php echo $val; ?></option>
                    <?php } ?>
                    </select> 
                    <input type="hidden" value="<?php echo ($custom_fields->required_text!='')?$custom_fields->required_text:$custom_fields->question.' Field Required' ?>" class="val_<?php echo $custom_fields->system_name; ?>" /> 
            <?php } ?>
       </div>
    </div>
        <?php
            endif;
            }
        }else{
            echo "No Custom Fields Added Yet!";
        }
    }
}
// Display any errors
if(!function_exists('votes_admin_notice_handler')){
    function votes_admin_notice_handler() {
        $errors = get_option('my_admin_errors');
        if($errors) {
            foreach ($errors->get_error_codes() as $errcode) {
                    echo '<div class="error"><p>'.$errors->get_error_message($errcode) . '</p></div>';
            }
        }   
    }
}
if(!function_exists('votes_admin_clear_errors')){
    function votes_admin_clear_errors() {
        update_option('my_admin_errors', false);
    }
}
if(!function_exists('wp_votes_save_custom_details')){
function wp_votes_save_custom_details( $post_id ) {   
    global $post,$wpdb; 

    if($_SERVER[ 'HTTP_X_REQUESTED_WITH' ]!='XMLHttpRequest'){
        $postsql = "SELECT ID FROM " .$wpdb->prefix.'posts'. " where post_type = '".VOTES_TYPE."' AND ID=".$post_id;
        $contestant_post = $wpdb->get_results($postsql);
        if(!empty($contestant_post)){
            $sql = "SELECT * FROM " . VOTES_ENTRY_CUSTOM_TABLE . " where delete_time = 0 AND system_name != 'contestant-desc'";
            $questions = $wpdb->get_results($sql);
             $error = new WP_Error(); 
            if(!empty($questions)){
                $posted_val=array();
                foreach($questions as $custom_fields){
                   $posted_val[$custom_fields->system_name]=$_POST[$custom_fields->system_name]; 
                    if($custom_fields->required=='Y'){ 
                        if($_POST[$custom_fields->system_name]==''){
                        $error_msg = ($custom_fields->required_text!='')?$custom_fields->required_text:$custom_fields->question.'Field required';
                        //$error->add('Invalid '.$custom_fields->system_name, '<strong>Error</strong> : '.$error_msg);                                                 
                        }
                    }  
                }
            }   
            if (count($error->get_error_codes())) { 
                if($_POST['action']=='editpost'){
                 //update_option('my_admin_errors', $error);  
                }     
                //return; 
            }
            $val_serialized = serialize($posted_val);
            $sql1 = "SELECT * FROM " . VOTES_POST_ENTRY_TABLE . " WHERE post_id_map = '" . $post_id . "'";
            $field_val = $wpdb->get_results($sql1);
            if(!empty($field_val)){
                $wpdb->query("UPDATE " . VOTES_POST_ENTRY_TABLE . " SET field_values = '" . $val_serialized . "' WHERE post_id_map = '" . $post_id . "'");
            }else{
              if (array_filter($posted_val)) {
                $wpdb->query("INSERT INTO " . VOTES_POST_ENTRY_TABLE . " (post_id_map,field_values)". " VALUES ('".$post_id."', '".$val_serialized. "')"); 
              }  
            }
            //skip auto save
            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
                return $post_id;
            }
            //check for you post type only
            if( $post->post_type == "homepage" ) {
                if( isset($_POST['link_homepage']) ) { update_post_meta( $post->ID, 'link_homepage', $_POST['link_homepage'] );}
            }
        }//Check post contestestant
    }
}
}

if(!function_exists('votes_delete_custom_fields_entry')){
    function votes_delete_custom_fields_entry($post_id){
        global $wpdb;
        $wpdb->query("DELETE FROM " . VOTES_POST_ENTRY_TABLE . " WHERE post_id_map = '" . $post_id . "'");
    }
}
add_action( 'before_delete_post', 'votes_delete_custom_fields_entry' );


/*function remove_quick_edit( $actions ) {
unset($actions['inline hide-if-no-js']);
return $actions;
}
add_filter('post_row_actions','remove_quick_edit',10,1);
 */
