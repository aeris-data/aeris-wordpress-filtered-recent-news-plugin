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
?>
<?php
if ($_GET['title'] !== "") {
?>

<div id="breadcrumbs">
	<div class="wrapper">
		<?php //if (function_exists('the_breadcrumb')) the_breadcrumb(); ?>
		<span class="current">&nbsp</span>
		<h1 rel="bookmark">
			 <?php echo"" .$_GET['title'];?> &nbsp;
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
			$argsListPost = array(
				'posts_per_page'   => -1,
				'offset'           => 0,
				'category'         => '',
				'category_name'    => ''.$GETcategories.'',
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
		the_posts_navigation();
		?>
		<?php 
			
			wp_reset_postdata();
		?>
	</main><!-- #main -->
</div><!-- #content-area -->

<?php
get_footer();
?>