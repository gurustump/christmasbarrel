<?php
/*
* Checks for the Permission to vote
*/
if(!function_exists('is_votable')){
	function is_votable($pid, $ip, $termid){       
		global $wpdb;
		$option = get_option(VOTES_SETTINGS);
		$freq = $option['frequency'];
		$where = '';
        if($option['vote_tracking_method'] == 'cookie_traced'){
            
            if($freq == 0){    		
    			return true;
    		}              
            $ua = voting_getBrowser();  
            $voter_cookie = $ua['name'].'@'.$termid.'@'.$pid;                        
            if($freq == 11){
                if($option['vote_votingtype'] != ''){
                    if($_COOKIE['voter_term_perm'.$termid] != null)
                        return false;
                }
                else{                    
                    if($_COOKIE['voter_cook_perm'.$pid] == $voter_cookie)
                        return false;                    
                }                
            }
            else{
                if($option['vote_votingtype'] != ''){                    
                    if($_COOKIE['voter_term'.$termid] != null)
                        return false;
                }
                else{                    
                    if($_COOKIE['voter_cook'.$pid] == $voter_cookie)
                        return false;                    
                }
            }            
            return true;
               
        }
        else{        
    		if($freq == 12){
    			// Once in 12 hrs
    			$where .= 'AND (SELECT TIME_TO_SEC(TIMEDIFF("'.date("Y-m-d H:i:s").'", `date`)) <= 43200 )';
    			
    		}else if($freq == 24){
    			// Once in 24 hrs
    			$where .= 'AND (SELECT TIME_TO_SEC(TIMEDIFF("'.date("Y-m-d H:i:s").'", `date`)) <= 86400 )';
    		}else if($freq == 1){
    			// Once per Calendar day
    			$days = 1;
    			$where .= 'AND (SELECT DATEDIFF("'.date("Y-m-d").'", `date`) < '.$days.' )';
    		}
    		else if($freq == 0){
    			//No Limit
    			return true;
    		}        
    		if($freq == 11 && $option['vote_votingtype']!='') {		  
    			$where .= 'AND `termid` = '.$termid;
    		}
    		
    		else if($option['vote_votingtype']=='' && $freq == 11) {
    			$where .= 'AND `post_id` = ' . $pid ;
    		}
    		
    		else if($option['vote_votingtype']) {
    			$where .= 'AND `termid` = '.$termid;
    		}
    		else{
    			$where .= 'AND `post_id` = ' . $pid ;
    		}
    		$vote_sql = 'SELECT * FROM `' . VOTES_TBL . '` WHERE `ip` =  "' . $ip . '" '.$where;
            
    		$voted = $wpdb->get_results($vote_sql);        
    		if(count($voted)){
    			return false;
    		}
    		return true;
        }
	}
}

/*
 * Saves the Polled Vote
 */
if (!function_exists('save_votes_for_post')) {

    function save_votes_for_post() {
        global $wpdb;
        if($_SERVER[ 'HTTP_X_REQUESTED_WITH' ]=='XMLHttpRequest'){
        $pid = $_GET['pid'];
        
        //Addingt the code for the WP_ID
        $option = get_option(VOTES_SETTINGS);
        
        if($option['onlyloggedinuser'] == 'on'){            
            $user_id = get_current_user_id();
            $ip = $user_id;            
        }
        else{
           //Check Cookie Trace Here 
           if($option['vote_tracking_method'] == 'cookie_traced'){
               $freq = $option['frequency'];
               $ua = voting_getBrowser();
               $voter_cookie = $ua['name'].'@'.$_GET['termid'].'@'.$pid; 
               //print_r($ua);exit;                 
               $ip = $voter_cookie;               
           }
           else{ 
               if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARTDED_FOR'] != '') {
        	    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        	    } else {
        	    $ip = $_SERVER['REMOTE_ADDR'];
        	   }
           }
           if(is_user_logged_in()){
                $user_id = get_current_user_id();
                $ip = $user_id; 
           }
        }
        
    	
		   
        //$ip = $_SERVER['REMOTE_ADDR'];
        $termid = $_GET['termid'];
        $paged = isset($_GET['paged']) ? $_GET['paged'] : 0;
        $args = '';
        $args .= 'id = '.$termid;
        if($paged > 0){
            $args .= ' paged = '.$paged;
        }
        $args .= ' ajaxcontent = 1';
        $result = array();
        $where = '';
		
		if(!votes_is_contest_started($termid)) {
			$option = get_option(VOTES_SETTINGS);
			$result['success'] = 0;
			$result['msg'] = $option['vote_tobestarteddesc'];
		}
		else if(votes_is_contest_reachedend($termid)) {
			$option = get_option(VOTES_SETTINGS);
			$result['success'] = 0;
			$result['msg'] = $option['vote_reachedenddesc'];
		}
		else if(get_post_status( $pid ) != 'publish' ) {
			$result['success'] = 0;
			$result['msg'] = __('Contestants not Available','voting-contest');
		}
	   else if(!is_votable($pid, $ip, $termid)){
		$option = get_option(VOTES_SETTINGS);
		$freq = $option['frequency'];        
		if($option['vote_votingtype']!='' && $freq == 11){
			$result['success'] = 0;
			$result['msg'] = __("Voting Restricted For Multiple Contestant",'voting-contest');            
		}
		else if($option['vote_votingtype']=='' && $freq == 11) {
			$result['success'] = 0;
			$result['msg'] = __("Multiple Votes Not Allowed For Same Contestant",'voting-contest');             
		}
		else{
			$result['success'] = 0;
			$result['msg'] = __('Already Voted','voting-contest');
		}
            
        } else {
            $cur_vote = get_post_meta($pid, VOTES_CUSTOMFIELD);
            //Save in Cookie If  Enabled
            if($option['vote_tracking_method'] == 'cookie_traced')
            {
               $freq = $option['frequency'];
               
               $current_time = $_GET['current_time'];   
               $current_time = explode(" ",$_GET['current_time']);       
               
               $hrs = explode(":",$current_time[4]);
                
               
               $timestamptime = mktime($hrs[0],$hrs[1],$hrs[2],date('n', strtotime($current_time[2])),$current_time[1],$current_time[3] );   
                    
                if($ua['name'] == "GC"){
                   $gmt_offset = $_GET['gmt_offset']/60; 
                   if($gmt_offset < 0){ 
                        if(!is_int($gmt_offset_hr)){
                            $gmt_offset_hr = (int)$gmt_offset;                       
                            $timestamptime = strtotime("-".$gmt_offset_hr." hours 30 minutes", $timestamptime);
                        }
                        else{
                            $gmt_offset_hr = (int)$gmt_offset;                       
                            $timestamptime = strtotime("-".$gmt_offset_hr." hours", $timestamptime);
                        }
                   }
                   else{
                        if(!is_int($gmt_offset_hr)){
                            $gmt_offset_hr = (int)$gmt_offset;                        
                            $timestamptime = strtotime("+".$gmt_offset_hr." hours 30 minutes", $timestamptime);
                        }
                        else{
                            $gmt_offset_hr = (int)$gmt_offset;                       
                            $timestamptime = strtotime("+".$gmt_offset_hr." hours", $timestamptime);
                        }
                   }                   
                }
                  
                   
               $total_cook_size = "";
               foreach($_COOKIE as $key => $cook):                  
                    if (isset($_COOKIE[$key])) {                            
                          $data = $_COOKIE[$key];
                          $serialized_data = serialize($data);
                          $size = strlen($serialized_data);                     
                          $total_cook_size += $size ;                          
                     }
               endforeach;               
               if($total_cook_size > 4000){
                    $result['success'] = 0;
			        $result['msg'] = __('Vote Limit Exceeded','voting-contest');
                    header('content-type: application/json; charset=utf-8');
                    echo $_GET['callback'] . '(' . json_encode($result) . ')';
                    die();
               }
               votes_set_cookies($timestamptime,$option,$pid,$termid,$voter_cookie);
               
            }
            $save_sql = 'INSERT INTO `' . VOTES_TBL . '` (
                `ip` ,
                `votes` ,
                `post_id`,
				`termid`,
                `date`
                ) VALUES ( 
                "' . $ip . '",
                    1,
                    ' . $pid . ',
				"'.$termid.'",
                        "'.date("Y-m-d H:i:s").'"
                ) ';
            $updated_vote = (isset($cur_vote[0]) ? $cur_vote[0] : 0)+1;
           
            $wpdb->query($save_sql);
            
            //New Updated Code to act with the SUper Cache
            $new_sql = "SELECT SUM(votes)  FROM " . VOTES_TBL ." WHERE post_id =".$pid;
            $total_v =  $wpdb->get_var($new_sql); 
            
            update_post_meta($pid, VOTES_CUSTOMFIELD, $total_v);
            
            $result['success'] = 1;
            $result['msg'] = __('Voted Successfully ','voting-contest');
            $result['votes'] = $total_v;
            $result['args'] = $args;
            //$result['content'] = do_shortcode('[showcontestants '.$args.']');
            
            $option = get_option(VOTES_SETTINGS);
            $freq = $option['frequency'];
            if($option['vote_votingtype']!='' && $freq == 11){
			     $result['button_flag'] = 1; 
                 $result['tax_id'] = $termid;    
            }           
            
        }

        header('content-type: application/json; charset=utf-8');
        echo $_GET['callback'] . '(' . json_encode($result) . ')';
        die();
	
	}else{
		wp_redirect( home_url() );
		die();	
	}
    }

}
add_action('wp_ajax_savevotes', 'save_votes_for_post');
add_action('wp_ajax_nopriv_savevotes', 'save_votes_for_post');


/*--------------------------------------------------------------------------------------------------
	LOGIN SYSTEM
--------------------------------------------------------------------------------------------------*/
if ( ! function_exists( 'zn_do_login' ) ) {		
	function zn_do_login()
	{
		if($_SERVER[ 'HTTP_X_REQUESTED_WITH' ]=='XMLHttpRequest'){
			if ( $_POST['zn_form_action'] == 'login' ) {
				$user = wp_signon();
				if ( is_wp_error($user) ) {
				   echo '<div id="login_error">'.$user->get_error_message().'</div>';
				   die();
				}
				else{
					echo 'success';
					die();
				}
			}
			elseif( $_POST['zn_form_action'] == 'register' ){
	
				$zn_error = false;
				$zn_error_message = array();
	
				if ( !empty( $_POST['user_login'] ) ) {
					if ( username_exists( $_POST['user_login'] ) ){	
						$zn_error = true;
						$zn_error_message[] = __('The username already exists','voting-contest');
					}
					else {
						$username = $_POST['user_login'];
					}
					
				}
				else {
					$zn_error = true;
					$zn_error_message[] = __('Please enter an username','voting-contest');
				}
	
				if ( !empty( $_POST['user_password'] ) ) {
					$password = $_POST['user_password'];
				}
				else {
					$zn_error = true;
					$zn_error_message[] = __('Please enter a password','voting-contest');
				}
	
				if ( ( empty( $_POST['user_password'] ) && empty( $_POST['user_password2'] ) ) || $_POST['user_password'] != $_POST['user_password2'] ) {
					$zn_error = true;
					$zn_error_message[] = __('Passwords do not match','voting-contest');
				}
	
	
				if ( !empty( $_POST['user_email'] ) ) {
	
					if( !email_exists( $_POST['user_email'] )) {
						if (!filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL)) {
							$zn_error = true;
							$zn_error_message[] = __('Please enter a valid EMAIL address','voting-contest');
						}
						else{
							$email = $_POST['user_email'];
						}
					    
					}
					else {
						$zn_error = true;
						$zn_error_message[] = __('This email address has already been used','voting-contest');
					}
					
				}
				else {
					$zn_error = true;
					$zn_error_message[] = __('Please enter an email address','voting-contest');
				}
	
	
	
	
				if ( $zn_error ){
					echo '<div id="login_error">';
					foreach ( $zn_error_message as $error) {
						echo $error.'<br />';
					}
					echo '</div>';
					
					die();
				}
				else {
					$user_data = array(
				'ID' => '',
				'user_pass' => $password,
				'user_login' => $username,
				'display_name' => $username,
				'user_email' => $email,
				'role' => get_option('default_role') // Use default role or another role, e.g. 'editor'
			    );
			    $user_id = wp_insert_user( $user_data );
			    wp_new_user_notification( $user_id, $password );
	
			    echo '<div id="login_error">'.__('Your account has been created.','voting-contest').'</div>';
			    die();
	
				}
				
	
			}
			elseif( $_POST['zn_form_action'] == 'reset_pass' ){
				echo do_action('login_form', 'resetpass');
			}
		}else{
			wp_redirect( home_url() );
			die();	
		}
	}
}

add_action("wp_ajax_nopriv_zn_do_login", "zn_do_login");
add_action("wp_ajax_zn_do_login", "zn_do_login");

if(!function_exists('zn_fb_login')){
    function zn_fb_login()
    {
             
          $fbdata = $_POST['responses'];
           
          if(!empty($fbdata['name'])) {
            $username = $fbdata['name'];
          }
          else if (!empty($fbdata['first_name']) && !empty($fbdata['last_name'])) {
            $username = $fbdata['first_name'].$fbdata['last_name'];
          }
          else {
    		$user_emailname = explode('@', $fbdata['email']);
            $username = $user_emailname[0];
          }
    	  $user_login = sanitize_user($username, true);
          
          if (($user_id_tmp = email_exists ($fbdata['email'])) !== false) {
              $user_data = get_userdata ($user_id_tmp);
              if ($user_data !== false) {
                $user_id = $user_data->ID;
                $user_login = $user_data->user_login;                             
                wp_clear_auth_cookie ();
                wp_set_auth_cookie ($user_data->ID, true);
                do_action ('wp_login', $user_data->user_login, $user_data);
              }
          }
          else 
          {
    		  $new_user = true;
              $user_login = voting_usernameexists($user_login);
              $user_password = wp_generate_password ();
    		  $user_role = get_option('default_role');
              $user_data = array (
    							'user_login' => $user_login,
    							'display_name' => (!empty ($fbdata['name']) ? $fbdata['name'] : $user_login),
    							'user_email' => $fbdata['email'],
    							'first_name' => $fbdata['first_name'],
    							'last_name' => $fbdata['last_name'],
    							'user_url' => $fbdata['website'],
    							'user_pass' => $user_password,
    							'description' => $fbdata['aboutme'],
    			                'role' => $user_role
    						);                            
                            
               $user_id = wp_insert_user ($user_data);           
            }
          
          exit;
      
    }
}
add_action("wp_ajax_nopriv_zn_fb_login", "zn_fb_login");
add_action("wp_ajax_zn_fb_login", "zn_fb_login");

function voting_usernameexists($username) {
    $nameexists = true;
    $index = 0;
    $userName = $username;
    while($nameexists == true){
      if (username_exists($userName) != 0) {
        $index++;
        $userName = $username.$index;
      }
      else {
        $nameexists = false;
      }
    }
	return $userName;
}
if(!function_exists('zn_tw_login')){
    function zn_tw_login()
    {        
        if(isset($_POST)){
            $votes_settings = $_POST;        
        } 
    	$connection = new TwitterOAuth($votes_settings['vote_tw_appid'] , $votes_settings['vote_tw_secret'] );
        $request_token = $connection->getRequestToken($votes_settings['current_callback_url']);        
            
    	//received token info from twitter
    	$_SESSION['token'] 			= $request_token['oauth_token'];
    	$_SESSION['token_secret'] 	= $request_token['oauth_token_secret'];
    	$_SESSION['current_callback_url'] 	= $votes_settings['current_callback_url'];
        
        if($connection->http_code=='200'){
    	   //redirect user to twitter
    	   $twitter_url = $connection->getAuthorizeURL($request_token['oauth_token']);
           echo $twitter_url;    	   
           exit;
    	}else{
    		_e('Error connecting to Twitter! Try again later!','voting-contest');
    	}
    }
}
add_action("wp_ajax_nopriv_zn_tw_login", "zn_tw_login");
add_action("wp_ajax_zn_tw_login", "zn_tw_login");

if(!function_exists('voting_save_twitter_in_session')){
    function voting_save_twitter_in_session()
    {
         $email = sanitize_email( $_POST['user_login'] );
         if(!is_email($email)){
            echo 0;
         }
         else{
            $_SESSION['twitter_saved_email'] = $email;
            echo 1;
         }
         exit;
    }
}
add_action('wp_ajax_nopriv_voting_save_twemail_session', 'voting_save_twitter_in_session');
add_action('wp_ajax_voting_save_twemail_session', 'voting_save_twitter_in_session');


if(!function_exists('zn_twitter_auth_login')){
    function zn_twitter_auth_login($votes_settings)
    {        
        if(isset($_REQUEST['oauth_token']) && $_SESSION['token'] == $_REQUEST['oauth_token']) 
        {    
    	// everything looks good, request access token
    	//successful response returns oauth_token, oauth_token_secret, user_id, and screen_name
    	$connection = new TwitterOAuth($votes_settings['vote_tw_appid'], $votes_settings['vote_tw_secret'], $_SESSION['token'] , $_SESSION['token_secret']);            
        
    	$access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);           
        
    	if($connection->http_code=='200')
    	{    
    	   
        		//redirect user to twitter
        	  $_SESSION['status'] = 'verified';
        	  $_SESSION['request_vars'] = $access_token;
        		
        	 // unset no longer needed request tokens
        	  unset($_SESSION['token']);
        	  unset($_SESSION['token_secret']);                          
              
              $screenname 	= $_SESSION['request_vars']['screen_name'];            
              $content = $connection->get('users/show', array('screen_name' => $screenname));        
              
              if(!empty($screenname)) {
                $username   = $screenname;
                $user_login = sanitize_user($username, true);
              }   
              
              $twitter_saved_email = $_SESSION['twitter_saved_email'];
                     
              if (($user_id_tmp = email_exists ($twitter_saved_email)) !== false) {              
                  $user_data = get_userdata ($user_id_tmp); 
                               
                  if ($user_data !== false) {  
                    
                    $user_id = $user_data->ID;
                    $user_login = $user_data->user_login;                  
                    wp_clear_auth_cookie ();
                    wp_set_auth_cookie ($user_data->ID, true);
                    do_action ('wp_login', $user_data->user_login, $user_data);
                    wp_redirect($_SESSION['current_callback_url']);
                    exit;
                  }              
                  
              }
              else{
                  $new_user = true;
                  $user_login_cus = voting_usernameexists($user_login);
                  $user_password = wp_generate_password ();
        		  $user_role = get_option('default_role');
                  $user_data = array (
        							'user_login' => $user_login_cus,
        							'display_name' => $user_login_cus,	
                                    'user_email' => $twitter_saved_email,					
        							'user_pass' => $user_password,
                                    'first_name' => $content->name,							
        			                'role' => $user_role,
                                    'description' => $content->description,
        						);                            
                                
                  $user_id = wp_insert_user ($user_data); 
                   
              }
                    
        	   	
        	}else{
        		_e('Error, Try again later!','voting-contest');
        	}
        		
        }
    }
}
if(!function_exists('is_current_user_voted')){
    function is_current_user_voted($pid, $ip, $termid)
    {        
        global $wpdb;
		$option = get_option(VOTES_SETTINGS);
		$freq = $option['frequency']; 
        
        if($option['vote_tracking_method'] == 'cookie_traced' && $option['vote_votingtype'] != ''){
            $ua = voting_getBrowser();               
            $voter_cookie = $ua['name'].'@'.$termid.'@'.$pid;          
            if($_COOKIE['voter_term_perm'.$termid] == $voter_cookie){
                return true;
            }  
            else{
                return false;
            }
        }
        if($freq == 11 && $option['vote_votingtype']!='') {
            $where .= 'AND `termid` = '.$termid.' AND `post_id` = ' . $pid;
        }
        $vote_sql = 'SELECT * FROM `' . VOTES_TBL . '` WHERE `ip` =  "' . $ip . '" '.$where;
        $voted = $wpdb->get_results($vote_sql);        
		if(count($voted)){
			return true;
		}
		return false;
    }
}
if(!function_exists('voting_profile_update')){
    function voting_profile_update($post,$error)
    {
        global $current_user, $wp_roles;
        /* Update user password. */
        if ( !empty($post['pass1'] ) && !empty( $post['pass2'] ) ) {
            if ( $post['pass1'] == $post['pass2'] )
                wp_update_user( array( 'ID' => $current_user->ID, 'user_pass' => esc_attr( $post['pass1'] ) ) );
            else
                $error->add('Invalid Password','<strong>Error</strong> : '.__('The passwords you entered do not match.  Your password was not updated.', 'voting-contest'));                
        }
    
        
        if ( !empty( $post['email'] ) ){
            if (!is_email(esc_attr( $post['email'] )))
                $error->add('Invalid Email','<strong>Error</strong> : '.__('The Email you entered is not valid.  please try again.', 'voting-contest'));
            elseif(email_exists(esc_attr( $post['email'] )) != $current_user->id )
                $error->add('Invalid Email Exists','<strong>Error</strong> : '.__('This email is already used by another user.  try a different one.', 'voting-contest'));
            else{
                wp_update_user( array ('ID' => $current_user->ID, 'user_email' => esc_attr( $post['email'] )));
            }
        }
    
        
        if ( !empty( $post['first-name'] ) )
            update_user_meta( $current_user->ID, 'first_name', esc_attr( $post['first-name'] ) );
        if ( !empty( $post['last-name'] ) )
            update_user_meta($current_user->ID, 'last_name', esc_attr( $post['last-name'] ) );
        if ( !empty( $post['nickname'] ) )
            update_user_meta( $current_user->ID, 'nickname', esc_attr( $post['nickname'] ) );
                        
        $error = myplugin_registration_errors($error);  
        
        if ( count($error->errors) == 0 ) {
            do_action('edit_user_profile_update', $current_user->ID);
        }
        else{
            return $error;
        }
            
    }
}
if(!function_exists('voting_getBrowser')){
    function voting_getBrowser() 
    { 
        $u_agent = $_SERVER['HTTP_USER_AGENT']; 
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version= "";
    
        //First get the platform?
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';
        }
        elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'mac';
        }
        elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'windows';
        }
        
        // Next get the name of the useragent yes seperately and for good reason
        if((preg_match('/MSIE/i',$u_agent) || preg_match('/Trident/i',$u_agent)) && !preg_match('/Opera/i',$u_agent)) 
        { 
            $bname = 'IE'; 
            $ub = "MSIE"; 
        } 
        elseif(preg_match('/Firefox/i',$u_agent)) 
        { 
            $bname = 'MF'; 
            $ub = "Firefox"; 
        } 
        elseif(preg_match('/Chrome/i',$u_agent)) 
        { 
            $bname = 'GC'; 
            $ub = "Chrome"; 
        } 
        elseif(preg_match('/Safari/i',$u_agent)) 
        { 
            $bname = 'AS'; 
            $ub = "Safari"; 
        } 
        elseif(preg_match('/Opera/i',$u_agent)) 
        { 
            $bname = 'O'; 
            $ub = "Opera"; 
        } 
        elseif(preg_match('/Netscape/i',$u_agent)) 
        { 
            $bname = 'N'; 
            $ub = "Netscape"; 
        } 
        
        // finally get the correct version number
        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) .
        ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches)) {
            // we have no matching number just continue
        }
        
        // see how many we have
        $i = count($matches['browser']);
        if ($i != 1) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
                $version= $matches['version'][0];
            }
            else {
                $version= $matches['version'][1];
            }
        }
        else {
            $version= $matches['version'][0];
        }
        
        // check if we have a number
        if ($version==null || $version=="") {$version="?";}
        
        return array(
            'userAgent' => $u_agent,
            'name'      => $bname,
            'version'   => $version,
            'platform'  => $platform,
            'pattern'    => $pattern
        );
    } 
}

if(!function_exists('votes_set_cookies')){
    function votes_set_cookies($timestamptime,$option,$pid,$termid,$voter_cookie,$cook_name=""){        
           $freq = $option['frequency'];
           if($freq == 12){
                $cookie_time = $timestamptime+3600*24*0.5;
                $cookie_name = ($option['vote_votingtype'] == '')?'voter_cook'.$pid:'voter_term'.$termid;
           }
           if($freq == 24){
                $cookie_time = $timestamptime+3600*24*1;
                $cookie_name = ($option['vote_votingtype'] == '')?'voter_cook'.$pid:'voter_term'.$termid;
           }
           if($freq == 1){
                $cookie_time = strtotime('tomorrow',$timestamptime)- 1;
                $cookie_name = ($option['vote_votingtype'] == '')?'voter_cook'.$pid:'voter_term'.$termid;
           }  
           if($freq == 11){
                //Changed the Name for the Votes Per category
                $cookie_time = $timestamptime+3600*24*365;
                $cookie_name = ($option['vote_votingtype'] == '')?'voter_cook_perm'.$pid:'voter_term_perm'.$termid;
           }             
           if($freq != 0){
                if($cook_name != "")
                    $cookie_name = $cook_name;       
                     
                //Make it as HTTP Cookie to prevent from XSS
                setrawcookie($cookie_name, $voter_cookie,$cookie_time, COOKIEPATH, COOKIE_DOMAIN, false,true);
                       
                              
           }
    }
}
