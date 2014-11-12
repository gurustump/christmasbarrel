<?php    
/*
Plugin Name: WP Voting Contest
Version: 2.5.2
Description: Quickly and seamlessly integrate an online contest with voting into your Wordpress 3.6+ website. You can start many types of online contests such as photo, video, audio, names with very little effort.
Author: Ohio Web Technologies
Author URI: http://www.ohiowebtech.com

Copyright (c) 2008-2014 Ohio Web Technologies All Rights Reserved.

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 */
error_reporting(0);
load_plugin_textdomain( 'voting-contest', '', dirname( plugin_basename( __FILE__ ) ) . '/lang' );
global $wpdb;  
if (!defined('VOTES_ABSPATH'))
    define('VOTES_ABSPATH', dirname(__FILE__) . '/');
if (!defined('VOTES_PATH'))
    define('VOTES_PATH', plugin_dir_url(__FILE__));
if (!defined('VOTES_TBL'))
    define('VOTES_TBL', $wpdb->prefix . 'votes_tbl');
    
if(!defined('VOTES_ENTRY_CUSTOM_TABLE'))
    define("VOTES_ENTRY_CUSTOM_TABLE", $wpdb->prefix . "votes_custom_field_contestant");    

if(!defined('VOTES_POST_ENTRY_TABLE'))
    define("VOTES_POST_ENTRY_TABLE", $wpdb->prefix . "votes_post_entry_contestant"); 
    
if(!defined('VOTES_USER_ENTRY_TABLE'))
    define("VOTES_USER_ENTRY_TABLE", $wpdb->prefix . "votes_user_entry_contestant"); 

if(!defined('VOTES_USER_CUSTOM_TABLE'))
    define("VOTES_USER_CUSTOM_TABLE", $wpdb->prefix . "votes_custom_registeration_contestant");         
    
if (!defined('VOTES_TYPE'))
    define('VOTES_TYPE', 'contestants');
if (!defined('VOTES_TAXONOMY'))
    define('VOTES_TAXONOMY', 'contest_category');
if (!defined('VOTES_CUSTOMFIELD'))
    define('VOTES_CUSTOMFIELD', 'votes_count');
if (!defined('VOTES_EXPIRATIONFIELD'))
    define('VOTES_EXPIRATIONFIELD', 'votes_expiration');
if (!defined('VOTES_SETTINGS'))
    define('VOTES_SETTINGS', 'votes_settings');    
if (!defined('VOTES_CONTENT_LENGTH'))
    define('VOTES_CONTENT_LENGTH', get_option('votesadvancedexcerpt_length'));
if (!defined('VOTES_CONTENT_ELLIPSES'))
    define('VOTES_CONTENT_ELLIPSES', get_option('votesadvancedexcerpt_ellipsis'));
if (!defined('VOTES_SHOW_DESC'))
    define('VOTES_SHOW_DESC', 'list');
if (!defined('VOTES_ENTRY_LIMIT_FORM'))
    define('VOTES_ENTRY_LIMIT_FORM', '');
if (!defined('VOTES_TEXTDOMAIN'))
    define('VOTES_TEXTDOMAIN', 'wp-pagenavi');
if (!defined('VOTES_TAXEXPIRATIONFIELD'))
    define('VOTES_TAXEXPIRATIONFIELD', 'votes_taxexpiration');
if (!defined('VOTES_TAXACTIVATIONLIMIT'))
    define('VOTES_TAXACTIVATIONLIMIT', 'votes_taxactivationlimit');
if (!defined('VOTES_TAXSTARTTIME'))
    define('VOTES_TAXSTARTTIME', 'votes_taxstarttime');
if (!defined('VOTES_GENERALEXPIRATIONFIELD'))
    define('VOTES_GENERALEXPIRATIONFIELD', 'votes_generalexpiration');
if (!defined('VOTES_GENERALSTARTTIME'))
    define('VOTES_GENERALSTARTTIME', 'votes_generalstarttime');
if (!defined('VOTES_CONTESTPHOTOGRAPHERNAME'))
    define('VOTES_CONTESTPHOTOGRAPHERNAME', 'contestant_photographer_name');

if (!defined('VOTES_SL_PLUGIN_DIR')) {
    define('VOTES_SL_PLUGIN_DIR', plugin_dir_path(__FILE__));}
if (!defined('VOTES_SL_PLUGIN_URL')) {
    define('VOTES_SL_PLUGIN_URL', plugin_dir_url(__FILE__));}
if (!defined('VOTES_SL_PLUGIN_FILE')) {
    define('VOTES_SL_PLUGIN_FILE', __FILE__);}
if (!defined('WP_VOTING_SL_STORE_API_URL')) {
    define('WP_VOTING_SL_STORE_API_URL', 'http://plugins.ohiowebtech.com');}
if (!defined('WP_VOTING_SL_PRODUCT_NAME')){
    define('WP_VOTING_SL_PRODUCT_NAME', 'WordPress Voting Photo Contest Plugin');}
    
//Updates available
if(!function_exists('votes_version_updater_admin')){    
    function votes_version_updater_admin()
    {
        if (!class_exists('Votes_Updater')) {
            include( dirname(__FILE__) . '/includes/votes-updater.php' );
        }
    
        $wp_voting_sl_license_key = trim(get_option('wp_voting_software_license_key'));
        $wp_voting_ = new Votes_Updater(WP_VOTING_SL_STORE_API_URL, __FILE__, array(
                'version' => '2.5.2',
                'license' => $wp_voting_sl_license_key,
                'item_name' => WP_VOTING_SL_PRODUCT_NAME,
                'author' => 'Ohio Web Technologies'
                ));
    }
}
add_action( 'admin_init', 'votes_version_updater_admin' );
 
 
            
register_activation_hook(__FILE__, 'votes_activation_init');
if (!function_exists('votes_activation_init')) {
    function votes_activation_init() {
        global $wpdb;
        //Get all posts and add the meta for vote order by fix
        $query = "Select ID from ".$wpdb->prefix ."posts where post_type='".VOTES_TYPE."'";
        $get_all_posts = $wpdb->get_results($query);
        if(is_array($get_all_posts)){
            foreach($get_all_posts  as $get_posts_id){
               $post_id_val = $get_posts_id->ID;
               $meta_values = get_post_meta( $post_id_val); 
               if(!is_array($meta_values['votes_count'])){
                 update_post_meta($post_id_val, VOTES_CUSTOMFIELD, 0);   
               }
            }
        }   
        $tbl_sql = 'CREATE TABLE IF NOT EXISTS ' . VOTES_TBL . '(
                    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    ip VARCHAR( 255 ) NOT NULL,
                    votes INT NOT NULL DEFAULT 0,
                    post_id INT NOT NULL,
                    termid VARCHAR( 255 ) NOT NULL DEFAULT "0",
                    date DATETIME
                    )';
                    
        $contestant_custom_table = "CREATE TABLE IF NOT EXISTS ".VOTES_ENTRY_CUSTOM_TABLE." (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,`sequence` int(11) NOT NULL DEFAULT '0',
        `question_type` enum('TEXT','TEXTAREA','MULTIPLE','SINGLE','DROPDOWN') NOT NULL DEFAULT 'TEXT',`question` text NOT NULL,
        `system_name` varchar(45) DEFAULT NULL,`response` text,`required` enum('Y','N') NOT NULL DEFAULT 'N',
        `required_text` text,`admin_only` enum('Y','N') NOT NULL DEFAULT 'N',`delete_time` varchar(45) DEFAULT 0,
        `wp_user` int(22) DEFAULT '1', `admin_view` VARCHAR(5) NOT NULL DEFAULT 'N', PRIMARY KEY (`id`),
        KEY `wp_user` (`wp_user`),KEY `system_name` (`system_name`),KEY `admin_only` (`admin_only`))ENGINE=InnoDB"; 
          
        $contestant_custom_val = "CREATE TABLE IF NOT EXISTS ".VOTES_POST_ENTRY_TABLE." (
                                  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                                  `post_id_map` int(11) NOT NULL,
                                  `field_values` longtext NOT NULL,
                                   PRIMARY KEY (`id`)
                                  )ENGINE=InnoDB";
                                  
        $contestant_register_custom_table = "CREATE TABLE IF NOT EXISTS ".VOTES_USER_CUSTOM_TABLE." (
                    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                    `sequence` int(11) NOT NULL DEFAULT '0',
                    `question_type` enum('TEXT','TEXTAREA','MULTIPLE','SINGLE','DROPDOWN') NOT NULL DEFAULT 'TEXT',
                    `question` text NOT NULL,
                    `system_name` varchar(45) DEFAULT NULL,
                    `response` text,
                    `required` enum('Y','N') NOT NULL DEFAULT 'N',
                    `required_text` text,
                    `admin_only` enum('Y','N') NOT NULL DEFAULT 'N',
                    `delete_time` varchar(45) DEFAULT 0,
                    `wp_user` int(22) DEFAULT '1',PRIMARY KEY (`id`),
                     KEY `wp_user` (`wp_user`),KEY `system_name` (`system_name`),KEY `admin_only` (`admin_only`)
                    )ENGINE=InnoDB";
                    
       $contestant_register_custom_val = "CREATE TABLE IF NOT EXISTS ".VOTES_USER_ENTRY_TABLE." (
                                  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                                  `user_id_map` int(11) NOT NULL,
                                  `field_values` longtext NOT NULL,
                                   PRIMARY KEY (`id`)
                                  )ENGINE=InnoDB";                                                                            
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');       
        $wpdb->query($tbl_sql);
        $wpdb->query($contestant_custom_table);
        $wpdb->query($contestant_custom_val);
        $wpdb->query($contestant_register_custom_table);
        $wpdb->query($contestant_register_custom_val);
        
        $desc_rs = wp_voting_get_contestant_desc(); 
        if(count($desc_rs[0]) == 0):
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
         
        $defaults = array('imgheight' => 92,
            'imgwidth' => 132,
            'imgdisplay' => FALSE,
            'title' => FALSE,
            'orderby' => 'date',
            'order' => 'desc',
            'termdisplay' => FALSE,
            'onlyloggedinuser' => FALSE,
            'frequency' => 1,
			'vote_votingtype' => FALSE,
            'deactivation' => 'on',
			'vote_tobestarteddesc' => __('Contest not yet open for voting','voting-contest'),
			'vote_reachedenddesc' => __('There is no Contest at this time','voting-contest'),
			'vote_entriescloseddesc' => __('Contest already Started.','voting-contest'),
			'votes_timertextcolor' => '#000000',
			'votes_timerbgcolor' => '#ffffff');
        $args = get_option(VOTES_SETTINGS);
        $args = wp_parse_args($args, $defaults);
        update_option(VOTES_SETTINGS, $args);
        update_option(VOTES_GENERALEXPIRATIONFIELD, '0');  
        //wp_redirect(get_admin_url().'plugins.php?activate=true&plugin_status=all&paged=1&s=');
    }}
register_uninstall_hook(__FILE__, 'votes_uninstall');
if (!function_exists('votes_uninstall')) {

    function votes_uninstall() {
        global $wpdb;
        $tbl_sql = 'DROP TABLE IF EXISTS ' . VOTES_TBL;
        $wpdb->query($tbl_sql);
        
        $tbl_sql1 = 'DROP TABLE IF EXISTS ' . VOTES_ENTRY_CUSTOM_TABLE;
        $wpdb->query($tbl_sql1);
        
        $tbl_sql2 = 'DROP TABLE IF EXISTS ' . VOTES_POST_ENTRY_TABLE;
        $wpdb->query($tbl_sql2);
        
        $tbl_sql3 = 'DROP TABLE IF EXISTS ' . VOTES_USER_CUSTOM_TABLE;
        $wpdb->query($tbl_sql3);
        
        $tbl_sql4 = 'DROP TABLE IF EXISTS ' . VOTES_USER_ENTRY_TABLE;
        $wpdb->query($tbl_sql4);

        $mycustomposts = get_posts(array('post_type' => VOTES_TYPE, 'numberposts' => -1, 'post_status' => 'any'));
        if (count($mycustomposts) > 0) {
            foreach ($mycustomposts as $mypost) {
                wp_delete_post($mypost->ID, true);
            }
        }
        $taxonomy = VOTES_TAXONOMY;

        $terms = get_terms($taxonomy, array('hide_empty' => false));
        $count = count($terms);
        if ($count > 0) {

            foreach ($terms as $term) {
                wp_delete_term($term->term_id, $taxonomy);
				delete_option($term->term_id . '_' . VOTES_TAXACTIVATIONLIMIT);
				delete_option($term->term_id . '_' . VOTES_TAXSTARTTIME);
                delete_option($term->term_id . '_' . VOTES_TAXEXPIRATIONFIELD);
                delete_option($term->term_id . '_' . VOTES_SETTINGS);
            }
        }
        delete_option(VOTES_SETTINGS);
		delete_option(VOTES_GENERALSTARTTIME);
        delete_option(VOTES_GENERALEXPIRATIONFIELD);
    }

}
if (!function_exists('votes_deactivation_init')) {

    function votes_deactivation_init() {
        $option = get_option(VOTES_SETTINGS);
        if (!$option['deactivation']) {
            votes_uninstall();
        }}
}
register_deactivation_hook(__FILE__, 'votes_deactivation_init');

include_once 'includes/votes_resize_image.php';
include_once 'includes/download_csv.php';
include_once 'includes/votes-init.php';
include_once 'includes/votes-advanced-excerpt.php';
include_once 'includes/votes-menu.php'; 
include_once 'includes/ds_resize.php';
include_once 'pagination/wp-pagenavi.php';
include_once 'includes/votes-user-registration.php';
include_once 'includes/votes-shortcode.php';
include_once 'includes/votes-csv.php';
include_once 'includes/votes-save.php';   
include_once 'includes/votes-metabox.php';
include_once 'includes/votescategory-metabox.php';
include_once 'includes/votes-content.php';
include_once 'includes/votes-contestant-custom.php'; 
include_once 'includes/votes-logs.php';
include_once 'includes/twitter/twitteroauth.php';




/************** Newly added for template override ********/
if(!function_exists('wpvotes_add_my_query_var')){
    function wpvotes_add_my_query_var($vars) {     
        $vars[] = 'contestants';
        return $vars;
    }
}
add_filter('query_vars', 'wpvotes_add_my_query_var');

add_action('parse_query','wp_votes_parse_query_function');
if(!function_exists('wp_votes_parse_query_function')){
    function wp_votes_parse_query_function()
    {    
        global $wp_query; 
        if(isset($wp_query->query_vars['contestants'])){
            if($wp_query->query_vars['contestants']!=''){
                add_filter('the_content','votes_content_update'); 
                //add_filter('post_thumbnail_html','votes_the_post_thumbnail');
                add_filter('single_template', 'vote_body_class');    
            } 
        }   
    }
}
/******************** Action need to processed ******************/

//Redirect to url back
add_action('wp_logout','wp_votes_go_home');

if(!function_exists('wp_votes_go_home')){
    function wp_votes_go_home(){ 
      $previous_url = $_SERVER['HTTP_REFERER']; 
      wp_redirect($previous_url);
      exit();
    }
}

//zero votes need to be shown on order by 
add_action('wp_insert_post', 'update_post_meta_val');
if (!function_exists('update_post_meta_val')) {
    function update_post_meta_val($post_id)
    {
        $vot = get_post_meta($post_id, VOTES_CUSTOMFIELD);
        if (!is_array($vot)) {
            update_post_meta($post_id, VOTES_CUSTOMFIELD, 0);
        }elseif (empty($vot)) {
            update_post_meta($post_id, VOTES_CUSTOMFIELD, 0);
        }
        return true;
    }
}
add_action('wp_update_post','update_post_meta_val');

//Update the custom field sequence
add_action('wp_ajax_update_sequence', 'votes_fieldcontestant');