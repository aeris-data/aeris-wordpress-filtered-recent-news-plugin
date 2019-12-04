<?php
/**
 * The template for displaying archive pages
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package aeris
 */

get_header(); 
$GETcategories = urldecode ($_GET['cat']);
$GETtitle = utf8_decode ($_GET['title']);
?>
<?php
if ($_GET['title'] !== "") {
?>

<div id="breadcrumbs">
	<div class="wrapper">
		<?php //if (function_exists('the_breadcrumb')) the_breadcrumb(); ?>
		<span class="current">&nbsp</span>
		<h1 rel="bookmark">
			 <?php echo"" .$GETtitle;?> &nbsp;
		</h1>
	</div>
</div>

<?php
}
?>

<div id="content-area" class="wrapper archives">
	<main id="main" class="site-main" role="main">

		<section role="listNews" class="posts">
			<?php
			
			global $post;
			if (function_exists('pll_current_language')) {
				$lang = pll_current_language();
			}
			// Hook for pagination on wp_query
			$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
			$argsListPost = array(
				'posts_per_page'   => get_option('posts_per_page'),
				'offset'           => 0,
				'category'         => '',
				'category_name'    => ''.$GETcategories.'',
				'lang'			   => $lang,
				'paged'          	=> $paged,
				'orderby'          => 'date',
				'order'            => 'DESC',
				'post_type'        => 'post',
				'post_status'      => 'publish',
				'suppress_filters' => true 
			);
			
			$the_query = new WP_Query( $argsListPost );

			// Pagination fix
			// $temp_query = $wp_query;
			// $wp_query   = NULL;
			// $wp_query   = $the_query;

			// $postsList = get_posts ($argsListPost);
			
			// foreach ($postsList as $post) :
			// 	setup_postdata( $post );
			while ( $the_query->have_posts() ) :
				$the_query->the_post();
				?>
				<div class="post-container">
				<?php
					get_template_part( 'template-parts/content', get_post_format() );
				?>
				</div>
				<?php
			endwhile;
			
			?>
			
		</section>
		<?php 
		// $test = the_post_navigation();
		// var_dump($test); 
		the_posts_navigation();
		
		wp_reset_postdata();
		?>
		<?php 
			// previous_posts_link( 'Older Posts' );
			// next_posts_link( 'Newer Posts', $the_query->max_num_pages );

			// // Reset main query object
			// $wp_query = NULL;
			// $wp_query = $temp_query;
			
		?>
	</main><!-- #main -->
</div><!-- #content-area -->

<?php
get_footer();
?>