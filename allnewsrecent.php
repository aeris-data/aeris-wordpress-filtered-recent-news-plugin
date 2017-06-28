<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">

<?php wp_head(); ?>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.2/css/font-awesome.min.css">
</head>

<body <?php body_class(); ?> data-color="<?php echo get_theme_mod( 'theme_aeris_main_color' );?>">
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'theme-aeris' ); ?></a>

	<header id="masterhead" class="site-header" role="banner">
		<?php 
			/***
			* init var header
			*/

			// logo
			$custom_logo_id = get_theme_mod( 'custom_logo' );
			$image = wp_get_attachment_image_src( $custom_logo_id , 'full' ); 

			// Description (slogan)
			$description = get_bloginfo( 'description', 'display' );

		?>
<div class="wrapper">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
				<img src="<?php echo $image[0];?>" alt="<?php bloginfo( 'name' ); ?>" title="<?php bloginfo( 'name' ); ?> : <?php echo $description;?>">
			</a>
			<div>
				<nav id="site-navigation" class="main-navigation" role="navigation" aria-label="Menu principal / Main menu">
					<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false"><?php esc_html_e( 'Primary Menu', 'theme-aeris' ); ?></button>
					<?php wp_nav_menu( array( 'theme_location' => 'menu-1', 'menu_id' => 'primary-menu' ) ); ?>
				</nav>
				
				<nav id="top-header-menu" role="navigation" aria-label="Menu secondaire / Second menu">
					<?php wp_nav_menu( array( 'theme_location' => 'header-menu', 'menu_id' => 'header-menu' ) ); ?>
				</nav>
			</div>
			
		</div>
	</header>
	
	
<div id="breadcrumbs">
	<div class="wrapper">
		<?php if (function_exists('the_breadcrumb')) the_breadcrumb(); ?>
		<span class="current">&nbsp</span>
		<h1 style="">
			 <?php echo"Widget : " .$_GET['title'];?> 
		</h1>
		
	</div>
</div>

	<div id="content-area" class="wrapper">
		<main id="main" class="site-main" role="main">

		
			<section role="listNews" class="posts">
               <?php
               
				global $post;
				$argsListPost = array(
					'posts_per_page'   => -1,
					'offset'           => 0,
					'category'         => '',
					'category_name'    => $_GET['cat'],
					'orderby'          => 'date',
					'order'            => 'DESC',
					'include'          => '',
					'exclude'          => '',
					'meta_key'         => '',
					'meta_value'       => '',
					'post_type'        => 'post',
					'post_mime_type'   => '',
					'post_parent'      => '',
					'author'		   => '',
					'author_name'	   => '',
					'post_status'      => 'publish',
					'suppress_filters' => true 
				);

				$postsList = get_posts ($argsListPost);
 				
				foreach ($postsList as $post) :
  				  setup_postdata( $post );
					?>
					<div class="post-container">
					<?php
					get_template_part( 'template-parts/content', get_post_format() );
					?>
					</div>
					<?php
				endforeach;
               
                ?>
				
            </section>
			<?php 
				
				wp_reset_postdata();
			?>
		</main><!-- #main -->
		<?php 
		//get_sidebar();
		?>
	</div><!-- #content-area -->

<?php

get_footer();


