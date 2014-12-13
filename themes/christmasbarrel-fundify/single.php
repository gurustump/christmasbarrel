<?php
/**
 * The Template for displaying all single posts.
 *
 * @package Fundify
 * @since Fundify 1.0
 */

get_header(); ?>

	<div class="title title-two pattern-<?php echo rand(1,4); ?>">
		<div class="container">
			<?php while ( have_posts() ) : the_post(); ?>
			<h1><?php the_title() ;?></h1>
			<h3><?php printf( __( 'By %s', 'fundify' ), '<span class="vcard"><a class="url fn n" href="' . get_author_posts_url( get_the_author_meta( "ID" ) ) . '" title="' . esc_attr( get_the_author() ) . '" rel="me">' . get_the_author() . '</a></span>' ); ?></h3>
			<?php endwhile; ?>
		</div>
		<!-- / container -->
	</div>
	<div id="content">
		<div class="container">
			<div id="main-content">
				<?php while ( have_posts() ) : the_post(); ?>
					<?php if (has_post_format('video')) { ?>

						<article <?php post_class() ?> id="post-<?php the_ID(); ?>">
							<div class="entry-content">
								<?php $youtube_vid_id = get_post_meta(get_the_ID(),'chrbar_youtube_vid_id',true); ?>
								<?php $vimeo_vid_id = get_post_meta(get_the_ID(),'chrbar_vimeo_vid_id',true); ?>
								<?php if ( ! empty($youtube_vid_id)) { ?>
								<div class="video-container">
									<iframe width="960" height="540" src="http://www.youtube.com/embed/<?php echo $youtube_vid_id; ?>?rel=0&modestbranding=1&wmode=transparent" frameborder="0" allowfullscreen></iframe>
								</div>
								<?php } else if ( ! empty($vimeo_vid_id)) { ?>
								<div class="video-container vimeo-video-container">
									<iframe src="//player.vimeo.com/video/<?php echo $vimeo_vid_id; ?>?title=0&amp;byline=0&amp;portrait=0" width="960" height="540" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
								</div>
								<?php } ?>
								<div class="content-body">
									<div class="social-share-section">
										<a class="youtube-subscribe" href="http://www.youtube.com/subscription_center?add_user=christmasbarrel">Subscribe</a>
										<?php 
										$post_thumbnail_url = get_stylesheet_directory_uri().'/img/Logoweb2014-fb-v2.jpg';
										if ( has_post_thumbnail() ) {
											$post_thumbnail_id = get_post_thumbnail_id();
											$post_thumbnail_url = wp_get_attachment_url( $post_thumbnail_id );
										} ?>
										<a target="_blank" class="facebook-share" href="http://www.facebook.com/sharer.php?s=100&p[title]=<?php the_title(); ?>&p[summary]=<?php echo get_the_excerpt(); ?>&p[url]=<?php the_permalink(); ?>&p[images][0]=<?php echo $post_thumbnail_url; ?>">Share</a>
									</div>
									<?php /*<div class="title-section">
										<h1 class="entry-title"><span class="number"><?php echo get_post_meta(get_the_ID(),'chrbar_advent_number',true); ?></span> - <?php the_title(); ?></h1>
									</div> */ ?>
									<div class="content-section">
										<?php the_content(); ?>
									</div>
								</div>
								<?php /*
								<div class="ad-space">
									<?php $ad336 = get_posts(array('post_type'=>'module','module_category_name' => 'ad336')); ?>
									<?php foreach($ad336 as $key=>$adpost) { ?>
										<?php echo customAd($adpost,'full') ; ?>
									<?php } ?>
								</div>
								*/ ?>
							</div>
						</article>
	
					<?php } else { 
						get_template_part( 'content', 'single' ); } 
					?>

					<?php
						// If comments are open or we have at least one comment, load up the comment template
						if ( comments_open() || '0' != get_comments_number() )
							comments_template( '', true );
					?>
				<?php endwhile; ?>
			</div>
			<?php get_sidebar(); ?>
		</div>
		<!-- / container -->
	</div>
	<!-- / content -->

<?php get_footer(); ?>