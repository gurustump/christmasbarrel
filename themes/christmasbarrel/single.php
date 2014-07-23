<?php
/**
 * @package WordPress
 * @subpackage HTML5-Reset-WordPress-Theme
 * @since HTML5 Reset 2.0
 */
 get_header(); ?>

	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

		<article <?php post_class() ?> id="post-<?php the_ID(); ?>">
			<div class="entry-content">
				<?php $youtube_vid_id = get_post_meta(get_the_ID(),'chrbar_youtube_vid_id',true); ?>
				<?php if ( ! empty($youtube_vid_id)) { ?>
				<div class="video-container">
					<iframe width="960" height="540" src="http://www.youtube.com/embed/<?php echo $youtube_vid_id; ?>?rel=0&modestbranding=1&wmode=transparent" frameborder="0" allowfullscreen></iframe>
				</div>
				<?php } ?>
				<div class="content-body">
					<div class="title-section">
						<h1 class="entry-title"><?php echo get_post_meta(get_the_ID(),'chrbar_advent_number',true); ?> - <?php the_title(); ?></h1>
					</div>
					<div class="content-section">
						<?php the_content(); ?>
					</div>
				</div>
				<div class="ad-space">
					<?php $ad336 = get_posts(array('post_type'=>'module','module_category_name' => 'ad336')); ?>
					<?php foreach($ad336 as $key=>$adpost) { ?>
						<?php echo customAd($adpost,'full') ; ?>
					<?php } ?>
				</div>
			</div>
		</article>

	<?php endwhile; endif; ?>
		<div class="scroll-nav-container SCROLL_CONTAINER">
			<div class="scroll-nav SCROLL_NAV">
				<ul class="vid-nav">
					<?php $adventPosts = get_posts(array('category_name' => 'advent','posts_per_page'=>-1)); 
					$numAdventPosts = count($adventPosts);
					$iAdventPost = 0;
					foreach($adventPosts as $key=>$post) { ?>
					<li<?php echo (++$iAdventPost === $numAdventPosts) ? ' class="last"':'' ?>>
						<?php echo customPostVideoThumbnail(get_the_ID(),'vid-thumb'); ?>
					</li>
					<?php } ?>
				</ul>
			</div>
			<a class="scroll-prev scroll-control SCROLL_CONTROL SCROLL_PREV" href="#">Previous</a>
			<a class="scroll-next scroll-control SCROLL_CONTROL SCROLL_NEXT" href="#">Next</a>
		</div>

<?php post_navigation(); ?>
	
<?php get_sidebar(); ?>

<?php get_footer(); ?>