<?php
/**
 * @package WordPress
 * @subpackage HTML5-Reset-WordPress-Theme
 * @since HTML5 Reset 2.0
 */
?>
 <div id="sidebar">

    <?php if (!function_exists('dynamic_sidebar') && !dynamic_sidebar('Sidebar Widgets')) : else : ?>
    
        <!-- All this stuff in here only shows up if you DON'T have any widgets active in this zone -->

    	<?php /* get_search_form(); */ ?>
    	
    	<!-- <h2><?php _e('Subscribe','html5reset'); ?></h2>
    	<ul>
    		<li><a href="<?php bloginfo('rss2_url'); ?>"><?php _e('Entries (RSS)','html5reset'); ?></a></li>
    		<li><a href="<?php bloginfo('comments_rss2_url'); ?>"><?php _e('Comments (RSS)','html5reset'); ?></a></li>
    	</ul>-->
		

		<script type="text/javascript">
		/*<![CDATA[*/
		jQuery(document).ready(function() {jQuery(".noopslikebox").hover(function() {jQuery(this).stop().animate({right: "0"}, "medium");}, function() {jQuery(this).stop().animate({right: "-250"}, "medium");}, 500);});
		/*]]>*/
		</script>
		<style type="text/css">
		.noopslikebox{background: url("https://internationalmedicalcorps.org/image/social-networking-/-blogs/Vertical_Facebook.gif") no-repeat scroll left center transparent !important;display: block;float: right;height: 600px;padding: 0 5px 0 46px;width: 245px;z-index: 999;position:fixed;right:-250px;top:15%;}
		.noopslikebox div{border:none;position:relative;display:block;}
		.noopslikebox span{bottom: 12px;font: 8px "lucida grande",tahoma,verdana,arial,sans-serif;position: absolute;right: 7px;text-align: right;z-index: 999;}
		.noopslikebox span a{color: gray;text-decoration:none;}
		.noopslikebox span a:hover{text-decoration:underline;}
		</style>
		<div class="noopslikebox">
			<div>
			<iframe src="https://www.facebook.com/plugins/likebox.php?href=https://www.facebook.com/ChristmasBarrel/1380481815520448;width=245&amp;colorscheme=light&amp;show_faces=true&amp;connections=8&amp;stream=false&amp;header=false&amp;height=270" scrolling="no" frameborder="0" scrolling="no" style="border: medium none; overflow: hidden; height: 270px; width: 245px;background:#fff;"></iframe>

		<a class="twitter-timeline"  href="https://twitter.com/ChristmasBarrel"  data-widget-id="391679478721556480">Tweets by @ChristmasBarrel</a>
		<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>




			</div>
		</div>
	
	<?php endif; ?>

</div>