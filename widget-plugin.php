<?php
/*
Plugin Name: Widget Plugin
Plugin URI: http://jsrwebsolutions.com
Description: A simple plugin that adds a simple widget
Version: 1.0
Author: Desiree Anne Q. Banua
Author URI: http://www.google.com/
*/

function start_session()
{
  session_start();
}
 
add_action('init', 'start_session', 1);

class wp_my_plugin extends WP_Widget {

// constructor
function wp_my_plugin() {
  parent::WP_Widget(false, $name = __('Search Listings', 'wp_widget_plugin') );
}

// widget form creation
function form($instance) {

// Check values
if( $instance) {

     $title = esc_attr($instance['title']);
     $text = esc_attr($instance['text']);
     $textarea = esc_textarea($instance['textarea']);

} else {

     $title = '';
     $text = '';
     $textarea = '';
}

?>

<p>
<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget Title', 'wp_widget_plugin'); ?></label>
<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
</p>

<p>
<label for="<?php echo $this->get_field_id('text'); ?>"><?php _e('Text:', 'wp_widget_plugin'); ?></label>
<input class="widefat" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>" type="text" value="<?php echo $text; ?>" />
</p>

<p>
<label for="<?php echo $this->get_field_id('textarea'); ?>"><?php _e('Textarea:', 'wp_widget_plugin'); ?></label>
<textarea class="widefat" id="<?php echo $this->get_field_id('textarea'); ?>" name="<?php echo $this->get_field_name('textarea'); ?>"><?php echo $textarea; ?></textarea>
</p>

<?php
}

// update widget
function update($new_instance, $old_instance) {
      $instance = $old_instance;
      // Fields
      $instance['title'] = strip_tags($new_instance['title']);
      $instance['text'] = strip_tags($new_instance['text']);
      $instance['textarea'] = strip_tags($new_instance['textarea']);
     return $instance;
}

// display widget
function widget($args, $instance) {

   extract( $args );
   // these are the widget options
   $title = apply_filters('widget_title', $instance['title']);
   $text = $instance['text'];
   $textarea = $instance['textarea'];
   echo $before_widget;
   // Display the widget
   echo '<div class="widget-text wp_widget_plugin_box">';

   // Check if title is set
   if ( $title ) {
      echo $before_title . $title . $after_title;
   }

   // Check if text is set
   if( $text ) {
      echo '<p class="wp_widget_plugin_text">'.$text.'</p>';
   }
   // Check if textarea is set
   if( $textarea ) {
     echo '<p class="wp_widget_plugin_textarea">'.$textarea.'</p>';
   }
   echo '</div>';
   echo $after_widget;

    $url = $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
    $tax = explode('/', $url);

    $current_tax = $tax[2];


    $location = get_terms('location');
	$category = get_terms('listing-category');


	foreach( $category as $key=>$category ) {
		$category_slug['slug'][$key] = $category->slug;
		$category_slug['name'][$key] = $category->name;
	}
	
	foreach($location as $key=>$location) {

		wp_reset_query();

		if( isset(  $_SESSION['location-listing-cat'] ) ) :

			if( $location->slug ==  $_SESSION['location-listing-cat'] ) :
				$style = "display:block;";
			else :
				$style = "display:none;";
			endif;

		else :
			$style = "display:none;";
		endif;


		echo '<a data-index="'.$key.'" class="listing-location" style= "display:block;" data-value="'.$location->slug.'"><h6>'.$location->name.'</h6></a><ul id="location'.$key.'" style = "'.$style.'" class="category">';
		$cat_array = array();
		foreach( $category_slug['slug'] as $key=>$slug  ) :
			
			if( isset( $_SESSION['location-listing-cat'] ) && $location->slug == $_SESSION['location-listing-cat'] && $current_tax == $slug  ) :

				if( isset( $_SESSION['active'] ) ) :
					$activate = " active";
				else :
					$activate = "";
				endif;
			
			else :
				$activate = "";
			endif;

			$args = array('post_type' => 'listing',
				'relation' => 'AND',
			    'tax_query' => array(
			        array(
			            'taxonomy' => 'location',
			            'field' => 'slug',
			            'terms' => $location->slug,
			        ),
			        array(
			            'taxonomy' => 'listing-category',
			            'field' => 'slug',
			            'terms' => $slug,
			        ),
			    ),
			 );
			


			$loop = new WP_Query( $args, 'category' );

			if($loop->have_posts()) {
			
				$count = $loop->found_posts; 
				$path = get_bloginfo('url');
				echo $term_slug;
				echo '<a data-index="'.$key.'" href="'.$path.'/listing-category/'.$slug.'/" class = "categorylist'.$activate.'" style= "display:block;">'.$category_slug['name'][$key].' ('.$count.')</a>';
			}

		endforeach;
	
		echo '</ul>';
	
	}
?>
<?php
	
}

}
 
// register widget
add_action('widgets_init', create_function('', 'return register_widget("wp_my_plugin");'));

add_action("widgets_init", "listing_init");

function listing_init() {

	define( "LISTING_NAVIGATION_URL",  plugins_url() . '/widgets-plugin/');
	// Add scripts and styles to the front end
	add_action('wp_enqueue_scripts', 'listing_nav_enqueue_scripts');
}

function listing_nav_enqueue_scripts() {

	wp_enqueue_script( "widgets-plugin", LISTING_NAVIGATION_URL . "script.js", array("jquery") );	

}


function home_listing() {

	$location = get_terms('location');
	$category = get_terms('listing-category');

	foreach( $category as $key=>$category ) {
		$category_slug['slug'][$key] = $category->slug;
		$category_slug['name'][$key] = $category->name;
	}
?>
	<ul class = "tabs" >
	<?php
		foreach($location as $key=>$location) :

			wp_reset_query();
?>
			<li><a href = "#tab<?php echo $key; ?>" class="listing-location-home"  data-index="<?php echo $key; ?>" data-value="<?php echo $location->slug; ?>"><?php echo $location->name; ?></a></li>
<?php		
			$location_list[] = $location->slug;

		endforeach;
?>
	</ul>
<?php
	foreach( $location_list as $key=>$location ) :
		
		$cat_array = array();
?>
		<div class="tab">
			<div id="tab<?php echo $key; ?>" class = "tab-content" style = "display:none;">
<?php		
		foreach( $category_slug['slug'] as $key=>$slug  ) :
		
			$args = array('post_type' => 'listing',
				'relation' => 'AND',
			    'tax_query' => array(
			        array(
			            'taxonomy' => 'location',
			            'field' => 'slug',
			            'terms' => $location,
			        ),
			        array(
			            'taxonomy' => 'listing-category',
			            'field' => 'slug',
			            'terms' => $slug,
			        ),
			    ),
			 );
			


			$loop = new WP_Query( $args, 'category' );

			if($loop->have_posts()) {
				$count = $loop->found_posts; 

				$path = get_bloginfo('url');
				echo '<div style="width:33.33%;padding:0 10pt 0 0;float:left;"><a data-index="'.$key.'" href="'.$path.'/listing-category/'.$slug.'/" >'.$category_slug['name'][$key].' ('.$count.')</a></div>';
			}

		endforeach;
	
		echo '<div class="clear"></div></div></div>';
	
	endforeach;
?>

<?php
 } 
 ?>
