
<?php
/*

Plugin Name: Aeris-Widget-Filtered-News
GitHub Plugin URI:
Version: 0.0.6

*/

/* Creationd' une classe dérivée de WP_Widget : */
class FilteredNews extends WP_Widget {
	
	// Constructeur
	function FilteredNews() {
		parent::WP_Widget ( false, $name = 'Aeris-Widget-Filtered-News', array (
				'name' => 'Aeris-Widget-Filtered-News',
				'description' => 'Affichage des news recentes par catégorie ' 
		) );
	}
	
	/////////////////////////////////////////////////////////////////////////////////////
	
	function widget($args, $instance) {
		
		extract ( $args );
		
		$displayMode= $instance[ 'displayMode' ] ? 'true' : 'false';
		$title = apply_filters ( 'widget_title', $instance ['title'] );
		$nb_posts = $instance ['nb_posts'];
		
		$categories = get_categories(array(
				'orderby' => 'date',
				'parent'  => 0));
		
		foreach ( $categories as $category ) {
			
			$CatArray[$category->name] = $instance[ $category->name] ? 'true' : 'false';
		}

		echo $before_widget;
		if ($title)
			echo $before_title . $title . $after_title;
		else
			echo $before_title . 'Résultats filtré' . $after_title;
		
		$CatStrQuery='';

		// Retrieve the checkbox
		foreach ( $categories as $category) {
		
			
			if( 'on' == $instance[ $category->name]) :  
				$CatStrQuery != "" && $CatStrQuery .= ",";
    			$CatStrQuery .= $category->name;
				$the_query = new WP_Query( array( 'category_name' => $CatStrQuery,
											  'posts_per_page'=> $nb_posts ));
				$instance['catQuery'] = $CatStrQuery;
			
			endif;}
							
	  	if( 'on' == $instance[ 'displayMode']) : 
	  		echo "<ul>";
	  	else:
	 		echo "<section role='listNews' class='posts'>";
	    endif;
	    
	    if ( $the_query->have_posts() ) {
	    	$url_All = "/index.php?newrecent=true&cat=".$CatStrQuery."&title=".$title;
	    	while ( $the_query->have_posts() ) {
	    		$the_query->the_post();
					$categories = get_the_terms( $post->ID, 'category');
	 			if( 'on' == $instance[ 'displayMode']) : ?>
	 			             
                	<li>
					    <a href='<?php echo  get_post_permalink($post->ID);?>'> 
						<?php  echo get_the_title($post->ID); ?></a><br>
						<span ><?php echo 'le '. get_the_date('j F Y'); echo ' à '. get_the_time('H').' h '.get_the_time('i');?></span>
					</li> 
 				
                <?php else: ?>
                
              		<div class="post-container">
                   		<?php get_template_part( 'template-parts/content', get_post_format() ); ?>
                   	</div>
              
                <?php endif;
            }
            
      } else {
            // no posts found
            }
            
      if( 'on' == $instance[ 'displayMode']) : 
      echo "<a href='".get_option('home').$url_All."'>Tout voir <span class='icon-angle-right'></span> </a>";
	  	echo "</ul>";
	  	
	  	
	  else:
	  echo "<a href='".get_option('home').$url_All."'>Tout voir <span class='icon-angle-right'></span></a>";
	  	echo "</section>";
	  
	  endif;
	 ?>
          
    
     <div class="textwidget">
     	<p><?php echo esc_attr( $text ); ?></p>
     </div>
        
<?php /* Restore original Post Data */
	 wp_reset_postdata();
	
	echo $after_widget;}
	
	////////////////////////////////////////////////////////////
	
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		
		$instance ['title'] = strip_tags ( $new_instance ['title'] );
		$instance ['nb_posts'] = $new_instance ['nb_posts'];
		$instance[ 'displayMode' ] = $new_instance[ 'displayMode' ];
		$categories = get_categories(array(
					 'orderby' => 'name',
				     'parent'  => 0));
		
		foreach ( $categories as $category ) {
			
			$instance[$category->name] = $new_instance[ $category->name];
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
		'parent'  => 0));?>

		<h3>catégorie</h3>
			
		<?php foreach ( $categories as $category ) {?>
		
		    <input class="checkbox" type="checkbox" 
		    <?php  checked( $instance[ $category->name], 'on' ); ?> 
		    	  id="<?php echo $this->get_field_id( $category->name); ?>" 
		    	  name="<?php echo $this->get_field_name( $category->name); ?>" 
		    	 /> 
		    	  
		    <label for="<?php echo $this->get_field_id( $category->name); ?>"><?php echo $category->name ?></label><br>

  		<?php } ?>


		<h3>Type d'affichage</h3>

	    <input class="checkbox" type="checkbox" 
	    <?php  checked( $instance[ 'displayMode'], 'on' ); ?> 
	    	  id="<?php echo $this->get_field_id( 'displayMode'); ?>" 
	    	  name="<?php echo $this->get_field_name( 'displayMode'); ?>" 
	    	 /> 
	    	  
	    <label for="<?php echo $this->get_field_id( 'displayMode'); ?>"><?php echo "Liste" ?></label><br>
		
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">
	        	<?php echo 'Titre:'; ?>
	        <input 
	        class="widefat"
			id="<?php echo $this->get_field_id('title'); ?>"
			name="<?php echo $this->get_field_name('title'); ?>" type="text"
			value="<?php echo $title; ?>" />
		   </label>
		</p>
		
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
	function register_FilteredNews_widget() {
		register_widget ( 'FilteredNews' );
	}
	add_action ('widgets_init','register_FilteredNews_widget');
	
	function fontawesome_widget_recentNews() {
		wp_enqueue_style ( 'fontawesome', 'http://netdna.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.css', '', '4.5.0', 'all' );
	}
	
	add_action ( 'admin_init','fontawesome_widget_recentNews');
	add_action ( 'admin_init','fontawesome_widget_recentNews');
	
	
	add_action('init', 'flitred_news_rewrite_rules');
	add_filter( 'query_vars', 'flitred_news_query_var' );
	add_filter('template_include', 'flitred_news_template_include', 1, 1); 
	
	
	function flitred_news_rewrite_rules() {
		global $wp_rewrite;
		add_rewrite_tag('%cat%','([^&]+)');
		add_rewrite_tag('%title%','([^&]+)');
	    add_rewrite_rule( 'newrecent/?$', 'index.php?newrecent=true&cat=$matches[1]&title=$matches[2]', 'top' );
	    $wp_rewrite->flush_rules();
	}
	
	function flitred_news_query_var( $vars ) {
	    $vars[] = 'newrecent';
	    $vars[] = 'cat';
	    $vars[] = 'title';
	    return $vars;
	}
	
	function flitred_news_template_include($template){
	    global $wp_query; 
	    $page_value = $wp_query->query_vars['newrecent']; 
	
	    if ($page_value && $page_value == "true") { 
	        return plugin_dir_path(__FILE__).'allnewsrecent.php'; 
	    }
	
	    return $template; 
	}
	?>