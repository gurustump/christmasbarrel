<?php
// Load jQuery
/*if ( !function_exists(core_mods) ) {
	function core_mods() {
		if ( !is_admin() ) {
			wp_deregister_script('jquery');
			wp_register_script('jquery', ("http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"), false);
			wp_enqueue_script('jquery');
		}
	}
	core_mods();
}*/

// register menus
add_action( 'init', 'register_menus' );
function register_menus() {
	register_nav_menus(
		array(
			'footer-menu'		=> __( 'Footer Menu' )
		)
	);
}

function add_first_and_last($output) {
	$output = preg_replace('/class="(\w*\s)?menu-item/', 'class="$1first-menu-item menu-item', $output, 1);
	$pos=strripos($output, 'class="menu-item');
	$len=strlen('class="menu-item');
	$rep='class="last-menu-item menu-item';
	//double-check for a later entry with menu-item later in the
	//class list
	if(strripos($output, ' menu-item ')>$pos){
	  $pos=strripos($output, ' menu-item ');
	  $len=strlen(' menu-item ');
	  $rep=' last-menu-item menu-item ';
	}
	$output = substr_replace($output, $rep, $pos, $len);
	return $output;
}
add_filter('wp_nav_menu', 'add_first_and_last');

	add_theme_support( 'post-formats', array('video')); // Add 3.1 post format theme support.

// post thumbnail support
if ( function_exists( 'add_theme_support' ) ) { 
	add_theme_support( 'post-thumbnails' ); 
}
add_image_size( 'vid-thumb', 224, 126, true );
add_image_size( 'vid-thumb-small', 160, 90, true );
// post thumbnail function
function customAd($item,$size) {
	if ( has_post_thumbnail($item->ID) ) {
		$thisAd = '<a class="ad-link" href="'.$item->post_excerpt.'" alt="'.$item->post_content.'" target="_blank">';
		$thisAd .= get_the_post_thumbnail($item->ID, $size);
		$thisAd .= '</a>';
		return $thisAd;
	}
}
function customPostThumbnail($postID,$size) {
	if ( has_post_thumbnail($postID) ) {
		$thisThumbnail = '<a class="post-thumbnail-container" href="';
		$thisThumbnail .= get_permalink($postID);
		$thisThumbnail .= '">';
		$thisThumbnail .= get_the_post_thumbnail($postID, $size);
		$thisThumbnail .= '</a>';
		return $thisThumbnail;
	}
}
function customPostVideoThumbnail($postID,$size) {
	if ( has_post_thumbnail($postID) ) {
		$thisTitle = get_the_title($postID);
		$thisTitleLength = strlen($thisTitle);
		$maxLength = $size == 'vid-thumb-small' ? 21:30;
		$truncatedTitle = substr($thisTitle,0,$maxLength).($thisTitleLength > $maxLength ? '&hellip;':'');
		$thisThumbnail = '<a id="vid_';
		$thisThumbnail .= get_post_meta($postID,'chrbar_youtube_vid_id',true);
		$thisThumbnail .= '" class="post-thumbnail-container" href="';
		$thisThumbnail .= get_permalink($postID);
		$thisThumbnail .= '">';
		$thisThumbnail .= get_the_post_thumbnail($postID, $size);
		$thisThumbnail .= '<span class="title">';
		$thisThumbnail .= $truncatedTitle;
		$thisThumbnail .= '</span></a>';
		$thisThumbnail .= '<div class="info-box">';
		$thisThumbnail .= '<div class="advent-number">'.get_post_meta($postID,'chrbar_advent_number',true).'</div>';
		$thisThumbnail .= '<div class="info-box-content"><h3>'.get_the_title($postID).'</h3>';
		$thisThumbnail .= '<div class="description">'.get_the_excerpt($postID).'</div></div>';
		$thisThumbnail .= '</div>';
		return $thisThumbnail;
	} else {
		return '<div class="advent-number">'.get_post_meta($postID,'chrbar_advent_number',true).'</div>';
	}
}

// register Module category and post type
add_action( 'init', 'create_module_taxonomy', 0 );
function create_module_taxonomy() {
	register_taxonomy('module_category',array('module'), array(
		'hierarchical' => true,
		'labels' => array(
			'name' => _x( 'Module Categories', 'taxonomy general name' ),
			'singular_name' => _x( 'Module Category', 'taxonomy singular name' ),
			'search_items' =>  __( 'Search Module Categories' ),
			'all_items' => __( 'All Module Categories' ),
			'parent_item' => __( 'Parent Module Category' ),
			'parent_item_colon' => __( 'Parent Module Category:' ),
			'edit_item' => __( 'Edit Module Category' ), 
			'update_item' => __( 'Update Module Category' ),
			'add_new_item' => __( 'Add New Module Category' ),
			'new_item_name' => __( 'New Module Category Name' ),
			'menu_name' => __( 'Module Categories' ),
		),
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => array( 'slug' => 'module_category' ),
  ));
}
add_action( 'init', 'module_post_type_init' );
function module_post_type_init() {
	$labels = array(
		'name' => _x( 'Modules', 'post type general name' ), 
		'singular_name' => _x( 'Module', 'post type singular name' ),
		'add_new' => _x( 'Add New', 'module' ),
		'add_new_item' => __( 'Add New Module' ),
		'edit_item' => __( 'Edit Module' ),
		'new_item' => __( 'New Module' ),
		'view_item' => __( 'View Module' ),
		'search_items' => __( 'Search Module' ),
		'not_found' =>  __( 'No modules found' ),
		'not_found_in_trash' => __( 'No modules found in Trash' ),
		'parent_item_colon' => ''
	);
	 
	$args = array( 
		'labels' => $labels,
		'public' => false,
		'publicly_queryable' => false,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => true,
		'capability_type' => 'post',
		'hierarchical' => true,
		'menu_position' => null,
		'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields' )
	);
 
	register_post_type( 'module', $args ); 
}

add_filter('gallery_style', create_function('$a', 'return "<div class=\'gallery\'>";'));

// shortcodes
// root path shortcode
function root_path_shortcode() {
	return get_bloginfo('url') . '/';
}
add_shortcode('root_path', 'root_path_shortcode');

include 'includes/meta-box.php';


/*function wptuts_add_rewrite_rules() {  
	add_rewrite_rule(  
		'^([^/]*)/about$',
		'index.php#about',
		'top'  
	);  
}  */

/*
function customAdmin() {
	echo '<!-- custom admin css -->
		  <link rel="stylesheet" type="text/css" href="' . get_bloginfo('stylesheet_directory') . '/_/css/wp-admin.css" />
		  <!-- /end custom adming css -->';
}
add_action('admin_head', 'customAdmin');
*/

// adding new setting for site offline mode to general settings page
// borrowed from http://codex.wordpress.org/Settings_API
function offline_setting_init() {
// Add the section to reading settings so we can add our
// fields to it
add_settings_section('offline_setting_section',
	'Site Offline Switch',
	'offline_setting_section_callback_function',
	'general');

// Add the field with the names and function to use for our new
// settings, put it in our new section
add_settings_field('offline_setting_name',
	'<strong>Site Offline</strong>',
	'offline_setting_callback_function',
	'general',
	'offline_setting_section');

// Register our setting so that $_POST handling is done for us and
// our callback function just has to echo the <input>
register_setting('general','offline_setting_name');
}// offline_settings_api_init()

add_action('admin_init', 'offline_setting_init');


// ------------------------------------------------------------------
// Settings section callback function
// ------------------------------------------------------------------
//
// This function is needed if we added a new section. This function 
// will be run at the start of our section
//

function offline_setting_section_callback_function() {
echo '<div style="max-width:500px"><p>This checkbox allows you to take the site offline and display only the "placeholder.php" file located at wp-content\themes\meghanstettler\includes to users who are not logged in.</p><p>The checkbox should remain <strong>unchecked</strong> for <strong>normal site operation</strong>.</p><p>The checkbox should be <strong>checked</strong> to take the site <strong>offline</strong>.</p></div>';
}

// ------------------------------------------------------------------
// Callback function for our example setting
// ------------------------------------------------------------------
//
// creates a checkbox true/false option. Other types are surely possible
//

function offline_setting_callback_function() {
echo '<input name="offline_setting_name" id="gv_thumbnails_insert_into_excerpt" type="checkbox" value="1" class="code" ' . checked( 1, get_option('offline_setting_name'), false ) . ' />';
}

// this will load the placeholder page for all non-logged in users as long as it's enabled.
if (get_option('offline_setting_name') == true) {
	function maintenace_mode() {
		if ( !current_user_can( 'read' ) || !is_user_logged_in() ) {
			die(include 'includes/placeholder.php');
		}
	}
	add_action('get_header', 'maintenace_mode');
}

?>