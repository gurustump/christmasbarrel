<?php
if ( current_user_can('edit_posts') ) {
    global $wpdb;
	if (isset($_REQUEST['move_contest_submit'])) {
	       
	    $posts = $_POST['selected_post'];        
	    $old_cat = absint($_POST['existing_contest_term']);
	    $new_cat = ($_POST['mapped_contest_term'] == -1) ? -1 : absint($_POST['mapped_contest_term']);
	    if(count($posts)){
		foreach ($posts as $post) {
		    $current_cats = wp_get_object_terms($post, VOTES_TAXONOMY,array('fields' => 'ids'));
		    $current_cats = array_diff($current_cats, array($old_cat));
		    if ($new_cat != -1) {
			    $current_cats[] = $new_cat;
		    }
    
		    if (count($current_cats) <= 0) {
			    $cls = 'Error';
			    $msg = 'Invalid Category';
		    } else {
			    $current_cats = array_values($current_cats);
			    $term = get_term($new_cat, VOTES_TAXONOMY);
			    //wp_set_post_terms( $post, $term->term_id, VOTES_TAXONOMY);                
			    wp_set_post_terms( $post, $current_cats, VOTES_TAXONOMY);
			    $cls = 'updated';
			    $msg = count($posts).' '.__('Contestants Successfully Moved','voting-contest');
                                
                $wpdb->update( 
                	VOTES_TBL, 
                	array( 
                		'termid' => $current_cats[0]
                	), 
                	array( 'post_id' => $post ), 
                	array( 
                		'%d'                		
                	), 
                	array( '%d' ) 
                );
                
		    }
		}
		echo '<div style="line-height:40px;" class="' . $cls . '">' . $msg . '</div>';
	    }
	}
?>
<div class="wrap">
    <input type="hidden" name="required_missing_text" id="required_missing_text" value="<?php _e('Required Field Missing','voting-contest')?>" />
    <?php echo html('h2', __('Move Contestants','voting-contest') ); ?>
    <div class="narrow">
        <form method="post" enctype="multipart/form-data" name="move_contest_form" id="move_contest_form">
            <table class="form-table"> 
                <tr valign="top">
                    <th scope="row"><label for="existing_contest_term"><?php _e('Move Contestants from this Category: ','voting-contest'); ?></label></th>
                    <td>
			<?php
			    wp_dropdown_categories(array('hide_empty' => true,
			    'name' => 'existing_contest_term',
			    'id' => 'existing_contest_term',
			    'hierarchical' => 1,
			    'show_count' => 1,
			    'taxonomy' => VOTES_TAXONOMY,
			    'show_option_none' => __('Select the Category','voting-contest')));
			?>
		    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><label for="selected_term_post"><?php _e('List of Contestants in Category','voting-contest'); ?></label></th>
                    <td>
                        <div id="selected_term_post_listing" style="max-height: 200px; overflow: auto; padding-top: 10px;">
                            <?php _e('Select the Category to get Contestants','voting-contest'); ?>
                            <input type="hidden" name="selected_post[]" class="selected-post" value="-1" />
                        </div>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="mapped_contest_term"><?php _e('To Category ','voting-contest'); ?></label></th>
                    <td>
			<?php
			    wp_dropdown_categories(array('hide_empty' => false,
                            'name' => 'mapped_contest_term',
                            'id' => 'mapped_contest_term',
                            'hierarchical' => 1,
                            'show_count' => 1,
                            'taxonomy' => VOTES_TAXONOMY,
                            'show_option_none' => __('Select the Category','voting-contest')));
			?>
		    </td>
                </tr>
            </table>
            <p class="submit"><input type="submit" value="<?php _e('Move Contestants','voting-contest'); ?>" class="button" id="move_contest_submit" name="move_contest_submit" /></p>
        </form>
    </div>
</div>
<?php
}
else{
    echo "<h2>You do not have sufficient permissions to do move post.</h2>";
}
?>
