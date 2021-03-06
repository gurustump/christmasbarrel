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
<!--[if gt IE 9]><!--> <html lang="en" <?php language_attributes(); ?> class="christmasbarrel"> <!--<![endif]-->
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width" />
	
	<title><?php wp_title( '|', true, 'right' ); ?></title>
	
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
	
	<!--[if lt IE 9]>
	<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
	<![endif]-->
	<?php echo is_front_page() ? '<meta property="og:image" content="'.get_stylesheet_directory_uri().'/img/Logoweb2014-fb-v2.jpg" />':''; ?>
	<meta property="og:title" content="<?php wp_title( '|', true, 'right' ); ?>" />
	<?php wp_head(); ?>
	<script>
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

		ga('create', 'UA-6626350-9', 'auto');
		ga('send', 'pageview');

	</script>
</head>
<body <?php body_class(); ?>>

<div id="page" class="hfeed site">
	<?php do_action( 'before' ); ?>

	<header id="header" class="site-header" role="banner">
		<div class="container">






			<?php 
				date_default_timezone_set('Pacific/Honolulu');
				$today = time();
				$thisYear = (int) (date('Y', $today));
				$christmas_day = mktime(0,0,0,12,25,$thisYear);
				$difference = ($christmas_day - $today);
				$days = ceil($difference / 60 /60 / 24);
			?>
			<?php /* <pre><?php echo $today; ?></pre>
			<pre><?php echo $christmas_day; ?></pre>
			<pre><?php echo date('Y M D d g:i', $today); ?></pre>
			<pre><?php echo date('Y M D d g:i', $christmas_day); ?></pre>
			<pre><?php echo $difference; ?></pre>
			<pre><?php echo ceil($difference / 60 / 60 / 24); ?></pre> */ ?>
			<?php if ($christmas_day > $today) { ?>
			<h2 class="days-til">
				<span class="count"><?php echo $days; ?></span>	
				<span class="label">Day<?php echo $days > 1 ? 's' : ''; ?> 'til Christmas</span>
			</h2>
			<?php } else { ?>
			<h2 class="merry-christmas"><span>Merry</span> <span>Christmas!</span></h2>
			<?php } ?>
			<?php echo is_front_page()?'<h1':'<div'; ?> class="header_logo">
				<a href="<?php echo get_bloginfo('url') . '/' ?>">
					<?php /* <img class="header_logo" src="<?php echo get_stylesheet_directory_uri(); ?>/img/Logoweb2014.jpg" alt="A Christmas Barrel" /> */ ?>
				</a>
			<?php echo is_front_page()?'</h1>':'</div>'; ?>



			<a href="#" class="menu-toggle"><i class="icon-menu"></i></a>

			<nav id="menu">
				
				<?php wp_nav_menu( array( 'theme_location' => 'primary-right', 'container' => false, 'menu_class' => 'right' ) ); ?>
			</nav>
		</div>
		<!-- / container -->
	</header>
	<!-- / header -->