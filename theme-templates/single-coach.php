<?php
/**
 * MSTW Coaching Staffs Template for displaying single coach profiles.
 *
 * 	NOTE: This is the "theme's framing". This template has been tested in the WordPress 
 * 	Twenty Eleven Theme. Plugin users will probably have to modify this template 
 * 	to fit their individual themes. 
 *
 *	MSTW Wordpress Plugins (http://shoalsummitsolutions.com)
 *	Copyright 2015 Mark O'Donnell (mark@shoalsummitsolutions.com)
 *
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU General Public License as published by
 *	the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.

 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *	GNU General Public License for more details.
 *
 *	You should have received a copy of the GNU General Public License
 *	along with this program. If not, see <http://www.gnu.org/licenses/>..
 *-------------------------------------------------------------------------*/
 ?>

	<?php get_header(); ?>

	<div id="primary">
		<div id="content" role="main">

			<?php while ( have_posts() ) : the_post(); ?>

				<nav id="nav-single">
					<h3 class="assistive-text"><?php _e( 'Post navigation', 'mstw-loc-domain' ); ?></h3>
					<span class="nav-previous">
						<?php $back =$_SERVER['HTTP_REFERER'];
						if( isset( $back ) && $back != '' ) { 
							echo '<a href="' . $back . '">';?>
							<span class="meta-nav">&larr;</span><?php _e( 'Return to roster', 'mstw-loc-domain' ); ?></a>
						<?php
						}?>
					</span> <!-- .nav-previous -->
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
				$degree = get_post_meta( $post->ID, 'mstw_cs_degree', true );
				$birth_date = get_post_meta( $post->ID, 'mstw_cs_birth_date', true );
				$home_town = get_post_meta( $post->ID, 'mstw_cs_home_town', true );
				$high_school = get_post_meta( $post->ID, 'mstw_cs_high_school', true );
				$family = nl2br( get_post_meta( $post->ID, 'mstw_cs_family', true ) );
				
				$options = get_option( 'mstw_cs_options' );
				extract( $options );
				?>
		
				<div class="coach-header coach-header-<?php echo( $staff_slug ) ?>">
					<!-- First, figure out the coach's photo -->
					<div id = "coach-photo">
						<?php 
						// Check the settings for the height and width of the photo
						// Default is 150 x 150
						
						$cs_image_width = isset( $options['gallery_photo_width'] ) ? $options['gallery_photo_width'] : '';
						$cs_image_height = isset( $options['gallery_photo_height'] ) ? $options['gallery_photo_height'] : '';
	
						$img_width = ( $cs_image_width == '' ) ? 150 : $cs_image_width;
						$img_height = ( $cs_image_height == '' ) ? 150 : $cs_image_height;
	
						//$img_width = ( $sp_image_width == '' ) ? 150 : $sp_image_width;
						//$img_height = ( $sp_image_height == '' ) ? 150 : $sp_image_height;
						
						// check if the post has a Post Thumbnail assigned to it.
						 if ( has_post_thumbnail( ) ) { //defaults to current post
							//Get the photo file;
							$photo_file_url = wp_get_attachment_thumb_url( get_post_thumbnail_id( ) ); //defaults to current post
							$alt = 'Photo of ' . $name;
						} else {
							// Default image is tied to the staff taxonomy. 
							// Try to load default-photo-staff-slug.jpg, If it does not exst,
							// Then load default-photo.jpg from the plugin -->
							$photo_file = WP_PLUGIN_DIR . '/coaching-staffs/images/default-photo' . '-' . $staff_slug . '.jpg';
							if ( file_exists( $photo_file ) ) {
								$photo_file_url = plugins_url() . '/coaching-staffs/images/default-photo' . '-' . $staff_slug . '.jpg';
								$alt = __( 'Default image for', 'mstw-loc-domain') . ' ' . $staff_slug;
							}
							else {
								$photo_file_url = plugins_url() . '/coaching-staffs/images/default-photo' . '.jpg';
								$alt = __( 'Photo not found.', 'mstw-loc-domain' );
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
							if( $options['show_experience'] ) {
								echo $row_start . $options['experience_label'] . $new_cell .  $experience . $row_end;
							}
							
							//ALMA MATER
							if( $options['show_alma_mater'] ) {
								echo $row_start . $options['alma_mater_label'] . $new_cell .  $alma_mater . $row_end;
							}
							
							// DEGREE
							if( $options['show_degree'] ) {
								echo $row_start . $options['degree_label'] . $new_cell .  $degree . $row_end;
							}
							
							// BIRTH DATE
							if( $options['show_birth_date'] ) {
								echo $row_start . $options['birth_date_label'] . $new_cell .  $birth_date . $row_end;
							}
							
							// HOMETOWN
							if( $options['show_home_town'] ) {
								echo $row_start . $options['home_town_label'] . $new_cell .  $home_town . $row_end;
							}
							
							// HIGH SCHOOL
							if( $options['show_high_school'] ) {
								echo $row_start . $options['high_school_label'] . $new_cell .  $high_school . $row_end;
							}
							
							// FAMILY
							if( $options['show_family'] ) {
								echo $row_start . $options['family_label'] . $new_cell .  $family . $row_end;
							}
							
							?>
							
						</tbody>
						</table> <!-- .coach-info -->
					
					</div><!-- #coach-name-positon-->
					
				</div><!-- .coach-header -->
		
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

	<?php get_footer();?>