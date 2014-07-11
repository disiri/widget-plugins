<?php
add_action('genesis_before_loop', 'listing_archive_title');

function listing_archive_title(){
	$taxonomy = get_query_var( 'taxonomy' );
    $queried_object = get_queried_object();
    $term_slug =   $queried_object->slug;

    $location = get_terms('location');
	$category = get_terms('listing-category');

	foreach( $category as $category ) {

		if( $category->slug == $term_slug) :
			
			$category_name = $category->name;
		
		endif;
	}

	foreach( $location as $location ) {

		if( $location->slug == $_SESSION['location-listing-cat']) :
			
			$location_name = $location->name;
		
		endif;
	}

?>
	<div class="listing-category">
		<div class="entry-header">
		<?php if( isset( $_SESSION['location-listing-cat']) ) : ?>

				<h1> Business Listings - <?php echo $location_name.' - '.$category_name;?></h1>
		<?php 
			else:
		?>
			<h1><?php single_cat_title('Business Listings - ')?></h1>
		<?php endif; ?>
		</div>
		<?php echo category_description(); ?>
		<div class="clear"> </div>
	</div>
	
<?php
}
remove_action('genesis_loop','genesis_do_loop');
add_action( 'genesis_loop', 'be_listing_loop' );

function be_listing_loop() {

	global $post;
	
	$taxonomy = get_query_var( 'taxonomy' );
    $queried_object = get_queried_object();
    $term_slug =   $queried_object->slug;
	 
	if( isset( $_SESSION['location-listing-cat'] ) ) :

		$loc = $_SESSION['location-listing-cat'];	

		$args = array(
		'posts_per_page' => 10,
		'post_type' => 'listing',
		'paged' => get_query_var( 'paged' ),
		'tax_query' => array(
			        array(
			            'taxonomy' => 'location',
			            'field' => 'slug',
			            'terms' => $loc,
			        ),
			        array(
			            'taxonomy' => 'listing-category',
			            'field' => 'slug',
			            'terms' => $term_slug,
			        ),
			    ),
		);

	else:
 
		$args = array(
			'posts_per_page' => 10,
			'post_type' => 'listing',
			'paged' => get_query_var( 'paged' ),
			'tax_query' => array(
				array(
					'taxonomy' => 'listing-category',
					'field' => 'slug',
					'terms' =>  $term_slug 
					)
				)
		);
	
	endif;
	
	global $wp_query;
	
	if( $wp_query->have_posts() ): 

		$field  = array(); 

		while( $wp_query->have_posts() ): $wp_query->the_post();
			
			$field[] = get_field( 'listing_interest' );

		endwhile;

	endif;
	
	$sortingArr = array( "gold" , "silver" , "bronze" );

	$result = array(); // result array

	foreach( $sortingArr as $val ) { // loop

	    $result[array_search( $val, $field )] = $val; // adding values

	}
	
	foreach( $result as $key=>$value ) :
		
?>
<?php
		if( isset( $_SESSION['location-listing-cat'] ) ) :

			$loc = $_SESSION['location-listing-cat'];

			$args = array(
				'posts_per_page' => 10,
				'post_type' 	 => 'listing',
				'paged' 		 => get_query_var( 'paged' ),
				'meta_key'		 => 'listing_interest',
				'meta_value'	 => $value,
				'tax_query' => array(
					        array(
					            'taxonomy' => 'location',
					            'field' => 'slug',
					            'terms' => $loc,
					        ),
					        array(
					            'taxonomy' => 'listing-category',
					            'field' => 'slug',
					            'terms' => $term_slug,
					        ),
					    ),
			);

		else:

			$args = array(
				'posts_per_page' => 10,
				'post_type' 	 => 'listing',
				'paged' 		 => get_query_var( 'paged' ),
				'meta_key'		 => 'listing_interest',
				'meta_value'	 => $value,
				'tax_query' 	 => array(
					array(
						'taxonomy' => 'listing-category',
						'field' => 'slug',
						'terms' =>  $term_slug 
						)
					)
			);

		endif;

			global $wp_query;
			
			$wp_query = new WP_Query( $args );
			
			if( $wp_query->have_posts() ): 
				while( $wp_query->have_posts() ): $wp_query->the_post();
						display();
?>
	
<?php 		
				endwhile;
					genesis_posts_nav();
			endif;
?>

<?php			
	endforeach;

add_action('genesis_sidebar', 'insert_mysidebar');
remove_action('genesis_sidebar', 'genesis_do_sidebar');

function insert_mysidebar() {
	if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('search-listing-sidebar') );
}
}

genesis();

function display() {
?>
	<div class="business-listing">
			
		<?php if(has_post_thumbnail()): ?>
			<div class="business-thumb">	
				
				<a href="<?php the_permalink() ?>"><?php	the_post_thumbnail(array(120,120));  ?></a>
			</div>
		<?php else: ?>
			<div class="business-thumb">
			<a href="<?php the_permalink() ?>"><img src="<?php echo get_stylesheet_directory_uri()?>/images/120img.png" alt="no thumbnail" width="120" height="120"/></a>
			</div>
		<?php endif; ?>
				
		<div class="business-content">	
			<h2 class="business-title">
				<a  title="<?php the_title_attribute(); ?>"  href="<?php the_permalink() ?>" ><?php the_title(); ?></a>
			</h2>
					
			<p><?php echo get_the_excerpt().'...'; ?></p>
					
			<div class="business-meta">
						
				<span class="phone"><a>PHONE:&nbsp;<?php  the_field('phone_number'); ?></a></span>

					<span class="contact"><a>Contact</a></span>
							
					<span class="website">
					<?php if(get_field('website')):
							echo '<a href="http://'.get_field('website').'" target="_blank">WEBSITE</a>';
						endif;?>
					</span>
							
				<div class="clear"> </div>
			</div>
					
			<div class="more-info" >
				<div class="stars">
					<?php echo do_shortcode( '[wps_ratings]' ); ?>
				</div>
				<a  title="<?php the_title_attribute(); ?>"  href="<?php the_permalink() ?>"  >More Info...</a>
			</div>
					
				</div>
				<div class="clear" ></div>
			</div>	<!--business listing-->

<?php
}
