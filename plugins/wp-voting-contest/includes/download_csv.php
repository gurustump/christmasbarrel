<?php
if (!class_exists('DownloadvoteCSV')) {
  class DownloadvoteCSV {
    public static function on_load() { 
      add_action('plugins_loaded',array(__CLASS__,'plugins_loaded'));
    }

    public static function plugins_loaded() {
      global $pagenow,$wpdb;
	if ($pagenow=='admin.php' && isset($_GET['votes_export'])  && $_GET['votes_export']=='Export') {
	  $term_id = $_GET['vote_contest_term'];
	  
	  $where_con ='';
	  if($term_id!='' && $term_id > 0){
	      $where_con .= ' AND tt.term_id='.$term_id;
	  }
	  
	  $sql1 = "SELECT * FROM ".$wpdb->prefix."posts "." as pos 
		 LEFT JOIN ".$wpdb->prefix."term_relationships as relterm ON (pos.ID=relterm.object_id)
		 LEFT JOIN ".$wpdb->prefix."term_taxonomy as tt ON (relterm.term_taxonomy_id = tt.term_taxonomy_id)
		 LEFT JOIN ".VOTES_POST_ENTRY_TABLE." as votepost ON (pos.ID=votepost.post_id_map)
		 WHERE pos.post_type = 'contestants' AND pos.post_status!='auto-draft' AND pos.post_status!='trash' ".$where_con.                " Group by pos.ID";
		 
	  $post_entries = $wpdb->get_results($sql1);
		  
	  $file_name = "contest_".date('d-m-Y-H-i-s').'.csv';
	  header("Content-type: application/csv");
	  header("Content-Disposition: attachment; filename=".$file_name);
	  header("Pragma: no-cache");
	  header("Expires: 0");
 
	  //header
	  $header = "Contestant Title,Status,Contest Category,Votes,Created Date,"; 
           
	  $sql = "SELECT * FROM " .VOTES_ENTRY_CUSTOM_TABLE." WHERE delete_time = 0 order by sequence";
	  $question = $wpdb->get_results($sql);
	  if(!empty($question)){
	      foreach($question as $ques){
		  $header .= $ques->question.',';
	      }
	  }
        
	  $header .="\r\n";
        
	  echo $header;
        
	  if(!empty($post_entries)){
            foreach($post_entries as $pos_val){
	      $posted_date = date('Y-m-d',strtotime($pos_val->post_date));
	      $user_author = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."users where ID='".$pos_val->post_author."'");
	      $category = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."terms where term_id='".$pos_val->term_taxonomy_id."'");
	      $votes_count = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."postmeta where post_id ='".$pos_val->ID."' AND meta_key ='".VOTES_CUSTOMFIELD."' ");
	      
	      $post_title = str_replace(',', '', $pos_val->post_title);
	      $post_content = str_replace(',', '', $pos_val->post_content);
	      $post_status = str_replace(',', '', $pos_val->post_status);
	      $cat_name = str_replace(',', '', $category->name);
	      $votes_count_val = str_replace(',', '', $votes_count->meta_value);
	      
	      $values = "\"$post_title\",\"$post_status\",\"$cat_name\",\"$votes_count_val\",\"$posted_date\",";
			      
	      $custom_values = unserialize($pos_val->field_values);
	      if(!empty($custom_values)){
		  foreach ($question as $ques_val){
		      if(is_array($custom_values[$ques_val->system_name])){
			
			if($ques_val->system_name!="contestant-desc")
			  $values .= implode(' - ',str_replace(',', '',$custom_values[$ques_val->system_name])).',';
			else
			   $values .= implode(' - ',str_replace(',', '',$post_content)).',';
			   
		      }else{
			
			if($ques_val->system_name!="contestant-desc")
			  $values .=str_replace(',', '',$custom_values[$ques_val->system_name]).',';
			else
			$values .=str_replace(',', '',$post_content).',';
		
		      }  
		  } 
	      }
	      $values .= "\r\n";
	      echo $values;
            }
	  }
	  exit();
	}
    }
  }
  DownloadvoteCSV::on_load();
}