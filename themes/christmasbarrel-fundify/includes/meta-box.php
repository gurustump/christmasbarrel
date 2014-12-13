<?php
/**
 * Registering meta boxes
 * For all meta box definitions, see meta-box-demo.php
 * For more information, please visit: 
 * @link http://www.deluxeblogtips.com/2010/04/how-to-create-meta-box-wordpress-post.html
 */

/********************* META BOX DEFINITIONS ***********************/

/**
 * Prefix of meta keys (optional)
 * Use underscore (_) at the beginning to make keys hidden
 * Alt.: You also can make prefix empty to disable it
 */
// Better has an underscore as last sign
$prefix = 'chrbar_';

global $meta_boxes;

$meta_boxes = array();

$meta_boxes[] = array(
	'id' => 'custom-posts',
	'title' => 'Post Custom Fields',
	'pages' => array( 'post' ),
	'context' => 'normal',
	'priority' => 'high',

	// List of meta fields
	'fields' => array(
		array(
			'name'	=> 'YouTube Video ID',
			'id'	=> $prefix . 'youtube_vid_id',
			'type'	=> 'text'
		),
		array(
			'name'	=> 'Vimeo Video ID',
			'id'	=> $prefix . 'vimeo_vid_id',
			'type'	=> 'text'
		),
		array(
			'name'	=> 'SoundCloud Embed Code',
			'id'	=> $prefix . 'soundcloud_embed',
			'type'	=> 'text'
		),
		array(
			'name'	=> 'Advent Calendar Number',
			'id'	=> $prefix . 'advent_number',
			'type'	=> 'select',
			'options' => array (
				'' => 'None',
				'1' => '1',
				'2' => '2',
				'3' => '3',
				'4' => '4',
				'5' => '5',
				'6' => '6',
				'7' => '7',
				'8' => '8',
				'9' => '9',
				'10' => '10',
				'11' => '11',
				'12' => '12',
				'13' => '13',
				'14' => '14',
				'15' => '15',
				'16' => '16',
				'17' => '17',
				'18' => '18',
				'19' => '19',
				'20' => '20',
				'21' => '21',
				'22' => '22',
				'23' => '23',
				'24' => '24'
			)
		)
	)
);



/********************* META BOX REGISTERING ***********************/

/**
 * Register meta boxes
 *
 * @return void
 */
function megstet_register_meta_boxes()
{
	global $meta_boxes;

	// Make sure there's no errors when the plugin is deactivated or during upgrade
	if ( class_exists( 'RW_Meta_Box' ) )
	{
		foreach ( $meta_boxes as $meta_box )
		{
			new RW_Meta_Box( $meta_box );
		}
	}
}
// Hook to 'admin_init' to make sure the meta box class is loaded
//  before (in case using the meta box class in another plugin)
// This is also helpful for some conditionals like checking page template, categories, etc.
add_action( 'admin_init', 'megstet_register_meta_boxes' );