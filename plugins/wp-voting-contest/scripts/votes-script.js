
function votes_countdown(el) {

		var countdown = jQuery(el);
		if (!countdown.length) return false;
		countdown.each(function(i, e) {
			var timer 	= jQuery(this).data('datetimer').split("-");
			var currenttimer = jQuery(this).data('currenttimer').split("-");
			jQuery(this).countDown({
				omitWeeks: false, 
				targetDate: {					
					'year':     timer[0],
					'month':    timer[1],
					'day':      timer[2],
					'hour':     timer[3],
					'min':      timer[4],
					'sec':      timer[5]
				},
				currentDate: {					
					'year':     currenttimer[0],
					'month':    currenttimer[1],
					'day':      currenttimer[2],
					'hour':     currenttimer[3],
					'min':      currenttimer[4],
					'sec':      currenttimer[5]
				},
				onComplete: function() {
					console.log('Completed');
                }
			
			});
			countdown.css('visibility','visible');

		});
		
}

function voting_check_show_description(dynamic_id,view)
{
    var value = $('#show_description_'+dynamic_id).val();
    if(value == view || value == 'both') 
        $('.text_description'+dynamic_id).show();
    else
        $('.text_description'+dynamic_id).hide();
}
	
jQuery(document).ready( function(){
	
	var versionjq = jQuery.fn.jquery;
	if(parseFloat(versionjq) >= parseFloat('1.10.0')){
	  var funct_name = 'on';	
	}else{
	  var funct_name = 'live';
	}
	
	jQuery("a[data-rel^='prettyPhoto'], .prettyphoto_link").prettyPhoto({theme:'pp_kalypso',social_tools:false, deeplinking:false});
	jQuery(".prettyPhoto").prettyPhoto({theme:'pp_kalypso'});
	jQuery("a[data-rel^='prettyPhoto[login_panel]']").prettyPhoto({theme:'pp_kalypso', default_width:800, social_tools:false, deeplinking:false});
	
	
	jQuery('.add-contestants-form').each(function(i, e) {
		if(jQuery(this).is(':visible')){
			jQuery(this).siblings('.votes-toggle-form').html('X Close');
		}else{
			//jQuery(this).siblings('.votes-toggle-form').html('Submit Entry');
		}	
	});
		
	if(!jQuery('.add-contestants-form ').length){
		jQuery('.votes-toggle-form').hide();
	}
	
	jQuery('.votes-toggle-form').click(function(e){
		var form_show_logged = jQuery('.votes-toggle-form').hasClass( "loggin_disabled" );
		var term_id = jQuery(this).attr("class").split(' ')[3];	
		if(form_show_logged==true){         
			var _self = jQuery(this);
			if(_self.siblings('.contestants-success').is(':visible'))
				_self.siblings('.contestants-success').hide();
			_self.siblings('.add-contestants-form').slideToggle('slow', function(){
				
				if(jQuery(this).is(':visible')){
				    var close_button_text = jQuery('#close_button_text ').val();
					_self.html('X '+close_button_text );
                    
                    if (typeof add_description_contestant_vote !== 'undefined' && $.isFunction(add_description_contestant_vote)) {
                        add_description_contestant_vote(term_id);
                    }
                    					
				}else{
				    var open_button_text = jQuery('#open_button_text').val();
					_self.html(open_button_text);
					jQuery('.contestants-errors').hide();
				}
			});
		}else{		
			jQuery('.vote_click_login_do'+term_id).click();
		}
	});
	
	
	
		    
    //Old jquery
    if(funct_name=='live'){
		
	jQuery('[class^=readsmore-btn]')[funct_name]('click', function(e){
		jQuery.cookie("short_code_id", $(this).attr('rev'), { path: '/' });
		e.preventDefault();
		window.location.href = jQuery(this).attr('href');	
	});
	
	jQuery('[class^=readsmoreactive-btn]')[funct_name]('click', function(e){
		jQuery.cookie("short_code_id", $(this).attr('rev'), { path: '/' });
		e.preventDefault();
		window.location.href = jQuery(this).attr('href');	
	});
	
	jQuery('[class^=vote_description_page]')[funct_name]('click', function(e){
		jQuery.cookie("short_code_id", $(this).attr('rev'), { path: '/' });
		e.preventDefault();
		window.location.href = jQuery(this).attr('href');	
	});
	
	   
	jQuery('.votes-pagination a')[funct_name]('click', function(e){
		var term_id = jQuery(this).parent().attr("class").split(' ')[1];
		page_view_list = jQuery('#contestants-listing'+term_id).attr('class');
		e.preventDefault();
		var link = jQuery(this).attr('href'); 
			if(link.indexOf('?') === -1){
				link = link;
			}else{
				link = link;
			}
		jQuery('#votes-post-container'+term_id).block({
		    message: null, 
		    overlayCSS: { 
			backgroundColor: '#fff', 
			opacity:         0.6 
		    }
		});
		jQuery('#votes-post-container'+term_id).load(link+' .contest-posts-container'+term_id,function(){
		    
		    /********* Newly added ************/
		    jQuery('#contestants-listing'+term_id).attr('class',page_view_list);   
		    if(page_view_list=='grid'){
			
			jQuery('.vote_description_title'+term_id).hide();
			jQuery('.vote_description_title_list'+term_id).hide();
			jQuery('.vote_description_title_grid'+term_id).show();
	    
			voting_check_show_description(term_id,'grid');
            
			var image_path = jQuery('#image_path').val();
			jQuery('.all_icons_vote'+term_id+' .readsmore-btn').attr('class','readsmoreactive-btn');
			jQuery('.tw_view_change'+term_id).removeClass('facebook-btn');
			jQuery('.fb_view_change'+term_id).removeClass('facebook-btn');	    
			jQuery('.face_view_change'+term_id).attr('src',image_path+'images/facebook_icon.png');
			jQuery('.tweet_view_change'+term_id).attr('src',image_path+'images/twitter_icons.png');
			jQuery('.grid_view_category'+term_id).show();
			jQuery('.normal_view_category'+term_id).hide();
		    }else{
			jQuery('.vote_description_title'+term_id).hide();
			jQuery('.vote_description_title_list'+term_id).show();
			jQuery('.vote_description_title_grid'+term_id).hide();
		
            voting_check_show_description(term_id,'list');            
					
			var face_list_img_path = jQuery('.face_list_img_path').val();
			var tweet_list_img_path = jQuery('.tweet_list_img_path').val();

			jQuery('.all_icons_vote'+term_id+' .readsmoreactive-btn').attr('class','readsmore-btn');
			jQuery('.tw_view_change'+term_id).addClass('facebook-btn');
			jQuery('.fb_view_change'+term_id).addClass('facebook-btn');	    
			jQuery('.face_view_change'+term_id).attr('src',face_list_img_path);
			jQuery('.tweet_view_change'+term_id).attr('src',tweet_list_img_path);
			
			jQuery('.grid_view_category'+term_id).hide();
			jQuery('.normal_view_category'+term_id).show();
		    }
		    
		jQuery("a[data-rel^='prettyPhoto'], .prettyphoto_link").prettyPhoto({theme:'pp_kalypso',social_tools:false, deeplinking:false});
		jQuery(".prettyPhoto").prettyPhoto({theme:'pp_kalypso'});
		jQuery("a[data-rel^='prettyPhoto[login_panel]']").prettyPhoto({theme:'pp_kalypso', default_width:800, social_tools:false, deeplinking:false});
		   /********* above Newly added ************/
		    
		     jQuery('#votes-post-container'+term_id).unblock();
		     jQuery('html,body').animate({
			scrollTop: jQuery("#paged-focus"+term_id).offset().top},
		     'slow');
				if(!jQuery.trim(jQuery('#votes-post-container'+term_id).html())){
					jQuery('#votes-post-container'+term_id).next('.countdown_wrapper').hide();
					jQuery('#votes-post-container'+term_id).html('No List found');
				}
		});
	    });
	
	    jQuery('.votes-pagination select')[funct_name]('change', function(e){
		var term_id = jQuery(this).attr("class");
		page_view_list = jQuery('#contestants-listing'+term_id).attr('class');
		e.preventDefault();
		var link = jQuery(this).val(); 
		jQuery('#votes-post-container'+term_id).block({
		    message: null, 
		    overlayCSS: { 
			backgroundColor: '#fff', 
			opacity:         0.6 
		    }
		});
		jQuery('#votes-post-container'+term_id).load(link+' .contest-posts-container'+term_id,function(){
		    jQuery('#votes-post-container'+term_id).unblock();
				jQuery('#paged-focus'+term_id).attr('tabindex', '1000').focus();
		    
		    jQuery('#contestants-listing'+term_id).attr('class',page_view_list);   
		    if(page_view_list=='grid'){
			jQuery('.vote_description_title'+term_id).hide();
			jQuery('.vote_description_title_list'+term_id).hide();
			jQuery('.vote_description_title_grid'+term_id).show();
			
			voting_check_show_description(term_id,'grid');
			var image_path = jQuery('#image_path').val();
			jQuery('.all_icons_vote'+term_id+' .readsmore-btn').attr('class','readsmoreactive-btn');
			jQuery('.tw_view_change'+term_id).removeClass('facebook-btn');
			jQuery('.fb_view_change'+term_id).removeClass('facebook-btn');	    
			jQuery('.face_view_change'+term_id).attr('src',image_path+'images/facebook_icon.png');
			jQuery('.tweet_view_change'+term_id).attr('src',image_path+'images/twitter_icons.png');
			jQuery('.grid_view_category'+term_id).show();
			jQuery('.normal_view_category'+term_id).hide();
		    }else{
			jQuery('.vote_description_title'+term_id).hide();
			jQuery('.vote_description_title_list'+term_id).show();
			jQuery('.vote_description_title_grid'+term_id).hide();
			
			voting_check_show_description(term_id,'list');
			var face_list_img_path = jQuery('.face_list_img_path').val();
			var tweet_list_img_path = jQuery('.tweet_list_img_path').val();
			jQuery('.all_icons_vote'+term_id+' .readsmoreactive-btn').attr('class','readsmore-btn');
			jQuery('.tw_view_change'+term_id).addClass('facebook-btn');
			jQuery('.fb_view_change'+term_id).addClass('facebook-btn');	    
			jQuery('.face_view_change'+term_id).attr('src',face_list_img_path);
			jQuery('.tweet_view_change'+term_id).attr('src',tweet_list_img_path);
			
			jQuery('.grid_view_category'+term_id).hide();
			jQuery('.normal_view_category'+term_id).show();
		    }
		    
		jQuery("a[data-rel^='prettyPhoto'], .prettyphoto_link").prettyPhoto({theme:'pp_kalypso',social_tools:false, deeplinking:false});
		jQuery(".prettyPhoto").prettyPhoto({theme:'pp_kalypso'});
		jQuery("a[data-rel^='prettyPhoto[login_panel]']").prettyPhoto({theme:'pp_kalypso', default_width:800, social_tools:false, deeplinking:false});
        
        
		
		
		});
	    });
	    
	jQuery('a.votebutton')[funct_name]('click', function(){	   
	var term_id = jQuery(this).attr("class").split(' ')[2];
	var link_clicked  = this;
	var pid = this.id.replace(/vote/, '');
	if(term_id==''){
	term_id=pid;
	}
	var votestermid = jQuery('#votes-term-id'+term_id).val();
	   if (jQuery(".votes-pagination .current").length > 0){
	       var currentpage = jQuery(".votes-pagination .current").html();
	   }else{
	       var currentpage = 0;
	   }
	   
	   
	   jQuery('#votes-post-container'+term_id).block({
	       message: "Processing...", 
	       overlayCSS: { 
		   backgroundColor: '#fff', 
		   opacity:         0.6 
	       }
	   });
	   jQuery.ajax({
	       url: votesajaxurl,
	       data:{
		   action:'savevotes',			
		   pid:pid,
		   termid: votestermid
		   //,paged: currentpage
			   
	       },
	       type: 'GET',
	       dataType: 'jsonp',
	       success: function( result ) {
		   jQuery('#votes-post-container'+term_id).unblock();
		   if(result.success==1){
		       jQuery(link_clicked).text(jQuery('.voted_text').val());
               
               var votes_content_title = jQuery('#votes_content_title').val();
                   
               if(votes_content_title != undefined)                 
                    jQuery('.votebutton').text(jQuery('.voted_text').val());   
                        
		       jQuery('.votescount'+pid).html(result.votes);
               jQuery('.votescount'+pid).append('<input type="hidden" id="votescounter'+pid+'" value='+result.votes+' />');
		       
		       var total_cnt = jQuery('.total_cnt_vote_res'+term_id).html();
		       jQuery('.total_cnt_vote_res'+term_id).html(parseInt(total_cnt)+parseInt(1));
		       
		       jQuery.fancybox('<h2 style="margin:10px 0  0 10px;font-size:inherit;">'+result.msg+'</h2>',
		       {
			   'width':200,
			   'height':50,
			   'maxWidth': 200,
			   'maxHeight': 50,
			   'minWidth': 200,
			   'minHeight': 50
		       }
		       );
			  
			    //jQuery('#contestants-listing').html(result.content);
		   }
		   else{ 
		      
		       jQuery.fancybox('<h2 style="margin:10px 0  0 15px;font-size:inherit;">'+result.msg+'</h2>',
		       {
			   'width':180,
			   'height':50,
			   'maxWidth': 1300,
			   'maxHeight': 50,
			   'minWidth': 180,
			   'minHeight': 50
		       }
		       );
		   }
		   
		 
		  
	       }	
	   });
	   return false;
       });
	
    }else{
	//New Jquery
	
	jQuery(document)[funct_name]('click','[class^=readsmore-btn]', function(e){
		jQuery.cookie("short_code_id", $(this).attr('rev'), { path: '/' });
		e.preventDefault();
		window.location.href = jQuery(this).attr('href');	
	});

	jQuery(document)[funct_name]('click','[class^=readsmoreactive-btn]', function(e){
		jQuery.cookie("short_code_id", $(this).attr('rev'), { path: '/' });
		e.preventDefault();
		window.location.href = jQuery(this).attr('href');	
	});
	
	jQuery(document)[funct_name]('click','[class^=vote_description_page]', function(e){
		jQuery.cookie("short_code_id", $(this).attr('rev'), { path: '/' });
		e.preventDefault();
		window.location.href = jQuery(this).attr('href');	
	});
		
	jQuery(document).on('click','.votes-pagination a', function(e){
		var term_id = jQuery(this).parent().attr("class").split(' ')[1];
         if(term_id == "profile"){
            var link = jQuery(this).attr('href');
            votes_display_profilescreen(link);
            
            jQuery('.voting-profile').block({
    		    message: null, 
    		    overlayCSS: { 
    			backgroundColor: '#fff', 
    			opacity:         0.6 
    		    }
            });
                        
            jQuery('html,body').animate({
			     scrollTop: jQuery(".voting-profile").offset().top},
            'slow');
            return false;
        }
		page_view_list = jQuery('#contestants-listing'+term_id).attr('class');
		e.preventDefault();
		var link = jQuery(this).attr('href'); 
			if(link.indexOf('?') === -1){
				link = link;
			}else{
				link = link;
			}
		jQuery('#votes-post-container'+term_id).block({
		    message: null, 
		    overlayCSS: { 
			backgroundColor: '#fff', 
			opacity:         0.6 
		    }
		});
		jQuery('#votes-post-container'+term_id).load(link+' .contest-posts-container'+term_id,function(){
		    
		    /********* Newly added ************/
		    jQuery('#contestants-listing'+term_id).attr('class',page_view_list);   
		    if(page_view_list=='grid'){
			jQuery('.vote_description_title'+term_id).hide();
			jQuery('.vote_description_title_list'+term_id).hide();
			jQuery('.vote_description_title_grid'+term_id).show();
			voting_check_show_description(term_id,'grid');
			var image_path = jQuery('#image_path').val();
			jQuery('.all_icons_vote'+term_id+' .readsmore-btn').attr('class','readsmoreactive-btn');
			jQuery('.tw_view_change'+term_id).removeClass('facebook-btn');
			jQuery('.fb_view_change'+term_id).removeClass('facebook-btn');	    
			jQuery('.face_view_change'+term_id).attr('src',image_path+'images/facebook_icon.png');
			jQuery('.tweet_view_change'+term_id).attr('src',image_path+'images/twitter_icons.png');
			jQuery('.grid_view_category'+term_id).show();
			jQuery('.normal_view_category'+term_id).hide();
		    }else{
			jQuery('.vote_description_title'+term_id).hide();
			jQuery('.vote_description_title_list'+term_id).show();
			jQuery('.vote_description_title_grid'+term_id).hide();			
			voting_check_show_description(term_id,'list');
			var face_list_img_path = jQuery('.face_list_img_path').val();
			var tweet_list_img_path = jQuery('.tweet_list_img_path').val();
			jQuery('.all_icons_vote'+term_id+' .readsmoreactive-btn').attr('class','readsmore-btn');
			jQuery('.tw_view_change'+term_id).addClass('facebook-btn');
			jQuery('.fb_view_change'+term_id).addClass('facebook-btn');	    
			jQuery('.face_view_change'+term_id).attr('src',face_list_img_path);
			jQuery('.tweet_view_change'+term_id).attr('src',tweet_list_img_path);
			jQuery('.grid_view_category'+term_id).hide();
			jQuery('.normal_view_category'+term_id).show();
		    }
		    
		jQuery("a[data-rel^='prettyPhoto'], .prettyphoto_link").prettyPhoto({theme:'pp_kalypso',social_tools:false, deeplinking:false});
		jQuery(".prettyPhoto").prettyPhoto({theme:'pp_kalypso'});
		jQuery("a[data-rel^='prettyPhoto[login_panel]']").prettyPhoto({theme:'pp_kalypso', default_width:800, social_tools:false, deeplinking:false});
        
        var sociall_tools    = "";               
                        
            window.markupp = '<div class="pp_pic_holder"> \
                \     <div class="pp_social"></div> \
    						<div class="ppt">&nbsp;</div> \
    						<div class="pp_top"> \
    							<div class="pp_left"></div> \
    							<div class="pp_middle"></div> \
    							<div class="pp_right"></div> \
    						</div> \
    						<div class="pp_content_container"> \
    							<div class="pp_left"> \
    							<div class="pp_right"> \
    								<div class="pp_content"> \
    									<div class="pp_loaderIcon"></div> \
    									<div class="pp_fade pp_single"> \
    										<a href="#" class="pp_expand" title="Expand the image">Expand</a> \
    										<div class="pp_hoverContainer"> \
    											<a class="pp_next" href="#">next</a> \
    											<a class="pp_previous" href="#">previous</a> \
    										</div> \
    										<div id="pp_full_res" class=""></div> \
    										<div class="pp_details"> \
    											<div class="pp_nav"> \
    												<a href="#" class="pp_arrow_previous">Previous</a> \
    												<p class="currentTextHolder">0/0</p> \
    												<a href="#" class="pp_arrow_next">Next</a> \
    											</div> \
    											<p class="pp_description"></p> \
                                                <p class="pp_mult_desc"></p> \
    											\
    											<a class="pp_close" href="#">Close</a> \
    										</div> \
    									</div> \
    								</div> \
    							</div> \
    							</div> \
    						</div> \
    						<div class="pp_bottom"> \
    							<div class="pp_left"></div> \
    							<div class="pp_middle"></div> \
    							<div class="pp_right"></div> \
    						</div> \                                                                                                                                                      </div> \
    					<div class="pp_overlay"></div>';      
            
           jQuery('a[data-gal^=prettyPhoto]').prettyPhoto({               
                theme:'pp_kalypso',                
    			markup: markupp,
                social_tools: sociall_tools,
                changepicturecallback: function()
                {                    
                    var votes_id = jQuery(".voteid").val();              
                    voting_add_contents(votes_id);                                         
                }                   	                            		
   		    }); 
  		
		  
		   /********* above Newly added ************/
		    
		     jQuery('#votes-post-container'+term_id).unblock();
		     jQuery('html,body').animate({
			 scrollTop: jQuery("#paged-focus"+term_id).offset().top},
		     'slow');
				if(!jQuery.trim(jQuery('#votes-post-container'+term_id).html())){
					jQuery('#votes-post-container'+term_id).next('.countdown_wrapper').hide();
					jQuery('#votes-post-container'+term_id).html('No List found');
				}
		});
	    });
	
	    jQuery(document)[funct_name]('change','.votes-pagination select', function(e){
		var term_id = jQuery(this).attr("class");
        if(term_id == "profile"){
            var link = jQuery(this).val();
            votes_display_profilescreen(link);
            jQuery('.voting-profile').block({
    		    message: null, 
    		    overlayCSS: { 
    			backgroundColor: '#fff', 
    			opacity:         0.6 
    		    }
            });
                        
            jQuery('html,body').animate({
			     scrollTop: jQuery(".voting-profile").offset().top},
            'slow');
            return false;            
        }
		page_view_list = jQuery('#contestants-listing'+term_id).attr('class');
		e.preventDefault();
		var link = jQuery(this).val(); 
		jQuery('#votes-post-container'+term_id).block({
		    message: null, 
		    overlayCSS: { 
			backgroundColor: '#fff', 
			opacity:         0.6 
		    }
		});
		jQuery('#votes-post-container'+term_id).load(link+' .contest-posts-container'+term_id,function(){
		    jQuery('#votes-post-container'+term_id).unblock();
				jQuery('#paged-focus'+term_id).attr('tabindex', '1000').focus();
		    jQuery('#contestants-listing'+term_id).attr('class',page_view_list);   
		    if(page_view_list=='grid'){
			jQuery('.vote_description_title'+term_id).hide();
			jQuery('.vote_description_title_list'+term_id).hide();
			jQuery('.vote_description_title_grid'+term_id).show();			
			
            voting_check_show_description(term_id,'grid');
			var image_path = jQuery('#image_path').val();
			jQuery('.all_icons_vote'+term_id+' .readsmore-btn').attr('class','readsmoreactive-btn');
			jQuery('.tw_view_change'+term_id).removeClass('facebook-btn');
			jQuery('.fb_view_change'+term_id).removeClass('facebook-btn');	    
			jQuery('.face_view_change'+term_id).attr('src',image_path+'images/facebook_icon.png');
			jQuery('.tweet_view_change'+term_id).attr('src',image_path+'images/twitter_icons.png');
			jQuery('.grid_view_category'+term_id).show();
			jQuery('.normal_view_category'+term_id).hide();
		    }else{
			jQuery('.vote_description_title'+term_id).hide();
			jQuery('.vote_description_title_list'+term_id).show();
			jQuery('.vote_description_title_grid'+term_id).hide();

			voting_check_show_description(term_id,'list');
			var face_list_img_path = jQuery('.face_list_img_path').val();
			var tweet_list_img_path = jQuery('.tweet_list_img_path').val();
			jQuery('.all_icons_vote'+term_id+' .readsmoreactive-btn').attr('class','readsmore-btn');
			jQuery('.tw_view_change'+term_id).addClass('facebook-btn');
			jQuery('.fb_view_change'+term_id).addClass('facebook-btn');	    
			jQuery('.face_view_change'+term_id).attr('src',face_list_img_path);
			jQuery('.tweet_view_change'+term_id).attr('src',tweet_list_img_path);
			jQuery('.grid_view_category'+term_id).hide();
			jQuery('.normal_view_category'+term_id).show();
		    }
		
		jQuery("a[data-rel^='prettyPhoto'], .prettyphoto_link").prettyPhoto({theme:'pp_kalypso',social_tools:false, deeplinking:false});
		jQuery(".prettyPhoto").prettyPhoto({theme:'pp_kalypso'});
		jQuery("a[data-rel^='prettyPhoto[login_panel]']").prettyPhoto({theme:'pp_kalypso', default_width:800, social_tools:false, deeplinking:false});
        
        var sociall_tools    = "";               
                        
            window.markupp = '<div class="pp_pic_holder"> \
                \     <div class="pp_social"></div> \
    						<div class="ppt">&nbsp;</div> \
    						<div class="pp_top"> \
    							<div class="pp_left"></div> \
    							<div class="pp_middle"></div> \
    							<div class="pp_right"></div> \
    						</div> \
    						<div class="pp_content_container"> \
    							<div class="pp_left"> \
    							<div class="pp_right"> \
    								<div class="pp_content"> \
    									<div class="pp_loaderIcon"></div> \
    									<div class="pp_fade pp_single"> \
    										<a href="#" class="pp_expand" title="Expand the image">Expand</a> \
    										<div class="pp_hoverContainer"> \
    											<a class="pp_next" href="#">next</a> \
    											<a class="pp_previous" href="#">previous</a> \
    										</div> \
    										<div id="pp_full_res" class=""></div> \
    										<div class="pp_details"> \
    											<div class="pp_nav"> \
    												<a href="#" class="pp_arrow_previous">Previous</a> \
    												<p class="currentTextHolder">0/0</p> \
    												<a href="#" class="pp_arrow_next">Next</a> \
    											</div> \
    											<p class="pp_description"></p> \
                                                <p class="pp_mult_desc"></p> \
    											\
    											<a class="pp_close" href="#">Close</a> \
    										</div> \
    									</div> \
    								</div> \
    							</div> \
    							</div> \
    						</div> \
    						<div class="pp_bottom"> \
    							<div class="pp_left"></div> \
    							<div class="pp_middle"></div> \
    							<div class="pp_right"></div> \
    						</div> \                                                                                                                                                      </div> \
    					<div class="pp_overlay"></div>';      
            
           jQuery('a[data-gal^=prettyPhoto]').prettyPhoto({               
                theme:'pp_kalypso',                
    			markup: markupp,
                social_tools: sociall_tools,
                changepicturecallback: function()
                {                    
                    var votes_id = jQuery(".voteid").val();              
                    voting_add_contents(votes_id);                                         
                }                   	                            		
   		    }); 
		  
		  
		});
	    });
        
        jQuery(document)[funct_name]('click','a.pretty_photo_vote', function(){          
            //ppOpen('#login_panel', '800',1);      
            ppOpen('#login_panel', '800');        
        });
        
        jQuery(document)[funct_name]('click','a[href="#login_panel"]', function(){          
            //ppOpen('#login_panel', '800',1);      
            ppOpen('#login_panel', '800');        
        });
        
	    
		jQuery(document)[funct_name]('click','a.votebutton', function(){		   
		   var term_id = jQuery(this).attr("class").split(' ')[2];              
		   var link_clicked  = this;
		   var pid = this.id.replace(/vote/, '');
           
			if(term_id==''){
			term_id=pid;
			}           
		   var votestermid = jQuery('#votes-term-id'+term_id).val();      
                 
		   
		   if (jQuery(".votes-pagination .current").length > 0){
		       var currentpage = jQuery(".votes-pagination .current").html();
		   }else{
		       var currentpage = 0;
		   }
           //var current_time  = new Date();
		   var today= new Date();
           var current_time  = today.toUTCString();
           var gmt_offset = today.getTimezoneOffset();
           var cur_timeinfo = new Array();   
          /* cur_timeinfo[0]     = current_time.getMonth() + 1;
           cur_timeinfo[1]     = current_time.getDate();
           cur_timeinfo[2]     = current_time.getFullYear();           
           cur_timeinfo[3]     = current_time.getHours();
           cur_timeinfo[4]     = current_time.getMinutes();
           cur_timeinfo[5]     = current_time.getSeconds();  
           */
		   jQuery('#votes-post-container'+term_id).block({
		       message: "Processing...", 
		       overlayCSS: { 
			   backgroundColor: '#fff', 
			   opacity:         0.6 
		       }
		   });
		   jQuery.ajax({
		       url: votesajaxurl,
		       data:{
			   action:'savevotes',			
			   pid:pid,
			   termid: votestermid,
               current_time:current_time,
               gmt_offset:gmt_offset
			   //,paged: currentpage
				   
		       },
		       type: 'GET',
		       dataType: 'jsonp',
		       success: function( result ) {
			   jQuery('#votes-post-container'+term_id).unblock();
			   if(result.success==1){
			       jQuery(link_clicked).text(jQuery('.voted_text').val());  
                   
                   var myElem = jQuery('.pp_social').text();
                   if(myElem != null)
                        jQuery('a#'+link_clicked.id).text(jQuery('.voted_text').val());
                        
                   var votes_content_title = jQuery('#votes_content_title').val();
                   
                   if(votes_content_title != undefined)                 
                        jQuery('.votebutton').text(jQuery('.voted_text').val());   
                                        
			       jQuery('.votescount'+pid).html(result.votes);
                   jQuery('.votescount'+pid).append('<input type="hidden" id="votescounter'+pid+'" value='+result.votes+' />');
			       
			       var total_cnt = jQuery('.total_cnt_vote_res'+term_id).html();
				   jQuery('.total_cnt_vote_res'+term_id).html(parseInt(total_cnt)+parseInt(1));
		       
			       jQuery.fancybox('<h2 style="margin:10px 0  0 10px;font-size:inherit;">'+result.msg+'</h2>',
			       {
				   'width':200,
				   'height':50,
				   'maxWidth': 200,
				   'maxHeight': 50,
				   'minWidth': 200,
				   'minHeight': 50
			       }
			       );
                   
                   if (typeof result.button_flag != 'undefined'){
    				    if(result.button_flag == 1){    				        
                            $('a.votebutton').each(function(i, obj) {                                              
                                if(jQuery(obj).hasClass(result.tax_id) === true){
                                    if(!jQuery(obj).is('#vote'+pid)){ 
                                        jQuery(obj).addClass('voting_grey_button');
                                        jQuery(obj).text(jQuery('.voted_text').val());
                                    }   
                                }                                                              
                            });
    				    }
                   }
                    
                    
				    //jQuery('#contestants-listing').html(result.content);
			   }
			   else{ 
			       
                   
                    
			       jQuery.fancybox('<h2 style="margin:10px 0  0 15px;font-size:inherit;">'+result.msg+'</h2>',
			       {
				   'width':180,
				   'height':50,
				   'maxWidth': 1300,
				   'maxHeight': 50,
				   'minWidth': 180,
				   'minHeight': 50
			       }
			       );
			   }
			   
			 
			  
		       }	
		   });
		   return false;
	       });
	
    }
    		
	votes_countdown('.countdown_dashboard');
});

        jQuery(document).ready(function(){  
            
            var sociall_tools    = "";               
                        
            window.markupp = '<div class="pp_pic_holder"> \
                \     <div class="pp_social"></div> \
    						<div class="ppt">&nbsp;</div> \
    						<div class="pp_top"> \
    							<div class="pp_left"></div> \
    							<div class="pp_middle"></div> \
    							<div class="pp_right"></div> \
    						</div> \
    						<div class="pp_content_container"> \
    							<div class="pp_left"> \
    							<div class="pp_right"> \
    								<div class="pp_content"> \
    									<div class="pp_loaderIcon"></div> \
    									<div class="pp_fade pp_single"> \
    										<a href="#" class="pp_expand" title="Expand the image">Expand</a> \
    										<div class="pp_hoverContainer"> \
    											<a class="pp_next" href="#">next</a> \
    											<a class="pp_previous" href="#">previous</a> \
    										</div> \
    										<div id="pp_full_res" class=""></div> \
    										<div class="pp_details"> \
    											<div class="pp_nav"> \
    												<a href="#" class="pp_arrow_previous">Previous</a> \
    												<p class="currentTextHolder">0/0</p> \
    												<a href="#" class="pp_arrow_next">Next</a> \
    											</div> \
    											<p class="pp_description"></p> \
                                                <p class="pp_mult_desc"></p> \
    											\
    											<a class="pp_close" href="#">Close</a> \
    										</div> \
    									</div> \
    								</div> \
    							</div> \
    							</div> \
    						</div> \
    						<div class="pp_bottom"> \
    							<div class="pp_left"></div> \
    							<div class="pp_middle"></div> \
    							<div class="pp_right"></div> \
    						</div> \                                                                                                                                                      </div> \
    					<div class="pp_overlay"></div>';      
            
           jQuery('a[data-gal^=prettyPhoto]').prettyPhoto({               
                theme:'pp_kalypso',                
    			markup: markupp,
                social_tools: sociall_tools,
                changepicturecallback: function()
                {                    
                    var votes_id = jQuery(".voteid").val();              
                    voting_add_contents(votes_id);                                         
                }                   	                            		
   		    });   
            
       });
        
        function voting_add_contents(votes_id){       
                
                var html_vte_counter = jQuery('.votescounter_'+votes_id).clone().find("a.vote-btn").remove().end().html();
                if(html_vte_counter != null){
                if(html_vte_counter.trim() != null)
                    html_vte_counter = "<div class='wp_voting wp_voting_count'>"+html_vte_counter+"</div>";
                } 
                    
                var html_vote_button = jQuery('.votescounter_'+votes_id).clone().find(".square").remove().end().html(); 
                if(html_vote_button != null){               
                if(html_vote_button.trim() != "")
                    html_vote_button ="<div class='wp_voting'>"+html_vote_button+"</div>";
                   
                }               
                
                html_social = '';
                var html_social = jQuery('.social_'+votes_id).find("a.readsmoreactive-btn").end().html();                        
                //html_social = jQuery('.pp_social').find("a.readsmore-btn").remove().end().html();
                 
                if(html_vote_button != null){  
                if(html_vote_button.trim() != "")
                    html_social = "<div class='facebook'>"+html_social+"</div>";
                }
                
                
                jQuery('.pp_social').html(html_vte_counter+html_vote_button+html_social); 
                jQuery('.pp_social').css("margin-right","20px");   
                
                jQuery('.pp_social').find("a.readsmore-btn").hide();
                jQuery('.pp_social').find("a.readsmoreactive-btn").hide();
                 
                if(html_vote_button.indexOf('voting_grey_button') >=0){                        
                        jQuery('.wp_voting a#vote'+votes_id).parent().css( "background", "#6D7B8D" );                               }
                
                jQuery.ajax({
            	       url: votesajaxurl,
            	       data:{
            		   action:'voting_additional_fields_pretty',			
            		   pid:votes_id       		   
           			   },
            	       type: 'POST',
            	       dataType: 'html',
            	       success: function( result ) {   
            	          if(result != 0) 
            		        jQuery('.pp_mult_desc').html(jQuery(result));
                            var pp_cnt_height = jQuery('.pp_content').height();
                            var pp_add_height = jQuery('.pp_mult_desc').height();
                            jQuery('.pp_content').css('height',pp_cnt_height+pp_add_height);
                       }	
                });  
                                                    
        } 
        
        function voting_change_values(votes_id){
                jQuery('.votescounter').text(jQuery('#votescounter'+votes_id).val()); 
                jQuery('.votebutton_popup').html(jQuery('a#vote'+votes_id).text());                      
        } 
        function votes_display_profilescreen(link)
        {           
            jQuery('.voting-profile').load(link+' .voting-profile',function(){
            });
        }
        function confirm_delete_single(vote_id)
        {
            var r = confirm(jQuery('#confirm_delete_single').val());
            if (r == true) {
               jQuery("#delete_contestants"+vote_id).submit();
               return true;
            } else {
               return false;
            }
        }
