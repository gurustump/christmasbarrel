<?php

if(!function_exists('wp_votes_extravars')){
    function wp_votes_extravars ( $vars ) {
	$vars[] = 'rnd';
	return $vars;
    }
}
add_filter( 'query_vars' , 'wp_votes_extravars'  );

/*
 * Enable to use shortcode in widgets
*/

add_filter('widget_text', 'shortcode_unautop');
add_filter('widget_text', 'do_shortcode');

/*
 * Fetches the list of post for particular Category 
 * Used in Bulk move 
 */
if (!function_exists('fetch_post_by_category')) {

    function fetch_post_by_category() {
        $out = '';
        $term_id = $_GET['term_id'];
        $postargs = array(
            'post_type' => VOTES_TYPE,
            'post_status' => 'publish',
            'tax_query' => array(
                array('taxonomy' => VOTES_TAXONOMY,
                    'field' => 'id',
                    'terms' => $term_id,
					'include_children' => false)
            ),
            'nopaging' => true
        );

        $contest_post = new WP_Query($postargs);
        if ($contest_post->post_count > 0) {
            $out.='<input type="checkbox" value="0" class="select_all_post" id="select_all_post">&nbsp;&nbsp;<b> '.__('Select All','voting-contest').'</b><br/>';
            while ($contest_post->have_posts()) {
                $contest_post->the_post();
                $out .= '<input type="checkbox" class="selected-post" name="selected_post[]" value="' . get_the_ID() . '"/>&nbsp;&nbsp;' . get_the_title() . '<br/>';
            }
        } else {
            $out .= 'No Post Found';
        }
	?>
	<script type="text/javascript">
	    var versionjq = jQuery.fn.jquery;
	    if(parseFloat(versionjq) >= parseFloat('1.10.0')){
	      var funct_name = 'on';
		jQuery('.select_all_post').on('click', function(){
		    if(jQuery('.select_all_post').attr('checked'))
		    jQuery('.selected-post').attr('checked',' checked');
		    else
		    jQuery('.selected-post').removeAttr('checked');
		});
	      
	    }else{
	      var funct_name = 'live';
	      jQuery('.select_all_post').live('click', function(){
		    if(jQuery('.select_all_post').attr('checked'))
		    jQuery('.selected-post').attr('checked',' checked');
		    else
		    jQuery('.selected-post').removeAttr('checked');
		});
	    }
	</script>
	<?php 
        echo $out;
        die();
    }

}
add_action('wp_ajax_votesbulkmove', 'fetch_post_by_category');

/*
 * Adds the post meta for the particular post
 */
if (!function_exists('wp_votes_add_custom_field')) {
    function wp_votes_add_custom_field($post_id) { 
        
        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || (defined('DOING_AJAX') && DOING_AJAX) || isset($_REQUEST['bulk_edit'])){        
            return $post_id;
        }
        
        if ($_POST['post_type'] == VOTES_TYPE) {                       
            update_post_meta($post_id, VOTES_CUSTOMFIELD, $_POST['votes_counter']);                           
        }
    }
}
add_action('save_post', 'wp_votes_add_custom_field');




/*
 *  Returns the list of terms associated with the post
 */
if (!function_exists('votes_term_list')) {

    function votes_term_list($id = 0, $taxonomy = NULL, $before = '', $sep = '', $after = '') {
        if (!$taxonomy) {
            $taxonomy = VOTES_TAXONOMY;
        }
        if (!$sep) {
            $sep = ', ';
        }
        $terms = get_the_terms($id, $taxonomy);
        if (empty($terms))
            return false;
        foreach ($terms as $term) {
            $term_lists[] = '<span class="single-category">' . $term->name . '</span>';
        }
        return $before . '&nbsp;' . join($sep, $term_lists) . $after;
    }

}

/*
 * Allows access to Ajaxurl from Front end
 */
if (!function_exists('add_ajaxurl_to_front')) {

    function add_ajaxurl_to_front() {
        ?>
        <script type="text/javascript">
            //<![CDATA[
            votesajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
			var servDateArray = '<?php echo date("Y/n/d/H/i/s", time()); ?>'.split('/');
			var servDate=new Date(Number(servDateArray[0]),Number(servDateArray[1])-1,Number(servDateArray[2]),Number(servDateArray[3]),Number(servDateArray[4]),Number(servDateArray[5]));
			
            //]]>
	    
	    var zn_do_login=new Object();
	    zn_do_login.ajaxurl = '<?php echo admin_url( 'admin-ajax.php' ) ?>';
        </script>
	<?php
	$option = get_option(VOTES_SETTINGS);
	?>
	    <style type="text/css">
		    .countdown_wrapper{
		    color:<?php echo $option['votes_timertextcolor']; ?>;
		    background-color:<?php echo $option['votes_timerbgcolor']; ?>;
		    }
	    </style>
	<?php
    }

}
add_action('wp_head', 'add_ajaxurl_to_front', 1);

$version_wp = get_bloginfo('version');
add_filter('the_posts', 'votes_conditionally_add_scripts_and_styles');
    // the_posts gets triggered before wp_head
  if (!function_exists('votes_conditionally_add_scripts_and_styles')) {  
    function votes_conditionally_add_scripts_and_styles($posts){
	    global $wp_query; 	    
	    if (empty($posts)) return $posts;
	    
	    $shortcode= 'showcontestants';
	    $shortcode1= 'profilescreen';
	    $shortcode2= 'bottomcontestants';
	    $shortcode3= 'upcomingcontestants';
	    $shortcode4= 'endcontestants';
	    $shortcode5= 'addcontestants';

	    $shortcode_found = false; // use this flag to see if styles and scripts need to be enqueued
	    foreach ($posts as $post) {
		    if (isset($wp_query->query_vars['contestants']) || stripos($post->post_content, '[' . $shortcode ) !== false || stripos($post->post_content, '[' . $shortcode1 ) !== false || stripos($post->post_content, '[' . $shortcode2 ) !== false || stripos($post->post_content, '[' . $shortcode3 ) !== false || stripos($post->post_content, '[' . $shortcode4 ) !== false || stripos($post->post_content, '[' . $shortcode5 ) !== false) {
			    $shortcode_found = true; 
			    break;
		    }
	    }
     
	    if ($shortcode_found) {
		votes_scripts_method();
	    }
     
	    return $posts;
    }
  }
  
if($version_wp > '3.3')
{
 /*
 * Enqueuing scripts and styles to frontend
 */
    if (!function_exists('votes_scripts_method')) {
    
	function votes_scripts_method() {
	    $options = get_option(VOTES_SETTINGS);
	    
	    wp_enqueue_script('jquery');
	    
	    if($options['vote_disable_jquery']!='on'){
		wp_register_script('votejquery', VOTES_PATH . 'scripts/jquery-min.js');
		wp_enqueue_script('votejquery');
	    }
	    if($options['vote_disable_jquery_cookie']!='on'){
	    wp_register_script('votecookiejs', VOTES_PATH . 'scripts/jquery.cookie.js');
	    wp_enqueue_script('votecookiejs',array('jquery'));
	    }
	    
	    wp_register_script('votesscript', VOTES_PATH . 'scripts/votes-script.js');
	    wp_register_script('votesblockui', VOTES_PATH . 'scripts/votes-blockui.js');
	        
	    //Newly Added
	    if($options['vote_disable_jquery_pretty']!='on'){
	    wp_register_script('prettyphoto', VOTES_PATH . 'scripts/jquery.prettyPhoto.js');
	    wp_enqueue_script('prettyphoto', array('jquery'));
	    }

	    if($options['vote_disable_jquery_fancy']!='on'){
	    wp_register_script('votesfancy', VOTES_PATH . 'scripts/votes-fancybox.js');
	    wp_enqueue_script('votesfancy', array('jquery'));
	    }
	    
	    wp_register_script('znscript', VOTES_PATH . 'scripts/znscript.js');     
	    wp_enqueue_script('znscript', array('jquery'));
        
        wp_register_script('votes-fbscript', VOTES_PATH . 'scripts/votes-fbscript.js');     
	    wp_enqueue_script('votes-fbscript', array('jquery'),'','2.0',true);
	    /*****/
	    
	    wp_register_script('votestimer', VOTES_PATH . 'scripts/votes-countdown.js');
	    
	    wp_register_style('votesdisplaystyle', VOTES_PATH . 'css/votes-display.css');
	    wp_enqueue_style('votesdisplaystyle');
	    
	    wp_register_style('votesfancy', VOTES_PATH . 'css/votes-fancybox.css');
	    wp_enqueue_style('votesfancy');
	    
	    wp_register_style('unicons', VOTES_PATH . 'css/webfonts/unicons.css');
	    wp_enqueue_style('unicons');  
	    
	    wp_register_style('prettyPhotodisplay', VOTES_PATH . 'css/prettyPhoto.css');
	    wp_enqueue_style('prettyPhotodisplay');
	    
	    wp_enqueue_script('votesblockui', array('jquery'));  
	    wp_enqueue_script('votestimer', array('jquery'));
	    wp_enqueue_script('votesscript', array('jquery'));
	    Voting_PageNavi_Core::stylesheets();
	}    
    }

}else{
    if (!function_exists('votes_scripts_method')) {
    
	function votes_scripts_method() {
	    
	    $options = get_option(VOTES_SETTINGS);
	    if($options['vote_disable_jquery']!='on'){
	    wp_deregister_script('jquery');
	    wp_register_script('jquery', VOTES_PATH . 'scripts/jquery-min.js');
	    }
	    wp_enqueue_script('jquery');
	   
	    if($options['vote_disable_jquery_cookie']!='on'){
	    wp_register_script('votecookiejs', VOTES_PATH . 'scripts/jquery.cookie.js');
	    wp_enqueue_script('votecookiejs',array('jquery'));
	    }
		    
	    wp_register_script('votesscript', VOTES_PATH . 'scripts/votes-script.js');
	    wp_enqueue_script('votesscript', array('jquery'));
	    
	    if($options['vote_disable_jquery_pretty']!='on'){
	    //pretty photo 
	    wp_register_script('prettyphoto', VOTES_PATH . 'scripts/jquery.prettyPhoto.js');
	    wp_enqueue_script('prettyphoto', array('jquery'));
	    }
	    
	    wp_register_script('votesblockui', VOTES_PATH . 'scripts/votes-blockui.js');
	    if($options['vote_disable_jquery_fancy']!='on'){
	    wp_register_script('votesfancy', VOTES_PATH . 'scripts/votes-fancybox.js');
	    wp_enqueue_script('votesfancy', array('jquery'));
	    }
	    
	    wp_register_style('votesfancy', VOTES_PATH . 'css/votes-fancybox.css');
	    wp_enqueue_style('votesfancy');
    
	    wp_register_style('prettyPhotodisplay', VOTES_PATH . 'css/prettyPhoto.css');
	    wp_enqueue_style('prettyPhotodisplay');
	    
	    wp_register_script('znscript', VOTES_PATH . 'scripts/znscript.js');    
	    wp_enqueue_script('znscript', array('jquery'));
        
        wp_register_script('votes-fbscript', VOTES_PATH . 'scripts/votes-fbscript.js');     
	    wp_enqueue_script('votes-fbscript', array('jquery'),'','2.0',true);
	    
	    
	    wp_register_script('votestimer', VOTES_PATH . 'scripts/votes-countdown.js');
	    wp_register_style('votesdisplaystyle', VOTES_PATH . 'css/votes-display.css');
	    wp_enqueue_style('votesdisplaystyle');
	    wp_register_style('unicons', VOTES_PATH . 'css/webfonts/unicons.css');
	    wp_enqueue_style('unicons');  
	    wp_enqueue_script('votestimer', array('jquery'));
	    Voting_PageNavi_Core::stylesheets();
		  
	}
    
    }
    
}

/*
 * Enqueuing scripts and styles to adminend
 */
if (!function_exists('votes_admin_styles')) {
    function votes_admin_styles() {    
    ?>
        <script type="text/javascript">
            //<![CDATA[
            votespluginurl = '<?php echo VOTES_PATH; ?>';
            votesajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
            //]]>
        </script>
        <style type="text/css">
            #votes_settings_form .wp-picker-holder {
                position: absolute;
                z-index: 10;
            }
            </style>
        <?php
	
	$version_wp = get_bloginfo('version');
	//Version fix to add the style and script
	if($version_wp > '3.4')
	{
	    wp_enqueue_style('jquery-ui-corecss', VOTES_PATH . 'css/jquery-ui-core.css');
	    wp_enqueue_style('jquery-ui-corecss');              
	    wp_register_style('votestimepickstyle', VOTES_PATH . 'css/jquery-ui-timepicker-addon.css');
	    wp_enqueue_style('votestimepickstyle');         
	    wp_enqueue_style('admin_stylesa', VOTES_PATH . 'css/admin-styles.css');
	    wp_enqueue_style('admin_stylesa');
	    wp_enqueue_style( 'wp-color-picker' );
	    wp_enqueue_script('jquery-ui-core',array('jquery'));
	    wp_enqueue_script('jquery-ui-datepicker',array('jquery', 'jquery-ui-core'));
	    wp_enqueue_script('jquery-ui-timepicker-addon',VOTES_PATH . 'scripts/jquery-ui-timepicker-addon.js',array('jquery-ui-core' ,'jquery-ui-datepicker', 'jquery-ui-slider'));   
	    wp_enqueue_script('votesadmincript',VOTES_PATH . 'scripts/votes-admin.js',array('jquery', 'jquery-ui-datepicker' , 'jquery-ui-timepicker-addon', 'wp-color-picker'));   
	
	}else{
	    echo '<link rel="stylesheet" media="all" type="text/css" href="'.VOTES_PATH . 'css/jquery-ui-core.css'.'" />';
	    echo '<link rel="stylesheet" media="all" type="text/css" href="'.VOTES_PATH . 'css/jquery-ui-timepicker-addon.css'.'" />';    
	    echo '<script type="text/javascript" src="'.VOTES_PATH . 'scripts/jquery-min.js'.'"></script>';
            echo '<script type="text/javascript" src="'.VOTES_PATH . 'scripts/jquery.ui.core.min.js'.'"></script>';
	    echo '<script type="text/javascript" src="'.VOTES_PATH . 'scripts/jquery-ui-timepicker-addon.js'.'"></script>';
	    echo "<script type='text/javascript' src='".VOTES_PATH . 'scripts/votes-admin_old.js'."'></script>";
	}
        
    }

}
if(!function_exists('votesadd_tax_admin_scripts')){
	function votesadd_tax_admin_scripts( $hook ) {	      
	    global $post, $taxonomy,$post_type;
	    if (  $hook == 'edit-tags.php' || $hook == 'contest_page_votes-license' || $hook=='toplevel_page_contestants' || $post_type=='contestants' || $taxonomy==VOTES_TAXONOMY || $hook=='contest_page_move_posts' || $hook=='contest_page_votes_csv' ||$hook=='contest_page_votes_export' || $hook=='contest_page_votes_purge' || $hook=='contest_page_fieldcontestant' || $hook=='contest_page_fieldregistration' || $hook == 'contest_page_votes_settings' || $hook=='contest_page_options-votesadvancedexcerpt' || $hook=='contest_page_contestpagenavi' || $hook=='contest_page_votes-license' || $hook=='admin_page_move_posts') {
		votes_admin_styles();
	    }
	}
}
add_action( 'admin_enqueue_scripts', 'votesadd_tax_admin_scripts', 10, 1 );

$menupos=26; // This helps to avoid menu position conflicts with other plugins.
while (isset($GLOBALS['menu'][$menupos])) $menupos+=1;
/*
 * Custom Post type and Taxonomy
 */
if (!function_exists('votes_custom_init')) {

    function votes_custom_init() {
       
	$menupos=26; // This helps to avoid menu position conflicts with other plugins.
	while (isset($GLOBALS['menu'][$menupos])) $menupos+=1;
	
        register_post_type(VOTES_TYPE, array('label' => __('Contestants','voting-contest'),
            'description' => '',
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => false,
            'capability_type' => 'post',
            'hierarchical' => false,
	    'rewrite' => false,
            'query_var' => true,
            'supports' => array('title',
                'editor',
                'thumbnail',
                'author',
		'comments',
                'page-attributes'),
            'labels' => array(
                'name' => __('Contestants','voting-contest'),
                'singular_name' => __('Contest','voting-contest'),
                'menu_name' => __('Contests','voting-contest'),
                'add_new' => __('Add Contestant','voting-contest'),
                'add_new_item' => __('Add New Contestant','voting-contest'),
                'edit' => __('Edit','voting-contest'),
                'edit_item' => __('Edit Contestant','voting-contest'),
                'new_item' => __('New Contestant','voting-contest'),
                'view' => __('View Contestant','voting-contest'),
                'view_item' => __('View Contestant','voting-contest'),
                'search_items' => __('Search Contestant','voting-contest'),
                'not_found' => __('No Contestants Found','voting-contest'),
                'not_found_in_trash' => __('No Contestants Found in Trash','voting-contest'),
                'parent' => 'Parent Contestants',
		'menu_position' => $menupos,
            )));

        register_taxonomy(VOTES_TAXONOMY, array(
					    0 => VOTES_TYPE,
					), array('hierarchical' => true,
					'label' => 'Contest Category',
					'show_ui' => true,
					'query_var' => true,
					'rewrite' => false,
					'singular_label' => __('Contest Category','voting-contest')));
		
        
       voting_alter_table_fields(); 
       
       if(isset($_REQUEST['oauth_token']) && $_SESSION['token'] == $_REQUEST['oauth_token']) {
        $votes_settings = get_option(VOTES_SETTINGS);
            do_action( 'zn_twitter_auth_hook', $votes_settings );           
       }
    }
}
add_action( 'zn_twitter_auth_hook', 'zn_twitter_auth_login', 10, 1 );
//Function to Alter the Table Fileds While Releasing
if(!function_exists('voting_alter_table_fields')){
    function voting_alter_table_fields()
    {
        global $wpdb;
        $query = "SHOW COLUMNS FROM ".VOTES_ENTRY_CUSTOM_TABLE;
        $columns = $wpdb->get_results($query);
        $colum_check=array();
        if(!empty($columns)){
            foreach($columns as $key => $col_val){ 
                $colum_check[] = $columns[$key]->Field;
            }
        }
        if(!in_array('admin_view',$colum_check)){
            $tbl_sqls = "ALTER TABLE ".VOTES_ENTRY_CUSTOM_TABLE." ADD `admin_view` VARCHAR(5) NOT NULL DEFAULT 'N'";
            $wpdb->query($tbl_sqls);   
        }
        if(!in_array('pretty_view',$colum_check)){
            $tbl_sqls = "ALTER TABLE ".VOTES_ENTRY_CUSTOM_TABLE." ADD `pretty_view` ENUM(  'Y',  'N' ) NOT NULL DEFAULT  'N'";
            $wpdb->query($tbl_sqls);   
        }
    }
}

/*
 * Customized columns in contest page
 */
if (!function_exists('add_new_votes_columns')) {

    function add_new_votes_columns() {
        $new_columns['cb'] = '<input type="checkbox" />';
        $new_columns['cb'] = '<input type="checkbox" />';
        $new_columns['image'] = __('Featured Image', 'voting-contest');
        $new_columns['title'] = __('Title', 'voting-contest');
        $new_columns[VOTES_TAXONOMY] = __('Contest Category', 'voting-contest');
        $new_columns['votes'] = __('Votes', 'voting-contest');
        $new_columns['date'] = __('Date', 'voting-contest');
        return $new_columns;
    }

}

if(!function_exists('add_new_votestax_column')){
    function add_new_votestax_column($existing_columns) {
	$new_columns['cb'] = '<input type="checkbox" />';
	$new_columns['id'] = __('ID', 'voting-contest');		
	$new_columns['starttime'] = __('Start Time', 'voting-contest');
	$new_columns['expiry'] = __('End Time', 'voting-contest');
	$new_columns['name'] = __('Name', 'voting-contest');
	$new_columns['description'] = __('Description', 'voting-contest');
	$new_columns['slug'] = __('Slug', 'voting-contest');
	$new_columns['posts'] = __('Voting Contest', 'voting-contest');
	return $new_columns;
    }
}

if(!function_exists('custom_new_votestax_column')){
    function custom_new_votestax_column($out, $column_name, $theme_id){
	$theme = get_term($theme_id, 'votes');
	switch ($column_name) {
		case 'id':				
			$out .= $theme_id;
		break;
		case 'expiry':
			$expoption  = get_option($theme_id . '_' . VOTES_TAXEXPIRATIONFIELD);		
			if(isset($expoption) && $expoption != '0' && $expoption){
				$votes_expiration = date('m-d-Y H:i:s', strtotime(str_replace('-', '/', $expoption )));
			}else{
				$votes_expiration = 'No Expiration';
			}
			$out .= $votes_expiration;
		break;
		case 'starttime':
			$startoption  = get_option($theme_id . '_' . VOTES_TAXSTARTTIME);		
			if(isset($startoption) && $startoption != '0' && $startoption){
				$starttime = date('m-d-Y H:i:s', strtotime(str_replace('-', '/', $startoption )));
			}else{
				$starttime = 'Not Set';
			}
			$out .= $starttime;
		break;
		default:
		break;
	}
	return $out; 
    }
}

if (!function_exists('custom_new_votes_column')) {

    function custom_new_votes_column($column, $post_id) {
        switch ($column) {
	    case 'voteid':
		    echo $post_id;
	    break;
            case VOTES_TAXONOMY:
                $terms = get_the_terms($post_id, VOTES_TAXONOMY);
                if (!empty($terms)) {
                    $out = array();
                    foreach ($terms as $c) {
                        $_taxonomy_title = esc_html(sanitize_term_field('name', $c->name, $c->term_id, 'category', 'display'));
                        $out[] = "<a href='edit.php?" . VOTES_TAXONOMY . "=$c->slug&post_type=" . VOTES_TYPE . "'>$_taxonomy_title</a>";
                    }
                    echo join(', ', $out);
                } else {
                    _e('Uncategorized','voting-contest');
                }
            break;

            case 'image':
                if (has_post_thumbnail($post_id)) {
		$image_arr = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'thumbnail');
		$image_src = $image_arr[0];			
		$image1 = wp_votes_image_resize_thumb(get_post_thumbnail_id($contest_post->ID),'',50,50,true);
		echo "<img src=".$image1['url']." width=".$image1['width']." height=".$image1['height']." class='left-img-thumb' />";
			
			
                } else {
                    echo 'No Featured Image';
                }
                break;
			case 'description':
                if ($excerptv = get_the_excerpt($post_id)) {
                    echo $excerptv ;
                } else {
                    echo 'No Description';
                }
                break;
			case 'slug':
                if ($slugv = votes_slug($post_id)) {
                    echo $slugv ;
                } else {
                    echo 'contest';
                }
            break;
            case 'votes':
                $votes = get_post_meta($post_id, VOTES_CUSTOMFIELD,'true');
		echo $votes;
                //echo isset($votes[0]) ? $votes[0] : 0;
            break;
        }
    }

}

/*Retrieves ths slug*/
if(!function_exists('votes_slug')){
    function votes_slug($postid){
      $post_data = get_post($postid);
	    $slug = $post_data->post_name;    
	    return $slug; 
    }
}
if(!function_exists('votes_custom_post_page_sort')){
    function votes_custom_post_page_sort($columns) {
	    $columns['votes']='votes';	 
	    return $columns;
    }
}

if(!function_exists('votes_column_orderby')){
    function votes_column_orderby( $vars ) {
	if ( isset( $vars['orderby'] ) && 'votes' == $vars['orderby'] ) {
	    $vars = array_merge( $vars, array(
		'meta_key' => 'votes_count',
		'orderby' => 'meta_value_num'
	    ) );
	}
	return $vars;
    }
}
add_filter( 'request', 'votes_column_orderby' );


if (!function_exists('votes_custom_column')) {
    function votes_custom_column() {
        add_filter('manage_edit-' . VOTES_TYPE . '_columns', 'add_new_votes_columns');
	add_filter('manage_edit-' . VOTES_TYPE . '_sortable_columns', 'votes_custom_post_page_sort', 10, 2);
        add_action('manage_' . VOTES_TYPE . '_posts_custom_column', 'custom_new_votes_column', 10, 2);

	add_filter("manage_edit-". VOTES_TAXONOMY ."_columns", 'add_new_votestax_column');
	add_action('manage_' . VOTES_TAXONOMY . '_custom_column', 'custom_new_votestax_column', 10, 3);

    }
}

/*
* Overview page
*/
if(!function_exists('votes_overview')){
    function votes_overview(){
	include_once('votes-overview.php');
    }	    
}


/*
 * Bulk Move
 */
if (!function_exists('move_posts')) {

    function move_posts() {
        include_once('move-posts.php');
    }

}
/*
 *  Purge voting entries page
 */
if (!function_exists('votes_purge')) {
    function votes_purge() {
        include_once('votes-purge.php');
    }
}


/*
 *  Voting settings page
 */
if (!function_exists('votes_settings')) {
    function votes_settings() {
        include_once('votes_settings.php');
    }
}

/*
* Changes the Position of the Votes Admin menu
*/
add_filter( 'custom_menu_order', 'votes_admin_submenu_order' );

if(!function_exists('votes_admin_submenu_order')){
    function votes_admin_submenu_order( $menu_ord ) 
    {
	global $submenu;
	$arr = array();
	$menu_re_order = array(17,5,15,10,18,19,20,21,22,23,16);
	foreach($menu_re_order as $re_order){
		$arr[] = $submenu['edit.php?post_type='.VOTES_TYPE][$re_order];
	}
	$submenu['edit.php?post_type='.VOTES_TYPE] = $arr;

	return $menu_ord;
    }
}
/*
*Change the Label of Admin menu
*/
if(!function_exists('votes_admin_submenu_labelrename')){
    function votes_admin_submenu_labelrename(){
	    global $menu,$submenu;
	    $submenu['edit.php?post_type='.VOTES_TYPE][1][0] = 'Contestants';
	    return $submenu;
	    
    }
}
add_filter( 'admin_head', 'votes_admin_submenu_labelrename' );   
/*
 *  Voting Admin menus
 */
if (!function_exists('votes_csv_menu_init')) {

function votes_csv_menu_init() {
    global $votes_type; 

    add_menu_page('Contests-Voting', 'Contest', 'manage_options',VOTES_TYPE, 'votes_overview');      
    
    add_submenu_page(VOTES_TYPE, __('Overview','voting-contest'), __('Overview','voting-contest'), 'manage_options', VOTES_TYPE, 'votes_overview');  

    add_submenu_page(VOTES_TYPE, __('Contest Category','voting-contest'),"<span class='vote_contest_cat'>".__('Contest Category','voting-contest')."</span>", 'publish_pages', 'edit-tags.php?taxonomy=contest_category&post_type=contestants', '');
    
    add_submenu_page(VOTES_TYPE, __('Contestants','voting-contest'), "<span class='vote_contest_contestants'>".__('Contestants','voting-contest')."</span>", 'publish_pages', 'edit.php?post_type=contestants', ''); 
    
    
    add_submenu_page('', __('Add Contestant','voting-contest'), __('Add Contestant','voting-contest'), 'publish_pages', 'post-new.php?post_type=contestants', '');
		    
    $movepg = add_submenu_page('', __('Move Contestant','voting-contest'), __('Move Contestant','voting-contest'), 'publish_pages', 'move_posts', 'move_posts');

    add_submenu_page('', __('Import Contestants','voting-contest'), __('Import Contestant','voting-contest'), 'publish_pages', 'votes_csv', 'votes_csv_import');
    
    add_submenu_page('', __('Export Contestants','voting-contest'), __('Export Contestant','voting-contest'), 'publish_pages', 'votes_export', 'votes_csv_export');
		 
    $mgmtpg = add_submenu_page(VOTES_TYPE, __('Clear Voting Entries','voting-contest'), __('Clear Voting Entries','voting-contest'), 'publish_pages', 'votes_purge', 'votes_purge');
    
    add_submenu_page('', __('Contestant fields','voting-contest'), __('Contestant fields','voting-contest'), 'publish_pages', 'fieldcontestant', 'votes_fieldcontestant');
    
    add_submenu_page(VOTES_TYPE, __('Registration fields','voting-contest'), __('Registration fields','voting-contest'), 'publish_pages', 'fieldregistration', 'votes_registrationcontestant');
    
    add_submenu_page('', __('Voting Logs','voting-contest'), __('Voting Logs','voting-contest'), 'publish_pages', 'votinglogs', 'votes_votinglogs');
	       
    $settingspg = add_submenu_page(VOTES_TYPE, __('Settings','voting-contest'), __('Settings','voting-contest'), 'publish_pages', 'votes_settings', 'votes_settings');
    
   

  } 
}  

add_action('init', 'votes_custom_init');
add_action('admin_init', 'votes_custom_column');
add_action('admin_menu', 'votes_csv_menu_init');  

function vote_tax_menu_correction($parent_file) {
	global $current_screen;
	$taxonomy = $current_screen->taxonomy;
	if ($taxonomy == 'contest_category'){
		$parent_file = VOTES_TYPE;
		
	    // Not our post type, exit earlier
	    if( 'contestants' != $current_screen->post_type )
		return;
	    
	    if( isset( $_GET['post_type'] ) && 'contestants' == $_GET['post_type'] )
	    {       
	    ?>
		<script type="text/javascript">
		jQuery(document).ready( function($) 
		{
		    var reference = $('.vote_contest_cat').parent().parent();
		    // add highlighting to our custom submenu
		    reference.addClass('current');
		    //remove higlighting from the default menu
		    reference.parent().find('li:first').removeClass('current');             
		});     
		</script>
	    <?php
	    }
	}
	return $parent_file;
}
add_action('parent_file', 'vote_tax_menu_correction');


/*
* Fixes for Category reordering while assigning Child category for the post
*/
    add_filter( 'wp_terms_checklist_args',  'votes_checklist_args'  );
    if(!function_exists('votes_checklist_args')){
	    function votes_checklist_args( $args ) {
		    add_action( 'admin_footer',  'votes_checklist_args_script' );
		    $args['checked_ontop'] = false;

		    return $args;
	    }
    }
    // Scrolls to first checked category
    if(!function_exists('votes_checklist_args_script')){
	function votes_checklist_args_script() {
	?>
	<script type="text/javascript">
	    jQuery(function(){
		jQuery('[id$="-all"] > ul.categorychecklist').each(function() {
			var $list = jQuery(this);
			var $firstChecked = $list.find(':checked').first();

			if ( !$firstChecked.length )
				return;

			var pos_first = $list.find(':checkbox').position().top;
			var pos_checked = $firstChecked.position().top;

			$list.closest('.tabs-panel').scrollTop(pos_checked - pos_first + 5);
		});
	    });
	</script>
	<?php
	}
    }

	
/**
 * Add Photographer Name  fields to media uploader
 *
 */

    if(!function_exists('votes_attachment_field_credit')) {
       function votes_attachment_field_credit( $form_fields, $post ) {
	   $form_fields['contestant-photographer-name'] = array(
		   'label' => 'Photographer Name',
		   'input' => 'text',
		   'value' => get_post_meta( $post->ID, VOTES_CONTESTPHOTOGRAPHERNAME, true ) 
	   );
	   return $form_fields;
       }
    }
add_filter( 'attachment_fields_to_edit', 'votes_attachment_field_credit', 10, 2 );

/**
 * Save values of Photographer Name  in media uploader
 */
    if(!function_exists('votes_attachment_field_credit_save')) {
	function votes_attachment_field_credit_save( $post, $attachment ) {
	    if( isset( $attachment['contestant-photographer-name'] ) )
		    update_post_meta( $post['ID'], VOTES_CONTESTPHOTOGRAPHERNAME, $attachment['contestant-photographer-name'] );
	    return $post;
	}
    }
add_filter( 'attachment_fields_to_save', 'votes_attachment_field_credit_save', 10, 2 );


/**
* Checks whether contest is Started or not
*/
if(!function_exists('votes_is_contest_started')) {
    function votes_is_contest_started($id = FALSE) {	     
	$idarr = explode(',', $id);
	$curterm = $time = NULL;
	if (count($idarr) > 1) {			
		       $time = get_option('VOTES_GENERALSTARTTIME');
	} 
	else if( !is_wp_error($curterm = get_term( $id, VOTES_TAXONOMY)) && isset($curterm)) {	
		       if( !votes_validateby_activation_limit($curterm->term_id) ){	
			       return FALSE;
			}
		       $time = get_option($curterm->term_id . '_' . VOTES_TAXSTARTTIME);
	}
	if($time != '0' && trim($time) && $time) {
	       $timeentered = strtotime(str_replace("-", "/", $time));
	       $currenttime = current_time( 'timestamp', 0 );
	       $time = date('Y-m-d-H-i-s', strtotime(str_replace('-', '/', $time)));
	       if($currenttime <= $timeentered) {
		       return FALSE;
	       }
	}else {
	       return TRUE;
	}
	return TRUE;
    }
}

/**
* Checks whether contest reached its end date or not
*/
if(!function_exists('votes_is_contest_reachedend')) {
    function votes_is_contest_reachedend($id = FALSE) {
	$idarr = explode(',', $id);
	 $curterm = $time = NULL;
	 
	 if (count($idarr) > 1) {			
			$time = get_option('VOTES_GENERALEXPIRATIONFIELD');
	 } 
	 else if( !is_wp_error($curterm = get_term( $id, VOTES_TAXONOMY)) && isset($curterm) ) {
			if(!votes_validateby_activation_limit($curterm->term_id)){
				return TRUE;
			 }
			$time = get_option($curterm->term_id . '_' . VOTES_TAXEXPIRATIONFIELD);
	 }
	 if($time != '0' && trim($time) && $time) {
		$timeentered = strtotime(str_replace("-", "/", $time));
		$currenttime = current_time( 'timestamp', 0 );
		$time = date('Y-m-d-H-i-s', strtotime(str_replace('-', '/', $time)));
		if($currenttime <= $timeentered) {
			return FALSE;
		}
	}else {
		return FALSE;
	}
	return TRUE;
    }
}

/**
* Makes the tax active once its constestant limits reached
*/
if(!function_exists('votes_make_tax_active')) {
    function votes_make_tax_active( $post_id ) {
	    $post_categories =  wp_get_object_terms( $post_id, VOTES_TAXONOMY );
	    $cats = array();
	    
	if(count($post_categories ) && !is_wp_error($post_categories ) && (get_post_type( $post_id ) == VOTES_TYPE))	{	
	    foreach($post_categories as $c){
		if(get_option($c->term_id.'_'.VOTES_TAXACTIVATIONLIMIT, TRUE) <= get_term_post_count_by_type($c->term_id)) {
		    update_option($c->term_id.'_'.VOTES_TAXISACTIVE, 'on');
		}
	    }
	}
    }
}

/*
 * Retrieves the Total Number of post in Term of particular Taxonomy
 */
if (!function_exists('get_term_post_count_by_type')) {

    function get_term_post_count_by_type($term, $taxonomy = VOTES_TAXONOMY, $type = VOTES_TYPE) {
        $args = array(
            'fields' => 'ids',
            'posts_per_page' => -1,
            'post_type' => $type,
            'tax_query' => array(
                array(
                    'taxonomy' => $taxonomy,
                    'field' => 'id',
                    'terms' => $term
                )
            ),
            'post_status' => 'publish'
        );

        $posts = get_posts($args);
		
        if (count($posts) > 0) {
            return count($posts);
        } else {
            return 0;
        }
    }

}

if(!function_exists('votes_get_admin_userid')) {
    function votes_get_admin_userid() {
	$args = array(
		'blog_id'      => $GLOBALS['blog_id'],
		'role'         => 'administrator',
		'orderby'      => 'ID',
		'order'        => 'ASC'
	 ); 
	 $user_list = get_users( $args );
	 return $user_list[0]->ID;
    }
}


if(!function_exists('votes_get_currentpageurl')) {
    function votes_get_currentpageurl($qstr = NULL) {
	$pageURL = 'http';
	if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
	$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") {
	 $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} else {
	 $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	if(stripos($pageURL, '?') && $qstr) {
	       $pageURL .= $pageURL .'&'.$qstr;
	}
	else {
	       $pageURL .= $pageURL .'?'.$qstr;
	}
	return $pageURL;
    }
}

if(!function_exists('votes_get_closest_contestid')) {
    function votes_get_closest_contestid() {
	global $wpdb;
	$terms = $wpdb->get_results("SELECT `option_name`  FROM `".$wpdb->prefix."_options` where `option_name` like '%_".VOTES_TAXSTARTTIME."'  and STR_TO_DATE( `option_value`, '%Y-%m-%d %T' ) > '".date('Y-m-d H:i:s')."'  order by STR_TO_DATE( `option_value`, '%Y-%m-%d %T' ) LIMIT 0,1");
	if(count($terms)) {
		$termdata = $terms[0]->option_name;
		$termdataArr = explode('_', $termdata );
		return $termdataArr[0];
	}
	return 0;
    }
}

if(!function_exists('votes_validateby_activation_limit')){
    function votes_validateby_activation_limit($id = NULL) {
	$limitcnt = (int)trim(get_option($id.'_'.VOTES_TAXACTIVATIONLIMIT));
	$postcnt = get_term_post_count_by_type($id);
	if(!$limitcnt ) {
		return TRUE;
	}else if($limitcnt > $postcnt){
		return FALSE;
	}else if( $postcnt >= $limitcnt ){
		return TRUE;
	}else {
		return TRUE;
	}
    }
}

if(!function_exists('votes_is_addform_blocked')){
    function votes_is_addform_blocked($id = NULL) {
	$starttime = get_option($id . '_' . VOTES_TAXSTARTTIME);
	$expirytime = get_option($id . '_' . VOTES_TAXEXPIRATIONFIELD);
	$starttimetimestamp = strtotime(str_replace("-", "/", $starttime));
	$expirytimetimestamp = strtotime(str_replace("-", "/", $expirytime));
	$currenttimestamp = current_time( 'timestamp', 0 );
	$isstarted = TRUE;
	$blocked = FALSE;
	$msg = FALSE;
	if( !trim($starttimetimestamp)) {
		$isstarted = FALSE;
		$blocked = FALSE;
	}else if($currenttimestamp > $starttimetimestamp){
		$blocked = TRUE;
		$isstarted = TRUE;
		if($blocked){
			$option = get_option(VOTES_SETTINGS);
			$msg = $option['vote_entriescloseddesc'];
		}
	}
	if(!$isstarted) {
		if(!trim($expirytimetimestamp)) {
			$blocked = FALSE;
		}else if($currenttimestamp > $expirytimetimestamp) {
			$blocked = TRUE;
			if($blocked){
				$option = get_option(VOTES_SETTINGS);
				$msg = $option['vote_reachedenddesc'];
			}
		}
	}
	return $msg;
    }
}

if(!function_exists('votes_get_available_contest')){
    function votes_get_available_contest($forcedisplay = FALSE){
	global $wpdb;
	if($forcedisplay) {
	    $taxargs = array('orderby' => 'name',
		    'order' => 'asc');
	    $id = array();
	    $v_terms = get_terms(VOTES_TAXONOMY, $taxargs);
	    if(count($v_terms)){
		    foreach ($v_terms as $v_term) {
			    $id[] = $v_term->term_id;

		    }
	    }
	    return $id;
	}
			
	$upcomingquery = "SELECT `option_name`  FROM `".$wpdb->prefix."options` where (`option_name` like '%_".VOTES_TAXSTARTTIME."'  and STR_TO_DATE( `option_value`, '%Y-%m-%d %T' ) > '".date('Y-m-d H:i:s')."') AND  (`option_name` like '%_".VOTES_TAXSTARTTIME."' and option_value != '')";
	
	$upcomingcontests = $wpdb->get_results($upcomingquery);
	$invalidterms = array();
	if(count($upcomingcontests)) {
	    foreach($upcomingcontests as $upcomingcontest) {
		$termarr = explode('_', $upcomingcontest->option_name);
		if(!in_array($termarr[0], $invalidterms))
		 array_push($invalidterms, $termarr[0]);
	    }
	}
	
	$expiredquery = "SELECT `option_name`  FROM `".$wpdb->prefix."options` where (`option_name` like '%_".VOTES_TAXEXPIRATIONFIELD."'  and STR_TO_DATE( `option_value`, '%Y-%m-%d %T' ) < '".date('Y-m-d H:i:s')."')  AND  (`option_name` like '%_".VOTES_TAXEXPIRATIONFIELD."' and option_value != '')";		
	$expiredcontests = $wpdb->get_results($expiredquery);		
	if(count($expiredcontests)) {
	    foreach($expiredcontests as $expiredcontest) {
		$termarr = explode('_', $expiredcontest->option_name);
		if(!in_array($termarr[0], $invalidterms))
			array_push($invalidterms, $termarr[0]);
	    }
	}
	
	
	$activationcountquery = "SELECT `option_name`  FROM `".$wpdb->prefix."options` where (`option_name` like '%_".VOTES_TAXACTIVATIONLIMIT."'  and  CONVERT(`option_value`, SIGNED) > 0)  AND  (`option_name` like '%_".VOTES_TAXACTIVATIONLIMIT."' and option_value != '')";		
	$activationcountterms = $wpdb->get_results($activationcountquery);
	if(count($activationcountterms)) {
	    foreach($activationcountterms as $activationcountterm){
		$termarr = explode('_', $activationcountterm->option_name);
		if(get_term_post_count_by_type($termarr[0]) < (int)trim(get_option($termarr[0].'_'.VOTES_TAXACTIVATIONLIMIT))){
		    array_push($invalidterms, $termarr[0]);
		}
	    }
	}
	if(count($invalidterms)) {
		$termquery = "SELECT t.*, tt.* FROM ".$wpdb->prefix."terms AS t INNER JOIN ".$wpdb->prefix."term_taxonomy AS tt ON t.term_id = tt.term_id WHERE tt.taxonomy IN ('".VOTES_TAXONOMY."') AND tt.count > 0 AND t.term_id NOT IN (".implode(', ', $invalidterms).") ORDER BY t.name ASC";
	}else {
		$termquery = "SELECT t.*, tt.* FROM ".$wpdb->prefix."terms AS t INNER JOIN ".$wpdb->prefix."term_taxonomy AS tt ON t.term_id = tt.term_id WHERE tt.taxonomy IN ('".VOTES_TAXONOMY."') AND tt.count > 0 ORDER BY t.name ASC";
	}
	$terms = $wpdb->get_results($termquery);
	$validterms = array();
	if(count($terms)) {
	    foreach($terms as $term){
		array_push($validterms, $term->term_id);
	    }
	}
	return $validterms;
    }
}

/*********** Select dropdown *********************************/
function wp_votes_select_input($name, $values, $default = '', $parameters = '') {
    $field = '<select name="' . ee_tep_output_string($name) . '"';
    if (ee_tep_not_null($parameters))
	    $field .= ' ' . $parameters;
    $field .= '>';

    if (empty($default) && isset($GLOBALS[$name]))
	    $default = stripslashes($GLOBALS[$name]);

    for ($i = 0, $n = sizeof($values); $i < $n; $i++) {
	    $field .= '<option value="' . $values[$i]['id'] . '"';
	    if ($default == $values[$i]['id']) {
		    $field .= 'selected = "selected"';
	    }

	    $field .= '>' . $values[$i]['text'] . '</option>';
    }
    $field .= '</select>';

    return $field;
}

function ee_tep_output_string($string, $translate = false, $protected = false) {
    if ($protected == true) {
	    return htmlspecialchars($string);
    } else {
	if ($translate == false) {
		return ee_tep_parse_input_field_data($string, array('"' => '&quot;'));
	} else {
		return ee_tep_parse_input_field_data($string, $translate);
	}
    }
}

function ee_tep_parse_input_field_data($data, $parse) {
    return strtr(trim($data), $parse);
}

function ee_tep_not_null($value) {
    if (is_array($value)) {
	    if (sizeof($value) > 0) {
		    return true;
	    } else {
		    return false;
	    }
    } else {
	    if (($value != '') && (strtolower($value) != 'null') && (strlen(trim($value)) > 0)) {
		    return true;
	    } else {
		    return false;
	    }
    }
}

if(!function_exists('wp_vote_custom_admin_menu_bar')){
    function wp_vote_custom_admin_menu_bar() {
	    wp_votes_tab_display();
    }
}
add_action( 'wp_after_admin_bar_render', 'wp_vote_custom_admin_menu_bar' );


if(!function_exists('wp_votes_tab_display')){
function wp_votes_tab_display() {
?>
	<?php
        if( isset( $_GET[ 'page' ]) || isset( $_GET['post_type']) || isset($_GET['action']) ) {    
    		$called_php_file = basename($_SERVER['REQUEST_URI'], '?' . $_SERVER['QUERY_STRING']);
    		$active_tab = $_GET[ 'page' ];
    		$active_tab1 = $_GET[ 'post_type' ];
            $active_tab2 = $_GET[ 'taxonomy' ];
            
            $current_post = $_GET['post'];
            if ( 'contestants' == get_post_type($current_post) )
                 $edit_post_page = $_GET['action'];		
	    }
        else
        {
		  return;
	    }
	    remove_action( 'admin_notices', 'update_nag', 3 );
	
	$page_show = array('move_posts','votes_csv','votes_export','votinglogs','fieldcontestant','edit');
    $post_show = array('contestants','move_posts','votes_csv','votes_export','votinglogs','fieldcontestant','edit');
    $tax_show  = array('contest_category');
    
	if((in_array($active_tab,$page_show) || in_array($active_tab1,$post_show) || in_array($edit_post_page,$page_show))&&($called_php_file=='edit.php' || $called_php_file=='post-new.php' || $called_php_file=='admin.php' || $called_php_file=='edit-tags.php' || $called_php_file =='post.php')) {
	 if(!in_array($active_tab2,$tax_show)){
        ?>
<style>
.menu_bar_admin{margin-bottom: 10px;padding-top:10px;}
.menu_bar_admin a{padding: 4px 10px 6px;font-weight: bold;font-size: 15px;line-height: 24px;}
.menu_bar_admin #icon-themes{margin-top:0px;}
.voting-tabs{border-bottom: 1px solid #ccc;}
li#toplevel_page_contestants a.wp-not-current-submenu{background:none repeat scroll 0 0 #0074A2;}
li#toplevel_page_contestants a div.wp-menu-image:before{color:#ffffff;}
li#toplevel_page_contestants a.wp-not-current-submenu:after{border: 8px solid rgba(0, 0, 0, 0);    content: " ";
height: 0;margin-top: -8px;pointer-events: none;position: absolute; right: 0;top: 50%;width: 0;border-right-color:#F1F1F1 !important;}

</style>

<script type="text/javascript">
	jQuery(document).ready( function($) 
	{
	       jQuery('li#toplevel_page_contestants').removeClass('wp-not-current-submenu');  
           jQuery('li#toplevel_page_contestants').addClass('wp-has-current-submenu'); 
           
           jQuery('li#toplevel_page_contestants a.toplevel_page_contestants').removeClass('wp-not-current-submenu');
           jQuery('li#toplevel_page_contestants a.toplevel_page_contestants').addClass('wp-has-current-submenu');
           
           var reference = $('.vote_contest_contestants').parent().parent();
	      // add highlighting to our custom submenu
	      reference.addClass('current');
	      //remove higlighting from the default menu
	      reference.parent().find('li:first').removeClass('current');   
                      
	});     
</script>
                
        <!-- Create a header in the default WordPress 'wrap' container -->
        <div class="menu_bar_admin">
            <div id="icon-themes" class="icon32"></div>           
            <div class="nav-tab-wrapper voting-tabs">
		<a href="edit.php?post_type=contestants" class="nav-tab <?php echo ($active_tab1 == 'contestants' && $called_php_file=='edit.php')? 'nav-tab-active' : ''; ?>"><?php _e('Contestants','voting-contest'); ?></a>
                <a href="post-new.php?post_type=contestants" class="nav-tab <?php echo ($active_tab1 == 'contestants' && $called_php_file=='post-new.php') ? 'nav-tab-active' : ''; ?>"><?php _e('Add Contestants','voting-contest'); ?></a>
		<a href="admin.php?page=move_posts" class="nav-tab <?php echo ($active_tab == 'move_posts') ? 'nav-tab-active' : ''; ?>"><?php _e('Move Contestant','voting-contest'); ?></a>
                <a href="admin.php?page=votes_csv" class="nav-tab <?php echo ($active_tab == 'votes_csv')? 'nav-tab-active' : ''; ?>"><?php _e('Import Contestant','voting-contest'); ?></a>
                <a href="admin.php?page=votes_export" class="nav-tab <?php echo ($active_tab == 'votes_export') ? 'nav-tab-active' : ''; ?>"><?php _e('Export Contestant','voting-contest'); ?></a>
                <a href="admin.php?page=votinglogs" class="nav-tab <?php echo ($active_tab == 'votinglogs') ? 'nav-tab-active' : ''; ?>"><?php _e('Vote Log','voting-contest'); ?></a>    
                <a href="admin.php?page=fieldcontestant" class="nav-tab <?php echo ($active_tab == 'fieldcontestant') ? 'nav-tab-active' : ''; ?>"><?php _e('Contestant Form Builder','voting-contest'); ?></a> 
            </div>          
        </div><!-- /.wrap -->
    <?php
    }
	}
    } // end wp_votes_tab_display
}

if(!function_exists('voting_additional_fields_pretty')){
    function voting_additional_fields_pretty($post_id = null)
    {
        ob_start();
        $vote_id = ($post_id == null)?$_POST['pid']:$post_id;
        ?>
        <div class="prettyphoto_additional_details">
        <?php       
            $votes_settings = get_option( VOTES_SETTINGS );            
            if($votes_settings['vote_show_date_prettyphoto'] == 'on'){ 
        ?>
                <span class="pretty_date"><?php _e('Date : ','voting-contest');echo get_the_time( "Y-m-d", $vote_id ); ?></span>
        <?php } ?>
        <?php            
            global $wpdb;
            $sql = "SELECT * FROM " . VOTES_ENTRY_CUSTOM_TABLE . " where pretty_view='Y' and delete_time = 0 order by sequence";
            $questions = $wpdb->get_results($sql);	    
            $sql1 = "SELECT * FROM " . VOTES_POST_ENTRY_TABLE . " WHERE post_id_map = '" . $vote_id. "'";
            $field_val = $wpdb->get_row($sql1);
            
            if(!empty($field_val)){
                $field_vals = unserialize($field_val->field_values);    
            }else{
                 $field_vals = '';
            }
		     $show_once1='1';
             if(!empty($questions)){
             foreach($questions as $ques){
               if(!empty($field_val)){
                 if($ques->system_name != 'contestant-desc'):                             
			     ?>
                 <div class="contestant_custom_fields">
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
                 </div>           
                <?php         
			      endif;
                }
			    $show_once1++;
                        }
                    }
                 ?>	       
                
        </div>
        <?php
        $out = ob_get_contents();
        ob_end_clean();
        if(isset($_POST['pid']))
            die($out);
        else
            return $out;        
    }
}
add_action('wp_ajax_voting_additional_fields_pretty', 'voting_additional_fields_pretty');
add_action('wp_ajax_nopriv_voting_additional_fields_pretty', 'voting_additional_fields_pretty');

function voting_delete_single_contesnts($vote_id) {
    global $current_user, $wp_roles;
    get_currentuserinfo();
       
    $post = get_post( $vote_id );
    
    if($post->post_author == $current_user->ID){
        wp_delete_post($post->ID, true);
        return '<div class="contestants-success vote-profile-status">
						<div class="success-rows">'
                        .__("Contestant Deleted Successfully","voting-contest").
                        '</div>'.
                '</div>';
    }
    else{
        return '<p class="required-mark">'.__("You do not have sufficient permission to delete","voting-contest").'</p>';
    }    
    
}

/*Bulk APPROVE for Contestants*/
add_action('admin_footer-edit.php', 'voting_bulk_add_approve');
add_action('load-edit.php', 'voting_bulk_add_approve_action');
add_action('admin_notices','voting_bulk_add_approve_notices');
function voting_bulk_add_approve() {
 
  global $post_type;
 
  if($post_type == VOTES_TYPE && ($_REQUEST['post_status'] == '' || $_REQUEST['post_status'] == 'pending')) {
    ?>
    <script type="text/javascript">
      jQuery(document).ready(function() {
        jQuery('<option>').val('approve').text('<?php _e('Approve')?>').appendTo("select[name='action']");   
        jQuery('<option>').val('approve').text('<?php _e('Approve')?>').appendTo("select[name='action2']");     
      });
    </script>
    <?php
  }
} 
function voting_bulk_add_approve_action() {
    $screen = get_current_screen();
 
    if (!isset($screen->post_type) || VOTES_TYPE !== $screen->post_type) {
        return; 
    }
    $wp_list_table = _get_list_table('WP_Posts_List_Table');
    
    $action = $wp_list_table->current_action(); 
    
    $approved = 0;   
    
    switch($action) 
    {       
        case 'approve':          
            // make sure ids are submitted.  depending on the resource type, this may be 'media' or 'ids'
            if(isset($_REQUEST['post'])) {
                    $post_ids = array_map('intval', $_REQUEST['post']);
            }
                               
            if(empty($post_ids)) return;               
            
            $sendback = remove_query_arg( array('exported', 'untrashed', 'deleted', 'ids'), wp_get_referer() );
            if ( ! $sendback )
                $sendback = admin_url( "edit.php?post_type=".VOTES_TYPE );         
                        
            $pagenum = $wp_list_table->get_pagenum();
            $sendback = add_query_arg( 'paged', $pagenum, $sendback );                    
            
            global $wpdb; 
            
            $exploded_ids = implode(',',$post_ids);
            
            //Get the Status Changing Contestants
            $query = "SELECT ID FROM $wpdb->posts WHERE ID IN ({$exploded_ids}) AND post_status = 'pending'";          
            $result_ids = $wpdb->get_results($query,'ARRAY_A');  
                         
            //Change the Status of the Contestants            
            foreach($post_ids as $pid):  
                $contestants = array( 'ID' => $pid, 'post_status' => 'publish' );
                remove_action('save_post', 'wp_votes_save_custom_details' );
                wp_update_post($contestants);   
                add_action('save_post', 'wp_votes_save_custom_details' );
            endforeach;            
            
            $sendback = add_query_arg( array('approved' => $approved, 'ids' => count($result_ids) ), $sendback );
            
            break;
            
        default: return;
  }
  
  wp_redirect($sendback);
  exit();  
  
}
function voting_bulk_add_approve_notices() {
        global $post_type, $pagenow;        
        if($pagenow == 'edit.php' && $post_type == VOTES_TYPE) {
                if (isset($_REQUEST['approved'])) {
                        //Print notice in admin bar
                        $message = sprintf( _n( 'Contestants approved.', '%s Contestants approved.', $_REQUEST['approved'] ), number_format_i18n( $_REQUEST['ids']) ) ;
                        if(!empty($message)) {
                                echo "<div class=\"updated\"><p>{$message}</p></div>";
                        }
                }
        }
}
