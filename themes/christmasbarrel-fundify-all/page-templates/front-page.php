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

	<?php if ( null == fundify_theme_mod( 'hero_slider' ) ) : ?>
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
	<?php endif; ?>

	<div id="content">
		<div class="container">
			
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

<h3 class="sbfp_title">Get<span class="green">Notified</span></h3>

<div class="socialBox_frontPage">

<div class="sbfp_icons">

<a href="https://www.youtube.com/user/ChristmasBarrel"><img src="http://rightstuff.us/Barrel2014/wp-content/uploads/2014/07/youtube.jpg" class="smSquare"></a>

<a href="https://www.facebook.com/ChristmasBarrel"><img src="http://rightstuff.us/Barrel2014/wp-content/uploads/2014/07/facebook.jpg" class="smSquare"></a>

<a href="https://plus.google.com/u/0/b/110359901439331284129/110359901439331284129/videos/p/pub?pageId=110359901439331284129"><img src="http://rightstuff.us/Barrel2014/wp-content/uploads/2014/07/google_plus.jpg" class="smSquare"></a>

<a href="https://twitter.com/ChristmasBarrel"><img src="http://rightstuff.us/Barrel2014/wp-content/uploads/2014/07/twitter.jpg" class="smSquare"></a>

<a href="https://www.facebook.com/ChristmasBarrel"><img src="http://rightstuff.us/Barrel2014/wp-content/uploads/2014/07/facebook.jpg" class="smSquare"></a>
</div>
<div class="sbfp_text">



Queen Elizabeth visited the set of @GameOfThrones today. When she left, she became the 1st royal to ever get out of "Game of Thrones" alive.
</div>
</div>

<h3 class="sbfp_title">2013<span class="green">Calendar</span></h3>

<div class="socialBox_frontPage">
<div class="sbfp_thumb_container">

<div class="sbfp_thumb"></div>
 <div class="sbfp_thumb"></div>
 <div class="sbfp_thumb"></div>
 <div class="sbfp_thumb"></div>
 <div class="sbfp_thumb"></div>
 <div class="sbfp_thumb"></div>
 <div class="sbfp_thumb"></div>
 <div class="sbfp_thumb"></div>
 <div class="sbfp_thumb"></div>
 <div class="sbfp_thumb"></div>
 <div class="sbfp_thumb"></div>
 <div class="sbfp_thumb"></div>
 <div class="sbfp_thumb"></div>
 <div class="sbfp_thumb"></div>
 <div class="sbfp_thumb"></div>
 <div class="sbfp_thumb"></div>
 <div class="sbfp_thumb"></div>
 <div class="sbfp_thumb"></div>
 <div class="sbfp_thumb"></div>
 <div class="sbfp_thumb"></div>
 <div class="sbfp_thumb"></div>
 <div class="sbfp_thumb"></div>
 <div class="sbfp_thumb"></div>
 <div class="sbfp_thumb"></div>
 


</div>

</div>


		</div>
		<!-- / container -->
	</div>
	<!-- / content -->

<?php get_footer(); ?>