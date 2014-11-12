<?php
/*
 *  Uploads Featured image 
 */
if (!function_exists('wp_votes_create_or_set_featured_image')) {
    function wp_votes_create_or_set_featured_image($url, $post_id) {
        $file_name = basename($url);
        $upload = wp_upload_bits($file_name, null, file_get_contents($url));
        $wp_filetype = wp_check_filetype(basename($url), null);
        $wp_upload_dir = wp_upload_dir();
        $attachment = array(
            'guid' => _wp_relative_upload_path($upload['url']),
            'post_mime_type' => $wp_filetype['type'],
            'post_title' => preg_replace('/\.[^.]+$/', '', $file_name),
            'post_content' => '',
            'post_status' => 'inherit'
        );
        $attach_id = wp_insert_attachment($attachment, false, $post_id);
	update_post_meta($post_id, '_thumbnail_id', $attach_id);
        $wp_attached_file = substr($wp_upload_dir['subdir'], 1) . '/' . $file_name;
        update_post_meta($attach_id, '_wp_attached_file', $wp_attached_file);
        $image_150 = votes_csv_resize($attach_id, '', 150, 150, true);
        $image_300 = votes_csv_resize($attach_id, '', 300, 0);
        $ds_image_ico = votes_csv_resize($attach_id, '', 80, 80, true);
        $ds_image_medium = votes_csv_resize($attach_id, '', 800, 0);
        $file_path = get_attached_file($attach_id);
        $orig_size = getimagesize($file_path);
        $wp_attachment_array = array(
            'width' => $orig_size[0],
            'height' => $orig_size[1],
            'hwstring_small' => "height='96' width='96'",
            'file' => $wp_attached_file,
            'sizes' => Array
                (
                'thumbnail' => Array
                    (
                    'file' => basename($image_150['url']),
                    'width' => $image_150['width'],
                    'height' => $image_150['height']
                ),
                'medium' => Array
                    (
                    'file' => basename($image_300['url']),
                    'width' => $image_300['width'],
                    'height' => $image_300['height']
                ),
                'post-thumbnail' => Array
                    (
                    'file' => basename($image_300['url']),
                    'width' => $image_300['width'],
                    'height' => $image_300['height']
                )
            ),
            'image_meta' => Array
                (
                'aperture' => 0,
                'credit' => '',
                'camera' => '',
                'caption' => '',
                'created_timestamp' => 0,
                'copyright' => '',
                'focal_length' => 0,
                'iso' => 0,
                'shutter_speed' => 0,
                'title' => ''
            )
        );
        update_post_meta($attach_id, '_wp_attachment_metadata', $wp_attachment_array);

        if ($attach_id) {
            return true;
        } else {
            return false;
        }
    }

}

if (!function_exists('votes_csv_import')) {

    function votes_csv_import() {
	global $wpdb;  
        if (isset($_POST['contest_csv_submit'])) {
            set_time_limit(0);
            $inserted = array();
            $csv_termid = $_POST['contest_csv_term'];
            if (isset($csv_termid) && $csv_termid > 0) {
                $csv_term = get_term($csv_termid, VOTES_TAXONOMY);

                $tempFile = $_FILES['contest_csv_file']['tmp_name'];
                $targetPath = VOTES_ABSPATH . "includes/uploads/";
                $sourceCSV = $_FILES['contest_csv_file']['name'];
                $get_source_file_ext = end(explode('.', $sourceCSV));
                $targetFile = str_replace('//', '/', $targetPath) . $_FILES['contest_csv_file']['name'];
                if ($get_source_file_ext == 'csv') {
                    if ($_FILES['contest_csv_file']['error'] == 0 && move_uploaded_file($tempFile, $targetFile)) {
                        $recipeData = array();
                        $row = 2;
                        $rec_cnt = 2;

                        $row = 1;
                        if (($handle = fopen($targetFile, "r")) !== FALSE) {
                            while (($data = fgetcsv($handle, 1000000, ",")) !== FALSE) {
                                $image_url = '';
                                $num = count($data);
                                if ($row != 1) {
                                    if (trim($data[0]) != '') {
                                        $attr = array('post_title' => $data[0],
                                            'post_content' => wpautop(convert_chars(($data[1]))),
                                            'post_type' => VOTES_TYPE,
                                            'post_status' => 'publish');

                                        if (!empty($data[2])) {
                                            $image_url = trim($data[2]);
                                        }
                                        $cur_id = wp_insert_post($attr);
					
                    update_post_meta($cur_id, VOTES_CUSTOMFIELD, 0);					
			
					$sql = "SELECT * FROM " . VOTES_ENTRY_CUSTOM_TABLE . " WHERE delete_time = 0 AND system_name != 'contestant-desc' order by sequence";
					$questions = $wpdb->get_results($sql);
					if(!empty($questions)){
					    $posted_val=array();
					    $i=3;
					    foreach($questions as $custom_fields){
					       $posted_val[$custom_fields->system_name]=$data[$i];
					       $i++;
					    }
					}
					$val_serialized = serialize($posted_val);                   
					$wpdb->query("INSERT INTO " . VOTES_POST_ENTRY_TABLE . " (post_id_map,field_values)". " VALUES ('".$cur_id."', '".$val_serialized. "')");
				    
					       update_post_meta($cur_id, VOTES_EXPIRATIONFIELD, '0');
                                        wp_set_object_terms($cur_id, $csv_term->slug, VOTES_TAXONOMY);
                                        if ($image_url != '') {
                                            wp_votes_create_or_set_featured_image($image_url, $cur_id);
                                        }
                                        $inserted[] = $cur_id;
                                    }
                                }
                                $row++;
                            }
                            $cls = "updated";
                            $msg = count($inserted) . " Contestants Uploaded";
                            fclose($handle);
                            @unlink($targetFile);
                        }
                    } else {
                        $cls = "error";
                        $msg = __('Error in Uploading','voting-contest');
                    }
                } else {
                    $cls = "error";
                    $msg = __('Invalid File format','voting-contest');
                }
            } else {
                $cls = "error";
                $msg = __('Invalid category.','voting-contest');
            }
            echo '<div style="line-height:40px;" class="' . $cls . '">' . $msg . '</div>';
        }
        ?>
        <div class="wrap">
        <?php echo html('h2', __('Import Contestants','voting-contest')); ?>
            <div class="narrow">
                <p><?php _e('Steps to Import Contestants','voting-contest'); ?></p>
                <ul style="list-style: disc inside none;">
                    <li> <?php _e('Prepare your Contestants CSV file in the format given below.','voting-contest'); ?></li>

                    <li><?php _e('Choose Category.','voting-contest');
		    echo sprintf('(&nbsp;'.__("Note: New Category can be created in this page","voting-contest").' <a href="%1$s"> '.__("Add Category","voting-contest").' </a> )', 'edit-tags.php?taxonomy=' . VOTES_TAXONOMY . '&post_type=' . VOTES_TYPE); ?> </li>
                    <li><?php _e('Upload the CSV file using the form and click upload','voting-contest'); ?></li>

                </ul>
                <style>
                    .sampledata{margin:10px;padding:20px;width:100%;border:solid 2px #000;}
                    .titledata{font-weight:bold;}

                </style>
                <div class="sampledata">
                    <div class="titledata"><?php _e('Sample file Data','voting-contest'); ?></div>
                    <div class="titledata" style="height:25px;"></div>
                    <div class="titledata"><?php _e('"contest_title","contest_content","featured_image_url"'); ?></div>
                    <div class="rowdata"><?php _e('"pagetitle1","pagecontent","http://i0.kym-cdn.com/entries/icons/original/000/007/263/photo_cat2.jpg"'); ?></div>
                    <div class="rowdata"><?php _e('"pagetitle2","pagecontent",""'); ?></div>
                    <div class="rowdata"><?php _e('.'); ?></div>
                    <div class="rowdata"><?php _e('.'); ?></div>
                    <div class="requireddata description"><p style="font-weight: bold;"><?php _e('<u>Required Fields</u>: contest_title','voting-contest'); ?></p>
                        <p><?php _e('<b>Note:</b> column values should be seperated by comma.','voting-contest'); ?><br/> 
			<?php _e('First line of the CSV file should be <b>"contest_title","contest_content","featured_image_url"'); ?></b><br/>
			<?php _e('By Default <b>votes</b> will be 0','voting-contest'); ?><br/>
			<?php _e('By Default <b>Expiration Date</b> will be 0','voting-contest'); ?>
                        </p>
                    </div>
                </div>

                <form method="post" enctype="multipart/form-data" name="contest_csv_form" id="contest_csv_form">
                    <p>
                        <label for="contest_csv_term"><?php _e('Choose a Category for the Contestants','voting-contest'); ?></label>
		    <?php
		    wp_dropdown_categories(array('hide_empty' => false,
			'name' => 'contest_csv_term',
			'id' => 'contest_csv_term',
			'hierarchical' => 1,
			'show_count' => 1,
			'taxonomy' => VOTES_TAXONOMY,
			'show_option_none' => __('Select the Category','voting-contest')));
		    ?>
                    </p>
                    <p>
                        <label for="contest_csv_file"><?php _e('Choose a file from your computer','voting-contest'); ?></label>
                        <input name="contest_csv_file" id="contest_csv_file" type="file" />
                    </p>

                    <p class="submit"><input type="submit" value="<?php _e('Upload file and import','voting-contest'); ?>" class="button" id="contest_csv_submit" name="contest_csv_submit"></p>
                </form>
            </div>
        </div>
        <?php
    }

}

if(!function_exists('votes_csv_export')){
    function votes_csv_export(){
    ?>      
    <div class="wrap">
        <h2><?php _e('Export Contestant Details','voting-contest'); ?></h2>
        <div class="narrow">
            <form action="admin.php" method="get" name="votes_export_form" id="votes_export_form">
                <p> <?php _e('Please select the contest you want to export.','voting-contest'); ?></p>
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
                <input type="hidden" name="votes_export" id="votes_export" value="Export" />
    
                <p class="submit"><input type="submit" value="<?php _e('Export','voting-contest'); ?>" class="button" id="votes_export_button" name="votes_export_button" /></p>
            </form>
	    <h5><?php _e('Please note: In order to properly export, commas will be stripped from the export','voting-contest'); ?></h5>
        </div>
    </div>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
	jQuery('#votes_export_form').submit(function(){
	    var dropval = jQuery('#vote_contest_term').val();
	    if(dropval=='-1'){
		jQuery('.error_category').hide();
		jQuery( "<p style='color:red;margin-left:230px;' class='error_category'><?php _e('Select the category to export','voting-contest'); ?></p>" ).insertAfter( ".form-table" );
		return false;
	    }
	    jQuery('.error_category').hide();
	    return true;
	});

    });
    </script>
<?php
    
    }
}