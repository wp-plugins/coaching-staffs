<?php
/**
 * The template for displaying Coaching Staff Archive pages using the MSTW Coaching Staffs plugin.
 * This will create a 'gallery view' of the staff.
 *
 * CHANGE LOG
 * 20130803-MAO:
 *	Began development
 *
 *
 */
 
	//if ( !function_exists( 'mstw_cs_set_fields_by_format' ) ) {
		//echo '<p> mstw_text_ctrl does not exist. </p>';
		//echo '<p> path:' . WP_CONTENT_DIR . '/plugins/coaching-staff/includes/mstw-cs-utility-functions.php</p>';
		//require_once  WP_CONTENT_DIR . '/plugins/coaching-staff/includes/mstw-cs-utility-functions.php';
	//};
 
	get_header(); 
	
	// Get the settings from the admin page
	//$options = get_option( 'mstw_cs_options' );
	
	//$sp_main_text_color = $options['sp_main_text_color'];
	//$sp_main_bkgd_color = $options['sp_main_bkgd_color'];
	//$hide_weight = $options['tr_hide_weight'];
	
	
	// Get the right settings for the format
	//$settings = mstw_cs_set_fields_by_format( $format );
	
	//echo '<h2>REVISED OPTIONS</h2>';
	//$options = wp_parse_args( $settings, $options );
	//print_r( $options );
	
	//$show_title = 1; /* this will come from a setting */
	
	//$use_coach_links = $options['pg_use_coach_links'];
	
	// figure out the staff name - for the title (if shown) and for staff-based styles
	$uri_array = explode( '/', $_SERVER['REQUEST_URI'] );	
	$staff_slug = $uri_array[sizeof( $uri_array )-2];
	$term = get_term_by( 'slug', $staff_slug, 'staffs' );
	$staff_name .= $term->name;
	
	?>
	
	<section id="primary">
	<div id="content-coach-gallery" role="main" >

	<header class="page-header page-header-<?php echo $staff_slug ?>">
		<?php echo '<h1 class="staff-head-title staff-head-title-' . $staff_slug . '">' . $staff_name . '</h1>'; ?>
	</header>

	<?php /* Start the Loop */ 
	// set the coach photo size based on admin settings, if any
	$cs_image_width = ''; //$options['sp_image_width'];
	$cs_image_height = ''; //$options['sp_image_height'];
	
	$img_width = ( $sp_image_width == '' ) ? 150 : $cs_image_width;
	$img_height = ( $sp_image_height == '' ) ? 150 : $cs_image_height;
	
	while ( have_posts() ) : the_post(); 
		$coach_id = get_post_meta( $post->ID, 'mstw_cs_position_coach', true );
		$name = get_the_title( $coach_id );
		$position = get_the_title( $post->ID );
		
		$experience = get_post_meta( $coach_id, 'mstw_cs_experience', true );
		$alma_mater = get_post_meta( $coach_id, 'mstw_cs_alma_mater', true );
		
		?> 
		
		<div class="coach-tile coach-tile-<?php echo( $staff_slug ) ?>">
		
			<div class = "coach-photo">
				<?php 
				
				// check if the post has a Post Thumbnail assigned to it.
				 if ( has_post_thumbnail( $coach_id ) ) { 
					//Get the photo file;
					$photo_file_url = wp_get_attachment_thumb_url( get_post_thumbnail_id( $coach_id ) );
					$alt = 'Photo of ' . $name;
				} else {
					// Default image is tied to the staff taxonomy. 
					// Try to load default-photo-staff-slug.jpg, If it does not exst,
					// Then load default-photo.jpg from the plugin -->
					$photo_file = WP_PLUGIN_DIR . '/coaching-staffs/images/default-photo' . '-' . $staff_slug . '.jpg';
					if ( file_exists( $photo_file ) ) {
						$photo_file_url = plugins_url() . '/coaching-staffs/images/default-photo' . '-' . $staff_slug . '.jpg';
					}
					else {
						$photo_file_url = plugins_url() . '/coaching-staffs/images/default-photo' . '.jpg';
						$alt = "Photo not found.";
					}
				}
				// See if the single-coach.php template is in the theme directory
				// If so, add a link to the coach's profile to the photo
				$single_coach_template = get_template_directory( ) . '/single-coach.php';
				
				if ( file_exists( $single_coach_template ) ) {
					echo( '<a href="' . get_permalink( $coach_id ) . '?position='. $post->ID . '">' . '<img src="' . $photo_file_url . '" alt="' . $alt . '" width="' . $img_width . '" height="' . $img_height . '" /></a>' );
				}
				else {
					echo( '<img src="' . $photo_file_url . '" alt="' . $alt . '" width="' . $img_width . '" height="' . $img_height . '" />' );
				}
				?>
				
			</div> <!-- .coach-photo -->
			
			<div class = "coach-info-container">
				<?php
				// See if the single-coach.php template is in the theme directory
				// If so, add a link to the coach's name
				if ( file_exists( $single_coach_template ) ) {
					$coach_html = '<a href="' .  get_permalink( $coach_id ) . '?position='. $post->ID . '">';
					$coach_html .= get_the_title( $coach_id ) . '</a>';
				}
				else {
					$coach_html = get_the_title( $coach_id );
				}
			
				?>
				
				<div class="coach-name-position"> 
					<h1><?php echo $coach_html ?></h1>
					<h2><?php echo $position ?></h2>
					</div>
				
			
				<table class="coach-info">
				<tbody>
					<?php 
					$row_start = '<tr><td class="lf-col">';
					$new_cell = ':</td><td class="rt-col">'; //colon is for the end of the title
					$row_end = '</td></tr>';
					
					// POSITION
					//if( $options['show_position'] ) {
						//echo $row_start . $options['position_label'] . $new_cell .  $position . $row_end;
						//echo $row_start . 'Position' . $new_cell .  $position . $row_end;
					//}
					
					//EXPERIENCE
					//if( $options['show_experience'] ) {
						//echo $row_start . $options['experience_label'] . $new_cell .  $experience . $row_end;
						echo $row_start . 'Experience' . $new_cell .  $experience . $row_end;
					//}
					
					//ALMA MATER
					//if( $options['show_year'] ) {
						//echo $row_start . $options['year_label'] . $new_cell .  $year . $row_end;
						echo $row_start . 'Alma Mater' . $new_cell .  $alma_mater . $row_end;
					//}
					
					?>
					
				</tbody>
				</table>
			</div><!-- .coach-info-container --> 	
		</div><!-- .coach-tile -->

	<?php endwhile; ?>

	</div><!-- #content -->
	</section><!-- #primary -->

<?php //get_sidebar(); ?>
<?php get_footer(); ?>