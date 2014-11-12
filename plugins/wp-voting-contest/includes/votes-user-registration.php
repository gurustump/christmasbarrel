<?php
session_start();
if(!function_exists('votes_custom_registration_fields_show')){
    
    function votes_custom_registration_fields_show(){
                     
	if (!isset($_SESSION['vote_login_function_ran']) || $_SESSION['vote_login_function_ran'] != '1') {
	    $_SESSION['vote_login_function_ran'] = '1';
	}else{
	    return;
	}
		wp_localize_script( 'znscript', 'zn_do_login', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		$current_url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; 

    ?>      
                               		
    	<div class="login_register_stuff hide">
    		<div id="login_panel">
    			<div class="inner-container login-panel">
    			 <?php // wp_login_form(  ); ?> 
    				<h3 class="m_title"><?php _e("YOU MUST BE REGISTERED AND LOGGED TO CONTINUE",'voting-contest');?></h3>
    					
    				<form id="login_form" name="login_form" method="post" class="zn_form_login" action="<?php echo site_url('wp-login.php', 'login_post') ?>">
    				
    					<?php if( get_option('users_can_register') ) { ?>
    						<a href="#" class="create_account" onClick="ppOpen('#register_panel', '450');"><?php _e("CREATE ACCOUNT",'voting-contest');?></a>
    					<?php } ?>
    					
    					<input type="text" id="username" name="log" class="inputbox" placeholder="<?php _e("Username",'voting-contest');?>">
    					<input type="password" id="password" name="pwd" class="inputbox" placeholder="<?php _e("Password",'voting-contest');?>">
    					<?php do_action('login_form');?>
    					<label class="zn_remember"><input type="checkbox" name="rememberme" id="rememberme" value="forever"><?php _e(" Remember Me",'voting-contest');?></label>
    					<input type="submit" id="login" name="submit_button" class="zn_sub_button" value="<?php _e("LOG IN",'voting-contest');?>">
    					<input type="hidden" value="login" class="" name="zn_form_action">
    					<input type="hidden" value="zn_do_login" class="" name="action">
    					<input type="hidden" value="<?php echo $current_url; ?>" class="zn_login_redirect" name="submit">
    					<div class="links"><a href="#" onClick="ppOpen('#forgot_panel', '350');"><?php _e("FORGOT YOUR PASSWORD?",'voting-contest');?></a></div>
    				</form>
                    <?php 
                    $votes_settings = get_option(VOTES_SETTINGS);                               
                    if($votes_settings['facebook_login'] == 'on'):
                        if(!is_user_logged_in()):    
                        ?>  
                        <div class="voting_facebook_login">                  
                            <input type="hidden" name="vote_fb_appid" id="vote_fb_appid" value="<?php echo $votes_settings['vote_fb_appid']; ?>" />
                            <fb:login-button scope="public_profile,email" onlogin="checkLoginState();" class="fb_login_button">
                            </fb:login-button>
                            
                            <div id="status">
                            </div>
                        </div>
                        <?php endif; ?>
                    <?php endif; ?>                    
                    <?php
                    if($votes_settings['twitter_login'] == 'on'):
                        if(!is_user_logged_in()):                                      
                        ?>  
                        <div class="voting_twitter_login">                  
                            <input type="hidden" name="vote_tw_appid" id="vote_tw_appid" value="<?php echo $votes_settings['vote_tw_appid']; ?>" />     
                            <input type="hidden" name="vote_tw_secret" id="vote_tw_secret" value="<?php echo $votes_settings['vote_tw_secret']; ?>" />  
                            <input type="hidden" name="current_callback_url" id="current_callback_url" value="<?php echo get_permalink(); ?>" /> 
                            <a href="javascript:voting_save_twemail_session()" title="<?php _e('Sign in with Twitter','voting-contest'); ?>">
                                <img src="<?php echo VOTES_PATH.'images/sign-in-with-twitter-gray.png'; ?>" border="0" />
                            </a>
                                                                             
                        </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <div class="clear"></div>
    
    			</div>
    		</div><!-- end login panel -->
            
            <?php
            if($votes_settings['twitter_login'] == 'on'):
                if(!is_user_logged_in()):   
            ?>
            <div id="twitter_register_panel">
                <div class="inner-container forgot-panel">
                <h3 class="m_title"><?php _e("ENTER YOUR EMAIL ADDRESS",'voting-contest');?></h3>
    					
    			<form id="twitter_save_form" name="twitter_save_form" method="post" class="zn_form_save_email">
    					<p>
    						<input type="text" id="user_email_save" name="user_login" class="inputbox" placeholder="<?php _e("E-mail",'voting-contest');?>"/>
    					</p>                                               
    					<p>
    						<input type="submit" id="check_email" name="submit" class="zn_sub_button" value="<?php _e("CONTINUE",'voting-contest');?>">
    					</p>    					
   				</form>
                
                <div class="links"><a href="#" onClick="ppOpen('#login_panel', '800');"><?php _e("RETURN BACK!",'voting-contest');?></a></div>
                
                </div>
            </div>
                <?php endif; ?>
            <?php endif;?>
    
    
    
    		<?php if( get_option('users_can_register') ) { ?>
    			<div id="register_panel">
    				<div class="inner-container register-panel">
    					<h3 class="m_title"><?php _e("CREATE ACCOUNT",'voting-contest');?></h3>
    					<form id="register_form" name="login_form" method="post" class="zn_form_login" action="<?php echo site_url('wp-login.php?action=register', 'login_post') ?>">
                            <div class="register-panel_inner">
                            <label><strong><?php _e('Username','voting-contest'); ?></strong><span class="required-mark">*</span></label>
    						<p>
    							<input type="text" id="reg-username" name="user_login" class="inputbox required_vote_custom" placeholder="<?php _e("Username",'voting-contest');?>" />
    						</p>
                            </div>
                            
                            <div class="register-panel_inner">
                            <label><strong><?php _e('Email Address','voting-contest'); ?></strong><span class="required-mark">*</span></label>
    						<p>
    							<input type="text" id="reg-email" name="user_email" class="inputbox required_vote_custom" placeholder="<?php _e("Your email",'voting-contest');?>" />
    						</p>
                            </div>
                            
                            <div class="register-panel_inner">
                            <label><strong><?php _e('Password','voting-contest'); ?></strong><span class="required-mark">*</span></label>
    						<p>
    							<input type="password" id="reg-pass" name="user_password" class="inputbox required_vote_custom" placeholder="<?php _e("Your password",'voting-contest');?>" />
    						</p>
                            </div>
                            
                            <div class="register-panel_inner">
                            <label><strong><?php _e('Confirm Password','voting-contest'); ?></strong><span class="required-mark">*</span></label>
    						<p>
    							<input type="password" id="reg-pass2" name="user_password2" class="inputbox required_vote_custom" placeholder="<?php _e("Verify password",'voting-contest');?>" />
    						</p>
                             </div>
                             <?php 
                            global $wpdb; 
                            $sql = "SELECT * FROM " . VOTES_USER_CUSTOM_TABLE . " WHERE admin_only  = 'Y' AND delete_time=0 order by sequence ";
                            $questions = $wpdb->get_results($sql);   
                           
                        ?>
                        
        <!-- Custom Fields -->                    
        <!-- Front end contestant custom fields code added -->  
        <?php
        if(!empty($questions)){
        foreach($questions as $custom_fields){
                         
        
        ?>
         <div class="register-panel_inner">
         <?php if($custom_fields->question_type=='SINGLE' || $custom_fields->question_type=='MULTIPLE'){
              $vals = '';$design='style="margin-top:2px;"';
         }else{$vals='';$design='';} ?>
         <label <?php echo $design; ?>><strong><?php echo $vals.$custom_fields->question; ?></strong>
         <?php if($custom_fields->required=='Y') {?>
         <span class="required-mark">*</span>
         <?php } ?>
         </label>
        <p>
            <?php 
            if($custom_fields->required=='Y'){$class="required_vote_custom";}else{$class="";} 
            if($custom_fields->question_type=='TEXT'){ ?>
            <input id="<?php echo $custom_fields->system_name; ?>" type="<?php echo strtolower($custom_fields->question_type); ?>" class="inputbox <?php echo $class; ?>" name="<?php echo $custom_fields->system_name; ?>" placeholder="<?php _e($custom_fields->question);?>"/>
            
            <?php } elseif($custom_fields->question_type=='TEXTAREA'){  ?>
        
                <textarea style="width: 100%;" rows="1" id="<?php echo $custom_fields->system_name; ?>" placeholder="<?php _e($custom_fields->question);?>" name="<?php echo $custom_fields->system_name; ?>" class="<?php echo $class; ?>" ></textarea>
        
        <?php }elseif($custom_fields->question_type=='SINGLE'){  ?>
        <?php $values = explode(',',$custom_fields->response); 
        foreach($values as $val){
        ?>   
        <span id="add_contestant_radio"> 
        <input class="<?php echo $class; ?> reg_radio_<?php echo $custom_fields->system_name; ?>" type="radio" name="<?php echo $custom_fields->system_name; ?>[]" value="<?php echo $val; ?>" id="<?php echo $custom_fields->system_name; ?>" /> <span class="question_radio <?php echo $custom_fields->system_name; ?>" ><?php echo $val; ?></span>
        </span> 
        
        <?php } ?> 
        <?php  }elseif($custom_fields->question_type=='MULTIPLE'){  ?>
        <?php $values = explode(',',$custom_fields->response); 
        foreach($values as $val){ ?>
        
        <span id="add_contestant_radio"> 
        <input class="<?php echo $class; ?> reg_check_<?php echo $custom_fields->system_name; ?>" type="checkbox"  name="<?php echo $custom_fields->system_name; ?>[]" value="<?php echo $val; ?>" id="<?php echo $custom_fields->system_name; ?>" />
        <span class="question_check <?php echo $custom_fields->system_name; ?>" ><?php echo $val; ?></span>  </span> 
        <?php }?>      
        <?php  } elseif($custom_fields->question_type=='DROPDOWN'){  ?>
        
        <?php $values = explode(',',$custom_fields->response); ?>
        <select class="<?php echo $class; ?>" style="width: 100%;padding: 0.428571rem;border: 1px solid #CCCCCC;border-radius: 3px 3px 3px 3px;" name="<?php echo $custom_fields->system_name; ?>" id="<?php echo $custom_fields->system_name; ?>">
        <option value=""><?php _e('Select','voting-contest'); ?></option>
        <?php foreach($values as $val){ ?>
              <option value="<?php echo $val; ?>"><?php echo $val; ?></option>
        <?php } ?>
        </select> 
        
        <?php } ?>                                 			
        </p></div>
        <?php    
        
        } 
        }
        ?>    
        
        <!-- Front end contestant custom fields code added -->     

                            

	    <p>
		    <input type="submit" id="signup" name="submit" class="zn_sub_button" value="<?php _e("CREATE MY ACCOUNT",'voting-contest');?>"/>
	    </p>
	    <input type="hidden" value="register" class="" name="zn_form_action">
	    <input type="hidden" value="zn_do_login" class="" name="action">
	    <input type="hidden" value="<?php echo $current_url; ?>" class="zn_login_redirect" name="submit">
	    <div class="links"><a href="#" onClick="ppOpen('#login_panel', '800');"><?php _e("ALREADY HAVE AN ACCOUNT?",'voting-contest');?></a></div>
	</form>
    					
    				</div>
    			</div><!-- end register panel -->
    		<?php } ?>
    		
            
            
    		<div id="forgot_panel">
    			<div class="inner-container forgot-panel">
    				<h3 class="m_title"><?php _e("FORGOT YOUR DETAILS?",'voting-contest');?></h3>
    				<form id="forgot_form" name="login_form" method="post" class="zn_form_lost_pass" action="<?php echo site_url('wp-login.php?action=lostpassword', 'login_post') ?>">
    					<p>
    						<input type="text" id="forgot-email" name="user_login" class="inputbox" placeholder="<?php _e("Username or E-mail",'voting-contest');?>"/>
    					</p>
                                               
    					<p>
    						<input type="submit" id="recover" name="submit" class="zn_sub_button" value="<?php _e("SEND MY DETAILS!",'voting-contest');?>">
    					</p>
    					<div class="links"><a href="#" onClick="ppOpen('#login_panel', '800');"><?php _e("AAH, WAIT, I REMEMBER NOW!",'voting-contest');?></a></div>
    				</form>
    				
    			</div>
    		</div><!-- end register panel -->
    	</div><!-- end login register stuff -->
<?php
    }
    
}

//On user registration add the fields in db
if(!function_exists('votes_register_extra_fields')){
  function votes_register_extra_fields($user_id){
    global $wpdb; 

    if ( ! is_super_admin() ) {
        $sql = "SELECT * FROM " . VOTES_USER_CUSTOM_TABLE . " WHERE admin_only  = 'Y' AND delete_time=0";
    }else{
        $sql = "SELECT * FROM " . VOTES_USER_CUSTOM_TABLE . " WHERE delete_time=0";
    }
    
    $questions = $wpdb->get_results($sql);   
    $error = new WP_Error();
     if(!empty($questions)){
            $posted_val=array();
            foreach($questions as $custom_fields){
               $posted_val[$custom_fields->system_name]=$_POST[$custom_fields->system_name];  
            }
        }

    $val_serialized = serialize($posted_val);
    $sql1 = "SELECT * FROM " . VOTES_USER_ENTRY_TABLE. " WHERE user_id_map = '".$user_id."'";
    $registered_entries = $wpdb->get_results($sql1);
    if(!empty($registered_entries)){
         $wpdb->query("UPDATE " . VOTES_USER_ENTRY_TABLE . " SET field_values = '" . $val_serialized . "' WHERE user_id_map = '" . $user_id . "'");
    }else
    {
       $wpdb->query("INSERT INTO " . VOTES_USER_ENTRY_TABLE . " (user_id_map,field_values)". " VALUES ('".$user_id."', '".$val_serialized. "')");
        if (!is_user_logged_in()) {
            //determine WordPress user account to impersonate
            $user_login = 'guest';
    
           //get user's ID
            $user = get_userdatabylogin($user_login);
    
            //login
            wp_set_current_user($user_id, $user_login);
            wp_set_auth_cookie($user_id);
            do_action('wp_login', $user_login);
        }
    }
                                   
      
  }  
}
add_action('user_register', 'votes_register_extra_fields'); 
add_action('personal_options_update', 'votes_register_extra_fields');
add_action('edit_user_profile_update', 'votes_register_extra_fields');
 
 
add_filter('user_profile_update_errors', 'myplugin_registration_errors', 10, 2);

//Validation error for registration
function myplugin_registration_errors ($errors, $sanitized_user_login) {
    global $wpdb;
    if ( ! is_super_admin() ) {
        $sql = "SELECT * FROM " . VOTES_USER_CUSTOM_TABLE . " where admin_only  = 'Y' AND delete_time=0 ";  
    }else
        $sql = "SELECT * FROM " . VOTES_USER_CUSTOM_TABLE . " where delete_time=0 ";
    
    $questions = $wpdb->get_results($sql);
    
    $error = new WP_Error();
     if(!empty($questions)){
            $posted_val=array();
            foreach($questions as $custom_fields){
               $posted_val[$custom_fields->system_name]=$_POST[$custom_fields->system_name];  
               if($custom_fields->required=='Y'){ 
                    if($_POST[$custom_fields->system_name]==''){
                        $req_text = ($custom_fields->required_text!='')?$custom_fields->required_text:$custom_fields->question." Field required";
                       $errors->add('Invalid '.$custom_fields->question, '<strong>Error</strong> : '.$req_text);                                                 
                    }
               } 
            }
        }            
    return $errors;
}
     
  
//Admin side User profile enhancing with custom fields
if(!function_exists('voting_modify_contact_methods')){
function voting_modify_contact_methods($user) {   
    global $wpdb;
    if ( ! is_super_admin() ) {
    $sql = "SELECT * FROM " . VOTES_USER_CUSTOM_TABLE . " where admin_only  = 'Y' AND delete_time=0 ";
    }else
    $sql = "SELECT * FROM " . VOTES_USER_CUSTOM_TABLE . " where delete_time=0 ";
    
    $questions = $wpdb->get_results($sql);  
    $sql1 = "SELECT * FROM " . VOTES_USER_ENTRY_TABLE. " WHERE user_id_map = '".$user->ID."'";
    $registered_entries = $wpdb->get_results($sql1);
    if(!empty($registered_entries)){
        $registration = unserialize($registered_entries[0]->field_values);
    }else
    {
        $registration=array();
    }
  if(!empty($questions)){
if(current_filter()=='user_new_form_tag') echo ">";   
?> 

<h3><?php _e('Custom Registration Fields','voting-contest'); ?></h3> 
<table class="form-table">
        <?php
       
        foreach($questions as $custom_fields){            
            if($custom_fields->required=='Y'){$class="required_vote_custom";$span_man="<span style='color:red;'>*</span>";}
            else{$class="";$span_man="";} 
        ?>
            <tr>
                <th><label for="<?php echo $custom_fields->question; ?>"><?php echo $custom_fields->question; ?><?php echo $span_man; ?></label></th>
                <td>
                    <?php 
                    
                    if($custom_fields->question_type=='TEXT'){ ?>
            
                        <input id="<?php echo $custom_fields->system_name; ?>" type="<?php echo strtolower($custom_fields->question_type); ?>" class="inputbox <?php echo $class; ?>" name="<?php echo $custom_fields->system_name; ?>" placeholder="<?php _e($custom_fields->question);?>" value="<?php echo $registration[$custom_fields->system_name]; ?>" />
                        
                   <?php } elseif($custom_fields->question_type=='TEXTAREA'){  ?>
        
                        <textarea style="width: 20%;" rows="1" id="<?php echo $custom_fields->system_name; ?>" placeholder="<?php _e($custom_fields->question);?>" name="<?php echo $custom_fields->system_name; ?>" class="<?php echo $class; ?>" ><?php echo $registration[$custom_fields->system_name]; ?></textarea>
                
                   <?php }elseif($custom_fields->question_type=='SINGLE'){  ?>
                   <?php $values = explode(',',$custom_fields->response); 
                    foreach($values as $val){
                    ?>   
                    <span id="add_contestant_radio"> 
                    <input class="<?php echo $class; ?> custom_reg_radio" type="radio" name="<?php echo $custom_fields->system_name; ?>[]" value="<?php echo $val; ?>" id="<?php echo $custom_fields->system_name; ?>" <?php if(is_array($registration[$custom_fields->system_name])){if(in_array($val,$registration[$custom_fields->system_name])){echo "checked";}} ?> /> 
                    <span class="question_radio" style="margin-right:10px;" ><?php echo $val; ?></span></span> 
                    <?php } ?> 
                    <?php  }elseif($custom_fields->question_type=='MULTIPLE'){  ?>
                    <?php $values = explode(',',$custom_fields->response); 
                    foreach($values as $val){ ?>
                    <span id="add_contestant_radio"> 
                    <input class="<?php echo $class; ?> custom_reg_check" type="checkbox"  name="<?php echo $custom_fields->system_name; ?>[]" value="<?php echo $val; ?>" id="<?php echo $custom_fields->system_name; ?>" <?php if(is_array($registration[$custom_fields->system_name])){if(in_array($val,$registration[$custom_fields->system_name])){echo "checked";}} ?> />
                    <span style="margin-right:10px;" class="question_check" ><?php echo $val; ?></span>  </span> 
                    <?php }?>      
        <?php  } elseif($custom_fields->question_type=='DROPDOWN'){  ?>
        
                <?php $values = explode(',',$custom_fields->response); ?>
                <select class="<?php echo $class; ?>" style="width: 20%;" name="<?php echo $custom_fields->system_name; ?>" id="<?php echo $custom_fields->system_name; ?>">
                <option value="">Select</option>
                <?php foreach($values as $val){ ?>
                      <option value="<?php echo $val; ?>" 
                      <?php echo($registration[$custom_fields->system_name]==$val)?'selected="selected"':'';?> > 
                      <?php echo $val; ?></option>
                <?php } ?>
                </select> 
        <?php } ?>                                 			
        </td> 
        </tr>
        <?php            
        } 

if(current_filter()=='user_new_form_tag') echo "</table";
else{ 
?>      
</table>
<?php
    }
    }
 }
}


add_filter('show_user_profile', 'voting_modify_contact_methods');
add_action('edit_user_profile', 'voting_modify_contact_methods');    
add_action('user_new_form_tag', 'voting_modify_contact_methods');
add_action( 'delete_user', 'votes_delete_user_custom_entry' );

if(!function_exists('votes_delete_user_custom_entry')){
    function votes_delete_user_custom_entry($user_id){
        global $wpdb;
        $wpdb->query("DELETE FROM " . VOTES_USER_ENTRY_TABLE . " WHERE user_id_map = '" . $user_id . "'");
    }
}
   