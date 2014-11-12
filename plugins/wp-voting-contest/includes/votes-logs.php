<?php
/**
 *  Displays Voting Logs in the admin end
 */  

if(!function_exists('votes_votinglogs')){
    function votes_votinglogs(){
	global $wpdb;
    
    wp_register_style('votesadmin', VOTES_PATH . 'css/admin-styles.css');
	wp_enqueue_style('votesadmin');      
    
    $orderby = ($_GET['orderby'] == null)?'log.date':$_GET['orderby'];   
    $order = ($_GET['order'] == null)?'desc':$_GET['order'];   
    
	// get counts
	$sql = "SELECT id FROM " . VOTES_TBL ;
    $total   =  $wpdb->get_results($sql); 
	
    $records_per_page = $_GET['logs_per_page'];
    
    if(empty($_GET['paged'])) {
	     $paged = 1;
    } 
    else 
    {
	     $paged = ((int) $_GET['paged']);
	}
    if ( isset( $records_per_page ) && $records_per_page )
	     $rpp = $records_per_page;
    else
    	 $rpp = 10;        
                
    $startat = ($paged - 1) * $rpp;             
                            
    $trans_navigation = paginate_links( array(
		'base' => add_query_arg( 'paged', '%#%' ),    
		'format' => '',    
		'total' => ceil(count($total) / $rpp),    
		'current' => $paged,        
	));
    
    if($_GET['action'] == "delete" ):    
        foreach($_GET['checkbox'] as $key => $check):
            $wpdb->delete( VOTES_TBL, array( 'id' => $key ), array( '%d' )  );
            
            $vote_count = get_post_meta( $check, VOTES_CUSTOMFIELD, true );
            if($vote_count != 0)
                update_post_meta($check, VOTES_CUSTOMFIELD, $vote_count - 1, $vote_count);
            $redirect_link = admin_url().'admin.php?page=votinglogs&delete_success=2';
            ?>
                <script type="text/javascript">
                    window.location.href = '<?php echo $redirect_link; ?>';
                </script>
            <?php 
        endforeach;
    endif;
    
    if(isset($_GET['delete_tbl_id']) && isset($_GET['delete_vote_id']))
    {
        $wpdb->delete( VOTES_TBL, array( 'id' => $_GET['delete_tbl_id'] ), array( '%d' )  );
        
        $vote_count = get_post_meta( $_GET['delete_vote_id'], VOTES_CUSTOMFIELD, true );
        if($vote_count != 0)
            update_post_meta($_GET['delete_vote_id'], VOTES_CUSTOMFIELD, $vote_count - 1, $vote_count);
        $redirect_link = admin_url().'admin.php?page=votinglogs&delete_success=1';
        ?>
            <script type="text/javascript">
                window.location.href = '<?php echo $redirect_link; ?>';
            </script>
        <?php        
    }
    if(isset($_GET['delete_success']))
    {
        ?>
         <div id="message" class="updated fade">
			<p><strong>
                <?php if($_GET['delete_success'] == 1): ?>
					<?php _e('Vote Entry deleted successfully','voting-contest'); ?>
                <?php else: ?>
                    <?php _e('Vote Entries deleted successfully','voting-contest'); ?>
                <?php endif; ?>
			    </strong>
			</p>
	    </div>
        <?php
    }
    
          
                
	?>
    
	<div class="wrap">
	 <h2><?php echo _e('Voting Logs','voting-contest'); ?></h2><br />
	    
     <?php ob_start();?>
     <?php 
            $log_no = (isset($rpp))?'&logs_per_page='.$rpp:'';            
            $actual_link = admin_url().'admin.php?page=votinglogs'.$log_no; 
            $yet_to_order = ($order == 'asc')?'desc':'asc';
     ?>
     
     <form id="form_voting_logs" name="form_voting_logs" method="GET" action="<?php echo admin_url().'admin.php'; ?>">
        <div class="tablenav top">
    		<div class="alignleft actions bulkactions">
    			<select name="action">
                <option selected="selected" value="-1">Bulk Actions</option>            	
                	<option value="delete">Delete</option>
                </select>
                <input type="submit" value="Apply" class="button action" id="doaction" name="">
     		</div>
    		<br class="clear" />
    	</div>
    
	    <table id="table" class="widefat manage-questions">
		<thead>
		    <tr>
		      
            <th class="manage-column" id="cb" scope="col" align="center" style="width:5%;">
                <input type="checkbox" id="vote_delete_log_all" style="vertical-align: top; margin: 7px 0 3px 28px;" />
            </th>  
    
			<th class="manage-column column-title sortable <?php echo $order; ?>" id="values" scope="col" title="Click to Sort" style="width:25%;">				
                <a href="<?php echo $actual_link.'&orderby=pst.post_title&order='.$yet_to_order; ?>">
                    <span><?php _e('Title','voting-contest');  ?></span>
                    <span class="sorting-indicator"></span>
                </a>
			</th>
			<th class="manage-column column-title" id="values" scope="col" title="Click to Sort" style="width:15%;">
				<?php _e('Author','voting-contest'); ?>
			</th>
    
			<th class="manage-column column-title" id="values" scope="col" title="Click to Sort"  style="width:10%;">
				<?php _e('Voter','voting-contest'); ?>
			</th>
			<th class="manage-column column-title sortable <?php echo $order; ?>" id="values" scope="col" title="Click to Sort" style="width:10%;">
                 <a href="<?php echo $actual_link.'&orderby=log.date&order='.$yet_to_order; ?>">
                    <span><?php _e('Vote Date','voting-contest'); ?></span>
                    <span class="sorting-indicator"></span>
                </a>          
				
			</th>
            <th class="manage-column column-title" id="values" scope="col" title="Click to Sort"  style="width:10%;">
				<?php _e('Delete Vote','voting-contest'); ?>
			</th>
    
		    </tr>
		</thead>
        
		<tbody>
			<?php
            $browser_array = array (
                            	'IE' => 'Internet Explorer',
                            	'MF' => 'Mozilla Firefox',
                            	'GC' => 'Google Chrome',
                            	'AS' => 'Apple Safari',
                            	'O'	 => 'Opera',
                            	'N'	 => 'Netscape'
                            );
            if($rpp != 'all')
			    $sql = "SELECT log.*,pst.post_title,pst.post_author FROM " . VOTES_TBL ." as log LEFT JOIN ".$wpdb->prefix."posts as pst on log.post_id=pst.ID ORDER BY ".$orderby." ".$order." LIMIT {$startat}, {$rpp} ";
            else
                $sql = "SELECT log.*,pst.post_title FROM " . VOTES_TBL ." as log LEFT JOIN ".$wpdb->prefix."posts as pst on log.post_id=pst.ID ORDER BY ".$orderby." ".$order;
		
			if ( is_super_admin() ) {
			    $voting_logs = $wpdb->get_results($sql);
			    if ($wpdb->num_rows > 0) {
			    
                    $i = 0; 
				    foreach ($voting_logs as $logs) {
				    $tbl_id        = $logs->id;
					$vote_id       = $logs->post_id;                    
                    $vote_catid    = $logs->termid;                            
                    $vote_author_id= $logs->post_author;
                    $vote_author   = ucfirst(get_the_author_meta( 'display_name', $vote_author_id ));
                    
					$voter_name    = $logs->ip;
                    if(filter_var($voter_name, FILTER_VALIDATE_IP) !== false)
                        $voter_name = $logs->ip;
                    else if(count(explode('@',$voter_name)) > 1){
                        $browser = explode('@',$voter_name);
                        $voter_name = $browser_array[$browser[0]];
                    } 
                    else
                        $voter_name = ucfirst(get_the_author_meta( 'display_name', $voter_name ));
                    
                    $vote_count    = $logs->votes;
                    $voted_date    = $logs->date;
                    
                    $tr_class = ($i%2 == 1)?'':'alternate';         
                    $i++;           
									
					?>
					<tr id="<?php echo $tbl_id ?>" class="<?php echo $tr_class; ?>">
					    
                        <td align="center">
                        <input name="row_id" type="hidden" value="<?php echo $question_id ?>" />
                         <?php if($system_name != "contestant-desc"): ?>                            
						    <input  style="margin:7px 0 22px 8px; vertical-align:top;" name="checkbox[<?php echo $tbl_id ?>]" value="<?php echo $vote_id; ?>" type="checkbox" class="question_checkbox"  title="Delete <?php echo $logs->post_title; ?>" />
                         <?php endif; ?>
					    </td>
                        					    
					    <td class="post-title page-title column-title"><strong><a href="post.php?post=<?php echo $vote_id ?>&action=edit"><?php echo $logs->post_title; ?></a></strong>
					
					    </td>
					    <td class="author column-author"><?php echo $vote_author ?></td>
					    
					    <td class="author column-author"><?php echo $voter_name ?></td>
					    <td class="author column-author"><?php echo $voted_date ?></td>
                        <td class="author column-author"><button class="delete_vote" id="<?php echo $tbl_id ?>" name="<?php echo $vote_id; ?>"><?php _e('Delete Vote','voting-contest'); ?></button></td>
					    
					   
					</tr>
				    <?php
				    }
			    }
                else{
                    ?>
                        <tr>
                            <td colspan="5"><?php _e('No Vote Entries Found','voting-contest'); ?></td>
                        </tr>
                    <?php
                }
		    }
		    ?>
		</tbody>
        <input type="hidden" name="page" value="votinglogs" />
        
       
        <tfoot>
            <tr>
                <td></td>
                <td>
                    <?php $logs_per_page = array('10' => '10','20' => '20', '25' => '25', '50' => '50', 'all' =>'All'); ?>          
                    <label><?php _e('Logs Per Page','voting-contest'); ?></label>
                    <select name="logs_per_page" id="logs_per_page">
                        <?php foreach($logs_per_page as $key => $logs): ?>
                            <?php $selected = ($key == $_GET['logs_per_page'])?'selected':''; ?>
                            <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $logs; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        </tfoot>
        
	    </table>
        
        
         <?php
         if ($trans_navigation) 
         {   
    		echo '<div class="tablenav top">';    
    		echo "<div class='tablenav-pages'><span class='pagination-links'>$trans_navigation</span></div>";               echo '</div>';    
   	     }  
         ?>
         <?php if(isset($_GET['paged'])): ?>
             <input type="hidden" value="<?php echo $_GET['paged']; ?>" name="paged" id="paged" />
         <?php endif; ?>
	    <div>
		
	    </div>
	</form>
       
	    <?php
	    $main_post_content = ob_get_clean();
	    echo $main_post_content;
	    ?>							
    
	</div>
    <script type="text/javascript">
        jQuery(document).ready(function () {
            jQuery('#logs_per_page').change(function (){
                jQuery('#form_voting_logs').submit();
            });
            
            jQuery('#vote_delete_log_all').click(function(event) {
             if(this.checked) {
                  // Iterate each checkbox
                  jQuery(':checkbox').each(function() {
                      this.checked = true;
                  });
              }
              else {
                jQuery(':checkbox').each(function() {
                      this.checked = false;
                  });
              }
            });
            
            jQuery('.delete_vote').click(function (e){
                e.preventDefault();
                if (confirm('<?php _e("Are you sure want to delete?","voting-contest"); ?>')){
                    var tbl_id = this.id;     
                    var vote_id = this.name;                  
                    jQuery('#form_voting_logs').append("<input type='hidden' name='delete_tbl_id' value="+tbl_id+" ><input type='hidden' name='delete_vote_id' value="+vote_id+" >");
                    jQuery('#form_voting_logs').submit();
			    
                }else{
                     return true;
                }
               
            });
        });
    </script>
	    
	 <?php 
    } 
}