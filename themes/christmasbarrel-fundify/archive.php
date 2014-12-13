<?php
/**
 * The template for displaying Archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Fundify
 * @since Fundify 1.0
 */

get_header(); ?>

	<div class="title title-two pattern-<?php echo rand(1,4); ?>">
		<div class="container">
			<h1><?php
				if ( is_category() ) {
					single_cat_title( '' );
				} elseif ( is_tag() ) {
					single_tag_title( '' );
				} elseif ( is_author() ) {
					/* Queue the first post, that way we know
					 * what author we're dealing with (if that is the case).
					*/
					the_post();
					echo '<span class="vcard"><a class="url fn n" href="' . get_author_posts_url( get_the_author_meta( "ID" ) ) . '" title="' . esc_attr( get_the_author() ) . '" rel="me">' . get_the_author() . '</a></span>';
					rewind_posts();

				} elseif ( is_day() ) {
					echo get_the_date();
				} elseif ( is_month() ) {
					echo get_the_date( 'F Y' );

				} elseif ( is_year() ) {
					echo get_the_date( 'Y' );

				} else {
					_e( 'Archives', 'fundify' );

				}
			?></h1>
			<h3><?php
				if ( is_category() ) {
					_e( 'Category Archives', 'fundify' );
				} elseif ( is_tag() ) {
					_e( 'Tag Archives', 'fundify' );
				} elseif ( is_author() ) {
					_e( 'Author Archives', 'fundify' );
				} elseif ( is_day() ) {
					_e( 'Daily Archives', 'fundify' );
				} elseif ( is_month() ) {
					_e( 'Monthly Archives', 'fundify' );
				} elseif ( is_year() ) {
					_e( 'Yearly Archives', 'fundify' );
				}
			?></h3>
		</div>
		<!-- / container -->
	</div>
	<div id="content">
		<div class="container">
			<div id="main-content">
				<?php if ( have_posts() ) : ?>

				<?php while ( have_posts() ) : the_post(); ?>
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

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
						<?php if ( ! is_singular() ) : ?>
						<h3 class="sans"><a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'fundify' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h3>
						<?php endif; ?>

						<?php if ( ! is_page() ) : ?>
						<div class="post-meta">
							<div class="date"><i class="icon-calendar"></i> <?php printf( __( 'Date Posted: %s', 'fundify' ), get_the_date() ); ?></div>
							<div class="comments"><span class="comments-link"><i class="icon-comment"></i><?php comments_popup_link( __( ' 0 Comments', 'fundify' ), __( '1 Comment', 'fundify' ), __( '% Comments', 'fundify' ) ); ?></span></div>
						</div>
						<?php endif; ?>
						
						<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'fundify' ) ); ?>

						<?php if ( is_singular() ) : ?>
							<?php wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'fundify' ), 'after' => '</div>' ) ); ?>
							<?php the_tags( '<p class="entry-tags">' . __( 'Tags:', 'fundify' ) . ' ', ', ', '</p>' ); ?>
						<?php endif; ?>
					</div><!-- .entry-content -->
				</article>
				<?php endwhile; ?>

				<?php do_action( 'fundify_loop_after' ); ?>

				<?php else : ?>

					<?php get_template_part( 'no-results', 'index' ); ?>

				<?php endif; ?>
			</div>
			<?php get_sidebar(); ?>
		</div>
		<!-- / container -->
	</div>
	<!-- / content -->

<?php get_footer(); ?>