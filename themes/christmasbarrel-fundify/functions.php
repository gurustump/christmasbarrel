<?php
/**
 * Christmas Barrel additional functions and definitions
 *
 */
/**
 * Enqueue scripts and styles
 */
function christmasbarrel_scripts() {
	global $edd_options;

	$protocol = is_ssl() ? 'https' : 'http'; 
	
	if ( fundify_theme_mod( 'responsive' ) )  {
		wp_enqueue_style( 'christmasbarrel-responsive', get_stylesheet_directory_uri() . '/css/responsive.css', array('fundify-style','fundify-responsive') );
	}
	wp_enqueue_script( 'christmasbarrel-scripts', get_stylesheet_directory_uri() . '/js/christmasbarrel.js', array(), 20140709, true );
}
add_action( 'wp_enqueue_scripts', 'christmasbarrel_scripts' );

add_image_size( 'vid-thumb', 224, 126, true );
add_image_size( 'vid-thumb-med', 178, 100, true );
add_image_size( 'vid-thumb-small', 160, 90, true );

add_theme_support( 'post-formats', array( 'gallery','image','video','audio' ) );


function customAd($item,$size) {
	if ( has_post_thumbnail($item->ID) ) {
		$thisAd = '<a class="ad-link" href="'.$item->post_excerpt.'" alt="'.$item->post_content.'" target="_blank">';
		$thisAd .= get_the_post_thumbnail($item->ID, $size);
		$thisAd .= '</a>';
		return $thisAd;
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

include 'includes/meta-box.php';