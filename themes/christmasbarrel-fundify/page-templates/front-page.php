<?php
/**
 * Template Name: Front Page
 *
 * This should be used in conjunction with the Fundify plugin.
 *
 * @package Fundify
 * @since Fundify 1.0
 */

global $wp_query;

get_header(); 
?>
	<div id="home-page-featured-static">
		<h1><span class="main">A Video Advent Calendar</span><span class="sub"></span></h1>
	</div>
	<?php /* if ( null == fundify_theme_mod( 'hero_slider' ) ) : ?>
	<div id="home-page-featured">
	


<?php
			if ( fundify_is_crowdfunding()  ) :
				$featured = new ATCF_Campaign_Query( array( 
					'posts_per_page' => 'grid' == fundify_theme_mod( 'hero_style' ) ? apply_filters( 'fundify_hero_campaign_grid', 16 ) : 1,
					'meta_query'     => array(
						array(
							'key'     => '_campaign_featured',
							'value'   => 1,
							'compare' => '=',
							'type'    => 'numeric'
						)
					)
				) ); 
			else :
				$featured = new WP_Query( array( 
					'posts_per_page' => 'grid' == fundify_theme_mod( 'hero_style' ) ? apply_filters( 'fundify_hero_campaign_grid', 16 ) : 1
				) ); 
			endif; 
		?>
		<?php if ( 'grid' == fundify_theme_mod( 'hero_style' ) ) : ?>
			<?php for ( $i = 0; $i < 2; $i++ ) : shuffle( $featured->posts ); ?>
			<ul>
				<?php while ( $featured->have_posts() ) : $featured->the_post(); ?>
				<li><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(); ?></a></li>
				<?php endwhile; ?>
			</ul>
			<?php endfor; ?>
		<?php else : ?>
			<?php while ( $featured->have_posts() ) : $featured->the_post(); ?>
				<?php $thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id(), 'fullsize' ); ?>
				<a href="<?php the_permalink(); ?>" class="home-page-featured-single"><img src="<?php echo $thumbnail[0]; ?>" /></a>
			<?php endwhile; ?>
		<?php endif; ?>

		<h1>
			<?php 
				$string = fundify_theme_mod( 'hero_text' ); 
				$lines = explode( "\n", $string );
			?>
			<span><?php echo implode( '</span><br /><span>', $lines ); ?></span>
		</h1>
		<!-- / container -->
	</div>
	<?php else : ?>
		<?php echo do_shortcode( fundify_theme_mod( 'hero_slider' ) ); ?>
	<?php endif; */ ?>

	<div id="content">
		<div class="container">
		 

<?php /*
			<?php locate_template( array( 'searchform-campaign.php' ), true ); ?>
			<?php locate_template( array( 'content-campaign-sort.php' ), true ); ?>

			<div id="projects">
				<section>
					<?php 
						if ( fundify_is_crowdfunding()  ) :
							$wp_query = new ATCF_Campaign_Query( array(
								'paged' => ( get_query_var( 'page' ) ? get_query_var( 'page' ) : 1 )
							) );
						else :
							$wp_query = new WP_Query( array(
								'posts_per_page' => get_option( 'posts_per_page' ),
								'paged'          => ( get_query_var('page') ? get_query_var('page') : 1 )
							) );
						endif;

						if ( $wp_query->have_posts() ) :
					?>

						<?php while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>
							<?php get_template_part( 'content', fundify_is_crowdfunding() ? 'campaign' : 'post' ); ?>
						<?php endwhile; ?>

					<?php else : ?>

						<?php get_template_part( 'no-results', 'index' ); ?>

					<?php endif; ?>
				</section>

				<?php do_action( 'fundify_loop_after' ); ?>
			</div>



*/ ?>					<?php 						$catAdvent = get_term_by('slug','advent','category');						$cat2014 = get_term_by('slug','2014','category');						$posts2014_query = get_posts( array( 'posts_per_page' => -1, 'category__and' => array($catAdvent->term_id, $cat2014->term_id) ) );  												foreach($posts2014_query as $post) {							if ($latestVideo) {								$this_youtube_id = get_post_meta($post->ID,'chrbar_youtube_vid_id',true);								$this_vimeo_id = get_post_meta($post->ID,'chrbar_vimeo_vid_id',true);																echo '<pre style="display:none">';									echo get_post_meta($post->ID,'chrbar_advent_number',true);									echo '<br />';									echo get_post_meta($latestVideo->ID,'chrbar_advent_number',true);								echo '</pre>';								if (get_post_meta($post->ID,'chrbar_advent_number',true) < get_post_meta($latestVideo->ID,'chrbar_advent_number',true) && (!empty($this_youtube_id) || !empty($this_vimeo_id))) {									$latestVideo = $post;								}							} else {								$latestVideo = $post;							}						}					?>			<pre style="display:none">				<?php print_r($latestVideo); ?>			</pre>			<?php $latestVideo_youtube_id = get_post_meta($latestVideo->ID,'chrbar_youtube_vid_id',true); ?>			<?php $latestVideo_vimeo_id = get_post_meta($latestVideo->ID,'chrbar_vimeo_vid_id',true); ?>			<?php if ( ! empty($latestVideo_youtube_id) || ! empty($latestVideo_vimeo_id )) { ?>			<h3 class="sbfp_title">Latest<span class="green">Video</span></h3>			<div class="section_frontPage">				<?php if ( ! empty($latestVideo_youtube_id) ) { ?>				<div class="video-container">					<iframe width="1280" height="720" src="http://www.youtube.com/embed/<?php echo $latestVideo_youtube_id; ?>?VQ=HD720&rel=0&modestbranding=1&wmode=transparent" frameborder="0" allowfullscreen></iframe>				</div>				<?php } else if ( ! empty($latestVideo_vimeo_id)) { ?>				<div class="video-container vimeo-video-container">					<iframe src="//player.vimeo.com/video/<?php echo $latestVideo_vimeo_id; ?>?title=0&amp;byline=0&amp;portrait=0" width="1140" height="641" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>				</div>				<?php } ?>			</div>			<?php } ?>
			<h3 class="sbfp_title">2014<span class="green">Calendar</span></h3>
			<div class="section_frontPage">
				<ul  class="vid-nav">
					<?php foreach($posts2014_query as $post) { ?>
					<li>
						<?php echo customPostVideoThumbnail(get_the_ID(),'vid-thumb'); ?>
					</li>
					<?php } ?>
				</ul>
			</div>



<h3 class="sbfp_title">Get<span class="green">Notified</span></h3>
			<div class="section_frontPage socialBox_frontPage">
				<ul class="sbfp_icons">
					<li>
						<a target="_blank" class="youtube" href="https://www.youtube.com/user/ChristmasBarrel">
							<img src="<?php echo get_stylesheet_directory_uri(); ?>/img/youtube.jpg" alt="YouTube" />
						</a>
					</li>
					<li>
						<a target="_blank" class="facebook" href="https://www.facebook.com/ChristmasBarrel">
							<img src="<?php echo get_stylesheet_directory_uri(); ?>/img/facebook.jpg" alt="Facebook" />
						</a>
					</li>
					<li>
						<a target="_blank" class="google-plus" href="https://plus.google.com/u/0/b/110359901439331284129/110359901439331284129/videos/p/pub?pageId=110359901439331284129">
							<img src="<?php echo get_stylesheet_directory_uri(); ?>/img/google_plus.jpg" alt="Google+" />
						</a>
					</li>
					<li>
						<a target="_blank" class="twitter" href="https://twitter.com/ChristmasBarrel">
							<img src="<?php echo get_stylesheet_directory_uri(); ?>/img/twitter.jpg" alt="Twitter" />
						</a>
					</li>
					<li>
						<a target="_blank" class="pinterest" href="https://www.pinterest.com/ChristmasBarrel">
							<img src="<?php echo get_stylesheet_directory_uri(); ?>/img/pinterest.png" alt="Pinterest" />
						</a>
					</li>
				</ul>
				<div class="sbfp_text">
					Subscribe, like, or follow now to get notified of the videos in December. You can always unsubscribe if you stop believing in Santa before then.
				</div>

			</div>




			<h3 id="2013Calendar" class="sbfp_title">2013<span class="green">Calendar</span></h3>
			<div class="section_frontPage">
				<ul  class="vid-nav">
					<?php 
						$cat2013 = get_term_by('slug','2013','category');
						$posts2013_query = get_posts( array( 'posts_per_page' => -1, 'category__and' => array($catAdvent->term_id, $cat2013->term_id) ) );  
					?>
					
					<?php foreach($posts2013_query as $post) { ?>
					<li>
						<?php echo customPostVideoThumbnail(get_the_ID(),'vid-thumb'); ?>
					</li>
					<?php } ?>
				</ul>
			</div>

		</div>
		<!-- / container -->
	</div>
	<!-- / content -->

<?php get_footer(); ?>