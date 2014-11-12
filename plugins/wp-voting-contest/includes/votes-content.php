<?php
session_start();
 if(!function_exists('vote_body_class')){
  function vote_body_class()
  {     
    global $wpdb,$post;
    get_header();
    
    if(isset($_SESSION['vote_login_function_ran']))
	   unset($_SESSION['vote_login_function_ran']);
	   
    $option = get_option(VOTES_SETTINGS);
    if(is_array($option)){
    $vote_sidebar = $option['vote_sidebar'];
    }else{
    $vote_sidebar='';
    }
    if($vote_sidebar=='on')
    $style='width:100%';
    else
    $style='';
   
?>
    <section class="vote_single_section">    
    <div class="vote_single_container">
        <div class="vote_single_post" style="<?php echo $style; ?>">
	 <?php
	  
	 $set_custom_page = $_COOKIE['short_code_id'];
	 if($set_custom_page!=''){
	  $main_navigation = $_COOKIE['short_code_id'];
	 }else
	  $main_navigation = $_SESSION['page_values'];
                 
	    $post_id = $post->ID;
	    $terms = get_the_terms($post_id, VOTES_TAXONOMY);
	    $termids = array();
	    foreach ($terms as $term) {
		    $termids[] = $term->term_id;
		    $term_listsarr[] ='<a href="'.$main_navigation.'">'.$term->name.'</a>';  
            $cat_id_cont = $term->term_id;
            $cat_name    = $term->name;
	    }
	    $categories = join(', ', $term_listsarr);
	    $categories_str = $categories;
        
        


	 ?>

	 <?php if ( is_single() ) : ?>
	    <h1 class="vote_single-title"><?php the_title(); ?></h1>
	 <?php else : ?>
	    <h1 class="vote_single-title">
	    <a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
	    </h1>
	 <?php endif; // is_single() ?>
	 	 
         <div id="prev-next">
             <header class="votenavigator">
             <ul>
                 <li><?php vote_previous_post_link('%link', '%title',true,$termids); ?> </li> 
                 <?php
		    $catopt = get_option($cat_id_cont . '_' . VOTES_SETTINGS);
                    //Getting the Custom middle_custom_navigation if the direct url of the contestant is given
                    $middle_custom_navigation=$catopt['middle_custom_navigation'];
		  ?>
		 <?php if($main_navigation != null && $middle_custom_navigation==''): ?>
                 <li><a href="<?php echo $main_navigation; ?>"><i class="voteicon-large-thumbnails"></i></a></li>
                 <?php else: ?>
                 <?php                  
                    $cat_link = $main_navigation = $middle_custom_navigation;                          
                        //Checking if the middle_navigation not set in the admin end
                        if($main_navigation == null) :
                        $category_flag = 1;
                 ?>
                        <li><a href="<?php echo vote_get_shortcode_test($cat_id_cont); ?>"><i class="voteicon-large-thumbnails"></i></a></li>
                     <?php   
                        else:                                  
                     ?>
                        <li><a href="<?php echo $main_navigation; ?>"><i class="voteicon-large-thumbnails"></i></a></li>
                     <?php 
                        endif; 
                    endif; 
                 ?>
                 <li><?php votes_next_post_link('%link', '%title',true,$termids); ?> </li>
             </ul>
             </header>
         </div>
         
         <?php if($category_flag == 1): ?>         
         <?php echo '<span class="description_cate">'.__("Category:","voting-contest").' &nbsp;<span class="single-category"><a href='.vote_get_shortcode_test($cat_id_cont).">".$cat_name."</a></span></span>"; ?>           
         <?php elseif($cat_link == null): ?>             
         <?php echo '<span class="description_cate">'.__("Category:","voting-contest").' &nbsp;<span class="single-category">' .$categories_str."</span></span>"; ?>
         <?php else: ?>                   
         <?php echo '<span class="description_cate">'.__("Category:","voting-contest").' &nbsp;<span class="single-category"><a href='.$cat_link.">".$cat_name."</a></span></span>"; ?>          
         <?php endif; ?> 
          
    		<?php
		votes_the_post_thumbnail($post_id,$termids);
		//$cat_id = $_SESSION['arg_category_id_vote'];
		$cat_id = $cat_id_cont;
		$option = $cat_id . '_' . VOTES_SETTINGS;
		$options_category =  get_option($option);
		if(is_array($options_category))
		    $image_contest = $options_category['imgcontest'];
		else
		    $image_contest = '';            
            
		?>
	  
            <div class="vote_single_content" <?php echo($image_contest=='')?'style="clear:both;"':'style="float:left;"' ?> >                   
    	       <?php the_content(); ?>   
            </div>

	     <?php 
            $sql = "SELECT * FROM " . VOTES_ENTRY_CUSTOM_TABLE . " where admin_view='Y' and delete_time = 0 order by sequence";
            $questions = $wpdb->get_results($sql);	    
            $sql1 = "SELECT * FROM " . VOTES_POST_ENTRY_TABLE . " WHERE post_id_map = '" . $post_id. "'";
            $field_val = $wpdb->get_row($sql1);
            
            if(!empty($field_val)){
                $field_vals = unserialize($field_val->field_values);    
            }else{
                 $field_vals = '';
            }
		     $show_once='1';
                    if(!empty($questions)){
                        foreach($questions as $ques){
                            if(!empty($field_val)){
                                if($ques->system_name != 'contestant-desc'): 
                             
			     if($show_once==1){?>
			     <!-- contestant custom feild values show -->
			     <div class="contestant_custom_fields">
			     <h2><?php _e('Additional Information','voting-contest'); ?></h2><?php } ?>
                             <div class="contestant_other_details">
                             <span><strong><?php echo $ques->question.':';?></strong></span>
                             <?php                                                         
                                                         
                             if(is_array($field_vals[$ques->system_name])){
                                $cust_val = implode(', ',$field_vals[$ques->system_name]);
                             }else{
                                $cust_val = $field_vals[$ques->system_name];
                             }
                                                          
                             ?>
                             <span><?php echo $cust_val; ?> </span>
                             </div>           
                            <?php
                            
			    if($show_once==1){ echo '</div>'; }
                            endif;
                            }
			    $show_once++;
                        }
                    }
                 ?>
	       <div class="vote_content_comment"><?php comments_template(); ?></div>
        </div>
        
        <?php echo voting_additional_fields_pretty($post_id); ?>
                                
	<?php
	if($vote_sidebar!='on'){
	     echo '<div class="votes_sidebar">';
		dynamic_sidebar();
	     echo '</div>';
	}
	?>
         </div>
    </section>
<?php
      get_footer();
      exit; 
  } 
}
    
  if(!function_exists('votes_content_update')){
      function votes_content_update(){
      
       $desc_rs = wp_voting_get_contestant_desc();
       if($desc_rs[0]->admin_view == "Y"):        
           $post_id = get_the_ID();
           $post_content = get_post($post_id);
           $vote_content ='<div class="vote_content"> 
                            <p class="vote_content_added">'.$post_content->post_content.'</p>
                            </div>';
         
           return $vote_content;
       endif;
       
      }
  }
  
  if(!function_exists('votes_the_post_thumbnail')){
   function votes_the_post_thumbnail($post_id,$termids){

    if(count($termids)<2){
     $cat_id = $termids[0];
    }else
     $cat_id = $_SESSION['arg_category_id_vote'];
    
       $option = $cat_id . '_' . VOTES_SETTINGS;
       $options_category =  get_option($option);
       if(is_array($options_category)){
	   $image_contest = $options_category['imgcontest'];
	   $votecount=$options_category['votecount'];
       }
       else{
	    $image_contest = '';
	    $votecount='';
       }
      
      if($image_contest==''){
	$post_thumbnail_id = get_post_thumbnail_id($post_id);
	$bigimg = wp_get_attachment_url( get_post_thumbnail_id($post_id) );
	$size = 'post-thumbnail';
	$size = apply_filters( 'post_thumbnail_size', $size );
	if ( $post_thumbnail_id ) {
	  $image_arr = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'large');
	  $image_src = $image_arr[0];
	  
	  $opt = get_option(VOTES_SETTINGS);	     
	  $image1 = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), $opt['page_cont_image']);  
	  $image_alt_text=votes_seo_friendly_alternative_text(get_the_title());
	  $html = "<img alt='".$image_alt_text."' src=".$image1[0]." width=".$image1[1]." height=".$image1[2]." class='left-img-thumb' longdesc='".get_permalink(get_the_ID())."' />";
	
	 } else {
         $image_alt_text=get_the_title();
		 $html = '<img alt="'.$image_alt_text.'" src="'.$image_src = VOTES_PATH .'images/no-image.jpg" longdesc="'.get_permalink(get_the_ID()).'" />';
	 }
     }
     /*********** Arguments neeed to show the sharing icons ***********************/
     $opt = get_option(VOTES_SETTINGS);
     $ht = $opt['imgheight'] ? $opt['imgheight'] : 92;
     $wi = $opt['imgwidth'] ? $opt['imgwidth'] : 132;
     $disp = $opt['imgdisplay'] ? $opt['imgdisplay'] : 0;
     $orderby = $opt['orderby'] ? $opt['orderby'] : 'votes';
     $order = $opt['order'] ? $opt['order'] : 'desc';
     $termdisplay = $opt['termdisplay'] ? $opt['termdisplay'] : 0;
     $title = $opt['title'] ? $opt['title'] : NULL;
     $onlyloggedinuser = $opt['onlyloggedinuser'] ? $opt['onlyloggedinuser'] : FALSE;
     $facebook = $opt['facebook'] ? $opt['facebook'] : 'off';
     $twitter = $opt['twitter'] ? $opt['twitter'] : 'off';
     $file_facebook = $opt['file_facebook'] ?$opt['file_facebook']:'';
     $file_twitter = $opt['file_twitter'] ?$opt['file_twitter']:'';
     $file_fb_default = $opt['file_fb_default'] ?$opt['file_fb_default']:'';
     $file_tw_default = $opt['file_tw_default'] ?$opt['file_tw_default']:'';
     
     $totvotesarr = array();
     $totvotesarr = get_post_meta($post_id, VOTES_CUSTOMFIELD);
     $totvotes = isset($totvotesarr[0]) ? $totvotesarr[0] : 0;
     if($totvotes == NULL) $totvotes = 0;
     $current_url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
     
     $shortcode_opt = $_SESSION['arg_shortcode_vote'];
     
     if(!$onlyloggedinuser){
      if($shortcode_opt['onlyloggedinuser']!='')
       $onlyloggedinuser=$shortcode_opt['onlyloggedinuser']; 
     }
     
     if($image_contest==''){
      $adv_excerpt = VotesAdvancedExcerpt::Instance();      
      $shor_desc = $adv_excerpt->filter(get_the_excerpt());
                   
      if($bigimg!='')
       $link_img_open = 'href="'.$bigimg.'"';
       else
       $link_img_open = 'href="'.VOTES_PATH.'images/no-image.jpg"';
       
       $show_in_pretty = vote_show_desc_prettyphoto();
       $pretty_excerpt = ($show_in_pretty == 1)?strip_tags($shor_desc):'';
       
     $vote_thumbmnail = '<div class="vote_thumbnail"><a '.$link_img_open.' class="prettyPhoto" alt="'.$pretty_excerpt.'" >'.$html.'</a></div>';  
     } 
	 $up_path =  wp_upload_dir();
	 $perma = get_permalink($post_id);
	 
      $vote_thumbmnail .='<div class="vote_functions">  
              <input type="hidden" id="votes_content_title" value="'.get_the_title().'" />            
			  <div class="vote-count vote_function_count votescounter_'.get_the_ID().'">
              <input type="hidden" class="voted_text" value="'.__('Voted','voting-contest').'" />';
	 
     	  
	 if($votecount==NULL){	  
	 $vote_thumbmnail .='<div class="square">
			     <span class="num Votes votescount' . $post_id . '">' . $totvotes . '<input type="hidden" id="votescounter' . $post_id . '" value=' . $totvotes . ' /></span>                 
			     <span class="vote">'.__('Votes','voting-contest').'</span>
			    </div>';
     
                            
	 }else{
	  $vote_thumbmnail .='<p style="margin-top:45%;"></p>';	
	 }
      
	 $terms = get_the_terms($post_id, VOTES_TAXONOMY);
	 $termids = array();
	 foreach ($terms as $term) {
		 $termids[] = $term->term_id;
		 $term_listsarr[] =$term->name;  
	 } 
      
	if (is_array($termids))
	  $votes_term_id = implode(',', $termids);
	else
	  $votes_term_id = '';
      
    if($opt['vote_tracking_method'] == 'cookie_traced'){
       $ua = voting_getBrowser();              
       $voter_cookie = $ua['name'].'@'.$votes_term_id.'@'.$post_id;   
       $ip = $voter_cookie;
    }
    else{ 
       if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARTDED_FOR'] != '') {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
        $ip = $_SERVER['REMOTE_ADDR'];
       }
    }
                  
	
      $option_1 = $cat_id. '_' . VOTES_TAXEXPIRATIONFIELD;
      $dateexpiry =  get_option($option_1);
      $cur_time = current_time( 'timestamp', 0 );
      if($dateexpiry==''){
	      $dateexpiry = date( 'Y-m-d H:i:s', current_time( 'timestamp', 0 ) );
      }
     if(strtotime($dateexpiry) >= $cur_time){	   
      if($onlyloggedinuser){
	 if(is_user_logged_in()){
	   if(!is_votable($post_id, $ip,$cat_id)){
	   $vote_thumbmnail .='<a id="vote' . $post_id . '" class="votebutton vote-btn  '.$post_id.'" href="javascript:void(0);" >'.__('Voted','voting-contest').'</a>';
       
       }
	   else{
	   $vote_thumbmnail .='<a id="vote' . $post_id . '" class="votebutton vote-btn  '.$post_id.'" href="javascript:void(0);" >'.__('Vote Now','voting-contest').'</a>';
       
       }       
	 }else{
	   //If not logged in Show the below will be shown in popup
	  votes_custom_registration_fields_show();
	  if(!is_votable($post_id, $ip,$cat_id)){
	   $vote_thumbmnail .='<a id="vote' . $post_id . '" style="color:#fff;" class="vote-btn vote_click" href="javascript:" onclick="ppOpen(\'#login_panel\', \'800\',1)" >'.__('Voted','voting-contest').'</a>';
       
      }
	  else{
	   $vote_thumbmnail .='<a id="vote' . $post_id . '" style="color:#fff;" class="vote-btn vote_click" href="javascript:" onclick="ppOpen(\'#login_panel\', \'800\',1)" >'.__('Vote Now','voting-contest').'</a>';
       
       
       }
	}
      }else{
	if(!is_votable($post_id, $ip,$cat_id)){
	 $vote_thumbmnail .='<a id="vote' . $post_id . '" class="votebutton vote-btn '.$post_id.'" href="javascript:void(0);" >'.__('Voted','voting-contest').'</a>';
     
     }
	else{
	 $vote_thumbmnail .='<a id="vote' . $post_id . '" class="votebutton vote-btn '.$post_id.'" href="javascript:void(0);" >'.__('Vote Now','voting-contest').'</a>';
     
     }
      }
     }
     $vote_thumbmnail .='<input type="hidden" value="' . $votes_term_id . '" name="votes-term-id" id="votes-term-id'.$post_id.'">
			 </div><div class="face_social_icons">';
      if($facebook!='off') {
       if($file_fb_default=='' && $file_facebook!=''){
	     if(file_exists($up_path['path'].'/'.$file_facebook))
		$face_img_path = $up_path['url'].'/'.$file_facebook;
	     else
		$face_img_path = VOTES_PATH.'images/facebook-share.png';
       }else{
	    $face_img_path = VOTES_PATH.'images/facebook-share.png';
       } 
	$vote_thumbmnail .= '<a target="_blank" class="facebook1-btn" href="http://www.facebook.com/sharer.php?u='.$perma.'&amp;t='.urlencode(get_the_title()).'"><img alt="Facebook share" src="'.$face_img_path.'"></a> <input type="hidden" value="'.$face_img_path.'" class="face_list_img_path" />';
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
       $vote_thumbmnail .= '<a target="_blank" class="facebook1-btn" href="http://twitter.com/home?status='.urlencode(get_the_title()).'%20'.$perma.'"><img alt="Tweet share" src="'.$twt_img_path.'"></a><input type="hidden" value="'.$twt_img_path.'" class="tweet_list_img_path" />';
      }      
      $vote_thumbmnail .='</div></div>'; //votefunctions div closed
      echo $vote_thumbmnail;          
      ?>
        <script type="text/javascript">
            var votes_id            = '<?php echo $post_id; ?>';
            jQuery(document).ready(function(){ 
            var votes_content_title = jQuery('#votes_content_title').val();      
            
            var social_link_html    = "";
                                    
            var face_list_img_path  = jQuery('.face_list_img_path').val();
                        
            if(face_list_img_path != null){
                var social_link_html = social_link_html + '<div class="facebook"><a target="_blank" href="http://www.facebook.com/sharer.php?u='+location.href+'&amp;t='+votes_content_title+'"><img alt="Facebook share" src="'+face_list_img_path+'"></a> </div>';                                      
            }                            
            var tweet_list_img_path = jQuery('.tweet_list_img_path').val();
            if(tweet_list_img_path != null ){
                social_link_html = social_link_html + '<div class="twitter"><a target="_blank" href="http://twitter.com/home?status='+votes_content_title+'%20'+location.href+'"><img alt="Tweet share" src="'+tweet_list_img_path+'"></a></div>';                    
            }
            
            var html_vte_counter = jQuery('.votescounter_'+votes_id).clone().find("a.vote-btn").remove().end().html();
            if(html_vte_counter.trim() != null)
                html_vte_counter = "<div class='wp_voting wp_voting_count'>"+html_vte_counter+"</div>"; 
                
            var html_vote_button = jQuery('.votescounter_'+votes_id).clone().find(".square").remove().end().html();                
            if(html_vote_button.trim() != "")
                html_vote_button ="<div class='wp_voting'>"+html_vote_button+"</div>"; 
                
              
                
            var wp_voting_buttons =  html_vte_counter+ html_vote_button;
            
            window.markupp = '<div class="pp_pic_holder"> \
                \     <div class="pp_social"></div> \
    						<div class="ppt">&nbsp;</div> \
    						<div class="pp_top"> \
    							<div class="pp_left"></div> \
    							<div class="pp_middle"></div> \
    							<div class="pp_right"></div> \
    						</div> \
    						<div class="pp_content_container"> \
    							<div class="pp_left"> \
    							<div class="pp_right"> \
    								<div class="pp_content"> \
    									<div class="pp_loaderIcon"></div> \
    									<div class="pp_fade pp_single"> \
    										<a href="#" class="pp_expand" title="Expand the image">Expand</a> \
    										<div class="pp_hoverContainer"> \
    											<a class="pp_next" href="#">next</a> \
    											<a class="pp_previous" href="#">previous</a> \
    										</div> \
    										<div id="pp_full_res"></div> \
    										<div class="pp_details"> \
    											<div class="pp_nav"> \
    												<a href="#" class="pp_arrow_previous">Previous</a> \
    												<p class="currentTextHolder">0/0</p> \
    												<a href="#" class="pp_arrow_next">Next</a> \
    											</div> \
    											<p class="pp_description"></p> \
                                                <p class="pp_description_additional"></p> \
    											\
    											<a class="pp_close" href="#">Close</a> \
    										</div> \
    									</div> \
    								</div> \
    							</div> \
    							</div> \
    						</div> \
    						<div class="pp_bottom"> \
    							<div class="pp_left"></div> \
    							<div class="pp_middle"></div> \
    							<div class="pp_right"></div> \
    						</div> \                                                                                                                                                      </div> \
    					<div class="pp_overlay"></div>';                                         
            window.sociall_tools = '<div class="pp_social">'+wp_voting_buttons+social_link_html+'</div>';
            
            jQuery('a[class^=prettyPhoto]').prettyPhoto({               
                theme:'pp_kalypso',                
    			markup: markupp,
                social_tools: sociall_tools,
                changepicturecallback: function()
                {
                    voting_change_values(votes_id);
                    jQuery('.pp_description_additional').html(jQuery('.prettyphoto_additional_details').html());
                    var pp_cnt_height = jQuery('.pp_content').height();
                    var pp_add_height = jQuery('.pp_description_additional').height();
                    jQuery('.pp_content').css('height',pp_cnt_height+pp_add_height);
                      
            
                }			
    		});   
            
                        
             
        });
        
        function voting_return_object()
        {
            var single_voting_object = {markup:window.markupp,social_tools:window.sociall_tools};
            return single_voting_object;
        } 
        </script>
      <?php
   }
  }
  
  if(!function_exists('vote_previous_post_link')){
    function vote_previous_post_link($format='&laquo; %link', $link='%title', $in_same_cat = false, $excluded_categories = '') {
     votes_post_next_prevoius_link($format, $link, $in_same_cat, $excluded_categories, true);
    }
  }
  
  if(!function_exists('votes_next_post_link')){
   function votes_next_post_link($format='%link &raquo;', $link='%title', $in_same_cat = false, $excluded_categories = ''){
    votes_post_next_prevoius_link($format, $link, $in_same_cat, $excluded_categories, false);
   }
  }
  
  if(!function_exists('votes_post_next_prevoius_link')){
   function votes_post_next_prevoius_link($format, $link, $in_same_cat = false, $excluded_categories = '', $previous = true ){ 
     if ( $previous && is_attachment() ){
	     $post = get_post( get_post()->post_parent );
     }
     else{
	     $post = votes_get_adjacent_post( $in_same_cat, $excluded_categories, $previous );
     }     
     if ( ! $post ) {
	     $output = '';
     } else {
      $title = $post->post_title;

      if ( empty( $post->post_title ) )
	      $title = $previous ? __( 'Previous Post','voting-contest' ) : __( 'Next Post','voting-contest' );

      $title = apply_filters( 'the_title', $title, $post->ID );
      $date = mysql2date( get_option( 'date_format' ), $post->post_date );
      $rel = $previous ? 'prev' : 'next';
      $rel_lr = $previous ? 'left' : 'right';
      $string = '<a href="' . get_permalink( $post ) . '" rel="'.$rel.'">';
      $inlink = str_replace( '%title', $title, $link );
      $inlink = str_replace( '%date', $date, $inlink );        
      $inlink = $string .'<i class="voteicon-select-'.$rel_lr.'"></i></a>';
 
      $output = str_replace( '%link', $inlink, $format );
     }
     $adjacent = $previous ? 'previous' : 'next';
     echo apply_filters( "{$adjacent}_post_link", $output, $format, $link, $post );
    }
  }
  if(!function_exists('votes_get_adjacent_post')){
   function votes_get_adjacent_post( $in_same_cat = false, $excluded_categories = '', $previous = true ) {
     global $wpdb;
     if ( ! $post = get_post() )
      return null;
     $current_post_date = $post->post_date;
     $join = '';
     $posts_in_ex_cats_sql = '';
     if ( $in_same_cat || ! empty( $excluded_categories ) ) {
       $join = " INNER JOIN $wpdb->term_relationships AS tr ON p.ID = tr.object_id INNER JOIN $wpdb->term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id";
       if ( $in_same_cat ) {
	       $cat_array = $excluded_categories;
	       if ( ! $cat_array || is_wp_error( $cat_array ) )
		       return '';
	       $join .= " AND tt.taxonomy = 'contest_category' AND tt.term_id IN (" . implode(',', $cat_array) . ")";
       }
       $posts_in_ex_cats_sql = "AND tt.taxonomy = 'contest_category'";
       
     }
     $adjacent = $previous ? 'previous' : 'next';
     $op = $previous ? '<' : '>';
     $order = $previous ? 'DESC' : 'ASC';
     $join  = apply_filters( "get_{$adjacent}_post_join", $join, $in_same_cat, $excluded_categories );
     //$where = apply_filters( "get_{$adjacent}_post_where", $wpdb->prepare("WHERE p.post_date $op %s AND p.post_type = %s AND p.post_status = 'publish' $posts_in_ex_cats_sql", $current_post_date, $post->post_type), $in_same_cat, $excluded_categories );
     $where = apply_filters( "get_{$adjacent}_post_where", $wpdb->prepare("WHERE p.ID $op $post->ID AND p.post_type = '".VOTES_TYPE."' AND p.post_status = 'publish' $posts_in_ex_cats_sql", $current_post_date, $post->post_type), $in_same_cat, $excluded_categories );
     $sort  = apply_filters( "get_{$adjacent}_post_sort", "ORDER BY p.ID $order LIMIT 1" );
     $query = "SELECT p.ID FROM $wpdb->posts AS p $join $where $sort";
     $query_key = 'adjacent_post_' . md5($query);
     $result = wp_cache_get($query_key, 'counts');
     if ( false !== $result ) {
	     if ( $result )
		     $result = get_post( $result );
	     return $result;
     }
     $result = $wpdb->get_var( $query );
     if ( null === $result )
      $result = '';
     wp_cache_set($query_key, $result, 'counts');
    
     if ( $result )
      $result = get_post( $result );
    
      return $result;
   } 
  }
        
//Accessing the shortcodes by browsing through the pages
if(!function_exists('vote_pages_with_shortcode'))
{
    function vote_pages_with_shortcode($shortcode, $args = array('sort_order' => 'DESC')) {
        if(!shortcode_exists($shortcode)) {
            // shortcode was not registered Checking
            return null;
        }
    
        //Browser through the pages to get the id
        $pages = get_pages($args);
        $list = array();
    
        foreach($pages as $page) {
            if(has_shortcode($page->post_content, $shortcode)) {
                $list[] = $page;
            }
        }
                           
        return $list;
    }
}
            
// Finding the shortcode in the pages...   
if(!function_exists('vote_get_shortcode_test'))
{
    function vote_get_shortcode_test($catid = '') 
    {
        $middle_navigation_flag = ''; 
        
        //Checking the Shortcode with the "showcontestants" ; 
        foreach(vote_pages_with_shortcode("showcontestants") as $p) {     
            
            $pattern = get_shortcode_regex();
    		preg_match_all( '/'. $pattern .'/s', $p->post_content, $matches );
            
    		if( is_array( $matches ) && array_key_exists( 2, $matches ) && in_array( 'showcontestants', $matches[2] ) )             {
    			foreach ($matches[0] as $value) {
    				$value = wpautop( $value, true );                                                 
                    $attributes = shortcode_parse_atts($value);
                    if($attributes['id'] == $catid){
                        $middle_navigation_flag = $p->guid;
                        return $middle_navigation_flag ;
                    } 
                       
    			}
    		} 
    
        }
        
        //Checking the Shortcode with the "topcontestants" ; 
        if($middle_navigation_flag == null):
        foreach(vote_pages_with_shortcode("topcontestants") as $p) {     
            
            $pattern = get_shortcode_regex();
    		preg_match_all( '/'. $pattern .'/s', $p->post_content, $matches );
            
    		if( is_array( $matches ) && array_key_exists( 2, $matches ) && in_array( 'topcontestants', $matches[2] ) )             {
    			foreach ($matches[0] as $value) {
    				$value = wpautop( $value, true );                                                 
                    $attributes = shortcode_parse_atts($value);
                    if($attributes['id'] == $catid){
                        $middle_navigation_flag = $p->guid;
                        return $middle_navigation_flag ;
                    } 
                       
    			}
    		} 
    
        }
        endif;
        
        //Checking the Shortcode with the "bottomcontestants" ;                 
        if($middle_navigation_flag == null):
        foreach(vote_pages_with_shortcode("bottomcontestants") as $p) {     
            
            $pattern = get_shortcode_regex();
    		preg_match_all( '/'. $pattern .'/s', $p->post_content, $matches );
            
    		if( is_array( $matches ) && array_key_exists( 2, $matches ) && in_array( 'bottomcontestants', $matches[2] ) )             {
    			foreach ($matches[0] as $value) {
    				$value = wpautop( $value, true );                                                 
                    $attributes = shortcode_parse_atts($value);
                    if($attributes['id'] == $catid){
                        $middle_navigation_flag = $p->guid;
                        return $middle_navigation_flag ;
                    } 
                       
    			}
    		} 
    
        }
        endif;
        
                
        //Checking the Shortcode with the "upcomingcontestants" ;                 
        if($middle_navigation_flag == null):
        foreach(vote_pages_with_shortcode("upcomingcontestants") as $p) {     
            
            $pattern = get_shortcode_regex();
    		preg_match_all( '/'. $pattern .'/s', $p->post_content, $matches );
            
    		if( is_array( $matches ) && array_key_exists( 2, $matches ) && in_array( 'upcomingcontestants', $matches[2] ) )             {
    			foreach ($matches[0] as $value) {
    				$value = wpautop( $value, true );                                                 
                    $attributes = shortcode_parse_atts($value);
                    if($attributes['id'] == $catid){
                        $middle_navigation_flag = $p->guid;
                        return $middle_navigation_flag ;
                    } 
                       
    			}
    		} 
    
        }
        endif;
        
    }
}

//Function to Check Whether to SHow Description in the PrettyPhoto Slideshow
if(!function_exists('vote_show_desc_prettyphoto')){
    function vote_show_desc_prettyphoto(){
        global $wpdb;
        $sql = "SELECT * FROM " . VOTES_ENTRY_CUSTOM_TABLE . " where system_name='contestant-desc' and pretty_view='Y' order by sequence";            
        $questions = $wpdb->get_results($sql);
        if(count($questions) > 0)
            return 1;
        else
            return 0;	   
    }
}
        
        
?>
