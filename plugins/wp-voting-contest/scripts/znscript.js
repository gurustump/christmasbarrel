/*--------------------------------------------------------------------------------------------------

	File: znscript.js

	Description: This is the main javascript file for the WordPress Voting Contest Plugin
	Please be carefull when editing this file

--------------------------------------------------------------------------------------------------*/
 ;(function($){
         
       $(document).ready(function(e) {
        
        
	
	var versionjq = jQuery.fn.jquery;
	if(parseFloat(versionjq) >= parseFloat('1.10.0')){
	  var funct_name_rep = 'on';	
	}else{
	  var funct_name_rep = 'live';
	}
	
      jQuery('.dynamic_id_multi_vot').each(function(){
	   var dynamic_id_multi_vot = jQuery(this).val();	    
     	
	   page_view_l = jQuery('#contestants-listing'+dynamic_id_multi_vot).attr('class');
	    var dynamic_id = dynamic_id_multi_vot;
	    if(page_view_l=='grid'){
		 var image_path = jQuery('#image_path').val();
		 $('#contestants-listing'+dynamic_id).attr('class','grid');
		 jQuery('.grid_view'+dynamic_id).attr('id','anchor_grid_active');
		 jQuery('.listing_view'+dynamic_id).attr('id','anchor_listing_normal');
            
          voting_check_show_description(dynamic_id,'grid');
                    
		  jQuery('.all_icons_vote'+dynamic_id+' .readsmore-btn').attr('class','readsmoreactive-btn');
		  jQuery('.tw_view_change'+dynamic_id).removeClass('facebook-btn');
		  jQuery('.fb_view_change'+dynamic_id).removeClass('facebook-btn');	    
		  jQuery('.face_view_change'+dynamic_id).attr('src',image_path+'images/facebook_icon.png');
		  jQuery('.tweet_view_change'+dynamic_id).attr('src',image_path+'images/twitter_icons.png');
	      
	      
		 jQuery('.grid_view_category'+dynamic_id).show();
		 jQuery('.normal_view_category'+dynamic_id).hide();
	    
	    }else if(page_view_l=='list'){
		 jQuery('.listing_view').attr('id','anchor_listing_active');
		 var face_list_img_path = jQuery('.face_list_img_path').val();
		 var tweet_list_img_path = jQuery('.tweet_list_img_path').val();
		 
		 $('#contestants-listing'+dynamic_id).attr('class','list');
         
         voting_check_show_description(dynamic_id,'list');
        
		 
		 jQuery('.listing_view'+dynamic_id).attr('id','anchor_listing_active');
		 jQuery('.grid_view'+dynamic_id).attr('id','anchor_grid_normal');
		 
		  jQuery('.all_icons_vote'+dynamic_id+' .readsmoreactive-btn').attr('class','readsmore-btn');
		  jQuery('.tw_view_change'+dynamic_id).addClass('facebook-btn');
		  jQuery('.fb_view_change'+dynamic_id).addClass('facebook-btn');	    
		  jQuery('.face_view_change'+dynamic_id).attr('src',face_list_img_path);
		  jQuery('.tweet_view_change'+dynamic_id).attr('src',tweet_list_img_path);
		 
		 jQuery('.grid_view_category'+dynamic_id).hide();
		 jQuery('.normal_view_category'+dynamic_id).show();
	    }
       });
      
       jQuery('.listing_view')[funct_name_rep]('click',function(){
	    var dynamic_id = jQuery(this).attr('class').split(' ')[1];
	    
            var face_list_img_path = jQuery('.face_list_img_path').val();
            var tweet_list_img_path = jQuery('.tweet_list_img_path').val();
            
            $('#contestants-listing'+dynamic_id).attr('class','list');
            
            voting_check_show_description(dynamic_id,'list');
                            
            jQuery(this).attr('id','anchor_listing_active');
	    
	    jQuery(".grid_view").filter("."+dynamic_id).attr('id','anchor_grid_normal');
	    
	    jQuery('.vote_description_title'+dynamic_id).hide();
	    jQuery('.vote_description_title_list'+dynamic_id).show();
	    jQuery('.vote_description_title_grid'+dynamic_id).hide();
	    
	    jQuery('.all_icons_vote'+dynamic_id+' .readsmoreactive-btn').attr('class','readsmore-btn');
	    jQuery('.tw_view_change'+dynamic_id).addClass('facebook-btn');
	    jQuery('.fb_view_change'+dynamic_id).addClass('facebook-btn');	    
            jQuery('.face_view_change'+dynamic_id).attr('src',face_list_img_path);
            jQuery('.tweet_view_change'+dynamic_id).attr('src',tweet_list_img_path);
	    
	    
            jQuery('.grid_view_category'+dynamic_id).hide();
            jQuery('.normal_view_category'+dynamic_id).show();
       });
           
       jQuery('.grid_view')[funct_name_rep]('click',function(){
	    
	    var dynamic_id = jQuery(this).attr('class').split(' ')[1];
	    
            var image_path = jQuery('#image_path').val();
            $('#contestants-listing'+dynamic_id).attr('class','grid');
            jQuery(this).attr('id','anchor_grid_active');
	    
	    jQuery(".listing_view").filter("."+dynamic_id).attr('id','anchor_listing_normal');  

	    jQuery('.vote_description_title_list'+dynamic_id).hide();
	    jQuery('.vote_description_title'+dynamic_id).hide();
	    jQuery('.vote_description_title_grid'+dynamic_id).show();
	    
	    voting_check_show_description(dynamic_id,'grid');          
	    	    
	    jQuery('.all_icons_vote'+dynamic_id+' .readsmore-btn').attr('class','readsmoreactive-btn');
	    jQuery('.tw_view_change'+dynamic_id).removeClass('facebook-btn');
	    jQuery('.fb_view_change'+dynamic_id).removeClass('facebook-btn');	    
            jQuery('.face_view_change'+dynamic_id).attr('src',image_path+'images/facebook_icon.png');
            jQuery('.tweet_view_change'+dynamic_id).attr('src',image_path+'images/twitter_icons.png');
	    
	    jQuery('.grid_view_category'+dynamic_id).show();
            jQuery('.normal_view_category'+dynamic_id).hide();
       });
        
               
	// LOGIN FORM
		jQuery(document).on('submit','.zn_form_login',function(event){  
			event.preventDefault();
		  
			var form = $(this),
				warning = false,
				button = $('.zn_sub_button',this),
				values = form.serialize();
             
            if(jQuery( ".inner-container" ).hasClass( "register-panel_add" )){
                    
                jQuery('.required_vote_custom').filter(':visible').each(function(){  
                    var type_bos = jQuery(this).attr('type'); 
                    var in_id = jQuery(this).attr('id');
                    if(type_bos=='checkbox'){    
                          var in_idc = jQuery(this).attr('id');
                          if ($('.reg_check_'+in_idc+':checked').length > 0){
                            $('.'+in_idc).attr('style','');
                          }
                          else{
                            $('.'+in_idc).attr('style','color:red');
                            warning = true;
                          }
                    }else if(type_bos=='radio'){
                        var in_ids = jQuery(this).attr('id');
                           if ($('.reg_radio_'+in_ids+':checked').length > 0){
                              $('.'+in_ids).attr('style',''); 
                           }else{
                              $('.'+in_ids).attr('style','color:red');
                              warning = true;
                           }
                    }
                    else{
        				if ( !$(this).val() ) { 
        				    $(this).attr('style','border:1px solid red;');
                            warning = true;
        				}else{
        				   $(this).attr('style','border:none;'); 
        				}
                    }
    			});            
            }else{  
              jQuery('input',form).each(function(){    
				if ( !$(this).val() ) {
					warning = true;
				}
              });   
            }
			if( warning ) {
			     jQuery(".error_empty").remove();
    			 
                 if(jQuery( ".inner-container" ).hasClass( "register-panel_add" )){
    			     $( ".m_title" ).after( "<p class='error_empty'>Please Fill In The Required Fields Below. </p>" );    
    			 }
                 if(jQuery( ".inner-container" ).hasClass( "login-panel_add" )){
    			     $( ".m_title" ).after( "<p class='error_empty'>Please Enter The Username and Password. </p>" );    
    			 }
				button.removeClass('zn_blocked');
				return false;
			}   
			
			if( button.hasClass('zn_blocked')) {
				return;
			}
			
			button.addClass('zn_blocked');
             
			jQuery.post(zn_do_login.ajaxurl, values, function(resp) { 
						 
			      jQuery(".error_empty").remove();
				var data = $(document.createElement('div')).html(resp);

				if ( $('#login_error', data).length ) {

					data.find('a').attr('onClick','ppOpen(\'#forgot_panel\', \'350\');return false;');
					$('div.links', form).html(data);
					button.removeClass('zn_blocked');

				}
				else {
				    
					if ( $('.zn_login_redirect', form).length > 0 ) {
						jQuery.prettyPhoto.close();
						redirect = $('.zn_login_redirect', form);
						href = redirect.val();
						window.location = href;
					}

				}
			      if(resp=='<div id="login_error">Your account has been created.</div>'){
				      button.addClass('zn_blocked');
				      setTimeout(function() {
					  jQuery.prettyPhoto.close();
							      redirect = $('.zn_login_redirect', form);
							      href = redirect.val();
							      window.location = href;
				      }, 500);
				      
			      }
			      return false;
				button.removeClass('zn_blocked');


			});
			 
			 
		});
        
        //PROFILE EDIT
        jQuery(document).on('click','.zn_sub_button_edit',function(event){  
			event.preventDefault();		  
			var form = $('.zn_form_profile'),
				warning = false,
				button = $('.zn_sub_button_edit',this),
				values = form.serialize(); 
            if(jQuery( ".inner-container" ).hasClass( "register-panel_add" )){                    
                jQuery('.required_vote_custom').filter(':visible').each(function(){  
                    var type_bos = jQuery(this).attr('type'); 
                    var in_id = jQuery(this).attr('id');
                    if(type_bos=='checkbox'){    
                          var in_idc = jQuery(this).attr('id');
                          if ($('.reg_check_'+in_idc+':checked').length > 0){
                            $('.'+in_idc).attr('style','');
                          }
                          else{
                            $('.'+in_idc).attr('style','color:red');
                            warning = true;
                          }
                    }else if(type_bos=='radio'){
                        var in_ids = jQuery(this).attr('id');
                           if ($('.reg_radio_'+in_ids+':checked').length > 0){
                              $('.'+in_ids).attr('style',''); 
                           }else{
                              $('.'+in_ids).attr('style','color:red');
                              warning = true;
                           }
                    }
                    else{
        				if ( !$(this).val() ) { 
        				    $(this).attr('style','border:1px solid red;');
                            warning = true;
        				}else{
        				   $(this).attr('style','border:none;'); 
        				}
                    }
    			});            
            }else{  
              jQuery('input',form).each(function(){    
				if ( !$(this).val() ) {
					warning = true;
				}
              });   
            }

			if( warning ) {
			     jQuery(".error_empty").remove();    			
                 if(jQuery( ".inner-container" ).hasClass( "register-panel_add" )){
    			     $( ".m_title" ).after( "<p class='error_empty'>Please Fill In The Required Fields Below. </p>" );    
    			 }                 
				button.removeClass('zn_blocked');
				return false;
			}   
            else{
                jQuery(".error_empty").remove();
            }
			
			if( button.hasClass('zn_blocked')) {
				return;
			}
            form.submit(); 	 
		});

		// LOST PASSWORD
		jQuery(document).on('submit','.zn_form_lost_pass',function(event){
			event.preventDefault();
		
			var form = $(this),
				warning = false,
				button = $('.zn_sub_button',this),
				values = form.serialize()+'&ajax_login=true';
               
			$('input',form).each(function(){
				if ( !$(this).val() ) {
					warning = true;
				}
			}); 
			
			if( warning ) {
			     jQuery(".error_empty").remove();
			     if(jQuery( ".inner-container" ).hasClass( "forgot-panel_add" )){
    			     jQuery( ".m_title" ).after( "<p class='error_empty'>Please Enter The Username / Email. </p>" );
    			 }
				button.removeClass('zn_blocked');
				return false;
			}
			
			if( button.hasClass('zn_blocked')) {
				return;
			}
			
			button.addClass('zn_blocked');
	        jQuery(".error_empty").remove();                        
			$.ajax({
				url: form.attr('action'),
				data: values,
				type: 'POST',
				cache: false,
				success: function (resp) {
					var data = $(document.createElement('div')).html(resp);
					
					$('div.links', form).html('');
					
					if ( $('#login_error', data).length ) {
					
						// We have an error
						var error = $('#login_error', data);
						error.find('a').attr('onClick','ppOpen(\'#forgot_panel\', \'350\');return false;');
						$('div.links', form).html(error);

					}
					else if ( $('.message', data).length ) {
						var message = $('.message', data);
						$('div.links', form).html(message);
					}
					else {
					
						jQuery.prettyPhoto.close();
						redirect = $('.zn_login_redirect', form);
						href = redirect.val();
						location.reload(true);
						//window.location = href;
					}
					
					button.removeClass('zn_blocked');
				},
				error: function (jqXHR , textStatus, errorThrown ) {
					$('div.links', form).html(errorThrown);

				}
			});
			 
			 
		});
        
        // EMAIL - TWITTER LOGIN
		jQuery(document).on('submit','.zn_form_save_email',function(event){
			event.preventDefault();
		
			var form = $(this),
				warning = false,
				button = $('.zn_sub_button',this),
				values = form.serialize()+'&ajax_login=true&action=voting_save_twemail_session';   
                
			
			$('input',form).each(function(){
				if ( !$(this).val() ) {
					warning = true;
				}
			}); 
			
			if( warning ) {
			     jQuery(".error_empty").remove();
			     if(jQuery( ".inner-container" ).hasClass( "forgot-panel_add" )){
    			     jQuery( ".m_title" ).after( "<p class='error_empty'>Please Enter The Email. </p>" );
    			 }
				button.removeClass('zn_blocked');
				return false;
			}
			
			if( button.hasClass('zn_blocked')) {
				return;
			}
			
			button.addClass('zn_blocked');
	        jQuery(".error_empty").remove();                        
				$.ajax({
				url: zn_do_login.ajaxurl,
				data: values,
				type: 'POST',
				cache: false,
				success: function (resp) {
				    if(resp == 0){
				        jQuery( ".m_title" ).after( "<p class='error_empty'>Please Enter The Valid Email. </p>" );
                        button.removeClass('zn_blocked');
				    }
                    else{
                        jQuery(".error_empty").remove();
                        votes_twitter_authentication();
                    }
				},
				error: function (jqXHR , textStatus, errorThrown ) {
					$('div.links', form).html(errorThrown);

				}
			});		 
			 
		});
        
        function voting_check_show_description(dynamic_id,view)
        {
            var value = $('#show_description_'+dynamic_id).val();
            if(value == view || value == 'both') 
                $('.text_description'+dynamic_id).show();
            else
                $('.text_description'+dynamic_id).hide();
        }
        
      });
      
})(jQuery);
/*--------------------------------------------------------------------------------------------------
	Pretty Photo
--------------------------------------------------------------------------------------------------*/
	var versionjq = jQuery.fn.jquery;
	if(parseFloat(versionjq) >= parseFloat('1.10.0')){
	  var funct_name_rep = 'on';	
	}else{
	  var funct_name_rep = 'live';
	}
    
     
	
	function ppOpen(panel, width,flag){        
        if(panel=='#forgot_panel'){
            jQuery( ".inner-container" ).removeClass('login-panel_add');
            jQuery( ".inner-container" ).removeClass('register-panel_add'); 
            jQuery( ".inner-container" ).removeClass('forgot-panel_add');  
            jQuery( ".inner-container" ).addClass('forgot-panel_add');  
        }else if(panel=='#login_panel'){
            jQuery( ".inner-container" ).removeClass('login-panel_add');
            jQuery( ".inner-container" ).removeClass('register-panel_add'); 
            jQuery( ".inner-container" ).removeClass('forgot-panel_add'); 
            jQuery( ".inner-container" ).addClass('login-panel_add');  
        }else if(panel=='#register_panel'){
            jQuery( ".inner-container" ).removeClass('login-panel_add');
            jQuery( ".inner-container" ).removeClass('register-panel_add'); 
            jQuery( ".inner-container" ).removeClass('forgot-panel_add'); 
            jQuery( ".inner-container" ).addClass('register-panel_add');
        }               
        
        jQuery( ".error_empty" ).remove();
        
        if(jQuery('.pp_pic_holder').size() > 0){
            jQuery.prettyPhoto.close();
        }	  
        
        setTimeout(function() {
			jQuery.fn.prettyPhoto({social_tools: false, deeplinking: false, show_title: false, default_width: width, theme:'pp_kalypso'});
			jQuery.prettyPhoto.open(panel);
		}, 300);
        
      
	} // function to open different panel within the panel
   
    
    
     
	jQuery("a[data-rel^='prettyPhoto'], .prettyphoto_link").prettyPhoto({theme:'pp_kalypso',social_tools:false, deeplinking:false});
	jQuery(".prettyPhoto").prettyPhoto({theme:'pp_kalypso'});
    
    jQuery("a[data-rel^='prettyPhoto[login_panel]']").click(function(){       
       jQuery( ".error_empty" ).remove(); 
    });
     
	jQuery("a[data-rel^='prettyPhoto[login_panel]']").prettyPhoto({theme:'pp_kalypso', default_width:800, social_tools:false, deeplinking:false});
	jQuery( ".inner-container" ).addClass('login-panel_add');
	
	if (funct_name_rep=='on') {
	    //code
   	    jQuery(document)[funct_name_rep]('click',".prettyPhoto_transparent",function(e){ 
		    e.preventDefault();
		    jQuery.fn.prettyPhoto({social_tools: false, deeplinking: false, show_title: false, default_width: 980, theme:'pp_kalypso transparent', opacity: 0.95});
		    jQuery.prettyPhoto.open($(this).attr('href'),'','');
	    });
 
	}else{
	    jQuery(".prettyPhoto_transparent")[funct_name_rep]('click',function(e){ 
		    e.preventDefault();
		    jQuery.fn.prettyPhoto({social_tools: false, deeplinking: false, show_title: false, default_width: 980, theme:'pp_kalypso transparent', opacity: 0.95});
		    jQuery.prettyPhoto.open($(this).attr('href'),'','');
	    });
	}
    
    
    