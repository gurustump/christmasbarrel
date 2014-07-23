<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package Fundify
 * @since Fundify 1.0
 */
?><!DOCTYPE html>
<!--[if IE 7]> <html class="ie7 oldie" lang="en" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8]> <html class="ie8 oldie" lang="en" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 9]> <html class="ie9" lang="en" <?php language_attributes(); ?>> <![endif]-->
<!--[if gt IE 9]><!--> <html lang="en" <?php language_attributes(); ?>> <!--<![endif]-->
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width" />
	
	<title><?php wp_title( '|', true, 'right' ); ?></title>
	
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
	
	<!--[if lt IE 9]>
	<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
	<![endif]-->

	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<div id="page" class="hfeed site">
	<?php do_action( 'before' ); ?>

	<header id="header" class="site-header" role="banner">
		<div class="container">






			<?php 
				date_default_timezone_set('Pacific/Honolulu');
				$christmas_day = mktime(0,0,0,12,25,2014);
				$today = time();
				$difference = ($christmas_day - $today);
				$days = (int) ($difference / 86400) + 1;
			?>
			
			<h2 class="days-til">
				<span class="count"><?php echo $days; ?></span><br>
				Days 'til Christmas 
			</h2>
<a href="http://christmasbarrel.com/"><img  class="header_logo" src="http://rightstuff.us/Barrel2014/wp-content/uploads/2014/07/Logoweb2014.jpg"></a>



			<a href="#" class="menu-toggle"><i class="icon-menu"></i></a>

			<nav id="menu">
				
				<?php wp_nav_menu( array( 'theme_location' => 'primary-right', 'container' => false, 'menu_class' => 'right' ) ); ?>
			</nav>
		</div>
		<!-- / container -->
	</header>
	<!-- / header -->