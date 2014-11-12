<?php
session_start(); 
/**
 *  Displays Contestants based on the specification with pagination
 */
 
if (!function_exists('wp_votes_generate_contestants')) {

    function wp_votes_generate_contestants($args) {
      
        global $wpdb;
	
	if(isset($_SESSION['arg_category_id_vote']))
		unset($_SESSION['arg_category_id_vote']);
        
	if(isset($_SESSION['arg_shortcode_vote']))
		unset($_SESSION['arg_shortcode_vote']);
	
	if(isset($_SESSION['vote_login_function_ran']))
		unset($_SESSION['vote_login_function_ran']);
		
	$_SESSION['arg_category_id_vote']=$args['id'];
	$_SESSION['arg_shortcode_vote'] = $args;
        $page_values=get_permalink();

        $out = '';
		
	if ($args['id'] != 0 && explode(',', $args['id']))
		$args['id'] = explode(',', $args['id']);
		
	if ($args['id'] == 0) {
		$v_terms = votes_get_available_contest($args['forcedisplay']);
		if(count($v_terms)){
				$args['id'] = $v_terms;
		}
		else {
			return 'No contestants to display.';
		}
	}
	$blocked = 0;

	if(!votes_is_contest_started(implode(',', $args['id']))) {
		$option = get_option(VOTES_SETTINGS);
		if(!$args['forcedisplay'])
			$out .= $option['vote_tobestarteddesc'];
		$blocked = 1;
	}
	else if(votes_is_contest_reachedend(implode(',', $args['id']))) {
		$option = get_option(VOTES_SETTINGS);
		if(!$args['forcedisplay'])
			$out .= $option['vote_reachedenddesc'];
		$blocked = 1;
	}
    
   		
	if(!$args['forcedisplay'] && $blocked){
		return $out;
	}
	else {	
		if (isset($args['paged']) && $args['paged'] > 0)
			$paged = $args['paged'];
		else{		
			if ( get_query_var('paged') ) {			
			    $paged = get_query_var('paged');
			} elseif ( get_query_var('page') ) {			
			    $paged = get_query_var('page');		 
			} else {
			    $paged = 1;
			}	
		}
		//$paged = get_query_var('paged') ? get_query_var('paged') : 1;      
        
        //Get Exclude IDs for the show contestants 
        if($args['exclude'] != null):
            $excluded_ids = explode(',',$args['exclude']);
        else:
            $excluded_ids = array();
        endif;      
                     
		if ($args['orderby'] == 'votes') { 
            
			$postargs = array(
				'post_type' => VOTES_TYPE,
				'post_status' => 'publish',
				'posts_per_page' => $args['postperpage'],
				'tax_query' => array(
					array('taxonomy' => $args['taxonomy'],
						'field' => 'id',
						'terms' => $args['id'],
						'include_children' => false)
				),
				'paged' => $paged,
				'meta_key' => VOTES_CUSTOMFIELD,
				'orderby' => 'meta_value_num',
				'order' => $args['order'],
                'post__not_in' => $excluded_ids,           
			);
		   
		}
        //Check the Show contestants orderby (top)            
		elseif ($args['orderby'] == 'top') {		     
		    $order_settings = 'DESC';
			$postargs = array(
				'post_type' => VOTES_TYPE,
				'post_status' => 'publish',
				'posts_per_page' => $args['postperpage'],
				'tax_query' => array(
					array('taxonomy' => $args['taxonomy'],
						'field' => 'id',
						'terms' => $args['id'],
						'include_children' => false)
				),
				'paged' => $paged,
				'meta_key' => VOTES_CUSTOMFIELD,
				'orderby' => 'meta_value_num',
				'order' => $order_settings,
                'post__not_in' => $excluded_ids,                
			);
		   
		}
        //Check the Show contestants orderby (bottom)            
		elseif ($args['orderby'] == 'bottom') { 		     
		    $order_settings = 'ASC';                                   
			$postargs = array(
				'post_type' => VOTES_TYPE,
				'post_status' => 'publish',
				'posts_per_page' => $args['postperpage'],
				'tax_query' => array(
					array('taxonomy' => $args['taxonomy'],
						'field' => 'id',
						'terms' => $args['id'],
						'include_children' => false)
				),
				'paged' => $paged,
				'meta_key' => VOTES_CUSTOMFIELD,
				'orderby' => 'meta_value_num',
				'order' => $order_settings,
                'post__not_in' => $excluded_ids,                
			);
		   
		}
         else {        
			$postargs = array(
				'post_type' => VOTES_TYPE,
				'post_status' => 'publish',
				'orderby' => $args['orderby'],
				'posts_per_page' => $args['postperpage'],
				'order' => $args['order'],				
				'tax_query' => array(
					array('taxonomy' => $args['taxonomy'],
						'field' => 'id',
						'terms' => $args['id'],
						'include_children' => false)
				),
				'paged' => $paged,
                'post__not_in' => $excluded_ids,
			);
	  
		}
                
                
		if (is_array($args['id']) && count($args['id']) > 1) {
			add_filter('posts_where_request', 'wp_votes_expiration_basedon_general');
		}
		else {
			global $taxid;
			$taxid = isset($args['id'][0]) ? $args['id'][0] : 0;
			add_filter('posts_where_request','wp_votes_expiration_basedon_taxid');
		}
			   
		if (is_array($args['id']))
			$votes_term_id = implode(',', $args['id']);
		else
			$votes_term_id = $args['id'];
		
        //Add the input type hidden show_description for description settings
        $cat_settings = get_option($args['id'][0].'_'.VOTES_SETTINGS);	
        echo "<input type='hidden' name='show_description_".$args['id'][0]."' id='show_description_".$args['id'][0]."' value='".$cat_settings['show_description']."' />";
        
		        	
		$current_url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";   
		
                $contest_post = new WP_Query($postargs);
		     
		if(!isset($args['hidecontestants'])){                  
			if ($contest_post->have_posts()) {
			    if($args['view']!=''){
			      if($args['view']=='grid'){
			      $out .= '<div class="grid_list_icons">
				</div><div class="clear"></div><div class="clear"></div>';
			      }
			      elseif($args['view']=='list'){
				$out .= '<div class="grid_list_icons">
				</div><div class="clear"></div><div class="clear"></div>';
			      }else{
				$out .= '
				<div class="grid_list_icons">
				    <a id="anchor_grid_normal" class="grid_view '.$args['id'][0].'"></a>
				    <a id="anchor_listing_normal" class="listing_view '.$args['id'][0].'" ></a>
				</div><div class="clear"></div><div class="clear"></div>';
				}  
			    }else{
			    $out .= '
			    <div class="grid_list_icons">
				<a id="anchor_grid_normal" class="grid_view '.$args['id'][0].'"></a>
				<a id="anchor_listing_normal" class="listing_view '.$args['id'][0].'" ></a>
			    </div><div class="clear"></div><div class="clear"></div>';
			  }
			}
		}
                
		if (trim($args['title']))
			$out .= '<div class="contest-caption"><h1>' . __($args['title']) . '</h1></div>';
		
		if($args['showtimer']) {
			$argId = implode(',', $args['id']);
			$out .= do_shortcode('[upcomingcontestants id='.$argId.' showcontestants=0 message=0]');
		}
		if($args['showtimer']) {
			$argId = implode(',', $args['id']);
			if(votes_is_contest_started($argId )){
				$out .= do_shortcode('[endcontestants id='.$argId.']');
				$out .= '<div class="clear"></div>';
			}
		}
		 $out.= wp_votes_total_count_votes($args['id'][0]);
         
         //Get the Text Description displayed in GRID/LIST Views
         $text_desc = wp_voting_get_text_description($args['id'][0]);         
                 
		if($args['showform']) {
			$argId = implode(',', $args['id']);
			$start_cont_time = get_option($args['id'][0]. '_' . VOTES_TAXSTARTTIME);
			$time = get_option($args['id'][0]. '_' . VOTES_TAXEXPIRATIONFIELD);
			$currenttime = current_time( 'timestamp', 0 );
			$timeentered = strtotime(str_replace("-", "/", $time));
			if($currenttime <= $timeentered || $start_cont_time=='') 
				$out .= do_shortcode('[addcontestants id='.$argId.' showcontestants=0 message=0 loggeduser='.$args['onlyloggedcansubmit'].']');
		}
				
		$out .= '<div id="votes-post-container'.$args['id'][0].'">';
		$out .= '
		<div class="contest-posts-container'.$args['id'][0].' votes-list">
		    <div id="paged-focus'.$args['id'][0].'"> 
		    <input type="hidden" value="' . $votes_term_id . '" name="votes-term-id" id="votes-term-id'.$args['id'][0].'">
		    <input type="hidden" id="image_path" value="'.VOTES_PATH.'" >';
			
		if ($contest_post->have_posts()) {
			$ajax_out = '';
			if($args['view']!=''){
			 $view_default = $args['view'];
			}else{
			  $view_default = 'list';
			}
			
			//Hide Contestants           
			if(!isset($args['hidecontestants'])){      
				$out .= '
				<input type="hidden" class="dynamic_id_multi_vot"  value="'.$args['id'][0].'">
				<div id="contestants-listing'.$args['id'][0].'" class="'.$view_default.'">';
				$i = 1;
				while ($contest_post->have_posts()) {	   
					$contest_post->the_post();
					$totvotesarr = array();
					$totvotesarr = get_post_meta(get_the_ID(), VOTES_CUSTOMFIELD);
					$totvotes = isset($totvotesarr[0]) ? $totvotesarr[0] : 0;
                    if($totvotes == NULL) $totvotes = 0;

					$terms = get_the_terms(get_the_ID(), VOTES_TAXONOMY);
					$intialterm = 0;

					$term_listsarr = array();
		    
					$termids = array();
					foreach ($terms as $term) {
						$termids[] = $term->term_id;
						$term_listsarr[] =$term->name;  
					}
					$categories = join(', ', $term_listsarr);
					if(strlen($categories)>29)
					$categories_str = substr($categories,'0','30').' <b>..</b>';
					else
					$categories_str = $categories;
				     
					$termlist = '<span class="normal_view_category'.$args['id'][0].'">'.__('Category:','voting-contest').' &nbsp;<span class="single-category">' .$categories_str."</span></span>";
					if(strlen($categories)>9)
					   $more_cat = substr($categories,'0','10').' <b>..</b>';
					else
					   $more_cat = $categories;
					  
					   $hided_term_list = '<span style="display:none;" class="grid_view_category'.$args['id'][0].'">Category: &nbsp;<span class="single-category">' .$more_cat.'</span></span>';
					 
				       $catopt = '';
			    
					if(isset($args['id'][0]))
					     $intialterm = $args['id'][0];
					else
					    $intialterm = $votes_term_id;
					
                    
					$middle_custom_navigation='';
					if ($catopt = get_option($intialterm . '_' . VOTES_SETTINGS)) {
						$thumb = $catopt['imgdisplay'];
						$termdisplay = $catopt['termdisplay'];
						$detaildisplay = $catopt['detaildisplay'];
						$middle_custom_navigation=$catopt['middle_custom_navigation'];
						$votecount=$catopt['votecount'];
					}
					if($middle_custom_navigation=='')
						$middle_custom_navigation = $page_values;
					
					if (is_array($args['id']) && count($args['id']) > 0) {
						if($thumb=='')
							$thumb = $args['thumb'];
						  
						if($termdisplay=='')  
							$termdisplay = $args['termdisplay'];
						if($detaildisplay=='')
							$detaildisplay = $args['detaildisplay'];
	
						$facebook = $args['facebook'];
						$twitter = $args['twitter']; 
						$file_facebook = $args['file_facebook']; 
						$file_fb_default = $args['file_fb_default'];
						$file_twitter = $args['file_twitter'];
						$file_tw_default = $args['file_tw_default'];
					}
			
					if($args['thumb']!=''){
						$thumb=$args['thumb'];
					}
                    
                   $adv_excerpt = VotesAdvancedExcerpt::Instance();      
                   $shor_desc = $adv_excerpt->filter(get_the_excerpt());
			
					$ajax_out .= '
						<div class="view"><input type="hidden" id="votes_content_title_'.get_the_ID().'" value="'.get_the_title().'" />';
                        
                    $show_in_pretty = vote_show_desc_prettyphoto();
                    $pretty_excerpt = ($show_in_pretty == 1)?strip_tags($shor_desc):'';
                    
					if(($thumb!='' && $thumb!=0) || ($thumb=='on')){
						if (has_post_thumbnail(get_the_ID())) {
												
							$option = get_option(VOTES_SETTINGS);
										
							$image_arr = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), $option['page_cont_image']);
							$image_src = $image_arr[0];			
							//$image1 = wp_votes_image_resize_thumb(get_post_thumbnail_id(),'',$args["width"],$args["height"],true);
							if($option['short_cont_image']=='')
							$option['short_cont_image'] = 'thumbnail';
							$image1 = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), $option['short_cont_image']);
							$image_alt_text=votes_seo_friendly_alternative_text(get_the_title());
                            $bigimg = wp_get_attachment_url(get_post_thumbnail_id(get_the_ID())).'?'.uniqid();
							$image = "<a class='thumb' id='gallery_".get_the_ID()."' alt='".$pretty_excerpt."<input type=hidden class=voteid value=".get_the_ID()." />' href=".$bigimg." data-gal='prettyPhoto[$categories_str]'><img alt='".$image_alt_text."' src=".$image1[0]." width=".$image1[1]." height=".$image1[2]." class='left-img-thumb' longdesc='".get_permalink(get_the_ID())."' /></a>";
												
						} else {
						
						$option = get_option(VOTES_SETTINGS);							
						if($option['short_cont_image']=='')
						 $short_cont_image = 'thumbnail';
						else
						 $short_cont_image =$option['short_cont_image'];
						
						$all_sizes = votes_list_thumbnail_sizes();
						$width_height = explode('~',$all_sizes[$short_cont_image]);
						$width=$width_height[0];
						$height=$width_height[1];
						
						$image_src = VOTES_PATH . 'images/no-image.jpg?'.uniqid();
						$image1 = wp_votes_image_resize_thumb('',$image_src,$args["width"],$args["height"],true);                       
                        $image_alt_text=get_the_title();
                        
						$image = "<a class='thumb no-img' alt='".$pretty_excerpt."<input type=hidden class=voteid value=".get_the_ID()." />' style='width:".$width."px;height:".$height."px;' href=".$image_src." data-gal='prettyPhoto[$categories_str]'><img alt='".$image_alt_text."' src=".$image1['url']." width=".$width." height=".$height."  class='left-img-thumb' longdesc='".get_permalink(get_the_ID())."' /></a>";
							
						}
						
						$ajax_out .= $image;
                        
                        
						
						$style_width = '';
					}else{
					   $style_width = 'style="width:100%;"';
					}
		
					$ajax_out .= '
				       <div class="view-content" '.$style_width.'>
					   <div class="text">';
					
					$option_general_setting = get_option(VOTES_SETTINGS);				
	
						if($view_default=='list'){
							if($option_general_setting['vote_truncation_list']!=''){
								 $title_details = mb_substr(get_the_title(),'0',$option_general_setting['vote_truncation_list']).'..';
							}else{
								$title_len= strlen(get_the_title());
								if($title_len > 100){
								    $title_details = mb_substr(get_the_title(),'0','100').'..';
								}else{
								    $title_details = get_the_title();
								}
							}
						}
						if($view_default=='grid'){
							if($option_general_setting['vote_truncation_grid']!=''){
								 $title_details = mb_substr(get_the_title(),'0',$option_general_setting['vote_truncation_grid']).'..';
							}else{
								$title_len= strlen(get_the_title());
								if($title_len > 100){
								    $title_details = mb_substr(get_the_title(),'0','100').'..';
								}else{
								    $title_details = get_the_title();
								}
							}
						}
						
						if($option_general_setting['vote_truncation_list']!=''){
							$list_details = mb_substr(get_the_title(),'0',$option_general_setting['vote_truncation_list']).'..';
						}
						else{
							$title_len= strlen(get_the_title());
							if($title_len > 100){
							    $list_details= mb_substr(get_the_title(),'0','100').'..';
							}else{
							    $list_details= get_the_title();
							}
						}
						
						if($option_general_setting['vote_truncation_grid']!=''){
							 $grid_details = mb_substr(get_the_title(),'0',$option_general_setting['vote_truncation_grid']).'..';
						}else{
							$title_len= strlen(get_the_title());
							if($title_len > 20){
							    $grid_details = mb_substr(get_the_title(),'0','20').'..';
							}else{
							    $grid_details = get_the_title();
							}
						}
					
						if ($detaildisplay) {	
						$perma_link = get_permalink(get_the_ID());		
						$ajax_out .= '<h1><a style="display:none;" class="vote_description_page '.$args['id'][0].' vote_description_title_list'.$args['id'][0].'" rev="'.$middle_custom_navigation.'" href="'.$perma_link.'">' . __($list_details) . '</a></h1>';
						$ajax_out .= '<h1><a style="display:none;" class="vote_description_page '.$args['id'][0].' vote_description_title_grid'.$args['id'][0].'" rev="'.$middle_custom_navigation.'" href="'.$perma_link.'">' . __($grid_details) . '</a></h1>';
						
						
						$ajax_out .='<input type="hidden"  value="'.mb_substr(get_the_title(),'0',$option_general_setting['vote_truncation_list']).'" />
						<input type="hidden"  value="'.mb_substr(get_the_title(),'0',$option_general_setting['vote_truncation_grid']).'" />'; 
						$ajax_out .= '<h1><a class="vote_description_page '.$args['id'][0].' vote_description_title'.$args['id'][0].'" rev="'.$middle_custom_navigation.'" href="'.$perma_link.'">' . __($title_details) . '</a></h1>';                       
                                                
						}
				
					if ($termdisplay) {
					   $ajax_out .= '<div class="votes-terms category">' . $termlist.$hided_term_list. '</div>';
					}
				    
                   
                    
                   //Strip the description to avoid the design issues   
                   //$shor_desc = strip_tags(__(get_the_excerpt())); 
                   $adv_excerpt = VotesAdvancedExcerpt::Instance();      
                   $shor_desc = $adv_excerpt->filter(get_the_excerpt());
                                                    
                                      
                   if($shor_desc == null)
                       $ajax_out .= '<div class="text_description'.$args['id'][0].'"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </div>'; 
                   else         
				       $ajax_out .= '<div class="text_description'.$args['id'][0].'"> ' . $shor_desc  .'</div>';
                                    
					
					$ajax_out .= '<div class="social_icons_class all_icons_vote'.$args['id'][0].' social_'.get_the_ID().'">';   
					$perma = get_permalink(get_the_ID());
					
					
					if($args['readmore']=='off')
					$ajax_out .= '<a class="readsmore-btn" rev="'.$middle_custom_navigation.'" href="'.$perma.'"></a>';
					
					//Check for facebook and twitter share icons
					$up_path =  wp_upload_dir();
					if($facebook!='off') {
					   if($file_fb_default=='' && $file_facebook!=''){
						 if(file_exists($up_path['path'].'/'.$file_facebook))
						    $face_img_path = $up_path['url'].'/'.$file_facebook;
						 else
						    $face_img_path = VOTES_PATH.'images/facebook-share.png';
					   }else{
						$face_img_path = VOTES_PATH.'images/facebook-share.png';
					   } 
					    $ajax_out .= '<a target="_blank" class="fb_view_change'.$args['id'][0].' facebook-btn" href="http://www.facebook.com/sharer.php?u='.$perma.'&amp;t='.urlencode(get_the_title()).'"><img alt="Share on Facebook" class="face_view_change'.$args['id'][0].'" src="'.$face_img_path.'"></a> <input type="hidden" value="'.$face_img_path.'" class="face_list_img_path" />';
					}
			    
					if($twitter!='off') {
						if($file_tw_default=='' && $file_twitter!=''){
						      if(file_exists($up_path['path'].'/'.$file_twitter))
							 $twt_img_path = $up_path['url'].'/'.$file_twitter;
						      else
							 $twt_img_path = VOTES_PATH.'images/tweet.png';
						}else{
						     $twt_img_path = VOTES_PATH.'images/tweet.png';
						}
						$ajax_out .= '<a target="_blank" class="tw_view_change'.$args['id'][0].' facebook-btn" href="http://twitter.com/home?status='.urlencode(get_the_title())."%20".$perma.'"><img alt="Tweet" class="tweet_view_change'.$args['id'][0].'" src="'.$twt_img_path.'"></a><input type="hidden" value="'.$twt_img_path.'" class="tweet_list_img_path" />';
					}                  
                    
				
					$ajax_out .= '</div></div>';
					//Text Div Closed
					
					
					
					$ajax_out .='<div class="vote-count votescounter_'.get_the_ID().'"><input type="hidden" class="voted_text" value="'.__('Voted','voting-contest').'" />';
					
					if($votecount==NULL){
					$ajax_out .='
					<div class="square">
						<span class="num Votes votescount' . get_the_ID() . '">' . $totvotes . '</span>
						<span class="vote">'.__('Votes','voting-contest').'</span>
					</div>';
					}else{
					$ajax_out .='<p style="margin-top:45%;"></p>';	
					}		
				
					if($option_general_setting['vote_tracking_method'] == 'cookie_traced'){
                        $ua = voting_getBrowser();               
                        $voter_cookie = $ua['name'].'@'.$args['id'][0].'@'.get_the_ID();          
                        $ip = $voter_cookie;
                    }
                    else{ 
                       if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARTDED_FOR'] != '') {
                        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                        } else {
                        $ip = $_SERVER['REMOTE_ADDR'];
                       }
                    }
					
					
                    $option_1 = $args['id'][0]. '_' . VOTES_TAXEXPIRATIONFIELD;
					$dateexpiry =  get_option($option_1);
					$cur_time = current_time( 'timestamp', 0 );
					if($dateexpiry==''){
						$dateexpiry = date( 'Y-m-d H:i:s', current_time( 'timestamp', 0 ) );
					}
					if(strtotime($dateexpiry) >= $cur_time){
						if($args['onlyloggedinuser']){
							if(is_user_logged_in()){
							 
                                $user_id = get_current_user_id();
                                $ip = $user_id;   
								
								if(!is_votable(get_the_ID(), $ip, $args['id'][0])){
								    if($option_general_setting['vote_votingtype'] != null && $option_general_setting['frequency'] == 11)
								        $grey_class = (is_current_user_voted(get_the_ID(), $ip, $args['id'][0]))?'':'voting_grey_button';
                                    else
                                        $grey_class = '';
                                        
    								$ajax_out .='
    								<a id="vote' . get_the_ID() . '" class="votebutton vote-btn '.$args['id'][0].' '.$grey_class.'" href="javascript:void(0);" >'.__('Voted','voting-contest').'</a>';                            
								}else{
    								$ajax_out .='
    								<a id="vote' . get_the_ID() . '" class="votebutton vote-btn '.$args['id'][0].'" href="javascript:void(0);" >'.__('Vote Now','voting-contest').'</a>';
                               
								}
							
							}else{
										  
								//If not logged in Show the below will be shown in popup
								 votes_custom_registration_fields_show();
								 if(!is_votable(get_the_ID(), $ip, $args['id'][0])){
								    
								 $ajax_out .='<a style="color:#fff;" class="vote-btn vote_click '.$args['id'][0].'" href="javascript:" onclick="ppOpen(\'#login_panel\', \'800\',1)" >'.__('Voted','voting-contest').'</a>';
                                 
								 }else
								 $ajax_out .='<a style="color:#fff;" class="vote-btn vote_click '.$args['id'][0].'" href="javascript:" onclick="ppOpen(\'#login_panel\', \'800\',1)" >'.__('Vote Now','voting-contest').'</a>';
                                 
							}
						}else{		
						  if(is_user_logged_in()){
						        $user_id = get_current_user_id();
                                $ip      = $user_id;  
                          }
							if(!is_votable(get_the_ID(), $ip, $args['id'][0])){
                                if($option_general_setting['vote_votingtype'] != null && $option_general_setting['frequency'] == 11)
							         $grey_class = (is_current_user_voted(get_the_ID(), $ip, $args['id'][0]))?'':'voting_grey_button';
                                else
                                     $grey_class ='';
                                     
							$ajax_out .='<a id="vote' . get_the_ID() . '" class="votebutton vote-btn '.$args['id'][0].' '.$grey_class.'" href="javascript:void(0);" >'.__('Voted','voting-contest').'</a>';
                            
							}else{
							$ajax_out .='<a id="vote' . get_the_ID() . '" class="votebutton vote-btn '.$args['id'][0].'" href="javascript:void(0);" >'.__('Vote Now','voting-contest').'</a>';                            
							}
							
						}
					}                
                   
			    
					$ajax_out .='</div>
							</div>
					</div>'; // view_content & view closed
					$i++;
				}
			}//Not hidden
		
			$version_wp = get_bloginfo('version');
			if($version_wp < '3.4'){
				if(is_array($args['id'])){
					if(isset($args['id'][0])){
						if(!in_array($args['id'][0],$termids)){
							$ajax_out='<div class="notfound">' . __("No contestants to display.",'voting-contest') . '</div>';
						}
					}
				}
			}
		
		
		
			$out .= $ajax_out;
			if (isset($args['ajaxcontent']) && $args['ajaxcontent'] > 0) {
				return $ajax_out;
			}
			$out .= '</div>';	
            
            
            //Added to function pagination = 0 attribute
            if(isset($args['pagination']) && $args['pagination'] == 0){    
                $out .= '';                
            }
            else{              
                $out .= voting_wp_pagenavi(array('query' => $contest_post),$args['id'][0]);
            }
           
            
		}
		else {
			if(!isset($args['hidecontestants']) && !isset($args['hideerrorcont'])){  
			       $out .='
				       <div class="notfound">' . __("No contestants to display.",'voting-contest') . '</div>';
			}
		}
			
			$out .= '</div></div>';
			$out .= '</div>';
			$out .= '<div class="clear"></div>';
			wp_reset_postdata();
			
		}	
		
		return $out;
	}
}

/* 
 * General Expiration Vote Contestant 
 */
 if(!function_exists('wp_votes_expiration_basedon_general') ){
        function wp_votes_expiration_basedon_general($where){
        global $wpdb;
        return $where.' AND ( select option_id from '.$wpdb->prefix.'options where (`option_name` = "'.VOTES_GENERALEXPIRATIONFIELD.'" AND `option_value` = 0 ) or (`option_name` = "'.VOTES_GENERALEXPIRATIONFIELD.'" AND `option_value` > "'.date('Y-m-d H:i:s').'" ) ) ';
       }
      }
/* 
 * Tax ID Expiration Vote Contestant
 */
if(!function_exists('wp_votes_expiration_basedon_taxid')){
        function wp_votes_expiration_basedon_taxid($where){
        global $wpdb,$taxid;
        //return $where.' AND ( select option_id from '.$wpdb->prefix.'options where (`option_name` = "'.$taxid.'_'.VOTES_TAXEXPIRATIONFIELD.'" AND `option_value` = 0 ) or (`option_name` = "'.$taxid.'_'.VOTES_TAXEXPIRATIONFIELD.'" AND `option_value` > "'.date('Y-m-d H:i:s').'" ) ) ';
	return $where.' AND ( select option_id from '.$wpdb->prefix.'options where (`option_name` = "'.$taxid.'_'.VOTES_TAXEXPIRATIONFIELD.'" AND `option_value` = 0 ) or (`option_name` = "'.$taxid.'_'.VOTES_TAXEXPIRATIONFIELD.'" ) ) ';
      }
    }


if (!function_exists('wp_votes_show_contest')) {

    function wp_votes_show_contest($data) {   
        $out = '';
        $opt = get_option(VOTES_SETTINGS);
	$ht = $opt['imgheight'] ? $opt['imgheight'] : 92;
        $wi = $opt['imgwidth'] ? $opt['imgwidth'] : 132;
        $disp = $opt['imgdisplay'] ? $opt['imgdisplay'] : '';
        $orderby = $opt['orderby'] ? $opt['orderby'] : 'votes';
        $order = $opt['order'] ? $opt['order'] : 'desc';
        $termdisplay = $opt['termdisplay'] ? $opt['termdisplay'] : 0;
	$detaildisplay = $opt['detaildisplay'] ? $opt['detaildisplay'] : 1;
        $title = $opt['title'] ? $opt['title'] : NULL;
	$onlyloggedinuser = $opt['onlyloggedinuser'] ? $opt['onlyloggedinuser'] : FALSE;
        $vote_onlyloggedcansubmit = $opt['vote_onlyloggedcansubmit']?$opt['vote_onlyloggedcansubmit']:FALSE;
        $facebook = $opt['facebook'] ? $opt['facebook'] : 'off';
	$vote_readmore = $opt['vote_readmore'] ? $opt['vote_readmore'] : 'off';
        $twitter = $opt['twitter'] ? $opt['twitter'] : 'off';
        $file_facebook = $opt['file_facebook'] ?$opt['file_facebook']:'';
        $file_twitter = $opt['file_twitter'] ?$opt['file_twitter']:'';
        $file_fb_default = $opt['file_fb_default'] ?$opt['file_fb_default']:'';
        $file_tw_default = $opt['file_tw_default'] ?$opt['file_tw_default']:'';                  
        $data = wp_parse_args($data, array(
            'orderby' => $orderby,
            'order' => $order,
            'postperpage' => get_option('posts_per_page'),
            'taxonomy' => VOTES_TAXONOMY,
            'id' => 0,
            'thumb' => $disp,
            'height' => $ht,
            'width' => $wi,
	    'readmore'=>$vote_readmore,
            'termdisplay' => $termdisplay,
	    'detaildisplay' => $detaildisplay,
	    'facebook'=>$facebook,
            'file_facebook'=>$file_facebook,
            'file_fb_default'=>$file_fb_default,
            'twitter'=>$twitter,
            'file_twitter'=>$file_twitter,
            'file_tw_default'=>$file_tw_default,
            'paged' => 0,
            'ajaxcontent' => 0,
            'title' => $title,
	    'onlyloggedinuser' => $onlyloggedinuser,
            'onlyloggedcansubmit'=>$vote_onlyloggedcansubmit,
			'showtimer' => 1,
			'showform' => 0,
			'forcedisplay' => 1));
        $contestants = wp_votes_generate_contestants($data);
        if ($data['ajaxcontent'] > 0)
            return $contestants;
        $out .= $contestants;
        return $out;
    }

}

/**
* @method add_contestants
* @desc Displays the form to add the contestants
*/
if(!function_exists('wp_votes_add_contestants')){
	function wp_votes_add_contestants($atts) {
	   
    if(isset($_SESSION['vote_login_function_ran']))
	   unset($_SESSION['vote_login_function_ran']);
	  
    $options = get_option(VOTES_SETTINGS);
		 
	if($options['vote_disable_jquery_validate']!='on'){
	wp_register_script('jquery.validate.js', (VOTES_PATH. "scripts/jquery.validate.min.js"), false, '1.8.1');
	wp_enqueue_script('jquery.validate.js');
	}
		 
	ob_start();   
        $opt = get_option(VOTES_SETTINGS);
        $vote_onlyloggedcansubmit = $opt['vote_onlyloggedcansubmit'];
	$pathc =  get_site_url();
 
	 extract( shortcode_atts( array(
		'id' => NULL,
		'showcontestants' => 1,
		'message' => 1,
		'loggeduser'=>$vote_onlyloggedcansubmit,
		), $atts ));
	 
    if($showcontestants){
        echo do_shortcode('[showcontestants id="'.$id.'" forcedisplay=1 showtimer=0 showform=1 hideerrorcont=1 ]'); 
        return;  
    }
    
    $option = $id . '_' . VOTES_SETTINGS;
    $options_category =  get_option($option);
    if(is_array($options_category))
	$image_contest = $options_category['imgcontest'];
    else
	$image_contest = '';
	
	
	$dynamic_id = str_replace(',','',$id);
	
	
	if($dynamic_id!=''){
		$votes_start_time=get_option($dynamic_id . '_' . VOTES_TAXSTARTTIME);
	}else{
		$votes_start_time='';
	}
	$current_time = current_time( 'timestamp', 0 );
	if($votes_start_time!='' && strtotime($votes_start_time) < $current_time)
	{
		return;
	}
   ?>
    <script type="text/javascript">
	jQuery(document).ready(function($) {
		<?php if($options['vote_disable_jquery_validate']!='on'){ ?>
		if(!jQuery().validate) {
			jQuery('head').after("<script type='text/javascript' src='<?php echo VOTES_PATH. "scripts/jquery.validate.min.js" ?>'>");
		}
		<?php } ?>
		jQuery(function(){ 
			jQuery('#add-contestants<?php echo $dynamic_id; ?>').validate({
				rules: {
				'contestant-title': "required",
				},
				messages: {
				'contestant-title': "<?php __('Enter the contestant title','voting-contest'); ?>",
				}
			});	
		  });
          	
	});
		var zn_do_login=new Object();
		zn_do_login.ajaxurl = '<?php echo admin_url( 'admin-ajax.php' ) ?>';
    </script>
    
   <div id="add-contestants-wrapper">	
    <?php
    if(get_term_by( 'id', $id, VOTES_TAXONOMY)) { 
     if($loggeduser!='' && !is_user_logged_in()){
         $class="logged_in_enabled";
         votes_custom_registration_fields_show();
    ?>  
        
        <a style="color:#fff;" class="vote_click_login_do<?php echo $dynamic_id; ?>" href="javascript:" onclick="ppOpen('#login_panel', '800',1)" ></a>
            
    <?php                  
     }else{$class="loggin_disabled";}
        global $wpdb; 
        $sql = "SELECT * FROM " . VOTES_ENTRY_CUSTOM_TABLE . " WHERE admin_only  = 'Y'  AND delete_time = 0 order by sequence";
        $questions = $wpdb->get_results($sql);
    ?>
    
	<div class="votes-btn votes-toggle-form <?php echo $class; ?> <?php echo $dynamic_id; ?>"><?php _e('Submit Entry','voting-contest'); ?></div><div class="clear"></div>
    <input type="hidden" name="open_button_text" id="open_button_text" value="<?php _e('Submit Entry','voting-contest'); ?>">
	<input type="hidden" name="close_button_text" id="close_button_text" value="<?php _e('Close','voting-contest'); ?>">
	
	<?php
	$status = votes_is_addform_blocked($id);
	if(!$status){
		$formProcessed = $formError  = FALSE;
		if(isset($_POST['savecontestant']) && isset($_POST['contestantform'.$dynamic_id])) {
			
			$error = new WP_Error();
			$supportedFormat = array('jpg', 'jpeg', 'png', 'gif', 'JPG', 'JPEG', 'PNG', 'GIF');
			$uploadedMeta = wp_check_filetype_and_ext('contestant-image', $_FILES['contestant-image']['name']);
			
			if(!get_term_by( 'id', $_POST['contest-id'], VOTES_TAXONOMY)) {
				$error->add(__('Invalid Save','voting-contest'), '<strong>'.__('Error','voting-contest').'</strong>: '.__('Some problem in Saving. Please Try Later','voting-contest'));
			}
			
			$contestant_title = strip_tags($_POST['contestant-title']);
			$contestant_desc = $_POST['contestant-desc'.$dynamic_id];
			$photographer_name = strip_tags($_POST['photographer-name']);
			if(!trim($contestant_title)) {
				$error->add(__('Invalid Title','voting-contest'), '<strong>'.__('Error','voting-contest').'</strong>: '.__('Enter the Contestants Title','voting-contest'));
			}
            
            //Get the contestant_desc field authentication
            $desc_rs = wp_voting_get_contestant_desc();
            if($desc_rs[0]->admin_only == "Y"):         
                if($desc_rs[0]->required == "Y"):              
                    if(!trim($contestant_desc)) {
				        $error->add(__('Invalid Description','voting-contest'), '<strong>'.__('Error','voting-contest').'</strong>: '.__('Enter the Contestants Description','voting-contest'));
			     }
                endif;                 
            endif;
			
			if($image_contest==''){
				if(($_FILES['contestant-image']['error']) || ($_FILES['contestant-image']['size'] <=0 )) {
					$error->add('Invalid File', '<strong>'.__('Error','voting-contest').'</strong>: '.__('Problem in File Upload','voting-contest'));
				}
				else if(!in_array($uploadedMeta['ext'], $supportedFormat)) {
					$error->add('Invalid File Format', '<strong>'.__('Error','voting-contest').'</strong>: '.__('Invalid File Format. (Note: Supported File Formats ','voting-contest').implode($supportedFormat, ', ').')');
				}
			}
			
			if(!trim($photographer_name)) {
				//$error->add('Invalid Photograpername', '<strong>Error</strong>: Enter the Contestants Name');
			}
            
            if(!empty($questions)){
                $posted_val=array();
                foreach($questions as $custom_fields){
                   if($custom_fields->system_name != 'contestant-desc'):
                   $posted_val[$custom_fields->system_name]=$_POST[$custom_fields->system_name];  
                   if($custom_fields->required=='Y'){ 
                        if($_POST[$custom_fields->system_name]==''){
                           $error->add('Invalid '.$custom_fields->question, '<strong>'.__('Error','voting-contest').'</strong>:'.$custom_fields->required_text);                                                 
                        }
                   } 
                   endif;
                }
            }              
			if (count($error->get_error_codes())) {
				$formError = TRUE;
				?>
				<div class="contestants-errors">
					<?php
					foreach ($error->get_error_codes() as $errcode) {
						echo '<div class="error-rows">'.$error->get_error_message($errcode) . '</div>';
					}
					?>
				</div>
				<?php
				}
				else {			
					
					global $user_ID;
					$args = array(			 
						  'post_author' => $user_ID,
						  'post_content' => $contestant_desc,
						  'post_status' => 'pending' ,
						  'post_type' => VOTES_TYPE,
						  'post_title' => $contestant_title,
						  
					); 
                    
                    //Added as new feature table
		    $post_track = "CREATE TABLE IF NOT EXISTS ".VOTES_POST_ENTRY_TRACK." (
                                  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                                  `user_id_map` int(11) NOT NULL,
                                   `ip` VARCHAR( 255 ) NOT NULL,
                                   `count_post` INT NOT NULL,
                                   PRIMARY KEY (`id`)
                                  )ENGINE=InnoDB";
		    $wpdb->query($post_track);	
            		     
		    $option_setting = get_option(VOTES_SETTINGS);
            $cont_details = array('contestant_title' => $contestant_title, 'contestant_desc' => $contestant_desc);
		    if($options_category['vote_contest_entry_person']!=''){
			$user_ID = get_current_user_id();
		
			if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARTDED_FOR'] != '') {
			    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
			    $ip = $_SERVER['REMOTE_ADDR'];
			}
			
			//$ip = $_SERVER['REMOTE_ADDR'];
			$query = "Select * from ".VOTES_POST_ENTRY_TRACK." where user_id_map='".$user_ID."'";
			$get_count_track = $wpdb->get_results($query);
			$count_val = count($get_count_track);
            
            
		     
			if($count_val>0){
				if($options_category['vote_contest_entry_person'] > $get_count_track[0]->count_post)
				{
					kses_remove_filters(); 
					$post_id = wp_insert_post($args);
					kses_init_filters() ;
					$new_count = $get_count_track[0]->count_post+1;
					$save_sql = "UPDATE " . VOTES_POST_ENTRY_TRACK . " SET count_post=" . $new_count . " WHERE id='" .$get_count_track[0]->id. "'";
					$wpdb->query($save_sql);
                    
                    wp_voting_send_notify_email($option_setting,$post_id,$cont_details);
					
				}else{
				  $formError = TRUE;
				?>
				<div class="contestants-errors">
					<div class="error-rows"><strong><?php _e('Error:','voting-contest'); ?></strong> <?php _e('You Already Submitted ','voting-contest'); ?><?php echo $get_count_track[0]->count_post;?> <?php _e('Entries.','voting-contest'); ?></div>
				</div>
				
				<?php
				
					//return;
				}
				
			}else{
				$save_sql = 'INSERT INTO `' . VOTES_POST_ENTRY_TRACK . '` (`user_id_map`,`ip`,
					`count_post`) VALUES ("' . $user_ID . '", "' . $ip . '", 1) ';
				$wpdb->query($save_sql);
				kses_remove_filters(); 
				$post_id = wp_insert_post($args);
				kses_init_filters() ;
                wp_voting_send_notify_email($option_setting,$post_id,$cont_details);
			}
			
		    }else{
				kses_remove_filters(); 
				$post_id = wp_insert_post($args);
				kses_init_filters() ;
                wp_voting_send_notify_email($option_setting,$post_id,$cont_details);
		    }
		    		    


   	    
		    
                    update_post_meta($post_id, VOTES_CUSTOMFIELD, 0);        
                    $val_serialized = serialize($posted_val);
                                       
                    $wpdb->query("INSERT INTO " . VOTES_POST_ENTRY_TABLE . " (post_id_map,field_values)". " VALUES ('".$post_id."', '".$val_serialized. "')");
				$attach_id = FALSE;
				if($post_id && !is_wp_error( $post_id )) {
					if($_FILES['contestant-image']['size']) {
						require_once (ABSPATH.'/wp-admin/includes/media.php');
						require_once (ABSPATH.'/wp-admin/includes/file.php');
						require_once (ABSPATH.'/wp-admin/includes/image.php');
						$attach_id = media_handle_upload('contestant-image', $post_id);
					}
					if($attach_id) {							
						set_post_thumbnail($post_id, $attach_id);
						//update_post_meta($attach_id, 'contestant_photographer_name', $photographer_name);
						//update_post_meta($post_id, 'contestant_photographer_name', $photographer_name);
					}	
					wp_set_post_terms( $post_id, $_POST['contest-id'], VOTES_TAXONOMY);
					do_action('votes_save_post', $post_id, $_POST);
					unset($_POST);
					
					$curl = get_permalink(get_the_ID());
					if(stripos($curl, '?')) {
						$curl .= '&success=1';
					}
					else {
						$curl .= '?success=1';
					}
					ob_end_flush();
					ob_start();
					$curl = get_permalink(get_the_ID());
					if(stripos($curl, '?')) {
						$curl .= '&success='.$id;
					}
					else
					{
						$curl .= '?success='.$id;
					}
					echo '<META HTTP-EQUIV=Refresh CONTENT="0; URL='.$curl.'">';
					$formProcessed = $id;
								
				}
				else {
				if(!$formError){
				$formError = TRUE;
				?>
				<div class="contestants-errors">
					<div class="error-rows"><strong><?php _e('Error:','voting-contest'); ?></strong><?php _e(' Problem in Saving. Please try it later.','voting-contest'); ?></div>
				</div>
				<?php
					}
				}
			}
		}
		if((isset($_GET['success']) && ($_GET['success'] == $id)) || $formProcessed == $id ){
			?>
				<div class="contestants-success">
						<div class="success-rows"><?php _e(' Contestants Successfully Added. Waiting for Admin Approval.','voting-contest'); ?></div>
				</div>	
			<?php
		}
		$cls = '';
		if($formError && isset($_POST['contestantform'.$dynamic_id])) {
			$cls = 'show-contestants-form';
		}
		?>
						                        
		<form id="add-contestants<?php echo $dynamic_id; ?>"  name="add-contestants" action="<?php echo get_permalink(get_the_ID()); ?>" method="post" enctype="multipart/form-data" class="form_add_contestant add-contestants-form <?php echo $cls; ?>">
			<div class="contestants-row">
				<div class="contestants-label">
					<label><?php _e('Title','voting-contest'); ?>  <span class="required-mark">*</span></label>
				</div>
				<div class="contestants-field">
					<input type="text" id="contestant-title" name="contestant-title" class="contestant-input" value="<?php echo isset($_POST['contestant-title'])?$_POST['contestant-title']: ''; ?>"/>
				</div>
			</div>
            
            <?php                  
            $desc_rs = wp_voting_get_contestant_desc();    
                   
            //Check if it is made visible in the admin end
            if($desc_rs[0]->admin_only == "Y"):            
                $required_desc = ($desc_rs[0]->required == "Y")?"*":'';
            ?>
                
			<div class="contestants-row">
                
				<div class="contestants-label">
					<label><?php echo $desc_rs[0]->question; ?>  <span class="required-mark"><?php echo $required_desc; ?></span></label>
				</div>
				<div class="contestants-field">
    			<?php
    			if(user_can_richedit()) {
    				$desc_val = isset($_POST['contestant-desc'])?$_POST['contestant-desc']: '';
    				$settings = array('media_buttons' => FALSE,'textarea_rows' => 2,'tinymce' => false);
    				//wp_editor($desc_val, 'contestant-desc'.$id, $settings); 
                    		$version_wp = get_bloginfo('version');
				if($version_wp > '3.3')
					wp_editor($desc_val, 'contestant-desc'.$dynamic_id, $settings);
				 else
				    echo "<textarea name='contestant-desc' id='contestant-desc' >".$desc_val."</textarea>"		
			     ?>
                 <div id="error<?php echo $dynamic_id; ?>"></div>
                 <?php if($desc_rs[0]->required == "Y"): ?>
                    <script type="text/javascript">
                   
                    jQuery(document).ready(function($) {             
                        
                        jQuery(function(){                         
                            jQuery("#<?php echo 'contestant-desc'.$dynamic_id; ?>").rules( "add", {
                                required:true,                                
                                messages:{
                                    required:"<?php _e('Enter the contestant description','voting-contest'); ?>"
                                }
                            });    
                        });                     
                    });
		               
		function add_description_contestant_vote(term_id) {
			<?php if($version_wp > '3.3') { ?>			
			if(jQuery('#contestant-desc'+term_id+'_parent').is(":visible")){			
				tinymce.dom.Event.add(tinyMCE.getInstanceById("contestant-desc"+term_id).getWin(), 'focusout', function(e) {
				   if(tinyMCE.getInstanceById("contestant-desc"+term_id).getContent()==''){
					jQuery("#contestant-desc"+term_id).rules( "add", {
					     required:true,
					     messages:{
						 required:"<?php _e('Enter the contestant description','voting-contest'); ?>"
					     }
					 }); 
				    }else{
					 jQuery("#contestant-desc"+term_id).rules("remove");
					 jQuery("#contestant-desc"+term_id).removeClass('error');
					 var label_hide_id = "contestant-desc"+term_id;
					 jQuery( "label[for='"+label_hide_id+"']").remove();
				    }    
				});
			}
			<?php } ?>
		}		
                </script>
                <?php endif; ?>
                <?php
                }
                else {
                ?>
                <textarea id="contestant-desc" name="contestant-desc" class="contestant-desc"><?php echo isset($_POST['contestant-desc'])?$_POST['contestant-desc']: ''; ?></textarea>
                <?php
                }
                ?>
                </div>
		</div>
        
        <?php endif; ?>
		
	<?php if($image_contest==''){ ?>
		<div class="contestants-row">
			<div class="contestants-label">
				<label><?php _e('Image  ','voting-contest'); ?><span class="required-mark">*</span> </label>
			</div>
			<div class="contestants-field">
				<input type="file" id="contestant-image<?php echo $dynamic_id; ?>"  name="contestant-image" class="contestant-input" />
			</div>
			
			<script type="text/javascript">
			  jQuery(document).ready(function($) {
			      jQuery(function(){ 
				  jQuery("#contestant-image<?php echo $dynamic_id; ?>").rules( "add", {
				      required:true,
				      messages:{
					  required: "<?php _e('Please upload the file','voting-contest'); ?>"
				      }
				  });  
			      });
			  });
			  </script>   
     
		</div>
        <?php } ?>
	
            <!-- Custom Fields -->                    
            <!-- Front end contestant custom fields code added -->  
            <?php
            if(!empty($questions)){
                $i=0;
            foreach($questions as $custom_fields){
            if($custom_fields->system_name != 'contestant-desc'):
            ?>
            <div class="contestants-row">
                <div class="contestants-label">
    				<label>
                    <?php 
                    if($custom_fields->question_type=='TEXT' || $custom_fields->question_type=='TEXTAREA'){
                        echo ''.$custom_fields->question;
                    }else{
                       echo ''.$custom_fields->question; 
                    }
                    ?>
                    <?php if($custom_fields->required=='Y'){?>
                          <span class="required-mark">*</span>
    			    <?php } ?>
					</label>
                </div>
                
                <div class="contestants-field">
                  <?php if($custom_fields->question_type=='TEXT'){ ?>
    					<input style="width: 100%;" type="<?php echo $custom_fields->question_type; ?>" id="<?php echo $custom_fields->system_name.$dynamic_id; ?>" name="<?php echo $custom_fields->system_name; ?>" />
                        <?php if($custom_fields->required=='Y'){?>
                        <script type="text/javascript">
                            jQuery(document).ready(function($) {
                                jQuery(function(){ 
                                    jQuery("#<?php echo $custom_fields->system_name.$dynamic_id; ?>").rules( "add", {
                                        required:true,
                                        messages:{
                                            required:"<?php echo ($custom_fields->required_text)?$custom_fields->required_text:"This Field is required"; ?>"
                                        }
                                    });    
                                });
                            });   
                        </script>
                        <?php } ?>
                        
                  <?php }elseif($custom_fields->question_type=='TEXTAREA'){  ?>
                        <textarea style="width: 100%;" rows="1" id="<?php echo $custom_fields->system_name.$dynamic_id; ?>" name="<?php echo $custom_fields->system_name; ?>" ></textarea> 
                        <?php if($custom_fields->required=='Y'){?>
                        <script type="text/javascript">
                            jQuery(document).ready(function($) {
                                jQuery(function(){ 
                                    jQuery("#<?php echo $custom_fields->system_name.$dynamic_id; ?>").rules( "add", {
                                        required:true,
                                        messages:{
                                            required:"<?php echo ($custom_fields->required_text)?$custom_fields->required_text:"This Field is required"; ?>"
                                        }
                                    });    
                                });
                            });   
                        </script>
                        <?php } ?>
                  <?php }elseif($custom_fields->question_type=='SINGLE'){  ?>
                       <?php $values = explode(',',$custom_fields->response); 
                             foreach($values as $val){
                        ?>
                        <span id="add_contestant_radio"> 
                        <input class="stt_float"  type="radio" name="<?php echo $custom_fields->system_name; ?>[]" value="<?php echo $val; ?>" id="<?php echo $custom_fields->system_name.$dynamic_id; ?>" /> <span class="stt_float"><?php echo $val; ?></span>
                        </span>
                        <?php } if($custom_fields->required=='Y'){?>
                        <script type="text/javascript">
                            jQuery(document).ready(function($) {       
                                jQuery(function(){   
                                    jQuery("#<?php echo $custom_fields->system_name.$dynamic_id; ?>").rules( "add", {
                                        required:true,
                                        messages:{
                                           required:"<?php echo ($custom_fields->required_text)?$custom_fields->required_text:"This Field is required"; ?>"
                                        }
                                    });    
                                });
                            });   
                        </script>
                        <?php } ?>
                  <?php  }elseif($custom_fields->question_type=='MULTIPLE'){  ?>
                      <?php $values = explode(',',$custom_fields->response); 
                            foreach($values as $val){ ?>
                      <span id="add_contestant_radio"> 
                      <input type="checkbox" class="stt_float" name="<?php echo $custom_fields->system_name; ?>[]" value="<?php echo $val; ?>" id="<?php echo $custom_fields->system_name.$dynamic_id; ?>" /><span class="stt_float"><?php echo $val; ?></span>  </span>
                      <?php } if($custom_fields->required=='Y'){?>      
                        <script type="text/javascript">
                            jQuery(document).ready(function($) {
                                jQuery(function(){ 
                                    jQuery("#<?php echo $custom_fields->system_name.$dynamic_id; ?>").rules( "add", {
                                        required:true,
                                        messages:{
                                           required:"<?php echo ($custom_fields->required_text)?$custom_fields->required_text:"This Field is required"; ?>"
                                        }
                                    });    
                                });
                            });   
                        </script>   
                         <?php } ?>
                  <?php  } elseif($custom_fields->question_type=='DROPDOWN'){  ?>
                  <?php $values = explode(',',$custom_fields->response); ?>
                        <select style="width: 100%;padding: 0.428571rem;border: 1px solid #CCCCCC;border-radius: 3px 3px 3px 3px;" name="<?php echo $custom_fields->system_name; ?>" id="<?php echo $custom_fields->system_name.$dynamic_id; ?>">
                        <option value="">Select</option>
                        <?php foreach($values as $val){ ?>
                              <option value="<?php echo $val; ?>"><?php echo $val; ?></option>
                        <?php } ?>
                        </select> 
                        
                        <?php if($custom_fields->required=='Y'){?>
                        <script type="text/javascript">
                            jQuery(document).ready(function($) {
                                jQuery(function(){ 
                                    jQuery("#<?php echo $custom_fields->system_name.$dynamic_id; ?>").rules( "add", {
                                        required:true,
                                        messages:{
                                            required:"<?php echo ($custom_fields->required_text)?$custom_fields->required_text:"This Field is required"; ?>"
                                        }
                                    });    
                                });
                            });   
                        </script>
                        <?php } ?>
                  <?php } ?>
                                            			
                    </div>
			</div>
        <?php
        $i++;  
        endif;  
       } 
    }
    ?>    
        
    <!-- Front end contestant custom fields code added -->                              
            	<div class="contestants-row">
    				<div class="contestants-label">
    					
    				</div>
    				<div class="contestants-field">
    					<input type="hidden" id="contestantform<?php echo $dynamic_id;?>"  name="contestantform<?php echo $dynamic_id;?>" value="contestantform<?php echo $dynamic_id;?>"/>
    					<input type="submit" id="savecontestant"  name="savecontestant" value="<?php _e('Save','voting-contest'); ?>"/>
    				</div>
    			</div>
    
    			<input type="hidden" id="contest-id"  name="contest-id" value="<?php echo $id; ?>"/>
    		</form>
    	
                        
			<?php
		}else {
			if($message) {
				?>
				<div class="warning activation-warning constestants-warning"><?php echo $status; ?></div>
				<?php
			}  
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function($) {
                    jQuery(function(){ 
                        jQuery("#add-contestants-wrapper").hide();
                        });    
                    });                  
            </script>
            <?php         
                                 
		}
        ?>
        </div>
    	<div class="clear"></div>
        <?php
		
		/*if($showcontestants)
			echo do_shortcode('[showcontestants id="'.$id.'" forcedisplay=1 showtimer=0 showform=0 hideerrorcont=1 ]'); */ 
            
	}
	else {
		?>
		<div class="warning login-warning constestants-warning"><?php _e('Must be a Problem in Displaying Form.','voting-contest'); ?></div>
		<?php
	}
		
		$out = ob_get_contents();
        ob_end_clean();
        return $out;
	}
}

if(!function_exists('wp_votes_start_contestants')) {
	function wp_votes_start_contestants($atts) {
		global $wpdb;
		ob_start();
		
		extract( shortcode_atts( array(
			  'id' => NULL,
			  'showcontestants' => 1,
			  'message' => 1
		 ), $atts ) );
		 if(!$id){
				$id = votes_get_closest_contestid();
		}
		$valid = FALSE;
		 if(!is_wp_error($curterm = get_term( $id, VOTES_TAXONOMY)) && isset($curterm) ) {
			$valid = TRUE;
            
            
		 $idarr = explode(',', $id);
		 $curterm = $time = NULL;
		 if (count($idarr) > 1) {
				$time = get_option('VOTES_GENERALSTARTTIME');
		 } 
		 else if( !is_wp_error($curterm = get_term( $id, VOTES_TAXONOMY)) && isset($curterm) ) {
				$time = get_option($curterm->term_id . '_' . VOTES_TAXSTARTTIME);
		 }
		 if($time != '0' && $time) {
			$timeentered = strtotime(str_replace("-", "/", $time));
			//$currenttime = strtotime(date('Y-m-d H:i:s'));
			
			$currenttime = current_time( 'timestamp', 0 );
			$time = date('Y-m-d-H-i-s', strtotime(str_replace('-', '/', $time)));
			$currenttime1 = str_replace(' ','-',str_replace(':','-',current_time( 'mysql', 0 )));
			if($currenttime <= $timeentered) {
 			
				if($showcontestants && $valid ){
				echo do_shortcode('[showcontestants id="'.$id.'" forcedisplay=1 showtimer=1 hidecontestants=1]'); 
				    return;
				}  
			?>
			<div class="countdown_wrapper">
				<div class="countdown_desc_wrapper countdown_startdesc_wrapper">
					<div class="countdown_tag"><?php _e('Voting Starts In:','voting-contest'); ?></div>
					
				</div>
				<div class="countdown_start_timer countdown_dashboard" id="countdown_start_dashboard<?php echo $id; ?>" data-datetimer="<?php echo $time; ?>" data-currenttimer="<?php echo $currenttime1; ?>">
				<div class="dash weeks_dash">
				
				<div class="digit">0</div>
				<div class="digit">0</div>
				<span class="dash_title"><?php _e('weeks','voting-contest'); ?></span>
			</div>
					<div class="dash days_dash">
						<div class="digit">0</div>
						<div class="digit">0</div>
						<span class="dash_title"><?php _e('days','voting-contest'); ?></span>
					</div>

					<div class="dash hours_dash">
						<div class="digit">0</div>
						<div class="digit">0</div>
						<span class="dash_title"><?php _e('hours','voting-contest'); ?></span>
					</div>

					<div class="dash minutes_dash">
						<div class="digit">0</div>
						<div class="digit">0</div>
						<span class="dash_title"><?php _e('minutes','voting-contest'); ?></span>
					</div>

					<div class="dash seconds_dash">
						<div class="digit">0</div>
						<div class="digit">0</div>
						<span class="dash_title"><?php _e('seconds','voting-contest'); ?></span>
					</div>
				</div>
				
			</div>
			<?php
			}
			else {
				if($message) {
					?>
				   <div class="votes_error error"><?php _e('No Upcoming Contest','voting-contest'); ?></div>
				  <?php
				}
			}
			
		 }else {
			if($message) {
			?>
			   <div class="votes_error error"><?php _e('Contest already Started.','voting-contest'); ?></div>
			  <?php
			}
		 }
		 }else {
			if($message) {
			  ?>
			   <div class="votes_error error"><?php _e('No Upcoming Contest','voting-contest'); ?></div>
			  <?php
			}
		 }
		 ?>
		<div class="clear"></div>

		 <?php
		 if($showcontestants && $valid )
			echo do_shortcode('[showcontestants id="'.$id.'" forcedisplay=1 showtimer=0]');
            
            
		$out = ob_get_contents();
        ob_end_clean();
        return $out;
	}
}

if(!function_exists('wp_votes_end_contestants')) {
	function wp_votes_end_contestants($atts) {
		ob_start();
		extract( shortcode_atts( array(
			  'id' => NULL
		 ), $atts ) );
		 if(!$id) {
			return '<div class="votes_error error">'.__('Timer Not Available','voting-contest').'</div>';
		 }
		 $idarr = explode(',', $id);
		 $curterm = $time = NULL;
		 if (count($idarr) > 1) {			
				//$time = get_option(VOTES_GENERALEXPIRATIONFIELD);
				return FALSE;
		 } 
		 else if( !is_wp_error($curterm = get_term( $id, VOTES_TAXONOMY)) && isset($curterm)) {
				$time = get_option($curterm->term_id . '_' . VOTES_TAXEXPIRATIONFIELD);
		 }
		 if($time != '0' && $time) {
			$timeentered = strtotime(str_replace("-", "/", $time));
			$currenttime = current_time( 'timestamp', 0 );
			$currenttime1 = str_replace(' ','-',str_replace(':','-',current_time( 'mysql', 0 )));
			
			$time = date('Y-m-d-H-i-s', strtotime(str_replace('-', '/', $time)));
			if($currenttime <= $timeentered) {
			?>
			<div class="countdown_wrapper">
				<div class="countdown_desc_wrapper countdown_enddesc_wrapper">
					<div class="countdown_tag"><?php _e('Contest Ends In:','voting-contest'); ?></div>
						
					</div>
					<div class="countdown_end_timer countdown_dashboard" id="countdown_end_dashboard<?php echo $id; ?>" data-datetimer="<?php echo $time; ?>"  data-currenttimer="<?php echo $currenttime1; ?>">
					<div class="dash weeks_dash">
					
					<div class="digit">0</div>
					<div class="digit">0</div>
					<span class="dash_title"><?php _e('weeks','voting-contest'); ?></span>
				</div>
					<div class="dash days_dash">
						<div class="digit">0</div>
						<div class="digit">0</div>
						<span class="dash_title"><?php _e('days','voting-contest'); ?></span>
					</div>

					<div class="dash hours_dash">
						<div class="digit">0</div>
						<div class="digit">0</div>
						<span class="dash_title"><?php _e('hours','voting-contest'); ?></span>
					</div>

					<div class="dash minutes_dash">
						<div class="digit">0</div>
						<div class="digit">0</div>
						<span class="dash_title"><?php _e('minutes','voting-contest'); ?></span>
					</div>

					<div class="dash seconds_dash">
						<div class="digit">0</div>
						<div class="digit">0</div>
						<span class="dash_title"><?php _e('seconds','voting-contest'); ?></span>
					</div>
				</div>
				
			</div>
			<?php
			}else{?>
			<div class="countdown_wrapper">
				<div class="countdown_title"><?php _e('Contest Ended','voting-contest'); ?></div>
			</div>
			<?php
			}
		 }
		 
		 ?>
		 
		<div class="clear"></div>
		 <?php
		$out = ob_get_contents();
		ob_end_clean();
        return $out;
	}
}

if(!function_exists('wp_votes_total_count_votes')) {
	function wp_votes_total_count_votes($id) {
		ob_start();
		global $wpdb;
		?>
		<div class="clear"></div>
		<?php      
		$setting_category = get_option($id . '_' . VOTES_SETTINGS);
		if($setting_category['total_vote_count']=='on'){
			
            // WP_Query arguments
            $args = array (
            	'post_type'    => VOTES_TYPE,
                'post_status'  => 'publish',
                'tax_query'    => array(
                		array(
                			'taxonomy' => VOTES_TAXONOMY,
                			'field' => 'id',
                			'terms' => $id
                		)
                	)
            );            
            
            // The Query
            $query = new WP_Query( $args );
            $votes_count = 0;
           
            if ( $query->have_posts() ) {
            	while ( $query->have_posts() ) {
            		$query->the_post();
                    $votes_count += get_post_meta(get_the_id(),'votes_count',true);          
            	}
            } 
            
            wp_reset_postdata();           
            
			if($votes_count){
			$term_name = get_term($id,VOTES_TAXONOMY);
			?>
			<div><span class="total_result_count"><?php echo __('Total votes for the Contest','voting-contest').'"'.$term_name->name.'": '; ?><span class="total_cnt_vote_res<?php echo $id; ?>"><?php echo $votes_count; ?></span></span></div>		
			<?php
			}
			
		}	
		$out = ob_get_contents();
		ob_end_clean();
		return $out;
	}
}
/**
 * Function used to get the details about the contestant_desc textarea
 * FROM the table VOTES_ENTRY_CUSTOM_TABLE 
 */

if(!function_exists('wp_voting_get_contestant_desc')){
    function wp_voting_get_contestant_desc(){
        global $wpdb;            
        $sql     = "SELECT * FROM " . VOTES_ENTRY_CUSTOM_TABLE . " WHERE system_name = 'contestant-desc'";
        $desc_rs = $wpdb->get_results($sql);    
        return $desc_rs;
    }
}
/**
 * Function to show the description in corresponding view   
 */
if(!function_exists('wp_voting_get_text_description')){
    function wp_voting_get_text_description($term_id){        
        $option = get_option($term_id . '_' . VOTES_SETTINGS);
        return $option; 
    } 
} 

/**
  * Function to add the description filed if it is not available
  */

if(!function_exists('wp_voting_add_description')){
    function wp_voting_add_description()
    {
        $desc_rs = wp_voting_get_contestant_desc(); 
            if(count($desc_rs[0]) == 0):
                global $wpdb;
                //Add the Custom Field in the Table VOTES_ENTRY_CUSTOM_TABLE
                $wpdb->insert( 
                	VOTES_ENTRY_CUSTOM_TABLE, 
                	array( 
                		'question_type' => 'TEXTAREA', 
                        'question'      => 'Description',
                		'system_name' => 'contestant-desc',
                        'required'    => 'Y',
                        'admin_only'  => 'Y', 
                        'admin_view'  => 'Y',  
                	), 
                	array( 
                		'%s','%s','%s','%s' ,'%s','%s'
                	) 
                );
        endif;
    }
}
add_action('init','wp_voting_add_description');

/*
 * Send Notification Email   
 */

if(!function_exists('wp_voting_send_notify_email')){
    function wp_voting_send_notify_email($option_setting,$post_id,$cont_details){
        if($option_setting['vote_notify_mail']=='on'){
            
        	if($option_setting['vote_admin_mail']!='')
        	$admin_email = $option_setting['vote_admin_mail'];
        	else
        	$admin_email = get_settings('admin_email');
            
            $contestant_title = $cont_details['contestant_title'];
            $contestant_desc = $cont_details['contestant_desc'];        	
        	
        	$admin_url1 = get_bloginfo('url');
        	$admin_url = $admin_url1.'&#47;wp-admin&#47;post.php?post='.$post_id.'&action=edit';
        		
            $subject = "New Contestant Entry Is Submitted";
            $message ='
            <html lang="en">
             <head>
              <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
              <title>'. get_bloginfo('name').'</title>
              <style type="text/css">
              a:hover { text-decoration: none !important; }
              .header h1 {color: #47c8db !important; font: bold 32px Helvetica, Arial, sans-serif; margin: 0; padding: 0; line-height: 40px;}
              .header p {color: #c6c6c6; font: normal 12px Helvetica, Arial, sans-serif; margin: 0; padding: 0; line-height: 18px;}
          
              .content h2 {color:#646464 !important; font-weight: bold; margin: 0; padding: 0; line-height: 26px; font-size: 18px; font-family: Helvetica, Arial, sans-serif;  }
              .content p {color:#767676; font-weight: normal; margin: 0; padding: 0; line-height: 20px; font-size: 12px;font-family: Helvetica, Arial, sans-serif;}
              .content a {color: #0eb6ce; text-decoration: none;}
              .footer p {font-size: 11px; color:#7d7a7a; margin: 0; padding: 0; font-family: Helvetica, Arial, sans-serif;}
              .footer a {color: #0eb6ce; text-decoration: none;}
              </style>
           </head>
           
          <body style="margin: 0; padding: 0; background: #4b4b4b;" bgcolor="#4b4b4b">
               
                <table cellpadding="0" cellspacing="0" border="0" align="center" width="100%" style="padding: 35px 0; background: #4b4b4b;">
                  <tr>
                    <td align="center" style="margin: 0; padding: 0; background:#4b4b4b;" >
                        <table cellpadding="0" cellspacing="0" border="0" align="center" width="600" style="font-family: Helvetica, Arial, sans-serif; background:#2a2a2a;" class="header">
                            <tr>
                            <td width="20"style="font-size: 0px;">&nbsp;</td>
                            <td width="580" align="left" style="padding: 18px 0 10px;">
                                <h1 style="color: #47c8db; font: bold 32px Helvetica, Arial, sans-serif; margin: 0; padding: 0; line-height: 40px;">'.get_bloginfo('name').'</h1>
                                <p style="color: #c6c6c6; font: normal 12px Helvetica, Arial, sans-serif; margin: 0; padding: 0; line-height: 18px;">'.get_bloginfo('description').'</p>
                            </td>
                          </tr>
                        </table><!-- header-->
                
                        <table cellpadding="0" cellspacing="0" border="0" align="center" width="600" style="font-family: Helvetica, Arial, sans-serif; background: #fff;" bgcolor="#fff">
                            
                            <tr>
                            <td width="600" valign="top" align="left" style="font-family: Helvetica, Arial, sans-serif; padding: 20px 0 0;" class="content">
                                <table cellpadding="0" cellspacing="0" border="0"  style="color: #717171; font: normal 11px Helvetica, Arial, sans-serif; margin: 0; padding: 0;" width="600">
        
                                <tr>
                                    <td width="21" style="font-size: 1px; line-height: 1px;">&nbsp;</td>
                                    <td style="padding: 20px 0 0;" align="left">            
                                        <h2 style="color:#646464; font-weight: bold; margin: 0; padding: 0; line-height: 26px; font-size: 18px; font-family: Helvetica, Arial, sans-serif; ">New Contestant Entry has been submitted</h2>
                                    </td>
                                    <td width="21" style="font-size: 1px; line-height: 1px;">&nbsp;</td>
                                </tr>
                    
                                <tr>
                                    <td width="21" style="font-size: 1px; line-height: 1px;"><p>&nbsp;</p></td>
                                    <td style="padding: 15px 0 15px;"  valign="top">
                                        <p>&nbsp;</p>
                                        <p style="color:#767676; font-weight: normal; margin: 0; padding: 0; line-height: 20px; font-size: 12px;font-family: Helvetica, Arial, sans-serif;"> <b>Contestant Title: </b>'.$contestant_title.'</p><br>
                        
                                        <p style="color:#767676; font-weight: normal; margin: 0; padding: 0; line-height: 20px; font-size: 12px;font-family: Helvetica, Arial, sans-serif;"> <b>Contestant Description:</b> '.$contestant_desc.'</p><br>
                                        <p style="color:#767676; font-weight: normal; margin: 0; padding: 0; line-height: 20px; font-size: 12px;font-family: Helvetica, Arial, sans-serif;"><a href="'.$admin_url.'">Click here</a> to view the entry</p><br/>
             
                               </td>
                                </tr>
                                </table>    
                            </td>
                            
                          </tr>
                            <tr>
                                <td width="600" align="left" style="padding: font-size: 0; line-height: 0; height: 3px;" height="3" colspan="2">&nbsp;</td>
                              </tr> 
                        </table><!-- body -->
                        <table cellpadding="0" cellspacing="0" border="0" align="center" width="600" style="font-family: Helvetica, Arial, sans-serif; line-height: 10px;" class="footer"> 
                        <tr>
                            <td align="center" style="padding: 5px 0 10px; font-size: 11px; color:#7d7a7a; margin: 0; line-height: 1.2;font-family: Helvetica, Arial, sans-serif;" valign="top">
                                <br><p style="font-size: 11px; color:#7d7a7a; margin: 0; padding: 0; font-family: Helvetica, Arial, sans-serif;">This is an Automated Email</p>
                                <p style="font-size: 11px; color:#7d7a7a; margin: 0; padding: 0; font-family: Helvetica, Arial, sans-serif;">Sent From <webversion style="color: #0eb6ce; text-decoration: none;"><a href="'.get_bloginfo('url').'">'.get_bloginfo('name').'</a></webversion>. Please Do not respond </p>
                            </td>
                          </tr>
                        </table><!-- footer-->
                    </td>
                    </td>
                </tr>
            </table>
          </body>
        </html>';
        
        if($option_setting['vote_from_name']!='')
        	$headers[] = 'From: '.$option_setting['vote_from_name'];
        
        	$headers[] = "Content-type: text/html";
        
        	wp_mail($admin_email, $subject,$message ,$headers);
        }
    }
}

if(!function_exists('wp_votes_show_profilescreen')){
    function wp_votes_show_profilescreen($atts)
    {
        ob_start();
        global $wpdb;
	
    	if(isset($_SESSION['arg_category_id_vote']))
    		unset($_SESSION['arg_category_id_vote']);
            
    	if(isset($_SESSION['arg_shortcode_vote']))
    		unset($_SESSION['arg_shortcode_vote']);
    	
    	if(isset($_SESSION['vote_login_function_ran']))
    		unset($_SESSION['vote_login_function_ran']);
    
        votes_custom_registration_fields_show();
        if(is_user_logged_in()){ 
            
           global $user_ID;     
           $votes_settings = get_option(VOTES_SETTINGS); 
           $file_fb_default = $votes_settings['file_fb_default'];  
           $file_facebook   = $votes_settings['file_facebook'];
           $file_tw_default = $votes_settings['file_tw_default'];
           $twt_img_path    = $votes_settings['twt_img_path'];
                      
           extract( shortcode_atts( array(
        		'form' => '1',
        		'contests' => '1',              
      	   ), $atts, 'profilescreen' ) );
           
           if($atts['contests'] == '1' || !isset($atts['contests'])):       
           
           $paged = get_query_var('paged') ? get_query_var('paged') : 1;
           $post_per_page = ($atts['postperpage'] == null)?get_option('posts_per_page'):$atts['postperpage'];
           $postargs = array(
				'post_type'     => VOTES_TYPE,
				'post_status'   => array( 'pending', 'publish','future' ),
				'orderby'       => 'id',
                'author'        => $user_ID,	
                'posts_per_page'=> $post_per_page,
                'paged'         => $paged,
	       );   
           
           //Delete Single  Contestants Code           
           if(isset($_POST['votes_single'])){           
                echo voting_delete_single_contesnts($_POST['votes_single']);
           }
              
            
           $contest_post = new WP_Query($postargs);
           if ($contest_post->have_posts()) {
                $i = 0;
                ?>
                <div class="voting-profile">
                <input type="hidden" name="confirm_delete_single" id="confirm_delete_single" value="<?php _e('Are you sure you want to delete?','voting-contest'); ?>"/>
                <table class="responsive-table">
                <thead>
                    <tr>
                        <th><?php _e('Image','voting-contest'); ?></th>
                        <th><?php _e('Image Name','voting-contest'); ?></th>
                        <th><?php _e('Upload Date','voting-contest'); ?></th>
                        <th><?php _e('Contest Name','voting-contest'); ?></th>                        
                        <th><?php _e('Votes','voting-contest'); ?></th>
                        <th><?php _e('Delete','voting-contest'); ?></th>
                        <th><?php _e('Share','voting-contest'); ?></th>
                        <th><?php _e('Status','voting-contest'); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php
                while ( $contest_post->have_posts() ) {
                    $class = ($i == 0)?"class='first-row'":'';
                    $contest_post->the_post();
                    $attachment_id = get_post_thumbnail_id($contest_post->post->ID );
                    $image1 = wp_get_attachment_image($attachment_id,'thumbnail');     
                    if($image1 == null){
                        $image1 = "<img src=".VOTES_PATH . 'images/no-image.jpg?'.uniqid()." class='attachment-thumbnail' />";                 }                                           
                    $perma = get_permalink(get_the_ID()); 
                    $status = $contest_post->post->post_status;           
                    
                    ?>
                    <tr>
                    <td><?php echo $image1; ?></td>                        
                    <td><?php echo get_the_title($attachment_id); ?></td>
                    <td><?php echo get_the_date(); ?></td>
                    <td>
                    <?php 
                            if($status == 'publish')
                                echo "<a href='".$perma."'>".get_the_title()."</a>"; 
                            else
                                echo get_the_title();
                    ?>
                    </td>                    
                    <td><?php echo get_post_meta( $contest_post->post->ID , VOTES_CUSTOMFIELD, true ); ?></td>
                    <td>
                    <form name="delete_contestants<?php echo $contest_post->post->ID; ?>" id="delete_contestants<?php echo $contest_post->post->ID; ?>" method="POST">                                                            <input type="hidden" id="votes_single" name="votes_single" value="<?php echo $contest_post->post->ID; ?>" />
                        <a href="javascript:" onclick="javascript:confirm_delete_single('<?php echo $contest_post->post->ID; ?>');" title="<?php _e('Delete','voting-contest'); ?>">
                        <img src="<?php echo VOTES_PATH.'images/delete.png'; ?>" alt="<?php _e('Delete','voting-contest'); ?>" class="submit_image"  />
                        </a>
                    </form>
                    </td>
                    <td>
                    <?php
                    if($status == 'publish'):
                       $up_path =  wp_upload_dir();            					
					   if($file_fb_default=='' && $file_facebook!=''){
						 if(file_exists($up_path['path'].'/'.$file_facebook))
						    $face_img_path = $up_path['url'].'/'.$file_facebook;
						 else
						    $face_img_path = VOTES_PATH.'images/facebook-share.png';
					   }else{
						$face_img_path = VOTES_PATH.'images/facebook-share.png';
					   } 
					    echo '<a target="_blank" class="fb_view_change'.$args['id'][0].' facebook-btn2" href="http://www.facebook.com/sharer.php?u='.$perma.'&amp;t='.urlencode(get_the_title()).'"><img alt="Share on Facebook" class="face_view_change'.$contest_post->post->ID.'" src="'.$face_img_path.'"></a>';
                        
                        if($file_tw_default=='' && $file_twitter!=''){
						      if(file_exists($up_path['path'].'/'.$file_twitter))
							 $twt_img_path = $up_path['url'].'/'.$file_twitter;
						      else
							 $twt_img_path = VOTES_PATH.'images/tweet.png';
						}else{
						     $twt_img_path = VOTES_PATH.'images/tweet.png';
						}
						echo '<a target="_blank" class="tw_view_change'.$args['id'][0].' facebook-btn2" href="http://twitter.com/home?status='.urlencode(get_the_title())."%20".$perma.'"><img alt="Tweet" class="tweet_view_change'.$args['id'][0].'" src="'.$twt_img_path.'"></a><input type="hidden" value="'.$twt_img_path.'" class="tweet_list_img_path" />';
                        
            		endif;			
                    ?>
                    </td>
                    <td><?php echo ucfirst($status); ?></td>
                    </tr>
                    <?php
                    $i++;
            	}
                ?>
                </tbody>
                </table>
                <?php echo voting_wp_pagenavi(array('query' => $contest_post),'profile'); ?>
                </div>
                
                <?php
                
           }
           else {
            	_e('No Contestants Found','voting-contest');
           }
            
           wp_reset_postdata();
           endif;
           
           if($atts['form'] == '1' || !isset($atts['form'])):  
            //add_filter('show_admin_bar', '__return_false');
            /* Get user info. */
            global $current_user, $wp_roles;
            get_currentuserinfo();
            
            /* Load the registration file. */
            require_once( ABSPATH . WPINC . '/registration.php' );
            $error = new WP_Error(); 
            /* If profile was saved, update profile. */
            if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) && $_POST['action'] == 'update-user' )         {
                $error = voting_profile_update($_POST,$error);                
                /* Redirect so the page will show updated info.*/
                if ( count($error->errors) == 0 ) {
                    ?>
                    <div class="contestants-success vote-profile-status">
						<div class="success-rows"><?php _e('Profile Updated Successfully','voting-contest'); ?></div>
				    </div>
                    <?php
                }
                else{                   
                    ?>
                    <div class="contestants-errors vote-profile-status">                        
                        <?php foreach($error->errors as $err): ?>
						<div class="success-rows"><?php echo $err[0]; ?></div>
                        <?php endforeach; ?>
				    </div>
                    <?php                    
                }
            }
            
           if (  count($error->errors) > 0 ) echo '<p class="required-mark">' . implode("<br />", $error) . '</p>';
           ?>
                <form method="post" id="adduser" class="zn_form_profile " action="<?php the_permalink(); ?>">
                <h3 class="m_title"><?php _e('Update Profile','voting-contest')?></h3>
                <div class="inner-container register-panel_add">
                  <div class="register-panel_inner">
                        <label for="user_login">
                            <strong><?php _e('Username', 'voting-contest'); ?></strong>
                            <span class="required-mark">*</span>
                        </label>
                        <p><input class="inputbox" name="user_login" type="text" id="user_login" value="<?php the_author_meta( 'user_login', $current_user->ID ); ?>" disabled="disabled" /></p>
                  </div>
                  
                  <div class="register-panel_inner">
                        <label for="email">
                            <strong><?php _e('E-mail', 'voting-contest'); ?></strong>
                            <span class="required-mark">*</span>
                        </label>
                        <p><input class="inputbox required_vote_custom" name="email" type="text" id="email" value="<?php the_author_meta( 'user_email', $current_user->ID ); ?>" /></p>
                  </div>
                  
                  <div class="register-panel_inner">
                        <label for="first-name">
                            <strong><?php _e('First Name', 'voting-contest'); ?></strong>                            
                        </label>
                        <p><input class="text-input" name="first-name" type="text" id="first-name" value="<?php the_author_meta( 'first_name', $current_user->ID ); ?>"  /></p>
                  </div>
                  
                   <div class="register-panel_inner">
                        <label for="last-name">
                            <strong><?php _e('Last Name', 'voting-contest'); ?></strong>                            
                        </label>
                        <p><input class="text-input" name="last-name" type="text" id="last-name" value="<?php the_author_meta( 'last_name', $current_user->ID ); ?>"  /></p>
                  </div>
                  
                  <div class="register-panel_inner">
                        <label for="nickname">
                            <strong><?php _e('Nickname', 'voting-contest'); ?></strong>                            
                        </label>
                        <p><input class="text-input" name="nickname" type="text" id="nickname" value="<?php the_author_meta( 'nickname', $current_user->ID ); ?>"  /></p>
                  </div>
                    								  
				 					
					<div class="register-panel_inner">
                        <label for="pass1">
                            <strong><?php _e('Password', 'voting-contest'); ?></strong>
                            <span class="required-mark">*</span>
                        </label>
                        <p><input class="text-input" name="pass1" type="password" id="pass1" /></p>
                    </div>
                    
                    <div class="register-panel_inner">
                        <label for="pass2">
                            <strong><?php _e('Repeat Password ', 'voting-contest'); ?></strong>
                            <span class="required-mark">*</span>
                        </label>
                        <p><input class="text-input" name="pass2" type="password" id="pass2" /></p>
                    </div>                    
					
          <?php 
            global $wpdb; 
            $sql = "SELECT * FROM " . VOTES_USER_CUSTOM_TABLE . " WHERE admin_only  = 'Y' AND delete_time=0 order by sequence ";
            $questions = $wpdb->get_results($sql);  
            
            $sql1 = "SELECT * FROM " . VOTES_USER_ENTRY_TABLE. " WHERE user_id_map = '".$current_user->ID."'";
            $registered_entries = $wpdb->get_results($sql1);
            if(!empty($registered_entries)){
                $registration = unserialize($registered_entries[0]->field_values);
            }           
            else
            {
                $registration=array();
            }
            
            if(count($error->errors) != 0){
              foreach($questions as $post_date): 
    				$registration[$post_date->system_name] = $_POST[$post_date->system_name];
              endforeach; 
            }            
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
            <input id="<?php echo $custom_fields->system_name; ?>" type="<?php echo strtolower($custom_fields->question_type); ?>" class="inputbox <?php echo $class; ?>" name="<?php echo $custom_fields->system_name; ?>" placeholder="<?php _e($custom_fields->question);?>" value="<?php echo $registration[$custom_fields->system_name]; ?>" />
            
            <?php } elseif($custom_fields->question_type=='TEXTAREA'){  ?>
        
                <textarea style="width: 100%;" rows="1" id="<?php echo $custom_fields->system_name; ?>" placeholder="<?php _e($custom_fields->question);?>" name="<?php echo $custom_fields->system_name; ?>" class="<?php echo $class; ?>" ><?php echo $registration[$custom_fields->system_name]; ?></textarea>
        
        <?php }elseif($custom_fields->question_type=='SINGLE'){  ?>
        <?php $values = explode(',',$custom_fields->response); 
        foreach($values as $val){
        ?>   
        <span id="add_contestant_radio"> 
        <input class="<?php echo $class; ?> reg_radio_<?php echo $custom_fields->system_name; ?>" type="radio" name="<?php echo $custom_fields->system_name; ?>[]" value="<?php echo $val; ?>" id="<?php echo $custom_fields->system_name; ?>" <?php if(is_array($registration[$custom_fields->system_name])){if(in_array($val,$registration[$custom_fields->system_name])){echo "checked";}} ?> /> 
            <span class="question_radio <?php echo $custom_fields->system_name; ?>" ><?php echo $val; ?></span>
        </span> 
        
        <?php } ?> 
        <?php  }elseif($custom_fields->question_type=='MULTIPLE'){  ?>
        <?php $values = explode(',',$custom_fields->response); 
        foreach($values as $val){ ?>
        
        <span id="add_contestant_radio"> 
        <input class="<?php echo $class; ?> reg_check_<?php echo $custom_fields->system_name; ?>" type="checkbox"  name="<?php echo $custom_fields->system_name; ?>[]" value="<?php echo $val; ?>" id="<?php echo $custom_fields->system_name; ?>" <?php if(is_array($registration[$custom_fields->system_name])){if(in_array($val,$registration[$custom_fields->system_name])){echo "checked";}} ?> />
        <span class="question_check <?php echo $custom_fields->system_name; ?>" ><?php echo $val; ?></span>  </span> 
        <?php }?>      
        <?php  } elseif($custom_fields->question_type=='DROPDOWN'){  ?>
        
        <?php $values = explode(',',$custom_fields->response); ?>
        <select class="<?php echo $class; ?>" style="width: 100%;padding: 0.428571rem;border: 1px solid #CCCCCC;border-radius: 3px 3px 3px 3px;" name="<?php echo $custom_fields->system_name; ?>" id="<?php echo $custom_fields->system_name; ?>">
        <option value=""><?php _e('Select','voting-contest'); ?></option>
        <?php foreach($values as $val){ ?>
              <option value="<?php echo $val; ?>" <?php echo($registration[$custom_fields->system_name]==$val)?'selected="selected"':'';?> ><?php echo $val; ?></option>
        <?php } ?>
        </select> 
        
        <?php } ?>                                 			
        </p></div>
        <?php    
        
        } 
        }
        ?>    
        
        <!-- Front end contestant custom fields code added -->                                  
                  <br />                  
                  <p class="form-submit">
                      <?php echo $referer; ?>
                      <input name="updateuser" type="submit" id="updateuser" class="zn_sub_button_edit" value="<?php _e('Update', 'voting-contest'); ?>" />
                      <?php wp_nonce_field( 'update-user' ) ?>
                      <input name="action" type="hidden" id="action" value="update-user" />
                  </p><!-- .form-submit -->
                </form><!-- #adduser -->
                </div>
            <?php
         endif; 
        }   
        else{
            _e('Login to Access this Section','voting-contest');
            ?>
            <a href="javascript:" onclick="ppOpen('#login_panel', '800',1);"><?php _e('Login','voting-contest'); ?></a>
            <?php
        }
        $out = ob_get_contents();
        ob_end_clean();
        return $out;
    }
}

/*
 *  Shortcodes to Display the Contestants
 */
add_shortcode('showcontestants', 'wp_votes_show_contest');
add_shortcode('addcontestants', 'wp_votes_add_contestants');
add_shortcode('upcomingcontestants', 'wp_votes_start_contestants');
add_shortcode('endcontestants', 'wp_votes_end_contestants');
add_shortcode('profilescreen', 'wp_votes_show_profilescreen');
