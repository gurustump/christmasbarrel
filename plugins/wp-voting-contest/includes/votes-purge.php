<?php
$votes_delete = mysql_real_escape_string($_POST['votes_delete']);
if ($votes_delete=='Delete') {    
    $term_id = mysql_real_escape_string($_POST['vote_contest_term']);
    $cls = 'updated';
    $msg = '';    
          
    $where = '';
    global $wpdb;
    
    if(isset($term_id) && $term_id > 0){
	   $where .= ' WHERE  `post_id` IN (SELECT DISTINCT `object_id` FROM `'.$wpdb->prefix.'term_relationships` INNER JOIN `'.$wpdb->prefix.'terms` as terms ON `term_id` = '.$term_id.' AND `term_taxonomy_id` = terms.term_id )';
	    
    }
    
	$cnt = 'SELECT  `post_id` , count(*) as cnt FROM ' . VOTES_TBL .$where.' GROUP BY `post_id`';
    $delquery = 'DELETE FROM ' . VOTES_TBL .$where;
   
    //echo $delquery;
    $cntresult = $wpdb->get_results($cnt);
    $result = $wpdb->query($delquery);

   foreach ($cntresult as $indpost) {
       $exvote = get_post_meta($indpost->post_id, VOTES_CUSTOMFIELD);
       if ($exvote[0] > $indpost->cnt)
            update_post_meta($indpost->post_id, VOTES_CUSTOMFIELD, ($exvote[0] - $indpost->cnt));
        else
            update_post_meta($indpost->post_id, VOTES_CUSTOMFIELD, 0);
    }
    if (count($cntresult) > 0)
        $msg = $result . __("Vote Entries Deleted",'voting-contest');
    else
        $msg = __("No Votes are polled",'voting-contest');
        
    
}
if (isset($msg)) {
    echo '<div class="' . $cls . '" style="line-height:40px;">' . $msg . '</div>';
}
?>

<div class="wrap">   

    <h2><?php _e('Clear Voting Entries','voting-contest'); ?></h2>
    <div class="narrow">
        <form action="<?php echo admin_url().'admin.php?page=votes_purge'; ?>" method="post" name="votes_delete_form" id="votes_delete_form">
            <p> <?php _e('Select the Contestants Category to Delete the Vote.','voting-contest'); ?></p>
            <p style="color: #ff0000;"> <?php _e('Note : If you do not select a Contestant Category, all votes will be deleted from all Contest Categories.','voting-contest'); ?></p>
            <table class="form-table"> 
                
                <tr valign="top">
                    <th scope="row">  <?php _e('Select the Contest','voting-contest'); ?>  </th>
                    <td>   
                        <?php
                        wp_dropdown_categories(array('hide_empty' => true,
                            'name' => 'vote_contest_term',
                            'id' => 'vote_contest_term',
                            'hierarchical' => 1,
                            'show_count' => 1,
                            'taxonomy' => VOTES_TAXONOMY,
                            'show_option_none' => __('Select the Category','voting-contest')));
                        ?>
                    </td>
                </tr>
            </table>
            <p class="submit">
            <input type="hidden" id="votes_delete" name="votes_delete" value="<?php _e('Delete','voting-contest'); ?>" />
            <input type="submit" value="<?php _e('Delete','voting-contest'); ?>" class="button" id="votes_delete_btn" name="votes_delete_btn" /></p>
        </form>
    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function () {                        
        jQuery('#votes_delete_btn').click(function (e){
            e.preventDefault();
            if (confirm('<?php _e("Are you sure want to delete?","voting-contest"); ?>')){                      
                jQuery('#votes_delete_form').submit();    	    
            }else{
                 return true;
            }
           
        });
    });
</script>
