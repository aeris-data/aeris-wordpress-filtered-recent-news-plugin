<?php
/**
* Plugin Name: Aeris Widget : Taxonomies list article
* Plugin URI : https://github.com/sedoo/sedoo-wppl-docmanager
* Description: Widget permettant de lister des articles en fonction de leur catégorie
* Author: Samir Boumaza - Pierre VERT
* Version: 1.0.0
* GitHub Plugin URI: aeris-data/aeris-wordpress-filtered-recent-news-plugin
* GitHub Branch:     master
*/

/* Creationd' une classe dérivée de WP_Widget : */
class FilteredNews extends WP_Widget {
	
	// Constructeur
	function FilteredNews() {
		parent::WP_Widget ( false, $name = 'Aeris-Widget-Filtered-News', array (
				'name' => 'Aeris-Widget: liste d\'articles par catégories',
				'description' => 'Affichage des articles par catégorie ' 
		) );
	}
	
	/////////////////////////////////////////////////////////////////////////////////////
	
	function widget($args, $instance) {
		
		extract ( $args );
		
		$displayMode= $instance[ 'displayMode' ] ? 'true' : 'false';
		$title = apply_filters ( 'widget_title', $instance ['title'] );
		$nb_posts = $instance ['nb_posts'];
		$offset = $instance ['offset'];
		
		$categories = get_categories(array(
				'orderby' 		=> 'name',
				'parent'  		=> '',
				'hide_empty' 	=> 1,
				'hierarchical'  => 1,
			));
		
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
				$the_query = new WP_Query( array( 	'category_name' => $CatStrQuery,
											  		'posts_per_page'=> $nb_posts,
												 	'offset'=> $offset));
				$instance['catQuery'] = $CatStrQuery;
			
			endif;}
							
	  	if( 'liste' == $instance[ 'displayMode']) : 
	  		echo "<ul>";
	  	elseif( 'embed' == $instance[ 'displayMode']):
			 echo "<section role='listNews'>";
		elseif( 'embedDetails' == $instance[ 'displayMode']):
			echo "<section role='listNews'>";
		elseif( 'full' == $instance[ 'displayMode']):
			echo "<section role='listNews' class='posts'>"; // class="posts" is needed by masonry
	    endif;
	    
	    if ( $the_query->have_posts() ) {
	    	$url_All = "/index.php?newrecent=true&cat=".$CatStrQuery."&title=".$title;
	    	while ( $the_query->have_posts() ) {
	    		$the_query->the_post();
					$categories = get_the_terms( $post->ID, 'category');
	 			if( 'liste' == $instance[ 'displayMode']) : ?>
	 			             
                	<li>
					    <a href='<?php echo  get_post_permalink($post->ID);?>'> 
						<?php  echo get_the_title($post->ID); ?></a><br>
						<span ><?php echo 'le '. get_the_date('j F Y'); echo ' à '. get_the_time('H').' h '.get_the_time('i');?></span>
					</li> 
 				
				<?php elseif('embed' == $instance[ 'displayMode']): ?>
                	<?php 
					//You might need to create this template in your theme or theme-child, custom it with your own css
					get_template_part( 'template-parts/content', 'embed-post' );  
					?>

				<?php elseif('embedDetails' == $instance[ 'displayMode']): ?>
                	<?php 
					//You might need to create this template in your theme or theme-child, custom it with your own css
					get_template_part( 'template-parts/content', get_post_format() );  
					?>

                <?php elseif('full' == $instance[ 'displayMode']): ?>
                	<div class="post-container">
						<?php 
						// class="post-container" on parent <div> element is needed by masonry
						//You might need to create this template in your theme or theme-child, custom it with your own css. Default is template-parts/content.php
						get_template_part( 'template-parts/content', get_post_format() );  
						?>
                   	</div>				
                <?php endif;
            }
            
      } else {
            // no posts found
            }
            
      if( 'liste' == $instance[ 'displayMode']) : 
      echo "<a href='".get_option('home').$url_All."'>Tout voir <span class='icon-angle-right'></span> </a>";
	  	echo "</ul>";
	  	
	  	
	  else:
		
		echo "</section>";
		echo "<a href='".get_option('home').$url_All."'>Tout voir <span class='icon-angle-right'></span></a>";
	  	
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
		$instance[ 'displayMode' ] = $new_instance[ 'displayMode' ];
		$instance ['nb_posts'] = $new_instance ['nb_posts'];
		$instance[ 'offset' ] = $new_instance[ 'offset' ];
		$categories = get_categories(array(
					 'orderby' => 'name',
				     'parent'  		=> '',
					 'hide_empty' 	=> 1,
					 'hierarchical'  => 1,
					));
		
		foreach ( $categories as $category ) {
			
			$instance[$category->name] = $new_instance[ $category->name];
		}
		
		return $instance;
	}
	
	////////////////////////////////////////////////
	function form($instance) {
		
		
		$title = esc_attr ( $instance ['title'] );
		$displayMode = esc_attr($instance['displayMode']);
		$nb_posts = esc_attr ( $instance ['nb_posts'] );
		$nb_posts = isset ( $instance ['nb_posts'] ) ? absint ( $instance ['nb_posts'] ) :5;
		$offset = esc_attr ( $instance ['offset'] );
		
		//Récupere la liste des catégorie
		$categories = get_categories(array(
		'orderby' => 'name',
		'parent'  		=> '',
		'hide_empty' 	=> 1,
		'hierarchical'  => 1,
		));
		?>

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

		<h3>Catégorie</h3>
			
		<?php foreach ( $categories as $category ) {?>
		
		    <input class="checkbox" type="checkbox" 
		    <?php  checked( $instance[ $category->name], 'on' ); ?> 
		    	  id="<?php echo $this->get_field_id( $category->name); ?>" 
		    	  name="<?php echo $this->get_field_name( $category->name); ?>" 
		    	 /> 
		    	  
		    <label for="<?php echo $this->get_field_id( $category->name); ?>"><?php echo $category->name ?></label><br>

  		<?php } ?>


		<h3>Type d'affichage</h3>

		<p>
			<input class="" id="<?php echo $this->get_field_id('displayMode_list'); ?>" name="<?php echo $this->get_field_name('displayMode'); ?>" type="radio" value="liste" <?php if($displayMode === 'liste'){ echo 'checked="checked"'; } ?> />
			<label for="<?php echo $this->get_field_id('displayMode_list'); ?>">
				<?php _e('Liste simple'); ?>				
			</label>
			<br>

			<input class="" id="<?php echo $this->get_field_id('displayMode_embed'); ?>" name="<?php echo $this->get_field_name('displayMode'); ?>" type="radio" value="embed" <?php if($displayMode === 'embed'){ echo 'checked="checked"'; } ?> />
			<label for="<?php echo $this->get_field_id('displayMode_embed'); ?>">
				<?php _e('Article(s) court(s) intégré(s) sur une colonne'); ?>
			</label>
			<br>

			<input class="" id="<?php echo $this->get_field_id('displayMode_embedDetails'); ?>" name="<?php echo $this->get_field_name('displayMode'); ?>" type="radio" value="embedDetails" <?php if($displayMode === 'embedDetails'){ echo 'checked="checked"'; } ?> />
			<label for="<?php echo $this->get_field_id('displayMode_embedDetails'); ?>">
				<?php _e('Article(s) détaillé()s intégré(s) sur une colonne'); ?>
			</label>
			<br>
			
			<input class="" id="<?php echo $this->get_field_id('displayMode_full'); ?>" name="<?php echo $this->get_field_name('displayMode'); ?>" type="radio" value="full" <?php if($displayMode === 'full'){ echo 'checked="checked"'; } ?> />
			<label for="<?php echo $this->get_field_id('displayMode_full'); ?>">
				<?php _e('Article(s) intégré(s) en "Masonry" (multi-colonnes)'); ?>
			</label>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'offset' ); ?>"><?php _e( 'Offset:' ); ?> 
	        <input style="width: 20%;"
			id="<?php echo $this->get_field_id( 'offset' ); ?>"
			name="<?php echo $this->get_field_name( 'offset' ); ?>"
			type="number" step="1" min="1" value="<?php echo $offset; ?>" /> </label>
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
		
		add_rewrite_tag('%cat%','([^&]+)');
		add_rewrite_tag('%title%','([^&]+)');
	    add_rewrite_rule( 'newrecent/?$', 'index.php?newrecent=true&cat=$matches[1]&title=$matches[2]', 'top' );
	    
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