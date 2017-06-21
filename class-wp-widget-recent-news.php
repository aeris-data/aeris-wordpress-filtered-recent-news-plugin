
<?php
/*

Plugin Name: Aeris-Widget-News-Recent
GitHub Plugin URI:
Version: 0.0.1

*/

/* Creationd' une classe dérivée de WP_Widget : */
class NewsRecent extends WP_Widget {
	
	// Constructeur
	function NewsRecent() {
		parent::WP_Widget ( false, $name = 'Aeris-Widget-News-Recent', array (
				'name' => 'Aeris-Widget-News-Recent',
				'description' => 'Affichage des news recentes par catégorie ou tag' 
		) );
	}
	
	 
	
	/////////////////////////////////////////////////////////////////////////////////////
	function widget($args, $instance) {
		
		extract ( $args );
		
		$tutu = $instance[ 'tutu' ] ? 'true' : 'false';
		
		$title = apply_filters ( 'widget_title', $instance ['title'] );
		$nb_posts = $instance ['nb_posts'];
		
		$categories = get_categories(array(
				'orderby' => 'date',
				'parent'  => 0
		) );
		
		foreach ( $categories as $category ) {
			
			$CatArray[$category->name] = $instance[ $category->name] ? 'true' : 'false';
		}
		
		$tags = get_tags();
		
		foreach ( $tags as $tag) {
			
			$TagArray[$tag->name] = $instance[$tag->name] ? 'true' : 'false';
		}
		
		
		
		$lastposts = get_posts ( array (
				'numberposts' => $nb_posts,
				
				//'category_name'    => 'car',
				
				//'post_type' => 'campaign',
				//'meta_type' => 'DATE',
				//'orderby' => 'meta_value',
				//'meta_key' => 'campaign_date_start',
				'post__not_in' => array (
						get_the_ID () 
				) 
		) );
		
		?>
		
		<style>
		aside ul li::before {
			content: none;
		}
		</style>
		<?php 
		echo $before_widget;
		if ($title)
			echo $before_title . $title . $after_title;
		else
			echo $before_title . 'News Récentes' . $after_title;
		
		echo "<ul style= 'padding-left:0px;!important' >";
		
		
		
		$the_query = new WP_Query( 'tag=toto' );
		
		if ( $the_query->have_posts() ) {
			echo '<ul>';
			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				echo '<li> ' . get_the_title() . '</li>';
			}
			echo '</ul>';
		} else {
			// no posts found
		}
		
		
		foreach ( $tags as $tag) {
			
			if( 'on' == $instance[ $tag->name]) :  echo $tag->name;?>
           
        <?php endif; 
		}
		
		
           
        
		
		$num = 	0;
		$CatStrQuery='';

		// Retrieve the checkbox
		foreach ( $categories as $category) {
		
			
			if( 'on' == $instance[ $category->name]) :  
				 $CatStrQuery != "" && $CatStrQuery .= ",";
    			$CatStrQuery .= $category->name;
				$query = new WP_Query( array( 'category_name' => $CatStrQuery,
												'posts_per_page'=> $nb_posts
				) );
				

				
				$lastposts = get_posts ( array (
						
						'numberposts' 		=> $nb_posts-$num,
						'category_name'    	=> $category->name,
						'orderby'			=> 'date',
				
			));
			endif;}
			
			if ( $query->have_posts() ) {
			echo '<ul>';
			while ( $query->have_posts() ) {
				$query->the_post();
				//echo '<li> ' . get_the_title() . get_post_permalink($post->ID).'</li>';?>
				<li style ="border-bottom: 1px solid #eee;">
				<a href='<?php echo  get_post_permalink($post->ID);?>'> 
				<?php echo get_the_title($post->ID); ?></a><br>
				<span style ="font-size:10px;" ><?php echo 'le '. get_the_date('j F Y'); echo ' à '. get_the_time('H').' h '.get_the_time('i');?></span>
				</li>
				
				<?php
			}
			echo '</ul>';
					} else {
			// no posts found
			}
				if (($nb_posts-$num != 0))	:
				foreach ( $lastposts as $post ) { ?>
					
				
		           
					<?php 
					
					$num++;};
					endif;
					?>
           
       
        
        

        <div class="textwidget">
            <p><?php echo esc_attr( $text ); ?></p>
        </div>
        <?php 
		/* Restore original Post Data */
		wp_reset_postdata();
		
		echo $after_widget;
	
	}
	
	////////////////////////////////////////////////////////////
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		
		// Récupération des paramètres envoyés
		$instance ['title'] = strip_tags ( $new_instance ['title'] );
		$instance ['nb_posts'] = $new_instance ['nb_posts'];
		$instance[ 'tutu' ] = $new_instance[ 'tutu' ];
		
		$categories = get_categories(array(
				'orderby' => 'name',
				'parent'  => 0
		) );
		
		foreach ( $categories as $category ) {
			
			$instance[$category->name] = $new_instance[ $category->name];
		}
		
		$tags = get_tags();
		foreach ( $tags as $tag) {
			 
			$tag->name; $instance[$tag->name] = $new_instance[ $tag->name];
		}
		
		return $instance;
	}
	
	////////////////////////////////////////////////
	function form($instance) {
		
		
		$title = esc_attr ( $instance ['title'] );
		$nb_posts = esc_attr ( $instance ['nb_posts'] );
		$nb_posts = isset ( $instance ['nb_posts'] ) ? absint ( $instance ['nb_posts'] ) :5;
		
		
		
		//Récupere la liste des catégorie
		$categories = get_categories(array(
		'orderby' => 'name',
		'parent'  => 0
		) );
		//Récupere la liste des tags 
		$tags = get_tags();
		
		?>

		<h3>catégorie</h3>
		<?php 
	foreach ( $categories as $category ) {?>
		

    <input class="checkbox" type="checkbox" 
    <?php  checked( $instance[ $category->name], 'on' ); ?> 
    	  id="<?php echo $this->get_field_id( $category->name); ?>" 
    	  name="<?php echo $this->get_field_name( $category->name); ?>" 
    	 /> 
    	  
    <label for="<?php echo $this->get_field_id( $category->name); ?>"><?php echo $category->name ?></label><br>

		
		<?php 	
		}?>
		
		<h3>tag</h3>
		<?php 
	foreach ( $tags as $tag) {?>
		

    <input class="checkbox" type="checkbox" 
    <?php checked( $instance[ $tag->name], 'on' ); ?> 
    	  id="<?php echo $this->get_field_id( $tag->name); ?>" 
    	  name="<?php echo $this->get_field_name($tag->name); ?>" /> 
    	  
    <label for="<?php echo $this->get_field_id( $tag->name); ?>"><?php echo $tag->name?></label><br>

		
		<?php 	
		}
		
		?>
		
		
	
		
<p>
	<label for="<?php echo $this->get_field_id('title'); ?>">
                <?php echo 'Titre:'; ?>
                <input class="widefat"
		id="<?php echo $this->get_field_id('title'); ?>"
		name="<?php echo $this->get_field_name('title'); ?>" type="text"
		value="<?php echo $title; ?>" />
	</label>
</p>
<p>
<p>
	<label for="<?php echo $this->get_field_id( 'nb_posts' ); ?>"><?php _e( 'Number of posts to show:' ); ?> 
            	<input style="width: 20%;"
		id="<?php echo $this->get_field_id( 'nb_posts' ); ?>"
		name="<?php echo $this->get_field_name( 'nb_posts' ); ?>"
		type="number" step="1" min="1" value="<?php echo $nb_posts; ?>" /> </label>

</p>


<?php
	}
}




/////////////////////////////////////////////////////////////////////////
function register_recentNews_widget() {
	register_widget ( 'NewsRecent' );
}
add_action ('widgets_init','register_recentNews_widget');


function fontawesome_widget_recentNews() {
	wp_enqueue_style ( 'fontawesome', 'http://netdna.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.css', '', '4.5.0', 'all' );
}

add_action ( 'admin_init','fontawesome_widget_recentNews');
?>