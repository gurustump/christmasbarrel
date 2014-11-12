<?php
if(!function_exists('vote_contestant_custom_field_delete')){
    function vote_contestant_custom_field_delete(){
    	global $wpdb;              

        if($_REQUEST['action']=='delete_fields' && !empty($_POST['checkbox'])){
            
    		if (is_array($_POST['checkbox'])){
    		  while(list($key,$value)=each($_POST['checkbox'])){
    		      $del_id=$key;          
                  $go_delete = true;                                 
                  if ( $go_delete ) {
                    //Delete question data
                    $delete_val = strtotime("now");   
                    $sql = "UPDATE " . VOTES_ENTRY_CUSTOM_TABLE . " SET delete_time = '" . $delete_val . "' WHERE id = '" . $del_id . "' AND system_name != 'contestant-desc' ";
                    $wpdb->query($sql);
                  
 			   }
    	   }
        }
       }
        
        //Single Delete code
    	if(!empty($_REQUEST['field_id']) && $_REQUEST['action']== 'delete_fields'){
            $go_delete = false;
            $sql = " SELECT * FROM " . VOTES_ENTRY_CUSTOM_TABLE ." WHERE id = '" . $_REQUEST['field_id']."' AND system_name != 'contestant-desc'";
            $rs = $wpdb->get_results( $sql );
            if( is_array( $rs ) && count( $rs ) > 0 ) {
                $go_delete = true;
            } 
            if ( $go_delete ) {
                //Delete question group data
                $delete_val = strtotime("now");
               	$wpdb->query("UPDATE " . VOTES_ENTRY_CUSTOM_TABLE . " SET delete_time = '" . $delete_val . "' WHERE id = '" . $_REQUEST['field_id'] . "'");
                //$sql = "DELETE FROM " . VOTES_ENTRY_CUSTOM_TABLE . " WHERE id='" . $_REQUEST['field_id'] . "'";
                //$wpdb->query($sql);
            }
    	}
        
    	?>
        <?php if($go_delete){ ?>
    	<div id="message" class="updated fade">
    	  <p><strong>
    		<?php _e('Contenstant Fields have been successfully deleted.','voting-contest');?>
    		</strong></p>
    	</div>
    	<?php
        }
    }
}


if(!function_exists('vote_contestant_registration_field_delete')){
    function vote_contestant_registration_field_delete(){
    	global $wpdb;              
    	
        if($_REQUEST['action']=='delete_fields' && !empty($_POST['checkbox'])){
    		if (is_array($_POST['checkbox'])){
    		  while(list($key,$value)=each($_POST['checkbox'])){
    		      $del_id=$key;          
                  $go_delete = true;                                 
                  if ( $go_delete ) {
                    //Delete question data
                $delete_val = strtotime("now");
               	$wpdb->query("UPDATE " . VOTES_USER_CUSTOM_TABLE . " SET delete_time = '" . $delete_val . "' WHERE id = '" . $del_id . "'");
                  }
 			   }
    	   }
        }
        
        //Single Delete code
    	if(!empty($_REQUEST['field_id']) && $_REQUEST['action']== 'delete_fields'){
            $go_delete = false;
            $sql = " SELECT * FROM " . VOTES_USER_CUSTOM_TABLE ." WHERE id = '" . $_REQUEST['field_id']."'";
            $rs = $wpdb->get_results( $sql );
            if( is_array( $rs ) && count( $rs ) > 0 ) {
                $go_delete = true;
            } 
            if ( $go_delete ) {
                //Delete question group data
                $delete_val = strtotime("now");
                $wpdb->query("UPDATE " . VOTES_USER_CUSTOM_TABLE . " SET delete_time = '" . $delete_val . "' WHERE id = '" . $_REQUEST['field_id'] . "'");
                //$sql = "DELETE FROM " . VOTES_USER_CUSTOM_TABLE . " WHERE id='" . $_REQUEST['field_id'] . "'";
                //$wpdb->query($sql);
            }
    	}
        
    	?>
        <?php if($go_delete){ ?>
    	<div id="message" class="updated fade">
    	  <p><strong>
    		<?php _e('Registration Fields have been successfully deleted.','voting-contest');?>
    		</strong></p>
    	</div>
    	<?php
        }
    }
}