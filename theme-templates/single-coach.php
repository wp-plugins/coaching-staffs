<?php
/**
 * MSTW Coaching Staffs Template for displaying single coach profiles.
 *
 * NOTE: This is the "theme's framing". The bulk of the work is done in 
 * content-single-coach.php. This template has been tested in the WordPress 
 * Twenty Eleven Theme. Plugin users will probably have to modify this template 
 * to fit their individual themes. 
 *
 * @package Twenty_Eleven
 * @subpackage Coaching_Staffs
 * @since Coaching Staffs 0.1
 */

	get_header(); 

?>

<div id="primary">
	<div id="content" role="main">

	<?php while ( have_posts() ) : the_post(); ?>

		<nav id="nav-single">
			<h3 class="assistive-text"><?php _e( 'Post navigation', 'twentyeleven' ); ?></h3>
			<span class="nav-previous">
			<?php $back =$_SERVER['HTTP_REFERER'];
			if( isset( $back ) && $back != '' ) { 
				echo '<a href="' . $back . '"><span class="meta-nav">&larr;</span>Return to roster</a>';
			}?>
		</nav><!-- #nav-single -->

		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

		<?php
		// Get the necessary data
		$name = get_the_title( $post->ID );
		$position = get_the_title( $_GET['position'] );
		$term_list = wp_get_post_terms( $_GET['position'], 'staffs' );
		//print_r( $term_list );
		//$staff_slug = $term_list[0]['slug'];
		if ( !empty( $term_list ) ) {
            //foreach ( $term_list as $term )
                $staff_slug = $term_list[0]->slug;
        }
		
		$experience = get_post_meta($post->ID, 'mstw_cs_experience', true );
		$alma_mater = get_post_meta($post->ID, 'mstw_cs_alma_mater', true );
		
		$options = get_option( 'mstw_cs_options' );
		extract( $options );
		
		//echo '<h2>ORIG OPTIONS</h2>';
		//print_r( $settings );
				
		//$options = mstw_cs_set_fields( $format, $options );
		//echo '<h2>FORMAT: ' . $format . ' OPTIONS</h2>';
		//print_r( $settings );
					
		//echo '<h2>REVISED OPTIONS</h2>';
		//$options = wp_parse_args( $settings, $options );
		//print_r( $options );
		
		//$sp_content_title = $options['sp_content_title'];
		//$sp_image_width = $options['sp_image_width'];
		//$sp_image_height = $options['sp_image_height'];
		
		// THIS WON'T WORK!!
		
		// Single Coach Page title
		/*$html = '<h1 class="coach-head-title ';
		$coach_staffs = wp_get_object_terms($post->ID, 'staffs');
		if( !empty( $coach_staffs ) ) {
			if( !is_wp_error( $coach_staffs ) ) {
				foreach( $coach_staffs as $staff ) {
					$team_name = $team->name;
					$team_slug = $team->slug;
					$html .=  'coach-head-title-' . $team_slug . ' ';
					//echo '<h1 class="coach-head-title" style="color:' . $sp_main_text_color . ';">' . $team_name. '</h1>'; 
				}
				$html .= '">';
			}
		}
		$html .= $team_name . '</h1>';
		
		echo $html;
		*/
		?>
		
		<header class="coach-header coach-header-<?php echo( $staff_slug ) ?>">
			<!-- First, figure out the coach's photo -->
			<div id = "coach-photo">
				<?php 
				// Check the settings for the height and width of the photo
				// Default is 150 x 150
				$img_width = ( $sp_image_width == '' ) ? 150 : $sp_image_width;
				$img_height = ( $sp_image_height == '' ) ? 150 : $sp_image_height;
				
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
				
				echo( '<img src="' . $photo_file_url . '" alt="' . $alt . '" width="' . $img_width . '" height="' . $img_height . '" />' );
				?>
			</div> <!-- #coach-photo -->
			
			<!-- Figure out the coach name and number -->
			<div id="coach-name-position"> 
				<div id="coach-name">
				<h1><?php echo $name; ?></h1> 
				<!-- get the position ID from the URL -->
				<h2><?php echo $position; ?> </h2>
				</div> <!-- #coach-name -->
			
			
				<table class="coach-info">
				<tbody>
					<?php 
					$row_start = '<tr><td class="lf-col">';
					$new_cell = ':</td><td class="rt-col">'; //colon is for the end of the title
					$row_end = '</td></tr>';
					
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
				</table> <!-- .coach-info -->
			
			</div><!-- #coach-name-positon-->
			
		</header><!-- #coach-header -->
		
		<?php if( get_the_content( ) != "" ) { ?>
			
			<div class="coach-bio"> <!-- coach-bio-<?php echo $staff_slug; ?> "> -->
			
				<?php $profile_bio_heading_text = ($profile_bio_heading_text == '' ) ? __( 'Profile', 'mstw-loc-domain' ) : $profile_bio_heading_text; ?>
				
				<h1><?php echo $profile_bio_heading_text ?></h1>

				<!--add the bio content (format it as desired in the post)-->
				<?php the_content(); ?>
			
			</div><!-- #coach-bio -->
			
		<?php } // end of if ( get_the_content() ) ?>

		</article><!-- #post-<?php the_ID(); ?> -->

	<?php endwhile; // end of the main loop. ?>

	</div><!-- #content -->
</div><!-- #primary -->

<?php get_footer(); ?>