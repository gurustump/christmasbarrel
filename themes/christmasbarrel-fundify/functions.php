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
		wp_enqueue_style( 'christmasbarrel-responsive', get_stylesheet_directory_uri() . '/css/responsive.css?v1', array('fundify-style','fundify-responsive') );
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
function customPostVideoThumbnail($postID,$size,$no_info_box) {
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
		$thisThumbnail .=  is_ssl() ? str_replace('http://','https://', get_the_post_thumbnail($postID, $size)) : get_the_post_thumbnail($postID, $size);
		$thisThumbnail .= '<span class="title">';
		$thisThumbnail .= $truncatedTitle;
		$thisThumbnail .= '</span></a>';
		if (!$no_info_box) {
		$thisThumbnail .= '<div class="info-box">';
		$thisThumbnail .= '<div class="advent-number"><span class="num">'.get_post_meta($postID,'chrbar_advent_number',true).'</span> <span class="sub">days \'til Christmas</span></div>';
		$thisThumbnail .= '<div class="info-box-content"><h3>'.get_the_title($postID).'</h3>';
		$thisThumbnail .= '<div class="description">'.get_the_excerpt($postID).'</div></div>';
		$thisThumbnail .= '</div>';
		}
		return $thisThumbnail;
	} else {
		return '<div class="advent-number">'.get_post_meta($postID,'chrbar_advent_number',true).' <span class="sub">day'.(get_post_meta($postID,'chrbar_advent_number',true) == 1 ? '':'s').' \'til Christmas</span></div>';
	}
}

// shortcodes
function list_campaigns_func($atts) {
	if ( fundify_is_crowdfunding()  ) {
		$campaigns = new ATCF_Campaign_Query( array(
			'paged' => ( get_query_var( 'page' ) ? get_query_var( 'page' ) : 1 )
		) );
		if ( $campaigns->have_posts() ) {
			ob_start();
				echo '<div class="campaign-list">';
				while ( $campaigns->have_posts() ) : $campaigns->the_post(); 
					get_template_part( 'content', 'campaign' );
				endwhile;
				echo '</div>';
			return ob_get_clean();
		}
	} else {
		return false;
	}
}
add_shortcode('list_campaigns', 'list_campaigns_func');

/**
 * Adds Advent_Calendar_Widget widget.
 */
class Advent_Calendar_Widget extends WP_Widget {
	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'advent_calendar_widget', // Base ID
			__( 'Advent Calendar', 'text_domain' ), // Name
			array( 'description' => __( "Lists all the videos in a year's advent calendar", 'text_domain' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
	
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}
		$catAdvent = get_term_by('slug','advent','category');
		$catYear = get_term_by('slug',$instance['year'],'category');
		$widget_posts_query = get_posts( array( 'posts_per_page' => -1, 'category__and' => array($catAdvent->term_id, $catYear->term_id) ) );
		
		echo '<ul class="vid-nav">';
		foreach($widget_posts_query as $post) {
		echo '<li>';
			echo customPostVideoThumbnail($post->ID,'vid-thumb',true);
		echo '</li>';
		}
		echo '</ul>';
		
		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
     	        $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'New title', 'text_domain' );
     	        $year = ! empty( $instance['year'] ) ? $instance['year'] : __( '2014', 'text_domain' );
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'year' ); ?>"><?php _e( 'Year:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'year' ); ?>" name="<?php echo $this->get_field_name( 'year' ); ?>" type="text" value="<?php echo esc_attr( $year ); ?>">
		</p>
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['year'] = ( ! empty( $new_instance['year'] ) ) ? strip_tags( $new_instance['year'] ) : '';

		return $instance;
	}

} // class Advent_Calendar_Widget

add_action( 'widgets_init', 'load_advent_calendar_widget' );

/* Function that registers our widget. */
function load_advent_calendar_widget() {
	register_widget( 'Advent_Calendar_Widget' );
}

include 'includes/meta-box.php';