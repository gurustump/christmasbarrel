<?php
if (!function_exists('votes_category_edit_configuration_form')) {

    function votes_category_edit_configuration_form() {
 
        $votes_expiration = $tax_activationcount = $votes_starttime =  ''; 
		$option = array('imgdisplay' => NULL,
						'termdisplay' => NULL,'middle_custom_navigation'=>NULL,'votecount'=>NULL,'show_description'=>VOTES_SHOW_DESC);		
		if(isset($_REQUEST['tag_ID'])) {
			$curterm = $_REQUEST['tag_ID'];
			$option = get_option($curterm . '_' . VOTES_SETTINGS);
			$expoption  = get_option($curterm . '_' . VOTES_TAXEXPIRATIONFIELD);
			$tax_activationcount = get_option($curterm . '_' . VOTES_TAXACTIVATIONLIMIT);
			$votes_starttime = get_option($curterm . '_' . VOTES_TAXSTARTTIME);
			if(isset($votes_starttime) && $votes_starttime != '0' && $votes_starttime){
				$votes_starttime = date('m-d-Y H:i', strtotime(str_replace('-', '/', $votes_starttime )));
			}
		}
		if(isset($expoption) && $expoption != '0' && $expoption){
			$votes_expiration = date('m-d-Y H:i', strtotime(str_replace('-', '/', $expoption )));
		}
		
        ?>

        <table class="form-table"> 
            
	    <tr valign="top">
                <th scope="row"><label for="imgcontest"><?php _e('Video Contest: ','voting-contest'); ?></label></th>
                <td>
                    <input type="checkbox" name="imgcontest" id="imgcontest" <?php checked('on', $option['imgcontest']); ?>/>
                    <br/><span class="description"> <?php _e('Image Field will not be shown in Front end Add Contestant (Submit Entries).','voting-contest'); ?></span>
                </td>
            </tr>
	    
	    <tr valign="top">
                <th scope="row"><label for="imgdisplay"><?php _e('Display Image: ','voting-contest'); ?></label></th>
                <td>
                    <input type="checkbox" name="imgdisplay" id="imgdisplay" <?php checked('on', $option['imgdisplay']); ?> <?php disabled('on', $option['imgcontest']); ?>/>
                    <br/><span class="description"> <?php _e('Display Featured Image in Contestant Listing.','voting-contest'); ?></span>
                </td>
        </tr>
	    
	    <tr valign="top">
                <th scope="row"><label for="votecount"><?php _e('Hide Vote Count: ','voting-contest'); ?></label></th>
                <td>
                    <input type="checkbox" name="votecount" id="votecount" <?php checked('on', $option['votecount']); ?> />
                    <br/><span class="description"> <?php _e('Hide Vote Count in Contestant Listing and Description Page.','voting-contest'); ?></span>
                </td>
            </tr>
		    
            <tr valign="top">
                <th scope="row"><label for="termdisplay"><?php _e('Display Categories: ','voting-contest'); ?></label></th>
                <td>
                    <input type="checkbox" name="termdisplay" id="termdisplay" <?php checked('on', $option['termdisplay']); ?>/>
                    <br/><span class="description"> <?php _e('Displays Categories in Contestant Listing.','voting-contest');?></span>
                    <input type="hidden" name="votes_category_settings" id="votes_category_settings" value="1"/>
                </td>

            </tr>

            <tr valign="top">
                <th scope="row"><label for="termdisplay"><?php _e('Show Total Vote Count: ','voting-contest'); ?></label></th>
                <td>
                    <input type="checkbox" name="total_vote_count" id="total_vote_count" <?php checked('on', $option['total_vote_count']); ?>/>
                    <br/><span class="description"> <?php _e('Displays Total Vote Count Of Category Below Contest Timer.','voting-contest');?></span>
		 </td>
            </tr>

	    
	    
	    <tr valign="top">
                <th scope="row"><label for="votes_starttime"><?php _e('Select Start Time: ','voting-contest'); ?></label></th>
                <td>
                   <input type="text" readonly="readonly" name="votes_starttime" id="votes_starttime" value="<?php  echo $votes_starttime; ?>" class="datetimepicker" /> <input class="button cleartime clearstarttime" type="button" value="Clear"/>
                    <br/><span class="description"><p><i><?php _e('Default: No Start Time','voting-contest'); ?></i></p></span>
                </td>

            </tr>
		<tr valign="top">
                <th scope="row"><label for="votes_expiration"><?php _e('Select End Time: ','voting-contest'); ?></label></th>
                <td>
                   <input type="text" readonly="readonly" name="votes_expiration" id="votes_expiration" value="<?php  echo $votes_expiration; ?>" class="datetimepicker" /> <input class="button cleartime" type="button" name="no_expiration" id="no_expiration" value="Clear"/>
                    <br/><span class="description"><p><i><?php _e('Default: No Expiration','voting-contest'); ?></i></p></span>
                </td>

        </tr>            
	
	    <tr valign="top">
                <th scope="row"><label for="tax_activationcount"><?php _e('Activation Count: ','voting-contest'); ?></label></th>
                <td>
                   <input type="text" name="tax_activationcount" id="tax_activationcount" value="<?php  echo $tax_activationcount; ?>"/> 
                    <br/><span class="description"><p><?php _e('Number of Contestants to be reached to make it Active.','voting-contest');?></p></span>
                </td>

        </tr>
	
			
	   
            
        <tr valign="top">
                <th scope="row"><label for="middle_custom_navigation"><?php _e('Middle Button on Custom Post Navigation: ','voting-contest'); ?></label></th>
                <td>
                   <input type="text" name="middle_custom_navigation" id="middle_custom_navigation" value="<?php  echo $option['middle_custom_navigation']; ?>"/>
		   <span style="display: none;color:red;" id="erro_valid_url"><?php _e('Enter Valid URL','voting-contest'); ?></span>
                    <br/><span class="description"><p><?php _e('Enter the URL of your main contest page (sets the :: button URL)','voting-contest');?></p></span>
		    
		</td>

            </tr>
            
        <?php 
            $show_desc_option = array(
                                '-1'        => 'Select',
                                'grid' => 'Grid View',
                                'list' => 'List View',
                                'both'      => 'Both'
                                ); 
        ?>
            
         <tr valign="top">
                <th scope="row"><label for="show_description"><?php _e('Show Contestant Description: ','voting-contest'); ?></label></th>
                <td>
                   <select name="show_description" id="show_description">
                        <?php foreach($show_desc_option as $key => $desc): ?>
                            <?php $selected = ($option['show_description'] == $key)?'selected':''; ?>
                            <option value="<?php echo $key; ?>" <?php echo $selected;?>>
                                <?php echo $desc; ?>
                            </option>
                        <?php endforeach; ?>
                   </select>                  
                    <span style="display: none;color:red;" id="erro_valid_url">Enter Valid URL</span>
                    <br/><span class="description"><p><?php _e('Select options to show contestant descriptions in the Corresponding view','voting-contest');?></p></span>
		    
		</td>

            </tr>
            
            <tr valign="top">
                <th scope="row"><label for="vote_contest_entry_person"><?php _e('Entry Limit Per User:','voting-contest'); ?></label></th>
                <td>
                  <input type="text" name="vote_contest_entry_person" id="vote_contest_entry_person" value="<?php  echo $option['vote_contest_entry_person']; ?>"/>
                    <br/><span class="description"> <p><?php _e('Limit the number of entries a single contestant may submit. Leave blank for unlimited entries per contestant.','voting-contest');?></p></span>
		 </td>
            </tr>
		
			
			
        </table>
	<script type="text/javascript">
	jQuery(document).ready(function(){
	    jQuery('#imgcontest').click(function() {
	      if(jQuery(this). is(':checked')){
		jQuery('#imgdisplay').attr('checked',false);
		jQuery('#imgdisplay').attr('disabled',true);	      
	      }else{
		jQuery('#imgdisplay').attr('disabled',false);
	      }
	    });
	    
	  jQuery("#middle_custom_navigation").keydown(function(){
	    if(validateURL(jQuery(this).val())){
		jQuery('#erro_valid_url').hide();
	    }else{
		jQuery('#erro_valid_url').show();
	    }
	  });
	    
	    jQuery('form').submit(function(){
		var middle_navigat =  jQuery('#middle_custom_navigation').val();
		if (middle_navigat!='') {	 
		    if(validateURL(jQuery('#middle_custom_navigation').val())){
			return true;
		    }else{
			return false;
		    }
		}else
		return true;
	    });

	 
	});
	
	function validateURL(textval) {
	    var urlregex = new RegExp(
		  "^(http|https|ftp)\://([a-zA-Z0-9\.\-]+(\:[a-zA-Z0-9\.&amp;%\$\-]+)*@)*((25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9])\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[0-9])|([a-zA-Z0-9\-]+\.)*[a-zA-Z0-9\-]+\.(com|edu|gov|int|mil|net|org|biz|arpa|info|name|pro|aero|coop|museum|[a-zA-Z]{2}))(\:[0-9]+)*(/($|[a-zA-Z0-9\.\,\?\'\\\+&amp;%\$#\=~_\-]+))*$");
	    return urlregex.test(textval);
	}
	</script>
        <?php
    }

}
if (!function_exists('votes_category_configuration_save')) {

    function votes_category_configuration_save( $ID ) {
        //Need to Block this if loop While Adding the Taxonomy Term from the Contestant Add Page 
        if(isset($_POST['votes_category_settings'])){
            $curterm = $ID;
    	    $imgdisplay = isset($_POST['imgdisplay']) ? $_POST['imgdisplay'] : NULL;
    	    $termdisplay = isset($_POST['termdisplay']) ? $_POST['termdisplay'] : NULL;
    	    $total_vote_count = isset($_POST['total_vote_count']) ? $_POST['total_vote_count'] : NULL;
    	    $imgcontest = isset($_POST['imgcontest'])?$_POST['imgcontest']:NULL;
                $middle_custom_navigation = isset($_POST['middle_custom_navigation'])?$_POST['middle_custom_navigation']:'';
    	    $votecount = isset($_POST['votecount']) ? $_POST['votecount'] : NULL;
            $show_description = isset($_POST['show_description'])?$_POST['show_description']:VOTES_SHOW_DESC;
            $vote_contest_entry_person = isset($_POST['vote_contest_entry_person'])?$_POST['vote_contest_entry_person']:VOTES_ENTRY_LIMIT_FORM;
    	    
    	    $args = array('imgdisplay' => $imgdisplay,
    			  'termdisplay' => $termdisplay,
    			  'total_vote_count' => $total_vote_count,
    			  'imgcontest' => $imgcontest,
    			  'middle_custom_navigation'=>$middle_custom_navigation,
    			  'votecount'=>$votecount,
                  'show_description'=>$show_description,
                  'vote_contest_entry_person' => $vote_contest_entry_person);
    	    
                update_option($curterm . '_' . VOTES_SETTINGS, $args);
    	    $votes_expiration = $votes_starttime  = NULL;
    	    if(isset($_POST['votes_expiration']) && $_POST['votes_expiration'] != '0' && trim($_POST['votes_expiration'])){
    		$votes_expiration = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $_POST['votes_expiration'] )));
    				
    	    }
    	    if(isset($_POST['votes_starttime']) && $_POST['votes_starttime'] != '0' && trim($_POST['votes_starttime'])){
    		$votes_starttime = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $_POST['votes_starttime'] )));
    			
    	    }
    	    
    	    //$votes_expiration = isset($_POST['votes_expiration']) ? $_POST['votes_expiration'] : NULL;
    	    $tax_activationcount = isset($_POST['tax_activationcount']) ? $_POST['tax_activationcount'] : NULL;
    	    update_option($curterm . '_' . VOTES_TAXEXPIRATIONFIELD, $votes_expiration);
    	    update_option($curterm . '_' . VOTES_TAXACTIVATIONLIMIT, $tax_activationcount);
    	    update_option($curterm . '_' . VOTES_TAXSTARTTIME, $votes_starttime);			
        }
    }

}

if (!function_exists('votes_category_configuration_delete')) {

    function votes_category_configuration_delete() {
		if(isset($_REQUEST['tag_ID'])) {
			$curterm = $_REQUEST['tag_ID'];
			if(get_option($curterm . '_' . VOTES_SETTINGS)){
				delete_option($curterm . '_' . VOTES_SETTINGS);
			}
			delete_option($curterm . '_' . VOTES_TAXEXPIRATIONFIELD);
			delete_option($curterm . '_' . VOTES_TAXACTIVATIONLIMIT);
			delete_option($curterm . '_' . VOTES_TAXSTARTTIME);
		}
    }

}

//wp_delete_term($term, $taxonomy);
add_action(VOTES_TAXONOMY . '_add_form_fields', 'votes_category_edit_configuration_form');
add_action(VOTES_TAXONOMY . '_edit_form', 'votes_category_edit_configuration_form');
add_action('created_term', 'votes_category_configuration_save');
add_action('edit_term', 'votes_category_configuration_save');
add_action('delete_term', 'votes_category_configuration_delete');