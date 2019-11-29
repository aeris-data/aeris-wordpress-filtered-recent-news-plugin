<?php
/**
* Plugin Name: Aeris Widget : Taxonomies list article
* Plugin URI : https://github.com/aeris-data/aeris-wordpress-filtered-recent-news-plugin
* Text Domain: aeris-wppl-filtered-news
* Domain Path: /languages
* Description: List post using categories filters
* Author: Samir Boumaza - Pierre VERT
* Version: 1.2.2
* GitHub Plugin URI: aeris-data/aeris-wordpress-filtered-recent-news-plugin
* GitHub Branch:     master
*/

/* 
* LOAD TEXT DOMAIN FOR TRANSLATION
*/

function aeris_wppl_filtered_news_load_plugin_textdomain() {
	$domain = 'aeris-wppl-filtered-news';
	$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
	// wp-content/languages/plugin-name/plugin-name-fr_FR.mo
	load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
	// wp-content/plugins/plugin-name/languages/plugin-name-fr_FR.mo
	load_plugin_textdomain( $domain, FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'init', 'aeris_wppl_filtered_news_load_plugin_textdomain' );

/* Creation d'une classe dérivée de WP_Widget : */
class FilteredNews extends WP_Widget {
	
	// Constructeur
	function FilteredNews() {
		parent::WP_Widget ( false, $name = 'Aeris-Widget-Filtered-News', array (
				'name' => 'Aeris Widget : Taxonomies list article',
				'description' => 'List post using categories filters' 
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
		if (($title) && ('on' == $instance[ 'displayTitle']))
			echo $before_title . $title . $after_title;
		
		$CatStrQuery='';

		if ($categories) {
			// Retrieve the checkbox
			$i=0; //count the checked checkbox
			foreach ( $categories as $category) {			

				if( 'on' == $instance[ $category->slug]) {
					$CatStrQuery != "" && $CatStrQuery .= ",";
					$CatStrQuery .= $category->slug;
					$the_query = new WP_Query( array( 	'category_name' => $CatStrQuery,
														'posts_per_page'=> $nb_posts,
														'offset'=> $offset));
					$instance['catQuery'] = $CatStrQuery;
					$i++;
				}
			}
		}
		// If no checkbox, all categories by default
		if ($i == 0) {
			$the_query = new WP_Query( array( 	'post_type' => 'post',
												'posts_per_page'=> $nb_posts,
												'offset'=> $offset));
		}  			

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
			$CatStrQueryURL = urlencode($CatStrQuery);
			$titleURL = utf8_encode($title);
			if (function_exists('pll_current_language')) {
				$lang = pll_current_language();
				$url_All = "/".pll_current_language()."/?newrecent=true&cat=".$CatStrQueryURL."&title=".$titleURL;
				
			} else {
				$url_All = "/?newrecent=true&cat=".$CatStrQueryURL."&title=".$titleURL;
			}
			
			
	    	while ( $the_query->have_posts() ) {
	    		$the_query->the_post();
				$categories = get_the_terms( $post->ID, 'category');
	 			if( 'liste' == $instance[ 'displayMode']) : 
					 $titleItem=mb_strimwidth(get_the_title($post->ID), 0, 50, '...');
					 ?>
                	<li>
					    <a href='<?php echo  get_post_permalink($post->ID);?>'> 
						<?php  echo $titleItem; ?></a><br>
						<small ><?php echo get_the_date('Y/m/d');?></small>
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
            
      if( ( 'liste' == $instance[ 'displayMode']) && ($nb_posts > 1)) : 
      echo "<a href=\"".get_option('home').$url_All."\" class=\"Aeris-seeAllButton\">".esc_html__('See all', 'aeris-wppl-filtered-news') ." <span class='icon-angle-right'></span> </a>";
	  	echo "</ul>";
	  	
	  elseif ( $nb_posts > 1 ):
		
		echo "</section>";
		echo "<a href='".get_option('home').$url_All."' class=\"Aeris-seeAllButton\">".esc_html__('See all', 'aeris-wppl-filtered-news') ." <span class='icon-angle-right'></span></a>";

	  else:
		echo "</section>";	
		
	  endif;
	 ?>

        
<?php /* Restore original Post Data */
	wp_reset_postdata();
	
	echo $after_widget;
	}
	
	////////////////////////////////////////////////////////////
	
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		
		$instance ['title'] = strip_tags ( $new_instance ['title'] );
		$instance[ 'displayTitle' ] = $new_instance[ 'displayTitle' ];
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
			
			$instance[$category->slug] = $new_instance[ $category->slug];
		}
		
		return $instance;
	}
	
	////////////////////////////////////////////////
	function form($instance) {
		
		
		$title = esc_attr ( $instance ['title'] );
		$displayTitle = esc_attr($instance['displayTitle']);
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
	        	<?php esc_html_e('Title', 'aeris-wppl-filtered-news'); ?>
	        <input 
	        class="widefat"
			id="<?php echo $this->get_field_id('title'); ?>"
			name="<?php echo $this->get_field_name('title'); ?>" type="text"
			value="<?php echo $title; ?>" />
		   </label>
		</p>	
		<p>
			<input class="checkbox" type="checkbox" 
			<?php  checked( $instance[ 'displayTitle'], 'on' ); ?> 
				id="<?php echo $this->get_field_id( 'displayTitle'); ?>" 
				name="<?php echo $this->get_field_name( 'displayTitle'); ?>" 
				/> 
				
			<label for="<?php echo $this->get_field_id( 'displayTitle'); ?>"><?php esc_html_e('Show title', 'aeris-wppl-filtered-news'); ?></label>
		</p>
		<hr>
		<h3><?php esc_html_e('Categories', 'aeris-wppl-filtered-news'); ?></h3>
			<p style="padding:3px 5px;border:1px solid #1F7E9E;border-radius:5px;color:#1F7E9E;font-style:italic;text-align:center"><?php esc_html_e('All categories selected by default if nothing checked', 'aeris-wppl-filtered-news');?></p>
			<hr>
		<?php foreach ( $categories as $category ) {?>
		    <input class="checkbox" type="checkbox" 
			<?php  
			checked( $instance[ $category->slug], 'on' ); ?> 
		    	  id="<?php echo $this->get_field_id( $category->slug); ?>" 
		    	  name="<?php echo $this->get_field_name( $category->slug); ?>" 
		    	 /> 
					     
		    <label for="<?php echo $this->get_field_id( $category->slug); ?>"><?php echo $category->name ?></label>
			<hr>
  		<?php } ?>

		<h3><?php esc_html_e('Layout', 'aeris-wppl-filtered-news'); ?></h3>

		<p>
			<input class="" id="<?php echo $this->get_field_id('displayMode_list'); ?>" name="<?php echo $this->get_field_name('displayMode'); ?>" type="radio" value="liste" <?php if($displayMode === 'liste'){ echo 'checked="checked"'; } ?> />
			<label for="<?php echo $this->get_field_id('displayMode_list'); ?>">
				<?php esc_html_e('Simple list', 'aeris-wppl-filtered-news'); ?>				
			</label>
			<br>

			<input class="" id="<?php echo $this->get_field_id('displayMode_embed'); ?>" name="<?php echo $this->get_field_name('displayMode'); ?>" type="radio" value="embed" <?php if($displayMode === 'embed'){ echo 'checked="checked"'; } ?> />
			<label for="<?php echo $this->get_field_id('displayMode_embed'); ?>">
				<?php esc_html_e('Short article(s) embed - 1 column', 'aeris-wppl-filtered-news'); ?>
			</label>
			<br>

			<input class="" id="<?php echo $this->get_field_id('displayMode_embedDetails'); ?>" name="<?php echo $this->get_field_name('displayMode'); ?>" type="radio" value="embedDetails" <?php if($displayMode === 'embedDetails'){ echo 'checked="checked"'; } ?> />
			<label for="<?php echo $this->get_field_id('displayMode_embedDetails'); ?>">
				<?php esc_html_e('Detailed article(s) embed - 1 column', 'aeris-wppl-filtered-news'); ?>
			</label>
			<br>
			
			<input class="" id="<?php echo $this->get_field_id('displayMode_full'); ?>" name="<?php echo $this->get_field_name('displayMode'); ?>" type="radio" value="full" <?php if($displayMode === 'full'){ echo 'checked="checked"'; } ?> />
			<label for="<?php echo $this->get_field_id('displayMode_full'); ?>">
				<?php esc_html_e('Detailed article(s) embed - multi columns (Masonry)', 'aeris-wppl-filtered-news'); ?>
			</label>
		</p>
		<hr>
		<p>
			<label for="<?php echo $this->get_field_id( 'offset' ); ?>"><?php esc_html_e( 'Offset:' ); ?> 
	        <input style="width: 20%;"
			id="<?php echo $this->get_field_id( 'offset' ); ?>"
			name="<?php echo $this->get_field_name( 'offset' ); ?>"
			type="number" step="1" min="0" value="<?php echo $offset; ?>" /> </label>
	   </p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'nb_posts' ); ?>"><?php esc_html_e( 'Number of posts to show:' ); ?> 
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