
<?php global $post; ?>

<ul class="vid-nav">
	<?php $adventPosts = get_posts(array('category_name' => 'advent','posts_per_page'=>-1)); ?>
	<?php foreach($adventPosts as $post) { ?>
	<li>
		<?php echo customPostVideoThumbnail(get_the_ID(),'vid-thumb'); ?>
	</li>
	<?php } ?>
</ul>
