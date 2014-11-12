<?php
$path =  wp_upload_dir();
if (isset($_POST['votes_settings'])) { 
	$votesexpiration = $_POST['votes_expiration'];
	if($votesexpiration != '0' && trim($votesexpiration)){
			$votesexpiration = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $votesexpiration)));
		
	}else{
		$votesexpiration = '0';
	}
	$votesstarttime = $_POST['votes_starttime'];
	if($votesstarttime != '0' && trim($votesstarttime)){
			$votesstarttime = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $votesstarttime)));
		
	}else{
		$votesstarttime = '0';
	}
    if($_FILES['facebook_image']['name']!=''){
        $paths = $_FILES['facebook_image']['name'];
        $ext = pathinfo($paths, PATHINFO_EXTENSION); 
        if($ext=='jpg' || $ext=='png' || $ext=='gif'){
        $name_facebook = $_FILES['facebook_image']['name'];
        move_uploaded_file($_FILES['facebook_image']['tmp_name'],$path['path'].'/'.$_FILES['facebook_image']['name']);
        }else{
            $error_image = 'Please upload the image in following formats (jpg, png, gif)';
        }
    }
    if($_FILES['twitter_image']['name']!=''){
        $paths = $_FILES['twitter_image']['name'];
        $ext = pathinfo($paths, PATHINFO_EXTENSION); 
        if($ext=='jpg' || $ext=='png' || $ext=='gif'){
        $name_twitter = $_FILES['twitter_image']['name'];
        move_uploaded_file($_FILES['twitter_image']['tmp_name'],$path['path'].'/'.$_FILES['twitter_image']['name']);
        }else{
            $error_image = 'Please upload the image in following formats (jpg, png, gif)';
        }
    }
    
    $value = $_POST['vote_admin_mail']; 
    $value = sanitize_email( $value );
    if($_POST['vote_admin_mail']!=''){
    if ( ! is_email( $value ) ) {
	$value = get_option( $option ); // Resets option to stored value in the case of failed sanitization
	if ( function_exists( 'add_settings_error' ) )
		$error_image =  'The email address entered did not appear to be a valid email address. Please enter a valid email address.';
	}
    }
    
			
    $args = array(
	'imgheight' => isset($_POST['vote_imgheight']) ? $_POST['vote_imgheight'] : '',
        'imgwidth' => isset($_POST['vote_imgwidth']) ? $_POST['vote_imgwidth'] : '',
	'short_cont_image' => isset($_POST['short_cont_image']) ? $_POST['short_cont_image'] : '',
        'page_cont_image' => isset($_POST['page_cont_image']) ? $_POST['page_cont_image'] : '',
        'imgdisplay' => isset($_POST['vote_imgdisplay']) ? $_POST['vote_imgdisplay'] : '',
	'title' => isset($_POST['vote_title']) ? $_POST['vote_title'] : '',
        'orderby' => isset($_POST['vote_orderby']) ? $_POST['vote_orderby'] : '',
        'order' => isset($_POST['vote_order']) ? $_POST['vote_order'] : '',
	'vote_sidebar' => isset($_POST['vote_sidebar']) ? $_POST['vote_sidebar'] : '',
	'vote_readmore'=>isset($_POST['vote_readmore']) ? $_POST['vote_readmore'] : '',
	
	'vote_notify_mail' => isset($_POST['vote_notify_mail']) ? $_POST['vote_notify_mail'] : '',
	'vote_admin_mail' => isset($_POST['vote_admin_mail']) ? $_POST['vote_admin_mail'] : '',
	'vote_from_name'=>isset($_POST['vote_from_name'])?$_POST['vote_from_name']:'',
        
	'termdisplay' => isset($_POST['vote_termdisplay']) ? $_POST['vote_termdisplay'] : '',
	'onlyloggedinuser' => isset($_POST['vote_onlyloggedinuser']) ? $_POST['vote_onlyloggedinuser'] : '',
	'vote_truncation_grid'=>isset($_POST['vote_truncation_grid']) ? $_POST['vote_truncation_grid'] : '',
	'vote_truncation_list'=>isset($_POST['vote_truncation_list']) ? $_POST['vote_truncation_list'] : '',
			
	'frequency' => isset($_POST['vote_frequency']) ? $_POST['vote_frequency'] : '',
	'vote_votingtype' => isset($_POST['vote_votingtype']) ? $_POST['vote_votingtype'] : '',		
	'deactivation' => isset($_POST['vote_deactivation']) ? $_POST['vote_deactivation'] : '',
	'vote_tobestarteddesc' => isset($_POST['vote_tobestarteddesc']) ? $_POST['vote_tobestarteddesc'] : '',
	'vote_reachedenddesc' => isset($_POST['vote_reachedenddesc']) ? $_POST['vote_reachedenddesc'] : '',
	'vote_entriescloseddesc' => isset($_POST['vote_entriescloseddesc']) ? $_POST['vote_entriescloseddesc'] : '',		
	'votes_timertextcolor' => isset($_POST['votes_timertextcolor']) ? $_POST['votes_timertextcolor'] : '',

        'vote_onlyloggedcansubmit' => isset($_POST['vote_onlyloggedcansubmit'])?$_POST['vote_onlyloggedcansubmit']:'',
        'vote_tracking_method' => isset($_POST['vote_tracking_method'])?$_POST['vote_tracking_method']:'',
        'facebook' => isset($_POST['vote_facebook']) ? $_POST['vote_facebook'] : '',
        'file_facebook' => isset($name_facebook) ? $name_facebook : $_POST['fb_uploaded_image'],
        'file_fb_default'=>isset($_POST['vote_facebook_default_img']) ? $_POST['vote_facebook_default_img'] : '',
        'facebook_login'=>isset($_POST['facebook_login']) ? $_POST['facebook_login'] : '',
        'vote_fb_appid'=>isset($_POST['vote_fb_appid']) ? $_POST['vote_fb_appid'] : '',        
        'twitter' => isset($_POST['vote_twitter']) ? $_POST['vote_twitter'] : '',
        'file_twitter' => isset($name_twitter) ? $name_twitter : $_POST['tw_uploaded_image'],
        'file_tw_default'=>isset($_POST['vote_twitter_default_img']) ? $_POST['vote_twitter_default_img'] : '',
        'twitter_login'=>isset($_POST['twitter_login']) ? $_POST['twitter_login'] : '',
        'vote_tw_appid'=>isset($_POST['vote_tw_appid']) ? $_POST['vote_tw_appid'] : '', 
        'vote_tw_secret'=>isset($_POST['vote_tw_secret']) ? $_POST['vote_tw_secret'] : '', 
        'votes_timerbgcolor' => isset($_POST['votes_timerbgcolor']) ? $_POST['votes_timerbgcolor'] : '',
        
        'vote_show_date_prettyphoto' => isset($_POST['vote_show_date_prettyphoto']) ? $_POST['vote_show_date_prettyphoto'] : '',        
	
	'vote_disable_jquery' => isset($_POST['disable_jquery']) ? $_POST['disable_jquery'] : '',
	'vote_disable_jquery_cookie' => isset($_POST['disable_jquery_cookie']) ? $_POST['disable_jquery_cookie'] : '',
	'vote_disable_jquery_fancy' => isset($_POST['disable_jquery_fancy']) ? $_POST['disable_jquery_fancy'] : '',
	'vote_disable_jquery_pretty' => isset($_POST['disable_jquery_pretty']) ? $_POST['disable_jquery_pretty'] : '',
	'vote_disable_jquery_validate' => isset($_POST['disable_jquery_validate']) ? $_POST['disable_jquery_validate'] : ''
	);
	
	if($error_image=='') {
		update_option(VOTES_SETTINGS, $args);
		update_option(VOTES_GENERALEXPIRATIONFIELD, $votesexpiration);
		update_option(VOTES_GENERALSTARTTIME, $votesstarttime);
	}
	
   if($error_image=='') 
    echo '<div style="line-height:40px;" class="updated">' . __('Settings Updated','voting-contest') . '</div>';
    else
    echo '<div style="line-height:40px;color:red;" class="updated">' . $error_image . '</div>';
}
	$option = get_option(VOTES_SETTINGS);
	$expoption = get_option(VOTES_GENERALEXPIRATIONFIELD);

	if($expoption == '0'){
		$votes_expiration = '';
	}else{			
		$votes_expiration = date('m-d-Y H:i', strtotime( str_replace('-', '/',$expoption )) );
	}
	$starttimeoption = get_option( VOTES_GENERALSTARTTIME);
	if($starttimeoption == '0'){
		$votes_starttime = '';
	}else{			
		$votes_starttime = date('m-d-Y H:i', strtotime( str_replace('-', '/',$starttimeoption )) );
	}
	
	
 function list_thumbnail_sizes(){
     global $_wp_additional_image_sizes;
     	$sizes = array();
 		foreach( get_intermediate_image_sizes() as $s ){
 			$sizes[ $s ] = array( 0, 0 );
 			if( in_array( $s, array( 'thumbnail', 'medium', 'large' ) ) ){
 				$sizes[ $s ][0] = get_option( $s . '_size_w' );
 				$sizes[ $s ][1] = get_option( $s . '_size_h' );
 			}else{
 				if( isset( $_wp_additional_image_sizes ) && isset( $_wp_additional_image_sizes[ $s ] ) )
 					$sizes[ $s ] = array( $_wp_additional_image_sizes[ $s ]['width'], $_wp_additional_image_sizes[ $s ]['height'], );
 			}
 		}
        $all_sizes = array();
 		foreach( $sizes as $size => $atts ){
 		     $all_sizes[$size] = $size . ' - ' .implode( 'x', $atts ); 			
 		}
        return $all_sizes;
 }
 
 $all_sizes = list_thumbnail_sizes();
 
	
?>
<div class="wrap">
    <h2><?php _e('Contest Settings','voting-contest'); ?></h2>
    <div class="narrow">
        <form action="" method="post" name="votes_settings_form" id="votes_settings_form" enctype="multipart/form-data">
            <h3><?php _e('Image Settings','voting-contest'); ?></h3>
            <table class="form-table"> 		
	    <tr valign="top">
		<th scope="row"><label for="short_cont_image"><?php _e('Shortcode Contest Image','voting-contest'); ?> </label></th>
		<td>
		<select class="size" data-user-setting="imgsize" data-setting="size" name="short_cont_image" id="short_cont_image">
		<?php foreach($all_sizes as $key=>$val): ?>
		<?php $selected = ($key == $option['short_cont_image'])?'selected':''; ?>
		<option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $val; ?></option>
		<?php endforeach;?>
		</select>
		</td>
	    </tr>
	    
	    <tr valign="top">
		<th scope="row"><label for="page_cont_image"><?php _e('Contestants Page Image','voting-contest'); ?> </label></th>
		<td>
		<select class="size" data-user-setting="imgsize" data-setting="size" name="page_cont_image" id="page_cont_image">
		<?php foreach($all_sizes as $key=>$val): ?>
		<?php $selected = ($key == $option['page_cont_image'])?'selected':''; ?>
		<option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $val; ?></option>
		<?php endforeach;?>
		</select>
		</td>
	    </tr>
	    

		
            </table>

            <h3><?php _e('Content Settings','voting-contest'); ?></h3>
            <table class="form-table"> 
	
		<tr  valign="top">
                    <th  scope="row"><label for="vote_notify_mail"><?php _e('Admin Notification','voting-contest'); ?> </label></th>
                    <td>
			<input type="checkbox" id="vote_notify_mail" name="vote_notify_mail"  <?php checked('on', $option['vote_notify_mail']); ?>/>
			       <span class="description"><?php _e('Admin notify on contestant entry submission.','voting-contest'); ?></span>
		    </td>
		</tr>
			
	
		<tr  valign="top">
                    <td  scope="row"><label for="vote_from_name"><?php _e('Email From Name','voting-contest'); ?> </label></th>
                    <td> <input type="text" id="vote_from_name" name="vote_from_name"  value="<?php echo $option['vote_from_name'] ?>"/><p class="description"><?php _e('Admin notification From name. Ex:YourName &lt;name@domain.com&gt;','voting-contest'); ?></p><p class="description"><?php _e('Note: Make sure that you entered "Email From Name" in the format <br/>YourName &lt;name@domain.com&gt;. Mail id is needed as in example <br/>Otherwise the mail may not be sent.
																														   ','voting-contest'); ?></p></td>
                </tr>
        

		<tr  valign="top">
                    <td  scope="row"><label for="vote_admin_mail"><?php _e('Notification E-Mail Id','voting-contest'); ?> </label></th>
                    <td> <input type="text" id="vote_admin_mail" name="vote_admin_mail"  value="<?php echo ($option['vote_admin_mail'])?$option['vote_admin_mail']:$_POST['vote_admin_mail']; ?>"/><p class="description"><?php _e('Admin notification E-mail Id','voting-contest'); ?></p><p class="description"><?php _e('Note: If no Email Id is set. Mail will be sent to admin email (Settings->General
																														   )','voting-contest'); ?></p></td>
                </tr>
        
		<tr  valign="top">
                    <td  scope="row"><label for="vote_title"><?php _e('Display Title','voting-contest'); ?> </label></th>
                    <td> <input type="text" id="vote_title" name="vote_title"  value="<?php echo $option['title'] ?>"/><p class="description"><?php _e('Title.','voting-contest'); ?></p></td>
                </tr>
        
	
	        <tr valign="top">
                    <th scope="row"><label for="vote_orderby"><?php _e('Order by','voting-contest'); ?></label> </th>
                    <td> 
                        <select id="vote_orderby" name="vote_orderby" >
                            <option value="author"<?php selected($option['orderby'], 'author'); ?>><?php _e('Author','voting-contest'); ?></option>
                            <option value="date"<?php selected($option['orderby'], 'date'); ?>><?php _e('Date','voting-contest'); ?></option>
                            <option value="title"<?php selected($option['orderby'], 'title'); ?>><?php _e('Title','voting-contest'); ?></option>
                            <option value="modified"<?php selected($option['orderby'], 'modified'); ?>><?php _e('Modified','voting-contest'); ?></option>
                            <option value="menu_order"<?php selected($option['orderby'], 'menu_order'); ?>><?php _e('Menu Order','voting-contest'); ?></option>
                            <option value="parent"<?php selected($option['orderby'], 'parent'); ?>><?php _e('Parent','voting-contest'); ?></option>
                            <option value="id"<?php selected($option['orderby'], 'id'); ?>><?php _e('ID','voting-contest'); ?> </option>
                            <option value="votes"<?php selected($option['orderby'], 'votes'); ?>><?php _e('Votes','voting-contest'); ?></option>
                        </select>
                        <input id="votes_order1" type="radio" value="asc" name="vote_order" <?php if ($option['order'] == 'asc')
        echo 'checked="checked"'; ?>/>
                        <label for="votes_order1"><?php _e('Asc','voting-contest'); ?></label>&nbsp;&nbsp;
                        <input id="votes_order0" type="radio" value="desc" name="vote_order" <?php if ($option['order'] == 'desc')
                                   echo 'checked="checked"'; ?>/>
                        <label for="votes_order0"><?php _e('Desc','voting-contest'); ?></label>

                    </td>
                </tr>
                
		<tr  valign="top">
                    <th  scope="row"><label for="vote_imgdisplay"><?php _e('Disable Sidebar','voting-contest'); ?> </label></th>
                    <td> <input type="checkbox" id="vote_sidebar" name="vote_sidebar"  <?php checked('on', $option['vote_sidebar']); ?>/> <span class="description"><?php _e('Disable Sidebar In Contestant Description Page.','voting-contest'); ?></span></td>
		</tr>
		
		<tr  valign="top">
                    <th  scope="row"><label for="vote_imgdisplay"><?php _e('Disable Read More Button','voting-contest'); ?> </label></th>
                    <td> <input type="checkbox" id="vote_readmore" name="vote_readmore"  <?php checked('on', $option['vote_readmore']); ?>/> <span class="description"><?php _e('Disable Read More Button In Contestant Page.','voting-contest'); ?></span></td>
		</tr>
						
		<tr  valign="top">
                    <th  scope="row"><label for="votes_timertextcolor"><?php _e('Votes Timer Text color','voting-contest'); ?> </label></th>
                    <td> 
					 <input type="text" maxlength="7" name="votes_timertextcolor" id="votes_timertextcolor" value="<?php  echo $option['votes_timertextcolor']; ?>" class="votes-color-field"/></td>
                </tr>
				<tr  valign="top">
                    <th  scope="row"><label for="votes_timerbgcolor"><?php _e('Votes Timer Background color','voting-contest'); ?> </label></th>
                    <td> 
					 <input type="text" maxlength="7" name="votes_timerbgcolor" id="votes_timerbgcolor" value="<?php  echo $option['votes_timerbgcolor']; ?>" class="votes-color-field"/></td>
                </tr>
            </table>
			<h3><?php _e('Contest Options','voting-contest'); ?></h3>
			<table class="form-table">
			 <tr  valign="top">
                    <th  scope="row"><label for="vote_onlyloggedinuser"><?php _e('Voting Permission','voting-contest'); ?> </label></th>
                    <td width="30%">
					
					<input type="checkbox" id="vote_onlyloggedinuser" name="vote_onlyloggedinuser"  <?php checked('on', $option['onlyloggedinuser']); ?>/> <span class="description"><?php _e('Must be logged in to Vote.','voting-contest'); ?></span>
				
					</td>
                    
                    <td>
					
					<input type="checkbox" id="vote_onlyloggedcansubmit" name="vote_onlyloggedcansubmit"  <?php checked('on', $option['vote_onlyloggedcansubmit']); ?>/> <span class="description"><?php _e('Must be logged in to submit entries.','voting-contest'); ?></span>
                        
					</td>
                    
                </tr>
                
             <tr  valign="top">
                    <th  scope="row"><label for="vote_tracking_method"><?php _e('Vote Tracking','voting-contest'); ?> </label></th>
                    <?php 
                    $vote_tracking_method = array(
                                                     'ip_traced'=>'IP Traced',
                                                    'cookie_traced'=>'Cookie Traced',                                                
                                            ); 
                    ?>
                    <td>					
					<select id="vote_tracking_method" name="vote_tracking_method"  <?php checked('on', $option['onlyloggedinuser']); ?>>
                        <?php foreach($vote_tracking_method as $key => $method): ?>
                            <?php $selected = ($key == $option['vote_tracking_method'])?'selected':''; ?>
                            <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $method; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <p class="description"><?php _e('Select how Votes will be Tracked when a User is not required to log in. IP Traced is the most secure!','voting-contest'); ?></p>				
					</td>                    
                    
                </tr>                
		
        	 
		
		
		
		<tr  valign="top">
                    <td  scope="row"><label for="vote_truncation_grid"><?php _e('Title Truncation grid view','voting-contest'); ?> </label></th>
                    <td> <input type="text" id="vote_truncation_grid" onkeypress="return isnumber(event);" name="vote_truncation_grid"  value="<?php echo $option['vote_truncation_grid'] ?>"/><p class="description"><?php _e('Limit the title characters show on contestant listing (grid view)','voting-contest'); ?></p></td>
                </tr>

		
		<tr  valign="top">
                    <td  scope="row"><label for="vote_truncation_list"><?php _e('Title Truncation list view','voting-contest'); ?> </label></th>
                    <td> <input type="text" id="vote_truncation_list" onkeypress="return isnumber(event);" name="vote_truncation_list"  value="<?php echo $option['vote_truncation_list'] ?>"/><p class="description"><?php _e('Limit the title characters show on contestant listing (List view)','voting-contest'); ?></p></td>
                </tr>
		
				
			 <tr  valign="top">
                    <th  scope="row"><label for="vote_frequency"><?php _e('Voting Frequency','voting-contest'); ?> </label></th>
                    <td>
					
					 <select id="vote_frequency" name="vote_frequency" >
                            <option value="0" <?php selected($option['frequency'], '0'); ?>><?php _e('No Limit','voting-contest'); ?></option>
                            <option value="12" <?php selected($option['frequency'], '12'); ?>><?php _e('Once every 12 Hours','voting-contest'); ?></option>
                            <option value="24" <?php selected($option['frequency'], '24'); ?>><?php _e('Once every 24 Hours','voting-contest'); ?></option>
                            <option value="1" <?php selected($option['frequency'], '1'); ?>><?php _e('Once per Calendar Day','voting-contest'); ?></option>
			    
			    <option value="11" <?php selected($option['frequency'], '11'); ?>><?php _e('Once per Category','voting-contest'); ?></option>
			                                
                        </select>
					<p class="description"><?php _e('Allows to change the Voting Frequency.','voting-contest'); ?></p>
					</td>
                </tr>
				 <tr  valign="top">
                    <th  scope="row"><label for="vote_votingtype"><?php _e('User Can Vote For:','voting-contest'); ?> </label></th>
                    <td>
					
					<input type="checkbox" id="vote_votingtype" name="vote_votingtype"  <?php checked('on', $option['vote_votingtype']); ?>/>
                        
					<p class="description"><?php _e('Select for Single Contestant<br />Unselect for All Contestants','voting-contest'); ?></p>
					</td>
                </tr>
		<tr  valign="top">
                    <td  scope="row"><label for="vote_tobestarteddesc"><?php _e('To Be Started Description','voting-contest'); ?> </label></th>
                    <td> <input type="text" id="vote_tobestarteddesc" name="vote_tobestarteddesc"  value="<?php echo $option['vote_tobestarteddesc'] ?>"/><p class="description"><?php _e('Start time Description.','voting-contest'); ?></p></td>
                </tr>
				 <tr  valign="top">
                    <td  scope="row"><label for="vote_reachedenddesc"><?php _e('Closed Description','voting-contest'); ?> </label></th>
                    <td> <input type="text" id="vote_reachedenddesc" name="vote_reachedenddesc"  value="<?php echo $option['vote_reachedenddesc'] ?>"/><p class="description"><?php _e('Closed Description.','voting-contest'); ?></p></td>
                </tr>
               <tr  valign="top">
                    <td  scope="row"><label for="vote_entriescloseddesc"><?php _e('Entries Closed Description','voting-contest'); ?> </label></th>
                    <td> <input type="text" id="vote_entriescloseddesc" name="vote_entriescloseddesc"  value="<?php echo $option['vote_entriescloseddesc'] ?>"/><p class="description"><?php _e('Entries Closed Description.','voting-contest'); ?></p></td>
                </tr>
            </table>
			<h3><?php _e('Deactivation Settings','voting-contest'); ?></h3>
			<table class="form-table">
			 <tr  valign="top">
                    <th  scope="row"><label for="vote_deactivation"><?php _e('Deactivation Settings','voting-contest'); ?> </label></th>
                    <td> <input type="checkbox" id="vote_deactivation" name="vote_deactivation"  <?php checked('on', $option['deactivation']); ?>/> <span class="description"><?php _e('Data will get retained after Deactivation.','voting-contest'); ?></span></td>
                </tr>

               
            </table>
            
            <!-- Added new fields -->
            <h3><?php _e('Facebook Sharing','voting-contest'); ?></h3>
		   <table class="form-table">
		    <tr  valign="top">
			<th  scope="row"><label for="vote_deactivation"><?php _e('Facebook Share','voting-contest'); ?> </label></th>
			<td> 
			<input type="checkbox" id="vote_facebook" name="vote_facebook"  <?php checked('on', $option['facebook']); ?>/>
			<span class="description"><?php _e('Enable Facebook Sharing.','voting-contest'); ?></span>
			</td>
		    </tr>
            <tr>
                    <td></td>
                    <td>
                    <input type="file" name="facebook_image" />
                    <?php if($option['file_facebook']!='' && $option['file_fb_default']==''){?>
                    <span style="position: relative;top: 10px;"><img style="height:auto;width:auto;" src="<?php echo $path['url'].'/'.$option['file_facebook']?>"/></span>
                    <?php } ?>
                    <p class="description"><?php _e('Suggested Image Size is max 105px width - max 36px height.','voting-contest'); ?></p>
                    <p class="description"><?php _e('Upload image to change the default facebook image.','voting-contest'); ?></p>
                    </td> 
            </tr>
            
           	<tr>
                    <th  scope="row"></th>
                    <td> 
                    <input type="checkbox" id="vote_facebook_default_img" name="vote_facebook_default_img"  <?php checked('on', $option['file_fb_default']); ?>/> 
                    <span class="description"><?php _e('Use default Facebook image.','voting-contest'); ?></span>
                    <input type="hidden" name="fb_uploaded_image" value="<?php echo $option['file_facebook']; ?>" />
                    </td>
            </tr> 
                      
            </table>
            
            <table class="form-table">
		    <tr  valign="top">
			<th scope="row"><label for="facebook_login"><?php _e('Facebook Login','voting-contest'); ?> </label></th>
			<td> 
			<input type="checkbox" id="facebook_login" name="facebook_login"  <?php checked('on', $option['facebook_login']); ?>/>
			<span class="description"><?php _e('Enable Facebook Login.','voting-contest'); ?></span>
			</td>
		    </tr>
            
            <tr>
                <td></td>
                <td> 
    			<input type="text" value="<?php echo $option['vote_fb_appid'] ?>" name="vote_fb_appid" id="vote_fb_appid" />
    			<span  class="description"><?php _e('Facebook App ID','voting-contest'); ?></span>
    			</td>
            </tr>           
                      
            </table>
            
            
            <h3><?php _e('Twitter Sharing','voting-contest'); ?></h3>
            <table class="form-table">
			 <tr  valign="top">
                    <th  scope="row"><label for="vote_twitter"><?php _e('Twitter Share','voting-contest'); ?> </label></th>
                    <td> <input type="checkbox" id="vote_twitter" name="vote_twitter"  <?php checked('on', $option['twitter']); ?>/>
                    <span class="description"><?php _e('Enable Twitter Sharing.','voting-contest'); ?></span>
                    </td>
            </tr>
            <tr>
                <th></th>
                <td><input type="file" name="twitter_image" />
                <?php if($option['file_twitter']!='' && $option['file_tw_default']==''){  ?>
                <span style="position: relative;top: 10px;"><img style="height:auto;width:auto;" src="<?php echo $path['url'].'/'.$option['file_twitter']?>"/></span>
                <?php } ?>
                <p class="description"><?php _e('Upload image to change the default tweet image.','voting-contest'); ?></p>
				<p class="description"><?php _e('Suggested Image Size is max 105px width - max 36px height.','voting-contest'); ?></p>
                </td>
            </tr>   
             <tr>
                    <th  scope="row"></th>
                    <td> 
                    <input type="checkbox" id="vote_twitter_default_img" name="vote_twitter_default_img"  <?php checked('on', $option['file_tw_default']); ?>/>
                    <span class="description"><?php _e('Use default Twitter image.','voting-contest'); ?></span>
                    <input type="hidden" name="tw_uploaded_image" value="<?php echo $option['file_twitter']; ?>" />
                    </td>
            </tr>   
            </table>
            
            <table class="form-table">
		    <tr  valign="top">
			<th scope="row"><label for="twitter_login"><?php _e('Twitter Login','voting-contest'); ?> </label></th>
			<td> 
			<input type="checkbox" id="twitter_login" name="twitter_login"  <?php checked('on', $option['twitter_login']); ?>/>
			<span class="description"><?php _e('Enable Twitter Login.','voting-contest'); ?></span>
			</td>
		    </tr>
            
            <tr>
                <td></td>
                <td> 
    			<input type="text" value="<?php echo $option['vote_tw_appid'] ?>" name="vote_tw_appid" id="vote_tw_appid" />
    			<span  class="description"><?php _e('Twitter API key','voting-contest'); ?></span>
    			</td>
            </tr>  
            
            <tr>
                <td></td>
                <td> 
    			<input type="text" value="<?php echo $option['vote_tw_secret'] ?>" name="vote_tw_secret" id="vote_tw_secret" />
    			<span  class="description"><?php _e('Twitter API Secret','voting-contest'); ?></span>
    			</td>
            </tr>         
                      
            </table>
	    
	<h3><?php _e('PrettyPhoto Settings','voting-contest'); ?></h3>  
    
    
    <table class="form-table">	    
	  
        <tr  valign="top">
                <th  scope="row"><label for="vote_show_date_prettyphoto"><?php _e('Show Date','voting-contest'); ?> </label></th>
                <td> <input type="checkbox" id="vote_show_date_prettyphoto" name="vote_show_date_prettyphoto"  <?php checked('on', $option['vote_show_date_prettyphoto']); ?>/>
                <span class="description"><?php _e('Show Date in Pretty Photo','voting-contest'); ?></span>
                </td>
        </tr>    
    </table>
    
    <h3><?php _e('Turn Off the Loading Scripts','voting-contest'); ?></h3>  
	
	<table class="form-table">
	     <tr  valign="top">
                    <th  scope="row"><label for="disable_jquery"><?php _e('Jquery','voting-contest'); ?> </label></th>
                    <td> <input type="checkbox" id="disable_jquery" name="disable_jquery"  <?php checked('on', $option['vote_disable_jquery']); ?>/>
                    <span class="description"><?php _e('Disable Jquery from Loading.','voting-contest'); ?></span>
                    </td>
            </tr>
	     
    	     <tr  valign="top">
                    <th  scope="row"><label for="disable_jquery_cookie"><?php _e('Jquery Cookie','voting-contest'); ?> </label></th>
                    <td> <input type="checkbox" id="disable_jquery_cookie" name="disable_jquery_cookie"  <?php checked('on', $option['vote_disable_jquery_cookie']); ?>/>
                    <span class="description"><?php _e('Disable Jquery Cookie from Loading.','voting-contest'); ?></span>
                    </td>
            </tr>
		     
	    <tr  valign="top">
                    <th  scope="row"><label for="disable_jquery_fancy"><?php _e('FancyBox','voting-contest'); ?> </label></th>
                    <td> <input type="checkbox" id="disable_jquery_fancy" name="disable_jquery_fancy"  <?php checked('on', $option['vote_disable_jquery_fancy']); ?>/>
                    <span class="description"><?php _e('Disable FancyBox from Loading.','voting-contest'); ?></span>
                    </td>
            </tr>
	    
	    <tr  valign="top">
                    <th  scope="row"><label for="disable_jquery_pretty"><?php _e('Pretty Photo','voting-contest'); ?> </label></th>
                    <td> <input type="checkbox" id="disable_jquery_pretty" name="disable_jquery_pretty"  <?php checked('on', $option['vote_disable_jquery_pretty']); ?>/>
                    <span class="description"><?php _e('Disable Pretty Photo from Loading.','voting-contest'); ?></span>
                    </td>
            </tr>
	    
	    <tr  valign="top">
                    <th  scope="row"><label for="disable_jquery_validate"><?php _e('Jquery Validate','voting-contest'); ?> </label></th>
                    <td> <input type="checkbox" id="disable_jquery_validate" name="disable_jquery_validate"  <?php checked('on', $option['vote_disable_jquery_validate']); ?>/>
                    <span class="description"><?php _e('Disable Validate jquery plugin from Loading.','voting-contest'); ?></span>
                    </td>
            </tr>
            
            
            
        </table>
	
		
			
            <p class="submit"><input type="submit" value="<?php _e('Update','voting-contest'); ?>" class="button" id="votes_settings" name="votes_settings"></p>
        </form>
        <div class="clear"></div>
    </div>
</div>


<script type="text/javascript">
function isnumber(evt){
	  var charCode = (evt.which) ? evt.which : evt.keyCode
	 //var charCode = evt.keyCode == 0 ? evt.charCode : evt.keyCode;
	if (charCode > 31 && (charCode < 48 || charCode > 57))
	    return false;
	
	return true;
}
</script>